<?php

namespace App\Services;

use App\Models\Bd\PipelineOpportunity;
use App\Models\KPI;
use Carbon\Carbon;

class PipelineService
{
    /**
     * Get KPI data for dashboard
     */
    public function getKPIs(): array
    {
        $currentMonth = Carbon::now()->month;
        $previousMonth = Carbon::now()->subMonth()->month;
        $currentQuarter = Carbon::now()->quarter;
        $previousQuarter = Carbon::now()->subQuarter()->quarter;

        // return [
        //     'active_opportunities' => [
        //         'value' => PipelineOpportunity::where('status', '!=', 'declined')->count(),
        //         'trend' => $this->calculateTrend(
        //             PipelineOpportunity::whereMonth('created_at', $currentMonth)->count(),
        //             PipelineOpportunity::whereMonth('created_at', $previousMonth)->count()
        //         ),
        //         'trend_type' => 'monthly'
        //     ],
        //     'pipeline_premium' => [
        //         'value' => PipelineOpportunity::where('status', '!=', 'declined')->sum('gross_premium'),
        //         'trend' => $this->calculateTrend(
        //             PipelineOpportunity::whereQuarter('created_at', $currentQuarter)->sum('gross_premium'),
        //             PipelineOpportunity::whereQuarter('created_at', $previousQuarter)->sum('gross_premium')
        //         ),
        //         'trend_type' => 'quarterly'
        //     ],
        //     'conversion_rate' => [
        //         'value' => $this->getConversionRate(),
        //         'trend' => $this->getConversionRateTrend(),
        //         'trend_type' => 'improvement'
        //     ],
        //     'critical_deadlines' => [
        //         'value' => $this->getCriticalDeadlinesCount(),
        //         'trend' => null,
        //         'trend_type' => 'attention'
        //     ]
        // ];

        return [
            'active_opportunities' => [
                'value' => 0,
                'trend' => $this->calculateTrend(
                    1,
                    1
                ),
                'trend_type' => 'monthly'
            ],
            'pipeline_premium' => [
                'value' => 0,
                'trend' => $this->calculateTrend(
                    0,
                    0
                ),
                'trend_type' => 'quarterly'
            ],
            'conversion_rate' => [
                'value' => 0,
                'trend' => 0,
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
        return "<span class='priority-badge {$class}'>{$opportunity->priority}</span>";
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
        return "<span class='status-badge {$class}'>{$opportunity->status}</span>";
    }

    /**
     * Get urgency class based on effective date
     */
    public function getUrgencyClass(PipelineOpportunity $opportunity): string
    {
        if (!$opportunity->effective_date) {
            return 'highlight-normal';
        }

        $today = Carbon::now();
        $effectiveDate = Carbon::parse($opportunity->effective_date);
        $daysToEffective = $today->diffInDays($effectiveDate, false);

        if ($daysToEffective <= 7) {
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
        if (!$amount) return '-';

        return '<span class="currency">$' . number_format($amount, 2) . '</span>';
    }

    /**
     * Get action buttons HTML
     */
    public function getActionButtons(PipelineOpportunity $opportunity): string
    {
        return "
            <div class='action-btn-group'>
                <button class='action-btn btn-view' onclick='viewOpportunity({$opportunity->id})' title='View'>
                    <i class='bx bx-eye'></i>
                </button>
                <button class='action-btn btn-edit' onclick='editOpportunity({$opportunity->id})' title='Edit'>
                    <i class='bx bx-edit'></i>
                </button>
                <button class='action-btn btn-pipeline' onclick='updatePipeline({$opportunity->id})' title='Update Pipeline'>
                    <i class='bx bx-git-branch'></i>
                </button>
                <button class='action-btn btn-docs' onclick='viewDocuments({$opportunity->id})' title='Documents'>
                    <i class='bx bx-file'></i>
                </button>
            </div>
        ";
    }

    private function calculateTrend($current, $previous): array
    {
        if ($previous == 0) {
            return ['percentage' => 0, 'direction' => 'neutral'];
        }

        $percentage = (($current - $previous) / $previous) * 100;
        $direction = $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'neutral');

        return [
            'percentage' => round(abs($percentage), 1),
            'direction' => $direction
        ];
    }

    private function getConversionRate(): float
    {
        $totalOpportunities = PipelineOpportunity::count();
        // $boundOpportunities = PipelineOpportunity::where('status', 'bound')->count();
        $boundOpportunities = 0;

        return $totalOpportunities > 0 ? round(($boundOpportunities / $totalOpportunities) * 100, 1) : 0;
    }

    private function getConversionRateTrend(): array
    {
        // Calculate previous period conversion rate and compare
        // $currentRate = $this->getConversionRate();
        // Implementation for previous period comparison...

        return ['percentage' => 2.1, 'direction' => 'up'];
    }

    private function getCriticalDeadlinesCount(): int
    {
        // return PipelineOpportunity::whereDate('quote_deadline', '<=', Carbon::now()->addDays(7))
        //     ->where('status', '!=', 'declined')
        //     ->count();
        return 0;
    }
}
