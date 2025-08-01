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
        Schema::table('stage_documents', function (Blueprint $table) {
            DB::statement('ALTER TABLE stage_documents ALTER COLUMN type_of_bus TYPE JSON USING to_json(type_of_bus)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage_documents', function (Blueprint $table) {
            DB::statement('ALTER TABLE stage_documents ALTER COLUMN type_of_bus TYPE VARCHAR USING type_of_bus::VARCHAR');
        });
    }
};
