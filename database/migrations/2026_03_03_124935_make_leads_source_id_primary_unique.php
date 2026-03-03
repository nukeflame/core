<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('leads_source')) {
            return;
        }

        if (!Schema::hasColumn('leads_source', 'id')) {
            DB::statement('ALTER TABLE leads_source ADD COLUMN id BIGINT');
        }

        DB::statement('CREATE SEQUENCE IF NOT EXISTS leads_source_id_seq');
        DB::statement("ALTER TABLE leads_source ALTER COLUMN id SET DEFAULT nextval('leads_source_id_seq')");

        DB::statement("
            UPDATE leads_source
            SET id = nextval('leads_source_id_seq')
            WHERE id IS NULL
        ");

        DB::statement("
            WITH duplicate_ids AS (
                SELECT ctid, ROW_NUMBER() OVER (PARTITION BY id ORDER BY ctid) AS rn
                FROM leads_source
                WHERE id IS NOT NULL
            )
            UPDATE leads_source ls
            SET id = nextval('leads_source_id_seq')
            FROM duplicate_ids di
            WHERE ls.ctid = di.ctid
              AND di.rn > 1
        ");

        DB::statement("
            SELECT setval(
                'leads_source_id_seq',
                COALESCE((SELECT MAX(id) FROM leads_source), 1),
                true
            )
        ");

        DB::statement('ALTER TABLE leads_source DROP CONSTRAINT IF EXISTS leads_source_pkey');
        DB::statement('ALTER TABLE leads_source ADD CONSTRAINT leads_source_pkey PRIMARY KEY (id)');
    }

    public function down(): void
    {
        if (!Schema::hasTable('leads_source') || !Schema::hasColumn('leads_source', 'id')) {
            return;
        }

        DB::statement('ALTER TABLE leads_source DROP CONSTRAINT IF EXISTS leads_source_pkey');
        DB::statement('ALTER TABLE leads_source ALTER COLUMN id DROP DEFAULT');
    }
};
