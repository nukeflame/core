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
        Schema::create('tax_types', function (Blueprint $table) {
            $table->string('tax_type', 8)->nullable(false);
            $table->string('type_description', 60)->nullable(false);
            $table->string('add_deduct', 1)->nullable(false);
            $table->string('control_account', 8)->nullable(false);
            $table->string('transtype', 20)->nullable(false);
            $table->string('basis', 20)->nullable(false);
            $table->string('tax_code', 8)->nullable(false);
            $table->string('analyse', 1)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_types');
    }
};
