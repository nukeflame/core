<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospect_docs', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('description', 50)->nullable(); // Varchar(50) for description
            $table->string('prospect_id', 50)->nullable(); // Varchar(50) for prospect_id
            $table->string('prospect_status', 50)->nullable(); // Varchar(50) for prospect_status
            $table->string('mimetype', 128)->nullable(); // Varchar(128) for mimetype
            $table->text('file')->nullable(); // Text field for the file column
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospect_docs');
    }
}
