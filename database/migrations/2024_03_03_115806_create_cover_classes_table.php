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
        Schema::create('cover_classes', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('reinclass', 4)->nullable(false);
            $table->string('class', 4)->nullable(false);
            $table->string('cover_no',20)->nullable(false);
            $table->string('endorsement_no',20)->nullable(false);
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_classes');
    }
};
