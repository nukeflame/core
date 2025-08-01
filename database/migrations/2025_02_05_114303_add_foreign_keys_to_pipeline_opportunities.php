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
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            // Ensure the columns exist before adding foreign keys
            // if (!Schema::hasColumn('pipeline_opportunities', 'stage')) {
            //     $table->unsignedBigInteger('stage')->nullable();
            // }

            if (!Schema::hasColumn('pipeline_opportunities', 'divisions')) {
                $table->string('divisions')->nullable();
            }

            if (!Schema::hasColumn('pipeline_opportunities', 'classcode')) {
                $table->string('classcode')->nullable();
            }

            if (!Schema::hasColumn('pipeline_opportunities', 'type_of_bus')) {
                $table->unsignedBigInteger('type_of_bus')->nullable();
            }

            // Foreign Key Constraints
            // $table->foreign('stage')->references('id')->on('lead_status')->onDelete('set null');
            // $table->foreign('divisions')->references('division_code')->on('reins_division')->onDelete('set null');
            $table->foreign('classcode')->references('class_code')->on('classes')->onDelete('set null');
            $table->foreign('type_of_bus')->references('bus_type_id')->on('business_types')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->dropForeign(['stage']);
            $table->dropForeign(['divisions']);
            $table->dropForeign(['classcode']);
            $table->dropForeign(['type_of_bus']);
        });
    }
};
