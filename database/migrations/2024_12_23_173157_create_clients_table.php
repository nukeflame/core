<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->string('global_customer_id', 191)->primary();  // global_customer_id is the primary key
            $table->string('full_name', 191)->nullable();
            $table->string('client_type', 191);
            $table->string('id_type', 191)->nullable();
            $table->string('id_value', 191)->nullable();
            $table->string('salutation_code', 191)->nullable();
            $table->string('pin_no', 191);
            $table->string('date_of_birth_registration', 191)->nullable();
            $table->string('gender_code', 191)->nullable();
            $table->string('occupation_code', 191)->nullable();
            $table->string('email', 191);
            $table->string('phone_1', 191)->nullable();
            $table->string('phone_2', 191)->nullable();
            $table->string('address_1', 191)->nullable();
            $table->string('address_2', 191)->nullable();
            $table->string('address_3', 191)->nullable();
            $table->string('created_by', 191)->nullable();
            $table->date('date_created')->nullable();
            $table->string('cr_processed', 1)->nullable();
            $table->string('street', 30)->nullable();
            $table->string('country', 15)->nullable();
            $table->string('incorporation_cert', 30)->nullable();
            $table->string('contact_position', 50)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->string('contact_email', 50)->nullable();
            $table->string('client_category', 2)->nullable();
            $table->string('contact_salutation', 5)->nullable();
            $table->string('telephone', 15)->nullable();
            $table->string('alternative_contact_name', 50)->nullable();
            $table->string('alternative_email', 50)->nullable();
            $table->string('alternative_phone_number', 50)->nullable();
            $table->string('alternative_salutation', 50)->nullable();
            $table->string('alternative_contact_position', 50)->nullable();
            $table->string('final_premium', 50)->nullable();
            $table->string('final_commission', 50)->nullable();
            $table->string('contact_fullname', 100)->nullable();
            $table->string('remarks', 100)->nullable();
            $table->string('division', 20)->nullable();
            $table->string('class_of_insurance', 20)->nullable();
            $table->string('nature_of_engagement', 20)->nullable();
            $table->string('agent_name', 100)->nullable();
            $table->decimal('agent_comm_rate', 5, 3)->nullable();  // numeric(5,3)
            $table->string('cr_handler', 20)->nullable();
            $table->string('bd_handler', 20)->nullable();
            $table->string('cr12', 50)->nullable();
            $table->string('quote_currency', 3)->nullable();

            // Optional: Add any additional constraints like indexes if needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
