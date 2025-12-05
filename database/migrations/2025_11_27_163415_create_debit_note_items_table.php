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
        Schema::create('debit_note_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('debit_note_id')
                ->constrained('debit_notes')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('line_no')->default(1);
            $table->string('item_code', 10)->nullable();
            $table->string('description', 100)->comment('Transaction type code');

            $table->string('class_group_code', 10)->nullable();
            $table->string('class_code', 10)->nullable();

            $table->decimal('line_rate', 8, 4)->nullable()->comment('Commission/rate %');
            $table->enum('ledger', ['DR', 'CR'])->default('DR')->comment('Debit or Credit');
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('original_amount', 18, 2)->nullable()->comment('Amount in original currency');
            $table->string('original_currency', 3)->nullable();

            $table->string('reference', 100)->nullable()->comment('External reference');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['debit_note_id', 'line_no']);
            $table->index('item_code');
            $table->index('ledger');

            $table->foreign('item_code')
                ->references('item_code')
                ->on('treaty_item_codes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_note_items');
    }
};
