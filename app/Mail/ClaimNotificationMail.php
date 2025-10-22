<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ClaimNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public array $emailData
    ) {}


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                )
            ],
            subject: $this->emailData['subject'],
            tags: [
                'claim-notification',
                'claim-' . $this->emailData['claim']->id
            ],
            metadata: [
                'claim_id' => 233, //$this->emailData['claim']->id ?? null,
                'claim_reference' => 'REF-8393', //$this->emailData['claim']->reference_number ?? null,
                'sent_by' => 'sender',  //=> $this->emailData['sender']->id,
                'tracking_id' => 'TRK-' . uniqid() //$this->emailData['tracking_id'] ?? null
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.claims.claim-notification',
            text: 'emails.claims.claim-notification-text',
            with: [
                'emailData' => $this->emailData,
                'claim' => $this->emailData['claim'],
                'cedant' => $this->emailData['cedant'],
                'sender' => $this->emailData['sender'],
                'messageContent' => $this->emailData['message'],
                'includeClaimDetails' => $this->emailData['include_claim_details'],
                'additionalNotes' => $this->emailData['additional_notes'],
                'trackingId' => $this->emailData['tracking_id'],
                'sentAt' => $this->emailData['sent_at']
            ]
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
}
