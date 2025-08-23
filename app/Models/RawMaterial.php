<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'category_id',
        'supplier_id',
        'unit',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'reorder_point',
        'reorder_quantity',
        'average_price',
        'last_purchase_price',
        'last_purchase_date',
        'expiry_date',
        'status',
        'storage_location',
        'lead_time_days',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'maximum_stock' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'reorder_quantity' => 'decimal:2',
        'average_price' => 'decimal:2',
        'last_purchase_price' => 'decimal:2',
        'last_purchase_date' => 'date',
        'expiry_date' => 'date',
        'lead_time_days' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($material) {
            if (empty($material->code)) {
                $material->code = self::generateCode();
            }
        });
    }

    /**
     * Generate unique material code
     */
    public static function generateCode(): string
    {
        $prefix = 'RM';
        $lastMaterial = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastMaterial) {
            $lastNumber = intval(substr($lastMaterial->code, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the category that owns the raw material
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier that owns the raw material
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the recipe ingredients using this raw material
     */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /**
     * Get the stock movements for this raw material
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'reference_id')
            ->where('type', 'raw_material');
    }

    /**
     * Get purchase order items for this raw material
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Scope a query to only include active materials
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include materials below reorder point
     */
    public function scopeBelowReorderPoint($query)
    {
        return $query->whereColumn('current_stock', '<=', 'reorder_point');
    }

    /**
     * Scope a query to only include materials below minimum stock
     */
    public function scopeBelowMinimumStock($query)
    {
        return $query->whereColumn('current_stock', '<', 'minimum_stock');
    }

    /**
     * Scope a query to only include expiring materials
     */
    public function scopeExpiring($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    /**
     * Check if material needs reordering
     */
    public function needsReordering(): bool
    {
        return $this->current_stock <= $this->reorder_point;
    }

    /**
     * Check if material is below minimum stock
     */
    public function isBelowMinimumStock(): bool
    {
        return $this->current_stock < $this->minimum_stock;
    }

    /**
     * Check if material is expiring soon
     */
    public function isExpiringSoon($days = 30): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date <= now()->addDays($days);
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
        $beforeStock = $this->current_stock;
        $quantity = $data['quantity'];
        $movementType = $data['movement_type'] ?? 'in';
        
        // Calculate after stock based on movement type
        $afterStock = $movementType === 'in'
            ? $beforeStock + $quantity
            : $beforeStock - $quantity;
            
        return StockMovement::create([
            'item_type' => 'raw_material',
            'item_id' => $this->id,
            'type' => $movementType,
            'quantity' => $quantity,
            'unit_cost' => $data['unit_cost'] ?? $this->average_price,
            'total_cost' => $data['total_cost'] ?? ($quantity * ($data['unit_cost'] ?? $this->average_price)),
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'reason' => $data['reason'] ?? 'adjustment',
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['performed_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Calculate average price based on purchase history
     */
    public function calculateAveragePrice(): float
    {
        $recentPurchases = $this->purchaseOrderItems()
            ->whereHas('purchaseOrder', function ($query) {
                $query->where('status', 'received')
                    ->where('received_date', '>=', now()->subMonths(6));
            })
            ->get();

        if ($recentPurchases->isEmpty()) {
            return $this->last_purchase_price ?? 0;
        }

        $totalValue = 0;
        $totalQuantity = 0;

        foreach ($recentPurchases as $item) {
            $totalValue += $item->total_price;
            $totalQuantity += $item->quantity;
        }

        return $totalQuantity > 0 ? round($totalValue / $totalQuantity, 2) : 0;
    }

    /**
     * Get the supplier for this raw material
     */
    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    /**
     * Get stock value
     */
    public function getStockValueAttribute(): float
    {
        return $this->current_stock * $this->average_price;
    }

    /**
     * Get stock status (use the status field from database)
     */
    public function getStockStatusAttribute(): string
    {
        return $this->status;
    }
    
    /**
     * Update stock status based on current levels
     */
    public function updateStockStatus(): void
    {
        if ($this->current_stock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->current_stock <= $this->minimum_stock) {
            $this->status = 'critical';
        } elseif ($this->current_stock <= $this->reorder_point) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'good';
        }
        $this->save();
    }
}