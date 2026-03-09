<?php

namespace App\Jobs;

use Nukeflame\Core\Services\OutlookService;
use Exception;
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
    protected $recipient;
    protected $ccEmails;
    protected $bccEmails;
    protected $emailData;
    protected $auth;
    protected $emailRecordId;

    /**
     * Create a new job instance.
     */
    public function __construct($recipient, $ccEmails, $bccEmails, $emailData, $emailRecordId = null, $auth = null)
    {
        $this->recipient = $recipient;
        $this->ccEmails = $ccEmails;
        $this->bccEmails = $bccEmails;
        $this->emailData = $emailData;
        $this->emailRecordId = $emailRecordId;
        $this->auth = $auth;
    }

    /**
     * Execute the job.
     */
    public function handle(OutlookService $outlookService): void
    {
        try {
            $emailPayload = $this->prepareEmailPayload();
            if (!empty($this->emailData['reply_to_id'])) {
                $result = $this->sendReply($outlookService, $emailPayload);
            } else {
                $result = $this->sendNewEmail($outlookService, $emailPayload);
            }

            if ($result['success']) {
                $this->handleSuccessfulSend($result);
            } else {
                throw new Exception($result['error'] ?? 'Unknown error occurred');
            }
        } catch (Exception $e) {
            $this->handleFailedSend($e);
            throw $e;
        }
    }

    /**
     * Send a new email
     */
    private function sendNewEmail(OutlookService $outlookService, array $emailPayload): array
    {
        return $outlookService->sendEmailWithMessageId($this->auth, $emailPayload);
    }

    /**
     * Send a reply to existing message
     */
    private function sendReply(OutlookService $outlookService, array $emailPayload): array
    {
        $replyData = [
            'toRecipients' => [
                ['address' => $this->recipient],
                // ['name' => '']
            ],
            'subject' => $emailPayload['subject'],
            'body' => $this->emailData['raw_message'],
            'bodyType' => $emailPayload['bodyType'],
            'attachments' => $emailPayload['attachments'] ?? [],
        ];

        return $outlookService->sendReply($this->auth, $this->emailData['reply_to_id'], $replyData);
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
            'to' => [['address' => $this->recipient]],
            'cc' => array_map(fn($email) => ['address' => $email], $this->ccEmails),
            'bcc' => array_map(fn($email) => ['address' => $email], $this->bccEmails),
            'attachments' => $this->emailData['attachments'] ?? [],
            'priority' => $this->emailData['priority'] ?? 'normal',
            'customHeaders' => $this->buildCustomHeaders(),
            'replyToId' => $this->emailData['reply_to_message_id'] ?? null,
            'conversationId' => $this->emailData['conversation_id'] ?? null
        ];
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

    // private function buildEmailBody(): string
    // {
    //     $claim = $this->emailData['claim'];
    //     $message = $this->emailData['message'];
    //     $companyName = config('app.name', 'Acentria International Reinsurance Brokers Limited');

    //     return "
    //     <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
    //         <div style='background: linear-gradient(135deg, #2c3e50, #3498db); color: white; padding: 20px; text-align: center;'>
    //             <h2 style='margin: 0;'>Claim Notification</h2>
    //             <p style='margin: 10px 0 0 0;'>{$companyName}</p>
    //         </div>

    //         <div style='padding: 20px;'>
    //             <div style='background: #f8f9fa; border-left: 4px solid #3498db; padding: 15px; margin: 20px 0;'>
    //                 <h3 style='margin: 0 0 10px 0; color: #2c3e50;'>Claim Information</h3>
    //                 <p><strong>Claim Number:</strong> {$claim->claim_no}</p>
    //                 <p><strong>Date:</strong> " . now()->format('F j, Y \a\t g:i A') . "</p>
    //                 <p><strong>Priority:</strong> " . ucfirst($this->emailData['priority'] ?? 'normal') . "</p>
    //             </div>

    //             <div style='background: white; border: 1px solid #e1e8ed; padding: 20px; margin: 20px 0;'>
    //                 {$message}
    //             </div>

    //             " . ($this->hasAttachments() ? "
    //             <div style='background: #f8f9fa; border: 1px solid #e1e8ed; padding: 15px; margin: 20px 0;'>
    //                 <h4 style='margin: 0 0 10px 0;'>📎 Attachments (" . count($this->emailData['attachments']) . ")</h4>
    //                 <p>Please review the attached documents for claim details.</p>
    //             </div>
    //             " : "") . "

    //             <div style='background: #34495e; color: #ecf0f1; padding: 20px; text-align: center; margin-top: 30px;'>
    //                 <h4 style='margin: 0 0 10px 0; color: #3498db;'>{$companyName}</h4>
    //                 <p style='margin: 5px 0; font-size: 14px;'>Professional Reinsurance Services</p>
    //                 <p style='margin: 15px 0 5px 0; font-size: 12px; opacity: 0.8;'>
    //                     © " . date('Y') . " {$companyName}. All rights reserved.
    //                 </p>
    //             </div>
    //         </div>
    //     </div>";
    // }

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
     * Check if email has attachments
     */
    private function hasAttachments(): bool
    {
        return !empty($this->emailData['attachments']) && count($this->emailData['attachments']) > 0;
    }

    /**
     * Handle successful email send
     */
    private function handleSuccessfulSend(array $result): void
    {
        $this->updateEmailStatus('sent', null, [
            'outlook_message_id' => $result['message_id'] ?? null,
            'conversation_id' => $result['conversation_id'] ?? null,
            'internet_message_id' => $result['internet_message_id'] ?? null,
            'sent_at' => $result['sent_at'] ?? now()->toISOString()
        ]);
    }

    /**
     * Handle failed email send
     */
    private function handleFailedSend(Exception $exception): void
    {
        $this->updateEmailStatus('failed', $exception->getMessage());
    }

    /**
     * Handle a job failure (all retries exhausted)
     */
    public function failed(Exception $exception): void
    {
        $this->updateEmailStatus('permanently_failed', $exception->getMessage());
    }

    /**
     * Update email status in database
     */
    private function updateEmailStatus(string $status, ?string $errorMessage = null, array $additionalData = []): void
    {
        try {
            $updateData = [
                'status' => $status,
                'updated_at' => now()
            ];

            if ($status === 'sent') {
                $updateData['sent_at'] = now();
                $updateData = array_merge($updateData, $additionalData);
            }

            if (in_array($status, ['failed', 'permanently_failed'])) {
                $updateData['failed_at'] = now();
                $updateData['error_message'] = $errorMessage;
            }

            if ($this->emailRecordId) {
                // Email::where('id', $this->emailRecordId)->update($updateData);
            } else {
                // Fallback: update by claim number and recipient
                // Email::where('claim_no', $this->emailData['claim']->claim_no)
                //     ->where('recipients', 'like', '%' . $this->recipient . '%')
                //     ->update($updateData);
            }
        } catch (Exception $e) {
        }
    }


    // /**
    //  * Handle a job failure.
    //  */
    // public function failed(Exception $exception): void
    // {
    //     // Update email status to permanently failed
    //     $this->updateEmailStatus('permanently_failed', $exception->getMessage());
    // }

    // /**
    //  * Update email status in database
    //  */
    // private function updateEmailStatus($status, $errorMessage = null)
    // {
    //     try {
    //         // Find the email record and update status
    //         Email::where('claim_no', $this->emailData['claim']->claim_no)
    //             ->where('recipients', 'like', '%' . $this->recipient . '%')
    //             ->update([
    //                 'status' => $status,
    //                 'sent_at' => $status === 'sent' ? now() : null,
    //                 'failed_at' => in_array($status, ['failed', 'permanently_failed']) ? now() : null,
    //                 'error_message' => $errorMessage,
    //                 'updated_at' => now()
    //             ]);
    //     } catch (Exception $e) {
    //     }
    // }
}
