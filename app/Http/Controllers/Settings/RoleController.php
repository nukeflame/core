<?php

namespace App\Http\Controllers\Settings;

use Nukeflame\Webmatics\Enums\PermissionsLevel;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        $userPermissionLevel = auth()->user()->role?->permission_level ?? 0;
        $roles = Role::where('is_active', true)
            ->where('slug', '!=', 'super_admin')
            ->where('name', '!=', '--')
            ->with(['permissions' => function ($query) {
                $query->select('id', 'name');
            }])
            ->when($userPermissionLevel > 0, function ($query) use ($userPermissionLevel) {
                return $query->where('permission_level', '<=', $userPermissionLevel);
            })
            ->get();

        $permissions = DB::table('permissions')
            ->select('permissions.*', DB::raw('COUNT(role_has_permissions.role_id) as roles_count'))
            ->leftJoin('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->groupBy('permissions.id')
            ->get();

        $departments = Department::where('status', 'A')->get();
        $permissionLevels = [
            'GUEST' => 1,
            'VIEWER' => 2,
            'EDITOR' => 3,
            'MODERATOR' => 4,
        ];

        if ((int) $userPermissionLevel >= PermissionsLevel::ADMIN) {
            $permissionLevels['ADMIN'] = 5;
        }

        return view('roles.roles_info', compact('roles', 'permissions', 'departments', 'permissionLevels'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:roles,name',
            'description' => 'required|string',
            'role_launch_stage' => 'required|in:general_availability,disabled',
            'department_ids' => 'nullable|array|exists:company_department,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {

            Role::create([
                'name' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'guard_name' => 'web',
                'permission_level' => $request->permission_level,
                'is_active' => $request->role_launch_stage === 'general_availability',
            ]);

            if ($request->has('department_ids')) {
                // $role->departments()->attach($request->department_ids);
            }

            DB::commit();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Role created successfully!',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);
        $role = Role::findOrFail($id);
        $role->update(['name' => Str::lower($request->name)]);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    public function roles_datatable(Request $request)
    {
        $userPermissionLevel = auth()->user()->role?->permission_level ?? 0;
        $roles = Role::where('is_active', true)
            ->withCount(['permissions as permission_count'])
            ->where('slug', '!=', 'super_admin')
            ->where('name', '!=', '--')
            ->when($userPermissionLevel > 0, function ($query) use ($userPermissionLevel) {
                return $query->where('permission_level', '<=', $userPermissionLevel);
            })
            ->with(['permissions' => function ($query) {
                $query->select('id', 'name');
            }, 'departments' => function ($query) {
                $query->select('company_department.id', 'company_department.department_code', 'company_department.department_name');
            }])
            ->get()
            ->map(function ($role) {
                $role->name = Str::title(str_replace('_', ' ', $role->name));
                return $role;
            });

        return DataTables::of($roles)
            ->addColumn('departments', function ($fn) {
                return $fn->departments->map(function ($department) {
                    return [
                        'id' => $department->id,
                        'department_code' => $department->department_code,
                        'department_name' => $department->department_name,
                    ];
                })->values()->toJson();
            })
            ->addColumn('status', function ($data) {
                return $data->is_active ? "<span class=''>Supported</span>" : "<span class=''>Non-applicable <i class='bx bx-error text-warning'></i></span>";
            })
            ->addColumn('permissions', function ($data) {
                return $data->permissions;
            })
            ->addColumn('created_at', function ($data) {
                return Carbon::parse($data->created_at)->format('M j, Y H:i');
            })
            ->addColumn('updated_at', function ($data) {
                return Carbon::parse($data->updated_at)->format('D, M j, Y H:i');
            })
            ->addColumn('action', function ($data) {
                $btn = "";
                $btn .= "<button class='btn btn-light btn-actions btn-sm me-2 view-role-departments' data-id='{$data->id}'><i class='bi bi-eye'></i> View Departments</button>";
                $btn .= "<button class='btn btn-light btn-actions btn-sm me-2 edit-role' data-id='{$data->id}'><i class='bx bx-pencil'></i> Edit</button>";
                if ($data->slug != 'super_admin') {
                    $btn .= " <button class='btn btn-outline-danger btn-actions btn-sm remove-role' data-id='{$data->id}'><i class='bx bx-trash'></i> Remove</button>";
                }

                return $btn;
            })
            ->rawColumns(['action', 'permissions', 'status'])
            ->make(true);
    }

    public function assignPermissionRole(Request $request)
    {
        try {
            $request->validate([
                'permission_ids' => 'nullable|array',
                'role_ids' => 'nullable|array',
            ]);

            $permissionIds = [];
            if ($request->has('permission_ids') && !empty($request->input('permission_ids')[0])) {
                $permissionIds = explode(',', $request->input('permission_ids')[0]);
            }

            $roleIds = $request->input('role_ids', []);

            if (empty($roleIds)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No roles selected for permission assignment.',
                ], 400);
            }

            $adminRole = Role::where('slug', 'super_admin')->first();
            if ($adminRole && !in_array($adminRole->id, $roleIds)) {
                $roleIds[] = $adminRole->id;
            }

            DB::beginTransaction();
            foreach ($roleIds as $roleId) {
                $role = Role::find($roleId);
                if (!$role) {
                    continue;
                }

                $currentPermissionIds = $role->permissions()->pluck('id')->toArray();

                $permissionsToAttach = array_diff($permissionIds, $currentPermissionIds);
                if (!empty($permissionsToAttach)) {
                    $role->permissions()->attach($permissionsToAttach);
                }

                $permissionsToDetach = array_diff($currentPermissionIds, $permissionIds);
                if (!empty($permissionsToDetach)) {
                    $role->permissions()->detach($permissionsToDetach);
                }

                $role->users->each(function ($user) {
                    $user->forgetCachedPermissions();
                });
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Permissions assigned to roles successfully!',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to assign permissions: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Invalid role data.',
            ], 422);
        }

        try {
            $role = Role::with(['users'])->findOrFail((int) $request->role_id);

            if ($role->slug === 'super_admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Super admin role cannot be deleted.',
                ], 403);
            }

            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role with assigned users.',
                ], 422);
            }

            DB::beginTransaction();
            $role->permissions()->detach();
            $role->departments()->detach();
            $role->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.',
            ]);
        } catch (ModelNotFoundException $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
            ], 404);
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage(),
            ], 500);
        }
    }
}
