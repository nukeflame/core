<?php

namespace App\Services;

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use App\Models\Role;
use App\Models\UserActivityLog;
use Exception;
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

    /**
     * Create a new user with the given data
     *
     * @param array $data User data
     * @param bool $sendWelcomeEmail Whether to send a welcome email
     * @param bool $requirePasswordReset Whether to require password reset on first login
     * @return array User and temporary password
     */
    public function createUser(array $data, bool $sendWelcomeEmail = false, bool $requirePasswordReset = true)
    {
        try {
            $temporaryPassword = $this->passwordService->generateTemporaryPassword();

            if (empty($temporaryPassword)) {
                throw new \Exception('Failed to generate temporary password');
            }

            $role = null;
            $roleId = $data['role_id'] ?? null;

            if ($roleId) {
                $role = DB::table('roles')
                    ->where('id', $roleId)
                    ->where('is_active', true)
                    ->first();

                if (!$role) {
                    throw new \Exception("Active role with ID {$roleId} not found");
                }
            }

            $department = null;
            $departmentCode = $data['department_id'] ?? null;

            if ($departmentCode) {
                $department = DB::table('company_department')
                    ->where('department_code', $departmentCode)
                    ->first();

                if (!$department) {
                    throw new \Exception("Department with code {$departmentCode} not found");
                }
            }

            $userCreationData = $data;
            unset($userCreationData['role_id']);

            $userData = array_merge([
                'password' => Hash::make($temporaryPassword),
                'is_active' => true,
                'is_staff' => $data['is_staff'] ?? false,
                'role_id' => $roleId,
                'created_by' => auth()->id(),
                'requires_password_reset' => $requirePasswordReset,
                'created_at' => now(),
                'updated_at' => now(),
                'password_expires_at' => $requirePasswordReset ? now()->addDays(1) : null,
            ], $userCreationData);

            $user = User::create($userData);

            if (!$user) {
                throw new \Exception('Failed to create user in database');
            }

            if ($roleId) {
                try {
                    DB::table('role_departments')->updateOrInsert([
                        'role_id' => $roleId,
                        'department_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception('User created but role assignment failed: ' . $e->getMessage());
                }
            }

            try {
                $this->logActivity($user, 'user_created', "Created user {$user->user_name} with role {$role->name}");
            } catch (\Exception $e) {
                logger()->warning('Failed to log user creation activity', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            if ($sendWelcomeEmail) {
                try {
                    SendWelcomeEmail::dispatch($user, $temporaryPassword);
                } catch (\Exception $e) {
                    logger()->error('Failed to dispatch welcome email', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $userWithRelations = User::with(['department', 'assigndDepartmentRole'])->find($user->id);

            return [
                'user' => $userWithRelations,
                'temporaryPassword' => $temporaryPassword,
                'success' => true,
                'message' => 'User created successfully',
                'role_assigned' => $role ? $role->name : null,
                'department_assigned' => $department ? $department->department_name : null
            ];
        } catch (\Exception $e) {
            logger()->error('UserService createUser failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    // public function createUser(array $data, bool $sendWelcomeEmail = false, bool $requirePasswordReset = true)
    // {
    //     $temporaryPassword = $this->passwordService->generateTemporaryPassword();
    //     $userId = User::query()->withTrashed()->max('id') + 1;
    //     if (isset($data['role_id'])) {
    //         $role = Role::find($data['role_id']);
    //     }

    //     $userData = array_merge([
    //         'id' => $userId,
    //         'is_active' => true,
    //         'created_by' => auth()->id(),
    //         'requires_password_reset' => $requirePasswordReset,
    //         'password' => Hash::make($temporaryPassword),
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //         'password_expires_at' => now()
    //     ], $data);

    //     $user = User::create($userData);

    //     $this->logActivity($user, 'user_created', 'Created user ' . $user->username);

    //     if (isset($role)) {
    //         $user->assignRole($role->name);
    //     }

    //     if ($sendWelcomeEmail) {
    //         SendWelcomeEmail::dispatch($user, $temporaryPassword);
    //     }


    //     return [
    //         'user' => [],
    //         'temporaryPassword' => $temporaryPassword
    //     ];
    // }

    /**
     * Update an existing user
     *
     * @param User $user The user to update
     * @param array $data Updated user data
     * @return User
     */
    public function updateUser(User $user, array $data)
    {
        $user->update($data);
        $this->logActivity($user, 'user_updated', 'Updated user ' . $user->username);

        return $user;
    }

    /**
     * Toggle a user's active status
     *
     * @param User $user The user to toggle
     * @return User
     */
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

    /**
     * Log user activity
     *
     * @param User $user The affected user
     * @param string $activityType Type of activity
     * @param string $description Description of activity
     * @param array $additionalData Additional data to log
     * @return UserActivityLog
     */
    private function logActivity(User $user, string $activityType, string $description, array $additionalData = [])
    {
        return UserActivityLog::create([
            'user_id' => auth()->id(),
            'activity_type' => $activityType,
            'ip_address' => request()->ip(),
            'description' => $description,
            'additional_data' => !empty($additionalData) ? $additionalData : null,
        ]);
    }
}
