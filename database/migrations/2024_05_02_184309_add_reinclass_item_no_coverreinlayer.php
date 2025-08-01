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
            $table->string('reinclass',4)->nullable(true);
            $table->string('item_no',3)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverreinlayers',function(Blueprint $table){
           $table->dropColumn('reinclass');
           $table->dropColumn('item_no');
        });
    }
};
