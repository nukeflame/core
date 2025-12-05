<?php

namespace App\Services;

use App\Models\TaxRate;
use Illuminate\Support\Facades\Cache;

class TaxCalculationService
{
    /**
     * Default tax rates (fallback if not in database)
     */
    protected const DEFAULTS = [
        'PREMIUM_LEVY' => 1,
        'REINSURANCE_LEVY' => 1,
        'WITHHOLDING_TAX' => 5.00,
    ];

    /**
     * Calculate premium levy
     */
    public function calculatePremiumLevy(float $grossAmount): float
    {
        $rate = $this->getCurrentRate('PREMIUM_LEVY');
        return $this->calculateAmount($grossAmount, $rate);
    }

    /**
     * Calculate reinsurance levy
     */
    public function calculateReinsuranceLevy(float $grossAmount): float
    {
        $rate = $this->getCurrentRate('REINSURANCE_LEVY');
        return $this->calculateAmount($grossAmount, $rate);
    }

    /**
     * Calculate withholding tax
     */
    public function calculateWithholdingTax(float $grossAmount): float
    {
        $rate = $this->getCurrentRate('WITHHOLDING_TAX');
        return $this->calculateAmount($grossAmount, $rate);
    }

    /**
     * Calculate brokerage amount
     */
    public function calculateBrokerage(float $grossAmount, float $brokerageRate): float
    {
        return $this->calculateAmount($grossAmount, $brokerageRate);
    }

    /**
     * Calculate amount based on rate
     */
    protected function calculateAmount(float $baseAmount, float $rate): float
    {
        if ($baseAmount <= 0 || $rate <= 0) {
            return 0;
        }

        return round($baseAmount * ($rate / 100), 2);
    }

    /**
     * Get current rate for a tax code
     */
    public function getCurrentRate(string $rateCode): float
    {
        // return TaxRate::getCurrentRate(
        //     $rateCode,
        //     self::DEFAULTS[$rateCode] ?? 0
        // );
        return self::DEFAULTS[$rateCode] ?? 0;
    }

    /**
     * Get all current tax rates
     */
    public function getAllCurrentRates(): array
    {
        // return TaxRate::getAllCurrentRates();
        return [];
    }

    /**
     * Calculate all taxes for a transaction
     */
    public function calculateAllTaxes(float $grossAmount, array $options = []): array
    {
        $result = [
            'gross_amount' => round($grossAmount, 2),
            'premium_levy' => 0,
            'premium_levy_rate' => 0,
            'reinsurance_levy' => 0,
            'reinsurance_levy_rate' => 0,
            'withholding_tax' => 0,
            'withholding_tax_rate' => 0,
            'brokerage' => 0,
            'brokerage_rate' => 0,
            'total_deductions' => 0,
            'net_amount' => $grossAmount,
        ];

        if (!empty($options['compute_premium_tax'])) {
            $result['premium_levy_rate'] = $this->getCurrentRate('PREMIUM_LEVY');
            $result['premium_levy'] = $this->calculatePremiumLevy($grossAmount);
        }

        if (!empty($options['compute_reinsurance_tax'])) {
            $result['reinsurance_levy_rate'] = $this->getCurrentRate('REINSURANCE_LEVY');
            $result['reinsurance_levy'] = $this->calculateReinsuranceLevy($grossAmount);
        }

        if (!empty($options['compute_withholding_tax'])) {
            $result['withholding_tax_rate'] = $this->getCurrentRate('WITHHOLDING_TAX');
            $result['withholding_tax'] = $this->calculateWithholdingTax($grossAmount);
        }

        if (!empty($options['brokerage_rate'])) {
            $result['brokerage_rate'] = (float) $options['brokerage_rate'];
            $result['brokerage'] = $this->calculateBrokerage(
                $grossAmount,
                $result['brokerage_rate']
            );
        }

        $result['total_deductions'] = round(
            $result['premium_levy']
                + $result['reinsurance_levy']
                + $result['withholding_tax']
                + $result['brokerage'],
            2
        );

        $result['net_amount'] = round(
            $grossAmount - $result['total_deductions'],
            2
        );

        return $result;
    }

    /**
     * Calculate sliding scale commission based on loss ratio
     */
    public function calculateSlidingCommission(
        float $grossPremium,
        float $incurredLosses,
        array $scale
    ): array {
        if ($grossPremium <= 0) {
            return [
                'loss_ratio' => 0,
                'commission_rate' => 0,
                'commission_amount' => 0,
            ];
        }

        $lossRatio = ($incurredLosses / $grossPremium) * 100;
        $applicableRate = 0;

        // Find applicable commission rate from scale
        foreach ($scale as $tier) {
            $minRatio = $tier['min_ratio'] ?? 0;
            $maxRatio = $tier['max_ratio'] ?? 100;
            $rate = $tier['commission_rate'] ?? 0;

            if ($lossRatio >= $minRatio && $lossRatio <= $maxRatio) {
                $applicableRate = $rate;
                break;
            }
        }

        return [
            'loss_ratio' => round($lossRatio, 2),
            'commission_rate' => $applicableRate,
            'commission_amount' => round($grossPremium * ($applicableRate / 100), 2),
        ];
    }

    /**
     * Calculate profit commission (loss participation)
     */
    public function calculateProfitCommission(
        float $grossPremium,
        float $incurredLosses,
        float $commissionRate,
        float $managementExpenseRate = 0
    ): array {
        $expenses = $grossPremium * ($managementExpenseRate / 100);
        $profit = $grossPremium - $incurredLosses - $expenses;

        if ($profit <= 0) {
            return [
                'profit' => 0,
                'commission_rate' => $commissionRate,
                'commission_amount' => 0,
            ];
        }

        return [
            'profit' => round($profit, 2),
            'commission_rate' => $commissionRate,
            'commission_amount' => round($profit * ($commissionRate / 100), 2),
        ];
    }

    /**
     * Validate tax calculation
     */
    public function validateCalculation(array $calculation): bool
    {
        $expectedNet = $calculation['gross_amount'] - $calculation['total_deductions'];
        $tolerance = 0.01; // 1 cent tolerance for rounding

        return abs($calculation['net_amount'] - $expectedNet) <= $tolerance;
    }

    /**
     * Clear rate cache
     */
    public function clearCache()
    {
        return null;
        // TaxRate::clearCache();
    }
}
