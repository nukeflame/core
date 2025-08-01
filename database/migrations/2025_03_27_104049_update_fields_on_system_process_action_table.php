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
        Schema::table('system_process_action', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'running', 'cancelled', 'completed', 'failed', 'pending'])->default('pending');
            $table->string('module')->nullable();
            $table->string('action_type')->nullable();
            $table->timestamp('scheduled_at')->nullable();

            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamp('performed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_process_action', function (Blueprint $table) {
            $table->dropColumn(['description', 'status', 'module', 'performed_by', 'performed_at', 'action_type', 'scheduled_at']);
        });
    }
};
