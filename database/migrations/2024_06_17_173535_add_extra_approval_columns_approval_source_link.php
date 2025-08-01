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
        Schema::table('approval_source_link', function (Blueprint $table) {
            $table->string('source_approval_by_column',50)->nullable(true);
            $table->string('source_approval_at_column',50)->nullable(true);
            $table->string('pay_method_code',5)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_source_link', function (Blueprint $table) {
            $table->dropColumn('source_approval_by_column');
            $table->dropColumn('source_approval_at_column');
            $table->dropColumn('pay_method_code');
        });
    }
};
