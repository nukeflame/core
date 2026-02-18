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
                'description' => 'GROSS PREMIUM',
                'item_type' => 'DEBIT',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'item_code' => 'IT02',
                'description' => 'CLAIMS',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'item_code' => 'IT03',
                'description' => 'COMMISSION',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'item_code' => 'IT04',
                'description' => 'REINSURANCE TAX',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'item_code' => 'IT05',
                'description' => 'PREMIUM TAX',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'item_code' => 'IT06',
                'description' => 'BROKERAGE',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'item_code' => 'IT26',
                'description' => 'PREMIUM PORTFOLIO ENTRY',
                'item_type' => 'DEBIT',
                'is_active' => true,
                'sort_order' => 26,
            ],
            [
                'item_code' => 'IT27',
                'description' => 'LOSS PORTFOLIO ENTRY',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 27,
            ],
            [
                'item_code' => 'IT29',
                'description' => 'WITHHOLDING TAX',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 29,
            ],
            [
                'item_code' => 'IT30',
                'description' => 'PREMIUM PORTFOLIO OUTGO',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'item_code' => 'IT31',
                'description' => 'LOSS PORTFOLIO OUTGO',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 31,
            ],
            [
                'item_code' => 'IT32',
                'description' => 'MANAGEMENT EXPENSES',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 32,
            ],
            [
                'item_code' => 'IT33',
                'description' => 'LOSSES CARRIED FORWARD',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 33,
            ],
            [
                'item_code' => 'IT34',
                'description' => 'BALANCES CARRIED FORWARD - ADJUSTMENTS',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 34,
            ],
            [
                'item_code' => 'IT35',
                'description' => 'VAT ON BROKERAGE',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 35,
            ],
            [
                'item_code' => 'IT36',
                'description' => 'VAT ON COMMISSION',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 36,
            ],
            [
                'item_code' => 'IT37',
                'description' => 'CITY LEVY',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 37,
            ],
            [
                'item_code' => 'IT38',
                'description' => 'CEDANT VAT',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 38,
            ],
            [
                'item_code' => 'IT39',
                'description' => 'BROKERAGE WITHHOLDING TAX',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 39,
            ],
            [
                'item_code' => 'IT40',
                'description' => 'PREMIUM WITHHOLDING TAX',
                'item_type' => 'CREDIT',
                'is_active' => true,
                'sort_order' => 40,
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
