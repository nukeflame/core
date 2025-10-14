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
        Schema::create('email_sync_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('delta_token')->nullable();
            $table->timestamp('delta_token_expires_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('last_successful_sync_at')->nullable();
            $table->integer('sync_attempts')->default(0);
            $table->integer('consecutive_failures')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->text('last_error')->nullable();
            $table->string('last_error_code')->nullable();
            $table->string('subscription_id', 191)->nullable();
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamp('subscription_created_at')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->string('lock_owner', 100)->nullable();
            $table->enum('status', ['active', 'paused', 'failed', 'disabled'])->default('active');
            $table->integer('total_emails_synced')->default(0);
            $table->integer('emails_synced_this_session')->default(0);
            $table->json('sync_statistics')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->unique('subscription_id');
            $table->index('subscription_expires_at');
            $table->index(['status', 'last_synced_at']);
            $table->index(['is_locked', 'locked_at']);
            $table->index('consecutive_failures');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sync_states');
    }
};
