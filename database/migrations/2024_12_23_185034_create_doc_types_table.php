<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_types', function (Blueprint $table) {
            $table->bigIncrements('id');  // id as bigserial, auto-increment
            $table->string('doc_type', 191);  // doc_type column
            $table->string('description', 191);  // description column
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('doc_for', 20)->nullable();  // doc_for column

            // Unique constraints
            $table->unique('description');
            $table->unique('doc_type');

            // Primary key
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
        Schema::dropIfExists('doc_types');
    }
}
