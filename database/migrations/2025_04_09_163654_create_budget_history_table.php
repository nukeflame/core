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
        Schema::create('budget_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budget_allocations');
            $table->enum('action', ['created', 'updated', 'status_changed', 'deleted', 'restored']);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            // Index
            $table->index(['budget_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_history');
    }
};
