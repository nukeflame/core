<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\CoverRegister;
use App\Models\DebitNote;
use App\Models\TransactionLog;
use App\Repositories\CoverRepository;
use App\Services\DebitNote\AmountCalculator;
use App\Services\DebitNote\LineItemProcessor;
use App\Services\DebitNote\StatusManager;
use Illuminate\Support\Facades\DB;

class DebitNoteService
{
    public const TYPE_FAC = SequenceService::BUSINESS_TYPE_FAC;
    public const TYPE_TREATY = SequenceService::BUSINESS_TYPE_TREATY;
    public const TYPE_RETRO = SequenceService::BUSINESS_TYPE_RETRO;

    public const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];
    public const CREDIT_CODES = ['IT02', 'IT03', 'IT04', 'IT05', 'IT06', 'IT07', 'IT08', 'IT10', 'IT21', 'IT27', 'IT29', 'IT30'];

    public function __construct(
        private readonly SequenceService $sequenceService,
        private readonly CoverRepository $coverRepository,
        // private readonly TaxCalculationService $taxService,
        private readonly AmountCalculator $amountCalculator,
        private readonly LineItemProcessor $lineItemProcessor,
        private readonly StatusManager $statusManager,
        // private readonly TransactionLog $transactionLogger
    ) {}

    public function generateDebitNoteNumber(string $businessType, ?int $year = null): string
    {
        $typeOfBus = match (true) {
            $this->coverRepository->isTreatyBusiness($businessType) => 'TREATY',
            $this->coverRepository->isFacultativeBusiness($businessType) => 'FAC',
            default => 'RETRO',
        };

        return (string) $this->sequenceService->generateDebitNoteNumber($typeOfBus, $year)->debit_no;
    }

    public function parseDebitNoteNumber(string $debitNoteNo): array
    {
        return $this->sequenceService->parseNoteNumber($debitNoteNo);
    }

    public function create(array $data, CoverRegister $cover): DebitNote
    {

        return DB::transaction(function () use ($data, $cover) {

            $this->validateCreateData($data);

            $calculation = $this->amountCalculator->calculate($data, $cover);

            $debitNoteNo = $this->generateDebitNoteNumber(
                $cover->type_of_bus,
                $data['postingYear'] ?? now()->year
            );

            $debitNote = $this->createDebitNoteRecord($debitNoteNo, $data, $cover, $calculation);

            $this->lineItemProcessor->createLineItems($debitNote, $calculation);

            if ($data['updateRipart'] ?? false) {
                $this->updateReinsurerParticipation($calculation['reinsurers']);
            }

            // $this->transactionLogger->log($debitNote, 'CREATE');

            return $debitNote->fresh(['items', 'cover', 'cover.customer']);
        });
    }

    public function update(DebitNote $debitNote, array $data): DebitNote
    {
        return DB::transaction(function () use ($debitNote, $data) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (! $debitNote->isEditable()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $debitNote->status,
                    'EDIT'
                );
            }

            $oldValues = $debitNote->toArray();

            $calculation = $this->amountCalculator->calculate($data, $debitNote->cover);

            $this->updateDebitNoteRecord($debitNote, $data, $calculation);

            if (isset($data['items'])) {
                $this->lineItemProcessor->replaceLineItems($debitNote, $data['items']);
            }

            // $this->transactionLogger->log($debitNote, 'UPDATE', $oldValues);

            return $debitNote->fresh(['items', 'cover']);
        });
    }

    public function submit(DebitNote $debitNote): DebitNote
    {
        return $this->statusManager->submit($debitNote);
    }

    public function approve(DebitNote $debitNote): DebitNote
    {
        return $this->statusManager->approve($debitNote);
    }

    public function reject(DebitNote $debitNote, string $reason): DebitNote
    {
        return $this->statusManager->reject($debitNote, $reason);
    }

    public function revertToDraft(DebitNote $debitNote): DebitNote
    {
        return $this->statusManager->revertToDraft($debitNote);
    }

    public function cancel(DebitNote $debitNote, string $reason): DebitNote
    {
        return $this->statusManager->cancel($debitNote, $reason);
    }

    public function post(DebitNote $debitNote): DebitNote
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (! $debitNote->canPost()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $debitNote->status,
                    DebitNote::STATUS_POSTED
                );
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_POSTED,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            // Create accounting entries
            // TODO: Implement accounting integration
            // $this->accountingService->createEntries($debitNote);

            // $this->transactionLogger->log($debitNote, 'POST', $oldValues);

            return $debitNote->fresh();
        });
    }

    public function delete(DebitNote $debitNote): bool
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (! $debitNote->isEditable()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $debitNote->status,
                    'DELETE'
                );
            }

            $oldValues = $debitNote->toArray();

            $debitNote->items()->delete();
            $debitNote->delete();

            // $this->transactionLogger->log($debitNote, 'DELETE', $oldValues);

            return true;
        });
    }

    public function duplicate(DebitNote $original): DebitNote
    {
        return DB::transaction(function () use ($original) {
            $cover = $original->cover;

            // Prepare new data
            $newData = $this->prepareDuplicateData($original);

            return $this->create($newData, $cover);
        });
    }

    public function getStatistics(array $filters = []): array
    {
        $query = DebitNote::query();

        $this->applyFilters($query, $filters);

        return [
            'total_count' => $query->count(),
            'total_gross' => round((float) $query->sum('gross_amount'), 2),
            'total_net' => round((float) $query->sum('net_amount'), 2),
            'by_status' => $this->getStatusBreakdown($filters),
            'pending_count' => DebitNote::pending()->count(),
            'approved_count' => DebitNote::approved()->count(),
            'posted_count' => DebitNote::posted()->count(),
        ];
    }

    public function calculateAmounts(array $data, ?CoverRegister $cover = null)
    {
        return $this->amountCalculator->calculate($data, $cover);
    }

    protected function validateCreateData(array $data): void
    {
        $required = ['postingDate', 'postingYear', 'postingQuarter', 'items'];

        foreach ($required as $field) {
            if (! isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (empty($data['items'])) {
            throw BusinessRuleException::missingItems();
        }
    }

    protected function createDebitNoteRecord(
        string $debitNoteNo,
        array $data,
        CoverRegister $cover,
        $calculation
    ): DebitNote {

        return DebitNote::create([
            'debit_note_no' => $debitNoteNo,
            'cover_no' => $cover->cover_no,
            'endorsement_no' => $cover->endorsement_no,
            'type_of_bus' => $cover->type_of_bus,
            'installment_no' => $data['installment'] ?? $this->getNextInstallment($cover),
            'reinsurer_posting' => $data['reinsurerPosting'] ?? null,
            'premium_pay_terms' => $cover->premium_payment_code,
            'posting_year' => $data['postingYear'],
            'posting_quarter' => $data['postingQuarter'],
            'posting_date' => $data['postingDate'],
            'brokerage_rate' => $data['brokerageRate'] ?? 0,
            'gross_amount' => $calculation['cedant']['gross_amount'],
            'commission_amount' => $calculation['cedant']['commission_amount'],
            'brokerage_amount' => $calculation['cedant']['brokerage_amount'],
            'premium_tax' => $calculation['cedant']['premium_tax'],
            'reinsurance_tax' => $calculation['cedant']['reinsurance_tax'],
            'withholding_tax' => $calculation['cedant']['withholding_tax'],
            'other_deductions' => $calculation['cedant']['other_deductions'],
            'total_deductions' => $calculation['cedant']['total_deductions'],
            'net_amount' => $calculation['cedant']['net_amount'],
            'comments' => $data['comments'] ?? null,
            'show_cedant' => $data['showCedant'] ?? false,
            'show_reinsurer' => $data['showReinsurer'] ?? false,
            'loss_participation' => $data['lossParticipation'] ?? false,
            'sliding_commission' => $data['slidingCommission'] ?? false,
            'status' => DebitNote::STATUS_DRAFT,
            'created_by' => auth()->id(),
        ]);
    }

    protected function updateDebitNoteRecord(
        DebitNote $debitNote,
        array $data,
        $calculation
    ): void {
        $debitNote->update([
            'posting_year' => $data['postingYear'] ?? $debitNote->posting_year,
            'posting_quarter' => $data['postingQuarter'] ?? $debitNote->posting_quarter,
            'posting_date' => $data['postingDate'] ?? $debitNote->posting_date,
            'brokerage_rate' => $data['brokerageRate'] ?? $debitNote->brokerage_rate,
            'gross_amount' => $calculation->grossAmount,
            'commission_amount' => $calculation->commissionAmount,
            'brokerage_amount' => $calculation->brokerageAmount,
            'premium_tax' => $calculation->premiumTax,
            'reinsurance_tax' => $calculation->reinsuranceTax,
            'withholding_tax' => $calculation->withholdingTax,
            'other_deductions' => $calculation->otherDeductions,
            'total_deductions' => $calculation->totalDeductions,
            'net_amount' => $calculation->netAmount,
            'comments' => $data['comments'] ?? $debitNote->comments,
            'updated_by' => auth()->id(),
        ]);
    }

    protected function getNextInstallment(CoverRegister $cover): int
    {
        $lastInstallment = DebitNote::where('cover_no', $cover->cover_no)
            ->where('endorsement_no', $cover->endorsement_no)
            ->whereNotIn('status', [DebitNote::STATUS_CANCELLED])
            ->max('installment_no');

        return ($lastInstallment ?? 0) + 1;
    }

    protected function prepareDuplicateData(DebitNote $original): array
    {
        return [
            'postingDate' => now()->toDateString(),
            'postingYear' => now()->year,
            'postingQuarter' => $this->getCurrentQuarter(),
            'brokerageRate' => $original->brokerage_rate,
            'reinsurerPosting' => $original->reinsurer_posting,
            'comments' => 'Duplicated from: ' . $original->debit_note_no,
            'showCedant' => $original->show_cedant,
            'showReinsurer' => $original->show_reinsurer,
            'lossParticipation' => $original->loss_participation,
            'slidingCommission' => $original->sliding_commission,
            'items' => $original->items->map(fn($item) => [
                'item_code' => $item->item_code,
                'description' => $item->description,
                'class_group' => $item->class_group_code,
                'class_name' => $item->class_code,
                'line_rate' => $item->line_rate,
                'ledger' => $item->ledger,
                'amount' => $item->amount,
            ])->toArray(),
        ];
    }

    protected function updateReinsurerParticipation(array $reinsurers): void
    {
        foreach ($reinsurers as $reinsurer) {
            DB::table('coverripart')->updateOrInsert(
                [
                    'endorsement_no' => $reinsurer->endorsementNo,
                    'cover_no' => $reinsurer->coverNo,
                    'partner_code' => $reinsurer->partnerCode,
                ],
                [
                    'share' => $reinsurer->share,
                    'premium' => $reinsurer['gross_amount'],
                    'total_premium' => $reinsurer['gross_amount'],
                    'commission' => $reinsurer['commission_amount'],
                    'brokerage_comm_rate' => $reinsurer['brokerage_rate'],
                    'brokerage_comm_amt' => $reinsurer['brokerage_amount'],
                    'prem_tax' => $reinsurer['premium_tax'],
                    'ri_tax' => $reinsurer['reinsurance_tax'],
                    'wht_amt' => $reinsurer['withholding_tax'],
                    'net_amount' => $reinsurer['net_amount'],
                    'updated_at' => now(),
                ]
            );
        }
    }

    protected function applyFilters($query, array $filters): void
    {
        if (! empty($filters['year'])) {
            $query->where('posting_year', $filters['year']);
        }

        if (! empty($filters['quarter'])) {
            $query->where('posting_quarter', $filters['quarter']);
        }

        if (! empty($filters['from_date'])) {
            $query->whereDate('posting_date', '>=', $filters['from_date']);
        }

        if (! empty($filters['to_date'])) {
            $query->whereDate('posting_date', '<=', $filters['to_date']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['type_of_bus'])) {
            $query->where('type_of_bus', $filters['type_of_bus']);
        }
    }

    protected function getStatusBreakdown(array $filters): array
    {
        $query = DebitNote::query();
        $this->applyFilters($query, $filters);

        return $query
            ->selectRaw('status, COUNT(*) as count, SUM(gross_amount) as gross_amount, SUM(net_amount) as net_amount')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->status => [
                    'count' => $item->count,
                    'gross_amount' => round((float) $item->gross_amount, 2),
                    'net_amount' => round((float) $item->net_amount, 2),
                ],
            ])
            ->toArray();
    }

    protected function getCurrentQuarter(): int
    {
        return (int) ceil(now()->month / 3);
    }
}
