<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id(); // If you want an auto-incrementing primary key, this is optional if not needed
            $table->string('full_name', 50)->nullable();
            $table->string('salutation', 50)->nullable();
            $table->string('industry', 50)->nullable();
            $table->string('customer_id', 50)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('client_type', 50)->nullable();
            $table->integer('year')->nullable();
            $table->string('prequalification', 50)->nullable();
            $table->string('organic_reference', 50)->nullable();
            $table->timestamps(); // To track created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
