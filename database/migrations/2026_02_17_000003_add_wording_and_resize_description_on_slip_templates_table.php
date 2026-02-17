<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        if (!Schema::hasColumn('slip_templates', 'wording')) {
            Schema::table('slip_templates', function (Blueprint $table) {
                $table->text('wording')->nullable()->after('description');
            });
        }

        if (Schema::hasColumn('slip_templates', 'description')) {
            DB::table('slip_templates')
                ->where(function ($query) {
                    $query->whereNull('wording')->orWhere('wording', '');
                })
                ->update([
                    'wording' => DB::raw('description'),
                ]);

            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement(
                    "UPDATE slip_templates SET description = LEFT(description, 255) WHERE description IS NOT NULL"
                );
                DB::statement(
                    "ALTER TABLE slip_templates MODIFY description VARCHAR(255) NULL"
                );
            } elseif ($driver === 'pgsql') {
                DB::statement(
                    "UPDATE slip_templates SET description = SUBSTRING(description FROM 1 FOR 255) WHERE description IS NOT NULL"
                );
                DB::statement(
                    "ALTER TABLE slip_templates ALTER COLUMN description TYPE VARCHAR(255) USING SUBSTRING(description FROM 1 FOR 255)"
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('slip_templates')) {
            return;
        }

        $driver = DB::getDriverName();
        if (Schema::hasColumn('slip_templates', 'description')) {
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE slip_templates MODIFY description TEXT NULL");
            } elseif ($driver === 'pgsql') {
                DB::statement("ALTER TABLE slip_templates ALTER COLUMN description TYPE TEXT");
            }
        }

        if (Schema::hasColumn('slip_templates', 'wording')) {
            Schema::table('slip_templates', function (Blueprint $table) {
                $table->dropColumn('wording');
            });
        }
    }
};

