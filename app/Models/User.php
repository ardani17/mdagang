<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'department',
        'position',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the production orders created by this user
     */
    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class, 'created_by');
    }

    /**
     * Get the production orders assigned to this user
     */
    public function assignedProductionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class, 'assigned_to');
    }

    /**
     * Get the quality inspections performed by this user
     */
    public function qualityInspections(): HasMany
    {
        return $this->hasMany(QualityInspection::class, 'inspector_id');
    }

    /**
     * Get the quality inspections approved by this user
     */
    public function approvedInspections(): HasMany
    {
        return $this->hasMany(QualityInspection::class, 'approved_by');
    }

    /**
     * Get the purchase orders created by this user
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'created_by');
    }

    /**
     * Get the purchase orders approved by this user
     */
    public function approvedPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    /**
     * Get the orders created by this user
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    /**
     * Get the invoices created by this user
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    /**
     * Get the payments created by this user
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    /**
     * Get the transactions created by this user
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    /**
     * Get the stock movements created by this user
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }

    /**
     * Get the activity logs for this user
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Scope a query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include administrators
     */
    public function scopeAdministrators($query)
    {
        return $query->where('role', 'administrator');
    }

    /**
     * Scope a query to only include regular users
     */
    public function scopeRegularUsers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Check if user is an administrator
     */
    public function isAdministrator(): bool
    {
        return $this->role === 'administrator';
    }

    /**
     * Check if user is a regular user
     */
    public function isRegularUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user has permission for a specific action
     */
    public function hasPermission(string $permission): bool
    {
        // For now, administrators have all permissions
        if ($this->isAdministrator()) {
            return true;
        }

        // Define specific permissions for regular users
        $userPermissions = [
            'view_dashboard',
            'view_products',
            'view_orders',
            'create_orders',
            'view_customers',
            'view_inventory',
        ];

        return in_array($permission, $userPermissions);
    }

    /**
     * Update last login information
     */
    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        ActivityLog::logLogin($this);
    }

    /**
     * Get user's full name with position
     */
    public function getFullTitleAttribute(): string
    {
        $title = $this->name;
        
        if ($this->position) {
            $title .= ' - ' . $this->position;
        }
        
        if ($this->department) {
            $title .= ' (' . $this->department . ')';
        }
        
        return $title;
    }

    /**
     * Get user's initials
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return substr($initials, 0, 2);
    }

    /**
     * Get user's activity summary
     */
    public function getActivitySummary($days = 30): array
    {
        $startDate = now()->subDays($days);
        
        return [
            'total_activities' => $this->activityLogs()->where('created_at', '>=', $startDate)->count(),
            'orders_created' => $this->orders()->where('created_at', '>=', $startDate)->count(),
            'invoices_created' => $this->invoices()->where('created_at', '>=', $startDate)->count(),
            'production_orders' => $this->productionOrders()->where('created_at', '>=', $startDate)->count(),
            'quality_inspections' => $this->qualityInspections()->where('created_at', '>=', $startDate)->count(),
            'last_login' => $this->last_login_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get user's performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'total_sales' => $this->orders()->completed()->sum('total_amount'),
            'total_orders' => $this->orders()->count(),
            'completed_orders' => $this->orders()->completed()->count(),
            'pending_orders' => $this->orders()->pending()->count(),
            'production_efficiency' => $this->calculateProductionEfficiency(),
            'quality_score' => $this->calculateQualityScore(),
        ];
    }

    /**
     * Calculate production efficiency
     */
    private function calculateProductionEfficiency(): ?float
    {
        $productionOrders = $this->assignedProductionOrders()->completed()->get();
        
        if ($productionOrders->isEmpty()) {
            return null;
        }
        
        $totalEfficiency = 0;
        $count = 0;
        
        foreach ($productionOrders as $order) {
            if ($order->efficiency) {
                $totalEfficiency += $order->efficiency;
                $count++;
            }
        }
        
        return $count > 0 ? round($totalEfficiency / $count, 2) : null;
    }

    /**
     * Calculate quality score
     */
    private function calculateQualityScore(): ?float
    {
        $inspections = $this->qualityInspections;
        
        if ($inspections->isEmpty()) {
            return null;
        }
        
        $totalScore = 0;
        $count = 0;
        
        foreach ($inspections as $inspection) {
            $totalScore += $inspection->quality_score;
            $count++;
        }
        
        return $count > 0 ? round($totalScore / $count, 2) : null;
    }
}
