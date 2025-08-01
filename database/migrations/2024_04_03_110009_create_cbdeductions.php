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
        Schema::create('cbdeductions', function (Blueprint $table) {
            $table->string('reference_no', 20);
            $table->string('cb_source_code', 5);
            $table->string('deduction_code', 3);
            $table->string('deduction_name', 100);
            $table->decimal('foreign_amount', 20,2);
            $table->decimal('local_amount', 20,2);
            $table->string('add_deduct', 1); //A/D (Add/Deduct)
            $table->string('account_no', 8); //Pick from coa_config where segment_code=COD it should be optional
            $table->dateTime('created_date');
            $table->string('created_by', 20);
            $table->unique(['reference_no','cb_source_code','deduction_code'], 'cbdeductions_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbdeductions');
    }
};
