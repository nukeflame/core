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
        Schema::create('claim_register', function (Blueprint $table) {
            $table->bigIncrements('claim_serial_no');
            $table->string('claim_no', 20);
            $table->unsignedBigInteger('customer_id');
            $table->string('cover_no', 20);
            $table->string('endorsement_no', 20);
            $table->date('cover_from');
            $table->date('cover_to');
            $table->string('type_of_bus', 3);
            $table->unsignedSmallInteger('branch_code');
            $table->string('broker_code', 10);
            $table->unsignedSmallInteger('cover_type');
            $table->string('class_group_code', 3);
            $table->string('class_code', 4);
            $table->date('date_of_loss');
            $table->date('date_notified_insurer');
            $table->date('date_notified_reinsurer');
            $table->string('cause_of_loss', 200);
            $table->string('loss_narration', 200);
            $table->string('insured_name', 150);
            $table->date('created_date');
            $table->string('created_by', 20);
            $table->dateTime('created_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('currency_code', 3);
            $table->string('currency_rate', 8);
            $table->char('status', 1);
            
            // You can add more columns or constraints here if needed
            
            // $table->timestamps(); // If you want to include timestamps (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_register');
    }
};
