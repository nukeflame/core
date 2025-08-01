<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_type', function (Blueprint $table) {
            $table->integer('type_code')->unique();  // numeric(5) in SQL translates to integer in Laravel
            $table->string('name', 50);
            $table->timestamps();  // Automatically creates created_at and updated_at columns

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_type');
    }
}
