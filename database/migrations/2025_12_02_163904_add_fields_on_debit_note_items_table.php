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
            $table->string('item_no')->nullable();
            $table->double('net_amount', 2)->nullable();
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_note_items', function (Blueprint $table) {
            $table->dropColumn(['item_no', 'net_amount', 'status']);
        });
    }
};
