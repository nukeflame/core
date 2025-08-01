<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prospect_docs', function (Blueprint $table) {
            $table->string('quote_reinsurer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_docs', function (Blueprint $table) {
            $table->dropColumn('quote_reinsurer_id');
        });
    }
};
