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
        Schema::dropIfExists('prospect_docs');

        Schema::create('prospect_docs', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->string('prospect_id')->index();
            $table->string('prospect_status')->nullable();
            $table->string('document_type_id')->nullable();
            $table->string('mimetype')->nullable();
            $table->string('file');
            $table->string('s3_path')->nullable();
            $table->string('s3_url')->nullable();
            $table->string('file_size')->nullable();
            $table->string('original_name')->nullable();
            $table->string('bus_type')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_docs');
    }
};
