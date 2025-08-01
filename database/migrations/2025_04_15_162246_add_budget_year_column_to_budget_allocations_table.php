<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBudgetYearColumnToBudgetAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('budget_allocations', function (Blueprint $table) {
            if (!Schema::hasColumn('budget_allocations', 'budget_year')) {
                $table->year('budget_year')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('budget_allocations', function (Blueprint $table) {
            if (Schema::hasColumn('budget_allocations', 'budget_year')) {
                $table->dropColumn('budget_year');
            }
        });
    }
}
