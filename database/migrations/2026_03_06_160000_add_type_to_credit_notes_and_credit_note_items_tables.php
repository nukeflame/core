<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('credit_notes', 'type')) {
            Schema::table('credit_notes', function (Blueprint $table) {
                $table->string('type', 50)->nullable()->after('type_of_bus');
                $table->index('type', 'credit_notes_type_index');
            });
        }

        if (! Schema::hasColumn('credit_note_items', 'type')) {
            Schema::table('credit_note_items', function (Blueprint $table) {
                $table->string('type', 50)->nullable()->after('ledger');
                $table->index('type', 'credit_note_items_type_index');
            });
        }

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("
                UPDATE credit_notes cn
                SET type = dn.type
                FROM debit_notes dn
                WHERE cn.cover_no = dn.cover_no
                  AND cn.endorsement_no = dn.endorsement_no
                  AND cn.posting_year = dn.posting_year
                  AND COALESCE(cn.posting_quarter, '') = COALESCE(dn.posting_quarter, '')
                  AND (cn.type IS NULL OR cn.type = '')
                  AND dn.type IS NOT NULL
            ");

            DB::statement("
                UPDATE credit_note_items cni
                SET type = cn.type
                FROM credit_notes cn
                WHERE cn.id = cni.credit_note_id
                  AND (cni.type IS NULL OR cni.type = '')
            ");
        } elseif ($driver === 'mysql') {
            DB::statement("
                UPDATE credit_notes cn
                INNER JOIN debit_notes dn
                    ON cn.cover_no = dn.cover_no
                   AND cn.endorsement_no = dn.endorsement_no
                   AND cn.posting_year = dn.posting_year
                   AND COALESCE(cn.posting_quarter, '') = COALESCE(dn.posting_quarter, '')
                SET cn.type = dn.type
                WHERE (cn.type IS NULL OR cn.type = '')
                  AND dn.type IS NOT NULL
            ");

            DB::statement("
                UPDATE credit_note_items cni
                INNER JOIN credit_notes cn ON cn.id = cni.credit_note_id
                SET cni.type = cn.type
                WHERE cni.type IS NULL OR cni.type = ''
            ");
        } else {
            DB::table('credit_notes')
                ->orderBy('id')
                ->chunkById(1000, function ($rows) {
                    foreach ($rows as $row) {
                        if (! empty($row->type)) {
                            continue;
                        }

                        $matchedType = DB::table('debit_notes')
                            ->where('cover_no', $row->cover_no)
                            ->where('endorsement_no', $row->endorsement_no)
                            ->where('posting_year', $row->posting_year)
                            ->where('posting_quarter', $row->posting_quarter)
                            ->orderByDesc('id')
                            ->value('type');

                        DB::table('credit_notes')
                            ->where('id', $row->id)
                            ->update(['type' => $matchedType]);
                    }
                });

            DB::table('credit_note_items')
                ->orderBy('id')
                ->chunkById(1000, function ($rows) {
                    foreach ($rows as $row) {
                        $type = DB::table('credit_notes')
                            ->where('id', $row->credit_note_id)
                            ->value('type');

                        DB::table('credit_note_items')
                            ->where('id', $row->id)
                            ->update(['type' => $type]);
                    }
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('credit_note_items', 'type')) {
            Schema::table('credit_note_items', function (Blueprint $table) {
                $table->dropIndex('credit_note_items_type_index');
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('credit_notes', 'type')) {
            Schema::table('credit_notes', function (Blueprint $table) {
                $table->dropIndex('credit_notes_type_index');
                $table->dropColumn('type');
            });
        }
    }
};

