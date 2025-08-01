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
        Schema::create('claim_ntf_perils', function (Blueprint $table) {
            $table->string('intimation_no', 20); 
            $table->decimal('tran_no', 20, 0);
            $table->string('peril_name', 100); 
            $table->string('dr_cr_note_no', 20); 
            $table->string('dr_cr', 3); 
            $table->string('entry_type_descr', 3)->nullable(); 
            $table->decimal('basic_amount', 20, 2);
            $table->decimal('rate', 8, 5);
            $table->decimal('final_amount', 20, 2);
            $table->string('status', 1); 
            $table->integer('account_year');
            $table->integer('account_month');
            $table->string('created_by', 20); 
            $table->string('updated_by', 20); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_ntf_perils');
    }
};
