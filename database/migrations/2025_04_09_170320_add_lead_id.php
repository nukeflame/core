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
    
    
      
            Schema::table('lead_status', function (Blueprint $table) {
                $table->id('lead_id')->first(); 
            });
        
    
    /**
     * Reverse the migrations.
     */
  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_status', function (Blueprint $table) {
            Schema::dropIfExists('lead_id');
        });
    }
};
