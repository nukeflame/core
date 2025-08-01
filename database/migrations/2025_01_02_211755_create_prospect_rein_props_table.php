<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectReinpropsTable extends Migration
{
    public function up()
    {
        Schema::create('prospect_reinprops', function (Blueprint $table) {
            $table->id();
            $table->string('pipeline_id');
            $table->string('opportunity_id');
            $table->string('reinclass', 100);
            $table->integer('item_no');
            $table->string('item_description', 255);
            $table->decimal('retention_rate', 8, 4);
            $table->decimal('treaty_rate', 8, 4);
            $table->decimal('retention_amount', 15, 2);
            $table->integer('no_of_lines');
            $table->decimal('treaty_amount', 15, 2);
            $table->decimal('treaty_limit', 15, 2);
            $table->decimal('port_prem_rate', 8, 4);
            $table->decimal('port_loss_rate', 8, 4);
            $table->decimal('profit_comm_rate', 8, 4);
            $table->decimal('mgnt_exp_rate', 8, 4);
            $table->integer('deficit_yrs');
            $table->decimal('estimated_income', 15, 2);
            $table->decimal('cashloss_limit', 15, 2);
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prospect_reinprops');
    }
}
