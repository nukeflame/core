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
        Schema::table('stage_documents', function (Blueprint $table) {
            DB::statement("SELECT setval('stage_documents_id_seq', (SELECT MAX(id) FROM stage_documents))");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage_documents', function (Blueprint $table) {
        });
    }
};
