<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBdDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('bd_docs', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable(false);
            $table->string('prospect_id')->nullable(false);
            $table->string('mimetype')->nullable(false);
            $table->string('file')->nullable(false);
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
        Schema::dropIfExists('bd_docs');
    }
}
