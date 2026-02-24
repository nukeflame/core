<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('quote_schedule_headers')) {
            return;
        }

        Schema::table('quote_schedule_headers', function (Blueprint $table) {
            if (!Schema::hasColumn('quote_schedule_headers', 'slug')) {
                $table->string('slug', 100)->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('quote_schedule_headers')) {
            return;
        }

        Schema::table('quote_schedule_headers', function (Blueprint $table) {
            if (Schema::hasColumn('quote_schedule_headers', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};
