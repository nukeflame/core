<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\OAuth2\Client\Provider\Google;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Http;

class GoogleOAuthController extends Controller
{
    /**
     * Handle the OAuth callback from Google
     */
    public function callback(Request $request)
    {
        try {
            // Get the authorization code and state from the callback
            $code = $request->get('code');
            $state = $request->get('state');
            $error = $request->get('error');

            // Handle OAuth errors
            if ($error) {
                return $this->handleOAuthError($error, $request->get('error_description'));
            }

            if (!$code) {
                return response()->json(['error' => 'Authorization code not provided'], 400);
            }

            if (!$state) {
                return response()->json(['error' => 'State parameter not provided'], 400);
            }

            // Validate state to prevent CSRF attacks
            // $userEmail = $this->validateStateAndGetUser($state);
            // if (!$userEmail) {
            //     return response()->json(['error' => 'Invalid or expired state parameter'], 400);
            // }

            // Initialize Google OAuth provider
            $provider = new Google([
                'clientId' => config('services.google.client_id'),
                'clientSecret' => config('services.google.client_secret'),
                'redirectUri' => config('services.google.redirect_uri'),
            ]);

            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            $resourceOwner = $provider->getResourceOwner($accessToken);
            $userInfo = $resourceOwner->toArray();

            // Prepare token data
            $tokenData = [
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'expires_at' => $accessToken->getExpires() ? Carbon::createFromTimestamp($accessToken->getExpires())->toDateTimeString() : null,
                'token_type' => 'Bearer',
                'scope' => $accessToken->getValues()['scope'] ?? [],
                'user_info' => $userInfo,
                'authenticated_at' => now()->toDateTimeString(),
            ];

            $userEmail = $userInfo['email'];

            // Store tokens (choose one of these methods based on your needs)
            $this->storeTokens($userEmail, $tokenData);

            // Clean up the state
            // Cache::forget("oauth_state_{$userEmail}");

            // Return success response
            return $this->successResponse($userEmail, $tokenData);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle OAuth errors
     */
    private function handleOAuthError(string $error, ?string $description = null)
    {
        $errorMessages = [
            'access_denied' => 'User denied access to the application',
            'invalid_request' => 'Invalid OAuth request',
            'unauthorized_client' => 'Client is not authorized',
            'unsupported_response_type' => 'Unsupported response type',
            'invalid_scope' => 'Invalid scope requested',
            'server_error' => 'Google server error',
            'temporarily_unavailable' => 'Google service temporarily unavailable',
        ];

        $message = $errorMessages[$error] ?? 'Unknown OAuth error';

        if ($description) {
            $message .= ': ' . $description;
        }

        return response()->json([
            'error' => $error,
            'message' => $message
        ], 400);
    }

    /**
     * Validate state parameter and get associated user email
     */
    private function validateStateAndGetUser(string $state): ?string
    {
        // Look for the user email associated with this state
        // $keys = Cache::getStore()->getRedis()->keys("*oauth_state_*");

        // foreach ($keys as $key) {
        //     $storedState = Cache::get(str_replace(config('cache.prefix') . ':', '', $key));
        //     if ($storedState === $state) {
        //         // Extract email from cache key
        //         $email = str_replace(['oauth_state_', config('cache.prefix') . ':'], '', $key);
        //         return $email;
        //     }
        // }

        return null;
    }

    /**
     * Store tokens - Choose the method that fits your application
     */
    private function storeTokens(string $userEmail, array $tokenData): void
    {
        DB::table('oauth_tokens')->updateOrInsert(
            [
                'email' => $userEmail,
                'provider' => 'google'
            ],
            [
                'access_token' => encrypt($tokenData['access_token']),
                'refresh_token' => encrypt($tokenData['refresh_token']),
                'expires_at' => Carbon::parse($tokenData['expires_at'])->timestamp,
                'scope' => $tokenData['scope'],
                'user_info' => json_encode($tokenData['user_info']),
                'updated_at' => now(),
                'created_at' => now()
            ]
        );
    }

    /**
     * Return success response
     */
    private function successResponse(string $userEmail, array $tokenData)
    {
        return response()->json([
            'success' => true,
            'message' => 'Google OAuth authentication successful',
            // 'user_email' => $userEmail,
            // 'expires_at' => $tokenData['expires_at'],
            // 'has_refresh_token' => !empty($tokenData['refresh_token']),
            // 'scopes' => explode(' ', $tokenData['scope'] ?? ''),
            // 'user_info' => [
            //     'name' => $tokenData['user_info']['name'] ?? null,
            //     'email' => $tokenData['user_info']['email'] ?? null,
            //     'picture' => $tokenData['user_info']['picture'] ?? null,
            // ]
        ]);
    }

    /**
     * Refresh an expired access token
     */
    public function refreshToken(Request $request)
    {
        try {
            $userEmail = $request->get('user_email');

            if (!$userEmail) {
                return response()->json(['error' => 'User email required'], 400);
            }

            // Get stored tokens
            $tokenData = $this->getStoredTokens($userEmail);

            if (!$tokenData || !$tokenData['refresh_token']) {
                return response()->json(['error' => 'No refresh token available'], 400);
            }

            $provider = new Google([
                'clientId' => config('services.google.client_id'),
                'clientSecret' => config('services.google.client_secret'),
                'redirectUri' => config('services.google.redirect_uri'),
            ]);

            // Use refresh token to get new access token
            $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $tokenData['refresh_token']
            ]);

            // Update stored tokens
            $updatedTokenData = array_merge($tokenData, [
                'access_token' => $newAccessToken->getToken(),
                'expires' => $newAccessToken->getExpires(),
                'expires_at' => $newAccessToken->getExpires() ? Carbon::createFromTimestamp($newAccessToken->getExpires())->toDateTimeString() : null,
                'refreshed_at' => now()->toDateTimeString(),
            ]);

            // If a new refresh token is provided, update it
            if ($newAccessToken->getRefreshToken()) {
                $updatedTokenData['refresh_token'] = $newAccessToken->getRefreshToken();
            }

            $this->storeTokens($userEmail, $updatedTokenData);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'expires_at' => $updatedTokenData['expires_at']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token refresh failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stored tokens for a user
     */
    private function getStoredTokens(string $userEmail): ?array
    {
        // Try cache first
        $tokens = Cache::get("google_tokens_{$userEmail}");
        if ($tokens) {
            return $tokens;
        }

        // Try database
        $record = DB::table('oauth_tokens')
            ->where('user_email', $userEmail)
            ->where('provider', 'google')
            ->first();

        if ($record) {
            return [
                'access_token' => decrypt($record->access_token),
                'refresh_token' => $record->refresh_token ? decrypt($record->refresh_token) : null,
                'expires_at' => $record->expires_at,
                'scope' => $record->scope,
                'user_info' => json_decode($record->user_info, true),
            ];
        }

        return null;
    }

    /**
     * Revoke Google tokens
     */
    public function revoke(Request $request)
    {
        try {
            $userEmail = $request->get('user_email');

            if (!$userEmail) {
                return response()->json(['error' => 'User email required'], 400);
            }

            $tokenData = $this->getStoredTokens($userEmail);

            if (!$tokenData) {
                return response()->json(['error' => 'No tokens found'], 404);
            }

            // Revoke token with Google
            $revokeUrl = 'https://oauth2.googleapis.com/revoke';
            $response = Http::post($revokeUrl, [
                'token' => $tokenData['access_token']
            ]);

            // Clean up stored tokens regardless of Google's response
            Cache::forget("google_tokens_{$userEmail}");

            DB::table('oauth_tokens')
                ->where('user_email', $userEmail)
                ->where('provider', 'google')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tokens revoked and cleaned up'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token revocation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
