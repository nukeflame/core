<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemProcessActionSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('system_process') || !Schema::hasTable('system_process_action')) {
            return;
        }

        $processColumns = Schema::getColumnListing('system_process');
        $actionColumns = Schema::getColumnListing('system_process_action');

        $actions = [
            [
                'process_nice_name' => 'cover-registration',
                'name' => 'Verify Cover',
                'nice_name' => 'verify_cover',
                'module' => 'cover_administration',
                'action_type' => 'verify',
                'description' => 'Verify cover registration before final approval.',
                'status' => 'completed',
            ],
            [
                'process_nice_name' => 'claim_intimation_process',
                'name' => 'Verify Claim Intimation',
                'nice_name' => 'verify_claim_intimation_process',
                'module' => 'claims_administration',
                'action_type' => 'verify',
                'description' => 'Verify claim intimation and queue for registration.',
                'status' => 'completed',
            ],
            [
                'process_nice_name' => 'cover-registration',
                'name' => 'Verify Claim Notification',
                'nice_name' => 'verify-claim-notification',
                'module' => 'claims_administration',
                'action_type' => 'verify',
                'description' => 'Legacy alias used by claim notification screens.',
                'status' => 'completed',
            ],
            [
                'process_nice_name' => 'claim_registration',
                'name' => 'Verify Claim',
                'nice_name' => 'verify_claim',
                'module' => 'claims_administration',
                'action_type' => 'verify',
                'description' => 'Verification action during claim registration flow.',
                'status' => 'completed',
            ],
            [
                'process_nice_name' => 'requisition-process',
                'name' => 'Authorize Requisition',
                'nice_name' => 'authorize-requisition',
                'module' => 'approvals',
                'action_type' => 'approve',
                'description' => 'Authorize requisition before final approval.',
                'status' => 'completed',
            ],
            [
                'process_nice_name' => 'requisition-process',
                'name' => 'Approve Requisition',
                'nice_name' => 'approve-requisition',
                'module' => 'approvals',
                'action_type' => 'approve',
                'description' => 'Final approval action for requisitions.',
                'status' => 'completed',
            ],
            [
                'process_nice_name' => 'gl-batch-process',
                'name' => 'Verify GL Batch',
                'nice_name' => 'verify-glbatch',
                'module' => 'reinsurance',
                'action_type' => 'verify',
                'description' => 'Verify GL batch before posting.',
                'status' => 'completed',
            ],
            [
                'process_nice_name' => 'user_management',
                'name' => 'Create User',
                'nice_name' => 'create-user',
                'module' => 'user_management',
                'action_type' => 'create',
                'description' => 'Create new users in the platform.',
                'status' => 'completed',
            ],
            [
                'process_nice_name' => 'user_management',
                'name' => 'Update User Roles',
                'nice_name' => 'update-user-roles',
                'module' => 'user_management',
                'action_type' => 'update',
                'description' => 'Modify assigned roles and effective permissions.',
                'status' => 'completed',
            ],
            [
                'process_nice_name' => 'integration_apis',
                'name' => 'Restart Integration Process',
                'nice_name' => 'restart-integration-process',
                'module' => 'integration_apis',
                'action_type' => 'update',
                'description' => 'Restart a stalled or manually stopped integration process.',
                'status' => 'completed',
            ],
        ];

        foreach ($actions as $action) {
            $process = DB::table('system_process')
                ->where('nice_name', $action['process_nice_name'])
                ->first();

            if (!$process) {
                $fallback = [
                    'name' => ucwords(str_replace(['-', '_'], ' ', $action['process_nice_name'])),
                    'nice_name' => $action['process_nice_name'],
                    'description' => 'Auto-created by seeder to satisfy action dependency.',
                    'execution_type' => 'manual',
                    'status' => 'active',
                    'priority' => 'medium',
                ];

                $processId = (int) DB::table('system_process')->max('id') + 1;
                DB::table('system_process')->insert($this->processPayload(array_merge(['id' => $processId], $fallback), $processColumns, true));
                $process = (object) ['id' => $processId];
            }

            $existing = DB::table('system_process_action')
                ->where('nice_name', $action['nice_name'])
                ->first();

            $record = array_merge($action, ['process_id' => $process->id]);
            unset($record['process_nice_name']);

            if ($existing) {
                DB::table('system_process_action')
                    ->where('id', $existing->id)
                    ->update($this->actionPayload($record, $actionColumns, false));

                continue;
            }

            $id = (int) DB::table('system_process_action')->max('id') + 1;
            DB::table('system_process_action')->insert($this->actionPayload(array_merge(['id' => $id], $record), $actionColumns, true));
        }
    }

    private function processPayload(array $data, array $columns, bool $creating): array
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

    private function actionPayload(array $data, array $columns, bool $creating): array
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

        if (in_array('performed_by', $columns, true) && !isset($payload['performed_by'])) {
            $payload['performed_by'] = 1;
        }

        if (in_array('performed_at', $columns, true)) {
            $payload['performed_at'] = now();
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
