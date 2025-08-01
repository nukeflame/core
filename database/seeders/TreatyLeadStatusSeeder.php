<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TreatyLeadStatusSeeder extends Seeder
{
    public function run()
    {
        $leadStatuses = [
            ['status_name' => 'lead-25%', 'id' => '1', 'category_type' => '1'],
            ['status_name' => 'proposal-50%', 'id' => '2', 'category_type' => '1'],
            ['status_name' => 'evaluation/negotiation-50%', 'id' => '3', 'category_type' => '1'],
            ['status_name' => 'Final-Normal', 'id' => '4', 'category_type' => '1'],
            ['status_name' => 'Won-Normal', 'id' => '5', 'category_type' => '1'],
            ['status_name' => 'Lost-Normal', 'id' => '6', 'category_type' => '1'],
            ['status_name' => 'Tender-details', 'id' => '1', 'category_type' => '2'],
            ['status_name' => 'Lost/Continue', 'id' => '2', 'category_type' => '2'],
            ['status_name' => 'Request for docs', 'id' => '3', 'category_type' => '2'],
            ['status_name' => 'docs-attach', 'id' => '4', 'category_type' => '2'],
            ['status_name' => 'lead-25%', 'id' => '5', 'category_type' => '2'],
            ['status_name' => 'proposal-50%', 'id' => '6', 'category_type' => '2'],
            ['status_name' => 'evaluation/negotiation-50%', 'id' => '7', 'category_type' => '2'],
            ['status_name' => 'Final Stage-Tender', 'id' => '8', 'category_type' => '2'],
            ['status_name' => 'Won-Tender', 'id' => '9', 'category_type' => '2'],
            ['status_name' => 'Lost-Tender', 'id' => '10', 'category_type' => '2'],
        ];

        foreach ($leadStatuses as $status) {
            DB::table('treaty_lead_status')->updateOrInsert(
                ['id' => $status['id'], 'category_type' => $status['category_type']], // Match on id and category_type
                [
                    'status_name' => $status['status_name'],
                    'id' => $status['id'],
                    'category_type' => $status['category_type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}