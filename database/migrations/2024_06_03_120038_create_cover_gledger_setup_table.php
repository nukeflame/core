<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cover_gledger_link', function (Blueprint $table) {
            $table->string('type_of_bus',3);
            $table->string('transaction_type',3);
            $table->string('entry_type_descr',3);
            $table->string('entry_type_name',100);
            $table->string('cedant_dr_cr',2);
            $table->string('reinsurer_dr_cr',2);
            $table->string('cedant_glaccount',8);
            $table->string('reinsurer_glaccount',8);
            $table->string('created_by',20)->nullable(false);
            $table->string('updated_by',20)->nullable(false);
            $table->timestamps();

            $table->unique(['type_of_bus','transaction_type','entry_type_descr'], 'cover_gledger_link_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_gledger_link');
    }
};
