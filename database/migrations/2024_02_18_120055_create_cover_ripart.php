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
        Schema::create('coverripart', function (Blueprint $table) {
            $table->decimal('tran_no',20,0)->nullable(false)->primary();
            $table->string('cover_no',20)->nullable(false);
            $table->string('endorsement_no',20)->nullable(false);
            $table->decimal('partner_no',11,0)->nullable(false);
            $table->decimal('period_year',4,0)->nullable(false);
            $table->decimal('period_month',2,0)->nullable(false);
            $table->decimal('share',8,5)->nullable(false);
            $table->decimal('written_lines',8,5)->nullable(false);
            $table->decimal('total_sum_insured',20,2)->nullable(false)->default(0);
            $table->decimal('total_premium',20,2)->nullable(false)->default(0);
            $table->decimal('total_commission',20,2)->nullable(false)->default(0);
            $table->decimal('sum_insured',20,2)->nullable(false)->default(0);
            $table->decimal('premium',20,2)->nullable(false)->default(0);
            $table->decimal('comm_rate',8,5)->nullable(false)->default(0);
            $table->decimal('commission',20,2)->nullable(false)->default(0);
            $table->decimal('wht_rate',8,5)->nullable(false)->default(0);
            $table->decimal('wht_amt',20,2)->nullable(false)->default(0);
            $table->string('cancelled',1)->nullable(false)->default('N');
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coverripart');
    }
};
