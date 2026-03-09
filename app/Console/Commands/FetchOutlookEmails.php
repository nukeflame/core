<?php

namespace App\Console\Commands;

use Nukeflame\Core\Services\OutlookService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FetchOutlookEmails extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'outlook:fetch-emails
                           {--user= : Email address to fetch emails for}
                           {--folder=inbox : Folder to fetch from (inbox, sent, drafts, etc.)}
                           {--limit=50 : Number of emails to fetch (max 999)}
                           {--since= : Fetch emails since date (Y-m-d format)}
                           {--debug : Enable debug mode}
                           {--verbose : Enable verbose output}
                           {--show-folders : List available folders}
                           {--show-profile : Show user profile information}
                           {--fetch-attachments : Download and store email attachments}
                           {--attachment-path=attachments : Path to store attachments (relative to storage)}
                           {--max-attachment-size=10 : Maximum attachment size in MB (default: 10MB)}
                           {--allowed-extensions=pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif : Allowed file extensions}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch emails from Outlook 365 using Microsoft Graph API with attachment support';

    private OutlookService $outlookService;
    private array $debugLog = [];
    private int $totalAttachments = 0;
    private int $downloadedAttachments = 0;
    private int $skippedAttachments = 0;
    private array $attachmentErrors = [];

    public function __construct(OutlookService $outlookService)
    {
        parent::__construct();
        $this->outlookService = $outlookService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startTime = microtime(true);
        $this->debugLog('Command started', ['timestamp' => now()->toISOString()]);

        try {
            $userEmail = $this->getUserEmail();
            if (!$userEmail) {
                $this->debugLog('Failed to get user email');
                return Command::FAILURE;
            }

            $this->debugLog('User email obtained', ['email' => $userEmail]);

            if (!$this->validateOptions()) {
                return Command::FAILURE;
            }

            if ($this->option('show-profile')) {
                $this->showUserProfile($userEmail);
            }

            if ($this->option('show-folders')) {
                $this->showMailFolders($userEmail);
            }

            if ($this->option('show-profile') || $this->option('show-folders')) {
                return Command::SUCCESS;
            }

            if (!$this->testConnection($userEmail)) {
                return Command::FAILURE;
            }

            $result = $this->fetchEmails($userEmail);

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            $this->info("Command completed in {$executionTime} seconds");
            $this->debugLog('Command completed', [
                'execution_time' => $executionTime,
                'result' => $result === Command::SUCCESS ? 'success' : 'failure'
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->handleException($e);
            return Command::FAILURE;
        }
    }

    /**
     * Get user email from option or config
     */
    private function getUserEmail(): ?string
    {
        $userEmail = $this->option('user') ?? config('mail.username');

        if (!$userEmail) {
            $this->error('Please provide a user email address using --user option');
            $this->info('Example: php artisan outlook:fetch-emails --user=user@company.com');
            return null;
        }

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email format: ' . $userEmail);
            return null;
        }

        return $userEmail;
    }

    /**
     * Validate command options
     */
    private function validateOptions(): bool
    {
        $limit = (int) $this->option('limit');
        if ($limit < 1 || $limit > 999) {
            $this->error('Limit must be between 1 and 999');
            return false;
        }

        $since = $this->option('since');
        if ($since && !$this->isValidDate($since)) {
            $this->error('Invalid date format for --since. Use Y-m-d format (e.g., 2024-01-15)');
            return false;
        }

        $maxSize = (float) $this->option('max-attachment-size');
        if ($maxSize <= 0 || $maxSize > 100) {
            $this->error('Max attachment size must be between 0.1 and 100 MB');
            return false;
        }

        if ($this->option('fetch-attachments')) {
            $attachmentPath = $this->option('attachment-path');
            if (!$attachmentPath || strlen($attachmentPath) === 0) {
                $this->error('Attachment path cannot be empty when fetching attachments');
                return false;
            }
        }

        return true;
    }

    /**
     * Test connection to Outlook service
     */
    private function testConnection(string $userEmail): bool
    {
        $this->info('Testing connection to Outlook service...');
        $this->debugLog('Testing connection', ['user' => $userEmail]);

        try {
            $profile = $this->outlookService->getUserProfile($userEmail);
            if (!$profile) {
                $this->error('Failed to connect to Outlook service. Authentication may be required.');
                $this->info('Try running: php artisan outlook:authenticate --user=' . $userEmail);
                return false;
            }

            $this->info('✓ Connection successful');
            $this->debugLog('Connection test passed', ['profile_id' => $profile['id'] ?? 'unknown']);
            return true;
        } catch (\Exception $e) {
            $this->error('Connection test failed: ' . $e->getMessage());
            $this->debugLog('Connection test failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Show user profile information
     */
    private function showUserProfile(string $userEmail): void
    {
        $this->info("Fetching user profile for: {$userEmail}");
        $this->debugLog('Fetching user profile', ['user' => $userEmail]);

        try {
            $profile = $this->outlookService->getUserProfile($userEmail);

            if (!$profile) {
                $this->error('Failed to fetch user profile. Please check authentication.');
                return;
            }

            $this->info('=== USER PROFILE ===');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Display Name', $profile['displayName'] ?? 'N/A'],
                    ['Email', $profile['mail'] ?? $profile['userPrincipalName'] ?? 'N/A'],
                    ['Job Title', $profile['jobTitle'] ?? 'N/A'],
                    ['Department', $profile['department'] ?? 'N/A'],
                    ['Office Location', $profile['officeLocation'] ?? 'N/A'],
                    ['Mobile Phone', $profile['mobilePhone'] ?? 'N/A'],
                    ['Business Phone', $profile['businessPhones'][0] ?? 'N/A'],
                    ['User ID', $profile['id'] ?? 'N/A'],
                    ['User Type', $profile['userType'] ?? 'N/A'],
                ]
            );

            $this->debugLog('User profile fetched successfully', ['user_id' => $profile['id'] ?? 'unknown']);
        } catch (\Exception $e) {
            $this->error('Failed to fetch user profile: ' . $e->getMessage());
            $this->debugLog('Failed to fetch user profile', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Show available mail folders
     */
    private function showMailFolders(string $userEmail): void
    {
        $this->info("Fetching mail folders for: {$userEmail}");
        $this->debugLog('Fetching mail folders', ['user' => $userEmail]);

        try {
            $folders = $this->outlookService->getMailFolders($userEmail);

            if (empty($folders)) {
                $this->warn('No folders found or failed to fetch folders.');
                $this->debugLog('No folders found');
                return;
            }

            $this->info('=== MAIL FOLDERS ===');
            $folderData = array_map(function ($folder) {
                return [
                    $folder['display_name'] ?? 'Unknown',
                    substr($folder['id'] ?? '', 0, 20) . '...',
                    $folder['total_item_count'] ?? 0,
                    $folder['unread_item_count'] ?? 0,
                    $folder['child_folder_count'] ?? 0
                ];
            }, $folders);

            $this->table(
                ['Display Name', 'ID', 'Total Items', 'Unread Items', 'Child Folders'],
                $folderData
            );

            $this->debugLog('Mail folders fetched successfully', ['folder_count' => count($folders)]);
        } catch (\Exception $e) {
            $this->error('Failed to fetch mail folders: ' . $e->getMessage());
            $this->debugLog('Failed to fetch mail folders', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fetch emails from Outlook
     */
    private function fetchEmails(string $userEmail): int
    {
        $folder = $this->option('folder');
        $limit = min((int) $this->option('limit'), 999);
        $since = $this->option('since') ?? now()->subDay()->format('Y-m-d');
        $fetchAttachments = $this->option('fetch-attachments');

        $this->info("Starting email fetch for: {$userEmail}");
        $this->info("Folder: {$folder}, Limit: {$limit}, Since: {$since}");

        if ($fetchAttachments) {
            $this->info("Attachment fetching: ENABLED");
            $this->info("Attachment path: " . $this->option('attachment-path'));
            $this->info("Max attachment size: " . $this->option('max-attachment-size') . "MB");
        }

        $this->debugLog('Starting email fetch', [
            'user' => $userEmail,
            'folder' => $folder,
            'limit' => $limit,
            'since' => $since,
            'fetch_attachments' => $fetchAttachments
        ]);

        try {
            $options = [
                'folder' => $folder,
                'limit' => $limit,
                'since' => $since,
            ];

            $progressBar = $this->output->createProgressBar($limit);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

            $emails = $this->outlookService->fetchEmails($userEmail, $options);
            $progressBar->advance(count($emails));
            $progressBar->finish();
            $this->newLine();

            $this->info("Successfully fetched " . count($emails) . " emails");
            $this->debugLog('Emails fetched successfully', ['count' => count($emails)]);

            if ($fetchAttachments && count($emails) > 0) {
                $this->processAttachments($userEmail, $emails);
            }

            if ($this->option('debug') && !empty($emails)) {
                $this->showEmailSummary($emails);
            }

            if (count($emails) > 0) {
                $this->showSampleEmails($emails, min(3, count($emails)));
            }

            if ($fetchAttachments) {
                $this->showAttachmentSummary();
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to fetch emails: ' . $e->getMessage());
            $this->debugLog('Failed to fetch emails', ['error' => $e->getMessage()]);
            $this->showTroubleshootingTips($userEmail);
            return Command::FAILURE;
        }
    }

    /**
     * Process email attachments
     */
    private function processAttachments(string $userEmail, array $emails): void
    {
        $this->info('Processing email attachments...');

        $emailsWithAttachments = array_filter($emails, fn($email) => $email['has_attachments'] ?? false);
        $this->totalAttachments = 0;

        if (empty($emailsWithAttachments)) {
            $this->info('No emails with attachments found.');
            return;
        }

        $this->info(count($emailsWithAttachments) . ' emails have attachments');

        $attachmentProgressBar = $this->output->createProgressBar(count($emailsWithAttachments));
        $attachmentProgressBar->setFormat('Processing attachments: %current%/%max% [%bar%] %percent:3s%%');

        foreach ($emailsWithAttachments as $email) {
            try {
                $this->processEmailAttachments($userEmail, $email);
                $attachmentProgressBar->advance();
            } catch (\Exception $e) {
                $this->attachmentErrors[] = [
                    'email_subject' => $email['subject'] ?? 'Unknown',
                    'error' => $e->getMessage()
                ];
                $this->debugLog('Failed to process attachments for email', [
                    'email_id' => $email['id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }

        $attachmentProgressBar->finish();
        $this->newLine();
    }

    /**
     * Process attachments for a single email
     */
    private function processEmailAttachments(string $userEmail, array $email): void
    {
        $emailId = $email['id'] ?? null;
        if (!$emailId) {
            throw new \Exception('Email ID not found');
        }

        $attachments = $this->outlookService->getEmailAttachments($userEmail, $emailId);

        if (empty($attachments)) {
            return;
        }

        $this->totalAttachments += count($attachments);
        $allowedExtensions = array_map('trim', explode(',', $this->option('allowed-extensions')));
        $maxSizeBytes = (float) $this->option('max-attachment-size') * 1024 * 1024;

        foreach ($attachments as $attachment) {
            try {
                if (!$this->shouldDownloadAttachment($attachment, $allowedExtensions, $maxSizeBytes)) {
                    $this->skippedAttachments++;
                    continue;
                }

                $this->downloadAttachment($userEmail, $emailId, $attachment, $email);
                $this->downloadedAttachments++;
            } catch (\Exception $e) {
                $this->attachmentErrors[] = [
                    'email_subject' => $email['subject'] ?? 'Unknown',
                    'attachment_name' => $attachment['name'] ?? 'Unknown',
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    /**
     * Check if attachment should be downloaded
     */
    private function shouldDownloadAttachment(array $attachment, array $allowedExtensions, float $maxSizeBytes): bool
    {
        $fileName = $attachment['name'] ?? '';
        $fileSize = $attachment['size'] ?? 0;

        // Check file size
        if ($fileSize > $maxSizeBytes) {
            $this->debugLog('Attachment skipped - too large', [
                'name' => $fileName,
                'size' => $fileSize,
                'max_size' => $maxSizeBytes
            ]);
            return false;
        }

        // Check file extension
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            $this->debugLog('Attachment skipped - extension not allowed', [
                'name' => $fileName,
                'extension' => $extension,
                'allowed' => $allowedExtensions
            ]);
            return false;
        }

        return true;
    }

    /**
     * Download and store attachment
     */
    private function downloadAttachment(string $userEmail, string $emailId, array $attachment, array $email): void
    {
        $attachmentId = $attachment['id'] ?? null;
        $fileName = $attachment['name'] ?? 'unknown_file';

        if (!$attachmentId) {
            throw new \Exception('Attachment ID not found');
        }

        $content = $this->outlookService->downloadAttachment($userEmail, $emailId, $attachmentId);

        if (!$content) {
            throw new \Exception('Failed to download attachment content');
        }

        // Create directory structure
        $basePath = $this->option('attachment-path');
        $emailDate = isset($email['date_received']) ? Carbon::parse($email['date_received'])->format('Y/m/d') : 'unknown_date';
        $emailSubject = $this->sanitizeFileName($email['subject'] ?? 'no_subject');

        $directory = "{$basePath}/{$emailDate}/{$emailSubject}";

        // Ensure unique filename
        $fileName = $this->getUniqueFileName($directory, $this->sanitizeFileName($fileName));
        $filePath = "{$directory}/{$fileName}";

        // Store the file
        Storage::put($filePath, $content);

        $this->debugLog('Attachment downloaded successfully', [
            'email_subject' => $email['subject'] ?? 'Unknown',
            'attachment_name' => $attachment['name'] ?? 'Unknown',
            'file_path' => $filePath,
            'file_size' => strlen($content)
        ]);

        if ($this->option('verbose')) {
            $this->info("Downloaded: {$fileName} (" . $this->formatBytes(strlen($content)) . ")");
        }
    }

    /**
     * Show attachment processing summary
     */
    private function showAttachmentSummary(): void
    {
        $this->newLine();
        $this->info('=== ATTACHMENT PROCESSING SUMMARY ===');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Attachments Found', $this->totalAttachments],
                ['Successfully Downloaded', $this->downloadedAttachments],
                ['Skipped', $this->skippedAttachments],
                ['Errors', count($this->attachmentErrors)],
                ['Storage Path', storage_path('app/' . $this->option('attachment-path'))]
            ]
        );

        if (!empty($this->attachmentErrors) && $this->option('debug')) {
            $this->newLine();
            $this->warn('Attachment Errors:');
            foreach ($this->attachmentErrors as $error) {
                $this->error("• {$error['email_subject']} -> {$error['attachment_name']}: {$error['error']}");
            }
        }
    }

    /**
     * Show summary of fetched emails
     */
    private function showEmailSummary(array $emails): void
    {
        $this->info('=== EMAIL FETCH SUMMARY ===');

        $totalEmails = count($emails);
        $readEmails = count(array_filter($emails, fn($e) => $e['is_read'] ?? false));
        $unreadEmails = $totalEmails - $readEmails;
        $withAttachments = count(array_filter($emails, fn($e) => $e['has_attachments'] ?? false));

        $senders = array_count_values(array_filter(array_column($emails, 'from')));
        $uniqueSenders = count($senders);
        $topSender = $uniqueSenders > 0 ? array_key_first($senders) : 'N/A';
        $topSenderCount = $uniqueSenders > 0 ? $senders[$topSender] : 0;

        $dates = array_filter(array_column($emails, 'date_received'));
        $dateRange = empty($dates) ? 'N/A' : min($dates) . ' to ' . max($dates);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Emails', $totalEmails],
                ['Read Emails', $readEmails],
                ['Unread Emails', $unreadEmails],
                ['With Attachments', $withAttachments],
                ['Unique Senders', $uniqueSenders],
                ['Top Sender', $topSender . ($topSenderCount > 0 ? " ({$topSenderCount} emails)" : '')],
                ['Date Range', $dateRange],
                ['Memory Usage', $this->formatBytes(memory_get_usage(true))],
                ['Peak Memory', $this->formatBytes(memory_get_peak_usage(true))],
            ]
        );
    }

    /**
     * Show sample emails
     */
    private function showSampleEmails(array $emails, int $count = 3): void
    {
        $this->newLine();
        $this->info("=== SAMPLE EMAILS (showing first {$count}) ===");

        foreach (array_slice($emails, 0, $count) as $index => $email) {
            $this->info("Email " . ($index + 1) . ":");
            $this->info("  Subject: " . ($email['subject'] ?: '[No Subject]'));
            $this->info("  From: " . ($email['from_name'] ?: $email['from'] ?: 'Unknown'));
            $this->info("  Date: " . ($email['date_received'] ?: 'Unknown'));
            $this->info("  Read: " . (($email['is_read'] ?? false) ? 'Yes' : 'No'));
            $this->info("  Attachments: " . (($email['has_attachments'] ?? false) ? 'Yes' : 'No'));
            $this->info("  Preview: " . substr($email['body_preview'] ?: '', 0, 100) . "...");
            $this->newLine();
        }
    }

    /**
     * Show troubleshooting tips
     */
    private function showTroubleshootingTips(string $userEmail): void
    {
        $this->newLine();
        $this->info('Troubleshooting suggestions:');
        $this->info('1. Ensure the user has authenticated: php artisan outlook:authenticate --user=' . $userEmail);
        $this->info('2. Check if the folder exists: php artisan outlook:fetch-emails --user=' . $userEmail . ' --show-folders');
        $this->info('3. Try with debug mode: php artisan outlook:fetch-emails --user=' . $userEmail . ' --debug');
        $this->info('4. Test connection: php artisan outlook:fetch-emails --user=' . $userEmail . ' --show-profile');
        $this->info('5. Check logs for detailed error information');
    }

    /**
     * Handle exceptions with detailed logging
     */
    private function handleException(\Exception $e): void
    {
        $this->error('Error: ' . $e->getMessage());

        if ($this->option('debug')) {
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }

        $this->debugLog('Exception occurred', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        Log::error('Outlook email fetch failed', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'debug_log' => $this->debugLog
        ]);
    }

    /**
     * Add entry to debug log
     */
    private function debugLog(string $message, array $context = []): void
    {
        $entry = [
            'timestamp' => now()->toISOString(),
            'message' => $message,
            'context' => $context,
            'memory' => memory_get_usage(true),
        ];

        $this->debugLog[] = $entry;

        if ($this->option('verbose')) {
            $this->line("[DEBUG] {$message} " . json_encode($context));
        }

        Log::debug('Outlook fetch: ' . $message, $context);
    }

    /**
     * Utility functions
     */
    private function isValidDate(string $date): bool
    {
        return (bool) \DateTime::createFromFormat('Y-m-d', $date);
    }

    private function sanitizeFileName(string $fileName): string
    {
        $fileName = preg_replace('/[^\w\-_\.]/', '_', $fileName);
        return substr($fileName, 0, 100); // Limit length
    }

    private function getUniqueFileName(string $directory, string $fileName): string
    {
        $counter = 1;
        $originalName = pathinfo($fileName, PATHINFO_FILENAME);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = $fileName;

        while (Storage::exists("{$directory}/{$newFileName}")) {
            $newFileName = "{$originalName}_{$counter}." . $extension;
            $counter++;
        }

        return $newFileName;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }
}
