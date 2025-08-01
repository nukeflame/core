<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientImplementationChecklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_implementation_checklist', function (Blueprint $table) {
            $table->increments('id');  // serial4 in SQL translates to increments in Laravel
            $table->string('client_number', 20)->nullable();
            $table->string('cover_no', 19)->nullable();
            $table->string('task_slug', 50);
            $table->integer('task_description_id')->nullable();
            $table->date('prompt_date')->nullable();
            $table->timestamps();  // Automatically creates created_at and updated_at columns
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->char('status', 1)->default('P')->nullable();  // bpchar(1) translates to char(1) in Laravel

            // Primary Key
            // $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_implementation_checklist');
    }
}
