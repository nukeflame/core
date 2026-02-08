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
        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->decimal('reinsurance_tax', 10, 2)->default(0)->after('premium_tax');
            $table->decimal('withholding_tax', 10, 2)->default(0)->after('reinsurance_tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->dropColumn([
                'reinsurance_tax',
                'withholding_tax',
            ]);
        });
    }
};
