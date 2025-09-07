<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc');

        // Filter by search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_range') && !empty($request->date_range)) {
            $dateRange = $request->date_range;
            $now = Carbon::now();
            
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('order_date', $now->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('order_date', $now->subDay()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('order_date', [
                        $now->startOfWeek()->toDateString(),
                        $now->endOfWeek()->toDateString()
                    ]);
                    break;
                case 'this_month':
                    $query->whereBetween('order_date', [
                        $now->startOfMonth()->toDateString(),
                        $now->endOfMonth()->toDateString()
                    ]);
                    break;
                case 'last_month':
                    $query->whereBetween('order_date', [
                        $now->subMonth()->startOfMonth()->toDateString(),
                        $now->subMonth()->endOfMonth()->toDateString()
                    ]);
                    break;
            }
        }

        $perPage = $request->has('per_page') ? $request->per_page : 25;
        $orders = $query->paginate($perPage);

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
                'last_page' => $orders->lastPage(),
                'prev_page_url' => $orders->previousPageUrl(),
                'next_page_url' => $orders->nextPageUrl(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string',
            'order_date' => 'required|date',
            'pickup_time' => 'nullable|date_format:H:i',
            'payment_method' => 'required|in:cash,transfer,ewallet,credit',
            'status' => 'required|in:pending,confirmed,processing,ready,delivered,completed,cancelled',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Find or create customer
            $customer = Customer::firstOrCreate(
                ['phone' => $validated['customer_phone']],
                [
                    'name' => $validated['customer_name'],
                    'address' => $validated['customer_address'] ?? null
                ]
            );

            // Calculate order totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $itemTotal = $item['quantity'] * $item['price'];
                $subtotal += $itemTotal;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $itemTotal,
                ];
            }

            $taxAmount = $subtotal * 0.1;
            $totalAmount = $subtotal + $taxAmount + ($validated['shipping_cost'] ?? 0);

            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . Str::random(6),
                'customer_id' => $customer->id,
                'user_id' => auth()->id(),
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['pickup_time'] ? Carbon::parse($validated['order_date'] . ' ' . $validated['pickup_time']) : null,
                'status' => $validated['status'],
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'total_amount' => $totalAmount,
                'shipping_address' => $validated['customer_address'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items
            foreach ($itemsData as $itemData) {
                $order->items()->create($itemData);
                
                // Update product stock if needed
                $product = Product::find($itemData['product_id']);
                if ($product) {
                    $product->decrement('current_stock', $itemData['quantity']);
                    $product->increment('reserved_stock', $itemData['quantity']);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order->load(['customer', 'items.product'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'user']);
        return response()->json(['data' => $order]);
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,processing,ready,delivered,completed,cancelled',
            'payment_status' => 'sometimes|in:unpaid,partial,paid',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'Order updated successfully',
            'data' => $order->load(['customer', 'items.product'])
        ]);
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();
            
            // Restore product stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('current_stock', $item->quantity);
                    $product->decrement('reserved_stock', $item->quantity);
                }
            }
            
            $order->delete();
            DB::commit();

            return response()->json(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,confirmed,processing,ready,delivered,completed,cancelled',
        ]);

        Order::whereIn('id', $validated['order_ids'])
            ->update(['status' => $validated['status']]);

        return response()->json(['message' => 'Orders updated successfully']);
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        try {
            DB::beginTransaction();
            
            $orders = Order::with('items')->whereIn('id', $validated['order_ids'])->get();
            
            foreach ($orders as $order) {
                // Restore product stock
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('current_stock', $item->quantity);
                        $product->decrement('reserved_stock', $item->quantity);
                    }
                }
                
                $order->delete();
            }
            
            DB::commit();

            return response()->json(['message' => 'Orders deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function print(Order $order)
    {
        $order->load(['customer', 'items.product']);
        // In a real application, you would generate a PDF here
        return response()->json([
            'message' => 'Print order requested',
            'data' => $order
        ]);
    }

    public function export(Request $request)
    {
        $query = Order::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc');

        // Apply filters similar to index method
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        // In a real application, you would generate CSV or Excel file here
        return response()->json([
            'message' => 'Export requested',
            'count' => $orders->count(),
            'data' => $orders
        ]);
    }
}