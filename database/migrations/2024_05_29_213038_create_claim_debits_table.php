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
        Schema::create('claim_debit', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->decimal('dr_no',9,0)->nullable(false);
            $table->string('document',3)->nullable(false);
            $table->unsignedBigInteger('period_year')->nullable(false);
            $table->unsignedBigInteger('period_month')->nullable(false);
            $table->string('cover_no',20)->nullable(false);
            $table->string('endorsement_no',20)->nullable(false);
            $table->string('claim_no',20)->nullable(false);
            $table->decimal('gross',20,2)->nullable(false);
            $table->decimal('net_amt',20,2)->nullable(false);
            $table->decimal('installment',2,0)->nullable(false);
            $table->string('reversed',1)->nullable(false)->default('N');
            $table->decimal('ref_dr_no',9,0)->nullable(true);
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
        Schema::dropIfExists('claim_debit');
    }
};
