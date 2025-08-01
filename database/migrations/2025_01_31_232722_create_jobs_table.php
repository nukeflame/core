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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue', 50)->nullable();
            $table->text('payload')->nullable();
            $table->string('attempts', 50)->nullable();
            $table->string('reserved_at', 50)->nullable();
            $table->string('available_at', 50)->nullable();
            $table->string('created_at', 50)->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
