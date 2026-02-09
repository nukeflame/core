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
        Schema::table('customeracc_det', function (Blueprint $table) {
            $table->string('quarter', 4)->nullable()->after('account_month')
                ->comment('Quarter of the posting period (e.g., Q1, Q2, Q3, Q4)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customeracc_det', function (Blueprint $table) {
            $table->dropColumn('quarter');
        });
    }
};
