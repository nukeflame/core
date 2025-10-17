<?php

namespace App\Jobs;

use App\Events\EmailSyncCompleted;
use App\Events\EmailSyncFailed;
use App\Events\EmailSyncProgress;
use App\Models\EmailSyncState;
use App\Models\User;
use App\Services\OutlookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
// use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
// use Mews\Purifier\Facades\Purifier;

class SyncUserEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    protected int $userId;
    protected int $totalProcessed = 0;
    protected int $totalInserted = 0;
    protected int $totalUpdated = 0;
    protected int $totalDeleted = 0;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function handle(OutlookService $graphService)
    {
        $syncState = EmailSyncState::firstOrCreate(
            ['user_id' => $this->userId],
            ['sync_attempts' => 0]
        );

        if ($syncState->is_syncing) {
            return;
        }

        try {
            $syncState->update([
                'is_syncing' => true,
                'sync_attempts' => $syncState->sync_attempts + 1,
                'last_attempt_at' => now()
            ]);

            $this->broadcastProgress('Started', 0, 0);

            $user = User::find($this->userId);
            if (!$user) {
                throw new \Exception("User not found: {$this->userId}");
            }

            $this->syncMessagesWithPagination($graphService, $user, $syncState);

            // Mark sync as complete
            $syncState->update([
                'is_syncing' => false,
                'last_synced_at' => now(),
                'sync_attempts' => 0,
                'last_error' => null
            ]);

            $this->broadcastCompletion();


            // Renew subscription if needed
            // if ($syncState->needsSubscriptionRenewal()) {
            //     // if ($syncState->subscription_id) {
            //     //     $subscription = $graphService->renewSubscription(
            //     //         $syncState->subscription_id,
            //     //         $user
            //     //     );
            //     // } else {
            //     $subscription = $graphService->createSubscription($user);
            //     // }

            //     // logger()->debug(['subscription' => $subscription]);


            //     // $syncState->update([
            //     //     'subscription_id' => $subscription['subscription_id'],
            //     //     'subscription_expires_at' => Carbon::parse($subscription['expiration_date'])
            //     // ]);
            // }

            // $result = $graphService->getDeltaMessages($user, $syncState->delta_token);

            // $this->processMessages($result['value'] ?? [], $user);

            // $deltaLink = $result['@odata.deltaLink'] ?? null;
            // if ($deltaLink) {
            //     $syncState->update([
            //         'delta_token' => $deltaLink,
            //         'last_synced_at' => now(),
            //         'sync_attempts' => 0,
            //         'last_error' => null
            //     ]);
            // }
        } catch (\Exception $e) {
            $syncState->update([
                'is_syncing' => false,
                'last_error' => $e->getMessage()
            ]);

            $this->broadcastFailure($e->getMessage());

            throw $e;
        }
    }

    protected function syncMessagesWithPagination(
        OutlookService $graphService,
        User $user,
        EmailSyncState $syncState
    ): void {
        $deltaToken = $syncState->delta_token;
        $totalMessages = 0;
        $batchNumber = 0;

        do {
            $batchNumber++;
            $result = $graphService->getDeltaMessages($user, $deltaToken);

            $messages = $result['value'] ?? [];
            $messageCount = count($messages);
            $totalMessages += $messageCount;

            if ($messageCount > 0) {
                $this->processMessagesInBatches($messages, $user);

                $this->broadcastProgress(
                    'Processing',
                    $this->totalProcessed,
                    $totalMessages,
                    $batchNumber
                );
            }

            $nextLink = $result['@odata.nextLink'] ?? null;
            $deltaLink = $result['@odata.deltaLink'] ?? null;

            if ($deltaLink) {
                $syncState->update([
                    'delta_token' => $deltaLink
                ]);
                break;
            }

            if ($nextLink) {
                parse_str(parse_url($nextLink, PHP_URL_QUERY), $params);
                $deltaToken = $params['$skiptoken'] ?? $params['$deltatoken'] ?? null;
            }
        } while ($nextLink);
    }

    protected function processMessagesInBatches(array $messages, User $user): void
    {
        $batchSize = 50;
        $chunks = array_chunk($messages, $batchSize);

        foreach ($chunks as $chunk) {
            DB::transaction(function () use ($chunk, $user) {
                $this->processBatch($chunk, $user);
            });
        }
    }

    protected function processBatch(array $messages, User $user): void
    {
        $toInsert = [];
        $deletedCount = 0;

        foreach ($messages as $message) {
            $this->totalProcessed++;

            if (isset($message['@removed'])) {
                $deleted = DB::table('fetched_emails')
                    ->where('user_id', $user->id)
                    ->where('uid', $message['id'])
                    ->delete();

                if ($deleted) {
                    $this->totalDeleted++;
                    $deletedCount++;
                }
                continue;
            }

            if (!isset($message['id'])) {
                logger()->warning('Message missing ID', ['user_id' => $user->id]);
                continue;
            }

            $toInsert[] = $this->prepareEmailData($message, $user);
        }

        if (!empty($toInsert)) {
            $existingIds = DB::table('fetched_emails')
                ->where('user_id', $user->id)
                ->whereIn('uid', array_column($toInsert, 'uid'))
                ->pluck('uid')
                ->toArray();

            $insertedCount = count(array_diff(array_column($toInsert, 'uid'), $existingIds));
            $updatedCount = count($existingIds);

            $this->totalInserted += $insertedCount;
            $this->totalUpdated += $updatedCount;

            DB::table('fetched_emails')->upsert(
                $toInsert,
                ['user_id', 'uid'],
                [
                    'user_email',
                    'subject',
                    'body_preview',
                    'updated_at',
                    'date_received',
                    'from_email',
                    'from_name',
                    'is_read',
                    'has_attachments',
                    'to_recipients',
                    'cc_recipients',
                    'bcc_recipients',
                    'date_sent',
                    'body_text',
                    'body_html',
                    'folder',
                    'importance'
                ]
            );
        }
    }

    protected function prepareEmailData(array $message, User $user): array
    {
        $toRecipients = $this->transformRecipients($message['toRecipients'] ?? []);
        $ccRecipients = $this->transformRecipients($message['ccRecipients'] ?? []);
        $bccRecipients = $this->transformRecipients($message['bccRecipients'] ?? []);

        $fromAddress = data_get($message, 'from.emailAddress.address');
        $fromName = data_get($message, 'from.emailAddress.name');

        $receivedAt = $this->parseDateTime($message['receivedDateTime'] ?? null);
        $sentDateTime = $this->parseDateTime($message['sentDateTime'] ?? null);

        $bodyContent = $message['body']['content'] ?? '';
        $folderName = Str::lower($message['parentFolder']['name']) ?? 'inbox';

        return [
            'user_id'         => $user->id,
            'uid'             => $message['id'],
            'message_id'      => $message['id'],
            'user_email'      => $user->email,
            'subject'         => $this->sanitizeText($message['subject'] ?? null),
            'body_preview'    => $this->sanitizeText($message['bodyPreview'] ?? null),
            'date_received'   => $receivedAt,
            'conversation_id' => $message['conversationId'],
            'from_email'      => $this->sanitizeEmail($fromAddress),
            'from_name'       => $this->sanitizeText($fromName),
            'is_read'         => (bool) ($message['isRead'] ?? false),
            'has_attachments' => (bool) ($message['hasAttachments'] ?? false),
            'to_recipients'   => $toRecipients,
            'cc_recipients'   => $ccRecipients,
            'bcc_recipients'  => $bccRecipients,
            'date_sent'       => $sentDateTime,
            'body_text'       => $this->stripAndTruncateHtml($bodyContent),
            'body_html'       => $this->sanitizeHtml($bodyContent),
            'importance'      => $message['importance'] ?? null,
            'folder'          => $folderName,
            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }

    protected function parseDateTime(?string $dateTime): ?Carbon
    {
        if (empty($dateTime)) {
            return null;
        }

        try {
            return Carbon::parse($dateTime);
        } catch (\Exception $e) {
            logger()->warning('Failed to parse datetime', [
                'user_id' => $this->userId,
                'datetime' => $dateTime,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    protected function transformRecipients(array $recipients): string
    {
        if (empty($recipients)) {
            return json_encode([]);
        }

        $transformed = collect($recipients)
            ->map(fn($recipient) => [
                'email' => $this->sanitizeEmail(data_get($recipient, 'emailAddress.address')),
                'name' => $this->sanitizeText(data_get($recipient, 'emailAddress.name')),
            ])
            ->filter(fn($recipient) => !empty($recipient['email']))
            ->values()
            ->toArray();

        return json_encode($transformed);
    }

    protected function sanitizeText(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        return trim(strip_tags($text));
    }

    protected function sanitizeEmail(?string $email): ?string
    {
        if (!$email) {
            return null;
        }

        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    protected function sanitizeHtml(?string $html): ?string
    {
        if (!$html) {
            return null;
        }

        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $html);
        $html = preg_replace('/on\w+\s*=\s*["\'].*?["\']/i', '', $html);

        return $html;
    }

    protected function stripAndTruncateHtml(?string $html, int $maxLength = 10000): ?string
    {
        if (!$html) {
            return null;
        }

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        return mb_substr($text, 0, $maxLength);
    }

    protected function broadcastProgress(
        string $status,
        int $processed,
        int $total,
        int $batchNumber = 0
    ): void {
        broadcast(new EmailSyncProgress(
            $this->userId,
            $status,
            $processed,
            $total,
            $this->totalInserted,
            $this->totalUpdated,
            $this->totalDeleted,
            $batchNumber
        ));
    }

    protected function broadcastCompletion(): void
    {
        broadcast(new EmailSyncCompleted(
            $this->userId,
            $this->totalProcessed,
            $this->totalInserted,
            $this->totalUpdated,
            $this->totalDeleted
        ));
    }

    protected function broadcastFailure(string $error): void
    {
        broadcast(new EmailSyncFailed(
            $this->userId,
            $error
        ));
    }

    public function failed(\Throwable $exception)
    {
        EmailSyncState::where('user_id', $this->userId)
            ->update(['is_syncing' => false]);

        $this->broadcastFailure($exception->getMessage());

        logger()->error('Email sync job failed permanently', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
