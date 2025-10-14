<?php

namespace App\Jobs;

use App\Services\OutlookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchOutlookEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailOptions;
    protected $token;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 2;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(array $emailOptions = [], ?array $token)
    {
        $this->emailOptions = $emailOptions;
        $this->token = $token ?? null;
    }

    /**
     * Execute the job.
     */
    public function handle(OutlookService $outlookService): void
    {
        try {
            $result = $outlookService->fetchEmails($this->emailOptions);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Outlook email fetch job failed permanently', [
            'error' => $exception->getMessage(),
            'options' => $this->emailOptions,
            'has_token' => !empty($this->token)
        ]);
    }
}
