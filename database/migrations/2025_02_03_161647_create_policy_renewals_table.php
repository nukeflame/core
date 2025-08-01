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
        Schema::create('policy_renewals', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_email');
            $table->string('policy_number')->unique();
            $table->date('renewal_date');
            $table->string('doc_name');
            $table->datetime('last_notice_sent')->nullable();
            $table->string('notice_status')->nullable();
            $table->timestamps();
        });

        Schema::create('policy_renewal_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_renewal_id')->constrained()->onDelete('cascade');
            $table->string('doc_name');
            $table->string('doc_path');
            $table->integer('doc_size');
            $table->enum('doc_type', ['cedant', 'reinsurer']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_renewal_documents');
        Schema::dropIfExists('policy_renewals');
    }
};
