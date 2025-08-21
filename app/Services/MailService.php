<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MailService
{
    private $auth;

    public function __construct(
        private OutlookService $outlookService,
        private EmailStorageService $storageService,
    ) {}

    public function getMailData(string $folder = 'inbox', ?string $search = null, int $limit = 50): array
    {
        logger()->info($this->getOnlineUsers());
        return [
            'emails' => $this->getEmails($folder, $limit, $search),
            'folders' => $this->getFolders(),
            'contacts' => $this->getContacts()
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
    public function getOnlineUsers(): Collection
    {
        // return Cache::remember('outlook.users', 1800, function () {
        //     try {
        //         // Matches original outlookService->getContacts() call
        //         return $this->outlookService->getContacts();
        //     } catch (\Exception $e) {
        //         return collect();
        //     }
        // });
        // return $this->outlookService->getContacts();

        $this->outlookService->getContacts();

        return collect();
    }



    public function getEmail(string $id): ?array
    {
        return $this->storageService->getStoredEmail($id);
    }

    public function sendEmail(array $data): bool
    {
        return false;

        // return $this->outlookService->sendEmail($data);
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
