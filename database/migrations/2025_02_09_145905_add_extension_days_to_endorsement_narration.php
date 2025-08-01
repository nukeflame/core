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
        Schema::table('endorsement_narration', function (Blueprint $table) {
            $table->integer('extension_days')->default(0)->after('narration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('endorsement_narration', function (Blueprint $table) {
            $table->dropColumn('extension_days');
        });
    }
};
