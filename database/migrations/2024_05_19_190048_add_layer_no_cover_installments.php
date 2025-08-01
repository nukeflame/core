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
        Schema::table('cover_installments',function(Blueprint $table){
            $table->dropUnique('cover_installments_1');
            // $table->dropUnique('cover_installments_2');
        });

        Schema::table('cover_installments',function(Blueprint $table){
            $table->string('layer_no',3)->nullable(true);
        });

        Schema::table('cover_premiums',function(Blueprint $table){
            $table->string('layer_no',3)->nullable(true);
            $table->integer('installment_no')->nullable(true);
        });

        Schema::table('cover_installments',function(Blueprint $table){
            $table->unique(['cover_no', 'endorsement_no', 'installment_no','layer_no'], 'cover_installments_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_installments',function(Blueprint $table){
            $table->dropUnique('cover_installments_1');
        });

        Schema::table('cover_premiums',function(Blueprint $table){
            $table->dropColumn('layer_no');
            // $table->dropColumn('installment_no');
         });

        Schema::table('cover_installments',function(Blueprint $table){
            $table->dropColumn('layer_no');
         });
    }
};
