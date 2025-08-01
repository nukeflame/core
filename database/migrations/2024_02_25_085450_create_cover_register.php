<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cover_register', function (Blueprint $table) {
            $table->bigIncrements('cover_serial_no');
            $table->unsignedBigInteger('customer_id');
            $table->string('cover_no', 20);
            $table->string('cover_title', 200);
            $table->char('cancelled', 1)->default('N');
            $table->string('endorsement_no', 20);
            $table->string('transaction_type', 3);
            $table->unsignedSmallInteger('branch_code');
            $table->string('broker_code', 10);
            $table->unsignedSmallInteger('cover_type');
            $table->string('type_of_bus', 3);
            $table->string('class_group_code', 3)->nullable();
            $table->string('class_code', 4)->nullable();
            $table->string('insured_name', 150)->nullable();
            $table->date('cover_from');
            $table->date('cover_to');
            $table->string('binder_cov_no', 20)->nullable();
            $table->tinyInteger('no_of_installments')->default(1);
            $table->string('pay_method_code', 10)->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->date('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 20)->nullable();
            $table->dateTime('dola')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('currency_rate', 8)->nullable();
            $table->string('type_of_sum_insured', 5)->nullable();
            $table->unsignedBigInteger('offer_share')->nullable();
            $table->unsignedBigInteger('total_sum_insured')->nullable();
            $table->unsignedBigInteger('cedant_premium')->nullable();
            $table->unsignedBigInteger('rein_premium')->nullable();
            $table->unsignedBigInteger('cedant_comm_rate')->nullable();
            $table->unsignedBigInteger('rein_comm_rate')->nullable();
            $table->unsignedBigInteger('cedant_comm_amount')->nullable();
            $table->unsignedBigInteger('rein_comm_amount')->nullable();
            $table->unsignedBigInteger('eml_rate')->nullable();
            $table->unsignedBigInteger('eml_amount')->nullable();
            $table->unsignedBigInteger('vat_charged')->nullable();
            $table->string('division_code', 3)->nullable();
            $table->string('treaty_type', 4)->nullable();
            $table->date('date_offered')->nullable();
            $table->unsignedSmallInteger('share_offered')->default(0);
            $table->unsignedSmallInteger('port_prem_rate')->default(0);
            $table->unsignedSmallInteger('port_loss_rate')->default(0);
            $table->unsignedSmallInteger('profit_comm_rate')->default(0);
            $table->unsignedSmallInteger('mgnt_exp_rate')->default(0);
            $table->unsignedSmallInteger('deficit_yrs')->default(0);
            $table->unsignedBigInteger('estimated_income')->default(0);
            $table->unsignedBigInteger('cashloss_limit')->default(0);
            $table->unsignedBigInteger('quota_share_total_limit')->default(0);
            $table->unsignedSmallInteger('retention_per')->default(0);
            $table->unsignedBigInteger('retention_amt')->default(0);
            $table->unsignedSmallInteger('no_of_lines')->default(0);
            $table->unsignedSmallInteger('treaty_reice')->default(0);
            $table->unsignedBigInteger('treaty_limit')->default(0);
            $table->unsignedSmallInteger('quake_comm_rate')->default(0);
            $table->unsignedSmallInteger('treaty_comm_rate')->default(0);
            $table->unsignedBigInteger('indemnity_limit')->default(0);
            $table->unsignedBigInteger('underlying_limit')->default(0);
            $table->unsignedBigInteger('egnpi')->default(0);
            $table->string('method', 1)->nullable();
            $table->unsignedBigInteger('min_bc_rate')->nullable();
            $table->unsignedBigInteger('max_bc_rate')->nullable();
            $table->unsignedBigInteger('flat_rate')->nullable();
            $table->unsignedBigInteger('upper_adj')->nullable();
            $table->unsignedBigInteger('lower_adj')->nullable();
            $table->unsignedBigInteger('min_deposit')->nullable();
            $table->string('deposit_frequency', 1)->nullable();
            $table->char('status', 1)->default('A');
            $table->string('verified',1)->nullable();

            // You can add more columns or constraints here if needed

            // $table->timestamps(); // If you want to include timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_register');
    }
};
