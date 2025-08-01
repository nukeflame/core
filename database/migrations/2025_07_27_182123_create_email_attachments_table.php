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
        Schema::create('email_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('message_id');
            $table->string('attachment_id');
            $table->string('user_email');
            $table->string('name');
            $table->string('content_type');
            $table->bigInteger('size');
            $table->boolean('is_inline')->default(false);
            $table->timestamp('last_modified')->nullable();
            $table->string('content_id')->nullable();
            $table->string('content_location')->nullable();
            $table->boolean('downloaded')->default(false);
            $table->string('file_path')->nullable();
            $table->text('download_error')->nullable();
            $table->timestamps();

            $table->unique(['message_id', 'attachment_id', 'user_email']);
            $table->index(['user_email', 'downloaded']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_attachments');
    }
};
