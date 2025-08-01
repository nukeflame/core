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
        Schema::table('premium_pay_terms',function(Blueprint $table){
            $table->decimal('premium_payment_days',5,0)->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('premium_pay_terms',function(Blueprint $table){
           $table->dropColumn('premium_payment_days');
        });
    }
};
