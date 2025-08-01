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
        Schema::table('fiscal_years', function (Blueprint $table) {
            $table->double('gross_profit', 15, 2)->nullable();
            $table->double('cost_income_ratio', 5, 2)->nullable();
            $table->double('profit_margin', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fiscal_years', function (Blueprint $table) {
            $table->dropColumn(['gross_profit', 'cost_income_ratio', 'profit_margin']);
        });
    }
};
