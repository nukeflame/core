<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Exception;

class DeletePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:delete
                            {--permissions=* : Array of permission names to delete}
                            {--guard= : Specify a guard to filter permissions}
                            {--all : Delete all permissions (requires confirmation)}
                            {--force : Skip confirmation when deleting permissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete specified permissions from the system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $permissionNames = $this->option('permissions');
        $guardName = $this->option('guard');
        $deleteAll = $this->option('all');
        $force = $this->option('force');

        if ($deleteAll) {
            $query = Permission::query();

            if ($guardName) {
                $query->where('guard_name', $guardName);
                $allCount = $query->count();
                $warningMessage = "You are about to delete ALL {$allCount} permissions";
                if ($guardName) {
                    $warningMessage .= " for guard '{$guardName}'";
                }
            } else {
                $allCount = $query->count();
                $warningMessage = "You are about to delete ALL {$allCount} permissions in the system";
            }

            $this->warn($warningMessage);
            $this->warn('This action cannot be undone and may break your application!');

            if (!$force && !$this->confirm('Are you absolutely sure you want to continue?', false)) {
                $this->info('Operation cancelled.');
                return 0;
            }

            try {
                DB::beginTransaction();
                $deleted = $query->delete();
                DB::commit();

                $this->info("Successfully deleted {$deleted} permissions.");
                return 0;
            } catch (Exception $e) {
                DB::rollBack();
                $this->error('Failed to delete permissions: ' . $e->getMessage());
                return 1;
            }
        }

        if (empty($permissionNames)) {
            $inputPermissions = $this->ask('Enter permission names to delete, separated by commas');
            $permissionNames = array_map('trim', explode(',', $inputPermissions));
        }

        if (empty($permissionNames)) {
            $this->error('No permissions specified. Command aborted.');
            return 1;
        }

        $this->info('Starting permission deletion process...');

        $notFoundCount = 0;
        $deletedCount = 0;
        $failedCount = 0;

        try {
            DB::beginTransaction();

            foreach ($permissionNames as $name) {
                if (empty($name)) continue;

                $query = Permission::where('name', $name);
                if ($guardName) {
                    $query->where('guard_name', $guardName);
                }

                $permission = $query->first();

                if (!$permission) {
                    $notFoundCount++;
                    $this->warn("Permission '{$name}'" . ($guardName ? " with guard '{$guardName}'" : "") . " not found. Skipping...");
                    continue;
                }

                if (!$force) {
                    $userCount = $permission->users->count();
                    $roleCount = $permission->roles->count();

                    if ($userCount > 0 || $roleCount > 0) {
                        $this->warn("Permission '{$name}' is currently:");
                        if ($userCount > 0) {
                            $this->line(" - Directly assigned to {$userCount} user(s)");
                        }
                        if ($roleCount > 0) {
                            $this->line(" - Used by {$roleCount} role(s)");
                        }

                        if (!$force && !$this->confirm("Continue with deleting this permission?", true)) {
                            $this->line("Skipping permission '{$name}'");
                            continue;
                        }
                    }
                }

                $permissionId = $permission->id;
                $permissionGuard = $permission->guard_name;

                if ($permission->delete()) {
                    $deletedCount++;
                    $this->info("Permission '{$name}' (ID: {$permissionId}, Guard: {$permissionGuard}) deleted successfully.");
                } else {
                    $failedCount++;
                    $this->error("Failed to delete permission '{$name}'.");
                }
            }

            DB::commit();

            $this->newLine();
            $this->info('Permission deletion completed:');
            $this->line(" - Deleted: {$deletedCount}");
            $this->line(" - Not found: {$notFoundCount}");

            if ($failedCount > 0) {
                $this->error(" - Failed: {$failedCount}");
            }

            return ($deletedCount > 0) ? 0 : 1;
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('An error occurred while deleting permissions: ' . $e->getMessage());
            return 1;
        }
    }
}
