<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up()
 {
  // Find all constraints for the doc_id column
  $constraints = DB::select(
   "SELECT conname
            FROM pg_constraint
            WHERE conrelid = 'tender_doc_param'::regclass
            AND contype = 'p'"
  );

  // Drop each primary key constraint found
  foreach ($constraints as $constraint) {
   DB::statement("ALTER TABLE tender_doc_param DROP CONSTRAINT IF EXISTS {$constraint->conname}");
  }

  // Modify the column type with USING clause
  DB::statement('ALTER TABLE tender_doc_param ALTER COLUMN doc_id TYPE BIGINT USING doc_id::bigint');

  // Add back the primary key
  Schema::table('tender_doc_param', function (Blueprint $table) {
   $table->primary('doc_id');
  });
 }

 public function down()
 {
  // Find and drop primary key constraints
  $constraints = DB::select(
   "SELECT conname
            FROM pg_constraint
            WHERE conrelid = 'tender_doc_param'::regclass
            AND contype = 'p'"
  );

  foreach ($constraints as $constraint) {
   DB::statement("ALTER TABLE tender_doc_param DROP CONSTRAINT IF EXISTS {$constraint->conname}");
  }

  // Convert back to original type
  DB::statement('ALTER TABLE tender_doc_param ALTER COLUMN doc_id TYPE INTEGER USING doc_id::integer');

  // Add back primary key
  Schema::table('tender_doc_param', function (Blueprint $table) {
   $table->primary('doc_id');
  });
 }
};
