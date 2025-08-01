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
        Schema::table('cbrequisitions', function (Blueprint $table) {
            $table->string('pay_method_code',5)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cbrequisitions', function (Blueprint $table) {
            $table->dropColumn('pay_method_code');
        });
    }
};
