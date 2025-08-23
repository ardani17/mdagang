<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\RawMaterial;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StockMovementController extends Controller
{
    /**
     * Display a listing of stock movements
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = StockMovement::with('creator');

            // Apply filters
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('reference_type')) {
                $query->where('reference_type', $request->reference_type);
            }

            if ($request->has('reference_id')) {
                $query->where('reference_id', $request->reference_id);
            }

            if ($request->has('direction')) {
                if ($request->direction === 'in') {
                    $query->incoming();
                } elseif ($request->direction === 'out') {
                    $query->outgoing();
                }
            }

            if ($request->has('location')) {
                $query->where('location', $request->location);
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('notes', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $movements = $query->paginate($perPage);

            // Add reference names to movements
            $movements->getCollection()->transform(function ($movement) {
                $movement->reference_name = $movement->reference_name;
                $movement->type_label = $movement->type_label;
                $movement->direction = $movement->direction;
                return $movement;
            });

            // Calculate summary
            $startDate = $request->get('date_from', Carbon::now()->startOfMonth());
            $endDate = $request->get('date_to', Carbon::now()->endOfMonth());
            $summary = StockMovement::getPeriodSummary(
                $request->get('reference_type'),
                $startDate,
                $endDate
            );

            return $this->successResponse([
                'movements' => $movements,
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch stock movements: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created stock movement
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:adjustment_in,adjustment_out,transfer_in,transfer_out,waste,initial',
            'reference_type' => 'required|in:raw_material,product',
            'reference_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            // Get the reference item
            $referenceItem = null;
            $beforeStock = 0;
            $afterStock = 0;

            if ($request->reference_type === 'raw_material') {
                $referenceItem = RawMaterial::findOrFail($request->reference_id);
                $beforeStock = $referenceItem->current_stock;
            } elseif ($request->reference_type === 'product') {
                $referenceItem = Product::findOrFail($request->reference_id);
                $beforeStock = $referenceItem->current_stock;
            }

            if (!$referenceItem) {
                return $this->errorResponse('Reference item not found');
            }

            // Determine if this is incoming or outgoing
            $incomingTypes = ['adjustment_in', 'transfer_in', 'initial'];
            $isIncoming = in_array($request->type, $incomingTypes);

            // Check if there's enough stock for outgoing movements
            if (!$isIncoming && $beforeStock < $request->quantity) {
                return $this->errorResponse("Insufficient stock. Available: {$beforeStock}");
            }

            // Update stock
            if ($isIncoming) {
                $afterStock = $beforeStock + $request->quantity;
                $referenceItem->updateStock($request->quantity, 'add');
            } else {
                $afterStock = $beforeStock - $request->quantity;
                $referenceItem->updateStock($request->quantity, 'subtract');
            }

            // Create stock movement record
            $movement = StockMovement::create([
                'type' => $request->type,
                'reference_type' => $request->reference_type,
                'reference_id' => $request->reference_id,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price ?? $referenceItem->cost_price ?? 0,
                'total_price' => $request->quantity * ($request->unit_price ?? $referenceItem->cost_price ?? 0),
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'location' => $request->location,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'create',
                'model_type' => 'StockMovement',
                'model_id' => $movement->id,
                'description' => "Created stock movement: {$movement->type_label} for {$referenceItem->name}",
                'changes' => json_encode($movement->toArray()),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Stock movement created successfully',
                'movement' => $movement,
                'updated_stock' => $afterStock,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create stock movement: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified stock movement
     */
    public function show($id): JsonResponse
    {
        try {
            $movement = StockMovement::with('creator')->findOrFail($id);

            return $this->successResponse([
                'movement' => $movement,
                'summary' => $movement->getSummary(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Stock movement not found');
        }
    }

    /**
     * Get stock movements for a specific item
     */
    public function byItem(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference_type' => 'required|in:raw_material,product',
            'reference_id' => 'required|integer',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        try {
            $query = StockMovement::where('reference_type', $request->reference_type)
                ->where('reference_id', $request->reference_id)
                ->with('creator');

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            $movements = $query->orderBy('created_at', 'desc')->get();

            // Get item details
            $item = null;
            if ($request->reference_type === 'raw_material') {
                $item = RawMaterial::find($request->reference_id);
            } elseif ($request->reference_type === 'product') {
                $item = Product::find($request->reference_id);
            }

            // Calculate summary
            $incoming = $movements->filter(fn($m) => $m->isIncoming());
            $outgoing = $movements->filter(fn($m) => $m->isOutgoing());

            $summary = [
                'item_name' => $item?->name ?? 'Unknown',
                'current_stock' => $item?->current_stock ?? 0,
                'total_movements' => $movements->count(),
                'incoming_quantity' => $incoming->sum('quantity'),
                'outgoing_quantity' => $outgoing->sum('quantity'),
                'incoming_value' => $incoming->sum('total_price'),
                'outgoing_value' => $outgoing->sum('total_price'),
                'net_change' => $incoming->sum('quantity') - $outgoing->sum('quantity'),
            ];

            return $this->successResponse([
                'movements' => $movements,
                'summary' => $summary,
                'item' => $item,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch item movements: ' . $e->getMessage());
        }
    }

    /**
     * Get stock summary report
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
            $referenceType = $request->get('reference_type');

            $summary = StockMovement::getPeriodSummary($referenceType, $startDate, $endDate);

            // Get top moving items
            $topItems = StockMovement::select('reference_type', 'reference_id')
                ->selectRaw('COUNT(*) as movement_count')
                ->selectRaw('SUM(quantity) as total_quantity')
                ->selectRaw('SUM(total_price) as total_value')
                ->when($referenceType, function ($q) use ($referenceType) {
                    $q->where('reference_type', $referenceType);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('reference_type', 'reference_id')
                ->orderByDesc('movement_count')
                ->limit(10)
                ->get();

            // Add item names to top items
            $topItems->transform(function ($item) {
                if ($item->reference_type === 'raw_material') {
                    $ref = RawMaterial::find($item->reference_id);
                } else {
                    $ref = Product::find($item->reference_id);
                }
                $item->item_name = $ref?->name ?? 'Unknown';
                return $item;
            });

            // Get daily movement trend
            $dailyTrend = StockMovement::whereBetween('created_at', [$startDate, $endDate])
                ->when($referenceType, function ($q) use ($referenceType) {
                    $q->where('reference_type', $referenceType);
                })
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(CASE WHEN type IN (\'purchase\', \'purchase_receipt\', \'production_output\', \'return\', \'adjustment_in\', \'transfer_in\', \'initial\') THEN quantity ELSE 0 END) as incoming')
                ->selectRaw('SUM(CASE WHEN type IN (\'sale\', \'production_consumption\', \'adjustment_out\', \'transfer_out\', \'waste\') THEN quantity ELSE 0 END) as outgoing')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $this->successResponse([
                'summary' => $summary,
                'top_items' => $topItems,
                'daily_trend' => $dailyTrend,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate summary: ' . $e->getMessage());
        }
    }

    /**
     * Get stock valuation
     */
    public function valuation(Request $request): JsonResponse
    {
        try {
            $method = $request->get('method', 'fifo'); // fifo, lifo, average
            $referenceType = $request->get('reference_type', 'all');

            $valuation = [];
            $totalValue = 0;
            $totalQuantity = 0;

            // Get all items based on reference type
            if ($referenceType === 'raw_material' || $referenceType === 'all') {
                $rawMaterials = RawMaterial::where('current_stock', '>', 0)->get();
                foreach ($rawMaterials as $material) {
                    if ($method === 'fifo') {
                        $val = StockMovement::getFIFOValuation('raw_material', $material->id);
                    } else {
                        // Simple average cost method
                        $val = [
                            'method' => 'average',
                            'total_quantity' => $material->current_stock,
                            'total_value' => $material->current_stock * $material->cost_price,
                            'average_cost' => $material->cost_price,
                        ];
                    }
                    
                    $valuation[] = [
                        'type' => 'raw_material',
                        'id' => $material->id,
                        'name' => $material->name,
                        'sku' => $material->sku,
                        'quantity' => $val['total_quantity'],
                        'value' => $val['total_value'],
                        'average_cost' => $val['average_cost'],
                    ];
                    
                    $totalValue += $val['total_value'];
                    $totalQuantity += $val['total_quantity'];
                }
            }

            if ($referenceType === 'product' || $referenceType === 'all') {
                $products = Product::where('current_stock', '>', 0)->get();
                foreach ($products as $product) {
                    if ($method === 'fifo') {
                        $val = StockMovement::getFIFOValuation('product', $product->id);
                    } else {
                        // Simple average cost method
                        $val = [
                            'method' => 'average',
                            'total_quantity' => $product->current_stock,
                            'total_value' => $product->current_stock * $product->cost_price,
                            'average_cost' => $product->cost_price,
                        ];
                    }
                    
                    $valuation[] = [
                        'type' => 'product',
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'quantity' => $val['total_quantity'],
                        'value' => $val['total_value'],
                        'average_cost' => $val['average_cost'],
                    ];
                    
                    $totalValue += $val['total_value'];
                    $totalQuantity += $val['total_quantity'];
                }
            }

            // Sort by value descending
            usort($valuation, function ($a, $b) {
                return $b['value'] <=> $a['value'];
            });

            return $this->successResponse([
                'method' => strtoupper($method),
                'items' => $valuation,
                'summary' => [
                    'total_items' => count($valuation),
                    'total_quantity' => $totalQuantity,
                    'total_value' => $totalValue,
                    'average_value_per_item' => count($valuation) > 0 ? $totalValue / count($valuation) : 0,
                ],
                'generated_at' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to calculate valuation: ' . $e->getMessage());
        }
    }

    /**
     * Get low stock items
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $lowStockItems = [];

            // Get low stock raw materials
            $rawMaterials = RawMaterial::whereRaw('current_stock <= minimum_stock')
                ->where('minimum_stock', '>', 0)
                ->get();

            foreach ($rawMaterials as $material) {
                $lowStockItems[] = [
                    'type' => 'raw_material',
                    'id' => $material->id,
                    'name' => $material->name,
                    'sku' => $material->sku,
                    'current_stock' => $material->current_stock,
                    'minimum_stock' => $material->minimum_stock,
                    'shortage' => $material->minimum_stock - $material->current_stock,
                    'unit' => $material->unit,
                    'last_purchase_price' => $material->last_purchase_price,
                    'estimated_value' => ($material->minimum_stock - $material->current_stock) * $material->last_purchase_price,
                ];
            }

            // Get low stock products
            $products = Product::whereRaw('current_stock <= minimum_stock')
                ->where('minimum_stock', '>', 0)
                ->get();

            foreach ($products as $product) {
                $lowStockItems[] = [
                    'type' => 'product',
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $product->current_stock,
                    'minimum_stock' => $product->minimum_stock,
                    'shortage' => $product->minimum_stock - $product->current_stock,
                    'unit' => $product->unit,
                    'cost_price' => $product->cost_price,
                    'estimated_value' => ($product->minimum_stock - $product->current_stock) * $product->cost_price,
                ];
            }

            // Sort by shortage descending
            usort($lowStockItems, function ($a, $b) {
                return $b['shortage'] <=> $a['shortage'];
            });

            $totalShortageValue = array_sum(array_column($lowStockItems, 'estimated_value'));

            return $this->successResponse([
                'items' => $lowStockItems,
                'summary' => [
                    'total_items' => count($lowStockItems),
                    'raw_materials_count' => count(array_filter($lowStockItems, fn($i) => $i['type'] === 'raw_material')),
                    'products_count' => count(array_filter($lowStockItems, fn($i) => $i['type'] === 'product')),
                    'total_shortage_value' => $totalShortageValue,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch low stock items: ' . $e->getMessage());
        }
    }

    /**
     * Export stock movements to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $query = StockMovement::with('creator');

            // Apply same filters as index
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('reference_type')) {
                $query->where('reference_type', $request->reference_type);
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            $movements = $query->orderBy('created_at', 'desc')->get();

            $csvData = [];
            $csvData[] = ['Date', 'Type', 'Direction', 'Reference', 'Item', 'Quantity', 'Unit Price', 'Total Value', 'Before Stock', 'After Stock', 'Location', 'Notes', 'Created By'];

            foreach ($movements as $movement) {
                $csvData[] = [
                    $movement->created_at->format('Y-m-d H:i'),
                    $movement->type_label,
                    $movement->direction,
                    $movement->reference_type,
                    $movement->reference_name,
                    $movement->quantity,
                    $movement->unit_price,
                    $movement->total_price,
                    $movement->before_stock,
                    $movement->after_stock,
                    $movement->location ?? '-',
                    $movement->notes ?? '-',
                    $movement->creator?->name ?? '-',
                ];
            }

            // Convert to CSV string
            $output = fopen('php://temp', 'r+');
            foreach ($csvData as $row) {
                fputcsv($output, $row);
            }
            rewind($output);
            $csvContent = stream_get_contents($output);
            fclose($output);

            return $this->successResponse([
                'csv' => base64_encode($csvContent),
                'filename' => 'stock_movements_' . date('Y-m-d_His') . '.csv',
                'count' => $movements->count(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export stock movements: ' . $e->getMessage());
        }
    }

    /**
     * Perform stock take/adjustment
     */
    public function stockTake(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.reference_type' => 'required|in:raw_material,product',
            'items.*.reference_id' => 'required|integer',
            'items.*.counted_stock' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $adjustments = [];

            foreach ($request->items as $itemData) {
                // Get the reference item
                $referenceItem = null;
                if ($itemData['reference_type'] === 'raw_material') {
                    $referenceItem = RawMaterial::findOrFail($itemData['reference_id']);
                } else {
                    $referenceItem = Product::findOrFail($itemData['reference_id']);
                }

                $currentStock = $referenceItem->current_stock;
                $countedStock = $itemData['counted_stock'];
                $difference = $countedStock - $currentStock;

                if ($difference != 0) {
                    // Determine adjustment type
                    $type = $difference > 0 ? 'adjustment_in' : 'adjustment_out';
                    $quantity = abs($difference);

                    // Create stock movement
                    $movement = StockMovement::create([
                        'type' => $type,
                        'reference_type' => $itemData['reference_type'],
                        'reference_id' => $itemData['reference_id'],
                        'quantity' => $quantity,
                        'unit_price' => $referenceItem->cost_price ?? 0,
                        'total_price' => $quantity * ($referenceItem->cost_price ?? 0),
                        'before_stock' => $currentStock,
                        'after_stock' => $countedStock,
                        'notes' => "Stock take adjustment. " . ($request->notes ?? ''),
                        'created_by' => auth()->id(),
                    ]);

                    // Update actual stock
                    $referenceItem->update(['current_stock' => $countedStock]);

                    $adjustments[] = [
                        'item' => $referenceItem->name,
                        'type' => $type,
                        'before' => $currentStock,
                        'after' => $countedStock,
                        'difference' => $difference,
                        'movement_id' => $movement->id,
                    ];
                }
            }

            // Log activity
            if (count($adjustments) > 0) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action_type' => 'create',
                    'model_type' => 'StockMovement',
                    'model_id' => 0,
                    'description' => "Performed stock take with " . count($adjustments) . " adjustments",
                    'changes' => json_encode($adjustments),
                ]);
            }

            DB::commit();

            return $this->successResponse([
                'message' => 'Stock take completed successfully',
                'adjustments' => $adjustments,
                'total_adjustments' => count($adjustments),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to perform stock take: ' . $e->getMessage());
        }
    }
}