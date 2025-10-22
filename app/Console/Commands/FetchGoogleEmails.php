<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Webklex\PHPIMAP\ClientManager;
use Google\Client as GoogleClient;
use Google\Service\Gmail;


class FetchGoogleEmails extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'google:fetch-emails
                           {--user= : Email address to fetch emails for}
                           {--folder=INBOX : Folder to fetch from}
                           {--limit=50 : Number of emails to fetch}
                           {--debug : Enable debug mode}
                           {--since= : Fetch emails since date (Y-m-d format)}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch emails from Gmail using IMAP with OAuth authentication';

    private $googleClient;
    private $imapClient;

    public function __construct()
    {
        parent::__construct();
        $this->initializeGoogleClient();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $userEmail = $this->option('user') ?? config('mail.username');
            $folder = $this->option('folder');
            $limit = (int) $this->option('limit') ?? 50;
            $since = $this->option('since') ?? now()->subDay()->format('Y-m-d');

            if (!$userEmail) {
                $this->error('Please provide a user email address using --user option');
                return Command::FAILURE;
            }

            $this->info("Starting email fetch for: {$userEmail}");
            $this->info("Folder: {$folder}, Limit: {$limit}");

            // Get or refresh OAuth token
            $accessToken = $this->getValidAccessToken($userEmail);
            if (!$accessToken) {
                $this->error('Failed to obtain valid access token');
                return Command::FAILURE;
            }

            if ($this->option('debug')) {
                $debugResult = $this->debugConnection($userEmail, $accessToken);
                if (!$debugResult) {
                    return Command::FAILURE;
                }
            }

            // Connect to IMAP
            $this->connectToIMAP($userEmail, $accessToken);

            // Fetch emails
            $emails = $this->fetchEmails($folder, $limit, $since, $userEmail);

            $this->info("Successfully fetched " . count($emails) . " emails");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Initialize Google OAuth client
     */
    private function initializeGoogleClient()
    {
        $this->googleClient = new GoogleClient();
        $this->googleClient->setClientId(config('services.google.client_id'));
        $this->googleClient->setClientSecret(config('services.google.client_secret'));
        $this->googleClient->setRedirectUri(config('services.google.redirect_uri'));
        $this->googleClient->setScopes($this->getRequiredScopes());
        $this->googleClient->setAccessType('offline');
        // $this->googleClient->setPrompt('consent');
    }

    /**
     * Get all required scopes for Gmail access
     */
    private function getRequiredScopes(): array
    {
        return [
            Gmail::GMAIL_READONLY,
            Gmail::GMAIL_MODIFY,
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ];
    }

    /**
     * Get valid access token (with refresh if needed)
     */
    private function getValidAccessToken($userEmail)
    {
        // Check if we have a stored token
        $tokenData = DB::table('oauth_tokens')
            ->where('email', $userEmail)
            ->where('provider', 'google')
            ->first();

        if (!$tokenData) {
            $this->error('No OAuth token found for user. Please authenticate first.');
            $this->info('Run: php artisan google:authenticate --user=' . $userEmail);
            return null;
        }

        try {
            $accessToken = [
                'access_token' => decrypt($tokenData->access_token),
                'refresh_token' => decrypt($tokenData->refresh_token),
                'expires_in' => $tokenData->expires_at - time(),
                'created' => time()
            ];

            $this->googleClient->setAccessToken($accessToken);

            // Check if token is expired and refresh if needed
            if ($this->googleClient->isAccessTokenExpired()) {
                $this->info('Token expired, refreshing...');

                if ($this->googleClient->getRefreshToken()) {
                    $newToken = $this->googleClient->fetchAccessTokenWithRefreshToken();

                    if (isset($newToken['error'])) {
                        $this->error('Token refresh failed: ' . $newToken['error']);
                        return null;
                    }

                    // Update stored token
                    DB::table('oauth_tokens')
                        ->where('email', $userEmail)
                        ->where('provider', 'google')
                        ->update([
                            'access_token' => encrypt($newToken['access_token']),
                            'expires_at' => time() + $newToken['expires_in'],
                            'updated_at' => now()
                        ]);

                    $this->info('Token refreshed successfully');
                    return $newToken['access_token'];
                } else {
                    $this->error('No refresh token available');
                    return null;
                }
            }

            return $accessToken['access_token'];
        } catch (\Exception $e) {
            $this->error('Token processing failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Connect to Gmail IMAP server with OAuth
     */
    private function connectToIMAP($userEmail, $accessToken)
    {
        $clientManager = new ClientManager();

        $this->info('Attempting Gmail IMAP connection...');

        try {
            $this->imapClient = $clientManager->make([
                'host' => 'imap.gmail.com',
                'port' => 993,
                'encryption' => 'ssl',
                'validate_cert' => true,
                'username' => $userEmail,
                'password' => $accessToken,
                'protocol' => 'imap',
                'authentication' => 'oauth',
                'timeout' => 30,
                'options' => [
                    'delimiter' => '/',
                    'fetch' => \Webklex\PHPIMAP\IMAP::FT_PEEK,
                    'common_folders' => [
                        'root' => 'INBOX',
                        'junk' => 'INBOX/Spam',
                        'draft' => 'INBOX/Drafts',
                        'sent' => 'INBOX/Sent',
                        'trash' => 'INBOX/Trash',
                    ],
                ]
            ]);

            $this->imapClient->connect();
            $this->info('Successfully connected to Gmail IMAP server');
        } catch (\Exception $e) {
            $this->error('IMAP Connection failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch emails from specified folder (including custom labels)
     */
    private function fetchEmails($folderName, $limit, $since, $userEmail)
    {
        try {
            $this->info("Fetching emails from folder: {$folderName}");

            // Get all available folders first (for debugging)
            if ($this->option('debug')) {
                $this->debugAvailableFolders();
            }

            // Try to get the specified folder/label
            $folder = $this->getFolder($folderName);

            if (!$folder) {
                $this->error("Folder '{$folderName}' not found");
                return [];
            }

            $this->info("Successfully accessed folder: {$folder->name}");

            // Build query with optional date filter
            $query = $folder->messages();

            if ($since) {
                $sinceDate = \Carbon\Carbon::parse($since);
                $query->since($sinceDate);
                $this->info("Filtering emails since: {$sinceDate->format('Y-m-d')}");
            }

            // Limit the number of messages
            $messages = $query->limit($limit)->get();

            $this->info("Found {$messages->count()} messages in {$folderName}");

            // Keep emails in memory array for debugging
            $emails = [];
            $bar = $this->output->createProgressBar($messages->count());
            $bar->start();

            foreach ($messages as $message) {
                try {
                    $email = [
                        'uid' => $message->getUid(),
                        'subject' => $message->getSubject(),
                        'from' => $message->getFrom()[0]->mail ?? 'Unknown',
                        'from_name' => $message->getFrom()[0]->personal ?? '',
                        'to' => collect($message->getTo())->pluck('mail')->toArray(),
                        'date' => $message->getDate(),
                        'body_text' => $message->getTextBody(),
                        'body_html' => $message->getHTMLBody(),
                        'attachments' => $this->getAttachmentInfo($message),
                        'flags' => $message->getFlags()->toArray(),
                        'size' => $message->getSize(),
                        'message_id' => $message->getMessageId(),
                        'folder' => $folderName
                    ];

                    $emails[] = $email;

                    // Debug output for first few emails
                    if ($this->option('debug') && count($emails) <= 3) {
                        $this->debugEmailInfo($email);
                    }

                    $this->storeEmailInDatabase($email, $userEmail);
                } catch (\Exception $e) {
                    $this->warn("Failed to process message UID {$message->getUid()}: " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            // Debug summary
            if ($this->option('debug')) {
                $this->debugEmailSummary($emails);
            }

            return $emails;
        } catch (\Exception $e) {
            $this->error("Error fetching emails: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get folder by name (handles both standard folders and custom labels)
     */
    private function getFolder($folderName)
    {
        try {
            // First try direct access
            return $this->imapClient->getFolder($folderName);
        } catch (\Exception $e) {
            // If direct access fails, search through all folders
            $folders = $this->imapClient->getFolders();

            foreach ($folders as $folder) {
                if (
                    $folder->name === $folderName ||
                    $folder->full_name === $folderName ||
                    str_contains($folder->name, $folderName)
                ) {
                    return $folder;
                }
            }

            return null;
        }
    }

    /**
     * Debug available folders and labels
     */
    private function debugAvailableFolders()
    {
        $this->info('=== AVAILABLE FOLDERS/LABELS ===');

        try {
            $folders = $this->imapClient->getFolders();

            foreach ($folders as $folder) {
                $messageCount = 0;
                try {
                    $messageCount = $folder->messages()->count();
                } catch (\Exception $e) {
                    // Some folders might not be accessible
                }

                $this->info("📁 {$folder->name} ({$folder->full_name}) - {$messageCount} messages");
            }

            $this->newLine();
        } catch (\Exception $e) {
            $this->error("Could not list folders: " . $e->getMessage());
        }
    }

    /**
     * Debug individual email information
     */
    private function debugEmailInfo($email)
    {
        $this->info("--- Email Debug ---");
        $this->info("Subject: " . substr($email['subject'], 0, 50) . "...");
        $this->info("From: {$email['from_name']} <{$email['from']}>");
        $this->info("Date: {$email['date']}");
        $this->info("Size: " . number_format($email['size']) . " bytes");
        $this->info("Attachments: " . count($email['attachments']));
        $this->info("Flags: " . implode(', ', $email['flags']));
        $this->newLine();
    }

    /**
     * Debug email collection summary
     */
    private function debugEmailSummary($emails)
    {
        $this->info('=== EMAIL FETCH SUMMARY ===');
        $this->info("Total emails in memory: " . count($emails));

        if (!empty($emails)) {
            $totalSize = array_sum(array_column($emails, 'size'));
            $this->info("Total size: " . number_format($totalSize) . " bytes");

            $dateRange = [
                'oldest' => min(array_column($emails, 'date')),
                'newest' => max(array_column($emails, 'date'))
            ];
            $this->info("Date range: {$dateRange['oldest']} to {$dateRange['newest']}");

            $senders = array_count_values(array_column($emails, 'from'));
            $this->info("Unique senders: " . count($senders));

            // Show memory usage
            $memoryUsage = memory_get_usage(true) / 1024 / 1024;
            $this->info("Memory usage: " . number_format($memoryUsage, 2) . " MB");
        }

        $this->newLine();
    }

    /**
     * Get attachment information
     */
    private function getAttachmentInfo($message)
    {
        $attachments = [];

        if ($message->hasAttachments()) {
            foreach ($message->getAttachments() as $attachment) {
                $attachments[] = [
                    'name' => $attachment->getName(),
                    'size' => $attachment->getSize(),
                    'type' => $attachment->getContentType(),
                    'disposition' => $attachment->getDisposition()
                ];
            }
        }

        return $attachments;
    }

    /**
     * Store email in database
     */
    private function storeEmailInDatabase($email, $userEmail)
    {
        try {
            DB::table('fetched_emails')->updateOrInsert(
                [
                    'message_id' => $email['message_id'],
                    'user_email' => $userEmail
                ],
                [
                    'uid' => $email['uid'],
                    'subject' => $email['subject'],
                    'from_email' => $email['from'],
                    'from_name' => $email['from_name'],
                    'to_emails' => json_encode($email['to']),
                    'date_received' => $email['date'],
                    'body_text' => $email['body_text'],
                    'body_html' => $email['body_html'],
                    'attachments' => json_encode($email['attachments']),
                    'flags' => json_encode($email['flags']),
                    'size' => $email['size'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        } catch (\Exception $e) {
            $this->warn("Failed to store email in database: " . $e->getMessage());
        }
    }

    /**
     * Debug connection method
     */
    private function debugConnection($userEmail, $accessToken)
    {
        $this->info('=== DEBUG GMAIL CONNECTION ===');

        // Test 1: Validate token with Gmail API
        $this->info('1. Testing token with Gmail API...');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://www.googleapis.com/gmail/v1/users/me/profile');

        if ($response->successful()) {
            $this->info('✓ Token is valid for Gmail API');
            $profile = $response->json();
            $this->info('Email: ' . $profile['emailAddress']);
        } else {
            $this->error('✗ Token invalid for Gmail API: ' . $response->status());
            return false;
        }

        // Test 2: Try IMAP connection
        $this->info('2. Testing Gmail IMAP connection...');

        try {
            $clientManager = new ClientManager();
            $client = $clientManager->make([
                'host' => 'imap.gmail.com',
                'port' => 993,
                'encryption' => 'ssl',
                'validate_cert' => true,
                'username' => $userEmail,
                'password' => $accessToken,
                'protocol' => 'imap',
                'authentication' => 'oauth',
                'timeout' => 10,
                'options'       => [
                    'common_folders' => [
                        'root' => 'INBOX',
                        'junk' => 'INBOX/Spam',
                        'draft' => 'INBOX/Drafts',
                        'sent' => 'INBOX/Sent',
                        'trash' => 'INBOX/Trash',
                    ],
                    'debug' => true,
                ],
            ]);

            $client->connect();
            $this->info('✓ Gmail IMAP connection successful!');
            $client->disconnect();
            return true;
        } catch (\Exception $e) {
            $this->error('✗ Gmail IMAP failed: ' . substr($e->getMessage(), 0, 100));
            return false;
        }
    }
}
