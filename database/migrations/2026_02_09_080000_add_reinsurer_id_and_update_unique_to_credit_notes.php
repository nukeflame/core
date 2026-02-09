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
        Schema::table('credit_notes', function (Blueprint $table) {
            // Add reinsurer_id field
            if (!Schema::hasColumn('credit_notes', 'reinsurer_id')) {
                $table->string('reinsurer_id', 50)->nullable()->index();
            }

            // Drop the index created in the previous migration if it exists
            // $table->index(['cover_no', 'endorsement_no'], 'cn_cover_endorsement_idx');
            $table->dropIndex('cn_cover_endorsement_idx');

            // Create the new composite unique constraint
            $table->unique(['cover_no', 'endorsement_no', 'reinsurer_id'], 'cn_cover_endorsement_reinsurer_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropUnique('cn_cover_endorsement_reinsurer_unique');
            
            // Re-create the index from the previous migration
            $table->index(['cover_no', 'endorsement_no'], 'cn_cover_endorsement_idx');

            if (Schema::hasColumn('credit_notes', 'reinsurer_id')) {
                $table->dropColumn('reinsurer_id');
            }
        });
    }
};
