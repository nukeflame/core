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
        Schema::table('company_department', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
            $table->string('cost_center')->nullable();
            $table->string('department_code')->nullable()->change();
            $table->decimal('budget', 15, 2)->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('company_department', 'id')->nullOnDelete();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreignId('department_head_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_department', function (Blueprint $table) {
            $table->unsignedSmallInteger('department_code')->nullable();
            $table->dropColumn([
                'id',
                'cost_center',
                'budget',
                'parent_id',
                'location_id',
                'department_head_id',
                'start_date',
                'email',
            ]);
        });
    }
};
