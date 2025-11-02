<?php

namespace App\Console\Commands;

use App\Models\EmailSyncState;
use App\Models\GraphSubscription;
use App\Models\User;
use App\Services\OutlookService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TestRealtimeEmailSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:test-realtime
                            {--user= : Test specific user by ID}
                            {--create-subscription : Create a test subscription}
                            {--test-webhook : Test webhook endpoint}
                            {--fix : Attempt to fix common issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and diagnose real-time email sync setup';

    protected int $passedTests = 0;
    protected int $failedTests = 0;
    protected array $issues = [];
    protected array $recommendations = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=======================================================');
        $this->info('  Real-Time Email Sync Configuration Test');
        $this->info('=======================================================');
        $this->newLine();

        // Run all diagnostic tests
        $this->testDatabaseTables();
        $this->testEnvironmentConfig();
        $this->testLaravelServices();
        $this->testOutlookConnections();
        $this->testSubscriptions();
        $this->testWebhookEndpoint();
        $this->testBroadcastingConfig();

        if ($this->option('create-subscription')) {
            $this->testCreateSubscription();
        }

        // Display summary
        $this->displaySummary();

        return $this->failedTests === 0 ? 0 : 1;
    }

    protected function testDatabaseTables()
    {
        $this->section('Database Tables');

        $requiredTables = [
            'users',
            'oauth_tokens',
            'fetched_emails',
            'email_sync_states',
            'graph_subscriptions',
            'webhook_deliveries',
        ];

        foreach ($requiredTables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $this->pass("Table '{$table}' exists");
            } else {
                $this->fail("Table '{$table}' missing");
                $this->issues[] = "Run: php artisan migrate";
            }
        }
    }

    protected function testEnvironmentConfig()
    {
        $this->section('Environment Configuration');

        $requiredEnvVars = [
            'AZURE_CLIENT_ID' => config('services.azure.client_id'),
            'AZURE_CLIENT_SECRET' => config('services.azure.client_secret'),
            'AZURE_TENANT_ID' => config('services.azure.tenant_id'),
            'AZURE_REDIRECT_URI' => config('services.azure.redirect_uri'),
            'AZURE_WEBHOOK_URL' => config('services.azure.webhook_url'),
            'AZURE_WEBHOOK_CLIENT_STATE' => config('services.azure.webhook_client_state'),
        ];

        foreach ($requiredEnvVars as $key => $value) {
            if (!empty($value)) {
                $this->pass("{$key} is configured");
            } else {
                $this->fail("{$key} is missing");
                $this->issues[] = "Add {$key} to your .env file";
            }
        }

        // Validate webhook URL
        $webhookUrl = config('services.azure.webhook_url');
        if (!empty($webhookUrl)) {
            if (filter_var($webhookUrl, FILTER_VALIDATE_URL) && str_starts_with($webhookUrl, 'https://')) {
                $this->pass("Webhook URL is valid HTTPS");
            } else {
                $this->fail("Webhook URL must be valid HTTPS URL");
                $this->issues[] = "Webhook URL must use HTTPS (use ngrok for local testing)";
            }
        }
    }

    protected function testLaravelServices()
    {
        $this->section('Laravel Services');

        // Test Horizon
        try {
            $horizon = \Laravel\Horizon\Contracts\SupervisorRepository::class;
            if (app()->bound($horizon)) {
                $this->pass("Laravel Horizon is installed");

                // Check if Horizon is running
                $masters = app($horizon)->all();
                if (!empty($masters)) {
                    $this->pass("Horizon is running");
                } else {
                    $this->fail("Horizon is not running");
                    $this->issues[] = "Start Horizon: php artisan horizon";
                }
            } else {
                $this->fail("Horizon is not installed");
            }
        } catch (\Exception $e) {
            $this->fail("Horizon check failed: " . $e->getMessage());
        }

        // Test Reverb
        $reverbConfig = config('broadcasting.connections.reverb');
        if ($reverbConfig) {
            $this->pass("Laravel Reverb is configured");

            // Try to check if Reverb is running
            try {
                $reverbHost = $reverbConfig['options']['host'] ?? 'localhost';
                $reverbPort = $reverbConfig['options']['port'] ?? 8080;

                $socket = @fsockopen($reverbHost, $reverbPort, $errno, $errstr, 1);
                if ($socket) {
                    $this->pass("Reverb is running on {$reverbHost}:{$reverbPort}");
                    fclose($socket);
                } else {
                    $this->fail("Reverb is not accessible");
                    $this->issues[] = "Start Reverb: php artisan reverb:start";
                }
            } catch (\Exception $e) {
                $this->warn("Could not verify Reverb status");
            }
        } else {
            $this->fail("Laravel Reverb is not configured");
        }

        // Test Queue Driver
        $queueDriver = config('queue.default');
        if (in_array($queueDriver, ['database', 'redis'])) {
            $this->pass("Queue driver is '{$queueDriver}'");
        } else {
            $this->fail("Queue driver should be 'database' or 'redis', currently '{$queueDriver}'");
        }

        // Test Broadcast Driver
        $broadcastDriver = config('broadcasting.default');
        if ($broadcastDriver === 'reverb') {
            $this->pass("Broadcast driver is 'reverb'");
        } else {
            $this->fail("Broadcast driver should be 'reverb', currently '{$broadcastDriver}'");
            $this->issues[] = "Set BROADCAST_DRIVER=reverb in .env";
        }
    }

    protected function testOutlookConnections()
    {
        $this->section('Outlook Connections');

        $userId = $this->option('user');

        $query = DB::table('oauth_tokens')
            ->where('provider', 'outlook');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $tokens = $query->get();

        if ($tokens->isEmpty()) {
            $this->fail("No Outlook connections found");
            $this->issues[] = "Users need to connect their Outlook accounts";
            return;
        }

        $this->info("Found {$tokens->count()} Outlook connection(s)");

        foreach ($tokens as $token) {
            $user = User::find($token->user_id);
            if (!$user) continue;

            $expiresAt = \Carbon\Carbon::createFromTimestamp($token->expires_at);
            $isValid = $expiresAt->isFuture();

            if ($isValid) {
                $this->pass("User '{$user->email}' - Token valid until {$expiresAt->format('Y-m-d H:i')}");
            } else {
                $this->fail("User '{$user->email}' - Token expired on {$expiresAt->format('Y-m-d H:i')}");
                $this->issues[] = "User needs to reconnect Outlook account";
            }
        }
    }

    protected function testSubscriptions()
    {
        $this->section('Graph API Subscriptions');

        $subscriptions = GraphSubscription::all();

        if ($subscriptions->isEmpty()) {
            $this->warn("No Graph API subscriptions found");
            $this->recommendations[] = "Create subscriptions: php artisan emails:manage-subscriptions --create-all";
            return;
        }

        $this->info("Found {$subscriptions->count()} subscription(s)");

        foreach ($subscriptions as $subscription) {
            $status = $subscription->isActive() ? 'Active' : 'Inactive';
            $expiresIn = $subscription->expiration_date?->diffForHumans();

            if ($subscription->isActive()) {
                $this->pass("User {$subscription->user_email} - {$status}, expires {$expiresIn}");
            } else if ($subscription->isExpired()) {
                $this->fail("User {$subscription->user_email} - Expired {$expiresIn}");
                $this->issues[] = "Renew subscription: php artisan emails:manage-subscriptions --renew={$subscription->subscription_id}";
            } else {
                $this->warn("User {$subscription->user_email} - {$status}");
            }
        }
    }

    protected function testWebhookEndpoint()
    {
        $this->section('Webhook Endpoint');

        $webhookUrl = config('services.azure.webhook_url');

        if (empty($webhookUrl)) {
            $this->fail("Webhook URL not configured");
            return;
        }

        $this->info("Testing: {$webhookUrl}");

        try {
            $response = Http::timeout(5)->get($webhookUrl . '?validationToken=test-token');

            if ($response->successful()) {
                if ($response->body() === 'test-token') {
                    $this->pass("Webhook endpoint responds correctly");
                } else {
                    $this->warn("Webhook endpoint responds but validation may be incorrect");
                }
            } else {
                $this->fail("Webhook endpoint returned status: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->fail("Cannot reach webhook endpoint: " . $e->getMessage());
            $this->issues[] = "Ensure webhook URL is accessible from internet (use ngrok for local testing)";
        }
    }

    protected function testBroadcastingConfig()
    {
        $this->section('Broadcasting Configuration');

        // Check routes/channels.php
        $channelsFile = base_path('routes/channels.php');
        if (file_exists($channelsFile)) {
            $content = file_get_contents($channelsFile);
            if (str_contains($content, 'email-sync')) {
                $this->pass("Private channel 'email-sync.{id}' is defined");
            } else {
                $this->fail("Private channel 'email-sync.{id}' not found");
                $this->issues[] = "Add email-sync channel to routes/channels.php";
            }
        }

        // Check if Echo is configured in frontend
        $indexBladeFile = resource_path('views/mail/index.blade.php');
        if (file_exists($indexBladeFile)) {
            $content = file_get_contents($indexBladeFile);
            if (str_contains($content, 'window.Echo')) {
                $this->pass("Frontend Echo configuration found");
            } else {
                $this->warn("Frontend Echo configuration may be missing");
            }
        }
    }

    protected function testCreateSubscription()
    {
        $this->section('Creating Test Subscription');

        $userId = $this->option('user');
        if (!$userId) {
            $this->error("Please specify --user=ID to create a subscription");
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("User not found");
            return;
        }

        $outlookService = app(OutlookService::class);
        $result = $outlookService->createSubscription($user);

        if ($result['success'] ?? false) {
            $this->pass("Subscription created successfully!");
            $this->info("Subscription ID: " . $result['subscription_id']);
            $this->info("Expires: " . $result['expiration_date']);
        } else {
            $this->fail("Failed to create subscription: " . ($result['error'] ?? 'Unknown error'));
        }
    }

    protected function displaySummary()
    {
        $this->newLine(2);
        $this->info('=======================================================');
        $this->info('  Test Summary');
        $this->info('=======================================================');

        $totalTests = $this->passedTests + $this->failedTests;
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tests', $totalTests],
                ['Passed', "<fg=green>{$this->passedTests}</>"],
                ['Failed', $this->failedTests > 0 ? "<fg=red>{$this->failedTests}</>" : '0'],
                ['Success Rate', $totalTests > 0 ? round(($this->passedTests / $totalTests) * 100, 1) . '%' : 'N/A'],
            ]
        );

        if (!empty($this->issues)) {
            $this->newLine();
            $this->error('⚠️  Issues Found:');
            foreach ($this->issues as $issue) {
                $this->line("  • {$issue}");
            }
        }

        if (!empty($this->recommendations)) {
            $this->newLine();
            $this->warn('💡 Recommendations:');
            foreach ($this->recommendations as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }

        if ($this->failedTests === 0) {
            $this->newLine();
            $this->info('✅ All tests passed! Real-time email sync is properly configured.');
        }
    }

    protected function section(string $title)
    {
        $this->newLine();
        $this->line("━━━ {$title} " . str_repeat('━', max(0, 50 - strlen($title))));
    }

    protected function pass(string $message)
    {
        $this->line("  <fg=green>✓</> {$message}");
        $this->passedTests++;
    }

    protected function fail(string $message)
    {
        $this->line("  <fg=red>✗</> {$message}");
        $this->failedTests++;
    }
}
