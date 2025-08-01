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
        Schema::create('clauses_param', function (Blueprint $table) {
            $table->string('clause_id',20)->nullable(false)->primary();;
            $table->string('class_code',20)->nullable(false);
            $table->string('clause_title',300)->nullable(false);
            $table->text('clause_wording')->nullable(false);
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->string('status',1)->nullable(false);
            $table->timestamps();
            $table->index(['clause_id'], 'clauses_param_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clauses_param');
    }
};
