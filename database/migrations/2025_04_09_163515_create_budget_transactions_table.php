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
        Schema::create('budget_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budget_allocations');
            $table->string('transaction_type');
            $table->decimal('amount', 15, 2);
            $table->string('reference_number')->nullable();
            $table->date('transaction_date');
            $table->string('quarter')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['budget_id', 'transaction_date']);
            $table->index('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_transactions');
    }
};
