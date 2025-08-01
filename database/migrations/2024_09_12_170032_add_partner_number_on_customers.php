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
            $table->string('partner_number',20)->nullable(true);
            $table->text('partner_number_errors')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers',function(Blueprint $table){
           $table->dropColumn('partner_number');
           $table->dropColumn('partner_number_errors');
        });
    }
};
