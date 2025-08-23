<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'product_id',
        'recipe_id',
        'quantity',
        'actual_quantity',
        'unit',
        'batch_number',
        'status',
        'priority',
        'scheduled_date',
        'start_date',
        'completion_date',
        'estimated_cost',
        'actual_cost',
        'production_time',
        'actual_production_time',
        'assigned_to',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'actual_quantity' => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'production_time' => 'integer',
        'actual_production_time' => 'integer',
        'scheduled_date' => 'datetime',
        'start_date' => 'datetime',
        'completion_date' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
            if (empty($order->batch_number)) {
                $order->batch_number = self::generateBatchNumber();
            }
            if (empty($order->created_by)) {
                $order->created_by = auth()->id();
            }
        });

        static::updating(function ($order) {
            // Update completion date when status changes to completed
            if ($order->isDirty('status') && $order->status === 'completed' && !$order->completion_date) {
                $order->completion_date = now();
            }
            // Update start date when status changes to in_progress
            if ($order->isDirty('status') && $order->status === 'in_progress' && !$order->start_date) {
                $order->start_date = now();
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'PO-' . date('Ymd');
        $lastOrder = self::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate batch number
     */
    public static function generateBatchNumber(): string
    {
        return 'BATCH-' . date('YmdHis') . '-' . mt_rand(1000, 9999);
    }

    /**
     * Get the product for this production order
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the recipe used for this production order
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Get the user who created this order
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to this order
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the quality inspection for this production order
     */
    public function qualityInspection(): HasOne
    {
        return $this->hasOne(QualityInspection::class);
    }

    /**
     * Get stock movements related to this production order
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'reference_id')
            ->where('reference_type', 'production_order');
    }

    /**
     * Scope a query to only include pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include in progress orders
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include high priority orders
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope a query to get orders scheduled for a date
     */
    public function scopeScheduledFor($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }

    /**
     * Check if production order can be started
     */
    public function canStart(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        if (!$this->recipe) {
            return false;
        }

        // Check ingredients availability
        $availability = $this->recipe->checkIngredientsAvailability($this->quantity);
        
        return $availability['available'];
    }

    /**
     * Start production
     */
    public function startProduction(): bool
    {
        if (!$this->canStart()) {
            return false;
        }

        // Deduct raw materials from inventory
        foreach ($this->recipe->ingredients as $ingredient) {
            $requiredQuantity = $ingredient->pivot->quantity * $this->quantity;
            
            // Update raw material stock
            $ingredient->updateStock($requiredQuantity, 'subtract');
            
            // Record stock movement
            $ingredient->recordStockMovement([
                'type' => 'production_consumption',
                'quantity' => $requiredQuantity,
                'notes' => "Used in production order: {$this->order_number}",
            ]);
        }

        // Update status
        $this->update([
            'status' => 'in_progress',
            'start_date' => now(),
        ]);

        return true;
    }

    /**
     * Complete production
     */
    public function completeProduction(array $data = []): bool
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        $actualQuantity = $data['actual_quantity'] ?? $this->quantity;
        $actualCost = $data['actual_cost'] ?? $this->estimated_cost;
        $actualTime = $data['actual_production_time'] ?? $this->production_time;

        // Add finished products to inventory
        $this->product->updateStock($actualQuantity, 'add');
        
        // Record stock movement
        $this->product->recordStockMovement([
            'type' => 'production_output',
            'quantity' => $actualQuantity,
            'notes' => "Produced from order: {$this->order_number}",
        ]);

        // Update production order
        $this->update([
            'status' => 'completed',
            'actual_quantity' => $actualQuantity,
            'actual_cost' => $actualCost,
            'actual_production_time' => $actualTime,
            'completion_date' => now(),
        ]);

        return true;
    }

    /**
     * Cancel production order
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }

        // If in progress, return materials to inventory
        if ($this->status === 'in_progress') {
            foreach ($this->recipe->ingredients as $ingredient) {
                $usedQuantity = $ingredient->pivot->quantity * $this->quantity;
                
                // Return materials to stock
                $ingredient->updateStock($usedQuantity, 'add');
                
                // Record stock movement
                $ingredient->recordStockMovement([
                    'type' => 'production_return',
                    'quantity' => $usedQuantity,
                    'notes' => "Returned from cancelled order: {$this->order_number}. Reason: {$reason}",
                ]);
            }
        }

        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . "\nCancelled: " . $reason,
        ]);

        return true;
    }

    /**
     * Calculate production efficiency
     */
    public function getEfficiencyAttribute(): ?float
    {
        if (!$this->actual_quantity || !$this->quantity) {
            return null;
        }

        return round(($this->actual_quantity / $this->quantity) * 100, 2);
    }

    /**
     * Calculate cost variance
     */
    public function getCostVarianceAttribute(): ?float
    {
        if (!$this->actual_cost || !$this->estimated_cost) {
            return null;
        }

        return $this->actual_cost - $this->estimated_cost;
    }

    /**
     * Calculate cost variance percentage
     */
    public function getCostVariancePercentageAttribute(): ?float
    {
        if (!$this->estimated_cost || $this->estimated_cost == 0) {
            return null;
        }

        return round((($this->actual_cost - $this->estimated_cost) / $this->estimated_cost) * 100, 2);
    }

    /**
     * Calculate time variance
     */
    public function getTimeVarianceAttribute(): ?int
    {
        if (!$this->actual_production_time || !$this->production_time) {
            return null;
        }

        return $this->actual_production_time - $this->production_time;
    }

    /**
     * Get production status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'on_hold' => 'On Hold',
            default => 'Unknown',
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
            default => 'Normal',
        };
    }

    /**
     * Check if order is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->status === 'completed' || $this->status === 'cancelled') {
            return false;
        }

        return $this->scheduled_date && $this->scheduled_date < now();
    }

    /**
     * Get production metrics
     */
    public function getMetrics(): array
    {
        return [
            'efficiency' => $this->efficiency,
            'cost_variance' => $this->cost_variance,
            'cost_variance_percentage' => $this->cost_variance_percentage,
            'time_variance' => $this->time_variance,
            'quality_passed' => $this->qualityInspection?->passed ?? null,
            'is_overdue' => $this->isOverdue(),
        ];
    }
}