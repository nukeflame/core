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
        Schema::create('email_profile_pictures', function (Blueprint $table) {
            $table->id();
            $table->string('sender_email');
            $table->string('user_email'); // The user who fetched the email
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->bigInteger('size')->nullable();
            $table->string('content_type')->default('image/jpeg');
            $table->boolean('downloaded')->default(false);
            $table->string('file_path')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('last_fetched');
            $table->timestamps();

            $table->unique(['sender_email', 'user_email']);
            $table->index(['user_email', 'downloaded']);
            $table->index('last_fetched');
        });

        // Table to link profile pictures to specific emails
        Schema::create('email_profile_picture_links', function (Blueprint $table) {
            $table->id();
            $table->string('message_id');
            $table->string('sender_email');
            $table->timestamps();

            $table->unique(['message_id', 'sender_email']);
            $table->index('sender_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_profile_picture_links');
        Schema::dropIfExists('email_profile_pictures');
    }
};
