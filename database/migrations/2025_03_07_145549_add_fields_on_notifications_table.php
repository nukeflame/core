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
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('notification_type')->nullable();
            $table->string('status')->nullable();
            $table->string('priority')->nullable();
            $table->string('amount')->nullable();
            $table->string('client')->nullable();
            $table->string('cover_no')->nullable();
            $table->string('endorsement_no')->nullable();
            $table->string('underwriter')->nullable();
            $table->unsignedBigInteger('approval_tracker_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn([
                'notification_type',
                'status',
                'priority',
                'amount',
                'client',
                'cover_no',
                'endorsement_no',
                'underwriter',
                'approval_tracker_id'
            ]);
        });
    }
};
