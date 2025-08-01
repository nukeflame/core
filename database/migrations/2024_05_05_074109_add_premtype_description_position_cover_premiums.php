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
            $table->string('premium_type_order_position',2)->nullable(true)->default(1);
            $table->string('premium_type_description',20)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_premiums',function(Blueprint $table){
            $table->dropColumn('premium_type_order_position');
            $table->dropColumn('premium_type_description');
            
        });
    }
};
