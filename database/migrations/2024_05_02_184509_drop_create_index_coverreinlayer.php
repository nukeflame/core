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
        Schema::table('coverreinlayers',function(Blueprint $table){
            // Drop the constraint
            $table->dropUnique('coverreinlayers_1');
            $table->unique(['cover_no', 'endorsement_no', 'layer_no','item_no','reinclass'], 'coverreinlayers_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coverreinlayers',function(Blueprint $table){
            $table->unique(['cover_no', 'endorsement_no', 'layer_no'], 'coverreinlayers_1');
            $table->dropUnique(['cover_no', 'endorsement_no', 'layer_no','item_no','reinclass'], 'coverreinlayers_1');
        });
    }
};
