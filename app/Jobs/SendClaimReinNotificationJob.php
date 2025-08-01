<?php

namespace App\Jobs;

use App\Models\ClaimRegister;
use App\Services\ClaimNotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendClaimReinNotificationJob implements ShouldQueue
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

    public bool $isResend = false;
    public $claim;
    public $emailData;
    /**
     * Create a new job instance.
     */
    public function __construct($claim, $emailData, $isResend = false)
    {
        $this->claim = $claim;
        // $this->emailData = $emailData;
        // $this->isResend = $isResend;
        logger($claim);
    }

    // public ClaimRegister $claim,
    //
    /**
     * Execute the job.
     */
    public function handle(ClaimNotificationService $notificationService): void
    {
        // logger($this->claim);
        // try {
        //     if (isset($this->emailData['schedule_send'])) {
        //         $scheduledTime = Carbon::parse($this->emailData['schedule_send']);

        //         if ($scheduledTime->isFuture()) {
        //             static::dispatch($this->claim, $this->emailData, $this->isResend)
        //                 ->delay($scheduledTime);
        //             return;
        //         }
        //     }

        //     // Send the notification
        //     $notificationService->sendNotification(
        //         $this->claim,
        //         $this->emailData,
        //         $this->isResend
        //     );

        //     return;
        // } catch (\Exception $e) {
        //     throw $e;
        // }
    }
}
