<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_claims', function (Blueprint $table) {
            $table->string('claim_ref_no', 191);
            $table->string('claim_no', 191)->nullable();
            $table->string('policy_holder', 191);
            $table->string('policy_no', 191);
            $table->string('risk_item', 191)->nullable();
            $table->string('client_type', 191)->nullable();
            $table->string('global_customer_id', 191);
            $table->timestamp('date_of_loss', 0);
            $table->timestamp('date_reported', 0);
            $table->string('description', 191)->nullable();
            $table->string('agent_no', 191);
            $table->string('branch', 191);
            $table->string('filename', 191)->nullable();
            $table->string('doc_type', 191)->nullable();
            $table->string('sum_insured', 191);
            $table->string('original_estimate', 191)->nullable();
            $table->string('global_intermediary_id', 191)->nullable();
            $table->string('id_type', 191)->nullable();
            $table->string('risk_no', 191)->nullable();
            $table->string('current_estimate', 191)->nullable();
            $table->string('class', 191);
            $table->string('status', 191);
            $table->timestamp('created_at', 0)->nullable();
            $table->timestamp('updated_at', 0)->nullable();
            $table->string('approved_flag', 2)->nullable();
            $table->string('approved_by', 50)->nullable();
            $table->date('approved_date')->nullable();
            $table->string('comment', 200)->nullable();
            $table->string('endt_renewal_no', 20)->nullable();
            $table->integer('cause_of_loss')->nullable();
            $table->string('location', 50)->nullable();
            $table->time('time_of_loss')->nullable();
            $table->integer('raised_by')->nullable();

            // Optional: Add any indexes or foreign key constraints if necessary
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_claims');
    }
}
