<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['customer', 'items.product', 'invoice', 'creator']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->has('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->has('date_from')) {
                $query->where('order_date', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('order_date', '<=', $request->date_to);
            }

            if ($request->has('delivery_date_from')) {
                $query->where('delivery_date', '>=', $request->delivery_date_from);
            }

            if ($request->has('delivery_date_to')) {
                $query->where('delivery_date', '<=', $request->delivery_date_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Check for overdue deliveries
            if ($request->has('overdue') && $request->overdue) {
                $query->where('delivery_date', '<', now())
                      ->whereNotIn('status', ['delivered', 'completed', 'cancelled']);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'order_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $orders = $query->paginate($perPage);

            // Calculate summary
            $summary = [
                'total_orders' => Order::count(),
                'pending_orders' => Order::pending()->count(),
                'processing_orders' => Order::processing()->count(),
                'completed_orders' => Order::completed()->count(),
                'total_revenue' => Order::completed()->sum('total_amount'),
                'unpaid_amount' => Order::unpaid()->sum('total_amount'),
            ];

            return $this->successResponse([
                'orders' => $orders,
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch orders: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'nullable|date',
            'delivery_date' => 'nullable|date|after_or_equal:order_date',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            // Check customer credit limit
            $customer = Customer::findOrFail($request->customer_id);
            if ($customer->credit_limit > 0) {
                $outstandingBalance = $customer->getOutstandingBalance();
                if ($outstandingBalance >= $customer->credit_limit) {
                    return $this->errorResponse('Customer has exceeded credit limit');
                }
            }

            // Create order
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date ?? now(),
                'delivery_date' => $request->delivery_date,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'shipping_cost' => $request->shipping_cost ?? 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'shipping_address' => $request->shipping_address ?? $customer->address,
                'billing_address' => $request->billing_address ?? $customer->address,
                'notes' => $request->notes,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
                'created_by' => auth()->id(),
            ]);

            // Add order items
            $subtotal = 0;
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                // Check product availability
                if ($product->current_stock < $itemData['quantity']) {
                    DB::rollBack();
                    return $this->errorResponse("Insufficient stock for product: {$product->name}. Available: {$product->current_stock}");
                }

                $price = $itemData['price'] ?? $product->selling_price;
                $discount = $itemData['discount'] ?? 0;
                $total = ($price * $itemData['quantity']) - $discount;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $price,
                    'discount' => $discount,
                    'total' => $total,
                ]);

                $subtotal += $total;
            }

            // Calculate and update totals
            $taxAmount = $subtotal * 0.1; // 10% tax
            $totalAmount = $subtotal + $taxAmount + $order->shipping_cost - $order->discount_amount;

            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'create',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Created order #{$order->order_number} for customer {$customer->name}",
                'changes' => json_encode($order->toArray()),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Order created successfully',
                'order' => $order->load(['customer', 'items.product']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order
     */
    public function show($id): JsonResponse
    {
        try {
            $order = Order::with(['customer', 'items.product', 'invoice', 'transaction', 'creator'])
                ->findOrFail($id);

            return $this->successResponse([
                'order' => $order,
                'summary' => $order->getSummary(),
                'can_confirm' => $order->canBeConfirmed(),
                'can_process' => $order->canBeProcessed(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Order not found');
        }
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'delivery_date' => 'nullable|date',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            // Check if order can be updated
            if (in_array($order->status, ['completed', 'delivered', 'cancelled'])) {
                return $this->errorResponse("Cannot update {$order->status} order");
            }

            $oldData = $order->toArray();
            
            // Update order details
            $order->update($request->only([
                'delivery_date',
                'shipping_cost',
                'discount_amount',
                'shipping_address',
                'billing_address',
                'notes',
            ]));

            // Recalculate totals if amounts changed
            if ($request->has(['shipping_cost', 'discount_amount'])) {
                $order->calculateTotals();
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Updated order #{$order->order_number}",
                'changes' => json_encode([
                    'old' => $oldData,
                    'new' => $order->toArray(),
                ]),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Order updated successfully',
                'order' => $order->load(['customer', 'items.product']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified order
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            // Check if order can be deleted
            if (!in_array($order->status, ['pending', 'cancelled'])) {
                return $this->errorResponse('Only pending or cancelled orders can be deleted');
            }

            // Log activity before deletion
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'delete',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Deleted order #{$order->order_number}",
                'changes' => json_encode($order->toArray()),
            ]);

            // Delete order items first
            $order->items()->delete();
            $order->delete();

            DB::commit();

            return $this->successResponse([
                'message' => 'Order deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete order: ' . $e->getMessage());
        }
    }

    /**
     * Confirm an order
     */
    public function confirm($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::with('items.product')->findOrFail($id);

            if (!$order->confirm()) {
                return $this->errorResponse('Order cannot be confirmed. Check if it has items and is in pending status.');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Confirmed order #{$order->order_number}",
                'changes' => json_encode(['status' => 'confirmed']),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Order confirmed successfully',
                'order' => $order->load(['customer', 'items.product', 'invoice']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to confirm order: ' . $e->getMessage());
        }
    }

    /**
     * Process an order
     */
    public function process($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::with('items.product')->findOrFail($id);

            if (!$order->process()) {
                return $this->errorResponse('Order cannot be processed. Check product availability and order status.');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Started processing order #{$order->order_number}",
                'changes' => json_encode(['status' => 'processing']),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Order is now being processed',
                'order' => $order->load(['customer', 'items.product']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to process order: ' . $e->getMessage());
        }
    }

    /**
     * Ship an order
     */
    public function ship(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tracking_number' => 'nullable|string',
            'shipping_carrier' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            if ($order->status !== 'processing') {
                return $this->errorResponse('Only processing orders can be shipped');
            }

            $order->update([
                'status' => 'shipped',
                'notes' => $order->notes . "\nShipped on " . now()->format('Y-m-d H:i') . 
                          ($request->tracking_number ? "\nTracking: {$request->tracking_number}" : '') .
                          ($request->shipping_carrier ? "\nCarrier: {$request->shipping_carrier}" : '') .
                          ($request->notes ? "\n{$request->notes}" : ''),
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Shipped order #{$order->order_number}",
                'changes' => json_encode([
                    'status' => 'shipped',
                    'tracking_number' => $request->tracking_number,
                    'shipping_carrier' => $request->shipping_carrier,
                ]),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Order shipped successfully',
                'order' => $order->load(['customer', 'items.product']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to ship order: ' . $e->getMessage());
        }
    }

    /**
     * Mark order as delivered
     */
    public function deliver($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            if (!$order->markAsDelivered()) {
                return $this->errorResponse('Only shipped orders can be marked as delivered');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Marked order #{$order->order_number} as delivered",
                'changes' => json_encode(['status' => 'delivered']),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Order marked as delivered',
                'order' => $order->load(['customer', 'items.product']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to mark order as delivered: ' . $e->getMessage());
        }
    }

    /**
     * Complete an order
     */
    public function complete($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            if (!$order->complete()) {
                return $this->errorResponse('Order cannot be completed');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Completed order #{$order->order_number}",
                'changes' => json_encode(['status' => 'completed']),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Order completed successfully',
                'order' => $order->load(['customer', 'items.product']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to complete order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $order = Order::with('items.product')->findOrFail($id);

            if (!$order->cancel($request->reason)) {
                return $this->errorResponse('Order cannot be cancelled');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Cancelled order #{$order->order_number}",
                'changes' => json_encode(['status' => 'cancelled', 'reason' => $request->reason]),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Order cancelled successfully',
                'order' => $order->load(['customer', 'items.product']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Add item to order
     */
    public function addItem(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            // Check if order can be modified
            if (!in_array($order->status, ['pending', 'confirmed'])) {
                return $this->errorResponse('Cannot add items to this order');
            }

            $product = Product::findOrFail($request->product_id);

            // Check product availability
            if ($product->current_stock < $request->quantity) {
                return $this->errorResponse("Insufficient stock. Available: {$product->current_stock}");
            }

            $price = $request->price ?? $product->selling_price;
            $discount = $request->discount ?? 0;
            $total = ($price * $request->quantity) - $discount;

            $item = $order->addItem([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $price,
                'discount' => $discount,
                'total' => $total,
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Added item {$product->name} to order #{$order->order_number}",
                'changes' => json_encode($item->toArray()),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Item added to order successfully',
                'order' => $order->load(['customer', 'items.product']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to add item: ' . $e->getMessage());
        }
    }

    /**
     * Remove item from order
     */
    public function removeItem($id, $itemId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id);

            // Check if order can be modified
            if (!in_array($order->status, ['pending', 'confirmed'])) {
                return $this->errorResponse('Cannot remove items from this order');
            }

            if (!$order->removeItem($itemId)) {
                return $this->errorResponse('Item not found in this order');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'description' => "Removed item from order #{$order->order_number}",
                'changes' => json_encode(['removed_item_id' => $itemId]),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Item removed from order successfully',
                'order' => $order->load(['customer', 'items.product']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to remove item: ' . $e->getMessage());
        }
    }

    /**
     * Get order statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

            $stats = [
                'total_orders' => Order::whereBetween('order_date', [$startDate, $endDate])->count(),
                'completed_orders' => Order::completed()->whereBetween('order_date', [$startDate, $endDate])->count(),
                'cancelled_orders' => Order::where('status', 'cancelled')->whereBetween('order_date', [$startDate, $endDate])->count(),
                'total_revenue' => Order::completed()->whereBetween('order_date', [$startDate, $endDate])->sum('total_amount'),
                'average_order_value' => Order::completed()->whereBetween('order_date', [$startDate, $endDate])->avg('total_amount'),
                'pending_amount' => Order::whereNotIn('status', ['cancelled', 'completed'])->sum('total_amount'),
                'overdue_deliveries' => Order::where('delivery_date', '<', now())
                    ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
                    ->count(),
            ];

            // Daily orders for chart
            $dailyOrders = Order::whereBetween('order_date', [$startDate, $endDate])
                ->selectRaw('DATE(order_date) as date')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(total_amount) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Top customers
            $topCustomers = Order::with('customer')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->select('customer_id')
                ->selectRaw('COUNT(*) as order_count')
                ->selectRaw('SUM(total_amount) as total_spent')
                ->groupBy('customer_id')
                ->orderByDesc('total_spent')
                ->limit(5)
                ->get();

            return $this->successResponse([
                'statistics' => $stats,
                'daily_orders' => $dailyOrders,
                'top_customers' => $topCustomers,
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
     * Export orders to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['customer', 'items.product']);

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

            $orders = $query->orderBy('order_date', 'desc')->get();

            $csvData = [];
            $csvData[] = ['Order Number', 'Date', 'Customer', 'Status', 'Payment Status', 'Items', 'Subtotal', 'Tax', 'Shipping', 'Discount', 'Total', 'Delivery Date'];

            foreach ($orders as $order) {
                $csvData[] = [
                    $order->order_number,
                    $order->order_date->format('Y-m-d'),
                    $order->customer->name,
                    $order->status,
                    $order->payment_status,
                    $order->items->count(),
                    $order->subtotal,
                    $order->tax_amount,
                    $order->shipping_cost,
                    $order->discount_amount,
                    $order->total_amount,
                    $order->delivery_date?->format('Y-m-d') ?? '-',
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
                'filename' => 'orders_' . date('Y-m-d_His') . '.csv',
                'count' => $orders->count(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export orders: ' . $e->getMessage());
        }
    }
}