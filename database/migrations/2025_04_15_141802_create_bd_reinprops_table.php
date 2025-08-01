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
        Schema::create('bd_reinprops', function (Blueprint $table) {
            $table->id();
            $table->string('opportunity_id', 20);
            // $table->string('cover_no', 20);
            // $table->string('endorsement_no', 20);
            $table->string('reinclass', 4);
            $table->string('item_no', 4);
            $table->string('item_description', 20);
            $table->decimal('retention_rate', 8, 4);
            $table->decimal('treaty_rate', 8, 4);
            $table->decimal('retention_amount', 20, 2);
            $table->decimal('no_of_lines', 20, 2);
            $table->decimal('treaty_amount', 20, 2);
            $table->decimal('treaty_limit', 20, 2);
            $table->decimal('port_prem_rate', 8, 4);
            $table->decimal('port_loss_rate', 8, 4);
            $table->decimal('profit_comm_rate', 8, 4);
            $table->decimal('mgnt_exp_rate', 8, 4);
            $table->string('deficit_yrs', 4);
            $table->decimal('estimated_income', 20, 2);
            $table->decimal('cashloss_limit', 20, 2);
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            // $table->unique(['cover_no', 'endorsement_no', 'reinclass', 'item_no']);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('opportunity_id')->references('opportunity_id')->on('pipeline_opportunities')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bd_reinprops');
    }
};
