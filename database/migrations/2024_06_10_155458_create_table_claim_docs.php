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
        Schema::create('claim_docs', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('cover_no',20)->nullable(false);
            $table->string('endorsement_no',20)->nullable(false);
            $table->string('claim_no',20)->nullable(false);
            $table->string('title',50)->nullable(false);
            $table->string('description',200)->nullable();
            $table->string('file',200)->nullable(false);
            $table->text('file_base64');
            $table->string('mime_type',100);
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->timestamps();
            $table->softDeletes();
            $table->string('deleted_by',20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_docs');
    }
};
