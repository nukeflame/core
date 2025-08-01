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
        // Schema::table('budget_allocations', function (Blueprint $table) {
        //     $table->string('budget_name')->nullable();
        //     $table->string('description')->nullable();
        //     $table->foreignId('created_by')->nullable()->constrained('users');
        //     $table->foreignId('updated_by')->nullable()->constrained('users');
        //     $table->timestamp('deleted_at')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('budget_allocations', function (Blueprint $table) {
        //     $table->dropColumn([
        //         'budget_name',
        //         'description',
        //         'created_by',
        //         'updated_by',
        //         'deleted_at'
        //     ]);
        // });
    }
};
