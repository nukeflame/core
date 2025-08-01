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
        Schema::create('cbrequisitions', function (Blueprint $table) {
            $table->string('doc_type', 3);
            $table->string('dept_code', 3);
            $table->bigInteger('serial_no', 6);
            $table->integer('account_year');
            $table->integer('account_month');
            $table->dateTime('effective_date');
            $table->string('cheque_no', 20);
            $table->dateTime('cheque_date', 6);
            $table->string('name', 50);
            $table->integer('customer_id');
            $table->decimal('foreign_gross_amount', 20, 2);
            $table->decimal('local_gross_amount', 20, 2);
            $table->decimal('foreign_add_deduct_amount', 20, 2);
            $table->decimal('local_add_deduct_amount', 20, 2);
            $table->decimal('foreign_nett_amount', 20, 2);
            $table->decimal('local_nett_amount', 20, 2);
            $table->dateTime('created_date');
            $table->string('created_by', 20);
            $table->dateTime('updated_date');
            $table->string('updated_by', 20);
            $table->string('entry_type_descr', 3);
            $table->string('cover_no', 20);
            $table->string('endorsement_no', 20);
            $table->string('debit_account', 20);
            $table->string('credit_account', 20);
            $table->string('classcode', 5);
            $table->string('analyse_payment', 1);
            $table->string('checked_flag', 1);
            $table->string('authorized_flag', 1);
            $table->string('approved_flag', 1);
            $table->string('checked_by', 1);
            $table->string('authorized_by', 1);
            $table->string('approved_by', 1);
            $table->dateTime('checked_date');
            $table->dateTime('authorized_date');
            $table->dateTime('approved_date');
            $table->string('source_code', 5);
            $table->string('offcd', 3);
            $table->string('branch', 3);
            $table->string('narration', 100);
            $table->string('payee_bank_code', 3);
            $table->string('payee_bank_name', 100);
            $table->string('payee_bank_branch_code', 3);
            $table->string('payee_bank_branch_name', 100);
            $table->string('currency_code', 100);
            $table->string('currency_rate', 100);
            $table->string('cancelled_flag', 1);
            $table->string('cancelled_by', 20);
            $table->dateTime('cancelled_date');
            $table->string('voucher_raised_flag', 1);
            $table->string('voucher_posted_flag', 1);
            $table->string('invoice_no', 20);
            $table->string('requisition_no', 20);
            $table->string('wht_cert_no', 20);
            $table->unique(['requisition_no'], 'cbrequisitions_1');
            $table->unique(['dept_code', 'serial_no', 'account_year', 'account_month', 'offcd'], 'cbrequisitions_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbrequisitions');
    }
};
