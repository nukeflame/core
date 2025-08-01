<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderDocParamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_doc_param', function (Blueprint $table) {
            $table->string('doc_id', 50)->nullable(); // Varchar(50) for 'doc_id'
            $table->string('doc_name', 200)->nullable(); // Varchar(200) for 'doc_name'
            $table->string('doc_description', 300)->nullable(); // Varchar(300) for 'doc_description'
            $table->text('base64')->nullable(); // Text field for 'base64'
            $table->string('doc_status', 10)->nullable(); // Varchar(10) for 'doc_status'
            $table->string('created_by', 20)->nullable(); // Varchar(20) for 'created_by'
            $table->string('updated_by', 20)->nullable(); // Varchar(20) for 'updated_by'
            $table->timestamp('created_at')->nullable(); // Timestamp for 'created_at'
            $table->timestamp('updated_at')->nullable(); // Timestamp for 'updated_at'
            $table->string('mimetype', 80)->nullable(); // Varchar(80) for 'mimetype'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_doc_param');
    }
}
