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

    public $tries = 3;

    public $timeout = 300;

    protected $requestData;

    protected $userId;

    protected $metadata;

    public function __construct(
        array $requestData,
        ?int $userId = null,
        ?array $metadata = null
    ) {
        $this->requestData = $requestData;
        $this->userId = $userId;
        $this->metadata = $metadata ?? [];
    }

    public function handle()
    {
        try {
            $payload = $this->buildCoverSlipRequestPayload();
            $reinsurers = $this->extractReinsurers($payload);
            $slipContext = $this->resolveSlipContext($payload);

            $this->processRequest($payload, $reinsurers, $slipContext, null);

            foreach ($reinsurers as $reinsurer) {
                $this->processRequest($payload, [$reinsurer], $slipContext, $reinsurer);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function processRequest(array $basePayload, array $reinsurers, array $slipContext, ?array $singleReinsurer): void
    {
        $requestPayload = array_merge($basePayload, [
            'reinsurers_data' => json_encode($reinsurers),
        ]);

        $request = Request::create('/doc/coverslip/facultative', 'POST', $requestPayload);

        /** @var QuotationController $controller */
        $controller = app(QuotationController::class);
        $response = $controller->quotationCoverSlip($request);

        $pdfContent = $response->getContent();
        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new \Exception("Failed to generate PDF. Status code: {$statusCode}");
        }

        $filename = $this->generateFilename($slipContext, $singleReinsurer);
        $description = $slipContext['document_label'];
        if ($singleReinsurer !== null) {
            $reinsurerName = $this->resolveReinsurerName($singleReinsurer);
            $description .= $reinsurerName !== null
                ? " - {$reinsurerName}"
                : ' - Reinsurer';
        }

        $metadata = [
            'slip_type' => $slipContext['slip_type'],
            'category_type' => $slipContext['category_type'],
            'scope' => $singleReinsurer === null ? 'all' : 'single',
            'reinsurer_id' => $singleReinsurer['id'] ?? null,
        ];

        $s3File = $this->storeInS3($pdfContent, $filename, $metadata);

        $this->saveDocumentRecord(
            $s3File['s3_url'],
            $filename,
            $pdfContent,
            $description,
            isset($singleReinsurer['id']) ? (int) $singleReinsurer['id'] : null,
            $this->resolveCedantId((string) ($basePayload['opportunity_id'] ?? ''))
        );
    }

    private function buildCoverSlipRequestPayload(): array
    {
        $opportunityId = (string) ($this->requestData['opportunity_id'] ?? '');
        $currentStage = (string) ($this->requestData['current_stage'] ?? Stage::LEAD);
        $categoryType = $this->resolveCategoryType($opportunityId);
        $slipType = ((int) $categoryType === 1) ? 'quotation' : 'facultative';
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
                ->filter(fn($rein) => (int) ($rein['id'] ?? 0) > 0)
                ->values()
                ->all();
        } elseif (is_string($reinsurersData)) {
            $decodedReinsurers = json_decode($reinsurersData, true);
            $reinsurersData = is_array($decodedReinsurers) ? $decodedReinsurers : [];
        } elseif (!is_array($reinsurersData)) {
            $reinsurersData = [];
        }

        $reinsurersData = collect($reinsurersData)
            ->map(function ($rein) {
                $id = (int) ($rein['id'] ?? $rein['customer_id'] ?? 0);

                return [
                    'id' => $id,
                    'written_share' => (float) ($rein['written_share'] ?? 0),
                ];
            })
            ->filter(fn($rein) => (int) ($rein['id'] ?? 0) > 0)
            ->values()
            ->all();

        return array_merge($this->requestData, [
            'opportunity_id' => $opportunityId,
            'printout_flag' => (bool) ($this->requestData['printout_flag'] ?? false),
            'current_stage' => $currentStage,
            'category_type' => (string) $categoryType,
            'slip_type' => $slipType,
            'reinsurers_data' => json_encode($reinsurersData),
        ]);
    }

    private function extractReinsurers(array $payload): array
    {
        $decoded = json_decode((string) ($payload['reinsurers_data'] ?? '[]'), true);
        $decoded = is_array($decoded) ? $decoded : [];

        return collect($decoded)
            ->map(function ($rein) {
                return [
                    'id' => (int) ($rein['id'] ?? $rein['customer_id'] ?? 0),
                    'written_share' => (float) ($rein['written_share'] ?? 0),
                ];
            })
            ->filter(fn($rein) => (int) ($rein['id'] ?? 0) > 0)
            ->values()
            ->all();
    }

    private function resolveReinsurerName(array $singleReinsurer): ?string
    {
        $name = $singleReinsurer['name']
            ?? $singleReinsurer['reinsurer_name']
            ?? null;

        if (is_string($name) && trim($name) !== '') {
            return trim($name);
        }

        $reinsurerId = (int) ($singleReinsurer['id'] ?? 0);
        if ($reinsurerId <= 0) {
            return null;
        }

        $customerName = DB::table('customers')
            ->where('customer_id', $reinsurerId)
            ->value('name');

        return is_string($customerName) && trim($customerName) !== ''
            ? trim($customerName)
            : null;
    }

    private function resolveSlipContext(array $payload): array
    {
        $categoryType = (string) ($payload['category_type'] ?? '2');
        $isQuotation = (int) $categoryType === 1;

        return [
            'category_type' => $categoryType,
            'slip_type' => $isQuotation ? 'quotation' : 'facultative',
            'document_label' => $isQuotation ? 'Quotation Placement Slip' : 'Facultative Placement Slip',
        ];
    }

    private function resolveCategoryType(string $opportunityId): string
    {
        $categoryFromRequest = (string) ($this->requestData['category_type'] ?? '');
        if (in_array($categoryFromRequest, ['1', '2'], true)) {
            return $categoryFromRequest;
        }

        $slipType = Str::lower((string) ($this->requestData['slip_type'] ?? ''));
        if ($slipType === 'quotation') {
            return '1';
        }
        if ($slipType === 'facultative') {
            return '2';
        }

        $categoryFromDb = DB::table('pipeline_opportunities')
            ->where('opportunity_id', $opportunityId)
            ->value('category_type');

        if (in_array((string) $categoryFromDb, ['1', '2'], true)) {
            return (string) $categoryFromDb;
        }

        return '2';
    }

    protected function generateFilename(array $slipContext, ?array $singleReinsurer): string
    {
        $currentStage = (string) ($this->requestData['current_stage'] ?? Stage::LEAD);
        $stageName = Str::of($currentStage)
            ->replace(['-', '_'], ' ')
            ->title()
            ->replace(' ', '_')
            ->value();
        $opportunityId = (string) ($this->requestData['opportunity_id'] ?? 'UNKNOWN');
        $timestamp = Carbon::now()->format('Ymd_His');
        $slipType = (string) ($slipContext['slip_type'] ?? 'facultative');

        $docLabel = Str::of((string) ($slipContext['document_label'] ?? 'Cover Slip'))
            ->replace(['-', '_'], ' ')
            ->title()
            ->replace(' ', '_')
            ->value();

        $scope = $singleReinsurer === null
            ? 'All_Reinsurers'
            : 'Reinsurer_';

        return sprintf(
            'doc/coverslip/%s/%s_Opp_%s_%s_%s_%s.pdf',
            $slipType,
            $docLabel,
            $opportunityId,
            $stageName,
            $scope,
            $timestamp
        );
    }

    protected function storeInS3(string $content, string $filename, array $extraMetadata = []): array
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        $metadata = array_merge([
            'opportunity_id' => $this->requestData['opportunity_id'] ?? 'unknown',
            'stage' => $this->requestData['current_stage'] ?? 'unknown',
            'generated_at' => Carbon::now()->toIso8601String(),
            'generated_by' => $this->userId ?? 'system',
        ], array_filter($extraMetadata, fn($v) => !is_null($v)));

        $disk->put($filename, $content, [
            'visibility' => 'public',
            'ContentType' => 'application/pdf',
            'Metadata' => $metadata
        ]);

        $s3Url = $disk->url($filename);

        return ['filename' => $filename, 's3_url' => $s3Url];
    }

    protected function saveDocumentRecord(
        string $s3Path,
        string $filename,
        string $pdfContent,
        string $description,
        ?int $reinsurerId = null,
        ?int $cedantId = null
    ): int
    {
        $stage = (string) ($this->requestData['current_stage'] ?? Stage::LEAD);
        $opportunityId = $this->requestData['opportunity_id'];

        $type = 'general';
        if (Str::lower($stage) === 'lead') {
            $type = 'reinsurer';
        }

        return DB::table('prospect_docs')->insertGetId([
            'description' => $description,
            'prospect_id' => $opportunityId,
            'prospect_status' => $stage,
            'document_type_id' => 'file_26_' . Carbon::now()->format('YmdHis') . '_' . random_int(100, 999),
            'mimetype' => 'application/pdf',
            'file' => basename($filename),
            's3_path' => $filename,
            's3_url' => $s3Path,
            'file_size' => strlen($pdfContent),
            'original_name' => basename($filename),
            'bus_type' => null,
            'version' => 1,
            'type' => $type,
            'reinsurer_id' => $reinsurerId,
            'cedant_id' => $cedantId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function resolveCedantId(string $opportunityId): ?int
    {
        $cedantFromRequest = $this->requestData['cedant_id'] ?? null;
        if (is_numeric($cedantFromRequest) && (int) $cedantFromRequest > 0) {
            return (int) $cedantFromRequest;
        }

        if (trim($opportunityId) === '') {
            return null;
        }

        $customerId = DB::table('pipeline_opportunities')
            ->where('opportunity_id', $opportunityId)
            ->value('customer_id');

        return is_numeric($customerId) && (int) $customerId > 0
            ? (int) $customerId
            : null;
    }

    public function failed(\Throwable $exception)
    {
        // Optionally notify administrators or update job status
        // Notification::route('mail', config('app.admin_email'))
        //     ->notify(new JobFailedNotification($exception, $this));
    }
}
