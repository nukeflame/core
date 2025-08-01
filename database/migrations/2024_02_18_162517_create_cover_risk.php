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
        Schema::create('cover_risk', function (Blueprint $table) {
            $table->decimal('id',20,0)->nullable(false)->primary();
            $table->string('cover_no',20)->nullable(false);
            $table->string('endorsement_no',20)->nullable(false);
            $table->string('title',100)->nullable(false);
            $table->string('address',200)->nullable(false);
            $table->decimal('sum_insured',20,2)->nullable(false)->default(0);
            $table->decimal('premium',20,2)->nullable(false)->default(0);
            $table->string('cancelled',1)->nullable(false)->default('N');
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
        Schema::dropIfExists('cover_risk');
    }
};
