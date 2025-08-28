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
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                'email' => 'required|email|unique:users,email',
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'phone_number' => 'nullable|string|max:20',
                'department_id' => 'required|exists:company_department,department_code',
                'role_id' => 'required|exists:roles,id',
                'is_staff' => 'nullable|boolean',
                'send_welcome_email' => 'nullable|boolean',
                'require_password_change' => 'nullable|boolean',
            ], [
                'username.required' => 'Username is required',
                'username.alpha_num' => 'Username must contain only letters and numbers',
                'username.min' => 'Username must be at least 3 characters',
                'username.max' => 'Username cannot exceed 20 characters',
                'username.unique' => 'This username is already taken',
                'email.required' => 'Email address is required',
                'email.email' => 'Please enter a valid email address',
                'email.unique' => 'This email address is already registered',
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'department_id.required' => 'Please select a department',
                'department_id.exists' => 'Selected department does not exist',
                'role_id.required' => 'Please select a role',
                'role_id.exists' => 'Selected role does not exist',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = [
                'name' => Str::title($request->first_name . ' ' . $request->last_name),
                'user_name' => Str::lower($request->username),
                'email' => Str::lower($request->email),
                'first_name' => Str::title($request->first_name),
                'last_name' => Str::title($request->last_name),
                'phone_number' => $request->phone_number,
                'department_id' => $request->department_id,
                'role_id' => $request->role_id,
                'is_staff' => $request->boolean('is_staff', true),
                'status' => 'A'
            ];

            $result = $this->userService->createUser(
                $userData,
                $request->boolean('send_welcome_email', false),
                $request->boolean('require_password_change', true)
            );

            if (!$result['success']) {
                throw new \Exception($result['message'] ?? 'User creation failed');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully. A temporary password has been generated.',
                // 'user' => $result['user']
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
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
            ->addColumn('username', function ($user) {
                return $user->user_name ?? null;
            })
            ->addColumn('full_name', function ($user) {
                return $user->name ?? $user->first_name . ' ' . $user->last_name;
            })
            ->addColumn('department_name', function ($user) {
                return 'N/A';
            })
            ->addColumn('role_name', function ($user) {
                $roles = $user->roles->pluck('name')->toArray();
                return !empty($roles) ? implode(', ', $roles) : 'No Role';
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
            ->addColumn('is_employee', function ($fn) {
                return '<span class="badge rounded-pill bg-danger-transparent">No</span>';
            })
            ->addColumn('action', function ($user) use ($auth) {
                $isAdmin = false;
                if ($auth && $auth->role) {
                    $isAdmin = in_array((int) $auth->role->permission_level, [
                        PermissionsLevel::SUPERADMIN,
                        PermissionsLevel::ADMIN
                    ]);
                }

                $actionButtons = '';
                $userId = (int) $user->id;
                $userEmail = e($user->email);

                $actionButtons .= '<div class="btn-group my-0 user-list-btns me-2">
                    <button type="button" class="btn btn-icon btn-sm btn-light p-0" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="changeStatus(' . $userId . ')">Change Status</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="resetPassword(' . $userId . ')">Reset Password</a></li>
                        <li><a class="dropdown-item user-assign-role" href="javascript:void(0);" data-user-id="' . $userId . '">Assign Role</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="changeDepartment(' . $userId . ')">Change Department</a></li>
                    </ul>
                </div>';

                if ($isAdmin) {
                    $actionButtons .= '<button class="btn btn-info btn-sm me-1 edit-user"
                                      data-user-id="' . $userId . '"
                                      title="Edit User">
                                      <i class="bx bx-edit"></i>
                                   </button>';

                    $actionButtons .= '<button class="btn btn-danger btn-sm remove-user"
                                      data-email="' . $userEmail . '"
                                      data-user-id="' . $userId . '"
                                      title="Delete User">
                                      <i class="bx bx-trash"></i>
                                   </button>';
                } else {
                    $actionButtons .= '<button class="btn btn-secondary btn-sm me-1"
                                      title="View User" disabled>
                                      <i class="bx bx-show"></i>
                                   </button>';
                }

                return $actionButtons;
            })
            ->filterColumn('full_name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(first_name,' ',last_name) like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('role_name', function ($query, $keyword) {
                $query->whereHas('roles', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['action', 'status', 'last_login', 'is_employee', 'role'])
            ->make(true);
    }

    public function destroy(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid input data',
                    'errors' => $validator->errors()
                ], 422);
            }

            $currentUser = User::with('roles')->find(auth()->id());

            if (!$currentUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current user not found'
                ], 404);
            }

            $hasPermission = false;

            // logger()->info(['ss' => $currentUser->hasRole(['admin', 'super_admin'])]);

            // if ($currentUser->hasRole(['admin', 'super_admin'])) {
            //     $hasPermission = true;
            // }

            // if (!$hasPermission) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'You do not have permission to delete users'
            //     ], 403);
            // }

            $userToDelete = User::with('roles')->where('email', $request->email)->first();

            if (!$userToDelete) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            if ($currentUser->id === $userToDelete->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete your own account'
                ], 403);
            }

            $targetUserRoles = $userToDelete->roles->pluck('name')->toArray();
            $currentUserRoles = $currentUser->roles->pluck('name')->toArray();

            if (in_array('super_admin', $targetUserRoles) && !in_array('super_admin', $currentUserRoles)) {
                logger()->warning('Non-super-admin tried to delete super admin', [
                    'current_user' => $currentUser->id,
                    'target_user' => $userToDelete->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete super admin accounts'
                ], 403);
            }

            if ($userToDelete->user_name === 'super_admin' || $userToDelete->user_name === 'pknuek') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the primary super admin account'
                ], 403);
            }

            DB::beginTransaction();

            try {
                $deletionStats = $this->cleanupUserData($userToDelete);

                $this->detachUserRoles($userToDelete);

                $this->deleteUser($userToDelete);


                DB::commit();

                return $this->successResponse('User deleted successfully', $deletionStats);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    private function cleanupUserData(User $userToDelete): array
    {
        $deletionStats = [];

        $deletionStats['activity_logs_deleted'] = DB::table('user_activity_logs')
            ->where('user_id', $userToDelete->id)
            ->delete();

        $deletionStats['notifications_deleted'] = DB::table('notifications')
            ->where('created_by', $userToDelete->id)
            ->delete();

        $deletionStats['notification_reads_deleted'] = DB::table('approval_notification_read')
            ->where('user_id', $userToDelete->id)
            ->delete();

        $deletionStats['approval_notifications_deleted'] = DB::table('approval_notification_user')
            ->where('user_id', $userToDelete->id)
            ->delete();

        return $deletionStats;
    }

    private function detachUserRoles(User $userToDelete): void
    {
        if ($userToDelete->roles()->exists()) {
            $userToDelete->roles()->detach();
        }
    }

    private function deleteUser(User $userToDelete): void
    {
        $deleted = $userToDelete->forceDelete();

        if (!$deleted) {
            throw new \Exception('Failed to delete user from database');
        }
    }

    private function successResponse(string $message, array $data = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    public function edit($id): JsonResponse
    {
        try {
            $user = User::with(['department', 'roles'])->findOrFail($id);

            $userData = [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone_number' => $user->phone_number,
                'department_id' => $user->department_id,
                'is_staff' => $user->is_staff,
                'status' => $user->status,
                'role_id' => $user->roles->first()?->id
            ];

            return response()->json($userData);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }
}
