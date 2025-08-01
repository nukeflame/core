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
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->string('effective_date',20)->change();
            $table->string('closing_date',20)->change();
            
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            //
        });
    }
};
