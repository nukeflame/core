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
        Schema::dropIfExists('emails');

        Schema::create('emails', function (Blueprint $table) {
            $table->id();

            $table->string('sender_email');
            $table->string('sender_name');
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->longText('body');

            $table->json('recipients')->nullable();
            $table->json('cc_emails')->nullable();
            $table->json('bcc_emails')->nullable();

            $table->unsignedBigInteger('claim_id')->nullable();
            $table->string('claim_no')->nullable();
            $table->foreign('claim_id')->references('claim_serial_no')->on('claim_register')->onDelete('set null');
            $table->index('claim_no');

            $table->string('priority')->default('normal');
            $table->string('category')->default('general');
            $table->string('reference')->nullable();

            $table->json('attachments')->nullable();
            $table->string('status')->default('queued');
            $table->string('folder')->default('sent');

            $table->string('reply_to_id')->nullable();
            $table->string('outlook_message_id')->nullable();
            $table->string('conversation_id')->nullable();
            $table->string('internet_message_id')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['sender_email', 'created_at']);
            $table->index(['recipient_email', 'created_at']);
            $table->index(['conversation_id']);
            $table->index(['outlook_message_id']);
            $table->index(['category', 'status']);
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
