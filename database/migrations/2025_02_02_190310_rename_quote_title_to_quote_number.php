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
        Schema::table('quotes', function (Blueprint $table) {
            $table->renameColumn('quote_title', 'quote_number');
        });

        
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('quote_number', 10)->unique()->change();
        });
    }

    public function down()
    {
        
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('quote_number', 100)->change();
        });

       
        Schema::table('quotes', function (Blueprint $table) {
            $table->renameColumn('quote_number', 'quote_title');
        });
    }
};
