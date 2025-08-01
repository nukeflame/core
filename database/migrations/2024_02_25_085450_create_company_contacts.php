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
        Schema::create('company_contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id');
            $table->string('contact_name', 100);
            $table->string('contact_position', 30);
            $table->string('contact_mobile_no', 20);
            $table->string('contact_email', 100);
            
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
        Schema::dropIfExists('company_contacts');
    }
};
