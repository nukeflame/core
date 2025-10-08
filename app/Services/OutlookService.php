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
    public function makeRequest(string $method, string $endpoint, array $options = []): array
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

            $httpClient = $this->prepareHttpClient($token, $options, $requestId);

            $response = $this->executeHttpRequest($httpClient, $method, $url, $options);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

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

        if (str_contains($contentType, 'application/json')) {
            $data = $response->json();
            return is_array($data) ? $data : [];
        }

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

        if (str_contains($contentType, 'text/')) {
            return [
                'content' => $response->body(),
                'content_type' => $contentType
            ];
        }

        if ($method === 'DELETE' || $response->status() === 204) {
            return ['success' => true];
        }

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
            $this->validateEmailData($emailData);

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

        if (strlen($emailData['subject']) > 255) {
            throw new Exception('Email subject cannot exceed 255 characters');
        }

        if (!empty($emailData['body']) && strlen($emailData['body']) > 10485760) {
            throw new Exception('Email body cannot exceed 10MB');
        }

        if (isset($emailData['priority']) && !in_array($emailData['priority'], ['low', 'normal', 'high'])) {
            throw new Exception('Priority must be one of: low, normal, high');
        }

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
        $emailData['subject'] = trim(strip_tags($emailData['subject']));

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
                return [
                    'success' => true,
                    'message_id' => $messageId,
                    'message' => 'Email sent successfully'
                ];
            }
            return ['success' => false];
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

            logger()->info('Fetching contacts', [
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

            logger()->info('Contacts fetched successfully', [
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
            logger()->error('Failed to get contacts', [
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
     * Enrich contact data with additional information
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
     * Get all users in the organization with comprehensive filtering
     * Enhanced version with presence integration and advanced analytics
     *
     * @param array $options Query options (limit, filter, presence, etc.)
     * @return array Array of users with optional presence information
     */
    public function getAllUsers($auth, array $options = []): array
    {
        try {
            $this->auth = $auth;

            $top            = min($options['limit'] ?? 100, 999);
            // $orderBy        = $options['orderBy'] ?? 'displayName';
            $select         = $options['select'] ?? $this->getDefaultUserSelectFields();
            $filter         = $options['filter'] ?? null;
            $accountEnabled = $options['account_enabled'] ?? null;
            $includePresence = $options['include_presence'] ?? false;
            $includePhotos   = $options['include_photos'] ?? false;

            $query = [
                '$top'     => $top,
                '$select' => is_array($select) ? implode(',', $select) : $select,
            ];

            $filters = [];

            if ($accountEnabled !== null) {
                $filters[] = "accountEnabled eq " . ($accountEnabled ? 'true' : 'false');
            }

            if (!($options['include_guests'] ?? false)) {
                $filters[] = "userType eq 'Member'";
            }

            if ($filter) {
                $filters[] = $filter;
            }

            if (!empty($options['department'])) {
                $department = addslashes($options['department']);
                $filters[] = "department eq '{$department}'";
            }

            if (!empty($options['location'])) {
                $location = addslashes($options['location']);
                $filters[] = "(city eq '{$location}' or officeLocation eq '{$location}')";
            }

            if (!empty($options['job_title_contains'])) {
                $jobTitle = addslashes($options['job_title_contains']);
                $filters[] = "startswith(jobTitle, '{$jobTitle}')";
            }

            if (!empty($filters)) {
                $query['$filter'] = implode(' and ', $filters);
            }

            $startTime = microtime(true);

            $response = $this->makeRequest('GET', '/users', ['query' => $query]);
            $users = $response['value'] ?? [];

            $fetchTime = round((microtime(true) - $startTime) * 1000, 2);

            $processedUsers = [];
            $presenceData = [];

            if ($includePresence && !empty($users)) {
                $presenceData = $this->getBulkUserPresence(array_column($users, 'id'));
            }

            foreach ($users as $user) {
                $enrichedUser = $this->enrichUserData($user, [
                    'include_presence' => $includePresence,
                    'include_photos'   => $includePhotos,
                    'presence_data'    => $presenceData[$user['id']] ?? null,
                ]);
                $processedUsers[] = $enrichedUser;
            }

            $totalTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'success'     => true,
                'users'       => $processedUsers,
                'count'       => count($processedUsers),
                'performance' => [
                    'fetch_time_ms' => $fetchTime,
                    'total_time_ms' => $totalTime,
                ],
                'pagination'  => [
                    'current_count' => count($processedUsers),
                    'top'           => $top,
                    'nextLink'      => $response['@odata.nextLink'] ?? null,
                ],
                'retrieved_at' => now()->toISOString(),
            ];
        } catch (Exception $e) {
            logger()->error('Failed to get all users', [
                'error' => $e->getMessage(),
                'user'  => $this->auth->email ?? 'unknown',
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
                'users'   => [],
                'count'   => 0,
            ];
        }
    }

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
                ($insights['communication']['users_with_phone'] / $totalUsers) * 100,
                2
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

            $batchSize = min($options['batch_size'] ?? 20, 20);
            $includeDetails = $options['include_details'] ?? true;

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
                    logger()->warning('Batch presence request failed', [
                        'batch_index' => $batchIndex,
                        'batch_size' => count($batch),
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            return $allPresenceData;
        } catch (Exception $e) {
            logger()->error('Failed to get bulk user presence', [
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
                        logger()->debug('Individual presence request failed', [
                            'user_id' => $userId,
                            'status' => $batchResponse['status'],
                            'batch_index' => $batchIndex
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            logger()->warning('Batch presence processing failed', [
                'batch_index' => $batchIndex,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        return $presenceData;
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
}
