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
        Schema::table('cover_risk', function (Blueprint $table) {
            if (Schema::hasColumn('cover_risk', 'address')) {
                $table->dropColumn(['address']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_risk', function (Blueprint $table) {
            $table->string('address', 200)->nullable();
        });
    }
};
