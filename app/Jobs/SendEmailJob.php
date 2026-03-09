<?php

namespace App\Jobs;

use App\Models\User;
use Nukeflame\Core\Services\OutlookService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;
    public $maxExceptions = 2;
    public $backoff = [30, 60, 120];

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

        // $this->onQueue('emails');
    }

    public function handle(OutlookService $outlookService)
    {
        try {
            $user = User::findOrFail($this->userId);
            $emailPayload = $this->prepareEmailPayload();

            if (!$outlookService->isTokenValid($user->email)) {
                throw new Exception('Invalid or expired Outlook token for user: ' . $user->email);
            }

            if (!empty($this->emailData['replyToId'])) {
                $result = $this->sendReply($user, $outlookService, $emailPayload);
            } else {
                $result = $outlookService->sendEmail($user, $emailPayload);
            }

            if ($result['success']) {
                return response()->json([
                    'message_id' => $result['message_id'],
                    'conversation_id' => $result['conversation_id'] ?? null,
                    'sent_at' => $result['sent_at'] ?? now()->toISOString()
                ]);
            } else {
                throw new Exception($result['error'] ?? 'Unknown error occurred while sending email');
            }
        } catch (Exception $e) {
            throw $e;
        }
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
            'body' => $this->emailData['body'],
            'bodyType' => $emailPayload['bodyType'],
            'attachments' => $emailPayload['attachments'] ?? [],
        ];

        return $outlookService->sendReplyAll($user, $this->emailData['replyToId'], $replyData);
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
     * Build email body with proper HTML formatting
     */
    private function buildEmailBody(): string
    {
        $message = $this->emailData['message'] ?? '';

        if (empty($message)) {
            $message = '<p>No message content provided.</p>';
        }

        $response = "<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                    <title>Email Notification</title>
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

                        body {
                            font-family: 'Poppins', Arial, sans-serif;
                            color: #333;
                            padding: 0px;
                            margin: 0px;
                        }
                        .email-container {
                            padding: 0px;
                            margin: 0px;
                        }
                        p {
                            margin: 0px;
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        " . $message . "
                    </div>
                </body>
                </html>";

        return $response;
    }

    /**
     * Build custom headers for email tracking
     */
    private function buildCustomHeaders(): array
    {
        $headers = [
            [
                'name' => 'X-Job-ID',
                'value' => $this->jobId
            ],
            [
                'name' => 'X-Email-Category',
                'value' => $this->emailData['category'] ?? 'send'
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
}
