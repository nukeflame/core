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
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_id', 191)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('change_type', 50);
            $table->string('resource', 500)->nullable();
            $table->string('client_state', 100)->nullable();
            $table->boolean('is_valid')->default(true);
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->ipAddress('source_ip')->nullable();
            $table->timestamps();

            $table->index('subscription_id');
            $table->index('user_id');
            $table->index(['is_processed', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
