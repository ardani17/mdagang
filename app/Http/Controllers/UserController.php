<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;

class UserController extends Controller
{
    /**
     * Get users with filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::query();

            // Apply search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('department', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                });
            }

            // Apply role filter
            if ($request->has('role') && $request->role) {
                $query->where('role', $request->role);
            }

            // Apply status filter
            if ($request->has('status') && $request->status) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($request->status === 'pending') {
                    $query->whereNull('email_verified_at');
                } elseif ($request->status === 'suspended') {
                    $query->where('is_active', false)->whereNotNull('email_verified_at');
                }
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 25);
            $users = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'admin_users' => User::where('role', 'administrator')->count(),
                'user_role' => User::where('role', 'user')->count(),
                'pending_users' => User::whereNull('email_verified_at')->count(),
                'recent_users' => User::where('created_at', '>=', now()->subDays(30))->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:100',
                'position' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:500',
                'role' => 'required|in:administrator,user',
                'password' => 'required|string|min:8|confirmed',
                'status' => 'required|in:active,inactive,pending',
                'send_welcome_email' => 'boolean',
                'force_password_change' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'department' => $request->department,
                    'position' => $request->position,
                    'address' => $request->address,
                    'role' => $request->role,
                    'password' => Hash::make($request->password),
                    'is_active' => $request->status === 'active',
                    'email_verified_at' => $request->status !== 'pending' ? now() : null,
                    'theme_preference' => 'light',
                    'force_password_change' => $request->force_password_change ?? false
                ]);

                // TODO: Send welcome email if requested
                if ($request->send_welcome_email) {
                    // Implement email sending logic here
                }

                // TODO: Log user creation activity
            });

            return response()->json([
                'success' => true,
                'message' => 'User created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single user details
     */
    public function show($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:100',
                'position' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:500',
                'role' => 'required|in:administrator,user',
                'password' => 'nullable|string|min:8|confirmed',
                'status' => 'required|in:active,inactive,pending',
                'theme_preference' => 'nullable|in:light,dark,system'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $request->department,
                'position' => $request->position,
                'address' => $request->address,
                'role' => $request->role,
                'is_active' => $request->status === 'active',
                'theme_preference' => $request->theme_preference ?? $user->theme_preference
            ];

            // Update email verification status
            if ($request->status !== 'pending' && !$user->email_verified_at) {
                $updateData['email_verified_at'] = now();
            } elseif ($request->status === 'pending') {
                $updateData['email_verified_at'] = null;
            }

            // Update password if provided
            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
                $updateData['force_password_change'] = true;
            }

            $user->update($updateData);

            // TODO: Log user update activity

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive,suspended'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update([
                'is_active' => $request->status === 'active'
            ]);

            // TODO: Log status change activity

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deletion of own account
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 422);
            }

            $user->delete();

            // TODO: Log user deletion activity

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions (delete, activate, deactivate)
     */
    public function bulkAction(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:delete,activate,deactivate',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::transaction(function () use ($request) {
                $userIds = $request->user_ids;
                $currentUserId = auth()->id();

                // Remove current user from the list if present
                $userIds = array_filter($userIds, function ($id) use ($currentUserId) {
                    return $id != $currentUserId;
                });

                switch ($request->action) {
                    case 'delete':
                        User::whereIn('id', $userIds)->delete();
                        break;

                    case 'activate':
                        User::whereIn('id', $userIds)->update(['is_active' => true]);
                        break;

                    case 'deactivate':
                        User::whereIn('id', $userIds)->update(['is_active' => false]);
                        break;
                }

                // TODO: Log bulk action activity
            });

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users to Excel
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'role', 'status']);
            
            $fileName = 'users-export-' . date('Y-m-d-H-i-s') . '.xlsx';
            $filePath = 'exports/' . $fileName;

            Excel::store(new UsersExport($filters), $filePath);

            return response()->json([
                'success' => true,
                'data' => [
                    'download_url' => storage_path('app/' . $filePath),
                    'file_name' => $fileName
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export users: ' . $e->getMessage()
            ], 500);
        }
    }
}