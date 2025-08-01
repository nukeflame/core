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
        Schema::create('bd_reinlayers', function (Blueprint $table) {
            $table->id();
            $table->string('opportunity_id', 20);
            // $table->string('cover_no', 20);
            // $table->string('endorsement_no', 20);
            $table->string('layer_no', 3);
            $table->decimal('indemnity_limit', 20, 2);
            $table->decimal('underlying_limit', 20, 2);
            $table->decimal('egnpi', 20, 2);
            $table->string('method', 1);
            $table->string('payment_frequency', 1);
            $table->decimal('min_bc_rate', 8, 4);
            $table->decimal('max_bc_rate', 8, 4);
            $table->decimal('flat_rate', 8, 4);
            $table->decimal('upper_adj', 8, 4);
            $table->decimal('lower_adj', 8, 4);
            $table->decimal('min_deposit', 20, 2);
            $table->string('reinclass', 4)->nullable(true);
            $table->string('item_no', 3)->nullable(true);
            // $table->unique(['cover_no', 'endorsement_no', 'layer_no']);
            $table->string('reinstatement_type', 10)->nullable(true);
            $table->decimal('reinstatement_value', 20, 2)->nullable(true);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->foreign('opportunity_id')->references('opportunity_id')->on('pipeline_opportunities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bd_reinlayers');
    }
};
