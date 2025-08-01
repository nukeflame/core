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
        Schema::create('glbatch', function (Blueprint $table) {
            $table->string('batch_no',20);
            $table->string('batch_source',5);
            $table->string('batch_type',5);
            $table->string('batch_title',200);
            $table->string('batch_description',200);
            $table->decimal('account_year',4,0);
            $table->decimal('account_month',4,0);
            $table->decimal('foreign_batch_amount',20,0);
            $table->decimal('local_batch_amount',20,0);
            $table->string('currency_code',3);
            $table->decimal('currency_rate',10,4);
            $table->string('batch_status',5);
            $table->string('no_of_entries',5);
            $table->string('reversal_reference',20);
            $table->string('cancelled_by',20);
            $table->date('cancelled_date');
            $table->string('entry_type_descr',50);
            $table->string('offcd',3);
            $table->string('reversal_batch',20);
            $table->string('original_batch',20);
            $table->string('escalated_by',20);
            $table->string('escalated_to',20);
            $table->date('escalated_date');
            $table->string('approved',1);
            $table->string('approved_by',20);
            $table->date('approved_date');
            $table->string('declined',1);
            $table->string('declined_reason',300);
            $table->date('declined_date');
            $table->string('authorized',1);
            $table->string('authorized_by',20);
            $table->date('authorized_date');
            $table->date('batch_date');
            $table->string('doc_type',3);
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->timestamps();

            $table->unique(['batch_no'], 'glbatch_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glbatch');
    }
};
