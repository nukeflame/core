<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\CoverRegister;
use App\Models\CoverRipart;
use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\TransactionLog;
use App\Repositories\CoverRepository;
use Illuminate\Support\Facades\DB;

class DebitNoteService
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

    public function generateDebitNoteNumber(string $businessType, ?int $year = null): string
    {
        $typeOfBus = 'RETRO';
        if ($this->coverRepository->isTreatyBusiness($businessType)) {
            $typeOfBus = 'TREATY';
        } elseif ($this->coverRepository->isFacultativeBusiness($businessType)) {
            $typeOfBus = 'FAC';
        }


        return (string) $this->sequenceService->generateDebitNoteNumber($typeOfBus, $year)->debit_no;
    }

    public function parseDebitNoteNumber(string $debitNoteNo): array
    {
        return $this->sequenceService->parseNoteNumber($debitNoteNo);
    }

    public function create(array $data, CoverRegister $cover): DebitNote
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
                        'updated_at'            => now(),
                    ]
                );
            }

            $debitNoteNo = $this->generateDebitNoteNumber(
                $cover->type_of_bus,
                $data['postingYear']
            );

            $ppw = $cover->premium_payment_code;

            $debitNote = DebitNote::create([
                'debit_note_no' => $debitNoteNo,
                'cover_no' => $cover->cover_no,
                'endorsement_no' => $cover->endorsement_no,
                'type_of_bus' => $data['typeOfBus'],
                'installment_no' => $data['installment'] ?? $this->getNextInstallment($cover),
                'reinsurer_posting' => $data['reinsurerPosting'],
                'premium_pay_terms' => $ppw,
                'posting_year' => $data['postingYear'],
                'posting_quarter' => $data['postingQuarter'],
                'posting_date' => $data['postingDate'],
                'brokerage_rate' => $data['brokerageRate'] ?? 0,
                'brokerage_amount' => $amounts['brokerage_amount'],
                'premium_tax' => $amounts['premium_tax'],
                'reinsurance_tax' => $amounts['reinsurance_tax'],
                'withholding_tax' => $amounts['withholding_tax'],
                'gross_amount' => $amounts['gross_amount'],
                'net_amount' => $amounts['net_amount'],
                'comments' => $data['comments'] ?? null,
                'show_cedant' => $data['showCedant'] ?? false,
                'show_reinsurer' => $data['showReinsurer'] ?? false,
                'loss_participation' => $data['lossParticipation'] ?? false,
                'sliding_commission' => $data['slidingCommission'] ?? false,
                'status' => DebitNote::STATUS_DRAFT,
                'created_by' => auth()->id(),
                // 'premium_levy' => $data['premium_levy']
            ]);

            $this->createLineItems($debitNote, $data['items']);

            $this->logTransaction($debitNote, 'CREATE');

            return $debitNote->fresh(['items']);
        });
    }

    public function approve(DebitNote $debitNote): DebitNote
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (!$debitNote->canApprove()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $debitNote->status,
                //     DebitNote::STATUS_APPROVED
                // );
            }

            // Prevent self-approval
            // if ($debitNote->submitted_by === auth()->id()) {
            //     throw BusinessRuleException::selfApproval();
            // }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            $this->logTransaction($debitNote, 'APPROVE', $oldValues);

            return $debitNote->fresh();
        });
    }

    public function reject(DebitNote $debitNote, string $reason): DebitNote
    {
        return DB::transaction(function () use ($debitNote, $reason) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (!$debitNote->canApprove()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $debitNote->status,
                //     DebitNote::STATUS_REJECTED
                // );
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_REJECTED,
                'rejected_at' => now(),
                'rejected_by' => auth()->id(),
                'rejection_reason' => $reason,
            ]);

            $this->logTransaction($debitNote, 'REJECT', $oldValues);

            return $debitNote->fresh();
        });
    }

    public function cancel(DebitNote $debitNote, string $reason): DebitNote
    {
        return DB::transaction(function () use ($debitNote, $reason) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (!$debitNote->canCancel()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $debitNote->status,
                //     DebitNote::STATUS_CANCELLED
                // );
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
                'cancellation_reason' => $reason,
            ]);

            $this->logTransaction($debitNote, 'CANCEL', $oldValues);

            return $debitNote->fresh();
        });
    }

    protected function getNextInstallment(CoverRegister $cover): int
    {
        $lastInstallment = DebitNote::where('cover_no', $cover->cover_no)
            ->where('endorsement_no', $cover->endorsement_no)
            ->whereNotIn('status', [DebitNote::STATUS_CANCELLED])
            ->max('installment_no');

        return ($lastInstallment ?? 0) + 1;
    }

    public function revertToDraft(DebitNote $debitNote): DebitNote
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if ($debitNote->status !== DebitNote::STATUS_REJECTED) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $debitNote->status,
                //     DebitNote::STATUS_DRAFT
                // );
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_DRAFT,
                'rejected_at' => null,
                'rejected_by' => null,
                'rejection_reason' => null,
            ]);

            $this->logTransaction($debitNote, 'REVERT_DRAFT', $oldValues);

            return $debitNote->fresh();
        });
    }

    public function post(DebitNote $debitNote): DebitNote
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (!$debitNote->canPost()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $debitNote->status,
                //     DebitNote::STATUS_POSTED
                // );
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_POSTED,
                'posted_at' => now(),
                'posted_by' => auth()->id(),
            ]);

            // Create accounting entries here if needed
            // $this->createAccountingEntries($debitNote);

            $this->logTransaction($debitNote, 'POST', $oldValues);

            return $debitNote->fresh();
        });
    }

    public function getStatistics(array $filters = []): array
    {
        $query = DebitNote::query();

        // Apply filters
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

        // By status
        $byStatus = DebitNote::query()
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
            'pending_count' => DebitNote::pending()->count(),
            'approved_count' => DebitNote::approved()->count(),
        ];
    }

    public function duplicate(DebitNote $original): DebitNote
    {
        return DB::transaction(function () use ($original) {
            $cover = CoverRegister::find($original->cover_no);

            $newData = $original->toArray();
            unset($newData['id'], $newData['debit_note_no'], $newData['created_at'], $newData['updated_at']);

            $newData['installment_no'] = $this->getNextInstallment($cover);
            $newData['posting_date'] = now()->toDateString();
            $newData['posting_year'] = now()->year;
            // $newData['posting_quarter'] = $this->getQuarterFromMonth(now()->month);

            // Get items
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

                if ($ledger === 'DR') {
                    // Debit items (income)
                    $reinsurerAmounts['gross'] += $sharedAmount;
                    $reinsurerAmounts['commission'] += $commission;
                    $reinsurerAmounts['brokerage'] += $brokerage;
                } else {
                    // Credit items (deductions)
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

            // $reinsurerAmounts['reinsurance_tax'] = $this->taxService->calculateReinsuranceLevy(
            //     $reinsurerAmounts['gross']
            // );
            // $reinsurerAmounts['withholding_tax'] = $this->taxService->calculateWithholdingTax(
            //     $reinsurerAmounts['gross']
            // );

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

    protected function createLineItems(DebitNote $debitNote, array $items): void
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
            $itemNo = 'ITM-' . date('Y') . '-' . str_pad($lineNo, 4, '0', STR_PAD_LEFT);
            $netAmount = '0.00';

            $insertData[] = [
                'debit_note_id' => $debitNote->id,
                'line_no' => $lineNo++,
                'item_code' => $itemCode,
                'item_no' => $itemNo,
                'status' => DebitNote::STATUS_POSTED,
                'description' => $item['description'] ?? $itemCode,
                'class_group_code' => $item['class_group'] ?? null,
                'class_code' => $item['class_name'] ?? null,
                'line_rate' => $item['line_rate'] ?? null,
                'ledger' => $ledger,
                'amount' => $amount,
                'net_amount' => $netAmount,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            DebitNoteItem::insert($insertData);
        }
    }

    public function determineLedger(?string $itemCode): string
    {
        if (empty($itemCode)) {
            return 'CR';
        }
        return in_array($itemCode, self::DEBIT_CODES) ? 'DR' : 'CR';
    }

    public function update(DebitNote $debitNote, array $data): DebitNote
    {
        return DB::transaction(function () use ($debitNote, $data) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (!$debitNote->isEditable()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $debitNote->status,
                //     'EDIT'
                // );
            }

            // Store old values for audit
            $oldValues = $debitNote->toArray();

            // Validate items
            if (isset($data['items'])) {
                // $this->validateItems($data['items']);
            }

            // Recalculate amounts
            $amounts = $this->calculateAmounts($data);

            // Update debit note
            $debitNote->update([
                'posting_year' => $data['posting_year'] ?? $debitNote->posting_year,
                'posting_quarter' => $data['posting_quarter'] ?? $debitNote->posting_quarter,
                'posting_date' => $data['posting_date'] ?? $debitNote->posting_date,
                'gross_amount' => $amounts['gross_amount'],
                'commission_rate' => $amounts['commission_rate'],
                'commission_amount' => $amounts['commission_amount'],
                'brokerage_rate' => $amounts['brokerage_rate'],
                'brokerage_amount' => $amounts['brokerage_amount'],
                'premium_levy' => $amounts['premium_levy'],
                'reinsurance_levy' => $amounts['reinsurance_levy'],
                'withholding_tax' => $amounts['withholding_tax'],
                'other_deductions' => $amounts['other_deductions'],
                'net_amount' => $amounts['net_amount'],
                'compute_premium_tax' => $data['compute_premium_tax'] ?? $debitNote->compute_premium_tax,
                'compute_reinsurance_tax' => $data['compute_reinsurance_tax'] ?? $debitNote->compute_reinsurance_tax,
                'compute_withholding_tax' => $data['compute_withholding_tax'] ?? $debitNote->compute_withholding_tax,
                'comments' => $data['comments'] ?? $debitNote->comments,
                'updated_by' => auth()->id(),
            ]);

            // Recreate line items
            if (isset($data['items'])) {
                $debitNote->items()->delete();
                $this->createLineItems($debitNote, $data['items']);
            }

            // Log transaction
            $this->logTransaction($debitNote, 'UPDATE', $oldValues);

            return $debitNote->fresh(['items']);
        });
    }

    public function delete(DebitNote $debitNote): bool
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (!$debitNote->isEditable()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $debitNote->status,
                //     'DELETE'
                // );
            }

            $oldValues = $debitNote->toArray();

            // Delete items
            $debitNote->items()->delete();

            // Soft delete
            $debitNote->delete();

            // Log transaction
            $this->logTransaction($debitNote, 'DELETE', $oldValues);

            return true;
        });
    }

    public function submit(DebitNote $debitNote): DebitNote
    {
        return DB::transaction(function () use ($debitNote) {
            $debitNote = DebitNote::lockForUpdate()->findOrFail($debitNote->id);

            if (!$debitNote->canSubmit()) {
                // throw BusinessRuleException::invalidStatusTransition(
                //     $debitNote->status,
                //     DebitNote::STATUS_PENDING
                // );
            }

            // Ensure has items
            if ($debitNote->items()->count() === 0) {
                // throw BusinessRuleException::missingItems();
            }

            $oldValues = $debitNote->toArray();

            $debitNote->update([
                'status' => DebitNote::STATUS_PENDING,
                'submitted_at' => now(),
                'submitted_by' => auth()->id(),
            ]);

            $this->logTransaction($debitNote, 'SUBMIT', $oldValues);

            return $debitNote->fresh();
        });
    }

    protected function logTransaction(DebitNote $debitNote, string $action, ?array $oldValues = null): void
    {
        try {
            TransactionLog::create([
                'entity_type' => 'debit_note',
                'entity_id' => $debitNote->id,
                'action' => $action,
                'old_values' => $oldValues,
                'new_values' => $debitNote->fresh()->toArray(),
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
        }
    }
}
