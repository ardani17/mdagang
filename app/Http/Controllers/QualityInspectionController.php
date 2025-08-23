<?php

namespace App\Http\Controllers;

use App\Models\QualityInspection;
use App\Models\ProductionOrder;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QualityInspectionController extends Controller
{
    /**
     * Display a listing of quality inspections
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = QualityInspection::with(['productionOrder.recipe.product', 'inspector']);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('inspection_code', 'like', "%{$search}%")
                      ->orWhere('batch_number', 'like', "%{$search}%")
                      ->orWhereHas('productionOrder', function ($q2) use ($search) {
                          $q2->where('order_number', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by result
            if ($request->has('result')) {
                $query->where('result', $request->result);
            }

            // Filter by production order
            if ($request->has('production_order_id')) {
                $query->where('production_order_id', $request->production_order_id);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('inspection_date', [$request->start_date, $request->end_date]);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $inspections = $query->paginate($perPage);

            return $this->successResponse($inspections, 'Quality inspections retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve quality inspections: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created quality inspection
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'production_order_id' => 'required|exists:production_orders,id',
            'inspection_date' => 'required|date',
            'inspector_id' => 'required|exists:users,id',
            'sample_size' => 'required|integer|min:1',
            'parameters' => 'required|array',
            'parameters.*.name' => 'required|string',
            'parameters.*.standard' => 'required|string',
            'parameters.*.actual' => 'required|string',
            'parameters.*.passed' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $productionOrder = ProductionOrder::find($request->production_order_id);
            
            // Check if production order is completed
            if ($productionOrder->status !== 'completed') {
                return $this->errorResponse('Production order must be completed before inspection', 422);
            }

            // Check if already inspected
            if ($productionOrder->qualityInspection) {
                return $this->errorResponse('Production order already has a quality inspection', 422);
            }

            // Generate inspection code
            $inspectionCode = 'QC-' . date('Ymd') . '-' . str_pad(
                QualityInspection::whereDate('created_at', today())->count() + 1,
                3,
                '0',
                STR_PAD_LEFT
            );

            // Calculate overall result
            $allPassed = collect($request->parameters)->every(function ($param) {
                return $param['passed'] === true;
            });

            // Create quality inspection
            $inspection = QualityInspection::create([
                'inspection_code' => $inspectionCode,
                'production_order_id' => $request->production_order_id,
                'batch_number' => $productionOrder->batch_number,
                'inspection_date' => $request->inspection_date,
                'inspector_id' => $request->inspector_id,
                'sample_size' => $request->sample_size,
                'parameters' => $request->parameters,
                'result' => $allPassed ? 'passed' : 'failed',
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Log activity
            ActivityLog::logCreation($inspection, 'Created quality inspection: ' . $inspectionCode);

            DB::commit();
            return $this->successResponse(
                $inspection->load(['productionOrder.recipe.product', 'inspector']),
                'Quality inspection created successfully',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to create quality inspection: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified quality inspection
     */
    public function show($id): JsonResponse
    {
        try {
            $inspection = QualityInspection::with([
                'productionOrder.recipe.product',
                'productionOrder.recipe.ingredients.rawMaterial',
                'inspector',
                'approvedBy'
            ])->findOrFail($id);

            // Add statistics
            $inspection->statistics = [
                'total_parameters' => count($inspection->parameters),
                'passed_parameters' => collect($inspection->parameters)->where('passed', true)->count(),
                'failed_parameters' => collect($inspection->parameters)->where('passed', false)->count(),
                'pass_rate' => count($inspection->parameters) > 0 
                    ? round((collect($inspection->parameters)->where('passed', true)->count() / count($inspection->parameters)) * 100, 2)
                    : 0,
            ];

            return $this->successResponse($inspection, 'Quality inspection retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Quality inspection not found', 404);
        }
    }

    /**
     * Update the specified quality inspection
     */
    public function update(Request $request, $id): JsonResponse
    {
        $inspection = QualityInspection::find($id);
        if (!$inspection) {
            return $this->errorResponse('Quality inspection not found', 404);
        }

        // Only allow updates if status is pending
        if ($inspection->status !== 'pending') {
            return $this->errorResponse('Cannot update approved or rejected inspection', 422);
        }

        $validator = Validator::make($request->all(), [
            'inspection_date' => 'sometimes|required|date',
            'inspector_id' => 'sometimes|required|exists:users,id',
            'sample_size' => 'sometimes|required|integer|min:1',
            'parameters' => 'sometimes|required|array',
            'parameters.*.name' => 'required_with:parameters|string',
            'parameters.*.standard' => 'required_with:parameters|string',
            'parameters.*.actual' => 'required_with:parameters|string',
            'parameters.*.passed' => 'required_with:parameters|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $oldValues = $inspection->toArray();

            // Update parameters if provided
            if ($request->has('parameters')) {
                // Calculate overall result
                $allPassed = collect($request->parameters)->every(function ($param) {
                    return $param['passed'] === true;
                });
                $inspection->result = $allPassed ? 'passed' : 'failed';
            }

            $inspection->fill($request->except('parameters'));
            if ($request->has('parameters')) {
                $inspection->parameters = $request->parameters;
            }
            $inspection->save();

            // Log activity
            $changes = array_diff_assoc($inspection->toArray(), $oldValues);
            if (!empty($changes)) {
                ActivityLog::logUpdate($inspection, $changes, 'Updated quality inspection: ' . $inspection->inspection_code);
            }

            DB::commit();
            return $this->successResponse(
                $inspection->load(['productionOrder.recipe.product', 'inspector']),
                'Quality inspection updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update quality inspection: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified quality inspection
     */
    public function destroy($id): JsonResponse
    {
        $inspection = QualityInspection::find($id);
        if (!$inspection) {
            return $this->errorResponse('Quality inspection not found', 404);
        }

        // Only allow deletion if status is pending
        if ($inspection->status !== 'pending') {
            return $this->errorResponse('Cannot delete approved or rejected inspection', 422);
        }

        DB::beginTransaction();
        try {
            // Log activity before deletion
            ActivityLog::logDeletion($inspection, 'Deleted quality inspection: ' . $inspection->inspection_code);
            
            $inspection->delete();
            
            DB::commit();
            return $this->successResponse(null, 'Quality inspection deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete quality inspection: ' . $e->getMessage());
        }
    }

    /**
     * Approve quality inspection
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $inspection = QualityInspection::find($id);
        if (!$inspection) {
            return $this->errorResponse('Quality inspection not found', 404);
        }

        if ($inspection->status !== 'pending') {
            return $this->errorResponse('Inspection has already been processed', 422);
        }

        $validator = Validator::make($request->all(), [
            'approval_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $inspection->status = 'approved';
            $inspection->approved_by = auth()->id();
            $inspection->approved_at = now();
            $inspection->approval_notes = $request->approval_notes;
            $inspection->save();

            // Update production order quality status
            $productionOrder = $inspection->productionOrder;
            $productionOrder->quality_status = $inspection->result;
            $productionOrder->save();

            // Log activity
            ActivityLog::log('update', $inspection, [
                'status' => ['old' => 'pending', 'new' => 'approved']
            ], 'Approved quality inspection: ' . $inspection->inspection_code);

            DB::commit();
            return $this->successResponse(
                $inspection->load(['productionOrder.recipe.product', 'inspector', 'approvedBy']),
                'Quality inspection approved successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to approve quality inspection: ' . $e->getMessage());
        }
    }

    /**
     * Reject quality inspection
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $inspection = QualityInspection::find($id);
        if (!$inspection) {
            return $this->errorResponse('Quality inspection not found', 404);
        }

        if ($inspection->status !== 'pending') {
            return $this->errorResponse('Inspection has already been processed', 422);
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string',
            'corrective_actions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            $inspection->status = 'rejected';
            $inspection->approved_by = auth()->id();
            $inspection->approved_at = now();
            $inspection->approval_notes = 'Rejection reason: ' . $request->rejection_reason;
            if ($request->corrective_actions) {
                $inspection->corrective_actions = $request->corrective_actions;
            }
            $inspection->save();

            // Update production order quality status
            $productionOrder = $inspection->productionOrder;
            $productionOrder->quality_status = 'rejected';
            $productionOrder->save();

            // Log activity
            ActivityLog::log('update', $inspection, [
                'status' => ['old' => 'pending', 'new' => 'rejected']
            ], 'Rejected quality inspection: ' . $inspection->inspection_code);

            DB::commit();
            return $this->successResponse(
                $inspection->load(['productionOrder.recipe.product', 'inspector', 'approvedBy']),
                'Quality inspection rejected'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to reject quality inspection: ' . $e->getMessage());
        }
    }

    /**
     * Get quality inspection statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_inspections' => QualityInspection::count(),
                'pending_inspections' => QualityInspection::where('status', 'pending')->count(),
                'approved_inspections' => QualityInspection::where('status', 'approved')->count(),
                'rejected_inspections' => QualityInspection::where('status', 'rejected')->count(),
                'passed_batches' => QualityInspection::where('result', 'passed')->count(),
                'failed_batches' => QualityInspection::where('result', 'failed')->count(),
                'pass_rate' => QualityInspection::count() > 0
                    ? round((QualityInspection::where('result', 'passed')->count() / QualityInspection::count()) * 100, 2)
                    : 0,
                'recent_inspections' => QualityInspection::with('productionOrder.recipe.product')
                    ->latest()
                    ->limit(5)
                    ->get(),
                'by_inspector' => QualityInspection::selectRaw('inspector_id, COUNT(*) as count')
                    ->with('inspector:id,name')
                    ->groupBy('inspector_id')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Quality inspection statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Generate quality report
     */
    public function generateReport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'product_id' => 'nullable|exists:products,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        try {
            $query = QualityInspection::with(['productionOrder.recipe.product'])
                ->whereBetween('inspection_date', [$request->start_date, $request->end_date]);

            if ($request->product_id) {
                $query->whereHas('productionOrder.recipe', function ($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                });
            }

            $inspections = $query->get();

            $report = [
                'period' => [
                    'start' => $request->start_date,
                    'end' => $request->end_date,
                ],
                'summary' => [
                    'total_inspections' => $inspections->count(),
                    'passed' => $inspections->where('result', 'passed')->count(),
                    'failed' => $inspections->where('result', 'failed')->count(),
                    'pass_rate' => $inspections->count() > 0
                        ? round(($inspections->where('result', 'passed')->count() / $inspections->count()) * 100, 2)
                        : 0,
                ],
                'by_product' => $inspections->groupBy('productionOrder.recipe.product.name')
                    ->map(function ($group, $productName) {
                        return [
                            'product' => $productName,
                            'total' => $group->count(),
                            'passed' => $group->where('result', 'passed')->count(),
                            'failed' => $group->where('result', 'failed')->count(),
                        ];
                    }),
                'common_failures' => $this->analyzeCommonFailures($inspections),
            ];

            return $this->successResponse($report, 'Quality report generated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Export quality inspections to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $query = QualityInspection::with(['productionOrder.recipe.product', 'inspector']);
            
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('inspection_date', [$request->start_date, $request->end_date]);
            }
            
            $inspections = $query->get();
            
            $csvData = [];
            $csvData[] = ['Inspection Code', 'Batch Number', 'Product', 'Inspection Date', 'Inspector', 'Result', 'Status'];
            
            foreach ($inspections as $inspection) {
                $csvData[] = [
                    $inspection->inspection_code,
                    $inspection->batch_number,
                    $inspection->productionOrder->recipe->product->name,
                    $inspection->inspection_date,
                    $inspection->inspector->name,
                    $inspection->result,
                    $inspection->status,
                ];
            }

            // In a real application, you would generate and return a CSV file
            return $this->successResponse($csvData, 'Quality inspections exported successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export quality inspections: ' . $e->getMessage());
        }
    }

    /**
     * Analyze common failures from inspections
     */
    private function analyzeCommonFailures($inspections)
    {
        $failures = [];
        
        foreach ($inspections as $inspection) {
            if ($inspection->result === 'failed' && is_array($inspection->parameters)) {
                foreach ($inspection->parameters as $param) {
                    if (!$param['passed']) {
                        $key = $param['name'];
                        if (!isset($failures[$key])) {
                            $failures[$key] = 0;
                        }
                        $failures[$key]++;
                    }
                }
            }
        }
        
        arsort($failures);
        
        return array_slice($failures, 0, 5, true);
    }
}