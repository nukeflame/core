<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->string('tender_no', 50)->nullable(); // Varchar(50) for 'tender_no'
            $table->string('tender_name', 400)->nullable(); // Varchar(400) for 'tender_name'
            $table->string('tender_description', 400)->nullable(); // Varchar(400) for 'tender_description'
            $table->date('closing_date'); // Date for 'closing_date'
            $table->string('tender_status', 10)->nullable(); // Varchar(10) for 'tender_status'
            $table->string('created_by', 20)->nullable(); // Varchar(20) for 'created_by'
            $table->string('updated_by', 20)->nullable(); // Varchar(20) for 'updated_by'
            $table->timestamp('created_at')->nullable(); // Timestamp for 'created_at'
            $table->timestamp('updated_at')->nullable(); // Timestamp for 'updated_at'
            $table->string('client_name', 150)->nullable(); // Varchar(150) for 'client_name'
            $table->string('tender_category', 400)->nullable(); // Varchar(400) for 'tender_category'
            $table->string('tender_nature', 400)->nullable(); // Varchar(400) for 'tender_nature'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenders');
    }
}
