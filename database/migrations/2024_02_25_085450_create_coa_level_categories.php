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
        Schema::create('coa_level_categories', function (Blueprint $table) {
            $table->bigIncrements('level_categ_id');
            $table->string('level_categ_name', 100);
            $table->unsignedInteger('level_id');
            $table->unsignedInteger('parent_id');
            $table->char('add_less', 1)->nullable();
            $table->char('dr_cr', 1)->nullable();
            
            // You can add more columns or constraints here if needed
            
            // $table->timestamps(); // If you want to include timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_level_categories');
    }
};
