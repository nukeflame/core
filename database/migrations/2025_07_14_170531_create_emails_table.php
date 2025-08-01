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
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->string('thread_id')->nullable();
            $table->string('in_reply_to')->nullable();
            $table->string('subject');
            $table->text('body');
            $table->json('from');
            $table->json('to');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('status', ['pending', 'sent', 'failed', 'received'])->default('pending');
            $table->json('headers')->nullable();
            $table->json('attachments')->nullable();
            $table->string('folder')->default('inbox');
            $table->timestamp('email_date');
            $table->boolean('starred')->default(false);
            $table->string('snippet')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['thread_id', 'email_date']);
            $table->index('message_id');

            // $table->string('sender_email');
            // $table->string('sender_name')->nullable();
            // $table->string('recipient_email');
            // $table->string('recipient_name')->nullable();
            // $table->string('subject');
            // $table->longText('body');
            // $table->json('attachments')->nullable();
            // $table->boolean('is_read')->default(false);
            // $table->string('folder')->default('inbox');
            // $table->boolean('starred')->default(false);
            // $table->timestamp('sent_at');
            // $table->timestamps();

            // $table->index(['recipient_email', 'sent_at']);
            // $table->index(['sender_email', 'sent_at']);
            // $table->index(['folder', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
