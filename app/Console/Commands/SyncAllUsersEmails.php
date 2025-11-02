<?php

namespace App\Console\Commands;

use App\Jobs\SyncUserEmails;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAllUsersEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:sync-all
                            {--user= : Sync specific user by ID}
                            {--active-only : Only sync users who have active Outlook connections}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync emails for all users or specific user with active Outlook connections';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting email sync...');

        $query = User::query();

        // If specific user is requested
        if ($userId = $this->option('user')) {
            $query->where('id', $userId);
        }

        // Filter users with active Outlook connections
        if ($this->option('active-only') || !$this->option('user')) {
            $query->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('oauth_tokens')
                    ->whereColumn('oauth_tokens.user_id', 'users.id')
                    ->where('oauth_tokens.provider', 'outlook')
                    ->where('oauth_tokens.expires_at', '>', now()->timestamp);
            });
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->warn('No users found with active Outlook connections.');
            return 0;
        }

        $this->info("Found {$users->count()} user(s) to sync.");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $synced = 0;
        $failed = 0;

        foreach ($users as $user) {
            try {
                // Check if user is already syncing
                $syncState = DB::table('email_sync_states')
                    ->where('user_id', $user->id)
                    ->first();

                if ($syncState && $syncState->is_syncing) {
                    $this->warn("\nSkipping user {$user->email} - sync already in progress");
                    continue;
                }

                // Dispatch sync job
                SyncUserEmails::dispatch($user->id);
                $synced++;

                $bar->advance();
            } catch (\Exception $e) {
                $failed++;
                $this->error("\nFailed to sync user {$user->email}: {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Email sync jobs dispatched");
        $this->table(
            ['Status', 'Count'],
            [
                ['Synced', $synced],
                ['Failed', $failed],
                ['Total', $users->count()],
            ]
        );

        return 0;
    }
}
