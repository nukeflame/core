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
            $table->dropColumn('notificaction_status');

            $table->string('notification_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_ntf_register', function (Blueprint $table) {
            $table->string('notificaction_status')->nullable();

            $table->dropColumn('notification_status');
        });
    }
};
