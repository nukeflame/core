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
        Schema::table('clauses_param', function (Blueprint $table) {
            $table->string('type_of_bus')->nullable(true);
            $table->string('class_group_code')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clauses_param', function (Blueprint $table) {
            $table->dropColumn('type_of_bus');
            $table->dropColumn('class_group_code');
        });
    }
};
