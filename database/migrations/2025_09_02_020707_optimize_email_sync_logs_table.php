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
        DB::table('email_sync_logs')
            ->where('status', 'running')
            ->where('started_at', '<', now()->subHours(2))
            ->update([
                'status' => 'failed',
                'error_message' => 'Process appears to have been interrupted - cleaned up during migration',
                'completed_at' => now(),
                'updated_at' => now()
            ]);

        Schema::table('email_sync_logs', function (Blueprint $table) {
            $table->index(['user_id', 'folder', 'status', 'completed_at'], 'idx_email_sync_logs_user_folder_status');

            $table->index(['user_id', 'folder', 'completed_at'], 'idx_email_sync_logs_successful_syncs');

            $table->index(['status', 'started_at'], 'idx_email_sync_logs_status_started');

            $table->index('user_id', 'idx_email_sync_logs_user_id');
            $table->index('folder', 'idx_email_sync_logs_folder');
            $table->index('status', 'idx_email_sync_logs_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_sync_logs', function (Blueprint $table) {
            $table->dropIndex('idx_email_sync_logs_user_folder_status');
            $table->dropIndex('idx_email_sync_logs_successful_syncs');
            $table->dropIndex('idx_email_sync_logs_status_started');
            $table->dropIndex('idx_email_sync_logs_user_id');
            $table->dropIndex('idx_email_sync_logs_folder');
            $table->dropIndex('idx_email_sync_logs_status');
        });
    }
};
