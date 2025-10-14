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

            // logger()->debug(['c' => $syncState->needsSubscriptionRenewal()]);

            // Renew subscription if needed
            if ($syncState->needsSubscriptionRenewal()) {
                if ($syncState->subscription_id) {
                    $subscription = $graphService->renewSubscription(
                        $syncState->subscription_id,
                        $user
                    );
                } else {
                    $subscription = $graphService->createSubscription($user);
                }

                // $syncState->update([
                //     'subscription_id' => $subscription['subscription_id'],
                //     'subscription_expires_at' => Carbon::parse($subscription['expiration_date'])
                // ]);
            }

            // // Fetch delta messages
            // $result = $graphService->getDeltaMessages(
            //     $this->userId,
            //     $syncState->delta_token
            // );

            // $this->processMessages($result['value'] ?? []);

            // // Store new delta token
            // $deltaLink = $result['@odata.deltaLink'] ?? null;
            // if ($deltaLink) {
            //     $syncState->update([
            //         'delta_token' => $deltaLink,
            //         'last_synced_at' => now(),
            //         'sync_attempts' => 0,
            //         'last_error' => null
            //     ]);
            // }

            // logger()->info('Email sync completed', [
            //     'user_id' => $this->userId,
            //     'messages_processed' => count($result['value'] ?? [])
            // ]);
        } catch (\Exception $e) {
            $syncState->update([
                'last_error' => $e->getMessage()
            ]);

            logger($e);

            throw $e;
        }
    }

    // protected function processMessages(array $messages): void
    // {
    //     foreach ($messages as $message) {
    //         // Handle deletions
    //         if (isset($message['@removed'])) {
    //             Email::where('message_id', $message['id'])->delete();
    //             continue;
    //         }

    //         // Upsert email
    //         Email::updateOrCreate(
    //             [
    //                 'user_id' => $this->userId,
    //                 'message_id' => $message['id']
    //             ],
    //             [
    //                 'subject' => $message['subject'] ?? null,
    //                 'body_preview' => $message['bodyPreview'] ?? null,
    //                 'from_address' => $message['from']['emailAddress']['address'] ?? null,
    //                 'from_name' => $message['from']['emailAddress']['name'] ?? null,
    //                 'is_read' => $message['isRead'] ?? false,
    //                 'has_attachments' => $message['hasAttachments'] ?? false,
    //                 'received_at' => isset($message['receivedDateTime'])
    //                     ? Carbon::parse($message['receivedDateTime'])
    //                     : null,
    //                 'metadata' => $message
    //             ]
    //         );
    //     }
    // }

    public function failed(\Throwable $exception)
    {
        logger()->error('Email sync job failed permanently', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);
    }
}
