<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('stage_documents') || !Schema::hasColumn('stage_documents', 'category_type')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE stage_documents ALTER COLUMN category_type TYPE TEXT USING category_type::TEXT');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('stage_documents') || !Schema::hasColumn('stage_documents', 'category_type')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE stage_documents ALTER COLUMN category_type TYPE INTEGER USING (CASE WHEN category_type ~ '[0-9]+' THEN ((regexp_match(category_type, '([0-9]+)'))[1])::INTEGER ELSE NULL END)");
        }
    }
};
