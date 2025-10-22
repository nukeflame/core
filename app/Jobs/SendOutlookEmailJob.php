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
use App\Services\S3AttachmentHandler;

class SendOutlookEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;
    public $maxExceptions = 2;
    public $backoff = [30, 60, 120]; // Retry after 30s, 1m, 2m

    protected array $emailData;
    protected int $userId;
    protected ?string $jobId;
    protected ?int $emailLogId;
    protected $emailRecordId;
    protected $s3Handler;

    public function __construct(array $emailData, int $userId, ?string $jobId = null)
    {
        $this->emailData = $emailData;
        $this->userId = $userId;
        $this->jobId = $jobId;
        $this->s3Handler = new S3AttachmentHandler();
    }

    public function handle(OutlookService $outlookService): void
    {
        try {
            $user = User::findOrFail($this->userId);
            $payload = $this->prepareEmailPayload();

            $this->emailLogId = $this->createEmailLog($user, 'running');

            if (!$outlookService->isTokenValid($user->email)) {
                throw new Exception('Invalid or expired Outlook token for user: ' . $user->email);
            }

            if (isset($this->emailData['replyToId']) && $this->emailData['replyToId']) {
                $result = $this->sendReply($user, $outlookService, $payload);
            } else {
                $result = $this->sendNewMessage($user, $outlookService, $payload);
            }

            if ($result['success']) {
                $this->updateEmailLog('success', [
                    'message_id' => $result['message_id'],
                    'conversation_id' => $result['conversation_id'] ?? null,
                    'sent_at' => $result['sent_at'] ?? now()->toISOString()
                ]);

                $this->dispatchFollowUpJobs($result);
            } else {
                throw new Exception($result['error'] ?? 'Unknown error occurred while sending email');
            }
        } catch (Exception $e) {
            // $this->updateEmailLog('failed', ['error' => $e->getMessage()]);

            if (!empty($this->emailData['tempFiles'])) {
                $this->s3Handler->cleanupTempFiles($this->emailData['tempFiles']);
            }

            throw $e;
        }
    }

    /**
     * Prepare email payload for Outlook service
     */
    private function prepareEmailPayload(): array
    {
        $result = [
            'subject' => $this->emailData['subject'],
            'body' => $this->buildEmailBody(),
            'bodyType' => 'HTML',
            'to' => $this->emailData['to'],
            'cc' => $this->emailData['cc'],
            'bcc' => $this->emailData['bcc'],
            'attachments' => $this->emailData['attachments'],
            'priority' => $this->emailData['priority'] ?? 'normal',
            'customHeaders' => $this->buildCustomHeaders(),
        ];

        if (isset($data['replyToId'])) {
            $result['replyToId'] = $this->emailData['replyToId'];
            $result['messageId'] = $this->emailData['messageId'];
            $result['conversationId'] = $this->emailData['conversationId'];
        }

        return $result;
    }

    /**
     * Send a reply to existing message
     */
    private function sendNewMessage($user, $outlookService, $emailPayload): array
    {
        return $outlookService->sendEmail($user, $emailPayload);
    }

    /**
     * Send a reply to existing message
     */
    private function sendReply($user, $outlookService, $emailPayload): array
    {
        $replyData = [
            'body' => $this->emailData['replyMessage'],
            'bodyType' => $emailPayload['bodyType'],
            'attachments' => $emailPayload['attachments'] ?? [],
        ];

        return $outlookService->sendReplyAll($user, $this->emailData['messageId'], $replyData);
    }

    /**
     * Build custom headers for email tracking
     */
    private function buildCustomHeaders(): array
    {
        $headers = [
            [
                'name' => 'X-Email-Category',
                'value' => $this->emailData['category']
            ],
            [
                'name' => 'X-Email-Reference',
                'value' => $this->emailData['reference']
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
        $message = $this->emailData['message'] ?? $this->emailData['replyMessage'] ?? null;

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
        $this->updateEmailLog('failed', [
            'error' => $exception->getMessage(),
            'failed_at' => now()->toISOString(),
            'attempts' => $this->attempts()
        ]);
        // notify administrators or user about permanent failure
        // NotifyEmailFailureJob::dispatch($this->userId, $this->emailData, $exception->getMessage());
    }

    private function createEmailLog(User $user, string $status): int
    {
        return DB::table('email_sync_logs')->insertGetId([
            'user_id' => $user->id,
            'status' => $status,
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'options' => json_encode(['jobId' => $this->jobId]),
        ]);
    }

    private function updateEmailLog(string $status, array $additionalData = []): void
    {
        if (!$this->emailLogId) return;

        $updateData = array_merge([
            'status' => $status,
            'completed_at' => now(),
            'updated_at' => now(),
            'options' => json_encode($additionalData)
        ]);

        DB::table('email_sync_logs')
            ->where('id', $this->emailLogId)
            ->update($updateData);
    }

    private function dispatchFollowUpJobs(array $result): void
    {
        // if (!empty($result['message_id'])) {

        //     $alreadyProcessed = DB::table('fetched_emails')
        //         ->where('message_id', $result['message_id'])
        //         ->exists();

        //     if ($alreadyProcessed) {
        //         return;
        //     }

        //     $toRecipients = $this->emailData['toRecipients'] ?? [];

        //     if (!empty($toRecipients)) {
        //         $matchingUsers = User::whereIn('email', $toRecipients)->get();

        //         // foreach ($matchingUsers as $recipientUser) {
        //         //     // $exists = DB::table('fetched_emails')
        //         //     //     ->where('message_id', $result['message_id'])
        //         //     //     ->where('user_id', $recipientUser->id)
        //         //     //     ->exists();

        //         //     // if (!$exists) {
        //         //     //     DB::table('fetched_emails')->insert([
        //         //     //         'uid' => $result['message_id'],
        //         //     //         'user_email' => $recipientUser->email,
        //         //     //         'user_id' => $recipientUser->id,
        //         //     //         'folder' => 'inbox',
        //         //     //         'message_id' => $result['message_id'],
        //         //     //         'conversation_id' => $result['conversation_id'],
        //         //     //         'system_category' => $this->emailData['category'],
        //         //     //         'system_ref_no' => $this->emailData['reference'],
        //         //     //         'date_received' => now(),
        //         //     //         'created_at' => now(),
        //         //     //     ]);
        //         //     // }
        //         // }
        //     }
        // }
    }
}
