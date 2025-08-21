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
use InvalidArgumentException;

class OutlookService
{
    private string $graphEndpoint = 'https://graph.microsoft.com/v1.0';
    private string $authEndpoint = 'https://login.microsoftonline.com';
    private ?array $token = null;
    private array $config;
    private int $timeout;
    protected $auth = null;

    private array $requiredScopes = [
        // Core identity and profile
        'openid',
        'offline_access',
        'profile',
        'email',

        // User permissions
        'User.Read',
        'User.ReadWrite',
        'User.ReadBasic.All',
        'User.Read.All',
        'User.ReadWrite.All',
        'User.Invite.All',
        'User.Export.All',
        'User.ManageIdentities.All',

        // Mail permissions
        'Mail.Read',
        'Mail.ReadWrite',
        'Mail.Send',
        'Mail.Send.Shared',
        'Mail.ReadBasic',
        'Mail.ReadWrite.Shared',
        'MailboxSettings.Read',
        'MailboxSettings.ReadWrite',
        'IMAP.AccessAsUser.All',
        'POP.AccessAsUser.All',
        'SMTP.Send',

        // // Calendar permissions
        // // 'Calendars.Read',
        // // 'Calendars.ReadWrite',
        // // 'Calendars.Read.Shared',
        // // 'Calendars.ReadWrite.Shared',

        // // Contacts permissions
        // // 'Contacts.Read',
        // // 'Contacts.ReadWrite',
        // // 'Contacts.Read.Shared',
        // // 'Contacts.ReadWrite.Shared',

        // Files and OneDrive permissions
        'Files.Read',
        'Files.ReadWrite',
        'Files.Read.All',
        'Files.ReadWrite.All',
        'Files.Read.Selected',
        'Files.ReadWrite.Selected',
        'Sites.Read.All',
        'Sites.ReadWrite.All',
        'Sites.Manage.All',
        'Sites.FullControl.All',

        // // // Tasks and To-Do permissions
        // // 'Tasks.Read',
        // // 'Tasks.ReadWrite',
        // // 'Tasks.Read.Shared',
        // // 'Tasks.ReadWrite.Shared',

        // // // Notes and OneNote permissions
        // // 'Notes.Read',
        // // 'Notes.Create',
        // // 'Notes.ReadWrite',
        // // 'Notes.Read.All',
        // // 'Notes.ReadWrite.All',

        // People permissions
        'People.Read',
        'People.Read.All',

        // Directory and organization permissions
        'Directory.Read.All',
        'Directory.ReadWrite.All',
        'Directory.AccessAsUser.All',
        'Organization.Read.All',
        'Organization.ReadWrite.All',

        // Groups permissions
        'Group.Read.All',
        'Group.ReadWrite.All',
        'GroupMember.Read.All',
        'GroupMember.ReadWrite.All',

        // // // Teams and chat permissions
        // // 'Chat.Read',
        // // 'Chat.ReadWrite',
        // // 'ChatMessage.Read',
        // // 'ChatMessage.Send',
        // // 'ChatMember.Read',
        // // 'ChatMember.ReadWrite',
        // // 'TeamsTab.Read.All',
        // // 'TeamsTab.ReadWrite.All',
        // // 'TeamsTab.Create',
        // 'Channel.ReadBasic.All',
        // 'ChannelMessage.Read.All',
        // 'ChannelMessage.Send',
        // 'ChannelMessage.ReadWrite',
        // 'ChannelMember.Read.All',
        // 'ChannelMember.ReadWrite.All',
        // // 'Team.ReadBasic.All',
        // // 'TeamMember.Read.All',
        // // 'TeamSettings.Read.All',
        // // 'TeamSettings.ReadWrite.All',

        // // SharePoint permissions
        // 'AllSites.Read',
        // 'AllSites.Write',
        // 'AllSites.Manage',
        // 'AllSites.FullControl',

        // Application and device permissions
        'Application.Read.All',
        'Application.ReadWrite.All',
        'Device.Read',
        'Device.Command',
        // 'DeviceManagementConfiguration.Read.All',
        // 'DeviceManagementConfiguration.ReadWrite.All',
        // 'DeviceManagementApps.Read.All',
        // 'DeviceManagementApps.ReadWrite.All',
        // 'DeviceManagementManagedDevices.Read.All',
        // 'DeviceManagementManagedDevices.ReadWrite.All',
        // 'DeviceManagementServiceConfig.Read.All',
        // 'DeviceManagementServiceConfig.ReadWrite.All',

        // // Security and compliance permissions
        // 'SecurityEvents.Read.All',
        // 'SecurityEvents.ReadWrite.All',
        // 'ThreatIndicators.ReadWrite.OwnedBy',
        // 'IdentityRiskEvent.Read.All',
        // 'IdentityRiskyUser.Read.All',
        // 'IdentityRiskyUser.ReadWrite.All',
        // 'Policy.Read.All',
        // 'Policy.ReadWrite.TrustFramework',
        // 'Policy.ReadWrite.ConditionalAccess',
        // 'Policy.ReadWrite.ApplicationConfiguration',

        // // Reports and analytics permissions
        // 'Reports.Read.All',
        // 'AuditLog.Read.All',
        'Directory.Read.All',

        // // Bookings permissions
        // 'Bookings.Read.All',
        // 'Bookings.ReadWrite.All',
        // 'Bookings.Manage.All',
        // 'BookingsAppointment.ReadWrite.All',

        // // Education permissions
        // 'EduAdministration.Read',
        // 'EduAdministration.ReadWrite',
        // 'EduAssignments.ReadBasic',
        // 'EduAssignments.ReadWriteBasic',
        // 'EduAssignments.Read',
        // 'EduAssignments.ReadWrite',
        // 'EduRoster.ReadBasic',
        // 'EduRoster.Read',
        // 'EduRoster.ReadWrite',

        // // External connections permissions
        // 'ExternalConnection.Read.OwnedBy',
        // 'ExternalConnection.ReadWrite.OwnedBy',
        // 'ExternalItem.Read.All',
        // 'ExternalItem.ReadWrite.OwnedBy',

        // // Information protection permissions
        // 'InformationProtectionPolicy.Read',
        // 'InformationProtectionContent.Sign.OwnedBy',
        // 'InformationProtectionContent.Write.OwnedBy',

        // // Print permissions
        // 'Printer.Read.All',
        // 'Printer.ReadWrite.All',
        // 'PrintJob.Read',
        // 'PrintJob.Read.All',
        // 'PrintJob.ReadWrite',
        // 'PrintJob.ReadWrite.All',

        // // Search permissions
        // 'SearchConfiguration.Read.All',
        // 'SearchConfiguration.ReadWrite.All',

        // Workbooks and Excel permissions
        'Files.ReadWrite',

        // // Cloud communications permissions
        // 'CloudPC.Read.All',
        // 'CloudPC.ReadWrite.All',

        // Places permissions
        'Place.Read.All',

        // // Presence permissions
        // 'Presence.Read',
        // 'Presence.Read.All',
        // 'Presence.ReadWrite',

        // // Short notes permissions
        // 'ShortNotes.Read',
        // 'ShortNotes.ReadWrite',

        // // Synchronization permissions
        // 'Synchronization.Read.All',
        // 'Synchronization.ReadWrite.All',

        // // Terms of use permissions
        // 'Agreement.Read.All',
        // 'Agreement.ReadWrite.All',
        // 'AgreementAcceptance.Read',
        // 'AgreementAcceptance.Read.All',

        // // Virtual events permissions
        // 'VirtualEvent.Read',
        // 'OnlineMeetings.Read',
        // 'OnlineMeetings.ReadWrite',

        // // Windows updates permissions
        // 'WindowsUpdates.ReadWrite.All',

        // // Privileged access permissions
        // 'PrivilegedAccess.Read.AzureAD',
        // 'PrivilegedAccess.Read.AzureADGroup',
        // 'PrivilegedAccess.ReadWrite.AzureADGroup',
        // // 'PrivilegedAccess.Read.AzureResources',

        // // // Role management permissions
        // 'RoleManagement.Read.Directory',
        // 'RoleManagement.ReadWrite.Directory',
        // // 'RoleManagement.Read.CloudPC',
        // // 'RoleManagement.ReadWrite.CloudPC',
        // 'RoleManagementPolicy.Read.AzureADGroup',
        // 'RoleManagementPolicy.ReadWrite.AzureADGroup',

        // // Cross-tenant access permissions
        // 'CrossTenantInformation.ReadBasic.All',
        // 'Policy.Read.CrossTenantAccess',
        // 'Policy.ReadWrite.CrossTenantAccess',

        // // Business scenarios permissions
        // 'BusinessScenarioConfig.Read.OwnedBy',
        // 'BusinessScenarioConfig.ReadWrite.OwnedBy',
        // 'BusinessScenarioData.Read.OwnedBy',
        // 'BusinessScenarioData.ReadWrite.OwnedBy',

        // // Industry data permissions
        // 'IndustryData.ReadBasic.All',
        // 'IndustryData-InboundFlow.Read.All',
        // 'IndustryData-InboundFlow.ReadWrite.All',
        // 'IndustryData-Run.Read.All',
        // 'IndustryData-SourceSystem.Read.All',
        // 'IndustryData-SourceSystem.ReadWrite.All',
        // 'IndustryData-TimePeriod.Read.All',
        // 'IndustryData-TimePeriod.ReadWrite.All',

        // // Lifecycle workflows permissions
        // 'LifecycleWorkflows.Read.All',
        // 'LifecycleWorkflows.ReadWrite.All',

        // // Network access permissions
        // 'NetworkAccess.Read.All',
        // 'NetworkAccess.ReadWrite.All',
        // 'NetworkAccessPolicy.Read.All',
        // 'NetworkAccessPolicy.ReadWrite.All',

        // // Records management permissions
        // 'RecordsManagement.Read.All',
        // 'RecordsManagement.ReadWrite.All',

        // // Teams activity permissions
        // 'TeamsActivity.Read',
        // 'TeamsActivity.Send',

        // User authentication methods permissions
        'UserAuthenticationMethod.Read',
        'UserAuthenticationMethod.ReadWrite',
        'UserAuthenticationMethod.Read.All',
        'UserAuthenticationMethod.ReadWrite.All'
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

    /**
     * Validate configuration
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
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        $token = $this->getValidToken();
        return $token !== null;
    }

    /**
     * Check if token is valid for specific user
     */
    public function isTokenValid(string $userEmail): bool
    {
        try {
            if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            $tokenRecord = DB::table('oauth_tokens')
                ->where('provider', 'outlook')
                ->where('email', $userEmail)
                ->first();

            if (!$tokenRecord) {
                return false;
            }

            $expiresAt = Carbon::createFromTimestamp($tokenRecord->expires_at);
            $isValid = !$expiresAt->isPast();

            return $isValid;
        } catch (Exception $e) {
            logger()->error('Token validation failed: ' . json_encode([
                'email' => $userEmail,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT));

            return false;
        }
    }

    /**
     * Get valid token (auto-refresh if needed)
     */
    public function getValidToken(): ?array
    {
        if ($this->token === null) {
            $this->token = $this->loadToken();
        }

        if (!$this->token) {
            return null;
        }

        $expiresAt = isset($this->token['expires_at'])
            ? Carbon::parse($this->token['expires_at'])
            : Carbon::createFromTimestamp($this->token['expires_in']);

        if ($expiresAt->isPast()) {
            if (isset($this->token['refresh_token'])) {
                return $this->refreshToken() ? $this->token : null;
            }
            return null;
        }

        return $this->token;
    }

    /**
     * Load token for current authenticated user
     */
    private function loadToken(): ?array
    {
        $user = $this->auth;
        if (!$user) {
            return null;
        }

        $tokenRecord = DB::table('oauth_tokens')
            ->where('provider', 'outlook')
            ->where('email', $user->email)
            ->first();

        if (!$tokenRecord) {
            return null;
        }

        try {
            return [
                'access_token' => decrypt($tokenRecord->access_token),
                'refresh_token' => decrypt($tokenRecord->refresh_token),
                'expires_in' => $tokenRecord->expires_at,
                'expires_at' => Carbon::createFromTimestamp($tokenRecord->expires_at)->toISOString(),
                'scope' => $tokenRecord->scope,
                'user_email' => $tokenRecord->email,
                'tenant_id' => $this->config['tenant_id']
            ];
        } catch (Exception $e) {
            logger()->error('Failed to decrypt token', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Refresh access token
     */
    public function refreshToken(): bool
    {
        try {
            if (!$this->token || !isset($this->token['refresh_token'])) {
                throw new Exception('No refresh token available');
            }

            $tenantId = $this->token['tenant_id'] ?? $this->config['tenant_id'];
            $tokenUrl = "{$this->authEndpoint}/{$tenantId}/oauth2/v2.0/token";

            $response = Http::asForm()->timeout(30)->post($tokenUrl, [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'refresh_token' => $this->token['refresh_token'],
                'grant_type' => 'refresh_token'
            ]);

            if (!$response->successful()) {
                logger()->error('Token refresh failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }

            $tokenData = $response->json();

            $expiresAt = now()->addSeconds($tokenData['expires_in']);
            $this->token = array_merge($this->token, [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? $this->token['refresh_token'],
                'expires_in' => $expiresAt->timestamp,
                'expires_at' => $expiresAt->toISOString(),
                'scope' => $tokenData['scope'] ?? $this->token['scope'],
                'refreshed_at' => now()->toISOString()
            ]);

            $user = $this->auth;
            if ($user) {
                $this->saveToken($user, $this->token);
            }

            return true;
        } catch (Exception $e) {
            logger()->error('Token refresh error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save token to database
     */
    public function saveToken($user, array $tokenData): void
    {
        if (!$user) {
            throw new Exception('No authenticated user found');
        }

        try {
            $expiresAt = isset($tokenData['expires_at']) && is_string($tokenData['expires_at'])
                ? Carbon::parse($tokenData['expires_at'])->timestamp
                : $tokenData['expires_in'];

            DB::table('oauth_tokens')->updateOrInsert(
                [
                    'email' => $user->email,
                    'provider' => 'outlook'
                ],
                [
                    'access_token' => encrypt($tokenData['access_token']),
                    'refresh_token' => encrypt($tokenData['refresh_token']),
                    'expires_at' => $expiresAt,
                    'user_id' => $user->id,
                    'scope' => $tokenData['scope'],
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        } catch (Exception $e) {
            logger()->error('Failed to save OAuth token', [
                'error' => $e->getMessage(),
                'email' => auth()->user()->email ?? 'unknown'
            ]);
            throw $e;
        }
    }

    /**
     * Get access token from authorization code
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

            $response = Http::asForm()->timeout(30)->post($tokenUrl, [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'code' => $code,
                'redirect_uri' => $this->config['redirect_uri'],
                'grant_type' => 'authorization_code',
                'code_verifier' => $codeVerifier
            ]);

            if (!$response->successful()) {
                logger()->error('Token exchange failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Failed to exchange authorization code for access token');
            }

            $tokenData = $response->json();
            $user = auth()->user();

            if (!$user) {
                throw new Exception('No authenticated user found');
            }

            $expiresAt = now()->addSeconds($tokenData['expires_in']);

            return [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_in' => $expiresAt->timestamp,
                'expires_at' => $expiresAt->toISOString(),
                'scope' => $tokenData['scope'],
                'tenant_id' => $this->config['tenant_id'],
                'auth_type' => 'authorization_code',
                'created_at' => now()->toISOString(),
                'user_email' => $user->email
            ];
        } catch (Exception $e) {
            logger()->error('Access token exchange failed', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get authorization URL for OAuth flow
     */
    public function getAuthUrl(): array
    {
        try {

            $state = Str::random(32);
            $codeVerifier = PkceHelper::generateCodeVerifier();
            $codeChallenge = PkceHelper::generateCodeChallenge($codeVerifier);

            Redis::setex("azure_auth_state_{$state}", 600, json_encode([
                'code_verifier' => $codeVerifier,
                'created_at' => now()->toISOString()
            ]));

            $authParams = [
                'client_id' => $this->config['client_id'],
                'response_type' => 'code',
                'redirect_uri' => $this->config['redirect_uri'],
                'scope' => implode(' ', $this->requiredScopes),
                'state' => $state,
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
                'response_mode' => 'query',
                'access_type' => 'offline',
            ];

            $user = auth()->user();
            if ($user && !empty($user->email)) {
                $authParams['login_hint'] = $user->email;
            } else {
                $authParams['prompt'] = 'select_account';
            }

            $authUrl = "{$this->authEndpoint}/{$this->config['tenant_id']}/oauth2/v2.0/authorize?" . http_build_query($authParams);

            return [
                'state' => $state,
                'authUrl' => $authUrl,
                'expires_at' => now()->addMinutes(10)->toISOString(),
                'scopes_requested' => count($this->requiredScopes)
            ];
        } catch (Exception $e) {
            logger()->error('Failed to generate authorization URL', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new Exception('Failed to generate authorization URL: ' . $e->getMessage());
        }
    }

    /**
     * Make authenticated HTTP request to Microsoft Graph
     */
    private function makeRequest(string $method, string $endpoint, array $options = []): array
    {
        $startTime = microtime(true);
        $requestId = Str::uuid()->toString();

        try {
            $token = $this->getValidToken();
            if (!$token) {
                throw new Exception('No valid authentication token available. Please re-authenticate.');
            }

            $url = $this->buildRequestUrl($endpoint);

            $method = strtoupper($method);
            $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
            if (!in_array($method, $allowedMethods)) {
                throw new Exception("Unsupported HTTP method: {$method}");
            }

            // Prepare HTTP client with authentication and options
            $httpClient = $this->prepareHttpClient($token, $options, $requestId);

            // Execute HTTP request with method-specific handling
            $response = $this->executeHttpRequest($httpClient, $method, $url, $options);

            // Calculate response time
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            // Handle response and errors
            $result = $this->handleApiResponse($response, $method, $url, $responseTime, $requestId);

            return $result;
        } catch (Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            $this->logRequestError($method, $endpoint, $e, $responseTime, $requestId);
            throw $e;
        }
    }

    /**
     * Log request error
     */
    private function logRequestError(string $method, string $endpoint, Exception $exception, float $responseTime, string $requestId): void
    {
        logger()->error('API request failed', [
            'request_id' => $requestId,
            'method' => $method,
            'endpoint' => $endpoint,
            'error' => $exception->getMessage(),
            'response_time_ms' => $responseTime,
            'user' => $this->auth->email ?? 'unknown',
            'exception_class' => get_class($exception)
        ]);
    }

    /**
     * Build complete request URL from endpoint
     *
     * @param string $endpoint API endpoint
     * @return string Complete URL
     */
    private function buildRequestUrl(string $endpoint): string
    {
        if (str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://')) {
            return $endpoint;
        }

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
        $httpClient = Http::withToken($token['access_token'])
            ->timeout($this->timeout)
            ->connectTimeout(30)
            ->retry(3, 1000, function ($exception, $request) use ($requestId) {
                if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                    logger()->warning('HTTP connection failed, retrying', [
                        'request_id' => $requestId,
                        'error' => $exception->getMessage()
                    ]);
                    return true;
                }

                if (isset($exception->response) && $exception->response->status() >= 500) {
                    logger()->warning('Server error, retrying', [
                        'request_id' => $requestId,
                        'status' => $exception->response->status()
                    ]);
                    return true;
                }

                return false;
            });

        $httpClient = $httpClient->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'Laravel-OutlookService/2.0',
            'X-Request-ID' => $requestId,
            // 'Prefer' => 'return=representation', // Get full objects in responses
        ]);

        if (isset($options['headers'])) {
            $httpClient = $httpClient->withHeaders($options['headers']);
        }

        if (isset($options['multipart'])) {
            $httpClient = $httpClient->asMultipart();
        } elseif (isset($options['form'])) {
            $httpClient = $httpClient->asForm();
        }

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

        if ($response->successful()) {
            return $this->parseSuccessfulResponse($response, $method);
        }

        $this->handleSpecificErrorCodes($response, $method, $url, $responseTime, $requestId);

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
                logger()->warning('Unexpected HTTP status code', [
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

        logger()->error('Bad Request error', [
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

        logger()->error('Unauthorized error - token may be invalid', [
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

        logger()->error('Forbidden error - insufficient permissions', [
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
        logger()->warning('Resource not found', [
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

        logger()->warning('Conflict error', [
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

        logger()->warning('Rate limit exceeded', [
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
        logger()->error('Microsoft server error', [
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
     * Get user's profile photo
     */
    public function getUserPhoto($user, string $size = '96x96'): ?string
    {
        try {
            $this->auth = $user;
            $token = $this->getValidToken();

            if (!$token) {
                return null;
            }

            $url = "{$this->graphEndpoint}/me/photos/{$size}/\$value";
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token['access_token']
            ])->get($url);

            if ($response->successful()) {
                return base64_encode($response->body());
            }
        } catch (Exception $e) {
            logger()->info('Profile photo not available: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Get mail folders
     */
    public function getMailFolders(): array
    {
        $response = $this->makeRequest('GET', '/me/mailFolders');
        return $response['value'] ?? [];
    }

    /**
     * Get a specific message by ID
     */
    public function getMessage(string $messageId, array $options = []): array
    {
        $query = [];

        if (!empty($options['select'])) {
            $query['$select'] = $options['select'];
        }

        if (!empty($options['expand'])) {
            $query['$expand'] = $options['expand'];
        }

        return $this->makeRequest('GET', "/me/messages/{$messageId}", ['query' => $query]);
    }

    /**
     * Get message attachments
     */
    public function getMessageAttachments(string $messageId): array
    {
        $response = $this->makeRequest('GET', "/me/messages/{$messageId}/attachments");
        return $response['value'] ?? [];
    }

    /**
     * Download attachment content
     */
    public function downloadAttachment(string $messageId, string $attachmentId): ?array
    {
        try {
            $attachment = $this->makeRequest('GET', "/me/messages/{$messageId}/attachments/{$attachmentId}");

            if (isset($attachment['contentBytes'])) {
                return [
                    'name' => $attachment['name'],
                    'contentType' => $attachment['contentType'],
                    'size' => $attachment['size'],
                    'content' => base64_decode($attachment['contentBytes'])
                ];
            }
        } catch (Exception $e) {
            logger()->error('Failed to download attachment: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Move message to folder
     */
    public function moveMessage(string $messageId, string $destinationFolderId): bool
    {
        try {
            $this->makeRequest('POST', "/me/messages/{$messageId}/move", [
                'json' => ['destinationId' => $destinationFolderId]
            ]);
            return true;
        } catch (Exception $e) {
            logger()->error('Failed to move message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete message
     */
    public function deleteMessage(string $messageId): bool
    {
        try {
            $this->makeRequest('DELETE', "/me/messages/{$messageId}");
            return true;
        } catch (Exception $e) {
            logger()->error('Failed to delete message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create draft email
     */
    public function createDraft(array $emailData): ?array
    {
        try {
            $message = [
                'subject' => $emailData['subject'],
                'body' => [
                    'contentType' => $emailData['bodyType'] ?? 'HTML',
                    'content' => $emailData['body']
                ],
                'toRecipients' => $this->formatRecipients($emailData['to'])
            ];

            return $this->makeRequest('POST', '/me/messages', ['json' => $message]);
        } catch (Exception $e) {
            logger()->error('Failed to create draft: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Search messages
     */
    public function searchMessages(string $query, array $options = []): array
    {
        $top = min($options['limit'] ?? 25, 100);
        $skip = $options['skip'] ?? 0;
        $select = $options['select'] ?? 'id,subject,from,receivedDateTime,isRead,hasAttachments,bodyPreview';

        $queryParams = [
            '$search' => "\"$query\"",
            '$top' => $top,
            '$skip' => $skip,
            '$select' => $select,
            '$orderby' => 'receivedDateTime DESC'
        ];

        $response = $this->makeRequest('GET', '/me/messages', ['query' => $queryParams]);
        return $response['value'] ?? [];
    }

    /**
     * Get calendar events
     */
    public function getCalendarEvents(array $options = []): array
    {
        $top = min($options['limit'] ?? 25, 100);
        $skip = $options['skip'] ?? 0;
        $select = $options['select'] ?? 'id,subject,start,end,location,organizer,attendees';
        $filter = $options['filter'] ?? null;

        $query = [
            '$top' => $top,
            '$skip' => $skip,
            '$select' => $select,
            '$orderby' => 'start/dateTime'
        ];

        if ($filter) {
            $query['$filter'] = $filter;
        }

        $response = $this->makeRequest('GET', '/me/events', ['query' => $query]);
        return $response['value'] ?? [];
    }

    /**
     * Create calendar event
     */
    public function createCalendarEvent(array $eventData): ?array
    {
        try {
            $event = [
                'subject' => $eventData['subject'],
                'start' => [
                    'dateTime' => $eventData['start'],
                    'timeZone' => $eventData['timezone'] ?? 'UTC'
                ],
                'end' => [
                    'dateTime' => $eventData['end'],
                    'timeZone' => $eventData['timezone'] ?? 'UTC'
                ]
            ];

            if (!empty($eventData['body'])) {
                $event['body'] = [
                    'contentType' => 'HTML',
                    'content' => $eventData['body']
                ];
            }

            if (!empty($eventData['location'])) {
                $event['location'] = [
                    'displayName' => $eventData['location']
                ];
            }

            if (!empty($eventData['attendees'])) {
                $event['attendees'] = array_map(function ($email) {
                    return [
                        'emailAddress' => [
                            'address' => $email,
                            'name' => $email
                        ],
                        'type' => 'required'
                    ];
                }, (array)$eventData['attendees']);
            }

            return $this->makeRequest('POST', '/me/events', ['json' => $event]);
        } catch (Exception $e) {
            logger()->error('Failed to create calendar event: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get contacts
     */
    public function getContacts(array $options = []): array
    {
        $top = min($options['limit'] ?? 25, 100);
        $skip = $options['skip'] ?? 0;
        $select = $options['select'] ?? 'id,displayName,emailAddresses,mobilePhone,businessPhones';

        $query = [
            '$top' => $top,
            '$skip' => $skip,
            '$select' => $select,
            '$orderby' => 'displayName'
        ];

        $response = $this->makeRequest('GET', '/me/contacts', ['query' => $query]);
        return $response['value'] ?? [];
    }

    /**
     * Test API connectivity
     */
    public function testConnection(): array
    {
        try {
            $user = $this->getUser();
            return [
                'status' => 'success',
                'user' => [
                    'name' => $user['displayName'] ?? 'N/A',
                    'email' => $user['mail'] ?? $user['userPrincipalName'] ?? 'N/A',
                    'id' => $user['id'] ?? 'N/A'
                ],
                'token_info' => $this->getTokenInfo()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'token_info' => $this->getTokenInfo()
            ];
        }
    }

    /**
     * Get token information for debugging
     */
    public function getTokenInfo(): ?array
    {
        $token = $this->getValidToken();
        if (!$token) {
            return null;
        }

        $expiresAt = Carbon::parse($token['expires_at']);

        return [
            'auth_type' => $token['auth_type'] ?? 'unknown',
            'expires_at' => $token['expires_at'],
            'scope' => $token['scope'] ?? null,
            'has_refresh_token' => isset($token['refresh_token']),
            'tenant_id' => $token['tenant_id'] ?? null,
            'is_expired' => $expiresAt->isPast(),
            'expires_in_minutes' => $expiresAt->diffInMinutes(now(), false)
        ];
    }

    /**
     * Get usage statistics
     */
    public function getUsageStats(): array
    {
        try {
            $inboxMessages = $this->makeRequest('GET', '/me/mailFolders/inbox/messages', [
                'query' => ['$top' => 1, '$count' => true]
            ]);

            $unreadMessages = $this->makeRequest('GET', '/me/messages', [
                'query' => [
                    '$filter' => 'isRead eq false',
                    '$top' => 1,
                    '$count' => true
                ]
            ]);

            return [
                'inbox_count' => $inboxMessages['@odata.count'] ?? 0,
                'unread_count' => $unreadMessages['@odata.count'] ?? 0,
                'last_checked' => now()->toISOString()
            ];
        } catch (Exception $e) {
            logger()->error('Failed to get usage stats: ' . $e->getMessage());
            return [
                'inbox_count' => 0,
                'unread_count' => 0,
                'error' => $e->getMessage(),
                'last_checked' => now()->toISOString()
            ];
        }
    }

    /**
     * Revoke and clear authentication
     */
    public function revokeAuthentication(): bool
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return false;
            }

            DB::table('oauth_tokens')
                ->where('email', $user->email)
                ->where('provider', 'outlook')
                ->delete();

            $this->token = null;

            return true;
        } catch (Exception $e) {
            logger()->error('Failed to revoke authentication: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get messages from a folder
     */
    public function getMessages(array $options = []): array
    {
        $folder = $options['folder'] ?? 'inbox';
        $top = min($options['limit'] ?? 25, 100); // Limit to max 100
        $skip = $options['skip'] ?? 0;
        $select = $options['select'] ?? 'id,subject,from,receivedDateTime,isRead,hasAttachments,bodyPreview';
        $filter = $options['filter'] ?? null;
        $orderBy = $options['orderBy'] ?? 'receivedDateTime DESC';

        $query = [
            '$top' => $top,
            '$skip' => $skip,
            '$select' => $select,
            '$orderby' => $orderBy
        ];

        if ($filter) {
            $query['$filter'] = $filter;
        }

        $endpoint = $folder === 'inbox' ? '/me/messages' : "/me/mailFolders/{$folder}/messages";

        $response = $this->makeRequest('GET', $endpoint, ['query' => $query]);
        return $response['value'] ?? [];
    }

    /**
     * Fetch emails from a specific folder
     */
    public function fetchEmails($auth, array $options = []): array
    {
        $folder = $options['folder'] ?? 'inbox';
        $limit = min($options['limit'] ?? 50, 999);
        $this->auth = $auth;

        $token = $this->getValidToken();
        if (!$token) {
            throw new Exception('No valid authentication token available');
        }

        $cacheKey = "outlook_emails_{$auth->email}_{$folder}_{$limit}";
        $cached = Redis::get($cacheKey);
        if ($cached && !($options['force_refresh'] ?? false)) {
            return json_decode($cached, true);
        }

        $selectFields =  implode(',', $this->getDefaultSelectFields(true));

        $url = $this->buildOptimizedEmailsUrl($folder, $limit, $selectFields, $options);

        $response = Http::withToken($token['access_token'])
            ->timeout(30)
            ->get($url);

        if (!$response->successful()) {
            throw new Exception('Failed to fetch emails: HTTP ' . $response->status());
        }

        $data = $response->json();
        $rawEmails = $data['value'] ?? [];

        $processedEmails = $this->processEmailsBulk($rawEmails);
        $results = $this->saveEmailsToDatabase($processedEmails, $folder) ?? [];

        // Cache for 2 minutes
        Redis::setex($cacheKey, 120, json_encode($results));

        return $results;
    }

    /**
     * Optimized bulk email processing
     */
    private function processEmailsBulk(array $rawEmails): array
    {
        if (empty($rawEmails)) {
            return [];
        }

        $emails = [];

        $emails = array_fill(0, count($rawEmails), null);

        $chunks = array_chunk($rawEmails, 50);
        $index = 0;

        foreach ($chunks as $chunk) {
            foreach ($chunk as $rawEmail) {
                $emails[$index] = $this->processEmails($rawEmail);
                $index++;
            }

            if (count($chunks) > 1) {
                usleep(1000);
            }
        }

        return array_filter($emails);
    }


    /**
     * Fast email processing with minimal allocations
     */
    private function processEmails(array $rawEmail): array
    {
        $from = $rawEmail['from']['emailAddress'] ?? null;

        return [
            'id' => $rawEmail['id'],
            'subject' => $rawEmail['subject'] ?? '[No Subject]',
            'from' => $from['address'] ?? null,
            'from_name' => $from['name'] ?? null,
            'to' => $this->extractRecipientsfast($rawEmail['toRecipients'] ?? []),
            'cc' => $this->extractRecipients($rawEmail['ccRecipients'] ?? []),
            'date_received' => $rawEmail['receivedDateTime'] ?? null,
            'date_sent' => $rawEmail['sentDateTime'] ?? null,
            'body_preview' => $rawEmail['bodyPreview'] ?? '',
            'body_content' => $this->extractBodyContent($rawEmail['body'] ?? null),
            'importance' => $rawEmail['importance'] ?? 'normal',
            'is_read' => $rawEmail['isRead'] ?? false,
            'has_attachments' => $rawEmail['hasAttachments'] ?? false,
            'message_id' => $rawEmail['internetMessageId'] ?? null,
            'conversation_id' => $rawEmail['conversationId'] ?? null,
        ];
    }

    /**
     * Fast recipient extraction
     */
    private function extractRecipientsfast(array $recipients): array
    {
        if (empty($recipients)) {
            return [];
        }

        return array_map(fn($r) => [
            'email' => $r['emailAddress']['address'] ?? null,
            'name' => $r['emailAddress']['name'] ?? null
        ], $recipients);
    }

    /**
     * Build optimized URL for single folder request
     */
    private function buildOptimizedEmailsUrl(string $folder, int $limit, string $select, array $options): string
    {
        $folderPath = match ($folder) {
            'inbox' => 'mailFolders/inbox',
            'sent' => 'mailFolders/sentitems',
            'drafts' => 'mailFolders/drafts',
            'deleted' => 'mailFolders/deleteditems',
            default => "mailFolders/{$folder}"
        };

        $params = [
            '$top' => $limit,
            '$select' => $select,
            '$orderby' => 'receivedDateTime desc',
        ];

        if ($since = $options['since'] ?? null) {
            $params['$filter'] = "receivedDateTime ge " . Carbon::parse($since)->toISOString();
        } else {
            $params['$filter'] = "receivedDateTime ge " . now()->subDays(30)->toISOString();
        }

        return $this->graphEndpoint . "/me/{$folderPath}/messages?" . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Save emails to database
     */
    private function saveEmailsToDatabase(array $emails, string $folder): array
    {
        if (empty($emails)) {
            return [];
        }

        $userEmail = auth()->user()->email;

        return DB::transaction(function () use ($emails, $folder, $userEmail) {
            $messageIds = collect($emails)->pluck('message_id')->filter()->toArray();
            $existing = DB::table('fetched_emails')
                ->where('user_email', $userEmail)
                ->where('folder', $folder)
                ->whereIn('message_id', $messageIds)
                ->pluck('message_id')
                ->toArray();

            $toUpdate = [];
            $toInsert = [];

            foreach ($emails as $email) {
                if (empty($email['message_id'])) continue;

                $emailData = [
                    'uid' => $email['id'],
                    'subject' => $email['subject'],
                    'from_email' => $email['from'],
                    'from_name' => $email['from_name'],
                    'to_recipients' => json_encode($email['to']),
                    'cc_recipients' => json_encode($email['cc']),
                    'date_received' => $email['date_received']
                        ? Carbon::parse($email['date_received'])->setTimezone(config('app.timezone'))->toDateTimeString()
                        : null,
                    'date_sent' => $email['date_sent']
                        ? Carbon::parse($email['date_sent'])->setTimezone(config('app.timezone'))->toDateTimeString()
                        : null,
                    'body_preview' => $email['body_preview'],
                    'body_text' => strip_tags($email['body_content'] ?? ''),
                    'body_html' => $email['body_content'],
                    'importance' => $email['importance'],
                    'is_read' => $email['is_read'],
                    'has_attachments' => $email['has_attachments'],
                    'conversation_id' => $email['conversation_id'],
                    'folder' => $folder,
                    'updated_at' => now(),
                ];


                if (in_array($email['message_id'], $existing)) {
                    $toUpdate[] = array_merge($emailData, ['message_id' => $email['message_id']]);
                } else {
                    $toInsert[] = array_merge($emailData, [
                        'message_id' => $email['message_id'],
                        'user_email' => $userEmail,
                        'created_at' => now()
                    ]);
                }
            }

            if (!empty($toInsert)) {
                foreach (array_chunk($toInsert, 100) as $chunk) {
                    DB::table('fetched_emails')->insert($chunk);
                }
            }

            if (!empty($toUpdate)) {
                foreach ($toUpdate as $update) {
                    DB::table('fetched_emails')
                        ->where('message_id', $update['message_id'])
                        ->where('user_email', $userEmail)
                        ->update(Arr::except($update, ['message_id']));
                }
            }

            DB::table('fetched_emails')
                ->where('user_email', $userEmail)
                ->where('folder', $folder)
                ->whereNotIn('message_id', $messageIds)
                ->delete();

            return DB::table('fetched_emails')
                ->where('user_email', $userEmail)
                ->where('folder', $folder)
                ->orderBy('date_received', 'desc')
                ->limit(100)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get attachments metadata for a specific email
     */
    // private function getEmailAttachments(string $messageId): array
    // {
    //     $url = $this->GRAPH_API_BASE . "/me/messages/{$messageId}/attachments";

    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer ' . $this->accessToken,
    //         'Content-Type' => 'application/json',
    //     ])->timeout(30)->get($url);

    //     if (!$response->successful()) {
    //         throw new \Exception('Failed to fetch attachments: HTTP ' . $response->status());
    //     }

    //     $data = $response->json();
    //     $attachments = [];

    //     foreach ($data['value'] ?? [] as $attachment) {
    //         $attachments[] = [
    //             'id' => $attachment['id'],
    //             'name' => $attachment['name'] ?? 'unknown',
    //             'content_type' => $attachment['contentType'] ?? 'application/octet-stream',
    //             'size' => $attachment['size'] ?? 0,
    //             'is_inline' => $attachment['isInline'] ?? false,
    //             'last_modified' => $attachment['lastModifiedDateTime'] ?? null,
    //             'content_id' => $attachment['contentId'] ?? null,
    //             'content_location' => $attachment['contentLocation'] ?? null,
    //             'downloaded' => false,
    //             'file_path' => null,
    //             'download_error' => null,
    //         ];
    //     }

    //     return $attachments;
    // }

    /**
     * Save attachments to database
     */
    private function saveAttachmentsToDatabase(string $messageId, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            try {
                $userEmail = auth()->user()->email;

                DB::table('email_attachments')->updateOrInsert(
                    [
                        'message_id' => $messageId,
                        'attachment_id' => $attachment['id'],
                        'user_email' => $userEmail
                    ],
                    [
                        'name' => $attachment['name'],
                        'content_type' => $attachment['content_type'],
                        'size' => $attachment['size'],
                        'is_inline' => $attachment['is_inline'],
                        'last_modified' => $attachment['last_modified'],
                        'content_id' => $attachment['content_id'],
                        'content_location' => $attachment['content_location'],
                        'downloaded' => $attachment['downloaded'],
                        'file_path' => $attachment['file_path'],
                        'download_error' => $attachment['download_error'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            } catch (\Exception $e) {
                logger()->info("Failed to save attachment {$attachment['name']}: " . $e->getMessage());
            }
        }
    }

    /**
     * Save profile picture to database
     */
    // private function saveProfilePictureToDatabase(string $messageId, string $senderEmail, array $profilePicture): void
    // {
    //     try {
    //         $userEmail = auth()->user()->email;

    //         DB::table('email_profile_pictures')->updateOrInsert(
    //             [
    //                 'sender_email' => $senderEmail,
    //                 'user_email' => $userEmail
    //             ],
    //             [
    //                 'width' => $profilePicture['width'],
    //                 'height' => $profilePicture['height'],
    //                 'size' => $profilePicture['size'],
    //                 'content_type' => $profilePicture['content_type'],
    //                 'downloaded' => $profilePicture['downloaded'],
    //                 'file_path' => $profilePicture['file_path'],
    //                 'error' => $profilePicture['error'],
    //                 'last_fetched' => now(),
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ]
    //         );

    //         if ($messageId) {
    //             DB::table('email_profile_picture_links')->updateOrInsert(
    //                 [
    //                     'message_id' => $messageId,
    //                     'sender_email' => $senderEmail
    //                 ],
    //                 [
    //                     'created_at' => now(),
    //                     'updated_at' => now()
    //                 ]
    //             );
    //         }
    //     } catch (\Exception $e) {
    //         logger()->info("Failed to save profile picture for {$senderEmail}: " . $e->getMessage());
    //     }
    // }


    /**
     * Build the Graph API URL for fetching emails
     */
    private function buildEmailsUrl(string $folder, int $limit, ?string $since, ?string $select, ?string $filter, string $orderBy): string
    {
        $folderPath = $folder === 'inbox' ? 'mailFolders/inbox' : "mailFolders/{$folder}";
        $url = $this->graphEndpoint . "/me/{$folderPath}/messages";

        $params = [
            '$top' => $limit,
            '$orderby' => $orderBy
        ];

        // Add select fields
        if ($select) {
            $params['$select'] = $select;
        } else {
            $params['$select'] = implode(',', $this->getDefaultSelectFields());
        }

        // Add date filter
        $filters = [];
        if ($since) {
            $sinceDate = Carbon::parse($since)->toISOString();
            $filters[] = "receivedDateTime ge {$sinceDate}";
        }

        // Add custom filter
        if ($filter) {
            $filters[] = $filter;
        }

        if (!empty($filters)) {
            $params['$filter'] = implode(' and ', $filters);
        }

        return $url . '?' . http_build_query($params);
    }


    /**
     * Get default fields to select from Graph API
     */
    private function getDefaultSelectFields(bool $fetchBody = false): array
    {
        $fields = [
            'id',
            'subject',
            'from',
            'toRecipients',
            'ccRecipients',
            'receivedDateTime',
            'sentDateTime',
            'importance',
            'isRead',
            'hasAttachments',
            'internetMessageId',
            'conversationId',
            'attachments',
        ];

        if ($fetchBody) {
            $fields[] = 'body';
            $fields[] = 'bodyPreview';
        }

        return $fields;
    }

    /**
     * Extract email address from Graph API email object
     */
    private function extractEmailAddress(?array $emailObject): ?string
    {
        return $emailObject['emailAddress']['address'] ?? null;
    }

    /**
     * Extract display name from Graph API email object
     */
    private function extractDisplayName(?array $emailObject): ?string
    {
        return $emailObject['emailAddress']['name'] ?? null;
    }

    /**
     * Extract recipients from Graph API recipients array
     */
    private function extractRecipients(array $recipients): array
    {
        return array_map(function ($recipient) {
            return [
                'email' => $recipient['emailAddress']['address'] ?? null,
                'name' => $recipient['emailAddress']['name'] ?? null
            ];
        }, $recipients);
    }

    /**
     * Extract body content
     */
    private function extractBodyContent(?array $body): ?string
    {
        if (!$body || !isset($body['content'])) {
            return null;
        }

        return $body['content'];
    }

    /**
     * Generate unique filename to avoid conflicts
     */
    private function generateUniqueFilename(string $originalName, string $messageId): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $timestamp = time();
        $messagePrefix = substr(md5($messageId), 0, 8);

        return Str::slug($basename) . "_{$messagePrefix}_{$timestamp}" . ($extension ? ".{$extension}" : '');
    }

    /**
     * Generate unique filename for profile picture
     */
    private function generateProfilePictureFilename(string $userEmail, string $size): string
    {
        $emailHash = md5($userEmail);
        $timestamp = time();

        return "profile_{$emailHash}_{$size}_{$timestamp}.jpg";
    }

    /**
     * Format bytes to human readable format
     */
    public function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

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

            // Validate required fields
            $this->validateEmailData($emailData);

            // Sanitize and prepare email data
            $emailData = $this->sanitizeEmailData($emailData);

            return $this->sendEmailWithMessageId($auth, $emailData);
        } catch (Exception $e) {
            logger()->error('Failed to send email via Outlook API', [
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
     * Validate email data structure and required fields
     */
    private function validateEmailData(array $emailData): void
    {
        if (empty($emailData['to'])) {
            throw new Exception('At least one recipient (to) is required');
        }

        if (empty($emailData['subject'])) {
            throw new Exception('Email subject is required');
        }

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

    /**
     * Send email and get message ID by creating draft first
     */
    public function sendEmailWithMessageId($auth, array $emailData): array
    {
        try {
            $this->auth = $auth;

            $draftResponse = $this->createDraftMessage($emailData);
            if (!$draftResponse['success']) {
                return $draftResponse;
            }

            $messageId = $draftResponse['message_id'];
            $sendResponse = $this->sendDraftMessage($messageId);

            if ($sendResponse['success']) {
                $sentMessage = $this->getMessageDetails($messageId);
                return [
                    'success' => true,
                    'message_id' => $messageId,
                    'conversation_id' => $sentMessage['conversationId'] ?? null,
                    'internet_message_id' => $sentMessage['internetMessageId'] ?? null,
                    'sent_at' => $sentMessage['sentDateTime'] ?? now()->toISOString(),
                    'message' => 'Email sent successfully'
                ];
            }

            return $sendResponse;
        } catch (Exception $e) {
            logger()->error('Failed to send email with message ID', [
                'error' => $e->getMessage(),
                'subject' => $emailData['subject'] ?? 'N/A'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message_id' => null
            ];
        }
    }

    /**
     * Create a draft message
     */
    public function createDraftMessage(array $emailData): array
    {
        try {
            $message = [
                'subject' => $emailData['subject'],
                'body' => [
                    'contentType' => $emailData['bodyType'] ?? 'HTML',
                    'content' => $emailData['body']
                ],
                'toRecipients' => $this->formatRecipients($emailData['to'])
            ];

            if (!empty($emailData['cc'])) {
                $message['ccRecipients'] = $this->formatRecipients($emailData['cc']);
            }

            if (!empty($emailData['bcc'])) {
                $message['bccRecipients'] = $this->formatRecipients($emailData['bcc']);
            }

            if (!empty($emailData['attachments'])) {
                $message['attachments'] = $this->formatAttachments($emailData['attachments']);
            }

            if (!empty($emailData['priority'])) {
                $message['importance'] = $this->mapPriorityToImportance($emailData['priority']);
            }

            // Add custom headers for tracking
            if (!empty($emailData['customHeaders'])) {
                $message['internetMessageHeaders'] = $emailData['customHeaders'];
            }

            $response = $this->makeRequest('POST', '/me/messages', ['json' => $message]);

            return [
                'success' => true,
                'message_id' => $response['id'],
                'conversation_id' => $response['conversationId'] ?? null
            ];
        } catch (Exception $e) {
            logger()->error('Failed to create draft message', [
                'error' => $e->getMessage(),
                'subject' => $emailData['subject'] ?? 'N/A'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message_id' => null
            ];
        }
    }

    /**
     * Send a draft message
     */
    public function sendDraftMessage(string $messageId): array
    {
        try {
            $this->makeRequest('POST', "/me/messages/{$messageId}/send");

            return [
                'success' => true,
                'message' => 'Draft message sent successfully'
            ];
        } catch (Exception $e) {
            logger()->error('Failed to send draft message: ' . json_encode([
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT));
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
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

            $query = [];

            if (!empty($options['select'])) {
                $query['$select'] = $options['select'];
            } else {
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
            logger()->error(json_encode([
                'message_id' => $messageId,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ], JSON_PRETTY_PRINT));
            return [];
        }
    }

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
        // try {
        //     $this->auth = $auth;
        //     $originalMessage = $this->getMessageDetails($originalMessageId);

        //     if (empty($originalMessage)) {
        //         throw new Exception('Original message not found');
        //     }

        //     // Validate reply permissions
        //     $canReply = $this->canReplyToMessage($originalMessageId);
        //     if (!$canReply['can_reply']) {
        //         throw new Exception($canReply['reason']);
        //     }

        //     // Build reply message
        //     $replyMessage = $this->buildReplyMessage($replyData, $originalMessage, 'reply');

        //     logger()->info('Sending reply', [
        //         'original_message_id' => $originalMessageId,
        //         'conversation_id' => $originalMessage['conversationId'],
        //         'original_subject' => $originalMessage['subject'],
        //         'user' => $auth->email
        //     ]);

        //     $startTime = microtime(true);

        //     // Send reply using Microsoft Graph
        //     $this->makeRequest('POST', "/me/messages/{$originalMessageId}/reply", [
        //         'json' => $replyMessage
        //     ]);

        //     $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        //     // Get conversation details for response
        //     $conversationMessages = $this->getConversationMessages($originalMessage['conversationId']);

        //     logger()->info('Reply sent successfully', [
        //         'original_message_id' => $originalMessageId,
        //         'conversation_id' => $originalMessage['conversationId'],
        //         'response_time_ms' => $responseTime,
        //         'user' => $auth->email
        //     ]);

        //     return [
        //         'success' => true,
        //         'original_message_id' => $originalMessageId,
        //         'conversation_id' => $originalMessage['conversationId'],
        //         'replied_to' => [
        //             'sender' => $originalMessage['from']['emailAddress']['address'] ?? 'Unknown'
        //         ],
        //         'conversation_message_count' => $conversationMessages['count'] ?? 0,
        //         'response_time_ms' => $responseTime,
        //         'message' => 'Reply sent successfully'
        //     ];
        // } catch (Exception $e) {
        //     logger()->error('Failed to send reply', [
        //         'original_message_id' => $originalMessageId,
        //         'error' => $e->getMessage(),
        //         'user' => $auth->email ?? 'unknown'
        //     ]);

        //     return [
        //         'success' => false,
        //         'error' => $e->getMessage(),
        //         'original_message_id' => $originalMessageId,
        //         'error_code' => $this->getErrorCode($e)
        //     ];
        // }
        return [];
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

            $canReply = $this->canReplyToMessage($originalMessageId);
            if (!$canReply['can_reply']) {
                throw new Exception($canReply['reason']);
            }

            $recipientPreview = $this->getReplyRecipientsPreview($originalMessageId, 'replyAll');

            $replyMessage = $this->buildReplyMessage($replyData, $originalMessage, 'replyAll');

            $startTime = microtime(true);

            $this->makeRequest('POST', "/me/messages/{$originalMessageId}/replyAll", [
                'json' => $replyMessage
            ]);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

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
                'response_time_ms' => $responseTime,
                'message' => 'Reply all sent successfully'
            ];
        } catch (Exception $e) {
            logger()->error('Failed to send reply all', [
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
     * Send a reply (regular or reply all) based on the type parameter
     */
    public function sendEmailReply($auth, string $originalMessageId, array $replyData, string $type = 'reply'): array
    {
        if ($type === 'replyAll') {
            return $this->sendReplyAll($auth, $originalMessageId, $replyData);
        }

        return $this->sendReply($auth, $originalMessageId, $replyData);
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

            $folderChecks = $this->checkMessageFolder($message);
            if (!$folderChecks['can_reply']) {
                return $folderChecks;
            }

            $senderChecks = $this->checkMessageSender($message);
            if (!$senderChecks['can_reply']) {
                return $senderChecks;
            }

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
            logger()->error('Failed to check reply permissions', [
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
            logger()->warning('Failed to parse message date for age check', [
                'received_date' => $receivedDateTime,
                'error' => $e->getMessage()
            ]);
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
     * Build reply message structure
     * New helper method for consistent reply building
     */
    private function buildReplyMessage(array $replyData, array $originalMessage, string $replyType): array
    {
        $this->validateInputs($replyData, $originalMessage, $replyType);

        $message = $this->buildBaseMessage($replyData);
        $this->addOptionalComponents($message, $replyData);

        return $message;
    }

    /**
     * Build the base message structure with comment object
     */
    private function buildBaseMessage(array $replyData): array
    {
        return [
            'comment' => $replyData['body'] ?? ''
        ];
    }

    /**
     * Add optional components to the message
     */
    private function addOptionalComponents(array &$message, array $replyData): void
    {
        if (!empty($replyData['attachments'])) {
            $message['attachments'] = $this->formatAttachments($replyData['attachments']);
        }

        // if (!empty($replyData['customHeaders'])) {
        //     $message['internetMessageHeaders'] = $this->formatCustomHeaders($replyData['customHeaders']);
        // }/
        // if (!empty($replyData['priority'])) {
        //     $message['importance'] = $this->mapPriorityToImportance($replyData['priority']);
        // }
    }

    /**
     * Validate all inputs
     */
    private function validateInputs(array $replyData, array $originalMessage, string $replyType): void
    {
        if (empty($replyData)) {
            throw new InvalidArgumentException('Reply data cannot be empty');
        }

        if (empty($originalMessage)) {
            throw new InvalidArgumentException('Original message cannot be empty');
        }

        if (empty($replyData['body'])) {
            throw new InvalidArgumentException('Reply content cannot be empty');
        }
    }

    /**
     * Normalize content type to uppercase
     */
    private function normalizeContentType(string $contentType): string
    {
        $normalized = strtoupper($contentType);

        if (!in_array($normalized, ['HTML', 'TEXT'])) {
            throw new InvalidArgumentException("Invalid content type: {$contentType}. Must be HTML or TEXT");
        }

        return $normalized;
    }

    /**
     * Format custom headers for API
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

    /**
     * Get reply recipients preview (to show user who will receive the reply)
     */
    public function getReplyRecipientsPreview(string $messageId, string $replyType = 'reply'): array
    {
        try {
            $originalMessage = $this->getMessageDetails($messageId);

            if (empty($originalMessage)) {
                throw new Exception('Original message not found');
            }

            $sender = $originalMessage['from']['emailAddress'] ?? null;
            $toRecipients = $originalMessage['toRecipients'] ?? [];
            $ccRecipients = $originalMessage['ccRecipients'] ?? [];
            $currentUserEmail = $this->auth->email;

            if ($replyType === 'replyAll') {
                // For reply all, include sender + all original recipients except current user
                $allRecipients = array_merge(
                    [$sender],
                    $toRecipients,
                    $ccRecipients
                );

                // Remove duplicates and current user
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
                        'type' => $recipient === $sender ? 'sender' : 'recipient'
                    ];
                }

                return [
                    'success' => true,
                    'reply_type' => 'replyAll',
                    'recipients' => $uniqueRecipients,
                    'recipient_count' => count($uniqueRecipients),
                    'original_subject' => $originalMessage['subject'] ?? 'N/A'
                ];
            } else {
                // For regular reply, only include sender
                if (!$sender || !isset($sender['emailAddress']['address'])) {
                    throw new Exception('Original sender not found');
                }

                return [
                    'success' => true,
                    'reply_type' => 'reply',
                    'recipients' => [[
                        'email' => $sender['emailAddress']['address'],
                        'name' => $sender['emailAddress']['name'] ?? null,
                        'type' => 'sender'
                    ]],
                    'recipient_count' => 1,
                    'original_subject' => $originalMessage['subject'] ?? 'N/A'
                ];
            }
        } catch (Exception $e) {
            logger()->error('Failed to get reply recipients preview', [
                'message_id' => $messageId,
                'reply_type' => $replyType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'recipients' => [],
                'recipient_count' => 0
            ];
        }
    }

    /**
     * Get conversation messages
     */
    public function getConversationMessages(string $conversationId): array
    {
        try {
            $response = $this->makeRequest('GET', "/me/messages", [
                'query' => [
                    '$filter' => "conversationId eq '{$conversationId}'",
                    '$orderby' => 'receivedDateTime desc',
                    '$select' => 'id,subject,from,toRecipients,receivedDateTime,sentDateTime,bodyPreview,importance,hasAttachments'
                ]
            ]);

            return [
                'success' => true,
                'messages' => $response['value'] ?? [],
                'count' => count($response['value'] ?? [])
            ];
        } catch (Exception $e) {
            logger()->error('Failed to get conversation messages', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'messages' => []
            ];
        }
    }

    /**
     * Map priority to Outlook importance
     */
    private function mapPriorityToImportance(string $priority): string
    {
        return match (strtolower($priority)) {
            'high' => 'high',
            'low' => 'low',
            default => 'normal'
        };
    }

    /**
     * Format recipients for Outlook API
     */
    private function formatRecipients(array $recipients): array
    {
        return array_map(function ($recipient) {
            if (is_string($recipient)) {
                return ['emailAddress' => ['address' => $recipient]];
            }

            return [
                'emailAddress' => [
                    'address' => $recipient['email'] ?? $recipient['address'],
                    'name' => $recipient['name'] ?? null
                ]
            ];
        }, $recipients);
    }

    /**
     * Format attachments for Outlook API
     */
    private function formatAttachments(array $attachments): array
    {
        return array_map(function ($attachment) {
            if (isset($attachment['path']) && file_exists($attachment['path'])) {
                return [
                    '@odata.type' => '#microsoft.graph.fileAttachment',
                    'name' => $attachment['name'],
                    'contentType' => $attachment['mime_type'],
                    'contentBytes' => base64_encode(file_get_contents($attachment['path']))
                ];
            }

            return [
                '@odata.type' => '#microsoft.graph.fileAttachment',
                'name' => $attachment['name'],
                'contentType' => $attachment['mime_type'] ?? 'application/octet-stream',
                'contentBytes' => $attachment['content'] ?? ''
            ];
        }, $attachments);
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

            logger()->info('Message categories and flags updated', [
                'message_id' => $messageId,
                'categories' => $categories,
                'has_flag' => !empty($flagData),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return true;
        } catch (Exception $e) {
            logger()->error('Failed to set message categories', [
                'message_id' => $messageId,
                'categories' => $categories,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);

            return false;
        }
    }

    /**
     * Mark message as read or unread
     *
     * @param string $messageId Message ID
     * @param bool $isRead Read status
     * @return bool Success status
     * @return bool $auth
     */
    public function markMessage($auth, string $messageId, bool $isRead = true): bool
    {
        try {
            $this->auth = $auth;

            if (empty($messageId)) {
                throw new Exception('Message ID is required');
            }

            $this->makeRequest('PATCH', "/me/messages/{$messageId}", [
                'json' => ['isRead' => $isRead]
            ]);

            return true;
        } catch (Exception $e) {
            logger()->error('Failed to mark message', [
                'message_id' => $messageId,
                'is_read' => $isRead,
                'error' => $e->getMessage(),
                'user' => $this->auth->email ?? 'unknown'
            ]);
            return false;
        }
    }
}
