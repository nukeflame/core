<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->string('group_id', 10)->nullable(false);
            $table->string('tax_type', 8)->nullable(false);
            $table->string('tax_code', 8)->nullable(false);
            $table->string('tax_description', 80)->nullable(false);
            $table->string('tax_rate', 5)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
