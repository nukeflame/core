<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function handle()
    {
        try {
            Mail::to($this->email->recipient_email)->send(new SendEmail($this->email));

            $this->email->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            $this->email->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
