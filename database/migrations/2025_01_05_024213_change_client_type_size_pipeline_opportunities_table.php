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
        DB::statement('ALTER TABLE pipeline_opportunities ALTER COLUMN client_type TYPE varchar(20)');
        DB::statement('ALTER TABLE pipeline_opportunities ALTER COLUMN client_category TYPE varchar(20)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
