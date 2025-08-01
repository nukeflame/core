<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspectPremtypesTable extends Migration
{
    public function up()
    {
        Schema::create('prospect_premtypes', function (Blueprint $table) {
            $table->id();
            $table->string('pipeline_id');
            $table->string('opportunity_id');
            $table->string('reinclass', 100);
            $table->string('treaty', 100);
            $table->string('premtype_code', 100);
            $table->string('premtype_name', 255);
            $table->decimal('comm_rate', 8, 4);
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prospect_premtypes');
    }
}
