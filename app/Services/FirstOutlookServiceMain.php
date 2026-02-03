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

class FirstOutlookServiceMain
{
    private string $graphEndpoint = 'https://graph.microsoft.com/v1.0';
    private string $authEndpoint = 'https://login.microsoftonline.com';
    private ?array $token = null;
    private array $config;
    private int $timeout;
    protected $auth = null;

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
// Complete implementation for Microsoft Graph Contacts API

/**
 * Get contacts from user's mailbox with filtering and pagination
 * Enhanced version with comprehensive search and filtering capabilities
 *
 * @param array $options Query options (folder, limit, filter, etc.)
 * @return array Array of contacts with metadata
 */
public function getContacts(array $options = []): array
{
    try {
        // Parse options with defaults
        $folder = $options['folder'] ?? 'contacts'; // contacts, mycontacts, or specific folder ID
        $top = min($options['limit'] ?? 50, 999); // Microsoft Graph limit
        $skip = $options['skip'] ?? 0;
        $filter = $options['filter'] ?? null;
        $orderBy = $options['orderBy'] ?? 'displayName';
        $select = $options['select'] ?? $this->getDefaultContactSelectFields();
        $search = $options['search'] ?? null;

        Log::info('Fetching contacts', [
            'folder' => $folder,
            'limit' => $top,
            'skip' => $skip,
            'has_filter' => !empty($filter),
            'has_search' => !empty($search),
            'user' => $this->auth->email ?? 'unknown'
        ]);

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
        if ($search) {
            $query['$search'] = "\"{$search}\"";
        }

        // Determine endpoint based on folder
        $endpoint = $this->buildContactsEndpoint($folder);

        $startTime = microtime(true);

        $response = $this->makeRequest('GET', $endpoint, ['query' => $query]);
        $contacts = $response['value'] ?? [];

        $processingTime = round((microtime(true) - $startTime) * 1000, 2);

        // Process and enrich contact data
        $processedContacts = array_map(function ($contact) use ($options) {
            return $this->enrichContactData($contact, $options);
        }, $contacts);

        // Generate contact insights
        $insights = $this->generateContactInsights($processedContacts);

        Log::info('Contacts fetched successfully', [
            'folder' => $folder,
            'contact_count' => count($processedContacts),
            'has_more' => !empty($response['@odata.nextLink']),
            'processing_time_ms' => $processingTime,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'contacts' => $processedContacts,
            'count' => count($processedContacts),
            'folder' => $folder,
            'insights' => $insights,
            'nextLink' => $response['@odata.nextLink'] ?? null,
            'processing_time_ms' => $processingTime,
            'retrieved_at' => now()->toISOString()
        ];
    } catch (Exception $e) {
        Log::error('Failed to get contacts', [
            'error' => $e->getMessage(),
            'folder' => $options['folder'] ?? 'contacts',
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'contacts' => [],
            'count' => 0
        ];
    }
}

/**
 * Get a specific contact by ID
 * Enhanced version with detailed contact information
 *
 * @param string $contactId Contact ID
 * @param array $options Query options
 * @return array|null Contact details or null if not found
 */
public function getContact(string $contactId, array $options = []): ?array
{
    try {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        $includePhoto = $options['include_photo'] ?? false;
        $select = $options['select'] ?? $this->getExtendedContactSelectFields();

        Log::info('Retrieving contact details', [
            'contact_id' => $contactId,
            'include_photo' => $includePhoto,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $query = ['$select' => $select];

        $response = $this->makeRequest('GET', "/me/contacts/{$contactId}", ['query' => $query]);

        // Get contact photo if requested
        $photoData = null;
        if ($includePhoto) {
            $photoData = $this->getContactPhoto($contactId);
        }

        $enrichedContact = $this->enrichContactData($response, array_merge($options, [
            'include_extended_info' => true,
            'photo_data' => $photoData
        ]));

        Log::info('Contact details retrieved', [
            'contact_id' => $contactId,
            'display_name' => $response['displayName'] ?? 'N/A',
            'has_photo' => !empty($photoData),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return $enrichedContact;
    } catch (Exception $e) {
        Log::error('Failed to get contact', [
            'contact_id' => $contactId,
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return null;
    }
}

/**
 * Create a new contact
 * Enhanced version with comprehensive contact data support
 *
 * @param array $contactData Contact information
 * @param array $options Creation options
 * @return array Creation result with contact ID
 */
public function createContact(array $contactData, array $options = []): array
{
    try {
        if (empty($contactData)) {
            throw new Exception('Contact data is required');
        }

        // Validate and sanitize contact data
        $sanitizedData = $this->sanitizeContactData($contactData);
        $validation = $this->validateContactData($sanitizedData);

        if (!$validation['is_valid']) {
            throw new Exception('Invalid contact data: ' . implode(', ', $validation['errors']));
        }

        $folder = $options['folder'] ?? 'contacts';

        Log::info('Creating new contact', [
            'display_name' => $sanitizedData['displayName'] ?? 'N/A',
            'folder' => $folder,
            'has_email' => !empty($sanitizedData['emailAddresses']),
            'has_phone' => !empty($sanitizedData['businessPhones']) || !empty($sanitizedData['homePhones']),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $startTime = microtime(true);

        // Determine endpoint based on folder
        $endpoint = $folder === 'contacts' ? '/me/contacts' : "/me/contactFolders/{$folder}/contacts";

        $response = $this->makeRequest('POST', $endpoint, ['json' => $sanitizedData]);

        $creationTime = round((microtime(true) - $startTime) * 1000, 2);

        // Upload photo if provided
        if (!empty($contactData['photo_content'])) {
            $this->uploadContactPhoto($response['id'], $contactData['photo_content']);
        }

        // Add to groups if specified
        if (!empty($options['groups'])) {
            $this->addContactToGroups($response['id'], $options['groups']);
        }

        Log::info('Contact created successfully', [
            'contact_id' => $response['id'],
            'display_name' => $response['displayName'],
            'creation_time_ms' => $creationTime,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'contact_id' => $response['id'],
            'contact' => $this->enrichContactData($response),
            'creation_time_ms' => $creationTime,
            'message' => 'Contact created successfully'
        ];
    } catch (Exception $e) {
        Log::error('Failed to create contact', [
            'error' => $e->getMessage(),
            'display_name' => $contactData['displayName'] ?? 'N/A',
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'contact_id' => null,
            'contact' => null
        ];
    }
}

/**
 * Update an existing contact
 * Enhanced version with selective field updates
 *
 * @param string $contactId Contact ID
 * @param array $updateData Data to update
 * @param array $options Update options
 * @return array Update result
 */
public function updateContact(string $contactId, array $updateData, array $options = []): array
{
    try {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        if (empty($updateData)) {
            throw new Exception('Update data is required');
        }

        // Validate and sanitize update data
        $sanitizedData = $this->sanitizeContactData($updateData);
        $validation = $this->validateContactData($sanitizedData, true); // Partial validation for updates

        if (!$validation['is_valid']) {
            throw new Exception('Invalid update data: ' . implode(', ', $validation['errors']));
        }

        Log::info('Updating contact', [
            'contact_id' => $contactId,
            'fields_to_update' => array_keys($sanitizedData),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $startTime = microtime(true);

        $response = $this->makeRequest('PATCH', "/me/contacts/{$contactId}", [
            'json' => $sanitizedData
        ]);

        $updateTime = round((microtime(true) - $startTime) * 1000, 2);

        // Update photo if provided
        if (!empty($updateData['photo_content'])) {
            $this->uploadContactPhoto($contactId, $updateData['photo_content']);
        }

        // Get updated contact details
        $updatedContact = $this->getContact($contactId, [
            'include_photo' => !empty($updateData['photo_content'])
        ]);

        Log::info('Contact updated successfully', [
            'contact_id' => $contactId,
            'updated_fields' => array_keys($sanitizedData),
            'update_time_ms' => $updateTime,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'contact_id' => $contactId,
            'updated_fields' => array_keys($sanitizedData),
            'contact' => $updatedContact,
            'update_time_ms' => $updateTime,
            'message' => 'Contact updated successfully'
        ];
    } catch (Exception $e) {
        Log::error('Failed to update contact', [
            'contact_id' => $contactId,
            'error' => $e->getMessage(),
            'fields_attempted' => array_keys($updateData),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'contact_id' => $contactId,
            'updated_fields' => []
        ];
    }
}

/**
 * Delete a contact
 * Enhanced version with safety checks
 *
 * @param string $contactId Contact ID
 * @param array $options Deletion options
 * @return bool Success status
 */
public function deleteContact(string $contactId, array $options = []): bool
{
    try {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        $confirmDeletion = $options['confirm'] ?? false;
        $backupContact = $options['backup_before_delete'] ?? false;

        // Get contact details for logging before deletion
        $contactDetails = $this->getContact($contactId, ['select' => 'id,displayName,emailAddresses']);

        if (!$contactDetails) {
            throw new Exception('Contact not found');
        }

        // Backup contact if requested
        if ($backupContact) {
            $this->backupContact($contactId, $contactDetails);
        }

        // Safety check for important contacts
        if (!$confirmDeletion && $this->isImportantContact($contactDetails)) {
            throw new Exception('Contact appears to be important. Set confirm=true to delete.');
        }

        Log::warning('Deleting contact', [
            'contact_id' => $contactId,
            'display_name' => $contactDetails['displayName'] ?? 'N/A',
            'backup_created' => $backupContact,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $this->makeRequest('DELETE', "/me/contacts/{$contactId}");

        Log::info('Contact deleted successfully', [
            'contact_id' => $contactId,
            'display_name' => $contactDetails['displayName'] ?? 'N/A',
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return true;
    } catch (Exception $e) {
        Log::error('Failed to delete contact', [
            'contact_id' => $contactId,
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return false;
    }
}

/**
 * Search contacts across all folders
 * Enhanced version with advanced search capabilities
 *
 * @param string $query Search query
 * @param array $options Search options
 * @return array Search results
 */
public function searchContacts(string $query, array $options = []): array
{
    try {
        if (empty(trim($query))) {
            throw new Exception('Search query cannot be empty');
        }

        $top = min($options['limit'] ?? 50, 999);
        $skip = $options['skip'] ?? 0;
        $searchFields = $options['search_fields'] ?? ['displayName', 'emailAddresses', 'companyName'];
        $orderBy = $options['orderBy'] ?? 'displayName';

        Log::info('Searching contacts', [
            'query' => $query,
            'limit' => $top,
            'search_fields' => $searchFields,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        // Build search parameters
        $searchParams = [
            '$search' => "\"{$query}\"",
            '$top' => $top,
            '$skip' => $skip,
            '$select' => $this->getDefaultContactSelectFields(),
            '$orderby' => $orderBy
        ];

        // Add additional filters if provided
        if (!empty($options['company_filter'])) {
            $searchParams['$filter'] = "startswith(companyName, '{$options['company_filter']}')";
        }

        $startTime = microtime(true);

        $response = $this->makeRequest('GET', '/me/contacts', ['query' => $searchParams]);
        $contacts = $response['value'] ?? [];

        $searchTime = round((microtime(true) - $startTime) * 1000, 2);

        // Process and enrich search results
        $processedContacts = array_map(function ($contact) use ($options) {
            return $this->enrichContactData($contact, $options);
        }, $contacts);

        // Analyze search results
        $searchInsights = $this->generateSearchResultInsights($processedContacts, $query);

        Log::info('Contact search completed', [
            'query' => $query,
            'results_count' => count($processedContacts),
            'search_time_ms' => $searchTime,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'contacts' => $processedContacts,
            'count' => count($processedContacts),
            'query' => $query,
            'insights' => $searchInsights,
            'search_time_ms' => $searchTime,
            'nextLink' => $response['@odata.nextLink'] ?? null,
            'searched_at' => now()->toISOString()
        ];
    } catch (Exception $e) {
        Log::error('Contact search failed', [
            'query' => $query,
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'contacts' => [],
            'count' => 0
        ];
    }
}

/**
 * Get contact folders
 * Enhanced version with folder management capabilities
 *
 * @param array $options Query options
 * @return array Array of contact folders
 */
public function getContactFolders(array $options = []): array
{
    try {
        $includeHidden = $options['include_hidden'] ?? false;
        $includeContactCount = $options['include_contact_count'] ?? true;
        $top = min($options['limit'] ?? 100, 1000);

        Log::info('Retrieving contact folders', [
            'include_hidden' => $includeHidden,
            'include_contact_count' => $includeContactCount,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $query = [
            '$top' => $top,
            '$select' => 'id,displayName,parentFolderId,wellKnownName',
            '$orderby' => 'displayName'
        ];

        $response = $this->makeRequest('GET', '/me/contactFolders', ['query' => $query]);
        $folders = $response['value'] ?? [];

        // Enrich folder data
        $enrichedFolders = array_map(function ($folder) use ($includeContactCount) {
            $enriched = [
                'id' => $folder['id'],
                'displayName' => $folder['displayName'],
                'parentFolderId' => $folder['parentFolderId'] ?? null,
                'wellKnownName' => $folder['wellKnownName'] ?? null,
                'is_default' => ($folder['wellKnownName'] ?? '') === 'contacts'
            ];

            // Get contact count if requested
            if ($includeContactCount) {
                $enriched['contact_count'] = $this->getContactCountInFolder($folder['id']);
            }

            return $enriched;
        }, $folders);

        Log::info('Contact folders retrieved', [
            'folder_count' => count($enrichedFolders),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'folders' => $enrichedFolders,
            'count' => count($enrichedFolders)
        ];
    } catch (Exception $e) {
        Log::error('Failed to get contact folders', [
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'folders' => [],
            'count' => 0
        ];
    }
}

/**
 * Create a new contact folder
 * NEW method for contact organization
 *
 * @param string $displayName Folder display name
 * @param string $parentFolderId Parent folder ID (optional)
 * @param array $options Creation options
 * @return array Creation result
 */
public function createContactFolder(string $displayName, string $parentFolderId = null, array $options = []): array
{
    try {
        if (empty(trim($displayName))) {
            throw new Exception('Folder display name is required');
        }

        // Check for duplicate names
        $existingFolders = $this->getContactFolders();
        foreach ($existingFolders['folders'] as $folder) {
            if (strcasecmp($folder['displayName'], $displayName) === 0) {
                if (empty($parentFolderId) || $folder['parentFolderId'] === $parentFolderId) {
                    throw new Exception('A folder with this name already exists in the specified location');
                }
            }
        }

        $folderData = [
            'displayName' => trim($displayName)
        ];

        if ($parentFolderId) {
            $folderData['parentFolderId'] = $parentFolderId;
        }

        Log::info('Creating contact folder', [
            'display_name' => $displayName,
            'parent_folder' => $parentFolderId ?? 'root',
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $endpoint = $parentFolderId
            ? "/me/contactFolders/{$parentFolderId}/childFolders"
            : '/me/contactFolders';

        $response = $this->makeRequest('POST', $endpoint, ['json' => $folderData]);

        Log::info('Contact folder created successfully', [
            'folder_id' => $response['id'],
            'display_name' => $response['displayName'],
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'folder' => [
                'id' => $response['id'],
                'displayName' => $response['displayName'],
                'parentFolderId' => $response['parentFolderId'] ?? null,
                'contact_count' => 0
            ],
            'created_at' => now()->toISOString()
        ];
    } catch (Exception $e) {
        Log::error('Failed to create contact folder', [
            'display_name' => $displayName,
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
 * Get contact photo
 * NEW method for profile picture management
 *
 * @param string $contactId Contact ID
 * @param array $options Photo options
 * @return array|null Photo data or null if not found
 */
public function getContactPhoto(string $contactId, array $options = []): ?array
{
    try {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        $size = $options['size'] ?? 'default'; // default, 48x48, 64x64, 96x96, 120x120, 240x240, 360x360, 432x432, 504x504, 648x648

        Log::info('Retrieving contact photo', [
            'contact_id' => $contactId,
            'size' => $size,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $endpoint = $size === 'default'
            ? "/me/contacts/{$contactId}/photo/\$value"
            : "/me/contacts/{$contactId}/photos/{$size}/\$value";

        $response = $this->makeRequest('GET', $endpoint);

        if (isset($response['content'])) {
            $photoData = [
                'content' => $response['content'],
                'content_type' => $response['content_type'] ?? 'image/jpeg',
                'size' => $response['size'] ?? strlen($response['content']),
                'size_formatted' => $this->formatBytes($response['size'] ?? strlen($response['content'])),
                'dimensions' => $size
            ];

            Log::info('Contact photo retrieved', [
                'contact_id' => $contactId,
                'size' => $photoData['size_formatted'],
                'content_type' => $photoData['content_type'],
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return $photoData;
        }

        return null;
    } catch (Exception $e) {
        Log::info('Contact photo not found or not accessible', [
            'contact_id' => $contactId,
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return null;
    }
}

/**
 * Upload contact photo
 * NEW method for profile picture management
 *
 * @param string $contactId Contact ID
 * @param string $photoContent Base64 encoded photo content or binary data
 * @param array $options Upload options
 * @return bool Success status
 */
public function uploadContactPhoto(string $contactId, string $photoContent, array $options = []): bool
{
    try {
        if (empty($contactId) || empty($photoContent)) {
            throw new Exception('Contact ID and photo content are required');
        }

        // Validate photo content
        $validation = $this->validatePhotoContent($photoContent);
        if (!$validation['is_valid']) {
            throw new Exception($validation['error']);
        }

        // Convert to binary if base64
        $binaryContent = $this->preparePho toContent($photoContent);

        Log::info('Uploading contact photo', [
            'contact_id' => $contactId,
            'photo_size' => $this->formatBytes(strlen($binaryContent)),
            'content_type' => $validation['content_type'],
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $this->makeRequest('PUT', "/me/contacts/{$contactId}/photo/\$value", [
            'body' => $binaryContent,
            'content_type' => $validation['content_type']
        ]);

        Log::info('Contact photo uploaded successfully', [
            'contact_id' => $contactId,
            'photo_size' => $this->formatBytes(strlen($binaryContent)),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return true;
    } catch (Exception $e) {
        Log::error('Failed to upload contact photo', [
            'contact_id' => $contactId,
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return false;
    }
}

/**
 * Bulk import contacts from various formats
 * NEW method for contact migration
 *
 * @param array $contactsData Array of contact data
 * @param array $options Import options
 * @return array Import results
 */
public function bulkImportContacts(array $contactsData, array $options = []): array
{
    try {
        if (empty($contactsData)) {
            throw new Exception('Contact data array cannot be empty');
        }

        $batchSize = min($options['batch_size'] ?? 10, 20);
        $skipDuplicates = $options['skip_duplicates'] ?? true;
        $updateExisting = $options['update_existing'] ?? false;
        $folder = $options['folder'] ?? 'contacts';

        Log::info('Starting bulk contact import', [
            'total_contacts' => count($contactsData),
            'batch_size' => $batchSize,
            'skip_duplicates' => $skipDuplicates,
            'update_existing' => $updateExisting,
            'folder' => $folder,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $results = [
            'total_processed' => 0,
            'successful' => [],
            'failed' => [],
            'duplicates_skipped' => [],
            'updated' => [],
            'summary' => [
                'success_count' => 0,
                'failure_count' => 0,
                'duplicate_count' => 0,
                'update_count' => 0
            ]
        ];

        // Process in batches
        $batches = array_chunk($contactsData, $batchSize);

        foreach ($batches as $batchIndex => $batch) {
            $batchResults = $this->processBatchContactImport($batch, $options, $batchIndex);

            $results['successful'] = array_merge($results['successful'], $batchResults['successful']);
            $results['failed'] = array_merge($results['failed'], $batchResults['failed']);
            $results['duplicates_skipped'] = array_merge($results['duplicates_skipped'], $batchResults['duplicates_skipped']);
            $results['updated'] = array_merge($results['updated'], $batchResults['updated']);

            $results['total_processed'] += count($batch);

            // Brief pause between batches
            if (count($batches) > 1 && $batchIndex < count($batches) - 1) {
                usleep(500000); // 0.5 second
            }
        }

        // Calculate summary
        $results['summary'] = [
            'success_count' => count($results['successful']),
            'failure_count' => count($results['failed']),
            'duplicate_count' => count($results['duplicates_skipped']),
            'update_count' => count($results['updated']),
            'success_rate' => $results['total_processed'] > 0
                ? round((count($results['successful']) / $results['total_processed']) * 100, 2)
                : 0
        ];

        Log::info('Bulk contact import completed', [
            'total_processed' => $results['total_processed'],
            'successful' => $results['summary']['success_count'],
            'failed' => $results['summary']['failure_count'],
            'success_rate' => $results['summary']['success_rate'] . '%',
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'results' => $results,
            'completed_at' => now()->toISOString()
        ];
    } catch (Exception $e) {
        Log::error('Bulk contact import failed', [
            'error' => $e->getMessage(),
            'total_contacts' => count($contactsData),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'results' => [
                'total_processed' => 0,
                'successful' => [],
                'failed' => [],
                'summary' => ['success_count' => 0, 'failure_count' => count($contactsData)]
            ]
        ];
    }
}

/**
 * Export contacts to various formats
 * NEW method for contact backup and migration
 *
 * @param array $options Export options
 * @return array Export results with contact data
 */
public function exportContacts(array $options = []): array
{
    try {
        $format = $options['format'] ?? 'json'; // json, csv, vcard
        $folder = $options['folder'] ?? 'contacts';
        $includePhotos = $options['include_photos'] ?? false;
        $limit = $options['limit'] ?? null;

        Log::info('Starting contact export', [
            'format' => $format,
            'folder' => $folder,
            'include_photos' => $includePhotos,
            'limit' => $limit,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        // Get all contacts
        $contactsResult = $this->getContacts([
            'folder' => $folder,
            'limit' => $limit ?? 999,
            'select' => $this->getExtendedContactSelectFields()
        ]);

        if (!$contactsResult['success']) {
            throw new Exception('Failed to retrieve contacts: ' . $contactsResult['error']);
        }

        $contacts = $contactsResult['contacts'];

        // Include photos if requested
        if ($includePhotos) {
            $contacts = $this->addPhotosToContacts($contacts);
        }

        // Format according to requested format
        $exportData = $this->formatContactsForExport($contacts, $format, $options);

        Log::info('Contact export completed', [
            'format' => $format,
            'contact_count' => count($contacts),
            'include_photos' => $includePhotos,
            'export_size' => strlen(json_encode($exportData)),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'format' => $format,
            'contact_count' => count($contacts),
            'export_data' => $exportData,
            'export_size_formatted' => $this->formatBytes(strlen(json_encode($exportData))),
            'exported_at' => now()->toISOString()
        ];
    } catch (Exception $e) {
        Log::error('Contact export failed', [
            'format' => $format ?? 'json',
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'export_data' => null
        ];
    }
}

/**
 * Get contact statistics and insights
 * NEW method for contact analytics
 *
 * @param array $options Analysis options
 * @return array Contact statistics and insights
 */
public function getContactStatistics(array $options = []): array
{
    try {
        $folder = $options['folder'] ?? 'contacts';
        $includeCompanyAnalysis = $options['include_company_analysis'] ?? true;
        $includeLocationAnalysis = $options['include_location_analysis'] ?? true;

        Log::info('Generating contact statistics', [
            'folder' => $folder,
            'include_company_analysis' => $includeCompanyAnalysis,
            'include_location_analysis' => $includeLocationAnalysis,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $startTime = microtime(true);

        // Get all contacts for analysis
        $contactsResult = $this->getContacts([
            'folder' => $folder,
            'limit' => 999,
            'select' => $this->getAnalyticsContactSelectFields()
        ]);

        if (!$contactsResult['success']) {
            throw new Exception('Failed to retrieve contacts for analysis');
        }

        $contacts = $contactsResult['contacts'];

        // Generate comprehensive statistics
        $statistics = [
            'overview' => $this->generateContactOverviewStats($contacts),
            'communication' => $this->generateCommunicationStats($contacts),
            'demographics' => $this->generateDemographicStats($contacts),
            'data_quality' => $this->generateDataQualityStats($contacts)
        ];

        // Add company analysis if requested
        if ($includeCompanyAnalysis) {
            $statistics['company_analysis'] = $this->generateCompanyAnalysis($contacts);
        }

        // Add location analysis if requested
        if ($includeLocationAnalysis) {
            $statistics['location_analysis'] = $this->generateLocationAnalysis($contacts);
        }

        $processingTime = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('Contact statistics generated', [
            'total_contacts' => count($contacts),
            'processing_time_ms' => $processingTime,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'statistics' => $statistics,
            'contact_count' => count($contacts),
            'processing_time_ms' => $processingTime,
            'generated_at' => now()->toISOString()
        ];
    } catch (Exception $e) {
        Log::error('Failed to generate contact statistics', [
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'statistics' => []
        ];
    }
}

// HELPER METHODS FOR CONTACTS FUNCTIONALITY

/**
 * Build contacts endpoint based on folder
 * NEW helper method
 */
private function buildContactsEndpoint(string $folder): string
{
    if ($folder === 'contacts' || $folder === 'mycontacts') {
        return '/me/contacts';
    } elseif ($folder === 'all') {
        return '/me/contacts';
    } else {
        // Assume it's a folder ID
        return "/me/contactFolders/{$folder}/contacts";
    }
}

/**
 * Get default contact select fields
 * NEW helper method
 */
private function getDefaultContactSelectFields(): string
{
    return implode(',', [
        'id',
        'displayName',
        'givenName',
        'surname',
        'emailAddresses',
        'businessPhones',
        'homePhones',
        'mobilePhone',
        'companyName',
        'jobTitle',
        'department',
        'officeLocation',
        'businessAddress',
        'homeAddress',
        'birthday',
        'createdDateTime',
        'lastModifiedDateTime',
        'categories'
    ]);
}

/**
 * Get extended contact select fields
 * NEW helper method
 */
private function getExtendedContactSelectFields(): string
{
    return implode(',', [
        'id',
        'displayName',
        'givenName',
        'middleName',
        'surname',
        'nickName',
        'title',
        'generation',
        'emailAddresses',
        'businessPhones',
        'homePhones',
        'mobilePhone',
        'otherAddress',
        'companyName',
        'jobTitle',
        'department',
        'profession',
        'manager',
        'assistantName',
        'officeLocation',
        'businessAddress',
        'homeAddress',
        'otherAddress',
        'birthday',
        'personalNotes',
        'spouseName',
        'children',
        'imAddresses',
        'website',
        'categories',
        'flag',
        'createdDateTime',
        'lastModifiedDateTime',
        'changeKey'
    ]);
}

/**
 * Get analytics contact select fields
 * NEW helper method
 */
private function getAnalyticsContactSelectFields(): string
{
    return implode(',', [
        'id',
        'displayName',
        'emailAddresses',
        'businessPhones',
        'homePhones',
        'mobilePhone',
        'companyName',
        'jobTitle',
        'department',
        'businessAddress',
        'homeAddress',
        'birthday',
        'categories',
        'createdDateTime',
        'lastModifiedDateTime'
    ]);
}

/**
 * Enrich contact data with additional information
 * NEW helper method
 */
private function enrichContactData(array $contact, array $options = []): array
{
    $enriched = [
        'id' => $contact['id'],
        'displayName' => $contact['displayName'] ?? $this->buildDisplayName($contact),
        'firstName' => $contact['givenName'] ?? null,
        'lastName' => $contact['surname'] ?? null,
        'fullName' => $this->buildFullName($contact),
        'company' => $contact['companyName'] ?? null,
        'jobTitle' => $contact['jobTitle'] ?? null,
        'department' => $contact['department'] ?? null,
        'email_addresses' => $this->formatEmailAddresses($contact['emailAddresses'] ?? []),
        'phone_numbers' => $this->formatPhoneNumbers($contact),
        'addresses' => $this->formatAddresses($contact),
        'birthday' => $contact['birthday'] ?? null,
        'categories' => $contact['categories'] ?? [],
        'created_date' => $contact['createdDateTime'] ?? null,
        'modified_date' => $contact['lastModifiedDateTime'] ?? null,
        'has_photo' => false, // Will be updated if photo data is provided
        'contact_quality_score' => $this->calculateContactQualityScore($contact)
    ];

    // Add extended information if requested
    if ($options['include_extended_info'] ?? false) {
        $enriched = array_merge($enriched, [
            'middle_name' => $contact['middleName'] ?? null,
            'nickname' => $contact['nickName'] ?? null,
            'title' => $contact['title'] ?? null,
            'generation' => $contact['generation'] ?? null,
            'profession' => $contact['profession'] ?? null,
            'manager' => $contact['manager'] ?? null,
            'assistant_name' => $contact['assistantName'] ?? null,
            'office_location' => $contact['officeLocation'] ?? null,
            'personal_notes' => $contact['personalNotes'] ?? null,
            'spouse_name' => $contact['spouseName'] ?? null,
            'children' => $contact['children'] ?? [],
            'im_addresses' => $contact['imAddresses'] ?? [],
            'website' => $contact['website'] ?? null,
            'flag' => $contact['flag'] ?? null
        ]);
    }

    // Add photo data if provided
    if (isset($options['photo_data']) && $options['photo_data']) {
        $enriched['has_photo'] = true;
        $enriched['photo'] = $options['photo_data'];
    }

    return $enriched;
}

/**
 * Generate contact insights
 * NEW helper method
 */
private function generateContactInsights(array $contacts): array
{
    $insights = [
        'total_contacts' => count($contacts),
        'with_email' => 0,
        'with_phone' => 0,
        'with_address' => 0,
        'with_company' => 0,
        'with_birthday' => 0,
        'recent_contacts' => 0,
        'average_quality_score' => 0,
        'top_companies' => [],
        'completion_rate' => 0
    ];

    $totalQualityScore = 0;
    $companies = [];
    $recentThreshold = now()->subDays(30);

    foreach ($contacts as $contact) {
        if (!empty($contact['email_addresses'])) {
            $insights['with_email']++;
        }

        if (!empty($contact['phone_numbers'])) {
            $insights['with_phone']++;
        }

        if (!empty($contact['addresses'])) {
            $insights['with_address']++;
        }

        if (!empty($contact['company'])) {
            $insights['with_company']++;
            $company = $contact['company'];
            $companies[$company] = ($companies[$company] ?? 0) + 1;
        }

        if (!empty($contact['birthday'])) {
            $insights['with_birthday']++;
        }

        if ($contact['created_date'] && Carbon::parse($contact['created_date'])->isAfter($recentThreshold)) {
            $insights['recent_contacts']++;
        }

        $totalQualityScore += $contact['contact_quality_score'] ?? 0;
    }

    // Calculate averages and rates
    if (count($contacts) > 0) {
        $insights['average_quality_score'] = round($totalQualityScore / count($contacts), 2);
        $insights['completion_rate'] = round(
            (($insights['with_email'] + $insights['with_phone'] + $insights['with_address']) / (count($contacts) * 3)) * 100,
            2
        );
    }

    // Top companies
    if (!empty($companies)) {
        arsort($companies);
        $insights['top_companies'] = array_slice($companies, 0, 5, true);
    }

    return $insights;
}

/**
 * Sanitize contact data for security
 * NEW helper method
 */
private function sanitizeContactData(array $contactData): array
{
    $sanitized = [];

    // Define allowed fields for contact creation/update
    $allowedFields = [
        'displayName', 'givenName', 'middleName', 'surname', 'nickName', 'title', 'generation',
        'emailAddresses', 'businessPhones', 'homePhones', 'mobilePhone', 'otherPhones',
        'companyName', 'jobTitle', 'department', 'profession', 'manager', 'assistantName',
        'officeLocation', 'businessAddress', 'homeAddress', 'otherAddress',
        'birthday', 'personalNotes', 'spouseName', 'children', 'imAddresses',
        'website', 'categories', 'flag'
    ];

    foreach ($contactData as $field => $value) {
        if (in_array($field, $allowedFields)) {
            if (is_string($value)) {
                $sanitized[$field] = trim(strip_tags($value));
            } elseif (is_array($value)) {
                $sanitized[$field] = $this->sanitizeContactArray($value, $field);
            } else {
                $sanitized[$field] = $value;
            }
        }
    }

    return $sanitized;
}

/**
 * Validate contact data
 * NEW helper method
 */
private function validateContactData(array $contactData, bool $isUpdate = false): array
{
    $validation = [
        'is_valid' => true,
        'errors' => []
    ];

    // For creation, displayName or givenName+surname is required
    if (!$isUpdate) {
        if (empty($contactData['displayName']) &&
            (empty($contactData['givenName']) || empty($contactData['surname']))) {
            $validation['errors'][] = 'Display name or first name + last name is required';
        }
    }

    // Validate email addresses if provided
    if (isset($contactData['emailAddresses'])) {
        foreach ($contactData['emailAddresses'] as $emailData) {
            $email = $emailData['address'] ?? $emailData;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validation['errors'][] = "Invalid email address: {$email}";
            }
        }
    }

    // Validate phone numbers format
    if (isset($contactData['businessPhones'])) {
        foreach ($contactData['businessPhones'] as $phone) {
            if (strlen($phone) > 64) {
                $validation['errors'][] = 'Phone number cannot exceed 64 characters';
                break;
            }
        }
    }

    // Validate birthday format
    if (isset($contactData['birthday'])) {
        try {
            Carbon::parse($contactData['birthday']);
        } catch (Exception $e) {
            $validation['errors'][] = 'Invalid birthday format';
        }
    }

    $validation['is_valid'] = empty($validation['errors']);
    return $validation;
}

/**
 * Sanitize contact array fields
 * NEW helper method
 */
private function sanitizeContactArray(array $arrayData, string $fieldType): array
{
    $sanitized = [];

    switch ($fieldType) {
        case 'emailAddresses':
            foreach ($arrayData as $email) {
                if (is_string($email)) {
                    $sanitized[] = ['address' => trim($email), 'name' => null];
                } elseif (is_array($email) && isset($email['address'])) {
                    $sanitized[] = [
                        'address' => trim($email['address']),
                        'name' => isset($email['name']) ? trim(strip_tags($email['name'])) : null
                    ];
                }
            }
            break;

        case 'businessPhones':
        case 'homePhones':
        case 'otherPhones':
            foreach ($arrayData as $phone) {
                if (is_string($phone)) {
                    $sanitized[] = trim($phone);
                }
            }
            break;

        case 'categories':
        case 'children':
        case 'imAddresses':
            foreach ($arrayData as $item) {
                if (is_string($item)) {
                    $sanitized[] = trim(strip_tags($item));
                }
            }
            break;

        default:
            $sanitized = $arrayData;
    }

    return $sanitized;
}

/**
 * Calculate contact quality score
 * NEW helper method
 */
private function calculateContactQualityScore(array $contact): int
{
    $score = 0;

    // Basic information (40 points max)
    if (!empty($contact['displayName'])) $score += 10;
    if (!empty($contact['givenName'])) $score += 10;
    if (!empty($contact['surname'])) $score += 10;
    if (!empty($contact['jobTitle'])) $score += 10;

    // Contact information (40 points max)
    if (!empty($contact['emailAddresses'])) $score += 20;
    if (!empty($contact['businessPhones']) || !empty($contact['homePhones']) || !empty($contact['mobilePhone'])) {
        $score += 20;
    }

    // Additional information (20 points max)
    if (!empty($contact['companyName'])) $score += 5;
    if (!empty($contact['businessAddress']) || !empty($contact['homeAddress'])) $score += 5;
    if (!empty($contact['birthday'])) $score += 5;
    if (!empty($contact['personalNotes'])) $score += 5;

    return min($score, 100);
}

/**
 * Build display name from contact parts
 * NEW helper method
 */
private function buildDisplayName(array $contact): string
{
    $parts = array_filter([
        $contact['givenName'] ?? null,
        $contact['surname'] ?? null
    ]);

    return implode(' ', $parts) ?: 'Unnamed Contact';
}

/**
 * Build full name from contact parts
 * NEW helper method
 */
private function buildFullName(array $contact): string
{
    $parts = array_filter([
        $contact['title'] ?? null,
        $contact['givenName'] ?? null,
        $contact['middleName'] ?? null,
        $contact['surname'] ?? null,
        $contact['generation'] ?? null
    ]);

    return implode(' ', $parts) ?: ($contact['displayName'] ?? 'Unnamed Contact');
}

/**
 * Format email addresses consistently
 * NEW helper method
 */
private function formatEmailAddresses(array $emailAddresses): array
{
    $formatted = [];

    foreach ($emailAddresses as $email) {
        if (is_string($email)) {
            $formatted[] = [
                'address' => $email,
                'name' => null,
                'type' => 'unknown'
            ];
        } elseif (is_array($email)) {
            $formatted[] = [
                'address' => $email['address'] ?? null,
                'name' => $email['name'] ?? null,
                'type' => $email['type'] ?? 'unknown'
            ];
        }
    }

    return $formatted;
}

/**
 * Format phone numbers from contact
 * NEW helper method
 */
private function formatPhoneNumbers(array $contact): array
{
    $phones = [];

    // Business phones
    foreach ($contact['businessPhones'] ?? [] as $phone) {
        $phones[] = [
            'number' => $phone,
            'type' => 'business'
        ];
    }

    // Home phones
    foreach ($contact['homePhones'] ?? [] as $phone) {
        $phones[] = [
            'number' => $phone,
            'type' => 'home'
        ];
    }

    // Mobile phone
    if (!empty($contact['mobilePhone'])) {
        $phones[] = [
            'number' => $contact['mobilePhone'],
            'type' => 'mobile'
        ];
    }

    return $phones;
}

/**
 * Format addresses from contact
 * NEW helper method
 */
private function formatAddresses(array $contact): array
{
    $addresses = [];

    // Business address
    if (!empty($contact['businessAddress'])) {
        $addresses[] = array_merge($contact['businessAddress'], ['type' => 'business']);
    }

    // Home address
    if (!empty($contact['homeAddress'])) {
        $addresses[] = array_merge($contact['homeAddress'], ['type' => 'home']);
    }

    // Other address
    if (!empty($contact['otherAddress'])) {
        $addresses[] = array_merge($contact['otherAddress'], ['type' => 'other']);
    }

    return $addresses;
}

/**
 * Process batch contact import
 * NEW helper method
 */
private function processBatchContactImport(array $batch, array $options, int $batchIndex): array
{
    $results = [
        'successful' => [],
        'failed' => [],
        'duplicates_skipped' => [],
        'updated' => []
    ];

    foreach ($batch as $index => $contactData) {
        try {
            // Check for duplicates if enabled
            if ($options['skip_duplicates'] && $this->isDuplicateContact($contactData)) {
                $results['duplicates_skipped'][] = [
                    'index' => $index,
                    'display_name' => $contactData['displayName'] ?? 'N/A',
                    'reason' => 'Duplicate contact found'
                ];
                continue;
            }

            // Try to update existing if enabled
            if ($options['update_existing']) {
                $existingContact = $this->findExistingContact($contactData);
                if ($existingContact) {
                    $updateResult = $this->updateContact($existingContact['id'], $contactData, $options);
                    if ($updateResult['success']) {
                        $results['updated'][] = [
                            'index' => $index,
                            'contact_id' => $existingContact['id'],
                            'display_name' => $contactData['displayName'] ?? 'N/A'
                        ];
                        continue;
                    }
                }
            }

            // Create new contact
            $createResult = $this->createContact($contactData, $options);
            if ($createResult['success']) {
                $results['successful'][] = [
                    'index' => $index,
                    'contact_id' => $createResult['contact_id'],
                    'display_name' => $contactData['displayName'] ?? 'N/A'
                ];
            } else {
                $results['failed'][] = [
                    'index' => $index,
                    'display_name' => $contactData['displayName'] ?? 'N/A',
                    'error' => $createResult['error']
                ];
            }
        } catch (Exception $e) {
            $results['failed'][] = [
                'index' => $index,
                'display_name' => $contactData['displayName'] ?? 'N/A',
                'error' => $e->getMessage()
            ];
        }
    }

    return $results;
}

/**
 * Get contact count in folder
 * NEW helper method
 */
private function getContactCountInFolder(string $folderId): int
{
    try {
        $response = $this->makeRequest('GET', "/me/contactFolders/{$folderId}/contacts", [
            'query' => ['$count' => 'true', '$top' => 1]
        ]);

        return $response['@odata.count'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Check if contact appears to be important
 * NEW helper method
 */
private function isImportantContact(array $contact): bool
{
    // Consider a contact important if they have:
    // - Multiple contact methods
    // - Company information
    // - Recent communication
    // - Categories indicating importance

    $importance_score = 0;

    if (!empty($contact['emailAddresses']) && count($contact['emailAddresses']) > 0) {
        $importance_score += 2;
    }

    if (!empty($contact['businessPhones']) || !empty($contact['mobilePhone'])) {
        $importance_score += 2;
    }

    if (!empty($contact['companyName'])) {
        $importance_score += 1;
    }

    if (!empty($contact['jobTitle'])) {
        $importance_score += 1;
    }

    if (!empty($contact['categories'])) {
        $categories = array_map('strtolower', $contact['categories']);
        if (array_intersect($categories, ['important', 'vip', 'client', 'manager', 'boss'])) {
            $importance_score += 3;
        }
    }

    return $importance_score >= 5;
}

/**
 * Backup contact before deletion
 * NEW helper method
 */
private function backupContact(string $contactId, array $contactDetails): void
{
    try {
        // Store backup in cache or database
        $backupKey = "contact_backup_{$contactId}_" . now()->timestamp;
        $backupData = [
            'contact_id' => $contactId,
            'contact_data' => $contactDetails,
            'deleted_by' => $this->auth->email ?? 'unknown',
            'deleted_at' => now()->toISOString()
        ];

        Cache::put($backupKey, $backupData, now()->addDays(30)); // Keep backup for 30 days

        Log::info('Contact backup created', [
            'contact_id' => $contactId,
            'backup_key' => $backupKey,
            'user' => $this->auth->email ?? 'unknown'
        ]);
    } catch (Exception $e) {
        Log::warning('Failed to create contact backup', [
            'contact_id' => $contactId,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Validate photo content
 * NEW helper method
 */
private function validatePhotoContent(string $photoContent): array
{
    $validation = [
        'is_valid' => false,
        'error' => null,
        'content_type' => 'image/jpeg'
    ];

    // Check if it's base64 encoded
    if (base64_encode(base64_decode($photoContent, true)) === $photoContent) {
        $binaryContent = base64_decode($photoContent);
    } else {
        $binaryContent = $photoContent;
    }

    // Check file size (max 4MB)
    if (strlen($binaryContent) > 4194304) {
        $validation['error'] = 'Photo size cannot exceed 4MB';
        return $validation;
    }

    // Check for image signatures
    $signatures = [
        'image/jpeg' => ["\xFF\xD8\xFF"],
        'image/png' => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
        'image/gif' => ["GIF87a", "GIF89a"]
    ];

    foreach ($signatures as $mimeType => $sigs) {
        foreach ($sigs as $sig) {
            if (str_starts_with($binaryContent, $sig)) {
                $validation['is_valid'] = true;
                $validation['content_type'] = $mimeType;
                return $validation;
            }
        }
    }

    $validation['error'] = 'Invalid image format. Supported formats: JPEG, PNG, GIF';
    return $validation;
}

/**
 * Prepare photo content for upload
 * NEW helper method
 */
private function preparePhotoContent(string $photoContent): string
{
    // Check if it's base64 encoded and decode if necessary
    if (base64_encode(base64_decode($photoContent, true)) === $photoContent) {
        return base64_decode($photoContent);
    }

    return $photoContent;
}

/**
 * Generate search result insights
 * NEW helper method
 */
private function generateSearchResultInsights(array $contacts, string $query): array
{
    $insights = [
        'total_results' => count($contacts),
        'match_types' => [
            'name_matches' => 0,
            'email_matches' => 0,
            'company_matches' => 0,
            'phone_matches' => 0
        ],
        'companies_found' => [],
        'quality_distribution' => [
            'high' => 0,    // 80-100
            'medium' => 0,  // 50-79
            'low' => 0      // 0-49
        ]
    ];

    $queryLower = strtolower($query);

    foreach ($contacts as $contact) {
        // Analyze match types
        if (str_contains(strtolower($contact['displayName'] ?? ''), $queryLower)) {
            $insights['match_types']['name_matches']++;
        }

        foreach ($contact['email_addresses'] ?? [] as $email) {
            if (str_contains(strtolower($email['address'] ?? ''), $queryLower)) {
                $insights['match_types']['email_matches']++;
                break;
            }
        }

        if (str_contains(strtolower($contact['company'] ?? ''), $queryLower)) {
            $insights['match_types']['company_matches']++;
        }

        foreach ($contact['phone_numbers'] ?? [] as $phone) {
            if (str_contains($phone['number'] ?? '', $query)) {
                $insights['match_types']['phone_matches']++;
                break;
            }
        }

        // Company distribution
        if (!empty($contact['company'])) {
            $company = $contact['company'];
            $insights['companies_found'][$company] = ($insights['companies_found'][$company] ?? 0) + 1;
        }

        // Quality distribution
        $score = $contact['contact_quality_score'] ?? 0;
        if ($score >= 80) {
            $insights['quality_distribution']['high']++;
        } elseif ($score >= 50) {
            $insights['quality_distribution']['medium']++;
        } else {
            $insights['quality_distribution']['low']++;
        }
    }

    return $insights;
}

/**
 * Add photos to contacts array
 * NEW helper method
 */
private function addPhotosToContacts(array $contacts): array
{
    foreach ($contacts as &$contact) {
        $photo = $this->getContactPhoto($contact['id']);
        if ($photo) {
            $contact['photo'] = $photo;
            $contact['has_photo'] = true;
        }
    }

    return $contacts;
}

/**
 * Format contacts for export
 * NEW helper method
 */
private function formatContactsForExport(array $contacts, string $format, array $options): array
{
    switch (strtolower($format)) {
        case 'csv':
            return $this->formatContactsAsCsv($contacts, $options);
        case 'vcard':
            return $this->formatContactsAsVcard($contacts, $options);
        case 'json':
        default:
            return $contacts;
    }
}

/**
 * Format contacts as CSV
 * NEW helper method
 */
private function formatContactsAsCsv(array $contacts, array $options): array
{
    $csvData = [];

    // CSV Headers
    $headers = [
        'ID', 'Display Name', 'First Name', 'Last Name', 'Company', 'Job Title',
        'Email 1', 'Email 2', 'Business Phone', 'Mobile Phone', 'Home Phone',
        'Business Address', 'Home Address', 'Birthday', 'Categories', 'Notes'
    ];

    $csvData[] = $headers;

    foreach ($contacts as $contact) {
        $emails = $contact['email_addresses'] ?? [];
        $phones = $contact['phone_numbers'] ?? [];
        $addresses = $contact['addresses'] ?? [];

        $businessPhone = '';
        $mobilePhone = '';
        $homePhone = '';

        foreach ($phones as $phone) {
            switch ($phone['type']) {
                case 'business':
                    if (empty($businessPhone)) $businessPhone = $phone['number'];
                    break;
                case 'mobile':
                    if (empty($mobilePhone)) $mobilePhone = $phone['number'];
                    break;
                case 'home':
                    if (empty($homePhone)) $homePhone = $phone['number'];
                    break;
            }
        }

        $businessAddress = '';
        $homeAddress = '';

        foreach ($addresses as $address) {
            $fullAddress = implode(', ', array_filter([
                $address['street'] ?? '',
                $address['city'] ?? '',
                $address['state'] ?? '',
                $address['postalCode'] ?? '',
                $address['countryOrRegion'] ?? ''
            ]));

            if ($address['type'] === 'business' && empty($businessAddress)) {
                $businessAddress = $fullAddress;
            } elseif ($address['type'] === 'home' && empty($homeAddress)) {
                $homeAddress = $fullAddress;
            }
        }

        $csvData[] = [
            $contact['id'],
            $contact['displayName'] ?? '',
            $contact['firstName'] ?? '',
            $contact['lastName'] ?? '',
            $contact['company'] ?? '',
            $contact['jobTitle'] ?? '',
            $emails[0]['address'] ?? '',
            $emails[1]['address'] ?? '',
            $businessPhone,
            $mobilePhone,
            $homePhone,
            $businessAddress,
            $homeAddress,
            $contact['birthday'] ?? '',
            implode('; ', $contact['categories'] ?? []),
            $contact['personal_notes'] ?? ''
        ];
    }

    return $csvData;
}

/**
 * Format contacts as vCard
 * NEW helper method
 */
private function formatContactsAsVcard(array $contacts, array $options): string
{
    $vcardData = '';

    foreach ($contacts as $contact) {
        $vcardData .= "BEGIN:VCARD\r\n";
        $vcardData .= "VERSION:3.0\r\n";

        // Name
        $vcardData .= "FN:" . ($contact['displayName'] ?? '') . "\r\n";
        if (!empty($contact['lastName']) || !empty($contact['firstName'])) {
            $vcardData .= "N:" . ($contact['lastName'] ?? '') . ";" .
                         ($contact['firstName'] ?? '') . ";" .
                         ($contact['middle_name'] ?? '') . ";" .
                         ($contact['title'] ?? '') . ";" .
                         ($contact['generation'] ?? '') . "\r\n";
        }

        // Organization
        if (!empty($contact['company'])) {
            $vcardData .= "ORG:" . $contact['company'];
            if (!empty($contact['department'])) {
                $vcardData .= ";" . $contact['department'];
            }
            $vcardData .= "\r\n";
        }

        // Title
        if (!empty($contact['jobTitle'])) {
            $vcardData .= "TITLE:" . $contact['jobTitle'] . "\r\n";
        }

        // Email addresses
        foreach ($contact['email_addresses'] ?? [] as $index => $email) {
            $type = $index === 0 ? 'PREF' : 'INTERNET';
            $vcardData .= "EMAIL;TYPE=$type:" . $email['address'] . "\r\n";
        }

        // Phone numbers
        foreach ($contact['phone_numbers'] ?? [] as $phone) {
            $type = strtoupper($phone['type']);
            $vcardData .= "TEL;TYPE=$type:" . $phone['number'] . "\r\n";
        }

        // Addresses
        foreach ($contact['addresses'] ?? [] as $address) {
            $type = strtoupper($address['type']);
            $vcardData .= "ADR;TYPE=$type:;;" .
                         ($address['street'] ?? '') . ";" .
                         ($address['city'] ?? '') . ";" .
                         ($address['state'] ?? '') . ";" .
                         ($address['postalCode'] ?? '') . ";" .
                         ($address['countryOrRegion'] ?? '') . "\r\n";
        }

        // Birthday
        if (!empty($contact['birthday'])) {
            $birthday = Carbon::parse($contact['birthday'])->format('Y-m-d');
            $vcardData .= "BDAY:$birthday\r\n";
        }

        // Notes
        if (!empty($contact['personal_notes'])) {
            $vcardData .= "NOTE:" . str_replace(["\r", "\n"], [" ", " "], $contact['personal_notes']) . "\r\n";
        }

        // Categories
        if (!empty($contact['categories'])) {
            $vcardData .= "CATEGORIES:" . implode(',', $contact['categories']) . "\r\n";
        }

        $vcardData .= "END:VCARD\r\n\r\n";
    }

    return $vcardData;
}

/**
 * Generate contact overview statistics
 * NEW helper method
 */
private function generateContactOverviewStats(array $contacts): array
{
    $stats = [
        'total_contacts' => count($contacts),
        'with_complete_name' => 0,
        'with_company_info' => 0,
        'recent_additions' => 0,
        'recently_modified' => 0,
        'quality_scores' => [
            'average' => 0,
            'high_quality' => 0,
            'needs_improvement' => 0
        ]
    ];

    $totalQuality = 0;
    $recentThreshold = now()->subDays(30);

    foreach ($contacts as $contact) {
        // Complete name check
        if (!empty($contact['firstName']) && !empty($contact['lastName'])) {
            $stats['with_complete_name']++;
        }

        // Company info check
        if (!empty($contact['company']) && !empty($contact['jobTitle'])) {
            $stats['with_company_info']++;
        }

        // Recent additions
        if ($contact['created_date'] && Carbon::parse($contact['created_date'])->isAfter($recentThreshold)) {
            $stats['recent_additions']++;
        }

        // Recently modified
        if ($contact['modified_date'] && Carbon::parse($contact['modified_date'])->isAfter($recentThreshold)) {
            $stats['recently_modified']++;
        }

        // Quality scoring
        $quality = $contact['contact_quality_score'] ?? 0;
        $totalQuality += $quality;

        if ($quality >= 80) {
            $stats['quality_scores']['high_quality']++;
        } elseif ($quality < 50) {
            $stats['quality_scores']['needs_improvement']++;
        }
    }

    if ($stats['total_contacts'] > 0) {
        $stats['quality_scores']['average'] = round($totalQuality / $stats['total_contacts'], 2);
    }

    return $stats;
}

/**
 * Generate communication statistics
 * NEW helper method
 */
private function generateCommunicationStats(array $contacts): array
{
    $stats = [
        'email_contacts' => 0,
        'phone_contacts' => 0,
        'multiple_emails' => 0,
        'multiple_phones' => 0,
        'complete_contact_info' => 0,
        'email_domains' => [],
        'phone_types' => [
            'business' => 0,
            'mobile' => 0,
            'home' => 0
        ]
    ];

    foreach ($contacts as $contact) {
        $emailCount = count($contact['email_addresses'] ?? []);
        $phoneCount = count($contact['phone_numbers'] ?? []);

        if ($emailCount > 0) {
            $stats['email_contacts']++;
            if ($emailCount > 1) {
                $stats['multiple_emails']++;
            }

            // Track email domains
            foreach ($contact['email_addresses'] as $email) {
                $domain = substr(strrchr($email['address'], "@"), 1);
                $stats['email_domains'][$domain] = ($stats['email_domains'][$domain] ?? 0) + 1;
            }
        }

        if ($phoneCount > 0) {
            $stats['phone_contacts']++;
            if ($phoneCount > 1) {
                $stats['multiple_phones']++;
            }

            // Track phone types
            foreach ($contact['phone_numbers'] as $phone) {
                $type = $phone['type'];
                if (isset($stats['phone_types'][$type])) {
                    $stats['phone_types'][$type]++;
                }
            }
        }

        // Complete contact info (email + phone + address)
        if ($emailCount > 0 && $phoneCount > 0 && !empty($contact['addresses'])) {
            $stats['complete_contact_info']++;
        }
    }

    // Sort email domains by frequency
    if (!empty($stats['email_domains'])) {
        arsort($stats['email_domains']);
        $stats['email_domains'] = array_slice($stats['email_domains'], 0, 10, true);
    }

    return $stats;
}

/**
 * Generate demographic statistics
 * NEW helper method
 */
private function generateDemographicStats(array $contacts): array
{
    $stats = [
        'with_birthday' => 0,
        'age_groups' => [
            'under_30' => 0,
            '30-50' => 0,
            '50-65' => 0,
            'over_65' => 0,
            'unknown' => 0
        ],
        'locations' => [],
        'upcoming_birthdays' => 0
    ];

    $now = now();
    $nextMonth = $now->copy()->addMonth();

    foreach ($contacts as $contact) {
        if (!empty($contact['birthday'])) {
            $stats['with_birthday']++;

            try {
                $birthday = Carbon::parse($contact['birthday']);
                $age = $birthday->diffInYears($now);

                // Age groups
                if ($age < 30) {
                    $stats['age_groups']['under_30']++;
                } elseif ($age < 50) {
                    $stats['age_groups']['30-50']++;
                } elseif ($age < 65) {
                    $stats['age_groups']['50-65']++;
                } else {
                    $stats['age_groups']['over_65']++;
                }

                // Upcoming birthdays (next 30 days)
                $nextBirthday = $birthday->copy()->year($now->year);
                if ($nextBirthday->isPast()) {
                    $nextBirthday->addYear();
                }

                if ($nextBirthday->isBetween($now, $nextMonth)) {
                    $stats['upcoming_birthdays']++;
                }
            } catch (Exception $e) {
                $stats['age_groups']['unknown']++;
            }
        } else {
            $stats['age_groups']['unknown']++;
        }

        // Location tracking
        foreach ($contact['addresses'] ?? [] as $address) {
            $location = $address['city'] ?? $address['state'] ?? 'Unknown';
            $stats['locations'][$location] = ($stats['locations'][$location] ?? 0) + 1;
        }
    }

    // Sort locations by frequency
    if (!empty($stats['locations'])) {
        arsort($stats['locations']);
        $stats['locations'] = array_slice($stats['locations'], 0, 10, true);
    }

    return $stats;
}

/**
 * Generate data quality statistics
 * NEW helper method
 */
private function generateDataQualityStats(array $contacts): array
{
    $stats = [
        'completeness' => [
            'full_name' => 0,
            'contact_method' => 0,
            'professional_info' => 0,
            'personal_info' => 0
        ],
        'data_issues' => [
            'missing_email' => 0,
            'missing_phone' => 0,
            'missing_company' => 0,
            'duplicate_names' => 0
        ],
        'improvement_suggestions' => []
    ];

    $nameFrequency = [];

    foreach ($contacts as $contact) {
        // Completeness tracking
        if (!empty($contact['firstName']) && !empty($contact['lastName'])) {
            $stats['completeness']['full_name']++;
        }

        if (!empty($contact['email_addresses']) || !empty($contact['phone_numbers'])) {
            $stats['completeness']['contact_method']++;
        }

        if (!empty($contact['company']) && !empty($contact['jobTitle'])) {
            $stats['completeness']['professional_info']++;
        }

        if (!empty($contact['birthday']) || !empty($contact['addresses'])) {
            $stats['completeness']['personal_info']++;
        }

        // Data issues
        if (empty($contact['email_addresses'])) {
            $stats['data_issues']['missing_email']++;
        }

        if (empty($contact['phone_numbers'])) {
            $stats['data_issues']['missing_phone']++;
        }

        if (empty($contact['company'])) {
            $stats['data_issues']['missing_company']++;
        }

        // Track name frequency for duplicate detection
        $fullName = trim(($contact['firstName'] ?? '') . ' ' . ($contact['lastName'] ?? ''));
        if (!empty($fullName)) {
            $nameFrequency[$fullName] = ($nameFrequency[$fullName] ?? 0) + 1;
        }
    }

    // Count duplicate names
    foreach ($nameFrequency as $name => $count) {
        if ($count > 1) {
            $stats['data_issues']['duplicate_names'] += $count;
        }
    }

    // Generate improvement suggestions
    $totalContacts = count($contacts);
    if ($totalContacts > 0) {
        $missingEmailRate = ($stats['data_issues']['missing_email'] / $totalContacts) * 100;
        $missingPhoneRate = ($stats['data_issues']['missing_phone'] / $totalContacts) * 100;
        $missingCompanyRate = ($stats['data_issues']['missing_company'] / $totalContacts) * 100;

        if ($missingEmailRate > 50) {
            $stats['improvement_suggestions'][] = 'Consider adding email addresses to improve communication';
        }

        if ($missingPhoneRate > 60) {
            $stats['improvement_suggestions'][] = 'Add phone numbers for better contact accessibility';
        }

        if ($missingCompanyRate > 70) {
            $stats['improvement_suggestions'][] = 'Include company information for better organization';
        }

        if ($stats['data_issues']['duplicate_names'] > 0) {
            $stats['improvement_suggestions'][] = 'Review contacts with duplicate names for potential merging';
        }
    }

    return $stats;
}

/**
 * Generate company analysis
 * NEW helper method
 */
private function generateCompanyAnalysis(array $contacts): array
{
    $analysis = [
        'total_companies' => 0,
        'contacts_with_company' => 0,
        'top_companies' => [],
        'industry_distribution' => [],
        'company_sizes' => [
            'large' => 0,    // 5+ contacts
            'medium' => 0,   // 2-4 contacts
            'small' => 0     // 1 contact
        ]
    ];

    $companies = [];

    foreach ($contacts as $contact) {
        if (!empty($contact['company'])) {
            $analysis['contacts_with_company']++;
            $company = $contact['company'];
            $companies[$company] = ($companies[$company] ?? 0) + 1;
        }
    }

    $analysis['total_companies'] = count($companies);

    // Sort companies by contact count
    if (!empty($companies)) {
        arsort($companies);
        $analysis['top_companies'] = array_slice($companies, 0, 10, true);

        // Categorize company sizes
        foreach ($companies as $company => $contactCount) {
            if ($contactCount >= 5) {
                $analysis['company_sizes']['large']++;
            } elseif ($contactCount >= 2) {
                $analysis['company_sizes']['medium']++;
            } else {
                $analysis['company_sizes']['small']++;
            }
        }
    }

    return $analysis;
}

/**
 * Generate location analysis
 * NEW helper method
 */
private function generateLocationAnalysis(array $contacts): array
{
    $analysis = [
        'contacts_with_location' => 0,
        'cities' => [],
        'states' => [],
        'countries' => [],
        'geographic_distribution' => [
            'domestic' => 0,
            'international' => 0,
            'unknown' => 0
        ]
    ];

    $userCountry = 'United States'; // Could be configured or detected

    foreach ($contacts as $contact) {
        $hasLocation = false;

        foreach ($contact['addresses'] ?? [] as $address) {
            if (!empty($address['city']) || !empty($address['state']) || !empty($address['countryOrRegion'])) {
                $hasLocation = true;

                if (!empty($address['city'])) {
                    $city = $address['city'];
                    $analysis['cities'][$city] = ($analysis['cities'][$city] ?? 0) + 1;
                }

                if (!empty($address['state'])) {
                    $state = $address['state'];
                    $analysis['states'][$state] = ($analysis['states'][$state] ?? 0) + 1;
                }

                if (!empty($address['countryOrRegion'])) {
                    $country = $address['countryOrRegion'];
                    $analysis['countries'][$country] = ($analysis['countries'][$country] ?? 0) + 1;

                    if ($country === $userCountry) {
                        $analysis['geographic_distribution']['domestic']++;
                    } else {
                        $analysis['geographic_distribution']['international']++;
                    }
                } else {
                    $analysis['geographic_distribution']['unknown']++;
                }
            }
        }

        if ($hasLocation) {
            $analysis['contacts_with_location']++;
        } else {
            $analysis['geographic_distribution']['unknown']++;
        }
    }

    // Sort by frequency and limit results
    foreach (['cities', 'states', 'countries'] as $key) {
        if (!empty($analysis[$key])) {
            arsort($analysis[$key]);
            $analysis[$key] = array_slice($analysis[$key], 0, 10, true);
        }
    }

    return $analysis;
}

/**
 * Check if contact is duplicate
 * NEW helper method
 */
private function isDuplicateContact(array $contactData): bool
{
    try {
        $searchQuery = '';

        // Build search query based on available data
        if (!empty($contactData['emailAddresses'])) {
            $email = is_array($contactData['emailAddresses'][0])
                ? $contactData['emailAddresses'][0]['address']
                : $contactData['emailAddresses'][0];
            $searchQuery = $email;
        } elseif (!empty($contactData['displayName'])) {
            $searchQuery = $contactData['displayName'];
        } elseif (!empty($contactData['givenName']) && !empty($contactData['surname'])) {
            $searchQuery = $contactData['givenName'] . ' ' . $contactData['surname'];
        }

        if (empty($searchQuery)) {
            return false;
        }

        $searchResult = $this->searchContacts($searchQuery, ['limit' => 5]);

        return $searchResult['success'] && $searchResult['count'] > 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Find existing contact by data
 * NEW helper method
 */
private function findExistingContact(array $contactData): ?array
{
    try {
        $searchQuery = '';

        // Build search query based on available data
        if (!empty($contactData['emailAddresses'])) {
            $email = is_array($contactData['emailAddresses'][0])
                ? $contactData['emailAddresses'][0]['address']
                : $contactData['emailAddresses'][0];
            $searchQuery = $email;
        } elseif (!empty($contactData['displayName'])) {
            $searchQuery = $contactData['displayName'];
        }

        if (empty($searchQuery)) {
            return null;
        }

        $searchResult = $this->searchContacts($searchQuery, ['limit' => 1]);

        if ($searchResult['success'] && $searchResult['count'] > 0) {
            return $searchResult['contacts'][0];
        }

        return null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Add contact to groups
 * NEW helper method
 */
private function addContactToGroups(string $contactId, array $groups): void
{
    foreach ($groups as $groupId) {
        try {
            // This would require additional Microsoft Graph API calls
            // Implementation depends on specific group management requirements
            Log::info('Contact group assignment requested', [
                'contact_id' => $contactId,
                'group_id' => $groupId,
                'user' => $this->auth->email ?? 'unknown'
            ]);
        } catch (Exception $e) {
            Log::warning('Failed to add contact to group', [
                'contact_id' => $contactId,
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
        }
    }
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



// USER DISCOVERY AND PRESENCE MANAGEMENT
// Complete implementation for Microsoft Graph Users and Presence APIs

/**
 * Get all users in the organization with comprehensive filtering
 * Enhanced version with presence integration and advanced analytics
 *
 * @param array $options Query options (limit, filter, presence, etc.)
 * @return array Array of users with optional presence information
 */
public function getAllUsers(array $options = []): array
{
    try {
        // Parse options with defaults
        $top = min($options['limit'] ?? 100, 999); // Microsoft Graph limit
        $skip = $options['skip'] ?? 0;
        $filter = $options['filter'] ?? null;
        $orderBy = $options['orderBy'] ?? 'displayName';
        $select = $options['select'] ?? $this->getDefaultUserSelectFields();
        $includePresence = $options['include_presence'] ?? false;
        $includePhotos = $options['include_photos'] ?? false;
        $accountEnabled = $options['account_enabled'] ?? true;

        Log::info('Fetching all organization users', [
            'limit' => $top,
            'skip' => $skip,
            'include_presence' => $includePresence,
            'include_photos' => $includePhotos,
            'account_enabled' => $accountEnabled,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        // Build query parameters
        $query = [
            '$top' => $top,
            '$skip' => $skip,
            '$select' => $select,
            '$orderby' => $orderBy
        ];

        // Build filter conditions
        $filters = [];

        if ($accountEnabled !== null) {
            $filters[] = "accountEnabled eq " . ($accountEnabled ? 'true' : 'false');
        }

        // Add user type filter (exclude guests by default unless specified)
        if (!($options['include_guests'] ?? false)) {
            $filters[] = "userType eq 'Member'";
        }

        // Add custom filter if provided
        if ($filter) {
            $filters[] = $filter;
        }

        // Add department filter if specified
        if (!empty($options['department'])) {
            $filters[] = "department eq '{$options['department']}'";
        }

        // Add location filter if specified
        if (!empty($options['location'])) {
            $filters[] = "(city eq '{$options['location']}' or officeLocation eq '{$options['location']}')";
        }

        // Add job title filter if specified
        if (!empty($options['job_title_contains'])) {
            $filters[] = "startswith(jobTitle, '{$options['job_title_contains']}')";
        }

        if (!empty($filters)) {
            $query['$filter'] = implode(' and ', $filters);
        }

        $startTime = microtime(true);

        // Get users from Microsoft Graph
        $response = $this->makeRequest('GET', '/users', ['query' => $query]);
        $users = $response['value'] ?? [];

        $fetchTime = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('Users fetched from Graph API', [
            'user_count' => count($users),
            'fetch_time_ms' => $fetchTime,
            'has_more' => !empty($response['@odata.nextLink']),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        // Process and enrich user data
        $processedUsers = [];
        $presenceStartTime = microtime(true);

        // Get presence information for all users if requested
        $presenceData = [];
        if ($includePresence && !empty($users)) {
            $presenceData = $this->getBulkUserPresence(array_column($users, 'id'));
        }

        foreach ($users as $user) {
            $enrichedUser = $this->enrichUserData($user, [
                'include_presence' => $includePresence,
                'include_photos' => $includePhotos,
                'presence_data' => $presenceData[$user['id']] ?? null
            ]);

            $processedUsers[] = $enrichedUser;
        }

        $processingTime = round((microtime(true) - $presenceStartTime) * 1000, 2);

        // Generate user insights and analytics
        $insights = $this->generateUserInsights($processedUsers, $options);

        $totalTime = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('User processing completed', [
            'processed_users' => count($processedUsers),
            'presence_processing_ms' => $processingTime,
            'total_time_ms' => $totalTime,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'users' => $processedUsers,
            'count' => count($processedUsers),
            'insights' => $insights,
            'performance' => [
                'fetch_time_ms' => $fetchTime,
                'processing_time_ms' => $processingTime,
                'total_time_ms' => $totalTime
            ],
            'pagination' => [
                'current_count' => count($processedUsers),
                'skip' => $skip,
                'top' => $top,
                'nextLink' => $response['@odata.nextLink'] ?? null
            ],
            'retrieved_at' => now()->toISOString()
        ];
    } catch (Exception $e) {
        Log::error('Failed to get all users', [
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
 * Get presence information for multiple users in bulk
 * Optimized batch processing for presence data
 *
 * @param array $userIds Array of user IDs
 * @param array $options Presence options
 * @return array Presence data keyed by user ID
 */
public function getBulkUserPresence(array $userIds, array $options = []): array
{
    try {
        if (empty($userIds)) {
            return [];
        }

        $batchSize = min($options['batch_size'] ?? 50, 50); // Microsoft Graph batch limit
        $includeDetails = $options['include_details'] ?? true;

        Log::info('Fetching bulk user presence', [
            'user_count' => count($userIds),
            'batch_size' => $batchSize,
            'include_details' => $includeDetails,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $allPresenceData = [];
        $batches = array_chunk($userIds, $batchSize);

        foreach ($batches as $batchIndex => $batch) {
            try {
                $batchPresence = $this->processBatchPresence($batch, $includeDetails, $batchIndex);
                $allPresenceData = array_merge($allPresenceData, $batchPresence);

                // Brief pause between batches to avoid rate limiting
                if (count($batches) > 1 && $batchIndex < count($batches) - 1) {
                    usleep(200000); // 0.2 second
                }
            } catch (Exception $e) {
                Log::warning('Batch presence request failed', [
                    'batch_index' => $batchIndex,
                    'batch_size' => count($batch),
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        Log::info('Bulk presence fetch completed', [
            'requested_users' => count($userIds),
            'presence_retrieved' => count($allPresenceData),
            'success_rate' => round((count($allPresenceData) / count($userIds)) * 100, 2) . '%',
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return $allPresenceData;
    } catch (Exception $e) {
        Log::error('Failed to get bulk user presence', [
            'user_count' => count($userIds),
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [];
    }
}

/**
 * Process a batch of presence requests
 * Helper method for bulk presence processing
 *
 * @param array $userIds Batch of user IDs
 * @param bool $includeDetails Include detailed presence info
 * @param int $batchIndex Batch number for logging
 * @return array Presence data for the batch
 */
private function processBatchPresence(array $userIds, bool $includeDetails, int $batchIndex): array
{
    $presenceData = [];

    try {
        // Build batch request
        $requests = [];
        foreach ($userIds as $index => $userId) {
            $requests[] = [
                'id' => $index,
                'method' => 'GET',
                'url' => "/users/{$userId}/presence"
            ];
        }

        $batchRequest = [
            'requests' => $requests
        ];

        $response = $this->makeRequest('POST', '/$batch', ['json' => $batchRequest]);

        if (isset($response['responses'])) {
            foreach ($response['responses'] as $batchResponse) {
                $requestIndex = $batchResponse['id'];
                $userId = $userIds[$requestIndex];

                if ($batchResponse['status'] === 200 && isset($batchResponse['body'])) {
                    $presenceInfo = $this->enrichPresenceData($batchResponse['body'], $includeDetails);
                    $presenceData[$userId] = $presenceInfo;
                } else {
                    // Log failed individual request but don't fail the whole batch
                    Log::debug('Individual presence request failed', [
                        'user_id' => $userId,
                        'status' => $batchResponse['status'],
                        'batch_index' => $batchIndex
                    ]);
                }
            }
        }
    } catch (Exception $e) {
        Log::warning('Batch presence processing failed', [
            'batch_index' => $batchIndex,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }

    return $presenceData;
}

/**
 * Get detailed presence information for a specific user
 * Enhanced version with presence history and activity details
 *
 * @param string $userId User ID (defaults to current user if 'me')
 * @param array $options Presence options
 * @return array|null Detailed presence information
 */
public function getUserPresence(string $userId = 'me', array $options = []): ?array
{
    try {
        $includeCalendar = $options['include_calendar'] ?? false;
        $includeActivity = $options['include_activity'] ?? true;
        $includeNotes = $options['include_notes'] ?? true;

        Log::info('Retrieving user presence', [
            'user_id' => $userId,
            'include_calendar' => $includeCalendar,
            'include_activity' => $includeActivity,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $startTime = microtime(true);

        // Get presence information
        $presenceResponse = $this->makeRequest('GET', "/users/{$userId}/presence");

        $retrievalTime = round((microtime(true) - $startTime) * 1000, 2);

        // Enrich presence data with additional context
        $enrichedPresence = $this->enrichPresenceData($presenceResponse, true);

        // Get calendar info if requested
        if ($includeCalendar) {
            $calendarInfo = $this->getUserCalendarBusyTime($userId);
            $enrichedPresence['calendar_info'] = $calendarInfo;
        }

        // Get user info for context
        $userInfo = $this->makeRequest('GET', "/users/{$userId}", [
            'query' => ['$select' => 'id,displayName,mail,jobTitle,department,officeLocation']
        ]);

        $enrichedPresence['user_info'] = [
            'id' => $userInfo['id'],
            'displayName' => $userInfo['displayName'],
            'email' => $userInfo['mail'] ?? $userInfo['userPrincipalName'],
            'jobTitle' => $userInfo['jobTitle'],
            'department' => $userInfo['department'],
            'officeLocation' => $userInfo['officeLocation']
        ];

        $enrichedPresence['retrieval_time_ms'] = $retrievalTime;
        $enrichedPresence['retrieved_at'] = now()->toISOString();

        Log::info('User presence retrieved successfully', [
            'user_id' => $userId,
            'availability' => $enrichedPresence['availability'],
            'activity' => $enrichedPresence['activity'],
            'retrieval_time_ms' => $retrievalTime,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return $enrichedPresence;
    } catch (Exception $e) {
        Log::error('Failed to get user presence', [
            'user_id' => $userId,
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return null;
    }
}

/**
 * Set current user's presence information
 * Enhanced version with validation and automatic scheduling
 *
 * @param string $availability Availability status
 * @param string|null $activity Activity status
 * @param array $options Additional presence options
 * @return array Set presence result
 */
public function setUserPresence(string $availability, string $activity = null, array $options = []): array
{
    try {
        // Validate availability and activity values
        $validation = $this->validatePresenceValues($availability, $activity);
        if (!$validation['is_valid']) {
            throw new Exception('Invalid presence values: ' . implode(', ', $validation['errors']));
        }

        $expirationDuration = $options['expiration_duration'] ?? null; // ISO 8601 duration
        $statusMessage = $options['status_message'] ?? null;
        $autoRevert = $options['auto_revert'] ?? false;

        Log::info('Setting user presence', [
            'availability' => $availability,
            'activity' => $activity,
            'expiration_duration' => $expirationDuration,
            'has_status_message' => !empty($statusMessage),
            'auto_revert' => $autoRevert,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        // Build presence data
        $presenceData = [
            'availability' => $availability
        ];

        if ($activity) {
            $presenceData['activity'] = $activity;
        }

        if ($expirationDuration) {
            $presenceData['expirationDuration'] = $expirationDuration;
        }

        // Set status message if provided
        if ($statusMessage) {
            $presenceData['statusMessage'] = [
                'message' => [
                    'content' => $statusMessage,
                    'contentType' => 'text'
                ]
            ];
        }

        $startTime = microtime(true);

        // Set presence via Microsoft Graph
        $this->makeRequest('PUT', '/me/presence/setPresence', ['json' => $presenceData]);

        $setTime = round((microtime(true) - $startTime) * 1000, 2);

        // Schedule auto-revert if requested
        if ($autoRevert && $expirationDuration) {
            $this->schedulePresenceRevert($availability, $expirationDuration);
        }

        // Get updated presence to confirm
        $updatedPresence = $this->getUserPresence('me');

        Log::info('User presence set successfully', [
            'availability' => $availability,
            'activity' => $activity,
            'set_time_ms' => $setTime,
            'confirmed_availability' => $updatedPresence['availability'] ?? 'unknown',
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'availability' => $availability,
            'activity' => $activity,
            'status_message' => $statusMessage,
            'expiration_duration' => $expirationDuration,
            'updated_presence' => $updatedPresence,
            'set_time_ms' => $setTime,
            'message' => 'Presence updated successfully'
        ];
    } catch (Exception $e) {
        Log::error('Failed to set user presence', [
            'availability' => $availability,
            'activity' => $activity,
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'availability' => null,
            'activity' => null
        ];
    }
}

/**
 * Clear current user's presence (reset to automatic)
 * NEW method for presence management
 *
 * @return bool Success status
 */
public function clearUserPresence(): bool
{
    try {
        Log::info('Clearing user presence', [
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $this->makeRequest('POST', '/me/presence/clearPresence');

        Log::info('User presence cleared successfully', [
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return true;
    } catch (Exception $e) {
        Log::error('Failed to clear user presence', [
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return false;
    }
}

/**
 * Get organization-wide presence statistics
 * NEW method for presence analytics
 *
 * @param array $options Analysis options
 * @return array Presence statistics and insights
 */
public function getOrganizationPresenceStats(array $options = []): array
{
    try {
        $includeDepartments = $options['include_departments'] ?? true;
        $includeLocations = $options['include_locations'] ?? true;
        $includeTimezone = $options['include_timezone'] ?? false;
        $userLimit = $options['user_limit'] ?? 500;

        Log::info('Generating organization presence statistics', [
            'include_departments' => $includeDepartments,
            'include_locations' => $includeLocations,
            'user_limit' => $userLimit,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $startTime = microtime(true);

        // Get all active users with presence
        $usersResult = $this->getAllUsers([
            'limit' => $userLimit,
            'include_presence' => true,
            'account_enabled' => true,
            'select' => 'id,displayName,mail,department,officeLocation,jobTitle,accountEnabled'
        ]);

        if (!$usersResult['success']) {
            throw new Exception('Failed to retrieve users for presence analysis');
        }

        $users = $usersResult['users'];

        // Generate comprehensive presence statistics
        $stats = [
            'overview' => $this->generatePresenceOverviewStats($users),
            'availability_distribution' => $this->generateAvailabilityDistribution($users),
            'activity_distribution' => $this->generateActivityDistribution($users)
        ];

        // Add department analysis if requested
        if ($includeDepartments) {
            $stats['department_analysis'] = $this->generateDepartmentPresenceAnalysis($users);
        }

        // Add location analysis if requested
        if ($includeLocations) {
            $stats['location_analysis'] = $this->generateLocationPresenceAnalysis($users);
        }

        // Add timezone analysis if requested
        if ($includeTimezone) {
            $stats['timezone_analysis'] = $this->generateTimezonePresenceAnalysis($users);
        }

        $processingTime = round((microtime(true) - $startTime) * 1000, 2);

        $stats['metadata'] = [
            'total_users_analyzed' => count($users),
            'processing_time_ms' => $processingTime,
            'generated_at' => now()->toISOString(),
            'timezone' => config('app.timezone'),
            'current_time' => now()->toTimeString()
        ];

        Log::info('Organization presence statistics generated', [
            'users_analyzed' => count($users),
            'processing_time_ms' => $processingTime,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'statistics' => $stats
        ];
    } catch (Exception $e) {
        Log::error('Failed to generate organization presence statistics', [
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'statistics' => []
        ];
    }
}

/**
 * Find available users for meeting or collaboration
 * NEW method for availability discovery
 *
 * @param array $options Search options for available users
 * @return array Available users with details
 */
public function findAvailableUsers(array $options = []): array
{
    try {
        $availabilityTypes = $options['availability_types'] ?? ['Available', 'AvailableIdle'];
        $department = $options['department'] ?? null;
        $location = $options['location'] ?? null;
        $jobTitleContains = $options['job_title_contains'] ?? null;
        $skillsRequired = $options['skills_required'] ?? [];
        $limit = $options['limit'] ?? 50;
        $excludeCurrentUser = $options['exclude_current_user'] ?? true;

        Log::info('Finding available users', [
            'availability_types' => $availabilityTypes,
            'department' => $department,
            'location' => $location,
            'limit' => $limit,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        $startTime = microtime(true);

        // Get users with presence information
        $getUsersOptions = [
            'limit' => $limit * 2, // Get more users to account for filtering
            'include_presence' => true,
            'account_enabled' => true
        ];

        // Add filters
        if ($department) {
            $getUsersOptions['department'] = $department;
        }
        if ($location) {
            $getUsersOptions['location'] = $location;
        }
        if ($jobTitleContains) {
            $getUsersOptions['job_title_contains'] = $jobTitleContains;
        }

        $usersResult = $this->getAllUsers($getUsersOptions);

        if (!$usersResult['success']) {
            throw new Exception('Failed to retrieve users for availability search');
        }

        $allUsers = $usersResult['users'];
        $currentUserEmail = $this->auth->email ?? auth()->user()->email ?? '';

        // Filter for available users
        $availableUsers = [];
        foreach ($allUsers as $user) {
            // Skip current user if requested
            if ($excludeCurrentUser && $user['email'] === $currentUserEmail) {
                continue;
            }

            // Check availability
            if (!isset($user['presence'])) {
                continue;
            }

            $availability = $user['presence']['availability'] ?? 'Unknown';
            if (!in_array($availability, $availabilityTypes)) {
                continue;
            }

            // Check skills if required
            if (!empty($skillsRequired)) {
                $userSkills = $user['skills'] ?? [];
                $hasRequiredSkills = !empty(array_intersect($skillsRequired, $userSkills));
                if (!$hasRequiredSkills) {
                    continue;
                }
            }

            // Add availability score
            $user['availability_score'] = $this->calculateAvailabilityScore($user);
            $availableUsers[] = $user;

            // Stop when we have enough users
            if (count($availableUsers) >= $limit) {
                break;
            }
        }

        // Sort by availability score (highest first)
        usort($availableUsers, function ($a, $b) {
            return $b['availability_score'] <=> $a['availability_score'];
        });

        $searchTime = round((microtime(true) - $startTime) * 1000, 2);

        // Generate availability insights
        $insights = $this->generateAvailabilityInsights($availableUsers, $allUsers);

        Log::info('Available users search completed', [
            'total_users_checked' => count($allUsers),
            'available_users_found' => count($availableUsers),
            'availability_rate' => count($allUsers) > 0 ? round((count($availableUsers) / count($allUsers)) * 100, 2) : 0,
            'search_time_ms' => $searchTime,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => true,
            'available_users' => $availableUsers,
            'count' => count($availableUsers),
            'insights' => $insights,
            'search_criteria' => [
                'availability_types' => $availabilityTypes,
                'department' => $department,
                'location' => $location,
                'skills_required' => $skillsRequired
            ],
            'search_time_ms' => $searchTime,
            'searched_at' => now()->toISOString()
        ];
    } catch (Exception $e) {
        Log::error('Failed to find available users', [
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'available_users' => [],
            'count' => 0
        ];
    }
}

/**
 * Get presence history for analytics
 * NEW method for presence tracking
 *
 * @param string $userId User ID
 * @param array $options History options
 * @return array Presence history data
 */
public function getUserPresenceHistory(string $userId, array $options = []): array
{
    try {
        $days = $options['days'] ?? 7;
        $includeWeekends = $options['include_weekends'] ?? false;
        $timezone = $options['timezone'] ?? config('app.timezone');

        Log::info('Retrieving user presence history', [
            'user_id' => $userId,
            'days' => $days,
            'include_weekends' => $includeWeekends,
            'user' => $this->auth->email ?? 'unknown'
        ]);

        // Note: Microsoft Graph doesn't provide historical presence data directly
        // This would typically require storing presence data over time in your application

        $history = [
            'user_id' => $userId,
            'period_days' => $days,
            'timezone' => $timezone,
            'data_points' => [],
            'summary' => [
                'most_common_availability' => null,
                'most_common_activity' => null,
                'average_available_hours' => 0,
                'presence_changes' => 0
            ],
            'note' => 'Presence history requires application-level tracking as Microsoft Graph does not store historical presence data'
        ];

        // In a real implementation, you would:
        // 1. Store presence updates in your database
        // 2. Query historical data from your storage
        // 3. Generate analytics from stored data

        return [
            'success' => true,
            'history' => $history,
            'retrieved_at' => now()->toISOString()
        ];
    } catch (Exception $e) {
        Log::error('Failed to get user presence history', [
            'user_id' => $userId,
            'error' => $e->getMessage(),
            'user' => $this->auth->email ?? 'unknown'
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage(),
            'history' => []
        ];
    }
}

// HELPER METHODS FOR USER DISCOVERY AND PRESENCE

/**
 * Get default user select fields
 * NEW helper method
 */
private function getDefaultUserSelectFields(): string
{
    return implode(',', [
        'id',
        'displayName',
        'mail',
        'userPrincipalName',
        'jobTitle',
        'department',
        'officeLocation',
        'businessPhones',
        'mobilePhone',
        'accountEnabled',
        'userType',
        'createdDateTime',
        'lastPasswordChangeDateTime',
        'companyName',
        'city',
        'country',
        'employeeId',
        'employeeType',
        'manager',
        'preferredLanguage',
        'usageLocation'
    ]);
}

/**
 * Enrich user data with additional information
 * NEW helper method
 */
private function enrichUserData(array $user, array $options = []): array
{
    $enriched = [
        'id' => $user['id'],
        'displayName' => $user['displayName'],
        'email' => $user['mail'] ?? $user['userPrincipalName'],
        'jobTitle' => $user['jobTitle'] ?? null,
        'department' => $user['department'] ?? null,
        'officeLocation' => $user['officeLocation'] ?? null,
        'accountEnabled' => $user['accountEnabled'] ?? null,
        'userType' => $user['userType'] ?? 'Member',
        'companyName' => $user['companyName'] ?? null,
        'city' => $user['city'] ?? null,
        'country' => $user['country'] ?? null,
        'businessPhones' => $user['businessPhones'] ?? [],
        'mobilePhone' => $user['mobilePhone'] ?? null,
        'preferredLanguage' => $user['preferredLanguage'] ?? null,
        'createdDateTime' => $user['createdDateTime'] ?? null,
        'is_guest' => ($user['userType'] ?? 'Member') === 'Guest',
        'has_phone' => !empty($user['businessPhones']) || !empty($user['mobilePhone']),
        'is_external' => $this->isExternalUser($user)
    ];

    // Add presence information if provided
    if ($options['include_presence'] && isset($options['presence_data'])) {
        $enriched['presence'] = $options['presence_data'];
        $enriched['is_available'] = $this->isUserAvailable($options['presence_data']);
    }

    // Add photo if requested
    if ($options['include_photos'] ?? false) {
        $enriched['photo'] = $this->getUserPhoto($user['id']);
    }

    return $enriched;
}

/**
 * Enrich presence data with additional context
 * NEW helper method
 */
private function enrichPresenceData(array $presenceData, bool $includeDetails = true): array
{
    $enriched = [
        'id' => $presenceData['id'] ?? null,
        'availability' => $presenceData['availability'] ?? 'Unknown',
        'activity' => $presenceData['activity'] ?? 'Unknown',
        'is_available' => $this->isPresenceAvailable($presenceData),
        'availability_priority' => $this->getAvailabilityPriority($presenceData['availability'] ?? 'Unknown'),
        'last_seen' => $presenceData['lastModifiedDateTime'] ?? null
    ];

    if ($includeDetails) {
        $enriched['status_message'] = $this->extractStatusMessage($presenceData);
        $enriched['availability_description'] = $this->getAvailabilityDescription($presenceData['availability'] ?? 'Unknown');
        $enriched['activity_description'] = $this->getActivityDescription($presenceData['activity'] ?? 'Unknown');
        $enriched['out_of_office'] = $presenceData['outOfOfficeSettings'] ?? null;
    }

    return $enriched;
}

/**
 * Generate comprehensive user insights
 * NEW helper method
 */
private function generateUserInsights(array $users, array $options): array
{
    $insights = [
        'overview' => [
            'total_users' => count($users),
            'active_users' => 0,
            'guest_users' => 0,
            'external_users' => 0,
            'users_with_presence' => 0,
            'available_users' => 0
        ],
        'departments' => [],
        'locations' => [],
        'user_types' => ['Member' => 0, 'Guest' => 0],
        'presence_summary' => [
            'Available' => 0,
            'Busy' => 0,
            'DoNotDisturb' => 0,
            'Away' => 0,
            'BeRightBack' => 0,
            'Offline' => 0,
            'Unknown' => 0
        ],
        'communication' => [
            'users_with_phone' => 0,
            'users_with_mobile' => 0,
            'phone_coverage' => 0
        ]
    ];

    foreach ($users as $user) {
        // Basic counts
        if ($user['accountEnabled'] ?? false) {
            $insights['overview']['active_users']++;
        }

        if ($user['is_guest'] ?? false) {
            $insights['overview']['guest_users']++;
        }

        if ($user['is_external'] ?? false) {
            $insights['overview']['external_users']++;
        }

        // User type distribution
        $userType = $user['userType'] ?? 'Member';
        if (isset($insights['user_types'][$userType])) {
            $insights['user_types'][$userType]++;
        }

        // Department distribution
        if (!empty($user['department'])) {
            $dept = $user['department'];
            $insights['departments'][$dept] = ($insights['departments'][$dept] ?? 0) + 1;
        }

        // Location distribution
        if (!empty($user['officeLocation'])) {
            $location = $user['officeLocation'];
            $insights['locations'][$location] = ($insights['locations'][$location] ?? 0) + 1;
        } elseif (!empty($user['city'])) {
            $location = $user['city'];
            $insights['locations'][$location] = ($insights['locations'][$location] ?? 0) + 1;
        }

        // Presence analysis
        if (isset($user['presence'])) {
            $insights['overview']['users_with_presence']++;

            $availability = $user['presence']['availability'] ?? 'Unknown';
            if (isset($insights['presence_summary'][$availability])) {
                $insights['presence_summary'][$availability]++;
            } else {
                $insights['presence_summary']['Unknown']++;
            }

            if ($user['is_available'] ?? false) {
                $insights['overview']['available_users']++;
            }
        }

        // Communication analysis
        if ($user['has_phone'] ?? false) {
            $insights['communication']['users_with_phone']++;
        }

        if (!empty($user['mobilePhone'])) {
            $insights['communication']['users_with_mobile']++;
        }
    }

    // Calculate rates and sort distributions
    $totalUsers = count($users);
    if ($totalUsers > 0) {
        $insights['communication']['phone_coverage'] = round(
            ($insights['communication']['users_with_phone'] / $totalUsers) * 100, 2
        );
    }

    // Sort departments and locations by count
    if (!empty($insights['departments'])) {
        arsort($insights['departments']);
        $insights['departments'] = array_slice($insights['departments'], 0, 10, true);
    }

    if (!empty($insights['locations'])) {
        arsort($insights['locations']);
        $insights['locations'] = array_slice($insights['locations'], 0, 10, true);
    }

    return $insights;
}

/**
 * Validate presence values
 * NEW helper method
 */
private function validatePresenceValues(string $availability, ?string $activity): array
{
    $validation = [
        'is_valid' => true,
        'errors' => []
    ];

    // Valid availability values
    $validAvailability = [
        'Available', 'AvailableIdle', 'Away', 'BeRightBack', 'Busy',
        'BusyIdle', 'DoNotDisturb', 'Offline', 'PresenceUnknown'
    ];

    if (!in_array($availability, $validAvailability)) {
        $validation['errors'][] = "Invalid availability: {$availability}. Valid values: " . implode(', ', $validAvailability);
    }

    // Valid activity values (if provided)
    if ($activity !== null) {
        $validActivity = [
            'Available', 'Away', 'BeRightBack', 'Busy', 'DoNotDisturb',
            'InACall', 'InAConferenceCall', 'Inactive', 'InAMeeting',
            'Offline', 'OffWork', 'OutOfOffice', 'PresenceUnknown',
            'Presenting', 'UrgentInterruptionsOnly'
        ];

        if (!in_array($activity, $validActivity)) {
            $validation['errors'][] = "Invalid activity: {$activity}. Valid values: " . implode(', ', $validActivity);
        }
    }

    $validation['is_valid'] = empty($validation['errors']);
    return $validation;
}

/**
 * Check if user is external to organization
 * NEW helper method
 */
private function isExternalUser(array $user): bool
{
    // Check user type
    if (($user['userType'] ?? 'Member') === 'Guest') {
        return true;
    }

    // Check email domain if we have organization domain info
    $userEmail = $user['mail'] ?? $user['userPrincipalName'] ?? '';
    $currentUserEmail = $this->auth->email ?? '';

    if (!empty($userEmail) && !empty($currentUserEmail)) {
        $userDomain = substr(strrchr($userEmail, "@"), 1);
        $orgDomain = substr(strrchr($currentUserEmail, "@"), 1);

        return strtolower($userDomain) !== strtolower($orgDomain);
    }

    return false;
}

/**
 * Check if user is available based on presence
 * NEW helper method
 */
private function isUserAvailable(?array $presenceData): bool
{
    if (!$presenceData) {
        return false;
    }

    $availability = $presenceData['availability'] ?? 'Unknown';
    $availableStates = ['Available', 'AvailableIdle'];

    return in_array($availability, $availableStates);
}

/**
 * Check if presence indicates availability
 * NEW helper method
 */
private function isPresenceAvailable(array $presenceData): bool
{
    $availability = $presenceData['availability'] ?? 'Unknown';
    $availableStates = ['Available', 'AvailableIdle'];

    return in_array($availability, $availableStates);
}

/**
 * Get availability priority for sorting
 * NEW helper method
 */
private function getAvailabilityPriority(string $availability): int
{
    $priorities = [
        'Available' => 10,
        'AvailableIdle' => 9,
        'BeRightBack' => 8,
        'Away' => 7,
        'Busy' => 6,
        'BusyIdle' => 5,
        'DoNotDisturb' => 4,
        'InAMeeting' => 3,
        'Offline' => 2,
        'PresenceUnknown' => 1,
        'Unknown' => 0
    ];

    return $priorities[$availability] ?? 0;
}

/**
 * Get human-readable availability description
 * NEW helper method
 */
private function getAvailabilityDescription(string $availability): string
{
    $descriptions = [
        'Available' => 'Available and ready to communicate',
        'AvailableIdle' => 'Available but inactive',
        'Away' => 'Away from computer',
        'BeRightBack' => 'Be right back',
        'Busy' => 'Busy and may not respond immediately',
        'BusyIdle' => 'Busy but inactive',
        'DoNotDisturb' => 'Do not disturb - urgent interruptions only',
        'Offline' => 'Offline and not available',
        'PresenceUnknown' => 'Presence status unknown',
        'Unknown' => 'Status not available'
    ];

    return $descriptions[$availability] ?? 'Status information not available';
}

/**
 * Get human-readable activity description
 * NEW helper method
 */
private function getActivityDescription(string $activity): string
{
    $descriptions = [
        'Available' => 'Available for communication',
        'Away' => 'Currently away',
        'BeRightBack' => 'Will be right back',
        'Busy' => 'Currently busy',
        'DoNotDisturb' => 'Do not disturb',
        'InACall' => 'Currently in a call',
        'InAConferenceCall' => 'In a conference call',
        'Inactive' => 'Currently inactive',
        'InAMeeting' => 'Currently in a meeting',
        'Offline' => 'Currently offline',
        'OffWork' => 'Currently off work',
        'OutOfOffice' => 'Currently out of office',
        'Presenting' => 'Currently presenting',
        'UrgentInterruptionsOnly' => 'Only urgent interruptions allowed',
        'Unknown' => 'Activity not available'
    ];

    return $descriptions[$activity] ?? 'Activity information not available';
}

/**
 * Extract status message from presence data
 * NEW helper method
 */
private function extractStatusMessage(array $presenceData): ?array
{
    $statusMessage = null;

    if (isset($presenceData['statusMessage'])) {
        $message = $presenceData['statusMessage'];
        $statusMessage = [
            'content' => $message['message']['content'] ?? null,
            'content_type' => $message['message']['contentType'] ?? 'text',
            'published_at' => $message['publishedDateTime'] ?? null,
            'expires_at' => $message['expiryDateTime'] ?? null
        ];
    }

    return $statusMessage;
}

/**
 * Calculate availability score for ranking
 * NEW helper method
 */
private function calculateAvailabilityScore(array $user): int
{
    $score = 0;

    // Base score from availability
    if (isset($user['presence'])) {
        $availability = $user['presence']['availability'] ?? 'Unknown';
        $score += $this->getAvailabilityPriority($availability) * 10;

        // Bonus for specific activities
        $activity = $user['presence']['activity'] ?? 'Unknown';
        if ($activity === 'Available') {
            $score += 20;
        } elseif (in_array($activity, ['Away', 'BeRightBack'])) {
            $score += 10;
        }
    }

    // Bonus for contact information
    if ($user['has_phone'] ?? false) {
        $score += 5;
    }

    // Bonus for internal users
    if (!($user['is_external'] ?? false)) {
        $score += 10;
    }

    // Bonus for active accounts
    if ($user['accountEnabled'] ?? false) {
        $score += 5;
    }

    return $score;
}

/**
 * Generate presence overview statistics
 * NEW helper method
 */
private function generatePresenceOverviewStats(array $users): array
{
    $stats = [
        'total_users' => count($users),
        'users_with_presence' => 0,
        'currently_available' => 0,
        'currently_busy' => 0,
        'currently_away' => 0,
        'currently_offline' => 0,
        'availability_rate' => 0,
        'response_rate' => 0
    ];

    $availableCount = 0;
    $responsiveStates = ['Available', 'AvailableIdle', 'Busy', 'BeRightBack'];

    foreach ($users as $user) {
        if (isset($user['presence'])) {
            $stats['users_with_presence']++;

            $availability = $user['presence']['availability'] ?? 'Unknown';

            switch ($availability) {
                case 'Available':
                case 'AvailableIdle':
                    $stats['currently_available']++;
                    $availableCount++;
                    break;
                case 'Busy':
                case 'BusyIdle':
                case 'DoNotDisturb':
                case 'InAMeeting':
                    $stats['currently_busy']++;
                    break;
                case 'Away':
                case 'BeRightBack':
                    $stats['currently_away']++;
                    break;
                case 'Offline':
                case 'PresenceUnknown':
                    $stats['currently_offline']++;
                    break;
            }

            // Count responsive users (likely to respond)
            if (in_array($availability, $responsiveStates)) {
                $stats['response_rate']++;
            }
        }
    }

    // Calculate rates
    if ($stats['users_with_presence'] > 0) {
        $stats['availability_rate'] = round(($availableCount / $stats['users_with_presence']) * 100, 2);
        $stats['response_rate'] = round(($stats['response_rate'] / $stats['users_with_presence']) * 100, 2);
    }

    return $stats;
}

/**
 * Generate availability distribution
 * NEW helper method
 */
private function generateAvailabilityDistribution(array $users): array
{
    $distribution = [
        'Available' => 0,
        'AvailableIdle' => 0,
        'Away' => 0,
        'BeRightBack' => 0,
        'Busy' => 0,
        'BusyIdle' => 0,
        'DoNotDisturb' => 0,
        'InAMeeting' => 0,
        'Offline' => 0,
        'PresenceUnknown' => 0,
        'NoPresenceData' => 0
    ];

    foreach ($users as $user) {
        if (isset($user['presence'])) {
            $availability = $user['presence']['availability'] ?? 'PresenceUnknown';
            if (isset($distribution[$availability])) {
                $distribution[$availability]++;
            } else {
                $distribution['PresenceUnknown']++;
            }
        } else {
            $distribution['NoPresenceData']++;
        }
    }

    // Convert to percentages
    $total = count($users);
    if ($total > 0) {
        foreach ($distribution as $status => $count) {
            $distribution[$status] = [
                'count' => $count,
                'percentage' => round(($count / $total) * 100, 2)
            ];
        }
    }

    return $distribution;
}

/**
 * Generate activity distribution
 * NEW helper method
 */
private function generateActivityDistribution(array $users): array
{
    $activities = [];

    foreach ($users as $user) {
        if (isset($user['presence']['activity'])) {
            $activity = $user['presence']['activity'];
            $activities[$activity] = ($activities[$activity] ?? 0) + 1;
        }
    }

    // Sort by frequency
    arsort($activities);

    // Convert to percentages
    $total = array_sum($activities);
    if ($total > 0) {
        foreach ($activities as $activity => $count) {
            $activities[$activity] = [
                'count' => $count,
                'percentage' => round(($count / $total) * 100, 2)
            ];
        }
    }

    return $activities;
}

/**
 * Generate department presence analysis
 * NEW helper method
 */
private function generateDepartmentPresenceAnalysis(array $users): array
{
    $departments = [];

    foreach ($users as $user) {
        $dept = $user['department'] ?? 'Unknown';

        if (!isset($departments[$dept])) {
            $departments[$dept] = [
                'total_users' => 0,
                'available_users' => 0,
                'busy_users' => 0,
                'away_users' => 0,
                'offline_users' => 0,
                'availability_rate' => 0
            ];
        }

        $departments[$dept]['total_users']++;

        if (isset($user['presence'])) {
            $availability = $user['presence']['availability'] ?? 'Unknown';

            switch ($availability) {
                case 'Available':
                case 'AvailableIdle':
                    $departments[$dept]['available_users']++;
                    break;
                case 'Busy':
                case 'BusyIdle':
                case 'DoNotDisturb':
                case 'InAMeeting':
                    $departments[$dept]['busy_users']++;
                    break;
                case 'Away':
                case 'BeRightBack':
                    $departments[$dept]['away_users']++;
                    break;
                default:
                    $departments[$dept]['offline_users']++;
            }
        }
    }

    // Calculate availability rates
    foreach ($departments as $dept => $stats) {
        if ($stats['total_users'] > 0) {
            $departments[$dept]['availability_rate'] = round(
                ($stats['available_users'] / $stats['total_users']) * 100, 2
            );
        }
    }

    // Sort by total users
    uasort($departments, function ($a, $b) {
        return $b['total_users'] <=> $a['total_users'];
    });

    return array_slice($departments, 0, 10, true);
}

/**
 * Generate location presence analysis
 * NEW helper method
 */
private function generateLocationPresenceAnalysis(array $users): array
{
    $locations = [];

    foreach ($users as $user) {
        $location = $user['officeLocation'] ?? $user['city'] ?? 'Unknown';

        if (!isset($locations[$location])) {
            $locations[$location] = [
                'total_users' => 0,
                'available_users' => 0,
                'busy_users' => 0,
                'away_users' => 0,
                'offline_users' => 0,
                'availability_rate' => 0
            ];
        }

        $locations[$location]['total_users']++;

        if (isset($user['presence'])) {
            $availability = $user['presence']['availability'] ?? 'Unknown';

            switch ($availability) {
                case 'Available':
                case 'AvailableIdle':
                    $locations[$location]['available_users']++;
                    break;
                case 'Busy':
                case 'BusyIdle':
                case 'DoNotDisturb':
                case 'InAMeeting':
                    $locations[$location]['busy_users']++;
                    break;
                case 'Away':
                case 'BeRightBack':
                    $locations[$location]['away_users']++;
                    break;
                default:
                    $locations[$location]['offline_users']++;
            }
        }
    }

    // Calculate availability rates
    foreach ($locations as $location => $stats) {
        if ($stats['total_users'] > 0) {
            $locations[$location]['availability_rate'] = round(
                ($stats['available_users'] / $stats['total_users']) * 100, 2
            );
        }
    }

    // Sort by total users
    uasort($locations, function ($a, $b) {
        return $b['total_users'] <=> $a['total_users'];
    });

    return array_slice($locations, 0, 10, true);
}

/**
 * Generate timezone presence analysis
 * NEW helper method
 */
private function generateTimezonePresenceAnalysis(array $users): array
{
    $timezones = [];
    $currentHour = now()->hour;

    foreach ($users as $user) {
        // This would require timezone information from user profiles
        // For now, we'll use a simplified approach based on country
        $country = $user['country'] ?? 'Unknown';
        $timezone = $this->getTimezoneByCountry($country);

        if (!isset($timezones[$timezone])) {
            $timezones[$timezone] = [
                'total_users' => 0,
                'available_users' => 0,
                'estimated_local_time' => $this->getEstimatedLocalTime($timezone),
                'business_hours' => $this->isBusinessHours($timezone),
                'availability_rate' => 0
            ];
        }

        $timezones[$timezone]['total_users']++;

        if (isset($user['presence']) && $user['is_available']) {
            $timezones[$timezone]['available_users']++;
        }
    }

    // Calculate availability rates
    foreach ($timezones as $tz => $stats) {
        if ($stats['total_users'] > 0) {
            $timezones[$tz]['availability_rate'] = round(
                ($stats['available_users'] / $stats['total_users']) * 100, 2
            );
        }
    }

    return $timezones;
}

/**
 * Generate availability insights
 * NEW helper method
 */
private function generateAvailabilityInsights(array $availableUsers, array $allUsers): array
{
    $insights = [
        'availability_summary' => [
            'total_users_checked' => count($allUsers),
            'available_users_found' => count($availableUsers),
            'availability_rate' => count($allUsers) > 0 ? round((count($availableUsers) / count($allUsers)) * 100, 2) : 0
        ],
        'department_availability' => [],
        'location_availability' => [],
        'recommendations' => []
    ];

    // Department breakdown
    $deptStats = [];
    foreach ($availableUsers as $user) {
        $dept = $user['department'] ?? 'Unknown';
        $deptStats[$dept] = ($deptStats[$dept] ?? 0) + 1;
    }
    arsort($deptStats);
    $insights['department_availability'] = array_slice($deptStats, 0, 5, true);

    // Location breakdown
    $locationStats = [];
    foreach ($availableUsers as $user) {
        $location = $user['officeLocation'] ?? $user['city'] ?? 'Unknown';
        $locationStats[$location] = ($locationStats[$location] ?? 0) + 1;
    }
    arsort($locationStats);
    $insights['location_availability'] = array_slice($locationStats, 0, 5, true);

    // Generate recommendations
    if ($insights['availability_summary']['availability_rate'] < 30) {
        $insights['recommendations'][] = 'Low availability detected. Consider scheduling meetings for later or checking with specific departments.';
    }

    if (count($availableUsers) < 5) {
        $insights['recommendations'][] = 'Few users available. Consider expanding search criteria or checking again later.';
    }

    $currentHour = now()->hour;
    if ($currentHour < 9 || $currentHour > 17) {
        $insights['recommendations'][] = 'Current time is outside typical business hours which may affect availability.';
    }

    return $insights;
}

/**
 * Get user's calendar busy time
 * NEW helper method
 */
private function getUserCalendarBusyTime(string $userId): array
{
    try {
        $startTime = now()->startOfDay()->toISOString();
        $endTime = now()->endOfDay()->toISOString();

        $calendarView = $this->makeRequest('GET', "/users/{$userId}/calendarView", [
            'query' => [
                'startDateTime' => $startTime,
                'endDateTime' => $endTime,
                '$select' => 'subject,start,end,showAs,isAllDay'
            ]
        ]);

        $events = $calendarView['value'] ?? [];

        return [
            'total_events_today' => count($events),
            'is_busy_now' => $this->isCurrentlyInMeeting($events),
            'next_free_time' => $this->getNextFreeTime($events),
            'busy_periods' => array_map(function($event) {
                return [
                    'subject' => $event['subject'],
                    'start' => $event['start']['dateTime'],
                    'end' => $event['end']['dateTime'],
                    'show_as' => $event['showAs']
                ];
            }, $events)
        ];
    } catch (Exception $e) {
        return [
            'error' => 'Calendar information not accessible',
            'total_events_today' => 0,
            'is_busy_now' => false
        ];
    }
}

/**
 * Check if user is currently in a meeting
 * NEW helper method
 */
private function isCurrentlyInMeeting(array $events): bool
{
    $now = now();

    foreach ($events as $event) {
        $start = Carbon::parse($event['start']['dateTime']);
        $end = Carbon::parse($event['end']['dateTime']);

        if ($now->between($start, $end)) {
            return true;
        }
    }

    return false;
}

/**
 * Get next free time for user
 * NEW helper method
 */
private function getNextFreeTime(array $events): ?string
{
    $now = now();
    $sortedEvents = collect($events)->sortBy(function($event) {
        return $event['start']['dateTime'];
    });

    foreach ($sortedEvents as $event) {
        $start = Carbon::parse($event['start']['dateTime']);
        if ($start->isAfter($now)) {
            return $start->toISOString();
        }
    }

    return null;
}

/**
 * Get timezone by country (simplified mapping)
 * NEW helper method
 */
private function getTimezoneByCountry(string $country): string
{
    $timezoneMap = [
        'United States' => 'America/New_York',
        'United Kingdom' => 'Europe/London',
        'Germany' => 'Europe/Berlin',
        'France' => 'Europe/Paris',
        'Japan' => 'Asia/Tokyo',
        'Australia' => 'Australia/Sydney',
        'Canada' => 'America/Toronto',
        'India' => 'Asia/Kolkata',
        'China' => 'Asia/Shanghai',
        'Brazil' => 'America/Sao_Paulo'
    ];

    return $timezoneMap[$country] ?? 'UTC';
}

/**
 * Get estimated local time for timezone
 * NEW helper method
 */
private function getEstimatedLocalTime(string $timezone): string
{
    try {
        return now($timezone)->format('H:i T');
    } catch (Exception $e) {
        return 'Unknown';
    }
}

/**
 * Check if current time is business hours for timezone
 * NEW helper method
 */
private function isBusinessHours(string $timezone): bool
{
    try {
        $localTime = now($timezone);
        $hour = $localTime->hour;
        $dayOfWeek = $localTime->dayOfWeek;

        // Monday = 1, Sunday = 0
        $isWeekday = $dayOfWeek >= 1 && $dayOfWeek <= 5;
        $isBusinessHour = $hour >= 9 && $hour <= 17;

        return $isWeekday && $isBusinessHour;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Schedule presence revert (placeholder for future implementation)
 * NEW helper method
 */
private function schedulePresenceRevert(string $originalAvailability, string $duration): void
{
    // This would typically involve:
    // 1. Parsing the ISO 8601 duration
    // 2. Scheduling a job/task to revert presence
    // 3. Storing the revert information

    Log::info('Presence revert scheduled', [
        'original_availability' => $originalAvailability,
        'duration' => $duration,
        'user' => $this->auth->email ?? 'unknown'
    ]);
}

/**
 * Get user photo (simplified implementation)
 * NEW helper method
 */
private function getUserPhoto(string $userId): ?array
{
    try {
        $response = $this->makeRequest('GET', "/users/{$userId}/photo/\$value");

        if (isset($response['content'])) {
            return [
                'content' => $response['content'],
                'content_type' => $response['content_type'] ?? 'image/jpeg',
                'size' => strlen($response['content'])
            ];
        }

        return null;
    } catch (Exception $e) {
        return null;
    }
}
}
