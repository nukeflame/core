<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('claim_rein_notes', function (Blueprint $table) {
            if (DB::getDriverName() === 'pgsql') {
                $indexExists = DB::table('pg_indexes')
                    ->where('tablename', 'claim_rein_notes')
                    ->where('indexname', 'claim_rein_notes_pkey')
                    ->exists();

                if ($indexExists) {
                    DB::statement('ALTER TABLE claim_rein_notes DROP CONSTRAINT claim_rein_notes_pkey');
                }
            }
            $table->id()->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_rein_notes', function (Blueprint $table) {
            DB::statement('ALTER TABLE claim_rein_notes DROP CONSTRAINT claim_rein_notes_pkey');
            $table->dropColumn('id');
        });
    }
};
