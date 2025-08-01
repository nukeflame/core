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
        Schema::table('cbrequisitions', function (Blueprint $table) {
            // Modify existing columns
            $table->string('checked_by', 20)->nullable()->change();
            $table->string('authorized_by', 20)->nullable()->change();
            $table->string('approved_by', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cbrequisitions', function (Blueprint $table) {
            // Revert changes if needed
            $table->string('checked_by')->nullable()->change();
            $table->string('authorized_by')->nullable()->change();
            $table->string('approved_by')->nullable()->change();
        });
    }
};
