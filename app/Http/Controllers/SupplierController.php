<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Supplier::query();

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $suppliers = $query->paginate($perPage);

            // Use paginatedResponse for paginated data
            return $this->paginatedResponse($suppliers, 'Suppliers retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve suppliers: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
        // Validation rules based on the Supplier model fillable fields
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:suppliers',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|integer|min:0',
            'lead_time_days' => 'nullable|integer|min:1',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:1|max:5',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }
            // For regular form submission, redirect back with errors
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Prepare data for creation
            $data = $request->all();
            
            // Generate a unique code if not provided
            if (empty($data['code'])) {
                $data['code'] = 'SUP' . str_pad(Supplier::count() + 1, 4, '0', STR_PAD_LEFT);
            }
            
            // Set default values
            if (!isset($data['is_active'])) {
                $data['is_active'] = true;
            }
            
            if (!isset($data['rating'])) {
                $data['rating'] = 3.0;
            }

            $supplier = Supplier::create($data);

            // Log activity
            ActivityLog::logCreation($supplier, 'Created new supplier: ' . $supplier->name);

            DB::commit();
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson()) {
                return $this->successResponse($supplier, 'Supplier created successfully', 201);
            }
            
            // For regular form submission, redirect with success message
            return redirect()->route('manufacturing.raw-materials.suppliers')
                ->with('success', 'Supplier berhasil ditambahkan: ' . $supplier->name);
                
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Handle database errors more gracefully
            $errorMessage = 'Gagal menambahkan supplier.';
            
            // Check for specific database errors
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'email')) {
                    $errorMessage = 'Email sudah terdaftar untuk supplier lain.';
                } elseif (str_contains($e->getMessage(), 'code')) {
                    $errorMessage = 'Kode supplier sudah digunakan.';
                } else {
                    $errorMessage = 'Data duplikat terdeteksi. Silakan periksa kembali.';
                }
            } elseif (str_contains($e->getMessage(), 'does not exist')) {
                $errorMessage = 'Terjadi kesalahan konfigurasi database. Silakan hubungi administrator.';
            }
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson()) {
                return $this->errorResponse($errorMessage, 422);
            }
            
            // For regular form submission, redirect back with error
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the actual error for debugging
            \Log::error('Supplier creation failed: ' . $e->getMessage());
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson()) {
                return $this->errorResponse('Terjadi kesalahan sistem. Silakan coba lagi.', 500);
            }
            
            // For regular form submission, redirect back with error
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Display the specified supplier
     */
    public function show($id): JsonResponse
    {
        try {
            $supplier = Supplier::with(['rawMaterials', 'purchaseOrders' => function ($query) {
                $query->latest()->limit(5);
            }])->findOrFail($id);

            // Add statistics
            $supplier->statistics = [
                'total_materials' => $supplier->rawMaterials()->count(),
                'active_materials' => $supplier->rawMaterials()->where('status', 'active')->count(),
                'total_orders' => $supplier->purchaseOrders()->count(),
                'pending_orders' => $supplier->purchaseOrders()->where('status', 'pending')->count(),
                'total_spent' => $supplier->purchaseOrders()->where('status', 'received')->sum('total_amount'),
            ];

            return $this->successResponse($supplier, 'Supplier retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Supplier not found', 404);
        }
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            if ($request->expectsJson()) {
                return $this->errorResponse('Supplier not found', 404);
            }
            return redirect()->route('manufacturing.raw-materials.suppliers')
                ->with('error', 'Supplier tidak ditemukan');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code,' . $id,
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'payment_terms' => 'nullable|string|max:100',
            'lead_time_days' => 'nullable|integer|min:0',
            'rating' => 'nullable|numeric|min:1|max:5',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return $this->errorResponse('Validation failed', 422, $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $oldValues = $supplier->toArray();
            
            // Prepare data for update
            $data = $request->all();
            
            // Handle boolean conversion for is_active
            if (isset($data['is_active'])) {
                $data['is_active'] = (bool) $data['is_active'];
            }
            
            $supplier->update($data);
            
            // Log activity
            $changes = array_diff_assoc($data, $oldValues);
            if (!empty($changes)) {
                ActivityLog::logUpdate($supplier, $changes, 'Updated supplier: ' . $supplier->name);
            }

            DB::commit();
            
            if ($request->expectsJson()) {
                return $this->successResponse($supplier, 'Supplier updated successfully');
            }
            
            return redirect()->route('manufacturing.raw-materials.suppliers.show', $supplier->id)
                ->with('success', 'Supplier berhasil diperbarui: ' . $supplier->name);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return $this->errorResponse('Failed to update supplier: ' . $e->getMessage());
            }
            
            return redirect()->back()
                ->with('error', 'Gagal memperbarui supplier: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified supplier
     */
    public function destroy($id): JsonResponse
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return $this->errorResponse('Supplier not found', 404);
        }

        // Check if supplier has related records
        if ($supplier->rawMaterials()->exists()) {
            return $this->errorResponse('Cannot delete supplier with associated raw materials', 422);
        }

        if ($supplier->purchaseOrders()->exists()) {
            return $this->errorResponse('Cannot delete supplier with associated purchase orders', 422);
        }

        DB::beginTransaction();
        try {
            // Log activity before deletion
            ActivityLog::logDeletion($supplier, 'Deleted supplier: ' . $supplier->name);
            
            $supplier->delete();
            
            DB::commit();
            return $this->successResponse(null, 'Supplier deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to delete supplier: ' . $e->getMessage());
        }
    }

    /**
     * Get supplier statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_suppliers' => Supplier::count(),
                'active_suppliers' => Supplier::where('status', 'active')->count(),
                'inactive_suppliers' => Supplier::where('status', 'inactive')->count(),
                'by_type' => Supplier::selectRaw('type, count(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type'),
                'top_suppliers' => Supplier::withCount('purchaseOrders')
                    ->orderBy('purchase_orders_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'name', 'code']),
                'recent_suppliers' => Supplier::latest()
                    ->limit(5)
                    ->get(['id', 'name', 'code', 'created_at']),
            ];

            return $this->successResponse($stats, 'Supplier statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Toggle supplier status
     */
    public function toggleStatus($id): JsonResponse
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return $this->errorResponse('Supplier not found', 404);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $supplier->status;
            $supplier->status = $supplier->status === 'active' ? 'inactive' : 'active';
            $supplier->save();

            // Log activity
            ActivityLog::logUpdate(
                $supplier, 
                ['status' => ['old' => $oldStatus, 'new' => $supplier->status]], 
                'Changed supplier status: ' . $supplier->name
            );

            DB::commit();
            return $this->successResponse($supplier, 'Supplier status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update supplier status: ' . $e->getMessage());
        }
    }

    /**
     * Get supplier's raw materials
     */
    public function rawMaterials($id): JsonResponse
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return $this->errorResponse('Supplier not found', 404);
        }

        try {
            $materials = $supplier->rawMaterials()
                ->with('category')
                ->paginate(15);

            return $this->successResponse($materials, 'Raw materials retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve raw materials: ' . $e->getMessage());
        }
    }

    /**
     * Get supplier's purchase orders
     */
    public function purchaseOrders($id): JsonResponse
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return $this->errorResponse('Supplier not found', 404);
        }

        try {
            $orders = $supplier->purchaseOrders()
                ->with('items.rawMaterial')
                ->latest()
                ->paginate(15);

            return $this->successResponse($orders, 'Purchase orders retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve purchase orders: ' . $e->getMessage());
        }
    }

    /**
     * Export suppliers to CSV
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $suppliers = Supplier::all();
            
            $csvData = [];
            $csvData[] = ['ID', 'Code', 'Name', 'Type', 'Contact Person', 'Email', 'Phone', 'City', 'Country', 'Status', 'Created At'];
            
            foreach ($suppliers as $supplier) {
                $csvData[] = [
                    $supplier->id,
                    $supplier->code,
                    $supplier->name,
                    $supplier->type,
                    $supplier->contact_person,
                    $supplier->email,
                    $supplier->phone,
                    $supplier->city,
                    $supplier->country,
                    $supplier->status,
                    $supplier->created_at->format('Y-m-d H:i:s'),
                ];
            }

            // In a real application, you would generate and return a CSV file
            // For now, we'll just return the data
            return $this->successResponse($csvData, 'Suppliers exported successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export suppliers: ' . $e->getMessage());
        }
    }
}