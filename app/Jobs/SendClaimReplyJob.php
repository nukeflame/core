<?php

namespace App\Jobs;

use App\Models\Email;
use App\Services\OutlookService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendClaimReplyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min
    public $timeout = 300; // 5 minutes

    protected $originalMessageId;
    protected $emailData;
    protected $emailRecordId;

    /**
     * Create a new job instance.
     */
    public function __construct($originalMessageId, $emailData, $emailRecordId)
    {
        $this->originalMessageId = $originalMessageId;
        $this->emailData = $emailData;
        $this->emailRecordId = $emailRecordId;
    }

    /**
     * Execute the job.
     */
    public function handle(OutlookService $outlookService): void
    {
        try {
            // Prepare reply data
            $replyData = [
                'subject' => $this->emailData['subject'],
                'body' => $this->buildReplyBody(),
                'bodyType' => 'HTML',
                'attachments' => $this->emailData['attachments'] ?? [],
                'comment' => 'Reply to claim notification',
                'priority' => 'normal'
            ];

            $auth = auth()->user;

            // Send the reply
            // $result = $outlookService->sendReply($this->originalMessageId, $replyData);
            $result = $outlookService->sendReplyAll($auth, $this->originalMessageId, $replyData);

            logger()->info(['dd' => $auth]);

            if ($result['success']) {
                $this->handleSuccessfulReply($result);
            } else {
                throw new Exception($result['error'] ?? 'Unknown error occurred while sending reply');
            }
        } catch (Exception $e) {
            $this->handleFailedReply($e);
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Build reply email body
     */
    private function buildReplyBody(): string
    {
        $claim = $this->emailData['claim'];
        $message = $this->emailData['message'];
        $companyName = config('app.name', 'Acentria International Reinsurance Brokers Limited');

        return "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='background: linear-gradient(135deg, #2c3e50, #3498db); color: white; padding: 15px; text-align: center;'>
                <h3 style='margin: 0;'>Reply: Claim {$claim->claim_no}</h3>
                <p style='margin: 5px 0 0 0; font-size: 14px;'>{$companyName}</p>
            </div>

            <div style='padding: 20px;'>
                <div style='background: #f8f9fa; border-left: 4px solid #3498db; padding: 15px; margin: 15px 0;'>
                    <p><strong>Claim Number:</strong> {$claim->claim_no}</p>
                    <p><strong>Reply Date:</strong> " . now()->format('F j, Y \a\t g:i A') . "</p>
                    <p><strong>Priority:</strong> " . ucfirst($this->emailData['priority'] ?? 'normal') . "</p>
                </div>

                <div style='background: white; border: 1px solid #e1e8ed; padding: 20px; margin: 15px 0;'>
                    {$message}
                </div>

                " . ($this->hasAttachments() ? "
                <div style='background: #f8f9fa; border: 1px solid #e1e8ed; padding: 15px; margin: 15px 0;'>
                    <h4 style='margin: 0 0 10px 0;'>📎 Additional Documents (" . count($this->emailData['attachments']) . ")</h4>
                    <p>Please review the additional documents attached to this reply.</p>
                </div>
                " : "") . "

                <div style='background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; padding: 12px; margin: 15px 0;'>
                    <p style='margin: 0; color: #856404; font-size: 14px;'>
                        <strong>📧 This is a reply</strong> to your previous correspondence regarding claim {$claim->claim_no}.
                    </p>
                </div>

                <div style='background: #34495e; color: #ecf0f1; padding: 15px; text-align: center; margin-top: 20px;'>
                    <p style='margin: 0; font-size: 14px;'>{$companyName}</p>
                    <p style='margin: 5px 0 0 0; font-size: 12px; opacity: 0.8;'>
                        © " . date('Y') . " All rights reserved.
                    </p>
                </div>
            </div>
        </div>";
    }

    /**
     * Check if reply has attachments
     */
    private function hasAttachments(): bool
    {
        return !empty($this->emailData['attachments']) && count($this->emailData['attachments']) > 0;
    }

    /**
     * Handle successful reply send
     */
    private function handleSuccessfulReply(array $result): void
    {
        Log::info('Claim reply email sent successfully', [
            'original_message_id' => $this->originalMessageId,
            'claim_no' => $this->emailData['claim']->claim_no,
            'conversation_id' => $result['conversation_id'] ?? null,
            'email_record_id' => $this->emailRecordId
        ]);

        // Update email record with success details
        $this->updateEmailStatus('sent', null, [
            'conversation_id' => $result['conversation_id'] ?? null,
            'sent_at' => now()->toISOString()
        ]);
    }

    /**
     * Handle failed reply send
     */
    private function handleFailedReply(Exception $exception): void
    {
        Log::error('Failed to send claim reply email', [
            'original_message_id' => $this->originalMessageId,
            'claim_no' => $this->emailData['claim']->claim_no ?? 'N/A',
            'error' => $exception->getMessage(),
            'attempt' => $this->attempts(),
            'email_record_id' => $this->emailRecordId
        ]);

        // Update email status to failed
        $this->updateEmailStatus('failed', $exception->getMessage());
    }

    /**
     * Handle a job failure (all retries exhausted)
     */
    public function failed(Exception $exception): void
    {
        Log::error('Claim reply email job failed permanently', [
            'original_message_id' => $this->originalMessageId,
            'claim_no' => $this->emailData['claim']->claim_no ?? 'N/A',
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'email_record_id' => $this->emailRecordId
        ]);

        // Update email status to permanently failed
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

            Email::where('id', $this->emailRecordId)->update($updateData);
        } catch (Exception $e) {
            Log::warning('Failed to update reply email status', [
                'email_record_id' => $this->emailRecordId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
        }
    }
}
