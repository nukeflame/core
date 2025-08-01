<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class SendQuoteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $pdfPath;
    protected $additionalFilePath;
    protected $pdfFilename;
    protected $fileName;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $filePath, $t = [], $pdfFilename, $fileName = [])
    {

        $this->data = $data;
        $this->pdfPath = $filePath; // This should be 'uploads/quote_267.pdf', not full path
        $this->additionalFilePath = is_array($t) ? $t : [$t];
        $this->pdfFilename = $pdfFilename;
        $this->fileName = is_array($fileName) ? $fileName : [$fileName];
    }

    public function handle()
    {
        try {
            // Ensure correct paths
            $fullPdfPath = $this->pdfPath; // e.g., 'uploads/quote_267.pdf'
            $pdfFilename = $this->pdfFilename;

            if (!Storage::disk('s3')->exists($fullPdfPath)) {
                // logger("PDF not found in S3: $fullPdfPath");
                return;
            }
            $pdfContent = Storage::disk('s3')->get($fullPdfPath);
            $pdfMimeType = Storage::disk('s3')->mimeType($fullPdfPath);
            Mail::send('emailnotifications.salesemails', $this->data, function ($message) use ($pdfContent, $pdfFilename, $pdfMimeType) {
                $message->to($this->data['email'])
                    ->cc(!empty($this->data['cc']) ? $this->data['cc'] : [])
                    ->subject($this->data["title"]);

                // Attach main PDF
                $pdfFilename = preg_replace('/_\d+/', '', $pdfFilename);
                // logger('file name: ' . $pdfFilename);
                $message->attachData($pdfContent, $pdfFilename, [
                    'mime' => $pdfMimeType,
                ]);

                // Attach additional file if it exists
                foreach ($this->additionalFilePath as $index => $addFilePath) {
                    if (!$addFilePath) {
                        // logger('error in file path');
                        continue;
                    }

                    $fullAdditionalFilePath = $addFilePath;

                    if (Storage::disk('s3')->exists($fullAdditionalFilePath)) {
                        $fileContent = Storage::disk('s3')->get($fullAdditionalFilePath);
                        $fileMimeType = Storage::disk('s3')->mimeType($fullAdditionalFilePath);
                        $fileName = $this->fileName[$index] ?? basename($fullAdditionalFilePath);
                        $fileName = preg_replace('/_\d+/', '', $fileName);
                        $message->attachData($fileContent, $fileName, [
                            'mime' => $fileMimeType,
                        ]);
                    } else {
                        // logger("Additional file not found in S3: $fullAdditionalFilePath");
                    }
                }
            });
        } catch (\Exception $e) {
        }
    }
}
