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
        Schema::table('claim_debit',function(Blueprint $table){
            $table->string('gl_updated',1)->nullable(false)->default('N');
            $table->date('gl_updated_at')->nullable(true);
            $table->string('gl_updated_invoice_reference',20)->nullable(true);
            $table->string('gl_updated_by',20)->nullable(true);
            $table->decimal('gl_updated_retries',2,0)->nullable(false)->default(0);
            $table->text('gl_updated_errors')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_debit',function(Blueprint $table){
            $table->dropColumn('gl_updated');
            $table->dropColumn('gl_updated_at');
            $table->dropColumn('gl_updated_order_reference');
            $table->dropColumn('gl_updated_by');
            $table->dropColumn('gl_updated_retries');
            $table->dropColumn('gl_updated_errors');
         });
    }
};
