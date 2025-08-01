<?php

namespace App\Mail;

use App\Models\PolicyRenewal;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RenewalNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $policy;
    protected $request;

    /**
     * Create a new message instance.
     */
    public function __construct(PolicyRenewal $policy, $request)
    {
        $this->policy = $policy;
        $this->request = $request;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->request['emailSubject'] ?? "Policy Renewal Notice - {$this->policy->policy_number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.renewal-notice',
            with: [
                'policy' => $this->policy,
                'days_until_renewal' => Carbon::now()->diffInDays($this->policy->renewal_date),
                'emailContent' => $this->request['emailContent']
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        try {
            $data = [];
            if (!$this->policy || !$this->policy->documents) {
                return [];
            }
            foreach ($this->policy->documents as $doc) {
                try {
                    $filePath = 'public/renewals/' . $doc->doc_name;
                    if (!Storage::exists($filePath)) {
                        continue;
                    }

                    $isCedantDoc = str_contains($doc->doc_name, 'Cedant_Renewal_Notice') &&
                        strtolower($this->request['recipentType']) === 'cedant';

                    $isReinsurerDoc = str_contains($doc->doc_name, 'Reinsurer_Renewal_Notice') &&
                        strtolower($this->request['recipentType']) === 'reinsurer';

                    if ($isCedantDoc || $isReinsurerDoc) {
                        $attachment = Attachment::fromStorage($filePath)
                            ->as($doc->doc_name)
                            ->withMime('application/pdf');

                        $data[] = $attachment;
                    }
                } catch (\Exception $e) {
                    logger()->error($e);
                    continue;
                }
            }

            return $data;
        } catch (\Exception $e) {
            logger()->error($e);
            return [];
        }
    }
}
