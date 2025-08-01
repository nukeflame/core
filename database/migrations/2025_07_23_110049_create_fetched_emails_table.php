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
        Schema::create('fetched_emails', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->string('message_id')->unique();
            $table->string('uid');
            $table->string('subject')->nullable();
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->json('to_emails');
            $table->timestamp('date_received');
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->json('attachments')->nullable();
            $table->json('flags')->nullable();
            $table->integer('size')->default(0);
            $table->timestamps();

            $table->index(['user_email', 'date_received']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fetched_emails');
    }
};
