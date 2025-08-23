<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'discount',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Calculate total
            $subtotal = $item->quantity * $item->price;
            $item->total = $subtotal - ($item->discount ?? 0);
        });

        static::saved(function ($item) {
            // Update order totals when item is saved
            $item->order->calculateTotals();
        });

        static::deleted(function ($item) {
            // Update order totals when item is deleted
            $item->order->calculateTotals();
        });
    }

    /**
     * Get the order that owns this item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get subtotal before discount
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): float
    {
        if ($this->subtotal <= 0) {
            return 0;
        }

        return round(($this->discount / $this->subtotal) * 100, 2);
    }

    /**
     * Get item profit
     */
    public function getProfitAttribute(): float
    {
        $cost = $this->product->cost_price * $this->quantity;
        return $this->total - $cost;
    }

    /**
     * Get item profit margin
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->total <= 0) {
            return 0;
        }

        return round(($this->profit / $this->total) * 100, 2);
    }

    /**
     * Check if product is available in required quantity
     */
    public function isAvailable(): bool
    {
        return $this->product->current_stock >= $this->quantity;
    }

    /**
     * Get shortage quantity if any
     */
    public function getShortageAttribute(): float
    {
        $shortage = $this->quantity - $this->product->current_stock;
        return max(0, $shortage);
    }

    /**
     * Apply discount
     */
    public function applyDiscount(float $amount = null, float $percentage = null): void
    {
        if ($percentage !== null) {
            $this->discount = $this->subtotal * ($percentage / 100);
        } elseif ($amount !== null) {
            $this->discount = min($amount, $this->subtotal);
        }

        $this->save();
    }

    /**
     * Update quantity
     */
    public function updateQuantity(float $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $this->update(['quantity' => $quantity]);
        return true;
    }

    /**
     * Update price
     */
    public function updatePrice(float $price): bool
    {
        if ($price < 0) {
            return false;
        }

        $this->update(['price' => $price]);
        return true;
    }
}