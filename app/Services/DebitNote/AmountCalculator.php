<?php

namespace App\Services\DebitNote;

use App\Models\CoverRegister;
use App\Models\CoverRipart;
use App\Services\TaxCalculationService;
use Illuminate\Support\Collection;

class AmountCalculator
{
    private const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];
    private const CREDIT_CODES = ['IT02', 'IT03', 'IT04', 'IT05', 'IT06', 'IT07', 'IT08', 'IT10', 'IT21', 'IT27', 'IT29', 'IT30'];
    private const PRECISION = 12;
    private const COMMISSION_ITEM_CODE = 'IT03';
    private const TAX_LEVY_ITEM_CODE = 'IT05';

    public function __construct(
        private readonly TaxCalculationService $taxService
    ) {}

    public function calculate(array $data, ?CoverRegister $cover = null): array
    {
        $items = $data['items'] ?? [];
        $brokerageRate = (float) ($data['brokerageRate'] ?? 0);

        $reinsurers = $this->loadReinsurers($cover);
        $reinsurerCalculations = $this->calculateReinsurerShares($items, $reinsurers, $brokerageRate);
        $cedantCalculation = $this->calculateCedantShare($items, $cover, $brokerageRate);
        $totals = $this->aggregateTotals($reinsurerCalculations);

        // logger()->debug(json_encode($reinsurerCalculations, JSON_PRETTY_PRINT));

        return $this->buildDebitNoteResult($totals, $brokerageRate, $reinsurerCalculations, $cedantCalculation);
    }

    protected function calculateReinsurerShares(array $items, Collection $reinsurers, float $brokerageRate): array
    {
        $calculations = [];

        foreach ($reinsurers as $reinsurer) {
            $share = (float) ($reinsurer->share ?? 0);

            if ($share <= 0) {
                continue;
            }

            $calculation = $this->calculateSingleReinsurerShare($items, $reinsurer, $share, $brokerageRate);

            if ($calculation) {
                $calculations[] = $calculation;
            }
        }

        return $calculations;
    }

    protected function calculateSingleReinsurerShare(
        array $items,
        CoverRipart $reinsurer,
        float $sharePercentage,
        float $brokerageRate
    ): array {
        $amountType = strtolower($reinsurer->amount_type ?? 'gross');
        $commissionRate = (float) ($reinsurer->commission_rate ?? 0);
        $commissionMode = strtolower($reinsurer->commission_mode ?? 'gross'); // 'gross' or 'net'

        $amounts = AmountAccumulator::create();

        foreach ($items as $item) {
            $this->processReinsurerLineItem(
                $item,
                $sharePercentage,
                $commissionRate,
                $commissionMode,
                $brokerageRate,
                $amounts
            );
        }

        // Apply taxes BEFORE calculating net (taxes need the gross amount)
        // $this->applyTaxes($amounts, $amountType);

        // Now calculate net amount
        $amounts->calculateNet();

        return $this->buildReinsurerCalculation(
            $reinsurer,
            $sharePercentage,
            $amountType,
            $commissionRate,
            $commissionMode,
            $brokerageRate,
            $amounts
        );
    }

    protected function processReinsurerLineItem(
        array $item,
        float $sharePercentage,
        float $commissionRate,
        string $commissionMode,
        float $brokerageRate,
        AmountAccumulator $amounts
    ): void {
        if (!$this->isValidItem($item)) {
            return;
        }

        $amount = (float) $item['amount'];
        $ledger = $this->getLedgerType($item);
        $lineCommissionRate = (float) ($item['line_rate'] ?? $commissionRate);

        $shareAmount = $this->percentage($amount, $sharePercentage);

        logger()->debug(json_encode($shareAmount, JSON_PRETTY_PRINT));


        if ($ledger === LedgerType::DEBIT) {
            $amounts->addGross($shareAmount);

            // Calculate commission based on mode
            if ($commissionMode === 'net') {
                // Commission on net = gross - brokerage
                $netBase = $shareAmount - $this->percentage($shareAmount, $brokerageRate);
                $commissionAmount = $this->percentage($netBase, $lineCommissionRate);
            } else {
                // Commission on gross (default)
                $commissionAmount = $this->percentage($shareAmount, $lineCommissionRate);
            }

            $amounts->addCommission($commissionAmount);
            $amounts->addBrokerage($this->percentage($shareAmount, $brokerageRate));
        } else {
            $amounts->addCredit($shareAmount);
        }
    }

    protected function calculateCedantShare(
        array $items,
        ?CoverRegister $cover,
        float $brokerageRate
    ): ?array {
        if (!$cover) {
            return null;
        }

        $cedantShare = (float) ($cover->share_offered ?? 0);

        if ($cedantShare <= 0) {
            return null;
        }

        $commissionMode = strtolower($cover->commission_mode ?? 'gross');

        $amounts = AmountAccumulator::create();
        $lineItems = [];
        $lastDebitItem = null;

        foreach ($items as $item) {
            if (!$this->isValidItem($item)) {
                continue;
            }

            $processedItems = $this->processCedantLineItem(
                $item,
                $cedantShare,
                $commissionMode,
                $amounts,
                $lastDebitItem
            );

            $lineItems = array_merge($lineItems, $processedItems);
        }

        $this->distributeTaxesToLineItems($lineItems, $amounts);

        $amounts->setPremiumTax(1);
        $amounts->calculateNet();

        return $this->buildCedantCalculation($cover, $cedantShare, $commissionMode, $brokerageRate, $amounts, $lineItems);
    }

    protected function processCedantLineItem(
        array $item,
        float $cedantShare,
        string $commissionMode,
        AmountAccumulator $amounts,
        ?array &$lastDebitItem
    ): array {
        $amount = (float) $item['amount'];
        $ledger = $this->getLedgerType($item);
        $commissionRate = (float) ($item['line_rate'] ?? 0);

        $shareAmount = $this->percentage($amount, $cedantShare);

        if ($commissionMode === 'net') {
            $netBase = $shareAmount * 0.99;
            $commissionAmount = $this->percentage($netBase, $commissionRate);
        } else {
            $commissionAmount = $this->percentage($shareAmount, $commissionRate);
        }

        $lineItems = [];

        if ($ledger === LedgerType::DEBIT) {
            $lastDebitItem = [
                'original_amount' => $amount,
                'share_amount' => $shareAmount,
            ];

            $lineItems[] = $this->buildLineItem(
                $item,
                $shareAmount,
                $commissionAmount,
                0,
                LedgerType::DEBIT,
                $cedantShare,
                $amount
            );

            if ($commissionAmount > 0) {
                $lineItems[] = $this->buildCommissionLineItem(
                    $item,
                    $commissionAmount,
                    $commissionRate,
                    $shareAmount
                );
            }

            if ($item['item_code'] === 'IT01') {
                $amounts->addGross($shareAmount);
            }

            $amounts->addCommission($commissionAmount);
        } else {
            $originalAmount = ($item['item_code'] === 'IT02') ? $amount : ($lastDebitItem['share_amount'] ?? $shareAmount);
            $creditAmount = $this->calculateCreditAmount($item, $shareAmount, $lastDebitItem);
            $lineRate = $this->calculateLineRate($item, $cedantShare);

            $lineItems[] = $this->buildLineItem(
                $item,
                $creditAmount,
                0,
                0,
                LedgerType::CREDIT,
                $lineRate,
                $originalAmount
            );

            if ($item['item_code'] !== 'IT04') {
                $amounts->addCredit($creditAmount);
            }
        }
        return $lineItems;
    }

    protected function calculateLineRate(array $item, float $cedantShare): float
    {
        if ($item['item_code'] === self::TAX_LEVY_ITEM_CODE) {
            return (float) $item['line_rate'];
        }

        return $cedantShare;
    }

    protected function calculateCreditAmount(array $item, float $shareAmount, ?array $lastDebitItem): float
    {
        if ($item['item_code'] === self::TAX_LEVY_ITEM_CODE && $lastDebitItem) {
            return $this->taxService->calculatePremiumLevy($lastDebitItem['share_amount']);
        }

        return $shareAmount;
    }

    protected function applyTaxes(AmountAccumulator $amounts, string $amountType): void
    {
        $taxBase = $this->determineTaxBase($amounts, $amountType);

        $premiumTax = $this->taxService->calculatePremiumLevy($taxBase);
        $amounts->setPremiumTax($premiumTax);

        $amounts->setReinsuranceTax($this->calculateReinsuranceTax($amounts->gross()));
        $amounts->setWithholdingTax($this->calculateWithholdingTax($amounts->gross()));
    }

    protected function distributeTaxesToLineItems(array &$lineItems, AmountAccumulator $amounts): void
    {
        $taxesDistributed = false;

        foreach ($lineItems as &$lineItem) {
            if ($lineItem['ledger'] === LedgerType::DEBIT && !$taxesDistributed) {
                $lineItem['premium_tax'] = $amounts->premiumTax();
                $lineItem['reinsurance_tax'] = $amounts->reinsuranceTax();
                $lineItem['withholding_tax'] = $amounts->withholdingTax();
                $taxesDistributed = true;
                break;
            }
        }
    }

    protected function aggregateTotals(array $calculations): array
    {
        $totals = AmountAccumulator::create();

        foreach ($calculations as $calc) {
            $totals->addGross($calc['gross_amount']);
            $totals->addCredit($calc['credit_amount'] ?? 0);
            $totals->addCommission($calc['commission_amount']);
            $totals->addBrokerage($calc['brokerage_amount']);
            $totals->setPremiumTax($totals->premiumTax() + $calc['premium_tax']);
            $totals->setReinsuranceTax($totals->reinsuranceTax() + $calc['reinsurance_tax']);
            $totals->setWithholdingTax($totals->withholdingTax() + $calc['withholding_tax']);
        }

        $totals->calculateNet();

        return $totals->toArray();
    }

    protected function determineTaxBase(AmountAccumulator $amounts, string $amountType): float
    {
        // If amount type is 'net', premium tax is calculated on net premium (gross - commission - brokerage)
        if ($amountType === 'net') {
            return $amounts->gross() - $amounts->credit() - $amounts->brokerage();
        }

        // Default: premium tax on gross amount
        return $amounts->gross();
    }

    protected function buildCommissionLineItem(
        array $sourceItem,
        float $commissionAmount,
        float $commissionRate,
        float $originalAmount
    ): array {
        return $this->buildLineItem(
            [
                'item_code' => self::COMMISSION_ITEM_CODE,
                'description' => 'Commission',
                'class_group' => $sourceItem['class_group'] ?? null,
                'class_name' => $sourceItem['class_name'] ?? null,
                'line_rate' => $commissionRate,
            ],
            $commissionAmount,
            $commissionAmount,
            0,
            LedgerType::CREDIT,
            $commissionRate,
            $originalAmount
        );
    }

    protected function buildLineItem(
        array $item,
        float $shareAmount,
        float $commissionAmount,
        float $brokerageAmount,
        string $ledger,
        float $lineRate,
        ?float $originalAmount = null
    ): array {
        return [
            'item_code' => $item['item_code'],
            'description' => $item['description'] ?? '',
            'class_group_code' => $item['class_group'] ?? null,
            'class_code' => $item['class_name'] ?? null,
            'amount' => $shareAmount,
            'line_rate' => $lineRate,
            'commission' => $commissionAmount,
            'brokerage' => $brokerageAmount,
            'premium_tax' => 0.0,
            'reinsurance_tax' => 0.0,
            'withholding_tax' => 0.0,
            'original_amount' => $originalAmount ?? $shareAmount,
            'ledger' => $ledger,
        ];
    }

    protected function buildReinsurerCalculation(
        CoverRipart $reinsurer,
        float $share,
        string $amountType,
        float $commissionRate,
        string $commissionMode,
        float $brokerageRate,
        AmountAccumulator $amounts
    ): array {
        return [
            'cover_no' => $reinsurer->cover_no,
            'endorsement_no' => $reinsurer->endorsement_no ?? '',
            'partner_code' => $reinsurer->partner_no,
            'reinsurer_name' => $reinsurer->partner?->name ?? '',
            'share' => $share,
            'amount_type' => $amountType,
            'commission_mode' => $commissionMode,
            'gross_amount' => $amounts->gross(),
            'credit_amount' => $amounts->credit(),
            'commission_rate' => $commissionRate,
            'commission_amount' => $amounts->commission(),
            'brokerage_rate' => $brokerageRate,
            'brokerage_amount' => $amounts->brokerage(),
            'premium_tax' => $amounts->premiumTax(),
            'premium_tax_base' => $amountType,
            'reinsurance_tax' => $amounts->reinsuranceTax(),
            'withholding_tax' => $amounts->withholdingTax(),
            'other_deductions' => $amounts->credit(),
            'total_deductions' => $amounts->totalDeductions(),
            'net_amount' => $amounts->net(),
            'breakdown' => [
                'income' => [
                    'gross_premium' => $amounts->gross(),
                ],
                'deductions' => [
                    'claims' => $amounts->credit(),
                    'commission' => $amounts->commission(),
                    'brokerage' => $amounts->brokerage(),
                    'premium_tax' => $amounts->premiumTax(),
                    'reinsurance_tax' => $amounts->reinsuranceTax(),
                    'withholding_tax' => $amounts->withholdingTax(),
                    'other' => $amounts->credit(),
                ],
                'net_due' => $amounts->net(),
            ],
        ];
    }

    protected function buildCedantCalculation(
        CoverRegister $cover,
        float $share,
        string $commissionMode,
        float $brokerageRate,
        AmountAccumulator $amounts,
        array $lineItems
    ): array {
        return [
            'cover_no' => $cover->cover_no,
            'endorsement_no' => $cover->endorsement_no ?? '',
            'cedant_name' => $cover->customer?->name ?? '',
            'share' => $share,
            'commission_mode' => $commissionMode,
            'gross_amount' => $amounts->gross(),
            'credit_amount' => $amounts->credit(),
            'commission_amount' => $amounts->commission(),
            'brokerage_rate' => 0,
            'brokerage_amount' => 0,
            'premium_tax' => $amounts->premiumTax(),
            'reinsurance_tax' => $amounts->reinsuranceTax(),
            'withholding_tax' => $amounts->withholdingTax(),
            'other_deductions' => $amounts->credit(),
            'total_deductions' => $amounts->credit(),
            'net_amount' => $amounts->net(),
            'items' => $lineItems,
        ];
    }

    protected function buildDebitNoteResult(
        array $totals,
        float $brokerageRate,
        array $reinsurers,
        ?array $cedant
    ): array {
        $netAmount = $totals['net'];

        return [
            'gross_amount' => $totals['gross'],
            'credit_amount' => $totals['credit'],
            'commission_amount' => $totals['commission'],
            'brokerage_rate' => $brokerageRate,
            'brokerage_amount' => $totals['brokerage'],
            'premium_tax' => $totals['premium_tax'],
            'reinsurance_tax' => $totals['reinsurance_tax'],
            'withholding_tax' => $totals['withholding_tax'],
            'other_deductions' => $totals['other_deductions'],
            'total_deductions' => $totals['total_deductions'],
            'net_amount' => $netAmount,
            'reinsurers' => $reinsurers,
            'cedant' => $cedant,
            'balance_summary' => [
                'total_credits' => $totals['gross'],
                'total_debits' => $totals['total_deductions'],
                'balance' => $netAmount,
                'balance_type' => $netAmount > 0 ? 'DUE_FROM' : 'DUE_TO',
            ],
        ];
    }

    protected function percentage(float $amount, float $rate): float
    {
        if ($amount <= 0 || $rate <= 0) {
            return 0.0;
        }

        return $amount * ($rate / 100);
    }

    protected function getLedgerType(array $item): string
    {
        if (isset($item['ledger'])) {
            return strtoupper($item['ledger']);
        }

        $itemCode = $item['item_code'] ?? $item['description'] ?? '';

        return in_array($itemCode, self::DEBIT_CODES) ? LedgerType::DEBIT : LedgerType::CREDIT;
    }

    protected function isValidItem(array $item): bool
    {
        return isset($item['amount']) && $item['amount'] > 0;
    }

    protected function loadReinsurers(?CoverRegister $cover): Collection
    {
        if (!$cover) {
            return collect();
        }

        return CoverRipart::with('partner')
            ->where('cover_no', $cover->cover_no)
            ->where('endorsement_no', $cover->endorsement_no ?? '')
            ->where('share', '>', 0)
            ->get();
    }

    protected function calculateReinsuranceTax(float $grossAmount): float
    {
        // Implement reinsurance tax calculation
        // Example: 0.5% of gross amount
        return $this->percentage($grossAmount, 0.5);
    }

    protected function calculateWithholdingTax(float $grossAmount): float
    {
        // Implement withholding tax calculation
        // Example: 5% of gross amount (or commission)
        return $this->percentage($grossAmount, 5.0);
    }

    public function roundForDisplay(array $data, int $decimals = 2): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->roundForDisplay($value, $decimals);
            } elseif (is_float($value) || is_numeric($value)) {
                $data[$key] = round((float) $value, $decimals);
            }
        }

        return $data;
    }
}

class AmountAccumulator
{
    private float $gross = 0.0;
    private float $credit = 0.0;
    private float $commission = 0.0;
    private float $brokerage = 0.0;
    private float $premiumTax = 0.0;
    private float $reinsuranceTax = 0.0;
    private float $withholdingTax = 0.0;
    private float $net = 0.0;

    public static function create(): self
    {
        return new self();
    }

    public function addGross(float $amount): void
    {
        $this->gross += $amount;
    }

    public function addCredit(float $amount): void
    {
        $this->credit += $amount;
    }

    public function addCommission(float $amount): void
    {
        $this->commission += $amount;
    }

    public function addBrokerage(float $amount): void
    {
        $this->brokerage += $amount;
    }

    public function setPremiumTax(float $amount): void
    {
        $this->premiumTax = $amount;
    }

    public function setReinsuranceTax(float $amount): void
    {
        $this->reinsuranceTax = $amount;
    }

    public function setWithholdingTax(float $amount): void
    {
        $this->withholdingTax = $amount;
    }

    public function calculateNet(): void
    {
        $this->net = $this->gross - $this->totalDeductions();
    }

    public function totalDeductions(): float
    {
        return $this->commission
            + $this->brokerage
            + $this->premiumTax
            + $this->reinsuranceTax
            + $this->withholdingTax
            + $this->credit;
    }

    public function gross(): float
    {
        return $this->gross;
    }

    public function credit(): float
    {
        return $this->credit;
    }

    public function commission(): float
    {
        return $this->commission;
    }

    public function brokerage(): float
    {
        return $this->brokerage;
    }

    public function premiumTax(): float
    {
        return $this->premiumTax;
    }

    public function reinsuranceTax(): float
    {
        return $this->reinsuranceTax;
    }

    public function withholdingTax(): float
    {
        return $this->withholdingTax;
    }

    public function net(): float
    {
        return $this->net;
    }

    public function toArray(): array
    {
        return [
            'gross' => $this->gross,
            'credit' => $this->credit,
            'commission' => $this->commission,
            'brokerage' => $this->brokerage,
            'premium_tax' => $this->premiumTax,
            'reinsurance_tax' => $this->reinsuranceTax,
            'withholding_tax' => $this->withholdingTax,
            'other_deductions' => $this->credit,
            'total_deductions' => $this->totalDeductions(),
            'net' => $this->net,
        ];
    }
}


class LedgerType
{
    public const DEBIT = 'DR';
    public const CREDIT = 'CR';
}
