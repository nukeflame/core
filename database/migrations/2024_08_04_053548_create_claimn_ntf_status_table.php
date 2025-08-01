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
        Schema::create('claim_ntf_status', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('intimation_no', 20);
            $table->string('status',5)->default('O');
            $table->integer('status_id');
            $table->string('description',200);
            $table->string('created_by',20);
            $table->string('updated_by',20)->nullable();
            $table->string('deleted_by',20)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('claim_status_param');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_ntf_status');
    }
};
