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
        Schema::create('claim_ntf_register', function (Blueprint $table) {
            $table->bigInteger('serial_no'); 
            $table->string('intimation_no', 20); 
            $table->string('cedant_claim_no', 50)->nullable(); 
            $table->bigInteger('customer_id'); 
            $table->string('cover_no', 20); 
            $table->string('endorsement_no', 20); 
            $table->date('cover_from'); 
            $table->date('cover_to'); 
            $table->string('type_of_bus', 3); 
            $table->smallInteger('branch_code'); 
            $table->string('broker_code', 10); 
            $table->smallInteger('cover_type'); 
            $table->string('class_group_code', 3); 
            $table->string('class_code', 4); 
            $table->date('date_of_loss'); 
            $table->date('date_notified_insurer'); 
            $table->date('date_notified_reinsurer'); 
            $table->string('cause_of_loss', 200); 
            $table->string('loss_narration', 200); 
            $table->string('insured_name', 150); 
            $table->string('created_by', 20); 
            $table->string('updated_by', 20); 
            $table->string('currency_code', 3); 
            $table->decimal('currency_rate',20, 2); 
            $table->string('status', 1); 
            $table->string('approval_status', 1)->default('P'); 
            $table->date('approved_date')->nullable(); 
            $table->string('approved_by',20)->nullable(); 
            $table->timestamps(); 

            $table->primary('serial_no'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_ntf_register');
    }
};
