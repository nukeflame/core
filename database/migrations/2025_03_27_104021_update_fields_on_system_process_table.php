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
        Schema::table('system_process', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('execution_type')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->string('priority')->default('medium');
            $table->json('parameters')->nullable();

            $table->foreignId('permission_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            $table->foreignId('initiated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_process', function (Blueprint $table) {
            $table->dropColumn(['description', 'status', 'priority', 'permission_id', 'initiated_by', 'started_at', 'completed_at', 'parameters', 'execution_type']);
        });
    }
};
