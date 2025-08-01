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
            $table->text('description')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_department', function (Blueprint $table) {
            $table->dropColumn(['manager_id', 'description']);
        });
    }
};
