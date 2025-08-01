<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectHandoverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospect_handover', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('prospect_id', 50)->nullable();
            $table->string('full_name', 50)->nullable();
            $table->string('client_type', 50)->nullable();
            $table->string('id_type', 50)->nullable();
            $table->string('id_value', 50)->nullable();
            $table->string('salutation_code', 50)->nullable();
            $table->string('pin_no', 50)->nullable();
            $table->string('date_of_birth_registration', 50)->nullable();
            $table->integer('gender_code')->nullable();
            $table->string('occupation_code', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->integer('phone_1')->nullable();
            $table->string('phone_2', 50)->nullable();
            $table->string('address_1', 50)->nullable();
            $table->string('address_2', 50)->nullable();
            $table->string('address_3', 50)->nullable();
            $table->integer('created_by')->nullable();
            $table->string('date_created', 50)->nullable();
            $table->string('cr_processed', 50)->nullable();
            $table->string('street', 50)->nullable();
            $table->integer('country')->nullable();
            $table->string('incorporation_cert', 50)->nullable();
            $table->string('contact_position', 50)->nullable();
            $table->integer('contact_phone')->nullable();
            $table->string('contact_email', 50)->nullable();
            $table->string('client_category', 50)->nullable();
            $table->string('contact_salutation', 50)->nullable();
            $table->integer('telephone')->nullable();
            $table->string('alternative_contact_name', 50)->nullable();
            $table->string('alternative_email', 50)->nullable();
            $table->integer('alternative_phone_number')->nullable();
            $table->string('alternative_salutation', 50)->nullable();
            $table->string('alternative_contact_position', 50)->nullable();
            $table->integer('final_premium')->nullable();
            $table->integer('final_commission')->nullable();
            $table->string('contact_fullname', 50)->nullable();
            $table->string('remarks', 50)->nullable();
            $table->integer('division')->nullable();
            $table->integer('class_of_insurance')->nullable();
            $table->integer('nature_of_engagement')->nullable();
            $table->string('agent_name', 50)->nullable();
            $table->float('agent_comm_rate')->nullable();
            $table->string('cr_handler', 50)->nullable();
            $table->string('bd_handler', 50)->nullable();
            $table->string('cr12', 50)->nullable();
            $table->string('cert_no', 50)->nullable();
            $table->string('town', 50)->nullable();
            $table->string('postal_address', 50)->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->string('quote_currency', 50)->nullable();
            $table->integer('prospect_verification_status')->nullable();
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospect_handover');
    }
}
