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
        Schema::table('gltransactions', function (Blueprint $table) {
            $table->string('batch_no',20)->nullable(true);
            $table->string('cheque_no',20)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gltransactions', function (Blueprint $table) {
            $table->dropColumn('batch_no');
            $table->dropColumn('cheque_no');
        });
    }
};
