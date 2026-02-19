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
            if (!Schema::hasColumn('doc_types', 'path')) {
                $table->string('path')->nullable()->after('file_name');
            }

            if (!Schema::hasColumn('doc_types', 's3_path')) {
                $table->string('s3_path')->nullable()->after('path');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('doc_types')) {
            return;
        }

        Schema::table('doc_types', function (Blueprint $table) {
            if (Schema::hasColumn('doc_types', 's3_path')) {
                $table->dropColumn('s3_path');
            }

            if (Schema::hasColumn('doc_types', 'path')) {
                $table->dropColumn('path');
            }
        });
    }
};

