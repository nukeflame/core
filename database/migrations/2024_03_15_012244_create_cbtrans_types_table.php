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
        Schema::create('cbtrans_types', function (Blueprint $table) {
            $table->string('doc_type', 3)->nullable(false);
            $table->string('type_code', 3)->nullable(false);
            $table->string('source_code', 3)->nullable(false);
            $table->string('description', 255)->nullable(false);
            $table->string('debit_account', 8)->nullable(true);
            $table->string('credit_account', 8)->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbtrans_types');
    }
};
