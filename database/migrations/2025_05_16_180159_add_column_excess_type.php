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
        Schema::table('handover_approvals', function (Blueprint $table) {
            $table->string('excess_type',5)->nullable()->after('excess');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('handover_approvals', function (Blueprint $table) {
            $table->dropColumn('excess_type');
        });
    }
};
