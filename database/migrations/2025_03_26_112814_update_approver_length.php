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
          DB::statement('ALTER TABLE prospect_handover ALTER COLUMN approver TYPE JSON  USING approver::JSON;');
      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospect_handover', function (Blueprint $table) {
            $table->string('approver', 10)->change();
        });
    }
};
