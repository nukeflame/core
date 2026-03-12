<?php

namespace Nukeflame\Core\Services;

use App\Models\ClaimNtfRegister;
use App\Models\ClaimRegister;
use App\Models\CoverDebit;
use App\Models\CoverRegister;
use App\Models\PolicyRenewal;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Business type mappings for grouping
     */
    private const FACULTATIVE_TYPES = ['FPR', 'FNP'];
    private const TREATY_TYPES = ['TPR', 'TNP'];
    private const PROPORTIONAL_TYPES = ['FPR', 'TPR'];
    private const NON_PROPORTIONAL_TYPES = ['FNP', 'TNP'];

    /**
     * Get all dashboard metrics for a specific period
     */
    public function getMetricsForPeriod(string $period = 'ytd', ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $month = $this->getMonthForPeriod($period);
        $quarter = $this->getQuarterForPeriod($period);

        return [
            'gwp' => $this->getGrossWrittenPremium($year, $month, $quarter),
            'netPremium' => $this->getNetPremiumIncome($year, $month, $quarter),
            'commissionIncome' => $this->getCommissionIncome($year, $month, $quarter),
            'lossRatio' => $this->getLossRatio($year, $month, $quarter),
            'renewalRate' => $this->getRenewalRate($year),
            'gwpChange' => $this->getGwpChangePercentage($year),
            'netPremiumChange' => $this->getNetPremiumChangePercentage($year),
            'lossRatioChange' => $this->getLossRatioChange($year),
        ];
    }

    /**
     * Get Gross Written Premium for period
     */
    public function getGrossWrittenPremium(int $year, ?int $month = null, ?int $quarter = null): float
    {
        $query = CoverRegister::where('account_year', $year)
            ->where('cancelled', 'N');

        $query = $this->applyPeriodFilter($query, $month, $quarter, 'account_month');

        return (float) $query->sum('rein_premium');
    }

    /**
     * Get Net Premium Income from debited covers
     */
    public function getNetPremiumIncome(int $year, ?int $month = null, ?int $quarter = null): float
    {
        $query = CoverDebit::where('period_year', $year)
            ->where('reversed', 'N');

        $query = $this->applyPeriodFilter($query, $month, $quarter, 'period_month');

        return (float) $query->sum('net_amt');
    }

    /**
     * Get Commission Income
     */
    public function getCommissionIncome(int $year, ?int $month = null, ?int $quarter = null): float
    {
        $query = CoverRegister::where('account_year', $year)
            ->where('cancelled', 'N');

        $query = $this->applyPeriodFilter($query, $month, $quarter, 'account_month');

        return (float) $query->sum('rein_comm_amount');
    }

    /**
     * Calculate Portfolio Loss Ratio
     * Loss Ratio = (Total Claims / Total Premium) * 100
     */
    public function getLossRatio(int $year, ?int $month = null, ?int $quarter = null): float
    {
        $totalPremium = $this->getGrossWrittenPremium($year, $month, $quarter);

        if ($totalPremium <= 0) {
            return 0.0;
        }

        // Get total claims from claim notifications (reserve amounts)
        $totalClaims = ClaimNtfRegister::whereYear('created_at', $year)
            ->sum('reserve_amount');

        // Also add paid claims from claim_debit if available
        $paidClaims = DB::table('claim_debit')
            ->where('period_year', $year)
            ->where('reversed', 'N')
            ->sum('gross');

        $totalClaims = max($totalClaims, $paidClaims);

        return round(($totalClaims / $totalPremium) * 100, 1);
    }

    /**
     * Get Renewal Rate
     */
    public function getRenewalRate(int $year): float
    {
        // Calculate based on policies that were due for renewal vs actually renewed
        $totalDueForRenewal = CoverRegister::whereYear('cover_to', $year - 1)
            ->where('cancelled', 'N')
            ->count();

        if ($totalDueForRenewal <= 0) {
            return 85.0; // Default industry average if no data
        }

        $renewed = CoverRegister::where('account_year', $year)
            ->where('cancelled', 'N')
            ->where('transaction_type', 'RN') // Renewal transaction type
            ->count();

        $rate = ($renewed / $totalDueForRenewal) * 100;

        return min(round($rate, 0), 100); // Cap at 100%
    }

    /**
     * Get business mix breakdown
     */
    public function getBusinessMix(int $year, ?int $month = null, ?int $quarter = null): array
    {
        $query = CoverRegister::where('account_year', $year)
            ->where('cancelled', 'N');

        $query = $this->applyPeriodFilter($query, $month, $quarter, 'account_month');

        $data = $query->select('type_of_bus', DB::raw('SUM(rein_premium) as premium'), DB::raw('COUNT(*) as count'))
            ->groupBy('type_of_bus')
            ->get()
            ->keyBy('type_of_bus');

        $totalPremium = $data->sum('premium');

        return [
            'facultative' => $this->calculateBusinessTypeGroup($data, self::FACULTATIVE_TYPES, $totalPremium),
            'treaty' => $this->calculateBusinessTypeGroup($data, self::TREATY_TYPES, $totalPremium),
            'fpr' => $this->calculateBusinessTypeStats($data, 'FPR', $totalPremium),
            'fnp' => $this->calculateBusinessTypeStats($data, 'FNP', $totalPremium),
            'tpr' => $this->calculateBusinessTypeStats($data, 'TPR', $totalPremium),
            'tnp' => $this->calculateBusinessTypeStats($data, 'TNP', $totalPremium),
            'total' => $totalPremium,
        ];
    }

    /**
     * Get recent activity for dashboard
     */
    public function getRecentActivity(int $limit = 5): Collection
    {
        // Try transaction logs first
        $activities = TransactionLog::with('user')
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($log) {
                return $this->formatActivityFromLog($log);
            });

        // If no transaction logs, get recent covers and claims
        if ($activities->isEmpty()) {
            $activities = $this->getRecentActivityFromCovers($limit);
        }

        return $activities;
    }

    /**
     * Get cover counts by type
     */
    public function getCoverCounts(int $year, ?int $month = null): array
    {
        $month = $month ?? now()->month;

        $baseQuery = fn() => CoverRegister::where('account_year', $year)
            ->where('account_month', $month)
            ->where('cancelled', 'N');

        return [
            'total' => [
                'title' => 'Total Reg. Covers',
                'amount' => $baseQuery()->count(),
            ],
            'debited' => [
                'title' => 'Debited Covers',
                'amount' => CoverDebit::where('period_year', $year)
                    ->where('period_month', $month)
                    ->where('reversed', 'N')
                    ->count(),
            ],
            'fac' => [
                'title' => 'Facultative Covers',
                'amount' => $baseQuery()->whereIn('type_of_bus', self::FACULTATIVE_TYPES)->count(),
            ],
            'tpr' => [
                'title' => 'Treaty Proportional',
                'amount' => $baseQuery()->where('type_of_bus', 'TPR')->count(),
            ],
            'tnp' => [
                'title' => 'Treaty Non-Prop.',
                'amount' => $baseQuery()->where('type_of_bus', 'TNP')->count(),
            ],
            'fpr' => [
                'title' => 'Fac. Proportional',
                'amount' => $baseQuery()->where('type_of_bus', 'FPR')->count(),
            ],
            'fnp' => [
                'title' => 'Fac. Non-Proportional',
                'amount' => $baseQuery()->where('type_of_bus', 'FNP')->count(),
            ],
        ];
    }

    /**
     * Get average commission rate
     */
    public function getAverageCommissionRate(int $year, ?int $month = null): float
    {
        $query = CoverRegister::where('account_year', $year)
            ->where('cancelled', 'N')
            ->where('rein_premium', '>', 0);

        if ($month) {
            $query->where('account_month', $month);
        }

        $avg = $query->avg('rein_comm_rate');

        return round($avg ?? 0, 1);
    }

    /**
     * Get GWP change percentage vs last year
     */
    private function getGwpChangePercentage(int $year): float
    {
        $currentGwp = $this->getGrossWrittenPremium($year);
        $lastYearGwp = $this->getGrossWrittenPremium($year - 1);

        if ($lastYearGwp <= 0) {
            return 0.0;
        }

        return round((($currentGwp - $lastYearGwp) / $lastYearGwp) * 100, 1);
    }

    /**
     * Get Net Premium change percentage vs budget/last year
     */
    private function getNetPremiumChangePercentage(int $year): float
    {
        $currentNet = $this->getNetPremiumIncome($year);
        $lastYearNet = $this->getNetPremiumIncome($year - 1);

        if ($lastYearNet <= 0) {
            return 0.0;
        }

        return round((($currentNet - $lastYearNet) / $lastYearNet) * 100, 1);
    }

    /**
     * Get Loss Ratio change vs target/last year
     */
    private function getLossRatioChange(int $year): float
    {
        $currentRatio = $this->getLossRatio($year);
        $lastYearRatio = $this->getLossRatio($year - 1);

        if ($lastYearRatio <= 0) {
            return 0.0;
        }

        // Negative change is good for loss ratio
        return round($currentRatio - $lastYearRatio, 1);
    }

    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, ?int $month, ?int $quarter, string $monthColumn)
    {
        if ($month) {
            return $query->where($monthColumn, $month);
        }

        if ($quarter) {
            $months = $this->getQuarterMonths($quarter);
            return $query->whereIn($monthColumn, $months);
        }

        return $query;
    }

    /**
     * Get month number for period
     */
    private function getMonthForPeriod(string $period): ?int
    {
        if ($period === 'month') {
            return now()->month;
        }
        return null;
    }

    /**
     * Get quarter number for period
     */
    private function getQuarterForPeriod(string $period): ?int
    {
        if ($period === 'quarter') {
            return now()->quarter;
        }
        return null;
    }

    /**
     * Get months array for a quarter
     */
    private function getQuarterMonths(int $quarter): array
    {
        return match ($quarter) {
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
            default => [],
        };
    }

    /**
     * Calculate stats for a group of business types
     */
    private function calculateBusinessTypeGroup(Collection $data, array $types, float $totalPremium): array
    {
        $premium = 0;
        $count = 0;
        $income = 0;

        foreach ($types as $type) {
            if ($data->has($type)) {
                $premium += $data[$type]->premium ?? 0;
                $count += $data[$type]->count ?? 0;
            }
        }

        // Get commission income for these types
        $income = CoverRegister::where('account_year', now()->year)
            ->where('cancelled', 'N')
            ->whereIn('type_of_bus', $types)
            ->sum('rein_comm_amount');

        return [
            'total' => $premium,
            'count' => $count,
            'income' => $income,
            'percentage' => $totalPremium > 0 ? round(($premium / $totalPremium) * 100, 1) : 0,
        ];
    }

    /**
     * Calculate stats for a single business type
     */
    private function calculateBusinessTypeStats(Collection $data, string $type, float $totalPremium): array
    {
        $typeData = $data->get($type);

        $premium = $typeData->premium ?? 0;
        $count = $typeData->count ?? 0;

        // Get commission income
        $income = CoverRegister::where('account_year', now()->year)
            ->where('cancelled', 'N')
            ->where('type_of_bus', $type)
            ->sum('rein_comm_amount');

        return [
            'gwp' => $premium,
            'count' => $count,
            'income' => $income,
            'percentage' => $totalPremium > 0 ? round(($premium / $totalPremium) * 100, 1) : 0,
        ];
    }

    /**
     * Format activity from transaction log
     */
    private function formatActivityFromLog(TransactionLog $log): array
    {
        $type = $this->getActivityType($log->entity_type, $log->action);

        return [
            'type' => $type,
            'icon' => $this->getActivityIcon($type),
            'iconClass' => $this->getActivityIconClass($type),
            'title' => $this->getActivityTitle($log),
            'description' => $this->getActivityDescription($log),
            'time' => $log->created_at->diffForHumans(),
            'amount' => $this->getActivityAmount($log),
        ];
    }

    /**
     * Get recent activity from covers when no logs available
     */
    private function getRecentActivityFromCovers(int $limit): Collection
    {
        $activities = collect();

        // Recent covers
        $recentCovers = CoverRegister::where('cancelled', 'N')
            ->latest('created_at')
            ->take($limit)
            ->get();

        foreach ($recentCovers as $cover) {
            $activities->push([
                'type' => 'new-cover',
                'icon' => 'ri-file-add-line',
                'iconClass' => 'new-cover',
                'title' => 'New ' . $this->getBusinessTypeName($cover->type_of_bus) . ' Cover Created',
                'description' => ($cover->class_code ?? 'General') . ' - ' . ($cover->insured_name ?? 'N/A'),
                'time' => $cover->created_at ? Carbon::parse($cover->created_at)->diffForHumans() : 'Recently',
                'amount' => 'KES ' . number_format($cover->rein_premium ?? 0),
            ]);
        }

        // Recent claims (if any)
        $recentClaims = ClaimNtfRegister::latest('created_at')
            ->take(2)
            ->get();

        foreach ($recentClaims as $claim) {
            $activities->push([
                'type' => 'claim',
                'icon' => 'ri-alert-line',
                'iconClass' => 'claim',
                'title' => 'Claim Notification',
                'description' => ($claim->class_code ?? 'General') . ' - ' . ($claim->insured_name ?? 'N/A'),
                'time' => $claim->created_at ? Carbon::parse($claim->created_at)->diffForHumans() : 'Recently',
                'amount' => 'KES ' . number_format($claim->reserve_amount ?? 0),
            ]);
        }

        return $activities->take($limit);
    }

    /**
     * Get activity type from entity and action
     */
    private function getActivityType(string $entityType, string $action): string
    {
        return match (true) {
            str_contains($entityType, 'debit') => 'payment',
            str_contains($entityType, 'credit') => 'payment',
            str_contains($entityType, 'claim') => 'claim',
            str_contains($entityType, 'renewal') => 'renewal',
            default => 'new-cover',
        };
    }

    /**
     * Get activity icon
     */
    private function getActivityIcon(string $type): string
    {
        return match ($type) {
            'new-cover' => 'ri-file-add-line',
            'renewal' => 'ri-repeat-line',
            'payment' => 'ri-money-dollar-circle-line',
            'claim' => 'ri-alert-line',
            default => 'ri-file-list-3-line',
        };
    }

    /**
     * Get activity icon class
     */
    private function getActivityIconClass(string $type): string
    {
        return $type;
    }

    /**
     * Get activity title
     */
    private function getActivityTitle(TransactionLog $log): string
    {
        $action = ucfirst(strtolower($log->action));
        $entity = str_replace('_', ' ', ucwords($log->entity_type));

        return "{$entity} {$action}d";
    }

    /**
     * Get activity description
     */
    private function getActivityDescription(TransactionLog $log): string
    {
        $newValues = $log->new_values ?? [];

        return $newValues['description'] ?? $newValues['cover_no'] ?? 'Transaction processed';
    }

    /**
     * Get activity amount
     */
    private function getActivityAmount(TransactionLog $log): string
    {
        $newValues = $log->new_values ?? [];
        $amount = $newValues['total_amount'] ?? $newValues['amount'] ?? $newValues['gross'] ?? 0;

        return 'KES ' . number_format($amount);
    }

    /**
     * Get business type display name
     */
    private function getBusinessTypeName(string $typeCode): string
    {
        return match ($typeCode) {
            'FPR' => 'Facultative Proportional',
            'FNP' => 'Facultative Non-Proportional',
            'TPR' => 'Treaty Proportional',
            'TNP' => 'Treaty Non-Proportional',
            default => 'Cover',
        };
    }
}
