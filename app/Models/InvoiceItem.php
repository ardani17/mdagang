<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
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
            $subtotal = $item->quantity * $item->unit_price;
            $item->total = $subtotal - ($item->discount ?? 0);
        });

        static::saved(function ($item) {
            // Update invoice totals when item is saved
            $item->invoice->calculateTotals();
        });

        static::deleted(function ($item) {
            // Update invoice totals when item is deleted
            $item->invoice->calculateTotals();
        });
    }

    /**
     * Get the invoice that owns this item
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
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
        return $this->quantity * $this->unit_price;
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
     * Update unit price
     */
    public function updateUnitPrice(float $price): bool
    {
        if ($price < 0) {
            return false;
        }

        $this->update(['unit_price' => $price]);
        return true;
    }
}