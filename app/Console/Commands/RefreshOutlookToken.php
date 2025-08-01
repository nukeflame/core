<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use TheNetworg\OAuth2\Client\Provider\Azure;

class RefreshOutlookToken extends Command
{
    protected $signature = 'outlook:refresh-token {--user= : Email address}';
    protected $description = 'Refresh Outlook OAuth token';

    public function handle()
    {
        $userEmail = $this->option('user');

        if (!$userEmail) {
            $this->error('Please provide user email with --user option');
            return Command::FAILURE;
        }

        // Get stored refresh token
        $tokens = Cache::get("outlook_tokens_{$userEmail}");

        if (!$tokens || !isset($tokens['refresh_token'])) {
            $this->error('No refresh token found. Please re-authenticate.');
            return Command::FAILURE;
        }

        $provider = new Azure([
            'clientId' => config('services.azure.client_id'),
            'clientSecret' => config('services.azure.client_secret'),
            'redirectUri' => config('services.azure.redirect_uri'),
        ]);

        try {
            $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $tokens['refresh_token']
            ]);

            // Store new tokens
            $newTokens = [
                'access_token' => $newAccessToken->getToken(),
                'refresh_token' => $newAccessToken->getRefreshToken() ?: $tokens['refresh_token'],
                'expires_at' => $newAccessToken->getExpires(),
                'scope' => implode(' ', $newAccessToken->getValues()['scope'] ?? [])
            ];

            Cache::put("outlook_tokens_{$userEmail}", $newTokens, 3600); // 1 hour

            $this->info('✅ Token refreshed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to refresh token: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
