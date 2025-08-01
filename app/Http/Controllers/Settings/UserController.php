<?php

namespace App\Http\Controllers\Settings;

use App\Enums\PermissionsLevel;
use App\Http\Controllers\Controller;
use App\Models\AllowedEmailDomain;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $departments = Department::where('status', 'A')->whereNot('department_name', '--')->get();
        // $roles = Role::whereNot('name', '--')
        //     ->with('departments')
        //     ->where('permission_level', '<=', $userPermissionLevel)
        //     ->where('is_active', true)
        //     ->orderBy('permission_level', 'desc')
        //     ->whereHas('departments', function ($query) use ($request) {
        //         $query->where('department_code', $request->department_id);
        //     })->get();
        // $auth = User::where('id', auth()->id)->departments();
        $roles = [];

        // logger($auth);


        return view('users.users_info', [
            'departments' => $departments,
            'roles' => []
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|alpha_num|min:3|max:20|unique:users,user_name',
                'email' => 'required|email|unique:users',
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'phone_number' => 'nullable|string|max:20',
                'department_id' => 'required|exists:company_department,department_code',
                'role_id' => 'required|exists:roles,id',
            ], [
                'username.unique' => 'The username is already taken',
                'department_id.required' => 'The department is required',
                'role_id.required' => 'The role is required',
                'username.unique' => 'The username is already taken',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'name' => Str::ucfirst($request->first_name . ' ' . $request->last_name),
                'user_name' => Str::lower($request->username),
                'email' => Str::lower($request->email),
                'first_name' => Str::ucfirst($request->first_name),
                'last_name' => Str::ucfirst($request->last_name),
                'phone_number' => $request->phone_number,
                'department_id' => $request->department_id,
                'role_id' => $request->role_id,
                'role_id' => false,
                'status' => 'A',
            ];

            $this->userService->createUser($data, $request->has('send_welcome_email'), $request->has('require_password_change'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully. A temporary password has been generated.'
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            logger()->info($e);
        }
    }

    public function checkUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $exists = User::where('user_name', $request->username)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function checkEmailDomain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if (AllowedEmailDomain::count() === 0) {
            return response()->json(['allowed' => true]);
        }

        $allowed = AllowedEmailDomain::where('domain', $request->domain)
            ->orWhere('domain', '*')
            ->exists();

        return response()->json(['allowed' => $allowed]);
    }

    public function getRolesByDepartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|string|exists:company_department,department_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $userPermissionLevel = auth()->user()->role->permission_level;
        $roles = Role::whereNot('name', '--')
            ->with('departments')
            ->where('permission_level', '<=', $userPermissionLevel)
            ->where('is_active', true)
            ->orderBy('permission_level', 'desc')
            ->whereHas('departments', function ($query) use ($request) {
                $query->where('department_code', $request->department_id);
            })->get();

        return response()->json($roles);
    }

    public function getUserData()
    {
        $user = User::select(['id', 'user_name', 'name', 'email', 'status', 'role_id', 'department_id', 'last_login', 'phone_number'])
            ->where('is_active', true)
            ->where('user_name', '!=', 'super_admin')
            ->orderBy('id', 'asc');

        $auth = User::where('id', auth()->user()->id)->first();
        return DataTables::of($user)
            ->addColumn('department', function ($fn) {
                return '-';
            })
            ->addColumn('status', function ($fn) {
                if ($fn->status === 'A') {
                    return '<span class="badge bg-success me-1">Active</span>';
                } else {
                    return '<span class="badge bg-danger me-1">Inactive</span>';
                }
            })
            ->addColumn('last_login', function ($fn) {
                return $fn->last_login ? Carbon::parse($fn->last_login)->format('d M Y H:i:s') : '-';
            })
            ->editColumn('role', function ($fn) {
                $roles = $fn->roles;

                if ($roles && $roles->count() > 0) {
                    return '<span class="badge bg-dark-transparent">' . $roles->pluck('name')->implode(', ') . '</span>';
                } else {
                    return '-';
                }
            })
            ->addColumn('is_employee', function ($fn) {
                return '<span class="badge rounded-pill bg-danger-transparent">No</span>';
            })
            ->addColumn('action', function ($fn) use ($auth) {
                $isAdmin = in_array((int) $auth->role->permission_level, [
                    PermissionsLevel::SUPERADMIN,
                    PermissionsLevel::ADMIN
                ]);

                $actionButtons = '';
                $id = (int) $fn->id;
                $actionButtons .= '<div class="btn-group my-0 user-list-btns me-2">
                    <button type="button" class="btn btn-icon btn-sm btn-light p-0" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="changeStatus(' . $id . ')">Change Status</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="resetPassword(' . $id . ')">Reset Password</a></li>
                        <li><a class="dropdown-item user-assign-role" href="javascript:void(0);">Assign Role</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="changeDepartment(' . $id . ')">Change Department</a></li>
                    </ul>
                </div>';

                if ($isAdmin) {
                    $email = e($fn->email);
                    $actionButtons .= '<button class="btn btn-light btn-sm me-2 edit-user"><i class="bx bx-pencil"></i> Edit</button>';
                    $actionButtons .= '<button class="btn btn-outline-danger btn-sm me-2 remove-user"><i class="bx bx-trash"></i> Delete</button>';
                }


                return $actionButtons;
            })
            ->rawColumns(['action', 'status', 'last_login', 'is_employee', 'role'])
            ->make(true);
    }

    public function destroy(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $currentUser = User::find(auth()->id());

            if (!$currentUser->hasRole(['admin', 'super_admin'])) {
                //&& !$currentUser->can(['users.manage.view'])
                return response()->json(['message' => 'Unauthorized access'], 403);
            }

            $user = User::where(['email' => $request->email])->firstOrFail();

            if ($currentUser->id === $user->id) {
                return response()->json(['message' => 'Cannot delete your own admin account'], 403);
            }

            if ($user->hasRole('super_admin') && !$currentUser->hasRole('super_admin')) {
                return response()->json(['message' => 'Cannot delete super admin accounts'], 403);
            }

            // $userName = $user->name;
            $user->delete();

            return response()->json(['message' => 'User deleted successfully', 'success' => true], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user', 'error' => $e->getMessage()], 500);
        }
    }
}
