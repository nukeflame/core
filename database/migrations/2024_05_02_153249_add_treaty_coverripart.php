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
        Schema::table('coverripart',function(Blueprint $table){
            $table->string('treaty_code',4)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverripart',function(Blueprint $table){
            $table->dropColumn('treaty_code',4);
        });
        
    }
};
