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
        Schema::create('cover_premiums', function (Blueprint $table) {
            $table->string('cover_no', 20);
            $table->string('endorsement_no', 20);
            $table->string('orig_endorsement_no', 20);
            $table->string('transaction_type', 3);
            $table->string('premium_type_code', 3);
            $table->string('premtype_name', 50);
            $table->string('entry_type_descr', 3);
            $table->string('type_of_bus', 3);
            $table->string('class_code', 4);
            $table->decimal('basic_amount',20,2);
            $table->string('apply_rate_flag',1);
            $table->decimal('rate',8,5);
            $table->decimal('quarter',1,0);
            $table->decimal('final_amount',20,2);
            $table->date('created_at');
            $table->date('updated_at');
            $table->string('created_by', 20);
            $table->string('updated_by', 20);
            $table->unique(['cover_no','endorsement_no','class_code','transaction_type','premium_type_code','entry_type_descr'], 'cover_premiums_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cover_premiums');
    }
};
