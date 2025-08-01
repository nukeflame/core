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
        Schema::table('claim_ntf_docs', function (Blueprint $table) {
            $table->string('file')->nullable()->change();
            $table->string('file_base64')->nullable()->change();
            $table->string('mime_type')->nullable()->change();
            $table->string('title')->nullable()->change();
            $table->string('document_type')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('claim_ntf_docs', function (Blueprint $table) {
            $table->dropColumn(['file', 'file_base64', 'mime_type', 'title', 'document_type']);
        });
    }
};
