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
            if (! Schema::hasColumn('customeracc_det', 'treaty_debit_no')) {
                $table->string('treaty_debit_no', 50)->nullable();
            }

            if (! Schema::hasColumn('customeracc_det', 'treaty_credit_no')) {
                $table->string('treaty_credit_no', 50)->nullable();
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

            if (Schema::hasColumn('customeracc_det', 'treaty_debit_no')) {
                $columns[] = 'treaty_debit_no';
            }

            if (Schema::hasColumn('customeracc_det', 'treaty_credit_no')) {
                $columns[] = 'treaty_credit_no';
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
