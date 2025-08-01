<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('prospect_rein_layers', function (Blueprint $table) {
            $table->id();
            $table->string('pipeline_id');
            $table->string('opportunity_id');
            $table->string('layer_no');
            $table->decimal('indemnity_limit', 15, 2)->default(0);
            $table->decimal('underlying_limit', 15, 2)->default(0);
            $table->decimal('egnpi', 15, 2);
            $table->string('method');
            $table->string('payment_frequency');
            $table->string('reinclass');
            $table->string('reinstatement_type');
            $table->decimal('reinstatement_value', 15, 2)->default(0);
            $table->string('item_no');
            $table->decimal('flat_rate', 15, 2)->default(0);
            $table->decimal('min_bc_rate', 15, 2)->default(0);
            $table->decimal('max_bc_rate', 15, 2)->default(0);
            $table->decimal('upper_adj', 15, 2)->default(0);
            $table->decimal('lower_adj', 15, 2)->default(0);
            $table->decimal('min_deposit', 15, 2)->default(0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospect_rein_layers');
    }
};
