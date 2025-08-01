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
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            // $table->date('closing_date')->nullable()->change();
            DB::statement('ALTER TABLE pipeline_opportunities ALTER COLUMN closing_date TYPE DATE USING closing_date::DATE');
            DB::statement('ALTER TABLE pipeline_opportunities ALTER COLUMN effective_date TYPE DATE USING effective_date::DATE');
            DB::statement('ALTER TABLE pipeline_opportunities ALTER COLUMN lead_owner TYPE varchar(20) USING lead_owner::varchar(20)');
            // $table->date('effective_date')->nullable();
            // $table->string('lead_owner', 20)->nullable();
            $table->string('country_code', 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->dropColumn([
                'closing_date',
                'effective_date',
                'lead_owner',
                'country_code',
            ]);
        });
    }
};
