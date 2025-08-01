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
        Schema::table('rein_notes',function(Blueprint $table){
            $table->string('item_title',100)->nullable(false)->default(' ');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rein_notes',function(Blueprint $table){
           $table->dropColumn('item_title');
        });
    }
};
