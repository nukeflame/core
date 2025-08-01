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
        Schema::table('claim_ntf_ack_docs', function (Blueprint $table) {
            $table->date('date_received')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_ntf_ack_docs', function (Blueprint $table) {
            $table->date('date_received')->nullable(false)->change();
        });
    }
};
