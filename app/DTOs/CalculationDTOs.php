<?php

namespace App\DTOs;

/**
 * Represents calculation for cedant's retained share
 */
readonly class CedantShareCalculation
{
    public function __construct(
        public string $coverNo,
        public string $endorsementNo,
        public string $cedantName,
        public float $share,
        public float $grossAmount,
        public float $creditAmount,
        public float $commissionAmount,
        public float $brokerageRate,
        public float $brokerageAmount,
        public float $premiumTax,
        public float $reinsuranceTax,
        public float $withholdingTax,
        public float $otherDeductions,
        public float $totalDeductions,
        public float $netAmount
    ) {}

    public function toArray(): array
    {
        return [
            'cover_no' => $this->coverNo,
            'endorsement_no' => $this->endorsementNo,
            'cedant_name' => $this->cedantName,
            'share' => $this->share,
            'gross_amount' => $this->grossAmount,
            'credit_amount' => $this->creditAmount,
            'commission_amount' => $this->commissionAmount,
            'brokerage_rate' => $this->brokerageRate,
            'brokerage_amount' => $this->brokerageAmount,
            'premium_tax' => $this->premiumTax,
            'reinsurance_tax' => $this->reinsuranceTax,
            'withholding_tax' => $this->withholdingTax,
            'other_deductions' => $this->otherDeductions,
            'total_deductions' => $this->totalDeductions,
            'net_amount' => $this->netAmount,
        ];
    }
}

/**
 * Represents calculation for a single reinsurer's share
 */
readonly class ReinsurerShareCalculation
{
    public function __construct(
        public string $coverNo,
        public string $endorsementNo,
        public string $partnerCode,
        public string $reinsurerName,
        public float $share,
        public string $amountType,
        public float $grossAmount,
        public float $creditAmount,
        public float $commissionRate,
        public float $commissionAmount,
        public float $brokerageRate,
        public float $brokerageAmount,
        public float $premiumTax,
        public string $premiumTaxBase,
        public float $reinsuranceTax,
        public float $withholdingTax,
        public float $otherDeductions,
        public float $totalDeductions,
        public float $netAmount
    ) {}

    public function toArray(): array
    {
        return [
            'cover_no' => $this->coverNo,
            'endorsement_no' => $this->endorsementNo,
            'partner_code' => $this->partnerCode,
            'reinsurer_name' => $this->reinsurerName,
            'share' => $this->share,
            'amount_type' => $this->amountType,
            'gross_amount' => $this->grossAmount,
            'credit_amount' => $this->creditAmount,
            'commission_rate' => $this->commissionRate,
            'commission_amount' => $this->commissionAmount,
            'brokerage_rate' => $this->brokerageRate,
            'brokerage_amount' => $this->brokerageAmount,
            'premium_tax' => $this->premiumTax,
            'premium_tax_base' => $this->premiumTaxBase,
            'reinsurance_tax' => $this->reinsuranceTax,
            'withholding_tax' => $this->withholdingTax,
            'other_deductions' => $this->otherDeductions,
            'total_deductions' => $this->totalDeductions,
            'net_amount' => $this->netAmount,
        ];
    }

    public function getBreakdown(): array
    {
        return [
            'income' => [
                'gross_premium' => $this->grossAmount,
            ],
            'deductions' => [
                'claims' => $this->creditAmount,
                'commission' => $this->commissionAmount,
                'brokerage' => $this->brokerageAmount,
                'premium_tax' => $this->premiumTax,
                'reinsurance_tax' => $this->reinsuranceTax,
                'withholding_tax' => $this->withholdingTax,
                'other' => $this->otherDeductions,
            ],
            'net_due' => $this->netAmount,
        ];
    }
}

/**
 * Represents the complete calculation result for a debit note
 */
readonly class DebitNoteCalculationResult
{
    public function __construct(
        public float $grossAmount,
        public float $creditAmount,
        public float $commissionAmount,
        public float $brokerageRate,
        public float $brokerageAmount,
        public float $premiumTax,
        public float $reinsuranceTax,
        public float $withholdingTax,
        public float $otherDeductions,
        public float $totalDeductions,
        public float $netAmount,
        public array $reinsurers,
        public ?CedantShareCalculation $cedant = null
    ) {}

    public function toArray(): array
    {
        return [
            'gross_amount' => $this->grossAmount,
            'credit_amount' => $this->creditAmount,
            'commission_amount' => $this->commissionAmount,
            'brokerage_rate' => $this->brokerageRate,
            'brokerage_amount' => $this->brokerageAmount,
            'premium_tax' => $this->premiumTax,
            'reinsurance_tax' => $this->reinsuranceTax,
            'withholding_tax' => $this->withholdingTax,
            'other_deductions' => $this->otherDeductions,
            'total_deductions' => $this->totalDeductions,
            'net_amount' => $this->netAmount,
            'reinsurers' => array_map(fn($r) => $r->toArray(), $this->reinsurers),
            'cedant' => $this->cedant?->toArray(),
        ];
    }

    public function getBalanceSummary(): array
    {
        return [
            'total_credits' => $this->grossAmount,
            'total_debits' => $this->totalDeductions,
            'balance' => $this->netAmount,
            'balance_type' => $this->netAmount > 0 ? 'DUE_FROM' : 'DUE_TO',
        ];
    }
}
