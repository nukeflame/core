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
        Schema::table('fetched_emails', function (Blueprint $table) {
            $table->unique(['user_id', 'uid'], 'fetched_emails_user_id_uid_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fetched_emails', function (Blueprint $table) {
            $table->dropUnique('fetched_emails_user_id_uid_unique');
        });
    }
};
