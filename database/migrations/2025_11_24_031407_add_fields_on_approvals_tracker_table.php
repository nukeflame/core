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
        // Add missing column to approvals_tracker
        Schema::table('approvals_tracker', function (Blueprint $table) {
            if (!Schema::hasColumn('approvals_tracker', 'actioned_at')) {
                $table->timestamp('actioned_at')->nullable()->after('updated_at');
            }
        });

        // Add indexes for better query performance
        Schema::table('approvals_tracker', function (Blueprint $table) {
            // Index for approver queries
            if (!$this->indexExists('approvals_tracker', 'approvals_tracker_approver_index')) {
                $table->index('approver', 'approvals_tracker_approver_index');
            }

            // Index for status queries
            if (!$this->indexExists('approvals_tracker', 'approvals_tracker_status_index')) {
                $table->index('status', 'approvals_tracker_status_index');
            }

            // Composite index for common query patterns
            if (!$this->indexExists('approvals_tracker', 'approvals_tracker_approver_status_index')) {
                $table->index(['approver', 'status'], 'approvals_tracker_approver_status_index');
            }

            // Index for created_at ordering
            if (!$this->indexExists('approvals_tracker', 'approvals_tracker_created_at_index')) {
                $table->index('created_at', 'approvals_tracker_created_at_index');
            }
        });

        // Add indexes to notifications table
        Schema::table('notifications', function (Blueprint $table) {
            // Index for notification_type queries
            if (!$this->indexExists('notifications', 'notifications_notification_type_index')) {
                $table->index('notification_type', 'notifications_notification_type_index');
            }

            // Index for approval_tracker_id foreign key
            if (!$this->indexExists('notifications', 'notifications_approval_tracker_id_index')) {
                $table->index('approval_tracker_id', 'notifications_approval_tracker_id_index');
            }

            // Index for status queries
            if (!$this->indexExists('notifications', 'notifications_status_index')) {
                $table->index('status', 'notifications_status_index');
            }
        });

        // Add indexes to approval_source_link table
        Schema::table('approval_source_link', function (Blueprint $table) {
            // Index for approval_id foreign key
            if (!$this->indexExists('approval_source_link', 'approval_source_link_approval_id_index')) {
                $table->index('approval_id', 'approval_source_link_approval_id_index');
            }

            // Composite index for source table queries
            if (!$this->indexExists('approval_source_link', 'approval_source_link_source_lookup_index')) {
                $table->index(
                    ['source_table', 'source_column_name', 'source_column_data'],
                    'approval_source_link_source_lookup_index'
                );
            }
        });

        // Add check constraint for status values (PostgreSQL specific)
        if (!$this->constraintExists('approvals_tracker', 'approvals_tracker_status_check')) {
            DB::statement("
                ALTER TABLE approvals_tracker
                ADD CONSTRAINT approvals_tracker_status_check
                CHECK (status IN ('P', 'A', 'R'))
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove check constraint
        DB::statement("ALTER TABLE approvals_tracker DROP CONSTRAINT IF EXISTS approvals_tracker_status_check");

        // Remove indexes from approval_source_link
        Schema::table('approval_source_link', function (Blueprint $table) {
            $this->dropIndexIfExists('approval_source_link', 'approval_source_link_approval_id_index');
            $this->dropIndexIfExists('approval_source_link', 'approval_source_link_source_lookup_index');
        });

        // Remove indexes from notifications
        Schema::table('notifications', function (Blueprint $table) {
            $this->dropIndexIfExists('notifications', 'notifications_notification_type_index');
            $this->dropIndexIfExists('notifications', 'notifications_approval_tracker_id_index');
            $this->dropIndexIfExists('notifications', 'notifications_status_index');
        });

        // Remove indexes from approvals_tracker
        Schema::table('approvals_tracker', function (Blueprint $table) {
            $this->dropIndexIfExists('approvals_tracker', 'approvals_tracker_approver_index');
            $this->dropIndexIfExists('approvals_tracker', 'approvals_tracker_status_index');
            $this->dropIndexIfExists('approvals_tracker', 'approvals_tracker_approver_status_index');
            $this->dropIndexIfExists('approvals_tracker', 'approvals_tracker_created_at_index');
        });

        // Remove actioned_at column
        Schema::table('approvals_tracker', function (Blueprint $table) {
            if (Schema::hasColumn('approvals_tracker', 'actioned_at')) {
                $table->dropColumn('actioned_at');
            }
        });
    }

    /**
     * Check if an index exists on a table (PostgreSQL specific)
     */
    private function indexExists(string $table, string $index): bool
    {
        $schemaName = DB::getConfig('schema') ?: 'public';

        $result = DB::selectOne("
            SELECT EXISTS (
                SELECT 1
                FROM pg_indexes
                WHERE schemaname = ?
                AND tablename = ?
                AND indexname = ?
            ) as exists
        ", [$schemaName, $table, $index]);

        return $result->exists ?? false;
    }

    /**
     * Check if a constraint exists on a table (PostgreSQL specific)
     */
    private function constraintExists(string $table, string $constraint): bool
    {
        $schemaName = DB::getConfig('schema') ?: 'public';

        $result = DB::selectOne("
            SELECT EXISTS (
                SELECT 1
                FROM pg_constraint
                WHERE conname = ?
                AND connamespace = (
                    SELECT oid
                    FROM pg_namespace
                    WHERE nspname = ?
                )
                AND conrelid = (
                    SELECT oid
                    FROM pg_class
                    WHERE relname = ?
                    AND relnamespace = (
                        SELECT oid
                        FROM pg_namespace
                        WHERE nspname = ?
                    )
                )
            ) as exists
        ", [$constraint, $schemaName, $table, $schemaName]);

        return $result->exists ?? false;
    }

    /**
     * Drop an index if it exists (safe drop)
     */
    private function dropIndexIfExists(string $table, string $index): void
    {
        if ($this->indexExists($table, $index)) {
            DB::statement("DROP INDEX IF EXISTS {$index}");
        }
    }
};
