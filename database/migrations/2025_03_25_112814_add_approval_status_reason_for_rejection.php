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
        Schema::table('prospect_handover', function (Blueprint $table) {
            $table->string('approver',10)->nullable();
            $table->string('handler',10)->nullable();
            $table->string('approval_status',2)->nullable();
            $table->longText('reason_for_rejection')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_handover', function (Blueprint $table) {
            $table->dropColumn('approver');
            $table->dropColumn('handler');
            $table->dropColumn('approval_status');
            $table->dropColumn('reason_for_rejection');
        });
    }
};
