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
        Schema::create('claim_ack_params', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('doc_name',100);
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->softDeletes();
            $table->string('deleted_by',20)->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_ack_params');
    }
};
