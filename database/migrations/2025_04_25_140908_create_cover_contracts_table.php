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
        Schema::create('cover_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('cedant_id')->nullable()->constrained('customers', 'customer_id');
            $table->enum('cover_type', ['Facultative Proportional', 'Facultative Non-Proportional', 'Treaty Proportional', 'Treaty Non-Proportional'])->default('Facultative Proportional');
            $table->foreignId('region_id')->constrained();
            $table->enum('line_of_business', ['Facultative', 'Treaty', 'Special Lines'])->default('fac-proportional');
            $table->double('premium', 15, 2);
            $table->date('inception_date');
            $table->enum('status', ['placed', 'pending', 'declined'])->default('pending');
            $table->date('placement_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_contracts');
    }
};
