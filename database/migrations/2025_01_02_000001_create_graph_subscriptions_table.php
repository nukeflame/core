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
        Schema::create('graph_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_id')->unique()->index();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_email');
            $table->string('resource');
            $table->string('change_type');
            $table->string('notification_url');
            $table->string('client_state');
            $table->timestamp('expiration_date');
            $table->enum('status', ['active', 'expired', 'failed', 'pending'])->default('active');
            $table->timestamp('last_notification_at')->nullable();
            $table->unsignedInteger('notification_count')->default(0);
            $table->timestamp('last_renewal_at')->nullable();
            $table->unsignedInteger('renewal_attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graph_subscriptions');
    }
};
