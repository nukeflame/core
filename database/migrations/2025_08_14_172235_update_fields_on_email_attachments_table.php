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
        Schema::table('email_attachments', function (Blueprint $table) {
            if (!Schema::hasColumn('email_attachments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }

            $table->index(['user_id', 'message_id']);
            $table->index(['message_id', 'attachment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_attachments', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'message_id']);
            $table->dropIndex(['message_id', 'attachment_id']);
        });
    }
};
