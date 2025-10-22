<?php

namespace App\Services;

use App\Jobs\SendWelcomeEmail;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use App\Models\UserActivityLog;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{

    protected PasswordService $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    public function createUser(array $data, bool $sendWelcomeEmail = false, bool $requirePasswordReset = true): array
    {
        return DB::transaction(function () use ($data, $sendWelcomeEmail, $requirePasswordReset) {
            try {
                $temporaryPassword = $this->generateSecureTemporaryPassword();

                $role = $this->validateAndGetRole($data);

                $department = $this->validateAndGetDepartment($data);

                $user = $this->createUserRecord($data, $temporaryPassword, $role, $requirePasswordReset);

                if ($role && $department) {
                    $this->assignRoleAndPermissions($user, $role, $department);
                } elseif ($role) {
                    $this->assignDefaultPermissions($user, $role);
                }

                $this->logUserCreation($user, $role);

                if ($sendWelcomeEmail) {
                    $this->dispatchWelcomeEmail($user, $temporaryPassword);
                }

                $userWithRelations = $this->getUserWithRelations($user->id);

                return $this->buildSuccessResponse($userWithRelations, $temporaryPassword, $role, $department);
            } catch (Exception $e) {
                throw $e;
            }
        });
    }

    protected function generateSecureTemporaryPassword(): string
    {
        $temporaryPassword = $this->passwordService->generateTemporaryPassword();

        if (empty($temporaryPassword)) {
            throw new Exception('Failed to generate temporary password');
        }

        return $temporaryPassword;
    }

    protected function validateAndGetRole(array $data): ?Role
    {
        $roleId = $data['role_id'] ?? null;

        if (!$roleId) {
            return null;
        }

        try {
            $role = Role::where('id', $roleId)
                ->where('is_active', true)
                ->firstOrFail();

            return $role;
        } catch (ModelNotFoundException $e) {
            throw new Exception("Active role with ID {$roleId} not found");
        }
    }

    protected function validateAndGetDepartment(array $data): ?Department
    {
        $departmentIdentifier = $data['department_id'] ?? null;

        if (!$departmentIdentifier) {
            return null;
        }

        if (is_numeric($departmentIdentifier)) {
            $department = Department::find($departmentIdentifier);
            if ($department) {
                return $department;
            }
        }

        $department = Department::where('department_code', $departmentIdentifier)->first();

        if (!$department) {
            throw new Exception("Department with identifier '{$departmentIdentifier}' not found");
        }

        return $department;
    }

    protected function createUserRecord(array $data, string $temporaryPassword, ?Role $role, bool $requirePasswordReset): User
    {
        $userData = $this->prepareUserData($data, $temporaryPassword, $role, $requirePasswordReset);

        $user = User::create($userData);

        if (!$user) {
            throw new Exception('Failed to create user in database');
        }

        return $user;
    }

    protected function prepareUserData(array $data, string $temporaryPassword, ?Role $role, bool $requirePasswordReset): array
    {
        $userCreationData = $data;
        unset($userCreationData['role_id'], $userCreationData['department_id']);

        return array_merge($userCreationData, [
            'password' => Hash::make($temporaryPassword),
            'is_active' => true,
            'is_staff' => $data['is_staff'] ?? false,
            'role_id' => $role?->id,
            'created_by' => auth()->id(),
            'requires_password_reset' => $requirePasswordReset,
            'password_expires_at' => $requirePasswordReset ? now()->addDay() : null,
        ]);
    }

    protected function assignRoleAndPermissions(User $user, Role $role, Department $department): void
    {
        try {
            $roleAssignment = DB::table('role_departments')
                ->where('role_id', $role->id)
                ->where('department_id', $department->id)
                ->first();

            if ($roleAssignment) {
                $this->syncUserPermissions($user, $role);
            } else {
                $this->assignDefaultPermissions($user, $role);
            }
        } catch (Exception $e) {
            throw new Exception('User created but role assignment failed: ' . $e->getMessage());
        }
    }

    protected function syncUserPermissions(User $user, Role $role): void
    {
        $permissions = $role->permissions;

        if ($permissions->isNotEmpty()) {
            if (method_exists($user, 'syncPermissions')) {
                $user->syncPermissions($permissions);
            } else {
                $this->manualSyncPermissions($user, $permissions);
            }
        } else {
            $this->assignDefaultPermissions($user, $role);
        }
    }

    protected function manualSyncPermissions(User $user, $permissions): void
    {
        DB::table('user_permissions')->where('user_id', $user->id)->delete();

        $userPermissions = $permissions->map(function ($permission) use ($user) {
            return [
                'user_id' => $user->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        DB::table('user_permissions')->insert($userPermissions->toArray());
    }

    protected function assignDefaultPermissions(User $user, ?Role $role = null): void
    {
        $defaultPermissions = $this->getDefaultPermissions();

        if ($defaultPermissions->isNotEmpty()) {
            if (method_exists($user, 'syncPermissions')) {
                $user->syncPermissions($defaultPermissions);
            } else {
                $this->manualSyncPermissions($user, $defaultPermissions);
            }
        }
    }

    protected function getDefaultPermissions()
    {
        return DB::table('permissions')
            ->whereIn('name', [
                'view_dashboard',
                'edit_profile',
                'change_password'
            ])
            ->get();
    }

    protected function logUserCreation(User $user, ?Role $role): void
    {
        try {
            $roleName = $role ? $role->name : 'No role assigned';
            $message = "Created user {$user->user_name} with role {$roleName}";

            $this->logActivity($user, 'user_created', $message);
        } catch (Exception $e) {
        }
    }

    protected function logActivity(User $user, string $action, string $message): void
    {
        // DB::table('user_activity_logs')->insert([
        //     'user_id' => $user->id,
        //     'action' => $action,
        //     'message' => $message,
        //     'activity_type' => $activityType,
        //     'description' => $description,
        //     'ip_address' => request()->ip(),
        //     'user_agent' => request()->userAgent(),
        //     'created_by' => auth()->id(),
        //     'created_at' => now(),

        // ]);
    }

    protected function dispatchWelcomeEmail(User $user, string $temporaryPassword): void
    {
        try {
            SendWelcomeEmail::dispatch($user, $temporaryPassword);
        } catch (Exception $e) {
        }
    }

    protected function getUserWithRelations(int $userId): User
    {
        return User::with([
            'role',
            'permissions',
            'creator:id,name,email'
        ])->findOrFail($userId);
    }

    protected function buildSuccessResponse(User $user, string $temporaryPassword, ?Role $role, ?Department $department): array
    {
        return [
            'user' => $user,
            'temporary_password' => $temporaryPassword,
            'success' => true,
            'message' => 'User created successfully',
            'role_assigned' => $role?->name,
            'department_assigned' => $department?->department_name,
            'permissions_count' => $user->permissions ? $user->permissions->count() : 0,
        ];
    }

    public function updateUser(User $user, array $data)
    {
        $user->update($data);
        $this->logActivity($user, 'user_updated', 'Updated user ' . $user->username);

        return $user;
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            throw new Exception('You cannot deactivate your own account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $action = $user->is_active ? 'activated' : 'deactivated';

        $this->logActivity($user, 'user_' . $action, ucfirst($action) . ' user ' . $user->username);

        return $user;
    }
}
