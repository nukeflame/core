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
        Schema::table('coverreinlayers',function(Blueprint $table){
            $table->string('reinstatement_type',10)->nullable(true);
            $table->decimal('reinstatement_value',20,2)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverreinlayers',function(Blueprint $table){
           $table->dropColumn('reinstatement_type');
           $table->dropColumn('reinstatement_value');
        });
    }
};
