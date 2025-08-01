<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TreatyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $fileName;
    protected $pdfFilename;
    protected $cedant_doc_name;
    protected $stage;
    protected $stageType;
    protected $pdfPath;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $fileName = [], $cedant_doc_name=[], $pdfFilename = [], $stage,$stageType,$pdfPath)
    {
        $this->data = $data;
        $this->fileName = is_array($fileName) ? $fileName : [$fileName];
        $this->pdfFilename = is_array($pdfFilename) ? $pdfFilename : [$pdfFilename];
        $this->cedant_doc_name = is_array($cedant_doc_name) ? $cedant_doc_name : [$cedant_doc_name];
        $this->stage = $stage;
        $this->stageType = $stageType;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // Log::info('TreatyJob started for email: ' . $this->data['email']);

            Mail::send('emailnotifications.treatySalesemails', $this->data, function ($message) {
                $message->to($this->data['email'])
                    ->cc(!empty($this->data['cc']) ? $this->data['cc'] : [])
                    ->subject($this->data["title"]);
                if (isset($this->cedant_doc_name)) {

                    foreach ($this->cedant_doc_name as $file) {
                        $fullPath = public_path('uploads/cedant_docs/' . basename($file));

                        if (file_exists($fullPath)) {
                            $message->attach($fullPath, [
                                'as' => basename($file),
                                'mime' => mime_content_type($fullPath),
                            ]);
                            Log::info('Attached file: ' . $fullPath);
                        } else {
                            Log::warning('File not found for attachment: ' . $fullPath);
                        }
                    }
                }
                if (isset($this->fileName)) {
                    foreach ($this->fileName as $file) {
                        $fullPath = public_path('uploads/' . basename($file));

                        if (file_exists($fullPath)) {
                            $message->attach($fullPath, [
                                'as' => basename($file),
                                'mime' => mime_content_type($fullPath),
                            ]);
                            Log::info('Attached file: ' . $fullPath);
                        } else {
                            Log::warning('File not found for attachment: ' . $fullPath);
                        }
                    }
                }

                if (!empty($this->pdfFilename)) {
                    foreach ($this->pdfFilename as $pdfFile) {
                        if (Storage::disk('s3')->exists($this->pdfPath)) {
                            $s3PdfContent = Storage::disk('s3')->get($this->pdfPath);
                            $message->attachData($s3PdfContent, basename($pdfFile), [
                                'mime' => Storage::disk('s3')->mimeType($this->pdfPath) ?: 'application/pdf',
                            ]);
                            Log::info('Attached S3 PDF: ' . $this->pdfPath);
                        } else {
                            Log::warning('S3 PDF not found: ' . $this->pdfPath);
                        }
                    }
                }
            });

            Log::info('TreatyJob completed successfully for email: ' . $this->data['email']);
        } catch (\Exception $e) {
            Log::error('TreatyJob failed for email: ' . $this->data['email'] . '. Error: ' . $e->getMessage());
            $this->fail($e);
        }
    }
}
