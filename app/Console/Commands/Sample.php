<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Webklex\PHPIMAP\ClientManager;
use TheNetworg\OAuth2\Client\Provider\Azure;
use League\OAuth2\Client\Token\AccessToken;

class Sample extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 's:fetc
                           {--user= : Email address to fetch emails for}
                           {--folder=INBOX : Folder to fetch from}
                           {--limit=50 : Number of emails to fetch}
                           {--debug : Enable debug mode}
                           {--since= : Fetch emails since date (Y-m-d format)}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch emails from Outlook 365 using IMAP with OAuth authentication';

    private $oauthProvider;
    private $imapClient;

    public function __construct()
    {
        parent::__construct();
        $this->initializeOAuthProvider();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $userEmail = $this->option('user') ?? config('mail.username');
            $folder = $this->option('folder');
            $limit = (int) $this->option('limit');
            $since = $this->option('since');

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
            $emails = $this->fetchEmails($folder, $limit, $since);

            logger($emails);


            // $this->info("Successfully fetched " . count($emails) . " emails");
            // $this->displayEmailSummary($emails);

            $this->info("Successfully fetched");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            logger()->error('Outlook email fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Initialize OAuth provider
     */
    private function initializeOAuthProvider()
    {
        $this->oauthProvider = new Azure([
            'clientId' => config('services.azure.client_id'),
            'clientSecret' => config('services.azure.client_secret'),
            'redirectUri' => config('services.azure.redirect_uri'),
            'scopes' => $this->getRequiredScopes()
        ]);
        // 'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
    }

    /**
     * Get all required scopes for full mail functionality
     */
    private function getRequiredScopes(): array
    {
        return [
            // Microsoft Graph API scopes (recommended)
            'https://graph.microsoft.com/Mail.ReadWrite',
            'https://graph.microsoft.com/Mail.Send',
            'https://graph.microsoft.com/MailboxSettings.ReadWrite',
            'https://graph.microsoft.com/User.Read',

            // Outlook-specific scopes (if needed for IMAP/SMTP)
            'https://outlook.office365.com/IMAP.AccessAsUser.All',
            'https://outlook.office365.com/SMTP.Send',
            'https://outlook.office365.com/Mail.ReadWrite',
            'https://outlook.office365.com/Mail.Read',

            // Additional useful scopes
            'offline_access', // For refresh tokens
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
            ->where('provider', 'outlook')
            ->first();

        if (!$tokenData) {
            $this->error('No OAuth token found for user. Please authenticate first.');
            $this->info('Run: php artisan outlook:authenticate --user=' . $userEmail);
            return null;
        }

        try {
            $accessToken = new AccessToken([
                'access_token' => decrypt($tokenData->access_token),
                'refresh_token' => decrypt($tokenData->refresh_token),
                'expires' => $tokenData->expires_at
            ]);

            // Log token info for debugging
            $this->info('Token expires at: ' . date('Y-m-d H:i:s', $accessToken->getExpires()));
            $this->info('Token expired: ' . ($accessToken->hasExpired() ? 'Yes' : 'No'));

            // Check if token is expired and refresh if needed
            if ($accessToken->hasExpired()) {
                $this->info('Token expired, refreshing...');
                return $this->refreshAccessToken($userEmail, $accessToken);
            }

            // Validate token by making a test Graph API call
            if (!$this->validateToken($accessToken->getToken(), $userEmail)) {
                $this->info('Token validation failed, attempting refresh...');
                return $this->refreshAccessToken($userEmail, $accessToken);
            }

            return $accessToken->getToken();
        } catch (\Exception $e) {
            $this->error('Token processing failed: ' . $e->getMessage());
            return null;
        }

        // $accessToken = new AccessToken([
        //     'access_token' => decrypt($tokenData->access_token),
        //     'refresh_token' => decrypt($tokenData->refresh_token),
        //     'expires' => $tokenData->expires_at
        // ]);

        // // Check if token is expired and refresh if needed
        // if ($accessToken->hasExpired()) {
        //     $this->info('Token expired, refreshing...');
        //     try {
        //         $newAccessToken = $this->oauthProvider->getAccessToken('refresh_token', [
        //             'refresh_token' => $accessToken->getRefreshToken()
        //         ]);

        //         // Update stored token
        //         DB::table('oauth_tokens')
        //             ->where('email', $userEmail)
        //             ->where('provider', 'outlook')
        //             ->update([
        //                 'access_token' => encrypt($newAccessToken->getToken()),
        //                 'refresh_token' => encrypt($newAccessToken->getRefreshToken()),
        //                 'expires_at' => $newAccessToken->getExpires(),
        //                 'updated_at' => now()
        //             ]);

        //         $this->info('Token refreshed successfully');
        //         return $newAccessToken->getToken();
        //     } catch (\Exception $e) {
        //         $this->error('Failed to refresh token: ' . $e->getMessage());
        //         return null;
        //     }
        // }

        // return $accessToken->getToken();
    }

    /**
     * Refresh access token
     */
    private function refreshAccessToken($userEmail, $accessToken)
    {
        try {
            $newAccessToken = $this->oauthProvider->getAccessToken('refresh_token', [
                'refresh_token' => $accessToken->getRefreshToken()
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

            $this->info('Token refreshed successfully');
            return $newAccessToken->getToken();
        } catch (\Exception $e) {
            $this->error('Failed to refresh token: ' . $e->getMessage());
            $this->info('You may need to re-authenticate. Run: php artisan outlook:authenticate --user=' . $userEmail);
            return null;
        }
    }

    /**
     * Validate token by making a test API call
     */
    private function validateToken($token, $userEmail)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get('https://graph.microsoft.com/v1.0/me');

            if ($response->successful()) {
                $userData = $response->json();
                $this->info('Token validated for user: ' . ($userData['mail'] ?? $userData['userPrincipalName']));
                return true;
            }

            $this->warn('Token validation failed: ' . $response->status());
            return false;
        } catch (\Exception $e) {
            $this->warn('Token validation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Connect to IMAP server with OAuth
     */
    private function connectToIMAP($userEmail, $accessToken)
    {
        $clientManager = new ClientManager();

        // Create SASL XOAUTH2 authentication string
        $authString = base64_encode(
            "user=" . $userEmail . "\x01auth=Bearer " . $accessToken . "\x01\x01"
        );

        $this->info('Attempting IMAP connection...');

        try {
            $this->imapClient = $clientManager->make([
                'host' => 'outlook.office365.com',
                'port' => 993,
                'encryption' => 'ssl',
                'validate_cert' => true,
                'username' => $userEmail,
                'password' => $authString,
                'protocol' => 'imap',
                'authentication' => 'oauth',
                'timeout' => 30,
                'options' => [
                    'delimiter' => '/',
                    'fetch' => \Webklex\PHPIMAP\IMAP::FT_PEEK,
                ]
            ]);

            $this->imapClient->connect();
            $this->info('Successfully connected to Outlook IMAP server');
        } catch (\Exception $e) {
            $this->error('IMAP Connection failed: ' . $e->getMessage());

            // Log additional debug info
            logger()->error('IMAP connection details', [
                'user' => $userEmail,
                'auth_string_length' => strlen($authString),
                'token_preview' => substr($accessToken, 0, 20) . '...',
                'error' => $e->getMessage()
            ]);

            throw $e;
        }


        // $this->imapClient = $clientManager->make([
        //     'host' => 'outlook.office365.com',
        //     'port' => 993,
        //     'encryption' => 'ssl',
        //     'validate_cert' => true,
        //     'username' => $userEmail,
        //     'password' => $authString,
        //     'protocol' => 'imap',
        //     'authentication' => 'oauth'
        // ]);

        // $this->imapClient->connect();
        // $this->info('Connected to Outlook IMAP server');
    }

    /**
     * Fetch emails from specified folder
     */
    private function fetchEmails($folderName, $limit, $since = null)
    {
        $folder = $this->imapClient->getFolder($folderName);

        // Build search criteria
        $query = $folder->query();

        if ($since) {
            $query->since($since);
        }

        // Fetch messages
        $messages = $query->limit($limit)->get();

        $emails = [];
        $bar = $this->output->createProgressBar(count($messages));
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
                    'message_id' => $message->getMessageId()
                ];

                $emails[] = $email;

                // Optionally store in database
                // $this->storeEmailInDatabase($email, $userEmail);
            } catch (\Exception $e) {
                $this->warn("Failed to process message UID {$message->getUid()}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $emails;
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
     * Display email summary
     */
    private function displayEmailSummary($emails)
    {
        $this->newLine();
        $this->info('Email Summary:');
        $this->line(str_repeat('-', 80));

        foreach (array_slice($emails, 0, 5) as $email) { // Show first 5
            $this->line("Subject: " . substr($email['subject'], 0, 60));
            $this->line("From: {$email['from_name']} <{$email['from']}>");
            $this->line("Date: " . $email['date']->format('Y-m-d H:i:s'));
            $this->line("Attachments: " . count($email['attachments']));
            $this->line(str_repeat('-', 80));
        }

        if (count($emails) > 5) {
            $this->info("... and " . (count($emails) - 5) . " more emails");
        }
    }

    /**
     * Add this method to test the connection step by step
     */
    private function debugConnection($userEmail, $accessToken)
    {
        $this->info('=== DEBUG CONNECTION ===');

        // Test 1: Validate token with Graph API
        $this->info('1. Testing token with Graph API...');
        $graphResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://graph.microsoft.com/v1.0/me');

        if ($graphResponse->successful()) {
            $this->info('✓ Token is valid for Graph API');
        } else {
            $this->error('✗ Token invalid for Graph API: ' . $graphResponse->status());
            return false;
        }

        // Test 2: Check IMAP settings
        $this->info('2. Testing IMAP settings...');
        $imapSettings = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://graph.microsoft.com/v1.0/me/mailboxSettings');

        if ($imapSettings->successful()) {
            $this->info('✓ Mailbox settings accessible');
        } else {
            $this->warn('⚠ Could not access mailbox settings');
        }

        // Test 3: Try IMAP connection with different auth strings
        $authStrings = [
            'standard' => base64_encode("user=" . $userEmail . "\x01auth=Bearer " . $accessToken . "\x01\x01"),
            'alternative' => base64_encode("user=" . $userEmail . "\001auth=Bearer " . $accessToken . "\001\001"),
        ];

        foreach ($authStrings as $type => $authString) {
            $this->info("3. Testing IMAP with {$type} auth string...");
            try {
                $clientManager = new ClientManager();
                $client = $clientManager->make([
                    'host' => 'outlook.office365.com',
                    'port' => 993,
                    'encryption' => 'ssl',
                    'validate_cert' => true,
                    'username' => $userEmail,
                    'password' => $authString,
                    'protocol' => 'imap',
                    'authentication' => 'oauth',
                    'timeout' => 10,
                ]);

                $client->connect();
                $this->info("✓ IMAP connection successful with {$type} format!");
                $client->disconnect();
                return $authString;
            } catch (\Exception $e) {
                $this->error("✗ IMAP failed with {$type}: " . substr($e->getMessage(), 0, 100));
            }
        }

        return false;
    }
}
