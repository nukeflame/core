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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department')) {
                $table->dropColumn('department');
            }
            if (Schema::hasColumn('users', 'pass_expiry_date')) {
                $table->dropColumn('pass_expiry_date');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_password_reset')->default(false);
            $table->boolean('two_factor_enabled')->default(false);
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('password_expires_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->foreignId('role_id')->constrained();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->date('pass_expiry_date')->nullable();
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone_number',
                'department_id',
                'is_active',
                'created_by',
                'two_factor_enabled',
                'failed_login_attempts',
                'requires_password_reset',
                'password_expires_at',
                'deleted_at',
                'last_login',
                'role_id'
            ]);
        });
    }
};
