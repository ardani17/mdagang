<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    public function data(Request $request)
{
    $query = Recipe::with(['product'])
        ->latest();

    // Search filter
    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('code', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
        });
    }

    // Status filter
    if ($request->status === 'active') {
        $query->where('is_active', true);
    } elseif ($request->status === 'inactive') {
        $query->where('is_active', false);
    }

    // Category filter
    if ($request->category) {
        $query->where('category', $request->category);
    }

    $recipes = $query->paginate(10);

    return response()->json([
        'success' => true,
        'data' => $recipes
    ]);
}

public function stats()
{
    $stats = [
        'total_recipes' => Recipe::count(),
        'active_recipes' => Recipe::where('is_active', true)->count(),
        'average_cost' => Recipe::avg('total_cost') ?? 0,
        'total_ingredients' => DB::table('recipe_ingredients')->count()
    ];

    return response()->json([
        'success' => true,
        'data' => $stats
    ]);
}

    /**
     * Display a listing of recipes
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // ingredients.rawMaterial
            $query = Recipe::with(['product', 'ingredients']);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by product
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            // Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $recipes = $query->paginate($perPage);

            // Add calculated fields
            foreach ($recipes as $recipe) {
                $recipe->total_cost = $recipe->calculateTotalCost();
                $recipe->cost_per_unit = $recipe->calculateCostPerUnit();
                
                // Tambahkan data tambahan untuk response
                $recipe->ingredients_count = $recipe->ingredients->count();
                $recipe->total_production_time = $recipe->total_time;
            }

            return $this->successResponse($recipes, 'Recipes retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve recipes: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created recipe
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:recipes',
            'description' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'batch_size' => 'required|numeric|min:1',
            'unit' => 'required|string|max:50',
            'production_time' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
            'notes' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.raw_material_id' => 'required|exists:raw_materials,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.unit' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            // Create recipe
            $recipeData = $request->except('ingredients');
            $recipe = Recipe::create($recipeData);
            
            // Create recipe ingredients
            foreach ($request->ingredients as $ingredient) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'raw_material_id' => $ingredient['raw_material_id'],
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit'],
                ]);
            }

            // Calculate and update costs
            $recipe->total_cost = $recipe->calculateTotalCost();
            $recipe->cost_per_unit = $recipe->calculateCostPerUnit();
            $recipe->save();

            // Log activity
            ActivityLog::logCreation($recipe, 'Created new recipe: ' . $recipe->name);

            DB::commit();
            return $this->successResponse(
                $recipe->load(['product', 'ingredients.rawMaterial']), 
                'Recipe created successfully', 
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return $this->errorResponse('Failed to create recipe: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified recipe
     */
    public function show($id): JsonResponse
    {
        try {
            $recipe = Recipe::with([
                'product',
                'ingredients.rawMaterial.supplier',
                'productionOrders' => function ($query) {
                    $query->latest()->limit(5);
                }
            ])->findOrFail($id);

            // Add calculated fields and statistics
            $recipe->total_cost = $recipe->calculateTotalCost();
            $recipe->cost_per_unit = $recipe->calculateCostPerUnit();
            $recipe->statistics = [
                'total_productions' => $recipe->productionOrders()->count(),
                'completed_productions' => $recipe->productionOrders()->where('status', 'completed')->count(),
                'total_quantity_produced' => $recipe->productionOrders()
                    ->where('status', 'completed')
                    ->sum('quantity_produced'),
                'average_production_time' => $recipe->productionOrders()
                    ->where('status', 'completed')
                    ->avg('actual_production_time'),
                'material_availability' => $this->checkMaterialAvailability($recipe),
            ];

            return $this->successResponse($recipe, 'Recipe retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Recipe not found', 404);
        }
    }

    /**
     * Update the specified recipe
     */
    public function update(Request $request, $id): JsonResponse
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->errorResponse('Recipe not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:50|unique:recipes,code,' . $id,
            'description' => 'nullable|string',
            'product_id' => 'sometimes|required|exists:products,id',
            'batch_size' => 'sometimes|required|numeric|min:1',
            'unit' => 'sometimes|required|string|max:50',
            'production_time' => 'sometimes|required|integer|min:1',
            'instructions' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|required|boolean',
            'ingredients' => 'sometimes|required|array|min:1',
            'ingredients.*.raw_material_id' => 'required_with:ingredients|exists:raw_materials,id',
            'ingredients.*.quantity' => 'required_with:ingredients|numeric|min:0',
            'ingredients.*.unit' => 'required_with:ingredients|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $oldValues = $recipe->toArray();
            
            // Update recipe
            $recipeData = $request->except('ingredients');
            $recipe->update($recipeData);

            // Update ingredients if provided
            if ($request->has('ingredients')) {
                // Delete old ingredients
                $recipe->ingredients()->delete();

                // Create new ingredients
                foreach ($request->ingredients as $ingredient) {
                    RecipeIngredient::create([
                        'recipe_id' => $recipe->id,
                        'raw_material_id' => $ingredient['raw_material_id'],
                        'quantity' => $ingredient['quantity'],
                        'unit' => $ingredient['unit'],
                    ]);
                }
            }

            // Recalculate costs
            $recipe->total_cost = $recipe->calculateTotalCost();
            $recipe->cost_per_unit = $recipe->calculateCostPerUnit();
            $recipe->save();

            // Log activity
            $changes = array_diff_assoc($recipeData, $oldValues);
            if (!empty($changes) || $request->has('ingredients')) {
                ActivityLog::logUpdate($recipe, $changes, 'Updated recipe: ' . $recipe->name);
            }

            DB::commit();
            return $this->successResponse(
                $recipe->load(['product', 'ingredients.rawMaterial']), 
                'Recipe updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update recipe: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified recipe
     */
    public function destroy($id): JsonResponse
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->errorResponse('Recipe not found', 404);
        }

        // Check if recipe has production orders
        if ($recipe->productionOrders()->exists()) {
            return $this->errorResponse('Cannot delete recipe with existing production orders', 422);
        }

        DB::beginTransaction();
        try {
            // Log activity before deletion
            ActivityLog::logDeletion($recipe, 'Deleted recipe: ' . $recipe->name);
            
            // Delete ingredients first
            $recipe->ingredients()->delete();
            
            // Delete recipe
            $recipe->delete();
            
            DB::commit();
            return $this->successResponse(null, 'Recipe deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete recipe: ' . $e->getMessage());
        }
    }

    /**
     * Calculate recipe cost
     */
    public function calculateCost($id): JsonResponse
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->errorResponse('Recipe not found', 404);
        }

        try {
            $recipe->load('ingredients.rawMaterial');
            
            $breakdown = [];
            $totalCost = 0;

            foreach ($recipe->ingredients as $ingredient) {
                $cost = $ingredient->quantity * $ingredient->rawMaterial->unit_cost;
                $totalCost += $cost;
                
                $breakdown[] = [
                    'material' => $ingredient->rawMaterial->name,
                    'quantity' => $ingredient->quantity,
                    'unit' => $ingredient->unit,
                    'unit_cost' => $ingredient->rawMaterial->unit_cost,
                    'total_cost' => $cost,
                    'percentage' => 0, // Will be calculated after total
                ];
            }

            // Calculate percentages
            foreach ($breakdown as &$item) {
                $item['percentage'] = $totalCost > 0 ? round(($item['total_cost'] / $totalCost) * 100, 2) : 0;
            }

            $result = [
                'recipe_name' => $recipe->name,
                'batch_size' => $recipe->batch_size,
                'unit' => $recipe->unit,
                'total_cost' => $totalCost,
                'cost_per_unit' => $recipe->batch_size > 0 ? $totalCost / $recipe->batch_size : 0,
                'breakdown' => $breakdown,
                'production_time' => $recipe->production_time,
                'suggested_selling_price' => $totalCost * 2.5, // 150% markup
            ];

            return $this->successResponse($result, 'Recipe cost calculated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to calculate cost: ' . $e->getMessage());
        }
    }

    /**
     * Check material availability for recipe
     */
    public function checkAvailability($id): JsonResponse
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->errorResponse('Recipe not found', 404);
        }

        try {
            $availability = $this->checkMaterialAvailability($recipe);
            
            return $this->successResponse($availability, 'Material availability checked successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to check availability: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a recipe
     */
    public function duplicate($id): JsonResponse
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->errorResponse('Recipe not found', 404);
        }

        DB::beginTransaction();
        try {
            // Create new recipe
            $newRecipe = $recipe->replicate();
            $newRecipe->name = $recipe->name . ' (Copy)';
            $newRecipe->code = $recipe->code . '-COPY-' . time();
            $newRecipe->is_active = false;
            $newRecipe->save();

            // Copy ingredients
            foreach ($recipe->ingredients as $ingredient) {
                RecipeIngredient::create([
                    'recipe_id' => $newRecipe->id,
                    'raw_material_id' => $ingredient->raw_material_id,
                    'quantity' => $ingredient->quantity,
                    'unit' => $ingredient->unit,
                ]);
            }

            // Calculate costs
            $newRecipe->total_cost = $newRecipe->calculateTotalCost();
            $newRecipe->cost_per_unit = $newRecipe->calculateCostPerUnit();
            $newRecipe->save();

            // Log activity
            ActivityLog::logCreation($newRecipe, 'Duplicated recipe from: ' . $recipe->name);

            DB::commit();
            return $this->successResponse(
                $newRecipe->load(['product', 'ingredients.rawMaterial']), 
                'Recipe duplicated successfully', 
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to duplicate recipe: ' . $e->getMessage());
        }
    }

    /**
     * Toggle recipe status
     */
    public function toggleStatus($id): JsonResponse
    {
        $recipe = Recipe::find($id);
        if (!$recipe) {
            return $this->errorResponse('Recipe not found', 404);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $recipe->is_active;
            $recipe->is_active = !$recipe->is_active;
            $recipe->save();

            // Log activity
            ActivityLog::logUpdate(
                $recipe, 
                ['is_active' => ['old' => $oldStatus, 'new' => $recipe->is_active]], 
                'Changed recipe status: ' . $recipe->name
            );

            DB::commit();
            return $this->successResponse($recipe, 'Recipe status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update recipe status: ' . $e->getMessage());
        }
    }

    /**
     * Get recipe statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_recipes' => Recipe::count(),
                'active_recipes' => Recipe::where('is_active', true)->count(),
                'inactive_recipes' => Recipe::where('is_active', false)->count(),
                'average_ingredients_per_recipe' => RecipeIngredient::selectRaw('COUNT(*) / COUNT(DISTINCT recipe_id) as avg')
                    ->first()->avg ?? 0,
                'most_used_materials' => RecipeIngredient::selectRaw('raw_material_id, COUNT(*) as usage_count')
                    ->with('rawMaterial:id,name')
                    ->groupBy('raw_material_id')
                    ->orderBy('usage_count', 'desc')
                    ->limit(5)
                    ->get(),
                'recipes_by_product' => Recipe::selectRaw('product_id, COUNT(*) as count')
                    ->with('product:id,name')
                    ->groupBy('product_id')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Recipe statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Export recipes to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $recipes = Recipe::with(['product', 'ingredients.rawMaterial'])->get();
            
            $csvData = [];
            $csvData[] = ['ID', 'Code', 'Name', 'Product', 'Batch Size', 'Unit', 'Total Cost', 'Cost Per Unit', 'Production Time', 'Status'];
            
            foreach ($recipes as $recipe) {
                $csvData[] = [
                    $recipe->id,
                    $recipe->code,
                    $recipe->name,
                    $recipe->product->name,
                    $recipe->batch_size,
                    $recipe->unit,
                    $recipe->calculateTotalCost(),
                    $recipe->calculateCostPerUnit(),
                    $recipe->production_time,
                    $recipe->is_active ? 'Active' : 'Inactive',
                ];
            }

            // In a real application, you would generate and return a CSV file
            // For now, we'll just return the data
            return $this->successResponse($csvData, 'Recipes exported successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export recipes: ' . $e->getMessage());
        }
    }

    /**
     * Check material availability for a recipe
     */
    private function checkMaterialAvailability($recipe)
    {
        $recipe->load('ingredients.rawMaterial');
        
        $availability = [
            'can_produce' => true,
            'max_batches' => PHP_INT_MAX,
            'materials' => [],
        ];

        foreach ($recipe->ingredients as $ingredient) {
            $material = $ingredient->rawMaterial;
            $required = $ingredient->quantity;
            $available = $material->current_stock;
            $batches = $required > 0 ? floor($available / $required) : 0;

            $materialStatus = [
                'name' => $material->name,
                'required' => $required,
                'available' => $available,
                'unit' => $ingredient->unit,
                'sufficient' => $available >= $required,
                'max_batches' => $batches,
            ];

            if (!$materialStatus['sufficient']) {
                $availability['can_produce'] = false;
            }

            if ($batches < $availability['max_batches']) {
                $availability['max_batches'] = $batches;
            }

            $availability['materials'][] = $materialStatus;
        }

        return $availability;
    }

    public function getRecipeDetails($id): JsonResponse
{
    try {
        $recipe = Recipe::with([
            'product',
            'ingredients' => function ($query) {
                $query->select([
                    'raw_materials.id',
                    'raw_materials.name as material_name',
                    'raw_materials.code as material_code', // Ganti sku dengan code
                    'raw_materials.average_price as unit_cost',
                    'raw_materials.unit',
                    'recipe_ingredients.quantity',
                    'recipe_ingredients.unit as recipe_unit',
                    'recipe_ingredients.notes'
                ]);
            }
        ])->findOrFail($id);

        // Calculate additional details
        $recipe->total_material_cost = $recipe->calculateTotalCost();
        $recipe->cost_per_unit = $recipe->calculateCostPerUnit();
        $recipe->batch_size = $recipe->batch_size ?? 100;
        $recipe->yield_quantity = $recipe->yield_quantity ?? 1;
        $recipe->yield_unit = $recipe->yield_unit ?? 'pcs';

        // Add ingredient cost details
        $recipe->ingredients->each(function ($ingredient) {
            $ingredient->total_cost = $ingredient->pivot->quantity * $ingredient->average_price;
            $ingredient->adjusted_quantity = $ingredient->pivot->quantity;
        });

        return $this->successResponse([
            'recipe' => $recipe,
            'cost_breakdown' => [
                'material_cost' => $recipe->total_material_cost,
                'labor_cost' => $recipe->labor_cost ?? 0,
                'overhead_cost' => $recipe->overhead_cost ?? 0,
                'total_cost' => $recipe->total_cost,
                'cost_per_unit' => $recipe->cost_per_unit
            ]
        ], 'Recipe details retrieved successfully');

    } catch (\Exception $e) {
        return $this->errorResponse('Failed to retrieve recipe details: ' . $e->getMessage(), 404);
    }
}

    /**
     * Update recipe costs based on current material prices
     */
    public function updateRecipeCosts($id): JsonResponse
    {
        try {
            $recipe = Recipe::findOrFail($id);
            
            // Recalculate total cost
            $recipe->total_cost = $recipe->calculateTotalCost();
            $recipe->save();

            return $this->successResponse([
                'recipe' => $recipe,
                'updated_cost' => $recipe->total_cost,
                'cost_per_unit' => $recipe->calculateCostPerUnit()
            ], 'Recipe costs updated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update recipe costs: ' . $e->getMessage());
        }
    }
}