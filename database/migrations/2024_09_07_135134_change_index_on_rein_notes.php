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
        Schema::table('rein_notes', function (Blueprint $table) {
            // Drop the primary key index named 'rein_notes_pkey'
            $table->dropPrimary('rein_notes_pkey');

            // Add a new index named 'rein_notes_1' on 'endorsement_no' and 'tran_no'
            $table->index(['endorsement_no', 'tran_no'], 'rein_notes_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rein_notes', function (Blueprint $table) {
            // Drop the index 'rein_notes_1'
            $table->dropIndex('rein_notes_1');

            // Re-add the primary key index on 'tran_no' field with name 'rein_notes_pkey'
            $table->primary('tran_no', 'rein_notes_pkey');
        });
    }
};