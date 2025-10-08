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
        Schema::create('bd_fac_reinsurers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reinsurer_id')->nullable();
            $table->string('opportunity_id')->nullable();
            $table->string('reinsurer_name')->nullable();
            $table->string('email')->nullable();
            $table->decimal('written_share', 15, 2)->nullable();
            $table->decimal('share_amount', 15, 2)->nullable();
            $table->decimal('premium_share', 15, 2)->nullable();
            $table->decimal('commission_rate', 10, 2)->nullable();
            $table->decimal('brokerage_rate', 10, 2)->nullable();
            $table->string('stage')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('fac_sent_at')->nullable();
            $table->timestamp('fac_responded_at')->nullable();
            $table->text('response_notes')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_lead_reinsurer')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('reinsurer_id');
            $table->index('opportunity_id');
            $table->index('stage');
            $table->index('status');
            $table->index(['opportunity_id', 'reinsurer_id', 'stage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bd_fac_reinsurers');
    }
};
