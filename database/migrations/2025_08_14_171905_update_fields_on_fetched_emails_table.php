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
        Schema::table('fetched_emails', function (Blueprint $table) {
            Schema::table('fetched_emails', function (Blueprint $table) {
                if (!Schema::hasColumn('fetched_emails', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->after('id');
                }

                $table->index(['user_id', 'date_received']);
                $table->index(['user_id', 'folder']);
                $table->index(['message_id', 'user_id']);
                $table->index('conversation_id');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fetched_emails', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'date_received']);
            $table->dropIndex(['user_id', 'folder']);
            $table->dropIndex(['message_id', 'user_id']);
            $table->dropIndex(['conversation_id']);
        });
    }
};
