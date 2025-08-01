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
        Schema::create('banktransactions', function (Blueprint $table) {
            $table->string('source', 5);
            $table->string('reference_no',200);
            $table->string('trans_description',200);
            $table->string('batch_no',20)->nullable();
            $table->string('item_no',20);
            $table->string('currency_code',3);
            $table->decimal('currency_rate',8,3);
            $table->string('dr_cr',1);
            $table->decimal('foreign_amount',20,2);
            $table->decimal('local_amount',20,2);
            $table->decimal('reconcilliation_year',4,0)->nullable();
            $table->decimal('reconcilliation_month',2,0)->nullable();
            $table->string('reconcilled',1);
            $table->date('reconcilliation_date')->nullable();
            $table->string('doc_type',5);
            $table->string('cheque_no',20)->nullable();
            $table->string('bank_acc_code',20);
            $table->string('created_by',20)->nullable();
            $table->string('updated_by',20)->nullable();
            $table->timestamps();
            $table->unique(['source', 'doc_type','reference_no', 'batch_no','item_no', 'bank_acc_code'], 'banktransactions_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banktransactions');
    }
};
