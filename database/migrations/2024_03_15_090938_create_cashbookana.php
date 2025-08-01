<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cashbookana', function (Blueprint $table) {
            $table->string('source_code', 3);
            $table->string('doc_type', 3);
            $table->string('reference_no',10);
            $table->unsignedSmallInteger('line_no');
            $table->unsignedSmallInteger('branch');
            $table->unsignedSmallInteger('item_no');
            $table->dateTime('receipt_date')->default(now());
            $table->date('created_date')->default(now());
            $table->string('created_by', 20);
            $table->decimal('allocated_amount',20,2);
            $table->decimal('unallocated_amount',20,2);
            $table->string('updated_by', 20);
            $table->date('updated_date');
            $table->time('created_time')->default(now());
            $table->time('updated_time')->default(now());
            $table->string('entry_type_descr', 3);
            $table->string('cover_no', 20)->nullable();
            $table->string('claim_no', 20)->nullable();
            $table->string('customer_id', 20)->nullable();
            $table->string('endorsement_no', 20)->nullable();
            $table->string('pay_request_no', 20)->nullable();
            $table->string('gl_account', 8)->nullable();
            $table->string('offcd', 3);
            $table->string('dr_cr', 1);
            $table->string('amount_in_words', 700);
            $table->string('orig_entry_type_descr', 3)->nullable();
            $table->decimal('analyse_amount',20,2);
            $table->string('currency_code', 3);
            $table->string('currency_rate', 8);
            $table->string('debit_note_no', 20)->nullable();
            $table->string('credit_note_no', 20)->nullable();
            $table->string('narration', 200);
            // $table->timestamps();
            $table->unique(['offcd', 'doc_type', 'entry_type_descr','reference_no','line_no','item_no'], 'cashbookana_1');
            $table->unique(['offcd', 'doc_type', 'entry_type_descr','reference_no','pay_request_no','line_no','item_no'], 'cashbookana_2');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashbookana');
    }
};
