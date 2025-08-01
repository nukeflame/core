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
        Schema::table('cover_debit',function(Blueprint $table){
            $table->text('gl_updated_errors')->change()->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_debit',function(Blueprint $table){
           $table->string('gl_updated_errors',250)->change();
        });
    }
};
