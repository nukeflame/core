<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $docTypes = [
            [
                'doc_type' => 'PROPOSAL FORM',
                'description' => 'PROPOSAL FORM',
                'category_type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'doc_type' => 'QUOTATION TEMPLATE',
                'description' => 'QUOTATION TEMPLATE',
                'category_type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'doc_type' => 'FINANCIALS',
                'description' => 'FINANCIALS',
                'category_type' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'doc_type' => 'QUOTATION TERMS',
                'description' => 'QUOTATION TERMS',
                'category_type' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'doc_type' => 'POLICY SCHEDULE',
                'description' => 'POLICY SCHEDULE',
                'category_type' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'doc_type' => 'SUM INSURED BREAKDOWN/WINSURED ITEMS',
                'description' => 'SUM INSURED BREAKDOWN/WINSURED ITEMS',
                'category_type' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]



        ];

        DB::table('doc_types')->insert($docTypes);
    }
}
