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
        Schema::table('schedule_headers', function (Blueprint $table) {
            $table->string('opportunity_id')->nullable();
            $table->unsignedBigInteger('schedule_header_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_headers', function (Blueprint $table) {
            $table->dropColumn('schedule_header_id', 'opportunity_id');
        });
    }
};
