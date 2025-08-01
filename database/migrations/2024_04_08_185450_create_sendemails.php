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
        Schema::create('sendemails', function (Blueprint $table) {
            $table->string('email_id', 4);
            $table->string('template_name', 100)->nullable(true);
            $table->string('email_subject', 100);
            $table->string('email_from', 100)->nullable(true);
            $table->string('email_to', 100)->nullable(true);
            $table->string('email_cc', 200)->nullable(true);
            $table->string('salutation', 20);
            $table->longText('email_body');
            $table->string('status', 10);
            $table->longText('email_error')->nullable(true);
            $table->date('sent_date')->nullable(true);
            $table->timestamps();
            $table->string('created_by', 20);
            $table->string('updated_by', 20);
            $table->unique(['email_id'], 'sendemails_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sendemails');
    }
};
