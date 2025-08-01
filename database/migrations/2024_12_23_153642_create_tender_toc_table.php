<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderTocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_toc', function (Blueprint $table) {
            $table->string('tender_no', 50)->nullable(); // Varchar(50) for 'tender_no'
            $table->string('tender_name', 200)->nullable(); // Varchar(200) for 'tender_name'
            $table->string('toc_no', 50)->nullable(); // Varchar(50) for 'toc_no'
            $table->string('doc_id', 50)->nullable(); // Varchar(50) for 'doc_id'
            $table->string('toc_section', 50)->nullable(); // Varchar(50) for 'toc_section'
            $table->string('toc_category', 100)->nullable(); // Varchar(100) for 'toc_category'
            $table->text('toc_description')->nullable(false); // Text for 'toc_description'
            $table->text('attach_name')->nullable(); // Text for 'attach_name'
            $table->string('sort_no', 20)->nullable(); // Varchar(20) for 'sort_no'
            $table->string('created_by', 20)->nullable(); // Varchar(20) for 'created_by'
            $table->string('updated_by', 20)->nullable(); // Varchar(20) for 'updated_by'
            $table->timestamp('created_at')->nullable(); // Timestamp for 'created_at'
            $table->timestamp('updated_at')->nullable(); // Timestamp for 'updated_at'
            $table->string('toc_head', 150)->nullable(); // Varchar(150) for 'toc_head'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_toc');
    }
}
