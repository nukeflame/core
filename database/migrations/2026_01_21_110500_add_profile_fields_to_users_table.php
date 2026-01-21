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
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation')->nullable();
            }
            if (!Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable();
            }
            if (!Schema::hasColumn('users', 'experience')) {
                $table->integer('experience')->nullable();
            }
            if (!Schema::hasColumn('users', 'professional_bio')) {
                $table->text('professional_bio')->nullable();
            }
            if (!Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable();
            }
            if (!Schema::hasColumn('users', 'social_networks')) {
                $table->json('social_networks')->nullable();
            }
            if (!Schema::hasColumn('users', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable();
            }
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'phone_number',
                'designation',
                'age',
                'experience',
                'professional_bio',
                'skills',
                'social_networks',
                'location',
                'address',
                'city',
                'country',
                'postal_code'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
