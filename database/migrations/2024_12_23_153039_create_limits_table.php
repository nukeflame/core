<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('limits', function (Blueprint $table) {
            $table->integer('class_category')->nullable(); // Integer field for 'class_category'
            $table->string('description', 50)->nullable(); // Varchar field for 'description'
            $table->string('details', 256)->nullable(); // Varchar field for 'details'
            $table->float('value')->nullable(); // Float field for 'value'
            $table->string('title', 50)->nullable(); // Varchar field for 'title'
            $table->integer('id')->nullable(); // Integer field for 'id'
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('limits');
    }
}
