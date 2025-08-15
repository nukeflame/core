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

class OutlookService
{
    private string $graphEndpoint = 'https://graph.microsoft.com/v1.0';
    private string $authEndpoint = 'https://login.microsoftonline.com';
    private ?array $token = null;
    private array $config;
    private int $timeout;
    protected $auth = null;

    private array $requiredScopes = [
        // 'openid offline_access profile User.Read Mail.ReadWrite Files.ReadWrite'
        // 'openid',
        // 'offline_access',
        // 'profile',
        // 'User.Read',
        // 'Mail.ReadWrite',
        // 'Mail.Send',
        // 'Mail.Read',
        // 'Mail.ReadBasic',
        // 'Files.ReadWrite',
        // 'User.ReadBasic.All',

        // application
        'https://graph.microsoft.com/.default',
        // delagated
        // 'openid offline_access profile User.Read Mail.ReadWrite Files.ReadWrite'
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
                throw new Exception("Missing required Azure configuration: {$key}");
            }
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
            $tokenRecord = DB::table('oauth_tokens')
                ->where('provider', 'outlook')
                ->where('email', $userEmail)
                ->first();

            if (!$tokenRecord) {
                return false;
            }

            $expiresAt = Carbon::createFromTimestamp($tokenRecord->expires_at);
            return !$expiresAt->isPast();
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

            $user = null;
            $this->saveToken($user, $this->token);
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
            'login_hint' => auth()->user()->email,
        ];

        $authUrl = "{$this->authEndpoint}/{$this->config['tenant_id']}/oauth2/v2.0/authorize?" . http_build_query($authParams);

        return [
            'state' => $state,
            'authUrl' => $authUrl,
        ];
    }

    /**
     * Make authenticated HTTP request to Microsoft Graph
     */
    private function makeRequest(string $method, string $endpoint, array $options = []): array
    {
        $token = $this->getValidToken();

        if (!$token) {
            throw new Exception('No valid authentication token available');
        }

        $url = str_starts_with($endpoint, 'http') ? $endpoint : $this->graphEndpoint . $endpoint;

        $httpClient = Http::withToken($token['access_token'])->timeout(30);

        $response = match (strtoupper($method)) {
            'GET' => $httpClient->get($url, $options['query'] ?? []),
            'POST' => $httpClient->post($url, $options['json'] ?? []),
            'PUT' => $httpClient->put($url, $options['json'] ?? []),
            'PATCH' => $httpClient->patch($url, $options['json'] ?? []),
            'DELETE' => $httpClient->delete($url),
            default => throw new Exception("Unsupported HTTP method: {$method}")
        };


        if (!$response->successful()) {
            $error = [
                'status' => $response->status(),
                'body' => $response->body(),
                'endpoint' => $endpoint
            ];

            logger()->error('Microsoft Graph API request failed: ' . json_encode([
                'error' => $error
            ], JSON_PRETTY_PRINT));

            throw new Exception("API request failed: HTTP {$response->status()}");
        }

        return $response->json() ?? [];
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
     * Mark message as read/unread
     */
    public function markMessage(string $messageId, bool $isRead = true): bool
    {
        try {
            $this->makeRequest('PATCH', "/me/messages/{$messageId}", [
                'json' => ['isRead' => $isRead]
            ]);
            return true;
        } catch (Exception $e) {
            logger()->error('Failed to mark message: ' . $e->getMessage());
            return false;
        }
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
     * Send email and return message details including ID
     */
    public function sendEmail($auth, array $emailData): array
    {
        try {
            $this->auth = $auth;

            $message = [
                'message' => [
                    'subject' => $emailData['subject'],
                    'body' => [
                        'contentType' => $emailData['bodyType'] ?? 'HTML',
                        'content' => $emailData['body']
                    ],
                    'toRecipients' => $this->formatRecipients($emailData['to'])
                ]
            ];

            if (!empty($emailData['cc'])) {
                $message['message']['ccRecipients'] = $this->formatRecipients($emailData['cc']);
            }

            if (!empty($emailData['bcc'])) {
                $message['message']['bccRecipients'] = $this->formatRecipients($emailData['bcc']);
            }

            if (!empty($emailData['attachments'])) {
                $message['message']['attachments'] = $this->formatAttachments($emailData['attachments']);
            }

            if (!empty($emailData['replyToId'])) {
                $message['message']['replyTo'] = $this->formatRecipients([$emailData['replyToEmail']]);
                // Set conversation ID for threading
                if (!empty($emailData['conversationId'])) {
                    $message['message']['conversationId'] = $emailData['conversationId'];
                }
            }

            if (!empty($emailData['priority'])) {
                $message['message']['importance'] = $this->mapPriorityToImportance($emailData['priority']);
            }

            if (!empty($emailData['customHeaders'])) {
                $message['message']['internetMessageHeaders'] = $emailData['customHeaders'];
            }

            // Send the email
            $this->makeRequest('POST', '/me/sendMail', ['json' => $message]);

            return $this->sendEmailWithMessageId($auth, $emailData);
        } catch (Exception $e) {
            logger()->error('Failed to send email via Outlook API', [
                'error' => $e->getMessage(),
                'subject' => $emailData['subject'] ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message_id' => null
            ];
        }
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
     * Get message details by ID
     */
    public function getMessageDetails(string $messageId): array
    {
        try {
            $response = $this->makeRequest('GET', "/me/messages/{$messageId}");

            return [
                'id' => $response['id'],
                'conversationId' => $response['conversationId'],
                'internetMessageId' => $response['internetMessageId'],
                'subject' => $response['subject'],
                'sentDateTime' => $response['sentDateTime'],
                'receivedDateTime' => $response['receivedDateTime'],
                'from' => $response['from'],
                'toRecipients' => $response['toRecipients'],
                'ccRecipients' => $response['ccRecipients'] ?? [],
                'bccRecipients' => $response['bccRecipients'] ?? [],
                'hasAttachments' => $response['hasAttachments'] ?? false,
                'importance' => $response['importance'] ?? 'normal',
                'isRead' => $response['isRead'] ?? false
            ];
        } catch (Exception $e) {
            logger()->error('Failed to get message details: ' . json_encode([
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT));
            return [];
        }
    }

    /**
     * Send a reply to an existing message
     */
    public function sendReply($auth, string $originalMessageId, array $replyData): array
    {
        try {
            $this->auth = $auth;
            $originalMessage = $this->getMessageDetails($originalMessageId);

            if (empty($originalMessage)) {
                throw new Exception('Original message not found');
            }

            $replyMessage = [
                "message" => [
                    "body" => [
                        "contentType" => $replyData['bodyType'] ?? 'html',
                        "content" => $replyData['body']
                    ]
                ]
            ];

            // if (!empty($replyData['attachments'])) {
            //     $replyMessage['message']['attachments'] = $this->formatAttachments($replyData['attachments']);
            // }

            logger()->info('Reply: ' . json_encode([
                'success' => true,
                'original_message_id' => $originalMessageId,
                'conversation_id' => $originalMessage['conversationId'],
                'message' => $replyData['body']
            ], JSON_PRETTY_PRINT));

            $this->makeRequest('POST', "/me/messages/{$originalMessageId}/reply", [
                'json' => $replyMessage
            ]);

            return [
                'success' => true,
                'original_message_id' => $originalMessageId,
                'conversation_id' => $originalMessage['conversationId'],
                'message' => 'Reply sent successfully'
            ];
        } catch (Exception $e) {
            logger()->error('Failed to send reply: ' . json_encode([
                'original_message_id' => $originalMessageId,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT));

            return [
                'success' => false,
                'error' => $e->getMessage()
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
}
