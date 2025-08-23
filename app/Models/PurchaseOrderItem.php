<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'raw_material_id',
        'item_name',
        'item_code',
        'quantity',
        'received_quantity',
        'unit',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Calculate total price
            $item->total_price = $item->quantity * $item->unit_price;
        });

        static::saved(function ($item) {
            // Update purchase order totals when item is saved
            $item->purchaseOrder->calculateTotals();
        });

        static::deleted(function ($item) {
            // Update purchase order totals when item is deleted
            $item->purchaseOrder->calculateTotals();
        });
    }

    /**
     * Get the purchase order that owns this item
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the raw material for this item
     */
    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    /**
     * Get remaining quantity to be received
     */
    public function getRemainingQuantityAttribute(): float
    {
        return max(0, $this->quantity - $this->received_quantity);
    }

    /**
     * Check if item is fully received
     */
    public function isFullyReceived(): bool
    {
        return $this->received_quantity >= $this->quantity;
    }

    /**
     * Check if item is partially received
     */
    public function isPartiallyReceived(): bool
    {
        return $this->received_quantity > 0 && $this->received_quantity < $this->quantity;
    }

    /**
     * Get receipt status
     */
    public function getReceiptStatusAttribute(): string
    {
        if ($this->received_quantity <= 0) {
            return 'pending';
        } elseif ($this->isFullyReceived()) {
            return 'received';
        } else {
            return 'partial';
        }
    }

    /**
     * Get receipt percentage
     */
    public function getReceiptPercentageAttribute(): float
    {
        if ($this->quantity <= 0) {
            return 0;
        }

        return min(100, round(($this->received_quantity / $this->quantity) * 100, 2));
    }

    /**
     * Record receipt of items
     */
    public function receiveItems(float $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $newReceivedQuantity = min($this->quantity, $this->received_quantity + $quantity);
        $actualReceived = $newReceivedQuantity - $this->received_quantity;

        if ($actualReceived > 0) {
            // Update received quantity
            $this->update(['received_quantity' => $newReceivedQuantity]);

            // Update raw material stock
            $this->rawMaterial->updateStock($actualReceived, 'add');

            // Record stock movement
            $this->rawMaterial->recordStockMovement([
                'type' => 'purchase_receipt',
                'quantity' => $actualReceived,
                'unit_price' => $this->unit_price,
                'total_price' => $actualReceived * $this->unit_price,
                'notes' => "Received from PO: {$this->purchaseOrder->po_number}",
            ]);

            return true;
        }

        return false;
    }

    /**
     * Get variance from supplier's standard price
     */
    public function getPriceVarianceAttribute(): ?float
    {
        $supplierPrice = $this->purchaseOrder->supplier
            ->rawMaterials()
            ->where('raw_material_id', $this->raw_material_id)
            ->first()
            ?->pivot
            ?->price;

        if (!$supplierPrice) {
            return null;
        }

        return $this->unit_price - $supplierPrice;
    }

    /**
     * Get variance percentage from supplier's standard price
     */
    public function getPriceVariancePercentageAttribute(): ?float
    {
        $supplierPrice = $this->purchaseOrder->supplier
            ->rawMaterials()
            ->where('raw_material_id', $this->raw_material_id)
            ->first()
            ?->pivot
            ?->price;

        if (!$supplierPrice || $supplierPrice == 0) {
            return null;
        }

        return round((($this->unit_price - $supplierPrice) / $supplierPrice) * 100, 2);
    }
}