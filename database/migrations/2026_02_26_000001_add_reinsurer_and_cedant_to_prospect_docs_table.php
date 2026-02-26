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
        Schema::table('prospect_docs', function (Blueprint $table) {
            if (!Schema::hasColumn('prospect_docs', 'reinsurer_id')) {
                $table->integer('reinsurer_id')->nullable()->index();
            }

            if (!Schema::hasColumn('prospect_docs', 'cedant_id')) {
                $table->integer('cedant_id')->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_docs', function (Blueprint $table) {
            if (Schema::hasColumn('prospect_docs', 'reinsurer_id')) {
                $table->dropIndex(['reinsurer_id']);
                $table->dropColumn('reinsurer_id');
            }

            if (Schema::hasColumn('prospect_docs', 'cedant_id')) {
                $table->dropIndex(['cedant_id']);
                $table->dropColumn('cedant_id');
            }
        });
    }
};
