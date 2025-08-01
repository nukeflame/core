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
        Schema::create('premium_pay_terms', function (Blueprint $table) {
            $table->string('pay_term_code', 20);
            $table->string('pay_term_desc', 200);
            $table->string('status', 1);
            $table->string('created_by', 20)->nullable(false);
            $table->string('updated_by', 20)->nullable(false);
            $table->timestamps();
            $table->unique(['pay_term_code'], 'premium_pay_terms_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premium_pay_terms');
    }
};
