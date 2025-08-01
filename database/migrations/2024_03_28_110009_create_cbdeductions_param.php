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
        Schema::create('cbdeductions_param', function (Blueprint $table) {
            $table->string('doc_type', 3);
            $table->string('deduction_code', 3);
            $table->string('deduction_name', 100);
            $table->string('percentage_flag', 1); //Y/N (Yes/No)
            $table->decimal('percentage', 8,4);
            $table->decimal('default_amount', 20,2);
            $table->string('percentage_basis', 1); //N/G (Nett/Gross)
            $table->string('add_deduct', 1); //A/D (Add/Deduct)
            $table->string('account_no', 8); //Pick from coa_config where segment_code=COD it should be optional
            $table->dateTime('created_date');
            $table->string('created_by', 20);
            $table->unique(['doc_type','deduction_code'], 'cbdeductions_param_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbdeductions_param');
    }
};
