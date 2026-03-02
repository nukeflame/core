<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemProcessSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('system_process')) {
            return;
        }

        $columns = Schema::getColumnListing('system_process');

        $processes = [
            [
                'name' => 'Cover Registration',
                'nice_name' => 'cover-registration',
                'description' => 'Core process for cover setup, validation, and approval lifecycle.',
                'execution_type' => 'manual',
                'status' => 'active',
                'priority' => 'high',
            ],
            [
                'name' => 'Cover Registration (Legacy)',
                'nice_name' => 'cover_registration',
                'description' => 'Legacy alias for cover registration process lookups.',
                'execution_type' => 'manual',
                'status' => 'active',
                'priority' => 'high',
            ],
            [
                'name' => 'Claim Intimation Process',
                'nice_name' => 'claim_intimation_process',
                'description' => 'Handles claim intimation verification and routing.',
                'execution_type' => 'manual',
                'status' => 'active',
                'priority' => 'high',
            ],
            [
                'name' => 'Claim Registration',
                'nice_name' => 'claim_registration',
                'description' => 'Registers validated claim intimations into the claims workflow.',
                'execution_type' => 'manual',
                'status' => 'active',
                'priority' => 'high',
            ],
            [
                'name' => 'Requisition Process',
                'nice_name' => 'requisition-process',
                'description' => 'Approvals workflow for payment requisitions.',
                'execution_type' => 'manual',
                'status' => 'active',
                'priority' => 'medium',
            ],
            [
                'name' => 'GL Batch Process',
                'nice_name' => 'gl-batch-process',
                'description' => 'Approval workflow for GL batch validation and posting.',
                'execution_type' => 'manual',
                'status' => 'active',
                'priority' => 'high',
            ],
            [
                'name' => 'User Management',
                'nice_name' => 'user_management',
                'description' => 'Administrative process for user onboarding and access management.',
                'execution_type' => 'manual',
                'status' => 'active',
                'priority' => 'medium',
            ],
            [
                'name' => 'System Maintenance',
                'nice_name' => 'system_maintenance',
                'description' => 'Operational process for maintenance windows and housekeeping actions.',
                'execution_type' => 'automated',
                'status' => 'active',
                'priority' => 'medium',
            ],
            [
                'name' => 'Integration APIs',
                'nice_name' => 'integration_apis',
                'description' => 'Monitoring and control process for integration APIs and background syncs.',
                'execution_type' => 'automated',
                'status' => 'active',
                'priority' => 'high',
            ],
        ];

        foreach ($processes as $process) {
            $existing = DB::table('system_process')
                ->where('nice_name', $process['nice_name'])
                ->first();

            if ($existing) {
                DB::table('system_process')
                    ->where('id', $existing->id)
                    ->update($this->payload($process, $columns, false));

                continue;
            }

            $id = (int) DB::table('system_process')->max('id') + 1;
            DB::table('system_process')->insert($this->payload(array_merge(['id' => $id], $process), $columns, true));
        }
    }

    private function payload(array $data, array $columns, bool $creating): array
    {
        $payload = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $columns, true)) {
                $payload[$key] = $value;
            }
        }

        if ($creating && in_array('created_by', $columns, true) && !isset($payload['created_by'])) {
            $payload['created_by'] = 'seeder';
        }

        if (in_array('updated_by', $columns, true) && !isset($payload['updated_by'])) {
            $payload['updated_by'] = 'seeder';
        }

        if (in_array('started_at', $columns, true) && !isset($payload['started_at'])) {
            $payload['started_at'] = now();
        }

        if (in_array('created_at', $columns, true) && !isset($payload['created_at'])) {
            $payload['created_at'] = now();
        }

        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        return $payload;
    }
}
