<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads_activity', function (Blueprint $table) {
            $table->integer('id')->nullable();  // Integer field for 'id'
            $table->string('title', 50)->nullable(); // Varchar field for 'title'
            $table->string('activity_date', 50)->nullable(); // Varchar field for 'activity_date'
            $table->string('activity_location', 50)->nullable(); // Varchar field for 'activity_location'
            $table->string('notes', 50)->nullable(); // Varchar field for 'notes'
            $table->string('activity_type', 50)->nullable(); // Varchar field for 'activity_type'
            $table->string('status', 50)->nullable(); // Varchar field for 'status'
            $table->string('lead_id', 50)->nullable(); // Varchar field for 'lead_id'
            $table->string('date_from', 50)->nullable(); // Varchar field for 'date_from'
            $table->string('date_to', 50)->nullable(); // Varchar field for 'date_to'
            $table->timestamps(); // To track created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads_activity');
    }
}
