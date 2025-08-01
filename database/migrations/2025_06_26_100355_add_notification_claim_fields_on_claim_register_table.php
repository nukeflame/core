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
        Schema::table('claim_register', function (Blueprint $table) {
            $table->string('notificaction_status')->default('notification_sent');
            $table->timestamp('notification_sent_at')->nullable();
            $table->unsignedBigInteger('notification_sent_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_register', function (Blueprint $table) {
            $table->dropColumn(['notificaction_status', 'notification_sent_at', 'notification_sent_by']);
        });
    }
};
