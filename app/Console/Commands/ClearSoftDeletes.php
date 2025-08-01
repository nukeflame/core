<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearSoftDeletes extends Command
{
    protected $signature = 'db:clear-soft-deletes {--table= : Specific table to clear} {--before= : Clear records deleted before this date (Y-m-d)} {--force : Force the operation without confirmation}';
    protected $description = 'Permanently delete all soft-deleted records from the database';

    /**
     * Get the model class for a given table name
     */
    protected function getModelForTable(string $table): ?string
    {
        $modelName = Str::studly(Str::singular($table));
        $possiblePaths = [
            "App\\Models\\{$modelName}",
            "App\\{$modelName}"
        ];

        foreach ($possiblePaths as $path) {
            if (class_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Get primary key from model or database
     */
    protected function getPrimaryKeyColumn(string $table): mixed
    {
        $modelClass = $this->getModelForTable($table);
        $r = null;
        if ($modelClass) {
            try {
                $model = new $modelClass();
                if (in_array('App\\Http\\Traits\\ModelCompositeKey', class_uses_recursive($model))) {
                    $r = ['composite' => $model->getKeyName(), 'table' => $model->getTable()];
                } else {
                    $r = ['normal' => $model->getKeyName(), 'table' => $model->getTable()];
                }
            } catch (\Exception $e) {
                $this->warn("Could not instantiate model for table {$table}: " . $e->getMessage());
            }
        }

        $result = DB::select(
            "
            SELECT a.attname as column_name
            FROM pg_index i
            JOIN pg_attribute a ON a.attrelid = i.indrelid
                AND a.attnum = ANY(i.indkey)
            WHERE i.indrelid = ?::regclass
            AND i.indisprimary",
            [$table]
        );

        return $result ? ['normal' => $result[0]?->column_name, 'table' => 'default'] : $r;
    }

    public function handle()
    {
        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");

        $tablesToProcess = collect($tables)
            ->pluck('table_name')
            ->filter(function ($table) {
                return Schema::hasColumn($table, 'deleted_at');
            });

        if ($this->option('table')) {
            $tablesToProcess = $tablesToProcess->filter(function ($table) {
                return $table === $this->option('table');
            });
        }

        if ($tablesToProcess->isEmpty()) {
            $this->error('No tables with soft deletes found!');
            return 1;
        }

        if (!$this->option('force')) {
            $this->warn('This will permanently delete all soft-deleted records from the following tables:');
            $this->line($tablesToProcess->implode(', '));

            if (!$this->confirm('Do you wish to continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $totalDeleted = 0;

        foreach ($tablesToProcess as $table) {
            try {
                $primaryKey = $this->getPrimaryKeyColumn($table);

                if (!$primaryKey) {
                    $this->warn("Skipping table {$table}: No primary key found");
                    continue;
                }

                DB::beginTransaction();
                $query = DB::table($table)->whereNotNull('deleted_at');
                $date = null;

                // Add date filter if specified
                if ($this->option('before')) {
                    $date = $this->option('before');
                    $query->where('deleted_at', '<', $date);
                }

                $count = $query->count();

                if ($count === 0) {
                    $this->info("No soft-deleted records found in {$table}");
                    DB::commit();
                    continue;
                }

                if (isset($primaryKey['normal']) && $primaryKey['normal'] != 'default' && $table === $primaryKey['table']) {
                    $primary = $primaryKey['normal'];
                } else {
                    if (isset($primaryKey['composite'])) {
                        $primary = implode(",", $primaryKey['composite']);
                    }
                }

                $chunkSize = 100;
                $deleted = 0;
                $idsToDelete  = [];

                while ($deleted < $count) {
                    if ($this->option('table') && $table === $primaryKey['table']) {
                        $idsToDelete = DB::table($table)
                            ->select($primary)
                            ->whereNotNull('deleted_at')
                            ->when($this->option('before'), function ($query) use ($date) {
                                return $query->where('deleted_at', '<', $date);
                            })
                            ->limit($chunkSize)
                            ->pluck($primary);

                        $deletedInChunk = DB::table($table)
                            ->whereIn($primary, $idsToDelete)
                            ->delete();
                    }

                    if (count($idsToDelete) <= 0) {
                        break;
                    }

                    $deleted += $deletedInChunk;

                    $this->info("Processed {$deleted}/{$count} records from {$table}");
                }

                DB::commit();
                $totalDeleted += $count;
                $this->info("Completed deleting {$count} records from {$table}");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error processing table {$table}: " . $e->getMessage());
                continue;
            }
        }

        $this->info("Operation completed! Total records deleted: {$totalDeleted}");
        return 0;
    }
}
