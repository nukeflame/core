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
        Schema::table('cover_installments', function (Blueprint $table) {
            $table->id();
            // $table->string('layer_no', 3)->nullable();
            $table->unsignedBigInteger('partner_no')->nullable();
            $table->string('dr_cr', 2)->nullable();
            $table->timestamp('deleted_at')->nullable();
            // $table->unique(['cover_no', 'endorsement_no', 'installment_no', 'layer_no', 'dr_cr'], 'cover_installments_composite_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_installments', function (Blueprint $table) {
            // $table->dropUnique('cover_installments_composite_unique');
            $table->dropColumn(['dr_cr', 'partner_no', 'deleted_at', 'id']);
        });
    }
};
