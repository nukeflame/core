<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('handover_approvals')) {
            return;
        }

        $columns = ['excess_type', 'excess', 'max/min', 'range'];
        foreach ($columns as $column) {
            if (!Schema::hasColumn('handover_approvals', $column)) {
                return;
            }
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE handover_approvals ALTER COLUMN excess_type TYPE TEXT USING excess_type::TEXT');
            DB::statement('ALTER TABLE handover_approvals ALTER COLUMN excess TYPE TEXT USING excess::TEXT');
            DB::statement('ALTER TABLE handover_approvals ALTER COLUMN "max/min" TYPE TEXT USING "max/min"::TEXT');
            DB::statement('ALTER TABLE handover_approvals ALTER COLUMN range TYPE TEXT USING range::TEXT');
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE handover_approvals MODIFY excess_type TEXT NULL');
            DB::statement('ALTER TABLE handover_approvals MODIFY excess TEXT NULL');
            DB::statement('ALTER TABLE handover_approvals MODIFY `max/min` TEXT NULL');
            DB::statement('ALTER TABLE handover_approvals MODIFY `range` TEXT NULL');
        }
    }

    public function down(): void
    {
        // Intentionally no-op: reverting to fixed-length strings may truncate JSON array payloads.
    }
};
