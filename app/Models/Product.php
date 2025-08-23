<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'unit',
        'selling_price',
        'cost_price',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'reorder_point',
        'reorder_quantity',
        'barcode',
        'image_url',
        'weight',
        'dimensions',
        'shelf_life_days',
        'storage_conditions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'maximum_stock' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'reorder_quantity' => 'decimal:2',
        'weight' => 'decimal:2',
        'shelf_life_days' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->code)) {
                $product->code = self::generateCode();
            }
            if (empty($product->barcode)) {
                $product->barcode = self::generateBarcode();
            }
        });
    }

    /**
     * Generate unique product code
     */
    public static function generateCode(): string
    {
        $prefix = 'PRD';
        $lastProduct = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastProduct) {
            $lastNumber = intval(substr($lastProduct->code, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique barcode
     */
    public static function generateBarcode(): string
    {
        do {
            $barcode = '8' . str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Get the category that owns the product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the recipe for this product
     */
    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class);
    }

    /**
     * Get the production orders for this product
     */
    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }

    /**
     * Get the order items for this product
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the stock movements for this product
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'reference_id')
            ->where('reference_type', 'product');
    }

    /**
     * Scope a query to only include active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include products below reorder point
     */
    public function scopeBelowReorderPoint($query)
    {
        return $query->whereColumn('current_stock', '<=', 'reorder_point');
    }

    /**
     * Scope a query to only include products below minimum stock
     */
    public function scopeBelowMinimumStock($query)
    {
        return $query->whereColumn('current_stock', '<', 'minimum_stock');
    }

    /**
     * Scope a query to only include products in stock
     */
    public function scopeInStock($query)
    {
        return $query->where('current_stock', '>', 0);
    }

    /**
     * Check if product needs reordering
     */
    public function needsReordering(): bool
    {
        return $this->current_stock <= $this->reorder_point;
    }

    /**
     * Check if product is below minimum stock
     */
    public function isBelowMinimumStock(): bool
    {
        return $this->current_stock < $this->minimum_stock;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(): bool
    {
        return $this->current_stock > 0;
    }

    /**
     * Check if product can be produced
     */
    public function canBeProduce(float $quantity = 1): bool
    {
        if (!$this->recipe) {
            return false;
        }

        foreach ($this->recipe->ingredients as $ingredient) {
            $requiredQuantity = $ingredient->pivot->quantity * $quantity;
            if ($ingredient->current_stock < $requiredQuantity) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get profit margin
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->selling_price <= 0) {
            return 0;
        }

        return round((($this->selling_price - $this->cost_price) / $this->selling_price) * 100, 2);
    }

    /**
     * Get profit amount
     */
    public function getProfitAmountAttribute(): float
    {
        return $this->selling_price - $this->cost_price;
    }

    /**
     * Get stock value
     */
    public function getStockValueAttribute(): float
    {
        return $this->current_stock * $this->cost_price;
    }

    /**
     * Get potential revenue
     */
    public function getPotentialRevenueAttribute(): float
    {
        return $this->current_stock * $this->selling_price;
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->isBelowMinimumStock()) {
            return 'critical';
        } elseif ($this->needsReordering()) {
            return 'low';
        } elseif ($this->current_stock >= $this->maximum_stock) {
            return 'overstock';
        } else {
            return 'normal';
        }
    }

    /**
     * Update stock quantity
     */
    public function updateStock(float $quantity, string $type = 'add'): void
    {
        if ($type === 'add') {
            $this->current_stock += $quantity;
        } elseif ($type === 'subtract') {
            $this->current_stock = max(0, $this->current_stock - $quantity);
        } else {
            $this->current_stock = $quantity;
        }

        $this->save();
    }

    /**
     * Record stock movement
     */
    public function recordStockMovement(array $data): StockMovement
    {
        return StockMovement::create([
            'type' => $data['type'],
            'reference_type' => 'product',
            'reference_id' => $this->id,
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'] ?? $this->cost_price,
            'total_price' => $data['total_price'] ?? ($data['quantity'] * ($data['unit_price'] ?? $this->cost_price)),
            'before_stock' => $this->current_stock,
            'after_stock' => $data['after_stock'] ?? $this->current_stock,
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Calculate production cost based on recipe
     */
    public function calculateProductionCost(float $quantity = 1): float
    {
        if (!$this->recipe) {
            return 0;
        }

        $totalCost = 0;

        foreach ($this->recipe->ingredients as $ingredient) {
            $requiredQuantity = $ingredient->pivot->quantity * $quantity;
            $totalCost += $requiredQuantity * $ingredient->average_price;
        }

        // Add labor and overhead costs if defined in recipe
        if ($this->recipe->labor_cost) {
            $totalCost += $this->recipe->labor_cost * $quantity;
        }

        if ($this->recipe->overhead_cost) {
            $totalCost += $this->recipe->overhead_cost * $quantity;
        }

        return round($totalCost, 2);
    }

    /**
     * Get sales performance for a period
     */
    public function getSalesPerformance($startDate = null, $endDate = null): array
    {
        $query = $this->orderItems()
            ->whereHas('order', function ($q) {
                $q->whereIn('status', ['completed', 'delivered']);
            });

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $items = $query->get();

        return [
            'total_quantity' => $items->sum('quantity'),
            'total_revenue' => $items->sum('total'),
            'order_count' => $items->count(),
            'average_quantity_per_order' => $items->count() > 0 ? round($items->sum('quantity') / $items->count(), 2) : 0,
        ];
    }

    /**
     * Get production history
     */
    public function getProductionHistory($limit = 10)
    {
        return $this->productionOrders()
            ->with(['qualityInspection'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Update cost price based on production costs
     */
    public function updateCostPrice(): void
    {
        $productionCost = $this->calculateProductionCost();
        
        if ($productionCost > 0) {
            $this->update(['cost_price' => $productionCost]);
        }
    }
}