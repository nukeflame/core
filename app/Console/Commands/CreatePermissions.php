<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class CreatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:create
                            {--permissions=* : Array of permission names to create}
                            {--guard=web : The guard name for the permissions}
                            {--force : Force creation even if permissions already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create multiple permissions at once';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $permissionNames = $this->option('permissions');
        $guardName = $this->option('guard');
        $force = $this->option('force');

        if (empty($permissionNames)) {
            $inputPermissions = $this->ask('Enter permission names separated by commas (e.g., create-posts,edit-posts,delete-posts)');
            $permissionNames = array_map('trim', explode(',', $inputPermissions));
        }

        if (empty($permissionNames)) {
            $this->error('No permissions specified. Command aborted.');
            return 1;
        }

        $this->info('Starting permission creation process...');
        $this->line('Guard: ' . $guardName);

        $existingCount = 0;
        $createdCount = 0;
        $failedCount = 0;

        try {
            DB::beginTransaction();

            foreach ($permissionNames as $name) {
                if (empty($name)) continue;

                $exists = Permission::where('name', $name)
                    ->where('guard_name', $guardName)
                    ->exists();

                if ($exists) {
                    $existingCount++;

                    if ($force) {
                        $this->line("Permission <comment>{$name}</comment> already exists. Recreating due to --force option.");
                        Permission::where('name', $name)->where('guard_name', $guardName)->delete();

                        Permission::create([
                            'name' => $name,
                            'status' => 'A',
                            'permission_code' => Str::slug($name) . '-' . rand(2030, 9999),
                            'description' => ucwords(str_replace(['_', '.'], ' ', $name)) . ' Permission',
                            'guard_name' => $guardName
                        ]);

                        $this->info("Permission <comment>{$name}</comment> recreated successfully.");
                        $createdCount++;
                    } else {
                        $this->warn("Permission <comment>{$name}</comment> already exists. Skipping...");
                    }

                    continue;
                }

                Permission::create([
                    'name' => $name,
                    'status' => 'A',
                    'permission_code' => Str::slug($name) . '-' . rand(2030, 9999),
                    'description' => ucwords(str_replace(['_', '.'], ' ', $name)) . ' Permission',
                    'guard_name' => $guardName
                ]);

                $this->info("Permission <comment>{$name}</comment> created successfully.");
                $createdCount++;
            }

            DB::commit();

            $this->newLine();
            $this->info('Permission creation completed:');
            $this->line(" - Created: {$createdCount}");
            $this->line(" - Already existed: {$existingCount}");

            if ($failedCount > 0) {
                $this->error(" - Failed: {$failedCount}");
            }

            return 0;
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('An error occurred while creating permissions: ' . $e->getMessage());
            return 1;
        }
    }
}
