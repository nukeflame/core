<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Simplified service for linking only inline images with email attachments
 * Focuses solely on converting cid: references to proper URLs
 */
class EmailImageLinker
{
    private Command $command;
    private string $accessToken;
    private object $currentUser;
    private string $storageDisk;

    public function __construct(Command $command, string $accessToken, object $currentUser, string $storageDisk = 'local')
    {
        $this->command = $command;
        $this->accessToken = $accessToken;
        $this->currentUser = $currentUser;
        $this->storageDisk = $storageDisk;
    }

    /**
     * Process email and link only inline images with attachments
     */
    public function processEmailImages(array $email): array
    {
        if (empty($email['body_content'])) {
            return $email;
        }

        if (!$this->hasCidReferences($email['body_content'])) {
            if ($this->command->option('debug')) {
                $this->command->info("📧 No cid: references found in email {$email['id']}, skipping image processing");
            }
            return $email;
        }

        try {
            if ($this->command->option('debug')) {
                $this->command->info("🖼️ Processing inline images for email: " . ($email['subject'] ?? 'No Subject'));
            }

            $attachments = $this->fetchInlineAttachmentDetails($email['id']);

            if (empty($attachments)) {
                if ($this->command->option('debug')) {
                    $this->command->info("📎 No inline attachments found for email {$email['id']}");
                }
                return $email;
            }

            $email['body_content'] = $this->replaceInlineImages($email['body_content'], $attachments, $email['id']);
            $email['inline_images'] = $this->getInlineImageAttachments($attachments);

            if ($this->command->option('debug')) {
                $imageCount = count($email['inline_images']);
                $this->command->info("✅ Processed {$imageCount} inline images for email {$email['id']}");
            }
        } catch (Exception $e) {
            if ($this->command->option('debug')) {
                $this->command->warn("❌ Failed to process images for email {$email['id']}: " . $e->getMessage());
            }
        }

        return $email;
    }

    /**
     * Check if HTML contains cid: references
     */
    private function hasCidReferences(string $html): bool
    {
        return preg_match('/src=["\']?cid:/i', $html);
    }

    /**
     * Fetch only inline attachment details
     */
    private function fetchInlineAttachmentDetails(string $messageId): array
    {
        $url = "https://graph.microsoft.com/v1.0/me/messages/{$messageId}/attachments";

        $response = Http::withToken($this->accessToken)
            ->timeout(30)
            ->get($url, [
                '$filter' => 'isInline eq true'
            ]);

        if (!$response->successful()) {
            throw new Exception('Failed to fetch inline attachments: HTTP ' . $response->status());
        }

        $attachments = $response->json()['value'] ?? [];

        if ($this->command->option('debug')) {
            $this->command->info("📎 Fetched " . count($attachments) . " inline attachments");
            foreach ($attachments as $attachment) {
                $contentId = $attachment['contentId'] ?? 'none';
                $this->command->info("  - {$attachment['name']} (CID: {$contentId})");
            }
        }

        return $attachments;
    }

    /**
     * Replace cid: references with proper URLs in HTML
     */
    private function replaceInlineImages(string $html, array $attachments, string $messageId): string
    {
        $cidMap = $this->buildContentIdMap($attachments);

        if (empty($cidMap)) {
            if ($this->command->option('debug')) {
                $this->command->warn("⚠️ No content IDs found in attachments");
            }
            return $html;
        }

        $pattern = '/(<img[^>]*src=["\']?)cid:([^"\'>\s]+)(["\']?[^>]*>)/i';

        $processedHtml = preg_replace_callback($pattern, function ($matches) use ($cidMap, $messageId) {
            $fullImgTag = $matches[0];
            $beforeSrc = $matches[1];
            $contentId = $matches[2];
            $afterSrc = $matches[3];

            $cleanContentId = trim($contentId, '<>');

            if (isset($cidMap[$cleanContentId])) {
                $attachment = $cidMap[$cleanContentId];

                $imageUrl = $this->generateImageUrl($messageId, $attachment['id'], $attachment['name']);

                $dataAttributes = sprintf(
                    ' data-attachment-id="%s" data-content-id="%s" data-original-name="%s"',
                    htmlspecialchars($attachment['id']),
                    htmlspecialchars($cleanContentId),
                    htmlspecialchars($attachment['name'])
                );

                if ($this->command->option('debug')) {
                    $this->command->info("🔄 Replaced: cid:{$contentId} -> {$imageUrl}");
                }

                $newAfterSrc = str_replace('>', $dataAttributes . '>', $afterSrc);

                return $beforeSrc . $imageUrl . $newAfterSrc;
            }

            if ($this->command->option('debug')) {
                $this->command->warn("❓ Unmatched content ID: cid:{$contentId}");
                $this->command->info("Available CIDs: " . implode(', ', array_keys($cidMap)));
            }

            return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==";
        }, $html);

        return $processedHtml;
    }

    /**
     * Build Content-ID to attachment mapping
     */
    private function buildContentIdMap(array $attachments): array
    {
        $map = [];
        foreach ($attachments as $attachment) {
            if (!empty($attachment['contentId'])) {

                $contentId = trim($attachment['contentId'], '<>');
                $map[$contentId] = $attachment;

                if ($this->command->option('debug')) {
                    $this->command->info("🗂️ Mapped CID: {$contentId} -> {$attachment['name']}");
                }
            }
        }
        return $map;
    }

    /**
     * Generate URL for inline image
     */
    private function generateImageUrl(string $messageId, string $attachmentId, string $filename): string
    {
        if ($this->command->option('debug')) {
            $this->command->info("🔗 Generated URL for: {$filename}");
        }

        // &attachmentId={$attachmentId}

        $baseUrl = config('app.url');
        $route = "/outlook/images/{$messageId}";
        $safeName = urlencode($filename);
        $url = "{$baseUrl}{$route}?filename={$safeName}";

        return $url;
    }

    /**
     * Get inline image attachments metadata
     */
    private function getInlineImageAttachments(array $attachments): array
    {
        return array_map(function ($attachment) {
            return [
                'id' => $attachment['id'] ?? null,
                'name' => $attachment['name'] ?? 'unknown',
                'content_type' => $attachment['contentType'] ?? 'application/octet-stream',
                'size' => $attachment['size'] ?? 0,
                'content_id' => trim($attachment['contentId'] ?? '', '<>'),
                'formatted_size' => $this->formatFileSize($attachment['size'] ?? 0),
                'is_image' => $this->isImageContentType($attachment['contentType'] ?? ''),
            ];
        }, $attachments);
    }

    /**
     * Check if content type is an image
     */
    private function isImageContentType(string $contentType): bool
    {
        return str_starts_with(strtolower($contentType), 'image/');
    }

    /**
     * Format file size for display
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}

/**
 * Simplified trait for image-only processing
 */
trait EmailImageLinkingTrait
{
    /**
     * Process emails with image linking only
     */
    private function processEmailsWithImageLinking(array $rawEmails): array
    {
        $emails = [];
        $imageLinker = new EmailImageLinker($this, $this->accessToken, $this->currentUser);

        if ($this->option('debug')) {
            $this->info("🖼️ Processing " . count($rawEmails) . " emails for inline image linking...");
        }

        foreach ($rawEmails as $index => $rawEmail) {
            try {
                if ($this->option('debug')) {
                    $this->info("Processing email " . ($index + 1) . "/" . count($rawEmails) . ": " . ($rawEmail['subject'] ?? 'No Subject'));
                }

                $x = $this->transformEmailData($rawEmail);
                $email = $imageLinker->processEmailImages($x);

                $emails[] = $email;
            } catch (Exception $e) {
                if ($this->option('debug')) {
                    $this->warn("❌ Failed to process email " . ($index + 1) . ": " . $e->getMessage());
                }

                try {
                    $emails[] = $this->transformEmailData($rawEmail);
                } catch (Exception $fallbackError) {
                    if ($this->option('debug')) {
                        $this->error("❌ Complete failure for email " . ($index + 1) . ": " . $fallbackError->getMessage());
                    }
                }
            }
        }

        if ($this->option('debug')) {
            $this->info("✅ Completed processing. Successfully processed " . count($emails) . " emails");
        }

        return $emails;
    }

    private function transformEmailData(array $rawEmail): array
    {
        if ($this->option('debug') && !isset($rawEmail['id'])) {
            $this->warn("Email missing ID field: " . json_encode($rawEmail, JSON_PRETTY_PRINT));
        }

        return [
            'id' => $rawEmail['id'] ?? throw new Exception('Email missing required ID field'),
            'subject' => $rawEmail['subject'] ?? '[No Subject]',
            'from' => $this->extractEmailAddress($rawEmail['from'] ?? null),
            'from_name' => $this->extractDisplayName($rawEmail['from'] ?? null),
            'to' => $this->extractRecipients($rawEmail['toRecipients'] ?? []),
            'cc' => $this->extractRecipients($rawEmail['ccRecipients'] ?? []),
            'bcc' => $this->extractRecipients($rawEmail['bccRecipients'] ?? []),
            'date_received' => $this->formatDateTime($rawEmail['receivedDateTime'] ?? null),
            'date_sent' => $this->formatDateTime($rawEmail['sentDateTime'] ?? null),
            'body_preview' => $rawEmail['bodyPreview'] ?? '',
            'body_content' => $this->extractBodyContent($rawEmail['body'] ?? null),
            'importance' => $rawEmail['importance'] ?? 'normal',
            'is_read' => $rawEmail['isRead'] ?? false,
            'has_attachments' => $rawEmail['hasAttachments'] ?? false,
            'message_id' => $rawEmail['internetMessageId'] ?? null,
            'conversation_id' => $rawEmail['conversationId'] ?? null,
            'categories' => $rawEmail['categories'] ?? [],
            'attachments' => [],
            'profile_picture' => null,
        ];
    }

    /**
     * Enhanced save method for emails with image links
     */
    private function saveEmailWithImageLinks(array $email): array
    {
        $result = $this->saveOrUpdateEmail($email);

        if (!empty($email['inline_images'])) {
            $this->saveInlineImageMetadata($email['message_id'], $email['inline_images']);
        }

        return $result;
    }

    /**
     * Save inline image metadata
     */
    private function saveInlineImageMetadata(string $messageId, array $inlineImages): void
    {
        foreach ($inlineImages as $image) {
            if (empty($image['content_id'])) {
                continue;
            }

            try {
                DB::table('email_attachments')->updateOrInsert(
                    [
                        'message_id' => $messageId,
                        'attachment_id' => $image['id'],
                        'user_id' => $this->currentUser->user_id,
                    ],
                    [
                        'name' => substr($image['name'], 0, 255),
                        'content_type' => $image['content_type'],
                        'size' => $image['size'],
                        'is_inline' => true,
                        'content_id' => $image['content_id'],
                        'user_email' => $this->currentUser->email,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                if ($this->option('debug')) {
                    $this->info("💾 Saved inline image: {$image['name']} (CID: {$image['content_id']})");
                }
            } catch (Exception $e) {
                if ($this->option('debug')) {
                    $this->warn("❌ Failed to save inline image metadata: " . $e->getMessage());
                }
            }
        }
    }

    private function extractBodyContent(?array $body): ?string
    {
        if (!$body) {
            return null;
        }

        return $body['content'] ?? null;
    }

    private function extractEmailAddress(?array $emailObject): ?string
    {
        if (!$emailObject || !isset($emailObject['emailAddress'])) {
            return null;
        }

        return $emailObject['emailAddress']['address'] ?? null;
    }

    private function extractDisplayName(?array $emailObject): ?string
    {
        if (!$emailObject || !isset($emailObject['emailAddress'])) {
            return null;
        }

        return $emailObject['emailAddress']['name'] ?? null;
    }

    private function extractRecipients(array $recipients): array
    {
        return array_map(function ($recipient) {
            return [
                'email' => $recipient['emailAddress']['address'] ?? null,
                'name' => $recipient['emailAddress']['name'] ?? null
            ];
        }, $recipients);
    }

    private function formatDateTime(?string $dateTime): ?string
    {
        if (!$dateTime) {
            return null;
        }

        try {
            return Carbon::parse($dateTime)->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            if ($this->option('debug')) {
                $this->warn("Failed to parse datetime '{$dateTime}': " . $e->getMessage());
            }
            return null;
        }
    }
}
