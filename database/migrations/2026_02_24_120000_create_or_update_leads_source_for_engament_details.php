<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('leads_source')) {
            Schema::create('leads_source', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('description', 500)->nullable();
                $table->char('status', 1)->default('A');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table('leads_source', function (Blueprint $table) {
                if (!Schema::hasColumn('leads_source', 'description')) {
                    $table->string('description', 500)->nullable()->after('name');
                }

                if (!Schema::hasColumn('leads_source', 'status')) {
                    $table->char('status', 1)->default('A')->after('description');
                }

                if (!Schema::hasColumn('leads_source', 'sort_order')) {
                    $table->unsignedInteger('sort_order')->default(0)->after('status');
                }

                if (!Schema::hasColumn('leads_source', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }

                if (!Schema::hasColumn('leads_source', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        $samples = [
            ['name' => 'Underwriter Tender', 'sort_order' => 1],
            ['name' => 'Public Tender', 'sort_order' => 2],
            ['name' => 'RFP & RFQ', 'sort_order' => 3],
            ['name' => 'Prequalification', 'sort_order' => 4],
        ];

        foreach ($samples as $sample) {
            $exists = DB::table('leads_source')->where('name', $sample['name'])->exists();
            if ($exists) {
                continue;
            }

            DB::table('leads_source')->insert([
                'name' => $sample['name'],
                'description' => $sample['name'] . ' engagement type option',
                'status' => 'A',
                'sort_order' => $sample['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Keep existing engagement options intact on rollback.
    }
};
