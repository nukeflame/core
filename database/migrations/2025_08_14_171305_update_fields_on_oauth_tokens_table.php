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
            $table->unsignedBigInteger('user_id');
            $table->string('email')->nullable()->change();
            $table->json('metadata')->nullable();

            $table->unique(['user_id', 'provider', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_tokens', function (Blueprint $table) {
            $table->dropUnique('oauth_tokens_user_id_provider_email_unique');

            $table->dropColumn('user_id');
            $table->dropColumn('metadata');

            $table->string('email')->nullable(false)->change();
        });
    }
};
