<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStageCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stage_comments', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('stage_id', 50)->nullable(); // Varchar(50) for 'stage_id'
            $table->string('prospect_id', 50)->nullable(); // Varchar(50) for 'prospect_id'
            $table->string('remarks', 50)->nullable(); // Varchar(50) for 'remarks'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stage_comments');
    }
}
