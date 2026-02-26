<?php

namespace App\Services;

use App\Jobs\SendEmailJob;
use App\Jobs\SendOutlookEmailJob;
use App\Jobs\SyncOutlookJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\S3AttachmentHandler;
use App\Models\User;

class MailService
{
    private $auth;
    protected string $batchId;
    protected $s3Handler;

    public function __construct(
        private OutlookService $outlookService,
        private EmailStorageService $storageService,
    ) {
        $this->auth = Auth::user() ?? null;
        $this->batchId = Str::uuid()->toString();
        $this->s3Handler = new S3AttachmentHandler();
    }

    public function getMailData(string $folder = 'inbox', ?string $search = null, int $limit = 50): array
    {
        $forceRefresh = false;
        return [
            'emails' => $this->getEmails($folder, $limit, $forceRefresh, $search),
            'folders' => $this->getFolders(),
            'contacts' => $this->getContacts(),
            'onlineUsers' => $this->getOnlineUsers()
        ];
    }

    public function getEmails(string $folder = 'inbox', int $limit = 50, bool $forceRefresh = false, ?string $search = null): Collection
    {
        return $this->storageService->getStoredEmails($folder, $limit, $search);
    }

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

    public function getOnlineUsers()
    {
        return Cache::remember('outlook.users', 1800, function () {
            try {
                $this->auth = auth()->user();
                $response = $this->outlookService->getAllUsers($this->auth, [
                    'limit' => 500,
                    'include_presence' => true,
                    'include_photos' => false,
                    // 'department' => 'Reinsurance',
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
                        'email' => $user['email'] ?? null,
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

    public function sendEmail(array $data, ?int $userId = null): bool
    {
        $authUser = $this->resolveSender($userId);
        if (!$authUser) {
            return false;
        }

        if (empty($data['subject']) || empty($data['message'])) {
            return false;
        }

        $allRecipients = $data['allRecipients'] ?? [];
        if (empty($allRecipients)) {
            $allRecipients = array_merge(
                (array) ($data['toEmails'] ?? []),
                (array) ($data['contacts'] ?? []),
                (array) ($data['ccEmail'] ?? []),
                (array) ($data['bccEmail'] ?? [])
            );
        }

        $allRecipients = array_values(array_filter(array_map(static function ($recipient) {
            if (is_string($recipient)) {
                return trim($recipient);
            }
            return $recipient;
        }, (array) $allRecipients)));

        if (empty($allRecipients)) {
            return false;
        }

        try {
            $recipientNames = ContactNameMappingService::getRecipientNames($data['customer'], $allRecipients);

            $attachmentsData = [];
            $tempFiles = [];
            if ($data['attachments'] && count($data['attachments']) > 0) {
                $attachment = $this->renameDataAttachements($data['attachments']);
                $s3Files = collect($attachment)->toArray();

                $result = $this->s3Handler->prepareAttachmentsFromS3($s3Files);

                if (!$result['success']) {
                    return false;
                }

                $attachmentsData = $result['attachments'];
                $tempFiles = $result['temp_files'];
            }

            $successCount = 0;
            $failedCount = 0;
            $recipientReinsurerMap = $this->buildRecipientReinsurerMap($allRecipients);

            if ($data['replyToId']) {
                $rawMessage = $this->formatRawMessageForHtml($data['message']);

                $dataPayload = [
                    'subject'       => $data['subject'],
                    'priority'      => $data['priority'] ?? 'normal',
                    'category'      => $data['category'],
                    'reference'     => $data['reference'],
                    'attachments'   => $attachmentsData,
                    'tempFiles'     => $tempFiles,
                    'senderName'    => $authUser->name,
                    'senderEmail'   => $authUser->email,
                    'cc'            => $data['ccEmail'] ?? [],
                    'bcc'           => $data['bccEmail'] ?? [],
                    'replyToId'     => $data['replyToId'],
                    'replyMessage'  => $rawMessage,
                    'messageId'     => $data['messageId'],
                    'to'            => $allRecipients,
                    'conversationId' => $data['conversationId'],
                ];

                $jobId = $this->batchId . '-reply';
                SendOutlookEmailJob::dispatch($dataPayload, $authUser->id, $jobId);
                return true;
            }

            foreach ($allRecipients as $index => $recipient) {
                try {
                    $jobId = $this->batchId . '-' . ($index + 1);

                    if (is_array($recipient)) {
                        $recipientEmail = $recipient;
                        $recipientKey = implode(',', $recipient);
                    } elseif (is_string($recipient)) {
                        $recipientEmail = strpos($recipient, ',') !== false
                            ? array_map('trim', explode(',', $recipient))
                            : [$recipient];
                        $recipientKey = $recipient;
                    } else {
                        $recipientEmail = [$recipient];
                        $recipientKey = $recipient;
                    }

                    $recipientAttachments = $this->filterAttachmentsForRecipient(
                        $attachmentsData,
                        $recipientEmail,
                        $recipientReinsurerMap
                    );

                    $recipientName = $recipientNames[$recipientKey] ?? 'Sir/Madam';
                    $personalizedMessage = $this->formatMessageForHtml($data['message'], $recipientName, $authUser->name);

                    $dataPayload = [
                        'subject'       => $data['subject'],
                        'message'       => $personalizedMessage,
                        'priority'      => $data['priority'] ?? 'normal',
                        'category'      => $data['category'],
                        'reference'     => $data['reference'],
                        'attachments'   => $recipientAttachments,
                        'tempFiles'     => $tempFiles,
                        'senderName'    => $authUser->name,
                        'senderEmail'   => $authUser->email,
                        'recipientName' => $recipientName,
                        'to'            => $recipientEmail,
                        'cc'            => $data['ccEmail'] ?? [],
                        'bcc'           => $data['bccEmail'] ?? [],
                    ];

                    SendOutlookEmailJob::dispatch($dataPayload, $authUser->id, $jobId);
                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                }
            }

            return $successCount > 0;
        } catch (\Exception $e) {
            if (!empty($tempFiles)) {
                $this->s3Handler->cleanupTempFiles($tempFiles);
            }
            return false;
        }
    }

    private function renameDataAttachements($files)
    {
        return collect($files)->map(function ($file) {
            $isArray = is_array($file);
            $originalName = trim((string) data_get($file, 'original_name', ''));

            if ($originalName === '') {
                return $file;
            }

            $extension = pathinfo((string) data_get($file, 'file', ''), PATHINFO_EXTENSION);
            if ($extension === '') {
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            }
            $extension = $extension !== '' ? strtolower($extension) : 'pdf';

            $normalizedOriginal = Str::lower($originalName);
            if (Str::contains($normalizedOriginal, 'facultative placement slip')) {
                $baseName = 'Facultative Placement Slip';
            } elseif (Str::contains($normalizedOriginal, 'quotation placement slip')) {
                $baseName = 'Quotation Placement Slip';
            } else {
                $parts = preg_split('/\s*-\s*/', $originalName);
                $baseName = trim((string) ($parts[0] ?? $originalName));
                if ($baseName === '') {
                    $baseName = $originalName;
                }
            }

            $newFileName = $baseName . '.' . $extension;

            if ($isArray) {
                $file['file'] = $newFileName;
                $file['original_name'] = $baseName;
                return $file;
            }

            $file->file = $newFileName;
            $file->original_name = $baseName;
            return $file;
        })->values()->all();
    }

    private function buildRecipientReinsurerMap(array $recipients): array
    {
        $emails = collect($recipients)
            ->flatMap(function ($recipient) {
                if (is_array($recipient)) {
                    return $recipient;
                }
                return [$recipient];
            })
            ->filter(fn($email) => is_string($email) && trim($email) !== '')
            ->map(fn($email) => strtolower(trim($email)))
            ->unique()
            ->values()
            ->all();

        if (empty($emails)) {
            return [];
        }

        $customerEmailMap = [];
        DB::table('customers')
            ->whereIn(DB::raw('LOWER(email)'), $emails)
            ->get(['email', 'customer_id'])
            ->each(function ($row) use (&$customerEmailMap) {
                $email = strtolower(trim((string) $row->email));
                if ($email === '') {
                    return;
                }

                $customerId = (int) $row->customer_id;
                if ($customerId <= 0) {
                    return;
                }

                $customerEmailMap[$email][] = $customerId;
            });

        $contactEmailMap = [];
        DB::table('customer_contacts')
            ->whereIn(DB::raw('LOWER(contact_email)'), $emails)
            ->get(['contact_email', 'customer_id'])
            ->each(function ($row) use (&$contactEmailMap) {
                $email = strtolower(trim((string) $row->contact_email));
                if ($email === '') {
                    return;
                }

                $customerId = (int) $row->customer_id;
                if ($customerId <= 0) {
                    return;
                }

                $contactEmailMap[$email][] = $customerId;
            });

        $map = [];
        foreach ($emails as $email) {
            $ids = $contactEmailMap[$email] ?? $customerEmailMap[$email] ?? [];
            $map[$email] = array_values(array_unique(array_map('intval', $ids)));
        }

        return $map;
    }

    private function filterAttachmentsForRecipient(array $attachments, array $recipientEmails, array $recipientReinsurerMap): array
    {
        $recipientIds = collect($recipientEmails)
            ->filter(fn($email) => is_string($email) && trim($email) !== '')
            ->map(fn($email) => strtolower(trim($email)))
            ->flatMap(fn($email) => $recipientReinsurerMap[$email] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        return array_values(array_filter($attachments, function ($attachment) use ($recipientIds) {
            $attachmentReinsurerId = data_get($attachment, 'reinsurer_id');
            if ($attachmentReinsurerId === null || $attachmentReinsurerId === '') {
                return true;
            }

            if (empty($recipientIds)) {
                return false;
            }

            return in_array((int) $attachmentReinsurerId, $recipientIds, true);
        }));
    }

    private function resolveSender(?int $userId = null): ?User
    {
        if ($userId !== null && $userId > 0) {
            return User::find($userId);
        }

        return $this->auth instanceof User ? $this->auth : null;
    }

    private function formatMessageForHtml($message, $recipientName = 'Sir/Madam', ?string $senderName = null)
    {
        $personalizedMessage = str_replace(
            ['{recipient_name}', '{recipient}'],
            $recipientName,
            $message
        );

        $html = nl2br(htmlspecialchars($personalizedMessage, ENT_QUOTES, 'UTF-8'));

        $html = str_replace("\n\n", "</p><p>", $html);
        $html = "<p>" . $html . "</p>";

        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        $html = preg_replace('/<p>(\d+\..*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ol>$1</ol>', $html);

        $html = preg_replace('/<p>[-•*]\s*(.*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>(?:(?!<ol>).)*<\/li>)/s', '<ul>$1</ul>', $html);

        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        if (!preg_match('/^<p>\s*(Dear|Hello|Hi|Greetings)/i', $html)) {
            $greeting = ContactNameMappingService::getAppropriateGreeting($recipientName);
            $html = "<p>" . $greeting . "</p>" . $html;
        }

        if (!preg_match('/(Best regards|Sincerely|Kind regards|Yours faithfully)/i', $html)) {
            $sender = $senderName ?: ($this->auth?->name ?? 'System');
            $html .= "<p>Best regards,<br>" . $sender . "</p>";
        }

        return $html;
    }

    private function formatRawMessageForHtml($message)
    {
        $html = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

        $html = str_replace("\n\n", "</p><p>", $html);

        $html = "<p>" . $html . "</p>";

        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        $html = preg_replace('/<p>(\d+\..*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ol>$1</ol>', $html);

        $html = preg_replace('/<p>[-•*]\s*(.*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>(?:(?!<ol>).)*<\/li>)/s', '<ul>$1</ul>', $html);

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

    public function getNewEmail()
    {
        try {
            $this->getEmails();

            return response()->json([
                'success' => true,
                'message' => 'Outlook sync queued successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to queue outlook sync',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getSinceDateForUser(int $userId, ?string $folder)
    {
        if ($userId) {
            $lastSync = DB::table('email_sync_logs')
                ->where('user_id', $userId)
                ->where('folder', $folder)
                ->where('status', 'success')
                ->orderBy('completed_at', 'desc')
                ->value('completed_at');

            if ($lastSync) {
                $lastSyncCarbon = Carbon::parse($lastSync)->setTimezone(config('app.timezone'));
                $sinceDate = $lastSyncCarbon->subHours(1)->utc()->toDateTimeString();

                return $sinceDate;
            }

            $daysBack = (int) 30;
            $sinceDate = now()->utc()->subDays($daysBack)->toDateTimeString();

            return $sinceDate;
        }
    }

    private function handleUserSyncError(object $user, Exception $e, ?int $syncLogId): void {}

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
