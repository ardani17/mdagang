<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Payment::with(['invoice.order.customer']);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('reference_number', 'like', "%{$search}%")
                      ->orWhereHas('invoice', function ($q2) use ($search) {
                          $q2->where('invoice_number', 'like', "%{$search}%");
                      })
                      ->orWhereHas('invoice.order.customer', function ($q3) use ($search) {
                          $q3->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by payment method
            if ($request->has('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            // Filter by invoice
            if ($request->has('invoice_id')) {
                $query->where('invoice_id', $request->invoice_id);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'payment_date');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $payments = $query->paginate($perPage);

            return $this->successResponse($payments, 'Payments retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve payments: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,e_wallet,other',
            'reference_number' => 'nullable|string|max:255|unique:payments',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::find($request->invoice_id);
            
            // Check if invoice is already fully paid
            if ($invoice->status === 'paid') {
                return $this->errorResponse('Invoice is already fully paid', 422);
            }

            // Check if payment amount exceeds remaining amount
            $remainingAmount = $invoice->total_amount - $invoice->paid_amount;
            if ($request->amount > $remainingAmount) {
                return $this->errorResponse('Payment amount exceeds remaining invoice amount', 422, [
                    'remaining_amount' => $remainingAmount,
                    'requested_amount' => $request->amount,
                ]);
            }

            // Generate reference number if not provided
            $referenceNumber = $request->reference_number ?? 'PAY-' . date('Ymd') . '-' . str_pad(
                Payment::whereDate('created_at', today())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            // Create payment
            $payment = Payment::create([
                'invoice_id' => $request->invoice_id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $referenceNumber,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Update invoice paid amount
            $invoice->paid_amount += $request->amount;
            
            // Update invoice status
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'paid';
                $invoice->paid_at = $request->payment_date;
                $invoice->order->update(['payment_status' => 'paid']);
            } else {
                $invoice->status = 'partial';
                $invoice->order->update(['payment_status' => 'partial']);
            }
            
            $invoice->save();

            // Log activity
            ActivityLog::logCreation($payment, 'Created payment: ' . $referenceNumber);

            DB::commit();
            return $this->successResponse($payment->load('invoice.order.customer'), 'Payment created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create payment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified payment
     */
    public function show($id): JsonResponse
    {
        try {
            $payment = Payment::with([
                'invoice.order.customer',
                'invoice.order.orderItems.product',
                'verifiedBy'
            ])->findOrFail($id);

            return $this->successResponse($payment, 'Payment retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Payment not found', 404);
        }
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, $id): JsonResponse
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }

        // Only allow updates if payment is pending
        if ($payment->status !== 'pending') {
            return $this->errorResponse('Cannot update payment with status: ' . $payment->status, 422);
        }

        $validator = Validator::make($request->all(), [
            'payment_date' => 'sometimes|required|date',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'payment_method' => 'sometimes|required|in:cash,bank_transfer,credit_card,debit_card,e_wallet,other',
            'reference_number' => 'sometimes|nullable|string|max:255|unique:payments,reference_number,' . $id,
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $oldValues = $payment->toArray();
            $invoice = $payment->invoice;
            
            // If amount is being changed, update invoice
            if ($request->has('amount') && $request->amount != $payment->amount) {
                $amountDifference = $request->amount - $payment->amount;
                $newPaidAmount = $invoice->paid_amount + $amountDifference;
                
                // Check if new amount is valid
                if ($newPaidAmount > $invoice->total_amount) {
                    return $this->errorResponse('New payment amount would exceed invoice total', 422);
                }
                
                // Update invoice paid amount
                $invoice->paid_amount = $newPaidAmount;
                
                // Update invoice status
                if ($invoice->paid_amount >= $invoice->total_amount) {
                    $invoice->status = 'paid';
                    $invoice->paid_at = $request->payment_date ?? $payment->payment_date;
                    $invoice->order->update(['payment_status' => 'paid']);
                } elseif ($invoice->paid_amount > 0) {
                    $invoice->status = 'partial';
                    $invoice->order->update(['payment_status' => 'partial']);
                } else {
                    $invoice->status = 'sent';
                    $invoice->order->update(['payment_status' => 'pending']);
                }
                
                $invoice->save();
            }

            $payment->update($request->all());

            // Log activity
            $changes = array_diff_assoc($request->all(), $oldValues);
            if (!empty($changes)) {
                ActivityLog::logUpdate($payment, $changes, 'Updated payment: ' . $payment->reference_number);
            }

            DB::commit();
            return $this->successResponse($payment->load('invoice.order.customer'), 'Payment updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update payment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified payment
     */
    public function destroy($id): JsonResponse
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }

        // Only allow deletion if payment is pending
        if ($payment->status !== 'pending') {
            return $this->errorResponse('Cannot delete payment with status: ' . $payment->status, 422);
        }

        DB::beginTransaction();
        try {
            $invoice = $payment->invoice;
            
            // Update invoice paid amount
            $invoice->paid_amount -= $payment->amount;
            
            // Update invoice status
            if ($invoice->paid_amount > 0) {
                $invoice->status = 'partial';
                $invoice->order->update(['payment_status' => 'partial']);
            } else {
                $invoice->status = 'sent';
                $invoice->paid_at = null;
                $invoice->order->update(['payment_status' => 'pending']);
            }
            
            $invoice->save();

            // Log activity before deletion
            ActivityLog::logDeletion($payment, 'Deleted payment: ' . $payment->reference_number);
            
            $payment->delete();
            
            DB::commit();
            return $this->successResponse(null, 'Payment deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete payment: ' . $e->getMessage());
        }
    }

    /**
     * Verify payment
     */
    public function verify(Request $request, $id): JsonResponse
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }

        if ($payment->status !== 'pending') {
            return $this->errorResponse('Payment has already been processed', 422);
        }

        $validator = Validator::make($request->all(), [
            'verification_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $payment->status = 'completed';
            $payment->verified_by = auth()->id();
            $payment->verified_at = now();
            $payment->verification_notes = $request->verification_notes;
            $payment->save();

            // Log activity
            ActivityLog::log('update', $payment, [
                'status' => ['old' => 'pending', 'new' => 'completed']
            ], 'Verified payment: ' . $payment->reference_number);

            DB::commit();
            return $this->successResponse($payment->load(['invoice.order.customer', 'verifiedBy']), 'Payment verified successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to verify payment: ' . $e->getMessage());
        }
    }

    /**
     * Cancel payment
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }

        if ($payment->status === 'cancelled') {
            return $this->errorResponse('Payment is already cancelled', 422);
        }

        if ($payment->status === 'refunded') {
            return $this->errorResponse('Cannot cancel refunded payment', 422);
        }

        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $invoice = $payment->invoice;
            
            // Update invoice if payment was completed
            if ($payment->status === 'completed') {
                $invoice->paid_amount -= $payment->amount;
                
                // Update invoice status
                if ($invoice->paid_amount > 0) {
                    $invoice->status = 'partial';
                    $invoice->order->update(['payment_status' => 'partial']);
                } else {
                    $invoice->status = 'sent';
                    $invoice->paid_at = null;
                    $invoice->order->update(['payment_status' => 'pending']);
                }
                
                $invoice->save();
            }

            $oldStatus = $payment->status;
            $payment->status = 'cancelled';
            $payment->cancelled_at = now();
            $payment->cancellation_reason = $request->cancellation_reason;
            $payment->save();

            // Log activity
            ActivityLog::log('update', $payment, [
                'status' => ['old' => $oldStatus, 'new' => 'cancelled']
            ], 'Cancelled payment: ' . $payment->reference_number);

            DB::commit();
            return $this->successResponse($payment->load('invoice.order.customer'), 'Payment cancelled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to cancel payment: ' . $e->getMessage());
        }
    }

    /**
     * Process refund for payment
     */
    public function refund(Request $request, $id): JsonResponse
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return $this->errorResponse('Payment not found', 404);
        }

        if ($payment->status !== 'completed') {
            return $this->errorResponse('Can only refund completed payments', 422);
        }

        if ($payment->status === 'refunded') {
            return $this->errorResponse('Payment has already been refunded', 422);
        }

        $validator = Validator::make($request->all(), [
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'refund_reason' => 'required|string',
            'refund_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,e_wallet,other',
            'refund_reference' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $invoice = $payment->invoice;
            
            // Update invoice paid amount
            $invoice->paid_amount -= $request->refund_amount;
            
            // Update invoice status
            if ($invoice->paid_amount >= $invoice->total_amount) {
                // Still fully paid (partial refund)
                $invoice->status = 'paid';
            } elseif ($invoice->paid_amount > 0) {
                $invoice->status = 'partial';
                $invoice->order->update(['payment_status' => 'partial']);
            } else {
                $invoice->status = 'sent';
                $invoice->paid_at = null;
                $invoice->order->update(['payment_status' => 'pending']);
            }
            
            $invoice->save();

            // Update payment
            $payment->status = $request->refund_amount >= $payment->amount ? 'refunded' : 'partial_refund';
            $payment->refund_amount = $request->refund_amount;
            $payment->refund_reason = $request->refund_reason;
            $payment->refund_method = $request->refund_method;
            $payment->refund_reference = $request->refund_reference;
            $payment->refunded_at = now();
            $payment->refunded_by = auth()->id();
            $payment->save();

            // Log activity
            ActivityLog::log('update', $payment, [
                'status' => ['old' => 'completed', 'new' => $payment->status],
                'refund_amount' => $request->refund_amount
            ], 'Refunded payment: ' . $payment->reference_number);

            DB::commit();
            return $this->successResponse($payment->load('invoice.order.customer'), 'Payment refunded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to process refund: ' . $e->getMessage());
        }
    }

    /**
     * Get payment statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_payments' => Payment::count(),
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'completed_payments' => Payment::where('status', 'completed')->count(),
                'cancelled_payments' => Payment::where('status', 'cancelled')->count(),
                'refunded_payments' => Payment::whereIn('status', ['refunded', 'partial_refund'])->count(),
                'total_amount' => Payment::where('status', 'completed')->sum('amount'),
                'refunded_amount' => Payment::whereIn('status', ['refunded', 'partial_refund'])->sum('refund_amount'),
                'this_month_total' => Payment::where('status', 'completed')
                    ->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year)
                    ->sum('amount'),
                'by_method' => Payment::where('status', 'completed')
                    ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                    ->groupBy('payment_method')
                    ->get(),
                'recent_payments' => Payment::with('invoice.order.customer')
                    ->latest('payment_date')
                    ->limit(10)
                    ->get(),
            ];

            return $this->successResponse($stats, 'Payment statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $query = Payment::with(['invoice.order.customer']);
            
            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
            }
            
            $payments = $query->get();
            
            $csvData = [];
            $csvData[] = ['Reference Number', 'Invoice', 'Customer', 'Payment Date', 'Amount', 'Method', 'Status'];
            
            foreach ($payments as $payment) {
                $csvData[] = [
                    $payment->reference_number,
                    $payment->invoice->invoice_number,
                    $payment->invoice->order->customer->name,
                    $payment->payment_date,
                    $payment->amount,
                    $payment->payment_method,
                    $payment->status,
                ];
            }

            // In a real application, you would generate and return a CSV file
            return $this->successResponse($csvData, 'Payments exported successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export payments: ' . $e->getMessage());
        }
    }
}