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
        Schema::table('endorsement_narration', function (Blueprint $table) {
            $table->decimal('endorsed_sum_insured', 15, 2)->nullable();
            $table->decimal('endorsed_cede_premium', 15, 2)->nullable();
            $table->decimal('endorsed_rein_premium', 15, 2)->nullable();
            $table->decimal('new_sum_insured', 15, 2)->nullable();
            $table->decimal('new_cede_premium', 15, 2)->nullable();
            $table->decimal('fac_shared', 15, 2)->nullable();
            $table->decimal('new_rein_premium', 15, 2)->nullable();
            $table->string('sum_insured_type')->nullable();
            $table->timestamp('deleted_at')->nullable()->after('sum_insured_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('endorsement_narration', function (Blueprint $table) {
            $table->dropColumn([
                'endorsed_sum_insured',
                'endorsed_cede_premium',
                'deleted_at',
                'sum_insured_type',
                'endorsed_rein_premium',
                'new_sum_insured',
                'new_cede_premium',
                'new_rein_premium',
                'fac_shared'
            ]);
        });
    }
};
//
