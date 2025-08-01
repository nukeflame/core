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
        Schema::create('ar_customers', function (Blueprint $table) {
            $table->string('customer_group', 5)->nullable(false);
            $table->string('customer_id', 50)->nullable(false);
            $table->string('customer_name', 100)->nullable(false);
            $table->string('telephone', 15)->nullable(false);
            $table->string('pin_number', 50)->nullable(false);
            $table->string('registration_no', 50)->nullable(false);
            $table->date('registration_date')->nullable(false);
            $table->string('source_of_income', 100)->nullable(false);
            $table->string('industry', 100)->nullable(false);
            $table->string('postal_address', 80)->nullable(false);
            $table->string('postal_street', 50)->nullable(false);
            $table->string('postal_city', 20)->nullable(false);
            $table->string('country_iso', 3)->nullable(false);
            $table->string('email', 100)->nullable(false);
            $table->string('tax_group', 5)->nullable(false);
            $table->string('contact_name', 80)->nullable(false);
            $table->string('contact_email', 80)->nullable(false);
            $table->string('contact_position', 25)->nullable(false);
            $table->string('contact_mobile_no', 15)->nullable(false);
            $table->string('bank_code', 4)->nullable(false);
            $table->string('bank_branch_code', 4)->nullable(false);
            $table->string('bank_account_no', 30)->nullable(false);
            $table->string('bank_account_name', 100)->nullable(false);
            $table->string('status', 1)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ar_customers');
    }
};
