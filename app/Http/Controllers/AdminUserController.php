<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = User::with(['roles', 'department'])->latest();

        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter if provided
        if ($request->has('role') && $request->role !== 'All Roles') {
            $query->role($request->role);
        }

        $users = $query->paginate(10);

        $totalUsers = User::count();
        $adminUsers = User::role('admin')->count();
        $portWorkers = User::role('port_worker')->count();
        $roles = Role::all();
        $departments = Department::where('is_active', true)->get();

        return view('admin.users', compact(
            'users',
            'totalUsers',
            'adminUsers',
            'portWorkers',
            'roles',
            'departments'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:12',
                'max:32',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,32}$/',
                'confirmed'
            ],
            'role' => 'required|exists:roles,name',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $departmentId = $validated['department_id'] ?? null;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'department_id' => $departmentId,
            'email_verified_at' => now(), // Auto-verify emails for admin-created users
        ]);

        // Assign role with appropriate guard based on role type
        $guardName = $this->getGuardForRole($validated['role']);
        $this->assignRoleWithGuard($user, $validated['role'], $guardName);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    /**
     * Get the appropriate guard name for a given role
     */
    private function getGuardForRole($roleName)
    {
        $guardMapping = [
            'admin' => 'web',           // Admin uses web guard but has admin role
            'ucua_officer' => 'web',    // UCUA Officer uses web guard (FIXED)
            'user' => 'web',            // Regular users use web guard
            'port_worker' => 'web',     // Port workers use web guard
            'department_head' => 'web', // Department heads use web guard
        ];

        return $guardMapping[$roleName] ?? 'web'; // Default to web guard
    }

    /**
     * Assign role to user with specific guard
     */
    private function assignRoleWithGuard($user, $roleName, $guardName)
    {
        $role = \Spatie\Permission\Models\Role::where('name', $roleName)
                    ->where('guard_name', $guardName)
                    ->first();

        if ($role) {
            $user->roles()->attach($role->id);
        } else {
            // Fallback to default assignment if role with specific guard doesn't exist
            $user->assignRole($roleName);
        }
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $departmentId = $validated['department_id'] ?? null;

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $departmentId,
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting the last admin
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return redirect()->back()->with('error', 'Cannot delete the last admin user.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }
} 
