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
        Schema::table('prospect_handover', function (Blueprint $table) {
            // $table->bigIncrements('handover_id');

            $table->string('division', 5)->change();
            $table->string('country',5)->change();
            $table->string('final_premium')->change();
            $table->string('final_commission')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_handover', function (Blueprint $table) {
            $table->integer('division')->change();
            $table->integer('country')->change();
            $table->integer('final_premium')->change();
            $table->integer('final_commission')->change();
            $table->dropColumn('handover_id');
        });
    }
};
