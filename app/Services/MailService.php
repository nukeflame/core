<?php

namespace App\Services;

use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MailService
{
    private $auth;
    protected string $batchId;

    public function __construct(
        private OutlookService $outlookService,
        private EmailStorageService $storageService,
    ) {
        $this->auth = Auth::user() ?? null;
        $this->batchId = Str::uuid()->toString();
    }

    public function getMailData(string $folder = 'inbox', ?string $search = null, int $limit = 50): array
    {
        // logger()->info(json_encode($this->getOnlineUsers(), JSON_PRETTY_PRINT));
        return [
            'emails' => $this->getEmails($folder, $limit, $search),
            'folders' => $this->getFolders(),
            'contacts' => $this->getContacts(),
            'onlineUsers' => $this->getOnlineUsers()
        ];
    }

    /**
     * Get emails from Outlook service or database
     * Maintains compatibility with original method signature
     */
    public function getEmails(string $folder = 'inbox', int $limit = 50, ?string $search = null): Collection
    {
        $cacheKey = "emails.{$folder}.{$limit}." . md5($search ?? '');

        return Cache::remember($cacheKey, 300, function () use ($folder, $limit, $search) {
            try {
                $storedEmails = $this->storageService->getStoredEmails($folder, $limit, $search);

                // If we have stored emails and no search, return them
                if ($storedEmails->isNotEmpty() && !$search) {
                    return $storedEmails;
                }

                return collect();
                // Fetch from Outlook service - matches original outlookService->getEmails() call
                // return $this->outlookService->getEmails($folder, $limit, $search);
            } catch (\Exception $e) {
                // Fallback to stored emails
                return $this->storageService->getStoredEmails($folder, $limit, $search);
            }
        });
    }

    /**
     * Get folders from Outlook service
     * Maintains compatibility with original method signature
     */
    public function getFolders(): Collection
    {
        // return Cache::remember('outlook.folders', 3600, function () {
        //     try {
        //         // Matches original outlookService->getFolders() call
        //         return $this->outlookService->getFolders();
        //     } catch (\Exception $e) {
        //         return $this->getDefaultFolders();
        //     }
        // });
        return collect();
    }

    /**
     * Get contacts from Outlook service
     * Maintains compatibility with original method signature
     */
    public function getContacts(): Collection
    {
        // return Cache::remember('outlook.contacts', 1800, function () {
        //     try {
        //         // Matches original outlookService->getContacts() call
        //         return $this->outlookService->getContacts();
        //     } catch (\Exception $e) {
        //         return collect();
        //     }
        // });
        // return $this->outlookService->getContacts();

        return collect();
    }

    /**
     * Get users from Outlook service
     * Maintains compatibility with original method signature
     */
    public function getOnlineUsers()
    {
        return Cache::remember('outlook.users', 1800, function () {
            try {
                $this->auth = auth()->user();
                $response = $this->outlookService->getAllUsers($this->auth, [
                    'limit' => 500,
                    'include_presence' => true,
                    'include_photos' => false,
                    // 'department' => 'Reinsurance Brokers',
                    'account_enabled' => true
                ]);

                $availableUsers = collect($response['users'])
                    ->filter(function ($user) {
                        return isset($user['presence']['is_available']) && $user['presence']['is_available'] === true;
                    })
                    ->values()
                    ->map(fn($user) => [
                        'id' => $user['id'],
                        'name' => $user['displayName'] ?? null,
                        'email' => $user['mail'] ?? null,
                        'jobTitle' => $user['jobTitle'] ?? null,
                        'department' => $user['department'] ?? null,
                        'officeLocation' => $user['officeLocation'] ?? null,
                        'isOnline' => true,
                        'status' => 'Available'
                    ])
                    ->toArray();

                return collect($availableUsers);
            } catch (\Exception $e) {
                return collect();
            }
        });
    }

    public function getEmail(string $id): ?array
    {
        return $this->storageService->getStoredEmail($id);
    }

    public function sendEmail(array $data): bool
    {
        if (!$this->auth) {
            return false;
        }

        if (empty($data['to']) || empty($data['subject']) || empty($data['body'])) {
            return false;
        }

        try {
            $allEmails = array_merge(
                $data['to'] ?? [],
                $data['cc'] ?? [],
                $data['bcc'] ?? []
            );

            $recipientNames = ContactNameMappingService::getRecipientNames(null, $allEmails);
            $attachments = [];

            // if ($request->hasFile('attachments')) {
            //     foreach ($request->file('attachments') as $index => $file) {
            //         try {
            //             $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
            //             $path = $file->storeAs('claim_attachments/' . $claim->claim_no, $filename, 'public');

            //             $attachments[] = [
            //                 'name' => $file->getClientOriginalName(),
            //                 'path' => storage_path('app/public/' . $path),
            //                 'size' => $file->getSize(),
            //                 'mime_type' => $file->getMimeType()
            //             ];
            //         } catch (Exception $e) {
            //             logger()->error('Failed to process attachment', [
            //                 'index' => $index,
            //                 'filename' => $file->getClientOriginalName(),
            //                 'error' => $e->getMessage()
            //             ]);
            //             throw new Exception("Failed to process attachment: " . $file->getClientOriginalName() . ". Error: " . $e->getMessage());
            //         }
            //     }
            // }

            $recipients = array_unique($data['to']);

            if (empty($recipients)) {
                return false;
            }

            $successCount = 0;
            $failedCount = 0;

            foreach ($recipients as $index => $recipient) {
                try {
                    $jobId = $this->batchId . '-' . ($index + 1);

                    $recipientEmail = is_array($recipient) ? $recipient : [$recipient];

                    $recipientName = $recipientNames[$recipient] ?? 'Sir/Madam';

                    $personalizedMessage = $this->formatMessageForHtml($data['body'], $recipientName);

                    $emailData = [
                        'subject' => $data['subject'],
                        'message' => $personalizedMessage,
                        'priority' => $data['priority'] ?? 'normal',
                        'attachments' => $attachments,
                        'senderName' => $this->auth->name,
                        'senderEmail' => $this->auth->email,
                        'recipientName' => $recipientName,
                        'to' => $recipientEmail,
                        'cc' => $data['cc'] ?? [],
                        'bcc' => $data['bcc'] ?? [],
                        'replyToId' => $data['reply_to_id'] ?? null,
                    ];

                    SendEmailJob::dispatch($emailData, $this->auth->id, $jobId)
                        ->delay(now()->addSeconds($index * 2));

                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    logger()->error("Failed to dispatch email job", [
                        'index' => $index,
                        'recipient' => $recipient,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return $successCount > 0;
        } catch (\Exception $e) {
            logger()->error('Email send failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function formatMessageForHtml($message, $recipientName = 'Sir/Madam')
    {
        if (empty($message)) {
            return '';
        }

        $personalizedMessage = str_replace('{recipient_name}', $recipientName, $message);

        if (preg_match('/<[^>]+>/', $personalizedMessage)) {
            return $personalizedMessage;
        }

        $html = htmlspecialchars($personalizedMessage, ENT_QUOTES, 'UTF-8');
        $html = nl2br($html);
        $html = '<p>' . str_replace("\n\n", '</p><p>', $html) . '</p>';

        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        return $html;
    }


    public function replyToEmail(string $id, array $data): bool
    {
        return false;
        // return $this->outlookService->replyToEmail($id, $data);
    }

    public function toggleStar(string $id, bool $starred): bool
    {
        return false;

        // return $this->outlookService->updateEmailFlag($id, 'flagged', $starred);
    }

    public function deleteEmail(string $id): bool
    {
        return false;

        // return $this->outlookService->deleteEmail($id);
    }

    public function archiveEmail(string $id): bool
    {
        return false;

        // return $this->outlookService->moveEmail($id, 'archive');
    }

    public function markAsSpam(string $id): bool
    {
        return false;

        // return $this->outlookService->moveEmail($id, 'junkemail');
    }

    public function markAsRead($id): bool
    {
        $this->auth = auth()->user();
        return $this->outlookService->markMessage($this->auth, $id, true);
    }

    public function markAsUnread(string $id): bool
    {
        $this->auth = auth()->user();
        return $this->outlookService->markMessage($this->auth, $id, false);
    }

    public function getNewEmailCount(): int
    {
        try {
            return 0;

            // return $this->outlookService->getNewEmailCount();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function downloadAttachment(string $emailId, string $attachmentId)
    {
        // $attachment = $this->outlookService->getAttachment($emailId, $attachmentId);

        // if (!$attachment) {
        //     throw new \Exception('Attachment not found');
        // }

        // return response()->streamDownload(
        //     function () use ($attachment) {
        //         echo base64_decode($attachment['contentBytes']);
        //     },
        //     $attachment['name'],
        //     ['Content-Type' => $attachment['contentType']]
        // );
        return null;
    }

    public function downloadAllAttachments(string $emailId)
    {
        return null;

        // $zipFile = $this->outlookService->createAttachmentsZip($emailId);

        // return response()->download($zipFile, 'attachments.zip')->deleteFileAfterSend();
    }

    private function getDefaultFolders(): Collection
    {
        return collect([
            (object) ['id' => 'inbox', 'displayName' => 'Inbox'],
            (object) ['id' => 'sent', 'displayName' => 'Sent Items'],
            (object) ['id' => 'drafts', 'displayName' => 'Drafts'],
            (object) ['id' => 'deleted', 'displayName' => 'Deleted Items'],
            (object) ['id' => 'spam', 'displayName' => 'Junk Email'],
        ]);
    }

    /**
     * Get single email by message_id
     */
    public function getEmailByMessageId(string $messageId, string $user_email)
    {
        return DB::table('fetched_emails')->where('user_email', $user_email)->where('uid', $messageId)->get();
    }
}
