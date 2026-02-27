<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $columns = [
            'updated_written_share',
            'updated_signed_share',
            'signed_share',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('bd_fac_reinsurers', $column)) {
                DB::statement(sprintf(
                    'ALTER TABLE bd_fac_reinsurers ALTER COLUMN %s TYPE NUMERIC(8,2) USING NULLIF(%s::text, \'\')::numeric',
                    $column,
                    $column
                ));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [
            'updated_written_share',
            'updated_signed_share',
            'signed_share',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('bd_fac_reinsurers', $column)) {
                DB::statement(sprintf(
                    'ALTER TABLE bd_fac_reinsurers ALTER COLUMN %s TYPE BIGINT USING ROUND(COALESCE(%s, 0))::bigint',
                    $column,
                    $column
                ));
            }
        }
    }
};
