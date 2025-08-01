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
        Schema::table('cover_reinclass',function(Blueprint $table){
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_reinclass',function(Blueprint $table){
            $table->dropColumn('prem_tax_rate');
            $table->dropColumn('prem_tax');
            $table->dropColumn('ri_tax_rate');
            $table->dropColumn('ri_tax');
         });
    }
};
