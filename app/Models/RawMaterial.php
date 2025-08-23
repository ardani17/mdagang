<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RawMaterial extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
        'category_id' => 'integer',
        'supplier_id' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['stock_value', 'stock_status_label', 'category_name'];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate code on creation
        static::creating(function ($material) {
            if (empty($material->code)) {
                $material->code = self::generateCode();
            }
            
            // Set initial status based on stock
            $material->updateStatus();
        });

        // Update status when stock changes
        static::updating(function ($material) {
            if ($material->isDirty(['current_stock', 'minimum_stock', 'reorder_point'])) {
                $material->updateStatus();
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
     * Relationships
     */
    
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
        return $this->hasMany(StockMovement::class, 'item_id')
            ->where('item_type', 'raw_material');
    }

    /**
     * Scopes
     */
    
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
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Accessors & Mutators
     */
    
    /**
     * Get stock value attribute
     */
    public function getStockValueAttribute(): float
    {
        return round($this->current_stock * $this->average_price, 2);
    }

    /**
     * Get stock status label attribute
     */
    public function getStockStatusLabelAttribute(): string
    {
        $labels = [
            'good' => 'Stok Baik',
            'low_stock' => 'Stok Rendah',
            'critical' => 'Stok Kritis',
            'out_of_stock' => 'Habis'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get category name attribute
     */
    public function getCategoryNameAttribute(): string
    {
        return $this->category ? $this->category->name : '-';
    }

    /**
     * Methods
     */
    
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
     * Update stock status based on current levels
     */
    public function updateStatus(): void
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
    }

    /**
     * Update stock quantity with transaction
     */
    public function updateStock(float $quantity, string $type = 'add', array $movementData = []): bool
    {
        DB::beginTransaction();
        
        try {
            $oldStock = $this->current_stock;
            
            switch ($type) {
                case 'add':
                    $this->current_stock += $quantity;
                    $movementType = 'in';
                    break;
                case 'subtract':
                    if ($this->current_stock < $quantity) {
                        throw new \Exception('Insufficient stock. Available: ' . $this->current_stock);
                    }
                    $this->current_stock -= $quantity;
                    $movementType = 'out';
                    break;
                case 'set':
                    $movementType = $this->current_stock > $quantity ? 'out' : 'in';
                    $quantity = abs($this->current_stock - $quantity);
                    $this->current_stock = $quantity;
                    break;
                default:
                    throw new \Exception('Invalid stock update type: ' . $type);
            }
            
            // Update status
            $this->updateStatus();
            $this->save();
            
            // Record stock movement
            $this->recordStockMovement(array_merge([
                'quantity' => $quantity,
                'movement_type' => $movementType,
                'before_stock' => $oldStock,
                'after_stock' => $this->current_stock,
            ], $movementData));
            
            DB::commit();
            
            // Log the stock update
            Log::info('Stock updated for raw material', [
                'material_id' => $this->id,
                'material_name' => $this->name,
                'type' => $type,
                'quantity' => $quantity,
                'old_stock' => $oldStock,
                'new_stock' => $this->current_stock,
                'status' => $this->status
            ]);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update stock for raw material', [
                'material_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Record stock movement
     */
    public function recordStockMovement(array $data): StockMovement
    {
        $defaults = [
            'item_type' => 'raw_material',
            'item_id' => $this->id,
            'type' => $data['movement_type'] ?? 'adjustment',
            'quantity' => $data['quantity'] ?? 0,
            'unit_cost' => $data['unit_cost'] ?? $this->average_price,
            'total_cost' => 0,
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'before_stock' => $data['before_stock'] ?? $this->current_stock,
            'after_stock' => $data['after_stock'] ?? $this->current_stock,
            'reason' => $data['reason'] ?? 'adjustment',
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ];
        
        // Calculate total cost
        $defaults['total_cost'] = $defaults['quantity'] * $defaults['unit_cost'];
        
        return StockMovement::create($defaults);
    }

    /**
     * Adjust stock with validation
     */
    public function adjustStock(string $adjustmentType, float $quantity, string $reason, ?string $notes = null): bool
    {
        $movementData = [
            'reason' => $reason,
            'notes' => $notes,
        ];
        
        switch ($adjustmentType) {
            case 'add':
                return $this->updateStock($quantity, 'add', $movementData);
            case 'subtract':
                return $this->updateStock($quantity, 'subtract', $movementData);
            case 'set':
                $difference = abs($this->current_stock - $quantity);
                $type = $this->current_stock > $quantity ? 'subtract' : 'add';
                return $this->updateStock($difference, $type, $movementData);
            default:
                throw new \Exception('Invalid adjustment type: ' . $adjustmentType);
        }
    }

    /**
     * Calculate average price based on purchase history
     */
    public function calculateAveragePrice(): float
    {
        $movements = $this->stockMovements()
            ->where('type', 'in')
            ->where('created_at', '>=', now()->subMonths(6))
            ->get();

        if ($movements->isEmpty()) {
            return $this->last_purchase_price ?? 0;
        }

        $totalValue = $movements->sum('total_cost');
        $totalQuantity = $movements->sum('quantity');

        return $totalQuantity > 0 ? round($totalValue / $totalQuantity, 2) : 0;
    }

    /**
     * Get latest stock movements
     */
    public function getLatestMovements($limit = 10)
    {
        return $this->stockMovements()
            ->with('createdBy:id,name')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Check if can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Check if used in any active recipes
        if ($this->recipeIngredients()->exists()) {
            return false;
        }
        
        // Check if has pending purchase orders
        // Add more business logic as needed
        
        return true;
    }

    /**
     * Get statistics for this material
     */
    public function getStatistics(): array
    {
        $thirtyDaysAgo = now()->subDays(30);
        
        $consumed = $this->stockMovements()
            ->where('type', 'out')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('quantity');
            
        $purchased = $this->stockMovements()
            ->where('type', 'in')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('quantity');
            
        $avgConsumption = $consumed / 30;
        $daysUntilStockout = $avgConsumption > 0 ? round($this->current_stock / $avgConsumption) : 999;
        
        return [
            'stock_status' => $this->stock_status_label,
            'stock_value' => $this->stock_value,
            'total_consumed_30days' => $consumed,
            'total_purchased_30days' => $purchased,
            'average_consumption_per_day' => round($avgConsumption, 2),
            'days_until_stockout' => $daysUntilStockout,
        ];
    }
}