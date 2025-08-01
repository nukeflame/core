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
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->string('type_of_bus',3)->nullable(false);
            $table->string('transaction_type',3)->nullable(false);
            $table->string('transaction_type_descr',100)->nullable(false);
            $table->string('ledger_type',3)->nullable(false);
            $table->string('ledger_type_descr',200)->nullable(false);
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
        Schema::dropIfExists('transaction_types');
    }
};
