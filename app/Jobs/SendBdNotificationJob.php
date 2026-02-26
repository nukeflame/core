<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Services\MailService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBdNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        private array $emailData,
        private int $userId
    ) {
    }

    public function handle(MailService $mailService): void
    {
        $customerId = (int) ($this->emailData['customer_id'] ?? 0);
        $data = $this->emailData;
        $data['customer'] = $customerId > 0
            ? Customer::where('customer_id', $customerId)->first()
            : null;

        $result = $mailService->sendEmail($data, $this->userId);

        if (!$result) {
            throw new Exception('Unable to dispatch BD notification email jobs.');
        }
    }
}
