<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\CoverRegister;
use App\Models\CoverRipart;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\TransactionLog;
use App\Repositories\CoverRepository;
use Illuminate\Support\Facades\DB;

class CreditNoteService
{
    public const TYPE_FAC = SequenceService::BUSINESS_TYPE_FAC;
    public const TYPE_TREATY = SequenceService::BUSINESS_TYPE_TREATY;
    public const TYPE_RETRO = SequenceService::BUSINESS_TYPE_RETRO;

    // Debit item codes
    public const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];

    // Credit item codes
    public const CREDIT_CODES = ['IT02', 'IT03', 'IT04', 'IT05', 'IT06', 'IT07', 'IT08', 'IT10', 'IT21', 'IT27', 'IT29', 'IT30'];

    private SequenceService $sequenceService;
    private CoverRepository $coverRepository;
    private TaxCalculationService $taxService;

    public function __construct(SequenceService $sequenceService, CoverRepository $coverRepository, TaxCalculationService $taxService)
    {
        $this->sequenceService = $sequenceService;
        $this->coverRepository = $coverRepository;
        $this->taxService = $taxService;
    }

    public function generateCreditNoteNumber(string $businessType, ?int $year = null): string
    {
        $typeOfBus = 'RETRO';
        if ($this->coverRepository->isTreatyBusiness($businessType)) {
            $typeOfBus = 'TREATY';
        } elseif ($this->coverRepository->isFacultativeBusiness($businessType)) {
            $typeOfBus = 'FAC';
        }

        return (string) $this->sequenceService->generateCreditNoteNumber($typeOfBus, $year)->credit_no;
    }

    public function parseCreditNoteNumber(string $creditNoteNo): array
    {
        return $this->sequenceService->parseNoteNumber($creditNoteNo);
    }

    public function create(array $data, CoverRegister $cover): CreditNote
    {
        return DB::transaction(function () use ($data, $cover) {
            $amounts = $this->calculateAmounts($data, $cover);

            foreach ($amounts['reinsurers'] as $re) {
                DB::table('coverripart')->updateOrInsert(
                    [
                        'endorsement_no' => $re['endorsement_no'],
                        'cover_no' => $re['cover_no'],
                    ],
                    [
                        'share'                => $re['share'],
                        'premium'              => $re['gross_amount'],
                        'total_premium'        => $re['gross_amount'],
                        'commission'           => $re['commission_amount'],
                        'brokerage_comm_rate'  => $re['brokerage_rate'],
                        'brokerage_comm_amt'   => $re['brokerage_amount'],
                        'prem_tax'             => $re['premium_tax'],
                        'ri_tax'               => $re['reinsurance_tax'],
                        'wht_amt'              => $re['withholding_tax'],
                        'net_amount'           => $re['net_amount'],
                        'updated_at'           => now(),
                    ]
                );
            }

            $creditNoteNo = $this->generateCreditNoteNumber(
                $cover->type_of_bus,
                $data['postingYear']
            );

            $ppw = $cover->premium_payment_code;

            $creditNote = CreditNote::create([
                'credit_note_no' => $creditNoteNo,
                'cover_no' => $cover->cover_no,
                'endorsement_no' => $cover->endorsement_no,
                'type_of_bus' => $data['typeOfBus'],
                'installment_no' => $data['installment'] ?? $this->getNextInstallment($cover),
                'posting_year' => $data['postingYear'],
                'posting_quarter' => $data['postingQuarter'],
                'posting_date' => $data['postingDate'],
                'brokerage_rate' => $data['brokerageRate'] ?? 0,
                'brokerage_amount' => $amounts['brokerage_amount'],
                'premium_levy' => $amounts['premium_tax'],
                'reinsurance_levy' => $amounts['reinsurance_tax'],
                'withholding_tax' => $amounts['withholding_tax'],
                'gross_amount' => $amounts['gross_amount'],
                'net_amount' => $amounts['net_amount'],
                'comments' => $data['comments'] ?? null,
                'show_cedant' => $data['showCedant'] ?? false,
                'show_reinsurer' => $data['showReinsurer'] ?? false,
                'loss_participation' => $data['lossParticipation'] ?? false,
                'sliding_commission' => $data['slidingCommission'] ?? false,
                'status' => CreditNote::STATUS_DRAFT,
                'created_by' => auth()->id(),
            ]);

            $this->createLineItems($creditNote, $data['items']);

            $this->logTransaction($creditNote, 'CREATE');

            return $creditNote->fresh(['items']);
        });
    }

    public function approve(CreditNote $creditNote): CreditNote
    {
        return DB::transaction(function () use ($creditNote) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->canApprove()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $creditNote->status,
                //     CreditNote::STATUS_APPROVED
                // );
            }

            $oldValues = $creditNote->toArray();

            $creditNote->update([
                'status' => CreditNote::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            $this->logTransaction($creditNote, 'APPROVE', $oldValues);

            return $creditNote->fresh();
        });
    }

    public function reject(CreditNote $creditNote, string $reason): CreditNote
    {
        return DB::transaction(function () use ($creditNote, $reason) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->canApprove()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $creditNote->status,
                //     CreditNote::STATUS_REJECTED
                // );
            }

            $oldValues = $creditNote->toArray();

            $creditNote->update([
                'status' => CreditNote::STATUS_REJECTED,
                'rejected_at' => now(),
                'rejected_by' => auth()->id(),
                'rejection_reason' => $reason,
            ]);

            $this->logTransaction($creditNote, 'REJECT', $oldValues);

            return $creditNote->fresh();
        });
    }

    public function cancel(CreditNote $creditNote, string $reason): CreditNote
    {
        return DB::transaction(function () use ($creditNote, $reason) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->canCancel()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $creditNote->status,
                //     CreditNote::STATUS_CANCELLED
                // );
            }

            $oldValues = $creditNote->toArray();

            $creditNote->update([
                'status' => CreditNote::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
                'cancellation_reason' => $reason,
            ]);

            $this->logTransaction($creditNote, 'CANCEL', $oldValues);

            return $creditNote->fresh();
        });
    }

    protected function getNextInstallment(CoverRegister $cover): int
    {
        $lastInstallment = CreditNote::where('cover_no', $cover->cover_no)
            ->where('endorsement_no', $cover->endorsement_no)
            ->whereNotIn('status', [CreditNote::STATUS_CANCELLED])
            ->max('installment_no');

        return ($lastInstallment ?? 0) + 1;
    }

    public function revertToDraft(CreditNote $creditNote): CreditNote
    {
        return DB::transaction(function () use ($creditNote) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if ($creditNote->status !== CreditNote::STATUS_REJECTED) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $creditNote->status,
                //     CreditNote::STATUS_DRAFT
                // );
            }

            $oldValues = $creditNote->toArray();

            $creditNote->update([
                'status' => CreditNote::STATUS_DRAFT,
                'rejected_at' => null,
                'rejected_by' => null,
                'rejection_reason' => null,
            ]);

            $this->logTransaction($creditNote, 'REVERT_DRAFT', $oldValues);

            return $creditNote->fresh();
        });
    }

    public function post(CreditNote $creditNote): CreditNote
    {
        return DB::transaction(function () use ($creditNote) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->canPost()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $creditNote->status,
                //     CreditNote::STATUS_POSTED
                // );
            }

            $oldValues = $creditNote->toArray();

            $creditNote->update([
                'status' => CreditNote::STATUS_POSTED,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            $this->logTransaction($creditNote, 'POST', $oldValues);

            return $creditNote->fresh();
        });
    }

    public function getStatistics(array $filters = []): array
    {
        $query = CreditNote::query();

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

        $total = $query->count();
        $totalGross = $query->sum('gross_amount');
        $totalNet = $query->sum('net_amount');

        $byStatus = CreditNote::query()
            ->when(!empty($filters['year']), fn($q) => $q->where('posting_year', $filters['year']))
            ->selectRaw('status, COUNT(*) as count, SUM(gross_amount) as gross')
            ->groupBy('status')
            ->pluck('gross', 'status')
            ->toArray();

        return [
            'total_count' => $total,
            'total_gross' => round((float) $totalGross, 2),
            'total_net' => round((float) $totalNet, 2),
            'by_status' => $byStatus,
            'pending_count' => CreditNote::pending()->count(),
            'approved_count' => CreditNote::approved()->count(),
        ];
    }

    public function duplicate(CreditNote $original): CreditNote
    {
        return DB::transaction(function () use ($original) {
            $cover = CoverRegister::find($original->cover_no);

            $newData = $original->toArray();
            unset($newData['id'], $newData['credit_note_no'], $newData['created_at'], $newData['updated_at']);

            $newData['installment_no'] = $this->getNextInstallment($cover);
            $newData['posting_date'] = now()->toDateString();
            $newData['posting_year'] = now()->year;

            $newData['items'] = $original->items->map(fn($item) => [
                'item_code' => $item->item_code,
                'description' => $item->description,
                'class_group' => $item->class_group_code,
                'class_name' => $item->class_code,
                'line_rate' => $item->line_rate,
                'ledger' => $item->ledger,
                'amount' => $item->amount,
            ])->toArray();

            return $this->create($newData, $cover);
        });
    }

    public function calculateAmounts(array $data, ?CoverRegister $cover = null): array
    {
        $items = $data['items'] ?? [];
        $brokerageRate = (float) ($data['brokerageRate'] ?? 0);

        $reinsurers = CoverRipart::with('partner')
            ->where([
                'cover_no' => $cover->cover_no,
                'endorsement_no' => $cover->endorsement_no
            ])
            ->get();

        $totals = [
            'gross' => 0,
            'credit' => 0,
            'commission' => 0,
            'brokerage' => 0,
            'premium_tax' => 0,
            'reinsurance_tax' => 0,
            'withholding_tax' => 0,
            'other_deductions' => 0,
            'net' => 0,
        ];

        $reinsurerBreakdown = [];

        foreach ($reinsurers as $reinsurer) {
            $share = (float) ($reinsurer->share ?? 0);
            $amountType = strtolower($reinsurer->amount_type ?? 'gross');

            if ($share == 0) {
                continue;
            }

            $reinsurerAmounts = [
                'gross' => 0,
                'credit' => 0,
                'commission' => 0,
                'brokerage' => 0,
                'premium_tax' => 0,
                'reinsurance_tax' => 0,
                'withholding_tax' => 0,
            ];

            foreach ($items as $item) {
                if (!isset($item['amount'])) {
                    continue;
                }

                $amount = (float) $item['amount'];
                $ledger = $item['ledger'] ?? $this->determineLedger(
                    $item['item_code'] ?? $item['description'] ?? ''
                );
                $commissionRate = (float) ($item['line_rate'] ?? 0);

                $sharedAmount = $this->calculatePercentage($amount, $share);
                $commission = $this->calculatePercentage($sharedAmount, $commissionRate);
                $brokerage = $this->calculatePercentage($sharedAmount, $brokerageRate);

                // For credit notes, default ledger is CR
                if ($ledger === 'CR') {
                    // Credit items (main amounts for credit notes)
                    $reinsurerAmounts['gross'] += $sharedAmount;
                    $reinsurerAmounts['commission'] += $commission;
                    $reinsurerAmounts['brokerage'] += $brokerage;
                } else {
                    // Debit items (deductions for credit notes)
                    $reinsurerAmounts['credit'] += $sharedAmount;
                }
            }

            if ($amountType === 'net') {
                $netPremium = $reinsurerAmounts['gross']
                    - $reinsurerAmounts['commission']
                    - $reinsurerAmounts['brokerage'];

                $reinsurerAmounts['premium_tax'] = $this->taxService->calculatePremiumLevy($netPremium);
            } else {
                $reinsurerAmounts['premium_tax'] = $this->taxService->calculatePremiumLevy(
                    $reinsurerAmounts['gross']
                );
            }

            $reinsurerOtherDeductions = $reinsurerAmounts['credit'];

            $totalDeductions =
                $reinsurerAmounts['commission'] +
                $reinsurerAmounts['brokerage'] +
                $reinsurerAmounts['premium_tax'] +
                $reinsurerAmounts['reinsurance_tax'] +
                $reinsurerAmounts['withholding_tax'] +
                $reinsurerOtherDeductions;

            $netAmount = $reinsurerAmounts['gross'] - $totalDeductions;

            $reinsurerBreakdown[] = [
                'cover_no' => $reinsurer->cover_no,
                'endorsement_no' => $reinsurer->endorsement_no,
                'reinsurer_name' => $reinsurer->partner?->name ?? 'Unknown',
                'share' => round($share, 4),
                'amount_type' => $amountType,
                'gross_amount' => round($reinsurerAmounts['gross'], 2),
                'credit_amount' => round($reinsurerAmounts['credit'], 2),
                'commission_amount' => round($reinsurerAmounts['commission'], 2),
                'brokerage_rate' => $brokerageRate,
                'brokerage_amount' => round($reinsurerAmounts['brokerage'], 2),
                'premium_tax' => round($reinsurerAmounts['premium_tax'], 2),
                'premium_tax_base' => $amountType,
                'reinsurance_tax' => round($reinsurerAmounts['reinsurance_tax'], 2),
                'withholding_tax' => round($reinsurerAmounts['withholding_tax'], 2),
                'other_deductions' => round($reinsurerOtherDeductions, 2),
                'total_deductions' => round($totalDeductions, 2),
                'net_amount' => round($netAmount, 2),
            ];

            $totals['gross'] += $reinsurerAmounts['gross'];
            $totals['credit'] += $reinsurerAmounts['credit'];
            $totals['commission'] += $reinsurerAmounts['commission'];
            $totals['brokerage'] += $reinsurerAmounts['brokerage'];
            $totals['premium_tax'] += $reinsurerAmounts['premium_tax'];
            $totals['reinsurance_tax'] += $reinsurerAmounts['reinsurance_tax'];
            $totals['withholding_tax'] += $reinsurerAmounts['withholding_tax'];
            $totals['other_deductions'] += $reinsurerOtherDeductions;
            $totals['net'] += $netAmount;
        }

        $finalTotalDeductions =
            $totals['commission'] +
            $totals['brokerage'] +
            $totals['premium_tax'] +
            $totals['reinsurance_tax'] +
            $totals['withholding_tax'] +
            $totals['other_deductions'];

        return [
            'gross_amount' => round($totals['gross'], 2),
            'credit_amount' => round($totals['credit'], 2),
            'commission_amount' => round($totals['commission'], 2),
            'brokerage_rate' => $brokerageRate,
            'brokerage_amount' => round($totals['brokerage'], 2),
            'premium_tax' => round($totals['premium_tax'], 2),
            'reinsurance_tax' => round($totals['reinsurance_tax'], 2),
            'withholding_tax' => round($totals['withholding_tax'], 2),
            'other_deductions' => round($totals['other_deductions'], 2),
            'total_deductions' => round($finalTotalDeductions, 2),
            'net_amount' => round($totals['net'], 2),
            'reinsurers' => $reinsurerBreakdown,
        ];
    }

    protected function calculatePercentage(float $amount, float $rate): float
    {
        if ($amount <= 0 || $rate <= 0) {
            return 0;
        }
        return round($amount * ($rate / 100), 2);
    }

    protected function createLineItems(CreditNote $creditNote, array $items): void
    {
        $lineNo = 1;
        $insertData = [];

        foreach ($items as $item) {
            $amount = (float) ($item['amount'] ?? 0);
            if ($amount <= 0 && empty($item['description']) && empty($item['item_code'])) {
                continue;
            }

            $itemCode = $item['item_code'] ?? $item['description'] ?? null;
            $ledger = $item['ledger'] ?? $this->determineLedger($itemCode);
            $itemNo = 'CRN-' . date('Y') . '-' . str_pad($lineNo, 4, '0', STR_PAD_LEFT);
            $netAmount = $item['net_amount'] ?? $amount;

            $insertData[] = [
                'credit_note_id' => $creditNote->id,
                'line_no' => $lineNo++,
                'item_code' => $itemCode,
                'item_no' => $itemNo,
                'status' => CreditNote::STATUS_POSTED,
                'description' => $item['description'] ?? $itemCode,
                'class_group_code' => $item['class_group'] ?? null,
                'class_code' => $item['class_name'] ?? null,
                'line_rate' => $item['line_rate'] ?? null,
                'ledger' => $ledger,
                'amount' => $amount,
                'commission' => $item['commission'] ?? 0,
                'brokerage' => $item['brokerage'] ?? 0,
                'premium_tax' => $item['premium_tax'] ?? 0,
                'net_amount' => $netAmount,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            CreditNoteItem::insert($insertData);
        }
    }

    public function determineLedger(?string $itemCode): string
    {
        if (empty($itemCode)) {
            return 'CR';
        }
        // For credit notes, default to CR unless explicitly a debit code
        return in_array($itemCode, self::DEBIT_CODES) ? 'DR' : 'CR';
    }

    public function update(CreditNote $creditNote, array $data): CreditNote
    {
        return DB::transaction(function () use ($creditNote, $data) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->isEditable()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $creditNote->status,
                //     'EDIT'
                // );
            }

            $oldValues = $creditNote->toArray();

            $amounts = $this->calculateAmounts($data);

            $creditNote->update([
                'posting_year' => $data['posting_year'] ?? $creditNote->posting_year,
                'posting_quarter' => $data['posting_quarter'] ?? $creditNote->posting_quarter,
                'posting_date' => $data['posting_date'] ?? $creditNote->posting_date,
                'gross_amount' => $amounts['gross_amount'],
                'commission_rate' => $amounts['commission_rate'] ?? 0,
                'commission_amount' => $amounts['commission_amount'],
                'brokerage_rate' => $amounts['brokerage_rate'],
                'brokerage_amount' => $amounts['brokerage_amount'],
                'premium_levy' => $amounts['premium_tax'],
                'reinsurance_levy' => $amounts['reinsurance_tax'],
                'withholding_tax' => $amounts['withholding_tax'],
                'other_deductions' => $amounts['other_deductions'],
                'net_amount' => $amounts['net_amount'],
                'compute_premium_tax' => $data['compute_premium_tax'] ?? $creditNote->compute_premium_tax,
                'compute_reinsurance_tax' => $data['compute_reinsurance_tax'] ?? $creditNote->compute_reinsurance_tax,
                'compute_withholding_tax' => $data['compute_withholding_tax'] ?? $creditNote->compute_withholding_tax,
                'comments' => $data['comments'] ?? $creditNote->comments,
                'updated_by' => auth()->id(),
            ]);

            if (isset($data['items'])) {
                $creditNote->items()->delete();
                $this->createLineItems($creditNote, $data['items']);
            }

            $this->logTransaction($creditNote, 'UPDATE', $oldValues);

            return $creditNote->fresh(['items']);
        });
    }

    public function delete(CreditNote $creditNote): bool
    {
        return DB::transaction(function () use ($creditNote) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->isEditable()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $creditNote->status,
                //     'DELETE'
                // );
            }

            $oldValues = $creditNote->toArray();

            $creditNote->items()->delete();
            $creditNote->delete();

            $this->logTransaction($creditNote, 'DELETE', $oldValues);

            return true;
        });
    }

    public function submit(CreditNote $creditNote): CreditNote
    {
        return DB::transaction(function () use ($creditNote) {
            $creditNote = CreditNote::lockForUpdate()->findOrFail($creditNote->id);

            if (!$creditNote->canSubmit()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $creditNote->status,
                //     CreditNote::STATUS_PENDING
                // );
            }

            if ($creditNote->items()->count() === 0) {
                // throw BusinessRuleException::missingItems();
            }

            $oldValues = $creditNote->toArray();

            $creditNote->update([
                'status' => CreditNote::STATUS_PENDING,
                'submitted_at' => now(),
                'submitted_by' => auth()->id(),
            ]);

            $this->logTransaction($creditNote, 'SUBMIT', $oldValues);

            return $creditNote->fresh();
        });
    }

    protected function logTransaction(CreditNote $creditNote, string $action, ?array $oldValues = null): void
    {
        try {
            TransactionLog::create([
                'entity_type' => 'credit_note',
                'entity_id' => $creditNote->id,
                'action' => $action,
                'old_values' => $oldValues,
                'new_values' => $creditNote->fresh()->toArray(),
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
        }
    }
}
