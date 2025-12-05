<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DebitNoteReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedItemCodes();
    }

    /**
     * Seed item codes reference table.
     */
    protected function seedItemCodes(): void
    {
        $itemCodes = [
            [
                'item_code' => 'IT01',
                'description' => 'Gross Premium',
                'item_type' => 'DEBIT',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'item_code' => 'IT02',
                'description' => 'Claims Payment',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'item_code' => 'IT03',
                'description' => 'Commission Allowance',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'item_code' => 'IT04',
                'description' => 'Reinsurance Levy',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'item_code' => 'IT05',
                'description' => 'Premium Levy',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'item_code' => 'IT06',
                'description' => 'Brokerage Fee',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'item_code' => 'IT26',
                'description' => 'Premium Portfolio Entry',
                'item_type' => 'DEBIT',
                'is_active' => true,
                'sort_order' => 26,
            ],
            [
                'item_code' => 'IT27',
                'description' => 'Loss Portfolio Entry',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 27,
            ],
            [
                'item_code' => 'IT29',
                'description' => 'Withholding Tax (WHT)',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 29,
            ],
        ];

        foreach ($itemCodes as $item) {
            DB::table('treaty_item_codes')->updateOrInsert(
                ['item_code' => $item['item_code']],
                array_merge($item, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
