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
        Schema::create('partner_identification', function (Blueprint $table) {
            $table->id();
            $table->string('identification_type', 50);
            // $table->string('identification_number', 50);
            $table->string('issued_by', 100);
            $table->date('issue_date')->nullable(true);
            $table->text('description')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_identification');
    }
};
