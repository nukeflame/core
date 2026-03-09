<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $columns = [
            'title' => 255,
            'description' => 255,
            'file' => 255,
            'mime_type' => 100,
            'created_by' => 100,
            'updated_by' => 100,
        ];

        foreach ($columns as $column => $length) {
            DB::statement("\n                DO $$\n                BEGIN\n                    IF EXISTS (\n                        SELECT 1\n                        FROM information_schema.columns\n                        WHERE table_schema = 'public'\n                          AND table_name = 'cover_attachment'\n                          AND column_name = '{$column}'\n                    ) THEN\n                        EXECUTE 'ALTER TABLE cover_attachment ALTER COLUMN {$column} TYPE VARCHAR({$length}) USING {$column}::text';\n                    END IF;\n                END\n                $$;\n            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [
            'title' => 50,
            'description' => 50,
            'file' => 50,
            'mime_type' => 50,
            'created_by' => 50,
            'updated_by' => 50,
        ];

        foreach ($columns as $column => $length) {
            DB::statement("\n                DO $$\n                BEGIN\n                    IF EXISTS (\n                        SELECT 1\n                        FROM information_schema.columns\n                        WHERE table_schema = 'public'\n                          AND table_name = 'cover_attachment'\n                          AND column_name = '{$column}'\n                    ) THEN\n                        EXECUTE 'ALTER TABLE cover_attachment ALTER COLUMN {$column} TYPE VARCHAR({$length}) USING LEFT({$column}::text, {$length})';\n                    END IF;\n                END\n                $$;\n            ");
        }
    }
};
