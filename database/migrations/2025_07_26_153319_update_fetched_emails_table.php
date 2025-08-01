<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fetched_emails', function (Blueprint $table) {
            $table->string('uid')->nullable()->change();
            $table->json('to_emails')->nullable()->change();
            $table->json('to_recipients')->nullable();
            $table->json('cc_recipients')->nullable();
            $table->timestamp('date_sent')->nullable();
            $table->string('importance')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('has_attachments')->default(false);
            $table->string('conversation_id')->nullable();
            $table->string('folder')->nullable();
            $table->text('body_preview')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fetched_emails', function (Blueprint $table) {
            $table->dropColumn([
                'to_recipients',
                'cc_recipients',
                'date_sent',
                'importance',
                'is_read',
                'has_attachments',
                'conversation_id',
                'folder',
                'body_preview'
            ]);
        });
    }
};
