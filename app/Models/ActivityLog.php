<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action_type',
        'module',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'changes',
        'ip_address',
        'user_agent',
        'risk_level',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->user_id)) {
                $log->user_id = auth()->id();
            }
            if (empty($log->ip_address)) {
                $log->ip_address = request()->ip();
            }
            if (empty($log->user_agent)) {
                $log->user_agent = request()->userAgent();
            }
        });
    }

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was affected (polymorphic)
     */
    public function model()
    {
        if ($this->model_type && $this->model_id) {
            $modelClass = $this->model_type;
            if (class_exists($modelClass)) {
                return $modelClass::find($this->model_id);
            }
        }
        
        return null;
    }

    /**
     * Scope a query to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action_type', $action);
    }

    /**
     * Scope a query to filter by model type
     */
    public function scopeByModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get action label
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action_type) {
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'restore' => 'Restored',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'view' => 'Viewed',
            'download' => 'Downloaded',
            'export' => 'Exported',
            'import' => 'Imported',
            'approve' => 'Approved',
            'reject' => 'Rejected',
            'cancel' => 'Cancelled',
            'complete' => 'Completed',
            'process' => 'Processed',
            'send' => 'Sent',
            'receive' => 'Received',
            'pay' => 'Paid',
            'refund' => 'Refunded',
            default => ucfirst($this->action_type),
        };
    }

    /**
     * Get model type label
     */
    public function getModelTypeLabelAttribute(): string
    {
        if (!$this->model_type) {
            return 'System';
        }

        $modelName = class_basename($this->model_type);
        
        return match($modelName) {
            'User' => 'User',
            'Customer' => 'Customer',
            'Supplier' => 'Supplier',
            'RawMaterial' => 'Raw Material',
            'Product' => 'Product',
            'Recipe' => 'Recipe',
            'ProductionOrder' => 'Production Order',
            'QualityInspection' => 'Quality Inspection',
            'PurchaseOrder' => 'Purchase Order',
            'Order' => 'Sales Order',
            'Invoice' => 'Invoice',
            'Payment' => 'Payment',
            'Transaction' => 'Transaction',
            'StockMovement' => 'Stock Movement',
            default => $modelName,
        };
    }

    /**
     * Get formatted changes
     */
    public function getFormattedChangesAttribute(): array
    {
        if (!$this->changes || !is_array($this->changes)) {
            return [];
        }

        $formatted = [];
        
        foreach ($this->changes as $field => $change) {
            if (is_array($change) && isset($change['old']) && isset($change['new'])) {
                $formatted[] = [
                    'field' => $this->formatFieldName($field),
                    'old_value' => $this->formatValue($change['old']),
                    'new_value' => $this->formatValue($change['new']),
                ];
            }
        }

        return $formatted;
    }

    /**
     * Format field name for display
     */
    private function formatFieldName($field): string
    {
        return ucwords(str_replace('_', ' ', $field));
    }

    /**
     * Format value for display
     */
    private function formatValue($value): string
    {
        if (is_null($value)) {
            return 'None';
        }
        
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        
        if (is_array($value)) {
            return json_encode($value);
        }
        
        return (string) $value;
    }

    /**
     * Log an activity
     */
    public static function log(string $action, $model = null, array $changes = null, string $description = null): self
    {
        $data = [
            'action_type' => $action,
            'description' => $description,
            'module' => self::detectModule($model),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'user_role' => auth()->user()?->role,
            'risk_level' => self::determineRiskLevel($action),
        ];

        if ($model) {
            $data['model_type'] = get_class($model);
            $data['model_id'] = $model->id;
        }

        if ($changes) {
            $data['changes'] = $changes;
        }

        return self::create($data);
    }

    /**
     * Detect module from model
     */
    private static function detectModule($model): string
    {
        if (!$model) {
            return 'system';
        }

        $modelName = class_basename(get_class($model));
        
        return match($modelName) {
            'User' => 'users',
            'Customer' => 'customers',
            'Supplier', 'RawMaterial', 'Recipe', 'RecipeIngredient', 'ProductionOrder', 'QualityInspection' => 'manufacturing',
            'Product', 'Category' => 'products',
            'Order', 'OrderItem' => 'orders',
            'Invoice', 'Payment', 'Transaction' => 'financial',
            'StockMovement' => 'inventory',
            'PurchaseOrder', 'PurchaseOrderItem' => 'purchasing',
            default => 'system',
        };
    }

    /**
     * Determine risk level based on action
     */
    private static function determineRiskLevel(string $action): string
    {
        return match($action) {
            'delete' => 'high',
            'update', 'create' => 'medium',
            'login', 'logout', 'view' => 'low',
            default => 'low',
        };
    }

    /**
     * Log a model creation
     */
    public static function logCreation($model, string $description = null): self
    {
        $description = $description ?? "Created {$model->getTable()} record";
        return self::log('create', $model, null, $description);
    }

    /**
     * Log a model update
     */
    public static function logUpdate($model, array $changes, string $description = null): self
    {
        $description = $description ?? "Updated {$model->getTable()} record";
        return self::log('update', $model, $changes, $description);
    }

    /**
     * Log a model deletion
     */
    public static function logDeletion($model, string $description = null): self
    {
        $description = $description ?? "Deleted {$model->getTable()} record";
        return self::log('delete', $model, null, $description);
    }

    /**
     * Log user login
     */
    public static function logLogin(User $user): self
    {
        return self::log('login', $user, null, "User logged in");
    }

    /**
     * Log user logout
     */
    public static function logLogout(User $user): self
    {
        return self::log('logout', $user, null, "User logged out");
    }

    /**
     * Get activity summary
     */
    public function getSummary(): array
    {
        return [
            'user' => $this->user?->name ?? 'System',
            'action' => $this->action_label,
            'model' => $this->model_type_label,
            'model_id' => $this->model_id,
            'description' => $this->description,
            'changes' => $this->formatted_changes,
            'ip_address' => $this->ip_address,
            'timestamp' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get activity statistics for a period
     */
    public static function getStatistics($startDate = null, $endDate = null): array
    {
        $query = self::query();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $logs = $query->get();

        return [
            'total_activities' => $logs->count(),
            'by_action' => $logs->groupBy('action_type')->map->count(),
            'by_model' => $logs->groupBy('model_type')->map->count(),
            'by_user' => $logs->groupBy('user_id')->map(function ($group) {
                $user = User::find($group->first()->user_id);
                return [
                    'name' => $user?->name ?? 'Unknown',
                    'count' => $group->count(),
                ];
            }),
            'by_date' => $logs->groupBy(function ($log) {
                return $log->created_at->format('Y-m-d');
            })->map->count(),
            'most_active_users' => $logs->groupBy('user_id')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->map(function ($count, $userId) {
                    $user = User::find($userId);
                    return [
                        'user' => $user?->name ?? 'Unknown',
                        'activities' => $count,
                    ];
                }),
        ];
    }

    /**
     * Clean old logs
     */
    public static function cleanOldLogs($days = 90): int
    {
        return self::where('created_at', '<', now()->subDays($days))->delete();
    }
}