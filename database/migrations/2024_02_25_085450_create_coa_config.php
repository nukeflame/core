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
        Schema::create('coa_config', function (Blueprint $table) {
            $table->string('segment_code', 3);
            $table->string('account_number', 8);
            $table->string('description', 150);
            $table->string('dr_cr', 2);
            $table->integer('level_categ_id_1');
            $table->integer('level_categ_id_2');
            $table->integer('level_categ_id_3');
            $table->integer('level_categ_id_4');
            $table->char('bank_flag', 1)->default('N');
            $table->string('created_by', 20);
            $table->timestamp('created_at')->useCurrent();
            $table->char('status', 1);
            
            // Primary key
            $table->primary(['segment_code', 'account_number']);
            
            // You can add more columns or constraints here if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_config');
    }
};
