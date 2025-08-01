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
        Schema::create('branch', function (Blueprint $table) {
            $table->smallInteger('branch_code')->unsigned(); // int(3)
            $table->string('branch_name', 30); // varchar(30)
            $table->char('status', 1); // varchar(1), assuming it's a single character
            
            // Add primary key if necessary
            $table->primary('branch_code');
            
            // You can add more columns or constraints here if needed
            
            // $table->timestamps(); // If you want to include timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch');
    }
};
