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
        Schema::create('cashbook', function (Blueprint $table) {
            $table->string('doc_type', 3);
            $table->unsignedInteger('transaction_no');
            $table->integer('account_year');
            $table->integer('line_no');
            $table->unsignedTinyInteger('account_month');
            $table->date('receipt_date')->useCurrent();
            $table->date('created_date')->useCurrent();
            $table->string('created_by', 20);
            $table->string('cheque_no', 20)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('name', 100);
            $table->string('payee', 100);
            $table->unsignedInteger('customer_id');
            $table->string('cbpay_method_code', 5);
            $table->decimal('local_amount', 20,2);
            $table->string('updated_by', 20);
            $table->date('updated_date');
            $table->dateTime('created_time')->useCurrent();
            $table->dateTime('updated_time')->useCurrent();
            $table->string('entry_type_descr', 3);
            $table->string('cover_no', 20)->nullable();
            $table->string('claim_no', 20)->nullable();
            $table->string('endorsement_no', 20)->nullable();
            $table->string('local_cheque', 1);
            $table->string('debit_account', 8)->nullable();
            $table->string('credit_account', 8)->nullable();
            $table->string('source_code', 3);
            $table->string('pay_request_no', 20)->nullable();
            $table->string('offcd', 3);
            $table->string('branch', 3);
            $table->string('analysed_cover', 1);
            $table->string('narration', 200);
            $table->string('amount_in_words', 700);
            $table->string('cancelled', 1);
            $table->string('cancelled_reference', 20)->nullable();
            $table->string('cancelled_reason', 200)->nullable();
            $table->string('orig_entry_type_descr', 3)->nullable();
            $table->string('multi_claims', 1);
            $table->decimal('foreign_amount', 20,2);
            $table->string('currency_code', 3);
            $table->string('currency_rate', 8);
            $table->string('bank_account_code', 8)->nullable();
            $table->string('debit_note_no', 20)->nullable();
            $table->string('credit_note_no', 20)->nullable();

            $table->unique(['offcd','source_code', 'doc_type', 'entry_type_descr', 'transaction_no', 'account_year', 'account_month', 'line_no'], 'cashbook_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashbook');
    }
};
