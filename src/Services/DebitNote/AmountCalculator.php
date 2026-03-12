<?php

namespace Nukeflame\Core\Services\DebitNote;

use App\Models\CoverRegister;
use App\Models\CoverRipart;
use Illuminate\Support\Collection;
use Nukeflame\Core\Services\TaxCalculationService;

class AmountCalculator
{
    private const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];
    public const COMMISSION_ITEM_CODE = 'IT03';
    public const BROKERAGE_ITEM_CODE = 'IT08';
    private const ITEM_PREMIUM_TAX = 'IT05';
    private const EPSILON = 0.0001;

    private const ITEM_GROSS_PREMIUM = 'IT01';
    private const ITEM_CLAIMS = 'IT02';

    private const LEDGER_DEBIT = 'DR';
    private const LEDGER_CREDIT = 'CR';


    public function __construct(
        private readonly TaxCalculationService $taxService
    ) {}

    public function calculate(array $data, ?CoverRegister $cover = null, $busType): array
    {
        $items = $data['items'] ?? [];
        $brokerageRate = (float) ($data['brokerageRate'] ?? 0);

        if (empty($items)) {
            return $this->buildEmptyResult($brokerageRate);
        }

        if ($busType === self::LEDGER_CREDIT) {
            $reinsurers = $this->loadReinsurers($cover);
            return $this->calculateReinsurerShares($items, $reinsurers, $brokerageRate, $data);
        } else {
            return  $this->calculateCedantShare($items, $cover, $brokerageRate, $data);
        }
    }

    protected function calculateReinsurerShares(array $items, Collection $reinsurers, float $brokerageRate, array $data): array
    {
        return $reinsurers
            ->filter(fn($r) => $this->hasValidShare($r))
            ->map(fn($r) => $this->calculateSingleReinsurerShare(
                $items,
                $r,
                $this->getReinsurerShare($r),
                $brokerageRate,
                $data
            ))
            ->filter()
            ->values()
            ->all();
    }

    protected function calculateSingleReinsurerShare(
        array $items,
        CoverRipart $reinsurer,
        float $sharePercentage,
        float $brokerageRate,
        array $data
    ): array {
        $config = ShareConfiguration::fromReinsurer($reinsurer, $brokerageRate, $data);
        $effectiveSharePercentage = $this->applyNetTaxShareAdjustment($sharePercentage, $config);
        $context = new CalculationContext($config, $effectiveSharePercentage);

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
        float $brokerageRate,
        array $data
    ): ?array {
        if (!$cover) {
            return null;
        }

        $cedantShare = (float) ($cover->share_offered ?? 0);
        if ($cedantShare <= self::EPSILON) {
            return null;
        }

        $config = ShareConfiguration::fromCedant($cover, $brokerageRate, $data);
        $effectiveSharePercentage = $this->applyNetTaxShareAdjustment($cedantShare, $config);
        $context = new CalculationContext($config, $effectiveSharePercentage);

        foreach ($items as $item) {
            $this->processCedantLineItem($item, $context);
        }

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
            $this->processDebitItem(
                $item,
                $amount,
                $shareAmount,
                $context,
                LedgerType::DEBIT,
                LedgerType::CREDIT,
                'cedant'
            );
        } else {
            $this->processCreditItem(
                $item,
                $amount,
                $shareAmount,
                $context,
                LedgerType::CREDIT,
                LedgerType::DEBIT,
                'cedant'
            );
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
            $this->processDebitItem(
                $item,
                $amount,
                $shareAmount,
                $context,
                LedgerType::CREDIT,
                LedgerType::DEBIT,
                'reinsurer'
            );
        } else {
            $this->processCreditItem(
                $item,
                $amount,
                $shareAmount,
                $context,
                LedgerType::DEBIT,
                LedgerType::DEBIT,
                'reinsurer'
            );
        }
    }

    protected function processDebitItem(
        array $item,
        float $originalAmount,
        float $shareAmount,
        CalculationContext $context,
        string $ledger,
        string $commissionLedgerType,
        string $type
    ): void {
        $itemCode = $item['item_code'] ?? '';
        $lineRate = (float) ($item['line_rate'] ?? $context->config->commissionRate);
        $commissionAmount = $this->calculateCommission($shareAmount, $context, $item, $type);

        $grossAmount = ($itemCode === self::COMMISSION_ITEM_CODE && abs($lineRate) > self::EPSILON)
            ? $shareAmount / ($lineRate / 100)
            : $shareAmount;

        if ($itemCode !== self::COMMISSION_ITEM_CODE) {
            $this->setLastDebitItem($context, $type, [
                'original_amount' => $originalAmount,
                'share_amount' => $grossAmount,
            ]);

            $context->lineItems[] = LineItemBuilder::build(
                $item,
                $shareAmount,
                $commissionAmount,
                0,
                $ledger,
                $context->sharePercentage,
                $originalAmount,
                (float) ($item['line_rate'] ?? 0)
            );

            $context->amounts->addGross($shareAmount);
        }

        if ($itemCode === self::COMMISSION_ITEM_CODE && $commissionAmount > self::EPSILON) {
            $context->lineItems[] = LineItemBuilder::buildCommission(
                $item,
                $commissionAmount,
                $lineRate,
                $grossAmount,
                $commissionLedgerType,
                $lineRate
            );

            $context->amounts->addCommission($commissionAmount);
        }
    }

    protected function processCreditItem(
        array $item,
        float $originalAmount,
        float $shareAmount,
        CalculationContext $context,
        string $ledger,
        string $commissionLedgerType,
        string $type
    ): void {
        $itemCode = $item['item_code'] ?? '';
        $creditAmount = $this->calculateCreditAmount($item, $shareAmount, $context, $type);
        $lineRate = $this->calculateLineRate($item, $context);

        $lastDebitItem = $this->getLastDebitItem($context, $type);
        $baseAmount = ($itemCode === 'IT02')
            ? $originalAmount
            : ($lastDebitItem['share_amount'] ?? $shareAmount);

        $brokerageAmount = $this->calculateBrokerage($shareAmount, $context, $item, $type);

        if ($creditAmount > self::EPSILON) {
            $context->lineItems[] = LineItemBuilder::build(
                $item,
                $creditAmount,
                $baseAmount,
                0,
                $ledger,
                $lineRate,
                $baseAmount,
                (float) ($item['line_rate'] ?? 0)
            );

            $context->amounts->addCredit($creditAmount);

            if ($itemCode === self::ITEM_PREMIUM_TAX) {
                $context->amounts->addPremiumTax($creditAmount);
            }
        }

        if ($brokerageAmount > self::EPSILON && $context->config->isReinsurer) {
            $context->lineItems[] = LineItemBuilder::buildBrokerage(
                $item,
                $brokerageAmount,
                $context->config->brokerageRate,
                $baseAmount,
                $commissionLedgerType,
                $context->config->brokerageRate
            );

            $context->amounts->addBrokerage($brokerageAmount);
        }
    }

    protected function calculateCommission(float $shareAmount, CalculationContext $context, array $item, string $type): float
    {
        $itemCode = $item['item_code'] ?? '';

        if ($itemCode !== self::COMMISSION_ITEM_CODE) {
            return 0;
        }

        $grossAmount = $shareAmount;

        if ($context->config->commissionMode === 'net') {
            $netFactor = (100 - $context->config->premiumLevy) / 100;
            return $grossAmount * $netFactor;
        }

        return $grossAmount;
    }

    protected function calculateBrokerage(float $shareAmount, CalculationContext $context, array $item, string $type): float
    {
        $itemCode = $item['item_code'] ?? '';

        if ($itemCode === 'IT02' || !$context->config->isReinsurer) {
            return 0.0;
        }

        $baseAmount = $this->getLastDebitItem($context, $type)['share_amount'] ?? 0;

        // When brokerage basis is Net of Tax, apply brokerage on the net base.
        if ($context->config->commissionMode === 'net' && $baseAmount > self::EPSILON) {
            $netFactor = (100 - $context->config->premiumLevy) / 100;
            $baseAmount = $baseAmount * $netFactor;
        }

        return $this->percentage($baseAmount, $context->config->brokerageRate);
    }

    protected function calculateCreditAmount(array $item, float $shareAmount, CalculationContext $context, string $type): float
    {
        $itemCode = $item['item_code'] ?? '';
        $lastDebitItem = $this->getLastDebitItem($context, $type);

        if ($itemCode === self::ITEM_PREMIUM_TAX && $lastDebitItem) {
            $grossAmount = $lastDebitItem['share_amount'];

            if ($context->config->computePremiumTax) {
                return $grossAmount * ($context->config->premiumLevy / 100);
            }

            return 0;
        }

        return $shareAmount;
    }

    protected function calculateLineRate(array $item, CalculationContext $context): float
    {
        $itemCode = $item['item_code'] ?? '';

        if ($itemCode === self::ITEM_PREMIUM_TAX) {
            return (float) ($item['line_rate'] ?? 0);
        }

        return $context->sharePercentage;
    }

    protected function applyTaxes(CalculationContext $context): void
    {
        $taxBase = $this->determineTaxBase($context);

        if ($context->config->computePremiumTax) {
            $context->amounts->setPremiumTax($this->taxService->calculatePremiumLevy($taxBase));
        }

        if ($context->config->computeReinsuranceTax) {
            // $context->amounts->setReinsuranceTax(
            //     $this->taxService->calculateReinsuranceLevy($taxBase)
            // );
        }

        if ($context->config->computeWithholdingTax) {
            // $context->amounts->setWithholdingTax(
            //     $this->taxService->calculateWithholdingTax($taxBase)
            // );
        }
    }

    protected function determineTaxBase(CalculationContext $context): float
    {
        // For cedant, net-of-tax should only affect commission computation.
        // Keep cedant tax base on gross so other figures remain unchanged.
        if ($context->config->isReinsurer && $context->config->commissionMode === 'net') {
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
                // $lineItem['premium_tax'] = $context->amounts->premiumTax();
                // $lineItem['reinsurance_tax'] = $context->amounts->reinsuranceTax();
                // $lineItem['withholding_tax'] = $context->amounts->withholdingTax();
                break;
            }
        }
    }

    protected function aggregateTotals(array $calculations): array
    {
        $totals = AmountAccumulator::create();

        foreach ($calculations as $calc) {
            $totals->addGross($calc['gross_amount'] ?? 0);
            $totals->addCredit($calc['credit_amount'] ?? 0);
            $totals->addCommission($calc['commission_amount'] ?? 0);
            $totals->addBrokerage($calc['brokerage_amount'] ?? 0);
            // $totals->setPremiumTax($totals->premiumTax() + ($calc['premium_tax'] ?? 0));
            // $totals->setReinsuranceTax($totals->reinsuranceTax() + ($calc['reinsurance_tax'] ?? 0));
            // $totals->setWithholdingTax($totals->withholdingTax() + ($calc['withholding_tax'] ?? 0));
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
                'balance_type' => $netAmount > self::EPSILON ? 'DUE_FROM' : 'DUE_TO',
            ],
        ];
    }

    protected function buildEmptyResult(float $brokerageRate): array
    {
        return [
            'gross_amount' => 0,
            'credit_amount' => 0,
            'commission_amount' => 0,
            'brokerage_rate' => $brokerageRate,
            'brokerage_amount' => 0,
            'premium_tax' => 0,
            'reinsurance_tax' => 0,
            'withholding_tax' => 0,
            'other_deductions' => 0,
            'total_deductions' => 0,
            'net_amount' => 0,
            'reinsurers' => [],
            'cedant' => null,
            'balance_summary' => [
                'total_credits' => 0,
                'total_debits' => 0,
                'balance' => 0,
                'balance_type' => 'DUE_TO',
            ],
        ];
    }

    protected function percentage(float $amount, float $rate): float
    {
        if ($amount <= self::EPSILON || $rate <= self::EPSILON) {
            return 0.0;
        }

        return $amount * ($rate / 100);
    }

    protected function applyNetTaxShareAdjustment(float $sharePercentage, ShareConfiguration $config): float
    {
        return $sharePercentage;
    }

    protected function getLedgerType(array $item): string
    {
        if (isset($item['ledger'])) {
            return strtoupper($item['ledger']);
        }

        $itemCode = $item['item_code'] ?? '';
        return in_array($itemCode, self::DEBIT_CODES, true) ? LedgerType::DEBIT : LedgerType::CREDIT;
    }

    protected function isValidItem(array $item): bool
    {
        return isset($item['amount']) && (float) $item['amount'] > self::EPSILON;
    }

    protected function hasValidShare($reinsurer): bool
    {
        return $this->getReinsurerShare($reinsurer) > self::EPSILON;
    }

    protected function setLastDebitItem(CalculationContext $context, string $type, array $item): void
    {
        if ($type === 'cedant') {
            $context->cedantLastDebitItem = $item;
            return;
        }

        $context->reinsurerLastDebitItem = $item;
    }

    protected function getLastDebitItem(CalculationContext $context, string $type): ?array
    {
        if ($type === 'cedant') {
            return $context->cedantLastDebitItem;
        }

        return $context->reinsurerLastDebitItem;
    }

    protected function getReinsurerShare($reinsurer): float
    {
        $isTreatyAcceptanceBased = $reinsurer->optional_acceptance !== null
            || $reinsurer->compulsory_acceptance !== null
            || $reinsurer->total_acceptance !== null;

        if ($isTreatyAcceptanceBased) {
            return (float) ($reinsurer->optional_acceptance ?? 0);
        }

        return (float) ($reinsurer->share ?? 0);
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
        public readonly float $commissionRate,
        public readonly string $commissionMode,
        public readonly float $brokerageRate,
        public readonly float $premiumLevy,
        public readonly float $reinsuranceLevy,
        public readonly float $withholdingTax,
        public readonly bool $computePremiumTax,
        public readonly bool $computeReinsuranceTax,
        public readonly bool $computeWithholdingTax,
        public readonly bool $applyNetTaxToShare,
    ) {}

    public static function fromReinsurer(CoverRipart $reinsurer, float $brokerageRate, array $data): self
    {
        $commissionMode = strtolower($reinsurer->commission_mode ?? 'gross');
        $premiumLevy = (float) ($data['premiumLevy'] ?? 1);
        $computePremiumTax = (bool) ($data['computePremiumTax'] ?? false);

        return new self(
            isReinsurer: true,
            commissionRate: (float) ($reinsurer->commission_rate ?? 0),
            commissionMode: $commissionMode,
            brokerageRate: $brokerageRate,
            computePremiumTax: $computePremiumTax,
            computeReinsuranceTax: (bool) ($data['computeReinsuranceTax'] ?? false),
            computeWithholdingTax: (bool) ($data['computeWithholdingTax'] ?? false),
            premiumLevy: $premiumLevy,
            reinsuranceLevy: (float) ($data['reinsuranceLevy'] ?? 0),
            withholdingTax: (float) ($data['withholdingTax'] ?? 0),
            applyNetTaxToShare: $commissionMode === 'net' && $computePremiumTax && $premiumLevy > 0,
        );
    }

    public static function fromCedant(CoverRegister $cover, float $brokerageRate, array $data): self
    {
        $partner = CoverRipart::where('cover_no', $cover->cover_no)
            ->where('endorsement_no', $cover->endorsement_no ?? '')
            ->where('share', '>', 0)
            ->first();

        $commissionMode = 'gross';

        if ($partner) {
            $netOfTax = (bool) ($partner->net_of_tax ?? false);
            $netOfCommission = (bool) ($partner->net_of_commission ?? false);

            if ($netOfTax || $netOfCommission) {
                $commissionMode = 'net';
            }
        }

        $premiumLevy = (float) ($data['premiumLevy'] ?? 1);
        $computePremiumTax = (bool) ($data['computePremiumTax'] ?? false);

        return new self(
            isReinsurer: false,
            commissionRate: 0,
            commissionMode: strtolower($commissionMode),
            brokerageRate: $brokerageRate,
            computePremiumTax: $computePremiumTax,
            computeReinsuranceTax: (bool) ($data['computeReinsuranceTax'] ?? false),
            computeWithholdingTax: (bool) ($data['computeWithholdingTax'] ?? false),
            premiumLevy: $premiumLevy,
            reinsuranceLevy: (float) ($data['reinsuranceLevy'] ?? 0),
            withholdingTax: (float) ($data['withholdingTax'] ?? 0),
            applyNetTaxToShare: strtolower($commissionMode) === 'net' && $premiumLevy > 0,
        );
    }
}

class CalculationContext
{
    public AmountAccumulator $amounts;
    public array $lineItems = [];
    public ?array $cedantLastDebitItem = null;
    public ?array $reinsurerLastDebitItem = null;

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
            'item_code' => $item['item_code'] ?? '',
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
            'original_line_rate' => $originalLineRate ?? ($item['line_rate'] ?? 0),
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
            'share' => (float) ($reinsurer->share ?? 0),
            'calculation_share' => $context->sharePercentage,
            'commission_mode' => $context->config->commissionMode,
            'gross_amount' => $context->amounts->gross(),
            'credit_amount' => $context->amounts->credit(),
            'commission_rate' => $context->config->commissionRate,
            'commission_amount' => $context->amounts->commission(),
            'brokerage_rate' => $context->config->brokerageRate,
            'brokerage_amount' => $context->amounts->brokerage(),
            'premium_tax' => $context->amounts->premiumTax(),
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
            'total_deductions' => $context->amounts->totalDeductions(),
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
        $this->gross += round($amount, 4);
    }

    public function addCredit(float $amount): void
    {
        $this->credit += round($amount, 4);
    }

    public function addCommission(float $amount): void
    {
        $this->commission += round($amount, 4);
    }

    public function addBrokerage(float $amount): void
    {
        $this->brokerage += round($amount, 4);
    }

    public function addPremiumTax(float $amount): void
    {
        $this->premiumTax += round($amount, 4);
    }

    public function addReinsuranceTax(float $amount): void
    {
        $this->reinsuranceTax += round($amount, 4);
    }

    public function addWithholdingTax(float $amount): void
    {
        $this->withholdingTax += round($amount, 4);
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
        return $this->commission + $this->brokerage + $this->credit;
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
