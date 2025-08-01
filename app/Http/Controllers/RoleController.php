<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('permission:manage-roles');
    }

    /**
     * Display a listing of roles
     */
    public function index() {}

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = Permission::all();

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles',
            'description' => 'nullable|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        $role->permissions()->attach($request->permissions);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'role_created',
            'ip_address' => $request->ip(),
            'description' => 'Created role ' . $role->name,
        ]);

        if ($request->has('continue')) {
            return redirect()->route('roles.create')
                ->with('success', 'Role created successfully.');
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->permissions);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'role_updated',
            'ip_address' => $request->ip(),
            'description' => 'Updated role ' . $role->name,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Toggle role active status
     */
    public function toggleStatus(Request $request, Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()
                ->with('error', 'System roles cannot be deactivated.');
        }

        $role->is_active = !$role->is_active;
        $role->save();

        $action = $role->is_active ? 'activated' : 'deactivated';

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'role_' . $action,
            'ip_address' => $request->ip(),
            'description' => ucfirst($action) . ' role ' . $role->name,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role ' . $action . ' successfully.');
    }

    /**
     * Remove the specified role (soft delete)
     */
    public function destroy(Request $request, Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()
                ->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => 'role_deleted',
            'ip_address' => $request->ip(),
            'description' => 'Deleted role ' . $role->name,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
