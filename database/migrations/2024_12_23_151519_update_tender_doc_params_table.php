<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTenderDocParamsTable extends Migration
{
 /**
  * Run the migrations.
  *
  * @return void
  */
 public function up()
 {
  Schema::table('tender_doc_param', function (Blueprint $table) {
   $table->string('tender_doc_id', 50)->default(0);
  });
 }

 /**
  * Reverse the migrations.
  *
  * @return void
  */
 public function down()
 {
  Schema::table('tender_doc_param', function (Blueprint $table) {
   //
  });
 }
}
