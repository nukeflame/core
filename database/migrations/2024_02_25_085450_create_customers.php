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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('customer_id');
            $table->string('name', 100);
            $table->unsignedInteger('customer_type');
            $table->string('registration_no', 50);
            $table->string('tax_no', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('postal_address', 80)->nullable();
            $table->string('postal_town', 50)->nullable();
            $table->string('city', 20)->nullable();
            $table->string('street', 20)->nullable();
            $table->string('country_iso', 20)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('fax_no', 20)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('financial_rate', 10)->nullable();
            $table->string('agency_rate', 10)->nullable();
            $table->string('status', 1)->nullable();
            $table->string('created_by', 20)->nullable();
            $table->date('startdate')->nullable();
            $table->date('created_at');
            $table->dateTime('update_timestamp')->default(DB::raw('CURRENT_TIMESTAMP'));

            // You can add more columns or constraints here if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
