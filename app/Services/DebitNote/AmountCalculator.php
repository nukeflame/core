<?php

namespace App\Services\DebitNote;

use App\Models\CoverRegister;
use App\Models\CoverRipart;
use App\Services\TaxCalculationService;
use Illuminate\Support\Collection;

class AmountCalculator
{
    private const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];
    public const COMMISSION_ITEM_CODE = 'IT03';
    public const BROKERAGE_ITEM_CODE = 'IT08';
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

        return $this->buildDebitNoteResult($totals, $brokerageRate, $reinsurerCalculations, $cedantCalculation);
    }

    protected function calculateReinsurerShares(array $items, Collection $reinsurers, float $brokerageRate): array
    {
        return $reinsurers
            ->filter(fn($r) => ((float) ($r->share ?? 0)) > 0)
            ->map(fn($r) => $this->calculateSingleReinsurerShare($items, $r, (float) $r->share, $brokerageRate))
            ->filter()
            ->values()
            ->all();
    }

    protected function calculateSingleReinsurerShare(
        array $items,
        CoverRipart $reinsurer,
        float $sharePercentage,
        float $brokerageRate
    ): array {
        $config = ShareConfiguration::fromReinsurer($reinsurer, $brokerageRate);
        $context = new CalculationContext($config, $sharePercentage);

        foreach ($items as $item) {
            $this->processReinsurerLineItem($item, $context);
        }

        $this->applyTaxes($context);
        $this->distributeTaxesToLineItems($context);
        $context->amounts->calculateNet();

        return ResultBuilder::buildReinsurerResult($reinsurer, $context);
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

        $config = ShareConfiguration::fromCedant($cover, $brokerageRate);
        $context = new CalculationContext($config, $cedantShare);

        foreach ($items as $item) {
            $this->processCedantLineItem($item, $context);
        }

        logger()->debug(json_encode(
            ['Cedant' =>array_filter($context->lineItems, fn($item) => str_contains($item['description'], 'Commission'))],
            JSON_PRETTY_PRINT
        ));

        $this->applyTaxes($context);
        $this->distributeTaxesToLineItems($context);
        $context->amounts->calculateNet();

        return ResultBuilder::buildCedantResult($cover, $context);
    }

    protected function processCedantLineItem(array $item, CalculationContext $context): void
    {
        if (!$this->isValidItem($item)) {
            return;
        }

        $amount = (float) $item['amount'];
        $ledger = $this->getLedgerType($item);
        $shareAmount = $this->percentage($amount, $context->sharePercentage);

        if ($ledger === LedgerType::DEBIT) {
            // $this->processDebitItem($item, $amount, $shareAmount, $context,  LedgerType::DEBIT, LedgerType::CREDIT);
        } else {
            $this->processCreditItem($item, $amount, $shareAmount, $context, LedgerType::CREDIT,);
        }
    }

    protected function processReinsurerLineItem(array $item, CalculationContext $context): void
    {
        if (!$this->isValidItem($item)) {
            return;
        }

        $amount = (float) $item['amount'];
        $ledger = $this->getLedgerType($item);
        $shareAmount = $this->percentage($amount, $context->sharePercentage);

        if ($ledger === LedgerType::DEBIT) {
            $this->processDebitItem($item, $amount, $shareAmount, $context, LedgerType::CREDIT, LedgerType::DEBIT);
        } else {
            $this->processCreditItem($item, $amount, $shareAmount, $context, LedgerType::DEBIT);
        }
    }

    protected function processDebitItem(
        array $item,
        float $originalAmount,
        float $shareAmount,
        CalculationContext $context,
        string $ledger,
        string $commissionLedger
    ): void {
        $context->lastDebitItem = [
            'original_amount' => $originalAmount,
            'share_amount' => $shareAmount,
        ];

        $lineRate = (float) ($item['line_rate'] ?? $context->config->commissionRate);
        $commissionAmount = $this->calculateCommission($shareAmount, $lineRate, $context);
        $brokerageAmount = $this->calculateBrokerage($shareAmount, $context);

        $context->lineItems[] = LineItemBuilder::build(
            $item,
            $shareAmount,
            $commissionAmount,
            $brokerageAmount,
            $ledger,
            $context->sharePercentage,
            $originalAmount,
            (float) ($item['line_rate'] ?? 0)
        );

        // logger()->debug(json_encode(
        //     ['Commission' => $commissionAmount],
        //     JSON_PRETTY_PRINT
        // ));

        // if ($commissionAmount > 0) {
        //     $context->lineItems[] = LineItemBuilder::buildCommission(
        //         $item,
        //         $commissionAmount,
        //         $lineRate,
        //         $shareAmount,
        //         $commissionLedger,
        //         $lineRate
        //     );
        // }

        // if ($brokerageAmount > 0 && $context->config->isReinsurer) {
        //     $context->lineItems[] = LineItemBuilder::buildBrokerage(
        //         $item,
        //         $brokerageAmount,
        //         $context->config->brokerageRate,
        //         $shareAmount,
        //         $commissionLedger,
        //         $context->config->brokerageRate
        //     );
        // }

        if ($item['item_code'] === 'IT01') {
            $context->amounts->addGross($shareAmount);
        }

        $context->amounts->addCommission($commissionAmount);
        // $context->amounts->addBrokerage($brokerageAmount);
    }

    protected function processCreditItem(
        array $item,
        float $originalAmount,
        float $shareAmount,
        CalculationContext $context,
        string $ledger,
    ): void {
        $creditAmount = $this->calculateCreditAmount($item, $shareAmount, $context);
        $lineRate = $this->calculateLineRate($item, $context);
        $baseAmount = ($item['item_code'] === 'IT02')
            ? $originalAmount
            : ($context->lastDebitItem['share_amount'] ?? $shareAmount);

        $context->lineItems[] = LineItemBuilder::build(
            $item,
            $creditAmount,
            0,
            0,
            $ledger,
            $lineRate,
            $baseAmount,
            (float) ($item['line_rate'] ?? 0)
        );

        if ($item['item_code'] !== 'IT04') {
            $context->amounts->addCredit($creditAmount);
        }
    }

    protected function calculateCommission(float $shareAmount, float $rate, CalculationContext $context): float
    {
        logger()->debug(json_encode(
            ['calculateCommission' => $context->config->commissionMode],
            JSON_PRETTY_PRINT
        ));
        if ($context->config->commissionMode === 'net') {
            $netBase = $shareAmount - $this->percentage($shareAmount, $context->config->brokerageRate);
            return $this->percentage($netBase, $rate);
        }

        return $this->percentage($shareAmount, $rate);
    }

    protected function calculateBrokerage(float $shareAmount, CalculationContext $context): float
    {
        return $context->config->isReinsurer
            ? $this->percentage($shareAmount, $context->config->brokerageRate)
            : 0.0;
    }

    protected function calculateCreditAmount(array $item, float $shareAmount, CalculationContext $context): float
    {
        if ($item['item_code'] === self::TAX_LEVY_ITEM_CODE && $context->lastDebitItem) {
            return $this->taxService->calculatePremiumLevy($context->lastDebitItem['share_amount']);
        }

        return $shareAmount;
    }

    protected function calculateLineRate(array $item, CalculationContext $context): float
    {
        if ($item['item_code'] === self::TAX_LEVY_ITEM_CODE) {
            return (float) ($item['line_rate'] ?? 0);
        }

        return $context->sharePercentage;
    }

    protected function applyTaxes(CalculationContext $context): void
    {
        $taxBase = $this->determineTaxBase($context);

        $context->amounts->setPremiumTax(
            $this->taxService->calculatePremiumLevy($taxBase)
        );

        $context->amounts->setReinsuranceTax(
            $this->taxService->calculateReinsuranceLevy($taxBase)
        );

        $context->amounts->setWithholdingTax(
            $this->taxService->calculateWithholdingTax($taxBase)
        );
    }

    protected function determineTaxBase(CalculationContext $context): float
    {
        if ($context->config->amountType === 'net') {
            return $context->amounts->gross()
                - $context->amounts->credit()
                - $context->amounts->brokerage();
        }

        return $context->amounts->gross();
    }

    protected function distributeTaxesToLineItems(CalculationContext $context): void
    {
        foreach ($context->lineItems as &$lineItem) {
            if ($lineItem['ledger'] === LedgerType::DEBIT) {
                $lineItem['premium_tax'] = $context->amounts->premiumTax();
                $lineItem['reinsurance_tax'] = $context->amounts->reinsuranceTax();
                $lineItem['withholding_tax'] = $context->amounts->withholdingTax();
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

        $itemCode = $item['item_code'] ?? '';
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

class ShareConfiguration
{
    public function __construct(
        public readonly bool $isReinsurer,
        public readonly string $amountType,
        public readonly float $commissionRate,
        public readonly string $commissionMode,
        public readonly float $brokerageRate
    ) {}

    public static function fromReinsurer(CoverRipart $reinsurer, float $brokerageRate): self
    {
        return new self(
            isReinsurer: true,
            amountType: strtolower($reinsurer->amount_type ?? 'gross'),
            commissionRate: (float) ($reinsurer->commission_rate ?? 0),
            commissionMode: strtolower($reinsurer->commission_mode ?? 'gross'),
            brokerageRate: $brokerageRate
        );
    }

    public static function fromCedant(CoverRegister $cover, float $brokerageRate): self
    {
        return new self(
            isReinsurer: false,
            amountType: strtolower($cover->commission_mode ?? 'gross'),
            commissionRate: 0,
            commissionMode: strtolower($cover->commission_mode ?? 'gross'),
            brokerageRate: $brokerageRate
        );
    }
}

class CalculationContext
{
    public AmountAccumulator $amounts;
    public array $lineItems = [];
    public ?array $lastDebitItem = null;

    public function __construct(
        public readonly ShareConfiguration $config,
        public readonly float $sharePercentage
    ) {
        $this->amounts = AmountAccumulator::create();
    }
}

class LineItemBuilder
{
    public static function build(
        array $item,
        float $shareAmount,
        float $commissionAmount,
        float $brokerageAmount,
        string $ledger,
        float $lineRate,
        ?float $originalAmount = null,
        ?float $originalLineRate = null
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
            'original_line_rate' => $originalLineRate ?? $item['line_rate'],
            'ledger' => $ledger,
        ];
    }

    public static function buildCommission(
        array $sourceItem,
        float $commissionAmount,
        float $commissionRate,
        float $originalAmount,  
        string $commissionLedger,
        ?float $originalLineRate = null
    ): array {
        return self::build(
            [
                'item_code' => AmountCalculator::COMMISSION_ITEM_CODE,
                'description' => 'Commission',
                'class_group' => $sourceItem['class_group'] ?? null,
                'class_name' => $sourceItem['class_name'] ?? null,
            ],
            $commissionAmount,
            $commissionAmount,
            0,
            $commissionLedger,
            $commissionRate,
            $originalAmount,
            $originalLineRate
        );
    }

    public static function buildBrokerage(
        array $sourceItem,
        float $brokerageAmount,
        float $brokerageRate,
        float $originalAmount,
        string $commissionLedger,
        ?float $originalLineRate = null
    ): array {
        return self::build(
            [
                'item_code' => AmountCalculator::BROKERAGE_ITEM_CODE,
                'description' => 'Brokerage',
                'class_group' => $sourceItem['class_group'] ?? null,
                'class_name' => $sourceItem['class_name'] ?? null,
            ],
            $brokerageAmount,
            0,
            $brokerageAmount,
            $commissionLedger,
            $brokerageRate,
            $originalAmount,
            $originalLineRate
        );
    }
}


class ResultBuilder
{
    public static function buildReinsurerResult(CoverRipart $reinsurer, CalculationContext $context): array
    {
        return [
            'cover_no' => $reinsurer->cover_no,
            'endorsement_no' => $reinsurer->endorsement_no ?? '',
            'partner_code' => $reinsurer->partner_no,
            'reinsurer_name' => $reinsurer->partner?->name ?? '',
            'share' => $context->sharePercentage,
            'amount_type' => $context->config->amountType,
            'commission_mode' => $context->config->commissionMode,
            'gross_amount' => $context->amounts->gross(),
            'credit_amount' => $context->amounts->credit(),
            'commission_rate' => $context->config->commissionRate,
            'commission_amount' => $context->amounts->commission(),
            'brokerage_rate' => $context->config->brokerageRate,
            'brokerage_amount' => $context->amounts->brokerage(),
            'premium_tax' => $context->amounts->premiumTax(),
            'premium_tax_base' => $context->config->amountType,
            'reinsurance_tax' => $context->amounts->reinsuranceTax(),
            'withholding_tax' => $context->amounts->withholdingTax(),
            'other_deductions' => $context->amounts->credit(),
            'total_deductions' => $context->amounts->totalDeductions(),
            'net_amount' => $context->amounts->net(),
            'items' => $context->lineItems,
            'breakdown' => self::buildBreakdown($context->amounts),
        ];
    }

    public static function buildCedantResult(CoverRegister $cover, CalculationContext $context): array
    {
        return [
            'cover_no' => $cover->cover_no,
            'endorsement_no' => $cover->endorsement_no ?? '',
            'cedant_name' => $cover->customer?->name ?? '',
            'share' => $context->sharePercentage,
            'commission_mode' => $context->config->commissionMode,
            'gross_amount' => $context->amounts->gross(),
            'credit_amount' => $context->amounts->credit(),
            'commission_amount' => $context->amounts->commission(),
            'brokerage_rate' => 0,
            'brokerage_amount' => 0,
            'premium_tax' => $context->amounts->premiumTax(),
            'reinsurance_tax' => $context->amounts->reinsuranceTax(),
            'withholding_tax' => $context->amounts->withholdingTax(),
            'other_deductions' => $context->amounts->credit(),
            'total_deductions' => $context->amounts->credit(),
            'net_amount' => $context->amounts->net(),
            'items' => $context->lineItems,
        ];
    }

    private static function buildBreakdown(AmountAccumulator $amounts): array
    {
        return [
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
        ];
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
