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
        Schema::table('doc_types', function (Blueprint $table) {
            $table->renameColumn('category_type', 'checkbox_doc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc_types', function (Blueprint $table) {
            $table->renameColumn('checkbox_doc', 'category_type');
            
        });
    }
};
