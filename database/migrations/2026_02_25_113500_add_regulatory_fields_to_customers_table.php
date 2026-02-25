<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
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
            if (Schema::hasColumn('customers', 'regulator_license_no')) {
                $table->dropColumn('regulator_license_no');
            }

            if (Schema::hasColumn('customers', 'licensing_authority')) {
                $table->dropColumn('licensing_authority');
            }

            if (Schema::hasColumn('customers', 'licensing_territory')) {
                $table->dropColumn('licensing_territory');
            }

            if (Schema::hasColumn('customers', 'aml_details')) {
                $table->dropColumn('aml_details');
            }
        });
    }
};

