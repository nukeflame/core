<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxGroup;
use App\Models\TaxType;
use App\Models\TaxRate;

class TaxDataSeeder extends Seeder
{
    public function run(): void
    {
        TaxGroup::updateOrCreate(
            ['group_id' => 'REINS'],
            ['group_description' => 'Reinsurance Taxes']
        );
        TaxType::updateOrCreate(
            ['tax_code' => 'PREMIUM_LEVY'],
            [
                'tax_type'        => 'LEVY',
                'type_description' => 'Premium Levy',
                'add_deduct'      => 'Y',
                'control_account' => '',
                'transtype'       => 'TAX',
                'BASIS'           => 'GROSS',
                'analyse'         => 'Y',
            ]
        );

        TaxType::updateOrCreate(
            ['tax_code' => 'REINSURANCE_LEVY'],
            [
                'tax_type'        => 'LEVY',
                'type_description' => 'Reinsurance Levy',
                'add_deduct'      => 'Y',
                'control_account' => '',
                'transtype'       => 'TAX',
                'BASIS'           => 'GROSS',
                'analyse'         => 'Y',
            ]
        );

        TaxType::updateOrCreate(
            ['tax_code' => 'WITHHOLDING_TAX'],
            [
                'tax_type'        => 'WHT',
                'type_description' => 'Withholding Tax',
                'add_deduct'      => 'Y',
                'control_account' => '',
                'transtype'       => 'TAX',
                'BASIS'           => 'GROSS',
                'analyse'         => 'Y',
            ]
        );

        TaxRate::updateOrCreate(
            ['tax_code' => 'PREMIUM_LEVY'],
            [
                'group_id'        => 'REINS',
                'tax_type'        => 'LEVY',
                'tax_description' => 'Premium Levy',
                'tax_rate'        => '1.00',
            ]
        );

        TaxRate::updateOrCreate(
            ['tax_code' => 'REINSURANCE_LEVY'],
            [
                'group_id'        => 'REINS',
                'tax_type'        => 'LEVY',
                'tax_description' => 'Reinsurance Levy',
                'tax_rate'        => '0.50',
            ]
        );

        TaxRate::updateOrCreate(
            ['tax_code' => 'WITHHOLDING_TAX'],
            [
                'group_id'        => 'REINS',
                'tax_type'        => 'WHT',
                'tax_description' => 'Withholding Tax',
                'tax_rate'        => '5.00',
            ]
        );

        $this->command->info('Tax default data seeded successfully.');
    }
}
