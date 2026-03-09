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
            'city',
            'street',
            'country_iso',
            'fax_no',
            'telephone',
            'financial_rate',
            'agency_rate',
            'created_by',
            'partner_number',
            'email'
        ];

        foreach ($columns as $column) {
            DB::statement("
                DO $$
                BEGIN
                    IF EXISTS (
                        SELECT 1
                        FROM information_schema.columns
                        WHERE table_schema = 'public'
                          AND table_name = 'customers'
                          AND column_name = '{$column}'
                    ) THEN
                        EXECUTE 'ALTER TABLE customers ALTER COLUMN {$column} TYPE VARCHAR(100) USING {$column}::text';
                    END IF;
                END
                $$;
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
