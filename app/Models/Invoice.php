<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'status',
        'payment_terms',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
            if (empty($invoice->invoice_date)) {
                $invoice->invoice_date = now();
            }
            if (empty($invoice->due_date)) {
                $invoice->due_date = now()->addDays(30);
            }
            if (empty($invoice->created_by)) {
                $invoice->created_by = auth()->id();
            }
        });

        static::updating(function ($invoice) {
            // Calculate balance due
            $invoice->balance_due = $invoice->total_amount - $invoice->paid_amount;
            
            // Update status based on payment
            if ($invoice->balance_due <= 0) {
                $invoice->status = 'paid';
            } elseif ($invoice->paid_amount > 0) {
                $invoice->status = 'partial';
            } elseif ($invoice->due_date < now() && $invoice->status !== 'cancelled') {
                $invoice->status = 'overdue';
            }
        });
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ym');
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -5));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the customer for this invoice
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order for this invoice
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the items for this invoice
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the payments for this invoice
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the transactions for this invoice
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the user who created this invoice
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include draft invoices
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include sent invoices
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope a query to only include paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include partial invoices
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    /**
     * Scope a query to only include overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('due_date', '<', now())
                    ->whereIn('status', ['sent', 'partial']);
            });
    }

    /**
     * Scope a query to only include unpaid invoices
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['sent', 'partial', 'overdue']);
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now() && 
               $this->balance_due > 0 && 
               !in_array($this->status, ['paid', 'cancelled']);
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid' || $this->balance_due <= 0;
    }

    /**
     * Check if invoice is partially paid
     */
    public function isPartiallyPaid(): bool
    {
        return $this->paid_amount > 0 && $this->balance_due > 0;
    }

    /**
     * Send the invoice
     */
    public function send(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        $this->update(['status' => 'sent']);

        // Here you would typically send an email to the customer
        // Mail::to($this->customer->email)->send(new InvoiceMail($this));

        return true;
    }

    /**
     * Record a payment
     */
    public function recordPayment(array $paymentData): Payment
    {
        $payment = $this->payments()->create([
            'customer_id' => $this->customer_id,
            'amount' => $paymentData['amount'],
            'payment_date' => $paymentData['payment_date'] ?? now(),
            'payment_method' => $paymentData['payment_method'],
            'reference_number' => $paymentData['reference_number'] ?? null,
            'notes' => $paymentData['notes'] ?? null,
            'status' => 'completed',
        ]);

        // Update paid amount and status
        $this->updatePaymentStatus();

        // Create transaction record
        Transaction::create([
            'type' => 'income',
            'category' => 'payment_received',
            'customer_id' => $this->customer_id,
            'invoice_id' => $this->id,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'reference_number' => $payment->reference_number,
            'description' => "Payment for Invoice: {$this->invoice_number}",
            'status' => 'completed',
        ]);

        return $payment;
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->payments()->where('status', 'completed')->sum('amount');
        
        $this->update([
            'paid_amount' => $totalPaid,
            'balance_due' => $this->total_amount - $totalPaid,
        ]);

        // Status is automatically updated in the updating event
    }

    /**
     * Cancel the invoice
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['paid', 'cancelled'])) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . "\nCancelled: " . $reason,
        ]);

        return true;
    }

    /**
     * Calculate and update totals
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('total');
        $taxAmount = $subtotal * 0.1; // 10% tax, adjust as needed
        
        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $subtotal + $taxAmount - $this->discount_amount,
            'balance_due' => ($subtotal + $taxAmount - $this->discount_amount) - $this->paid_amount,
        ]);
    }

    /**
     * Add item to invoice
     */
    public function addItem(array $itemData): InvoiceItem
    {
        $item = $this->items()->create($itemData);
        $this->calculateTotals();
        
        return $item;
    }

    /**
     * Remove item from invoice
     */
    public function removeItem(int $itemId): bool
    {
        $deleted = $this->items()->where('id', $itemId)->delete();
        
        if ($deleted) {
            $this->calculateTotals();
        }
        
        return $deleted > 0;
    }

    /**
     * Apply discount
     */
    public function applyDiscount(float $amount = null, float $percentage = null): void
    {
        if ($percentage !== null) {
            $this->discount_amount = $this->subtotal * ($percentage / 100);
        } elseif ($amount !== null) {
            $this->discount_amount = min($amount, $this->subtotal);
        }

        $this->save();
        $this->calculateTotals();
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdueAttribute(): ?int
    {
        if (!$this->isOverdue()) {
            return null;
        }

        return $this->due_date->diffInDays(now());
    }

    /**
     * Get payment progress percentage
     */
    public function getPaymentProgressAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 100;
        }

        return min(100, round(($this->paid_amount / $this->total_amount) * 100, 2));
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'sent' => 'Sent',
            'paid' => 'Paid',
            'partial' => 'Partially Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    /**
     * Get invoice summary
     */
    public function getSummary(): array
    {
        return [
            'invoice_number' => $this->invoice_number,
            'customer' => $this->customer->name,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'paid_amount' => $this->paid_amount,
            'balance_due' => $this->balance_due,
            'payment_progress' => $this->payment_progress,
            'invoice_date' => $this->invoice_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'is_overdue' => $this->isOverdue(),
            'days_overdue' => $this->days_overdue,
            'items_count' => $this->items()->count(),
        ];
    }

    /**
     * Generate PDF
     */
    public function generatePdf()
    {
        // This would generate a PDF version of the invoice
        // You would typically use a package like DomPDF or similar
        // return PDF::loadView('invoices.pdf', ['invoice' => $this])->download();
    }

    /**
     * Clone invoice
     */
    public function duplicate(): Invoice
    {
        $newInvoice = $this->replicate(['invoice_number', 'paid_amount', 'balance_due']);
        $newInvoice->invoice_number = self::generateInvoiceNumber();
        $newInvoice->invoice_date = now();
        $newInvoice->due_date = now()->addDays(30);
        $newInvoice->status = 'draft';
        $newInvoice->paid_amount = 0;
        $newInvoice->balance_due = $newInvoice->total_amount;
        $newInvoice->save();

        // Copy items
        foreach ($this->items as $item) {
            $newInvoice->items()->create($item->toArray());
        }

        return $newInvoice;
    }
}