<?php

namespace App\Http\Controllers\Settings;

use App\Enums\PermissionsLevel;
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
            }])
            ->get()
            ->map(function ($role) {
                $role->name = Str::title(str_replace('_', ' ', $role->name));
                return $role;
            });

        return DataTables::of($roles)
            ->addColumn('departments', function ($fn) {
                return 30;
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
                $btn .= "<button class='btn btn-light btn-actions btn-sm me-2 edit-role' data-id='{$data->id}'><i class='bi bi-eye'></i> View Departments</button>";
                $btn .= "<button class='btn btn-light btn-actions btn-sm me-2 edit-role' data-id='{$data->id}'><i class='bx bx-pencil'></i> Edit</button>";
                if ($data->slug != 'super_admin') {
                    $btn .= " <button class='btn btn-outline-danger btn-actions btn-sm remove-role' data-id='{$data->id}'><i class='bx bx-trash'></i> Remove</button>";
                }

                return $btn;
            })
            ->rawColumns(['action', 'permissions', 'status'])
            ->make('true');
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
        // try {
        //     if (!auth()->check()) {
        //         return response()->json(['message' => 'Unauthenticated'], 401);
        //     }

        //     $currentUser = User::find(auth()->id());

        //     // if (!$currentUser->hasRole(['admin', 'super_admin']) && !$currentUser->can(['users.manage.view'])) {
        //     //     return response()->json(['message' => 'Unauthorized access'], 403);
        //     // }

        //     $user = User::where(['email' => $request->emeail])->firstOrFail();

        //     if ($currentUser->id === $user->id) {
        //         return response()->json(['message' => 'Cannot delete your own admin account'], 403);
        //     }

        //     if ($user->hasRole('super_admin') && !$currentUser->hasRole('super_admin')) {
        //         return response()->json(['message' => 'Cannot delete super admin accounts'], 403);
        //     }

        //     $userName = $user->name;
        //     $user->delete();

        //     return response()->json(['message' => 'User deleted successfully'], 200);
        // } catch (ModelNotFoundException $e) {
        //     return response()->json(['message' => 'User not found'], 404);
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'Failed to delete user', 'error' => $e->getMessage()], 500);
        // }
    }
}
