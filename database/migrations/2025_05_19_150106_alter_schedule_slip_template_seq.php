<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bd_schedule_template_data', function (Blueprint $table) {
            $table->dropPrimary(); // drop existing PK constraint on clause_id
        });

        DB::statement('ALTER TABLE bd_schedule_template_data ALTER COLUMN clause_id TYPE INTEGER USING clause_id::integer');

        Schema::table('bd_schedule_template_data', function (Blueprint $table) {
            $table->primary('clause_id'); // re-add PK
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bd_schedule_template_data', function (Blueprint $table) {
        });
    }
};
