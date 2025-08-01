<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePipelineOpportunitiesTable extends Migration
{
    public function up()
    {
        // Schema::table('pipeline_opportunities', function (Blueprint $table) {
            // Add only new columns that do not already exist in the table

            // Prospect columns
            // if (!Schema::hasColumn('pipeline_opportunities', 'prospect')) {
            //     $table->string('prospect')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'type_of_bus')) {
            //     $table->string('type_of_bus')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'lead_year')) {
            //     $table->string('lead_year')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'prod_cost')) {
            //     $table->string('prod_cost')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'branchcode')) {
            //     $table->string('branchcode')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'broker_flag')) {
            //     $table->string('broker_flag')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'brokercode')) {
            //     $table->string('brokercode')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'division')) {
            //     $table->string('division')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'pay_method')) {
            //     $table->string('pay_method')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'no_of_installments')) {
            //     $table->string('no_of_installments')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'currency_code')) {
            //     $table->string('currency_code')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'today_currency')) {
            //     $table->string('today_currency')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'premium_payment_term')) {
            //     $table->string('premium_payment_term')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'class_group')) {
            //     $table->string('class_group')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'classcode')) {
            //     $table->string('classcode')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'insured_name')) {
            //     $table->string('insured_name')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'fac_date_offered')) {
            //     $table->date('fac_date_offered')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'sum_insured_type')) {
            //     $table->string('sum_insured_type')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'total_sum_insured')) {
            //     $table->string('total_sum_insured')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'apply_eml')) {
            //     $table->string('apply_eml')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'eml_rate')) {
            //     $table->decimal('eml_rate', 8, 2)->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'eml_amt')) {
            //     $table->decimal('eml_amt')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'effective_sum_insured')) {
            //     $table->decimal('effective_sum_insured')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'risk_details')) {
            //     $table->string('risk_details')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'cede_premium')) {
            //     $table->string('cede_premium')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'rein_premium')) {
            //     $table->string('rein_premium')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'fac_share_offered')) {
            //     $table->decimal('fac_share_offered', 5, 2)->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'comm_rate')) {
            //     $table->string('comm_rate')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'comm_amt')) {
            //     $table->string('comm_amt')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'reins_comm_type')) {
            //     $table->string('reins_comm_type')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'reins_comm_amt')) {
            //     $table->decimal('reins_comm_amt')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'brokerage_comm_type')) {
            //     $table->string('brokerage_comm_type')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'brokerage_comm_amt')) {
            //     $table->decimal('brokerage_comm_amt')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'brokerage_comm_rate')) {
            //     $table->string('brokerage_comm_rate')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'vat_charged')) {
            //     $table->decimal('vat_charged')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'limit_per_reinclass')) {
            //     $table->string('limit_per_reinclass')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'layer_no')) {
            //     $table->json('layer_no')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'nonprop_reinclass')) {
            //     $table->json('nonprop_reinclass')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'nonprop_reinclass_desc')) {
            //     $table->json('nonprop_reinclass_desc')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'indemnity_treaty_limit')) {
            //     $table->decimal('indemnity_treaty_limit')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'underlying_limit')) {
            //     $table->decimal('underlying_limit')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'engage_type')) {
            //     $table->string('engage_type')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'effective_date')) {
            //     $table->date('effective_date')->nullable();
            // }

            // if (!Schema::hasColumn('pipeline_opportunities', 'closing_date')) {
            //     $table->date('closing_date')->nullable();
            // }

            // // Prospect Owner column
            // if (!Schema::hasColumn('pipeline_opportunities', 'lead_owner')) {
            //     $table->string('lead_owner')->nullable();
            // }
        // });

    }

    public function down()
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->dropColumn([
                'prospect',
                'type_of_bus',
                'lead_year',
                'prod_cost',
                'branchcode',
                'broker_flag',
                'brokercode',
                'division',
                'pay_method',
                'no_of_installments',
                'currency_code',
                'today_currency',
                'premium_payment_term',
                'class_group',
                'classcode',
                'insured_name',
                'fac_date_offered',
                'sum_insured_type',
                'total_sum_insured',
                'apply_eml',
                'eml_rate',
                'eml_amt',
                'effective_sum_insured',
                'risk_details',
                'cede_premium',
                'rein_premium',
                'fac_share_offered',
                'comm_rate',
                'comm_amt',
                'reins_comm_type',
                'reins_comm_amt',
                'brokerage_comm_type',
                'brokerage_comm_amt',
                'brokerage_comm_rate',
                'vat_charged',
                'limit_per_reinclass',
                'layer_no',
                'nonprop_reinclass',
                'nonprop_reinclass_desc',
                'indemnity_treaty_limit',
                'underlying_limit',
                'engage_type',
                'effective_date',
                'closing_date',
                'lead_owner'
            ]);
        });
    }
}
