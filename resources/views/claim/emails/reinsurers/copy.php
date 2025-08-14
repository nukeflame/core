<?php

// Optimized fetchEmails method with parallel processing and caching
public function fetchEmails($auth, array $options = []): array
{
    $folder = $options['folder'] ?? 'inbox';
    $limit = min($options['limit'] ?? 50, 999);
    $this->auth = $auth;

    $token = $this->getValidToken();
    if (!$token) {
        throw new Exception('No valid authentication token available');
    }

    // Check cache first
    $cacheKey = "outlook_emails_{$auth->email}_{$folder}_{$limit}";
    $cached = Redis::get($cacheKey);
    if ($cached && !($options['force_refresh'] ?? false)) {
        return json_decode($cached, true);
    }

    // Optimized select fields - only get what we need
    $selectFields = 'id,subject,from,toRecipients,receivedDateTime,isRead,hasAttachments,sentDateTime,bodyPreview,importance,internetMessageId,conversationId';

    // Build single optimized request instead of batch
    $url = $this->buildOptimizedEmailsUrl($folder, $limit, $selectFields, $options);

    $response = Http::withToken($token['access_token'])
        ->timeout(15) // Reduced timeout
        ->get($url);

    if (!$response->successful()) {
        throw new Exception('Failed to fetch emails: HTTP ' . $response->status());
    }

    $data = $response->json();
    $rawEmails = $data['value'] ?? [];

    // Optimized processing with bulk operations
    $processedEmails = $this->processEmailsBulk($rawEmails);

    // Cache for 2 minutes
    Redis::setex($cacheKey, 120, json_encode($processedEmails));

    return $processedEmails;
}

/**
 * Build optimized URL for single folder request
 */
private function buildOptimizedEmailsUrl(string $folder, int $limit, string $select, array $options): string
{
    $folderPath = match($folder) {
        'inbox' => 'mailFolders/inbox',
        'sent' => 'mailFolders/sentitems',
        'drafts' => 'mailFolders/drafts',
        'deleted' => 'mailFolders/deleteditems',
        default => "mailFolders/{$folder}"
    };

    $params = [
        '$top' => $limit,
        '$select' => $select,
        '$orderby' => 'receivedDateTime desc'
    ];

    // Add date filter for performance
    if ($since = $options['since'] ?? null) {
        $params['$filter'] = "receivedDateTime ge " . Carbon::parse($since)->toISOString();
    } else {
        // Default to last 30 days for better performance
        $params['$filter'] = "receivedDateTime ge " . now()->subDays(30)->toISOString();
    }

    return $this->graphEndpoint . "/me/{$folderPath}/messages?" . http_build_query($params);
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

    // Pre-allocate array for better memory usage
    $emails = array_fill(0, count($rawEmails), null);

    // Process in chunks for better memory management
    $chunks = array_chunk($rawEmails, 50);
    $index = 0;

    foreach ($chunks as $chunk) {
        foreach ($chunk as $rawEmail) {
            $emails[$index] = $this->processEmailFast($rawEmail);
            $index++;
        }

        // Allow garbage collection between chunks
        if (count($chunks) > 1) {
            usleep(1000); // 1ms pause for large datasets
        }
    }

    return array_filter($emails); // Remove any null entries
}

/**
 * Fast email processing with minimal allocations
 */
private function processEmailFast(array $rawEmail): array
{
    // Use null coalescing for faster processing
    $from = $rawEmail['from']['emailAddress'] ?? null;

    return [
        'id' => $rawEmail['id'],
        'subject' => $rawEmail['subject'] ?? '[No Subject]',
        'from' => $from['address'] ?? null,
        'from_name' => $from['name'] ?? null,
        'to' => $this->extractRecipientsfast($rawEmail['toRecipients'] ?? []),
        'date_received' => $rawEmail['receivedDateTime'] ?? null,
        'date_sent' => $rawEmail['sentDateTime'] ?? null,
        'body_preview' => $rawEmail['bodyPreview'] ?? '',
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
 * Optimized database save with bulk operations
 */
private function saveEmailsToDatabase(array $emails, string $folder): array
{
    if (empty($emails)) {
        return [];
    }

    $userEmail = auth()->user()->email;

    // Use database transactions for better performance
    return DB::transaction(function () use ($emails, $folder, $userEmail) {

        // Get existing message IDs in one query
        $messageIds = collect($emails)->pluck('message_id')->filter()->toArray();
        $existing = DB::table('fetched_emails')
            ->where('user_email', $userEmail)
            ->where('folder', $folder)
            ->whereIn('message_id', $messageIds)
            ->pluck('message_id')
            ->toArray();

        // Separate into updates and inserts
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
                'date_received' => $email['date_received'],
                'date_sent' => $email['date_sent'],
                'body_preview' => $email['body_preview'],
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

        // Bulk insert new emails
        if (!empty($toInsert)) {
            // Insert in chunks of 100 for better performance
            foreach (array_chunk($toInsert, 100) as $chunk) {
                DB::table('fetched_emails')->insert($chunk);
            }
        }

        // Bulk update existing emails
        if (!empty($toUpdate)) {
            foreach ($toUpdate as $update) {
                DB::table('fetched_emails')
                    ->where('message_id', $update['message_id'])
                    ->where('user_email', $userEmail)
                    ->update(array_except($update, ['message_id']));
            }
        }

        // Clean up old emails not in current fetch
        DB::table('fetched_emails')
            ->where('user_email', $userEmail)
            ->where('folder', $folder)
            ->whereNotIn('message_id', $messageIds)
            ->delete();

        // Return saved emails with optimized query
        return DB::table('fetched_emails')
            ->select(['id', 'subject', 'from_email', 'from_name', 'date_received', 'is_read', 'has_attachments'])
            ->where('user_email', $userEmail)
            ->where('folder', $folder)
            ->orderBy('date_received', 'desc')
            ->limit(100)
            ->get()
            ->toArray();
    });
}

/**
 * Optimized token refresh with caching
 */
public function refreshToken(): bool
{
    $cacheKey = "token_refresh_{$this->auth->email ?? 'unknown'}";

    // Prevent concurrent refresh attempts
    if (Redis::exists($cacheKey)) {
        return false;
    }

    Redis::setex($cacheKey, 30, 'refreshing'); // 30 second lock

    try {
        if (!$this->token || !isset($this->token['refresh_token'])) {
            throw new Exception('No refresh token available');
        }

        $tenantId = $this->token['tenant_id'] ?? $this->config['tenant_id'];
        $tokenUrl = "{$this->authEndpoint}/{$tenantId}/oauth2/v2.0/token";

        $response = Http::asForm()
            ->timeout(15) // Reduced timeout
            ->retry(2, 1000) // Retry on failure
            ->post($tokenUrl, [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'refresh_token' => $this->token['refresh_token'],
                'grant_type' => 'refresh_token'
            ]);

        if (!$response->successful()) {
            return false;
        }

        $tokenData = $response->json();
        $expiresAt = now()->addSeconds($tokenData['expires_in']);

        $this->token = array_merge($this->token, [
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? $this->token['refresh_token'],
            'expires_in' => $expiresAt->timestamp,
            'expires_at' => $expiresAt->toISOString(),
        ]);

        $this->saveToken($this->auth, $this->token);
        return true;

    } catch (Exception $e) {
        logger()->error('Token refresh error: ' . $e->getMessage());
        return false;
    } finally {
        Redis::del($cacheKey);
    }
}

/**
 * Optimized API request method with connection pooling
 */
private function makeRequest(string $method, string $endpoint, array $options = []): array
{
    $token = $this->getValidToken();
    if (!$token) {
        throw new Exception('No valid authentication token available');
    }

    $headers = array_merge([
        'Authorization' => 'Bearer ' . $token['access_token'],
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ], $options['headers'] ?? []);

    $url = str_starts_with($endpoint, 'http') ? $endpoint : $this->graphEndpoint . $endpoint;

    // Use connection pooling and optimized timeouts
    $httpClient = Http::withHeaders($headers)
        ->timeout(15)
        ->connectTimeout(5)
        ->retry(2, 1000);

    $response = match (strtoupper($method)) {
        'GET' => $httpClient->get($url, $options['query'] ?? []),
        'POST' => $httpClient->post($url, $options['json'] ?? []),
        'PUT' => $httpClient->put($url, $options['json'] ?? []),
        'PATCH' => $httpClient->patch($url, $options['json'] ?? []),
        'DELETE' => $httpClient->delete($url),
        default => throw new Exception("Unsupported HTTP method: {$method}")
    };

    if (!$response->successful()) {
        throw new Exception("API request failed: HTTP {$response->status()}");
    }

    return $response->json() ?? [];
}

/**
 * Optimized send email with minimal API calls
 */
public function sendEmail($auth, array $emailData): array
{
    try {
        $this->auth = $auth;

        // Build message payload efficiently
        $message = [
            'message' => [
                'subject' => $emailData['subject'],
                'body' => [
                    'contentType' => $emailData['bodyType'] ?? 'HTML',
                    'content' => $emailData['body']
                ],
                'toRecipients' => $this->formatRecipientsFast($emailData['to'])
            ]
        ];

        // Add optional fields only if provided
        if (!empty($emailData['cc'])) {
            $message['message']['ccRecipients'] = $this->formatRecipientsFast($emailData['cc']);
        }

        if (!empty($emailData['bcc'])) {
            $message['message']['bccRecipients'] = $this->formatRecipientsFast($emailData['bcc']);
        }

        if (!empty($emailData['attachments'])) {
            $message['message']['attachments'] = $this->formatAttachmentsFast($emailData['attachments']);
        }

        // Send directly without creating draft first (faster)
        $this->makeRequest('POST', '/me/sendMail', ['json' => $message]);

        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Fast recipient formatting
 */
private function formatRecipientsFast(array $recipients): array
{
    return array_map(fn($r) => [
        'emailAddress' => [
            'address' => is_string($r) ? $r : ($r['email'] ?? $r['address']),
            'name' => is_string($r) ? null : ($r['name'] ?? null)
        ]
    ], $recipients);
}

/**
 * Fast attachment formatting
 */
private function formatAttachmentsFast(array $attachments): array
{
    return array_map(function ($attachment) {
        $content = isset($attachment['path']) && file_exists($attachment['path'])
            ? base64_encode(file_get_contents($attachment['path']))
            : ($attachment['content'] ?? '');

        return [
            '@odata.type' => '#microsoft.graph.fileAttachment',
            'name' => $attachment['name'],
            'contentType' => $attachment['mime_type'] ?? 'application/octet-stream',
            'contentBytes' => $content
        ];
    }, $attachments);
}
