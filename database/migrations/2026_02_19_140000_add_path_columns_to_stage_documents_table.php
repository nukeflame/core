<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stage_documents')) {
            return;
        }

        Schema::table('stage_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('stage_documents', 'path')) {
                $table->string('path')->nullable()->after('type_of_bus');
            }

            if (!Schema::hasColumn('stage_documents', 's3_path')) {
                $table->string('s3_path')->nullable()->after('path');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('stage_documents')) {
            return;
        }

        Schema::table('stage_documents', function (Blueprint $table) {
            if (Schema::hasColumn('stage_documents', 's3_path')) {
                $table->dropColumn('s3_path');
            }

            if (Schema::hasColumn('stage_documents', 'path')) {
                $table->dropColumn('path');
            }
        });
    }
};

