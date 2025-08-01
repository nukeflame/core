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
        Schema::table('cover_premtypes',function(Blueprint $table){
            // $table->dropIndex('cover_premtypes_pkey');
            $table->dropPrimary('cover_premtypes_pkey');
            $table->unique(['cover_no', 'endorsement_no','reinclass','treaty','premtype_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_premtypes',function(Blueprint $table){
            $table->dropUnique(['cover_no', 'endorsement_no','reinclass','treaty','premtype_code']);
        });
    }
};
