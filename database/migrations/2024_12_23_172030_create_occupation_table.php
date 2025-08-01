<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOccupationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('occupation', function (Blueprint $table) {
            $table->string('occupation_code', 191);
            $table->string('name', 191);
            $table->timestamps(0);  // Automatically creates created_at and updated_at columns with precision 0
            $table->string('category', 2)->nullable();

            // Constraints
            $table->primary('occupation_code');  // Set occupation_code as the primary key
            $table->unique('occupation_code');   // Ensure occupation_code is unique
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('occupation');
    }
}
