<?php

namespace App\Services;

use App\Models\RawMaterial;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class RawMaterialService
{
    /**
     * Get paginated raw materials with filters
     */
    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        $query = RawMaterial::with(['category', 'supplier']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Apply supplier filter
        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply stock status filter
        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'low':
                    $query->whereRaw('current_stock <= minimum_stock');
                    break;
                case 'critical':
                    $query->whereRaw('current_stock <= minimum_stock / 2');
                    break;
                case 'out_of_stock':
                    $query->where('current_stock', 0);
                    break;
                case 'in_stock':
                    $query->whereRaw('current_stock > minimum_stock');
                    break;
            }
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Create a new raw material
     */
    public function create(array $data): RawMaterial
    {
        DB::beginTransaction();
        
        try {
            // Set average_price if not provided
            if (!isset($data['average_price']) || empty($data['average_price'])) {
                $data['average_price'] = $data['last_purchase_price'] ?? 0;
            }
            
            // Create the raw material
            $material = RawMaterial::create($data);
            
            // Create initial stock movement if there's initial stock
            if ($material->current_stock > 0) {
                StockMovement::create([
                    'item_type' => 'raw_material',
                    'item_id' => $material->id,
                    'type' => 'in',
                    'quantity' => $material->current_stock,
                    'unit_cost' => $material->last_purchase_price,
                    'total_cost' => $material->current_stock * $material->last_purchase_price,
                    'before_stock' => 0,
                    'after_stock' => $material->current_stock,
                    'reason' => 'initial_stock',
                    'notes' => 'Initial stock entry',
                    'created_by' => auth()->id(),
                ]);
            }
            
            // Log activity
            ActivityLog::logCreation($material, 'Created new raw material: ' . $material->name);
            
            DB::commit();
            
            Log::info('Raw material created successfully', [
                'material_id' => $material->id,
                'material_name' => $material->name,
                'created_by' => auth()->id()
            ]);
            
            return $material->load(['category', 'supplier']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create raw material', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            throw $e;
        }
    }

    /**
     * Update a raw material
     */
    public function update(RawMaterial $material, array $data): RawMaterial
    {
        DB::beginTransaction();
        
        try {
            $oldValues = $material->toArray();
            
            // Don't allow direct stock updates through regular update
            unset($data['current_stock']);
            
            // Update average_price if last_purchase_price changes
            if (isset($data['last_purchase_price']) && !isset($data['average_price'])) {
                $data['average_price'] = $data['last_purchase_price'];
            }
            
            // Update the material
            $material->update($data);
            
            // Log activity with changes
            $changes = array_diff_assoc($data, $oldValues);
            if (!empty($changes)) {
                ActivityLog::logUpdate($material, $changes, 'Updated raw material: ' . $material->name);
            }
            
            DB::commit();
            
            Log::info('Raw material updated successfully', [
                'material_id' => $material->id,
                'material_name' => $material->name,
                'changes' => $changes,
                'updated_by' => auth()->id()
            ]);
            
            return $material->load(['category', 'supplier']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update raw material', [
                'material_id' => $material->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            throw $e;
        }
    }

    /**
     * Delete a raw material
     */
    public function delete(RawMaterial $material): array
    {
        DB::beginTransaction();
        
        try {
            // Check if material can be deleted
            if (!$material->canBeDeleted()) {
                throw new \Exception('Cannot delete raw material that is used in active recipes or has pending orders.');
            }
            
            $stockValue = 0;
            $hasStock = $material->current_stock > 0;
            $originalStock = $material->current_stock;
            
            // If material has current stock, adjust it to zero first
            if ($hasStock) {
                $stockValue = $material->current_stock * $material->average_price;
                
                // Create stock movement record for the adjustment
                StockMovement::create([
                    'item_type' => 'raw_material',
                    'item_id' => $material->id,
                    'type' => 'out',
                    'quantity' => $material->current_stock,
                    'unit_cost' => $material->average_price,
                    'total_cost' => $stockValue,
                    'before_stock' => $material->current_stock,
                    'after_stock' => 0,
                    'reason' => 'material_deletion',
                    'notes' => 'Stock adjusted to zero before material deletion - ' . $material->name,
                    'created_by' => auth()->id(),
                ]);
                
                // Log the stock adjustment activity
                ActivityLog::log('update', $material, [
                    'stock_adjustment' => [
                        'old_stock' => $material->current_stock,
                        'new_stock' => 0,
                        'reason' => 'material_deletion',
                        'value_impact' => $stockValue
                    ]
                ], 'Stock adjusted to zero before deletion: ' . $material->name);
                
                // Set stock to zero
                $material->current_stock = 0;
                $material->status = 'out_of_stock';
                $material->save();
            }
            
            // Log activity before deletion
            $deletionMessage = 'Deleted raw material: ' . $material->name;
            if ($hasStock) {
                $deletionMessage .= ' (Stock adjusted from ' . number_format($originalStock, 2) . ' units, value: Rp ' . number_format($stockValue, 0, ',', '.') . ')';
            }
            ActivityLog::logDeletion($material, $deletionMessage);
            
            // Delete the material
            $materialName = $material->name;
            $material->delete();
            
            DB::commit();
            
            Log::info('Raw material deleted successfully', [
                'material_name' => $materialName,
                'had_stock' => $hasStock,
                'stock_value_adjusted' => $stockValue,
                'deleted_by' => auth()->id()
            ]);
            
            return [
                'deleted_material' => $materialName,
                'had_stock' => $hasStock,
                'stock_value_adjusted' => $stockValue
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete raw material', [
                'material_id' => $material->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Adjust stock for a raw material
     */
    public function adjustStock(RawMaterial $material, string $adjustmentType, float $quantity, string $reason, ?string $notes = null): RawMaterial
    {
        DB::beginTransaction();
        
        try {
            $oldStock = $material->current_stock;
            
            // Perform the stock adjustment
            $material->adjustStock($adjustmentType, $quantity, $reason, $notes);
            
            // Log activity
            ActivityLog::log('update', $material, [
                'stock' => ['old' => $oldStock, 'new' => $material->current_stock],
                'adjustment_type' => $adjustmentType,
                'quantity' => $quantity,
                'reason' => $reason
            ], 'Adjusted stock for raw material: ' . $material->name);
            
            // Check if low stock alert needed
            if ($material->status === 'critical' || $material->status === 'low_stock') {
                // Trigger low stock notification (implement notification system)
                $this->sendLowStockNotification($material);
            }
            
            DB::commit();
            
            Log::info('Stock adjusted successfully', [
                'material_id' => $material->id,
                'material_name' => $material->name,
                'adjustment_type' => $adjustmentType,
                'quantity' => $quantity,
                'old_stock' => $oldStock,
                'new_stock' => $material->current_stock,
                'reason' => $reason,
                'adjusted_by' => auth()->id()
            ]);
            
            return $material->fresh(['category', 'supplier']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to adjust stock', [
                'material_id' => $material->id,
                'error' => $e->getMessage(),
                'adjustment_data' => compact('adjustmentType', 'quantity', 'reason', 'notes')
            ]);
            
            throw $e;
        }
    }

    /**
     * Get low stock materials
     */
    public function getLowStock(): Collection
    {
        return RawMaterial::with(['supplier'])
            ->whereRaw('current_stock <= minimum_stock')
            ->orderBy('current_stock', 'asc')
            ->get()
            ->map(function ($material) {
                $material->stock_percentage = $material->minimum_stock > 0 
                    ? round(($material->current_stock / $material->minimum_stock) * 100, 2) 
                    : 0;
                return $material;
            });
    }

    /**
     * Get material statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_items' => RawMaterial::count(),
            'active_materials' => RawMaterial::where('is_active', true)->count(),
            'low_stock_items' => RawMaterial::where('status', 'low_stock')->count(),
            'critical_items' => RawMaterial::where('status', 'critical')->count(),
            'out_of_stock_materials' => RawMaterial::where('status', 'out_of_stock')->count(),
            'total_value' => RawMaterial::selectRaw('SUM(current_stock * average_price) as total')
                ->first()->total ?? 0,
            'by_category' => RawMaterial::with('category:id,name')
                ->select('category_id', DB::raw('count(*) as count'))
                ->groupBy('category_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'category' => $item->category ? $item->category->name : 'Uncategorized',
                        'count' => $item->count
                    ];
                }),
            'by_supplier' => RawMaterial::with('supplier:id,name')
                ->select('supplier_id', DB::raw('count(*) as count'))
                ->whereNotNull('supplier_id')
                ->groupBy('supplier_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'supplier' => $item->supplier ? $item->supplier->name : 'Unknown',
                        'count' => $item->count
                    ];
                }),
        ];
    }

    /**
     * Export raw materials to array for CSV
     */
    public function exportToArray(): array
    {
        $materials = RawMaterial::with(['category', 'supplier'])->get();
        
        $csvData = [];
        $csvData[] = ['ID', 'Code', 'Name', 'Category', 'Supplier', 'Unit', 'Average Price', 'Current Stock', 'Min Stock', 'Stock Value', 'Status'];
        
        foreach ($materials as $material) {
            $csvData[] = [
                $material->id,
                $material->code,
                $material->name,
                $material->category_name,
                $material->supplier ? $material->supplier->name : '-',
                $material->unit,
                $material->average_price,
                $material->current_stock,
                $material->minimum_stock,
                $material->stock_value,
                $material->status,
            ];
        }
        
        return $csvData;
    }

    /**
     * Send low stock notification
     */
    protected function sendLowStockNotification(RawMaterial $material): void
    {
        // Implement notification logic here
        // For now, just log it
        Log::warning('Low stock alert', [
            'material_id' => $material->id,
            'material_name' => $material->name,
            'current_stock' => $material->current_stock,
            'minimum_stock' => $material->minimum_stock,
            'status' => $material->status
        ]);
    }

    /**
     * Bulk update materials status
     */
    public function bulkUpdateStatus(): int
    {
        $updated = 0;
        
        RawMaterial::chunk(100, function ($materials) use (&$updated) {
            foreach ($materials as $material) {
                $oldStatus = $material->status;
                $material->updateStatus();
                
                if ($oldStatus !== $material->status) {
                    $material->save();
                    $updated++;
                }
            }
        });
        
        Log::info('Bulk status update completed', [
            'updated_count' => $updated
        ]);
        
        return $updated;
    }
}