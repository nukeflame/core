<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customeracc_det', function (Blueprint $table) {
            $table->string('portfolio_type', 3)->default('IN');
            $table->date('posting_date')->nullable();
            $table->decimal('port_prem_rate', 10, 4)->default(0);
            $table->decimal('port_premium_amt', 18, 2)->default(0);
            $table->decimal('port_loss_rate', 10, 4)->default(0);
            $table->decimal('port_outstanding_loss_amt', 18, 2)->default(0);
            $table->decimal('port_prem_amt', 18, 2)->default(0);
            $table->decimal('port_loss_amt', 18, 2)->default(0);
            $table->text('comments')->nullable();
            $table->boolean('show_cedant')->default(true);
            $table->boolean('show_reinsurer')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('customeracc_det', function (Blueprint $table) {
            $table->dropColumn([
                'portfolio_type',
                'posting_date',
                'port_prem_rate',
                'port_premium_amt',
                'port_loss_rate',
                'port_outstanding_loss_amt',
                'port_prem_amt',
                'port_loss_amt',
                'comments',
                'show_cedant',
                'show_reinsurer',
            ]);
        });
    }
};
