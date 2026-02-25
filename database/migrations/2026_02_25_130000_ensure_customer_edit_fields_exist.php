<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'state')) {
                $table->string('state', 100)->nullable();
            }

            if (!Schema::hasColumn('customers', 'security_rating')) {
                $table->string('security_rating', 50)->nullable();
            }

            if (!Schema::hasColumn('customers', 'rating_agency')) {
                $table->string('rating_agency', 100)->nullable();
            }

            if (!Schema::hasColumn('customers', 'rating_date')) {
                $table->date('rating_date')->nullable();
            }

            if (!Schema::hasColumn('customers', 'insured_type')) {
                $table->string('insured_type', 50)->nullable();
            }

            if (!Schema::hasColumn('customers', 'industry_occupation')) {
                $table->string('industry_occupation', 255)->nullable();
            }

            if (!Schema::hasColumn('customers', 'date_of_birth_incorporation')) {
                $table->date('date_of_birth_incorporation')->nullable();
            }

            if (!Schema::hasColumn('customers', 'regulator_license_no')) {
                $table->string('regulator_license_no', 100)->nullable();
            }

            if (!Schema::hasColumn('customers', 'licensing_authority')) {
                $table->string('licensing_authority', 255)->nullable();
            }

            if (!Schema::hasColumn('customers', 'licensing_territory')) {
                $table->string('licensing_territory', 100)->nullable();
            }

            if (!Schema::hasColumn('customers', 'aml_details')) {
                $table->text('aml_details')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = [
                'state',
                'security_rating',
                'rating_agency',
                'rating_date',
                'insured_type',
                'industry_occupation',
                'date_of_birth_incorporation',
                'regulator_license_no',
                'licensing_authority',
                'licensing_territory',
                'aml_details',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

