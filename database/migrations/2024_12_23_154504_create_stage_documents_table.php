<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStageDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stage_documents', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->integer('stage')->nullable(); // Integer for 'stage'
            $table->integer('engage_type')->nullable(); // Integer for 'engage_type'
            $table->integer('doc_type')->nullable(); // Integer for 'doc_type'
            $table->string('mandatory', 50)->nullable(); // Varchar(50) for 'mandatory'
            $table->integer('division')->nullable(); // Integer for 'division'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stage_documents');
    }
}
