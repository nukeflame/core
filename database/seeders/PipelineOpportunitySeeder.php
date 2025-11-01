<?php

namespace Database\Seeders;

use App\Models\Bd\PipelineOpportunity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PipelineOpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates sample treaty opportunities for testing the Treaty Pipeline UI
     */
    public function run(): void
    {
        $currentYear = Carbon::now()->year;

        $opportunities = [
            [
                'opportunity_id' => 'TRT-' . $currentYear . '-001',
                'insured_name' => 'Kenya Airways Ltd',
                'customer_id' => 3,
                'client_category' => 'O',
                'type_of_bus' => 'TPR',
                'classcode' => 101,
                'divisions' => 'GR',
                'cede_premium' => 12500000.00,
                'comm_rate' => 12.5,
                'stage' => 1,
                'probability' => 40,
                'priority' => 'high',
                'status' => 'active',
                'next_action' => 'Schedule initial meeting',
                'effective_date' => Carbon::now()->addDays(45),
                'closing_date' => Carbon::now()->addDays(40),
                'expected_closure_date' => Carbon::now()->addDays(35),
                'pip_year' => $currentYear,
                'description' => 'Aviation treaty renewal with enhanced coverage',
            ],
            [
                'opportunity_id' => 'TRT-' . $currentYear . '-002',
                'insured_name' => 'Safaricom PLC',
                'customer_id' => 3,
                'client_category' => 'N',
                'type_of_bus' => 'TNP',
                'classcode' => 101,
                'divisions' => 'GR',
                'cede_premium' => 8750000.00,
                'comm_rate' => 15.0,
                'stage' => 2,
                'probability' => 65,
                'priority' => 'medium',
                'status' => 'active',
                'next_action' => 'Submit proposal document',
                'effective_date' => Carbon::now()->addDays(60),
                'closing_date' => Carbon::now()->addDays(55),
                'expected_closure_date' => Carbon::now()->addDays(50),
                'pip_year' => $currentYear,
                'description' => 'Telecommunications infrastructure non-proportional treaty',
            ],
            [
                'opportunity_id' => 'TRT-' . $currentYear . '-003',
                'insured_name' => 'East African Breweries',
                'customer_id' => 3,
                'client_category' => 'O',
                'type_of_bus' => 'TPR',
                'classcode' => 101,
                'divisions' => 'GR',
                'cede_premium' => 5200000.00,
                'comm_rate' => 10.0,
                'stage' => 3,
                'probability' => 75,
                'priority' => 'high',
                'status' => 'active',
                'next_action' => 'Complete due diligence review',
                'effective_date' => Carbon::now()->addDays(30),
                'closing_date' => Carbon::now()->addDays(25),
                'expected_closure_date' => Carbon::now()->addDays(20),
                'pip_year' => $currentYear,
                'description' => 'Fire and allied perils proportional treaty',
            ],
            [
                'opportunity_id' => 'TRT-' . $currentYear . '-004',
                'insured_name' => 'KCB Bank Group',
                'customer_id' => 3,
                'client_category' => 'N',
                'type_of_bus' => 'TNP',
                'classcode' => 101,
                'divisions' => 'GR',
                'cede_premium' => 9800000.00,
                'comm_rate' => 13.5,
                'stage' => 4,
                'probability' => 80,
                'priority' => 'critical',
                'status' => 'active',
                'next_action' => 'Finalize terms and conditions',
                'effective_date' => Carbon::now()->addDays(20),
                'closing_date' => Carbon::now()->addDays(15),
                'expected_closure_date' => Carbon::now()->addDays(12),
                'pip_year' => $currentYear,
                'description' => 'Financial institutions professional indemnity treaty',
            ],
            [
                'opportunity_id' => 'TRT-' . $currentYear . '-005',
                'insured_name' => 'Bamburi Cement Ltd',
                'customer_id' => 5,
                'client_category' => 'O',
                'type_of_bus' => 'TPR',
                'classcode' => 101,
                'divisions' => 'GR',
                'cede_premium' => 6500000.00,
                'comm_rate' => 11.0,
                'stage' => 5,
                'probability' => 90,
                'priority' => 'high',
                'status' => 'active',
                'next_action' => 'Obtain final approval',
                'effective_date' => Carbon::now()->addDays(10),
                'closing_date' => Carbon::now()->addDays(8),
                'expected_closure_date' => Carbon::now()->addDays(5),
                'pip_year' => $currentYear,
                'description' => 'Engineering and construction all risks treaty',
            ],
            [
                'opportunity_id' => 'TRT-' . $currentYear . '-006',
                'insured_name' => 'Nairobi Hospital',
                'customer_id' => 8,
                'client_category' => 'N',
                'type_of_bus' => 'TNP',
                'classcode' => 101,
                'divisions' => 'GR',
                'cede_premium' => 7200000.00,
                'comm_rate' => 14.0,
                'stage' => 1,
                'probability' => 45,
                'priority' => 'medium',
                'status' => 'active',
                'next_action' => 'Conduct risk assessment',
                'effective_date' => Carbon::now()->addDays(90),
                'closing_date' => Carbon::now()->addDays(85),
                'expected_closure_date' => Carbon::now()->addDays(80),
                'pip_year' => $currentYear,
                'description' => 'Medical malpractice excess of loss treaty',
            ],
            [
                'opportunity_id' => 'TRT-' . $currentYear . '-007',
                'insured_name' => 'Kenya Ports Authority',
                'customer_id' => 8,
                'client_category' => 'O',
                'type_of_bus' => 'TPR',
                'classcode' => 101,
                'divisions' => 'GR',
                'cede_premium' => 11000000.00,
                'comm_rate' => 12.0,
                'stage' => 2,
                'probability' => 60,
                'priority' => 'high',
                'status' => 'active',
                'next_action' => 'Prepare detailed proposal',
                'effective_date' => Carbon::now()->addDays(70),
                'closing_date' => Carbon::now()->addDays(65),
                'expected_closure_date' => Carbon::now()->addDays(60),
                'pip_year' => $currentYear,
                'description' => 'Marine cargo and hull proportional treaty',
            ],
            [
                'opportunity_id' => 'TRT-' . $currentYear . '-008',
                'insured_name' => 'Standard Chartered Bank',
                'customer_id' => 8,
                'client_category' => 'N',
                'type_of_bus' => 'TNP',
                'classcode' => 101,
                'divisions' => 'GR',
                'cede_premium' => 4800000.00,
                'comm_rate' => 16.0,
                'stage' => 3,
                'probability' => 70,
                'priority' => 'low',
                'status' => 'active',
                'next_action' => 'Review underwriting guidelines',
                'effective_date' => Carbon::now()->addDays(50),
                'closing_date' => Carbon::now()->addDays(45),
                'expected_closure_date' => Carbon::now()->addDays(42),
                'pip_year' => $currentYear,
                'description' => 'Bankers blanket bond excess treaty',
            ],
        ];

        foreach ($opportunities as $opportunity) {
            PipelineOpportunity::updateOrCreate(
                ['opportunity_id' => $opportunity['opportunity_id']],
                $opportunity
            );
        }

        $this->command->info('Successfully seeded ' . count($opportunities) . ' pipeline opportunities');
    }
}
