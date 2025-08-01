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
        Schema::table('prospect_docs', function (Blueprint $table) {
            $table->string('type_of_treaty_doc',5)->nullable()->after('prospect_status');
            $table->string('bus_type',5)->nullable()->after('type_of_treaty_doc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_docs', function (Blueprint $table) {
            $table->dropColumn('type_of_treaty_doc');
            $table->dropColumn('bus_type');
        });
    }
};
