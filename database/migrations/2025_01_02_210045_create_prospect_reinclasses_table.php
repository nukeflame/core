<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectReinclassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospect_reinclasses', function (Blueprint $table) {
            $table->id(); 
            $table->string('pipeline_id'); 
            $table->string('opportunity_id'); 
            $table->string('reinclass', 100); 
            $table->string('created_by', 100)->nullable(); 
            $table->string('updated_by', 100)->nullable(); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospect_reinclasses');
    }
}
