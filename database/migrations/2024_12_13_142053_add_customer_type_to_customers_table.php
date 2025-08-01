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
        if (Schema::hasColumn('customers', 'customer_type')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('customer_type');
            });
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->json('customer_type')->nullable()->default(json_encode([]));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'customer_type')) {
                $table->dropColumn('customer_type');
            }
        });
    }
};
