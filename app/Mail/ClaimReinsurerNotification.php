<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ClaimReinsurerNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $priority = match ($this->emailData['priority']) {
            'high' => 1,
            'low' => 5,
            default => 3
        };

        return new Envelope(
            from: new Address(
                $this->emailData['sender_email'],
                $this->emailData['sender_name']
            ),
            replyTo: [
                new Address(
                    $this->emailData['sender_email'],
                    $this->emailData['sender_name']
                )
            ],
            subject: $this->emailData['subject'],
            tags: ['claim-notification', 'reinsurer'],
            metadata: [
                'claim_no' => $this->emailData['claim']->claim_no,
                'category' => $this->emailData['category'],
                'reference' => $this->emailData['reference']
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.claims.claim-reinsurer-notification',
            with: [
                'claim' => $this->emailData['claim'],
                'message' => $this->emailData['message'],
                'reference' => $this->emailData['reference'],
                'category' => $this->emailData['category'],
                'priority' => $this->emailData['priority'],
                'senderName' => $this->emailData['sender_name'],
                'senderEmail' => $this->emailData['sender_email'],
                'companyName' => config('app.name', 'Acentria International Reinsurance Brokers Limited'),
                'currentYear' => date('Y')
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
        $attachments = [];

        if (!empty($this->emailData['attachments'])) {
            foreach ($this->emailData['attachments'] as $attachment) {
                try {
                    if (file_exists($attachment['path'])) {
                        $attachments[] = Attachment::fromPath($attachment['path'])
                            ->as($attachment['name'])
                            ->withMime($attachment['mime_type']);

                        Log::debug('Attachment added to email', [
                            'name' => $attachment['name'],
                            'size' => $attachment['size'],
                            'mime_type' => $attachment['mime_type']
                        ]);
                    } else {
                        Log::warning('Attachment file not found', [
                            'path' => $attachment['path'],
                            'name' => $attachment['name']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to attach file', [
                        'path' => $attachment['path'],
                        'name' => $attachment['name'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $attachments;
    }
}
