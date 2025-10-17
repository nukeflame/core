<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_sync_states', function (Blueprint $table) {
            $table->boolean('is_syncing')->default(false)->after('sync_attempts');
            $table->index(['user_id', 'is_syncing']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_sync_states', function (Blueprint $table) {
            $table->dropIndex('user_syncing_index');
            $table->dropColumn('is_syncing');
        });
    }
};
