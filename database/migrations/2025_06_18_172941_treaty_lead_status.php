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
        Schema::Create('treaty_lead_status', function (Blueprint $table) {
            $table->bigIncrements('lead_id');
            $table->string('id', 10);
            $table->string('status_name');
            $table->string('category_type', 5);
            $table->timestamps(); // created_at and updated_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
