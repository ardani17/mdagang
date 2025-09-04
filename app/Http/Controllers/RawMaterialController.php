<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\RawMaterialService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RawMaterialController extends Controller
{
    protected RawMaterialService $service;

    /**
     * Create a new controller instance.
     */
    public function __construct(RawMaterialService $service)
    {
        // $this->middleware('auth');
        $this->service = $service;
    }

    /**
     * Display a listing of raw materials
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search', 
                'category_id', 
                'supplier_id', 
                'status', 
                'stock_status',
                'sort_by',
                'sort_order'
            ]);
            
            $perPage = $request->get('per_page', 15);
            $materials = $this->service->getPaginated($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Raw materials retrieved successfully',
                'data' => $materials
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve raw materials', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve raw materials',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    public function get(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search', 
                'category_id', 
                'supplier_id', 
                'status', 
                'stock_status',
                'sort_by',
                'sort_order'
            ]);
            
            $perPage = $request->get('per_page', 15);
            $materials = $this->service->getPaginated($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Raw materials retrieved successfully',
                'data' => $materials
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve raw materials', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve raw materials',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Store a newly created raw material
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:raw_materials',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'required|string|max:50',
            'last_purchase_price' => 'required|numeric|min:0',
            'average_price' => 'nullable|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0',
            'reorder_point' => 'nullable|numeric|min:0',
            'reorder_quantity' => 'nullable|numeric|min:0',
            'lead_time_days' => 'nullable|integer|min:0',
            'storage_location' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            
            // Set defaults
            $data['is_active'] = $data['is_active'] ?? true;
            $data['reorder_point'] = $data['reorder_point'] ?? $data['minimum_stock'];
            $data['reorder_quantity'] = $data['reorder_quantity'] ?? ($data['maximum_stock'] ?? $data['minimum_stock'] * 2) - $data['minimum_stock'];
            
            $material = $this->service->create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Raw material created successfully',
                'data' => $material
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Failed to create raw material', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create raw material',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Display the specified raw material
     */
    public function show($id): JsonResponse
    {
        try {
            $material = RawMaterial::with(['category', 'supplier'])->findOrFail($id);
            
            // Add statistics
            $material->statistics = $material->getStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Raw material retrieved successfully',
                'data' => $material
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Raw material not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve raw material', [
                'material_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve raw material',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Update the specified raw material
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $material = RawMaterial::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'code' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('raw_materials')->ignore($material->id)
                ],
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:categories,id',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'unit' => 'nullable|string|max:50',
                'last_purchase_price' => 'nullable|numeric|min:0',
                'average_price' => 'nullable|numeric|min:0',
                'minimum_stock' => 'nullable|numeric|min:0',
                'maximum_stock' => 'nullable|numeric|min:0',
                'reorder_point' => 'nullable|numeric|min:0',
                'reorder_quantity' => 'nullable|numeric|min:0',
                'lead_time_days' => 'nullable|integer|min:0',
                'storage_location' => 'nullable|string|max:100',
                'expiry_date' => 'nullable|date',
                'notes' => 'nullable|string',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $material = $this->service->update($material, $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Raw material updated successfully',
                'data' => $material
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Raw material not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Failed to update raw material', [
                'material_id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update raw material',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Remove the specified raw material
     */
    public function destroy($id): JsonResponse
    {
        try {
            $material = RawMaterial::findOrFail($id);
            $result = $this->service->delete($material);
            
            $message = 'Raw material deleted successfully';
            if ($result['had_stock']) {
                $message .= '. Stock was automatically adjusted to zero and recorded in movement history.';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Raw material not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Failed to delete raw material', [
                'material_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $message = 'Failed to delete raw material';
            if (strpos($e->getMessage(), 'Cannot delete') !== false) {
                $message = $e->getMessage();
            }
            
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 400);
        }
    }

    /**
     * Adjust stock for a raw material
     */
    public function adjustStock(Request $request, $id): JsonResponse
    {
        try {
            $material = RawMaterial::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'adjustment_type' => 'required|in:add,subtract,set',
                'quantity' => 'required|numeric|min:0',
                'reason' => 'required|in:purchase,production,adjustment,damage,return,other',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            
            // Check if subtract would result in negative stock
            if ($data['adjustment_type'] === 'subtract' && $material->current_stock < $data['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock',
                    'error' => 'Current stock: ' . $material->current_stock . ', requested: ' . $data['quantity']
                ], 422);
            }
            
            $material = $this->service->adjustStock(
                $material,
                $data['adjustment_type'],
                $data['quantity'],
                $data['reason'],
                $data['notes'] ?? null
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'data' => $material
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Raw material not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Failed to adjust stock', [
                'material_id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Get stock movements for a raw material
     */
    public function stockMovements(Request $request, $id): JsonResponse
    {
        try {
            $material = RawMaterial::find($id);
            
            if (!$material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Raw material not found',
                    'data' => [
                        'data' => [],
                        'total' => 0,
                        'current_page' => 1,
                        'per_page' => 15
                    ]
                ], 404);
            }
            
            $perPage = $request->get('per_page', 15);
            $movements = $material->stockMovements()
                ->with(['createdBy' => function($query) {
                    $query->select('id', 'name');
                }])
                ->latest()
                ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Stock movements retrieved successfully',
                'data' => $movements
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve stock movements', [
                'material_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty data structure instead of error
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stock movements',
                'data' => [
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'per_page' => 15
                ],
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 200); // Return 200 with empty data to prevent frontend errors
        }
    }

    /**
     * Get low stock materials
     */
    public function lowStock(): JsonResponse
    {
        try {
            $materials = $this->service->getLowStock();
            
            return response()->json([
                'success' => true,
                'message' => 'Low stock materials retrieved successfully',
                'data' => $materials
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve low stock materials', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve low stock materials',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Get material statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->service->getStatistics();
            
            return response()->json([
                'success' => true,
                'message' => 'Raw material statistics retrieved successfully',
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Get form data (categories, units, suppliers)
     */
    public function getFormData(): JsonResponse
    {
        try {
            $data = [
                'categories' => Category::active()
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name']),
                'units' => [
                    ['value' => 'kg', 'label' => 'Kilogram (kg)'],
                    ['value' => 'liter', 'label' => 'Liter'],
                    ['value' => 'pcs', 'label' => 'Pieces (pcs)'],
                    ['value' => 'gram', 'label' => 'Gram'],
                    ['value' => 'ml', 'label' => 'Mililiter (ml)'],
                    ['value' => 'ton', 'label' => 'Ton'],
                    ['value' => 'box', 'label' => 'Box'],
                    ['value' => 'pack', 'label' => 'Pack'],
                ],
                'suppliers' => Supplier::active()
                    ->orderBy('name')
                    ->get(['id', 'name', 'contact_person', 'phone', 'email', 'address', 'rating', 'lead_time_days'])
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Form data retrieved successfully',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve form data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve form data',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Get categories for dropdown
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = Category::active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name']);
            
            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve categories', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Get units for dropdown
     */
    public function getUnits(): JsonResponse
    {
        try {
            $units = [
                ['value' => 'kg', 'label' => 'Kilogram (kg)'],
                ['value' => 'liter', 'label' => 'Liter'],
                ['value' => 'pcs', 'label' => 'Pieces (pcs)'],
                ['value' => 'gram', 'label' => 'Gram'],
                ['value' => 'ml', 'label' => 'Mililiter (ml)'],
                ['value' => 'ton', 'label' => 'Ton'],
                ['value' => 'box', 'label' => 'Box'],
                ['value' => 'pack', 'label' => 'Pack'],
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Units retrieved successfully',
                'data' => $units
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to retrieve units', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve units',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * Export raw materials to CSV
     */
    public function export(): JsonResponse
    {
        try {
            $csvData = $this->service->exportToArray();
            
            return response()->json([
                'success' => true,
                'message' => 'Raw materials exported successfully',
                'data' => $csvData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to export raw materials', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to export raw materials',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    public function updatePrice($id, Request $request): JsonResponse
    {
        try {
            $request->validate([
                'unit_cost' => 'required|numeric|min:0'
            ]);

            $material = RawMaterial::findOrFail($id);
            $material->average_price = $request->unit_cost;
            $material->save();

            return response()->json([
                'success' => true,
                'message' => 'Material price updated successfully',
                'data' => [
                    'material' => $material,
                    'new_price' => $material->average_price
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update material price: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}