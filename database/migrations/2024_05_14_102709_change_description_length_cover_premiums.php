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
        Schema::table('cover_premiums',function(Blueprint $table){
            // Change column length
            $table->string('premtype_name',100)->nullable(true)->change();
            $table->string('premium_type_description',100)->nullable(true)->change();
              });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_premiums',function(Blueprint $table){
            $table->string('premtype_name',50)->nullable(true)->change();
            $table->string('premium_type_description',20)->nullable(true)->change();
            });
    }
};
