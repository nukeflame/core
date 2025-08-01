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
        Schema::table('cover_register', function (Blueprint $table) {
            $table->string('apply_eml',1)->default('N');
            $table->decimal('effective_sum_insured',20,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_register', function (Blueprint $table) {
            $table->dropColumn('apply_eml');
            $table->dropColumn('effective_sum_insured');
        });
    }
};
