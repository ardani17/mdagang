<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'name',
        'version',
        'description',
        'yield_quantity',
        'yield_unit',
        'production_time',
        'preparation_time',
        'total_cost',
        'labor_cost',
        'overhead_cost',
        'instructions',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'yield_quantity' => 'decimal:2',
        'production_time' => 'integer',
        'preparation_time' => 'integer',
        'total_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'overhead_cost' => 'decimal:2',
        'instructions' => 'array',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($recipe) {
            if (empty($recipe->code)) {
                $recipe->code = self::generateCode();
            }
            if (empty($recipe->version)) {
                $recipe->version = '1.0';
            }
        });

        static::updating(function ($recipe) {
            // Recalculate total cost when recipe is updated
            $recipe->total_cost = $recipe->calculateTotalCost();
        });
    }

    /**
     * Generate unique recipe code
     */
    public static function generateCode(): string
    {
        $prefix = 'RCP';
        $lastRecipe = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastRecipe) {
            $lastNumber = intval(substr($lastRecipe->code, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the product that owns the recipe
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the ingredients for this recipe
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(RawMaterial::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the recipe ingredients with details
     */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /**
     * Get the production orders using this recipe
     */
    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }

    /**
     * Scope a query to only include active recipes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to get latest version of recipes
     */
    public function scopeLatestVersion($query)
    {
        return $query->whereIn('id', function ($subquery) {
            $subquery->selectRaw('MAX(id)')
                ->from('recipes')
                ->groupBy('product_id');
        });
    }

    /**
     * Calculate total cost of the recipe
     */
    public function calculateTotalCost(): float
    {
        $ingredientsCost = 0;

        foreach ($this->ingredients as $ingredient) {
            $ingredientsCost += $ingredient->pivot->quantity * $ingredient->average_price;
        }

        $totalCost = $ingredientsCost + ($this->labor_cost ?? 0) + ($this->overhead_cost ?? 0);

        return round($totalCost, 2);
    }

    /**
     * Calculate cost per unit
     */
    public function getCostPerUnitAttribute(): float
    {
        if ($this->yield_quantity <= 0) {
            return 0;
        }

        return round($this->total_cost / $this->yield_quantity, 2);
    }

    /**
     * Get total production time in minutes
     */
    public function getTotalTimeAttribute(): int
    {
        return ($this->preparation_time ?? 0) + ($this->production_time ?? 0);
    }

    /**
     * Check if all ingredients are available for production
     */
    public function checkIngredientsAvailability(float $multiplier = 1): array
    {
        $availability = [
            'available' => true,
            'missing_ingredients' => [],
            'insufficient_ingredients' => [],
        ];

        foreach ($this->ingredients as $ingredient) {
            $requiredQuantity = $ingredient->pivot->quantity * $multiplier;
            
            if (!$ingredient->is_active) {
                $availability['available'] = false;
                $availability['missing_ingredients'][] = [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'status' => 'inactive',
                ];
            } elseif ($ingredient->current_stock < $requiredQuantity) {
                $availability['available'] = false;
                $availability['insufficient_ingredients'][] = [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'required' => $requiredQuantity,
                    'available' => $ingredient->current_stock,
                    'shortage' => $requiredQuantity - $ingredient->current_stock,
                ];
            }
        }

        return $availability;
    }

    /**
     * Calculate maximum producible quantity based on available ingredients
     */
    public function calculateMaxProducibleQuantity(): float
    {
        $maxQuantity = PHP_FLOAT_MAX;

        foreach ($this->ingredients as $ingredient) {
            if ($ingredient->pivot->quantity > 0) {
                $possibleQuantity = floor($ingredient->current_stock / $ingredient->pivot->quantity);
                $maxQuantity = min($maxQuantity, $possibleQuantity);
            }
        }

        return $maxQuantity === PHP_FLOAT_MAX ? 0 : $maxQuantity;
    }

    /**
     * Clone recipe to create a new version
     */
    public function createNewVersion(array $changes = []): Recipe
    {
        $newRecipe = $this->replicate();
        
        // Increment version
        $versionParts = explode('.', $this->version);
        $versionParts[count($versionParts) - 1]++;
        $newRecipe->version = implode('.', $versionParts);
        
        // Apply any changes
        foreach ($changes as $key => $value) {
            $newRecipe->$key = $value;
        }
        
        $newRecipe->save();

        // Copy ingredients
        foreach ($this->ingredients as $ingredient) {
            $newRecipe->ingredients()->attach($ingredient->id, [
                'quantity' => $ingredient->pivot->quantity,
                'unit' => $ingredient->pivot->unit,
                'notes' => $ingredient->pivot->notes,
            ]);
        }

        // Deactivate old version
        $this->update(['is_active' => false]);

        return $newRecipe;
    }

    /**
     * Get recipe efficiency metrics
     */
    public function getEfficiencyMetrics(): array
    {
        $productionOrders = $this->productionOrders()
            ->where('status', 'completed')
            ->get();

        if ($productionOrders->isEmpty()) {
            return [
                'average_actual_time' => null,
                'average_actual_cost' => null,
                'average_yield_variance' => null,
                'success_rate' => null,
            ];
        }

        $totalTime = 0;
        $totalCost = 0;
        $totalYieldVariance = 0;
        $successCount = 0;

        foreach ($productionOrders as $order) {
            $totalTime += $order->actual_production_time ?? $this->total_time;
            $totalCost += $order->actual_cost ?? $this->total_cost;
            
            if ($order->actual_quantity && $order->quantity) {
                $yieldVariance = (($order->actual_quantity - $order->quantity) / $order->quantity) * 100;
                $totalYieldVariance += $yieldVariance;
            }

            if ($order->qualityInspection && $order->qualityInspection->passed) {
                $successCount++;
            }
        }

        $count = $productionOrders->count();

        return [
            'average_actual_time' => round($totalTime / $count, 0),
            'average_actual_cost' => round($totalCost / $count, 2),
            'average_yield_variance' => round($totalYieldVariance / $count, 2),
            'success_rate' => round(($successCount / $count) * 100, 2),
        ];
    }

    /**
     * Get nutritional information (if applicable)
     */
    public function getNutritionalInfo(): array
    {
        // This would aggregate nutritional data from ingredients
        // For now, return empty array
        return [];
    }

    /**
     * Validate recipe completeness
     */
    public function isComplete(): bool
    {
        return !empty($this->name) &&
               !empty($this->product_id) &&
               $this->yield_quantity > 0 &&
               !empty($this->instructions) &&
               $this->ingredients()->count() > 0;
    }

    /**
     * Get recipe complexity level
     */
    public function getComplexityLevelAttribute(): string
    {
        $ingredientCount = $this->ingredients()->count();
        $totalTime = $this->total_time;

        if ($ingredientCount <= 3 && $totalTime <= 30) {
            return 'simple';
        } elseif ($ingredientCount <= 7 && $totalTime <= 60) {
            return 'moderate';
        } else {
            return 'complex';
        }
    }

    /**
     * Update recipe cost
     */
    public function updateCost(): void
    {
        $this->total_cost = $this->calculateTotalCost();
        $this->save();
    }
}