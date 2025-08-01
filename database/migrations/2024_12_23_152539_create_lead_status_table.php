<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_status', function (Blueprint $table) {
            $table->integer('id')->nullable();  // Integer field for 'id'
            $table->string('status_name', 50)->nullable(); // Varchar field for 'status_name'
            $table->timestamps(); // Added for created_at and updated_at tracking (optional)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_status');
    }
}
