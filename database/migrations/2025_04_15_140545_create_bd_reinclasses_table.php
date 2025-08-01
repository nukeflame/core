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
        Schema::create('bd_reinclasses', function (Blueprint $table) {
            $table->id();
            $table->string('opportunity_id', 20);
            // $table->string('cover_no', 20);
            // $table->string('endorsement_no', 20);
            $table->string('reinclass', 4);
            // $table->primary(['cover_no', 'endorsement_no', 'reinclass']); // Define composite primary key
            $table->foreign('reinclass')->references('class_code')->on('reinsclasses')->onDelete('cascade');
            $table->foreign('opportunity_id')->references('opportunity_id')->on('pipeline_opportunities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bd_reinclasses');
    }
    
};
