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
        Schema::create('currency', function (Blueprint $table) {
            $table->string('country_iso', 3);
            $table->string('currency_code', 3);
            $table->string('currency_name', 80);
            $table->char('base_currency', 1)->default('N');
            $table->char('status', 1)->default('Y');

            // Primary key
            $table->primary(['country_iso', 'currency_code']);
            
            // You can add more columns or constraints here if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency');
    }
};
