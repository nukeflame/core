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
        Schema::create('tender_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tender_id');
            $table->string('tender_no');
            $table->unsignedBigInteger('stage_id');
            $table->string('email_dated');
            $table->string('commence_year');
            $table->json('main_email'); // {email, name, type}
            $table->json('cc_emails')->nullable(); // Array of CC emails
            $table->string('pdf_filename'); // PDF filename in S3
            $table->unsignedBigInteger('approver_id');
            $table->unsignedBigInteger('submitter_id');
            $table->enum('status', ['0', '1', '2'])->default('0');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('tender_id')->references('id')->on('tenders')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('submitter_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tender_approvals');
    }
};
