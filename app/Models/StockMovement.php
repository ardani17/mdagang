<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type',
        'item_id',
        'type',
        'quantity',
        'unit_cost',
        'total_cost',
        'reference_type',
        'reference_id',
        'from_location',
        'to_location',
        'before_stock',
        'after_stock',
        'reason',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'before_stock' => 'decimal:3',
        'after_stock' => 'decimal:3',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movement) {
            if (empty($movement->created_by)) {
                $movement->created_by = auth()->id();
            }
            if (empty($movement->total_cost)) {
                $movement->total_cost = $movement->quantity * $movement->unit_cost;
            }
        });
    }

    /**
     * Get the user who created this movement
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the reference model (polymorphic)
     */
    public function reference()
    {
        if ($this->reference_type === 'raw_material') {
            return $this->belongsTo(RawMaterial::class, 'reference_id');
        } elseif ($this->reference_type === 'product') {
            return $this->belongsTo(Product::class, 'reference_id');
        } elseif ($this->reference_type === 'production_order') {
            return $this->belongsTo(ProductionOrder::class, 'reference_id');
        }
        
        return null;
    }

    /**
     * Scope a query to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by reference type
     */
    public function scopeByReferenceType($query, $referenceType)
    {
        return $query->where('reference_type', $referenceType);
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include incoming movements
     */
    public function scopeIncoming($query)
    {
        return $query->whereIn('type', ['purchase', 'purchase_receipt', 'production_output', 'return', 'adjustment_in', 'transfer_in']);
    }

    /**
     * Scope a query to only include outgoing movements
     */
    public function scopeOutgoing($query)
    {
        return $query->whereIn('type', ['sale', 'production_consumption', 'adjustment_out', 'transfer_out', 'waste']);
    }

    /**
     * Get movement type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'purchase' => 'Purchase',
            'purchase_receipt' => 'Purchase Receipt',
            'sale' => 'Sale',
            'production_consumption' => 'Production Consumption',
            'production_output' => 'Production Output',
            'production_return' => 'Production Return',
            'return' => 'Return',
            'adjustment_in' => 'Stock Adjustment (In)',
            'adjustment_out' => 'Stock Adjustment (Out)',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out',
            'waste' => 'Waste/Damage',
            'initial' => 'Initial Stock',
            default => 'Unknown',
        };
    }

    /**
     * Get movement direction (in/out)
     */
    public function getDirectionAttribute(): string
    {
        $incomingTypes = ['purchase', 'purchase_receipt', 'production_output', 'return', 'adjustment_in', 'transfer_in', 'initial', 'production_return'];
        
        return in_array($this->type, $incomingTypes) ? 'in' : 'out';
    }

    /**
     * Get stock change amount
     */
    public function getStockChangeAttribute(): float
    {
        return $this->after_stock - $this->before_stock;
    }

    /**
     * Get formatted reference
     */
    public function getReferenceNameAttribute(): string
    {
        if ($this->reference_type === 'raw_material') {
            $material = RawMaterial::find($this->reference_id);
            return $material ? $material->name : 'Unknown Material';
        } elseif ($this->reference_type === 'product') {
            $product = Product::find($this->reference_id);
            return $product ? $product->name : 'Unknown Product';
        } elseif ($this->reference_type === 'production_order') {
            $order = ProductionOrder::find($this->reference_id);
            return $order ? $order->order_number : 'Unknown Order';
        }
        
        return 'Unknown';
    }

    /**
     * Check if movement is positive (incoming)
     */
    public function isIncoming(): bool
    {
        return $this->direction === 'in';
    }

    /**
     * Check if movement is negative (outgoing)
     */
    public function isOutgoing(): bool
    {
        return $this->direction === 'out';
    }

    /**
     * Get movement summary
     */
    public function getSummary(): array
    {
        return [
            'type' => $this->type_label,
            'direction' => $this->direction,
            'reference' => $this->reference_name,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'before_stock' => $this->before_stock,
            'after_stock' => $this->after_stock,
            'stock_change' => $this->stock_change,
            'location' => $this->location,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'created_by' => $this->creator?->name,
        ];
    }

    /**
     * Get stock movements summary for a period
     */
    public static function getPeriodSummary($referenceType = null, $startDate = null, $endDate = null): array
    {
        $query = self::query();

        if ($referenceType) {
            $query->byReferenceType($referenceType);
        }

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $movements = $query->get();

        $incoming = $movements->filter(fn($m) => $m->isIncoming());
        $outgoing = $movements->filter(fn($m) => $m->isOutgoing());

        return [
            'total_movements' => $movements->count(),
            'incoming_count' => $incoming->count(),
            'outgoing_count' => $outgoing->count(),
            'incoming_quantity' => $incoming->sum('quantity'),
            'outgoing_quantity' => $outgoing->sum('quantity'),
            'incoming_value' => $incoming->sum('total_price'),
            'outgoing_value' => $outgoing->sum('total_price'),
            'net_quantity' => $incoming->sum('quantity') - $outgoing->sum('quantity'),
            'net_value' => $incoming->sum('total_price') - $outgoing->sum('total_price'),
            'by_type' => $movements->groupBy('type')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'quantity' => $group->sum('quantity'),
                    'value' => $group->sum('total_price'),
                ];
            }),
        ];
    }

    /**
     * Get stock valuation using FIFO method
     */
    public static function getFIFOValuation($referenceType, $referenceId): array
    {
        $movements = self::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->orderBy('created_at')
            ->get();

        $stock = [];
        $totalValue = 0;
        $totalQuantity = 0;

        foreach ($movements as $movement) {
            if ($movement->isIncoming()) {
                // Add to stock
                $stock[] = [
                    'quantity' => $movement->quantity,
                    'unit_price' => $movement->unit_price,
                    'remaining' => $movement->quantity,
                ];
            } else {
                // Remove from stock using FIFO
                $toRemove = $movement->quantity;
                
                foreach ($stock as &$batch) {
                    if ($toRemove <= 0) break;
                    
                    if ($batch['remaining'] > 0) {
                        $removed = min($batch['remaining'], $toRemove);
                        $batch['remaining'] -= $removed;
                        $toRemove -= $removed;
                    }
                }
            }
        }

        // Calculate current stock value
        foreach ($stock as $batch) {
            if ($batch['remaining'] > 0) {
                $totalQuantity += $batch['remaining'];
                $totalValue += $batch['remaining'] * $batch['unit_price'];
            }
        }

        return [
            'method' => 'FIFO',
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'average_cost' => $totalQuantity > 0 ? $totalValue / $totalQuantity : 0,
            'batches' => array_filter($stock, fn($b) => $b['remaining'] > 0),
        ];
    }
}