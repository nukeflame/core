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
        Schema::create('bd_cedant_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('opportunity_id');
            $table->unsignedBigInteger('customer_contact_id')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('mobile_number')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_cc_email')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bd_cedant_contacts');
    }
};
