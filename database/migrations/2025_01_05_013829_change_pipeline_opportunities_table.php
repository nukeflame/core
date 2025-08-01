<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {

            $table->decimal('total_sum_insured', 20, 2)->nullable();
            $table->decimal('eml_rate', 8, 2)->nullable();
            $table->decimal('eml_amt', 20, 2)->nullable();
            $table->decimal('effective_sum_insured', 20, 2)->nullable();
            $table->decimal('cede_premium', 20, 2)->nullable();
            $table->decimal('rein_premium', 20, 2)->nullable();
            $table->decimal('comm_amt', 20, 2)->nullable();
            $table->decimal('reins_comm_amt', 20, 2)->nullable();
            $table->decimal('brokerage_comm_amt', 20, 2)->nullable();
            $table->decimal('vat_charged', 20, 2)->nullable();
            $table->decimal('indemnity_treaty_limit', 20, 2)->nullable();
            $table->decimal('underlying_limit', 20, 2)->nullable();
            $table->json('layer_no')->nullable();
            $table->json('nonprop_reinclass')->nullable();
            $table->json('nonprop_reinclass_desc')->nullable();
            $table->decimal('engage_type', 4, 0)->nullable()->change();
            $table->string('prospect', 255)->nullable();
            $table->string('type_of_bus', 255)->nullable();
            $table->string('branchcode', 50)->nullable();
            $table->string('broker_flag', 1)->nullable();
            $table->string('brokercode', 50)->nullable();
            $table->string('pay_method', 50)->nullable();
            $table->integer('no_of_installments')->nullable();
            $table->string('currency_code', 50)->nullable();
            $table->string('today_currency', 50)->nullable();
            $table->string('premium_payment_term', 50)->nullable();
            $table->string('class_group', 50)->nullable();
            $table->string('classcode', 50)->nullable();
            $table->string('insured_name', 255)->nullable();
            $table->date('fac_date_offered')->nullable();
            $table->string('sum_insured_type', 50)->nullable();
            $table->string('apply_eml', 1)->nullable();
            $table->string('risk_details', 255)->nullable();
            $table->string('limit_per_reinclass', 50)->nullable();
            $table->decimal('fac_share_offered', 8, 4)->nullable();
            $table->decimal('comm_rate', 8, 4)->nullable();
            $table->string('reins_comm_type', 50)->nullable();
            $table->string('brokerage_comm_type', 50)->nullable();
            $table->string('brokerage_comm_rate', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->dropColumn([
                'total_sum_insured',
                'eml_rate',
                'eml_amt',
                'effective_sum_insured',
                'cede_premium',
                'rein_premium',
                'comm_amt',
                'reins_comm_amt',
                'brokerage_comm_amt',
                'vat_charged',
                'indemnity_treaty_limit',
                'underlying_limit',
                'layer_no',
                'nonprop_reinclass',
                'nonprop_reinclass_desc',
                // 'narration',
                'engage_type',
                'prospect',
                'type_of_bus',
                'branchcode',
                'broker_flag',
                'brokercode',
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
                'apply_eml',
                'risk_details',
                'limit_per_reinclass',
                'fac_share_offered',
                'comm_rate',
                'reins_comm_type',
                'brokerage_comm_type',
                'brokerage_comm_rate'
            ]);
        });
    }
};
