<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnderwriterAccountHandlersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('underwriter_account_handlers', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->integer('users_id')->nullable(); // Integer for 'users_id'
            $table->integer('divisions_id')->nullable(); // Integer for 'divisions_id'
            $table->integer('account_handler')->nullable(); // Integer for 'account_handler'
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
        Schema::dropIfExists('underwriter_account_handlers');
    }
}
