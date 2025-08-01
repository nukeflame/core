<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('prospect_registers', function (Blueprint $table) {
            $table->id();
            $table->string('pipeline_id');
            $table->string('opportunity_id');
            $table->string('branch_code', 100);
            $table->string('broker_code', 100)->default(0);
            $table->string('cover_type', 100);
            $table->string('class_code', 100);
            $table->string('class_group_code', 100);
            $table->string('insured_name', 255);
            $table->date('effective_date');
            $table->date('cover_from');
            $table->date('cover_to');
            $table->string('account_year', 4);
            $table->string('account_month', 2);
            $table->string('binder_cov_no', 100);
            $table->string('pay_method_code', 100);
            $table->string('currency_code', 3);
            $table->decimal('currency_rate', 10, 2);
            $table->string('type_of_sum_insured', 100);
            $table->decimal('rein_premium', 15, 2);
            $table->decimal('total_sum_insured', 15, 2)->default(0);
            $table->decimal('cedant_premium', 15, 2);
            $table->string('apply_eml', 1)->default('N');
            $table->decimal('eml_rate', 8, 4)->default(0);
            $table->decimal('eml_amount', 15, 2)->default(0);
            $table->decimal('effective_sum_insured', 15, 2)->default(0);
            $table->decimal('cedant_comm_rate', 8, 4);
            $table->decimal('cedant_comm_amount', 15, 2);
            $table->string('rein_comm_type', 100);
            $table->decimal('rein_comm_rate', 8, 4)->default(0);
            $table->decimal('brokerage_comm_rate', 8, 4)->default(0);
            $table->decimal('brokerage_comm_amt', 15, 2)->default(0);
            $table->string('brokerage_comm_type', 100);
            $table->decimal('reinsurer_per_treaty', 8, 4);
            $table->decimal('rein_comm_amount', 15, 2);
            $table->string('division_code', 100);
            $table->boolean('vat_charged')->default(false);
            $table->string('treaty_type', 100);
            $table->text('risk_details');
            $table->string('cover_title', 255);
            $table->date('date_offered');
            $table->decimal('share_offered', 8, 4)->default(0);
            $table->string('no_of_installments')->default(0);
            $table->decimal('port_prem_rate', 8, 4)->default(0);
            $table->decimal('port_loss_rate', 8, 4)->default(0);
            $table->decimal('profit_comm_rate', 8, 4)->default(0);
            $table->decimal('mgnt_exp_rate', 8, 4)->default(0);
            $table->decimal('deficit_yrs', 8, 4)->default(0);
            $table->string('deposit_frequency', 100)->default(0);
            $table->decimal('prem_tax_rate', 8, 4)->default(0);
            $table->decimal('ri_tax_rate', 8, 4)->default(0);
            $table->string('status', 1)->default('A');
            $table->string('verified', 100)->nullable();
            $table->timestamps();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_registers');
    }
};
