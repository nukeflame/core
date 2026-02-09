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
        Schema::table('debit_note_items', function (Blueprint $table) {
            $table->decimal('original_line_rate', 10, 4)->nullable();
        });

        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->decimal('original_line_rate', 10, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_note_items', function (Blueprint $table) {
            $table->dropColumn('original_line_rate');
        });

        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->dropColumn('original_line_rate');
        });
    }
};
