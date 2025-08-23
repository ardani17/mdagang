<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user
     * Note: Registration is disabled. Only administrators can create new users.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'sometimes|boolean',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact administrator.'],
            ]);
        }

        // Delete old tokens
        $user->tokens()->delete();

        // Create new token
        $tokenName = 'auth_token';
        if ($request->remember) {
            $token = $user->createToken($tokenName, ['*'], now()->addDays(30))->plainTextToken;
        } else {
            $token = $user->createToken($tokenName)->plainTextToken;
        }

        // Update last login
        $user->updateLastLogin();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'department' => $user->department,
                    'position' => $user->position,
                    'initials' => $user->initials,
                    'permissions' => $this->getUserPermissions($user),
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        
        // Delete current access token
        $request->user()->currentAccessToken()->delete();

        ActivityLog::logLogout($user);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        $user = $request->user();
        
        // Delete all tokens
        $request->user()->tokens()->delete();

        ActivityLog::log('logout_all', $user, null, 'User logged out from all devices');

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices successfully',
        ]);
    }

    /**
     * Get current user
     */
    public function user(Request $request)
    {
        $user = $request->user();

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
                'permissions' => $this->getUserPermissions($user),
            ],
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'department' => 'sometimes|string|max:100',
            'position' => 'sometimes|string|max:100',
        ]);

        $oldData = $user->only(['name', 'phone', 'department', 'position']);
        
        $user->update($request->only(['name', 'phone', 'department', 'position']));

        $changes = [];
        foreach ($oldData as $key => $oldValue) {
            if ($request->has($key) && $oldValue != $request->$key) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $request->$key,
                ];
            }
        }

        if (!empty($changes)) {
            ActivityLog::logUpdate($user, $changes, 'User updated profile');
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'department' => $user->department,
                'position' => $user->position,
            ],
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Delete all tokens except current
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        ActivityLog::log('change_password', $user, null, 'User changed password');

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();
        
        // Delete current token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Get user permissions
     */
    private function getUserPermissions(User $user): array
    {
        if ($user->isAdministrator()) {
            return [
                'dashboard' => ['view', 'export'],
                'users' => ['view', 'create', 'update', 'delete'],
                'customers' => ['view', 'create', 'update', 'delete'],
                'suppliers' => ['view', 'create', 'update', 'delete'],
                'raw_materials' => ['view', 'create', 'update', 'delete'],
                'products' => ['view', 'create', 'update', 'delete'],
                'recipes' => ['view', 'create', 'update', 'delete'],
                'production' => ['view', 'create', 'update', 'delete', 'approve'],
                'quality' => ['view', 'create', 'update', 'approve'],
                'purchase_orders' => ['view', 'create', 'update', 'delete', 'approve'],
                'orders' => ['view', 'create', 'update', 'delete', 'approve'],
                'invoices' => ['view', 'create', 'update', 'delete', 'send'],
                'payments' => ['view', 'create', 'update', 'delete'],
                'transactions' => ['view', 'create', 'update', 'delete'],
                'inventory' => ['view', 'update', 'adjust'],
                'reports' => ['view', 'export'],
                'settings' => ['view', 'update'],
            ];
        }

        // Regular user permissions
        return [
            'dashboard' => ['view'],
            'customers' => ['view', 'create', 'update'],
            'products' => ['view'],
            'recipes' => ['view'],
            'production' => ['view', 'create'],
            'quality' => ['view', 'create'],
            'purchase_orders' => ['view', 'create'],
            'orders' => ['view', 'create', 'update'],
            'invoices' => ['view', 'create'],
            'payments' => ['view'],
            'inventory' => ['view'],
            'reports' => ['view'],
        ];
    }
}