<?php

namespace App\Jobs;

// use App\Services\ClaimNotificationService;

use App\Models\ClaimNtfRegister;
use App\Services\ClaimNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class SendClaimNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job should run.
     */
    public int $timeout = 120;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [30, 120, 300]; // 30 seconds, 2 minutes, 5 minutes
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ClaimNtfRegister $claim,
        public array $emailData,
        public bool $isResend = false
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ClaimNotificationService $notificationService): void
    {
        try {
            if (isset($this->emailData['schedule_send'])) {
                $scheduledTime = Carbon::parse($this->emailData['schedule_send']);

                if ($scheduledTime->isFuture()) {
                    static::dispatch($this->claim, $this->emailData, $this->isResend)
                        ->delay($scheduledTime);
                    return;
                }
            }

            // Send the notification
            $notificationService->sendNotification(
                $this->claim,
                $this->emailData,
                $this->isResend
            );

            return;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Update claim status to indicate failure
        $this->claim->update([
            'notification_failed_at' => now(),
            'notification_failure_reason' => $exception->getMessage()
        ]);

        // You could also send an alert to administrators here
        // $this->notifyAdministrators($exception);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'claim_serial_no:' . $this->claim->id,
            'email-notification',
            $this->isResend ? 'resend' : 'initial'
        ];
    }
}
