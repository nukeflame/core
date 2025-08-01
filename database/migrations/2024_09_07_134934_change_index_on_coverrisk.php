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
        Schema::table('cover_risk', function (Blueprint $table) {
            // Drop the primary key index named 'cover_risk_pkey'
            $table->dropPrimary('cover_risk_pkey');

            // Add a new index named 'cover_risk_1' on 'endorsement_no' and 'id'
            $table->index(['endorsement_no', 'id'], 'cover_risk_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_risk', function (Blueprint $table) {
            // Drop the index 'cover_risk_1'
            $table->dropIndex('cover_risk_1');

            // Re-add the primary key index on 'id' field with name 'cover_risk_pkey'
            $table->primary('id', 'cover_risk_pkey');
        });
    }
};