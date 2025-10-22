<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Bd\Leads\TenderApproval;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class ApprovalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $approval;

    /**
     * Create a new job instance.
     */
    public function __construct(TenderApproval $approval)
    {
        $this->approval = $approval;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Map numeric status to readable status
            $statusMap = [
                0 => 'Pending',
                1 => 'Approved',
                2 => 'Rejected',
            ];

            // If status is 0 (pending), notify the approvers
            if ($this->approval->status === '0') {
                // Get all approvers from the approver_id array
                $approvers = User::whereIn('id', $this->approval->approver_id)->get();

                if ($approvers->isEmpty()) {
                    throw new \Exception('No valid approvers found for tender ID ' . $this->approval->tender_id);
                }

                // First approver is the main recipient, others are CC'd
                $mainApprover = $approvers->first();
                $ccApprovers = $approvers->slice(1)->pluck('email')->toArray();

                Mail::send('emailnotifications.tender_approval', ['approval' => $this->approval, 'mainApprovers' => $mainApprover], function ($message) use ($mainApprover, $ccApprovers) {
                    $message->to($mainApprover->email)
                        ->subject('Tender Approval Required');
                    if (!empty($ccApprovers)) {
                        $message->cc($ccApprovers);
                    }
                });
            }

            if (in_array($this->approval->status, [1, 2])) {
                $submitter = User::findOrFail($this->approval->submitter_id);

                Mail::send('emailnotifications.tender_status', [
                    'approval' => $this->approval,
                    'statusText' => $statusMap[$this->approval->status],
                    'submitter' => $submitter,
                ], function ($message) use ($submitter, $statusMap) {
                    $message->to($submitter->email)
                        ->subject('Tender Status Update: ' . $statusMap[$this->approval->status]);
                });
            }
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }
}
