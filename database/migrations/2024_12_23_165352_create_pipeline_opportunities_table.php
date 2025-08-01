<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePipelineOpportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pipeline_opportunities', function (Blueprint $table) {
            $table->id(); // This adds an auto-incrementing primary key
            $table->integer('pipeline_id')->nullable();
            $table->string('opportunity_id', 50)->nullable();
            $table->integer('stage')->nullable();
            $table->integer('divisions')->nullable();
            $table->string('currency', 50)->nullable();
            $table->integer('premium')->nullable();
            $table->integer('fiscal_period')->nullable();
            $table->integer('insurance_class')->nullable();
            $table->integer('engage_type')->nullable();
            $table->string('effective_date', 50)->nullable();
            $table->string('closing_date', 50)->nullable();
            $table->float('income')->nullable();
            $table->string('lead_owner', 50)->nullable();
            $table->string('lead_status', 50)->nullable();
            $table->string('contact_name', 50)->nullable();
            $table->string('physical_address', 50)->nullable();
            $table->string('rating', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->integer('phone')->nullable();
            $table->integer('lead_source')->nullable();
            $table->string('source_desc', 50)->nullable();
            $table->string('customer_id', 50)->nullable();
            $table->string('fullname', 50)->nullable();
            $table->string('prequalification', 50)->nullable();
            $table->string('industry', 50)->nullable();
            $table->string('client_type', 50)->nullable();
            $table->string('client_category', 50)->nullable();
            $table->integer('pip_year')->nullable();
            $table->string('handed_over', 50)->nullable();
            $table->string('lead_handler', 50)->nullable();
            $table->string('created_at', 50)->nullable();
            $table->string('updated_at', 50)->nullable();
            $table->string('contact_position', 50)->nullable();
            $table->integer('country_code')->nullable();
            $table->integer('telephone')->nullable();
            $table->string('alternate_contact', 50)->nullable();
            $table->string('alternate_email', 50)->nullable();
            $table->integer('alternate_phone')->nullable();
            $table->string('alternate_position', 50)->nullable();
            $table->string('town', 50)->nullable();
            $table->float('production_cost')->nullable();
            $table->string('prod_currency', 50)->nullable();
            $table->string('narration', 50)->nullable();
            $table->string('pq_status', 50)->nullable();
            $table->string('postal_address', 50)->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->text('pq_comments')->nullable();

            // Optional: You can also add indexes or foreign keys if needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pipeline_opportunities');
    }
}
