<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('prospect_installments', function (Blueprint $table) {
            $table->id();
            $table->string('pipeline_id');
            $table->string('opportunity_id');
            $table->integer('layer_no')->default(0);
            $table->string('trans_type');
            $table->string('entry_type');
            $table->string('installment_no');
            $table->date('installment_date');
            $table->decimal('installment_amt', 15, 2);
            $table->string('dr_cr');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_installments');
    }
};
