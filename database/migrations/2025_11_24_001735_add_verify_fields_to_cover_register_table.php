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
        Schema::table('cover_register', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_register', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);

            $table->dropColumn([
                'verified_by',
                'verified_at',
            ]);
        });
    }
};
