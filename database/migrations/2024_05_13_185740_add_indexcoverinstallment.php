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
            $table->unique(['cover_no', 'endorsement_no', 'installment_no'], 'cover_installments_1');
            $table->unique(['cover_no', 'installment_no','trans_type','entry_type'], 'cover_installments_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_installments',function(Blueprint $table){
            $table->dropUnique('cover_installments_1');
            $table->dropUnique('cover_installments_2');
        });
    }
};
