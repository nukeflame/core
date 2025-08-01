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
            // Drop the constraint
            $table->dropUnique('cover_premiums_1');
            $table->unique(['cover_no', 'endorsement_no', 'class_code', 'quarter','treaty', 'transaction_type', 'premium_type_code', 'entry_type_descr','layer_no','installment_no'], 'cover_premiums_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_premiums',function(Blueprint $table){
            $table->unique(['cover_no', 'endorsement_no', 'class_code','treaty', 'transaction_type', 'premium_type_code', 'entry_type_descr'], 'cover_premiums_1');
            $table->dropUnique(['cover_no', 'endorsement_no', 'class_code', 'quarter','treaty', 'transaction_type', 'premium_type_code', 'entry_type_descr','layer_no','installment_no'], 'cover_premiums_1');
        });
    }
};
