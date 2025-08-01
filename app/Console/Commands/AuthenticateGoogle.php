<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\OAuth2\Client\Provider\Google;
use Illuminate\Support\Facades\Cache;

class AuthenticateGoogle extends Command
{
    protected $signature = 'google:authenticate {--user= : Email address to authenticate} {--interactive : Enable interactive token retrieval}';
    protected $description = 'Authenticate with Google OAuth for Gmail and other services';

    public function handle()
    {
        $userEmail = $this->option('user');

        if (!$userEmail) {
            $this->error('Please provide user email with --user option');
            return Command::FAILURE;
        }

        if (!$this->validateConfig()) {
            return Command::FAILURE;
        }

        $provider = new Google([
            'clientId' => config('services.google.client_id'),
            'clientSecret' => config('services.google.client_secret'),
            'redirectUri' => config('services.google.redirect_uri'),
        ]);

        $state = bin2hex(random_bytes(16));
        Cache::put("oauth_state_{$userEmail}", $state, 600); // 10 minutes

        $authUrl = $provider->getAuthorizationUrl(
            [
                'state' => $state,
                'login_hint' => $userEmail,
                // 'scope' => implode(' ', $this->getRequiredScopes()),
                'access_type' => 'offline', // Required for refresh tokens
                // 'prompt' => 'consent',
                'scope' => [
                    // Gmail API scopes
                    'https://www.googleapis.com/auth/gmail.readonly',
                    'https://www.googleapis.com/auth/gmail.modify',
                    'https://www.googleapis.com/auth/gmail.compose',
                    'https://www.googleapis.com/auth/gmail.send',
                    'https://www.googleapis.com/auth/gmail.labels',
                    'https://www.googleapis.com/auth/gmail.settings.basic',
                    'https://www.googleapis.com/auth/gmail.settings.sharing',

                    // Google Drive API (if needed)
                    'https://www.googleapis.com/auth/drive.file',

                    // Google Calendar API (if needed)
                    'https://www.googleapis.com/auth/calendar.readonly',

                    // Basic profile information
                    'https://www.googleapis.com/auth/userinfo.email',
                    'https://www.googleapis.com/auth/userinfo.profile',
                    'openid',

                    // For IMAP/SMTP access (if using protocols directly)
                    'https://mail.google.com/',
                ]
            ]
        );

        $this->displayAuthenticationInstructions($authUrl);

        if ($this->option('interactive')) {
            return $this->handleInteractiveAuth($provider, $userEmail, $state);
        }

        $this->info('Please visit this URL to authenticate:');

        return Command::SUCCESS;
    }

    /**
     * Get all required scopes for full Gmail and Google services functionality
     */
    private function getRequiredScopes(): array
    {
        return [
            // Gmail API scopes
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/gmail.modify',
            'https://www.googleapis.com/auth/gmail.compose',
            'https://www.googleapis.com/auth/gmail.send',
            'https://www.googleapis.com/auth/gmail.labels',
            'https://www.googleapis.com/auth/gmail.settings.basic',
            'https://www.googleapis.com/auth/gmail.settings.sharing',

            // Google Drive API (if needed)
            'https://www.googleapis.com/auth/drive.file',

            // Google Calendar API (if needed)
            'https://www.googleapis.com/auth/calendar.readonly',

            // Basic profile information
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'openid',

            // For IMAP/SMTP access (if using protocols directly)
            'https://mail.google.com/',
        ];
    }

    /**
     * Display authentication instructions
     */
    private function displayAuthenticationInstructions(string $authUrl): void
    {
        $this->newLine();
        $this->info('=== GOOGLE OAUTH AUTHENTICATION ===');
        $this->newLine();
        $this->info('Please visit this URL to authenticate:');
        $this->line($authUrl);
        $this->newLine();
        $this->warn('IMPORTANT: Make sure to grant ALL requested permissions for full Gmail functionality!');
        $this->newLine();
        $this->info('Required permissions include:');
        $this->line('• Read, modify, compose, and send Gmail messages');
        $this->line('• Manage Gmail labels and settings');
        $this->line('• Access basic profile information');
        $this->line('• IMAP/SMTP access (if using protocols directly)');
        $this->line('• Optional: Google Drive and Calendar access');
        $this->newLine();
        $this->info('After authentication, you will be redirected to your callback URL.');
        $this->info('Make sure your callback URL handler processes and stores the OAuth tokens.');
        $this->newLine();
        $this->comment('Note: Google requires "offline" access type and "consent" prompt for refresh tokens.');
        $this->newLine();
    }

    /**
     * Handle interactive authentication (if callback handling is implemented)
     */
    private function handleInteractiveAuth(Google $provider, string $userEmail, string $state): int
    {
        $this->info('Waiting for authentication callback...');
        $this->info('Press Ctrl+C to cancel if authentication fails.');

        // Poll for token (this would need to be implemented based on your callback handling)
        $maxAttempts = 60; // 5 minutes
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            sleep(5);
            $attempts++;

            // Check if tokens were stored (implement based on your storage method)
            $tokens = $this->checkForStoredTokens($userEmail);

            if ($tokens) {
                $this->info('✅ Authentication successful!');
                $this->displayTokenInfo($tokens);
                return Command::SUCCESS;
            }

            $this->line('.');
        }

        $this->error('Authentication timeout. Please try again.');
        return Command::FAILURE;
    }

    /**
     * Check for stored tokens (implement based on your storage method)
     */
    private function checkForStoredTokens(string $userEmail): ?array
    {
        // This should be implemented based on how you store tokens
        // Example using cache:
        return Cache::get("google_tokens_{$userEmail}");
    }

    /**
     * Display token information
     */
    private function displayTokenInfo(array $tokens): void
    {
        $this->newLine();
        $this->info('Token Information:');
        $this->table(
            ['Property', 'Value'],
            [
                ['Access Token (first 20 chars)', substr($tokens['access_token'] ?? 'N/A', 0, 20) . '...'],
                ['Refresh Token Available', isset($tokens['refresh_token']) ? 'Yes' : 'No'],
                ['Expires At', isset($tokens['expires']) ? date('Y-m-d H:i:s', $tokens['expires']) : 'N/A'],
                ['Token Type', $tokens['token_type'] ?? 'Bearer'],
                ['Scope', $tokens['scope'] ?? 'N/A'],
            ]
        );
        $this->newLine();
        $this->info('You can now use these tokens for Gmail and Google services operations.');

        if (!isset($tokens['refresh_token'])) {
            $this->newLine();
            $this->warn('⚠️  No refresh token received!');
            $this->warn('This might happen if the user has already granted permissions.');
            $this->warn('To force a refresh token, revoke access in Google Account settings and re-authenticate.');
        }
    }

    /**
     * Validate Google configuration
     */
    private function validateConfig(): bool
    {
        $requiredConfigs = [
            'services.google.client_id' => 'Google Client ID',
            'services.google.client_secret' => 'Google Client Secret',
            'services.google.redirect_uri' => 'Google Redirect URI',
        ];

        foreach ($requiredConfigs as $config => $name) {
            if (!config($config)) {
                $this->error("Missing configuration: {$name} ({$config})");
                return false;
            }
        }

        $redirectUri = config('services.google.redirect_uri');
        if (!filter_var($redirectUri, FILTER_VALIDATE_URL)) {
            $this->error("Invalid redirect URI format: {$redirectUri}");
            return false;
        }

        // Additional Google-specific validations
        $clientId = config('services.google.client_id');
        if (!str_ends_with($clientId, '.apps.googleusercontent.com')) {
            $this->warn("Google Client ID should end with '.apps.googleusercontent.com'");
        }

        return true;
    }
}
