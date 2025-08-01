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
        Schema::table('bd_premtypes', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->after('premtype');
            $table->timestamp('updated_at')->nullable()->after('created_at');
            $table->string('created_by')->nullable()->after('updated_at');
            $table->string('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bd_premtypes', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
};
