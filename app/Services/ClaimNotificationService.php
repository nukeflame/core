<?php

namespace App\Services;

use App\Http\Controllers\PrintoutController;
use App\Models\ClaimNotification;
use App\Models\ClaimNtfRegister;
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ClaimNotificationMail;
use Illuminate\Http\Request;

class ClaimNotificationService
{

    private $printOutController;

    public function __construct(PrintoutController $printOutController)
    {
        $this->printOutController = $printOutController;
    }

    /**
     * Send claim notification email
     */
    public function sendNotification($claim, array $data, bool $isResend = false): bool
    {
        try {
            $notification = $this->createNotificationRecord($claim, $data, $isResend);
            $emailData = $this->prepareEmailData($claim, $data);

            $mailable = new ClaimNotificationMail($emailData);
            $request = new Request();
            $request->replace(['intimation_no' => $data['intimation_no']]);
            $pdfResponse = $this->printOutController->claimNtfDocsAckLetter($request, null);
            $data['pdf_content'] = $pdfResponse->getContent();
            $data['attach_supporting_docs'] = true;

            if ($data['attach_supporting_docs'] ?? false) {
                // $this->attachFiles($mailable, $claim);
                $this->attachGeneratedPdf($mailable, $claim, $data);
            }
            // Send the email
            $this->sendEmail($mailable, $claim, $data);

            // Update notification status
            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
                'message_id' => $this->generateMessageId()
            ]);

            return true;
        } catch (\Exception $e) {
            // Update notification with error
            if (isset($notification)) {
                $notification->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'failed_at' => now()
                ]);
            }

            throw $e;
        }

        return false;
    }

    /**
     * Generate email preview withosut sending
     */
    public function generateEmailPreview(ClaimNtfRegister $claim, array $data): array
    {
        $emailData = $this->prepareEmailData($claim, $data);

        return [
            'to' => $this->getRecipients($claim, $data),
            'cc' => $data['cc_recipients'] ?? [],
            'bcc' => $data['bcc_recipients'] ?? [],
            'subject' => $emailData['subject'],
            'message' => $emailData['message'],
            'attachments' => $this->getAttachmentInfo($claim),
            'priority' => $data['priority'] ?? 'normal',
            'scheduled_for' => $data['schedule_send'] ?? null,
            'estimated_size' => $this->calculateEmailSize($emailData, $claim)
        ];
    }

    /**
     * Create notification record in database
     */
    private function createNotificationRecord(ClaimNtfRegister $claim, array $data, bool $isResend): ClaimNotification
    {
        $message = $data['message'] ?? 'No message provided';
        $subject = $data['subject'] ?? 'Claim Notification';

        $data = [
            'subject' => $subject,
            'message' => $message,
            'priority' => $data['priority'] ?? 'normal',
            'tracking_id' => Str::uuid(),
            'sent_at' => now(),
            'attachments' => $this->getAttachmentInfo($claim),
            'estimated_size' => $this->calculateEmailSize($data, $claim)
        ];

        return ClaimNotification::create([
            'claim_id' => $claim->serial_no,
            'user_id' => auth()->id(),
            'status' => 'pending',
            'is_resend' => $isResend,
            'data' => $data,
            'recipients' => $this->getRecipients($claim, $data),
            'scheduled_for' => isset($data['schedule_send']) ? Carbon::parse($data['schedule_send']) : null
        ]);
    }

    /**
     * Prepare email data for mailable
     */
    private function prepareEmailData(ClaimNtfRegister $claim, array $data): array
    {
        return [
            'subject' => $data['subject'],
            'message' => $this->processMessageTemplate($data['message'], $claim),
            'claim' => $claim,
            'cedant' => $claim->cedant,
            'sender' => auth()->user(),
            'priority' => $data['priority'] ?? 'normal',
            'include_claim_details' => $data['include_claim_details'] ?? true,
            'additional_notes' => $data['additional_notes'] ?? null,
            'tracking_id' => Str::uuid(),
            'sent_at' => now()
        ];
    }

    /**
     * Process message template with variables
     */
    private function processMessageTemplate(string $message, ClaimNtfRegister $claim): string
    {
        $customer = Customer::where('customer_id', $claim->customer_id)->first();

        $variables = [
            '{{claim_reference}}' => $claim->intimeation_no,
            '{{claim_amount}}' => number_format(0, 2),
            '{{claim_date}}' => Carbon::parse($claim->cover_from)->format('Y-m-d'),
            '{{cedant_name}}' => $customer->name,
            '{{today_date}}' => now()->format('Y-m-d'),
            '{{company_name}}' => config('app.name'),
            '{{sender_name}}' => auth()->user()->name ?? 'System',
        ];

        return str_replace(
            array_keys($variables),
            array_values($variables),
            $message
        );
    }

    /**
     * Attach files to mailable
     */
    private function attachFiles($mailable, ClaimNtfRegister $claim): void
    {
        if (!empty($claim->files) && is_iterable($claim->files)) {
            foreach ($claim->files as $file) {
                if ($this->shouldAttachFile($file)) {
                    $filePath = Storage::path($file->path);

                    if (Storage::exists($file->path)) {
                        $mailable->attach($filePath, [
                            'as' => $file->original_name,
                            'mime' => $file->mime_type
                        ]);
                    } else {
                    }
                }
            }
        }
    }

    private function attachGeneratedPdf($mailable, ClaimNtfRegister $claim, array $data): void
    {
        if (isset($data['pdf_content'])) {
            $mailable->attachData(
                $data['pdf_content'],
                'Claim_Acknowledgement_Letter_' . $claim->intimation_no . '.pdf',
                [
                    'mime' => 'application/pdf'
                ]
            );
        }
    }

    /**
     * Determine if file should be attached
     */
    private function shouldAttachFile($file): bool
    {
        // Skip files that are too large (>10MB)
        if ($file->size > 10 * 1024 * 1024) {
            return false;
        }

        // Skip certain file types for security
        $blockedExtensions = ['exe', 'bat', 'cmd', 'scr', 'vbs', 'js'];
        if (in_array(strtolower($file->extension), $blockedExtensions)) {
            return false;
        }

        return true;
    }

    /**
     * Send the email
     */
    private function sendEmail($mailable, ClaimNtfRegister $claim, array $data): void
    {
        $recipients = $this->getRecipients($claim, $data);
        $data = [
            'subject' => 'Claim Update - Reference #CLM-2024-001',
            'claim' => $claim, // Your claim model instance
            'cedant' => 'cedant', // Your cedant model instance
            'sender' => 'sender', // User who sent the notification
            'message' => 'Your claim has been updated. Please review the details below.',
            'include_claim_details' => true,
            'additional_notes' => 'Please submit any additional documentation by the end of the week.',
            'tracking_id' => 'TRK-' . uniqid(),
            'sent_at' => now()
        ];

        Mail::to($recipients)
            ->when(!empty($data['cc_recipients']), function ($mail) use ($data) {
                return $mail->cc($data['cc_recipients']);
            })
            ->when(!empty($data['bcc_recipients']), function ($mail) use ($data) {
                return $mail->bcc($data['bcc_recipients']);
            })
            ->when($data['send_copy_to_sender'] ?? false, function ($mail) {
                return $mail->cc(auth()->user()->email);
            })
            ->send($mailable);
    }

    /**
     * Get recipients for the email
     */
    private function getRecipients(ClaimNtfRegister $claim, array $data): array
    {
        $customer = Customer::where('customer_id', $claim->customer_id)->first();
        $recipients = $data['recipients'] ?? [];

        // Always include cedant email as primary recipient
        if (!in_array($customer->email, $recipients)) {
            $recipients[] = $customer->email;
        }

        return array_unique($recipients);
    }

    /**
     * Get attachment information
     */
    private function getAttachmentInfo(ClaimNtfRegister $claim): array
    {
        // return $claim->files->map(function ($file) {
        //     return [
        //         'name' => $file->original_name,
        //         'size' => $this->formatFileSize($file->size),
        //         'type' => $file->mime_type,
        //         'will_attach' => $this->shouldAttachFile($file)
        //     ];
        // })->toArray();
        return [];
    }

    /**
     * Calculate estimated email size
     */
    private function calculateEmailSize(array $emailData, ClaimNtfRegister $claim): string
    {
        // $textSize = strlen($emailData['message']) + strlen($emailData['subject']);
        // $attachmentSize = $claim->files->where('size', '<=', 10 * 1024 * 1024)->sum('size');

        // return $this->formatFileSize($textSize + $attachmentSize);
        return '0 B'; // Placeholder, implement actual size calculation if needed
    }

    /**
     * Format file size for display
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }

    /**
     * Generate unique message ID for tracking
     */
    private function generateMessageId(): string
    {
        return sprintf(
            '<%s.%s@%s>',
            time(),
            Str::random(8),
            parse_url(config('app.url'), PHP_URL_HOST)
        );
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats(ClaimNtfRegister $claim): array
    {
        $notifications = $claim->notifications;

        return [
            'total_sent' => $notifications->where('status', 'sent')->count(),
            'total_failed' => $notifications->where('status', 'failed')->count(),
            'last_sent' => $notifications->where('status', 'sent')->last()?->sent_at,
            'last_failed' => $notifications->where('status', 'failed')->last()?->failed_at,
            'resend_count' => $notifications->where('is_resend', true)->count()
        ];
    }
}
