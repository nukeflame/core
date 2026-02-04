<?php

namespace App\DTOs;

/**
 * Represents the complete calculation result for a debit note
 */
class DebitNoteCalculationResult
{
    public function __construct(
        public readonly float $grossAmount,
        public readonly float $creditAmount,
        public readonly float $commissionAmount,
        public readonly float $brokerageRate,
        public readonly float $brokerageAmount,
        public readonly float $premiumTax,
        public readonly float $reinsuranceTax,
        public readonly float $withholdingTax,
        public readonly float $otherDeductions,
        public readonly float $totalDeductions,
        public readonly float $netAmount,
        public readonly array $reinsurers,
        public readonly ?CedantShareCalculation $cedant = null
    ) {}

    /**
     * Convert to array for JSON serialization
     */
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
            'reinsurers' => array_map(fn ($r) => $r->toArray(), $this->reinsurers),
            'cedant' => $this->cedant?->toArray(),
        ];
    }

    /**
     * Get balance summary
     */
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

/**
 * Represents calculation for a single reinsurer's share
 */
class ReinsurerShareCalculation
{
    public function __construct(
        public readonly string $coverNo,
        public readonly string $endorsementNo,
        public readonly string $partnerCode,
        public readonly string $reinsurerName,
        public readonly float $share,
        public readonly string $amountType,
        public readonly float $grossAmount,
        public readonly float $creditAmount,
        public readonly float $commissionRate,
        public readonly float $commissionAmount,
        public readonly float $brokerageRate,
        public readonly float $brokerageAmount,
        public readonly float $premiumTax,
        public readonly string $premiumTaxBase,
        public readonly float $reinsuranceTax,
        public readonly float $withholdingTax,
        public readonly float $otherDeductions,
        public readonly float $totalDeductions,
        public readonly float $netAmount
    ) {}

    /**
     * Convert to array
     */
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

    /**
     * Get breakdown by category
     */
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
 * Represents calculation for cedant's retained share
 */
class CedantShareCalculation
{
    public function __construct(
        public readonly string $coverNo,
        public readonly string $endorsementNo,
        public readonly string $cedantName,
        public readonly float $share,
        public readonly float $grossAmount,
        public readonly float $creditAmount,
        public readonly float $commissionAmount,
        public readonly float $brokerageRate,
        public readonly float $brokerageAmount,
        public readonly float $premiumTax,
        public readonly float $reinsuranceTax,
        public readonly float $withholdingTax,
        public readonly float $otherDeductions,
        public readonly float $totalDeductions,
        public readonly float $netAmount
    ) {}

    /**
     * Convert to array
     */
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
