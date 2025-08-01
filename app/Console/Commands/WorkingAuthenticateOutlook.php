<?php

namespace App\Console\Commands;

use App\Helpers\PkceHelper;
use App\Services\AzureGraphService;
use Illuminate\Console\Command;

class WorkingAuthenticateOutlook extends Command
{
    protected $signature = 'working-outlook:authenticate {--user= : Email address to authenticate} {--interactive : Enable interactive token retrieval}';
    protected $description = 'Authenticate with Outlook 365 OAuth';

    public function handle()
    {
        $userEmail = $this->option('user');

        if (!$userEmail) {
            $this->error('Please provide user email with --user option');
            return Command::FAILURE;
        }


        try {
            $azureService = new AzureGraphService();

            // Generate a unique state for security
            $state = bin2hex(random_bytes(16));

            // Store state in cache for verification (optional but recommended)
            cache()->put("azure_auth_state_{$state}", true, now()->addMinutes(10));

            // Get authorization URL
            $authUrl = $azureService->getAuthorizationUrl($state);

            $this->info('Azure Authentication Setup');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            $this->info('Configuration:');
            $this->line("Client ID: " . config('services.azure.client_id'));
            $this->line("Redirect URI: " . config('services.azure.redirect_uri'));
            $this->line("Tenant ID: " . config('services.azure.tenant_id', 'common'));

            $this->line('');
            $this->info('Authorization URL Generated:');
            $this->line($authUrl);

            $this->line('');
            $this->warn('Next Steps:');
            $this->line('1. Copy the URL above and open it in your browser');
            $this->line('2. Sign in with your Microsoft account');
            $this->line('3. Grant the requested permissions');
            $this->line('4. You will be redirected to your callback URL');
            $this->line('5. The callback will handle the authorization code');

            $this->line('');
            $this->info("State Token (for verification): {$state}");
        } catch (\Exception $e) {
            $this->error('Authentication setup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
