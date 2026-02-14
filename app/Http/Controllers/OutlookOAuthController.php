<?php

namespace App\Http\Controllers;

use App\Jobs\SyncUserEmails;
use App\Models\EmailSyncState;
use App\Models\GraphSubscription;
use App\Services\OutlookService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class OutlookOAuthController extends Controller
{
    private $outlookService;

    public function __construct()
    {
        $this->outlookService = new OutlookService();
    }

    public function callback(Request $request)
    {
        try {
            if ($request->has('error')) {
                return $this->handleError($request);
            }

            if (!$request->has('code')) {
                return response()->json([
                    'error' => 'Missing authorization code',
                    'message' => 'No authorization code received from Microsoft'
                ], 400);
            }

            $code = $request->get('code');
            $state = $request->get('state');

            $pkceData = Redis::get("azure_auth_state_{$state}");
            if (!$pkceData) {
                throw new Exception('Invalid or expired state parameter');
            }

            $pkceInfo = json_decode($pkceData, true);
            $codeVerifier = $pkceInfo['code_verifier'];

            Redis::del("azure_auth_state_{$state}");

            $tokenData = $this->outlookService->getAccessToken($code, $codeVerifier);

            if (empty($tokenData)) {
                throw new Exception('Failed to exchange authorization code for tokens');
            }

            $user = $request->user();
            $this->outlookService->saveToken($user, $tokenData);

            $userProfile = $this->outlookService->getUserProfile($user);
            $cacheKey = "outlook_profile_{$userProfile['user']['email']}";
            Cache::forget($cacheKey);

            EmailSyncState::firstOrCreate(
                ['user_id' => $request->user()->id],
                ['status' => 'active']
            );

            $this->ensureRealtimeSubscription($user);

            SyncUserEmails::dispatch($request->user()->id, 'full')
                ->delay(now()->addSeconds(5));

            return redirect()->route('mail.index', ['outlook_connected' => 'true'])->with([
                'success' => 'Outlook connected successfully.',
                'connected' => true,
                'user' => $userProfile
            ]);
        } catch (Exception $e) {
            return redirect()->route('mail.index')->withErrors([
                'errors' => 'Failed to complete connection',
                'message' => 'Failed to process authentication callback',
                'details' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ]);
        }
    }

    private function handleError(Request $request)
    {
        $error = $request->get('error');
        $errorDescription = $request->get('error_description');

        $errorMessages = [
            'access_denied' => 'User denied access to the application',
            'invalid_request' => 'Invalid request parameters',
            'unauthorized_client' => 'Client not authorized',
            'unsupported_response_type' => 'Unsupported response type',
            'invalid_scope' => 'Invalid or unknown scope',
            'server_error' => 'Authorization server error',
            'temporarily_unavailable' => 'Authorization server temporarily unavailable'
        ];

        $message = $errorMessages[$error] ?? 'Unknown authentication error';

        return redirect()->back()->withErrors(
            [
                'errors' => $error,
                'message' => $message,
                'description' => $errorDescription
            ]
        );
    }

    public function connect(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $existingConnection = DB::table('oauth_tokens')
                ->where('email', $user->email)
                ->where('provider', 'outlook')
                ->first();

            if ($existingConnection && $this->outlookService->isTokenValid($user->email)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Already connected to Outlook',
                    'connected' => true
                ]);
            }

            $auth = $this->outlookService->getAuthUrl();

            return response()->json([
                'success' => true,
                'auth_url' => $auth['authUrl'],
                'message' => 'Redirect to Microsoft for authentication'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate connection. Please try again.',
                'error_code' => 'CONNECTION_INIT_FAILED'
            ], 500);
        }
    }

    public function status(): JsonResponse
    {
        try {
            $user = Auth::user();
            $this->outlookService->setAuthenticatedUser($user);

            if (!$user) {
                return response()->json([
                    'connected' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $connection = DB::table('oauth_tokens')
                ->where('email', $user->email)
                ->where('provider', 'outlook')
                ->first();

            if (!$connection) {
                return response()->json([
                    'connected' => false,
                    'message' => 'No Outlook connection found'
                ]);
            }

            if (!$this->outlookService->isTokenValid($user->email)) {
                $validToken = $this->outlookService->getValidToken();
                if (!$validToken) {
                    return response()->json([
                        'connected' => false,
                        'error' => 'Token expired and refresh failed',
                        'requires_reconnect' => true
                    ]);
                }
            }

            $profile = $this->outlookService->getUserProfile($user);
            // $profilePhoto = $this->outlookService->getUserPhoto($user);
            if ($profile) {
                $cacheKey = "outlook_profile_{$user->email}";
                $userProfile = Cache::remember($cacheKey, 900, function () use ($profile) {
                    return $profile;
                });

                if ($userProfile) {
                    return response()->json([
                        'connected' => true,
                        'email' => $userProfile['user']['email'] ?? null,
                        'displayName' => $userProfile['user']['name'] ?? null,
                        'last_updated' => $connection->updated_at
                    ]);
                }
            }

            return response()->json([
                'connected' => true,
                'email' => null,
                'displayName' => null,
                'last_updated' => $connection->updated_at
            ]);
        } catch (Exception $e) {
            return response()->json([
                'connected' => false,
                'error' => 'Failed to check connection status'
            ], 500);
        }
    }

    public function disconnect(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $deleted = DB::table('oauth_tokens')
                ->where('email', $user->email)
                ->where('provider', 'outlook')
                ->delete();

            if ($deleted) {
                $cacheKey = "outlook_profile_{$user->email}";
                Cache::forget($cacheKey);
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully disconnected from Outlook'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No connection found to disconnect'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to disconnect'
            ], 500);
        }
    }

    public function test(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $testResult = $this->outlookService->testConnection();

            return response()->json([
                'success' => $testResult['status'] === 'success',
                'data' => $testResult
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'string|in:delta,full'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->id;
        $syncType = $request->input('type', 'delta');

        $syncState = EmailSyncState::where('user_id', $userId)->first();

        if ($syncState && ($syncState->is_locked || ($syncState->is_syncing ?? false))) {
            return response()->json([
                'message' => 'Sync already in progress',
                'status' => 'locked'
            ], 409);
        }

        SyncUserEmails::dispatch($userId, $syncType);

        return response()->json([
            'success' => true,
            'message' => 'Sync initiated successfully',
            'status' => 'processing',
            'type' => $syncType
        ], 202);
    }

    private function ensureRealtimeSubscription($user): void
    {
        try {
            $activeSubscription = GraphSubscription::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->where('expiration_date', '>', now()->addHours(6))
                ->latest('expiration_date')
                ->first();

            if ($activeSubscription) {
                return;
            }

            $expiringSubscription = GraphSubscription::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->latest('expiration_date')
                ->first();

            if ($expiringSubscription && !$expiringSubscription->isExpired()) {
                $this->outlookService->renewSubscription($expiringSubscription->subscription_id, $user);
                return;
            }

            $this->outlookService->createSubscription($user);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
