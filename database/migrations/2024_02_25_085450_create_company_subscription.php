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
        Schema::create('company_subscription', function (Blueprint $table) {
            $table->unsignedSmallInteger('subscription_code')->primary();
            $table->unsignedBigInteger('company_id');
            $table->dateTime('expiry_date');
            $table->char('locked', 1);
            
            // Add foreign key constraint to company_id referencing companies table
            $table->foreign('company_id')->references('company_id')->on('companies');
            
            // You can add more columns or constraints here if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_subscription');
    }
};
