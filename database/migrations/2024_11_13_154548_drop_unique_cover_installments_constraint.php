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
        if (DB::getDriverName() === 'pgsql') {
            $indexExists = DB::table('pg_indexes')
                ->where('tablename', 'cover_installments')
                ->where('indexname', 'cover_installments_1')
                ->exists();

            if ($indexExists) {
                Schema::table('cover_installments', function (Blueprint $table) {
                    $table->dropUnique('cover_installments_1');
                });
            }

            $indexExists = DB::table('pg_indexes')
                ->where('tablename', 'cover_installments')
                ->where('indexname', 'cover_installments_2')
                ->exists();

            if ($indexExists) {
                Schema::table('cover_installments', function (Blueprint $table) {
                    $table->dropUnique('cover_installments_2');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
