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
        Schema::table('claim_ntf_register', function (Blueprint $table) {
            $table->timestamp('notification_failed_at')->nullable();
            $table->string('notification_failure_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_ntf_register', function (Blueprint $table) {
            $table->dropColumn(['notification_failed_at', 'notification_failure_reason']);
        });
    }
};
