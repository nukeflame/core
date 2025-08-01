<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('gender', function (Blueprint $table) {
            $table->integer('gender_code')->nullable();
            $table->string('name', 50)->nullable();
            $table->string('created_at', 50)->nullable();
            $table->string('updated_at', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gender');
    }
};
