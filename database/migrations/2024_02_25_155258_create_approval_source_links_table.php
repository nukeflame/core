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
        Schema::create('approval_source_link', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('approval_id')->nullable(false);
            $table->unsignedBigInteger('process_id')->nullable(false);
            $table->unsignedBigInteger('process_action')->nullable(false);
            $table->string('source_table')->nullable(false);
            $table->string('source_column_name')->nullable(false);
            $table->string('source_column_data')->nullable(false);
            $table->string('source_approval_column')->nullable(false);
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_source_link');
    }
};
