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
        Schema::create('approvals_tracker', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('process_id')->nullable(false);
            $table->unsignedBigInteger('process_action')->nullable(false);
            $table->unsignedBigInteger('approver')->nullable(false);
            $table->string('comment', 200)->nullable(false);
            $table->string('approver_comment', 200)->nullable(true);
            $table->string('data', 200)->nullable(false);
            // $table->string('source_identifier',200)->nullable(false);
            $table->string('status', 10)->nullable(false)->default('P');
            $table->string('created_by', 20)->nullable(false);
            $table->string('updated_by', 20)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals_tracker');
    }
};
