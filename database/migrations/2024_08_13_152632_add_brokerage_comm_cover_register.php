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
            $table->string('brokerage_comm_type',5)->nullable(true);
            $table->decimal('brokerage_comm_amt',20,2)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_register',function(Blueprint $table){
           $table->dropColumn('brokerage_comm_type');
           $table->dropColumn('brokerage_comm_amt');
        });
    }
};
