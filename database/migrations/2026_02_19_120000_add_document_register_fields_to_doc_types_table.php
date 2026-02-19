<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('doc_types')) {
            return;
        }

        Schema::table('doc_types', function (Blueprint $table) {
            if (!Schema::hasColumn('doc_types', 'code')) {
                $table->string('code', 50)->nullable()->after('id');
            }

            if (!Schema::hasColumn('doc_types', 'country')) {
                $table->string('country', 100)->default('All')->after('description');
            }

            if (!Schema::hasColumn('doc_types', 'is_required')) {
                $table->char('is_required', 1)->default('Y')->after('country');
            }

            if (!Schema::hasColumn('doc_types', 'is_default')) {
                $table->char('is_default', 1)->default('Y')->after('is_required');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('doc_types')) {
            return;
        }

        Schema::table('doc_types', function (Blueprint $table) {
            if (Schema::hasColumn('doc_types', 'is_default')) {
                $table->dropColumn('is_default');
            }

            if (Schema::hasColumn('doc_types', 'is_required')) {
                $table->dropColumn('is_required');
            }

            if (Schema::hasColumn('doc_types', 'country')) {
                $table->dropColumn('country');
            }

            if (Schema::hasColumn('doc_types', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
};
