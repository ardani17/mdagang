<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = Category::with('parent');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by parent
        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        if ($request->get('per_page') === 'all') {
            $categories = $query->get();
            return $this->successResponse($categories);
        }

        $categories = $query->paginate($request->get('per_page', 15));
        return $this->paginatedResponse($categories);
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'type' => 'required|in:product,raw_material,expense,income',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
        ]);

        // Check for circular reference
        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            if ($parent->type !== $request->type) {
                return $this->errorResponse('Parent category must be of the same type', 422);
            }
        }

        $category = Category::create($validated);
        $category->load('parent');

        ActivityLog::logCreation($category, 'Category created: ' . $category->name);

        return $this->successResponse($category, 'Category created successfully', 201);
    }

    /**
     * Display the specified category
     */
    public function show($id)
    {
        $category = Category::with(['parent', 'children', 'products', 'rawMaterials'])
            ->findOrFail($id);

        $data = $category->toArray();
        $data['statistics'] = [
            'children_count' => $category->children->count(),
            'products_count' => $category->products->count(),
            'raw_materials_count' => $category->rawMaterials->count(),
        ];

        return $this->successResponse($data);
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('categories')->ignore($category->id),
            ],
            'type' => 'sometimes|in:product,raw_material,expense,income',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
        ]);

        // Check for circular reference
        if ($request->has('parent_id') && $request->parent_id) {
            if ($request->parent_id == $category->id) {
                return $this->errorResponse('Category cannot be its own parent', 422);
            }

            // Check if the new parent is a child of this category
            $parent = Category::find($request->parent_id);
            if ($this->isDescendant($category, $parent)) {
                return $this->errorResponse('Cannot set a descendant as parent', 422);
            }

            if ($parent->type !== ($request->type ?? $category->type)) {
                return $this->errorResponse('Parent category must be of the same type', 422);
            }
        }

        $oldData = $category->toArray();
        $category->update($validated);
        $category->load('parent');

        // Log changes
        $changes = [];
        foreach ($validated as $key => $value) {
            if ($oldData[$key] != $value) {
                $changes[$key] = [
                    'old' => $oldData[$key],
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            ActivityLog::logUpdate($category, $changes, 'Category updated: ' . $category->name);
        }

        return $this->successResponse($category, 'Category updated successfully');
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Check if category has children
        if ($category->children()->exists()) {
            return $this->errorResponse('Cannot delete category with subcategories', 422);
        }

        // Check if category has products
        if ($category->products()->exists()) {
            return $this->errorResponse('Cannot delete category with products', 422);
        }

        // Check if category has raw materials
        if ($category->rawMaterials()->exists()) {
            return $this->errorResponse('Cannot delete category with raw materials', 422);
        }

        $categoryName = $category->name;
        $category->delete();

        ActivityLog::logDeletion($category, 'Category deleted: ' . $categoryName);

        return $this->successResponse(null, 'Category deleted successfully');
    }

    /**
     * Get category tree structure
     */
    public function tree(Request $request)
    {
        $query = Category::whereNull('parent_id')->with('children');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $categories = $query->get();
        
        return $this->successResponse($categories);
    }

    /**
     * Check if a category is a descendant of another
     */
    private function isDescendant($category, $potentialDescendant)
    {
        $children = $category->children;
        
        foreach ($children as $child) {
            if ($child->id == $potentialDescendant->id) {
                return true;
            }
            if ($this->isDescendant($child, $potentialDescendant)) {
                return true;
            }
        }
        
        return false;
    }
}