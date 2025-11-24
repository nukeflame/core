<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates a history table to audit every sequence increment.
     * Useful for debugging, compliance, and tracing issues.
     */
    public function up(): void
    {
        Schema::create('sequence_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sequence_id')->nullable()->comment('Reference to sequences.id');
            $table->string('sequence_name', 50)->comment('Denormalized for faster queries');
            $table->unsignedBigInteger('old_value')->nullable()->comment('Value before increment');
            $table->unsignedBigInteger('new_value')->comment('Value after increment');
            $table->string('formatted_number', 100)->nullable()->comment('The formatted number that was generated');
            $table->unsignedInteger('year')->nullable()->comment('Year of the sequence');
            $table->string('action', 20)->default('increment')->comment('Action type: increment, reset, manual_set');
            $table->string('triggered_by', 50)->nullable()->comment('Username or system process');
            $table->string('ip_address', 45)->nullable()->comment('IP address of request');
            $table->string('user_agent', 255)->nullable()->comment('User agent string');
            $table->json('context')->nullable()->comment('Additional context (e.g., cover_no, endorsement_no)');
            $table->timestamp('created_at')->useCurrent();

            $table->index('sequence_id', 'seq_history_sequence_id_idx');
            $table->index('sequence_name', 'seq_history_name_idx');
            $table->index('year', 'seq_history_year_idx');
            $table->index('created_at', 'seq_history_created_at_idx');
            $table->index(['sequence_name', 'year', 'created_at'], 'seq_history_name_year_date_idx');
            $table->index('triggered_by', 'seq_history_triggered_by_idx');
            $table->index('action', 'seq_history_action_idx');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("COMMENT ON TABLE sequence_history IS 'Audit trail for all sequence increments and modifications'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequence_history');
    }
};
