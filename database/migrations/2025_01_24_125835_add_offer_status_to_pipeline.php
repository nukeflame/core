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
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->string('status', 5)->nullable();
            $table->string('confirmation_file', 255)->nullable();
            $table->string('query_file', 255)->nullable();
            $table->string('query_text', 255)->nullable();
            $table->string('decline_negotiation_text', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'confirmation_file',
                'query_file',
                'query_text',
                'decline_negotiation_text',
            ]);
        });
    }
};
