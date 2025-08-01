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
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('company_id');
            $table->string('company_name', 100);
            $table->unsignedSmallInteger('subscription_code');
            $table->string('postal_address', 80);
            $table->string('postal_code', 50);
            $table->string('postal_city', 50);
            $table->char('status', 1);
            $table->string('country_code', 3);
            $table->unsignedBigInteger('contact_code');

            // Add primary key constraint
            // $table->primary('company_id');

            // You can add more columns or constraints here if needed

            // $table->timestamps(); // If you want to include timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
