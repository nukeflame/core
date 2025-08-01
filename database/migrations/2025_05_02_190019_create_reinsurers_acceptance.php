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
        Schema::create('reinsurers_declined', function (Blueprint $table) {
            
            $table->id();
            $table->integer('customer_id');
            $table->string('reason');
            $table->string('opportunity_id');
            $table->timestamps();
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            // $table->foreignId('customer_id')->constrained('reinsurers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reinsurers_declined', function (Blueprint $table) {
            $table->dropForeign(['customer_id']); 
        });
        Schema::dropIfExists('reinsurers_declined');
        
    }
};
