<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */


    public function up()
    {
        // // Drop the existing primary key constraint
        // DB::statement('ALTER TABLE quote_schedule_headers DROP CONSTRAINT quote_schedule_headers_pkey1');

        // // Add the primary key on 'id' column
        // DB::statement('ALTER TABLE quote_schedule_headers ADD PRIMARY KEY (id)');
    }

    public function down()
    {
        // Revert the changes by dropping the primary key
        // DB::statement('ALTER TABLE quote_schedule_headers DROP CONSTRAINT quote_schedule_headers_pkey');
    }
};
