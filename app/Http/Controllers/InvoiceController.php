<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Invoice::with(['order.customer', 'payments']);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('order.customer', function ($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by customer
            if ($request->has('customer_id')) {
                $query->whereHas('order', function ($q) use ($request) {
                    $q->where('customer_id', $request->customer_id);
                });
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
            }

            // Filter by overdue
            if ($request->has('overdue') && $request->overdue) {
                $query->where('status', '!=', 'paid')
                      ->where('due_date', '<', now());
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $invoices = $query->paginate($perPage);

            // Add calculated fields
            foreach ($invoices as $invoice) {
                $invoice->days_overdue = $invoice->getDaysOverdue();
                $invoice->remaining_amount = $invoice->getRemainingAmount();
            }

            return $this->successResponse($invoices, 'Invoices retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve invoices: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'payment_terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $order = Order::find($request->order_id);
            
            // Check if order already has an invoice
            if ($order->invoice) {
                return $this->errorResponse('Order already has an invoice', 422);
            }

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad(
                Invoice::whereYear('created_at', date('Y'))
                    ->whereMonth('created_at', date('m'))
                    ->count() + 1, 
                4, 
                '0', 
                STR_PAD_LEFT
            );

            // Calculate amounts
            $subtotal = $order->total_amount;
            $discountAmount = 0;
            
            if ($request->discount_type && $request->discount_value) {
                if ($request->discount_type === 'percentage') {
                    $discountAmount = $subtotal * ($request->discount_value / 100);
                } else {
                    $discountAmount = $request->discount_value;
                }
            }

            $taxableAmount = $subtotal - $discountAmount;
            $taxAmount = $taxableAmount * (($request->tax_rate ?? 0) / 100);
            $totalAmount = $taxableAmount + $taxAmount;

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'order_id' => $request->order_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'discount_amount' => $discountAmount,
                'tax_rate' => $request->tax_rate ?? 0,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'status' => 'draft',
                'payment_terms' => $request->payment_terms,
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions,
            ]);

            // Update order status
            $order->update(['invoice_status' => 'invoiced']);

            // Log activity
            ActivityLog::logCreation($invoice, 'Created invoice: ' . $invoiceNumber);

            DB::commit();
            return $this->successResponse($invoice->load('order.customer'), 'Invoice created successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice
     */
    public function show($id): JsonResponse
    {
        try {
            $invoice = Invoice::with([
                'order.customer',
                'order.orderItems.product',
                'payments'
            ])->findOrFail($id);

            // Add calculated fields
            $invoice->days_overdue = $invoice->getDaysOverdue();
            $invoice->remaining_amount = $invoice->getRemainingAmount();
            $invoice->payment_history = $invoice->payments()->latest()->get();

            return $this->successResponse($invoice, 'Invoice retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Invoice not found', 404);
        }
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, $id): JsonResponse
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->errorResponse('Invoice not found', 404);
        }

        // Only allow updates if invoice is draft or sent
        if (!in_array($invoice->status, ['draft', 'sent'])) {
            return $this->errorResponse('Cannot update invoice with status: ' . $invoice->status, 422);
        }

        $validator = Validator::make($request->all(), [
            'invoice_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after_or_equal:invoice_date',
            'payment_terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $oldValues = $invoice->toArray();

            // Recalculate amounts if tax or discount changed
            if ($request->has('tax_rate') || $request->has('discount_type') || $request->has('discount_value')) {
                $subtotal = $invoice->subtotal;
                $discountAmount = 0;
                
                $discountType = $request->discount_type ?? $invoice->discount_type;
                $discountValue = $request->discount_value ?? $invoice->discount_value;
                
                if ($discountType && $discountValue) {
                    if ($discountType === 'percentage') {
                        $discountAmount = $subtotal * ($discountValue / 100);
                    } else {
                        $discountAmount = $discountValue;
                    }
                }

                $taxableAmount = $subtotal - $discountAmount;
                $taxRate = $request->tax_rate ?? $invoice->tax_rate;
                $taxAmount = $taxableAmount * ($taxRate / 100);
                $totalAmount = $taxableAmount + $taxAmount;

                $invoice->discount_type = $discountType;
                $invoice->discount_value = $discountValue;
                $invoice->discount_amount = $discountAmount;
                $invoice->tax_rate = $taxRate;
                $invoice->tax_amount = $taxAmount;
                $invoice->total_amount = $totalAmount;
            }

            // Update other fields
            $invoice->fill($request->except(['tax_rate', 'discount_type', 'discount_value']));
            $invoice->save();

            // Log activity
            $changes = array_diff_assoc($invoice->toArray(), $oldValues);
            if (!empty($changes)) {
                ActivityLog::logUpdate($invoice, $changes, 'Updated invoice: ' . $invoice->invoice_number);
            }

            DB::commit();
            return $this->successResponse($invoice->load('order.customer'), 'Invoice updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update invoice: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy($id): JsonResponse
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->errorResponse('Invoice not found', 404);
        }

        // Only allow deletion if invoice is draft
        if ($invoice->status !== 'draft') {
            return $this->errorResponse('Cannot delete invoice with status: ' . $invoice->status, 422);
        }

        // Check if invoice has payments
        if ($invoice->payments()->exists()) {
            return $this->errorResponse('Cannot delete invoice with payments', 422);
        }

        DB::beginTransaction();
        try {
            // Update order status
            $invoice->order->update(['invoice_status' => 'pending']);

            // Log activity before deletion
            ActivityLog::logDeletion($invoice, 'Deleted invoice: ' . $invoice->invoice_number);
            
            $invoice->delete();
            
            DB::commit();
            return $this->successResponse(null, 'Invoice deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete invoice: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice to customer
     */
    public function send($id): JsonResponse
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->errorResponse('Invoice not found', 404);
        }

        if ($invoice->status === 'paid') {
            return $this->errorResponse('Invoice is already paid', 422);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $invoice->status;
            $invoice->status = 'sent';
            $invoice->sent_at = now();
            $invoice->save();

            // In a real application, you would send email here
            // Mail::to($invoice->order->customer->email)->send(new InvoiceMail($invoice));

            // Log activity
            ActivityLog::log('update', $invoice, [
                'status' => ['old' => $oldStatus, 'new' => 'sent']
            ], 'Sent invoice to customer: ' . $invoice->invoice_number);

            DB::commit();
            return $this->successResponse($invoice, 'Invoice sent successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to send invoice: ' . $e->getMessage());
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Request $request, $id): JsonResponse
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->errorResponse('Invoice not found', 404);
        }

        if ($invoice->status === 'paid') {
            return $this->errorResponse('Invoice is already paid', 422);
        }

        $validator = Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,e_wallet,other',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => $request->payment_date,
                'amount' => $invoice->total_amount - $invoice->paid_amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->payment_reference ?? 'PAY-' . time(),
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            // Update invoice
            $oldStatus = $invoice->status;
            $invoice->paid_amount = $invoice->total_amount;
            $invoice->status = 'paid';
            $invoice->paid_at = $request->payment_date;
            $invoice->save();

            // Update order payment status
            $invoice->order->update(['payment_status' => 'paid']);

            // Log activity
            ActivityLog::log('update', $invoice, [
                'status' => ['old' => $oldStatus, 'new' => 'paid'],
                'paid_amount' => $invoice->total_amount
            ], 'Marked invoice as paid: ' . $invoice->invoice_number);

            DB::commit();
            return $this->successResponse($invoice->load('payments'), 'Invoice marked as paid successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to mark invoice as paid: ' . $e->getMessage());
        }
    }

    /**
     * Record partial payment for invoice
     */
    public function recordPayment(Request $request, $id): JsonResponse
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->errorResponse('Invoice not found', 404);
        }

        if ($invoice->status === 'paid') {
            return $this->errorResponse('Invoice is already fully paid', 422);
        }

        $remainingAmount = $invoice->total_amount - $invoice->paid_amount;

        $validator = Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:' . $remainingAmount,
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,e_wallet,other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number ?? 'PAY-' . time(),
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            // Update invoice
            $oldPaidAmount = $invoice->paid_amount;
            $invoice->paid_amount += $request->amount;
            
            // Check if fully paid
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
            ActivityLog::log('update', $invoice, [
                'paid_amount' => ['old' => $oldPaidAmount, 'new' => $invoice->paid_amount],
                'status' => $invoice->status
            ], 'Recorded payment for invoice: ' . $invoice->invoice_number);

            DB::commit();
            return $this->successResponse([
                'invoice' => $invoice->load('payments'),
                'payment' => $payment
            ], 'Payment recorded successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to record payment: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for invoice
     */
    public function generatePdf($id): JsonResponse
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->errorResponse('Invoice not found', 404);
        }

        try {
            $invoice->load(['order.customer', 'order.orderItems.product']);
            
            // In a real application, you would generate and return a PDF file
            // $pdf = PDF::loadView('invoices.pdf', compact('invoice'));
            // return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
            
            // For now, return success message
            return $this->successResponse([
                'message' => 'PDF generation would happen here',
                'invoice_number' => $invoice->invoice_number,
                'download_url' => url('/api/invoices/' . $id . '/download')
            ], 'PDF generated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get invoice statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_invoices' => Invoice::count(),
                'draft_invoices' => Invoice::where('status', 'draft')->count(),
                'sent_invoices' => Invoice::where('status', 'sent')->count(),
                'paid_invoices' => Invoice::where('status', 'paid')->count(),
                'partial_invoices' => Invoice::where('status', 'partial')->count(),
                'overdue_invoices' => Invoice::where('status', '!=', 'paid')
                    ->where('due_date', '<', now())
                    ->count(),
                'total_amount' => Invoice::sum('total_amount'),
                'paid_amount' => Invoice::sum('paid_amount'),
                'outstanding_amount' => Invoice::sum('total_amount') - Invoice::sum('paid_amount'),
                'this_month_total' => Invoice::whereMonth('invoice_date', now()->month)
                    ->whereYear('invoice_date', now()->year)
                    ->sum('total_amount'),
                'average_payment_time' => Invoice::where('status', 'paid')
                    ->whereNotNull('paid_at')
                    ->selectRaw('AVG(DATEDIFF(paid_at, invoice_date)) as avg_days')
                    ->first()->avg_days ?? 0,
            ];

            return $this->successResponse($stats, 'Invoice statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Export invoices to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $query = Invoice::with(['order.customer']);
            
            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
            }
            
            $invoices = $query->get();
            
            $csvData = [];
            $csvData[] = ['Invoice Number', 'Customer', 'Invoice Date', 'Due Date', 'Total Amount', 'Paid Amount', 'Status'];
            
            foreach ($invoices as $invoice) {
                $csvData[] = [
                    $invoice->invoice_number,
                    $invoice->order->customer->name,
                    $invoice->invoice_date,
                    $invoice->due_date,
                    $invoice->total_amount,
                    $invoice->paid_amount,
                    $invoice->status,
                ];
            }

            // In a real application, you would generate and return a CSV file
            return $this->successResponse($csvData, 'Invoices exported successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export invoices: ' . $e->getMessage());
        }
    }
}