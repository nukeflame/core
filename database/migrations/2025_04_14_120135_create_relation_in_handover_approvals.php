<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('handover_approvals', function (Blueprint $table) {
            $table->dropColumn([
                'cr_processed',
                'street',
                'country',
                'incorporation_cert',
                'contact_position',
                'contact_phone',
                'contact_email',
                'client_category',
                'contact_salutation',
                'telephone',
                'alternative_contact_name',
                'alternative_email',
                'alternative_phone_number',
                'alternative_salutation',
                'alternative_contact_position',
                'final_premium',
                'final_commission',
                'contact_fullname',
                'division',
                'class_of_insurance',
                'nature_of_engagement',
                'agent_name',
                'agent_comm_rate',
                'cr_handler',
                'bd_handler',
                'cr12',
                'cert_no',
                'town',
                'postal_address',
                'postal_code',
                'quote_currency',
                'prospect_verification_status',
                'occupation_code',
                'full_name',
                'id_type',
                'id_value',
                'salutation_code',
                'pin_no',
                'date_of_birth_registration',
                'gender_code',
                'email',
                'phone_1',
                'phone_2',
                'address_1',
                'address_2',
                'address_3',


            ]);

            $table->foreign('prospect_id')
                ->references('opportunity_id')
                ->on('pipeline_opportunities')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('handover_approvals', function (Blueprint $table) {
            $table->dropForeign(['prospect_id']);

            $table->string('cr_processed', 50)->nullable();
            $table->string('street', 50)->nullable();
            $table->string('country', 5)->nullable();
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
            $table->string('final_premium', 255)->nullable();
            $table->string('final_commission', 255)->nullable();
            $table->string('contact_fullname', 50)->nullable();
            $table->string('division', 5)->nullable();
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
        });
    }
};
