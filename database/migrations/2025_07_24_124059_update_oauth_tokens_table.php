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
        Schema::table('oauth_tokens', function (Blueprint $table) {
            $table->text('refresh_token')->nullable()->change();
            $table->text('scope')->nullable();
            $table->json('user_info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_tokens', function (Blueprint $table) {
            $table->text('refresh_token');
            $table->dropColumn(['scope', 'user_info']);
        });
    }
};
