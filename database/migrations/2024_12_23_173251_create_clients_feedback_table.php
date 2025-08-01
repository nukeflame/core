<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients_feedback', function (Blueprint $table) {
            $table->bigIncrements('id');  // id as bigserial, auto-increment
            $table->string('category', 191);
            $table->string('comment', 191);
            $table->date('response')->nullable();  // nullable date column
            $table->date('staff')->nullable();  // nullable date column
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('remember_token', 100)->nullable();

            // $table->primary('id');  // Primary key constraint
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients_feedback');
    }
}
