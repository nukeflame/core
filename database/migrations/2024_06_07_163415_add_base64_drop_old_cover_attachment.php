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
        Schema::table('cover_attachment', function (Blueprint $table) {
            if(Schema::hasColumn('cover_attachment','base64_encoded'))
            {
                $table->dropColumn('base64_encoded');
            }

            $table->text('file_base64')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cover_attachment', function (Blueprint $table) {
            $table->dropColumn('file_base64');
        });
    }
};
