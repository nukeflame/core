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
        Schema::table('rein_notes',function(Blueprint $table){
            $table->string('glReinsurer_updated',1)->nullable(false)->default('N');
            $table->date('glReinsurer_updated_at')->nullable(true);
            $table->string('glReinsurer_updated_order_reference',20)->nullable(true);
            $table->string('glReinsurer_updated_by',20)->nullable(true);
            $table->decimal('glReinsurer_updated_retries',2,0)->nullable(false)->default(0);
            $table->text('glReinsurer_updated_errors')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rein_notes',function(Blueprint $table){
           $table->dropColumn('glReinsurer_updated');
           $table->dropColumn('glReinsurer_updated_at');
           $table->dropColumn('glReinsurer_updated_order_reference');
           $table->dropColumn('glReinsurer_updated_by');
           $table->dropColumn('glReinsurer_updated_retries');
           $table->dropColumn('glReinsurer_updated_errors');
        });
    }
};