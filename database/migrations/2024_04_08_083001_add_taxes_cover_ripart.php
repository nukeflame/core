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
            $table->decimal('prem_tax_rate',8,5)->default(0);
            $table->decimal('prem_tax',20 ,2)->default(0);
            $table->decimal('ri_tax_rate',8,5)->default(0);
            $table->decimal('ri_tax',20,2)->default(0);
            $table->decimal('total_claim_amt',20,2)->default(0);
            $table->decimal('claim_amt',20,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverripart',function(Blueprint $table){
            $table->dropColumn('prem_tax_rate');
            $table->dropColumn('prem_tax');
            $table->dropColumn('ri_tax_rate');
            $table->dropColumn('ri_tax');
            $table->dropColumn('total_claim_amt');
            $table->dropColumn('claim_amt');
         });
    }
};
