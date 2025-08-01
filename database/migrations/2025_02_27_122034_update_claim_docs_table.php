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
        Schema::table('claim_docs', function (Blueprint $table) {
            $table->string('file')->change()->nullable();
            $table->string('file_base64')->change()->nullable();
            $table->string('mime_type')->change()->nullable();
            $table->string('title')->change()->nullable();
            $table->string('document_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_docs', function (Blueprint $table) {
            $table->dropColumn(['file', 'file_base64', 'mime_type', 'title', 'document_type']);
        });
    }
};
