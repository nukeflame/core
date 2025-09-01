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
        Schema::create('claim_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('claim_ntf_register_id');
            $table->string('intimation_no', 50)->index();
            $table->enum('status', [
                'NOTIFICATION',
                'DEBIT_CREATION',
                'CLAIMS_ENQUIRY',
                'SETTLEMENT',
                'COMPLETED',
                'CANCELLED',
                'REJECTED'
            ])->index();
            $table->enum('stage', [
                'PENDING',
                'IN_PROGRESS',
                'COMPLETED',
                'APPROVED',
                'REJECTED',
                'ON_HOLD'
            ])->default('PENDING');
            $table->text('remarks')->nullable();
            $table->json('additional_data')->nullable()->comment('Store additional status-specific data');
            $table->decimal('amount', 15, 2)->nullable()->comment('Amount related to this status change');
            $table->string('reference_no', 100)->nullable()->comment('External reference number');
            $table->timestamp('status_date')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('claim_ntf_register_id')->references('serial_no')->on('claim_ntf_register')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['claim_ntf_register_id', 'status']);
            $table->index(['intimation_no', 'created_at']);
            $table->index('status_date');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_status_logs');
    }
};
