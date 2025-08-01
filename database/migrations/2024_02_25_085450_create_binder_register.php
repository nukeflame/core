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
        Schema::create('binder_register', function (Blueprint $table) {
            $table->string('binder_cov_no', 20)->primary();
            $table->string('insured_name', 80);
            $table->string('agency_name', 80);
            $table->date('created_at');
            // Add any additional columns here if needed
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binder_register');
    }
};
