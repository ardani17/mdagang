<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductionOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinishedProductController extends Controller
{
    /**
     * Get finished products with filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['category', 'unit'])
                ->where('type', 'finished') // Hanya produk jadi
                ->orderBy('name');

            // Apply search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if ($request->has('status') && $request->status) {
                $query->where(function ($q) use ($request) {
                    switch ($request->status) {
                        case 'available':
                            $q->where('current_stock', '>', DB::raw('min_stock'));
                            break;
                        case 'low_stock':
                            $q->where('current_stock', '<=', DB::raw('min_stock'))
                              ->where('current_stock', '>', 0);
                            break;
                        case 'out_of_stock':
                            $q->where('current_stock', '<=', 0);
                            break;
                    }
                });
            }

            // Apply category filter
            if ($request->has('category') && $request->category) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('name', $request->category);
                });
            }

            $products = $query->paginate($request->get('per_page', 15));

            // Transform data for frontend
            $transformedProducts = $products->getCollection()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'current_stock' => $product->current_stock,
                    'min_stock' => $product->min_stock,
                    'unit_cost' => $this->calculateUnitCost($product),
                    'selling_price' => $product->selling_price,
                    'expiry_date' => $product->expiry_date,
                    'last_production' => $this->getLastProductionDate($product),
                    'status' => $this->getStockStatus($product),
                    'unit' => $product->unit->name ?? 'unit'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedProducts,
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch finished products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get finished products statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = Product::where('type', 'finished')
                ->select([
                    DB::raw('COUNT(*) as total_products'),
                    DB::raw('SUM(CASE WHEN current_stock > min_stock THEN 1 ELSE 0 END) as available_products'),
                    DB::raw('SUM(CASE WHEN current_stock <= min_stock AND current_stock > 0 THEN 1 ELSE 0 END) as low_stock_products'),
                    DB::raw('SUM(CASE WHEN current_stock <= 0 THEN 1 ELSE 0 END) as out_of_stock_products'),
                    DB::raw('SUM(current_stock * unit_cost) as total_stock_value')
                ])
                ->first();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single product details
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with(['category', 'unit', 'productionOrders'])
                ->where('type', 'finished')
                ->findOrFail($id);

            $productDetails = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'description' => $product->description,
                'category' => $product->category->name ?? 'Uncategorized',
                'current_stock' => $product->current_stock,
                'min_stock' => $product->min_stock,
                'unit_cost' => $this->calculateUnitCost($product),
                'selling_price' => $product->selling_price,
                'expiry_date' => $product->expiry_date,
                'last_production' => $this->getLastProductionDate($product),
                'status' => $this->getStockStatus($product),
                'unit' => $product->unit->name ?? 'unit',
                'production_history' => $product->productionOrders()
                    ->where('status', 'completed')
                    ->orderBy('completed_at', 'desc')
                    ->take(10)
                    ->get(['order_number', 'quantity', 'actual_quantity', 'completed_at']),
                'stock_movements' => $product->stockMovements()
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get(['type', 'quantity', 'reason', 'created_at'])
            ];

            return response()->json([
                'success' => true,
                'data' => $productDetails
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adjust product stock
     */
    public function adjustStock(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'adjustment_type' => 'required|in:addition,subtraction,set',
                'quantity' => 'required|numeric|min:0',
                'reason' => 'required|string|max:255',
                'notes' => 'nullable|string'
            ]);

            $product = Product::where('type', 'finished')->findOrFail($id);

            DB::transaction(function () use ($product, $request) {
                $oldStock = $product->current_stock;

                switch ($request->adjustment_type) {
                    case 'addition':
                        $product->current_stock += $request->quantity;
                        break;
                    case 'subtraction':
                        $product->current_stock = max(0, $product->current_stock - $request->quantity);
                        break;
                    case 'set':
                        $product->current_stock = $request->quantity;
                        break;
                }

                $product->save();

                // Record stock movement
                $product->stockMovements()->create([
                    'type' => $request->adjustment_type,
                    'quantity' => $request->quantity,
                    'previous_stock' => $oldStock,
                    'new_stock' => $product->current_stock,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'user_id' => auth()->id()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'data' => [
                    'new_stock' => $product->current_stock,
                    'status' => $this->getStockStatus($product)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product price
     */
    public function updatePrice(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'selling_price' => 'required|numeric|min:0',
                'cost_price' => 'nullable|numeric|min:0',
                'reason' => 'required|string|max:255'
            ]);

            $product = Product::where('type', 'finished')->findOrFail($id);

            $oldPrice = $product->selling_price;
            $product->selling_price = $request->selling_price;
            
            if ($request->has('cost_price')) {
                $product->unit_cost = $request->cost_price;
            }

            $product->save();

            // Record price change
            $product->priceChanges()->create([
                'old_price' => $oldPrice,
                'new_price' => $request->selling_price,
                'reason' => $request->reason,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Price updated successfully',
                'data' => [
                    'selling_price' => $product->selling_price,
                    'unit_cost' => $product->unit_cost
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update price: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export products data
     */
    public function exportData(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['category', 'unit'])
                ->where('type', 'finished')
                ->orderBy('name');

            // Apply filters same as index method
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            if ($request->has('status') && $request->status) {
                $query->where(function ($q) use ($request) {
                    switch ($request->status) {
                        case 'available':
                            $q->where('current_stock', '>', DB::raw('min_stock'));
                            break;
                        case 'low_stock':
                            $q->where('current_stock', '<=', DB::raw('min_stock'))
                              ->where('current_stock', '>', 0);
                            break;
                        case 'out_of_stock':
                            $q->where('current_stock', '<=', 0);
                            break;
                    }
                });
            }

            $products = $query->get();

            $exportData = $products->map(function ($product) {
                return [
                    'SKU' => $product->sku,
                    'Nama Produk' => $product->name,
                    'Kategori' => $product->category->name ?? 'Uncategorized',
                    'Stok Saat Ini' => $product->current_stock,
                    'Stok Minimum' => $product->min_stock,
                    'Status Stok' => $this->getStockStatus($product),
                    'Biaya Produksi' => $this->calculateUnitCost($product),
                    'Harga Jual' => $product->selling_price,
                    'Tanggal Kadaluarsa' => $product->expiry_date,
                    'Produksi Terakhir' => $this->getLastProductionDate($product),
                    'Margin' => $product->selling_price - $this->calculateUnitCost($product)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $exportData,
                'filename' => 'finished-products-export-' . date('Y-m-d') . '.xlsx'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate unit cost based on production costs
     */
    private function calculateUnitCost(Product $product): float
    {
        // Jika sudah ada unit cost, gunakan itu
        if ($product->unit_cost > 0) {
            return $product->unit_cost;
        }

        // Hitung dari rata-rata biaya produksi terakhir
        $avgCost = ProductionOrder::where('product_id', $product->id)
            ->where('status', 'completed')
            ->where('actual_cost', '>', 0)
            ->where('actual_quantity', '>', 0)
            ->avg(DB::raw('actual_cost / actual_quantity'));

        return $avgCost ?? 0;
    }

    /**
     * Get last production date
     */
    private function getLastProductionDate(Product $product): ?string
    {
        $lastProduction = ProductionOrder::where('product_id', $product->id)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->first();

        return $lastProduction->completed_at ?? null;
    }

    /**
     * Get stock status
     */
    private function getStockStatus(Product $product): string
    {
        if ($product->current_stock <= 0) {
            return 'out_of_stock';
        }

        if ($product->current_stock <= $product->min_stock) {
            return 'low_stock';
        }

        return 'available';
    }
}