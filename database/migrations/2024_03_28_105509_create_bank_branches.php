<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bank_branches', function (Blueprint $table) {
            $table->string('bank_code', 3);
            $table->string('bank_branch_code', 3);
            $table->string('bank_branch_name', 100);
            $table->dateTime('created_date');
            $table->string('created_by', 20);
            $table->unique(['bank_code','bank_branch_code'], 'bank_branches_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_branches');
    }
};
