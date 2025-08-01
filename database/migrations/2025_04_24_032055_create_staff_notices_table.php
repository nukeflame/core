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
        Schema::create('staff_notices', function (Blueprint $table) {
            $table->id();
            $table->string('notice');
            $table->text('description')->nullable();
            $table->string('type')->default('All vendor');
            $table->dateTime('effective_from');
            $table->dateTime('expired_at')->nullable();
            $table->string('issued_by');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_notices');
    }
};
