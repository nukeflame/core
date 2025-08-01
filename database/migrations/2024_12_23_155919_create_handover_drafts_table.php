<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHandoverDraftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handover_drafts', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->integer('user_id')->nullable();
            $table->string('prospect_id', 50)->nullable();
            $table->string('full_name', 50)->nullable();
            $table->string('client_type', 50)->nullable();
            $table->string('client_category', 50)->nullable();
            $table->integer('division')->nullable();
            $table->integer('cr_handler')->nullable();
            $table->string('bd_handler', 50)->nullable();
            $table->string('incorporation_cert', 50)->nullable();
            $table->integer('class_of_insurance')->nullable();
            $table->integer('nature_of_engagement')->nullable();
            $table->string('id_type', 50)->nullable();
            $table->string('id_value', 50)->nullable();
            $table->string('cert_no', 50)->nullable();
            $table->string('date_of_birth_registration', 50)->nullable();
            $table->string('salutation_code', 50)->nullable();
            $table->string('alternative_salutation', 50)->nullable();
            $table->string('alternative_contact_name', 50)->nullable();
            $table->string('alternative_contact_email', 50)->nullable();
            $table->integer('alternative_phone_number')->nullable();
            $table->string('alternative_contact_position', 50)->nullable();
            $table->integer('pin_no')->nullable();
            $table->string('cr12', 50)->nullable();
            $table->string('gender_code', 50)->nullable();
            $table->string('occupation_code', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->integer('phone_1')->nullable();
            $table->integer('telephone')->nullable();
            $table->string('inception_date', 50)->nullable();
            $table->string('address_3', 50)->nullable();
            $table->integer('country')->nullable();
            $table->string('town', 50)->nullable();
            $table->string('postal_address', 50)->nullable();
            $table->integer('postal_code')->nullable();
            $table->string('street', 50)->nullable();
            $table->string('agent_name', 50)->nullable();
            $table->float('agent_commission_rate')->nullable();
            $table->float('final_premium')->nullable();
            $table->float('final_commission')->nullable();
            $table->string('remarks', 50)->nullable();
            $table->integer('created_by')->nullable();
            $table->string('date_created', 50)->nullable();
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
        Schema::dropIfExists('handover_drafts');
    }
}
