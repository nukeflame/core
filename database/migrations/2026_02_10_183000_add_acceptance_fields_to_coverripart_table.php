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
        Schema::table('coverripart', function (Blueprint $table) {
            $table->decimal('compulsory_acceptance', 28, 12)->default(0);
            $table->decimal('optional_acceptance', 28, 12)->default(0);
            $table->decimal('total_acceptance', 28, 12)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverripart', function (Blueprint $table) {
            $table->dropColumn([
                'compulsory_acceptance',
                'optional_acceptance',
                'total_acceptance'
            ]);
        });
    }
};
