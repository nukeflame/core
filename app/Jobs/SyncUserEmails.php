<?php

namespace App\Jobs;


use App\Models\EmailSyncState;
use App\Models\User;
use App\Services\MicrosoftGraphService;
use App\Services\OutlookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SyncUserEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    protected int $userId;

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

        try {
            $syncState->update([
                'sync_attempts' => $syncState->sync_attempts + 1,
                'last_attempt_at' => now()
            ]);

            $user = User::find($this->userId);

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

            $result = $graphService->getDeltaMessages(
                $user,
                $syncState->delta_token
            );

            $this->processMessages($result['value'] ?? [], $user);

            $deltaLink = $result['@odata.deltaLink'] ?? null;
            if ($deltaLink) {
                $syncState->update([
                    'delta_token' => $deltaLink,
                    'last_synced_at' => now(),
                    'sync_attempts' => 0,
                    'last_error' => null
                ]);
            }
        } catch (\Exception $e) {
            $syncState->update([
                'last_error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function processMessages(array $messages, $user): void
    {
        $filtered = Arr::except($messages[0], ['bodyPreview', 'body']);
        // logger()->debug(json_encode($filtered, JSON_PRETTY_PRINT));

        foreach ($messages as $message) {
            if (isset($message['@removed'])) {
                DB::table('fetched_emails')
                    ->where('user_id', $user->id)
                    ->where('uid', $message['id'])
                    ->delete();

                continue;
            }

            $toRecipients = $this->transformRecipients($message['toRecipients'] ?? []);
            $ccRecipients = $this->transformRecipients($message['ccRecipients'] ?? []);
            $bccRecipients = $this->transformRecipients($message['bccRecipients'] ?? []);

            $fromAddress = data_get($message, 'from.emailAddress.address');
            $fromName = data_get($message, 'from.emailAddress.name');

            $receivedAt = null;
            if (!empty($message['receivedDateTime'])) {
                try {
                    $receivedAt = Carbon::parse($message['receivedDateTime']);
                } catch (\Exception $e) {
                    logger()->warning('Invalid receivedDateTime', [
                        'uid' => $message['id'],
                        'value' => $message['receivedDateTime']
                    ]);
                }
            }
            $sentDateTime = null;
            if (!empty($message['sentDateTime'])) {
                try {
                    $sentDateTime = Carbon::parse($message['sentDateTime']);
                } catch (\Exception $e) {
                    logger()->warning('Invalid sentDateTime', [
                        'uid' => $message['id'],
                        'value' => $message['sentDateTime']
                    ]);
                }
            }

            DB::table('fetched_emails')->upsert(
                [[
                    'user_id'      => $this->userId,
                    'uid'          => $message['id'],
                    'user_email'   => $user->email,
                    'subject'      => $message['subject'] ?? null,
                    'body_preview' => $message['bodyPreview'] ?? null,
                    'updated_at'   => now(),
                ]],
                ['user_id', 'uid'], // Unique constraint columns
                [
                    'user_email',
                    'subject',
                    'body_preview',
                    'updated_at'
                ]
            );

            // DB::table('fetched_emails')->upsert(
            //     [[
            //         'user_id'         => $this->userId,
            //         'uid'             => $message['id'],
            //         'user_email'      => $user->email,
            //         'subject'         => $message['subject'] ?? null,
            //         'body_preview'    => $message['bodyPreview'] ?? null,
            //         // 'from_email'      => $fromAddress,
            //         // 'from_name'       => $fromName,
            //         // 'is_read'         => (bool) ($message['isRead'] ?? false),
            //         // 'has_attachments' => (bool) ($message['hasAttachments'] ?? false),
            //         // 'date_received'   => $receivedAt,
            //         // 'to_recipients'   => $toRecipients,
            //         // 'cc_recipients'   => $ccRecipients,
            //         // 'bcc_recipients'  => $bccRecipients,
            //         // 'date_sent'       => $sentDateTime,
            //         // // 'body_text'       => $this->stripAndTruncateHtml($message['body'] ?? ''),
            //         // 'body_html'       => $message['body'],
            //         // 'importance'      => $message['importance'] ?? null,
            //         // 'conversation_id' => $message['conversationId'] ?? null,
            //         // 'categories'      => json_encode($message['categories'] ?? []),
            //         // 'folder'          => $message['parentFolder']['displayName'] ?? 'Unknown',
            //         'updated_at'      => now(),
            //     ]],
            //     ['user_id', 'uid'],
            //     [
            //         'user_email',
            //         'subject',
            //         'body_preview',
            //         // 'from_email',
            //         // 'from_name',
            //         // 'is_read',
            //         // 'has_attachments',
            //         // 'date_received',
            //         // 'to_recipients',
            //         // 'cc_recipients',
            //         // 'bcc_recipients',
            //         // 'date_sent',
            //         // 'body_text',
            //         // 'body_html',
            //         // 'importance',
            //         // 'conversation_id',
            //         // 'categories',
            //         // 'folder',
            //         'updated_at'
            //     ]
            // );
        }
    }

    protected function transformRecipients(array $recipients): string
    {
        if (empty($recipients)) {
            return json_encode([]);
        }

        $transformed = collect($recipients)
            ->map(fn($recipient) => [
                'email' => data_get($recipient, 'emailAddress.address'),
                'name' => data_get($recipient, 'emailAddress.name'),
            ])
            ->filter(fn($recipient) => !empty($recipient['email']))
            ->values()
            ->toArray();

        return json_encode($transformed);
    }

    private function stripAndTruncateHtml(?string $html, int $maxLength = 10000): ?string
    {
        if (!$html) {
            return null;
        }

        $text = strip_tags($html);
        return substr($text, 0, $maxLength);
    }

    public function failed(\Throwable $exception)
    {
        logger()->error('Email sync job failed permanently', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);
    }
}
