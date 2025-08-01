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
            $table->decimal('total_mdp_amt',20,2)->nullable(true);
            $table->decimal('mdp_amt',20,2)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverripart',function(Blueprint $table){
           $table->dropColumn('mdp_amt');
           $table->dropColumn('mdp_amt');
        });
    }
};
