<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomersExport;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Customer::query();
            
            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }
            
            // Apply status filter
            if ($request->has('status') && !empty($request->status)) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($request->status === 'vip') {
                    $query->where('type', 'business')->where('is_active', true);
                }
            }
            
            // Apply sorting
            $sortField = $request->get('sort', 'name');
            $sortDirection = 'asc';
            
            switch ($sortField) {
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'purchase':
                    // Assuming you have a relationship with orders
                    $query->withSum('orders', 'total_amount')
                         ->orderBy('orders_sum_total_amount', 'desc');
                    break;
                case 'last_order':
                    // Assuming you have a relationship with orders
                    $query->withMax('orders', 'order_date')
                         ->orderBy('orders_max_order_date', 'desc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
                    break;
            }
            
            // Pagination
            $perPage = $request->get('per_page', 25);
            $customers = $query->paginate($perPage);
            
            // Transform data for frontend
            $transformedCustomers = $customers->getCollection()->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'customer_id' => $customer->code,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'total_purchase' => $customer->total_purchase_amount ?? 0,
                    'total_orders' => $customer->orders_count ?? 0,
                    'last_order_date' => $customer->last_order_date,
                    'last_order_amount' => $customer->last_order_amount,
                    'status' => $this->getCustomerStatus($customer),
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'type' => $customer->type,
                    'credit_limit' => $customer->credit_limit,
                    'outstanding_balance' => $customer->outstanding_balance,
                    'is_active' => $customer->is_active,
                    'created_at' => $customer->created_at,
                    'updated_at' => $customer->updated_at
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $transformedCustomers,
                'meta' => [
                    'current_page' => $customers->currentPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                    'last_page' => $customers->lastPage(),
                    'from' => $customers->firstItem(),
                    'to' => $customers->lastItem(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function stats()
    {
        try {
            $totalCustomers = Customer::count();
            $activeCustomers = Customer::where('is_active', true)->count();
            
            // Calculate average purchase (you might need to adjust this based on your order structure)
            $avgPurchase = Customer::whereHas('orders')
                ->withSum('orders', 'total_amount')
                ->get()
                ->avg('orders_sum_total_amount') ?? 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_customers' => $totalCustomers,
                    'active_customers' => $activeCustomers,
                    'avg_purchase' => round($avgPurchase, 2),
                    'vip_customers' => Customer::where('type', 'business')
                        ->where('is_active', true)
                        ->count(),
                    'new_customers_this_month' => Customer::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customer statistics: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'type' => 'required|in:individual,business',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Generate customer code
            $lastCustomer = Customer::orderBy('id', 'desc')->first();
            $nextId = $lastCustomer ? $lastCustomer->id + 1 : 1;
            $code = 'CUST-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            
            $customer = Customer::create([
                'code' => $code,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'type' => $request->type,
                'tax_id' => $request->tax_id,
                'credit_limit' => $request->credit_limit ?? 0,
                'outstanding_balance' => 0,
                'notes' => $request->notes,
                'is_active' => $request->is_active ?? true
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Customer $customer)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $customer->load(['orders' => function($query) {
                    $query->orderBy('created_at', 'desc')->take(5);
                }])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customer: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'type' => 'required|in:individual,business',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $customer->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(Customer $customer)
    {
        try {
            // Check if customer has orders
            if ($customer->orders()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete customer with existing orders'
                ], 422);
            }
            
            $customer->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updateStatus(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $customer->update(['is_active' => $request->status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Customer status updated successfully',
                'data' => $customer
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function bulkActions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,activate,deactivate',
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $customers = Customer::whereIn('id', $request->customer_ids);
            
            switch ($request->action) {
                case 'delete':
                    // Check if any customer has orders
                    $customersWithOrders = $customers->whereHas('orders')->count();
                    if ($customersWithOrders > 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Cannot delete customers with existing orders'
                        ], 422);
                    }
                    $customers->delete();
                    $message = 'Customers deleted successfully';
                    break;
                    
                case 'activate':
                    $customers->update(['is_active' => true]);
                    $message = 'Customers activated successfully';
                    break;
                    
                case 'deactivate':
                    $customers->update(['is_active' => false]);
                    $message = 'Customers deactivated successfully';
                    break;
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function export(Request $request)
    {
        try {
            $filters = $request->only(['search', 'status', 'sort']);
            
            return Excel::download(new CustomersExport($filters), 'customers-' . date('Y-m-d') . '.xlsx');
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export customers: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getCustomerStatus($customer)
    {
        if (!$customer->is_active) {
            return 'inactive';
        }
        
        if ($customer->type === 'business') {
            return 'vip';
        }
        
        return 'active';
    }
}