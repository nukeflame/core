<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\CoverRegister;
use App\Models\CreditNote;
use App\Repositories\CoverRepository;
use App\Services\DebitNote\AmountCalculator;
use App\Services\DebitNote\LineItemProcessor;
use App\Services\DebitNote\StatusManager;
use Illuminate\Support\Facades\DB;

class CreditNoteService
{
    public const TYPE_FAC = SequenceService::BUSINESS_TYPE_FAC;
    public const TYPE_TREATY = SequenceService::BUSINESS_TYPE_TREATY;
    public const TYPE_RETRO = SequenceService::BUSINESS_TYPE_RETRO;

    public const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];
    public const CREDIT_CODES = ['IT02', 'IT03', 'IT04', 'IT05', 'IT06', 'IT07', 'IT08', 'IT10', 'IT21', 'IT27', 'IT29', 'IT30'];
    private const LEDGER_CREDIT = 'CR';
    public function __construct(
        private readonly SequenceService $sequenceService,
        private readonly CoverRepository $coverRepository,
        private readonly AmountCalculator $amountCalculator,
        private readonly LineItemProcessor $lineItemProcessor,
        private readonly StatusManager $statusManager,
    ) {}

    public function generateCreditNoteNumber(string $businessType, ?int $year = null): string
    {
        $typeOfBus = match (true) {
            $this->coverRepository->isTreatyBusiness($businessType) => 'TREATY',
            $this->coverRepository->isFacultativeBusiness($businessType) => 'FAC',
            default => 'RETRO',
        };

        return (string) $this->sequenceService->generateCreditNoteNumber($typeOfBus, $year)->credit_no;
    }

    public function parseCreditNoteNumber(string $creditNoteNo): array
    {
        return $this->sequenceService->parseNoteNumber($creditNoteNo);
    }

    public function create(array $data, CoverRegister $cover): array
    {
        return DB::transaction(function () use ($data, $cover) {
            $this->validateCreateData($data);

            $calculation = $this->amountCalculator->calculate($data, $cover, self::LEDGER_CREDIT);
            $creditNotes = [];

            if (!empty($calculation)) {
                foreach ($calculation as $reinsurer) {
                    $creditNoteNo = $this->generateCreditNoteNumber(
                        $cover->type_of_bus,
                        $data['postingYear'] ?? now()->year
                    );
                    $creditNote = $this->createCreditNoteRecord(
                        $creditNoteNo,
                        $data,
                        $cover,
                        $reinsurer
                    );

                    $this->lineItemProcessor->createCreditNoteLineItems($creditNote, $reinsurer['items']);
                    $creditNotes[] = $creditNote;
                }
            }

            $this->updateReinsurerParticipation($calculation);
            return $creditNotes;
        });
    }

    public function update(CreditNote $creditNote, array $data): CreditNote
    {
        return DB::transaction(function () use ($creditNote, $data) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->isEditable()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $creditNote->status,
                    'EDIT'
                );
            }

            $oldValues = $creditNote->toArray();

            $calculation = $this->amountCalculator->calculate($data, $creditNote->cover, self::LEDGER_CREDIT);

            $this->updateCreditNoteRecord($creditNote, $data, $calculation);

            if (isset($data['items'])) {
                $this->lineItemProcessor->replaceCreditNoteLineItems($creditNote, $data['items']);
            }

            return $creditNote->fresh(['items', 'cover']);
        });
    }

    // public function submit(CreditNote $creditNote): CreditNote
    // {
    //     return $this->statusManager->submit($creditNote);
    // }

    // public function approve(CreditNote $creditNote): CreditNote
    // {
    //     return $this->statusManager->approve($creditNote);
    // }

    // public function reject(CreditNote $creditNote, string $reason): CreditNote
    // {
    //     return $this->statusManager->reject($creditNote, $reason);
    // }

    // public function revertToDraft(CreditNote $creditNote): CreditNote
    // {
    //     return $this->statusManager->revertToDraft($creditNote);
    // }

    // public function cancel(CreditNote $creditNote, string $reason): CreditNote
    // {
    //     return $this->statusManager->cancel($creditNote, $reason);
    // }

    public function post(CreditNote $creditNote): CreditNote
    {
        return DB::transaction(function () use ($creditNote) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->canPost()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $creditNote->status,
                    CreditNote::STATUS_POSTED
                );
            }

            $oldValues = $creditNote->toArray();

            $creditNote->update([
                'status' => CreditNote::STATUS_POSTED,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            // Create accounting entries
            // TODO: Implement accounting integration
            // $this->accountingService->createEntries($creditNote);

            return $creditNote->fresh();
        });
    }

    public function delete(CreditNote $creditNote): bool
    {
        return DB::transaction(function () use ($creditNote) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->isEditable()) {
                throw BusinessRuleException::invalidStatusTransition(
                    $creditNote->status,
                    'DELETE'
                );
            }

            $oldValues = $creditNote->toArray();

            $creditNote->items()->delete();
            $creditNote->delete();

            return true;
        });
    }

    public function duplicate(CreditNote $original): CreditNote
    {
        return DB::transaction(function () use ($original) {
            $cover = $original->cover;

            if (!$cover) {
                throw new \RuntimeException('Original credit note has no associated cover');
            }

            $newData = $this->prepareDuplicateData($original);

            return $this->create($newData, $cover);
        });
    }

    public function getStatistics(array $filters = []): array
    {
        $query = CreditNote::query();

        $this->applyFilters($query, $filters);

        return [
            'total_count' => $query->count(),
            'total_gross' => round((float) $query->sum('gross_amount'), 2),
            'total_net' => round((float) $query->sum('net_amount'), 2),
            'by_status' => $this->getStatusBreakdown($filters),
            'pending_count' => CreditNote::pending()->count(),
            'approved_count' => CreditNote::approved()->count(),
            'posted_count' => CreditNote::posted()->count(),
        ];
    }

    public function calculateAmounts(array $data, ?CoverRegister $cover = null): array
    {
        return $this->amountCalculator->calculate($data, $cover, self::LEDGER_CREDIT);
    }

    protected function validateCreateData(array $data): void
    {
        $required = ['postingDate', 'postingYear', 'postingQuarter', 'items'];

        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if (empty($data['items'])) {
            throw BusinessRuleException::missingItems();
        }
    }

    protected function createCreditNoteRecord(
        string $creditNoteNo,
        array $data,
        CoverRegister $cover,
        array $calculation
    ): CreditNote {

        return CreditNote::create([
            'credit_note_no' => $creditNoteNo,
            'cover_no' => $cover->cover_no,
            'endorsement_no' => $cover->endorsement_no,
            'type_of_bus' => $cover->type_of_bus,
            'type' => $data['entryTypeDescr'] ?? null,
            'installment_no' => $data['installment'] ?? $this->getNextInstallment($cover),
            'reinsurer_posting' => $data['reinsurerPosting'] ?? null,
            'premium_pay_terms' => $cover->premium_payment_code,
            'posting_year' => $data['postingYear'],
            'posting_quarter' => $data['postingQuarter'],
            'posting_date' => $data['postingDate'],
            'currency' => $data['currencyCode'] ?? ($cover->currency_code ?? 'KES'),
            'exchange_rate' => $data['currencyRate'] ?? ($cover->currency_rate ?? 1),
            'brokerage_rate' => $data['brokerageRate'] ?? 0,
            'gross_amount' => $calculation['gross_amount'],
            'commission_amount' => $calculation['commission_amount'],
            'brokerage_amount' => $calculation['brokerage_amount'],
            'premium_levy' => $calculation['premium_tax'],
            'reinsurance_levy' => $calculation['reinsurance_tax'],
            'withholding_tax' => $calculation['withholding_tax'],
            'other_deductions' => $calculation['other_deductions'],
            'total_deductions' => $calculation['total_deductions'],
            'net_amount' => $calculation['net_amount'],
            'comments' => $data['comments'] ?? null,
            'show_cedant' => $data['showCedant'] ?? false,
            'show_reinsurer' => $data['showReinsurer'] ?? false,
            'loss_participation' => $data['lossParticipation'] ?? false,
            'sliding_commission' => $data['slidingCommission'] ?? false,
            'reinsurer_id' => $calculation['partner_code'] ?? null,
            'status' => CreditNote::STATUS_DRAFT,
            'created_by' => auth()->id(),
        ]);
    }

    protected function updateCreditNoteRecord(
        CreditNote $creditNote,
        array $data,
        array $calculation
    ): void {
        $financialData = $calculation['cedant'] ?? $calculation;

        $creditNote->update([
            'posting_year' => $data['postingYear'] ?? $creditNote->posting_year,
            'posting_quarter' => $data['postingQuarter'] ?? $creditNote->posting_quarter,
            'posting_date' => $data['postingDate'] ?? $creditNote->posting_date,
            'brokerage_rate' => $data['brokerageRate'] ?? $creditNote->brokerage_rate,

            // Financial amounts
            'gross_amount' => $financialData['gross_amount'] ?? $creditNote->gross_amount,
            'credit_amount' => $financialData['credit_amount'] ?? $creditNote->credit_amount,
            'commission_amount' => $financialData['commission_amount'] ?? $creditNote->commission_amount,
            'brokerage_amount' => $financialData['brokerage_amount'] ?? $creditNote->brokerage_amount,

            // Taxes
            'premium_levy' => $financialData['premium_tax'] ?? $creditNote->premium_levy,
            'reinsurance_levy' => $financialData['reinsurance_tax'] ?? $creditNote->reinsurance_levy,
            'withholding_tax' => $financialData['withholding_tax'] ?? $creditNote->withholding_tax,
            'other_deductions' => $financialData['other_deductions'] ?? $creditNote->other_deductions,
            'total_deductions' => $financialData['total_deductions'] ?? $creditNote->total_deductions,
            'net_amount' => $financialData['net_amount'] ?? $creditNote->net_amount,

            'comments' => $data['comments'] ?? $creditNote->comments,
            'updated_by' => auth()->id(),
        ]);
    }

    protected function getNextInstallment(CoverRegister $cover): int
    {
        $lastInstallment = CreditNote::where('cover_no', $cover->cover_no)
            ->where('endorsement_no', $cover->endorsement_no)
            ->whereNotIn('status', [CreditNote::STATUS_CANCELLED])
            ->max('installment_no');

        return ($lastInstallment ?? 0) + 1;
    }

    protected function prepareDuplicateData(CreditNote $original): array
    {
        return [
            'postingDate' => now()->toDateString(),
            'postingYear' => now()->year,
            'postingQuarter' => $this->getCurrentQuarter(),
            'brokerageRate' => $original->brokerage_rate,
            'reinsurerPosting' => $original->reinsurer_posting,
            'comments' => 'Duplicated from: ' . $original->credit_note_no,
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
                    'endorsement_no' => $reinsurer['endorsement_no'],
                    'cover_no' => $reinsurer['cover_no'],
                ],
                [
                    'share' => $reinsurer['share'],
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
        if (!empty($filters['year'])) {
            $query->where('posting_year', $filters['year']);
        }

        if (!empty($filters['quarter'])) {
            $query->where('posting_quarter', $filters['quarter']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('posting_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('posting_date', '<=', $filters['to_date']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type_of_bus'])) {
            $query->where('type_of_bus', $filters['type_of_bus']);
        }
    }

    protected function getStatusBreakdown(array $filters): array
    {
        $query = CreditNote::query();
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
