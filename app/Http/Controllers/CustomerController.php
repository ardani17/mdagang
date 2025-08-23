<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter by credit status
        if ($request->has('credit_status')) {
            if ($request->credit_status === 'exceeded') {
                $query->whereColumn('outstanding_balance', '>', 'credit_limit');
            } elseif ($request->credit_status === 'available') {
                $query->whereColumn('outstanding_balance', '<', 'credit_limit');
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Include relationships
        if ($request->boolean('with_orders')) {
            $query->with('orders');
        }

        // Pagination
        if ($request->get('per_page') === 'all') {
            $customers = $query->get();
            return $this->successResponse($customers);
        }

        $customers = $query->paginate($request->get('per_page', 15));
        return $this->paginatedResponse($customers);
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'type' => 'required|in:individual,business',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $customer = Customer::create($validated);

        ActivityLog::logCreation($customer, 'Customer created: ' . $customer->name);

        return $this->successResponse($customer, 'Customer created successfully', 201);
    }

    /**
     * Display the specified customer
     */
    public function show($id)
    {
        $customer = Customer::with(['orders' => function ($query) {
            $query->latest()->limit(10);
        }, 'invoices' => function ($query) {
            $query->latest()->limit(10);
        }, 'payments' => function ($query) {
            $query->latest()->limit(10);
        }])->findOrFail($id);

        $data = $customer->toArray();
        $data['statistics'] = [
            'total_orders' => $customer->orders()->count(),
            'completed_orders' => $customer->orders()->where('status', 'completed')->count(),
            'total_spent' => $customer->orders()->where('status', 'completed')->sum('total_amount'),
            'unpaid_invoices' => $customer->invoices()->whereIn('status', ['sent', 'partial', 'overdue'])->count(),
            'available_credit' => $customer->available_credit,
            'credit_exceeded' => $customer->hasExceededCreditLimit(),
        ];

        return $this->successResponse($data);
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('customers')->ignore($customer->id),
            ],
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
            'city' => 'sometimes|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'type' => 'sometimes|in:individual,business',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $oldData = $customer->toArray();
        $customer->update($validated);

        // Log changes
        $changes = [];
        foreach ($validated as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            ActivityLog::logUpdate($customer, $changes, 'Customer updated: ' . $customer->name);
        }

        // Update outstanding balance if needed
        $customer->updateOutstandingBalance();

        return $this->successResponse($customer, 'Customer updated successfully');
    }

    /**
     * Remove the specified customer
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        // Check if customer has orders
        if ($customer->orders()->exists()) {
            return $this->errorResponse('Cannot delete customer with existing orders', 422);
        }

        // Check if customer has unpaid invoices
        if ($customer->invoices()->whereIn('status', ['sent', 'partial', 'overdue'])->exists()) {
            return $this->errorResponse('Cannot delete customer with unpaid invoices', 422);
        }

        $customerName = $customer->name;
        $customer->delete();

        ActivityLog::logDeletion($customer, 'Customer deleted: ' . $customerName);

        return $this->successResponse(null, 'Customer deleted successfully');
    }

    /**
     * Get customer orders
     */
    public function orders($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        
        $query = $customer->orders()->with('items.product');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('order_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('order_date', '<=', $request->to_date);
        }

        // Sorting
        $query->orderBy($request->get('sort_by', 'order_date'), $request->get('sort_order', 'desc'));

        $orders = $query->paginate($request->get('per_page', 15));
        
        return $this->paginatedResponse($orders);
    }

    /**
     * Get customer invoices
     */
    public function invoices($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        
        $query = $customer->invoices();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // Sorting
        $query->orderBy($request->get('sort_by', 'invoice_date'), $request->get('sort_order', 'desc'));

        $invoices = $query->paginate($request->get('per_page', 15));
        
        return $this->paginatedResponse($invoices);
    }

    /**
     * Get customer payments
     */
    public function payments($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        
        $query = $customer->payments();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        // Sorting
        $query->orderBy($request->get('sort_by', 'payment_date'), $request->get('sort_order', 'desc'));

        $payments = $query->paginate($request->get('per_page', 15));
        
        return $this->paginatedResponse($payments);
    }

    /**
     * Get customer statement
     */
    public function statement($id, Request $request)
    {
        $customer = Customer::findOrFail($id);
        
        $fromDate = $request->get('from_date', now()->subMonths(3)->startOfDay());
        $toDate = $request->get('to_date', now()->endOfDay());

        // Get all transactions
        $invoices = $customer->invoices()
            ->whereBetween('invoice_date', [$fromDate, $toDate])
            ->get()
            ->map(function ($invoice) {
                return [
                    'date' => $invoice->invoice_date,
                    'type' => 'invoice',
                    'reference' => $invoice->invoice_number,
                    'description' => 'Invoice',
                    'debit' => $invoice->total_amount,
                    'credit' => 0,
                    'balance' => 0,
                ];
            });

        $payments = $customer->payments()
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->where('status', 'completed')
            ->get()
            ->map(function ($payment) {
                return [
                    'date' => $payment->payment_date,
                    'type' => 'payment',
                    'reference' => $payment->payment_number,
                    'description' => 'Payment received',
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'balance' => 0,
                ];
            });

        // Combine and sort by date
        $transactions = $invoices->concat($payments)->sortBy('date')->values();

        // Calculate running balance
        $balance = 0;
        $transactions = $transactions->map(function ($transaction) use (&$balance) {
            $balance += $transaction['debit'] - $transaction['credit'];
            $transaction['balance'] = $balance;
            return $transaction;
        });

        $data = [
            'customer' => $customer->only(['id', 'code', 'name', 'email', 'phone', 'address']),
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'transactions' => $transactions,
            'summary' => [
                'total_invoiced' => $invoices->sum('debit'),
                'total_paid' => $payments->sum('credit'),
                'balance' => $balance,
                'credit_limit' => $customer->credit_limit,
                'available_credit' => $customer->available_credit,
            ],
        ];

        return $this->successResponse($data);
    }

    /**
     * Update customer credit limit
     */
    public function updateCreditLimit(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'credit_limit' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $oldLimit = $customer->credit_limit;
        $customer->update(['credit_limit' => $validated['credit_limit']]);

        ActivityLog::log('update', $customer, [
            'credit_limit' => [
                'old' => $oldLimit,
                'new' => $validated['credit_limit'],
            ],
        ], 'Credit limit updated: ' . $validated['reason']);

        return $this->successResponse($customer, 'Credit limit updated successfully');
    }
}