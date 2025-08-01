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
        Schema::table('roles', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('slug')->nullable();
            $table->integer('permission_level')->nullable();
            $table->unsignedBigInteger('department_code')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'permission_level', 'department_code', 'is_active', 'slug']);
        });
    }
};
