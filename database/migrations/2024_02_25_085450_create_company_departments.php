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
        Schema::create('company_department', function (Blueprint $table) {
            $table->bigInteger('company_id');
            $table->unsignedSmallInteger('department_code')->primary();
            $table->string('department_name');
            $table->string('status', 1);

            $table->foreign('company_id')->references('company_id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_department');
    }
};
