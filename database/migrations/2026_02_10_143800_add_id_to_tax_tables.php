<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add serial id primary key to tax_rates
        if (!Schema::hasColumn('tax_rates', 'id')) {
            DB::statement('ALTER TABLE tax_rates ADD COLUMN id SERIAL PRIMARY KEY');
        }

        // Add serial id primary key to tax_types
        if (!Schema::hasColumn('tax_types', 'id')) {
            DB::statement('ALTER TABLE tax_types ADD COLUMN id SERIAL PRIMARY KEY');
        }

        // Expand tax_code columns to accommodate longer codes (e.g. REINSURANCE_LEVY)
        DB::statement('ALTER TABLE tax_types ALTER COLUMN tax_code TYPE VARCHAR(30)');
        DB::statement('ALTER TABLE tax_rates ALTER COLUMN tax_code TYPE VARCHAR(30)');
        DB::statement('ALTER TABLE tax_rates ALTER COLUMN tax_rate TYPE VARCHAR(10)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tax_rates', 'id')) {
            Schema::table('tax_rates', function (Blueprint $table) {
                $table->dropColumn('id');
            });
        }

        if (Schema::hasColumn('tax_types', 'id')) {
            Schema::table('tax_types', function (Blueprint $table) {
                $table->dropColumn('id');
            });
        }
    }
};
