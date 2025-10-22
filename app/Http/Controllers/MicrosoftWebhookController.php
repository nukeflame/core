<?php

namespace App\Http\Controllers;

use App\Jobs\SyncUserEmails;
use App\Models\EmailSyncState;
use App\Models\WebhookDelivery;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MicrosoftWebhookController extends Controller
{
    public function handleNotification(Request $request)
    {
        logger()->debug(json_encode('Connecting validation', JSON_PRETTY_PRINT));

        if ($request->query('validationToken')) {
            $token = $request->query('validationToken');
            logger()->debug($token);
            return response($token, 200)->header('Content-Type', 'text/plain');
        }



        $data = $request->all();

        if (isset($data['value']) && is_array($data['value'])) {
            foreach ($data['value'] as $notification) {
                $expectedClientState = config('services.azure.client_state');

                if (($notification['clientState'] ?? '') !== $expectedClientState) {
                    continue;
                }

                logger()->info('Valid notification received', [
                    'subscriptionId' => $notification['subscriptionId'],
                    'changeType' => $notification['changeType'],
                    'resource' => $notification['resource']
                ]);
            }
        }

        return response()->json(['status' => 'ok'], 200);

        // if ($request->query('validationToken')) {
        //     $token = $request->query('validationToken');
        //     logger()->info('Validation token received', ['token' => $token]);
        //     return response($token, 200)->header('Content-Type', 'text/plain');
        // }

        // // Parse notification data
        // try {
        //     $data = $request->all();
        //     logger()->info('Notification data', ['data' => $data]);

        //     // Check for 'value' array (standard Graph notification format)
        //     if (isset($data['value']) && is_array($data['value'])) {
        //         foreach ($data['value'] as $notification) {
        //             logger()->info('Processing notification', [
        //                 'subscriptionId' => $notification['subscriptionId'] ?? 'unknown',
        //                 'changeType' => $notification['changeType'] ?? 'unknown',
        //                 'resource' => $notification['resource'] ?? 'unknown',
        //                 'clientState' => $notification['clientState'] ?? 'none'
        //             ]);
        //         }
        //     }

        //     return response()->json(['status' => 'ok'], 200);
        // } catch (\Exception $e) {
        //     logger()->error('Webhook processing error', [
        //         'error' => $e->getMessage(),
        //         'trace' => $e->getTraceAsString()
        //     ]);

        //     // Still return 200 to acknowledge receipt
        //     return response()->json(['status' => 'error'], 200);
        // }
    }
    /**
     * Handle actual notifications
     */
    private function handleNotificationPost(Request $request)
    {
        // if ($request->has('validationToken')) {
        //     return response($request->input('validationToken'), 200)->header('Content-Type', 'text/plain');
        // }

        // $notifications = $request->input('value', []);

        // logger()->warning(json_encode($notifications, JSON_PRETTY_PRINT));

        // foreach ($notifications as $notification) {
        //     // if ($notification['clientState'] !== config('services.azure.webhook_client_state')) {
        //     //     logger()->warning('Invalid client state in webhook', [
        //     //         'notification' => $notification,
        //     //     ]);
        //     //     continue;
        //     // }

        //     // // Extract user from subscription or resource
        //     // $userId = $this->extractUserId($notification);

        //     // if ($userId) {
        //     //     SyncUserEmails::dispatch($userId);
        //     // }
        // }

        // if ($request->isMethod('post') && $request->has('validationToken')) {
        //     $validationToken = $request->query('validationToken');

        //     logger()->info('Webhook validation request received', [
        //         'token_length' => strlen($validationToken),
        //         'ip' => $request->ip()
        //     ]);

        //     return response($validationToken, 200)
        //         ->header('Content-Type', 'text/plain');
        // }

        // if ($request->isMethod('post')) {
        //     return $this->handleNotificationPost($request);
        // }

        // // Invalid request
        // return response()->json(['error' => 'Invalid request'], 400);

        try {
            $notifications = $request->input('value', []);
            $clientState = config('services.azure.webhook_client_state');

            logger()->info('Received Graph webhook notifications', [
                'count' => count($notifications)
            ]);

            foreach ($notifications as $notification) {
                // Verify client state
                if (
                    isset($notification['clientState']) &&
                    $notification['clientState'] !== $clientState
                ) {
                    logger()->warning('Invalid client state', [
                        'expected' => $clientState,
                        'received' => $notification['clientState']
                    ]);
                    continue;
                }

                // Process notification
                $this->processNotification($notification);
            }

            // Microsoft expects 202 Accepted
            return response()->json(['status' => 'accepted'], 202);
        } catch (\Exception $e) {
            logger()->error('Failed to process webhook', [
                'error' => $e->getMessage()
            ]);

            // Still return 202 to prevent retries
            return response()->json(['status' => 'error'], 202);
        }
    }

    /**
     * Process individual notification
     */
    private function processNotification(array $notification)
    {
        // Your notification processing logic here
        logger()->info('Processing notification', [
            'subscription_id' => $notification['subscriptionId'] ?? null,
            'resource' => $notification['resource'] ?? null,
            'change_type' => $notification['changeType'] ?? null
        ]);

        // Store in database, trigger events, etc.
    }

    /**
     * Handle incoming webhook notifications
     */
    // public function handleNotification(Request $request)
    // {
    //     logger()->debug(['$request->validationToken' => $request->has('validationToken')]);

    //     if ($request->has('validationToken')) {
    //         return response()->json($request->input('validationToken'), 200)
    //             ->header('Content-Type', 'text/plain');
    //     }

    //     if ($request->isMethod('post')) {
    //         return response()->json(['status' => 'accepted'], 202);
    //         // return $this->handleNotificationPost($request);
    //     }

    //     // Invalid request
    //     return response()->json(['error' => 'Invalid request'], 400);
    //     // // $notifications = $request->input('value', []);

    //     // if (empty($notifications)) {
    //     //     logger()->warning('Webhook received with no notifications');
    //     //     return response()->json(['status' => 'no_content'], 200);
    //     // }

    //     // logger()->info('Webhook notifications received', [
    //     //     'count' => count($notifications),
    //     //     'ip' => $request->ip()
    //     // ]);

    //     // // foreach ($notifications as $notification) {
    //     // //     try {
    //     // //         $this->processNotification($notification, $request->ip());
    //     // //     } catch (\Exception $e) {
    //     // //         logger()->error('Failed to process webhook notification', [
    //     // //             'notification' => $notification,
    //     // //             'error' => $e->getMessage()
    //     // //         ]);
    //     // //     }
    //     // // }

    //     // // logger()->debug(['webhook' => $request->all()]);

    //     // return response()->json(['status' => 'accepted'], 202);
    // }

    /**
     * Process individual notification
     */
    // private function processNotification(array $notification, ?string $sourceIp): void
    // {
    //     $isValid = ($notification['clientState'] ?? null) ===
    //         config('services.microsoft.webhook_client_state');

    //     if (!$isValid) {
    //         logger()->warning('Invalid client state in webhook', [
    //             'notification' => $notification,
    //             'ip' => $sourceIp
    //         ]);
    //     }

    //     $userId = $this->extractUserId($notification);

    //     // Log webhook delivery
    //     WebhookDelivery::create([
    //         'subscription_id' => $notification['subscriptionId'] ?? null,
    //         'user_id' => $userId,
    //         'change_type' => $notification['changeType'] ?? 'unknown',
    //         'resource' => $notification['resource'] ?? null,
    //         'client_state' => $notification['clientState'] ?? null,
    //         'is_valid' => $isValid,
    //         'is_processed' => false,
    //         'payload' => $notification,
    //         'source_ip' => $sourceIp
    //     ]);

    //     // Dispatch sync job if valid
    //     if ($isValid && $userId) {
    //         // SyncUserEmails::dispatch($userId, 'webhook')
    //         //     ->delay(now()->addSeconds(5)); // Small delay to batch changes

    //         logger()->info('Sync job dispatched from webhook', [
    //             'user_id' => $userId,
    //             'change_type' => $notification['changeType'] ?? 'unknown'
    //         ]);
    //     }
    // }

    /**
     * Extract user ID from notification
     */
    // private function extractUserId(array $notification): ?int
    // {
    //     $subscriptionId = $notification['subscriptionId'] ?? null;

    //     if (!$subscriptionId) {
    //         return null;
    //     }

    //     $syncState = EmailSyncState::where('subscription_id', $subscriptionId)->first();

    //     return $syncState?->user_id;
    // }
}
