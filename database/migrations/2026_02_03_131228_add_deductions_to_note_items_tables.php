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
        // Add deduction columns to debit_note_items
        Schema::table('debit_note_items', function (Blueprint $table) {
            $table->decimal('commission', 18, 2)->default(0)->after('amount');
            $table->decimal('premium_tax', 18, 2)->default(0)->after('commission');
        });

        // Add deduction columns to credit_note_items
        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->decimal('commission', 18, 2)->default(0)->after('amount');
            $table->decimal('brokerage', 18, 2)->default(0)->after('commission');
            $table->decimal('premium_tax', 18, 2)->default(0)->after('brokerage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_note_items', function (Blueprint $table) {
            $table->dropColumn(['commission', 'premium_tax']);
        });

        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->dropColumn(['commission', 'brokerage', 'premium_tax']);
        });
    }
};
