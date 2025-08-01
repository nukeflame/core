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
            $table->decimal('prem_tax_rate',8,5)->default(0);
            $table->decimal('ri_tax_rate',8,5)->default(0);
            $table->string('account_year',4);
            $table->string('account_month',2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_register',function(Blueprint $table){
            $table->dropColumn('prem_tax_rate');
            $table->dropColumn('ri_tax_rate');
            $table->dropColumn('account_year');
            $table->dropColumn('account_month');
         });
    }
};
