<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->string('class_code', 4)->primary();
            $table->string('class_name', 80);
            $table->char('combined', 1)->default('N');
            $table->string('class_group_code', 3);
            // $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->char('status', 1);
            
            // You can add more columns or constraints here if needed
            
            $table->timestamps(); // If you want to include timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
