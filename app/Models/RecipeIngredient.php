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
        // Gunakan eager loading untuk menghindari N+1 query
        if (!$this->relationLoaded('rawMaterial')) {
            $this->load('rawMaterial');
        }
        
        if (!$this->rawMaterial) {
            return 0;
        }
        
        return round($this->quantity * ($this->rawMaterial->average_price ?? 0), 2);
    }

    /**
     * Check if ingredient is available in required quantity
     */
    public function isAvailable(float $multiplier = 1): bool
    {
        if (!$this->rawMaterial) {
            return false;
        }
        
        $requiredQuantity = $this->quantity * $multiplier;
        return $this->rawMaterial->current_stock >= $requiredQuantity;
    }

    /**
     * Get shortage quantity if any
     */
    public function getShortage(float $multiplier = 1): float
    {
        if (!$this->rawMaterial) {
            return $this->quantity * $multiplier;
        }
        
        $requiredQuantity = $this->quantity * $multiplier;
        $shortage = $requiredQuantity - $this->rawMaterial->current_stock;
        return max(0, round($shortage, 3));
    }

    /**
     * Calculate percentage of total recipe cost
     */
    public function getCostPercentageAttribute(): ?float
    {
        // Hindari circular dependency dengan tidak memanggil recipe->total_cost
        // Biarkan controller yang menghitung persentase berdasarkan context
        return null;
    }

    /**
     * Alternative method untuk menghitung cost percentage
     * Harus dipanggil dengan menyediakan total cost secara explicit
     */
    public function calculateCostPercentage(float $totalCost): float
    {
        if ($totalCost <= 0) {
            return 0;
        }
        
        $ingredientCost = $this->cost;
        return round(($ingredientCost / $totalCost) * 100, 2);
    }

    /**
     * Scope untuk eager load relationships yang diperlukan
     */
    public function scopeWithCost($query)
    {
        return $query->with(['rawMaterial' => function ($query) {
            $query->select('id', 'average_price', 'current_stock');
        }]);
    }
}