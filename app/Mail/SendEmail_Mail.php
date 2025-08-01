<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail_Mail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_content;
    public $title;
    public $createdate;
    public $base64Documents;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email_content, $title, $createdate, $subject, $user)
    {
        $this->email_content = $email_content;
        $this->title = $title;
        $this->createdate = $createdate;
        $this->subject = $subject;
        // $this->base64Documents = $base64Documents;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $schem = schemaName();
        $sender = env('MAIL_USERNAME');


        $data = $this->email_content;
        $title = $this->title;
        $createdate = $this->createdate;
        $subject = $this->subject;
        // $base64Documents = $this->base64Documents;

        $mail = $this->subject($subject)
            ->from($sender)
            ->view('emailnotifications.sendemails', ['content' => $data, 'title' => $title, 'createdate' => $createdate]);

        // foreach ($base64Documents as $key => $base64Document) {
        //     $base64Document = $base64Document->document;
        //     $decodedDocument = base64_decode($base64Document);

        //     $mail->attachData($decodedDocument, 'document_' . ($key + 1) . '.pdf', [
        //         'mime' => 'application/pdf',
        //     ]);
        // }
        return $mail;
    }
}
