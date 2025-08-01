<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OutlookAuthenticate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'outlook:authenticate
                           {--client-id= : Azure AD Application (Client) ID}
                           {--client-secret= : Azure AD Application Client Secret}
                           {--tenant-id= : Azure AD Tenant ID (optional, defaults to common)}
                           {--redirect-uri=http://localhost:8000/auth/outlook/callback : Redirect URI}
                           {--scopes= : Comma-separated list of scopes (optional, uses defaults)}
                           {--auth-type=delegated : Authentication type (delegated or application)}
                           {--save-config : Save authentication configuration to .env}
                           {--token-file=storage/app/outlook_token.json : Path to save token}
                           {--open-browser : Automatically open browser for authentication}
                           {--validate : Validate existing saved token}
                           {--refresh : Refresh existing token}
                           {--revoke : Revoke existing token}
                           {--show-permissions : Show required permissions for each scope}';

    /**
     * The console command description.
     */
    protected $description = 'Authenticate with Microsoft Graph API for Outlook access';

    private array $requiredScopes = [
        'delegated' => [
            'https://graph.microsoft.com/Mail.Read',
            'https://graph.microsoft.com/User.Read',
            'https://graph.microsoft.com/User.ReadBasic.All',
            'https://graph.microsoft.com/ProfilePhoto.Read.All',
            'offline_access'
        ],
        'application' => [
            'https://graph.microsoft.com/Mail.Read',
            'https://graph.microsoft.com/User.Read.All',
            'https://graph.microsoft.com/ProfilePhoto.Read.All'
        ]
    ];

    private string $authEndpoint = 'https://login.microsoftonline.com';
    private string $graphEndpoint = 'https://graph.microsoft.com/v1.0';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if ($this->option('show-permissions')) {
                return $this->showPermissions();
            }

            if ($this->option('validate')) {
                return $this->validateToken();
            }

            if ($this->option('refresh')) {
                return $this->refreshToken();
            }

            if ($this->option('revoke')) {
                return $this->revokeToken();
            }

            return $this->authenticate();
        } catch (\Exception $e) {
            $this->error('Authentication failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Main authentication flow
     */
    private function authenticate(): int
    {
        $this->info('=== Microsoft Graph Authentication ===');

        // Get configuration
        $config = $this->getAuthConfig();
        if (!$config) {
            return Command::FAILURE;
        }

        $authType = $this->option('auth-type');

        if ($authType === 'application') {
            return $this->authenticateApplication($config);
        } else {
            return $this->authenticateDelegated($config);
        }
    }

    /**
     * Get authentication configuration
     */
    private function getAuthConfig(): ?array
    {
        $clientId = $this->option('client-id') ?: env('AZURE_CLIENT_ID');
        $clientSecret = $this->option('client-secret') ?: env('AZURE_CLIENT_SECRET');
        $tenantId = $this->option('tenant-id') ?: env('AZURE_TENANT_ID', 'common');

        if (!$clientId) {
            $clientId = $this->ask('Enter your Azure AD Application (Client) ID');
        }

        if (!$clientSecret) {
            $clientSecret = $this->secret('Enter your Azure AD Application Client Secret');
        }

        if (!$clientId || !$clientSecret) {
            $this->error('Client ID and Client Secret are required');
            return null;
        }

        $config = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'tenant_id' => $tenantId,
            'redirect_uri' => $this->option('redirect-uri'),
            'scopes' => $this->getScopes()
        ];

        if ($this->option('save-config')) {
            $this->saveConfiguration($config);
        }

        return $config;
    }

    /**
     * Get scopes for authentication
     */
    private function getScopes(): array
    {
        $authType = $this->option('auth-type');
        $customScopes = $this->option('scopes');

        if ($customScopes) {
            return explode(',', $customScopes);
        }

        return $this->requiredScopes[$authType] ?? $this->requiredScopes['delegated'];
    }

    /**
     * Application (Client Credentials) authentication flow
     */
    private function authenticateApplication(array $config): int
    {
        $this->info('Using Application (Client Credentials) authentication...');

        $tokenUrl = "{$this->authEndpoint}/{$config['tenant_id']}/oauth2/v2.0/token";

        $response = Http::asForm()->post($tokenUrl, [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'scope' => implode(' ', $config['scopes']),
            'grant_type' => 'client_credentials'
        ]);

        if (!$response->successful()) {
            $this->error('Failed to obtain access token: ' . $response->body());
            return Command::FAILURE;
        }

        $tokenData = $response->json();

        $token = [
            'access_token' => $tokenData['access_token'],
            'token_type' => $tokenData['token_type'],
            'expires_in' => $tokenData['expires_in'],
            'expires_at' => now()->addSeconds($tokenData['expires_in'])->toISOString(),
            'scope' => $tokenData['scope'] ?? implode(' ', $config['scopes']),
            'auth_type' => 'application',
            'tenant_id' => $config['tenant_id'],
            'created_at' => now()->toISOString()
        ];

        $this->saveToken($token);
        $this->info('✓ Application authentication successful!');
        $this->showTokenInfo($token);

        return Command::SUCCESS;
    }

    /**
     * Delegated (Authorization Code) authentication flow
     */
    private function authenticateDelegated(array $config): int
    {
        $this->info('Using Delegated (Authorization Code) authentication...');

        // Step 1: Generate authorization URL
        $state = Str::random(32);
        $codeVerifier = $this->generateCodeVerifier();
        $codeChallenge = $this->generateCodeChallenge($codeVerifier);

        $authParams = [
            'client_id' => $config['client_id'],
            'response_type' => 'code',
            'redirect_uri' => $config['redirect_uri'],
            'scope' => implode(' ', $config['scopes']),
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'response_mode' => 'query'
        ];

        $authUrl = "{$this->authEndpoint}/{$config['tenant_id']}/oauth2/v2.0/authorize?" . http_build_query($authParams);

        $this->info('=== AUTHORIZATION REQUIRED ===');
        $this->line('Please visit the following URL to authorize the application:');
        $this->line('');
        $this->line($authUrl);
        $this->line('');

        if ($this->option('open-browser')) {
            $this->openBrowser($authUrl);
        }

        // Step 2: Get authorization code
        $authCode = $this->ask('Enter the authorization code from the callback URL');
        $receivedState = $this->ask('Enter the state parameter from the callback URL (optional)', $state);

        if ($receivedState !== $state) {
            $this->warn('State parameter mismatch. Continuing anyway...');
        }

        // Step 3: Exchange code for token
        $tokenUrl = "{$this->authEndpoint}/{$config['tenant_id']}/oauth2/v2.0/token";

        $response = Http::asForm()->post($tokenUrl, [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $authCode,
            'redirect_uri' => $config['redirect_uri'],
            'grant_type' => 'authorization_code',
            'code_verifier' => $codeVerifier
        ]);

        if (!$response->successful()) {
            $this->error('Failed to exchange authorization code for token: ' . $response->body());
            return Command::FAILURE;
        }

        $tokenData = $response->json();

        $token = [
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? null,
            'token_type' => $tokenData['token_type'],
            'expires_in' => $tokenData['expires_in'],
            'expires_at' => now()->addSeconds($tokenData['expires_in'])->toISOString(),
            'scope' => $tokenData['scope'],
            'auth_type' => 'delegated',
            'tenant_id' => $config['tenant_id'],
            'created_at' => now()->toISOString()
        ];

        $this->saveToken($token);
        $this->info('✓ Delegated authentication successful!');
        $this->showTokenInfo($token);

        return Command::SUCCESS;
    }

    /**
     * Validate existing token
     */
    private function validateToken(): int
    {
        $token = $this->loadToken();
        if (!$token) {
            $this->error('No saved token found. Run authentication first.');
            return Command::FAILURE;
        }

        $this->info('=== TOKEN VALIDATION ===');

        // Check expiration
        $expiresAt = Carbon::parse($token['expires_at']);
        $isExpired = $expiresAt->isPast();

        if ($isExpired) {
            $this->warn('Token is expired');
            if (isset($token['refresh_token'])) {
                $this->info('Refresh token is available for renewal');
            }
        } else {
            $this->info('Token is valid (expires: ' . $expiresAt->format('Y-m-d H:i:s') . ')');
        }

        // Test API call
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token['access_token'],
        ])->timeout(10)->get($this->graphEndpoint . '/me');

        if ($response->successful()) {
            $userInfo = $response->json();
            $this->info('✓ API validation successful');
            $this->line('User: ' . ($userInfo['displayName'] ?? 'N/A'));
            $this->line('Email: ' . ($userInfo['mail'] ?? $userInfo['userPrincipalName'] ?? 'N/A'));
        } else {
            $this->error('✗ API validation failed: HTTP ' . $response->status());
            if ($response->status() === 401) {
                $this->warn('Token is invalid or expired');
            }
        }

        $this->showTokenInfo($token);

        return $response->successful() && !$isExpired ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Refresh existing token
     */
    private function refreshToken(): int
    {
        $token = $this->loadToken();
        if (!$token) {
            $this->error('No saved token found');
            return Command::FAILURE;
        }

        if (!isset($token['refresh_token'])) {
            $this->error('No refresh token available. Re-authentication required.');
            return Command::FAILURE;
        }

        $this->info('Refreshing access token...');

        $config = $this->getStoredConfig();
        if (!$config) {
            $this->error('Configuration not found. Please re-authenticate.');
            return Command::FAILURE;
        }

        $tokenUrl = "{$this->authEndpoint}/{$token['tenant_id']}/oauth2/v2.0/token";

        $response = Http::asForm()->post($tokenUrl, [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'refresh_token' => $token['refresh_token'],
            'grant_type' => 'refresh_token'
        ]);

        if (!$response->successful()) {
            $this->error('Failed to refresh token: ' . $response->body());
            return Command::FAILURE;
        }

        $tokenData = $response->json();

        $newToken = array_merge($token, [
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? $token['refresh_token'],
            'expires_in' => $tokenData['expires_in'],
            'expires_at' => now()->addSeconds($tokenData['expires_in'])->toISOString(),
            'scope' => $tokenData['scope'] ?? $token['scope'],
            'refreshed_at' => now()->toISOString()
        ]);

        $this->saveToken($newToken);
        $this->info('✓ Token refreshed successfully!');
        $this->showTokenInfo($newToken);

        return Command::SUCCESS;
    }

    /**
     * Revoke existing token
     */
    private function revokeToken(): int
    {
        $token = $this->loadToken();
        if (!$token) {
            $this->error('No saved token found');
            return Command::FAILURE;
        }

        if ($this->confirm('Are you sure you want to revoke the current token?')) {
            // Delete token file
            $tokenFile = $this->option('token-file');
            if (file_exists($tokenFile)) {
                unlink($tokenFile);
            }

            $this->info('✓ Token revoked and removed');
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }

    /**
     * Show required permissions
     */
    private function showPermissions(): int
    {
        $this->info('=== REQUIRED MICROSOFT GRAPH PERMISSIONS ===');

        $this->line('');
        $this->info('DELEGATED PERMISSIONS (User Context):');
        foreach ($this->requiredScopes['delegated'] as $scope) {
            $this->line("  - {$scope}");
        }

        $this->line('');
        $this->info('APPLICATION PERMISSIONS (App-Only):');
        foreach ($this->requiredScopes['application'] as $scope) {
            $this->line("  - {$scope}");
        }

        $this->line('');
        $this->info('PERMISSION DESCRIPTIONS:');
        $this->table(['Permission', 'Description'], [
            ['Mail.Read', 'Read user mail / Read mail in all mailboxes'],
            ['User.Read', 'Read signed-in user profile'],
            ['User.ReadBasic.All', 'Read all users\' basic profiles (includes photos)'],
            ['User.Read.All', 'Read all users\' full profiles'],
            ['ProfilePhoto.Read.All', 'Read all profile photos'],
            ['offline_access', 'Maintain access to data (refresh tokens)']
        ]);

        $this->line('');
        $this->warn('APPLICATION PERMISSIONS require administrator consent!');

        return Command::SUCCESS;
    }

    /**
     * Save authentication configuration
     */
    private function saveConfiguration(array $config): void
    {
        $envFile = base_path('.env');
        $envContent = file_exists($envFile) ? file_get_contents($envFile) : '';

        $updates = [
            'AZURE_CLIENT_ID' => $config['client_id'],
            'AZURE_CLIENT_SECRET' => $config['client_secret'],
            'AZURE_TENANT_ID' => $config['tenant_id'],
            'AZURE_REDIRECT_URI' => $config['redirect_uri']
        ];

        foreach ($updates as $key => $value) {
            if (str_contains($envContent, $key . '=')) {
                $envContent = preg_replace('/^' . $key . '=.*$/m', $key . '=' . $value, $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);
        $this->info('✓ Configuration saved to .env file');
    }

    /**
     * Get stored configuration
     */
    private function getStoredConfig(): ?array
    {
        return [
            'client_id' => env('AZURE_CLIENT_ID'),
            'client_secret' => env('AZURE_CLIENT_SECRET'),
            'tenant_id' => env('AZURE_TENANT_ID', 'common'),
            'redirect_uri' => env('AZURE_REDIRECT_URI', 'http://localhost:8000/auth/outlook/callback')
        ];
    }

    /**
     * Save token to file
     */
    private function saveToken(array $token): void
    {
        $tokenFile = $this->option('token-file');
        $directory = dirname($tokenFile);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($tokenFile, json_encode($token, JSON_PRETTY_PRINT));
        $this->line("Token saved to: {$tokenFile}");
    }

    /**
     * Load token from file
     */
    private function loadToken(): ?array
    {
        $tokenFile = $this->option('token-file');

        if (!file_exists($tokenFile)) {
            return null;
        }

        $content = file_get_contents($tokenFile);
        return json_decode($content, true);
    }

    /**
     * Show token information
     */
    private function showTokenInfo(array $token): void
    {
        $this->info('=== TOKEN INFORMATION ===');
        $this->table(['Property', 'Value'], [
            ['Type', $token['auth_type']],
            ['Expires At', $token['expires_at']],
            ['Scope', $token['scope']],
            ['Has Refresh Token', isset($token['refresh_token']) ? 'Yes' : 'No'],
            ['Tenant ID', $token['tenant_id']],
            ['Token Preview', substr($token['access_token'], 0, 20) . '...']
        ]);
    }

    /**
     * Generate PKCE code verifier
     */
    private function generateCodeVerifier(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * Generate PKCE code challenge
     */
    private function generateCodeChallenge(string $codeVerifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
    }

    /**
     * Open browser (platform-specific)
     */
    private function openBrowser(string $url): void
    {
        $os = PHP_OS_FAMILY;

        try {
            switch ($os) {
                case 'Windows':
                    exec("start {$url}");
                    break;
                case 'Darwin': // macOS
                    exec("open {$url}");
                    break;
                case 'Linux':
                    exec("xdg-open {$url}");
                    break;
                default:
                    $this->warn('Cannot automatically open browser on this platform');
                    return;
            }
            $this->info('Browser opened automatically');
        } catch (\Exception $e) {
            $this->warn('Failed to open browser automatically: ' . $e->getMessage());
        }
    }
}
