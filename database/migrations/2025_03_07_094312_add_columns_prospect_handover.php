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
        Schema::table('prospect_handover', function (Blueprint $table) {
            $table->string('excess')->nullable();
            $table->string('max/min')->nullable();
            // $table->string('effective_sum_insured')->nullable();
            $table->string('quote_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_handover', function (Blueprint $table) {
            $table->dropColumn(['excess','max/min','quote_number']);
            
        });
    }
};
