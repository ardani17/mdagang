<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ActivityLog;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['category', 'recipe']);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Filter by status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            // Filter by stock status
            if ($request->has('stock_status')) {
                switch ($request->stock_status) {
                    case 'low':
                        $query->whereRaw('current_stock <= min_stock');
                        break;
                    case 'out_of_stock':
                        $query->where('current_stock', 0);
                        break;
                    case 'in_stock':
                        $query->whereRaw('current_stock > min_stock');
                        break;
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);

            // Add calculated fields
            foreach ($products as $product) {
                $product->stock_status = $product->getStockStatus();
                $product->profit_margin = $product->getProfitMargin();
            }

            return $this->successResponse($products, 'Products retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve products: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:raw,processed,finished',
            'unit' => 'required|string|max:50',
            'base_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'recipe_id' => 'nullable|exists:recipes,id',
            'production_time' => 'nullable|integer|min:0',
            'shelf_life_days' => 'nullable|integer|min:0',
            'storage_conditions' => 'nullable|string',
            'barcode' => 'nullable|string|max:100|unique:products',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $data = $request->except('image');

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }

            $product = Product::create($data);

            // Create initial stock movement if there's initial stock
            if ($product->current_stock > 0) {
                StockMovement::create([
                    'type' => 'product',
                    'reference_id' => $product->id,
                    'movement_type' => 'in',
                    'quantity' => $product->current_stock,
                    'unit_cost' => $product->base_price,
                    'total_cost' => $product->current_stock * $product->base_price,
                    'reason' => 'initial_stock',
                    'notes' => 'Initial stock entry',
                    'performed_by' => auth()->id(),
                ]);
            }

            // Log activity
            ActivityLog::logCreation($product, 'Created new product: ' . $product->name);

            DB::commit();
            return $this->successResponse($product->load(['category', 'recipe']), 'Product created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with([
                'category',
                'recipe.ingredients.rawMaterial',
                'orderItems.order',
                'stockMovements' => function ($query) {
                    $query->latest()->limit(10);
                }
            ])->findOrFail($id);

            // Add statistics
            $product->statistics = [
                'stock_status' => $product->getStockStatus(),
                'stock_value' => $product->getStockValue(),
                'profit_margin' => $product->getProfitMargin(),
                'total_sold_30days' => $product->orderItems()
                    ->whereHas('order', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    })
                    ->sum('quantity'),
                'revenue_30days' => $product->orderItems()
                    ->whereHas('order', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    })
                    ->sum('total'),
                'average_sales_per_day' => $product->orderItems()
                    ->whereHas('order', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    })
                    ->sum('quantity') / 30,
                'days_until_stockout' => $product->current_stock > 0 ? 
                    round($product->current_stock / max(1, $product->orderItems()
                        ->whereHas('order', function ($q) {
                            $q->where('created_at', '>=', now()->subDays(30));
                        })
                        ->sum('quantity') / 30)) : 0,
            ];

            return $this->successResponse($product, 'Product retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Product not found', 404);
        }
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|max:50|unique:products,sku,' . $id,
            'description' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'type' => 'sometimes|required|in:raw,processed,finished',
            'unit' => 'sometimes|required|string|max:50',
            'base_price' => 'sometimes|required|numeric|min:0',
            'selling_price' => 'sometimes|required|numeric|min:0',
            'min_stock' => 'sometimes|required|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'recipe_id' => 'nullable|exists:recipes,id',
            'production_time' => 'nullable|integer|min:0',
            'shelf_life_days' => 'nullable|integer|min:0',
            'storage_conditions' => 'nullable|string',
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $oldValues = $product->toArray();
            $data = $request->except(['image', 'current_stock']); // Don't allow direct stock updates

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }

            $product->update($data);
            
            // Log activity
            $changes = array_diff_assoc($data, $oldValues);
            if (!empty($changes)) {
                ActivityLog::logUpdate($product, $changes, 'Updated product: ' . $product->name);
            }

            DB::commit();
            return $this->successResponse($product->load(['category', 'recipe']), 'Product updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        // Check if product has orders
        if ($product->orderItems()->exists()) {
            return $this->errorResponse('Cannot delete product with existing orders', 422);
        }

        // Check if there's current stock
        if ($product->current_stock > 0) {
            return $this->errorResponse('Cannot delete product with current stock', 422);
        }

        DB::beginTransaction();
        try {
            // Delete image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Log activity before deletion
            ActivityLog::logDeletion($product, 'Deleted product: ' . $product->name);
            
            $product->delete();
            
            DB::commit();
            return $this->successResponse(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Adjust stock for a product
     */
    public function adjustStock(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|numeric|min:0',
            'reason' => 'required|in:production,sale,return,damage,adjustment,other',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $oldStock = $product->current_stock;
            $quantity = $request->quantity;
            $movementType = 'adjustment';
            $movementQuantity = 0;

            switch ($request->adjustment_type) {
                case 'add':
                    $product->current_stock += $quantity;
                    $movementType = 'in';
                    $movementQuantity = $quantity;
                    break;
                case 'subtract':
                    if ($product->current_stock < $quantity) {
                        return $this->errorResponse('Insufficient stock', 422);
                    }
                    $product->current_stock -= $quantity;
                    $movementType = 'out';
                    $movementQuantity = $quantity;
                    break;
                case 'set':
                    $movementQuantity = abs($product->current_stock - $quantity);
                    $movementType = $product->current_stock > $quantity ? 'out' : 'in';
                    $product->current_stock = $quantity;
                    break;
            }

            $product->save();

            // Create stock movement record
            StockMovement::create([
                'type' => 'product',
                'reference_id' => $product->id,
                'movement_type' => $movementType,
                'quantity' => $movementQuantity,
                'unit_cost' => $product->base_price,
                'total_cost' => $movementQuantity * $product->base_price,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'performed_by' => auth()->id(),
            ]);

            // Log activity
            ActivityLog::log('update', $product, [
                'stock' => ['old' => $oldStock, 'new' => $product->current_stock]
            ], 'Adjusted stock for product: ' . $product->name);

            // Check if low stock alert needed
            if ($product->current_stock <= $product->min_stock) {
                // In a real application, you would send notifications here
            }

            DB::commit();
            return $this->successResponse($product, 'Stock adjusted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to adjust stock: ' . $e->getMessage());
        }
    }

    /**
     * Calculate product costs
     */
    public function calculateCosts($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        try {
            $costs = [
                'base_price' => $product->base_price,
                'selling_price' => $product->selling_price,
                'profit_margin' => $product->getProfitMargin(),
                'profit_amount' => $product->selling_price - $product->base_price,
            ];

            // If product has a recipe, calculate material costs
            if ($product->recipe) {
                $materialCost = 0;
                $materials = [];

                foreach ($product->recipe->ingredients as $ingredient) {
                    $cost = $ingredient->quantity * $ingredient->rawMaterial->unit_cost;
                    $materialCost += $cost;
                    
                    $materials[] = [
                        'name' => $ingredient->rawMaterial->name,
                        'quantity' => $ingredient->quantity,
                        'unit' => $ingredient->unit,
                        'unit_cost' => $ingredient->rawMaterial->unit_cost,
                        'total_cost' => $cost,
                    ];
                }

                $costs['material_cost'] = $materialCost;
                $costs['materials'] = $materials;
                $costs['suggested_price'] = $materialCost * 2.5; // 150% markup suggestion
            }

            return $this->successResponse($costs, 'Product costs calculated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to calculate costs: ' . $e->getMessage());
        }
    }

    /**
     * Get product statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_products' => Product::count(),
                'active_products' => Product::where('is_active', true)->count(),
                'inactive_products' => Product::where('is_active', false)->count(),
                'low_stock_products' => Product::whereRaw('current_stock <= min_stock')->count(),
                'out_of_stock_products' => Product::where('current_stock', 0)->count(),
                'total_stock_value' => Product::selectRaw('SUM(current_stock * base_price) as total')->first()->total ?? 0,
                'total_retail_value' => Product::selectRaw('SUM(current_stock * selling_price) as total')->first()->total ?? 0,
                'by_category' => Product::selectRaw('category_id, count(*) as count')
                    ->with('category:id,name')
                    ->groupBy('category_id')
                    ->get(),
                'by_type' => Product::selectRaw('type, count(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type'),
                'top_selling' => Product::withCount(['orderItems as sold_quantity' => function ($query) {
                    $query->select(DB::raw('SUM(quantity)'))
                        ->whereHas('order', function ($q) {
                            $q->where('created_at', '>=', now()->subDays(30));
                        });
                }])
                ->orderBy('sold_quantity', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'sku']),
            ];

            return $this->successResponse($stats, 'Product statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Toggle product status
     */
    public function toggleStatus($id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $product->is_active;
            $product->is_active = !$product->is_active;
            $product->save();

            // Log activity
            ActivityLog::logUpdate(
                $product, 
                ['is_active' => ['old' => $oldStatus, 'new' => $product->is_active]], 
                'Changed product status: ' . $product->name
            );

            DB::commit();
            return $this->successResponse($product, 'Product status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update product status: ' . $e->getMessage());
        }
    }

    /**
     * Export products to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $products = Product::with(['category'])->get();
            
            $csvData = [];
            $csvData[] = ['ID', 'SKU', 'Name', 'Category', 'Type', 'Unit', 'Base Price', 'Selling Price', 'Current Stock', 'Min Stock', 'Stock Value', 'Status'];
            
            foreach ($products as $product) {
                $csvData[] = [
                    $product->id,
                    $product->sku,
                    $product->name,
                    $product->category->name,
                    $product->type,
                    $product->unit,
                    $product->base_price,
                    $product->selling_price,
                    $product->current_stock,
                    $product->min_stock,
                    $product->getStockValue(),
                    $product->is_active ? 'Active' : 'Inactive',
                ];
            }

            // In a real application, you would generate and return a CSV file
            // For now, we'll just return the data
            return $this->successResponse($csvData, 'Products exported successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export products: ' . $e->getMessage());
        }
    }
}