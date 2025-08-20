<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\OutlookService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendOutlookEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $maxExceptions = 2;
    public $backoff = [30, 60, 120]; // Retry after 30s, 1m, 2m

    protected array $emailData;
    protected int $userId;
    protected ?string $jobId;
    protected ?int $emailLogId;
    protected $emailRecordId;


    public function __construct(array $emailData, int $userId, ?string $jobId = null)
    {
        $this->emailData = $emailData;
        $this->userId = $userId;
        $this->jobId = $jobId;
        // $this->emailRecordId = $emailRecordId;
        // $this->onQueue('emails'); // Use dedicated email queue
    }

    public function handle(OutlookService $outlookService): void
    {
        try {
            $user = User::findOrFail($this->userId);
            $emailPayload = $this->prepareEmailPayload();

            $this->emailLogId = $this->createEmailLog($user, 'processing');

            if (!$outlookService->isTokenValid($user->email)) {
                throw new Exception('Invalid or expired Outlook token for user: ' . $user->email);
            }

            if (!empty($this->emailData['replyToId'])) {
                $result = $this->sendReply($user, $outlookService, $emailPayload);
            } else {
                $result = $outlookService->sendEmail($user, $emailPayload);
            }

            // logger()->info(['result' => $result]);
            if ($result['success']) {
                // $this->updateEmailLog('sent', [
                //     'message_id' => $result['message_id'],
                //     'conversation_id' => $result['conversation_id'] ?? null,
                //     'sent_at' => $result['sent_at'] ?? now()->toISOString()
                // ]);

                // Dispatch follow-up jobs if needed
                // $this->dispatchFollowUpJobs($result);
            } else {
                throw new Exception($result['error'] ?? 'Unknown error occurred while sending email');
            }
        } catch (Exception $e) {
            // $this->updateEmailLog('failed', ['error' => $e->getMessage()]);

            logger()->error('Email send job failed', [
                'job_id' => $this->jobId,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries
            ]);

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Prepare email payload for Outlook service
     */
    private function prepareEmailPayload(): array
    {
        return [
            'subject' => $this->emailData['subject'],
            'body' => $this->buildEmailBody(),
            'bodyType' => 'HTML',
            'to' => $this->emailData['to'],
            'cc' => $this->emailData['cc'],
            'bcc' => $this->emailData['bcc'],
            'attachments' => $this->emailData['attachments'] ?? [],
            'priority' => $this->emailData['priority'] ?? 'normal',
            'customHeaders' => $this->buildCustomHeaders(),
            'replyToId' => $this->emailData['replyToId'] ?? null,
            'conversationId' => $this->emailData['conversationId'] ?? null
        ];
    }


    /**
     * Send a reply to existing message
     */
    private function sendReply(User $user, OutlookService $outlookService, array $emailPayload): array
    {
        $replyData = [
            // 'toRecipients' => [
            //     ['address' => $this->emailData['to']],
            // ],
            // 'subject' => $emailPayload['subject'],
            'body' => $this->emailData['replyMessage'],
            'bodyType' => $emailPayload['bodyType'],
            'attachments' => $emailPayload['attachments'] ?? [],
        ];

        return $outlookService->sendReplyAll($user, $this->emailData['replyToId'], $replyData);
    }

    /**
     * Build custom headers for email tracking
     */
    private function buildCustomHeaders(): array
    {
        $headers = [
            [
                'name' => 'X-Claim-Number',
                'value' => $this->emailData['claim']->claim_no
            ],
            [
                'name' => 'X-Email-Category',
                'value' => $this->emailData['category'] ?? 'claim'
            ],
            [
                'name' => 'X-System-Generated',
                'value' => 'true'
            ]
        ];

        if (!empty($this->emailData['reference'])) {
            $headers[] = [
                'name' => 'X-Reference',
                'value' => $this->emailData['reference']
            ];
        }

        if ($this->emailRecordId) {
            $headers[] = [
                'name' => 'X-Email-Record-ID',
                'value' => (string)$this->emailRecordId
            ];
        }

        return $headers;
    }

    /**
     * Build email body with proper HTML formatting
     */
    private function buildEmailBody(): string
    {
        // $claim = $this->emailData['claim'];
        $message = $this->emailData['message'];
        // $companyName = config('app.name', 'Acentria International Reinsurance Brokers Limited');

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Claim Notification</title>
            </head>
            <body>
                <div>{$message}</div>
            </body>
            </html>";
    }


    public function failed(Exception $exception): void
    {
        // $this->updateEmailLog('failed', [
        //     'error' => $exception->getMessage(),
        //     'failed_at' => now()->toISOString(),
        //     'attempts' => $this->attempts()
        // ]);

        logger()->error('Email send job permanently failed', [
            'job_id' => $this->jobId,
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // Optionally notify administrators or user about permanent failure
        // NotifyEmailFailureJob::dispatch($this->userId, $this->emailData, $exception->getMessage());
    }

    private function createEmailLog(User $user, string $status): int
    {
        // return DB::table('email_logs')->insertGetId([
        //     'user_id' => $user->id,
        //     'user_email' => $user->email,
        //     'job_id' => $this->jobId,
        //     'subject' => $this->emailData['subject'] ?? 'N/A',
        //     'to_recipients' => json_encode($this->emailData['to']),
        //     'cc_recipients' => json_encode($this->emailData['cc'] ?? []),
        //     'bcc_recipients' => json_encode($this->emailData['bcc'] ?? []),
        //     'body_preview' => substr(strip_tags($this->emailData['body'] ?? ''), 0, 200),
        //     'priority' => $this->emailData['priority'] ?? 'normal',
        //     'status' => $status,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);
        return 0;
    }

    private function updateEmailLog(string $status, array $additionalData = []): void
    {
        if (!$this->emailLogId) return;

        $updateData = array_merge([
            'status' => $status,
            'updated_at' => now()
        ], $additionalData);

        // DB::table('email_logs')
        //     ->where('id', $this->emailLogId)
        //     ->update($updateData);
    }

    private function dispatchFollowUpJobs(array $result): void
    {
        // Example: Schedule email tracking job
        // if (!empty($result['message_id'])) {
        //     TrackEmailDeliveryJob::dispatch($result['message_id'], $this->userId)
        //         ->delay(now()->addMinutes(5));
        // }
    }
}
