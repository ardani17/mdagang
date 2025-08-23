<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'tax_id',
        'payment_terms',
        'lead_time_days',
        'minimum_order_value',
        'rating',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lead_time_days' => 'integer',
        'minimum_order_value' => 'decimal:2',
        'rating' => 'decimal:1',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->code)) {
                $supplier->code = self::generateCode();
            }
        });
    }

    /**
     * Generate unique supplier code
     */
    public static function generateCode(): string
    {
        $prefix = 'SUP';
        $lastSupplier = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastSupplier) {
            $lastNumber = intval(substr($lastSupplier->code, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the raw materials supplied by this supplier
     */
    public function rawMaterials(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class, 'supplier_raw_materials')
            ->withPivot('price', 'lead_time_days', 'minimum_order_quantity')
            ->withTimestamps();
    }

    /**
     * Get the purchase orders for this supplier
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the payments to this supplier
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include active suppliers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include top-rated suppliers
     */
    public function scopeTopRated($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Get total purchase amount from this supplier
     */
    public function getTotalPurchaseAmountAttribute(): float
    {
        return $this->purchaseOrders()
            ->whereIn('status', ['received', 'partial'])
            ->sum('total_amount');
    }

    /**
     * Get pending purchase orders count
     */
    public function getPendingOrdersCountAttribute(): int
    {
        return $this->purchaseOrders()
            ->whereIn('status', ['pending', 'approved', 'ordered'])
            ->count();
    }

    /**
     * Calculate average delivery time
     */
    public function calculateAverageDeliveryTime(): ?float
    {
        $completedOrders = $this->purchaseOrders()
            ->whereNotNull('received_date')
            ->get();

        if ($completedOrders->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($completedOrders as $order) {
            if ($order->order_date && $order->received_date) {
                $days = $order->order_date->diffInDays($order->received_date);
                $totalDays += $days;
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 1) : null;
    }

    /**
     * Update supplier rating based on performance
     */
    public function updateRating(): void
    {
        $factors = [
            'delivery_performance' => $this->calculateDeliveryPerformance(),
            'quality_score' => $this->calculateQualityScore(),
            'price_competitiveness' => $this->calculatePriceCompetitiveness(),
        ];

        // Weighted average calculation
        $weights = [
            'delivery_performance' => 0.4,
            'quality_score' => 0.4,
            'price_competitiveness' => 0.2,
        ];

        $rating = 0;
        foreach ($factors as $factor => $score) {
            if ($score !== null) {
                $rating += $score * $weights[$factor];
            }
        }

        $this->update(['rating' => round($rating, 1)]);
    }

    /**
     * Calculate delivery performance score
     */
    private function calculateDeliveryPerformance(): ?float
    {
        $orders = $this->purchaseOrders()
            ->whereNotNull('received_date')
            ->get();

        if ($orders->isEmpty()) {
            return null;
        }

        $onTimeCount = 0;
        foreach ($orders as $order) {
            if ($order->received_date <= $order->expected_date) {
                $onTimeCount++;
            }
        }

        return ($onTimeCount / $orders->count()) * 5;
    }

    /**
     * Calculate quality score based on inspections
     */
    private function calculateQualityScore(): ?float
    {
        // This would be calculated based on quality inspection results
        // For now, return a default value
        return 4.0;
    }

    /**
     * Calculate price competitiveness
     */
    private function calculatePriceCompetitiveness(): ?float
    {
        // This would compare prices with other suppliers
        // For now, return a default value
        return 3.5;
    }
}