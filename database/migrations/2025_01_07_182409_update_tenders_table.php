<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTendersTable extends Migration
{
 /**
  * Run the migrations.
  *
  * @return void
  */
 public function up()
 {
  Schema::table('tenders', function (Blueprint $table) {
   $table->string('footer_color')->nullable(true);
   $table->text('footer_content')->nullable(true);
  });
 }

 /**
  * Reverse the migrations.
  *
  * @return void
  */
 public function down()
 {
  Schema::table('tenders', function (Blueprint $table) {
   //
  });
 }
}
