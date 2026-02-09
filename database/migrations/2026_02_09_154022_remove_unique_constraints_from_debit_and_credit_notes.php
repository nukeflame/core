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
        Schema::table('debit_notes', function (Blueprint $table) {
            // Drop unique constraints for cover_no and endorsement_no
            $table->dropUnique('dn_cover_endorsement_unique');
            $table->dropUnique('debit_notes_endorsement_no_unique');
        });

        Schema::table('credit_notes', function (Blueprint $table) {
            // Drop unique constraints for cover_no and endorsement_no
            // Note: This was updated in a previous migration to include reinsurer_id
            $table->dropUnique('cn_cover_endorsement_reinsurer_unique');
            
            // Check if individual endorsement_no unique exists (it might not)
            // Based on research, it doesn't seem to exist on credit_notes currently, 
            // but we'll use dropUnique if it was originally there.
            // Actually, let's only drop what we found exists to avoid errors.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->unique(['cover_no', 'endorsement_no'], 'dn_cover_endorsement_unique');
            $table->unique('endorsement_no', 'debit_notes_endorsement_no_unique');
        });

        Schema::table('credit_notes', function (Blueprint $table) {
            // Restore the constraint that included reinsurer_id
            $table->unique(['cover_no', 'endorsement_no', 'reinsurer_id'], 'cn_cover_endorsement_reinsurer_unique');
        });
    }
};
