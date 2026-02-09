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
            // Drop the composite unique constraint
            $table->dropUnique('cn_cover_endorsement_unique');
            
            // Also drop the unique constraint on endorsement_no if it exists separately
            // Based on the original migration: $table->string('endorsement_no', 50)->nullable()->unique();
            $table->dropUnique(['endorsement_no']);
            
            // Convert to normal indexes instead to maintain performance but allow duplicates
            $table->index(['cover_no', 'endorsement_no'], 'cn_cover_endorsement_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropIndex('cn_cover_endorsement_idx');
            $table->unique(['cover_no', 'endorsement_no'], 'cn_cover_endorsement_unique');
            $table->unique(['endorsement_no']);
        });
    }
};
