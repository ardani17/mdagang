<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'type',
        'category',
        'customer_id',
        'supplier_id',
        'order_id',
        'invoice_id',
        'amount',
        'payment_method',
        'reference_number',
        'transaction_date',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = self::generateTransactionNumber();
            }
            if (empty($transaction->transaction_date)) {
                $transaction->transaction_date = now();
            }
            if (empty($transaction->created_by)) {
                $transaction->created_by = auth()->id();
            }
        });

        static::created(function ($transaction) {
            // Update related records based on transaction type
            if ($transaction->type === 'income' && $transaction->invoice_id) {
                $transaction->invoice->updatePaymentStatus();
            }
            if ($transaction->type === 'expense' && $transaction->supplier_id) {
                // Update supplier payment records if needed
            }
        });
    }

    /**
     * Generate unique transaction number
     */
    public static function generateTransactionNumber(): string
    {
        $prefix = 'TRX-' . date('YmdHis');
        $random = mt_rand(1000, 9999);
        
        return $prefix . '-' . $random;
    }

    /**
     * Get the customer for this transaction
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the supplier for this transaction
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the order for this transaction
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the invoice for this transaction
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the user who created this transaction
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include income transactions
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope a query to only include expense transactions
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope a query to only include completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get transaction type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'income' => 'Income',
            'expense' => 'Expense',
            default => 'Unknown',
        };
    }

    /**
     * Get transaction category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
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
            default => 'Uncategorized',
        };
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
            default => 'Unknown',
        };
    }

    /**
     * Complete the transaction
     */
    public function complete(): bool
    {
        if ($this->status === 'completed') {
            return true;
        }

        $this->update(['status' => 'completed']);

        // Update related records
        if ($this->invoice_id) {
            $this->invoice->updatePaymentStatus();
        }

        if ($this->order_id) {
            $this->order->update(['payment_status' => 'paid']);
        }

        return true;
    }

    /**
     * Cancel the transaction
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'description' => $this->description . "\nCancelled: " . $reason,
        ]);

        return true;
    }

    /**
     * Reverse the transaction (create opposite transaction)
     */
    public function reverse(string $reason = null): Transaction
    {
        $reverseType = $this->type === 'income' ? 'expense' : 'income';
        
        $reverseTransaction = self::create([
            'type' => $reverseType,
            'category' => 'refund',
            'customer_id' => $this->customer_id,
            'supplier_id' => $this->supplier_id,
            'order_id' => $this->order_id,
            'invoice_id' => $this->invoice_id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'reference_number' => 'REV-' . $this->transaction_number,
            'description' => "Reversal of transaction {$this->transaction_number}. Reason: {$reason}",
            'status' => 'completed',
        ]);

        // Update original transaction
        $this->update([
            'status' => 'reversed',
            'description' => $this->description . "\nReversed: " . $reason,
        ]);

        return $reverseTransaction;
    }

    /**
     * Get net amount (considering type)
     */
    public function getNetAmountAttribute(): float
    {
        return $this->type === 'income' ? $this->amount : -$this->amount;
    }

    /**
     * Get transaction summary
     */
    public function getSummary(): array
    {
        return [
            'transaction_number' => $this->transaction_number,
            'type' => $this->type,
            'category' => $this->category_label,
            'amount' => $this->amount,
            'net_amount' => $this->net_amount,
            'payment_method' => $this->payment_method_label,
            'status' => $this->status,
            'date' => $this->transaction_date->format('Y-m-d H:i'),
            'customer' => $this->customer?->name,
            'supplier' => $this->supplier?->name,
            'reference' => $this->reference_number,
        ];
    }

    /**
     * Get transactions for cash flow report
     */
    public static function getCashFlow($startDate = null, $endDate = null): array
    {
        $query = self::completed();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $transactions = $query->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');

        return [
            'income' => $income,
            'expense' => $expense,
            'net' => $income - $expense,
            'transactions' => $transactions,
        ];
    }

    /**
     * Get category-wise breakdown
     */
    public static function getCategoryBreakdown($type = null, $startDate = null, $endDate = null): array
    {
        $query = self::completed();

        if ($type) {
            $query->where('type', $type);
        }

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->get()
            ->groupBy('category')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                    'average' => $group->avg('amount'),
                ];
            })
            ->toArray();
    }
}