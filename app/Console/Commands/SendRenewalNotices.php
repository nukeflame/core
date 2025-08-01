<?php

namespace App\Console\Commands;

use App\Mail\RenewalNoticeMail;
use App\Models\PolicyRenewal;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRenewalNotices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renewal:send-notices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send renewal notices for policies due for renewal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting renewal notice job...');

        try {
            // Get policies due for renewal in next 30 days
            $policies = PolicyRenewal::query()
                ->whereDate('renewal_date', '>=', Carbon::now())
                ->whereDate('renewal_date', '<=', Carbon::now()->addDays(30))
                ->whereNull('last_notice_sent')
                ->orWhere('last_notice_sent', '<=', Carbon::now()->subDays(7))
                ->get();

            $this->info("Found {$policies->count()} policies requiring notices.");

            foreach ($policies as $policy) {
                try {
                    // Mail::to($policy->client_email)
                    //     ->send(new RenewalNoticeMail($policy));

                    $policy->update([
                        'last_notice_sent' => Carbon::now(),
                        'notice_status' => 'sent'
                    ]);

                    $this->info("Notice sent for policy #{$policy->policy_number}");
                    // logger("Renewal notice sent", [
                    //     'policy_number' => $policy->policy_number,
                    //     'client_email' => $policy->client_email
                    // ]);
                } catch (\Exception $e) {
                    $this->error("Failed to send notice for policy #{$policy->policy_number}");
                    // logger("Failed to send renewal notice", [
                    //     'policy_number' => $policy->policy_number,
                    //     'error' => $e->getMessage()
                    // ]);
                }
            }
        } catch (\Exception $e) {
            $this->error("Job failed: {$e->getMessage()}");
            logger("Renewal notice job failed", ['error' => $e->getMessage()]);
        }
    }
}
