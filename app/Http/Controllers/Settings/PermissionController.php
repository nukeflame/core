<?php

namespace App\Http\Controllers\Settings;

use Nukeflame\Webmatics\Enums\SystemActionEnums;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PHPUnit\Event\Telemetry\System;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PermissionController extends Controller
{
    public function index()
    {
        $userPermissionLevel = auth()->user()->role->permission_level;
        $roles = Role::whereNot('name', '--')
            ->where('permission_level', '<=', $userPermissionLevel)
            ->where('is_active', true)
            ->whereNot('slug', 'super_admin')
            ->orderBy('permission_level', 'desc')
            ->get();


        return view('permissions.permissions_info', compact('roles'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => 'required|unique:permissions',
            ]);

            Permission::create(['name' => Str::lower($request->name)]);
            foreach ($request->roles as $role) {
                $role = Role::where('name', $role)->first();
                $role->givePermissionTo(Str::lower($request->name));
            }

            DB::commit();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], 422);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save'
            ]);
        }
    }

    public function getPermissionsByCategory($category)
    {
        try {
            $permissions = [];

            switch ($category) {
                case 'system_maintenance':
                    break;
                case 'user_backup':
                    break;
                case 'cover_registration':
                    $permissions = Permission::where('name', 'like', 'cover.%')
                        ->where('status', 'A')
                        ->get(['id', 'name']);
                    break;
                case 'gl_batch_process':
                    break;
                case 'claim_registration':
                    $permissions = Permission::where('name', 'like', 'claims.%')
                        ->where('status', 'A')
                        ->get(['id', 'name']);
                    break;

                case SystemActionEnums::CLAIM_INTIMATION_PROCESS:
                    $permissions = Permission::where('name', 'like', 'claims.%')
                        ->where('status', 'A')
                        ->get(['id', 'name']);
                    break;

                case 'user_management':
                    break;

                default:
                    $permissions = [];
                    break;
            }

            return response()->json([
                'status' => 'success',
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function permissions_datatable(Request $request)
    {
        $permissions = Permission::query()
            ->orderBy('created_at', 'desc')
            ->withCount('roles')
            ->with(['roles' => function ($query) {
                $query->whereNot('slug', 'super_admin');
            }]);

        return DataTables::of($permissions)
            ->addColumn('roles_count', function ($data) {
                return $data->roles_count;
            })
            ->addColumn('roles', function ($data) {
                return $data->roles;
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d M Y H:i');
            })
            ->addColumn('status', function ($data) {
                return $data->status === 'A' ?
                    "<span class='badge bg-success'>Supported</span>" :
                    "<span class='badge bg-warning'>Non-applicable <i class='bx bx-error text-warning'></i></span>";
            })
            ->addColumn('action', function ($data) {
                $btn = "";
                $btn .= "<button class='btn btn-outline-primary btn-sm edit-btn' data-id='{$data->id}'>Edit</button>";
                $btn .= " <button class='btn btn-outline-danger btn-sm delete-btn' data-id='{$data->id}'>Remove</button>";

                return $btn;
            })
            ->filter(function ($query) {
                if (request()->has('status_filter') && request('status_filter') != '') {
                    $statusFilter = request('status_filter');
                    $query->where('status', $statusFilter);
                }
            })
            ->rawColumns(['action', 'roles', 'created_at', 'status', 'roles_count'])
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

            $admin = Role::where('slug', 'super_admin')->first();
            if ($admin) {
                $roleIds = array_unique(array_merge([$admin->id], $roleIds));
            }

            DB::beginTransaction();

            foreach ($roleIds as $roleId) {
                $role = Role::find($roleId);
                if (!$role) {
                    continue;
                }

                $role->syncPermissions($permissionIds);

                foreach ($role->users as $user) {
                    $user->forgetCachedPermissions();
                }
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
                'message' => 'Failed to assign permissions. Please try again.',
            ], 500);
        }
    }
}
