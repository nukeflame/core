<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnderwriterEngagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('underwriter_engagements', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('global_customer_id', 50)->nullable(); // Varchar(50) for 'global_customer_id'
            $table->string('engagement_type', 50)->nullable(); // Varchar(50) for 'engagement_type'
            $table->string('attachment', 64)->nullable(); // Varchar(64) for 'attachment'
            $table->string('created_at', 50)->nullable(); // Varchar(50) for 'created_at'
            $table->string('updated_at', 50)->nullable(); // Varchar(50) for 'updated_at'
            $table->string('policy_no', 50)->nullable(); // Varchar(50) for 'policy_no'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('underwriter_engagements');
    }
}
