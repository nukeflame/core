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
        Schema::table('claim_ntf_ack_docs',function(Blueprint $table){
            $table->string('doc_name',200)->nullable(true);
            $table->string('file',200)->nullable(true);
            $table->text('file_base64')->nullable(true);
            $table->string('mime_type',100)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_ntf_ack_docs',function(Blueprint $table){
           $table->dropColumn('doc_name');
           $table->dropColumn('file');
           $table->dropColumn('file_base64');
           $table->dropColumn('mime_type');
        });
    }
};
