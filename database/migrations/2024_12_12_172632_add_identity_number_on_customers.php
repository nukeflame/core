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
        Schema::table('customers',function(Blueprint $table){
            $table->string('identity_number_type',10)->nullable(true);
            $table->string('identity_number',50)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers',function(Blueprint $table){
           $table->dropColumn('identity_number_type');
           $table->dropColumn('identity_number');
        });
    }
};
