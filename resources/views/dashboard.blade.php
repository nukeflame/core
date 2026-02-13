@extends('layouts.app', [
    'pageTitle' => 'Dashboard - ' . $company->company_name,
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <!-- Dashboard Header -->
    <div class="row">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb bi-dashboard">
            <div class="dashboard">
                <div class="header">
                    <div>
                        <p class="fw-semibold fs-18 mb-0">Welcome back, {{ $firstName }} | {{ date('F j, Y') }}</p>
                        <span class="fs-semibold text-muted">Easily track key metrics and monitor performance across your
                            entire reinsurance portfolio.</span>
                    </div>
                    <div class="btn-list mt-md-0 mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="metrics-grid">
        <!-- Gross Written Premium -->
        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon primary">
                    <i class="ri-money-dollar-circle-line"></i>
                </div>
            </div>
            <div class="metric-label">Gross Written Premium (GWP)</div>
            <div class="metric-value">
                <span class="metric-value-currency">KES</span>
                {{ number_format($metrics['gwp'] ?? 0, 0) }}
            </div>
            <div class="metric-change {{ ($metrics['gwpChange'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                <i class="ri-arrow-{{ ($metrics['gwpChange'] ?? 0) >= 0 ? 'up' : 'down' }}-line"></i>
                <span>{{ ($metrics['gwpChange'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($metrics['gwpChange'] ?? 0, 1) }}%
                    vs Last Year</span>
            </div>
        </div>

        <!-- Net Premium Income -->
        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon success">
                    <i class="ri-hand-coin-line"></i>
                </div>
            </div>
            <div class="metric-label">Net Premium Income</div>
            <div class="metric-value">
                <span class="metric-value-currency">KES</span>
                {{ number_format($metrics['netPremium'] ?? 0, 0) }}
            </div>
            <div class="metric-change {{ ($metrics['netPremiumChange'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                <i class="ri-arrow-{{ ($metrics['netPremiumChange'] ?? 0) >= 0 ? 'up' : 'down' }}-line"></i>
                <span>{{ ($metrics['netPremiumChange'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($metrics['netPremiumChange'] ?? 0, 1) }}%
                    vs Last Year</span>
            </div>
        </div>

        <!-- Commission Income -->
        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon warning">
                    <i class="ri-percent-line"></i>
                </div>
            </div>
            <div class="metric-label">Commission Income</div>
            <div class="metric-value">
                <span class="metric-value-currency">KES</span>
                {{ number_format($metrics['commissionIncome'] ?? 0, 0) }}
            </div>
            <div class="metric-change neutral">
                <i class="ri-subtract-line"></i>
                <span>{{ number_format($avgCommRate ?? 0, 1) }}% Avg Rate</span>
            </div>
        </div>

        <!-- Loss Ratio -->
        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon danger">
                    <i class="ri-alert-line"></i>
                </div>
            </div>
            <div class="metric-label">Portfolio Loss Ratio</div>
            <div class="metric-value">{{ number_format($metrics['lossRatio'] ?? 0, 1) }}<span
                    style="font-size: 20px;">%</span></div>
            <div class="metric-change {{ ($metrics['lossRatioChange'] ?? 0) <= 0 ? 'positive' : 'negative' }}">
                <i class="ri-arrow-{{ ($metrics['lossRatioChange'] ?? 0) <= 0 ? 'down' : 'up' }}-line"></i>
                <span>{{ number_format(abs($metrics['lossRatioChange'] ?? 0), 1) }}% vs Last Year</span>
            </div>
        </div>

        <!-- Active Covers -->
        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon info">
                    <i class="ri-file-shield-2-line"></i>
                </div>
            </div>
            <div class="metric-label">Active Covers</div>
            <div class="metric-value">{{ $totalCovers['amount'] ?? 0 }}</div>
            <div class="metric-change positive">
                <i class="ri-arrow-up-line"></i>
                <span>{{ $totalDebitedCovers['amount'] ?? 0 }} Debited</span>
            </div>
        </div>

        <!-- Renewal Rate -->
        <div class="metric-card">
            <div class="metric-card-header">
                <div class="metric-icon success">
                    <i class="ri-loop-right-line"></i>
                </div>
            </div>
            <div class="metric-label">Renewal Rate</div>
            <div class="metric-value">{{ number_format($metrics['renewalRate'] ?? 0, 0) }}<span
                    style="font-size: 20px;">%</span></div>
            <div class="metric-change {{ ($metrics['renewalRate'] ?? 0) >= 84 ? 'positive' : 'neutral' }}">
                <i class="ri-arrow-{{ ($metrics['renewalRate'] ?? 0) >= 84 ? 'up' : 'right' }}-line"></i>
                <span>{{ ($metrics['renewalRate'] ?? 0) >= 84 ? 'Above' : 'Below' }} Industry Avg (84%)</span>
            </div>
        </div>
    </div>

    <!-- Portfolio Mix -->
    <div class="section-card">
        <div class="section-header">
            <h2 class="section-title">Business Mix Overview</h2>
            <div class="section-actions">
                <button class="btn-filter {{ ($currentPeriod ?? 'ytd') === 'ytd' ? 'active' : '' }}"
                    data-period="ytd">YTD</button>
                <button class="btn-filter {{ ($currentPeriod ?? 'ytd') === 'quarter' ? 'active' : '' }}"
                    data-period="quarter">Q{{ now()->quarter }}</button>
                <button class="btn-filter {{ ($currentPeriod ?? 'ytd') === 'month' ? 'active' : '' }}"
                    data-period="month">{{ now()->format('M') }}</button>
            </div>
        </div>
        <div class="section-body">
            <div class="business-split">
                <div class="business-split-item">
                    <div class="business-split-value">KES
                        {{ number_format(($businessMix['facultative']['total'] ?? 0) / 1000000, 1) }}M</div>
                    <div class="business-split-label">Facultative</div>
                    <div class="business-split-percentage">
                        {{ number_format($businessMix['facultative']['percentage'] ?? 0, 1) }}%</div>
                </div>
                <div class="business-split-divider"></div>
                <div class="business-split-item">
                    <div class="business-split-value">KES
                        {{ number_format(($businessMix['treaty']['total'] ?? 0) / 1000000, 1) }}M</div>
                    <div class="business-split-label">Treaty</div>
                    <div class="business-split-percentage">
                        {{ number_format($businessMix['treaty']['percentage'] ?? 0, 1) }}%</div>
                </div>
            </div>

            <div class="portfolio-grid mt-4">
                <!-- Facultative Proportional -->
                <div class="portfolio-item">
                    <div class="portfolio-item-header">
                        <span class="portfolio-type">Facultative - Proportional</span>
                        <span class="portfolio-count">{{ $coverCounts['fpr']['amount'] ?? 0 }}</span>
                    </div>
                    <div class="portfolio-metrics">
                        <div>
                            <div class="portfolio-metric-label">GWP</div>
                            <div class="portfolio-metric-value">KES
                                {{ number_format(($businessMix['fpr']['gwp'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES
                                {{ number_format(($businessMix['fpr']['income'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                    </div>
                    @php
                        $fprPercentage = $businessMix['fpr']['percentage'] ?? 0;
                    @endphp
                    <div class="portfolio-progress">
                        <div class="portfolio-progress-label">
                            <span>Portfolio Share</span>
                            <span class="fw-semibold">{{ number_format($fprPercentage, 0) }}%</span>
                        </div>
                        <div class="progress-bar-modern">
                            <div class="progress-bar-fill" style="width: {{ min($fprPercentage, 100) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Facultative Non-Proportional -->
                <div class="portfolio-item">
                    <div class="portfolio-item-header">
                        <span class="portfolio-type">Facultative - Non-Proportional</span>
                        <span class="portfolio-count">{{ $coverCounts['fnp']['amount'] ?? 0 }}</span>
                    </div>
                    <div class="portfolio-metrics">
                        <div>
                            <div class="portfolio-metric-label">GWP</div>
                            <div class="portfolio-metric-value">KES
                                {{ number_format(($businessMix['fnp']['gwp'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES
                                {{ number_format(($businessMix['fnp']['income'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                    </div>
                    @php
                        $fnpPercentage = $businessMix['fnp']['percentage'] ?? 0;
                    @endphp
                    <div class="portfolio-progress">
                        <div class="portfolio-progress-label">
                            <span>Portfolio Share</span>
                            <span class="fw-semibold">{{ number_format($fnpPercentage, 0) }}%</span>
                        </div>
                        <div class="progress-bar-modern">
                            <div class="progress-bar-fill" style="width: {{ min($fnpPercentage, 100) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Treaty Proportional -->
                <div class="portfolio-item">
                    <div class="portfolio-item-header">
                        <span class="portfolio-type">Treaty - Proportional</span>
                        <span class="portfolio-count">{{ $coverCounts['tpr']['amount'] ?? 0 }}</span>
                    </div>
                    <div class="portfolio-metrics">
                        <div>
                            <div class="portfolio-metric-label">GWP</div>
                            <div class="portfolio-metric-value">KES
                                {{ number_format(($businessMix['tpr']['gwp'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES
                                {{ number_format(($businessMix['tpr']['income'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                    </div>
                    @php
                        $tprPercentage = $businessMix['tpr']['percentage'] ?? 0;
                    @endphp
                    <div class="portfolio-progress">
                        <div class="portfolio-progress-label">
                            <span>Portfolio Share</span>
                            <span class="fw-semibold">{{ number_format($tprPercentage, 0) }}%</span>
                        </div>
                        <div class="progress-bar-modern">
                            <div class="progress-bar-fill" style="width: {{ min($tprPercentage, 100) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Treaty Non-Proportional -->
                <div class="portfolio-item">
                    <div class="portfolio-item-header">
                        <span class="portfolio-type">Treaty - Non-Proportional</span>
                        <span class="portfolio-count">{{ $coverCounts['tnp']['amount'] ?? 0 }}</span>
                    </div>
                    <div class="portfolio-metrics">
                        <div>
                            <div class="portfolio-metric-label">GWP</div>
                            <div class="portfolio-metric-value">KES
                                {{ number_format(($businessMix['tnp']['gwp'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES
                                {{ number_format(($businessMix['tnp']['income'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                    </div>
                    @php
                        $tnpPercentage = $businessMix['tnp']['percentage'] ?? 0;
                    @endphp
                    <div class="portfolio-progress">
                        <div class="portfolio-progress-label">
                            <span>Portfolio Share</span>
                            <span class="fw-semibold">{{ number_format($tnpPercentage, 0) }}%</span>
                        </div>
                        <div class="progress-bar-modern">
                            <div class="progress-bar-fill" style="width: {{ min($tnpPercentage, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section-card">
        <div class="section-header">
            <h2 class="section-title">Quick Actions</h2>
        </div>
        <div class="section-body">
            <div class="quick-actions-grid">
                <button class="quick-action-btn" onclick="window.location.href=''">
                    <div class="quick-action-icon">
                        <i class="ri-add-circle-line"></i>
                    </div>
                    <div class="quick-action-content">
                        <h4>New Cover</h4>
                        <p>Create facultative cover</p>
                    </div>
                </button>

                <button class="quick-action-btn" onclick="window.location.href=''">
                    <div class="quick-action-icon">
                        <i class="ri-add-circle-line"></i>
                    </div>
                    <div class="quick-action-content">
                        <h4>New Prospect</h4>
                        <p>Create a lead prospect</p>
                    </div>
                </button>

                <button class="quick-action-btn" onclick="window.location.href=''">
                    <div class="quick-action-icon">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                    <div class="quick-action-content">
                        <h4>View All Covers</h4>
                        <p>Browse portfolio</p>
                    </div>
                </button>

                <button class="quick-action-btn" onclick="window.location.href=''">
                    <div class="quick-action-icon">
                        <i class="ri-file-chart-line"></i>
                    </div>
                    <div class="quick-action-content">
                        <h4>Generate Report</h4>
                        <p>Export analytics</p>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="section-card">
        <div class="section-header">
            <h2 class="section-title">Recent Activity</h2>
            <a href="#" class="btn-filter">View All</a>
        </div>
        <div class="section-body">
            <div class="activity-feed">
                @forelse($recentActivity ?? [] as $activity)
                    <div class="activity-item">
                        <div class="activity-icon {{ $activity['iconClass'] ?? 'new-cover' }}">
                            <i class="{{ $activity['icon'] ?? 'ri-file-add-line' }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $activity['title'] ?? 'Activity' }}</div>
                            <div class="activity-description">{{ $activity['description'] ?? '' }}</div>
                            <div class="activity-time">{{ $activity['time'] ?? 'Recently' }}</div>
                        </div>
                        <div class="activity-amount">{{ $activity['amount'] ?? '' }}</div>
                    </div>
                @empty
                    <div class="activity-item">
                        <div class="activity-icon new-cover">
                            <i class="ri-information-line"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">No Recent Activity</div>
                            <div class="activity-description">Your recent activities will appear here</div>
                            <div class="activity-time">-</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.btn-filter[data-period]').on('click', function() {
                $('.btn-filter[data-period]').removeClass('active');
                $(this).addClass('active');

                const period = $(this).data('period');

                $('.business-split-value, .portfolio-metric-value').addClass('loading');

                $.ajax({
                    url: '{{ route('dashboard.metrics') }}',
                    method: 'GET',
                    data: {
                        period: period
                    },
                    success: function(response) {
                        if (response.success) {
                            updateDashboardMetrics(response.metrics, response.businessMix);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load metrics:', xhr);
                    },
                    complete: function() {
                        $('.business-split-value, .portfolio-metric-value').removeClass(
                            'loading');
                    }
                });
            });

            function updateDashboardMetrics(metrics, businessMix) {
                const facTotal = (businessMix.facultative?.total || 0) / 1000000;
                const treatyTotal = (businessMix.treaty?.total || 0) / 1000000;

                $('.business-split-item:first .business-split-value').text('KES ' + facTotal.toFixed(1) + 'M');
                $('.business-split-item:first .business-split-percentage').text((businessMix.facultative
                    ?.percentage || 0).toFixed(1) + '%');

                $('.business-split-item:last .business-split-value').text('KES ' + treatyTotal.toFixed(1) + 'M');
                $('.business-split-item:last .business-split-percentage').text((businessMix.treaty?.percentage || 0)
                    .toFixed(1) + '%');
            }

            setTimeout(function() {
                $('.progress-bar-fill').each(function() {
                    const style = $(this).attr('style');
                    if (style) {
                        const match = style.match(/width:\s*([\d.]+)%/);
                        if (match) {
                            const width = match[1];
                            $(this).css('width', '0%');
                            setTimeout(() => {
                                $(this).css('width', width + '%');
                            }, 100);
                        }
                    }
                });
            }, 300);

            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 600);
                }
            });
        });
    </script>
@endpush
{{-- 

@extends('layouts.app', ['pageTitle' => 'Portfolio Intelligence - ' . $company->company_name])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reinsurance-modern.css') }}">
    <style>
        :root {
            --re-success: #10b981;
            --re-danger: #ef4444;
            --re-warning: #f59e0b;
            --re-primary: #3b82f6;
        }

        .dashboard-wrapper {
            padding: 1.5rem;
            background: #f8fafc;
        }

        .metric-grid-modern {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
        }

        .re-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem;
            transition: transform 0.2s;
        }

        .re-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .status-dot {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .progress-thin {
            height: 6px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
        }

        .trend-up {
            color: var(--re-success);
            font-size: 0.85rem;
        }

        .trend-down {
            color: var(--re-danger);
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-wrapper">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h4 class="fw-bold mb-1">Portfolio Intelligence</h4>
                <p class="text-muted mb-0">Market View: {{ date('D, M j, Y') }} | <span
                        class="text-primary">{{ $company->company_name }}</span></p>
            </div>
            <div class="btn-group shadow-sm">
                <button class="btn btn-white border px-4 active">Live View</button>
                <button class="btn btn-white border px-4">Consolidated</button>
            </div>
        </div>

        <div class="metric-grid-modern mb-4">
            <div class="re-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="p-2 bg-light rounded-3 text-primary"><i class="ri-scales-3-line fs-20"></i></div>
                    @php $cr = $metrics['combinedRatio'] ?? 0; @endphp
                    <span
                        class="badge {{ $cr < 100 ? 'bg-success-transparent text-success' : 'bg-danger-transparent text-danger' }}">
                        {{ $cr < 100 ? 'Underwriting Profit' : 'Technical Deficit' }}
                    </span>
                </div>
                <p class="text-muted small fw-medium mb-1">Combined Operating Ratio</p>
                <h3 class="fw-bold mb-2">{{ number_format($cr, 1) }}%</h3>
                <div class="progress-thin">
                    <div class="progress-bar {{ $cr < 100 ? 'bg-success' : 'bg-danger' }}"
                        style="width: {{ min($cr, 100) }}%"></div>
                </div>
            </div>

            <div class="re-card">
                <div class="p-2 bg-light rounded-3 text-info d-inline-block mb-3"><i class="ri-shield-check-line fs-20"></i>
                </div>
                <p class="text-muted small fw-medium mb-1">Net Retention Ratio</p>
                <h3 class="fw-bold mb-2">{{ number_format($metrics['retentionRatio'] ?? 0, 1) }}%</h3>
                <div
                    class="d-flex align-items-center {{ ($metrics['retentionChange'] ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="ri-arrow-{{ ($metrics['retentionChange'] ?? 0) >= 0 ? 'up' : 'down' }}-s-fill"></i>
                    <span>{{ abs($metrics['retentionChange'] ?? 0) }}% vs Target</span>
                </div>
            </div>

            <div class="re-card">
                <div class="p-2 bg-light rounded-3 text-warning d-inline-block mb-3"><i class="ri-coins-line fs-20"></i>
                </div>
                <p class="text-muted small fw-medium mb-1">Gross Written Premium</p>
                <h3 class="fw-bold mb-2"><small class="fs-14 fw-normal">KES</small>
                    {{ number_format(($metrics['gwp'] ?? 0) / 1000000, 1) }}M</h3>
                <p class="mb-0 text-muted small"><span class="status-dot bg-warning"></span>YTD Performance</p>
            </div>

            <div class="re-card">
                <div class="p-2 bg-light rounded-3 text-danger d-inline-block mb-3"><i class="ri-pulse-line fs-20"></i>
                </div>
                <p class="text-muted small fw-medium mb-1">Net Loss Ratio</p>
                <h3 class="fw-bold mb-2">{{ number_format($metrics['lossRatio'] ?? 0, 1) }}%</h3>
                <span class="trend-{{ ($metrics['lossRatioChange'] ?? 0) <= 0 ? 'up' : 'down' }}">
                    {{ ($metrics['lossRatioChange'] ?? 0) <= 0 ? 'Improving' : 'Deteriorating' }} by
                    {{ abs($metrics['lossRatioChange'] ?? 0) }}%
                </span>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="re-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Business Mix Analysis</h5>
                        <select class="form-select form-select-sm w-auto border-0 bg-light">
                            <option>Current Year</option>
                            <option>Last Quarter</option>
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 small text-muted">SEGMENT</th>
                                    <th class="border-0 small text-muted">GWP (M)</th>
                                    <th class="border-0 small text-muted">LOSS RATIO</th>
                                    <th class="border-0 small text-muted text-end">PORTFOLIO %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (['fpr' => 'Fac - Proportional', 'fnp' => 'Fac - Non Proportional', 'tpr' => 'Treaty - Proportional', 'tnp' => 'Treaty - Non Proportional'] as $key => $label)
                                    <tr>
                                        <td><span class="fw-semibold text-dark">{{ $label }}</span></td>
                                        <td>KES {{ number_format(($businessMix[$key]['gwp'] ?? 0) / 1000000, 2) }}</td>
                                        <td>
                                            <span class="d-flex align-items-center">
                                                <span
                                                    class="status-dot {{ ($businessMix[$key]['lossRatio'] ?? 0) > 75 ? 'bg-danger' : 'bg-success' }}"></span>
                                                {{ number_format($businessMix[$key]['lossRatio'] ?? 0, 1) }}%
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <span
                                                    class="me-2 small">{{ number_format($businessMix[$key]['percentage'] ?? 0, 0) }}%</span>
                                                <div class="progress-thin w-50">
                                                    <div class="progress-bar bg-primary"
                                                        style="width: {{ $businessMix[$key]['percentage'] ?? 0 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="re-card h-100">
                    <h5 class="fw-bold mb-4">Underwriting Actions</h5>
                    <div class="d-grid gap-3">
                        <a href="/"
                            class="btn btn-primary d-flex align-items-center justify-content-between p-3 rounded-3 shadow-none">
                            <div class="text-start">
                                <div class="fw-bold">New Placement</div>
                                <div class="small opacity-75">Create Facultative Cover</div>
                            </div>
                            <i class="ri-add-line fs-24"></i>
                        </a>

                        <a href="/"
                            class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-3 border">
                            <div class="text-start text-dark">
                                <div class="fw-bold">Quarterly Report</div>
                                <div class="small text-muted">Export Solvency II Data</div>
                            </div>
                            <i class="ri-file-chart-line fs-24 text-muted"></i>
                        </a>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <h6 class="small fw-bold text-muted text-uppercase mb-3">Recent Underwriting Feed</h6>
                        @forelse($recentActivity ?? [] as $activity)
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="status-dot bg-info"></span>
                                </div>
                                <div class="ms-2">
                                    <div class="small fw-bold text-dark">{{ $activity['title'] }}</div>
                                    <div class="text-muted smaller">{{ $activity['time'] }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small">No pending notifications.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}
