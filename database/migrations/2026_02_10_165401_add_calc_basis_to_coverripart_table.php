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
        Schema::table('coverripart', function (Blueprint $table) {
            $table->integer('net_of_tax')->default(0);
            $table->integer('net_of_claims')->default(0);
            $table->integer('net_of_commission')->default(0);
            $table->integer('net_of_premium')->default(0);
            $table->integer('premium_tax')->default(0);
            $table->integer('net_withholding_tax')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverripart', function (Blueprint $table) {
            $table->dropColumn([
                'net_of_tax',
                'net_of_claims',
                'net_of_commission',
                'net_of_premium',
                'premium_tax',
                'net_withholding_tax'
            ]);
        });
    }
};
