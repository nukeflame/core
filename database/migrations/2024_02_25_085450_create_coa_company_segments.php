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
        Schema::create('coa_company_segments', function (Blueprint $table) {
            $table->string('segment_code', 3)->primary();
            $table->string('segment_name', 50);
            $table->tinyInteger('segment_length');
            $table->tinyInteger('segment_position');
            
            // You can add more columns or constraints here if needed
            
            $table->timestamps(); // If you want to include timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_company_segments');
    }
};
