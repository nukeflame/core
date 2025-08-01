<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class AttachPermissionsToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:attach-permissions
                            {--user=* : ID or IDs of users}
                            {--permissions=* : Array of permission names to attach}
                            {--all-users : Flag to attach permissions to all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach multiple permissions to one or more users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userIds = $this->option('user');
        $permissionNames = $this->option('permissions');
        $allUsers = $this->option('all-users');

        if (empty($userIds) && !$allUsers) {
            $this->error('You must specify at least one user ID with --user or use --all-users flag.');
            return 1;
        }

        if (empty($permissionNames)) {
            $this->error('No permissions specified. Use --permissions option to specify permissions.');
            return 1;
        }

        if ($allUsers) {
            $users = User::all();
            if ($users->isEmpty()) {
                $this->error('No users found in the database.');
                return 1;
            }
            $this->warn('Attaching permissions to ALL users (' . $users->count() . ')');
            if (!$this->confirm('Do you want to continue?', false)) {
                $this->info('Operation cancelled.');
                return 0;
            }
        } else {
            $users = collect();
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if (!$user) {
                    $this->error("User with ID {$userId} not found.");
                    continue;
                }
                $users->push($user);
            }

            if ($users->isEmpty()) {
                $this->error('None of the specified users were found.');
                return 1;
            }
        }

        $validPermissions = [];
        $invalidPermissions = [];

        foreach ($permissionNames as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();

            if ($permission) {
                $validPermissions[] = $permission;
            } else {
                $invalidPermissions[] = $permissionName;
            }
        }

        if (count($invalidPermissions) > 0) {
            $this->warn('The following permissions do not exist:');
            foreach ($invalidPermissions as $name) {
                $this->line(" - {$name}");
            }
        }

        if (count($validPermissions) === 0) {
            $this->error('No valid permissions provided. Command aborted.');
            return 1;
        }

        $this->info('Found ' . count($validPermissions) . ' valid permissions and ' . $users->count() . ' users.');

        $processedCount = 0;
        foreach ($users as $user) {
            $this->line('');
            $this->info("Processing user: {$user->name} (ID: {$user->id})");

            foreach ($validPermissions as $permission) {
                if ($user->hasPermissionTo($permission->name)) {
                    $this->line(" - Permission '{$permission->name}' already assigned");
                } else {
                    $user->givePermissionTo($permission);
                    $this->line(" - <info>Permission '{$permission->name}' attached</info>");
                }
            }

            $this->displayUserPermissions($user);
            $processedCount++;
        }

        $this->newLine();
        $this->info("Completed! Processed {$processedCount} users.");

        return 0;
    }

    /**
     * Display all permissions attached to the user
     *
     * @param \App\Models\User $user
     * @return void
     */
    protected function displayUserPermissions($user)
    {
        $this->line("All permissions for user {$user->name} (ID: {$user->id}):");

        $permissions = $user->getAllPermissions();

        if ($permissions->isEmpty()) {
            $this->line(" <comment>No permissions assigned</comment>");
            return;
        }

        $headers = ['ID', 'Name', 'Guard'];
        $rows = [];

        foreach ($permissions as $permission) {
            $rows[] = [
                $permission->id,
                $permission->name,
                $permission->guard_name
            ];
        }

        $this->table($headers, $rows);
        $this->line(" <info>Total: {$permissions->count()} permission(s)</info>");
    }
}
