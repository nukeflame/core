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
        Schema::create('user_budget_setups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('budget_setup_id')->nullable();
            $table->decimal('est_production', 18, 2)->default(0);
            $table->decimal('return_on_investment', 18, 2)->default(0);
            $table->json('sectors')->nullable()->comment('Array of selected sectors');
            $table->json('policies')->nullable()->comment('Array of selected policies');
            $table->json('categories')->nullable()->comment('Array of selected categories');
            $table->string('status', 5)->default('A')->comment('A=Active, D=Inactive');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('budget_setup_id')->references('id')->on('budget_setups')->onDelete('set null');
            $table->unique(['user_id'], 'user_budget_setups_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_budget_setups');
    }
};
