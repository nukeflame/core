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
        Schema::table('customeracc_det', function (Blueprint $table) {
            if (!Schema::hasColumn('customeracc_det', 'premium_levy')) {
                $table->decimal('premium_levy', 24, 12)->default(0)->after('local_taxes_amount');
            }

            if (!Schema::hasColumn('customeracc_det', 'reinsurance_levy')) {
                $table->decimal('reinsurance_levy', 24, 12)->default(0)->after('premium_levy');
            }

            if (!Schema::hasColumn('customeracc_det', 'withholding_tax')) {
                $table->decimal('withholding_tax', 24, 12)->default(0)->after('reinsurance_levy');
            }

            if (!Schema::hasColumn('customeracc_det', 'loss_participation')) {
                $table->boolean('loss_participation')->default(false)->after('withholding_tax');
            }

            if (!Schema::hasColumn('customeracc_det', 'sliding_commission')) {
                $table->boolean('sliding_commission')->default(false)->after('loss_participation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customeracc_det', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('customeracc_det', 'sliding_commission')) {
                $columns[] = 'sliding_commission';
            }

            if (Schema::hasColumn('customeracc_det', 'loss_participation')) {
                $columns[] = 'loss_participation';
            }

            if (Schema::hasColumn('customeracc_det', 'withholding_tax')) {
                $columns[] = 'withholding_tax';
            }

            if (Schema::hasColumn('customeracc_det', 'reinsurance_levy')) {
                $columns[] = 'reinsurance_levy';
            }

            if (Schema::hasColumn('customeracc_det', 'premium_levy')) {
                $columns[] = 'premium_levy';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
