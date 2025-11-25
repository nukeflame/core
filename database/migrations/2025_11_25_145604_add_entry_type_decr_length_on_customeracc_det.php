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
        Schema::table('customeracc_det', function (Blueprint $table) {
            $table->enum('status', ['not_paid', 'paid', 'partial'])->default('not_paid');
            $table->string('entry_type_descr', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customeracc_det', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->string('entry_type_descr')->nullable(false)->change();
        });
    }
};
