<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customer_contacts')) {
            return;
        }

        Schema::table('customer_contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_contacts', 'department')) {
                $table->string('department', 50)->nullable()->after('contact_email');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('customer_contacts')) {
            return;
        }

        Schema::table('customer_contacts', function (Blueprint $table) {
            if (Schema::hasColumn('customer_contacts', 'department')) {
                $table->dropColumn('department');
            }
        });
    }
};

