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
            $table->decimal('comm_rate', 8, 2)->change();
            $table->decimal('reins_comm_rate', 8, 2)->change();
            $table->decimal('fac_share_offered', 8, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->decimal('comm_rate', 8, 4)->change();
            $table->decimal('reins_comm_rate', 8, 4)->change();
            $table->decimal('fac_share_offered', 8, 4)->change();
        });
    }
};
