<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('customeracc_det', 'portfolio_type')) {
            Schema::table('customeracc_det', function (Blueprint $table) {
                $table->string('portfolio_type', 3)->default('IN');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customeracc_det', 'portfolio_type')) {
            Schema::table('customeracc_det', function (Blueprint $table) {
                $table->dropColumn('portfolio_type');
            });
        }
    }
};
