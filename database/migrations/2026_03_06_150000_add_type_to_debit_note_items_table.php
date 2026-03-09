<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('debit_note_items', 'type')) {
            Schema::table('debit_note_items', function (Blueprint $table) {
                $table->string('type', 50)->nullable()->after('ledger');
                $table->index('type', 'debit_note_items_type_index');
            });
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("
                UPDATE debit_note_items dni
                SET type = dn.type
                FROM debit_notes dn
                WHERE dn.id = dni.debit_note_id
                  AND (dni.type IS NULL OR dni.type = '')
            ");
        } elseif ($driver === 'mysql') {
            DB::statement("
                UPDATE debit_note_items dni
                INNER JOIN debit_notes dn ON dn.id = dni.debit_note_id
                SET dni.type = dn.type
                WHERE dni.type IS NULL OR dni.type = ''
            ");
        } else {
            DB::table('debit_note_items')
                ->orderBy('id')
                ->chunkById(1000, function ($rows) {
                    foreach ($rows as $row) {
                        $type = DB::table('debit_notes')
                            ->where('id', $row->debit_note_id)
                            ->value('type');

                        DB::table('debit_note_items')
                            ->where('id', $row->id)
                            ->update(['type' => $type]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('debit_note_items', 'type')) {
            Schema::table('debit_note_items', function (Blueprint $table) {
                $table->dropIndex('debit_note_items_type_index');
                $table->dropColumn('type');
            });
        }
    }
};

