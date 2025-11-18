{{-- resources/views/components/cover/financial-summary.blade.php --}}

@props(['cover'])

@php
    // Calculate financial totals from cover participations
    $coverPart = $cover->coverPart ?? collect();

    $totalSumInsured = $coverPart->sum('sum_insured');
    $totalPremium = $coverPart->sum('premium');
    $totalCommission = $coverPart->sum('commission');
    $totalBrokerage = $coverPart->sum('brokerage_comm_amt');
    $totalWHT = $coverPart->sum('wht_amt');
    $totalFronting = $coverPart->sum('fronting_amt');

    // Calculate net premium
    $grossPremium = $totalPremium;
    $totalDeductions = $totalCommission + $totalBrokerage + $totalWHT + $totalFronting;
    $netPremium = $grossPremium - $totalDeductions;

    // Calculate averages
    $avgCommissionRate = $coverPart->count() > 0 ? $coverPart->avg('comm_rate') : 0;
    $totalPlacedShare = $coverPart->sum('share');
@endphp

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-gradient-primary text-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="ri-money-dollar-circle-line me-2"></i>Financial Summary
        </h6>
    </div>
    <div class="card-body p-0">
        {{-- Premium Summary --}}
        <div class="financial-section">
            <div class="section-header">
                <small class="text-muted text-uppercase fw-semibold">Premium Overview</small>
            </div>

            <div class="financial-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="financial-label">
                        <i class="ri-funds-line text-primary me-2"></i>Gross Premium
                    </span>
                    <span class="financial-value text-primary fw-bold">
                        {{ number_format($grossPremium, 2) }}
                    </span>
                </div>
            </div>

            <div class="financial-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="financial-label">
                        <i class="ri-percent-line text-info me-2"></i>Average Commission
                    </span>
                    <span class="financial-value">
                        {{ number_format($avgCommissionRate, 2) }}%
                        <small class="text-muted d-block">{{ number_format($totalCommission, 2) }}</small>
                    </span>
                </div>
            </div>

            @if ($totalBrokerage > 0)
                <div class="financial-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="financial-label">
                            <i class="ri-briefcase-line text-secondary me-2"></i>Total Brokerage
                        </span>
                        <span class="financial-value">
                            {{ number_format($totalBrokerage, 2) }}
                        </span>
                    </div>
                </div>
            @endif

            <div class="financial-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="financial-label">
                        <i class="ri-file-list-3-line text-warning me-2"></i>Total WHT
                    </span>
                    <span class="financial-value text-warning">
                        {{ number_format($totalWHT, 2) }}
                    </span>
                </div>
            </div>

            @if ($totalFronting > 0)
                <div class="financial-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="financial-label">
                            <i class="ri-shield-check-line text-info me-2"></i>Fronting Fees
                        </span>
                        <span class="financial-value text-info">
                            {{ number_format($totalFronting, 2) }}
                        </span>
                    </div>
                </div>
            @endif

            <div class="financial-item total-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="financial-label fw-bold">
                        <i class="ri-checkbox-circle-line text-success me-2"></i>Net Premium
                    </span>
                    <span class="financial-value text-success fw-bold fs-6">
                        {{ number_format($netPremium, 2) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Capacity Summary --}}
        <div class="financial-section">
            <div class="section-header">
                <small class="text-muted text-uppercase fw-semibold">Capacity Breakdown</small>
            </div>

            <div class="financial-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="financial-label">
                        <i class="ri-shield-star-line text-primary me-2"></i>Treaty Capacity
                    </span>
                    <span class="financial-value fw-semibold">
                        {{ number_format($cover->effective_sum_insured, 2) }}
                    </span>
                </div>
            </div>

            <div class="financial-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="financial-label">
                        <i class="ri-pie-chart-line text-success me-2"></i>Placed Share
                    </span>
                    <span class="financial-value">
                        {{ number_format($totalPlacedShare, 4) }}%
                        <small class="text-muted d-block">
                            {{ number_format(($totalPlacedShare / 100) * $cover->effective_sum_insured, 2) }}
                        </small>
                    </span>
                </div>
            </div>

            <div class="financial-item">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="financial-label">
                        <i class="ri-contrast-line text-warning me-2"></i>Remaining
                    </span>
                    <span class="financial-value text-warning">
                        {{ number_format(100 - $totalPlacedShare, 4) }}%
                        <small class="text-muted d-block">
                            {{ number_format(((100 - $totalPlacedShare) / 100) * $cover->effective_sum_insured, 2) }}
                        </small>
                    </span>
                </div>
            </div>
        </div>

        {{-- Deductions Summary --}}
        <div class="financial-section">
            <div class="section-header">
                <small class="text-muted text-uppercase fw-semibold">Total Deductions</small>
            </div>

            <div class="deductions-chart mb-3">
                <div class="progress" style="height: 25px; border-radius: 8px;">
                    @php
                        $commissionPercent = $grossPremium > 0 ? ($totalCommission / $grossPremium) * 100 : 0;
                        $brokeragePercent = $grossPremium > 0 ? ($totalBrokerage / $grossPremium) * 100 : 0;
                        $whtPercent = $grossPremium > 0 ? ($totalWHT / $grossPremium) * 100 : 0;
                        $frontingPercent = $grossPremium > 0 ? ($totalFronting / $grossPremium) * 100 : 0;
                        $netPercent = 100 - ($commissionPercent + $brokeragePercent + $whtPercent + $frontingPercent);
                    @endphp

                    @if ($commissionPercent > 0)
                        <div class="progress-bar bg-primary" role="progressbar"
                            style="width: {{ $commissionPercent }}%" data-bs-toggle="tooltip"
                            title="Commission: {{ number_format($commissionPercent, 2) }}%">
                        </div>
                    @endif

                    @if ($brokeragePercent > 0)
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $brokeragePercent }}%"
                            data-bs-toggle="tooltip" title="Brokerage: {{ number_format($brokeragePercent, 2) }}%">
                        </div>
                    @endif

                    @if ($whtPercent > 0)
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $whtPercent }}%"
                            data-bs-toggle="tooltip" title="WHT: {{ number_format($whtPercent, 2) }}%">
                        </div>
                    @endif

                    @if ($frontingPercent > 0)
                        <div class="progress-bar bg-secondary" role="progressbar"
                            style="width: {{ $frontingPercent }}%" data-bs-toggle="tooltip"
                            title="Fronting: {{ number_format($frontingPercent, 2) }}%">
                        </div>
                    @endif

                    @if ($netPercent > 0)
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $netPercent }}%"
                            data-bs-toggle="tooltip" title="Net: {{ number_format($netPercent, 2) }}%">
                        </div>
                    @endif
                </div>
            </div>

            <div class="deductions-legend">
                <div class="legend-item">
                    <span class="legend-color bg-primary"></span>
                    <small>Commission ({{ number_format($commissionPercent, 1) }}%)</small>
                </div>
                @if ($brokeragePercent > 0)
                    <div class="legend-item">
                        <span class="legend-color bg-info"></span>
                        <small>Brokerage ({{ number_format($brokeragePercent, 1) }}%)</small>
                    </div>
                @endif
                <div class="legend-item">
                    <span class="legend-color bg-warning"></span>
                    <small>WHT ({{ number_format($whtPercent, 1) }}%)</small>
                </div>
                @if ($frontingPercent > 0)
                    <div class="legend-item">
                        <span class="legend-color bg-secondary"></span>
                        <small>Fronting ({{ number_format($frontingPercent, 1) }}%)</small>
                    </div>
                @endif
                <div class="legend-item">
                    <span class="legend-color bg-success"></span>
                    <small>Net ({{ number_format($netPercent, 1) }}%)</small>
                </div>
            </div>

            <div class="financial-item total-item mt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="financial-label fw-bold">Total Deductions</span>
                    <span class="financial-value text-danger fw-bold">
                        {{ number_format($totalDeductions, 2) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Key Metrics --}}
        <div class="financial-section border-0">
            <div class="section-header">
                <small class="text-muted text-uppercase fw-semibold">Key Metrics</small>
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <div class="metric-card">
                        <div class="metric-icon bg-primary-subtle">
                            <i class="ri-team-line text-primary"></i>
                        </div>
                        <div class="metric-content">
                            <small class="text-muted d-block">Reinsurers</small>
                            <strong class="fs-5">{{ $coverPart->count() }}</strong>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="metric-card">
                        <div class="metric-icon bg-success-subtle">
                            <i class="ri-percent-line text-success"></i>
                        </div>
                        <div class="metric-content">
                            <small class="text-muted d-block">Retention</small>
                            <strong class="fs-5">{{ number_format(100 - $totalPlacedShare, 2) }}%</strong>
                        </div>
                    </div>
                </div>

                @if ($cover->no_of_installments > 1)
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-icon bg-info-subtle">
                                <i class="ri-calendar-check-line text-info"></i>
                            </div>
                            <div class="metric-content">
                                <small class="text-muted d-block">Installments</small>
                                <strong class="fs-5">{{ $cover->no_of_installments }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-icon bg-warning-subtle">
                                <i class="ri-coins-line text-warning"></i>
                            </div>
                            <div class="metric-content">
                                <small class="text-muted d-block">Per Installment</small>
                                <strong
                                    class="fs-6">{{ number_format($netPremium / $cover->no_of_installments, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        // Initialize tooltips for the deductions chart
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush

<style>
    /* Financial Summary Styles */
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--cover-primary) 0%, #0a58ca 100%);
    }

    .financial-section {
        padding: 1rem;
        border-bottom: 1px solid var(--cover-border);
    }

    .financial-section:last-child {
        border-bottom: none;
    }

    .section-header {
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #f1f3f5;
    }

    .financial-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .financial-item:last-child {
        border-bottom: none;
    }

    .financial-item.total-item {
        background: #f8f9fa;
        padding: 1rem;
        margin: 0 -1rem;
        padding-left: 2rem;
        padding-right: 2rem;
        border-radius: 8px;
        border: none;
    }

    .financial-label {
        font-size: 0.875rem;
        color: #495057;
        display: flex;
        align-items: center;
    }

    .financial-value {
        font-size: 0.9375rem;
        color: #212529;
        font-weight: 600;
        text-align: right;
    }

    .financial-value small {
        font-size: 0.75rem;
        font-weight: 400;
        margin-top: 2px;
    }

    /* Deductions Chart */
    .deductions-chart {
        padding: 0.5rem 0;
    }

    .progress {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .progress-bar:hover {
        filter: brightness(1.1);
    }

    .deductions-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: center;
        padding: 0.5rem 0;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        display: inline-block;
    }

    /* Metric Cards */
    .metric-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.2s ease;
    }

    .metric-card:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }

    .metric-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .metric-content {
        flex: 1;
        min-width: 0;
    }

    .metric-content small {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .metric-content strong {
        display: block;
        line-height: 1.2;
        margin-top: 2px;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .financial-section {
            padding: 0.75rem;
        }

        .financial-item.total-item {
            margin: 0 -0.75rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .metric-card {
            padding: 0.5rem;
        }

        .metric-icon {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }

        .deductions-legend {
            gap: 0.5rem;
            font-size: 0.75rem;
        }
    }

    /* Print styles */
    @media print {
        .financial-section {
            page-break-inside: avoid;
        }

        .metric-card:hover {
            transform: none;
        }
    }
</style>
