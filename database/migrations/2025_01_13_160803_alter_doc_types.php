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
        DB::statement('ALTER TABLE doc_types ALTER COLUMN id SET DATA TYPE BIGINT;');

        // Create the sequence if it doesn't exist
        DB::statement("
        DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_class WHERE relname = 'doc_types_id_seq') THEN
                CREATE SEQUENCE doc_types_id_seq START 25;
            END IF;
        END
        $$;
    ");

        // Set the default value to auto-increment using the sequence
        DB::statement("ALTER TABLE doc_types ALTER COLUMN id SET DEFAULT nextval('doc_types_id_seq');");

        // Update the sequence to start from the current max id value or 25
        DB::statement("
        SELECT setval('doc_types_id_seq', COALESCE((SELECT MAX(id) FROM doc_types), 25));
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE doc_types ALTER COLUMN id DROP DEFAULT;');
    }
};
