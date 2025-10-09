<?php

namespace App\Services;

use App\Models\Bd\PipelineOpportunity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PipelineService
{
    /**
     * Get KPI data for dashboard
     */
    public function getKPIs(): array
    {
        $now = Carbon::now();
        // $currentMonth = $now->month;
        // $currentYear = $now->year;

        $previousPeriod = $now->copy()->subMonth();
        $previousMonth = $previousPeriod->month;
        // $[previousMonthYear] = $previousPeriod->year;

        // $currentQuarter = $now->quarter;
        $previousQuarterPeriod = $now->copy()->subQuarter();
        // $previousQuarter = $previousQuarterPeriod->quarter;
        // $previousYear = $previousQuarterPeriod->year;

        $activeOpp = DB::table('pipeline_opportunities')
            ->where('status', '!=', 'declined')
            ->count();

        // $pipTrend = $this->calculateTrend(
        //     DB::table('pipeline_opportunities')
        //         ->whereRaw('EXTRACT(QUARTER FROM created_at::timestamp) = ? AND EXTRACT(YEAR FROM created_at::timestamp) = ?', [$currentQuarter, $currentYear])
        //         ->sum('cede_premium'),

        //     DB::table('pipeline_opportunities')
        //         ->whereRaw('EXTRACT(QUARTER FROM created_at::timestamp) = ? AND EXTRACT(YEAR FROM created_at::timestamp) = ?', [$previousQuarter, $previousYear])
        //         ->sum('cede_premium')
        // );

        $pipPremium = DB::table('pipeline_opportunities')
            ->where('status', '!=', 'declined')
            ->sum('cede_premium');

        // $activeTrend = $this->calculateTrend(
        //     DB::table('pipeline_opportunities')
        //         ->whereRaw("EXTRACT(MONTH FROM created_at::timestamp) = ?", [$currentMonth])
        //         ->count(),
        //     DB::table('pipeline_opportunities')
        //         ->whereRaw("EXTRACT(MONTH FROM created_at::timestamp) = ?", [$previousMonth])
        //         ->count()
        // );

        return [
            'active_opportunities' => [
                'value' => $activeOpp,
                'trend' => 0,
                'trend_type' => 'monthly'
            ],
            'pipeline_premium' => [
                'value' => $pipPremium,
                'trend' => 0,
                'trend_type' => 'quarterly'
            ],
            'conversion_rate' => [
                'value' => $this->getConversionRate(),
                'trend' => $this->getConversionRateTrend(),
                'trend_type' => 'improvement'
            ],
            'critical_deadlines' => [
                'value' => $this->getCriticalDeadlinesCount(),
                'trend' => null,
                'trend_type' => 'attention'
            ]
        ];
    }

    /**
     * Get priority badge HTML
     */
    public function getPriorityBadge(PipelineOpportunity $opportunity): string
    {
        $priorities = [
            'critical' => 'priority-critical',
            'high' => 'priority-high',
            'medium' => 'priority-medium',
            'low' => 'priority-low'
        ];

        $class = $priorities[$opportunity->priority] ?? 'priority-medium';
        $label = ucfirst($opportunity->priority ?? 'medium');

        return sprintf(
            '<span class="priority-badge %s">%s</span>',
            htmlspecialchars($class, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadge(PipelineOpportunity $opportunity): string
    {
        $statuses = [
            'inquiry' => 'status-inquiry',
            'quoted' => 'status-quoted',
            'negotiation' => 'status-negotiation',
            'bound' => 'status-bound',
            'declined' => 'status-declined'
        ];

        $class = $statuses[$opportunity->status] ?? 'status-inquiry';
        $label = ucfirst($opportunity->status ?? 'inquiry');

        return sprintf(
            '<span class="status-badge %s">%s</span>',
            htmlspecialchars($class, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Get urgency class based on effective date
     */
    public function getUrgencyClass(PipelineOpportunity $opportunity): string
    {
        if (!$opportunity->effective_date) {
            return 'highlight-normal';
        }

        $today = Carbon::now()->startOfDay();
        $effectiveDate = Carbon::parse($opportunity->effective_date)->startOfDay();
        $daysToEffective = $today->diffInDays($effectiveDate, false);

        if ($daysToEffective < 0) {
            return 'highlight-overdue';
        } elseif ($daysToEffective <= 7) {
            return 'highlight-critical';
        } elseif ($daysToEffective <= 14) {
            return 'highlight-urgent';
        } elseif ($daysToEffective <= 30) {
            return 'highlight-upcoming';
        }

        return 'highlight-normal';
    }

    /**
     * Format currency values
     */
    public function formatCurrency($amount): string
    {
        if (!$amount) {
            return '-';
        }

        return sprintf(
            '<span class="currency">$%s</span>',
            number_format((float)$amount, 2)
        );
    }

    /**
     * Get action buttons HTML
     */
    public function getActionButtons(PipelineOpportunity $opportunity): string
    {
        $id = (int)$opportunity->id;

        return sprintf(
            '<div class="action-btn-group">
                <button class="action-btn btn-view" onclick="viewOpportunity(%d)" title="View">
                    <i class="bx bx-eye"></i>
                </button>
                <button class="action-btn btn-edit" onclick="editOpportunity(%d)" title="Edit">
                    <i class="bx bx-edit"></i>
                </button>
                <button class="action-btn btn-pipeline" onclick="updatePipeline(%d)" title="Update Pipeline">
                    <i class="bx bx-git-branch"></i>
                </button>
                <button class="action-btn btn-docs" onclick="viewDocuments(%d)" title="Documents">
                    <i class="bx bx-file"></i>
                </button>
            </div>',
            $id,
            $id,
            $id,
            $id
        );
    }

    /**
     * Calculate trend between two periods
     */
    private function calculateTrend($current, $previous): array
    {
        // Handle null or non-numeric values
        $current = (float)($current ?? 0);
        $previous = (float)($previous ?? 0);

        if ($previous == 0) {
            if ($current > 0) {
                return ['percentage' => 100.0, 'direction' => 'up'];
            }
            return ['percentage' => 0.0, 'direction' => 'neutral'];
        }

        $percentage = (($current - $previous) / $previous) * 100;
        $direction = $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'neutral');

        return [
            'percentage' => round(abs($percentage), 1),
            'direction' => $direction
        ];
    }

    /**
     * Get current conversion rate
     */
    private function getConversionRate(): float
    {
        $totalOpportunities = DB::table('pipeline_opportunities')->count();
        $boundOpportunities = DB::table('pipeline_opportunities')
            ->where('status', 'bound')
            ->count();

        return $totalOpportunities > 0
            ? round(($boundOpportunities / $totalOpportunities) * 100, 1)
            : 0.0;
    }

    /**
     * Get conversion rate trend
     */
    private function getConversionRateTrend(): array
    {
        $now = Carbon::now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $previousMonthStart = $now->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // Current month conversion rate
        $currentTotal = DB::table('pipeline_opportunities')
            ->where('created_at', '>=', $currentMonthStart)
            ->count();

        $currentBound = DB::table('pipeline_opportunities')
            ->where('created_at', '>=', $currentMonthStart)
            ->where('status', 'bound')
            ->count();

        $currentRate = $currentTotal > 0 ? ($currentBound / $currentTotal) * 100 : 0;

        // Previous month conversion rate
        $previousTotal = DB::table('pipeline_opportunities')
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->count();

        $previousBound = DB::table('pipeline_opportunities')
            ->whereBetween('created_at', [$previousMonthStart, $previousMonthEnd])
            ->where('status', 'bound')
            ->count();

        $previousRate = $previousTotal > 0 ? ($previousBound / $previousTotal) * 100 : 0;

        return $this->calculateTrend($currentRate, $previousRate);
    }

    /**
     * Get count of opportunities with critical deadlines
     */
    private function getCriticalDeadlinesCount(): int
    {
        $criticalDate = Carbon::now()->addDays(7)->format('Y-m-d');

        return DB::table('pipeline_opportunities')
            ->whereRaw('effective_date::date <= ?', [$criticalDate])
            ->where('status', '!=', 'declined')
            ->where('status', '!=', 'bound')
            ->count();
    }
}
