<?php

namespace App\Mail;

use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReinsurerFacultativeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $filepath;
    public $filename;
    public $subject;
    public $content;
    public $coverSlipPdf;
    public $coverSlipFilename;
    public $coverSlipFilepath;

    /**
     * Create a new message instance.
     */
    public function __construct(
        $filepath,
        $filename,
        $subject,
        $content,
        $coverSlipPdf,
        $coverSlipFilename,
        $coverSlipFilepath
    ) {
        $this->filepath = $filepath;
        $this->filename = $filename;
        $this->subject = $subject;
        $this->content = $content;
        $this->coverSlipPdf = $coverSlipPdf;
        $this->coverSlipFilename = $coverSlipFilename;
        $this->coverSlipFilepath = $coverSlipFilepath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject ?? 'Reinsurer Facultative Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.credit_note',
            with: [
                'policy' => '',
                'days_until' => Carbon::now()->diffInDays(now()),
                'content' => $this->content
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
            $filePath = 'reinsurers/' . $this->filename;
            $filePath2 = 'coverslip/' . $this->coverSlipFilename;


            try {
                $attach1 = Attachment::fromStorage('public/' . $filePath)
                    ->as($this->filename)
                    ->withMime('application/pdf');
                $data[] = $attach1;

                $attach2 = Attachment::fromStorage('public/' . $filePath2)
                    ->as($this->coverSlipFilename)
                    ->withMime('application/pdf');
                $data[] = $attach2;
            } catch (\Exception $e) {
                throw ($e);
            }

            return $data;
        } catch (Exception $e) {
            return [];
        }
    }
}
