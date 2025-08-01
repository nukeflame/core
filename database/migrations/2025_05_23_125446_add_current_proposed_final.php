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
        Schema::table('quote_schedules', function (Blueprint $table) {
            $table->string('current',20)->nullable()->after('details');
            $table->string('proposed',20)->nullable()->after('current');
            $table->string('final',20)->nullable()->after('proposed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_schedules', function (Blueprint $table) {
            $table->dropColumn('current');
            $table->dropColumn('proposed');
            $table->dropColumn('final');
        });
    }
};
