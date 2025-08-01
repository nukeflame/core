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
        Schema::table('cover_register', function (Blueprint $table) {
            $table->decimal('share_offered', 10, 4)->change();
            $table->decimal('port_prem_rate', 10, 4)->change();
            $table->decimal('port_loss_rate', 10, 4)->change();
            $table->decimal('profit_comm_rate', 10, 4)->change();
            $table->decimal('mgnt_exp_rate', 10, 4)->change();
            $table->decimal('retention_per', 10, 4)->change();
            $table->decimal('treaty_reice', 10, 4)->change();
            $table->decimal('quake_comm_rate', 10, 4)->change();
            $table->decimal('treaty_comm_rate', 10, 4)->change();
            $table->decimal('offer_share', 25, 5)->nullable();
            $table->decimal('total_sum_insured', 25, 5)->change();
            $table->decimal('cedant_premium', 25, 5)->change();
            $table->decimal('rein_premium', 25, 5)->change();
            $table->decimal('cedant_comm_rate', 25, 5)->change();
            $table->decimal('rein_comm_rate', 25, 5)->change();
            $table->decimal('cedant_comm_amount', 25, 5)->change();
            $table->decimal('rein_comm_amount', 25, 5)->change();
            $table->decimal('eml_rate', 25, 5)->change();
            $table->decimal('eml_amount', 25, 5)->change();
            $table->decimal('vat_charged', 25, 5)->change();
            $table->decimal('estimated_income', 25, 5)->change();
            $table->decimal('cashloss_limit', 25, 5)->change();
            $table->decimal('quota_share_total_limit', 25, 5)->change();
            $table->decimal('retention_amt', 25, 5)->change();
            $table->decimal('treaty_limit', 25, 5)->change();
            $table->decimal('indemnity_limit', 25, 5)->change();
            $table->decimal('underlying_limit', 25, 5)->change();
            $table->decimal('egnpi', 25, 5)->change();
            $table->decimal('min_bc_rate', 25, 5)->change();
            $table->decimal('max_bc_rate', 25, 5)->change();
            $table->decimal('flat_rate', 25, 5)->change();
            $table->decimal('upper_adj', 25, 5)->change();
            $table->decimal('lower_adj', 25, 5)->change();
            $table->decimal('min_deposit', 25, 5)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_register', function (Blueprint $table) {
            $table->integer('share_offered')->change();
            $table->integer('port_prem_rate')->change();
            $table->integer('port_loss_rate')->change();
            $table->integer('profit_comm_rate')->change();
            $table->integer('mgnt_exp_rate')->change();
            $table->integer('retention_per')->change();
            $table->integer('treaty_reice')->change();
            $table->integer('quake_comm_rate')->change();
            $table->integer('treaty_comm_rate')->change();
            $table->decimal('offer_share', 25, 5)->nullable();
            $table->bigInteger('total_sum_insured')->change();
            $table->bigInteger('cedant_premium')->change();
            $table->bigInteger('rein_premium')->change();
            $table->bigInteger('cedant_comm_rate')->change();
            $table->bigInteger('rein_comm_rate')->change();
            $table->bigInteger('cedant_comm_amount')->change();
            $table->bigInteger('rein_comm_amount')->change();
            $table->bigInteger('eml_rate')->change();
            $table->bigInteger('eml_amount')->change();
            $table->bigInteger('vat_charged')->change();
            $table->bigInteger('estimated_income')->change();
            $table->bigInteger('cashloss_limit')->change();
            $table->bigInteger('quota_share_total_limit')->change();
            $table->bigInteger('retention_amt')->change();
            $table->bigInteger('treaty_limit')->change();
            $table->bigInteger('indemnity_limit')->change();
            $table->bigInteger('underlying_limit')->change();
            $table->bigInteger('egnpi')->change();
            $table->bigInteger('min_bc_rate')->change();
            $table->bigInteger('max_bc_rate')->change();
            $table->bigInteger('flat_rate')->change();
            $table->bigInteger('upper_adj')->change();
            $table->bigInteger('lower_adj')->change();
            $table->bigInteger('min_deposit')->change();
        });
    }
};
