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
        Schema::create('bd_premtypes', function (Blueprint $table) {
            $table->id();
            $table->string('opportunity_id', 20);
            // $table->string('cover_no', 20)->unique();
            // $table->string('endorsement_no', 20)->unique();
            $table->string('reinclass', 4);
            $table->string('premtype_code', 3);
            $table->string('premtype_name', 50);
            $table->string('comm_rate', 8);
            $table->string('treaty', 10)->nullable();
            $table->softDeletes();
            // $table->primary(['cover_no', 'endorsement_no', 'reinclass', 'premtype_code']); // Define composite primary key
            // $table->foreign(['cover_no', 'endorsement_no', 'reinclass'])->references(['cover_no', 'endorsement_no', 'reinclass'])->on('cover_reinclass')->onDelete('cascade');
            $table->foreign('reinclass')->references('class_code')->on('reinsclasses')->onDelete('cascade');
            $table->foreign('opportunity_id')->references('opportunity_id')->on('pipeline_opportunities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bd_premtypes');
    }
};
