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
        if (!Schema::hasTable('slip_templates')) {
            Schema::create('slip_templates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('schedule_title', 150);
                $table->string('class_group_code', 20)->nullable();
                $table->string('class_code', 20)->nullable();
                $table->string('class_group', 100)->nullable();
                $table->string('class_name', 120)->nullable();
                $table->text('description')->nullable();
                $table->string('type_of_bus', 20)->nullable();
                $table->char('status', 1)->default('A');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slip_templates');
    }
};
