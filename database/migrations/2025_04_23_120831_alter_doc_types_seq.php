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
        Schema::table('doc_types', function (Blueprint $table) {
            DB::statement("SELECT setval('doc_types_id_seq', (SELECT MAX(id) FROM doc_types))");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc_types', function (Blueprint $table) {
        });
    }
};
