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
        Schema::table('tenders', function (Blueprint $table) {
            $table->string('prospect_id',20)->nullable()->after('id');
            $table->foreign('prospect_id')->references('opportunity_id')->on('pipeline_opportunities')->onDeleteCascade();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropForeign(['prospect_id']);
            $table->dropColumn('prospect_id');
        });
    }
};
