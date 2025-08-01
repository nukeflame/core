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
        Schema::create('glbatchdtl', function (Blueprint $table) {
            $table->string('batch_no',20);
            $table->string('item_no',20);
            $table->string('item_description',200);
            $table->string('glaccount',8);
            $table->decimal('foreign_cr_amount',20,2);
            $table->decimal('local_cr_amount',20,2);
            $table->decimal('foreign_dr_amount',20,2);
            $table->decimal('local_dr_amount',20,2);
            $table->string('dr_cr',1);
            $table->string('currency_code',3);
            $table->decimal('currency_rate',10,4);
            $table->string('deleted',1);
            $table->string('offcd',3);
            $table->string('payee_name',200);
            $table->string('reference_no',20);
            $table->string('entry_type_descr',3);
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->timestamps();

            $table->unique(['batch_no','item_no'], 'glbatchdtl_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glbatchdtl');
    }
};
