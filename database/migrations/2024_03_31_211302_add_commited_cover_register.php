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
            $table->string('commited',1)->default('N')->nullable(false);
            $table->string('commited_by',20)->nullable(true);
            $table->timestamp('updated_at')->nullable(true);
            $table->string('updated_by',20)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_register',function(Blueprint $table){
            $table->dropColumn('commited');
            $table->dropColumn('commited_by');
            $table->dropColumn('updated_at');
            $table->dropColumn('updated_by');
         });
    }
};
