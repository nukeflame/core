<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\OutlookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TrackEmailDeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    protected string $messageId;
    protected int $userId;

    public function __construct(string $messageId, int $userId)
    {
        $this->messageId = $messageId;
        $this->userId = $userId;
        $this->onQueue('email-tracking');
    }

    public function handle(OutlookService $outlookService): void
    {
        try {
            $user = User::findOrFail($this->userId);

            if (!$outlookService->isTokenValid($user->email)) {
                logger()->warning('Cannot track email - invalid token', [
                    'message_id' => $this->messageId,
                    'user_id' => $this->userId
                ]);
                return;
            }

            $messageDetails = $outlookService->getMessageDetails($this->messageId);

            if (!empty($messageDetails)) {
                // DB::table('email_logs')
                //     ->where('message_id', $this->messageId)
                //     ->update([
                //         'delivery_status' => 'delivered',
                //         'tracked_at' => now(),
                //         'message_details' => json_encode($messageDetails)
                //     ]);

                logger()->info('Email delivery tracked successfully', [
                    'message_id' => $this->messageId,
                    'user_id' => $this->userId
                ]);
            }
        } catch (\Exception $e) {
            logger()->error('Failed to track email delivery', [
                'message_id' => $this->messageId,
                'user_id' => $this->userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}

// // Migration: database/migrations/create_email_logs_table.php
// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     public function up(): void
//     {
//         Schema::create('email_logs', function (Blueprint $table) {
//             $table->id();
//             $table->unsignedBigInteger('user_id');
//             $table->string('user_email');
//             $table->string('job_id')->nullable()->index();
//             $table->string('message_id')->nullable()->index();
//             $table->string('conversation_id')->nullable();
//             $table->string('subject');
//             $table->json('to_recipients');
//             $table->json('cc_recipients')->nullable();
//             $table->json('bcc_recipients')->nullable();
//             $table->text('body_preview')->nullable();
//             $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
//             $table->enum('status', ['queued', 'processing', 'sent', 'failed'])->default('queued');
//             $table->enum('delivery_status', ['pending', 'delivered', 'bounced', 'failed'])->nullable();
//             $table->text('error')->nullable();
//             $table->timestamp('sent_at')->nullable();
//             $table->timestamp('tracked_at')->nullable();
//             $table->json('message_details')->nullable();
//             $table->timestamps();

//             $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
//             $table->index(['user_id', 'created_at']);
//             $table->index(['status', 'created_at']);
//         });

//         Schema::create('email_batch_logs', function (Blueprint $table) {
//             $table->id();
//             $table->string('batch_id')->unique();
//             $table->unsignedBigInteger('user_id');
//             $table->integer('total_emails');
//             $table->integer('dispatched_count')->default(0);
//             $table->integer('failed_to_dispatch')->default(0);
//             $table->enum('status', ['processing', 'dispatched', 'partially_dispatched', 'completed', 'failed']);
//             $table->timestamps();

//             $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
//         });
//     }

//     public function down(): void
//     {
//         Schema::dropIfExists('email_batch_logs');
//         Schema::dropIfExists('email_logs');
//     }
// };
