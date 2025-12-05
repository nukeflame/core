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
            $table->dropColumn([
                'compulsory_acceptance',
                'optional_acceptance',
                'total_acceptance',
            ]);

            $table->string('territorial_scope')->nullable();
            $table->string('basis_of_acceptance')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_register', function (Blueprint $table) {
            $table->double('compulsory_acceptance', 4)->default(0);
            $table->double('optional_acceptance', 4)->default(0);
            $table->double('total_acceptance', 4)->default(0);

            $table->dropColumn(['territorial_scope', 'basis_of_acceptance']);
        });
    }
};
