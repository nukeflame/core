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
        Schema::create('treaty_documents', function (Blueprint $table) {
            $table->id();
            $table->string('endorsement_no')->nullable()->index();
            $table->string('cover_no')->nullable()->index();
            $table->string('document_type', 100)->index();
            $table->string('reference')->unique();
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('generated_by')->nullable();
            $table->string('status', 50)->default('generated')->index();
            $table->timestamp('generated_date')->nullable();
            $table->timestamps();

            $table->index(['endorsement_no', 'document_type']);
            $table->index(['cover_no', 'document_type']);
            $table->index(['status', 'generated_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treaty_documents');
    }
};
