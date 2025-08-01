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
        Schema::create('coverreinlayers', function (Blueprint $table) {
            $table->string('cover_no', 20);
            $table->string('endorsement_no', 20);
            $table->string('layer_no', 3);
            $table->decimal('indemnity_limit',20,2);
            $table->decimal('underlying_limit',20,2);
            $table->decimal('egnpi',20,2);
            $table->string('method', 1);
            $table->string('payment_frequency', 1);
            $table->decimal('min_bc_rate',8,4);
            $table->decimal('max_bc_rate',8,4);
            $table->decimal('flat_rate',8,4);
            $table->decimal('upper_adj',8,4);
            $table->decimal('lower_adj',8,4);
            $table->decimal('min_deposit',20,2);
            $table->timestamp('created_at')->nullable(false);
            $table->timestamp('updated_at')->nullable(true);
            $table->unique(['cover_no','endorsement_no','layer_no'], 'coverreinlayers_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coverreinlayers');
    }
};
