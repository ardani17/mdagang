<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PriceChange;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductPricingController extends Controller
{
    /**
     * Get products pricing data
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['category', 'unit'])
                ->where('type', 'finished')
                ->orderBy('name');

            // Apply search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            // Apply category filter
            if ($request->has('category') && $request->category) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('name', $request->category);
                });
            }

            $products = $query->get();

            // Transform data with pricing calculations
            $transformedProducts = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'unit_cost' => $this->calculateUnitCost($product),
                    'selling_price' => $product->selling_price,
                    'current_stock' => $product->current_stock,
                    'min_stock' => $product->min_stock,
                    'margin' => $this->calculateMargin($product),
                    'profit_per_unit' => $product->selling_price - $this->calculateUnitCost($product),
                    'total_profit_potential' => ($product->selling_price - $this->calculateUnitCost($product)) * $product->current_stock,
                    'last_price_update' => $this->getLastPriceUpdateDate($product)
                ];
            });

            // Calculate overall statistics
            $stats = [
                'average_margin' => $transformedProducts->avg('margin') ?? 0,
                'low_margin_products' => $transformedProducts->where('margin', '<', 20)->count(),
                'total_profit_potential' => $transformedProducts->sum('total_profit_potential'),
                'total_products' => $transformedProducts->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $transformedProducts,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pricing data: ' . $e->getMessage()
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
                'reason' => 'required|string|max:255',
                'notes' => 'nullable|string'
            ]);

            $product = Product::where('type', 'finished')->findOrFail($id);

            DB::transaction(function () use ($product, $request) {
                $oldPrice = $product->selling_price;
                $product->selling_price = $request->selling_price;
                $product->save();

                // Record price change
                PriceChange::create([
                    'product_id' => $product->id,
                    'old_price' => $oldPrice,
                    'new_price' => $request->selling_price,
                    'change_type' => 'manual',
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'changed_by' => auth()->id()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Price updated successfully',
                'data' => [
                    'selling_price' => $product->selling_price,
                    'margin' => $this->calculateMargin($product),
                    'profit_per_unit' => $product->selling_price - $this->calculateUnitCost($product)
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
     * Bulk update prices
     */
    public function bulkUpdatePrices(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'category' => 'nullable|string',
                'update_type' => 'required|in:margin,markup,fixed,percentage',
                'value' => 'required|numeric',
                'reason' => 'required|string|max:255'
            ]);

            $query = Product::where('type', 'finished');

            if ($request->category) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('name', $request->category);
                });
            }

            $products = $query->get();
            $updatedCount = 0;

            DB::transaction(function () use ($products, $request, &$updatedCount) {
                foreach ($products as $product) {
                    $oldPrice = $product->selling_price;
                    $newPrice = $this->calculateNewPrice($product, $request->update_type, $request->value);

                    if ($newPrice != $oldPrice) {
                        $product->selling_price = $newPrice;
                        $product->save();

                        // Record price change
                        PriceChange::create([
                            'product_id' => $product->id,
                            'old_price' => $oldPrice,
                            'new_price' => $newPrice,
                            'change_type' => 'bulk_' . $request->update_type,
                            'reason' => $request->reason,
                            'notes' => "Bulk update: {$request->update_type} with value {$request->value}",
                            'changed_by' => auth()->id()
                        ]);

                        $updatedCount++;
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} products",
                'data' => [
                    'updated_count' => $updatedCount,
                    'total_products' => $products->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update prices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get price history for a product
     */
    public function getPriceHistory($id): JsonResponse
    {
        try {
            $product = Product::where('type', 'finished')->findOrFail($id);
            
            $priceHistory = PriceChange::with('changer')
                ->where('product_id', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($change) {
                    return [
                        'old_price' => $change->old_price,
                        'new_price' => $change->new_price,
                        'change_type' => $change->change_type,
                        'reason' => $change->reason,
                        'notes' => $change->notes,
                        'changed_by' => $change->changer->name ?? 'System',
                        'changed_at' => $change->created_at->format('Y-m-d H:i:s'),
                        'price_difference' => $change->new_price - $change->old_price,
                        'percentage_change' => $change->old_price > 0 ? 
                            (($change->new_price - $change->old_price) / $change->old_price) * 100 : 0
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => [
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'current_price' => $product->selling_price
                    ],
                    'price_history' => $priceHistory
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch price history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export pricing data
     */
    public function exportPricingData(Request $request): JsonResponse
    {
        try {
            $products = Product::with(['category', 'unit'])
                ->where('type', 'finished')
                ->get()
                ->map(function ($product) {
                    $unitCost = $this->calculateUnitCost($product);
                    $margin = $this->calculateMargin($product);
                    
                    return [
                        'SKU' => $product->sku,
                        'Nama Produk' => $product->name,
                        'Kategori' => $product->category->name ?? 'Uncategorized',
                        'Biaya Produksi' => $unitCost,
                        'Harga Jual' => $product->selling_price,
                        'Margin' => $margin,
                        'Profit per Unit' => $product->selling_price - $unitCost,
                        'Stok Saat Ini' => $product->current_stock,
                        'Total Profit Potensial' => ($product->selling_price - $unitCost) * $product->current_stock,
                        'Status Margin' => $margin >= 30 ? 'Tinggi' : ($margin >= 20 ? 'Sedang' : 'Rendah'),
                        'Terakhir Update' => $this->getLastPriceUpdateDate($product)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $products,
                'filename' => 'pricing-export-' . date('Y-m-d') . '.xlsx',
                'exported_at' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export pricing data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate optimal price
     */
    public function calculateOptimalPrice(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'target_margin' => 'nullable|numeric|min:0|max:100',
                'market_price' => 'nullable|numeric|min:0',
                'competitor_prices' => 'nullable|array'
            ]);

            $product = Product::where('type', 'finished')->findOrFail($id);
            $unitCost = $this->calculateUnitCost($product);

            // Calculate based on target margin
            $targetMargin = $request->target_margin ?? 30;
            $priceByMargin = $unitCost / (1 - ($targetMargin / 100));

            // Consider market price if provided
            $marketPrice = $request->market_price;
            $competitorPrices = $request->competitor_prices ?? [];

            $optimalPrice = $this->determineOptimalPrice($priceByMargin, $marketPrice, $competitorPrices);

            return response()->json([
                'success' => true,
                'data' => [
                    'current_price' => $product->selling_price,
                    'unit_cost' => $unitCost,
                    'current_margin' => $this->calculateMargin($product),
                    'suggested_price' => $optimalPrice,
                    'suggested_margin' => $this->calculateMarginFromCost($unitCost, $optimalPrice),
                    'price_difference' => $optimalPrice - $product->selling_price,
                    'margin_difference' => $this->calculateMarginFromCost($unitCost, $optimalPrice) - $this->calculateMargin($product),
                    'calculation_basis' => $marketPrice ? 'market_based' : 'margin_based'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate optimal price: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate new price based on update type
     */
    private function calculateNewPrice(Product $product, string $type, float $value): float
    {
        $unitCost = $this->calculateUnitCost($product);
        $currentPrice = $product->selling_price;

        switch ($type) {
            case 'margin':
                return $unitCost / (1 - ($value / 100));
                
            case 'markup':
                return $unitCost * (1 + ($value / 100));
                
            case 'fixed':
                return $currentPrice + $value;
                
            case 'percentage':
                return $currentPrice * (1 + ($value / 100));
                
            default:
                return $currentPrice;
        }
    }

    /**
     * Calculate unit cost
     */
    private function calculateUnitCost(Product $product): float
    {
        return $product->unit_cost > 0 ? $product->unit_cost : 0;
    }

    /**
     * Calculate margin percentage
     */
    private function calculateMargin(Product $product): float
    {
        $unitCost = $this->calculateUnitCost($product);
        if ($unitCost === 0 || $product->selling_price === 0) {
            return 0;
        }
        
        return round((($product->selling_price - $unitCost) / $product->selling_price) * 100, 2);
    }

    /**
     * Calculate margin from cost and price
     */
    private function calculateMarginFromCost(float $cost, float $price): float
    {
        if ($cost === 0 || $price === 0) {
            return 0;
        }
        
        return round((($price - $cost) / $price) * 100, 2);
    }

    /**
     * Get last price update date
     */
    private function getLastPriceUpdateDate(Product $product): ?string
    {
        $lastUpdate = PriceChange::where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastUpdate->created_at->format('Y-m-d') ?? null;
    }

    /**
     * Determine optimal price considering multiple factors
     */
    private function determineOptimalPrice(float $marginPrice, ?float $marketPrice, array $competitorPrices): float
    {
        $prices = [$marginPrice];

        if ($marketPrice) {
            $prices[] = $marketPrice;
        }

        if (!empty($competitorPrices)) {
            $prices = array_merge($prices, $competitorPrices);
        }

        // Use average of all considered prices
        return round(array_sum($prices) / count($prices), 2);
    }
}