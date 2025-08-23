<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'po_number',
        'order_date',
        'expected_date',
        'received_date',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'total_amount',
        'payment_terms',
        'payment_status',
        'reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->po_number)) {
                $order->po_number = self::generateOrderNumber();
            }
            if (empty($order->order_date)) {
                $order->order_date = now();
            }
            if (empty($order->created_by)) {
                $order->created_by = auth()->id();
            }
        });

        static::updating(function ($order) {
            // Calculate totals when items change
            if ($order->isDirty(['subtotal', 'tax_amount', 'shipping_cost'])) {
                $order->total_amount = $order->subtotal + $order->tax_amount + $order->shipping_cost;
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'PUR-' . date('Ym');
        $lastOrder = self::where('po_number', 'like', $prefix . '%')
            ->orderBy('po_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->po_number, -5));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the supplier for this purchase order
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the items for this purchase order
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get the user who created this order
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this order
     */

    /**
     * Scope a query to only include pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include approved orders
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope a query to only include received orders
     */
    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    /**
     * Scope a query to only include unpaid orders
     */
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    /**
     * Scope a query to only include overdue orders
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'received')
            ->where('expected_date', '<', now());
    }

    /**
     * Check if order is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'received' && 
               $this->status !== 'cancelled' &&
               $this->expected_date && 
               $this->expected_date < now();
    }

    /**
     * Check if order can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'draft' && $this->items()->count() > 0;
    }

    /**
     * Approve the purchase order
     */
    public function approve(int $userId = null): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'sent',
        ]);

        return true;
    }

    /**
     * Mark order as sent
     */
    public function markAsSent(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $this->update(['status' => 'sent']);

        return true;
    }

    /**
     * Mark order as partially received
     */
    public function markAsPartiallyReceived(array $receivedItems = []): bool
    {
        if (!in_array($this->status, ['confirmed', 'partial'])) {
            return false;
        }

        $this->update(['status' => 'partial']);

        // Update received quantities for items
        foreach ($receivedItems as $itemId => $quantity) {
            $item = $this->items()->find($itemId);
            if ($item) {
                $item->increment('received_quantity', $quantity);
            }
        }

        return true;
    }

    /**
     * Mark order as fully received
     */
    public function markAsReceived(array $receivedItems = []): bool
    {
        if (!in_array($this->status, ['confirmed', 'partial'])) {
            return false;
        }

        // Update inventory for each item
        foreach ($this->items as $item) {
            $receivedQty = $receivedItems[$item->id] ?? $item->quantity;
            
            // Update raw material stock
            $item->rawMaterial->updateStock($receivedQty, 'add');
            
            // Update last purchase price
            $item->rawMaterial->update(['last_purchase_price' => $item->unit_price]);
            
            // Record stock movement
            $item->rawMaterial->recordStockMovement([
                'movement_type' => 'in',
                'quantity' => $receivedQty,
                'unit_cost' => $item->unit_price,
                'total_cost' => $receivedQty * $item->unit_price,
                'reference_type' => 'purchase_order',
                'reference_id' => $this->id,
                'reason' => 'purchase',
                'notes' => "Purchase Order: {$this->po_number}",
                'performed_by' => auth()->id(),
            ]);
            
            // Update received quantity
            $item->update(['received_quantity' => $receivedQty]);
        }

        $this->update([
            'status' => 'received',
            'received_date' => now(),
        ]);

        // Update supplier's last order date if method exists
        if (method_exists($this->supplier, 'updateLastOrderDate')) {
            $this->supplier->updateLastOrderDate();
        }

        return true;
    }

    /**
     * Cancel the purchase order
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['received', 'cancelled'])) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'notes' => ($this->notes ?? '') . "\nCancelled: " . $reason,
        ]);

        return true;
    }

    /**
     * Calculate and update totals
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('total_price');
        // Don't recalculate tax_amount here - it should be set from the frontend
        // based on the user's input tax percentage
        
        $this->update([
            'subtotal' => $subtotal,
            // Keep existing tax_amount, don't overwrite it
            'total_amount' => $subtotal + $this->tax_amount + $this->shipping_cost,
        ]);
    }

    /**
     * Add item to purchase order
     */
    public function addItem(array $itemData): PurchaseOrderItem
    {
        $item = $this->items()->create($itemData);
        $this->calculateTotals();
        
        return $item;
    }

    /**
     * Remove item from purchase order
     */
    public function removeItem(int $itemId): bool
    {
        $deleted = $this->items()->where('id', $itemId)->delete();
        
        if ($deleted) {
            $this->calculateTotals();
        }
        
        return $deleted > 0;
    }

    /**
     * Get delivery performance
     */
    public function getDeliveryPerformanceAttribute(): ?string
    {
        if (!$this->received_date || !$this->expected_date) {
            return null;
        }

        $daysDifference = $this->expected_date->diffInDays($this->received_date, false);

        if ($daysDifference <= 0) {
            return 'on_time';
        } elseif ($daysDifference <= 2) {
            return 'slightly_late';
        } else {
            return 'late';
        }
    }

    /**
     * Get payment due date
     */
    public function getPaymentDueDateAttribute(): ?string
    {
        if (!$this->received_date || !$this->payment_terms) {
            return null;
        }

        $days = intval(preg_replace('/[^0-9]/', '', $this->payment_terms));
        
        return $this->received_date->addDays($days);
    }

    /**
     * Check if payment is overdue
     */
    public function isPaymentOverdue(): bool
    {
        return $this->payment_status === 'unpaid' && 
               $this->payment_due_date && 
               $this->payment_due_date < now();
    }

    /**
     * Get order summary
     */
    public function getSummary(): array
    {
        return [
            'po_number' => $this->po_number,
            'supplier' => $this->supplier->name,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'total_amount' => $this->total_amount,
            'items_count' => $this->items()->count(),
            'order_date' => $this->order_date->format('Y-m-d'),
            'expected_date' => $this->expected_date?->format('Y-m-d'),
            'received_date' => $this->received_date?->format('Y-m-d'),
            'is_overdue' => $this->isOverdue(),
            'is_payment_overdue' => $this->isPaymentOverdue(),
            'delivery_performance' => $this->delivery_performance,
        ];
    }
}