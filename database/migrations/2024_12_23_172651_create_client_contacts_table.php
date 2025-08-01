<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');  // bigserial in SQL translates to bigIncrements in Laravel
            $table->string('global_customer_id', 191);
            $table->string('c_name', 191);
            $table->string('position', 191);
            $table->string('phone_no', 191);
            $table->string('c_email', 191);
            $table->timestamps(0);  // Automatically creates created_at and updated_at columns with precision 0
            $table->string('first_name', 50)->nullable();
            $table->string('surname', 50)->nullable();
            $table->string('other_names', 50)->nullable();

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
        Schema::dropIfExists('client_contacts');
    }
}
