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
        Schema::table('tenders', function (Blueprint $table) {
            $table->id()->first(); // Adds auto-incrementing id as primary key
        });

        // Update existing records with unique IDs
        $tenders = DB::table('tenders')->get();
        $index = 1;
        foreach ($tenders as $tender) {
            DB::table('tenders')
                ->where('tender_no', $tender->tender_no)
                ->update(['id' => $index++]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropColumn('id');
        });
    }
};
