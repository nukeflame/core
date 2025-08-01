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
        Schema::create('cover_reinclass', function (Blueprint $table) {
            $table->string('cover_no', 20);
            $table->string('endorsement_no', 20);
            $table->string('reinclass', 4);
            $table->primary(['cover_no', 'endorsement_no','reinclass']); // Define composite primary key
            $table->foreign('reinclass')->references('class_code')->on('reinsclasses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_reinclass');
    }
};
