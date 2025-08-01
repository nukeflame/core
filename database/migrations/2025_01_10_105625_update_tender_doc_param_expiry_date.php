<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTenderDocParamExpiryDate extends Migration
{
 /**
  * Run the migrations.
  *
  * @return void
  */
 public function up()
 {
  if (!Schema::hasColumn('tender_doc_param', 'expiry_date')) {
   Schema::table('tender_doc_param', function (Blueprint $table) {
    $table->string('expiry_date')->nullable(true);
   });
  }
  if (!Schema::hasColumn('tender_doc_param', 'renewable')) {
   Schema::table('tender_doc_param', function (Blueprint $table) {
    $table->integer('renewable')->nullable(true);
   });
  }
 }

 /**
  * Reverse the migrations.
  *
  * @return void
  */
 public function down()
 {
  if (Schema::hasColumn('tender_doc_param', 'expiry_date')) {
   Schema::table('tender_doc_param', function (Blueprint $table) {
    $table->string('expiry_date')->nullable(true);
   });
  }
  if (!Schema::hasColumn('tender_doc_param', 'renewable')) {
   Schema::table('tender_doc_param', function (Blueprint $table) {
    $table->integer('renewable')->nullable(true);
   });
  }
 }
}
