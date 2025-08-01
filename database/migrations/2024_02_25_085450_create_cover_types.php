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
        Schema::create('cover_type', function (Blueprint $table) {
            $table->unsignedSmallInteger('type_id')->primary();
            $table->string('type_name', 30);
            $table->char('status', 1)->default('A');
            $table->string('short_description', 1);
            
            // You can add more columns or constraints here if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_type');
    }
};
