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
        Schema::create('settings_menu', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('title',100);
            $table->string('route')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('created_by',20);
            $table->string('updated_by',20)->nullable();
            $table->timestamps();
        });

        Schema::table('settings_menu',function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('settings_menu')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_menu');
    }
};
