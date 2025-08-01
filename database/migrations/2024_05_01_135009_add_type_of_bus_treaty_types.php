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
        Schema::table('treaty_types',function(Blueprint $table){
            $table->string('type_of_bus',3)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treaty_types',function(Blueprint $table){
           $table->dropColumn('type_of_bus');
        });
    }
};
