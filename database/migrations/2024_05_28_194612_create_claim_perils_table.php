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
        Schema::create('claim_perils', function (Blueprint $table) {
            $table->string('claim_no', 20);
            $table->decimal('tran_no',20,0)->nullable(false);
            $table->string('peril_name',100)->nullable(false);
            $table->string('dr_cr_note_no',20)->nullable(false);
            $table->string('dr_cr',3)->nullable(false);
            $table->string('entry_type_descr',3)->nullable(true);
            $table->decimal('basic_amount',20,2)->nullable(false);
            $table->decimal('rate',8,5)->nullable(false);
            $table->decimal('final_amount',20,2)->nullable(false);
            $table->string('status',1)->nullable(false);
            $table->decimal('account_year',4,0)->nullable(false);
            $table->decimal('account_month',2,0)->nullable(false);
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_perils');
    }
};
