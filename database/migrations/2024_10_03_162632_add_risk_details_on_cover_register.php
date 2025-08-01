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
        Schema::table('cover_register',function(Blueprint $table){
            $table->string('risk_details',500)->nullable(true);
            $table->date('effective_date')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_register',function(Blueprint $table){
           $table->dropColumn('risk_details');
           $table->dropColumn('effective_date');
        });
    }
};
