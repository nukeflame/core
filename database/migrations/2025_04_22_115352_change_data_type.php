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
            $table->date('tr_date_offered')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->timestamp('tr_date_offered')->nullable()->change();
        });
    }
};
