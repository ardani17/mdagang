<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'customer_id',
        'supplier_id',
        'invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'bank_name',
        'bank_account',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = self::generatePaymentNumber();
            }
            if (empty($payment->payment_date)) {
                $payment->payment_date = now();
            }
            if (empty($payment->created_by)) {
                $payment->created_by = auth()->id();
            }
        });

        static::created(function ($payment) {
            // Update invoice payment status if related to invoice
            if ($payment->invoice_id && $payment->status === 'completed') {
                $payment->invoice->updatePaymentStatus();
            }

            // Create transaction record
            Transaction::create([
                'type' => $payment->customer_id ? 'income' : 'expense',
                'category' => $payment->customer_id ? 'payment_received' : 'payment_made',
                'customer_id' => $payment->customer_id,
                'supplier_id' => $payment->supplier_id,
                'invoice_id' => $payment->invoice_id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'reference_number' => $payment->reference_number,
                'transaction_date' => $payment->payment_date,
                'description' => $payment->notes,
                'status' => $payment->status,
            ]);
        });
    }

    /**
     * Generate unique payment number
     */
    public static function generatePaymentNumber(): string
    {
        $prefix = 'PAY-' . date('YmdHis');
        $random = mt_rand(1000, 9999);
        
        return $prefix . '-' . $random;
    }

    /**
     * Get the customer for this payment
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the supplier for this payment
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the invoice for this payment
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the user who created this payment
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by payment method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Get payment type (income or expense)
     */
    public function getTypeAttribute(): string
    {
        if ($this->customer_id) {
            return 'income';
        } elseif ($this->supplier_id) {
            return 'expense';
        } else {
            return 'unknown';
        }
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'check' => 'Check',
            'online' => 'Online Payment',
            'other' => 'Other',
            default => 'Unknown',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            default => 'Unknown',
        };
    }

    /**
     * Complete the payment
     */
    public function complete(): bool
    {
        if ($this->status === 'completed') {
            return true;
        }

        $this->update(['status' => 'completed']);

        // Update invoice if related
        if ($this->invoice_id) {
            $this->invoice->updatePaymentStatus();
        }

        return true;
    }

    /**
     * Cancel the payment
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['completed', 'cancelled', 'refunded'])) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . "\nCancelled: " . $reason,
        ]);

        return true;
    }

    /**
     * Refund the payment
     */
    public function refund(float $amount = null, string $reason = null): Payment
    {
        $refundAmount = $amount ?? $this->amount;

        $refundPayment = self::create([
            'customer_id' => $this->supplier_id, // Reverse the parties
            'supplier_id' => $this->customer_id,
            'invoice_id' => $this->invoice_id,
            'amount' => $refundAmount,
            'payment_method' => $this->payment_method,
            'reference_number' => 'REF-' . $this->payment_number,
            'notes' => "Refund for payment {$this->payment_number}. Reason: {$reason}",
            'status' => 'completed',
        ]);

        // Update original payment status
        if ($refundAmount >= $this->amount) {
            $this->update(['status' => 'refunded']);
        }

        // Update invoice if related
        if ($this->invoice_id) {
            $this->invoice->updatePaymentStatus();
        }

        return $refundPayment;
    }

    /**
     * Verify payment
     */
    public function verify(): bool
    {
        // Here you would implement payment verification logic
        // For example, checking with payment gateway, bank API, etc.
        
        if ($this->status === 'pending') {
            $this->update(['status' => 'completed']);
            return true;
        }

        return false;
    }

    /**
     * Get payment summary
     */
    public function getSummary(): array
    {
        return [
            'payment_number' => $this->payment_number,
            'type' => $this->type,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method_label,
            'status' => $this->status,
            'payment_date' => $this->payment_date->format('Y-m-d H:i'),
            'customer' => $this->customer?->name,
            'supplier' => $this->supplier?->name,
            'invoice' => $this->invoice?->invoice_number,
            'reference' => $this->reference_number,
        ];
    }

    /**
     * Get payments summary for a period
     */
    public static function getPeriodSummary($startDate = null, $endDate = null): array
    {
        $query = self::completed();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $payments = $query->get();

        $income = $payments->filter(fn($p) => $p->type === 'income')->sum('amount');
        $expense = $payments->filter(fn($p) => $p->type === 'expense')->sum('amount');

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'net_amount' => $income - $expense,
            'payment_count' => $payments->count(),
            'average_payment' => $payments->avg('amount'),
            'by_method' => $payments->groupBy('payment_method')->map->sum('amount'),
        ];
    }
}