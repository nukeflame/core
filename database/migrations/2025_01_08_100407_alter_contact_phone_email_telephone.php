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
        // Drop the specified columns
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->dropColumn([
                'contact_name',
                'email',
                'phone',
                'telephone'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the columns
        Schema::table('pipeline_opportunities', function (Blueprint $table) {
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('telephone', 20)->nullable();
        });
    }
};