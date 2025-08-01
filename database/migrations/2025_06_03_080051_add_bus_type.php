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
            $table->string('bus_type',10)->nullable();
            $table->string('attachment_file', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc_types', function (Blueprint $table) {
            $table->dropColumn('bus_type');
            $table->dropColumn('attachment_file');
        });
    }
};
