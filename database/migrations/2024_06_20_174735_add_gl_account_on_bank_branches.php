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
        Schema::table('bank_branches', function (Blueprint $table) {
            $table->string('gl_account',8);
            $table->string('bank_account_no',20);
            $table->string('bank_account_name',100);
            $table->timestamps();
            $table->string('updated_by',20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_branches', function (Blueprint $table) {
            $table->dropColumn('gl_account');
            $table->dropColumn('bank_account_no');
            $table->dropColumn('bank_account_name');
            $table->dropColumn('updated_by');
            $table->dropColumn('updated_at');
            $table->dropColumn('created_at');
        });
    }
};
