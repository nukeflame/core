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
            $table->decimal('fronting_rate',8,5)->nullable(true)->default(0);
            $table->decimal('fronting_amt',20,2)->nullable(true)->default(0);
            $table->decimal('brokerage_comm_rate',8,5)->nullable(true)->default(0);
            $table->decimal('brokerage_comm_amt',20,2)->nullable(true)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverripart',function(Blueprint $table){
           $table->dropColumn('fronting_rate');
           $table->dropColumn('fronting_amt');
           $table->dropColumn('brokerage_comm_rate');
           $table->dropColumn('brokerage_comm_amt');
        });
    }
};
