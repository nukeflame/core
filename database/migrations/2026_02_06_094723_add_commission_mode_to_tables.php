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
        Schema::table('coverripart', function (Blueprint $table) {
            $table->string('commission_mode', 10)->default('gross')->after('commission_rate')
                ->comment('Commission calculation mode: gross or net');
        });

        Schema::table('cover_register', function (Blueprint $table) {
            $table->string('commission_mode', 10)->default('gross')->after('share_offered')
                ->comment('Commission calculation mode: gross or net');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverripart', function (Blueprint $table) {
            $table->dropColumn('commission_mode');
        });

        Schema::table('cover_register', function (Blueprint $table) {
            $table->dropColumn('commission_mode');
        });
    }
};
