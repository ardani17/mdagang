<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'inspection_code',
        'inspection_date',
        'inspector_id',
        'batch_number',
        'sample_size',
        'passed',
        'defects_found',
        'defect_rate',
        'parameters',
        'notes',
        'corrective_actions',
        'approved_by',
        'approval_date',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'defects_found' => 'integer',
        'defect_rate' => 'decimal:2',
        'sample_size' => 'integer',
        'parameters' => 'array',
        'inspection_date' => 'datetime',
        'approval_date' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inspection) {
            if (empty($inspection->inspection_code)) {
                $inspection->inspection_code = self::generateInspectionCode();
            }
            if (empty($inspection->inspection_date)) {
                $inspection->inspection_date = now();
            }
        });

        static::saving(function ($inspection) {
            // Calculate defect rate
            if ($inspection->sample_size > 0) {
                $inspection->defect_rate = ($inspection->defects_found / $inspection->sample_size) * 100;
            }
        });
    }

    /**
     * Generate unique inspection code
     */
    public static function generateInspectionCode(): string
    {
        $prefix = 'QC-' . date('Ymd');
        $lastInspection = self::where('inspection_code', 'like', $prefix . '%')
            ->orderBy('inspection_code', 'desc')
            ->first();

        if ($lastInspection) {
            $lastNumber = intval(substr($lastInspection->inspection_code, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the production order for this inspection
     */
    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    /**
     * Get the inspector user
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    /**
     * Get the approver user
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include passed inspections
     */
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    /**
     * Scope a query to only include failed inspections
     */
    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    /**
     * Scope a query to only include approved inspections
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_by');
    }

    /**
     * Scope a query to only include pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->whereNull('approved_by');
    }

    /**
     * Check if inspection is approved
     */
    public function isApproved(): bool
    {
        return $this->approved_by !== null;
    }

    /**
     * Approve inspection
     */
    public function approve(int $userId = null): bool
    {
        $this->update([
            'approved_by' => $userId ?? auth()->id(),
            'approval_date' => now(),
        ]);

        return true;
    }

    /**
     * Get quality score (0-100)
     */
    public function getQualityScoreAttribute(): float
    {
        if ($this->defect_rate === null) {
            return 100;
        }

        return max(0, 100 - $this->defect_rate);
    }

    /**
     * Get quality grade
     */
    public function getQualityGradeAttribute(): string
    {
        $score = $this->quality_score;

        if ($score >= 95) {
            return 'A';
        } elseif ($score >= 85) {
            return 'B';
        } elseif ($score >= 75) {
            return 'C';
        } elseif ($score >= 60) {
            return 'D';
        } else {
            return 'F';
        }
    }

    /**
     * Get inspection status
     */
    public function getStatusAttribute(): string
    {
        if (!$this->passed) {
            return 'failed';
        }

        if (!$this->isApproved()) {
            return 'pending_approval';
        }

        return 'approved';
    }

    /**
     * Get inspection result summary
     */
    public function getSummary(): array
    {
        return [
            'code' => $this->inspection_code,
            'date' => $this->inspection_date->format('Y-m-d H:i'),
            'batch' => $this->batch_number,
            'passed' => $this->passed,
            'quality_score' => $this->quality_score,
            'quality_grade' => $this->quality_grade,
            'defects_found' => $this->defects_found,
            'defect_rate' => $this->defect_rate,
            'sample_size' => $this->sample_size,
            'status' => $this->status,
            'inspector' => $this->inspector?->name,
            'approver' => $this->approver?->name,
        ];
    }

    /**
     * Check parameters against standards
     */
    public function checkParameters(): array
    {
        $results = [];
        
        if (!$this->parameters || !is_array($this->parameters)) {
            return $results;
        }

        foreach ($this->parameters as $param) {
            $name = $param['name'] ?? 'Unknown';
            $value = $param['value'] ?? null;
            $min = $param['min'] ?? null;
            $max = $param['max'] ?? null;
            $target = $param['target'] ?? null;

            $status = 'pass';
            $deviation = null;

            if ($value !== null) {
                if ($min !== null && $value < $min) {
                    $status = 'fail';
                    $deviation = $value - $min;
                } elseif ($max !== null && $value > $max) {
                    $status = 'fail';
                    $deviation = $value - $max;
                } elseif ($target !== null) {
                    $deviation = $value - $target;
                    $tolerance = ($max - $min) / 4; // 25% tolerance
                    if (abs($deviation) > $tolerance) {
                        $status = 'warning';
                    }
                }
            }

            $results[] = [
                'name' => $name,
                'value' => $value,
                'target' => $target,
                'min' => $min,
                'max' => $max,
                'status' => $status,
                'deviation' => $deviation,
            ];
        }

        return $results;
    }

    /**
     * Add parameter check
     */
    public function addParameter(array $parameter): void
    {
        $parameters = $this->parameters ?? [];
        $parameters[] = $parameter;
        $this->update(['parameters' => $parameters]);
    }

    /**
     * Record defect
     */
    public function recordDefect(string $description, string $severity = 'minor'): void
    {
        $defects = $this->parameters ?? [];
        $defects[] = [
            'type' => 'defect',
            'description' => $description,
            'severity' => $severity,
            'recorded_at' => now()->toDateTimeString(),
        ];
        
        $this->update([
            'parameters' => $defects,
            'defects_found' => $this->defects_found + 1,
        ]);
    }

    /**
     * Add corrective action
     */
    public function addCorrectiveAction(string $action): void
    {
        $actions = $this->corrective_actions ? $this->corrective_actions . "\n" : '';
        $actions .= "- " . $action . " (" . now()->format('Y-m-d H:i') . ")";
        $this->update(['corrective_actions' => $actions]);
    }
}