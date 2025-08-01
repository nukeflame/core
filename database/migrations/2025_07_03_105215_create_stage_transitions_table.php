<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stage_transitions', function (Blueprint $table) {
            $table->id();
            $table->string('opportunity_id',20);
            $table->foreign('opportunity_id')->references('opportunity_id')->on('pipeline_opportunities')->onDelete('cascade');            
            $table->string('stage');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('duration')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stage_transitions');
    }
};
