<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalutationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salutation', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->integer('salutation_code')->nullable(); // Integer for 'salutation_code'
            $table->string('name', 50)->nullable(); // Varchar(50) for 'name'
            $table->string('created_at', 50)->nullable(); // Varchar(50) for 'created_at'
            $table->string('updated_at', 50)->nullable(); // Varchar(50) for 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salutation');
    }
}
