<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'raw_material_id',
        'quantity',
        'unit',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    /**
     * Get the recipe that owns the ingredient
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Get the raw material for this ingredient
     */
    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    /**
     * Get the cost of this ingredient
     */
    public function getCostAttribute(): float
    {
        return $this->quantity * $this->rawMaterial->average_price;
    }

    /**
     * Check if ingredient is available in required quantity
     */
    public function isAvailable(float $multiplier = 1): bool
    {
        $requiredQuantity = $this->quantity * $multiplier;
        return $this->rawMaterial->current_stock >= $requiredQuantity;
    }

    /**
     * Get shortage quantity if any
     */
    public function getShortage(float $multiplier = 1): float
    {
        $requiredQuantity = $this->quantity * $multiplier;
        $shortage = $requiredQuantity - $this->rawMaterial->current_stock;
        return max(0, $shortage);
    }

    /**
     * Calculate percentage of total recipe cost
     */
    public function getCostPercentageAttribute(): float
    {
        if (!$this->recipe || $this->recipe->total_cost <= 0) {
            return 0;
        }

        return round(($this->cost / $this->recipe->total_cost) * 100, 2);
    }
}