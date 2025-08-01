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
        Schema::table('claim_ntf_perils', function (Blueprint $table) {
            $table->id();
            $table->unique('id');
            $table->double('gross_premium', 20, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_ntf_perils', function (Blueprint $table) {
            $table->dropColumn(['id', 'gross_premium']);
            $table->dropUnique(['id']);
        });
    }
};
