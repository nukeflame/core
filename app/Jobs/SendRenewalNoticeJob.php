<?php

namespace App\Jobs;

use App\Mail\RenewalNoticeMail;
use App\Models\PolicyRenewal;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRenewalNoticeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $result;

    /**
     * Create a new job instance.
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $policy = $this->result['policy'];
            $request = $this->result['request'];
            $email_cc = [];
            $email_to = [];
            $email_to[] = 'reinsurance@acentriagroup.com';
            $email_to[] = 'pknuek@gmail.com';

            Mail::to($email_to)
                ->cc($email_cc)
                ->send(new RenewalNoticeMail($policy, $request));

            $policy->update([
                'last_notice_sent' => Carbon::now(),
                'notice_status' => 'Sent'
            ]);
        } catch (\Exception $e) {
        }
    }
}
