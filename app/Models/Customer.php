<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'type',
        'tax_id',
        'credit_limit',
        'outstanding_balance',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->code)) {
                $customer->code = self::generateCode();
            }
        });
    }

    /**
     * Generate unique customer code
     */
    public static function generateCode(): string
    {
        $prefix = 'CUST';
        $lastCustomer = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastCustomer) {
            $lastNumber = intval(substr($lastCustomer->code, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the orders for the customer
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the invoices for the customer
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the payments for the customer
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include business customers
     */
    public function scopeBusiness($query)
    {
        return $query->where('type', 'business');
    }

    /**
     * Scope a query to only include individual customers
     */
    public function scopeIndividual($query)
    {
        return $query->where('type', 'individual');
    }

    /**
     * Check if customer has exceeded credit limit
     */
    public function hasExceededCreditLimit(): bool
    {
        return $this->outstanding_balance > $this->credit_limit;
    }

    /**
     * Get available credit
     */
    public function getAvailableCreditAttribute(): float
    {
        return max(0, $this->credit_limit - $this->outstanding_balance);
    }

    /**
     * Update outstanding balance
     */
    public function updateOutstandingBalance(): void
    {
        $unpaidInvoices = $this->invoices()
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->sum('balance_due');

        $this->update(['outstanding_balance' => $unpaidInvoices]);
    }
}