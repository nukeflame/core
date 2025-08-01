<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendHandOverApproverEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;

    /**
     * Create a new job instance.
     *
     * @param array $emailData
     */
    public function __construct(array $emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Mail::send('emailnotifications.salesemails', $this->emailData, function ($message) {
                $message->to($this->emailData['email'])
                    ->cc(!empty($this->emailData['cc']) ? $this->emailData['cc'] : [])
                    ->subject($this->emailData['title'] ?? 'Notification');
            });
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     */
    public function failed(\Throwable $exception) {}
}
