<?php

namespace App\Services\DebitNote;

use App\DTOs\DebitNoteCalculationResult;
use App\DTOs\ReinsurerShareCalculation;
use App\DTOs\CedantShareCalculation;
use App\Models\CoverRegister;
use App\Models\CoverRipart;
use App\Services\TaxCalculationService;

/**
 * Handles all amount calculations for debit notes
 * 
 * Implements the proper reinsurance treaty proportional account calculations:
 * 1. Premium Share = Gross Premium × Share Percentage
 * 2. Commission = Premium Share × Commission Rate
 * 3. Brokerage = Premium Share × Brokerage Rate
 * 4. Premium Tax = Premium Share × Tax Rate (or Net Premium for net basis)
 * 5. Claims Share = Gross Claims × Share Percentage
 * 6. Net Amount = Premium Share - All Deductions
 */
class AmountCalculator
{
    // Debit item codes (income/premium items)
    private const DEBIT_CODES = ['IT01', 'IT11', 'IT20', 'IT26'];
    
    // Credit item codes (deductions/claims)
    private const CREDIT_CODES = ['IT02', 'IT03', 'IT04', 'IT05', 'IT06', 'IT07', 'IT08', 'IT10', 'IT21', 'IT27', 'IT29', 'IT30'];

    public function __construct(
        private readonly TaxCalculationService $taxService
    ) {}

    /**
     * Calculate all amounts for a debit note
     * 
     * @param array $data Input data containing items and rates
     * @param CoverRegister|null $cover Cover register for reinsurer shares
     * @return DebitNoteCalculationResult Complete calculation result
     */
    public function calculate(array $data, ?CoverRegister $cover = null): DebitNoteCalculationResult
    {
        $items = $data['items'] ?? [];
        $brokerageRate = (float) ($data['brokerageRate'] ?? 0);

        // Get reinsurers participating in this cover
        $reinsurers = $this->getReinsurers($cover);
        
        // Calculate for each reinsurer
        $reinsurerCalculations = $this->calculateReinsurerShares($items, $reinsurers, $brokerageRate);
        
        // Calculate cedant share if applicable
        $cedantCalculation = $this->calculateCedantShare($items, $cover, $brokerageRate);
        
        // Aggregate totals
        $totals = $this->aggregateTotals($reinsurerCalculations);

        return new DebitNoteCalculationResult(
            grossAmount: $totals['gross'],
            creditAmount: $totals['credit'],
            commissionAmount: $totals['commission'],
            brokerageRate: $brokerageRate,
            brokerageAmount: $totals['brokerage'],
            premiumTax: $totals['premium_tax'],
            reinsuranceTax: $totals['reinsurance_tax'],
            withholdingTax: $totals['withholding_tax'],
            otherDeductions: $totals['other_deductions'],
            totalDeductions: $totals['total_deductions'],
            netAmount: $totals['net'],
            reinsurers: $reinsurerCalculations,
            cedant: $cedantCalculation
        );
    }

    /**
     * Calculate amounts for each reinsurer's share
     */
    protected function calculateReinsurerShares(
        array $items,
        $reinsurers,
        float $brokerageRate
    ): array {
        $calculations = [];

        foreach ($reinsurers as $reinsurer) {
            $share = (float) ($reinsurer->share ?? 0);
            
            if ($share <= 0) {
                continue;
            }

            $amountType = strtolower($reinsurer->amount_type ?? 'gross');
            $commissionRate = (float) ($reinsurer->commission_rate ?? 0);

            // Initialize amounts
            $amounts = [
                'gross' => 0,
                'credit' => 0,
                'commission' => 0,
                'brokerage' => 0,
            ];

            // Process each line item
            foreach ($items as $item) {
                $this->processLineItem($item, $share, $commissionRate, $brokerageRate, $amounts);
            }

            // Calculate taxes based on amount type
            $taxBase = $this->calculateTaxBase($amounts, $amountType);
            
            $amounts['premium_tax'] = $this->taxService->calculatePremiumLevy($taxBase);
            $amounts['reinsurance_tax'] = $this->calculateReinsuranceTax($amounts['gross']);
            $amounts['withholding_tax'] = $this->calculateWithholdingTax($amounts['gross']);

            // Calculate total deductions and net amount
            $totalDeductions = $amounts['commission'] 
                + $amounts['brokerage'] 
                + $amounts['premium_tax']
                + $amounts['reinsurance_tax']
                + $amounts['withholding_tax']
                + $amounts['credit'];

            $netAmount = $amounts['gross'] - $totalDeductions;

            $calculations[] = new ReinsurerShareCalculation(
                coverNo: $reinsurer->cover_no,
                endorsementNo: $reinsurer->endorsement_no,
                partnerCode: $reinsurer->partner_code,
                reinsurerName: $reinsurer->partner?->name ?? 'Unknown',
                share: round($share, 4),
                amountType: $amountType,
                grossAmount: round($amounts['gross'], 2),
                creditAmount: round($amounts['credit'], 2),
                commissionRate: $commissionRate,
                commissionAmount: round($amounts['commission'], 2),
                brokerageRate: $brokerageRate,
                brokerageAmount: round($amounts['brokerage'], 2),
                premiumTax: round($amounts['premium_tax'], 2),
                premiumTaxBase: $amountType,
                reinsuranceTax: round($amounts['reinsurance_tax'], 2),
                withholdingTax: round($amounts['withholding_tax'], 2),
                otherDeductions: round($amounts['credit'], 2),
                totalDeductions: round($totalDeductions, 2),
                netAmount: round($netAmount, 2)
            );
        }

        return $calculations;
    }

    /**
     * Process a single line item
     */
    protected function processLineItem(
        array $item,
        float $sharePercentage,
        float $commissionRate,
        float $brokerageRate,
        array &$amounts
    ): void {
        if (!isset($item['amount']) || $item['amount'] <= 0) {
            return;
        }

        $amount = (float) $item['amount'];
        $ledger = $this->determineLedger($item);
        $lineCommissionRate = (float) ($item['line_rate'] ?? $commissionRate);

        // Calculate share of this item
        $shareAmount = $this->calculatePercentage($amount, $sharePercentage);

        if ($ledger === 'DR') {
            // Debit items = Premium income
            $amounts['gross'] += $shareAmount;
            
            // Calculate commission and brokerage on the share amount
            $amounts['commission'] += $this->calculatePercentage($shareAmount, $lineCommissionRate);
            $amounts['brokerage'] += $this->calculatePercentage($shareAmount, $brokerageRate);
        } else {
            // Credit items = Claims/Deductions
            $amounts['credit'] += $shareAmount;
        }
    }

    /**
     * Calculate cedant's share
     */
    protected function calculateCedantShare(
        array $items,
        ?CoverRegister $cover,
        float $brokerageRate
    ): ?CedantShareCalculation {
        if (!$cover) {
            return null;
        }

        $cedantShare = (float) ($cover->share_offered ?? 0);
        
        if ($cedantShare <= 0) {
            return null;
        }

        $amounts = [
            'gross' => 0,
            'credit' => 0,
            'commission' => 0,
            'brokerage' => 0,
        ];

        foreach ($items as $item) {
            if (!isset($item['amount']) || $item['amount'] <= 0) {
                continue;
            }

            $amount = (float) $item['amount'];
            $ledger = $this->determineLedger($item);
            $commissionRate = (float) ($item['line_rate'] ?? 0);

            $shareAmount = $this->calculatePercentage($amount, $cedantShare);

            if ($ledger === 'DR') {
                $amounts['gross'] += $shareAmount;
                $amounts['commission'] += $this->calculatePercentage($shareAmount, $commissionRate);
                $amounts['brokerage'] += $this->calculatePercentage($shareAmount, $brokerageRate);
            } else {
                $amounts['credit'] += $shareAmount;
            }
        }

        $amounts['premium_tax'] = $this->taxService->calculatePremiumLevy($amounts['gross']);
        $amounts['reinsurance_tax'] = $this->calculateReinsuranceTax($amounts['gross']);
        $amounts['withholding_tax'] = $this->calculateWithholdingTax($amounts['gross']);

        $totalDeductions = $amounts['commission']
            + $amounts['brokerage']
            + $amounts['premium_tax']
            + $amounts['reinsurance_tax']
            + $amounts['withholding_tax']
            + $amounts['credit'];

        $netAmount = $amounts['gross'] - $totalDeductions;

        return new CedantShareCalculation(
            coverNo: $cover->cover_no,
            endorsementNo: $cover->endorsement_no,
            cedantName: $cover->customer?->customer_name ?? 'Cedant',
            share: round($cedantShare, 4),
            grossAmount: round($amounts['gross'], 2),
            creditAmount: round($amounts['credit'], 2),
            commissionAmount: round($amounts['commission'], 2),
            brokerageRate: $brokerageRate,
            brokerageAmount: round($amounts['brokerage'], 2),
            premiumTax: round($amounts['premium_tax'], 2),
            reinsuranceTax: round($amounts['reinsurance_tax'], 2),
            withholdingTax: round($amounts['withholding_tax'], 2),
            otherDeductions: round($amounts['credit'], 2),
            totalDeductions: round($totalDeductions, 2),
            netAmount: round($netAmount, 2)
        );
    }

    /**
     * Aggregate totals from all reinsurer calculations
     */
    protected function aggregateTotals(array $calculations): array
    {
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

        foreach ($calculations as $calc) {
            $totals['gross'] += $calc->grossAmount;
            $totals['credit'] += $calc->creditAmount;
            $totals['commission'] += $calc->commissionAmount;
            $totals['brokerage'] += $calc->brokerageAmount;
            $totals['premium_tax'] += $calc->premiumTax;
            $totals['reinsurance_tax'] += $calc->reinsuranceTax;
            $totals['withholding_tax'] += $calc->withholdingTax;
            $totals['other_deductions'] += $calc->otherDeductions;
            $totals['net'] += $calc->netAmount;
        }

        $totals['total_deductions'] = 
            $totals['commission'] +
            $totals['brokerage'] +
            $totals['premium_tax'] +
            $totals['reinsurance_tax'] +
            $totals['withholding_tax'] +
            $totals['other_deductions'];

        return array_map(fn($value) => round($value, 2), $totals);
    }

    /**
     * Calculate tax base depending on amount type
     */
    protected function calculateTaxBase(array $amounts, string $amountType): float
    {
        if ($amountType === 'net') {
            // For net basis, tax is calculated on net premium (after commission and brokerage)
            return $amounts['gross'] - $amounts['commission'] - $amounts['brokerage'];
        }

        // For gross basis, tax is calculated on gross premium
        return $amounts['gross'];
    }

    /**
     * Calculate percentage of an amount
     */
    protected function calculatePercentage(float $amount, float $rate): float
    {
        if ($amount <= 0 || $rate <= 0) {
            return 0;
        }

        return round($amount * ($rate / 100), 2);
    }

    /**
     * Determine if item is debit (DR) or credit (CR)
     */
    protected function determineLedger(array $item): string
    {
        // Check explicit ledger assignment
        if (isset($item['ledger'])) {
            return strtoupper($item['ledger']);
        }

        // Determine from item code
        $itemCode = $item['item_code'] ?? $item['description'] ?? '';
        
        return in_array($itemCode, self::DEBIT_CODES) ? 'DR' : 'CR';
    }

    /**
     * Get reinsurers for a cover
     */
    protected function getReinsurers(?CoverRegister $cover)
    {
        if (!$cover) {
            return collect();
        }

        return CoverRipart::with('partner')
            ->where('cover_no', $cover->cover_no)
            ->where('endorsement_no', $cover->endorsement_no)
            ->where('share', '>', 0)
            ->get();
    }

    /**
     * Calculate reinsurance tax
     * Override this method to implement specific reinsurance tax logic
     */
    protected function calculateReinsuranceTax(float $grossAmount): float
    {
        // Implement if reinsurance tax applies
        // Example: return $this->taxService->calculateReinsuranceLevy($grossAmount);
        return 0;
    }

    /**
     * Calculate withholding tax
     * Override this method to implement specific WHT logic
     */
    protected function calculateWithholdingTax(float $grossAmount): float
    {
        // Implement if withholding tax applies
        // Example: return $this->taxService->calculateWithholdingTax($grossAmount);
        return 0;
    }
}