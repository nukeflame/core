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
        Schema::table('stage_comments', function (Blueprint $table) {
            $table->string('quote_title_intro', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage_comments', function (Blueprint $table) {
            Schema::table('stage_comments', function (Blueprint $table) {
                $table->string('quote_title_intro', 100)->nullable();
            });
        });
    }
};
