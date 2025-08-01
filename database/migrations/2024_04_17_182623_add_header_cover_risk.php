<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cover_risk',function(Blueprint $table){
            $table->decimal('header',2,0);
            $table->longText('details');
            // $table->dropColumn('address');
            $table->foreign('header')->references('id')->on('schedule_headers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_risk',function(Blueprint $table){
            $table->dropColumn('header');
            $table->dropColumn('details');
         });
    }
};
