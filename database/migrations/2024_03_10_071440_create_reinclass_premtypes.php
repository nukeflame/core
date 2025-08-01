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
        Schema::create('reinclass_premtypes', function (Blueprint $table) {
            $table->string('reinclass', 4);
            $table->string('premtype_code', 3);
            $table->string('premtype_name', 50);
            $table->char('status', 1);
            $table->primary(['reinclass', 'premtype_code']); // Define composite primary key
            // $table->foreign('reinclass')->references('class_code')->on('reinsclasses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down(): void
    {
        Schema::dropIfExists('reinclass_premtypes');
    }
};
