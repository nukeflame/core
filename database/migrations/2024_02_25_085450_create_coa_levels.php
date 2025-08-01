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
        Schema::create('coa_levels', function (Blueprint $table) {
            $table->tinyInteger('level_id')->unsigned()->primary();
            $table->string('name', 30);
            $table->char('status', 1);
            
            // You can add more columns or constraints here if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_levels');
    }
};
