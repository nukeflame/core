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
        // Update any existing NULL values to 'N'
        DB::table('cover_register')
                        ->whereNull('broker_flag')
                        ->update(['broker_flag' => 'N']);

        Schema::table('cover_register', function (Blueprint $table) {
            $table->string('broker_flag',1)->nullable(false)->default('N')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_register', function (Blueprint $table) {
            $table->string('broker_flag',1)->nullable(true)->change();
        });
    }
};
