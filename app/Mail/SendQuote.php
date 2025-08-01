<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendQuote extends Mailable
{
    use Queueable, SerializesModels;
    public $quotationData;
    public $pdfPath;
    /**
     * Create a new message instance.
     */
    public function __construct($quotationData, $pdfPath)
    {

        $this->quotationData = $quotationData;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     // return new Envelope(
    //     //     subject: 'Send Quote',
    //     //     from: 'sales@gmail.com',
    //     // );

    // }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Bd_views.mail.quote-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
    public function build()
    {
        return $this->subject('Your Quotation')
            ->view('emails.quotation')
            ->attach($this->pdfPath, [
                'as' => 'quotation.pdf',
                'mime' => 'application/pdf',
            ])
            ->with(['quotationData' => $this->quotationData]);
    }
}
