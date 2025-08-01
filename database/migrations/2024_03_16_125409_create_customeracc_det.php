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
        Schema::create('customeracc_det', function (Blueprint $table) {
            $table->string('branch', 3);
            $table->integer('customer_id');
            $table->string('source_code', 3);
            $table->string('doc_type', 3);
            $table->string('entry_type_descr', 3);
            $table->string('reference', 20);
            $table->integer('account_year');
            $table->integer('account_month');
            $table->integer('line_no');
            $table->string('cheque_no', 20);
            $table->dateTime('cheque_date');
            $table->string('cover_no', 20);
            $table->string('endorsement_no', 20);
            $table->string('insured', 100);
            $table->string('class', 3);
            $table->string('currency_code', 3);
            $table->string('currency_rate', 8);
            $table->string('created_by', 20);
            $table->date('created_date');
            $table->time('created_time');
            $table->string('updated_by', 20);
            $table->dateTime('updated_datetime');
            $table->string('dr_cr', 1);
            $table->decimal('foreign_basic_amount', 20, 2);
            $table->decimal('local_basic_amount', 20, 2);
            $table->decimal('foreign_taxes_amount', 20, 2);
            $table->decimal('local_taxes_amount', 20, 2);
            $table->decimal('foreign_nett_amount', 20, 2);
            $table->decimal('local_nett_amount', 20, 2);
            $table->decimal('allocated_amount', 20, 2);
            $table->decimal('unallocated_amount', 20, 2);
            $table->unique(['source_code', 'doc_type', 'entry_type_descr', 'reference', 'account_year', 'account_month', 'line_no'], 'customeracc_det_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customeracc_det');
    }
};
