<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\TenderEmail;
use App\Models\Tender;

class SendTenderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tender;
    protected $pdfBase64;
    protected $prospectId;
    protected $pdfPath;
    protected $pdfFilename;
    protected $mainEmail;
    protected $ccEmail;


    public function __construct($tender, $pdfBase64, $prospectId, $pdfPath, $pdfFilename, $mainEmail = null, $ccEmail = null)
    {
        $this->tender = $tender;
        $this->pdfBase64 = $pdfBase64;
        $this->prospectId = $prospectId;
        $this->pdfPath = $pdfPath;
        $this->pdfFilename = $pdfFilename;
        $this->mainEmail = $mainEmail;
        $this->ccEmail = $ccEmail;

    }

    public function handle()
    {
        try {
            $fullPdfPath = $this->pdfPath; // e.g., 'uploads/quote_267.pdf'
            $pdfFilename = $this->pdfFilename;

            if (!Storage::disk('s3')->exists($fullPdfPath)) {
                // logger("PDF not found in S3: $fullPdfPath");
                return;
            }
            // Fetch content and MIME type of the DOMPDF PDF from S3
            $s3PdfContent = Storage::disk('s3')->get($this->pdfPath);
            $s3PdfMime = Storage::disk('s3')->mimeType($this->pdfPath);
            $s3PdfFilename = preg_replace('/_\d+/', '', $this->pdfFilename);

            // Decode frontend base64 PDF
            $frontendPdfContent = base64_decode($this->pdfBase64);
            $frontendPdfFilename = 'TenderUpload_' . $this->tender->tender_no . '.pdf';

            // Get recipient email (replace with your logic)
            $recipientEmail = $this->mainEmail['email'] ?? 'default@example.com';
            $recipientName = $this->mainEmail['name'] ?? null;
            if (!$recipientEmail) {
                \Log::error('No valid recipient email provided for tender: ' . $this->tender->tender_no);
                return; // or throw an exception
            }

            Mail::send('emailnotifications.tender', [
                'tender' => $this->tender,
                'recipientName' => $recipientName,
            ], function ($message) use ($recipientEmail, $s3PdfContent, $s3PdfMime, $s3PdfFilename, $frontendPdfContent, $frontendPdfFilename) {

                $message->to($recipientEmail);
                if (!empty($this->ccEmail)) {
                    foreach ($this->ccEmail as $cc) {
                        if (isset($cc['email'])) {
                            $message->cc($cc['email'] ?? null);
                        }
                    }
                }

                $message->subject('Confirmation OF Interest To Particepate In Reinsurance  Broker Tender Process - ' . $this->tender->tender_no);

                // Attach the DOMPDF PDF from S3
                $message->attachData($s3PdfContent, $s3PdfFilename, [
                    'mime' => $s3PdfMime,
                ]);

                // Attach the base64 PDF from frontend
                $message->attachData($frontendPdfContent, $frontendPdfFilename, [
                    'mime' => 'application/pdf',
                ]);
            });


        } catch (\Exception $e) {
            // Log error or handle failure
            \Log::error('Failed to send tender email: ' . $e->getMessage());
            $this->fail($e);
        }
    }

    // private function getRecipientEmail($prospectId)
    // {
    //     // Replace with your logic to fetch recipient email based on prospect_id
    //     // Example: $prospect = Prospect::findOrFail($prospectId); return $prospect->email;
    //     return 'derrickriziki7@gmail.com'; // Placeholder
    // }
}