<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE pipeline_opportunities 
            ALTER COLUMN indemnity_treaty_limit 
            TYPE json 
            USING to_json(indemnity_treaty_limit)
        ");

        DB::statement("
            ALTER TABLE pipeline_opportunities 
            ALTER COLUMN underlying_limit 
            TYPE json 
            USING to_json(underlying_limit)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE pipeline_opportunities 
            ALTER COLUMN indemnity_treaty_limit 
            TYPE numeric 
            USING (indemnity_treaty_limit::numeric)
        ");

        DB::statement("
            ALTER TABLE pipeline_opportunities 
            ALTER COLUMN underlying_limit 
            TYPE numeric 
            USING (underlying_limit::numeric)
        ");
    }
};
