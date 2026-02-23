<?php

namespace App\Jobs;

use App\Enums\Stage;
use App\Http\Controllers\QuotationController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Filesystem\FilesystemAdapter;

class GenerateBdCoverSlipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * @var array
     */
    protected $requestData;

    /**
     * @var int|null
     */
    protected $userId;

    /**
     * @var array|null
     */
    protected $metadata;

    /**
     * Create a new job instance.
     *
     * @param array $requestData - The request data to pass to bdCoverSlip()
     * @param int|null $userId
     * @param array|null $metadata
     */
    public function __construct(
        array $requestData,
        ?int $userId = null,
        ?array $metadata = null
    ) {
        $this->requestData = $requestData;
        $this->userId = $userId;
        $this->metadata = $metadata ?? [];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $request = Request::create(
                '/doc/coverslip/facultative',
                'POST',
                $this->buildFacultativeRequestPayload()
            );

            $this->processRequest($request);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function processRequest($request): void
    {
        /** @var QuotationController $controller */
        $controller = app(QuotationController::class);
        $response = $controller->quotationCoverSlip($request);

        $pdfContent = $response->getContent();
        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new \Exception("Failed to generate PDF. Status code: {$statusCode}");
        }

        $filename = $this->generateFilename();
        $s3File = $this->storeInS3($pdfContent, $filename);

        $this->saveDocumentRecord($s3File['s3_url'], $filename, $pdfContent);
    }

    private function buildFacultativeRequestPayload(): array
    {
        $opportunityId = (string) ($this->requestData['opportunity_id'] ?? '');
        $currentStage = (string) ($this->requestData['current_stage'] ?? Stage::LEAD);
        $reinsurersData = $this->requestData['reinsurers_data'] ?? null;

        if ($reinsurersData === null || $reinsurersData === '') {
            $reinsurersData = DB::table('bd_fac_reinsurers')
                ->where('opportunity_id', $opportunityId)
                ->select('reinsurer_id as id', 'written_share')
                ->get()
                ->map(fn($rein) => [
                    'id' => (int) $rein->id,
                    'written_share' => (float) ($rein->written_share ?? 0),
                ])
                ->values()
                ->all();
        } elseif (is_string($reinsurersData)) {
            $decodedReinsurers = json_decode($reinsurersData, true);
            $reinsurersData = is_array($decodedReinsurers) ? $decodedReinsurers : [];
        } elseif (!is_array($reinsurersData)) {
            $reinsurersData = [];
        }

        return array_merge($this->requestData, [
            'opportunity_id' => $opportunityId,
            'printout_flag' => (bool) ($this->requestData['printout_flag'] ?? false),
            'current_stage' => $currentStage,
            'category_type' => (string) ($this->requestData['category_type'] ?? '2'),
            'slip_type' => 'facultative',
            'reinsurers_data' => json_encode($reinsurersData),
        ]);
    }

    /**
     * Generate unique filename
     *
     * @return string
     */
    protected function generateFilename(): string
    {
        $currentStage = $this->requestData['current_stage'];
        $stageName = str_replace(' ', '_', strtolower((string) $currentStage));
        $opportunityId = $this->requestData['opportunity_id'] ?? 'unknown';
        $timestamp = Carbon::now()->format('YmdHis');

        return sprintf(
            'doc/coverslip/facultative/Fac_Cover_Slip_%s_Opp_%s_%s.pdf',
            $stageName,
            $opportunityId,
            $timestamp
        );
    }

    /**
     * Store PDF in S3
     *
     * @param string $content
     * @param string $filename
     * @return array
     */
    protected function storeInS3(string $content, string $filename): array
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        $disk->put($filename, $content, [
            'visibility' => 'public',
            'ContentType' => 'application/pdf',
            'Metadata' => [
                'opportunity_id' => $this->requestData['opportunity_id'] ?? 'unknown',
                'stage' => $this->requestData['current_stage'] ?? 'unknown',
                'generated_at' => Carbon::now()->toIso8601String(),
                'generated_by' => $this->userId ?? 'system',
            ]
        ]);

        $s3Url = $disk->url($filename);

        return ['filename' => $filename, 's3_url' => $s3Url];
    }

    /**
     * Save document record to database
     *
     * @param string $s3Path
     * @param string $filename
     * @param string $pdfContent
     * @return int Document ID
     */
    protected function saveDocumentRecord(string $s3Path, string $filename, string $pdfContent): int
    {
        $stage = $this->requestData['current_stage'];
        $documentType = 'Cover Slip';
        $opportunityId = $this->requestData['opportunity_id'];

        $type = 'general';
        if (Str::lower($stage) === 'lead') {
            $type = 'reinsurer';
        }

        return DB::table('prospect_docs')->insertGetId([
            'description' => $documentType,
            'prospect_id' => $opportunityId,
            'prospect_status' => $stage,
            'document_type_id' => 'file_26_' . Carbon::now()->format('YmdHis') . '_0',
            'mimetype' => 'application/pdf',
            'file' => basename($filename),
            's3_path' => $filename,
            's3_url' => $s3Path,
            'file_size' => strlen($pdfContent),
            'original_name' => basename($filename),
            'bus_type' => null,
            'version' => 1,
            'type' => $type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        // Optionally notify administrators or update job status
        // Notification::route('mail', config('app.admin_email'))
        //     ->notify(new JobFailedNotification($exception, $this));
    }
}
