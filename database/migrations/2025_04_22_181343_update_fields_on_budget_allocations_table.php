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
        Schema::table('budget_allocations', function (Blueprint $table) {
            // $table->dropColumn(['cost_center_id']);
            // $table->dropColumn(['q1_amount', 'q2_amount', 'q3_amount', 'q4_amount']);

            $table->decimal('total_income_budget', 15, 2)->default(0);
            $table->decimal('total_expense_budget', 15, 2)->default(0);
            $table->decimal('q1_expense_amount', 15, 2)->default(0);
            $table->decimal('q2_expense_amount', 15, 2)->default(0);
            $table->decimal('q3_expense_amount', 15, 2)->default(0);
            $table->decimal('q4_expense_amount', 15, 2)->default(0);
            $table->decimal('q1_income_amount', 15, 2)->default(0);
            $table->decimal('q2_income_amount', 15, 2)->default(0);
            $table->decimal('q3_income_amount', 15, 2)->default(0);
            $table->decimal('q4_income_amount', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budget_allocations', function (Blueprint $table) {

            $table->dropColumn([
                'total_income_budget',
                'total_expense_budget',
                'q1_expense_amount',
                'q2_expense_amount',
                'q3_expense_amount',
                'q4_expense_amount',
                'q1_income_amount',
                'q2_income_amount',
                'q3_income_amount',
                'q4_income_amount',
            ]);
        });
    }
};
