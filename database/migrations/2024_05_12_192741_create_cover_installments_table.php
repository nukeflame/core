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
        Schema::create('cover_installments', function (Blueprint $table) {
            $table->string('cover_no',20)->nullable(false);
            $table->string('endorsement_no',20)->nullable(false);
            $table->string('trans_type',3)->nullable(false);
            $table->string('entry_type',3)->nullable(false);
            $table->decimal('installment_no',4,0)->nullable(false);
            $table->date('installment_date');
            $table->decimal('installment_amt',20,2)->nullable(false);
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_installments');
    }
};
