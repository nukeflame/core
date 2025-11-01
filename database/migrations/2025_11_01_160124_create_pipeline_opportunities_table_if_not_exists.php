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
        if (!Schema::hasTable('pipeline_opportunities')) {
            Schema::create('pipeline_opportunities', function (Blueprint $table) {
                $table->id();
                $table->string('opportunity_id')->unique()->index();

                // Customer/Client Information
                $table->integer('customer_id')->nullable()->index();
                $table->string('insured_name')->nullable();
                $table->string('client_category', 10)->nullable()->comment('O=Organic, N=New');
                $table->json('contact_name')->nullable();
                $table->json('email')->nullable();
                $table->json('phone')->nullable();
                $table->json('telephone')->nullable();

                // Business Type and Classification
                $table->string('type_of_bus', 10)->nullable()->index()->comment('TPR=Treaty Proportional, TNP=Treaty Non-Proportional, FPR=Facultative Proportional, FNP=Facultative Non-Proportional');
                $table->integer('classcode')->nullable()->index();
                $table->string('divisions', 10)->nullable()->index();

                // Financial Information
                $table->decimal('cede_premium', 15, 2)->nullable()->comment('Estimated/Ceded Premium');
                $table->decimal('comm_rate', 5, 2)->nullable()->comment('Commission Rate');
                $table->decimal('expected_premium', 15, 2)->nullable();
                $table->decimal('gross_premium', 15, 2)->nullable();

                // Stage and Status
                $table->integer('stage')->default(1)->index()->comment('1=Qualification, 2=Proposal, 3=Due Diligence, 4=Negotiation, 5=Approval');
                $table->integer('probability')->default(0)->comment('Win Probability 0-100');
                $table->string('priority', 20)->default('medium')->index()->comment('critical, high, medium, low');
                $table->string('status', 50)->nullable()->index()->comment('active, pending, closed, cancelled');
                $table->timestamp('stage_updated_at')->nullable();

                // Actions and Next Steps
                $table->string('next_action')->nullable();
                $table->date('expected_closure_date')->nullable();

                // Dates
                $table->date('effective_date')->nullable()->index();
                $table->date('closing_date')->nullable()->index();
                $table->date('expiry_date')->nullable();
                $table->date('fac_date_offered')->nullable();
                $table->date('quote_deadline')->nullable();

                // Prequalification
                $table->char('prequalification', 1)->default('N')->comment('Y/N');
                $table->char('pq_status', 1)->nullable()->comment('P=Proposal, W=Won, L=Lost, C=Closed');
                $table->text('pq_comments')->nullable();

                // Pipeline and Assignment
                $table->integer('pipeline_id')->nullable()->index()->comment('References pipeline ID if submitted to sales');
                $table->integer('lead_owner')->nullable()->index();
                $table->string('pip_year', 4)->nullable()->index();

                // Additional Metadata
                $table->text('description')->nullable();
                $table->string('territory_id')->nullable();
                $table->string('account_executive')->nullable();
                $table->char('cr_processed', 1)->default('N')->comment('Cover Register Processed');

                // Audit Fields
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Indexes for performance
                $table->index(['type_of_bus', 'stage']);
                $table->index(['type_of_bus', 'status']);
                $table->index(['customer_id', 'type_of_bus']);
            });
        } else {
            Schema::table('pipeline_opportunities', function (Blueprint $table) {
                // Add missing columns if table exists
                if (!Schema::hasColumn('pipeline_opportunities', 'stage')) {
                    $table->integer('stage')->default(1)->index()->comment('1=Qualification, 2=Proposal, 3=Due Diligence, 4=Negotiation, 5=Approval');
                }

                if (!Schema::hasColumn('pipeline_opportunities', 'probability')) {
                    $table->integer('probability')->default(0)->comment('Win Probability 0-100');
                }

                if (!Schema::hasColumn('pipeline_opportunities', 'priority')) {
                    $table->string('priority', 20)->default('medium')->index()->comment('critical, high, medium, low');
                }

                if (!Schema::hasColumn('pipeline_opportunities', 'next_action')) {
                    $table->string('next_action')->nullable();
                }

                if (!Schema::hasColumn('pipeline_opportunities', 'expected_closure_date')) {
                    $table->date('expected_closure_date')->nullable();
                }

                if (!Schema::hasColumn('pipeline_opportunities', 'stage_updated_at')) {
                    $table->timestamp('stage_updated_at')->nullable();
                }

                if (!Schema::hasColumn('pipeline_opportunities', 'status')) {
                    $table->string('status', 50)->nullable()->index()->comment('active, pending, closed, cancelled');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop if explicitly requested - use with caution
        // Schema::dropIfExists('pipeline_opportunities');

        // Or remove only the new columns
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            if (Schema::hasColumn('pipeline_opportunities', 'stage')) {
                $table->dropColumn('stage');
            }
            if (Schema::hasColumn('pipeline_opportunities', 'probability')) {
                $table->dropColumn('probability');
            }
            if (Schema::hasColumn('pipeline_opportunities', 'priority')) {
                $table->dropColumn('priority');
            }
            if (Schema::hasColumn('pipeline_opportunities', 'next_action')) {
                $table->dropColumn('next_action');
            }
            if (Schema::hasColumn('pipeline_opportunities', 'expected_closure_date')) {
                $table->dropColumn('expected_closure_date');
            }
            if (Schema::hasColumn('pipeline_opportunities', 'stage_updated_at')) {
                $table->dropColumn('stage_updated_at');
            }
            if (Schema::hasColumn('pipeline_opportunities', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
