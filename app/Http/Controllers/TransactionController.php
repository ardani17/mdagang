<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Transaction::with(['customer', 'supplier', 'order', 'invoice', 'creator']);

            // Apply filters
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->has('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->has('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->has('date_from')) {
                $query->where('transaction_date', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('transaction_date', '<=', $request->date_to);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('transaction_number', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'transaction_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $transactions = $query->paginate($perPage);

            // Calculate summary
            $summary = [
                'total_income' => Transaction::completed()->income()->sum('amount'),
                'total_expense' => Transaction::completed()->expense()->sum('amount'),
                'net_balance' => Transaction::completed()->income()->sum('amount') - 
                                Transaction::completed()->expense()->sum('amount'),
            ];

            return $this->successResponse([
                'transactions' => $transactions,
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch transactions: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created transaction
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:income,expense',
            'category' => 'required|in:sale,purchase,payment_received,payment_made,refund,salary,utility,rent,tax,other',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,check,online,other',
            'transaction_date' => 'nullable|date',
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_id' => 'nullable|exists:orders,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'reference_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            // Validate business rules
            if ($request->type === 'income' && !$request->customer_id && !$request->invoice_id) {
                return $this->errorResponse('Income transactions require either customer or invoice reference');
            }

            if ($request->type === 'expense' && !$request->supplier_id && !in_array($request->category, ['salary', 'utility', 'rent', 'tax', 'other'])) {
                return $this->errorResponse('Expense transactions require supplier reference unless category is operational expense');
            }

            // Create transaction
            $transaction = Transaction::create([
                'type' => $request->type,
                'category' => $request->category,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_date' => $request->transaction_date ?? now(),
                'customer_id' => $request->customer_id,
                'supplier_id' => $request->supplier_id,
                'order_id' => $request->order_id,
                'invoice_id' => $request->invoice_id,
                'reference_number' => $request->reference_number,
                'description' => $request->description,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'create',
                'model_type' => 'Transaction',
                'model_id' => $transaction->id,
                'description' => "Created {$request->type} transaction #{$transaction->transaction_number}",
                'changes' => json_encode($transaction->toArray()),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Transaction created successfully',
                'transaction' => $transaction->load(['customer', 'supplier', 'order', 'invoice']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create transaction: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified transaction
     */
    public function show($id): JsonResponse
    {
        try {
            $transaction = Transaction::with(['customer', 'supplier', 'order', 'invoice', 'creator'])
                ->findOrFail($id);

            return $this->successResponse([
                'transaction' => $transaction,
                'summary' => $transaction->getSummary(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Transaction not found');
        }
    }

    /**
     * Update the specified transaction
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|in:income,expense',
            'category' => 'sometimes|in:sale,purchase,payment_received,payment_made,refund,salary,utility,rent,tax,other',
            'amount' => 'sometimes|numeric|min:0',
            'payment_method' => 'sometimes|in:cash,bank_transfer,credit_card,debit_card,check,online,other',
            'transaction_date' => 'sometimes|date',
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,processing,completed,failed,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);

            // Check if transaction can be updated
            if (in_array($transaction->status, ['completed', 'cancelled', 'reversed'])) {
                return $this->errorResponse("Cannot update {$transaction->status} transaction");
            }

            $oldData = $transaction->toArray();
            $transaction->update($request->all());

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Transaction',
                'model_id' => $transaction->id,
                'description' => "Updated transaction #{$transaction->transaction_number}",
                'changes' => json_encode([
                    'old' => $oldData,
                    'new' => $transaction->toArray(),
                ]),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Transaction updated successfully',
                'transaction' => $transaction->load(['customer', 'supplier', 'order', 'invoice']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update transaction: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified transaction
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);

            // Check if transaction can be deleted
            if ($transaction->status === 'completed') {
                return $this->errorResponse('Cannot delete completed transaction. Please reverse it instead.');
            }

            // Log activity before deletion
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'delete',
                'model_type' => 'Transaction',
                'model_id' => $transaction->id,
                'description' => "Deleted transaction #{$transaction->transaction_number}",
                'changes' => json_encode($transaction->toArray()),
            ]);

            $transaction->delete();
            DB::commit();

            return $this->successResponse([
                'message' => 'Transaction deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete transaction: ' . $e->getMessage());
        }
    }

    /**
     * Complete a transaction
     */
    public function complete($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);

            if ($transaction->status === 'completed') {
                return $this->errorResponse('Transaction is already completed');
            }

            $transaction->complete();

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Transaction',
                'model_id' => $transaction->id,
                'description' => "Completed transaction #{$transaction->transaction_number}",
                'changes' => json_encode(['status' => 'completed']),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Transaction completed successfully',
                'transaction' => $transaction->load(['customer', 'supplier', 'order', 'invoice']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to complete transaction: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a transaction
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
            $transaction = Transaction::findOrFail($id);

            if (!$transaction->cancel($request->reason)) {
                return $this->errorResponse('Cannot cancel this transaction');
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Transaction',
                'model_id' => $transaction->id,
                'description' => "Cancelled transaction #{$transaction->transaction_number}",
                'changes' => json_encode(['status' => 'cancelled', 'reason' => $request->reason]),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Transaction cancelled successfully',
                'transaction' => $transaction->load(['customer', 'supplier', 'order', 'invoice']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to cancel transaction: ' . $e->getMessage());
        }
    }

    /**
     * Reverse a transaction
     */
    public function reverse(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);

            if ($transaction->status !== 'completed') {
                return $this->errorResponse('Only completed transactions can be reversed');
            }

            $reverseTransaction = $transaction->reverse($request->reason);

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'update',
                'model_type' => 'Transaction',
                'model_id' => $transaction->id,
                'description' => "Reversed transaction #{$transaction->transaction_number}",
                'changes' => json_encode([
                    'original_transaction' => $transaction->id,
                    'reverse_transaction' => $reverseTransaction->id,
                    'reason' => $request->reason,
                ]),
            ]);

            DB::commit();

            return $this->successResponse([
                'message' => 'Transaction reversed successfully',
                'original_transaction' => $transaction,
                'reverse_transaction' => $reverseTransaction->load(['customer', 'supplier', 'order', 'invoice']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to reverse transaction: ' . $e->getMessage());
        }
    }

    /**
     * Get cash flow report
     */
    public function cashFlow(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

            $cashFlow = Transaction::getCashFlow($startDate, $endDate);

            // Group by date for chart
            $dailyFlow = Transaction::completed()
                ->dateRange($startDate, $endDate)
                ->selectRaw('DATE(transaction_date) as date')
                ->selectRaw('SUM(CASE WHEN type = \'income\' THEN amount ELSE 0 END) as income')
                ->selectRaw('SUM(CASE WHEN type = \'expense\' THEN amount ELSE 0 END) as expense')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $this->successResponse([
                'summary' => [
                    'total_income' => $cashFlow['income'],
                    'total_expense' => $cashFlow['expense'],
                    'net_cash_flow' => $cashFlow['net'],
                ],
                'daily_flow' => $dailyFlow,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate cash flow report: ' . $e->getMessage());
        }
    }

    /**
     * Get category breakdown
     */
    public function categoryBreakdown(Request $request): JsonResponse
    {
        try {
            $type = $request->get('type'); // income or expense
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

            $breakdown = Transaction::getCategoryBreakdown($type, $startDate, $endDate);

            // Add category labels
            $categories = [
                'sale' => 'Sales',
                'purchase' => 'Purchase',
                'payment_received' => 'Payment Received',
                'payment_made' => 'Payment Made',
                'refund' => 'Refund',
                'salary' => 'Salary',
                'utility' => 'Utility',
                'rent' => 'Rent',
                'tax' => 'Tax',
                'other' => 'Other',
            ];

            $formattedBreakdown = [];
            foreach ($breakdown as $category => $data) {
                $formattedBreakdown[] = [
                    'category' => $category,
                    'label' => $categories[$category] ?? 'Unknown',
                    'count' => $data['count'],
                    'total' => $data['total'],
                    'average' => $data['average'],
                    'percentage' => 0, // Will calculate below
                ];
            }

            // Calculate percentages
            $grandTotal = array_sum(array_column($formattedBreakdown, 'total'));
            if ($grandTotal > 0) {
                foreach ($formattedBreakdown as &$item) {
                    $item['percentage'] = round(($item['total'] / $grandTotal) * 100, 2);
                }
            }

            return $this->successResponse([
                'breakdown' => $formattedBreakdown,
                'total' => $grandTotal,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
                'type' => $type ?? 'all',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate category breakdown: ' . $e->getMessage());
        }
    }

    /**
     * Export transactions to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $query = Transaction::with(['customer', 'supplier', 'order', 'invoice']);

            // Apply same filters as index
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('date_from')) {
                $query->where('transaction_date', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->where('transaction_date', '<=', $request->date_to);
            }

            $transactions = $query->orderBy('transaction_date', 'desc')->get();

            $csvData = [];
            $csvData[] = ['Transaction Number', 'Date', 'Type', 'Category', 'Amount', 'Payment Method', 'Customer', 'Supplier', 'Status', 'Description'];

            foreach ($transactions as $transaction) {
                $csvData[] = [
                    $transaction->transaction_number,
                    $transaction->transaction_date->format('Y-m-d H:i'),
                    $transaction->type_label,
                    $transaction->category_label,
                    $transaction->amount,
                    $transaction->payment_method_label,
                    $transaction->customer?->name ?? '-',
                    $transaction->supplier?->name ?? '-',
                    $transaction->status_label,
                    $transaction->description ?? '-',
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
                'filename' => 'transactions_' . date('Y-m-d_His') . '.csv',
                'count' => $transactions->count(),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export transactions: ' . $e->getMessage());
        }
    }
}