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
        if (!Schema::hasTable('slip_templates')) {
            return;
        }

        Schema::table('slip_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('slip_templates', 'class_group_code')) {
                $table->string('class_group_code', 20)->nullable()->after('schedule_title');
            }

            if (!Schema::hasColumn('slip_templates', 'class_code')) {
                $table->string('class_code', 20)->nullable()->after('class_group_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('slip_templates')) {
            return;
        }

        Schema::table('slip_templates', function (Blueprint $table) {
            if (Schema::hasColumn('slip_templates', 'class_code')) {
                $table->dropColumn('class_code');
            }

            if (Schema::hasColumn('slip_templates', 'class_group_code')) {
                $table->dropColumn('class_group_code');
            }
        });
    }
};

