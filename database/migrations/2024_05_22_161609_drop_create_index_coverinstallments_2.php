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
            // Drop the constraint
            $table->dropUnique('cover_installments_2');
            $table->unique(['cover_no',  'endorsement_no', 'layer_no','installment_no', 'trans_type', 'entry_type'], 'cover_installments_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_installments',function(Blueprint $table){
            $table->unique(['cover_no', 'installment_no', 'trans_type', 'entry_type'], 'cover_installments_2');
            $table->dropUnique(['cover_no',  'endorsement_no', 'layer_no','installment_no', 'trans_type', 'entry_type'], 'cover_installments_2');
        });
    }
};
