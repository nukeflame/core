<?php

namespace App\Mail;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function build()
    {
        $mail = $this->from($this->email->sender_email, $this->email->sender_name)
            ->subject($this->email->subject)
            ->view('emails.template')
            ->with(['emailContent' => $this->email]);

        // Add attachments
        if ($this->email->attachments) {
            foreach ($this->email->attachments as $attachment) {
                $mail->attach(storage_path('app/' . $attachment['path']), [
                    'as' => $attachment['name']
                ]);
            }
        }

        return $mail;
    }
}
