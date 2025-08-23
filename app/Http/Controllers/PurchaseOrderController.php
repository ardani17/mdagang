<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of purchase orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PurchaseOrder::with(['supplier', 'items.rawMaterial', 'creator']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->has('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->has('date_from')) {
                $query->where('order_date', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('order_date', '<=', $request->date_to);
            }

            if ($request->has('expected_from')) {
                $query->where('expected_date', '>=', $request->expected_from);
            }

            if ($request->has('expected_to')) {
                $query->where('expected_date', '<=', $request->expected_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('po_number', 'like', "%{$search}%")
                      ->orWhereHas('supplier', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Check for overdue orders
            if ($request->has('overdue') && $request->overdue) {
                $query->overdue();
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'order_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $purchaseOrders = $query->paginate($perPage);

            // Calculate summary
            $summary = [
                'total_orders' => PurchaseOrder::count(),
                'pending_orders' => PurchaseOrder::pending()->count(),
                'approved_orders' => PurchaseOrder::approved()->count(),
                'received_orders' => PurchaseOrder::received()->count(),
                'overdue_orders' => PurchaseOrder::overdue()->count(),
                'total_value' => PurchaseOrder::sum('total_amount'),
                'unpaid_amount' => PurchaseOrder::where('payment_status', 'unpaid')->sum('total_amount'),
            ];

            // Transform data for frontend compatibility
            $transformedData = $purchaseOrders->getCollection()->map(function ($po) {
                try {
                    return [
                        'id' => $po->id,
                        'po_number' => $po->po_number,
                        'order_number' => $po->po_number,
                        'reference' => $po->reference ?? $po->po_number,
                        'supplier_id' => $po->supplier_id,
                        'supplier_name' => $po->supplier ? $po->supplier->name : 'Unknown',
                        'supplier_contact' => $po->supplier ? ($po->supplier->contact_person ?? $po->supplier->phone ?? '') : '',
                        'order_date' => $po->order_date ? $po->order_date->format('Y-m-d') : null,
                        'expected_date' => $po->expected_date ? $po->expected_date->format('Y-m-d') : null,
                        'status' => $po->status,
                        'payment_status' => $po->payment_status,
                        'total_items' => $po->items ? $po->items->count() : 0,
                        'total_quantity' => $po->items ? $po->items->sum('quantity') : 0,
                        'subtotal' => $po->subtotal,
                        'tax_amount' => $po->tax_amount,
                        'shipping_cost' => $po->shipping_cost,
                        'total_amount' => $po->total_amount,
                        'completed_date' => $po->received_date ? $po->received_date->format('Y-m-d') : null,
                        'created_at' => $po->created_at,
                        'updated_at' => $po->updated_at,
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error transforming purchase order data: ' . $e->getMessage(), ['po_id' => $po->id ?? 'unknown']);
                    return [
                        'id' => $po->id ?? null,
                        'po_number' => $po->po_number ?? 'Unknown',
                        'error' => 'Data transformation error'
                    ];
                }
            });

            $purchaseOrders->setCollection($transformedData);

            return $this->successResponse([
                'purchase_orders' => [
                    'data' => $transformedData,
                    'pagination' => [
                        'total' => $purchaseOrders->total(),
                        'per_page' => $purchaseOrders->perPage(),
                        'current_page' => $purchaseOrders->currentPage(),
                        'last_page' => $purchaseOrders->lastPage(),
                        'from' => $purchaseOrders->firstItem(),
                        'to' => $purchaseOrders->lastItem(),
                    ]
                ],
                'data' => $transformedData,
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch purchase orders: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return $this->errorResponse('Failed to fetch purchase orders: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'nullable|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'payment_terms' => 'nullable|string|max:50',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            // Check supplier status
            $supplier = Supplier::findOrFail($request->supplier_id);
            if (!$supplier->is_active) {
                return $this->errorResponse('Cannot create purchase order for inactive supplier');
            }

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date ?? now(),
                'expected_date' => $request->expected_date,
                'status' => 'draft',
                'payment_status' => 'unpaid',
                'payment_terms' => $request->payment_terms ?? 'Net 30',
                'shipping_cost' => $request->shipping_cost ?? 0,
                'reference' => $request->reference,
                'notes' => $request->notes,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'created_by' => auth()->id(),
            ]);

            // Add purchase order items
            $subtotal = 0;
            foreach ($request->items as $itemData) {
                $rawMaterial = RawMaterial::findOrFail($itemData['raw_material_id']);
                
                $unitPrice = $itemData['unit_price'] ?? $rawMaterial->last_purchase_price ?? 0;
                $totalPrice = $unitPrice * $itemData['quantity'];

                $purchaseOrder->items()->create([
                    'raw_material_id' => $rawMaterial->id,
                    'item_name' => $rawMaterial->name,
                    'item_code' => $rawMaterial->code ?? $rawMaterial->sku ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit' => $rawMaterial->unit ?? 'pcs',
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'received_quantity' => 0,
                ]);

                $subtotal += $totalPrice;
            }

            // Calculate and update totals with percentage-based tax and manual shipping
            $taxAmount = $request->tax_amount ?? 0; // Frontend calculates this from percentage
            $shippingCost = $request->shipping_cost ?? 0;
            $totalAmount = $subtotal + $taxAmount + $shippingCost;

            $purchaseOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'create',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Created purchase order #{$purchaseOrder->po_number} for supplier {$supplier->name}",
                'changes' => json_encode($purchaseOrder->toArray()),
                'risk_level' => 'low',
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Purchase order created successfully',
                'purchase_order' => $purchaseOrder->load(['supplier', 'items.rawMaterial']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase order
     */
    public function show($id): JsonResponse
    {
        try {
            $purchaseOrder = PurchaseOrder::with(['supplier', 'items.rawMaterial', 'creator'])
                ->findOrFail($id);

            return $this->successResponse([
                'purchase_order' => $purchaseOrder,
                'summary' => $purchaseOrder->getSummary(),
                'can_approve' => $purchaseOrder->canBeApproved(),
                'is_overdue' => $purchaseOrder->isOverdue(),
                'is_payment_overdue' => $purchaseOrder->isPaymentOverdue(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Purchase order not found');
        }
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'expected_date' => 'nullable|date',
            'payment_terms' => 'nullable|string|max:50',
            'shipping_cost' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);

            // Check if order can be updated
            if (in_array($purchaseOrder->status, ['received', 'cancelled'])) {
                return $this->errorResponse("Cannot update {$purchaseOrder->status} purchase order");
            }

            $oldData = $purchaseOrder->toArray();
            
            // Update purchase order details
            $purchaseOrder->update($request->only([
                'expected_date',
                'payment_terms',
                'shipping_cost',
                'reference',
                'notes',
            ]));

            // Recalculate totals if amounts changed
            if ($request->has('shipping_cost')) {
                $purchaseOrder->calculateTotals();
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Updated purchase order #{$purchaseOrder->po_number}",
                'changes' => json_encode([
                    'old' => $oldData,
                    'new' => $purchaseOrder->toArray(),
                ]),
                'risk_level' => 'low',
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Purchase order updated successfully',
                'purchase_order' => $purchaseOrder->load(['supplier', 'items.rawMaterial']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase order
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);

            // Check if order can be deleted
            if (!in_array($purchaseOrder->status, ['draft', 'cancelled'])) {
                return $this->errorResponse('Only pending or cancelled purchase orders can be deleted');
            }

            // Log activity before deletion
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'delete',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Deleted purchase order #{$purchaseOrder->po_number}",
                'changes' => json_encode($purchaseOrder->toArray()),
                'risk_level' => 'medium',
            ]);

            // Delete order items first
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();

            DB::commit();

            return $this->successResponse([
                'message' => 'Purchase order deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Approve a purchase order
     */
    public function approve($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::with('items.rawMaterial')->findOrFail($id);

            if (!$purchaseOrder->approve()) {
                return $this->errorResponse('Purchase order cannot be approved. Check if it has items and is in pending status.');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Approved purchase order #{$purchaseOrder->po_number}",
                'changes' => json_encode(['status' => 'sent']),
                'risk_level' => 'medium',
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Purchase order approved successfully',
                'purchase_order' => $purchaseOrder->load(['supplier', 'items.rawMaterial']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to approve purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Mark purchase order as sent
     */
    public function send($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);

            if (!$purchaseOrder->markAsSent()) {
                return $this->errorResponse('Only approved purchase orders can be marked as sent');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Sent purchase order #{$purchaseOrder->po_number} to supplier",
                'changes' => json_encode(['status' => 'confirmed']),
                'risk_level' => 'low',
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Purchase order marked as sent',
                'purchase_order' => $purchaseOrder->load(['supplier', 'items.rawMaterial']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to send purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Receive items for a purchase order
     */
    public function receive(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'received_items' => 'required|array',
            'received_items.*.item_id' => 'required|integer',
            'received_items.*.quantity' => 'required|numeric|min:0',
            'partial' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::with('items.rawMaterial')->findOrFail($id);

            // Prepare received items array
            $receivedItems = [];
            foreach ($request->received_items as $item) {
                $receivedItems[$item['item_id']] = $item['quantity'];
            }

            // Mark as partially or fully received
            if ($request->get('partial', false)) {
                if (!$purchaseOrder->markAsPartiallyReceived($receivedItems)) {
                    return $this->errorResponse('Purchase order cannot be received in current status');
                }
                $status = 'partially received';
            } else {
                if (!$purchaseOrder->markAsReceived($receivedItems)) {
                    return $this->errorResponse('Purchase order cannot be received in current status');
                }
                $status = 'fully received';
            }

            // Add notes if provided
            if ($request->notes) {
                $purchaseOrder->update([
                    'notes' => $purchaseOrder->notes . "\nReceived on " . now()->format('Y-m-d H:i') . ": " . $request->notes,
                ]);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Marked purchase order #{$purchaseOrder->po_number} as {$status}",
                'changes' => json_encode([
                    'status' => $purchaseOrder->status,
                    'received_items' => $receivedItems,
                ]),
                'risk_level' => 'low',
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => "Purchase order {$status} successfully",
                'purchase_order' => $purchaseOrder->load(['supplier', 'items.rawMaterial']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to receive purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a purchase order
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);

            if (!$purchaseOrder->cancel($request->reason ?? 'Cancelled by user')) {
                return $this->errorResponse('Purchase order cannot be cancelled');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Cancelled purchase order #{$purchaseOrder->po_number}",
                'changes' => json_encode(['status' => 'cancelled', 'reason' => $request->reason ?? 'Cancelled by user']),
                'risk_level' => 'medium',
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Purchase order cancelled successfully',
                'purchase_order' => $purchaseOrder->load(['supplier', 'items.rawMaterial']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to cancel purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Add item to purchase order
     */
    public function addItem(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);

            // Check if order can be modified
            if (!in_array($purchaseOrder->status, ['draft'])) {
                return $this->errorResponse('Cannot add items to this purchase order');
            }

            $rawMaterial = RawMaterial::findOrFail($request->raw_material_id);
            $unitPrice = $request->unit_price ?? $rawMaterial->last_purchase_price ?? 0;
            $totalPrice = $unitPrice * $request->quantity;

            $item = $purchaseOrder->addItem([
                'raw_material_id' => $rawMaterial->id,
                'item_name' => $rawMaterial->name,
                'item_code' => $rawMaterial->code ?? $rawMaterial->sku ?? null,
                'quantity' => $request->quantity,
                'unit' => $rawMaterial->unit ?? 'pcs',
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'received_quantity' => 0,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Added item {$rawMaterial->name} to purchase order #{$purchaseOrder->po_number}",
                'changes' => json_encode($item->toArray()),
                'risk_level' => 'low',
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Item added to purchase order successfully',
                'purchase_order' => $purchaseOrder->load(['supplier', 'items.rawMaterial']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to add item: ' . $e->getMessage());
        }
    }

    /**
     * Remove item from purchase order
     */
    public function removeItem($id, $itemId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($id);

            // Check if order can be modified
            if (!in_array($purchaseOrder->status, ['draft'])) {
                return $this->errorResponse('Cannot remove items from this purchase order');
            }

            if (!$purchaseOrder->removeItem($itemId)) {
                return $this->errorResponse('Item not found in this purchase order');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Removed item from purchase order #{$purchaseOrder->po_number}",
                'changes' => json_encode(['removed_item_id' => $itemId]),
                'risk_level' => 'low',
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Item removed from purchase order successfully',
                'purchase_order' => $purchaseOrder->load(['supplier', 'items.rawMaterial']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to remove item: ' . $e->getMessage());
        }
    }

    /**
     * Get purchase order statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

            $stats = [
                'total_orders' => PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->count(),
                'approved_orders' => PurchaseOrder::approved()->whereBetween('order_date', [$startDate, $endDate])->count(),
                'received_orders' => PurchaseOrder::received()->whereBetween('order_date', [$startDate, $endDate])->count(),
                'cancelled_orders' => PurchaseOrder::where('status', 'cancelled')->whereBetween('order_date', [$startDate, $endDate])->count(),
                'total_value' => PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->sum('total_amount'),
                'average_order_value' => PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])->avg('total_amount'),
                'overdue_orders' => PurchaseOrder::where('expected_date', '<', now())
                    ->whereNotIn('status', ['received', 'cancelled'])
                    ->count(),
                'unpaid_amount' => PurchaseOrder::where('payment_status', 'unpaid')->sum('total_amount'),
            ];

            // Daily orders for chart
            $dailyOrders = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])
                ->selectRaw('DATE(order_date) as date')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(total_amount) as value')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Top suppliers
            $topSuppliers = PurchaseOrder::with('supplier')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', 'received')
                ->select('supplier_id')
                ->selectRaw('COUNT(*) as order_count')
                ->selectRaw('SUM(total_amount) as total_value')
                ->groupBy('supplier_id')
                ->orderByDesc('total_value')
                ->limit(5)
                ->get();

            // Delivery performance
            $deliveryPerformance = [
                'on_time' => PurchaseOrder::received()
                    ->whereBetween('order_date', [$startDate, $endDate])
                    ->whereRaw('received_date <= expected_date')
                    ->count(),
                'late' => PurchaseOrder::received()
                    ->whereBetween('order_date', [$startDate, $endDate])
                    ->whereRaw('received_date > expected_date')
                    ->count(),
            ];

            return $this->successResponse([
                'statistics' => $stats,
                'daily_orders' => $dailyOrders,
                'top_suppliers' => $topSuppliers,
                'delivery_performance' => $deliveryPerformance,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate statistics: ' . $e->getMessage());
        }
    }

    /**
     * Export purchase orders to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $query = PurchaseOrder::with(['supplier', 'items.rawMaterial']);

            // Apply same filters as index
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from')) {
                $query->where('order_date', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('order_date', '<=', $request->date_to);
            }

            $purchaseOrders = $query->orderBy('order_date', 'desc')->get();

            $csvData = [];
            $csvData[] = ['Order Number', 'Date', 'Supplier', 'Status', 'Payment Status', 'Items', 'Subtotal', 'Tax', 'Shipping', 'Discount', 'Total', 'Expected Date', 'Received Date'];

            foreach ($purchaseOrders as $order) {
                $csvData[] = [
                    $order->po_number,
                    $order->order_date->format('Y-m-d'),
                    $order->supplier->name,
                    $order->status,
                    $order->payment_status,
                    $order->items->count(),
                    $order->subtotal,
                    $order->tax_amount,
                    $order->shipping_cost,
                    0, // discount_amount not available
                    $order->total_amount,
                    $order->expected_date?->format('Y-m-d') ?? '-',
                    $order->received_date?->format('Y-m-d') ?? '-',
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
                'filename' => 'purchase_orders_' . date('Y-m-d_His') . '.csv',
                'count' => $purchaseOrders->count(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export purchase orders: ' . $e->getMessage());
        }
    
    }

    /**
     * Quick receive purchase order (for testing/demo purposes)
     */
    public function quickReceive($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::with('items.rawMaterial')->findOrFail($id);

            // Auto-approve if draft
            if ($purchaseOrder->status === 'draft') {
                $purchaseOrder->update(['status' => 'sent']);
            }

            // Auto-confirm if sent
            if ($purchaseOrder->status === 'sent') {
                $purchaseOrder->update(['status' => 'confirmed']);
            }

            // Mark as received with full quantities
            $receivedItems = [];
            foreach ($purchaseOrder->items as $item) {
                $receivedItems[$item->id] = $item->quantity;
            }

            if (!$purchaseOrder->markAsReceived($receivedItems)) {
                return $this->errorResponse('Purchase order cannot be received in current status');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'module' => 'purchase_orders',
                'model_type' => 'PurchaseOrder',
                'model_id' => $purchaseOrder->id,
                'description' => "Quick received purchase order #{$purchaseOrder->po_number}",
                'changes' => json_encode([
                    'status' => 'received',
                    'received_items' => $receivedItems,
                ]),
                'risk_level' => 'low',
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Purchase order received successfully and stock updated',
                'purchase_order' => $purchaseOrder->load(['supplier', 'items.rawMaterial']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to receive purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Print purchase order
     */
    public function print($id)
    {
        try {
            $purchaseOrder = PurchaseOrder::with(['supplier', 'items.rawMaterial', 'creator'])
                ->findOrFail($id);

            // Return a simple print view or PDF
            return response()->json([
                'success' => true,
                'message' => 'Purchase order print data retrieved successfully',
                'data' => $purchaseOrder,
                'print_url' => route('ajax.purchase-orders.print', $id)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Purchase order not found');
        }
    }
}