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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->string('icon')->default('ti ti-bell');
            $table->boolean('read')->default(false);
            $table->dateTime('effective_from')->default(now());
            $table->dateTime('expired_at');
            $table->enum('type', ['verify_cover', 'verify_claim_intimation_process', 'verify-glbatch', 'authorize-requisition', 'approve-requisition']);
            $table->foreignId('created_by')->nullable()->references('id')->on('users');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('approval_notification_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_notification_id')->nullable()->references('id')->on('notifications')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('approval_notification_read', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_notification_id')->nullable()->references('id')->on('notifications')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('store_notification_id')->nullable()->references('id')->on('approvals_tracker')->cascadeOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_notification_read');
        Schema::dropIfExists('approval_notification_user');
        Schema::dropIfExists('notifications');
    }
};
