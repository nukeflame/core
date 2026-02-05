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
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();

            $table->string('debit_note_no', 30)->unique();
            $table->string('cover_no', 50)->index();
            $table->string('endorsement_no', 50)->nullable()->unique();
            $table->string('type_of_bus', 10)->comment('FAC, TREATY, RETRO');
            $table->unsignedSmallInteger('installment_no')->default(1);

            $table->year('posting_year');
            $table->string('posting_quarter', 5)->comment('Q1, Q2, Q3, Q4');
            $table->date('posting_date');
            $table->string('currency', 3)->default('KES');
            $table->decimal('exchange_rate', 24, 12)->default(1.000000);

            $table->decimal('gross_amount', 28, 12)->default(0);
            $table->decimal('commission_rate', 24, 12)->default(0);
            $table->decimal('commission_amount', 28, 12)->default(0);
            $table->decimal('brokerage_rate', 24, 12)->default(0);
            $table->decimal('brokerage_amount', 28, 12)->default(0);
            $table->decimal('premium_levy', 28, 12)->default(0);
            $table->decimal('reinsurance_levy', 28, 12)->default(0);
            $table->decimal('withholding_tax', 28, 12)->default(0);
            $table->decimal('other_deductions', 28, 12)->default(0);
            $table->decimal('net_amount', 28, 12)->default(0);

            $table->boolean('compute_premium_tax')->default(false);
            $table->boolean('compute_reinsurance_tax')->default(false);
            $table->boolean('compute_withholding_tax')->default(false);
            $table->boolean('loss_participation')->default(false);
            $table->boolean('sliding_commission')->default(false);
            $table->boolean('show_cedant')->default(false);
            $table->boolean('show_reinsurer')->default(false);

            $table->text('comments')->nullable();
            $table->text('internal_notes')->nullable();

            $table->string('status', 20)->default('DRAFT');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('posting_date');
            $table->index(['posting_year', 'posting_quarter']);
            $table->index(['cover_no', 'endorsement_no', 'installment_no'], 'dn_cover_installment_idx');
            $table->index('created_at');
            $table->unique(['cover_no', 'endorsement_no'], 'dn_cover_endorsement_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_notes');
    }
};
