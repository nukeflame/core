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
        Schema::create('claim_ntf_ack_docs', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('cover_no',20)->nullable(false);
            $table->string('endorsement_no',20)->nullable(false);
            $table->string('intimation_no',20)->nullable(false);
            $table->bigInteger('doc_id');
            $table->date('date_received');
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->timestamps();
            $table->softDeletes();
            $table->string('deleted_by',20)->nullable(true);

            $table->foreign('doc_id')->references('id')->on('claim_ack_params');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_ntf_ack_docs');
    }
};
