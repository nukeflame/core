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
        Schema::create('glmastbal', function (Blueprint $table) {
            // $table->id();
            $table->string('account_number', 8);
            $table->integer('account_year');
            $table->integer('account_month');
            $table->bigInteger('year_opening_bal');
            $table->bigInteger('period_opening_bal');
            $table->bigInteger('ytd_debits');
            $table->bigInteger('ytd_credits');
            $table->bigInteger('ytd_bal');
            $table->bigInteger('period_debits');
            $table->bigInteger('period_credits');
            $table->bigInteger('ytd_closing_bal');
            $table->bigInteger('period_closing_bal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glmastbal');
    }
};
