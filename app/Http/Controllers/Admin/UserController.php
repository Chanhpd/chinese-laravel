<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminLog;
use App\Models\UserTopicProgress;
use App\Models\SavedVocabulary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get all users with pagination and filters.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate($request->get('per_page', 20));

        // Return JSON for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'status' => $user->status,
                        'blocked_at' => $user->blocked_at,
                        'last_login_at' => $user->last_login_at,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ];
                }),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ]);
        }

        // Return view for web requests
        return view('admin.users.index', compact('users'));
    }

    /**
     * Get a specific user with details.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Get user statistics
        $stats = [
            'topics_started' => UserTopicProgress::where('user_id', $id)->count(),
            'words_learned' => UserTopicProgress::where('user_id', $id)->sum('completed_words'),
            'saved_vocabularies' => SavedVocabulary::where('user_id', $id)->count(),
            'mastery_breakdown' => [
                'beginner' => UserTopicProgress::where('user_id', $id)->where('mastery_level', 'beginner')->count(),
                'intermediate' => UserTopicProgress::where('user_id', $id)->where('mastery_level', 'intermediate')->count(),
                'advanced' => UserTopicProgress::where('user_id', $id)->where('mastery_level', 'advanced')->count(),
                'mastered' => UserTopicProgress::where('user_id', $id)->where('mastery_level', 'mastered')->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'blocked_at' => $user->blocked_at,
                    'last_login_at' => $user->last_login_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'statistics' => $stats,
            ],
        ]);
    }

    /**
     * Create a new user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['user', 'admin', 'super_admin'])],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'blocked'])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status ?? 'active',
        ]);

        // Log admin action
        AdminLog::log(
            'create_user',
            "Created user: {$user->email}",
            'User',
            $user->id,
            null,
            $user->only(['name', 'email', 'role', 'status'])
        );

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    /**
     * Update user information.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldValues = $user->only(['name', 'email', 'role', 'status']);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:8',
            'role' => ['sometimes', 'required', Rule::in(['user', 'admin', 'super_admin'])],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive', 'blocked'])],
        ]);

        $data = $request->only(['name', 'email', 'role', 'status']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Log admin action
        AdminLog::log(
            'update_user',
            "Updated user: {$user->email}",
            'User',
            $user->id,
            $oldValues,
            $user->only(['name', 'email', 'role', 'status'])
        );

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    /**
     * Delete a user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting super admin
        if ($user->role === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete super admin',
            ], 403);
        }

        $userEmail = $user->email;
        $user->delete();

        // Log admin action
        AdminLog::log(
            'delete_user',
            "Deleted user: {$userEmail}",
            'User',
            $id
        );

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Change user role.
     */
    public function changeRole(Request $request, $id)
    {
        $request->validate([
            'role' => ['required', Rule::in(['user', 'admin', 'super_admin'])],
        ]);

        $user = User::findOrFail($id);
        $oldRole = $user->role;

        // Only super admin can create/modify super admin
        if ($request->role === 'super_admin' && !auth()->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only super admin can assign super admin role',
            ], 403);
        }

        $user->update(['role' => $request->role]);

        // Log admin action
        AdminLog::log(
            'change_role',
            "Changed role from {$oldRole} to {$request->role} for user: {$user->email}",
            'User',
            $user->id,
            ['role' => $oldRole],
            ['role' => $request->role]
        );

        return response()->json([
            'success' => true,
            'message' => 'User role changed successfully',
            'data' => $user,
        ]);
    }

    /**
     * Block a user.
     */
    public function block($id)
    {
        $user = User::findOrFail($id);

        // Cannot block super admin
        if ($user->role === 'super_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot block super admin',
            ], 403);
        }

        $user->block();

        // Log admin action
        AdminLog::log(
            'block_user',
            "Blocked user: {$user->email}",
            'User',
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'User blocked successfully',
            'data' => $user,
        ]);
    }

    /**
     * Unblock a user.
     */
    public function unblock($id)
    {
        $user = User::findOrFail($id);
        $user->unblock();

        // Log admin action
        AdminLog::log(
            'unblock_user',
            "Unblocked user: {$user->email}",
            'User',
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'User unblocked successfully',
            'data' => $user,
        ]);
    }

    /**
     * Get user's learning progress.
     */
    public function progress($id)
    {
        $user = User::findOrFail($id);

        $progress = UserTopicProgress::where('user_id', $id)
            ->with('topic')
            ->orderBy('last_studied_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    /**
     * Get user's saved vocabularies.
     */
    public function savedVocabularies($id)
    {
        $user = User::findOrFail($id);

        $saved = SavedVocabulary::where('user_id', $id)
            ->with('vocabulary.topic')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $saved,
        ]);
    }
}
