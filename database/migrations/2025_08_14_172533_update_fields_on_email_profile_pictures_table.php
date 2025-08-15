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
        Schema::table('email_profile_pictures', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');

            $table->unique(['user_id', 'sender_email']);
            $table->index(['sender_email', 'downloaded']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_profile_pictures', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'sender_email']);
            $table->dropIndex(['sender_email', 'downloaded']);
        });
    }
};
