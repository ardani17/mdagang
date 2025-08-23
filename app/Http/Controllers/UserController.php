<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        // Filter by department
        if ($request->has('department') && $request->department !== '') {
            $query->where('department', $request->department);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created user
     * Only administrators can create users
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:administrator,user',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
                'department' => $request->department,
                'position' => $request->position,
                'is_active' => $request->is_active ?? true,
            ]);

            ActivityLog::logCreate($user, 'User created by administrator');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'department' => $user->department,
                    'position' => $user->position,
                    'is_active' => $user->is_active,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'department' => $user->department,
                'position' => $user->position,
                'initials' => $user->initials,
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:administrator,user',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $user->toArray();
            
            $updateData = $request->only(['name', 'email', 'role', 'phone', 'department', 'position', 'is_active']);
            
            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Log changes
            $changes = [];
            foreach ($updateData as $key => $value) {
                if ($key !== 'password' && isset($oldData[$key]) && $oldData[$key] != $value) {
                    $changes[$key] = [
                        'old' => $oldData[$key],
                        'new' => $value,
                    ];
                }
            }

            if (!empty($changes)) {
                ActivityLog::logUpdate($user, $changes, 'User updated by administrator');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'department' => $user->department,
                    'position' => $user->position,
                    'is_active' => $user->is_active,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting the last administrator
        if ($user->role === 'administrator') {
            $adminCount = User::where('role', 'administrator')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus administrator terakhir',
                ], 400);
            }
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri',
            ], 400);
        }

        DB::beginTransaction();
        try {
            ActivityLog::logDelete($user, 'User deleted by administrator');
            
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Activate a user
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'User sudah aktif',
            ], 400);
        }

        $user->update(['is_active' => true]);

        ActivityLog::log('activate_user', $user, null, 'User activated by administrator');

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diaktifkan',
        ]);
    }

    /**
     * Deactivate a user
     */
    public function deactivate($id)
    {
        $user = User::findOrFail($id);

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'User sudah nonaktif',
            ], 400);
        }

        // Prevent deactivating self
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menonaktifkan akun sendiri',
            ], 400);
        }

        // Prevent deactivating the last administrator
        if ($user->role === 'administrator') {
            $activeAdminCount = User::where('role', 'administrator')
                ->where('is_active', true)
                ->count();
            if ($activeAdminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menonaktifkan administrator terakhir',
                ], 400);
            }
        }

        $user->update(['is_active' => false]);

        // Revoke all tokens
        $user->tokens()->delete();

        ActivityLog::log('deactivate_user', $user, null, 'User deactivated by administrator');

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dinonaktifkan',
        ]);
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke all tokens to force re-login
        $user->tokens()->delete();

        ActivityLog::log('reset_password', $user, null, 'Password reset by administrator');

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset',
        ]);
    }

    /**
     * Get user statistics
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'administrators' => User::where('role', 'administrator')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'users_by_department' => User::select('department', DB::raw('count(*) as count'))
                ->whereNotNull('department')
                ->groupBy('department')
                ->get(),
            'recent_logins' => User::whereNotNull('last_login_at')
                ->orderBy('last_login_at', 'desc')
                ->take(10)
                ->get(['id', 'name', 'email', 'last_login_at']),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}