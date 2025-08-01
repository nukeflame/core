<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectPremiumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospect_premiums', function (Blueprint $table) {
            $table->id(); 
            $table->string('pipeline_id');
            $table->string('opportunity_id');
            $table->string('orig_opportunity_id')->nullable();
            $table->string('transaction_type');
            $table->integer('premium_type_code')->default(0);
            $table->string('premtype_name')->default('Gross Premium');
            $table->string('quarter')->nullable();
            $table->string('entry_type_descr')->default('PRM');
            $table->integer('premium_type_order_position')->default(1);
            $table->string('premium_type_description')->default('Gross Premium');
            $table->string('type_of_bus')->nullable();
            $table->string('class_code')->nullable();
            $table->decimal('basic_amount', 15, 2)->default(0);
            $table->char('apply_rate_flag', 1)->default('Y');
            $table->string('treaty')->default('FAC');
            $table->decimal('rate', 10, 2)->default(0);
            $table->char('dr_cr', 2)->default('DR');
            $table->decimal('final_amount', 15, 2)->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospect_premiums');
    }
}
