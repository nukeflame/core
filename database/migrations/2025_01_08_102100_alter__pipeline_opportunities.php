<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PharIo\Manifest\Email;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->json('contact_name')->nullable();
            $table->json('email')->nullable();
            $table->json('phone')->nullable();
            $table->json('telephone')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipeline_opportunities', function (Blueprint $table) {

            $table->dropColumn([
                'contact_name',
                'email',
                'phone',
                'telephone'

            ]);
        });
    }
};
