<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_number',
        'user_id',
        'order_date',
        'delivery_date',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_cost',
        'total_amount',
        'shipping_address',
        'billing_address',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'order_date' => 'datetime',
        'delivery_date' => 'datetime',
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
            if (empty($order->order_date)) {
                $order->order_date = now();
            }
            if (empty($order->created_by)) {
                $order->created_by = auth()->id();
            }
        });

        static::updating(function ($order) {
            // Calculate totals when amounts change
            if ($order->isDirty(['subtotal', 'tax_amount', 'shipping_cost', 'discount_amount'])) {
                $order->total_amount = $order->subtotal + $order->tax_amount + $order->shipping_cost - $order->discount_amount;
            }

            // Update customer outstanding balance when payment status changes
            if ($order->isDirty('payment_status')) {
                $order->customer->updateOutstandingBalance();
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . date('Ymd');
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
     * Get the customer for this order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the items for this order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the invoice for this order
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get the transaction for this order
     */
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Get the user who created this order
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed orders
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include processing orders
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include delivered orders
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope a query to only include unpaid orders
     */
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    /**
     * Scope a query to only include paid orders
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Check if order can be confirmed
     */
    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending' && $this->items()->count() > 0;
    }

    /**
     * Check if order can be processed
     */
    public function canBeProcessed(): bool
    {
        if ($this->status !== 'confirmed') {
            return false;
        }

        // Check if all products are available
        foreach ($this->items as $item) {
            if ($item->product->current_stock < $item->quantity) {
                return false;
            }
        }

        return true;
    }

    /**
     * Confirm the order
     */
    public function confirm(): bool
    {
        if (!$this->canBeConfirmed()) {
            return false;
        }

        $this->update(['status' => 'confirmed']);

        // Create invoice
        $this->createInvoice();

        return true;
    }

    /**
     * Process the order
     */
    public function process(): bool
    {
        if (!$this->canBeProcessed()) {
            return false;
        }

        // Deduct products from inventory
        foreach ($this->items as $item) {
            $item->product->updateStock($item->quantity, 'subtract');
            
            // Record stock movement
            $item->product->recordStockMovement([
                'type' => 'sale',
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
                'total_price' => $item->total,
                'notes' => "Order: {$this->order_number}",
            ]);
        }

        $this->update(['status' => 'processing']);

        return true;
    }

    /**
     * Complete the order
     */
    public function complete(): bool
    {
        if (!in_array($this->status, ['processing', 'shipped'])) {
            return false;
        }

        $this->update(['status' => 'completed']);

        return true;
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered(): bool
    {
        if ($this->status !== 'shipped') {
            return false;
        }

        $this->update([
            'status' => 'delivered',
            'delivery_date' => now(),
        ]);

        return true;
    }

    /**
     * Cancel the order
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['completed', 'delivered', 'cancelled'])) {
            return false;
        }

        // If order was processing, return products to inventory
        if (in_array($this->status, ['processing', 'shipped'])) {
            foreach ($this->items as $item) {
                $item->product->updateStock($item->quantity, 'add');
                
                // Record stock movement
                $item->product->recordStockMovement([
                    'type' => 'return',
                    'quantity' => $item->quantity,
                    'notes' => "Cancelled Order: {$this->order_number}. Reason: {$reason}",
                ]);
            }
        }

        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . "\nCancelled: " . $reason,
        ]);

        // Cancel related invoice if exists
        if ($this->invoice) {
            $this->invoice->cancel();
        }

        return true;
    }

    /**
     * Calculate and update totals
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('total');
        $taxAmount = $subtotal * 0.1; // 10% tax, adjust as needed
        
        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $subtotal + $taxAmount + $this->shipping_cost - $this->discount_amount,
        ]);
    }

    /**
     * Add item to order
     */
    public function addItem(array $itemData): OrderItem
    {
        $item = $this->items()->create($itemData);
        $this->calculateTotals();
        
        return $item;
    }

    /**
     * Remove item from order
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
     * Create invoice for the order
     */
    public function createInvoice(): Invoice
    {
        if ($this->invoice) {
            return $this->invoice;
        }

        $invoice = Invoice::create([
            'customer_id' => $this->customer_id,
            'order_id' => $this->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'balance_due' => $this->total_amount,
            'status' => 'draft',
            'notes' => "Invoice for Order: {$this->order_number}",
        ]);

        // Copy order items to invoice items
        foreach ($this->items as $orderItem) {
            $invoice->items()->create([
                'product_id' => $orderItem->product_id,
                'description' => $orderItem->product->name,
                'quantity' => $orderItem->quantity,
                'unit_price' => $orderItem->price,
                'total' => $orderItem->total,
            ]);
        }

        return $invoice;
    }

    /**
     * Check if order is overdue for delivery
     */
    public function isOverdueForDelivery(): bool
    {
        return $this->delivery_date && 
               $this->delivery_date < now() && 
               !in_array($this->status, ['delivered', 'completed', 'cancelled']);
    }

    /**
     * Get order profit
     */
    public function getProfitAttribute(): float
    {
        $totalCost = 0;
        $totalRevenue = 0;

        foreach ($this->items as $item) {
            $totalCost += $item->product->cost_price * $item->quantity;
            $totalRevenue += $item->total;
        }

        return $totalRevenue - $totalCost;
    }

    /**
     * Get order profit margin
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->subtotal <= 0) {
            return 0;
        }

        return round(($this->profit / $this->subtotal) * 100, 2);
    }

    /**
     * Get order summary
     */
    public function getSummary(): array
    {
        return [
            'order_number' => $this->order_number,
            'customer' => $this->customer->name,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'total_amount' => $this->total_amount,
            'items_count' => $this->items()->count(),
            'order_date' => $this->order_date->format('Y-m-d H:i'),
            'delivery_date' => $this->delivery_date?->format('Y-m-d'),
            'is_overdue' => $this->isOverdueForDelivery(),
            'profit' => $this->profit,
            'profit_margin' => $this->profit_margin,
        ];
    }
}