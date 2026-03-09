<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customeracc_det', function (Blueprint $table) {
            if (! Schema::hasColumn('customeracc_det', 'profit_comm_rate')) {
                $table->decimal('profit_comm_rate', 10, 4)->default(0);
            }

            if (! Schema::hasColumn('customeracc_det', 'mgnt_exp_rate')) {
                $table->decimal('mgnt_exp_rate', 10, 4)->default(0);
            }

            if (! Schema::hasColumn('customeracc_det', 'compute_premium_tax')) {
                $table->boolean('compute_premium_tax')->default(false);
            }

            if (! Schema::hasColumn('customeracc_det', 'compute_reinsurance_tax')) {
                $table->boolean('compute_reinsurance_tax')->default(false);
            }

            if (! Schema::hasColumn('customeracc_det', 'compute_withholding_tax')) {
                $table->boolean('compute_withholding_tax')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('customeracc_det', function (Blueprint $table) {
            $toDrop = [];

            foreach ([
                'profit_comm_rate',
                'mgnt_exp_rate',
                'compute_premium_tax',
                'compute_reinsurance_tax',
                'compute_withholding_tax',
            ] as $column) {
                if (Schema::hasColumn('customeracc_det', $column)) {
                    $toDrop[] = $column;
                }
            }

            if (! empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
