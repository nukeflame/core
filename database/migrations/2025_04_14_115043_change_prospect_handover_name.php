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
        if (Schema::hasTable('prospect_handover')) {
            Schema::rename('prospect_handover', 'handover_approvals');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('handover_approvals')) {
            Schema::rename('handover_approvals', 'prospect_handover');
        }
    }
};
