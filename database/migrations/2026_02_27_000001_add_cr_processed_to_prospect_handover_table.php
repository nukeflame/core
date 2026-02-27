<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('prospect_handover') || Schema::hasColumn('prospect_handover', 'cr_processed')) {
            return;
        }

        Schema::table('prospect_handover', function (Blueprint $table): void {
            $table->char('cr_processed', 1)->default('N')->comment('Cover Register Processed');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('prospect_handover') || !Schema::hasColumn('prospect_handover', 'cr_processed')) {
            return;
        }

        Schema::table('prospect_handover', function (Blueprint $table): void {
            $table->dropColumn('cr_processed');
        });
    }
};

