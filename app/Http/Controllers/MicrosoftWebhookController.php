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
        if ($request->query('validationToken')) {
            $token = $request->query('validationToken');
            return response($token, 200)->header('Content-Type', 'text/plain');
        }



        $data = $request->all();

        if (isset($data['value']) && is_array($data['value'])) {
            foreach ($data['value'] as $notification) {
                $expectedClientState = config('services.azure.client_state');

                if (($notification['clientState'] ?? '') !== $expectedClientState) {
                    continue;
                }
            }
        }

        return response()->json(['status' => 'ok'], 200);

        // if ($request->query('validationToken')) {
        //     $token = $request->query('validationToken');
        //     return response($token, 200)->header('Content-Type', 'text/plain');
        // }

        // // Parse notification data
        // try {
        //     $data = $request->all();

        //     // Check for 'value' array (standard Graph notification format)
        //     if (isset($data['value']) && is_array($data['value'])) {
        //         foreach ($data['value'] as $notification) {
        //         }
        //     }

        //     return response()->json(['status' => 'ok'], 200);
        // } catch (\Exception $e) {
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

        // foreach ($notifications as $notification) {
        //     // if ($notification['clientState'] !== config('services.azure.webhook_client_state')) {
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

            foreach ($notifications as $notification) {
                // Verify client state
                if (
                    isset($notification['clientState']) &&
                    $notification['clientState'] !== $clientState
                ) {
                    continue;
                }

                // Process notification
                $this->processNotification($notification);
            }

            // Microsoft expects 202 Accepted
            return response()->json(['status' => 'accepted'], 202);
        } catch (\Exception $e) {
            // Still return 202 to prevent retries
            return response()->json(['status' => 'error'], 202);
        }
    }

    /**
     * Process individual notification
     */
    private function processNotification(array $notification)
    {
        // Store in database, trigger events, etc.
    }

    /**
     * Handle incoming webhook notifications
     */
    // public function handleNotification(Request $request)
    // {
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
    //     //     return response()->json(['status' => 'no_content'], 200);
    //     // }

    //     // // foreach ($notifications as $notification) {
    //     // //     try {
    //     // //         $this->processNotification($notification, $request->ip());
    //     // //     } catch (\Exception $e) {
    //     // //     }
    //     // // }

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
