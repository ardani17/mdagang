<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductionOrderController extends Controller
{
    /**
     * Display a listing of production orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ProductionOrder::with(['recipe.product', 'qualityInspection']);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('batch_number', 'like', "%{$search}%")
                      ->orWhereHas('recipe', function ($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by recipe
            if ($request->has('recipe_id')) {
                $query->where('recipe_id', $request->recipe_id);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $orders = $query->paginate($perPage);

            return $this->successResponse($orders, 'Production orders retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve production orders: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created production order
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'recipe_id' => 'required|exists:recipes,id',
            'quantity_planned' => 'nullable|numeric',
            'order_date' => 'required|date',
            'target_date' => 'nullable|date|after:start_date',
            'priority' => 'required',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $recipe = Recipe::find($request->recipe_id);
            
            // Check material availability
            $availability = $this->checkMaterialAvailability($recipe, $request->quantity_planned);
            if (!$availability['can_produce']) {
                return $this->errorResponse('Insufficient materials for production', 422, $availability);
            }

            // Generate order number and batch number
            $orderNumber = 'PRD-' . date('Ymd') . '-' . str_pad(ProductionOrder::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
            $batchNumber = 'BATCH-' . date('Ymd') . '-' . strtoupper(substr(md5(time()), 0, 6));

            // Create production order
            $order = ProductionOrder::create([
                'order_number' => $orderNumber,
                'batch_number' => $batchNumber,
                'recipe_id' => $request->recipe_id,
                'quantity_planned' => $request->quantity_planned ?? 1,
                'quantity_produced' => 0,
                'start_date' => $request->order_date,
                'target_date' => $request->target_date ?? '',
                'priority' => $request->priority,
                'status' => 'pending',
                'notes' => $request->notes,
                'estimated_cost' => $recipe->total_cost * $request->quantity_planned,
            ]);

            // Log activity
            ActivityLog::logCreation($order, 'Created production order: ' . $orderNumber);

            DB::commit();
            return $this->successResponse($order->load('recipe.product'), 'Production order created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create production order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified production order
     */
    public function show($id): JsonResponse
    {
        try {
            $order = ProductionOrder::with([
                'recipe.product',
                'recipe.ingredients.rawMaterial',
                'qualityInspection'
            ])->findOrFail($id);

            // Add material requirements
            $order->material_requirements = $this->calculateMaterialRequirements($order);
            
            // Add production progress
            $order->progress_percentage = $order->quantity_planned > 0 
                ? round(($order->quantity_produced / $order->quantity_planned) * 100, 2) 
                : 0;

            return $this->successResponse($order, 'Production order retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Production order not found', 404);
        }
    }

    /**
     * Update the specified production order
     */
    public function update(Request $request, $id): JsonResponse
    {
        $order = ProductionOrder::find($id);
        if (!$order) {
            return $this->errorResponse('Production order not found', 404);
        }

        // Only allow updates if status is pending or in_progress
        if (!in_array($order->status, ['pending', 'in_progress'])) {
            return $this->errorResponse('Cannot update production order with status: ' . $order->status, 422);
        }

        $validator = Validator::make($request->all(), [
            'quantity_planned' => 'sometimes|required|numeric|min:1',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'priority' => 'sometimes|required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $oldValues = $order->toArray();
            
            // If quantity is being updated, check material availability
            if ($request->has('quantity_planned') && $request->quantity_planned != $order->quantity_planned) {
                $recipe = Recipe::find($order->recipe_id);
                $availability = $this->checkMaterialAvailability($recipe, $request->quantity_planned);
                if (!$availability['can_produce']) {
                    return $this->errorResponse('Insufficient materials for updated quantity', 422, $availability);
                }
                
                // Update estimated cost
                $order->estimated_cost = $recipe->total_cost * $request->quantity_planned;
            }

            $order->update($request->all());

            // Log activity
            $changes = array_diff_assoc($request->all(), $oldValues);
            if (!empty($changes)) {
                ActivityLog::logUpdate($order, $changes, 'Updated production order: ' . $order->order_number);
            }

            DB::commit();
            return $this->successResponse($order->load('recipe.product'), 'Production order updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update production order: ' . $e->getMessage());
        }
    }

    public function updateProgress(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'progress' => 'required|integer|min:0|max:100',
                'actual_quantity' => 'nullable|integer|min:0',
                'notes' => 'nullable|string|max:1000',
                'log_type' => 'required|in:progress,issue,complete'
            ]);

            $order = ProductionOrder::findOrFail($id);

            DB::transaction(function () use ($order, $request) {
                // Update order progress
                $order->progress = $request->progress;
                
                if ($request->has('actual_quantity')) {
                    $order->actual_quantity = $request->actual_quantity;
                }

                // Update status based on progress
                if ($request->progress === 100) {
                    $order->status = 'completed';
                    $order->completed_at = now();
                } elseif ($request->progress > 0 && $order->status === 'pending') {
                    $order->status = 'in_progress';
                }

                $order->save();

                // Add production log
                $order->productionLogs()->create([
                    'type' => $request->log_type,
                    'description' => $request->notes ?? 'Progress updated to ' . $request->progress . '%',
                    'progress' => $request->progress,
                    'logged_by' => auth()->id()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Progress updated successfully',
                'data' => [
                    'progress' => $order->progress,
                    'status' => $order->status,
                    'actual_quantity' => $order->actual_quantity
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update progress: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,in_progress,completed,cancelled',
                'reason' => 'nullable|string|max:500'
            ]);

            $order = ProductionOrder::findOrFail($id);
            $oldStatus = $order->status;

            DB::transaction(function () use ($order, $request, $oldStatus) {
                $order->status = $request->status;
                
                if ($request->status === 'completed') {
                    $order->completed_at = now();
                    $order->progress = 100;
                } elseif ($request->status === 'cancelled') {
                    $order->cancelled_at = now();
                }

                $order->save();

                // Add status change log
                $order->productionLogs()->create([
                    'type' => 'status_change',
                    'description' => 'Status changed from ' . $oldStatus . ' to ' . $request->status . 
                                    ($request->reason ? '. Reason: ' . $request->reason : ''),
                    'progress' => $order->progress,
                    'logged_by' => auth()->id()
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => [
                    'status' => $order->status,
                    'progress' => $order->progress
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print production order
     */
    public function printOrder($id): JsonResponse
    {
        try {
            $order = ProductionOrder::with([
                'recipe.ingredients.rawMaterial',
                'product',
                'operator'
            ])->findOrFail($id);

            $pdf = PDF::loadView('pdf.production-order', [
                'order' => $order,
                'ingredients' => $order->recipe->ingredients->map(function ($ingredient) use ($order) {
                    return [
                        'name' => $ingredient->name,
                        'quantity' => $ingredient->pivot->quantity * $order->batch_size,
                        'unit' => $ingredient->pivot->unit,
                        'notes' => $ingredient->pivot->notes
                    ];
                })
            ]);

            $filename = 'production-order-' . $order->order_number . '.pdf';

            return response()->json([
                'success' => true,
                'data' => [
                    'pdf_url' => 'data:application/pdf;base64,' . base64_encode($pdf->output()),
                    'filename' => $filename
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get production statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = ProductionOrder::selectRaw('
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress_orders,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_orders,
                AVG(progress) as average_progress
            ')->first();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified production order
     */
    public function destroy($id): JsonResponse
    {
        $order = ProductionOrder::find($id);
        if (!$order) {
            return $this->errorResponse('Production order not found', 404);
        }

        // Only allow deletion if status is pending
        if ($order->status !== 'pending') {
            return $this->errorResponse('Cannot delete production order with status: ' . $order->status, 422);
        }

        DB::beginTransaction();
        try {
            // Log activity before deletion
            ActivityLog::logDeletion($order, 'Deleted production order: ' . $order->order_number);
            
            $order->delete();
            
            DB::commit();
            return $this->successResponse(null, 'Production order deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete production order: ' . $e->getMessage());
        }
    }

    /**
     * Start production order
     */
    public function start($id): JsonResponse
    {
        $order = ProductionOrder::find($id);
        if (!$order) {
            return $this->errorResponse('Production order not found', 404);
        }

        if ($order->status !== 'pending') {
            return $this->errorResponse('Production order must be pending to start', 422);
        }

        DB::beginTransaction();
        try {
            $recipe = Recipe::find($order->recipe_id);
            
            // Check and consume materials
            $availability = $this->checkMaterialAvailability($recipe, $order->quantity_planned);
            if (!$availability['can_produce']) {
                return $this->errorResponse('Insufficient materials to start production', 422, $availability);
            }

            // Consume raw materials
            foreach ($recipe->ingredients as $ingredient) {
                $material = $ingredient->rawMaterial;
                $requiredQuantity = $ingredient->quantity * $order->quantity_planned;
                
                // Update stock
                $material->current_stock -= $requiredQuantity;
                $material->save();

                // Create stock movement
                StockMovement::create([
                    'type' => 'raw_material',
                    'reference_id' => $material->id,
                    'movement_type' => 'out',
                    'quantity' => $requiredQuantity,
                    'unit_cost' => $material->unit_cost,
                    'total_cost' => $requiredQuantity * $material->unit_cost,
                    'reason' => 'production',
                    'notes' => 'Used in production order: ' . $order->order_number,
                    'performed_by' => auth()->id(),
                ]);
            }

            // Update order status
            $order->status = 'in_progress';
            $order->actual_start_date = now();
            $order->save();

            // Log activity
            ActivityLog::log('update', $order, ['status' => ['old' => 'pending', 'new' => 'in_progress']], 'Started production order: ' . $order->order_number);

            DB::commit();
            return $this->successResponse($order->load('recipe.product'), 'Production order started successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to start production order: ' . $e->getMessage());
        }
    }

    /**
     * Complete production order
     */
    public function complete(Request $request, $id): JsonResponse
    {
        $order = ProductionOrder::find($id);
        if (!$order) {
            return $this->errorResponse('Production order not found', 404);
        }

        if ($order->status !== 'in_progress') {
            return $this->errorResponse('Production order must be in progress to complete', 422);
        }

        $validator = Validator::make($request->all(), [
            'quantity_produced' => 'required|numeric|min:0|max:' . $order->quantity_planned,
            'waste_quantity' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $recipe = Recipe::find($order->recipe_id);
            $product = Product::find($recipe->product_id);

            // Update product stock
            $product->current_stock += $request->quantity_produced;
            $product->save();

            // Create stock movement for produced products
            StockMovement::create([
                'type' => 'product',
                'reference_id' => $product->id,
                'movement_type' => 'in',
                'quantity' => $request->quantity_produced,
                'unit_cost' => $recipe->cost_per_unit,
                'total_cost' => $request->quantity_produced * $recipe->cost_per_unit,
                'reason' => 'production',
                'notes' => 'Produced from order: ' . $order->order_number,
                'performed_by' => auth()->id(),
            ]);

            // Calculate actual production time
            $actualProductionTime = $order->actual_start_date 
                ? now()->diffInHours($order->actual_start_date) 
                : $recipe->production_time;

            // Update order
            $order->quantity_produced = $request->quantity_produced;
            $order->waste_quantity = $request->waste_quantity ?? 0;
            $order->status = 'completed';
            $order->actual_end_date = now();
            $order->actual_production_time = $actualProductionTime;
            $order->actual_cost = $recipe->total_cost * $order->quantity_planned; // Materials already consumed
            $order->efficiency_percentage = $order->quantity_planned > 0 
                ? round(($request->quantity_produced / $order->quantity_planned) * 100, 2) 
                : 0;
            $order->notes = $request->notes ?? $order->notes;
            $order->save();

            // Log activity
            ActivityLog::log('update', $order, [
                'status' => ['old' => 'in_progress', 'new' => 'completed'],
                'quantity_produced' => $request->quantity_produced
            ], 'Completed production order: ' . $order->order_number);

            DB::commit();
            return $this->successResponse($order->load('recipe.product'), 'Production order completed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to complete production order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel production order
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $order = ProductionOrder::find($id);
        if (!$order) {
            return $this->errorResponse('Production order not found', 404);
        }

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return $this->errorResponse('Cannot cancel production order with status: ' . $order->status, 422);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            // If order was in progress, return consumed materials
            if ($order->status === 'in_progress') {
                $recipe = Recipe::find($order->recipe_id);
                
                // Return raw materials to stock
                foreach ($recipe->ingredients as $ingredient) {
                    $material = $ingredient->rawMaterial;
                    $returnQuantity = $ingredient->quantity * $order->quantity_planned;
                    
                    // Update stock
                    $material->current_stock += $returnQuantity;
                    $material->save();

                    // Create stock movement
                    StockMovement::create([
                        'type' => 'raw_material',
                        'reference_id' => $material->id,
                        'movement_type' => 'in',
                        'quantity' => $returnQuantity,
                        'unit_cost' => $material->unit_cost,
                        'total_cost' => $returnQuantity * $material->unit_cost,
                        'reason' => 'return',
                        'notes' => 'Returned from cancelled production order: ' . $order->order_number,
                        'performed_by' => auth()->id(),
                    ]);
                }
            }

            $oldStatus = $order->status;
            $order->status = 'cancelled';
            $order->notes = ($order->notes ? $order->notes . "\n" : '') . 'Cancellation reason: ' . $request->reason;
            $order->save();

            // Log activity
            ActivityLog::log('update', $order, [
                'status' => ['old' => $oldStatus, 'new' => 'cancelled']
            ], 'Cancelled production order: ' . $order->order_number);

            DB::commit();
            return $this->successResponse($order->load('recipe.product'), 'Production order cancelled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to cancel production order: ' . $e->getMessage());
        }
    }

    /**
     * Get production order statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_orders' => ProductionOrder::count(),
                'pending_orders' => ProductionOrder::where('status', 'pending')->count(),
                'in_progress_orders' => ProductionOrder::where('status', 'in_progress')->count(),
                'completed_orders' => ProductionOrder::where('status', 'completed')->count(),
                'cancelled_orders' => ProductionOrder::where('status', 'cancelled')->count(),
                'total_quantity_produced' => ProductionOrder::where('status', 'completed')->sum('quantity_produced'),
                'average_efficiency' => ProductionOrder::where('status', 'completed')->avg('efficiency_percentage') ?? 0,
                'orders_by_priority' => ProductionOrder::selectRaw('priority, count(*) as count')
                    ->where('status', '!=', 'cancelled')
                    ->groupBy('priority')
                    ->pluck('count', 'priority'),
                'recent_orders' => ProductionOrder::with('recipe.product')
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];

            return $this->successResponse($stats, 'Production order statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Check material availability for production
     */
    private function checkMaterialAvailability($recipe, $quantity)
    {
        $recipe->load('recipeIngredients');
        
        $availability = [
            'can_produce' => true,
            'materials' => [],
        ];

        foreach ($recipe->recipeIngredients as $ingredient) {
            $material = $ingredient->rawMaterial;
            $required = $ingredient->quantity * $quantity;
            $available = $material->current_stock;

            $materialStatus = [
                'name' => $material->name,
                'required' => $required,
                'available' => $available,
                'unit' => $ingredient->unit,
                'sufficient' => $available >= $required,
                'shortage' => max(0, $required - $available),
            ];

            if (!$materialStatus['sufficient']) {
                $availability['can_produce'] = false;
            }

            $availability['materials'][] = $materialStatus;
        }

        return $availability;
    }

    /**
     * Calculate material requirements for production order
     */
    private function calculateMaterialRequirements($order)
    {
        $requirements = [];
        
        foreach ($order->recipe->ingredients as $ingredient) {
            $requirements[] = [
                'material' => $ingredient->rawMaterial->name,
                'quantity_per_batch' => $ingredient->quantity,
                'total_required' => $ingredient->quantity * $order->quantity_planned,
                'unit' => $ingredient->unit,
                'current_stock' => $ingredient->rawMaterial->current_stock,
                'unit_cost' => $ingredient->rawMaterial->unit_cost,
                'total_cost' => $ingredient->quantity * $order->quantity_planned * $ingredient->rawMaterial->unit_cost,
            ];
        }

        return $requirements;
    }

    public function getProductionHistory(Request $request): JsonResponse
    {
        try {
            $query = ProductionOrder::with(['product', 'recipe'])
                // ->where('status', 'completed')
                ->orderBy('start_date', 'desc');

            // Apply search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhereHas('product', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                    //   ->orWhereHas('operator', function ($q) use ($search) {
                    //       $q->where('name', 'like', "%{$search}%");
                    //   });
                });
            }

            // Apply product filter
            if ($request->has('product_id') && $request->product_id) {
                $query->where('product_id', $request->product_id);
            }

            // Apply date range filter
            // if ($request->has('start_date') && $request->start_date) {
            //     $query->whereDate('completed_at', '>=', $request->start_date);
            // }

            // if ($request->has('end_date') && $request->end_date) {
            //     $query->whereDate('completed_at', '<=', $request->end_date);
            // }

            // Apply period filter
            if ($request->has('period')) {
                $query->where(function ($q) use ($request) {
                    // switch ($request->period) {
                    //     case 'today':
                    //         $q->whereDate('completed_at', today());
                    //         break;
                    //     case 'week':
                    //         $q->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    //         break;
                    //     case 'month':
                    //         $q->whereMonth('completed_at', now()->month)
                    //           ->whereYear('completed_at', now()->year);
                    //         break;
                    //     case 'quarter':
                    //         $q->whereBetween('completed_at', [
                    //             now()->startOfQuarter(),
                    //             now()->endOfQuarter()
                    //         ]);
                    //         break;
                    // }
                });
            }

            $orders = $query->paginate($request->get('per_page', 15));

            // Transform data for frontend
            $transformedOrders = $orders->getCollection()->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'product_name' => $order->product->name ?? 'N/A',
                    'product_sku' => $order->product->sku ?? 'N/A',
                    'batch_count' => $order->batch_count,
                    'target_production' => $order->quantity,
                    'actual_production' => $order->actual_quantity ?? $order->quantity,
                    'completed_date' => $order->start_date,
                    'estimated_duration' => $order->estimated_time / 60, // convert to hours
                    'actual_duration' => $order->actual_production_time ? $order->actual_production_time / 60 : 0,
                    'efficiency' => $this->calculateEfficiency($order),
                    'estimated_cost' => $order->estimated_cost,
                    'actual_cost' => $order->actual_cost,
                    'qc_status' => $order->quality_status ?? 'not_required',
                    'operator_name' => $order->operator->name ?? 'Sultan',
                    'recipe_version' => $order->recipe->version ?? '1.0'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedOrders,
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch production history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get production statistics
     */
    public function getProductionStats(Request $request): JsonResponse
    {
        try {
            $query = ProductionOrder::where('status', 'completed');

            // Apply date range if provided
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('completed_at', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('completed_at', '<=', $request->end_date);
            }

            $stats = [
                'total_orders' => $query->count(),
                'total_production' => $query->sum('actual_quantity'),
                'total_cost' => $query->sum('actual_cost'),
                'average_efficiency' => $query->avg(DB::raw('(actual_quantity / quantity) * 100')) ?? 0,
                'average_duration' => $query->avg('actual_production_time') / 60 ?? 0
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch production stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed order analysis
     */
    public function getOrderAnalysis($id): JsonResponse
    {
        try {
            $order = ProductionOrder::with([
                'product', 
                'recipe', 
                'operator',
                'qualityInspection',
                'productionLogs'
            ])->findOrFail($id);

            $analysis = [
                'order' => $order,
                'efficiency_analysis' => $this->getEfficiencyAnalysis($order),
                'cost_analysis' => $this->getCostAnalysis($order),
                'quality_analysis' => $this->getQualityAnalysis($order),
                'timeline_analysis' => $this->getTimelineAnalysis($order)
            ];

            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate order report
     */
    public function generateOrderReport($id): JsonResponse
    {
        try {
            $order = ProductionOrder::with([
                'product', 
                'recipe', 
                'operator',
                'qualityInspection',
                'productionLogs'
            ])->findOrFail($id);

            $report = [
                'order_summary' => $order,
                'production_metrics' => [
                    'efficiency' => $this->calculateEfficiency($order),
                    'cost_variance' => $order->actual_cost - $order->estimated_cost,
                    'time_variance' => $order->actual_production_time - $order->estimated_time,
                    'yield_rate' => ($order->actual_quantity / $order->quantity) * 100
                ],
                'quality_metrics' => $this->getQualityMetrics($order),
                'recommendations' => $this->generateRecommendations($order)
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate production efficiency
     */
    private function calculateEfficiency(ProductionOrder $order): float
    {
        if ($order->actual_production_time <= 0 || $order->estimated_time <= 0) {
            return 0;
        }

        $timeEfficiency = ($order->estimated_time / $order->actual_production_time) * 100;
        $yieldEfficiency = ($order->actual_quantity / $order->quantity) * 100;
        
        return round(($timeEfficiency + $yieldEfficiency) / 2, 2);
    }

    /**
     * Get efficiency analysis
     */
    private function getEfficiencyAnalysis(ProductionOrder $order): array
    {
        return [
            'time_efficiency' => $order->estimated_time > 0 ? 
                round(($order->estimated_time / $order->actual_production_time) * 100, 2) : 0,
            'yield_efficiency' => $order->quantity > 0 ? 
                round(($order->actual_quantity / $order->quantity) * 100, 2) : 0,
            'overall_efficiency' => $this->calculateEfficiency($order)
        ];
    }

    /**
     * Get cost analysis
     */
    private function getCostAnalysis(ProductionOrder $order): array
    {
        return [
            'estimated_cost' => $order->estimated_cost,
            'actual_cost' => $order->actual_cost,
            'cost_variance' => $order->actual_cost - $order->estimated_cost,
            'cost_variance_percentage' => $order->estimated_cost > 0 ? 
                round((($order->actual_cost - $order->estimated_cost) / $order->estimated_cost) * 100, 2) : 0,
            'cost_per_unit' => $order->actual_quantity > 0 ? 
                round($order->actual_cost / $order->actual_quantity, 2) : 0
        ];
    }

    /**
     * Get quality analysis
     */
    private function getQualityAnalysis(ProductionOrder $order): array
    {
        $inspection = $order->qualityInspection;
        
        if (!$inspection) {
            return [
                'status' => 'not_required',
                'defect_rate' => 0,
                'pass_rate' => 100
            ];
        }

        return [
            'status' => $inspection->status,
            'defect_rate' => $inspection->total_checked > 0 ? 
                round(($inspection->defect_count / $inspection->total_checked) * 100, 2) : 0,
            'pass_rate' => $inspection->total_checked > 0 ? 
                round(($inspection->passed_count / $inspection->total_checked) * 100, 2) : 0,
            'defects' => $inspection->defects ?? []
        ];
    }

    /**
     * Get timeline analysis
     */
    private function getTimelineAnalysis(ProductionOrder $order): array
    {
        $logs = $order->productionLogs;

        return [
            'total_duration' => $order->actual_production_time,
            'setup_time' => $logs->where('type', 'setup')->sum('duration') ?? 0,
            'production_time' => $logs->where('type', 'production')->sum('duration') ?? 0,
            'downtime' => $logs->where('type', 'downtime')->sum('duration') ?? 0,
            'log_entries' => $logs->count()
        ];
    }

    /**
     * Get quality metrics
     */
    private function getQualityMetrics(ProductionOrder $order): array
    {
        $inspection = $order->qualityInspection;
        
        if (!$inspection) {
            return [
                'status' => 'Not Required',
                'defect_rate' => '0%',
                'pass_rate' => '100%'
            ];
        }

        return [
            'status' => ucfirst($inspection->status),
            'defect_rate' => round(($inspection->defect_count / $inspection->total_checked) * 100, 2) . '%',
            'pass_rate' => round(($inspection->passed_count / $inspection->total_checked) * 100, 2) . '%',
            'total_checked' => $inspection->total_checked,
            'defect_count' => $inspection->defect_count
        ];
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(ProductionOrder $order): array
    {
        $recommendations = [];
        $efficiency = $this->calculateEfficiency($order);
        $costVariance = $order->actual_cost - $order->estimated_cost;

        if ($efficiency < 85) {
            $recommendations[] = [
                'type' => 'efficiency',
                'message' => 'Efisiensi produksi rendah. Pertimbangkan untuk mengoptimalkan proses setup dan mengurangi downtime.',
                'priority' => 'high'
            ];
        }

        if ($costVariance > ($order->estimated_cost * 0.1)) {
            $recommendations[] = [
                'type' => 'cost',
                'message' => 'Biaya aktual melebihi budget lebih dari 10%. Evaluasi penggunaan material dan tenaga kerja.',
                'priority' => 'high'
            ];
        }

        if ($order->actual_quantity < $order->quantity) {
            $recommendations[] = [
                'type' => 'yield',
                'message' => 'Hasil produksi tidak mencapai target. Periksa kualitas bahan baku dan proses produksi.',
                'priority' => 'medium'
            ];
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'success',
                'message' => 'Produksi berjalan dengan baik. Pertahankan kinerja ini.',
                'priority' => 'low'
            ];
        }

        return $recommendations;
    }
}