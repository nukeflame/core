<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderSubcategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_subcategories', function (Blueprint $table) {
            $table->string('tender_no', 50)->nullable(); // Varchar(50) for 'tender_no'
            $table->string('toc_no', 5)->nullable(); // Varchar(5) for 'toc_no'
            $table->string('subcat_desc', 500)->nullable(); // Varchar(500) for 'subcat_desc'
            $table->string('doc_id', 5)->nullable(); // Varchar(5) for 'doc_id'
            $table->string('subcat_id', 4)->nullable(); // Varchar(4) for 'subcat_id'
            $table->timestamps(); // Created and updated timestamps (optional, based on your needs)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_subcategories');
    }
}
