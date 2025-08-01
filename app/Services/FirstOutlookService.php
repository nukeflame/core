<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use TheNetworg\OAuth2\Client\Provider\Azure;
use League\OAuth2\Client\Token\AccessToken;
use Carbon\Carbon;

class FirstOutlookService
{
    private Azure $oauthProvider;
    private const GRAPH_API_BASE = 'https://graph.microsoft.com/v1.0';
    private const TOKEN_REFRESH_BUFFER = 300; // 5 minutes

    public function __construct()
    {
        $this->initializeOAuthProvider();
    }

    /**
     * Initialize OAuth provider for Microsoft Azure/Outlook
     */
    private function initializeOAuthProvider(): void
    {
        $this->oauthProvider = new Azure([
            'clientId' => config('services.azure.client_id'),
            'clientSecret' => config('services.azure.client_secret'),
            'redirectUri' => config('services.azure.redirect_uri'),
            'urlAuthorize' => 'https://login.microsoftonline.com/' . config('services.azure.tenant_id', 'common') . '/oauth2/v2.0/authorize',
            'urlAccessToken' => 'https://login.microsoftonline.com/' . config('services.azure.tenant_id', 'common') . '/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
            'scopes' => $this->getRequiredScopes(),
        ]);
    }

    /**
     * Get all required scopes for Outlook access
     */
    private function getRequiredScopes(): array
    {
        return [
            'https://graph.microsoft.com/Mail.Read',
            'https://graph.microsoft.com/Mail.ReadWrite',
            'https://graph.microsoft.com/User.Read',
            'offline_access'
        ];
    }

    /**
     * Get valid access token (with refresh if needed)
     */
    public function getValidAccessToken(string $userEmail): ?string
    {
        $tokenData = DB::table('oauth_tokens')
            ->where('email', $userEmail)
            ->where('provider', 'outlook')
            ->first();

        if (!$tokenData) {
            Log::warning('No OAuth token found for user', ['email' => $userEmail]);
            return null;
        }

        try {
            $accessToken = new AccessToken([
                'access_token' => decrypt($tokenData->access_token),
                'refresh_token' => decrypt($tokenData->refresh_token),
                'expires' => $tokenData->expires_at
            ]);

            // Check if token needs refresh (with buffer)
            if ($this->tokenNeedsRefresh($accessToken)) {
                Log::info('Token expired or close to expiry, refreshing...', ['email' => $userEmail]);
                return $this->refreshAccessToken($userEmail, $accessToken);
            }

            // Validate token with a quick API call
            if (!$this->validateToken($accessToken->getToken())) {
                Log::warning('Token validation failed, attempting refresh...', ['email' => $userEmail]);
                return $this->refreshAccessToken($userEmail, $accessToken);
            }

            return $accessToken->getToken();
        } catch (\Exception $e) {
            Log::error('Token processing failed', [
                'email' => $userEmail,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if token needs refresh
     */
    private function tokenNeedsRefresh(AccessToken $token): bool
    {
        return $token->getExpires() &&
            $token->getExpires() <= (time() + self::TOKEN_REFRESH_BUFFER);
    }

    /**
     * Refresh the access token using refresh token
     */
    private function refreshAccessToken(string $userEmail, AccessToken $accessToken): ?string
    {
        try {
            $newAccessToken = $this->oauthProvider->getAccessToken('refresh_token', [
                'refresh_token' => $accessToken->getRefreshToken(),
            ]);

            // Update stored token
            DB::table('oauth_tokens')
                ->where('email', $userEmail)
                ->where('provider', 'outlook')
                ->update([
                    'access_token' => encrypt($newAccessToken->getToken()),
                    'refresh_token' => encrypt($newAccessToken->getRefreshToken()),
                    'expires_at' => $newAccessToken->getExpires(),
                    'updated_at' => now()
                ]);

            Log::info('Token refreshed successfully', ['email' => $userEmail]);
            return $newAccessToken->getToken();
        } catch (\Exception $e) {
            Log::error('Failed to refresh token', [
                'email' => $userEmail,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Validate token by making a test API call
     */
    private function validateToken(string $accessToken): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->timeout(10)->get(self::GRAPH_API_BASE . '/me');

            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Token validation exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Fetch emails using Microsoft Graph API
     */
    public function fetchEmails(string $userEmail, array $options = []): array
    {
        $accessToken = $this->getValidAccessToken($userEmail);
        if (!$accessToken) {
            throw new \Exception('Failed to obtain valid access token');
        }

        $folder = $options['folder'] ?? 'inbox';
        $limit = min($options['limit'] ?? 50, 999); // Graph API max is 999
        $since = $options['since'] ?? null;
        $select = $options['select'] ?? $this->getDefaultSelectFields();

        $url = $this->buildEmailsUrl($folder, $limit, $since, $select);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->get($url);

            if (!$response->successful()) {
                throw new \Exception('Graph API request failed: ' . $response->status() . ' - ' . $response->body());
            }

            $data = $response->json();
            $emails = $this->processEmails($data['value'] ?? [], $userEmail, $folder);

            Log::info('Successfully fetched emails', [
                'email' => $userEmail,
                'count' => count($emails),
                'folder' => $folder
            ]);

            return $emails;
        } catch (\Exception $e) {
            Log::error('Failed to fetch emails', [
                'email' => $userEmail,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Build the Graph API URL for fetching emails
     */
    private function buildEmailsUrl(string $folder, int $limit, ?string $since, array $select): string
    {
        $folderPath = $folder === 'inbox' ? 'inbox' : "mailFolders/{$folder}";
        $url = self::GRAPH_API_BASE . "/me/{$folderPath}/messages";

        $params = [
            '$top' => $limit,
            '$select' => implode(',', $select),
            '$orderby' => 'receivedDateTime desc'
        ];

        if ($since) {
            $sinceDate = Carbon::parse($since)->toISOString();
            $params['$filter'] = "receivedDateTime ge {$sinceDate}";
        }

        return $url . '?' . http_build_query($params);
    }

    /**
     * Get default fields to select from Graph API
     */
    private function getDefaultSelectFields(): array
    {
        return [
            'id',
            'subject',
            'from',
            'toRecipients',
            'ccRecipients',
            'bccRecipients',
            'receivedDateTime',
            'sentDateTime',
            'body',
            'bodyPreview',
            'importance',
            'isRead',
            'hasAttachments',
            'internetMessageId',
            'conversationId',
            'parentFolderId'
        ];
    }

    /**
     * Process raw email data from Graph API
     */
    private function processEmails(array $rawEmails, string $userEmail, string $folder): array
    {
        $emails = [];

        foreach ($rawEmails as $rawEmail) {
            try {
                $email = $this->transformEmail($rawEmail, $folder);
                $emails[] = $email;
                $this->storeEmailInDatabase($email, $userEmail);
            } catch (\Exception $e) {
                Log::warning('Failed to process email', [
                    'email_id' => $rawEmail['id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $emails;
    }

    /**
     * Transform raw Graph API email data to our format
     */
    private function transformEmail(array $rawEmail, string $folder): array
    {
        return [
            'id' => $rawEmail['id'],
            'subject' => $rawEmail['subject'] ?? '',
            'from' => $this->extractEmailAddress($rawEmail['from'] ?? null),
            'from_name' => $this->extractDisplayName($rawEmail['from'] ?? null),
            'to' => $this->extractRecipients($rawEmail['toRecipients'] ?? []),
            'cc' => $this->extractRecipients($rawEmail['ccRecipients'] ?? []),
            'bcc' => $this->extractRecipients($rawEmail['bccRecipients'] ?? []),
            'date_received' => $rawEmail['receivedDateTime'] ?? null,
            'date_sent' => $rawEmail['sentDateTime'] ?? null,
            'body_text' => $this->extractBodyContent($rawEmail['body'] ?? null, 'text'),
            'body_html' => $this->extractBodyContent($rawEmail['body'] ?? null, 'html'),
            'body_preview' => $rawEmail['bodyPreview'] ?? '',
            'importance' => $rawEmail['importance'] ?? 'normal',
            'is_read' => $rawEmail['isRead'] ?? false,
            'has_attachments' => $rawEmail['hasAttachments'] ?? false,
            'message_id' => $rawEmail['internetMessageId'] ?? null,
            'conversation_id' => $rawEmail['conversationId'] ?? null,
            'folder_id' => $rawEmail['parentFolderId'] ?? null,
            'folder' => $folder,
            'attachments' => [] // Will be populated separately if needed
        ];
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
     * Extract body content based on content type preference
     */
    private function extractBodyContent(?array $body, string $preferredType = 'html'): ?string
    {
        if (!$body || !isset($body['content'])) {
            return null;
        }

        $contentType = strtolower($body['contentType'] ?? 'text');

        if ($preferredType === 'text' && $contentType === 'html') {
            // Convert HTML to plain text if needed
            return strip_tags($body['content']);
        }

        return $body['content'];
    }

    /**
     * Fetch email attachments
     */
    public function fetchEmailAttachments(string $userEmail, string $emailId): array
    {
        $accessToken = $this->getValidAccessToken($userEmail);
        if (!$accessToken) {
            throw new \Exception('Failed to obtain valid access token');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get(self::GRAPH_API_BASE . "/me/messages/{$emailId}/attachments");

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch attachments: ' . $response->status());
            }

            $attachments = [];
            foreach ($response->json()['value'] ?? [] as $attachment) {
                $attachments[] = [
                    'id' => $attachment['id'],
                    'name' => $attachment['name'] ?? 'Unknown',
                    'content_type' => $attachment['contentType'] ?? 'application/octet-stream',
                    'size' => $attachment['size'] ?? 0,
                    'is_inline' => $attachment['isInline'] ?? false,
                ];
            }

            return $attachments;
        } catch (\Exception $e) {
            Log::error('Failed to fetch email attachments', [
                'email_id' => $emailId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get mail folders
     */
    public function getMailFolders(string $userEmail): array
    {
        $accessToken = $this->getValidAccessToken($userEmail);
        if (!$accessToken) {
            throw new \Exception('Failed to obtain valid access token');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get(self::GRAPH_API_BASE . '/me/mailFolders');

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch folders: ' . $response->status());
            }

            $folders = [];
            foreach ($response->json()['value'] ?? [] as $folder) {
                $folders[] = [
                    'id' => $folder['id'],
                    'display_name' => $folder['displayName'],
                    'parent_folder_id' => $folder['parentFolderId'] ?? null,
                    'child_folder_count' => $folder['childFolderCount'] ?? 0,
                    'unread_item_count' => $folder['unreadItemCount'] ?? 0,
                    'total_item_count' => $folder['totalItemCount'] ?? 0,
                ];
            }

            return $folders;
        } catch (\Exception $e) {
            Log::error('Failed to fetch mail folders', [
                'user_email' => $userEmail,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Store email in database
     */
    private function storeEmailInDatabase(array $email, string $userEmail): void
    {
        try {
            DB::table('fetched_emails')->updateOrInsert(
                [
                    'message_id' => $email['message_id'],
                    'user_email' => $userEmail
                ],
                [
                    'outlook_id' => $email['id'],
                    'subject' => $email['subject'],
                    'from_email' => $email['from'],
                    'from_name' => $email['from_name'],
                    'to_recipients' => json_encode($email['to']),
                    'cc_recipients' => json_encode($email['cc']),
                    'bcc_recipients' => json_encode($email['bcc']),
                    'date_received' => $email['date_received'],
                    'date_sent' => $email['date_sent'],
                    'body_text' => $email['body_text'],
                    'body_html' => $email['body_html'],
                    'body_preview' => $email['body_preview'],
                    'importance' => $email['importance'],
                    'is_read' => $email['is_read'],
                    'has_attachments' => $email['has_attachments'],
                    'conversation_id' => $email['conversation_id'],
                    'folder_id' => $email['folder_id'],
                    'folder' => $email['folder'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        } catch (\Exception $e) {
            Log::warning('Failed to store email in database', [
                'email_id' => $email['id'],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get user profile information
     */
    public function getUserProfile(string $userEmail): ?array
    {
        $accessToken = $this->getValidAccessToken($userEmail);
        if (!$accessToken) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get(self::GRAPH_API_BASE . '/me');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to fetch user profile', [
                'user_email' => $userEmail,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }


    // getEmailAttachments

}
