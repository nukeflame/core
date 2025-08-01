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
        Schema::create('wht_rates', function (Blueprint $table) {
            $table->integer('id');
            $table->string('description',50);
            $table->decimal('rate',5,2);
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->string('deleted_by',20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wht_rates');
    }
};
