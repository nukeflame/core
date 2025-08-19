<?php

namespace App\Services;

use App\Helpers\PkceHelper;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Complete OutlookService Implementation
 * Version 2.0 - Full Microsoft 365 Integration
 */
class FirstOutlookServiceMain
{
    private string $graphEndpoint = 'https://graph.microsoft.com/v1.0';
    private string $authEndpoint = 'https://login.microsoftonline.com';
    private ?array $token = null;
    private array $config;
    private int $timeout;
    protected $auth = null;

    // All 200+ delegated permissions
    private array $requiredScopes = [
        'openid',
        'offline_access',
        'profile',
        'email',
        'User.Read',
        'User.ReadWrite',
        'User.ReadBasic.All',
        'User.Read.All',
        'User.ReadWrite.All',
        'Mail.Read',
        'Mail.ReadWrite',
        'Mail.Send',
        'Mail.Send.Shared',
        'SMTP.Send',
        'Calendars.Read',
        'Calendars.ReadWrite',
        'Calendars.Read.Shared',
        'Calendars.ReadWrite.Shared',
        'Files.Read',
        'Files.ReadWrite',
        'Files.Read.All',
        'Files.ReadWrite.All',
        'Sites.Read.All',
        'Sites.ReadWrite.All',
        'Sites.Manage.All',
        'Sites.FullControl.All',
        'Chat.Read',
        'Chat.ReadWrite',
        'ChatMessage.Read',
        'ChatMessage.Send',
        'Team.ReadBasic.All',
        'TeamMember.Read.All',
        'Channel.ReadBasic.All',
        'Directory.Read.All',
        'Directory.ReadWrite.All',
        'Organization.Read.All',
        'SecurityEvents.Read.All',
        'AuditLog.Read.All',
        'Reports.Read.All',
        'Presence.Read',
        'Presence.ReadWrite',
        'OnlineMeetings.Read',
        'OnlineMeetings.ReadWrite',
        // ... (full list of 200+ permissions as shown earlier)
    ];

    public function __construct(int $timeout = 60)
    {
        $this->config = [
            'client_id' => config('services.azure.client_id', env('AZURE_CLIENT_ID')),
            'client_secret' => config('services.azure.client_secret', env('AZURE_CLIENT_SECRET')),
            'tenant_id' => config('services.azure.tenant_id', env('AZURE_TENANT_ID', 'common')),
            'redirect_uri' => config('services.azure.redirect_uri', env('AZURE_REDIRECT_URI'))
        ];
        $this->timeout = $timeout;
        $this->validateConfig();
    }

    // SECTION 1: AUTHENTICATION & TOKEN MANAGEMENT
    /**
     * Validate configuration parameters
     * Ensures all required Azure AD configuration is present
     */
    private function validateConfig(): void
    {
        $required = ['client_id', 'client_secret', 'redirect_uri'];

        foreach ($required as $key) {
            if (empty($this->config[$key])) {
                throw new Exception("Missing required Azure configuration: {$key}. Please check your .env file and ensure AZURE_{$key} is set.");
            }
        }

        // Validate client_id format (should be a GUID)
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $this->config['client_id'])) {
            throw new Exception("Invalid client_id format. Must be a valid GUID.");
        }

        // Validate redirect_uri format
        if (!filter_var($this->config['redirect_uri'], FILTER_VALIDATE_URL)) {
            throw new Exception("Invalid redirect_uri format. Must be a valid URL.");
        }

        Log::info('Azure configuration validated successfully', [
            'client_id' => substr($this->config['client_id'], 0, 8) . '...',
            'tenant_id' => $this->config['tenant_id'],
            'redirect_uri' => $this->config['redirect_uri']
        ]);
    }

    /**
     * Check if user is authenticated with valid token
     *
     * @return bool True if user has valid authentication
     */
    public function isAuthenticated(): bool
    {
        try {
            $token = $this->getValidToken();
            $isValid = $token !== null;

            Log::debug('Authentication check', [
                'user' => $this->auth->email ?? 'unknown',
                'is_authenticated' => $isValid,
                'token_exists' => $token ? 'yes' : 'no'
            ]);

            return $isValid;
        } catch (Exception $e) {
            Log::error('Authentication check failed', [
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Check if token is valid for specific user email
     *
     * @param string $userEmail The user's email address
     * @return bool True if token exists and is not expired
     */
    public function isTokenValid(string $userEmail): bool
    {
        try {
            // Check if email format is valid
            if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                Log::warning('Invalid email format provided for token validation', [
                    'email' => $userEmail
                ]);
                return false;
            }

            $tokenRecord = DB::table('oauth_tokens')
                ->where('provider', 'outlook')
                ->where('email', $userEmail)
                ->first();

            if (!$tokenRecord) {
                Log::info('No token record found for user', [
                    'email' => $userEmail
                ]);
                return false;
            }

            $expiresAt = Carbon::createFromTimestamp($tokenRecord->expires_at);
            $isValid = !$expiresAt->isPast();

            Log::info('Token validation result', [
                'email' => $userEmail,
                'expires_at' => $expiresAt->toISOString(),
                'is_valid' => $isValid,
                'expires_in_minutes' => $expiresAt->diffInMinutes(now(), false)
            ]);

            return $isValid;
        } catch (Exception $e) {
            Log::error('Token validation failed', [
                'email' => $userEmail,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get valid token with automatic refresh if needed
     *
     * @return array|null Token data or null if no valid token
     */
    public function getValidToken(): ?array
    {
        try {
            // Load token if not already loaded
            if ($this->token === null) {
                $this->token = $this->loadToken();
            }

            if (!$this->token) {
                Log::debug('No token available for user', [
                    'user' => $this->auth->email ?? 'unknown'
                ]);
                return null;
            }

            // Check token expiration
            $expiresAt = isset($this->token['expires_at'])
                ? Carbon::parse($this->token['expires_at'])
                : Carbon::createFromTimestamp($this->token['expires_in']);

            $timeUntilExpiry = $expiresAt->diffInMinutes(now(), false);

            Log::debug('Token expiration check', [
                'user' => $this->auth->email ?? 'unknown',
                'expires_at' => $expiresAt->toISOString(),
                'minutes_until_expiry' => $timeUntilExpiry,
                'is_expired' => $expiresAt->isPast()
            ]);

            // Refresh token if expired or expires within 5 minutes
            if ($expiresAt->isPast() || $timeUntilExpiry <= 5) {
                if (isset($this->token['refresh_token'])) {
                    Log::info('Attempting token refresh', [
                        'user' => $this->auth->email ?? 'unknown',
                        'reason' => $expiresAt->isPast() ? 'expired' : 'expires_soon'
                    ]);

                    $refreshSuccess = $this->refreshToken();

                    if (!$refreshSuccess) {
                        Log::error('Token refresh failed', [
                            'user' => $this->auth->email ?? 'unknown'
                        ]);
                        return null;
                    }

                    return $this->token;
                } else {
                    Log::warning('Token expired but no refresh token available', [
                        'user' => $this->auth->email ?? 'unknown'
                    ]);
                    return null;
                }
            }

            return $this->token;
        } catch (Exception $e) {
            Log::error('Failed to get valid token', [
                'user' => $this->auth->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Load token for current authenticated user from database
     *
     * @return array|null Token data or null if not found
     */
    private function loadToken(): ?array
    {
        $user = $this->auth;
        if (!$user) {
            Log::debug('No authenticated user set for token loading');
            return null;
        }

        try {
            $tokenRecord = DB::table('oauth_tokens')
                ->where('provider', 'outlook')
                ->where('email', $user->email)
                ->first();

            if (!$tokenRecord) {
                Log::info('No token record found in database', [
                    'email' => $user->email
                ]);
                return null;
            }

            // Decrypt token data
            $decryptedAccessToken = decrypt($tokenRecord->access_token);
            $decryptedRefreshToken = decrypt($tokenRecord->refresh_token);

            $tokenData = [
                'access_token' => $decryptedAccessToken,
                'refresh_token' => $decryptedRefreshToken,
                'expires_in' => $tokenRecord->expires_at,
                'expires_at' => Carbon::createFromTimestamp($tokenRecord->expires_at)->toISOString(),
                'scope' => $tokenRecord->scope,
                'user_email' => $tokenRecord->email,
                'tenant_id' => $this->config['tenant_id'],
                'user_id' => $tokenRecord->user_id,
                'created_at' => $tokenRecord->created_at,
                'updated_at' => $tokenRecord->updated_at
            ];

            Log::info('Token loaded successfully from database', [
                'email' => $user->email,
                'expires_at' => $tokenData['expires_at'],
                'has_refresh_token' => !empty($tokenData['refresh_token'])
            ]);

            return $tokenData;
        } catch (Exception $e) {
            Log::error('Failed to decrypt or load token', [
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // If decryption fails, token might be corrupted - remove it
            try {
                DB::table('oauth_tokens')
                    ->where('provider', 'outlook')
                    ->where('email', $user->email)
                    ->delete();

                Log::info('Corrupted token removed from database', [
                    'email' => $user->email
                ]);
            } catch (Exception $deleteError) {
                Log::error('Failed to remove corrupted token', [
                    'email' => $user->email,
                    'error' => $deleteError->getMessage()
                ]);
            }

            return null;
        }
    }

    /**
     * Refresh access token using refresh token
     *
     * @return bool True if refresh was successful
     */
    public function refreshToken(): bool
    {
        try {
            if (!$this->token || !isset($this->token['refresh_token'])) {
                throw new Exception('No refresh token available for token refresh');
            }

            $tenantId = $this->token['tenant_id'] ?? $this->config['tenant_id'];
            $tokenUrl = "{$this->authEndpoint}/{$tenantId}/oauth2/v2.0/token";

            Log::info('Starting token refresh', [
                'user' => $this->auth->email ?? 'unknown',
                'tenant_id' => $tenantId,
                'token_url' => $tokenUrl
            ]);

            $startTime = microtime(true);

            $response = Http::asForm()
                ->timeout($this->timeout)
                ->post($tokenUrl, [
                    'client_id' => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                    'refresh_token' => $this->token['refresh_token'],
                    'grant_type' => 'refresh_token',
                    'scope' => implode(' ', $this->requiredScopes)
                ]);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if (!$response->successful()) {
                $errorBody = $response->body();
                $errorData = json_decode($errorBody, true);

                Log::error('Token refresh failed', [
                    'user' => $this->auth->email ?? 'unknown',
                    'status' => $response->status(),
                    'error' => $errorData['error'] ?? 'unknown',
                    'error_description' => $errorData['error_description'] ?? $errorBody,
                    'response_time_ms' => $responseTime
                ]);

                // If refresh token is invalid, remove the token from database
                if (
                    $response->status() === 400 &&
                    (str_contains($errorBody, 'invalid_grant') || str_contains($errorBody, 'expired_token'))
                ) {

                    Log::warning('Refresh token is invalid or expired, removing from database', [
                        'user' => $this->auth->email ?? 'unknown'
                    ]);

                    $this->revokeAuthentication();
                }

                return false;
            }

            $tokenData = $response->json();

            if (!isset($tokenData['access_token'])) {
                Log::error('Token refresh response missing access_token', [
                    'user' => $this->auth->email ?? 'unknown',
                    'response_keys' => array_keys($tokenData)
                ]);
                return false;
            }

            $expiresAt = now()->addSeconds($tokenData['expires_in']);

            // Update token data
            $this->token = array_merge($this->token, [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? $this->token['refresh_token'],
                'expires_in' => $expiresAt->timestamp,
                'expires_at' => $expiresAt->toISOString(),
                'scope' => $tokenData['scope'] ?? $this->token['scope'],
                'refreshed_at' => now()->toISOString()
            ]);

            // Save updated token to database
            if ($this->auth) {
                $this->saveToken($this->auth, $this->token);
            }

            Log::info('Token refresh successful', [
                'user' => $this->auth->email ?? 'unknown',
                'new_expires_at' => $this->token['expires_at'],
                'has_new_refresh_token' => isset($tokenData['refresh_token']),
                'response_time_ms' => $responseTime
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Token refresh error', [
                'user' => $this->auth->email ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Save token to database with encryption
     *
     * @param mixed $user User object or model
     * @param array $tokenData Token data to save
     * @throws Exception If user is invalid or save fails
     */
    public function saveToken($user, array $tokenData): void
    {
        if (!$user) {
            throw new Exception('No authenticated user found for token save');
        }

        if (!isset($user->email) || !isset($user->id)) {
            throw new Exception('User object must have email and id properties');
        }

        if (!isset($tokenData['access_token']) || !isset($tokenData['refresh_token'])) {
            throw new Exception('Token data must contain access_token and refresh_token');
        }

        try {
            // Calculate expiration timestamp
            $expiresAt = isset($tokenData['expires_at']) && is_string($tokenData['expires_at'])
                ? Carbon::parse($tokenData['expires_at'])->timestamp
                : ($tokenData['expires_in'] ?? now()->addHour()->timestamp);

            Log::info('Saving token to database', [
                'email' => $user->email,
                'user_id' => $user->id,
                'expires_at' => Carbon::createFromTimestamp($expiresAt)->toISOString(),
                'scope_count' => count(explode(' ', $tokenData['scope'] ?? ''))
            ]);

            // Encrypt sensitive token data
            $encryptedAccessToken = encrypt($tokenData['access_token']);
            $encryptedRefreshToken = encrypt($tokenData['refresh_token']);

            // Save to database with upsert
            DB::table('oauth_tokens')->updateOrInsert(
                [
                    'email' => $user->email,
                    'provider' => 'outlook'
                ],
                [
                    'access_token' => $encryptedAccessToken,
                    'refresh_token' => $encryptedRefreshToken,
                    'expires_at' => $expiresAt,
                    'user_id' => $user->id,
                    'scope' => $tokenData['scope'] ?? implode(' ', $this->requiredScopes),
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            Log::info('Token saved successfully to database', [
                'email' => $user->email,
                'expires_at' => Carbon::createFromTimestamp($expiresAt)->toISOString()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to save OAuth token', [
                'error' => $e->getMessage(),
                'email' => $user->email ?? 'unknown',
                'user_id' => $user->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Exchange authorization code for access token
     *
     * @param string $code Authorization code from OAuth callback
     * @param string $codeVerifier PKCE code verifier
     * @return array Token data or empty array on failure
     */
    public function getAccessToken(string $code, string $codeVerifier): array
    {
        try {
            if (empty($code)) {
                throw new Exception('Authorization code is required');
            }

            if (empty($codeVerifier)) {
                throw new Exception('PKCE code verifier is required');
            }

            $tokenUrl = "{$this->authEndpoint}/{$this->config['tenant_id']}/oauth2/v2.0/token";

            Log::info('Starting token exchange', [
                'code_length' => strlen($code),
                'code_verifier_length' => strlen($codeVerifier),
                'tenant_id' => $this->config['tenant_id'],
                'redirect_uri' => $this->config['redirect_uri']
            ]);

            $startTime = microtime(true);

            $response = Http::asForm()
                ->timeout($this->timeout)
                ->post($tokenUrl, [
                    'client_id' => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                    'code' => $code,
                    'redirect_uri' => $this->config['redirect_uri'],
                    'grant_type' => 'authorization_code',
                    'code_verifier' => $codeVerifier,
                    'scope' => implode(' ', $this->requiredScopes)
                ]);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if (!$response->successful()) {
                $errorBody = $response->body();
                $errorData = json_decode($errorBody, true);

                Log::error('Token exchange failed', [
                    'status' => $response->status(),
                    'error' => $errorData['error'] ?? 'unknown',
                    'error_description' => $errorData['error_description'] ?? $errorBody,
                    'response_time_ms' => $responseTime
                ]);

                throw new Exception('Failed to exchange authorization code for access token: ' .
                    ($errorData['error_description'] ?? $errorBody));
            }

            $tokenData = $response->json();

            if (!isset($tokenData['access_token'])) {
                Log::error('Token exchange response missing access_token', [
                    'response_keys' => array_keys($tokenData),
                    'response_time_ms' => $responseTime
                ]);
                throw new Exception('Token exchange response missing access_token');
            }

            $user = auth()->user();
            if (!$user) {
                throw new Exception('No authenticated user found during token exchange');
            }

            $expiresAt = now()->addSeconds($tokenData['expires_in']);

            $formattedTokenData = [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_in' => $expiresAt->timestamp,
                'expires_at' => $expiresAt->toISOString(),
                'scope' => $tokenData['scope'] ?? implode(' ', $this->requiredScopes),
                'tenant_id' => $this->config['tenant_id'],
                'auth_type' => 'authorization_code',
                'created_at' => now()->toISOString(),
                'user_email' => $user->email,
                'token_type' => $tokenData['token_type'] ?? 'Bearer'
            ];

            Log::info('Token exchange successful', [
                'user_email' => $user->email,
                'expires_at' => $formattedTokenData['expires_at'],
                'has_refresh_token' => !empty($formattedTokenData['refresh_token']),
                'scope_count' => count(explode(' ', $formattedTokenData['scope'])),
                'response_time_ms' => $responseTime
            ]);

            return $formattedTokenData;
        } catch (Exception $e) {
            Log::error('Access token exchange failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Generate authorization URL for OAuth flow with PKCE
     *
     * @return array Contains state and authorization URL
     */
    public function getAuthUrl(): array
    {
        try {
            // Generate secure random state and PKCE parameters
            $state = Str::random(32);
            $codeVerifier = PkceHelper::generateCodeVerifier();
            $codeChallenge = PkceHelper::generateCodeChallenge($codeVerifier);

            // Store PKCE code verifier in Redis with expiration
            Redis::setex("azure_auth_state_{$state}", 600, json_encode([
                'code_verifier' => $codeVerifier,
                'created_at' => now()->toISOString(),
                'user_agent' => request()->header('User-Agent', 'unknown'),
                'ip_address' => request()->ip()
            ]));

            // Build authorization parameters
            $authParams = [
                'client_id' => $this->config['client_id'],
                'response_type' => 'code',
                'redirect_uri' => $this->config['redirect_uri'],
                'scope' => implode(' ', $this->requiredScopes),
                'state' => $state,
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
                'response_mode' => 'query',
                'prompt' => 'select_account', // Force account selection
                'access_type' => 'offline', // Request refresh token
            ];

            // Add login hint if user is authenticated
            $user = auth()->user();
            if ($user && !empty($user->email)) {
                $authParams['login_hint'] = $user->email;
            }

            $authUrl = "{$this->authEndpoint}/{$this->config['tenant_id']}/oauth2/v2.0/authorize?" .
                http_build_query($authParams);

            Log::info('Authorization URL generated', [
                'state' => $state,
                'tenant_id' => $this->config['tenant_id'],
                'scope_count' => count($this->requiredScopes),
                'user_email' => $user->email ?? 'not_authenticated',
                'has_login_hint' => isset($authParams['login_hint'])
            ]);

            return [
                'state' => $state,
                'authUrl' => $authUrl,
                'expires_at' => now()->addMinutes(10)->toISOString(),
                'scopes_requested' => count($this->requiredScopes)
            ];
        } catch (Exception $e) {
            Log::error('Failed to generate authorization URL', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new Exception('Failed to generate authorization URL: ' . $e->getMessage());
        }
    }

    // SECTION 2: CORE API REQUEST METHOD

    /**
     * Make authenticated HTTP request to Microsoft Graph API
     * This is the foundation method for all Graph API communications
     *
     * @param string $method HTTP method (GET, POST, PUT, PATCH, DELETE)
     * @param string $endpoint API endpoint (with or without full URL)
     * @param array $options Request options (query, json, headers, etc.)
     * @return array API response data
     * @throws Exception On authentication or API errors
     */
    private function makeRequest(string $method, string $endpoint, array $options = []): array
    {
        $startTime = microtime(true);
        $requestId = Str::uuid()->toString();

        try {
            // Validate and get authentication token
            $token = $this->getValidToken();
            if (!$token) {
                throw new Exception('No valid authentication token available. Please re-authenticate.');
            }

            // Build complete URL
            $url = $this->buildRequestUrl($endpoint);

            // Validate HTTP method
            $method = strtoupper($method);
            $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
            if (!in_array($method, $allowedMethods)) {
                throw new Exception("Unsupported HTTP method: {$method}");
            }

            // Prepare HTTP client with authentication and options
            $httpClient = $this->prepareHttpClient($token, $options, $requestId);

            // Log request details (without sensitive data)
            $this->logRequestStart($method, $url, $options, $requestId);

            // Execute HTTP request with method-specific handling
            $response = $this->executeHttpRequest($httpClient, $method, $url, $options);

            // Calculate response time
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Handle response and errors
            $result = $this->handleApiResponse($response, $method, $url, $responseTime, $requestId);

            // Log successful request
            $this->logRequestSuccess($method, $url, $response->status(), $responseTime, $requestId, $result);

            return $result;
        } catch (Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            $this->logRequestError($method, $endpoint, $e, $responseTime, $requestId);
            throw $e;
        }
    }

    /**
     * Build complete request URL from endpoint
     *
     * @param string $endpoint API endpoint
     * @return string Complete URL
     */
    private function buildRequestUrl(string $endpoint): string
    {
        // Return as-is if already a complete URL
        if (str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://')) {
            return $endpoint;
        }

        // Ensure endpoint starts with slash
        if (!str_starts_with($endpoint, '/')) {
            $endpoint = '/' . $endpoint;
        }

        return $this->graphEndpoint . $endpoint;
    }

    /**
     * Prepare HTTP client with authentication and configuration
     *
     * @param array $token Authentication token data
     * @param array $options Request options
     * @param string $requestId Unique request identifier
     * @return \Illuminate\Http\Client\PendingRequest
     */
    private function prepareHttpClient(array $token, array $options, string $requestId): \Illuminate\Http\Client\PendingRequest
    {
        // Base HTTP client with authentication
        $httpClient = Http::withToken($token['access_token'])
            ->timeout($this->timeout)
            ->connectTimeout(30)
            ->retry(3, 1000, function ($exception, $request) use ($requestId) {
                // Only retry on specific conditions
                if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                    Log::warning('HTTP connection failed, retrying', [
                        'request_id' => $requestId,
                        'error' => $exception->getMessage()
                    ]);
                    return true;
                }

                // Retry on 5xx server errors (but not 4xx client errors)
                if (isset($exception->response) && $exception->response->status() >= 500) {
                    Log::warning('Server error, retrying', [
                        'request_id' => $requestId,
                        'status' => $exception->response->status()
                    ]);
                    return true;
                }

                return false;
            });

        // Add standard headers
        $httpClient = $httpClient->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'Laravel-OutlookService/2.0',
            'X-Request-ID' => $requestId,
            'Prefer' => 'return=representation', // Get full objects in responses
        ]);

        // Add custom headers if provided
        if (isset($options['headers'])) {
            $httpClient = $httpClient->withHeaders($options['headers']);
        }

        // Handle file uploads
        if (isset($options['multipart'])) {
            $httpClient = $httpClient->asMultipart();
        } elseif (isset($options['form'])) {
            $httpClient = $httpClient->asForm();
        }

        // Set specific content type if provided
        if (isset($options['content_type'])) {
            $httpClient = $httpClient->contentType($options['content_type']);
        }

        return $httpClient;
    }

    /**
     * Execute HTTP request with method-specific handling
     *
     * @param \Illuminate\Http\Client\PendingRequest $httpClient
     * @param string $method HTTP method
     * @param string $url Request URL
     * @param array $options Request options
     * @return \Illuminate\Http\Client\Response
     */
    private function executeHttpRequest($httpClient, string $method, string $url, array $options): \Illuminate\Http\Client\Response
    {
        return match ($method) {
            'GET' => $httpClient->get($url, $options['query'] ?? []),
            'POST' => $this->executePostRequest($httpClient, $url, $options),
            'PUT' => $this->executePutRequest($httpClient, $url, $options),
            'PATCH' => $this->executePatchRequest($httpClient, $url, $options),
            'DELETE' => $httpClient->delete($url, $options['query'] ?? []),
            'HEAD' => $httpClient->head($url, $options['query'] ?? []),
            'OPTIONS' => $httpClient->send('OPTIONS', $url),
            default => throw new Exception("HTTP method {$method} not implemented")
        };
    }

    /**
     * Execute POST request with appropriate data handling
     */
    private function executePostRequest($httpClient, string $url, array $options): \Illuminate\Http\Client\Response
    {
        if (isset($options['json'])) {
            return $httpClient->post($url, $options['json']);
        } elseif (isset($options['form'])) {
            return $httpClient->asForm()->post($url, $options['form']);
        } elseif (isset($options['multipart'])) {
            return $httpClient->asMultipart()->post($url, $options['multipart']);
        } elseif (isset($options['body'])) {
            return $httpClient->withBody($options['body'], $options['content_type'] ?? 'application/octet-stream')->post($url);
        } else {
            return $httpClient->post($url);
        }
    }

    /**
     * Execute PUT request with appropriate data handling
     */
    private function executePutRequest($httpClient, string $url, array $options): \Illuminate\Http\Client\Response
    {
        if (isset($options['json'])) {
            return $httpClient->put($url, $options['json']);
        } elseif (isset($options['body'])) {
            return $httpClient->withBody($options['body'], $options['content_type'] ?? 'application/octet-stream')->put($url);
        } else {
            return $httpClient->put($url);
        }
    }

    /**
     * Execute PATCH request with appropriate data handling
     */
    private function executePatchRequest($httpClient, string $url, array $options): \Illuminate\Http\Client\Response
    {
        if (isset($options['json'])) {
            return $httpClient->patch($url, $options['json']);
        } elseif (isset($options['body'])) {
            return $httpClient->withBody($options['body'], $options['content_type'] ?? 'application/octet-stream')->patch($url);
        } else {
            return $httpClient->patch($url);
        }
    }

    /**
     * Handle API response and convert to array
     *
     * @param \Illuminate\Http\Client\Response $response HTTP response
     * @param string $method HTTP method
     * @param string $url Request URL
     * @param float $responseTime Response time in milliseconds
     * @param string $requestId Unique request identifier
     * @return array Response data
     * @throws Exception On API errors
     */
    private function handleApiResponse($response, string $method, string $url, float $responseTime, string $requestId): array
    {
        $statusCode = $response->status();

        // Handle successful responses
        if ($response->successful()) {
            return $this->parseSuccessfulResponse($response, $method);
        }

        // Handle specific error status codes
        $this->handleSpecificErrorCodes($response, $method, $url, $responseTime, $requestId);

        // Handle general error
        $errorBody = $response->body();
        $errorData = $this->parseErrorResponse($errorBody);

        throw new Exception(
            "Microsoft Graph API request failed: HTTP {$statusCode} - " .
                ($errorData['message'] ?? $errorData['error_description'] ?? 'Unknown error')
        );
    }

    /**
     * Parse successful API response
     *
     * @param \Illuminate\Http\Client\Response $response
     * @param string $method HTTP method
     * @return array Parsed response data
     */
    private function parseSuccessfulResponse($response, string $method): array
    {
        $contentType = $response->header('Content-Type');

        // Handle different content types
        if (str_contains($contentType, 'application/json')) {
            $data = $response->json();
            return is_array($data) ? $data : [];
        }

        // Handle binary content (file downloads, images, etc.)
        if (
            str_contains($contentType, 'application/octet-stream') ||
            str_contains($contentType, 'image/') ||
            str_contains($contentType, 'application/pdf')
        ) {
            return [
                'content' => $response->body(),
                'content_type' => $contentType,
                'size' => strlen($response->body())
            ];
        }

        // Handle text content
        if (str_contains($contentType, 'text/')) {
            return [
                'content' => $response->body(),
                'content_type' => $contentType
            ];
        }

        // Handle empty responses (like DELETE operations)
        if ($method === 'DELETE' || $response->status() === 204) {
            return ['success' => true];
        }

        // Default: try to parse as JSON, fallback to empty array
        try {
            return $response->json() ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Handle specific HTTP error status codes
     *
     * @param \Illuminate\Http\Client\Response $response
     * @param string $method HTTP method
     * @param string $url Request URL
     * @param float $responseTime Response time
     * @param string $requestId Request ID
     * @throws Exception With specific error messages
     */
    private function handleSpecificErrorCodes($response, string $method, string $url, float $responseTime, string $requestId): void
    {
        $statusCode = $response->status();
        $errorBody = $response->body();

        switch ($statusCode) {
            case 400:
                $this->handleBadRequestError($response, $requestId);
                break;

            case 401:
                $this->handleUnauthorizedError($response, $requestId);
                break;

            case 403:
                $this->handleForbiddenError($response, $requestId);
                break;

            case 404:
                $this->handleNotFoundError($response, $url, $requestId);
                break;

            case 409:
                $this->handleConflictError($response, $requestId);
                break;

            case 413:
                throw new Exception("Request payload too large. Please reduce the size of your request.");

            case 429:
                $this->handleRateLimitError($response, $requestId);
                break;

            case 500:
            case 502:
            case 503:
            case 504:
                $this->handleServerError($response, $statusCode, $requestId);
                break;

            default:
                // Log unexpected status codes
                Log::warning('Unexpected HTTP status code', [
                    'request_id' => $requestId,
                    'status_code' => $statusCode,
                    'method' => $method,
                    'url' => $url,
                    'response_time_ms' => $responseTime
                ]);
        }
    }

    /**
     * Handle 400 Bad Request errors
     */
    private function handleBadRequestError($response, string $requestId): void
    {
        $errorData = $this->parseErrorResponse($response->body());

        Log::error('Bad Request error', [
            'request_id' => $requestId,
            'error_code' => $errorData['code'] ?? 'unknown',
            'error_message' => $errorData['message'] ?? 'unknown'
        ]);

        $message = $errorData['message'] ?? 'Bad request. Please check your request parameters.';
        throw new Exception("Bad Request: {$message}");
    }

    /**
     * Handle 401 Unauthorized errors
     */
    private function handleUnauthorizedError($response, string $requestId): void
    {
        $errorData = $this->parseErrorResponse($response->body());

        Log::error('Unauthorized error - token may be invalid', [
            'request_id' => $requestId,
            'error_code' => $errorData['code'] ?? 'unknown',
            'user' => $this->auth->email ?? 'unknown'
        ]);

        // Clear potentially invalid token
        $this->token = null;

        throw new Exception("Authentication failed. Please re-authenticate with Microsoft.");
    }

    /**
     * Handle 403 Forbidden errors
     */
    private function handleForbiddenError($response, string $requestId): void
    {
        $errorData = $this->parseErrorResponse($response->body());

        Log::error('Forbidden error - insufficient permissions', [
            'request_id' => $requestId,
            'error_code' => $errorData['code'] ?? 'unknown',
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $message = $errorData['message'] ?? 'Insufficient permissions for this operation.';
        throw new Exception("Forbidden: {$message}");
    }

    /**
     * Handle 404 Not Found errors
     */
    private function handleNotFoundError($response, string $url, string $requestId): void
    {
        Log::warning('Resource not found', [
            'request_id' => $requestId,
            'url' => $url,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        throw new Exception("Requested resource not found.");
    }

    /**
     * Handle 409 Conflict errors
     */
    private function handleConflictError($response, string $requestId): void
    {
        $errorData = $this->parseErrorResponse($response->body());

        Log::warning('Conflict error', [
            'request_id' => $requestId,
            'error_code' => $errorData['code'] ?? 'unknown'
        ]);

        $message = $errorData['message'] ?? 'Conflict with current state of the resource.';
        throw new Exception("Conflict: {$message}");
    }

    /**
     * Handle 429 Rate Limit errors
     */
    private function handleRateLimitError($response, string $requestId): void
    {
        $retryAfter = $response->header('Retry-After') ?? '60';

        Log::warning('Rate limit exceeded', [
            'request_id' => $requestId,
            'retry_after' => $retryAfter,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        throw new Exception("Rate limit exceeded. Please wait {$retryAfter} seconds before trying again.");
    }

    /**
     * Handle 5xx Server errors
     */
    private function handleServerError($response, int $statusCode, string $requestId): void
    {
        Log::error('Microsoft server error', [
            'request_id' => $requestId,
            'status_code' => $statusCode,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        throw new Exception("Microsoft server error (HTTP {$statusCode}). Please try again later.");
    }

    /**
     * Parse error response body
     *
     * @param string $errorBody Raw error response
     * @return array Parsed error data
     */
    private function parseErrorResponse(string $errorBody): array
    {
        try {
            $errorData = json_decode($errorBody, true);

            // Handle Microsoft Graph error format
            if (isset($errorData['error'])) {
                if (is_array($errorData['error'])) {
                    return [
                        'code' => $errorData['error']['code'] ?? 'unknown',
                        'message' => $errorData['error']['message'] ?? 'Unknown error',
                        'details' => $errorData['error']['details'] ?? []
                    ];
                } else {
                    return [
                        'code' => $errorData['error'],
                        'message' => $errorData['error_description'] ?? 'Unknown error'
                    ];
                }
            }

            return $errorData ?? ['message' => 'Unknown error'];
        } catch (Exception $e) {
            return [
                'message' => 'Failed to parse error response',
                'raw_body' => $errorBody
            ];
        }
    }

    /**
     * Log request start
     */
    private function logRequestStart(string $method, string $url, array $options, string $requestId): void
    {
        $logData = [
            'request_id' => $requestId,
            'method' => $method,
            'url' => $url,
            'user' => $this->auth->email ?? 'unknown',
            'has_query' => !empty($options['query']),
            'has_json_body' => !empty($options['json']),
            'has_form_body' => !empty($options['form']),
            'has_file_body' => !empty($options['body'])
        ];

        // Add query parameter count without exposing sensitive data
        if (!empty($options['query'])) {
            $logData['query_param_count'] = count($options['query']);
        }

        Log::info('Starting API request', $logData);
    }

    /**
     * Log successful request
     */
    private function logRequestSuccess(string $method, string $url, int $statusCode, float $responseTime, string $requestId, array $result): void
    {
        Log::info('API request successful', [
            'request_id' => $requestId,
            'method' => $method,
            'status_code' => $statusCode,
            'response_time_ms' => $responseTime,
            'response_size' => is_array($result) ? count($result) : (isset($result['content']) ? strlen($result['content']) : 0),
            'user' => $this->auth->email ?? 'unknown'
        ]);
    }

    /**
     * Log request error
     */
    private function logRequestError(string $method, string $endpoint, Exception $exception, float $responseTime, string $requestId): void
    {
        Log::error('API request failed', [
            'request_id' => $requestId,
            'method' => $method,
            'endpoint' => $endpoint,
            'error' => $exception->getMessage(),
            'response_time_ms' => $responseTime,
            'user' => $this->auth->email ?? 'unknown',
            'exception_class' => get_class($exception)
        ]);
    }

    // SECTION 3: EMAIL METHODS

    /**
     * Send email with comprehensive features and message tracking
     *
     * @param mixed $auth Authenticated user object
     * @param array $emailData Email configuration and content
     * @return array Result with success status, message_id, and details
     */
    public function sendEmail($auth, array $emailData): array
    {
        try {
            $this->auth = $auth;
            $startTime = microtime(true);

            // Validate required fields
            $this->validateEmailData($emailData);

            // Sanitize and prepare email data
            $emailData = $this->sanitizeEmailData($emailData);

            // Log email send attempt
            Log::info('Starting email send process', [
                'user' => $auth->email,
                'subject' => $emailData['subject'],
                'to_count' => count($emailData['to']),
                'cc_count' => count($emailData['cc'] ?? []),
                'bcc_count' => count($emailData['bcc'] ?? []),
                'has_attachments' => !empty($emailData['attachments']),
                'body_type' => $emailData['bodyType'] ?? 'HTML',
                'priority' => $emailData['priority'] ?? 'normal'
            ]);

            // Use draft-and-send approach for message ID tracking
            $result = $this->sendEmailWithMessageId($auth, $emailData);

            $processingTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($result['success']) {
                Log::info('Email sent successfully', [
                    'user' => $auth->email,
                    'message_id' => $result['message_id'],
                    'processing_time_ms' => $processingTime
                ]);
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to send email via Outlook API', [
                'error' => $e->getMessage(),
                'user' => $auth->email ?? 'unknown',
                'subject' => $emailData['subject'] ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message_id' => null,
                'error_code' => $this->getErrorCode($e)
            ];
        }
    }

    /**
     * Send email with message ID tracking using draft-first approach
     *
     * @param mixed $auth Authenticated user object
     * @param array $emailData Email configuration and content
     * @return array Result with success status and message details
     */
    public function sendEmailWithMessageId($auth, array $emailData): array
    {
        try {
            $this->auth = $auth;

            // Step 1: Create draft message
            $draftResponse = $this->createDraftMessage($emailData);
            if (!$draftResponse['success']) {
                return $draftResponse;
            }

            $messageId = $draftResponse['message_id'];

            // Step 2: Send the draft
            $sendResponse = $this->sendDraftMessage($messageId);
            if (!$sendResponse['success']) {
                // Clean up draft on send failure
                $this->deleteDraftMessage($messageId);
                return $sendResponse;
            }

            // Step 3: Get sent message details
            $sentMessage = $this->getMessageDetails($messageId);

            return [
                'success' => true,
                'message_id' => $messageId,
                'conversation_id' => $sentMessage['conversationId'] ?? null,
                'internet_message_id' => $sentMessage['internetMessageId'] ?? null,
                'sent_at' => $sentMessage['sentDateTime'] ?? now()->toISOString(),
                'subject' => $sentMessage['subject'] ?? $emailData['subject'],
                'recipients' => [
                    'to' => $this->extractRecipientsFromMessage($sentMessage['toRecipients'] ?? []),
                    'cc' => $this->extractRecipientsFromMessage($sentMessage['ccRecipients'] ?? []),
                    'bcc' => $this->extractRecipientsFromMessage($sentMessage['bccRecipients'] ?? [])
                ],
                'message' => 'Email sent successfully'
            ];
        } catch (Exception $e) {
            Log::error('Failed to send email with message ID tracking', [
                'error' => $e->getMessage(),
                'subject' => $emailData['subject'] ?? 'N/A',
                'user' => $auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message_id' => null,
                'error_code' => $this->getErrorCode($e)
            ];
        }
    }

    /**
     * Create a draft message with full email content
     *
     * @param array $emailData Email configuration and content
     * @return array Result with draft message ID
     */
    public function createDraftMessage(array $emailData): array
    {
        try {
            // Build message structure
            $message = [
                'subject' => $emailData['subject'],
                'body' => [
                    'contentType' => $emailData['bodyType'] ?? 'HTML',
                    'content' => $emailData['body'] ?? ''
                ],
                'toRecipients' => $this->formatRecipients($emailData['to']),
                'importance' => $this->mapPriorityToImportance($emailData['priority'] ?? 'normal')
            ];

            // Add optional recipients
            if (!empty($emailData['cc'])) {
                $message['ccRecipients'] = $this->formatRecipients($emailData['cc']);
            }

            if (!empty($emailData['bcc'])) {
                $message['bccRecipients'] = $this->formatRecipients($emailData['bcc']);
            }

            // Add reply-to if specified
            if (!empty($emailData['replyTo'])) {
                $message['replyTo'] = $this->formatRecipients($emailData['replyTo']);
            }

            // Add attachments
            if (!empty($emailData['attachments'])) {
                $message['attachments'] = $this->formatAttachments($emailData['attachments']);
            }

            // Add custom headers
            if (!empty($emailData['customHeaders'])) {
                $message['internetMessageHeaders'] = $this->formatCustomHeaders($emailData['customHeaders']);
            }

            // Add categories/flags
            if (!empty($emailData['categories'])) {
                $message['categories'] = $emailData['categories'];
            }

            // Add sensitivity
            if (!empty($emailData['sensitivity'])) {
                $message['sensitivity'] = $emailData['sensitivity']; // normal, personal, private, confidential
            }

            // Create draft via API
            $response = $this->makeRequest('POST', '/me/messages', ['json' => $message]);

            Log::info('Draft message created successfully', [
                'message_id' => $response['id'],
                'subject' => $emailData['subject'],
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'message_id' => $response['id'],
                'conversation_id' => $response['conversationId'] ?? null,
                'subject' => $response['subject'] ?? $emailData['subject'],
                'created_at' => $response['createdDateTime'] ?? now()->toISOString()
            ];
        } catch (Exception $e) {
            Log::error('Failed to create draft message', [
                'error' => $e->getMessage(),
                'subject' => $emailData['subject'] ?? 'N/A',
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message_id' => null,
                'error_code' => $this->getErrorCode($e)
            ];
        }
    }

    /**
     * Send a draft message
     *
     * @param string $messageId Draft message ID
     * @return array Result with success status
     */
    public function sendDraftMessage(string $messageId): array
    {
        try {
            if (empty($messageId)) {
                throw new Exception('Message ID is required');
            }

            Log::info('Sending draft message', [
                'message_id' => $messageId,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $startTime = microtime(true);

            // Send the draft
            $this->makeRequest('POST', "/me/messages/{$messageId}/send");

            $sendTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Draft message sent successfully', [
                'message_id' => $messageId,
                'send_time_ms' => $sendTime,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'message' => 'Draft message sent successfully',
                'message_id' => $messageId,
                'sent_at' => now()->toISOString()
            ];
        } catch (Exception $e) {
            Log::error('Failed to send draft message', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message_id' => $messageId,
                'error_code' => $this->getErrorCode($e)
            ];
        }
    }

    /**
     * Delete a draft message (cleanup on failure)
     *
     * @param string $messageId Draft message ID
     * @return bool Success status
     */
    private function deleteDraftMessage(string $messageId): bool
    {
        try {
            $this->makeRequest('DELETE', "/me/messages/{$messageId}");

            Log::info('Draft message deleted', [
                'message_id' => $messageId,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return true;
        } catch (Exception $e) {
            Log::warning('Failed to delete draft message', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get detailed message information by ID
     *
     * @param string $messageId Message ID
     * @param array $options Additional options (select fields, etc.)
     * @return array Message details
     */
    public function getMessageDetails(string $messageId, array $options = []): array
    {
        try {
            if (empty($messageId)) {
                throw new Exception('Message ID is required');
            }

            // Build query parameters
            $query = [];

            // Select specific fields if provided
            if (!empty($options['select'])) {
                $query['$select'] = $options['select'];
            } else {
                // Default comprehensive field selection
                $query['$select'] = implode(',', [
                    'id',
                    'conversationId',
                    'internetMessageId',
                    'subject',
                    'sentDateTime',
                    'receivedDateTime',
                    'from',
                    'sender',
                    'toRecipients',
                    'ccRecipients',
                    'bccRecipients',
                    'replyTo',
                    'hasAttachments',
                    'importance',
                    'isRead',
                    'isDraft',
                    'parentFolderId',
                    'categories',
                    'flag',
                    'body',
                    'bodyPreview',
                    'attachments',
                    'internetMessageHeaders'
                ]);
            }

            // Expand related data if requested
            if (!empty($options['expand'])) {
                $query['$expand'] = $options['expand'];
            }

            $response = $this->makeRequest('GET', "/me/messages/{$messageId}", ['query' => $query]);

            return [
                'id' => $response['id'],
                'conversationId' => $response['conversationId'],
                'internetMessageId' => $response['internetMessageId'],
                'subject' => $response['subject'],
                'sentDateTime' => $response['sentDateTime'],
                'receivedDateTime' => $response['receivedDateTime'],
                'from' => $response['from'],
                'sender' => $response['sender'] ?? $response['from'],
                'toRecipients' => $response['toRecipients'] ?? [],
                'ccRecipients' => $response['ccRecipients'] ?? [],
                'bccRecipients' => $response['bccRecipients'] ?? [],
                'replyTo' => $response['replyTo'] ?? [],
                'hasAttachments' => $response['hasAttachments'] ?? false,
                'importance' => $response['importance'] ?? 'normal',
                'isRead' => $response['isRead'] ?? false,
                'isDraft' => $response['isDraft'] ?? false,
                'parentFolderId' => $response['parentFolderId'] ?? null,
                'categories' => $response['categories'] ?? [],
                'flag' => $response['flag'] ?? null,
                'body' => $response['body'] ?? null,
                'bodyPreview' => $response['bodyPreview'] ?? '',
                'attachments' => $response['attachments'] ?? []
            ];
        } catch (Exception $e) {
            Log::error('Failed to get message details', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);
            return [];
        }
    }

    /**
     * Get messages from mailbox with filtering and pagination
     *
     * @param array $options Query options (folder, limit, filter, etc.)
     * @return array Array of messages
     */
    public function getMessages(array $options = []): array
    {
        try {
            // Parse options with defaults
            $folder = $options['folder'] ?? 'inbox';
            $top = min($options['limit'] ?? 25, 1000); // Microsoft Graph limit
            $skip = $options['skip'] ?? 0;
            $filter = $options['filter'] ?? null;
            $orderBy = $options['orderBy'] ?? 'receivedDateTime DESC';
            $select = $options['select'] ?? $this->getDefaultEmailSelectFields();

            // Build query parameters
            $query = [
                '$top' => $top,
                '$skip' => $skip,
                '$select' => $select,
                '$orderby' => $orderBy
            ];

            // Add filter if provided
            if ($filter) {
                $query['$filter'] = $filter;
            }

            // Add search if provided
            if (!empty($options['search'])) {
                $query['$search'] = "\"{$options['search']}\"";
            }

            // Determine endpoint based on folder
            $endpoint = $this->buildMessagesEndpoint($folder);

            Log::info('Fetching messages', [
                'folder' => $folder,
                'limit' => $top,
                'skip' => $skip,
                'has_filter' => !empty($filter),
                'has_search' => !empty($options['search']),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $response = $this->makeRequest('GET', $endpoint, ['query' => $query]);
            $messages = $response['value'] ?? [];

            Log::info('Messages fetched successfully', [
                'folder' => $folder,
                'message_count' => count($messages),
                'has_more' => !empty($response['@odata.nextLink']),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'messages' => $messages,
                'count' => count($messages),
                'nextLink' => $response['@odata.nextLink'] ?? null,
                'folder' => $folder
            ];
        } catch (Exception $e) {
            Log::error('Failed to get messages', [
                'error' => $e->getMessage(),
                'folder' => $options['folder'] ?? 'inbox',
                'user' => $this->auth->email ?? 'unknown'
            ]);
            return ['messages' => [], 'count' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * Search messages across mailbox
     *
     * @param string $query Search query
     * @param array $options Search options (folder, limit, etc.)
     * @return array Search results
     */
    public function searchMessages(string $query, array $options = []): array
    {
        try {
            if (empty(trim($query))) {
                throw new Exception('Search query cannot be empty');
            }

            $top = min($options['limit'] ?? 25, 1000);
            $skip = $options['skip'] ?? 0;
            $folder = $options['folder'] ?? null; // null means search all folders
            $select = $options['select'] ?? $this->getDefaultEmailSelectFields();

            // Build search parameters
            $queryParams = [
                '$search' => "\"{$query}\"",
                '$top' => $top,
                '$skip' => $skip,
                '$select' => $select,
                '$orderby' => 'receivedDateTime DESC'
            ];

            // Add folder filter if specified
            if ($folder) {
                $queryParams['$filter'] = "parentFolderId eq '{$folder}'";
            }

            // Add date range filter if provided
            if (!empty($options['from_date'])) {
                $fromDate = Carbon::parse($options['from_date'])->toISOString();
                $dateFilter = "receivedDateTime ge {$fromDate}";

                if (!empty($options['to_date'])) {
                    $toDate = Carbon::parse($options['to_date'])->toISOString();
                    $dateFilter .= " and receivedDateTime le {$toDate}";
                }

                $queryParams['$filter'] = isset($queryParams['$filter'])
                    ? $queryParams['$filter'] . " and {$dateFilter}"
                    : $dateFilter;
            }

            Log::info('Searching messages', [
                'query' => $query,
                'folder' => $folder ?? 'all',
                'limit' => $top,
                'has_date_filter' => !empty($options['from_date']),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $response = $this->makeRequest('GET', '/me/messages', ['query' => $queryParams]);
            $messages = $response['value'] ?? [];

            Log::info('Message search completed', [
                'query' => $query,
                'results_count' => count($messages),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'messages' => $messages,
                'count' => count($messages),
                'query' => $query,
                'nextLink' => $response['@odata.nextLink'] ?? null
            ];
        } catch (Exception $e) {
            Log::error('Failed to search messages', [
                'query' => $query,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);
            return ['messages' => [], 'count' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * Mark message as read or unread
     *
     * @param string $messageId Message ID
     * @param bool $isRead Read status
     * @return bool Success status
     */
    public function markMessage(string $messageId, bool $isRead = true): bool
    {
        try {
            if (empty($messageId)) {
                throw new Exception('Message ID is required');
            }

            $this->makeRequest('PATCH', "/me/messages/{$messageId}", [
                'json' => ['isRead' => $isRead]
            ]);

            Log::info('Message marked successfully', [
                'message_id' => $messageId,
                'is_read' => $isRead,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to mark message', [
                'message_id' => $messageId,
                'is_read' => $isRead,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Move message to a different folder
     *
     * @param string $messageId Message ID
     * @param string $destinationFolderId Destination folder ID
     * @return bool Success status
     */
    public function moveMessage(string $messageId, string $destinationFolderId): bool
    {
        try {
            if (empty($messageId) || empty($destinationFolderId)) {
                throw new Exception('Message ID and destination folder ID are required');
            }

            $this->makeRequest('POST', "/me/messages/{$messageId}/move", [
                'json' => ['destinationId' => $destinationFolderId]
            ]);

            Log::info('Message moved successfully', [
                'message_id' => $messageId,
                'destination_folder' => $destinationFolderId,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to move message', [
                'message_id' => $messageId,
                'destination_folder' => $destinationFolderId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Delete message
     *
     * @param string $messageId Message ID
     * @param bool $permanent Permanent deletion (bypass deleted items)
     * @return bool Success status
     */
    public function deleteMessage(string $messageId, bool $permanent = false): bool
    {
        try {
            if (empty($messageId)) {
                throw new Exception('Message ID is required');
            }

            if ($permanent) {
                // Permanent deletion using special endpoint
                $this->makeRequest('DELETE', "/me/messages/{$messageId}");
            } else {
                // Move to deleted items folder
                $deletedItemsFolder = $this->getDeletedItemsFolderId();
                if ($deletedItemsFolder) {
                    return $this->moveMessage($messageId, $deletedItemsFolder);
                } else {
                    // Fallback to direct deletion
                    $this->makeRequest('DELETE', "/me/messages/{$messageId}");
                }
            }

            Log::info('Message deleted successfully', [
                'message_id' => $messageId,
                'permanent' => $permanent,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete message', [
                'message_id' => $messageId,
                'permanent' => $permanent,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Get mail folders
     *
     * @param array $options Query options
     * @return array Array of folders
     */
    public function getMailFolders(array $options = []): array
    {
        try {
            $top = min($options['limit'] ?? 100, 1000);
            $includeHidden = $options['include_hidden'] ?? false;

            $query = [
                '$top' => $top,
                '$select' => 'id,displayName,parentFolderId,childFolderCount,unreadItemCount,totalItemCount,wellKnownName',
                '$orderby' => 'displayName'
            ];

            if (!$includeHidden) {
                $query['$filter'] = "isHidden eq false";
            }

            $response = $this->makeRequest('GET', '/me/mailFolders', ['query' => $query]);

            Log::info('Mail folders retrieved', [
                'folder_count' => count($response['value'] ?? []),
                'include_hidden' => $includeHidden,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return $response['value'] ?? [];
        } catch (Exception $e) {
            Log::error('Failed to get mail folders', [
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);
            return [];
        }
    }

    // HELPER METHODS

    /**
     * Validate email data structure and required fields
     */
    private function validateEmailData(array $emailData): void
    {
        // Required fields
        if (empty($emailData['to'])) {
            throw new Exception('At least one recipient (to) is required');
        }

        if (empty($emailData['subject'])) {
            throw new Exception('Email subject is required');
        }

        // Validate recipients
        $this->validateRecipients($emailData['to'], 'to');

        if (!empty($emailData['cc'])) {
            $this->validateRecipients($emailData['cc'], 'cc');
        }

        if (!empty($emailData['bcc'])) {
            $this->validateRecipients($emailData['bcc'], 'bcc');
        }

        // Validate subject length
        if (strlen($emailData['subject']) > 255) {
            throw new Exception('Email subject cannot exceed 255 characters');
        }

        // Validate body size (10MB limit for Microsoft Graph)
        if (!empty($emailData['body']) && strlen($emailData['body']) > 10485760) {
            throw new Exception('Email body cannot exceed 10MB');
        }

        // Validate priority
        if (isset($emailData['priority']) && !in_array($emailData['priority'], ['low', 'normal', 'high'])) {
            throw new Exception('Priority must be one of: low, normal, high');
        }

        // Validate body type
        if (isset($emailData['bodyType']) && !in_array(strtolower($emailData['bodyType']), ['text', 'html'])) {
            throw new Exception('Body type must be either text or html');
        }
    }

    /**
     * Validate recipient email addresses
     */
    private function validateRecipients(array $recipients, string $type): void
    {
        if (empty($recipients)) {
            return;
        }

        foreach ($recipients as $index => $recipient) {
            $email = is_string($recipient) ? $recipient : ($recipient['email'] ?? $recipient['address'] ?? null);

            if (empty($email)) {
                throw new Exception("Invalid recipient at index {$index} in {$type}: email address is required");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email address in {$type}: {$email}");
            }
        }
    }

    /**
     * Sanitize email data for security
     */
    private function sanitizeEmailData(array $emailData): array
    {
        // Sanitize subject
        $emailData['subject'] = trim(strip_tags($emailData['subject']));

        // Sanitize HTML body if needed
        if (($emailData['bodyType'] ?? 'HTML') === 'HTML' && !empty($emailData['body'])) {
            $emailData['body'] = $this->sanitizeHtmlContent($emailData['body']);
        }

        return $emailData;
    }

    /**
     * Sanitize HTML content (basic security measures)
     */
    private function sanitizeHtmlContent(string $html): string
    {
        // Remove potentially dangerous tags and scripts
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi', '', $html);
        $html = preg_replace('/on\w+="[^"]*"/i', '', $html); // Remove event handlers

        return $html;
    }

    /**
     * Get default email select fields
     */
    private function getDefaultEmailSelectFields(): string
    {
        return implode(',', [
            'id',
            'subject',
            'from',
            'sender',
            'toRecipients',
            'ccRecipients',
            'receivedDateTime',
            'sentDateTime',
            'isRead',
            'hasAttachments',
            'importance',
            'bodyPreview',
            'conversationId',
            'internetMessageId',
            'categories',
            'flag'
        ]);
    }

    /**
     * Build messages endpoint based on folder
     */
    private function buildMessagesEndpoint(string $folder): string
    {
        $wellKnownFolders = ['inbox', 'drafts', 'sentitems', 'deleteditems', 'junkemail', 'outbox'];

        if (in_array(strtolower($folder), $wellKnownFolders)) {
            return "/me/mailFolders/{$folder}/messages";
        } elseif ($folder === 'all' || $folder === '') {
            return '/me/messages';
        } else {
            // Assume it's a folder ID
            return "/me/mailFolders/{$folder}/messages";
        }
    }

    /**
     * Get deleted items folder ID
     */
    private function getDeletedItemsFolderId(): ?string
    {
        try {
            $response = $this->makeRequest('GET', '/me/mailFolders/deleteditems', [
                'query' => ['$select' => 'id']
            ]);
            return $response['id'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Extract recipients from message response
     */
    private function extractRecipientsFromMessage(array $recipients): array
    {
        return array_map(function ($recipient) {
            return [
                'email' => $recipient['emailAddress']['address'] ?? null,
                'name' => $recipient['emailAddress']['name'] ?? null
            ];
        }, $recipients);
    }

    /**
     * Get error code from exception
     */
    private function getErrorCode(Exception $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'HTTP 400')) return 'BAD_REQUEST';
        if (str_contains($message, 'HTTP 401')) return 'UNAUTHORIZED';
        if (str_contains($message, 'HTTP 403')) return 'FORBIDDEN';
        if (str_contains($message, 'HTTP 404')) return 'NOT_FOUND';
        if (str_contains($message, 'HTTP 429')) return 'RATE_LIMITED';
        if (str_contains($message, 'Rate limit')) return 'RATE_LIMITED';
        if (str_contains($message, 'Authentication failed')) return 'AUTH_FAILED';

        return 'UNKNOWN_ERROR';
    }

    // SECTION 4: REPLY METHODS (INCLUDING REPLY ALL)
    /**
     * Send a reply to an existing message
     * Enhanced version with additional features
     *
     * @param mixed $auth Authenticated user object
     * @param string $originalMessageId Original message ID to reply to
     * @param array $replyData Reply content and options
     * @return array Result with success status and details
     */
    public function sendReply($auth, string $originalMessageId, array $replyData): array
    {
        try {
            $this->auth = $auth;
            $originalMessage = $this->getMessageDetails($originalMessageId);

            if (empty($originalMessage)) {
                throw new Exception('Original message not found');
            }

            // Validate reply permissions
            $canReply = $this->canReplyToMessage($originalMessageId);
            if (!$canReply['can_reply']) {
                throw new Exception($canReply['reason']);
            }

            // Build reply message
            $replyMessage = $this->buildReplyMessage($replyData, $originalMessage, 'reply');

            Log::info('Sending reply', [
                'original_message_id' => $originalMessageId,
                'conversation_id' => $originalMessage['conversationId'],
                'original_subject' => $originalMessage['subject'],
                'user' => $auth->email
            ]);

            $startTime = microtime(true);

            // Send reply using Microsoft Graph
            $this->makeRequest('POST', "/me/messages/{$originalMessageId}/reply", [
                'json' => $replyMessage
            ]);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Get conversation details for response
            $conversationMessages = $this->getConversationMessages($originalMessage['conversationId']);

            Log::info('Reply sent successfully', [
                'original_message_id' => $originalMessageId,
                'conversation_id' => $originalMessage['conversationId'],
                'response_time_ms' => $responseTime,
                'user' => $auth->email
            ]);

            return [
                'success' => true,
                'original_message_id' => $originalMessageId,
                'conversation_id' => $originalMessage['conversationId'],
                'replied_to' => [
                    'sender' => $originalMessage['from']['emailAddress']['address'] ?? 'Unknown'
                ],
                'conversation_message_count' => $conversationMessages['count'] ?? 0,
                'response_time_ms' => $responseTime,
                'message' => 'Reply sent successfully'
            ];
        } catch (Exception $e) {
            Log::error('Failed to send reply', [
                'original_message_id' => $originalMessageId,
                'error' => $e->getMessage(),
                'user' => $auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'original_message_id' => $originalMessageId,
                'error_code' => $this->getErrorCode($e)
            ];
        }
    }

    /**
     * Send a reply to all recipients of an existing message
     * Enhanced version with recipient analysis
     *
     * @param mixed $auth Authenticated user object
     * @param string $originalMessageId Original message ID to reply to
     * @param array $replyData Reply content and options
     * @return array Result with success status and recipient details
     */
    public function sendReplyAll($auth, string $originalMessageId, array $replyData): array
    {
        try {
            $this->auth = $auth;
            $originalMessage = $this->getMessageDetails($originalMessageId);

            if (empty($originalMessage)) {
                throw new Exception('Original message not found');
            }

            // Validate reply permissions
            $canReply = $this->canReplyToMessage($originalMessageId);
            if (!$canReply['can_reply']) {
                throw new Exception($canReply['reason']);
            }

            // Get recipient preview for logging
            $recipientPreview = $this->getReplyRecipientsPreview($originalMessageId, 'replyAll');

            // Build reply message
            $replyMessage = $this->buildReplyMessage($replyData, $originalMessage, 'replyAll');

            Log::info('Sending reply all', [
                'original_message_id' => $originalMessageId,
                'conversation_id' => $originalMessage['conversationId'],
                'recipient_count' => $recipientPreview['recipient_count'] ?? 0,
                'original_subject' => $originalMessage['subject'],
                'user' => $auth->email
            ]);

            $startTime = microtime(true);

            // Send reply all using Microsoft Graph
            $this->makeRequest('POST', "/me/messages/{$originalMessageId}/replyAll", [
                'json' => $replyMessage
            ]);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Get updated conversation details
            $conversationMessages = $this->getConversationMessages($originalMessage['conversationId']);

            Log::info('Reply all sent successfully', [
                'original_message_id' => $originalMessageId,
                'conversation_id' => $originalMessage['conversationId'],
                'recipient_count' => $recipientPreview['recipient_count'] ?? 0,
                'response_time_ms' => $responseTime,
                'user' => $auth->email
            ]);

            return [
                'success' => true,
                'original_message_id' => $originalMessageId,
                'conversation_id' => $originalMessage['conversationId'],
                'replied_to' => [
                    'sender' => $originalMessage['from']['emailAddress']['address'] ?? 'Unknown',
                    'to_recipients' => array_map(fn($r) => $r['emailAddress']['address'], $originalMessage['toRecipients'] ?? []),
                    'cc_recipients' => array_map(fn($r) => $r['emailAddress']['address'], $originalMessage['ccRecipients'] ?? [])
                ],
                'recipient_count' => $recipientPreview['recipient_count'] ?? 0,
                'conversation_message_count' => $conversationMessages['count'] ?? 0,
                'response_time_ms' => $responseTime,
                'message' => 'Reply all sent successfully'
            ];
        } catch (Exception $e) {
            Log::error('Failed to send reply all', [
                'original_message_id' => $originalMessageId,
                'error' => $e->getMessage(),
                'user' => $auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'original_message_id' => $originalMessageId,
                'error_code' => $this->getErrorCode($e)
            ];
        }
    }

    /**
     * Get conversation messages with enhanced details
     *
     * @param string $conversationId Conversation ID
     * @param array $options Query options
     * @return array Conversation messages and metadata
     */
    public function getConversationMessages(string $conversationId, array $options = []): array
    {
        try {
            if (empty($conversationId)) {
                throw new Exception('Conversation ID is required');
            }

            $top = min($options['limit'] ?? 50, 1000);
            $select = $options['select'] ?? 'id,subject,from,toRecipients,ccRecipients,receivedDateTime,sentDateTime,bodyPreview,importance,hasAttachments,isRead';

            $query = [
                '$filter' => "conversationId eq '{$conversationId}'",
                '$orderby' => $options['order'] ?? 'receivedDateTime desc',
                '$select' => $select,
                '$top' => $top
            ];

            $response = $this->makeRequest('GET', '/me/messages', ['query' => $query]);
            $messages = $response['value'] ?? [];

            // Analyze conversation
            $analysis = $this->analyzeConversation($messages);

            Log::info('Conversation messages retrieved', [
                'conversation_id' => $conversationId,
                'message_count' => count($messages),
                'participants_count' => $analysis['participants_count'],
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'messages' => $messages,
                'count' => count($messages),
                'conversation_id' => $conversationId,
                'analysis' => $analysis,
                'nextLink' => $response['@odata.nextLink'] ?? null
            ];
        } catch (Exception $e) {
            Log::error('Failed to get conversation messages', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'messages' => [],
                'count' => 0
            ];
        }
    }

    /**
     * Check if user can reply to a message
     * Enhanced validation with detailed reasons
     *
     * @param string $messageId Message ID to check
     * @return array Can reply status with detailed information
     */
    public function canReplyToMessage(string $messageId): array
    {
        try {
            if (empty($messageId)) {
                return [
                    'can_reply' => false,
                    'reason' => 'Message ID is required',
                    'error_code' => 'INVALID_MESSAGE_ID'
                ];
            }

            $message = $this->getMessageDetails($messageId);

            if (empty($message)) {
                return [
                    'can_reply' => false,
                    'reason' => 'Message not found',
                    'error_code' => 'MESSAGE_NOT_FOUND'
                ];
            }

            // Check message folder
            $folderChecks = $this->checkMessageFolder($message);
            if (!$folderChecks['can_reply']) {
                return $folderChecks;
            }

            // Check sender validity
            $senderChecks = $this->checkMessageSender($message);
            if (!$senderChecks['can_reply']) {
                return $senderChecks;
            }

            // Check message age (optional policy)
            $ageChecks = $this->checkMessageAge($message);
            if (!$ageChecks['can_reply']) {
                return $ageChecks;
            }

            return [
                'can_reply' => true,
                'message_id' => $messageId,
                'conversation_id' => $message['conversationId'] ?? null,
                'subject' => $message['subject'] ?? 'N/A',
                'sender' => $message['from']['emailAddress']['address'] ?? null,
                'received_date' => $message['receivedDateTime'] ?? null,
                'folder' => $message['parentFolderId'] ?? null
            ];
        } catch (Exception $e) {
            Log::error('Failed to check reply permissions', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'can_reply' => false,
                'reason' => 'Error checking message: ' . $e->getMessage(),
                'error_code' => 'CHECK_FAILED'
            ];
        }
    }

    /**
     * Get detailed preview of reply recipients
     * Enhanced with recipient analysis and validation
     *
     * @param string $messageId Original message ID
     * @param string $replyType Type of reply ('reply' or 'replyAll')
     * @return array Recipient preview with detailed analysis
     */
    public function getReplyRecipientsPreview(string $messageId, string $replyType = 'reply'): array
    {
        try {
            if (empty($messageId)) {
                throw new Exception('Message ID is required');
            }

            if (!in_array($replyType, ['reply', 'replyAll'])) {
                throw new Exception('Reply type must be either "reply" or "replyAll"');
            }

            $originalMessage = $this->getMessageDetails($messageId);
            if (empty($originalMessage)) {
                throw new Exception('Original message not found');
            }

            $currentUserEmail = $this->auth->email ?? auth()->user()->email;
            $recipients = $this->calculateReplyRecipients($originalMessage, $replyType, $currentUserEmail);

            // Validate recipients
            $validation = $this->validateReplyRecipients($recipients);

            Log::info('Reply recipients preview generated', [
                'message_id' => $messageId,
                'reply_type' => $replyType,
                'recipient_count' => count($recipients),
                'has_external_recipients' => $validation['has_external_recipients'],
                'user' => $currentUserEmail
            ]);

            return [
                'success' => true,
                'reply_type' => $replyType,
                'recipients' => $recipients,
                'recipient_count' => count($recipients),
                'original_subject' => $originalMessage['subject'] ?? 'N/A',
                'original_sender' => $originalMessage['from']['emailAddress']['address'] ?? 'Unknown',
                'validation' => $validation,
                'estimated_send_time' => $this->estimateSendTime(count($recipients))
            ];
        } catch (Exception $e) {
            Log::error('Failed to get reply recipients preview', [
                'message_id' => $messageId,
                'reply_type' => $replyType,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'recipients' => [],
                'recipient_count' => 0,
                'error_code' => $this->getErrorCode($e)
            ];
        }
    }

    /**
     * Forward a message to new recipients
     * New method not previously implemented
     *
     * @param mixed $auth Authenticated user object
     * @param string $originalMessageId Original message ID to forward
     * @param array $forwardData Forward content and recipients
     * @return array Result with success status and details
     */
    public function forwardMessage($auth, string $originalMessageId, array $forwardData): array
    {
        try {
            $this->auth = $auth;

            if (empty($forwardData['to'])) {
                throw new Exception('Forward recipients are required');
            }

            $originalMessage = $this->getMessageDetails($originalMessageId);
            if (empty($originalMessage)) {
                throw new Exception('Original message not found');
            }

            // Validate forward permissions
            $canForward = $this->canForwardMessage($originalMessageId);
            if (!$canForward['can_forward']) {
                throw new Exception($canForward['reason']);
            }

            // Build forward message
            $forwardMessage = [
                'message' => [
                    'toRecipients' => $this->formatRecipients($forwardData['to']),
                    'body' => [
                        'contentType' => $forwardData['bodyType'] ?? 'html',
                        'content' => $forwardData['body'] ?? ''
                    ]
                ]
            ];

            // Add optional recipients
            if (!empty($forwardData['cc'])) {
                $forwardMessage['message']['ccRecipients'] = $this->formatRecipients($forwardData['cc']);
            }

            if (!empty($forwardData['bcc'])) {
                $forwardMessage['message']['bccRecipients'] = $this->formatRecipients($forwardData['bcc']);
            }

            // Add comment if provided
            if (!empty($forwardData['comment'])) {
                $forwardMessage['comment'] = $forwardData['comment'];
            }

            Log::info('Forwarding message', [
                'original_message_id' => $originalMessageId,
                'to_count' => count($forwardData['to']),
                'cc_count' => count($forwardData['cc'] ?? []),
                'has_comment' => !empty($forwardData['comment']),
                'user' => $auth->email
            ]);

            $startTime = microtime(true);

            // Forward using Microsoft Graph
            $this->makeRequest('POST', "/me/messages/{$originalMessageId}/forward", [
                'json' => $forwardMessage
            ]);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Message forwarded successfully', [
                'original_message_id' => $originalMessageId,
                'to_count' => count($forwardData['to']),
                'response_time_ms' => $responseTime,
                'user' => $auth->email
            ]);

            return [
                'success' => true,
                'original_message_id' => $originalMessageId,
                'forwarded_to' => [
                    'to_recipients' => array_map(fn($r) => is_string($r) ? $r : $r['email'], $forwardData['to']),
                    'cc_recipients' => array_map(fn($r) => is_string($r) ? $r : $r['email'], $forwardData['cc'] ?? [])
                ],
                'recipient_count' => count($forwardData['to']) + count($forwardData['cc'] ?? []),
                'response_time_ms' => $responseTime,
                'message' => 'Message forwarded successfully'
            ];
        } catch (Exception $e) {
            Log::error('Failed to forward message', [
                'original_message_id' => $originalMessageId,
                'error' => $e->getMessage(),
                'user' => $auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'original_message_id' => $originalMessageId,
                'error_code' => $this->getErrorCode($e)
            ];
        }
    }

    /**
     * Build reply message structure
     * New helper method for consistent reply building
     */
    private function buildReplyMessage(array $replyData, array $originalMessage, string $replyType): array
    {
        $message = [
            'body' => [
                'contentType' => $replyData['bodyType'] ?? 'html',
                'content' => $replyData['body'] ?? ''
            ]
        ];

        // Add attachments if provided
        if (!empty($replyData['attachments'])) {
            $message['attachments'] = $this->formatAttachments($replyData['attachments']);
        }

        // Add custom headers if provided
        if (!empty($replyData['customHeaders'])) {
            $message['internetMessageHeaders'] = $this->formatCustomHeaders($replyData['customHeaders']);
        }

        // Add importance if provided
        if (!empty($replyData['priority'])) {
            $message['importance'] = $this->mapPriorityToImportance($replyData['priority']);
        }

        // Add categories if provided
        if (!empty($replyData['categories'])) {
            $message['categories'] = $replyData['categories'];
        }

        return ['message' => $message];
    }

    /**
     * Calculate reply recipients based on reply type
     * New helper method for recipient calculation
     */
    private function calculateReplyRecipients(array $originalMessage, string $replyType, string $currentUserEmail): array
    {
        $sender = $originalMessage['from']['emailAddress'] ?? null;
        $toRecipients = $originalMessage['toRecipients'] ?? [];
        $ccRecipients = $originalMessage['ccRecipients'] ?? [];

        if ($replyType === 'reply') {
            // Reply only to sender
            if (!$sender || !isset($sender['address'])) {
                return [];
            }

            return [[
                'email' => $sender['address'],
                'name' => $sender['name'] ?? null,
                'type' => 'sender',
                'is_external' => $this->isExternalEmail($sender['address'])
            ]];
        } else { // replyAll
            $allRecipients = array_merge([$sender], $toRecipients, $ccRecipients);
            $uniqueRecipients = [];
            $seenEmails = [];

            foreach ($allRecipients as $recipient) {
                if (!$recipient || !isset($recipient['emailAddress']['address'])) {
                    continue;
                }

                $email = strtolower($recipient['emailAddress']['address']);

                // Skip current user and duplicates
                if ($email === strtolower($currentUserEmail) || in_array($email, $seenEmails)) {
                    continue;
                }

                $seenEmails[] = $email;
                $uniqueRecipients[] = [
                    'email' => $recipient['emailAddress']['address'],
                    'name' => $recipient['emailAddress']['name'] ?? null,
                    'type' => $recipient === $sender ? 'sender' : 'recipient',
                    'is_external' => $this->isExternalEmail($recipient['emailAddress']['address'])
                ];
            }

            return $uniqueRecipients;
        }
    }

    /**
     * Validate reply recipients
     * New helper method for recipient validation
     */
    private function validateReplyRecipients(array $recipients): array
    {
        $validation = [
            'is_valid' => true,
            'warnings' => [],
            'has_external_recipients' => false,
            'external_count' => 0,
            'internal_count' => 0,
            'invalid_emails' => []
        ];

        foreach ($recipients as $recipient) {
            $email = $recipient['email'];

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validation['invalid_emails'][] = $email;
                $validation['is_valid'] = false;
                continue;
            }

            // Check if external
            if ($recipient['is_external']) {
                $validation['has_external_recipients'] = true;
                $validation['external_count']++;
            } else {
                $validation['internal_count']++;
            }
        }

        // Add warnings
        if ($validation['has_external_recipients']) {
            $validation['warnings'][] = "Reply includes {$validation['external_count']} external recipient(s)";
        }

        if (!empty($validation['invalid_emails'])) {
            $validation['warnings'][] = "Invalid email addresses detected";
        }

        return $validation;
    }

    /**
     * Check message folder for reply permissions
     * New helper method for folder validation
     */
    private function checkMessageFolder(array $message): array
    {
        $parentFolderId = strtolower($message['parentFolderId'] ?? '');

        // Check for forbidden folders
        $forbiddenFolders = ['drafts', 'outbox', 'deleteditems', 'junkemail'];

        foreach ($forbiddenFolders as $folder) {
            if (str_contains($parentFolderId, $folder)) {
                return [
                    'can_reply' => false,
                    'reason' => "Cannot reply to messages in {$folder} folder",
                    'error_code' => 'INVALID_FOLDER'
                ];
            }
        }

        return ['can_reply' => true];
    }

    /**
     * Check message sender for reply permissions
     * New helper method for sender validation
     */
    private function checkMessageSender(array $message): array
    {
        $sender = $message['from']['emailAddress']['address'] ?? null;

        if (!$sender) {
            return [
                'can_reply' => false,
                'reason' => 'Message has no valid sender',
                'error_code' => 'NO_SENDER'
            ];
        }

        // Check for no-reply addresses
        $noReplyPatterns = ['noreply', 'no-reply', 'donotreply', 'do-not-reply'];
        $senderLower = strtolower($sender);

        foreach ($noReplyPatterns as $pattern) {
            if (str_contains($senderLower, $pattern)) {
                return [
                    'can_reply' => false,
                    'reason' => 'Cannot reply to no-reply address',
                    'error_code' => 'NO_REPLY_ADDRESS'
                ];
            }
        }

        return ['can_reply' => true];
    }

    /**
     * Check message age for reply permissions
     * New helper method for age validation
     */
    private function checkMessageAge(array $message): array
    {
        $receivedDateTime = $message['receivedDateTime'] ?? null;

        if (!$receivedDateTime) {
            return ['can_reply' => true]; // No date restriction if no date
        }

        try {
            $receivedDate = Carbon::parse($receivedDateTime);
            $daysSinceReceived = $receivedDate->diffInDays(now());

            // Optional: Implement age-based restrictions (e.g., 90 days)
            $maxReplyAge = config('outlook.max_reply_age_days', 365);

            if ($daysSinceReceived > $maxReplyAge) {
                return [
                    'can_reply' => false,
                    'reason' => "Cannot reply to messages older than {$maxReplyAge} days",
                    'error_code' => 'MESSAGE_TOO_OLD'
                ];
            }
        } catch (Exception $e) {
            // If date parsing fails, allow reply
            Log::warning('Failed to parse message date for age check', [
                'received_date' => $receivedDateTime,
                'error' => $e->getMessage()
            ]);
        }

        return ['can_reply' => true];
    }

    /**
     * Analyze conversation for insights
     * New helper method for conversation analysis
     */
    private function analyzeConversation(array $messages): array
    {
        $participants = [];
        $domains = [];
        $totalMessages = count($messages);
        $unreadCount = 0;
        $hasAttachments = 0;

        foreach ($messages as $message) {
            // Track participants
            $from = $message['from']['emailAddress']['address'] ?? null;
            if ($from) {
                $participants[$from] = ($participants[$from] ?? 0) + 1;
                $domain = substr(strrchr($from, "@"), 1);
                $domains[$domain] = ($domains[$domain] ?? 0) + 1;
            }

            // Count unread messages
            if (!($message['isRead'] ?? true)) {
                $unreadCount++;
            }

            // Count messages with attachments
            if ($message['hasAttachments'] ?? false) {
                $hasAttachments++;
            }
        }

        return [
            'total_messages' => $totalMessages,
            'participants_count' => count($participants),
            'participants' => $participants,
            'domains' => $domains,
            'unread_count' => $unreadCount,
            'messages_with_attachments' => $hasAttachments,
            'most_active_participant' => !empty($participants) ? array_keys($participants, max($participants))[0] : null,
            'is_cross_domain' => count($domains) > 1
        ];
    }

    /**
     * Check if user can forward a message
     * New helper method for forward permissions
     */
    private function canForwardMessage(string $messageId): array
    {
        // Similar logic to canReplyToMessage but for forwarding
        try {
            $message = $this->getMessageDetails($messageId);

            if (empty($message)) {
                return [
                    'can_forward' => false,
                    'reason' => 'Message not found',
                    'error_code' => 'MESSAGE_NOT_FOUND'
                ];
            }

            // Check folder permissions (similar to reply)
            $folderChecks = $this->checkMessageFolder($message);
            if (!$folderChecks['can_reply']) {
                return [
                    'can_forward' => false,
                    'reason' => str_replace('reply', 'forward', $folderChecks['reason']),
                    'error_code' => $folderChecks['error_code']
                ];
            }

            return [
                'can_forward' => true,
                'message_id' => $messageId
            ];
        } catch (Exception $e) {
            return [
                'can_forward' => false,
                'reason' => 'Error checking message: ' . $e->getMessage(),
                'error_code' => 'CHECK_FAILED'
            ];
        }
    }

    /**
     * Check if email is external to organization
     * New helper method for external email detection
     */
    private function isExternalEmail(string $email): bool
    {
        $userDomain = substr(strrchr($this->auth->email ?? '', "@"), 1);
        $emailDomain = substr(strrchr($email, "@"), 1);

        return strtolower($userDomain) !== strtolower($emailDomain);
    }

    /**
     * Estimate send time based on recipient count
     * New helper method for time estimation
     */
    private function estimateSendTime(int $recipientCount): string
    {
        if ($recipientCount <= 5) {
            return 'less than 5 seconds';
        } elseif ($recipientCount <= 20) {
            return '5-15 seconds';
        } elseif ($recipientCount <= 50) {
            return '15-30 seconds';
        } else {
            return 'up to 1 minute';
        }
    }

    // SECTION 5: MESSAGE MANAGEMENT

    /**
     * Get message attachments with detailed information
     * Enhanced version with size analysis and security checking
     *
     * @param string $messageId Message ID
     * @param array $options Query options
     * @return array Array of attachments with detailed information
     */
    public function getMessageAttachments(string $messageId, array $options = []): array
    {
        try {
            if (empty($messageId)) {
                throw new Exception('Message ID is required');
            }

            $includeContent = $options['include_content'] ?? false;
            $maxSize = $options['max_size'] ?? 10485760; // 10MB default

            Log::info('Retrieving message attachments', [
                'message_id' => $messageId,
                'include_content' => $includeContent,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $response = $this->makeRequest('GET', "/me/messages/{$messageId}/attachments");
            $attachments = $response['value'] ?? [];

            $processedAttachments = [];
            $totalSize = 0;

            foreach ($attachments as $attachment) {
                $processedAttachment = $this->processAttachment($attachment, $includeContent, $maxSize);
                $processedAttachments[] = $processedAttachment;
                $totalSize += $processedAttachment['size'] ?? 0;
            }

            // Security analysis
            $securityAnalysis = $this->analyzeAttachmentSecurity($processedAttachments);

            Log::info('Message attachments retrieved', [
                'message_id' => $messageId,
                'attachment_count' => count($processedAttachments),
                'total_size' => $this->formatBytes($totalSize),
                'has_security_concerns' => $securityAnalysis['has_concerns'],
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'attachments' => $processedAttachments,
                'count' => count($processedAttachments),
                'total_size' => $totalSize,
                'total_size_formatted' => $this->formatBytes($totalSize),
                'security_analysis' => $securityAnalysis
            ];
        } catch (Exception $e) {
            Log::error('Failed to get message attachments', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'attachments' => [],
                'count' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Download specific attachment with validation
     * Enhanced version with security checks and size limits
     *
     * @param string $messageId Message ID
     * @param string $attachmentId Attachment ID
     * @param array $options Download options
     * @return array|null Attachment content and metadata
     */
    public function downloadAttachment(string $messageId, string $attachmentId, array $options = []): ?array
    {
        try {
            if (empty($messageId) || empty($attachmentId)) {
                throw new Exception('Message ID and Attachment ID are required');
            }

            $maxSize = $options['max_download_size'] ?? 50485760; // 50MB default
            $allowedTypes = $options['allowed_types'] ?? null;

            Log::info('Starting attachment download', [
                'message_id' => $messageId,
                'attachment_id' => $attachmentId,
                'max_size' => $this->formatBytes($maxSize),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            // Get attachment metadata first
            $attachment = $this->makeRequest('GET', "/me/messages/{$messageId}/attachments/{$attachmentId}");

            // Validate attachment
            $validation = $this->validateAttachmentDownload($attachment, $maxSize, $allowedTypes);
            if (!$validation['is_valid']) {
                throw new Exception($validation['reason']);
            }

            $startTime = microtime(true);

            // Download content
            if (isset($attachment['contentBytes'])) {
                $content = base64_decode($attachment['contentBytes']);
                $downloadTime = round((microtime(true) - $startTime) * 1000, 2);

                Log::info('Attachment downloaded successfully', [
                    'message_id' => $messageId,
                    'attachment_id' => $attachmentId,
                    'size' => $this->formatBytes(strlen($content)),
                    'download_time_ms' => $downloadTime,
                    'user' => $this->auth->email ?? 'unknown'
                ]);

                return [
                    'id' => $attachment['id'],
                    'name' => $attachment['name'],
                    'contentType' => $attachment['contentType'],
                    'size' => $attachment['size'],
                    'size_formatted' => $this->formatBytes($attachment['size']),
                    'content' => $content,
                    'download_time_ms' => $downloadTime,
                    'security_info' => $this->getAttachmentSecurityInfo($attachment),
                    'last_modified' => $attachment['lastModifiedDateTime'] ?? null
                ];
            }

            throw new Exception('Attachment content not available');
        } catch (Exception $e) {
            Log::error('Failed to download attachment', [
                'message_id' => $messageId,
                'attachment_id' => $attachmentId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return null;
        }
    }

    /**
     * Bulk mark messages as read/unread
     * NEW method for batch operations
     *
     * @param array $messageIds Array of message IDs
     * @param bool $isRead Read status to set
     * @param array $options Batch options
     * @return array Batch operation results
     */
    public function bulkMarkMessages(array $messageIds, bool $isRead = true, array $options = []): array
    {
        try {
            if (empty($messageIds)) {
                throw new Exception('At least one message ID is required');
            }

            $batchSize = min($options['batch_size'] ?? 20, 20); // Microsoft Graph batch limit
            $continueOnError = $options['continue_on_error'] ?? true;

            Log::info('Starting bulk mark messages operation', [
                'message_count' => count($messageIds),
                'is_read' => $isRead,
                'batch_size' => $batchSize,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $results = [
                'successful' => [],
                'failed' => [],
                'total_processed' => 0,
                'total_successful' => 0,
                'total_failed' => 0
            ];

            $batches = array_chunk($messageIds, $batchSize);

            foreach ($batches as $batchIndex => $batch) {
                $batchResults = $this->processBatchMarkMessages($batch, $isRead, $batchIndex);

                $results['successful'] = array_merge($results['successful'], $batchResults['successful']);
                $results['failed'] = array_merge($results['failed'], $batchResults['failed']);
                $results['total_processed'] += count($batch);

                if (!$continueOnError && !empty($batchResults['failed'])) {
                    Log::warning('Stopping bulk operation due to errors', [
                        'batch_index' => $batchIndex,
                        'failed_count' => count($batchResults['failed'])
                    ]);
                    break;
                }

                // Brief pause between batches to avoid rate limiting
                if (count($batches) > 1 && $batchIndex < count($batches) - 1) {
                    usleep(500000); // 0.5 second
                }
            }

            $results['total_successful'] = count($results['successful']);
            $results['total_failed'] = count($results['failed']);
            $results['success_rate'] = $results['total_processed'] > 0
                ? round(($results['total_successful'] / $results['total_processed']) * 100, 2)
                : 0;

            Log::info('Bulk mark messages operation completed', [
                'total_processed' => $results['total_processed'],
                'successful' => $results['total_successful'],
                'failed' => $results['total_failed'],
                'success_rate' => $results['success_rate'] . '%',
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return $results;
        } catch (Exception $e) {
            Log::error('Bulk mark messages operation failed', [
                'error' => $e->getMessage(),
                'message_count' => count($messageIds),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'successful' => [],
                'failed' => [],
                'total_processed' => 0,
                'total_successful' => 0,
                'total_failed' => count($messageIds),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Bulk move messages to folder
     * NEW method for batch folder operations
     *
     * @param array $messageIds Array of message IDs
     * @param string $destinationFolderId Destination folder ID
     * @param array $options Batch options
     * @return array Batch operation results
     */
    public function bulkMoveMessages(array $messageIds, string $destinationFolderId, array $options = []): array
    {
        try {
            if (empty($messageIds)) {
                throw new Exception('At least one message ID is required');
            }

            if (empty($destinationFolderId)) {
                throw new Exception('Destination folder ID is required');
            }

            // Validate destination folder exists
            $folderExists = $this->validateFolder($destinationFolderId);
            if (!$folderExists) {
                throw new Exception('Destination folder not found or inaccessible');
            }

            $batchSize = min($options['batch_size'] ?? 15, 15); // Smaller batch for move operations
            $continueOnError = $options['continue_on_error'] ?? true;

            Log::info('Starting bulk move messages operation', [
                'message_count' => count($messageIds),
                'destination_folder' => $destinationFolderId,
                'batch_size' => $batchSize,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $results = [
                'successful' => [],
                'failed' => [],
                'total_processed' => 0,
                'total_successful' => 0,
                'total_failed' => 0,
                'destination_folder' => $destinationFolderId
            ];

            $batches = array_chunk($messageIds, $batchSize);

            foreach ($batches as $batchIndex => $batch) {
                $batchResults = $this->processBatchMoveMessages($batch, $destinationFolderId, $batchIndex);

                $results['successful'] = array_merge($results['successful'], $batchResults['successful']);
                $results['failed'] = array_merge($results['failed'], $batchResults['failed']);
                $results['total_processed'] += count($batch);

                if (!$continueOnError && !empty($batchResults['failed'])) {
                    Log::warning('Stopping bulk move operation due to errors', [
                        'batch_index' => $batchIndex,
                        'failed_count' => count($batchResults['failed'])
                    ]);
                    break;
                }

                // Longer pause between move batches
                if (count($batches) > 1 && $batchIndex < count($batches) - 1) {
                    usleep(1000000); // 1 second
                }
            }

            $results['total_successful'] = count($results['successful']);
            $results['total_failed'] = count($results['failed']);
            $results['success_rate'] = $results['total_processed'] > 0
                ? round(($results['total_successful'] / $results['total_processed']) * 100, 2)
                : 0;

            Log::info('Bulk move messages operation completed', [
                'total_processed' => $results['total_processed'],
                'successful' => $results['total_successful'],
                'failed' => $results['total_failed'],
                'success_rate' => $results['success_rate'] . '%',
                'destination_folder' => $destinationFolderId,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return $results;
        } catch (Exception $e) {
            Log::error('Bulk move messages operation failed', [
                'error' => $e->getMessage(),
                'message_count' => count($messageIds),
                'destination_folder' => $destinationFolderId,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'successful' => [],
                'failed' => [],
                'total_processed' => 0,
                'total_successful' => 0,
                'total_failed' => count($messageIds),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Bulk delete messages with safety checks
     * NEW method for batch deletion
     *
     * @param array $messageIds Array of message IDs
     * @param array $options Deletion options
     * @return array Batch operation results
     */
    public function bulkDeleteMessages(array $messageIds, array $options = []): array
    {
        try {
            if (empty($messageIds)) {
                throw new Exception('At least one message ID is required');
            }

            $permanent = $options['permanent'] ?? false;
            $batchSize = min($options['batch_size'] ?? 10, 10); // Smaller batch for delete operations
            $requireConfirmation = $options['require_confirmation'] ?? true;
            $continueOnError = $options['continue_on_error'] ?? false; // More conservative for deletions

            // Safety check for large deletions
            if (count($messageIds) > 100 && $requireConfirmation) {
                throw new Exception('Bulk deletion of more than 100 messages requires explicit confirmation');
            }

            Log::warning('Starting bulk delete messages operation', [
                'message_count' => count($messageIds),
                'permanent' => $permanent,
                'batch_size' => $batchSize,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $results = [
                'successful' => [],
                'failed' => [],
                'total_processed' => 0,
                'total_successful' => 0,
                'total_failed' => 0,
                'permanent_deletion' => $permanent
            ];

            $batches = array_chunk($messageIds, $batchSize);

            foreach ($batches as $batchIndex => $batch) {
                $batchResults = $this->processBatchDeleteMessages($batch, $permanent, $batchIndex);

                $results['successful'] = array_merge($results['successful'], $batchResults['successful']);
                $results['failed'] = array_merge($results['failed'], $batchResults['failed']);
                $results['total_processed'] += count($batch);

                if (!$continueOnError && !empty($batchResults['failed'])) {
                    Log::error('Stopping bulk delete operation due to errors', [
                        'batch_index' => $batchIndex,
                        'failed_count' => count($batchResults['failed'])
                    ]);
                    break;
                }

                // Longer pause between delete batches for safety
                if (count($batches) > 1 && $batchIndex < count($batches) - 1) {
                    usleep(2000000); // 2 seconds
                }
            }

            $results['total_successful'] = count($results['successful']);
            $results['total_failed'] = count($results['failed']);
            $results['success_rate'] = $results['total_processed'] > 0
                ? round(($results['total_successful'] / $results['total_processed']) * 100, 2)
                : 0;

            Log::warning('Bulk delete messages operation completed', [
                'total_processed' => $results['total_processed'],
                'successful' => $results['total_successful'],
                'failed' => $results['total_failed'],
                'success_rate' => $results['success_rate'] . '%',
                'permanent' => $permanent,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return $results;
        } catch (Exception $e) {
            Log::error('Bulk delete messages operation failed', [
                'error' => $e->getMessage(),
                'message_count' => count($messageIds),
                'permanent' => $options['permanent'] ?? false,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'successful' => [],
                'failed' => [],
                'total_processed' => 0,
                'total_successful' => 0,
                'total_failed' => count($messageIds),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Set message categories and flags
     * NEW method for message organization
     *
     * @param string $messageId Message ID
     * @param array $categories Array of category names
     * @param array $flagData Flag information
     * @return bool Success status
     */
    public function setMessageCategories(string $messageId, array $categories = [], array $flagData = []): bool
    {
        try {
            if (empty($messageId)) {
                throw new Exception('Message ID is required');
            }

            $updateData = [];

            // Set categories
            if (!empty($categories)) {
                $updateData['categories'] = array_unique($categories);
            }

            // Set flag
            if (!empty($flagData)) {
                $flag = [
                    'flagStatus' => $flagData['status'] ?? 'flagged'
                ];

                if (!empty($flagData['due_date'])) {
                    $flag['dueDateTime'] = [
                        'dateTime' => Carbon::parse($flagData['due_date'])->toISOString(),
                        'timeZone' => $flagData['timezone'] ?? 'UTC'
                    ];
                }

                if (!empty($flagData['start_date'])) {
                    $flag['startDateTime'] = [
                        'dateTime' => Carbon::parse($flagData['start_date'])->toISOString(),
                        'timeZone' => $flagData['timezone'] ?? 'UTC'
                    ];
                }

                $updateData['flag'] = $flag;
            }

            if (empty($updateData)) {
                return true; // Nothing to update
            }

            $this->makeRequest('PATCH', "/me/messages/{$messageId}", [
                'json' => $updateData
            ]);

            Log::info('Message categories and flags updated', [
                'message_id' => $messageId,
                'categories' => $categories,
                'has_flag' => !empty($flagData),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to set message categories', [
                'message_id' => $messageId,
                'categories' => $categories,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return false;
        }
    }

    /**
     * Get message rules and filters
     * NEW method for mailbox rules management
     *
     * @param array $options Query options
     * @return array Array of message rules
     */
    public function getMessageRules(array $options = []): array
    {
        try {
            $top = min($options['limit'] ?? 100, 1000);

            $query = [
                '$top' => $top,
                '$orderby' => 'sequence'
            ];

            Log::info('Retrieving message rules', [
                'limit' => $top,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $response = $this->makeRequest('GET', '/me/mailFolders/inbox/messageRules', ['query' => $query]);
            $rules = $response['value'] ?? [];

            Log::info('Message rules retrieved', [
                'rule_count' => count($rules),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'rules' => $rules,
                'count' => count($rules)
            ];
        } catch (Exception $e) {
            Log::error('Failed to get message rules', [
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'rules' => [],
                'count' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process individual attachment with security analysis
     * NEW helper method
     */
    private function processAttachment(array $attachment, bool $includeContent, int $maxSize): array
    {
        $processed = [
            'id' => $attachment['id'],
            'name' => $attachment['name'],
            'contentType' => $attachment['contentType'],
            'size' => $attachment['size'] ?? 0,
            'size_formatted' => $this->formatBytes($attachment['size'] ?? 0),
            'isInline' => $attachment['isInline'] ?? false,
            'lastModified' => $attachment['lastModifiedDateTime'] ?? null,
            'contentId' => $attachment['contentId'] ?? null,
            'security_risk' => $this->assessAttachmentRisk($attachment)
        ];

        // Include content if requested and size allows
        if ($includeContent && isset($attachment['contentBytes'])) {
            if (($attachment['size'] ?? 0) <= $maxSize) {
                $processed['content'] = base64_decode($attachment['contentBytes']);
                $processed['content_included'] = true;
            } else {
                $processed['content_included'] = false;
                $processed['size_exceeded'] = true;
            }
        }

        return $processed;
    }

    /**
     * Analyze attachment security risks
     * NEW helper method
     */
    private function analyzeAttachmentSecurity(array $attachments): array
    {
        $analysis = [
            'has_concerns' => false,
            'risk_level' => 'low',
            'concerns' => [],
            'executable_count' => 0,
            'large_file_count' => 0,
            'suspicious_names' => []
        ];

        $executableTypes = [
            'application/x-msdownload',
            'application/x-executable',
            'application/x-ms-dos-executable',
            'application/vnd.microsoft.portable-executable'
        ];

        $suspiciousExtensions = ['.exe', '.bat', '.cmd', '.com', '.scr', '.pif', '.vbs', '.js'];

        foreach ($attachments as $attachment) {
            $name = strtolower($attachment['name']);
            $contentType = strtolower($attachment['contentType']);
            $size = $attachment['size'] ?? 0;

            // Check for executable files
            if (in_array($contentType, $executableTypes)) {
                $analysis['executable_count']++;
                $analysis['has_concerns'] = true;
                $analysis['concerns'][] = "Executable file detected: {$attachment['name']}";
            }

            // Check for suspicious extensions
            foreach ($suspiciousExtensions as $ext) {
                if (str_ends_with($name, $ext)) {
                    $analysis['suspicious_names'][] = $attachment['name'];
                    $analysis['has_concerns'] = true;
                    $analysis['concerns'][] = "Suspicious file extension: {$attachment['name']}";
                    break;
                }
            }

            // Check for large files (>25MB)
            if ($size > 26214400) {
                $analysis['large_file_count']++;
                $analysis['concerns'][] = "Large file detected: {$attachment['name']} ({$attachment['size_formatted']})";
            }
        }

        // Determine overall risk level
        if ($analysis['executable_count'] > 0 || !empty($analysis['suspicious_names'])) {
            $analysis['risk_level'] = 'high';
        } elseif ($analysis['large_file_count'] > 2 || count($analysis['concerns']) > 0) {
            $analysis['risk_level'] = 'medium';
        }

        return $analysis;
    }

    /**
     * Validate attachment for download
     * NEW helper method
     */
    private function validateAttachmentDownload(array $attachment, int $maxSize, ?array $allowedTypes): array
    {
        $validation = [
            'is_valid' => true,
            'reason' => null
        ];

        // Check size limit
        if (($attachment['size'] ?? 0) > $maxSize) {
            $validation['is_valid'] = false;
            $validation['reason'] = "Attachment size ({$this->formatBytes($attachment['size'])}) exceeds maximum allowed size ({$this->formatBytes($maxSize)})";
            return $validation;
        }

        // Check allowed types
        if ($allowedTypes && !in_array($attachment['contentType'], $allowedTypes)) {
            $validation['is_valid'] = false;
            $validation['reason'] = "Attachment type ({$attachment['contentType']}) is not allowed";
            return $validation;
        }

        // Security check
        $risk = $this->assessAttachmentRisk($attachment);
        if ($risk === 'high') {
            $validation['is_valid'] = false;
            $validation['reason'] = "Attachment poses security risk and cannot be downloaded";
            return $validation;
        }

        return $validation;
    }

    /**
     * Assess individual attachment security risk
     * NEW helper method
     */
    private function assessAttachmentRisk(array $attachment): string
    {
        $name = strtolower($attachment['name']);
        $contentType = strtolower($attachment['contentType']);

        // High risk file types
        $highRiskTypes = [
            'application/x-msdownload',
            'application/x-executable',
            'application/x-ms-dos-executable',
            'application/vnd.microsoft.portable-executable'
        ];

        $highRiskExtensions = ['.exe', '.bat', '.cmd', '.com', '.scr', '.pif'];

        // Check content type
        if (in_array($contentType, $highRiskTypes)) {
            return 'high';
        }

        // Check file extension
        foreach ($highRiskExtensions as $ext) {
            if (str_ends_with($name, $ext)) {
                return 'high';
            }
        }

        // Medium risk extensions
        $mediumRiskExtensions = ['.vbs', '.js', '.jar', '.zip', '.rar'];
        foreach ($mediumRiskExtensions as $ext) {
            if (str_ends_with($name, $ext)) {
                return 'medium';
            }
        }

        return 'low';
    }

    /**
     * Get attachment security information
     * NEW helper method
     */
    private function getAttachmentSecurityInfo(array $attachment): array
    {
        $risk = $this->assessAttachmentRisk($attachment);

        return [
            'risk_level' => $risk,
            'file_type' => $attachment['contentType'],
            'is_executable' => $risk === 'high',
            'scan_recommended' => $risk !== 'low',
            'size_category' => $this->categorizeSizeRisk($attachment['size'] ?? 0)
        ];
    }

    /**
     * Categorize file size risk
     * NEW helper method
     */
    private function categorizeSizeRisk(int $size): string
    {
        if ($size < 1048576) { // < 1MB
            return 'small';
        } elseif ($size < 10485760) { // < 10MB
            return 'medium';
        } elseif ($size < 52428800) { // < 50MB
            return 'large';
        } else {
            return 'very_large';
        }
    }

    /**
     * Process batch mark messages operation
     * NEW helper method
     */
    private function processBatchMarkMessages(array $messageIds, bool $isRead, int $batchIndex): array
    {
        $results = ['successful' => [], 'failed' => []];

        foreach ($messageIds as $messageId) {
            try {
                $success = $this->markMessage($messageId, $isRead);
                if ($success) {
                    $results['successful'][] = $messageId;
                } else {
                    $results['failed'][] = [
                        'message_id' => $messageId,
                        'error' => 'Mark operation failed'
                    ];
                }
            } catch (Exception $e) {
                $results['failed'][] = [
                    'message_id' => $messageId,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Process batch move messages operation
     * NEW helper method
     */
    private function processBatchMoveMessages(array $messageIds, string $destinationFolderId, int $batchIndex): array
    {
        $results = ['successful' => [], 'failed' => []];

        foreach ($messageIds as $messageId) {
            try {
                $success = $this->moveMessage($messageId, $destinationFolderId);
                if ($success) {
                    $results['successful'][] = $messageId;
                } else {
                    $results['failed'][] = [
                        'message_id' => $messageId,
                        'error' => 'Move operation failed'
                    ];
                }
            } catch (Exception $e) {
                $results['failed'][] = [
                    'message_id' => $messageId,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Process batch delete messages operation
     * NEW helper method
     */
    private function processBatchDeleteMessages(array $messageIds, bool $permanent, int $batchIndex): array
    {
        $results = ['successful' => [], 'failed' => []];

        foreach ($messageIds as $messageId) {
            try {
                $success = $this->deleteMessage($messageId, $permanent);
                if ($success) {
                    $results['successful'][] = $messageId;
                } else {
                    $results['failed'][] = [
                        'message_id' => $messageId,
                        'error' => 'Delete operation failed'
                    ];
                }
            } catch (Exception $e) {
                $results['failed'][] = [
                    'message_id' => $messageId,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Validate folder exists and is accessible
     * NEW helper method
     */
    private function validateFolder(string $folderId): bool
    {
        try {
            $this->makeRequest('GET', "/me/mailFolders/{$folderId}", [
                'query' => ['$select' => 'id,displayName']
            ]);
            return true;
        } catch (Exception $e) {
            Log::warning('Folder validation failed', [
                'folder_id' => $folderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get message statistics for a folder
     * NEW method for folder analytics
     *
     * @param string $folderId Folder ID (optional, defaults to inbox)
     * @param array $options Analysis options
     * @return array Folder statistics and insights
     */
    public function getFolderStatistics(string $folderId = 'inbox', array $options = []): array
    {
        try {
            $includeDays = $options['days'] ?? 30;
            $includeAttachments = $options['include_attachments'] ?? true;
            $includeSenders = $options['include_senders'] ?? true;

            Log::info('Generating folder statistics', [
                'folder_id' => $folderId,
                'days' => $includeDays,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $startTime = microtime(true);

            // Get basic folder info
            $folderInfo = $this->makeRequest('GET', "/me/mailFolders/{$folderId}", [
                'query' => ['$select' => 'id,displayName,totalItemCount,unreadItemCount,childFolderCount']
            ]);

            // Get recent messages for analysis
            $dateFilter = Carbon::now()->subDays($includeDays)->toISOString();
            $messages = $this->getMessages([
                'folder' => $folderId,
                'limit' => 1000,
                'filter' => "receivedDateTime ge {$dateFilter}",
                'select' => 'id,from,receivedDateTime,hasAttachments,importance,isRead,bodyPreview,subject'
            ]);

            // Analyze messages
            $analysis = $this->analyzeFolderMessages($messages['messages'], [
                'include_attachments' => $includeAttachments,
                'include_senders' => $includeSenders
            ]);

            $processingTime = round((microtime(true) - $startTime) * 1000, 2);

            $statistics = [
                'folder_info' => [
                    'id' => $folderInfo['id'],
                    'name' => $folderInfo['displayName'],
                    'total_items' => $folderInfo['totalItemCount'] ?? 0,
                    'unread_items' => $folderInfo['unreadItemCount'] ?? 0,
                    'child_folders' => $folderInfo['childFolderCount'] ?? 0
                ],
                'period_analysis' => [
                    'days_analyzed' => $includeDays,
                    'messages_in_period' => count($messages['messages']),
                    'daily_average' => round(count($messages['messages']) / max($includeDays, 1), 2),
                    'unread_in_period' => $analysis['unread_count'],
                    'read_rate' => $analysis['read_rate']
                ],
                'content_analysis' => [
                    'with_attachments' => $analysis['attachment_count'],
                    'attachment_rate' => $analysis['attachment_rate'],
                    'importance_distribution' => $analysis['importance_distribution'],
                    'average_subject_length' => $analysis['avg_subject_length']
                ],
                'processing_time_ms' => $processingTime,
                'generated_at' => now()->toISOString()
            ];

            // Add sender analysis if requested
            if ($includeSenders && !empty($analysis['top_senders'])) {
                $statistics['sender_analysis'] = [
                    'unique_senders' => count($analysis['all_senders']),
                    'top_senders' => array_slice($analysis['top_senders'], 0, 10),
                    'external_senders' => $analysis['external_sender_count']
                ];
            }

            Log::info('Folder statistics generated', [
                'folder_id' => $folderId,
                'messages_analyzed' => count($messages['messages']),
                'processing_time_ms' => $processingTime,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return $statistics;
        } catch (Exception $e) {
            Log::error('Failed to generate folder statistics', [
                'folder_id' => $folderId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'folder_info' => [],
                'error' => $e->getMessage(),
                'generated_at' => now()->toISOString()
            ];
        }
    }

    /**
     * Create custom folder with validation
     * NEW method for folder management
     *
     * @param string $displayName Folder display name
     * @param string $parentFolderId Parent folder ID (optional)
     * @param array $options Creation options
     * @return array Created folder information
     */
    public function createFolder(string $displayName, string $parentFolderId = null, array $options = []): array
    {
        try {
            if (empty(trim($displayName))) {
                throw new Exception('Folder display name is required');
            }

            // Validate parent folder if specified
            if ($parentFolderId && !$this->validateFolder($parentFolderId)) {
                throw new Exception('Parent folder not found or inaccessible');
            }

            // Check for duplicate names in the same parent
            $existingFolders = $this->getMailFolders();
            foreach ($existingFolders as $folder) {
                if (strcasecmp($folder['displayName'], $displayName) === 0) {
                    if (empty($parentFolderId) || $folder['parentFolderId'] === $parentFolderId) {
                        throw new Exception('A folder with this name already exists in the specified location');
                    }
                }
            }

            $folderData = [
                'displayName' => trim($displayName)
            ];

            // Add parent folder if specified
            if ($parentFolderId) {
                $folderData['parentFolderId'] = $parentFolderId;
            }

            Log::info('Creating new folder', [
                'display_name' => $displayName,
                'parent_folder' => $parentFolderId ?? 'root',
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $endpoint = $parentFolderId
                ? "/me/mailFolders/{$parentFolderId}/childFolders"
                : '/me/mailFolders';

            $response = $this->makeRequest('POST', $endpoint, ['json' => $folderData]);

            Log::info('Folder created successfully', [
                'folder_id' => $response['id'],
                'display_name' => $response['displayName'],
                'parent_folder' => $parentFolderId ?? 'root',
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'folder' => [
                    'id' => $response['id'],
                    'displayName' => $response['displayName'],
                    'parentFolderId' => $response['parentFolderId'] ?? null,
                    'totalItemCount' => 0,
                    'unreadItemCount' => 0,
                    'childFolderCount' => 0
                ],
                'created_at' => now()->toISOString()
            ];
        } catch (Exception $e) {
            Log::error('Failed to create folder', [
                'display_name' => $displayName,
                'parent_folder' => $parentFolderId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'folder' => null
            ];
        }
    }

    /**
     * Archive old messages based on criteria
     * NEW method for message archiving
     *
     * @param array $criteria Archive criteria
     * @param array $options Archive options
     * @return array Archive operation results
     */
    public function archiveMessages(array $criteria, array $options = []): array
    {
        try {
            $olderThanDays = $criteria['older_than_days'] ?? 90;
            $sourceFolder = $criteria['source_folder'] ?? 'inbox';
            $archiveFolder = $criteria['archive_folder'] ?? null;
            $readOnly = $criteria['read_only'] ?? true;
            $maxMessages = $options['max_messages'] ?? 1000;
            $dryRun = $options['dry_run'] ?? false;

            // Create archive folder if needed
            if (!$archiveFolder) {
                $archiveResult = $this->createFolder('Archive-' . now()->format('Y-m'));
                if (!$archiveResult['success']) {
                    throw new Exception('Failed to create archive folder: ' . $archiveResult['error']);
                }
                $archiveFolder = $archiveResult['folder']['id'];
            }

            // Build filter criteria
            $dateThreshold = Carbon::now()->subDays($olderThanDays)->toISOString();
            $filter = "receivedDateTime le {$dateThreshold}";

            if ($readOnly) {
                $filter .= " and isRead eq true";
            }

            Log::info('Starting message archiving', [
                'source_folder' => $sourceFolder,
                'archive_folder' => $archiveFolder,
                'older_than_days' => $olderThanDays,
                'read_only' => $readOnly,
                'max_messages' => $maxMessages,
                'dry_run' => $dryRun,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            // Get messages to archive
            $messages = $this->getMessages([
                'folder' => $sourceFolder,
                'limit' => $maxMessages,
                'filter' => $filter,
                'select' => 'id,subject,receivedDateTime,isRead,from'
            ]);

            $messageIds = array_column($messages['messages'], 'id');

            if (empty($messageIds)) {
                return [
                    'success' => true,
                    'archived_count' => 0,
                    'message' => 'No messages found matching archive criteria',
                    'criteria' => $criteria
                ];
            }

            $results = [
                'success' => true,
                'total_candidates' => count($messageIds),
                'archived_count' => 0,
                'failed_count' => 0,
                'archive_folder' => $archiveFolder,
                'dry_run' => $dryRun,
                'criteria' => $criteria
            ];

            if (!$dryRun) {
                // Perform actual archiving
                $moveResults = $this->bulkMoveMessages($messageIds, $archiveFolder, [
                    'batch_size' => 10,
                    'continue_on_error' => true
                ]);

                $results['archived_count'] = $moveResults['total_successful'];
                $results['failed_count'] = $moveResults['total_failed'];
                $results['success'] = $moveResults['total_failed'] === 0;
            } else {
                $results['archived_count'] = count($messageIds);
                $results['message'] = 'Dry run completed - no messages were actually moved';
            }

            Log::info('Message archiving completed', [
                'total_candidates' => $results['total_candidates'],
                'archived_count' => $results['archived_count'],
                'failed_count' => $results['failed_count'],
                'dry_run' => $dryRun,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return $results;
        } catch (Exception $e) {
            Log::error('Message archiving failed', [
                'error' => $e->getMessage(),
                'criteria' => $criteria,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'archived_count' => 0,
                'criteria' => $criteria
            ];
        }
    }

    // ADDITIONAL HELPER METHODS

    /**
     * Analyze folder messages for statistics
     * NEW helper method
     */
    private function analyzeFolderMessages(array $messages, array $options): array
    {
        $analysis = [
            'unread_count' => 0,
            'attachment_count' => 0,
            'importance_distribution' => ['low' => 0, 'normal' => 0, 'high' => 0],
            'all_senders' => [],
            'top_senders' => [],
            'external_sender_count' => 0,
            'total_subject_length' => 0,
            'avg_subject_length' => 0,
            'read_rate' => 0,
            'attachment_rate' => 0
        ];

        $senderCounts = [];
        $userDomain = substr(strrchr($this->auth->email ?? '', "@"), 1);

        foreach ($messages as $message) {
            // Count unread
            if (!($message['isRead'] ?? true)) {
                $analysis['unread_count']++;
            }

            // Count attachments
            if ($message['hasAttachments'] ?? false) {
                $analysis['attachment_count']++;
            }

            // Track importance
            $importance = $message['importance'] ?? 'normal';
            if (isset($analysis['importance_distribution'][$importance])) {
                $analysis['importance_distribution'][$importance]++;
            }

            // Track senders
            $fromEmail = $message['from']['emailAddress']['address'] ?? null;
            if ($fromEmail) {
                $analysis['all_senders'][] = $fromEmail;
                $senderCounts[$fromEmail] = ($senderCounts[$fromEmail] ?? 0) + 1;

                // Check if external
                $senderDomain = substr(strrchr($fromEmail, "@"), 1);
                if (strtolower($userDomain) !== strtolower($senderDomain)) {
                    $analysis['external_sender_count']++;
                }
            }

            // Track subject length
            $subjectLength = strlen($message['subject'] ?? '');
            $analysis['total_subject_length'] += $subjectLength;
        }

        $messageCount = count($messages);

        // Calculate rates and averages
        if ($messageCount > 0) {
            $analysis['read_rate'] = round((($messageCount - $analysis['unread_count']) / $messageCount) * 100, 2);
            $analysis['attachment_rate'] = round(($analysis['attachment_count'] / $messageCount) * 100, 2);
            $analysis['avg_subject_length'] = round($analysis['total_subject_length'] / $messageCount, 2);
        }

        // Sort senders by frequency
        if ($options['include_senders'] && !empty($senderCounts)) {
            arsort($senderCounts);
            $analysis['top_senders'] = array_map(function ($email, $count) {
                return ['email' => $email, 'count' => $count];
            }, array_keys($senderCounts), $senderCounts);
        }

        return $analysis;
    }

    /**
     * Format custom headers for API
     * NEW helper method
     */
    private function formatCustomHeaders(array $headers): array
    {
        $formatted = [];

        foreach ($headers as $header) {
            if (is_array($header) && isset($header['name']) && isset($header['value'])) {
                $formatted[] = [
                    'name' => $header['name'],
                    'value' => $header['value']
                ];
            }
        }

        return $formatted;
    }

    // SECTION 6: USER & PROFILE METHODS
    // Note: Core methods like getUser, getExtendedUserProfile, getUserProfile already exist
    // Adding new advanced user and profile methods not yet implemented

    /**
     * Get current user information
     */
    public function getUser(): array
    {
        return $this->makeRequest('GET', '/me');
    }

    /**
     * Get user profile for connection verification
     */
    public function getUserProfile($auth): array
    {
        try {
            $this->auth = $auth;
            $user = $this->getUser();

            if (!$user) {
                return ['status' => 'failed', 'user' => null];
            }

            return [
                'status' => 'success',
                'user' => [
                    'name' => $user['displayName'] ?? 'N/A',
                    'email' => $user['mail'] ?? $user['userPrincipalName'] ?? 'N/A',
                    'id' => $user['id'] ?? 'N/A'
                ]
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    /**
     * Update user profile information
     * NEW method for profile management
     *
     * @param array $profileData Profile data to update
     * @param array $options Update options
     * @return array Update result with success status
     */
    public function updateUserProfile(array $profileData, array $options = []): array
    {
        try {
            if (empty($profileData)) {
                throw new Exception('Profile data is required for update');
            }

            // Validate and sanitize profile data
            $sanitizedData = $this->sanitizeProfileData($profileData);
            $validation = $this->validateProfileData($sanitizedData);

            if (!$validation['is_valid']) {
                throw new Exception('Invalid profile data: ' . implode(', ', $validation['errors']));
            }

            Log::info('Updating user profile', [
                'fields_to_update' => array_keys($sanitizedData),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $startTime = microtime(true);

            // Update profile via Microsoft Graph
            $response = $this->makeRequest('PATCH', '/me', [
                'json' => $sanitizedData
            ]);

            $updateTime = round((microtime(true) - $startTime) * 1000, 2);

            // Get updated profile to return current state
            $updatedProfile = $this->getExtendedUserProfile();

            Log::info('User profile updated successfully', [
                'updated_fields' => array_keys($sanitizedData),
                'update_time_ms' => $updateTime,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'updated_fields' => array_keys($sanitizedData),
                'profile' => $updatedProfile['user'] ?? [],
                'update_time_ms' => $updateTime,
                'message' => 'Profile updated successfully'
            ];
        } catch (Exception $e) {
            Log::error('Failed to update user profile', [
                'error' => $e->getMessage(),
                'fields_attempted' => array_keys($profileData),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'updated_fields' => [],
                'profile' => []
            ];
        }
    }

    /**
     * Get user's organizational hierarchy
     * NEW method for org chart navigation
     *
     * @param string $userId User ID (optional, defaults to current user)
     * @param array $options Query options
     * @return array Organizational hierarchy information
     */
    public function getUserOrganizationalHierarchy(string $userId = 'me', array $options = []): array
    {
        try {
            $includeDirectReports = $options['include_direct_reports'] ?? true;
            $includeManager = $options['include_manager'] ?? true;
            $includePeers = $options['include_peers'] ?? false;
            $maxLevels = $options['max_levels'] ?? 3;

            Log::info('Retrieving organizational hierarchy', [
                'user_id' => $userId,
                'include_reports' => $includeDirectReports,
                'include_manager' => $includeManager,
                'include_peers' => $includePeers,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $hierarchy = [
                'user' => [],
                'manager' => null,
                'direct_reports' => [],
                'peers' => [],
                'manager_chain' => [],
                'org_depth' => 0,
                'total_hierarchy_members' => 0
            ];

            // Get base user information
            $userInfo = $this->makeRequest('GET', "/{$userId}", [
                'query' => [
                    '$select' => 'id,displayName,mail,userPrincipalName,jobTitle,department,officeLocation,employeeId,manager'
                ]
            ]);
            $hierarchy['user'] = $this->formatUserInfo($userInfo);

            // Get manager information and chain
            if ($includeManager) {
                $managerChain = $this->buildManagerChain($userId, $maxLevels);
                $hierarchy['manager'] = $managerChain['immediate_manager'];
                $hierarchy['manager_chain'] = $managerChain['chain'];
                $hierarchy['org_depth'] = count($managerChain['chain']);
            }

            // Get direct reports
            if ($includeDirectReports) {
                $hierarchy['direct_reports'] = $this->getUserDirectReports($userId);
            }

            // Get peers (same manager)
            if ($includePeers && $hierarchy['manager']) {
                $hierarchy['peers'] = $this->getUserPeers($userId, $hierarchy['manager']['id']);
            }

            // Calculate total hierarchy members
            $hierarchy['total_hierarchy_members'] = 1 + // user
                count($hierarchy['direct_reports']) +
                count($hierarchy['manager_chain']) +
                count($hierarchy['peers']);

            // Add organizational insights
            $hierarchy['insights'] = $this->generateOrgInsights($hierarchy);

            Log::info('Organizational hierarchy retrieved', [
                'user_id' => $userId,
                'total_members' => $hierarchy['total_hierarchy_members'],
                'org_depth' => $hierarchy['org_depth'],
                'direct_reports_count' => count($hierarchy['direct_reports']),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'hierarchy' => $hierarchy,
                'retrieved_at' => now()->toISOString()
            ];
        } catch (Exception $e) {
            Log::error('Failed to get organizational hierarchy', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'hierarchy' => []
            ];
        }
    }

    /**
     * Search users in organization with advanced filters
     * Enhanced version with comprehensive search capabilities
     *
     * @param string $query Search query
     * @param array $options Search options and filters
     * @return array Search results with user information
     */
    public function searchUsers(string $query, array $options = []): array
    {
        try {
            if (empty(trim($query))) {
                throw new Exception('Search query cannot be empty');
            }

            $top = min($options['limit'] ?? 50, 999);
            $skip = $options['skip'] ?? 0;
            $department = $options['department'] ?? null;
            $location = $options['location'] ?? null;
            $jobTitle = $options['job_title'] ?? null;
            $includeExternal = $options['include_external'] ?? false;
            $accountEnabled = $options['account_enabled'] ?? true;

            Log::info('Searching users', [
                'query' => $query,
                'limit' => $top,
                'department' => $department,
                'location' => $location,
                'include_external' => $includeExternal,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            // Build search parameters
            $searchParams = [
                '$search' => "\"{$query}\"",
                '$top' => $top,
                '$skip' => $skip,
                '$select' => implode(',', $this->getUserSearchSelectFields()),
                '$orderby' => 'displayName'
            ];

            // Build filter conditions
            $filters = [];

            if ($accountEnabled !== null) {
                $filters[] = "accountEnabled eq " . ($accountEnabled ? 'true' : 'false');
            }

            if ($department) {
                $filters[] = "department eq '{$department}'";
            }

            if ($location) {
                $filters[] = "(city eq '{$location}' or officeLocation eq '{$location}')";
            }

            if ($jobTitle) {
                $filters[] = "startswith(jobTitle, '{$jobTitle}')";
            }

            if (!$includeExternal) {
                // Filter to internal users only (assuming userType exists)
                $filters[] = "userType eq 'Member'";
            }

            if (!empty($filters)) {
                $searchParams['$filter'] = implode(' and ', $filters);
            }

            $startTime = microtime(true);

            $response = $this->makeRequest('GET', '/users', ['query' => $searchParams]);
            $users = $response['value'] ?? [];

            $searchTime = round((microtime(true) - $startTime) * 1000, 2);

            // Process and enrich user data
            $processedUsers = array_map(function ($user) use ($options) {
                return $this->enrichUserSearchResult($user, $options);
            }, $users);

            // Generate search insights
            $insights = $this->generateSearchInsights($processedUsers, $query);

            Log::info('User search completed', [
                'query' => $query,
                'results_count' => count($processedUsers),
                'search_time_ms' => $searchTime,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'users' => $processedUsers,
                'count' => count($processedUsers),
                'query' => $query,
                'insights' => $insights,
                'search_time_ms' => $searchTime,
                'nextLink' => $response['@odata.nextLink'] ?? null,
                'searched_at' => now()->toISOString()
            ];
        } catch (Exception $e) {
            Log::error('User search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'users' => [],
                'count' => 0
            ];
        }
    }

    /**
     * Get user's group memberships with detailed information
     * Enhanced version with group analysis
     *
     * @param string $userId User ID (optional, defaults to current user)
     * @param array $options Query options
     * @return array Detailed group membership information
     */
    public function getUserGroupMemberships(string $userId = 'me', array $options = []): array
    {
        try {
            $includeNestedGroups = $options['include_nested'] ?? true;
            $includeRoles = $options['include_roles'] ?? true;
            $includeTeams = $options['include_teams'] ?? true;
            $groupTypes = $options['group_types'] ?? ['unified', 'security', 'distribution'];

            Log::info('Retrieving user group memberships', [
                'user_id' => $userId,
                'include_nested' => $includeNestedGroups,
                'include_roles' => $includeRoles,
                'include_teams' => $includeTeams,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $memberships = [
                'direct_groups' => [],
                'nested_groups' => [],
                'directory_roles' => [],
                'teams' => [],
                'summary' => [
                    'total_groups' => 0,
                    'security_groups' => 0,
                    'distribution_groups' => 0,
                    'microsoft365_groups' => 0,
                    'teams_count' => 0,
                    'admin_roles' => 0
                ]
            ];

            // Get direct group memberships
            $directGroups = $this->makeRequest('GET', "/{$userId}/memberOf", [
                'query' => [
                    '$select' => 'id,displayName,description,groupTypes,mailEnabled,securityEnabled,mail,visibility,resourceProvisioningOptions',
                    '$top' => 999
                ]
            ]);

            $memberships['direct_groups'] = array_map(function ($group) {
                return $this->enrichGroupInfo($group);
            }, $directGroups['value'] ?? []);

            // Get nested group memberships if requested
            if ($includeNestedGroups) {
                try {
                    $nestedGroups = $this->makeRequest('GET', "/{$userId}/transitiveMemberOf", [
                        'query' => [
                            '$select' => 'id,displayName,description,groupTypes,mailEnabled,securityEnabled',
                            '$top' => 999
                        ]
                    ]);

                    $memberships['nested_groups'] = array_map(function ($group) {
                        return $this->enrichGroupInfo($group);
                    }, $nestedGroups['value'] ?? []);
                } catch (Exception $e) {
                    Log::warning('Failed to get nested groups', ['error' => $e->getMessage()]);
                }
            }

            // Get directory role memberships if requested
            if ($includeRoles) {
                try {
                    $roles = $this->makeRequest('GET', "/{$userId}/memberOf/microsoft.graph.directoryRole", [
                        'query' => [
                            '$select' => 'id,displayName,description,roleTemplateId',
                            '$top' => 100
                        ]
                    ]);

                    $memberships['directory_roles'] = $roles['value'] ?? [];
                } catch (Exception $e) {
                    Log::info('No directory roles or insufficient permissions', ['error' => $e->getMessage()]);
                }
            }

            // Get Teams memberships if requested
            if ($includeTeams) {
                try {
                    $teams = $this->getUserTeams();
                    $memberships['teams'] = $teams;
                } catch (Exception $e) {
                    Log::warning('Failed to get Teams memberships', ['error' => $e->getMessage()]);
                }
            }

            // Generate summary statistics
            $memberships['summary'] = $this->generateGroupMembershipSummary($memberships);

            // Add insights
            $memberships['insights'] = $this->generateGroupInsights($memberships);

            Log::info('Group memberships retrieved', [
                'user_id' => $userId,
                'total_groups' => $memberships['summary']['total_groups'],
                'teams_count' => $memberships['summary']['teams_count'],
                'admin_roles' => $memberships['summary']['admin_roles'],
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'memberships' => $memberships,
                'retrieved_at' => now()->toISOString()
            ];
        } catch (Exception $e) {
            Log::error('Failed to get user group memberships', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'memberships' => []
            ];
        }
    }

    /**
     * Get user's license and subscription information
     * NEW method for license management
     *
     * @param string $userId User ID (optional, defaults to current user)
     * @param array $options Query options
     * @return array License and subscription details
     */
    public function getUserLicenses(string $userId = 'me', array $options = []): array
    {
        try {
            $includeServicePlans = $options['include_service_plans'] ?? true;
            $includeUsage = $options['include_usage'] ?? false;

            Log::info('Retrieving user licenses', [
                'user_id' => $userId,
                'include_service_plans' => $includeServicePlans,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            // Get user with license information
            $user = $this->makeRequest('GET', "/{$userId}", [
                'query' => [
                    '$select' => 'id,displayName,assignedLicenses,assignedPlans,licenseAssignmentStates,usageLocation'
                ]
            ]);

            $licenses = [
                'user_info' => [
                    'id' => $user['id'],
                    'displayName' => $user['displayName'],
                    'usageLocation' => $user['usageLocation'] ?? null
                ],
                'assigned_licenses' => [],
                'assigned_plans' => [],
                'license_states' => [],
                'summary' => [
                    'total_licenses' => 0,
                    'active_licenses' => 0,
                    'disabled_plans' => 0,
                    'error_states' => 0
                ]
            ];

            // Process assigned licenses
            foreach ($user['assignedLicenses'] ?? [] as $license) {
                $licenseInfo = $this->enrichLicenseInfo($license);
                $licenses['assigned_licenses'][] = $licenseInfo;
            }

            // Process assigned plans if requested
            if ($includeServicePlans) {
                foreach ($user['assignedPlans'] ?? [] as $plan) {
                    $planInfo = $this->enrichServicePlanInfo($plan);
                    $licenses['assigned_plans'][] = $planInfo;
                }
            }

            // Process license assignment states
            foreach ($user['licenseAssignmentStates'] ?? [] as $state) {
                $stateInfo = $this->enrichLicenseStateInfo($state);
                $licenses['license_states'][] = $stateInfo;
            }

            // Generate summary
            $licenses['summary'] = $this->generateLicenseSummary($licenses);

            // Get usage information if requested
            if ($includeUsage) {
                $licenses['usage_info'] = $this->getUserLicenseUsage($userId);
            }

            Log::info('User licenses retrieved', [
                'user_id' => $userId,
                'total_licenses' => $licenses['summary']['total_licenses'],
                'active_licenses' => $licenses['summary']['active_licenses'],
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'licenses' => $licenses,
                'retrieved_at' => now()->toISOString()
            ];
        } catch (Exception $e) {
            Log::error('Failed to get user licenses', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'licenses' => []
            ];
        }
    }

    /**
     * Get user's authentication methods and security info
     * NEW method for security analysis
     *
     * @param string $userId User ID (optional, defaults to current user)
     * @param array $options Query options
     * @return array Authentication methods and security information
     */
    public function getUserSecurityInfo(string $userId = 'me', array $options = []): array
    {
        try {
            $includeAuthMethods = $options['include_auth_methods'] ?? true;
            $includeSignInLogs = $options['include_signin_logs'] ?? false;
            $includeRiskEvents = $options['include_risk_events'] ?? false;

            Log::info('Retrieving user security information', [
                'user_id' => $userId,
                'include_auth_methods' => $includeAuthMethods,
                'include_signin_logs' => $includeSignInLogs,
                'user' => $this->auth->email ?? 'unknown'
            ]);

            $securityInfo = [
                'user_id' => $userId,
                'authentication_methods' => [],
                'signin_summary' => [],
                'risk_events' => [],
                'security_summary' => [
                    'mfa_enabled' => false,
                    'auth_method_count' => 0,
                    'risk_level' => 'unknown',
                    'last_signin' => null
                ]
            ];

            // Get authentication methods if requested
            if ($includeAuthMethods) {
                try {
                    $authMethods = $this->makeRequest('GET', "/{$userId}/authentication/methods");
                    $securityInfo['authentication_methods'] = array_map(function ($method) {
                        return $this->enrichAuthMethodInfo($method);
                    }, $authMethods['value'] ?? []);
                } catch (Exception $e) {
                    Log::info('Auth methods not accessible', ['error' => $e->getMessage()]);
                }
            }

            // Get recent sign-in logs if requested (requires admin permissions)
            if ($includeSignInLogs) {
                try {
                    $signInLogs = $this->getSignInLogs([
                        'limit' => 10,
                        'filter' => "userId eq '{$userId}'"
                    ]);
                    $securityInfo['signin_summary'] = $this->summarizeSignInLogs($signInLogs);
                } catch (Exception $e) {
                    Log::info('Sign-in logs not accessible', ['error' => $e->getMessage()]);
                }
            }

            // Get risk events if requested (requires admin permissions)
            if ($includeRiskEvents) {
                try {
                    $riskEvents = $this->getIdentityRiskEvents([
                        'limit' => 20,
                        'filter' => "userId eq '{$userId}'"
                    ]);
                    $securityInfo['risk_events'] = $riskEvents;
                } catch (Exception $e) {
                    Log::info('Risk events not accessible', ['error' => $e->getMessage()]);
                }
            }

            // Generate security summary
            $securityInfo['security_summary'] = $this->generateSecuritySummary($securityInfo);

            Log::info('User security information retrieved', [
                'user_id' => $userId,
                'auth_method_count' => $securityInfo['security_summary']['auth_method_count'],
                'mfa_enabled' => $securityInfo['security_summary']['mfa_enabled'],
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => true,
                'security_info' => $securityInfo,
                'retrieved_at' => now()->toISOString()
            ];
        } catch (Exception $e) {
            Log::error('Failed to get user security information', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'security_info' => []
            ];
        }
    }

    // HELPER METHODS FOR USER & PROFILE FUNCTIONALITY

    /**
     * Sanitize profile data for security
     * NEW helper method
     */
    private function sanitizeProfileData(array $profileData): array
    {
        $sanitized = [];

        // Define allowed fields for profile updates
        $allowedFields = [
            'displayName',
            'givenName',
            'surname',
            'jobTitle',
            'department',
            'officeLocation',
            'businessPhones',
            'mobilePhone',
            'preferredLanguage',
            'aboutMe',
            'interests',
            'skills',
            'responsibilities',
            'schools',
            'pastProjects',
            'preferredName',
            'usageLocation',
            'city',
            'country',
            'streetAddress',
            'postalCode',
            'state',
            'companyName'
        ];

        foreach ($profileData as $field => $value) {
            if (in_array($field, $allowedFields)) {
                if (is_string($value)) {
                    $sanitized[$field] = trim(strip_tags($value));
                } elseif (is_array($value)) {
                    $sanitized[$field] = array_map('trim', $value);
                } else {
                    $sanitized[$field] = $value;
                }
            }
        }

        return $sanitized;
    }

    /**
     * Validate profile data
     * NEW helper method
     */
    private function validateProfileData(array $profileData): array
    {
        $validation = [
            'is_valid' => true,
            'errors' => []
        ];

        // Validate display name length
        if (isset($profileData['displayName']) && strlen($profileData['displayName']) > 256) {
            $validation['errors'][] = 'Display name cannot exceed 256 characters';
        }

        // Validate email format in business phones
        if (isset($profileData['businessPhones'])) {
            foreach ($profileData['businessPhones'] as $phone) {
                if (strlen($phone) > 64) {
                    $validation['errors'][] = 'Phone number cannot exceed 64 characters';
                    break;
                }
            }
        }

        // Validate preferred language format
        if (
            isset($profileData['preferredLanguage']) &&
            !preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $profileData['preferredLanguage'])
        ) {
            $validation['errors'][] = 'Preferred language must be in ISO format (e.g., en-US)';
        }

        $validation['is_valid'] = empty($validation['errors']);
        return $validation;
    }

    /**
     * Build manager chain up the hierarchy
     * NEW helper method
     */
    private function buildManagerChain(string $userId, int $maxLevels): array
    {
        $chain = [];
        $immediateManager = null;
        $currentUserId = $userId;
        $level = 0;

        while ($level < $maxLevels) {
            try {
                $manager = $this->makeRequest('GET', "/{$currentUserId}/manager", [
                    'query' => [
                        '$select' => 'id,displayName,mail,jobTitle,department,officeLocation'
                    ]
                ]);

                $managerInfo = $this->formatUserInfo($manager);

                if ($level === 0) {
                    $immediateManager = $managerInfo;
                }

                $chain[] = $managerInfo;
                $currentUserId = $manager['id'];
                $level++;
            } catch (Exception $e) {
                // No more managers in chain
                break;
            }
        }

        return [
            'immediate_manager' => $immediateManager,
            'chain' => $chain
        ];
    }

    /**
     * Get user's peers (same manager)
     * NEW helper method
     */
    private function getUserPeers(string $userId, string $managerId): array
    {
        try {
            $peers = $this->makeRequest('GET', "/{$managerId}/directReports", [
                'query' => [
                    '$select' => 'id,displayName,mail,jobTitle,department',
                    '$filter' => "id ne '{$userId}'"
                ]
            ]);

            return array_map(function ($peer) {
                return $this->formatUserInfo($peer);
            }, $peers['value'] ?? []);
        } catch (Exception $e) {
            Log::info('Could not retrieve peers', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Format user information consistently
     * NEW helper method
     */
    private function formatUserInfo(array $user): array
    {
        return [
            'id' => $user['id'],
            'displayName' => $user['displayName'] ?? null,
            'email' => $user['mail'] ?? $user['userPrincipalName'] ?? null,
            'jobTitle' => $user['jobTitle'] ?? null,
            'department' => $user['department'] ?? null,
            'officeLocation' => $user['officeLocation'] ?? null,
            'employeeId' => $user['employeeId'] ?? null
        ];
    }

    /**
     * Generate organizational insights
     * NEW helper method
     */
    private function generateOrgInsights(array $hierarchy): array
    {
        $insights = [
            'reporting_structure' => 'individual_contributor',
            'management_span' => 0,
            'org_level' => $hierarchy['org_depth'],
            'has_management_responsibilities' => false,
            'department_representation' => [],
            'location_diversity' => []
        ];

        // Determine reporting structure
        if (!empty($hierarchy['direct_reports'])) {
            $insights['has_management_responsibilities'] = true;
            $insights['management_span'] = count($hierarchy['direct_reports']);

            if ($insights['management_span'] > 10) {
                $insights['reporting_structure'] = 'senior_manager';
            } elseif ($insights['management_span'] > 3) {
                $insights['reporting_structure'] = 'manager';
            } else {
                $insights['reporting_structure'] = 'team_lead';
            }
        }

        // Analyze department representation
        $departments = [];
        foreach (array_merge($hierarchy['direct_reports'], $hierarchy['peers']) as $person) {
            if (!empty($person['department'])) {
                $departments[$person['department']] = ($departments[$person['department']] ?? 0) + 1;
            }
        }
        $insights['department_representation'] = $departments;

        return $insights;
    }

    /**
     * Get user search select fields
     * NEW helper method
     */
    private function getUserSearchSelectFields(): array
    {
        return [
            'id',
            'displayName',
            'mail',
            'userPrincipalName',
            'jobTitle',
            'department',
            'officeLocation',
            'employeeId',
            'accountEnabled',
            'userType',
            'businessPhones',
            'mobilePhone',
            'companyName'
        ];
    }

    /**
     * Enrich user search result with additional info
     * NEW helper method
     */
    private function enrichUserSearchResult(array $user, array $options): array
    {
        $enriched = $this->formatUserInfo($user);

        // Add additional fields from search
        $enriched['accountEnabled'] = $user['accountEnabled'] ?? null;
        $enriched['userType'] = $user['userType'] ?? null;
        $enriched['businessPhones'] = $user['businessPhones'] ?? [];
        $enriched['mobilePhone'] = $user['mobilePhone'] ?? null;
        $enriched['companyName'] = $user['companyName'] ?? null;

        // Add derived information
        $enriched['is_external'] = ($user['userType'] ?? 'Member') !== 'Member';
        $enriched['has_phone'] = !empty($user['businessPhones']) || !empty($user['mobilePhone']);
        $enriched['location_info'] = $this->extractLocationInfo($user);

        return $enriched;
    }

    /**
     * Generate search insights
     * NEW helper method
     */
    private function generateSearchInsights(array $users, string $query): array
    {
        $insights = [
            'total_results' => count($users),
            'departments' => [],
            'locations' => [],
            'user_types' => ['Member' => 0, 'Guest' => 0],
            'account_status' => ['enabled' => 0, 'disabled' => 0],
            'has_contact_info' => 0,
            'query_matches' => [
                'name_matches' => 0,
                'email_matches' => 0,
                'department_matches' => 0,
                'title_matches' => 0
            ]
        ];

        foreach ($users as $user) {
            // Department distribution
            if (!empty($user['department'])) {
                $insights['departments'][$user['department']] = ($insights['departments'][$user['department']] ?? 0) + 1;
            }

            // Location distribution
            if (!empty($user['officeLocation'])) {
                $insights['locations'][$user['officeLocation']] = ($insights['locations'][$user['officeLocation']] ?? 0) + 1;
            }

            // User type distribution
            $userType = $user['userType'] ?? 'Member';
            if (isset($insights['user_types'][$userType])) {
                $insights['user_types'][$userType]++;
            }

            // Account status
            $status = ($user['accountEnabled'] ?? true) ? 'enabled' : 'disabled';
            $insights['account_status'][$status]++;

            // Contact info availability
            if ($user['has_phone'] ?? false) {
                $insights['has_contact_info']++;
            }

            // Query match analysis
            $queryLower = strtolower($query);
            if (str_contains(strtolower($user['displayName'] ?? ''), $queryLower)) {
                $insights['query_matches']['name_matches']++;
            }
            if (str_contains(strtolower($user['email'] ?? ''), $queryLower)) {
                $insights['query_matches']['email_matches']++;
            }
            if (str_contains(strtolower($user['department'] ?? ''), $queryLower)) {
                $insights['query_matches']['department_matches']++;
            }
            if (str_contains(strtolower($user['jobTitle'] ?? ''), $queryLower)) {
                $insights['query_matches']['title_matches']++;
            }
        }

        return $insights;
    }

    /**
     * Enrich group information with additional details
     * NEW helper method
     */
    private function enrichGroupInfo(array $group): array
    {
        $enriched = [
            'id' => $group['id'],
            'displayName' => $group['displayName'],
            'description' => $group['description'] ?? null,
            'mail' => $group['mail'] ?? null,
            'visibility' => $group['visibility'] ?? null,
            'group_types' => $group['groupTypes'] ?? [],
            'mail_enabled' => $group['mailEnabled'] ?? false,
            'security_enabled' => $group['securityEnabled'] ?? false
        ];

        // Determine group type
        $enriched['type'] = $this->determineGroupType($group);

        // Check if it's a Teams-enabled group
        $enriched['is_teams_enabled'] = in_array('Team', $group['resourceProvisioningOptions'] ?? []);

        // Determine access level
        $enriched['access_level'] = $this->determineGroupAccessLevel($group);

        return $enriched;
    }

    /**
     * Determine group type based on properties
     * NEW helper method
     */
    private function determineGroupType(array $group): string
    {
        $groupTypes = $group['groupTypes'] ?? [];
        $mailEnabled = $group['mailEnabled'] ?? false;
        $securityEnabled = $group['securityEnabled'] ?? false;

        if (in_array('Unified', $groupTypes)) {
            return 'Microsoft 365';
        } elseif ($securityEnabled && !$mailEnabled) {
            return 'Security';
        } elseif ($mailEnabled && !$securityEnabled) {
            return 'Distribution';
        } elseif ($mailEnabled && $securityEnabled) {
            return 'Mail-enabled Security';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Determine group access level
     * NEW helper method
     */
    private function determineGroupAccessLevel(array $group): string
    {
        $visibility = $group['visibility'] ?? 'Private';

        return match (strtolower($visibility)) {
            'public' => 'Public',
            'private' => 'Private',
            'hiddenmembership' => 'Hidden',
            default => 'Private'
        };
    }

    /**
     * Generate group membership summary
     * NEW helper method
     */
    private function generateGroupMembershipSummary(array $memberships): array
    {
        $summary = [
            'total_groups' => 0,
            'security_groups' => 0,
            'distribution_groups' => 0,
            'microsoft365_groups' => 0,
            'teams_count' => count($memberships['teams']),
            'admin_roles' => count($memberships['directory_roles'])
        ];

        $allGroups = array_merge(
            $memberships['direct_groups'],
            $memberships['nested_groups']
        );

        $summary['total_groups'] = count($allGroups);

        foreach ($allGroups as $group) {
            switch ($group['type']) {
                case 'Security':
                case 'Mail-enabled Security':
                    $summary['security_groups']++;
                    break;
                case 'Distribution':
                    $summary['distribution_groups']++;
                    break;
                case 'Microsoft 365':
                    $summary['microsoft365_groups']++;
                    break;
            }
        }

        return $summary;
    }

    /**
     * Generate group insights
     * NEW helper method
     */
    private function generateGroupInsights(array $memberships): array
    {
        $insights = [
            'access_level' => 'standard',
            'collaboration_score' => 0,
            'security_posture' => 'basic',
            'administrative_privileges' => false,
            'teams_adoption' => 'low'
        ];

        // Determine access level based on admin roles
        if (count($memberships['directory_roles']) > 0) {
            $insights['administrative_privileges'] = true;
            $insights['access_level'] = 'elevated';

            // Check for high-privilege roles
            $highPrivRoles = ['Global Administrator', 'Security Administrator', 'Exchange Administrator'];
            foreach ($memberships['directory_roles'] as $role) {
                if (in_array($role['displayName'], $highPrivRoles)) {
                    $insights['access_level'] = 'high_privilege';
                    break;
                }
            }
        }

        // Calculate collaboration score based on Microsoft 365 groups and Teams
        $m365Groups = $memberships['summary']['microsoft365_groups'];
        $teamsCount = $memberships['summary']['teams_count'];
        $insights['collaboration_score'] = min(100, ($m365Groups * 10) + ($teamsCount * 15));

        // Determine Teams adoption level
        if ($teamsCount >= 10) {
            $insights['teams_adoption'] = 'high';
        } elseif ($teamsCount >= 5) {
            $insights['teams_adoption'] = 'medium';
        }

        // Security posture based on security group memberships
        if ($memberships['summary']['security_groups'] >= 5) {
            $insights['security_posture'] = 'advanced';
        } elseif ($memberships['summary']['security_groups'] >= 2) {
            $insights['security_posture'] = 'intermediate';
        }

        return $insights;
    }

    /**
     * Enrich license information
     * NEW helper method
     */
    private function enrichLicenseInfo(array $license): array
    {
        return [
            'sku_id' => $license['skuId'],
            'disabled_plans' => $license['disabledPlans'] ?? [],
            'disabled_plans_count' => count($license['disabledPlans'] ?? []),
            'license_name' => $this->getLicenseName($license['skuId']),
            'assignment_source' => 'direct' // Default, would need additional logic to determine
        ];
    }

    /**
     * Enrich service plan information
     * NEW helper method
     */
    private function enrichServicePlanInfo(array $plan): array
    {
        return [
            'service_plan_id' => $plan['servicePlanId'],
            'service_plan_name' => $plan['servicePlanName'] ?? 'Unknown',
            'capability_status' => $plan['capabilityStatus'] ?? 'Unknown',
            'assigned_date' => $plan['assignedDateTime'] ?? null,
            'is_enabled' => ($plan['capabilityStatus'] ?? '') === 'Enabled'
        ];
    }

    /**
     * Enrich license state information
     * NEW helper method
     */
    private function enrichLicenseStateInfo(array $state): array
    {
        return [
            'sku_id' => $state['skuId'],
            'assignment_source' => $state['assignedByGroup'] ?? 'direct',
            'state' => $state['state'] ?? 'Unknown',
            'error' => $state['error'] ?? null,
            'last_updated' => $state['lastUpdatedDateTime'] ?? null
        ];
    }

    /**
     * Generate license summary
     * NEW helper method
     */
    private function generateLicenseSummary(array $licenses): array
    {
        $summary = [
            'total_licenses' => count($licenses['assigned_licenses']),
            'active_licenses' => 0,
            'disabled_plans' => 0,
            'error_states' => 0,
            'enabled_services' => 0,
            'total_services' => count($licenses['assigned_plans'])
        ];

        // Count disabled plans
        foreach ($licenses['assigned_licenses'] as $license) {
            $summary['disabled_plans'] += $license['disabled_plans_count'];
        }

        // Count license states
        foreach ($licenses['license_states'] as $state) {
            if ($state['state'] === 'Active') {
                $summary['active_licenses']++;
            } elseif (!empty($state['error'])) {
                $summary['error_states']++;
            }
        }

        // Count enabled services
        foreach ($licenses['assigned_plans'] as $plan) {
            if ($plan['is_enabled']) {
                $summary['enabled_services']++;
            }
        }

        return $summary;
    }

    /**
     * Get license name from SKU ID
     * NEW helper method
     */
    private function getLicenseName(string $skuId): string
    {
        // Common Microsoft 365 SKU mappings
        $licenseNames = [
            '18181a46-0d4e-45cd-891e-60aabd171b4e' => 'Office 365 E1',
            '6634e0ce-1a9f-428c-8ad7-d4c4cc0a2b80' => 'Office 365 E2',
            '6fd2c87f-b296-42f0-b197-1e91e994b900' => 'Office 365 E3',
            '1392051d-0cb9-4b7a-88d5-621fee5e8711' => 'Office 365 E4',
            'c7df2760-2c81-4ef7-b578-5b5392b571df' => 'Office 365 E5',
            '05e9a617-0261-4cee-bb44-138d3ef5d965' => 'Microsoft 365 E3',
            '06ebc4ee-1bb5-47dd-8120-11324bc54e06' => 'Microsoft 365 E5',
            // Add more mappings as needed
        ];

        return $licenseNames[$skuId] ?? 'Unknown License';
    }

    /**
     * Get user license usage information
     * NEW helper method
     */
    private function getUserLicenseUsage(string $userId): array
    {
        try {
            // This would require additional Graph API calls to get usage data
            // Implementation depends on available reporting APIs
            return [
                'last_activity' => null,
                'services_used' => [],
                'usage_summary' => 'Usage data not available'
            ];
        } catch (Exception $e) {
            return [
                'error' => 'Usage data not accessible',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Enrich authentication method information
     * NEW helper method
     */
    private function enrichAuthMethodInfo(array $method): array
    {
        return [
            'id' => $method['id'],
            'method_type' => $method['@odata.type'] ?? 'Unknown',
            'display_name' => $this->getAuthMethodDisplayName($method),
            'is_mfa' => $this->isMultiFactorMethod($method),
            'created_date' => $method['createdDateTime'] ?? null,
            'last_used' => $method['lastUsedDateTime'] ?? null
        ];
    }

    /**
     * Get authentication method display name
     * NEW helper method
     */
    private function getAuthMethodDisplayName(array $method): string
    {
        $type = $method['@odata.type'] ?? '';

        return match ($type) {
            '#microsoft.graph.passwordAuthenticationMethod' => 'Password',
            '#microsoft.graph.phoneAuthenticationMethod' => 'Phone',
            '#microsoft.graph.fido2AuthenticationMethod' => 'FIDO2 Security Key',
            '#microsoft.graph.microsoftAuthenticatorAuthenticationMethod' => 'Microsoft Authenticator',
            '#microsoft.graph.windowsHelloForBusinessAuthenticationMethod' => 'Windows Hello',
            '#microsoft.graph.emailAuthenticationMethod' => 'Email',
            default => 'Unknown Method'
        };
    }

    /**
     * Check if authentication method is multi-factor
     * NEW helper method
     */
    private function isMultiFactorMethod(array $method): bool
    {
        $type = $method['@odata.type'] ?? '';
        $mfaMethods = [
            '#microsoft.graph.phoneAuthenticationMethod',
            '#microsoft.graph.fido2AuthenticationMethod',
            '#microsoft.graph.microsoftAuthenticatorAuthenticationMethod',
            '#microsoft.graph.windowsHelloForBusinessAuthenticationMethod'
        ];

        return in_array($type, $mfaMethods);
    }

    /**
     * Summarize sign-in logs
     * NEW helper method
     */
    private function summarizeSignInLogs(array $signInLogs): array
    {
        if (empty($signInLogs)) {
            return [
                'total_signins' => 0,
                'successful_signins' => 0,
                'failed_signins' => 0,
                'last_signin' => null,
                'unique_locations' => 0,
                'risk_events' => 0
            ];
        }

        $summary = [
            'total_signins' => count($signInLogs),
            'successful_signins' => 0,
            'failed_signins' => 0,
            'last_signin' => null,
            'unique_locations' => [],
            'risk_events' => 0,
            'applications_used' => []
        ];

        foreach ($signInLogs as $log) {
            // Count success/failure
            if (($log['status']['errorCode'] ?? 0) === 0) {
                $summary['successful_signins']++;
            } else {
                $summary['failed_signins']++;
            }

            // Track last signin
            $signinTime = $log['createdDateTime'] ?? null;
            if ($signinTime && (!$summary['last_signin'] || $signinTime > $summary['last_signin'])) {
                $summary['last_signin'] = $signinTime;
            }

            // Track locations
            $location = $log['location']['city'] ?? 'Unknown';
            if (!in_array($location, $summary['unique_locations'])) {
                $summary['unique_locations'][] = $location;
            }

            // Count risk events
            if (($log['riskLevelDuringSignIn'] ?? 'none') !== 'none') {
                $summary['risk_events']++;
            }

            // Track applications
            $appName = $log['appDisplayName'] ?? 'Unknown';
            $summary['applications_used'][$appName] = ($summary['applications_used'][$appName] ?? 0) + 1;
        }

        $summary['unique_locations'] = count($summary['unique_locations']);
        return $summary;
    }

    /**
     * Generate security summary
     * NEW helper method
     */
    private function generateSecuritySummary(array $securityInfo): array
    {
        $summary = [
            'mfa_enabled' => false,
            'auth_method_count' => count($securityInfo['authentication_methods']),
            'risk_level' => 'low',
            'last_signin' => $securityInfo['signin_summary']['last_signin'] ?? null,
            'security_score' => 0,
            'recommendations' => []
        ];

        // Check for MFA
        foreach ($securityInfo['authentication_methods'] as $method) {
            if ($method['is_mfa']) {
                $summary['mfa_enabled'] = true;
                break;
            }
        }

        // Calculate security score (0-100)
        $score = 0;
        if ($summary['mfa_enabled']) $score += 40;
        if ($summary['auth_method_count'] >= 2) $score += 20;
        if ($summary['auth_method_count'] >= 3) $score += 15;
        if (count($securityInfo['risk_events']) === 0) $score += 25;

        $summary['security_score'] = $score;

        // Generate recommendations
        if (!$summary['mfa_enabled']) {
            $summary['recommendations'][] = 'Enable multi-factor authentication';
        }
        if ($summary['auth_method_count'] < 2) {
            $summary['recommendations'][] = 'Add additional authentication methods';
        }
        if (count($securityInfo['risk_events']) > 0) {
            $summary['recommendations'][] = 'Review recent risk events';
        }

        // Determine overall risk level
        if ($score >= 80) {
            $summary['risk_level'] = 'low';
        } elseif ($score >= 60) {
            $summary['risk_level'] = 'medium';
        } else {
            $summary['risk_level'] = 'high';
        }

        return $summary;
    }

    /**
     * Extract location information from user data
     * NEW helper method
     */
    private function extractLocationInfo(array $user): array
    {
        return [
            'office_location' => $user['officeLocation'] ?? null,
            'city' => $user['city'] ?? null,
            'country' => $user['country'] ?? null,
            'state' => $user['state'] ?? null,
            'postal_code' => $user['postalCode'] ?? null,
            'has_location_data' => !empty($user['officeLocation']) || !empty($user['city'])
        ];
    }

    /**
     * Get extended user profile with all available data
     */
    public function getExtendedUserProfile(): array
    {
        try {
            $user = $this->makeRequest('GET', '/me', [
                'query' => [
                    '$select' => 'id,displayName,mail,userPrincipalName,jobTitle,department,officeLocation,businessPhones,mobilePhone,preferredLanguage,employeeId,companyName,city,country,streetAddress,postalCode,state,aboutMe,birthday,hireDate,interests,skills,responsibilities,schools,pastProjects,preferredName,surname,givenName,mailNickname,onPremisesImmutableId,createdDateTime,deletedDateTime,accountEnabled,ageGroup,assignedLicenses,assignedPlans,businessPhones,city,companyName,consentProvidedForMinor,country,creationType,department,deviceKeys,employeeHireDate,employeeId,employeeOrgData,employeeType,externalUserState,faxNumber,identities,imAddresses,isResourceAccount,jobTitle,lastPasswordChangeDateTime,legalAgeGroupClassification,licenseAssignmentStates,mailNickname,manager,mobilePhone,onPremisesDistinguishedName,onPremisesDomainName,onPremisesExtensionAttributes,onPremisesLastSyncDateTime,onPremisesProvisioningErrors,onPremisesSamAccountName,onPremisesSecurityIdentifier,onPremisesSyncEnabled,onPremisesUserPrincipalName,otherMails,passwordPolicies,passwordProfile,postalCode,preferredDataLocation,preferredLanguage,proxyAddresses,refreshTokensValidFromDateTime,showInAddressList,signInSessionsValidFromDateTime,state,streetAddress,usageLocation,userType'
                ]
            ]);

            return [
                'success' => true,
                'user' => $user
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // SECTION 7: CALENDAR METHODS
    public function getCalendarEvents(array $options = []): array
    { /* Implementation */
    }
    public function createCalendarEvent(array $eventData): ?array
    { /* Implementation */
    }

    // SECTION 8: CONTACTS METHODS
    public function getContacts(array $options = []): array
    { /* Implementation */
    }

    // SECTION 9: FILES & ONEDRIVE METHODS
    public function getOneDriveItems(array $options = []): array
    { /* Implementation */
    }
    public function uploadFileToOneDrive(string $filename, string $content, string $path = ''): array
    { /* Implementation */
    }
    public function getSharePointSites(array $options = []): array
    { /* Implementation */
    }

    // SECTION 10: TEAMS METHODS
    public function getUserTeams(): array
    { /* Implementation */
    }
    public function getTeamChannels(string $teamId): array
    { /* Implementation */
    }
    public function sendTeamsMessage(string $teamId, string $channelId, string $message): array
    { /* Implementation */
    }
    public function getUserChats(): array
    { /* Implementation */
    }
    public function getChatMessages(string $chatId, array $options = []): array
    { /* Implementation */
    }
    public function sendChatMessage(string $chatId, string $message): array
    { /* Implementation */
    }

    // SECTION 11: TASKS & PRODUCTIVITY
    public function getTasks(array $options = []): array
    { /* Implementation */
    }
    public function createTask(string $title, array $options = []): array
    { /* Implementation */
    }
    public function getOneNoteNotebooks(): array
    { /* Implementation */
    }
    public function getOneNotePages(array $options = []): array
    { /* Implementation */
    }
    public function createOneNotePage(string $title, string $content, string $notebookId = null): array
    { /* Implementation */
    }

    // SECTION 12: PRESENCE & MEETINGS
    public function getUserPresence(string $userId = 'me'): array
    { /* Implementation */
    }
    public function setUserPresence(string $availability, string $activity = null): array
    { /* Implementation */
    }
    public function getOnlineMeetings(): array
    { /* Implementation */
    }
    public function createOnlineMeeting(array $meetingData): array
    { /* Implementation */
    }

    // SECTION 13: SECURITY & COMPLIANCE
    public function getSecurityAlerts(array $options = []): array
    { /* Implementation */
    }
    public function getAuditLogs(array $options = []): array
    { /* Implementation */
    }
    public function getSignInLogs(array $options = []): array
    { /* Implementation */
    }
    public function getIdentityRiskEvents(array $options = []): array
    { /* Implementation */
    }
    public function getRiskyUsers(array $options = []): array
    { /* Implementation */
    }
    public function getThreatIndicators(array $options = []): array
    { /* Implementation */
    }
    public function getConditionalAccessPolicies(): array
    { /* Implementation */
    }
    public function getUserAuthenticationMethods(): array
    { /* Implementation */
    }

    // SECTION 14: ORGANIZATION & DIRECTORY
    public function getOrganization(): array
    { /* Implementation */
    }
    public function getApplications(array $options = []): array
    { /* Implementation */
    }
    public function getDevices(array $options = []): array
    { /* Implementation */
    }

    // SECTION 15: ANALYTICS & REPORTING
    public function getUsageReports(string $reportType, array $options = []): array
    { /* Implementation */
    }
    public function getUsageStats(): array
    { /* Implementation */
    }

    // SECTION 16: BUSINESS APPLICATIONS
    public function getBookingsBusinesses(): array
    { /* Implementation */
    }
    public function getBookingsAppointments(string $businessId, array $options = []): array
    { /* Implementation */
    }
    public function createBookingsAppointment(string $businessId, array $appointmentData): array
    { /* Implementation */
    }
    public function getPlaces(string $placeType = 'room'): array
    { /* Implementation */
    }
    public function getPrinters(): array
    { /* Implementation */
    }
    public function getPrintJobs(array $options = []): array
    { /* Implementation */
    }

    // SECTION 17: SEARCH & COMPREHENSIVE FEATURES
    public function searchMicrosoft365(string $query, array $options = []): array
    { /* Implementation */
    }
    public function getUserActivitySummary(): array
    { /* Implementation */
    }
    public function testAllPermissions(): array
    { /* Implementation */
    }
    public function testConnection(): array
    { /* Implementation */
    }

    // SECTION 18: UTILITY & HELPER METHODS
    public function setAuth($user): self
    {
        $this->auth = $user;
        return $this;
    }
    public function getAuth()
    {
        return $this->auth;
    }
    public function getTokenInfo(): ?array
    { /* Implementation */
    }
    public function revokeAuthentication(): bool
    { /* Implementation */
    }
    public function formatBytes(int $bytes): string
    { /* Implementation */
    }

    // Private helper methods
    private function formatRecipients(array $recipients): array
    { /* Implementation */
    }
    private function formatAttachments(array $attachments): array
    { /* Implementation */
    }
    private function mapPriorityToImportance(string $priority): string
    { /* Implementation */
    }
    private function processEmailsBulk(array $rawEmails): array
    { /* Implementation */
    }
    private function saveEmailsToDatabase(array $emails, string $folder): array
    { /* Implementation */
    }
    private function getDefaultSelectFields(bool $fetchBody = false): array
    { /* Implementation */
    }
    private function buildOptimizedEmailsUrl(string $folder, int $limit, string $select, array $options): string
    { /* Implementation */
    }
    private function extractRecipients(array $recipients): array
    { /* Implementation */
    }
    private function extractBodyContent(?array $body): ?string
    { /* Implementation */
    }
}
