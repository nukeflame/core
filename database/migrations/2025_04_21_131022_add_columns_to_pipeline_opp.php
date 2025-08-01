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
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->string('prem_tax_rate',20)->nullable();
            $table->string('ri_tax_rate',20)->nullable();
            $table->string('treaty_code',20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->dropColumn('prem_tax_rate');
            $table->dropColumn('ri_tax_rate');
            $table->dropColumn('treaty_code');
        });
    }
};
