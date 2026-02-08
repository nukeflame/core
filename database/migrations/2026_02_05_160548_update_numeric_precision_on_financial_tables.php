<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Update debit_notes table
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN exchange_rate TYPE NUMERIC(24, 12) USING exchange_rate::numeric(24, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN gross_amount TYPE NUMERIC(28, 12) USING gross_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN commission_rate TYPE NUMERIC(24, 12) USING commission_rate::numeric(24, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN commission_amount TYPE NUMERIC(28, 12) USING commission_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN brokerage_rate TYPE NUMERIC(24, 12) USING brokerage_rate::numeric(24, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN brokerage_amount TYPE NUMERIC(28, 12) USING brokerage_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN premium_levy TYPE NUMERIC(28, 12) USING premium_levy::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN reinsurance_levy TYPE NUMERIC(28, 12) USING reinsurance_levy::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN withholding_tax TYPE NUMERIC(28, 12) USING withholding_tax::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN other_deductions TYPE NUMERIC(28, 12) USING other_deductions::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_notes ALTER COLUMN net_amount TYPE NUMERIC(28, 12) USING net_amount::numeric(28, 12)');

            // Update debit_note_items table
            DB::statement('ALTER TABLE debit_note_items ALTER COLUMN line_rate TYPE NUMERIC(24, 12) USING line_rate::numeric(24, 12)');
            DB::statement('ALTER TABLE debit_note_items ALTER COLUMN amount TYPE NUMERIC(28, 12) USING amount::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_note_items ALTER COLUMN original_amount TYPE NUMERIC(28, 12) USING original_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_note_items ALTER COLUMN net_amount TYPE NUMERIC(28, 12) USING net_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_note_items ALTER COLUMN commission TYPE NUMERIC(28, 12) USING commission::numeric(28, 12)');
            DB::statement('ALTER TABLE debit_note_items ALTER COLUMN premium_tax TYPE NUMERIC(28, 12) USING premium_tax::numeric(28, 12)');

            // Update credit_notes table
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN exchange_rate TYPE NUMERIC(24, 12) USING exchange_rate::numeric(24, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN gross_amount TYPE NUMERIC(28, 12) USING gross_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN commission_rate TYPE NUMERIC(24, 12) USING commission_rate::numeric(24, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN commission_amount TYPE NUMERIC(28, 12) USING commission_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN brokerage_rate TYPE NUMERIC(24, 12) USING brokerage_rate::numeric(24, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN brokerage_amount TYPE NUMERIC(28, 12) USING brokerage_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN premium_levy TYPE NUMERIC(28, 12) USING premium_levy::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN reinsurance_levy TYPE NUMERIC(28, 12) USING reinsurance_levy::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN withholding_tax TYPE NUMERIC(28, 12) USING withholding_tax::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN other_deductions TYPE NUMERIC(28, 12) USING other_deductions::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_notes ALTER COLUMN net_amount TYPE NUMERIC(28, 12) USING net_amount::numeric(28, 12)');

            // Update credit_note_items table
            DB::statement('ALTER TABLE credit_note_items ALTER COLUMN line_rate TYPE NUMERIC(24, 12) USING line_rate::numeric(24, 12)');
            DB::statement('ALTER TABLE credit_note_items ALTER COLUMN amount TYPE NUMERIC(28, 12) USING amount::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_note_items ALTER COLUMN original_amount TYPE NUMERIC(28, 12) USING original_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_note_items ALTER COLUMN net_amount TYPE NUMERIC(28, 12) USING net_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_note_items ALTER COLUMN commission TYPE NUMERIC(28, 12) USING commission::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_note_items ALTER COLUMN brokerage TYPE NUMERIC(28, 12) USING brokerage::numeric(28, 12)');
            DB::statement('ALTER TABLE credit_note_items ALTER COLUMN premium_tax TYPE NUMERIC(28, 12) USING premium_tax::numeric(28, 12)');

            // Update customeracc_det table
            DB::statement('ALTER TABLE customeracc_det ALTER COLUMN foreign_basic_amount TYPE NUMERIC(28, 12) USING foreign_basic_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE customeracc_det ALTER COLUMN local_basic_amount TYPE NUMERIC(28, 12) USING local_basic_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE customeracc_det ALTER COLUMN foreign_taxes_amount TYPE NUMERIC(28, 12) USING foreign_taxes_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE customeracc_det ALTER COLUMN local_taxes_amount TYPE NUMERIC(28, 12) USING local_taxes_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE customeracc_det ALTER COLUMN foreign_nett_amount TYPE NUMERIC(28, 12) USING foreign_nett_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE customeracc_det ALTER COLUMN local_nett_amount TYPE NUMERIC(28, 12) USING local_nett_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE customeracc_det ALTER COLUMN allocated_amount TYPE NUMERIC(28, 12) USING allocated_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE customeracc_det ALTER COLUMN unallocated_amount TYPE NUMERIC(28, 12) USING unallocated_amount::numeric(28, 12)');
            DB::statement('ALTER TABLE customeracc_det ALTER COLUMN currency_rate TYPE NUMERIC(24, 12) USING currency_rate::numeric(24, 12)');
        } else {
            // Update debit_notes table
            Schema::table('debit_notes', function (Blueprint $table) {
                $table->decimal('exchange_rate', 24, 12)->change();
                $table->decimal('gross_amount', 28, 12)->change();
                $table->decimal('commission_rate', 24, 12)->change();
                $table->decimal('commission_amount', 28, 12)->change();
                $table->decimal('brokerage_rate', 24, 12)->change();
                $table->decimal('brokerage_amount', 28, 12)->change();
                $table->decimal('premium_levy', 28, 12)->change();
                $table->decimal('reinsurance_levy', 28, 12)->change();
                $table->decimal('withholding_tax', 28, 12)->change();
                $table->decimal('other_deductions', 28, 12)->change();
                $table->decimal('net_amount', 28, 12)->change();
            });

            // Update debit_note_items table
            Schema::table('debit_note_items', function (Blueprint $table) {
                $table->decimal('line_rate', 24, 12)->change();
                $table->decimal('amount', 28, 12)->change();
                $table->decimal('original_amount', 28, 12)->change();
                $table->decimal('net_amount', 28, 12)->change();
                $table->decimal('commission', 28, 12)->change();
                $table->decimal('premium_tax', 28, 12)->change();
            });

            // Update credit_notes table
            Schema::table('credit_notes', function (Blueprint $table) {
                $table->decimal('exchange_rate', 24, 12)->change();
                $table->decimal('gross_amount', 28, 12)->change();
                $table->decimal('commission_rate', 24, 12)->change();
                $table->decimal('commission_amount', 28, 12)->change();
                $table->decimal('brokerage_rate', 24, 12)->change();
                $table->decimal('brokerage_amount', 28, 12)->change();
                $table->decimal('premium_levy', 28, 12)->change();
                $table->decimal('reinsurance_levy', 28, 12)->change();
                $table->decimal('withholding_tax', 28, 12)->change();
                $table->decimal('other_deductions', 28, 12)->change();
                $table->decimal('net_amount', 28, 12)->change();
            });

            // Update credit_note_items table
            Schema::table('credit_note_items', function (Blueprint $table) {
                $table->decimal('line_rate', 24, 12)->change();
                $table->decimal('amount', 28, 12)->change();
                $table->decimal('original_amount', 28, 12)->change();
                $table->decimal('net_amount', 28, 12)->change();
                $table->decimal('commission', 28, 12)->change();
                $table->decimal('brokerage', 28, 12)->change();
                $table->decimal('premium_tax', 28, 12)->change();
            });

            // Update customeracc_det table
            Schema::table('customeracc_det', function (Blueprint $table) {
                $table->decimal('foreign_basic_amount', 28, 12)->change();
                $table->decimal('local_basic_amount', 28, 12)->change();
                $table->decimal('foreign_taxes_amount', 28, 12)->change();
                $table->decimal('local_taxes_amount', 28, 12)->change();
                $table->decimal('foreign_nett_amount', 28, 12)->change();
                $table->decimal('local_nett_amount', 28, 12)->change();
                $table->decimal('allocated_amount', 28, 12)->change();
                $table->decimal('unallocated_amount', 28, 12)->change();
                $table->decimal('currency_rate', 24, 12)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting precision would be complex as it might vary per column.
        // Usually, financial precision updates are not reverted without specific business reasons.
    }
};
