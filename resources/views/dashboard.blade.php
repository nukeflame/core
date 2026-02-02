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
                <span>{{ ($metrics['gwpChange'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($metrics['gwpChange'] ?? 0, 1) }}% vs Last Year</span>
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
                <span>{{ ($metrics['netPremiumChange'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($metrics['netPremiumChange'] ?? 0, 1) }}% vs Last Year</span>
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
            <div class="metric-value">{{ number_format($metrics['lossRatio'] ?? 0, 1) }}<span style="font-size: 20px;">%</span></div>
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
            <div class="metric-value">{{ number_format($metrics['renewalRate'] ?? 0, 0) }}<span style="font-size: 20px;">%</span></div>
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
                <button class="btn-filter {{ ($currentPeriod ?? 'ytd') === 'ytd' ? 'active' : '' }}" data-period="ytd">YTD</button>
                <button class="btn-filter {{ ($currentPeriod ?? 'ytd') === 'quarter' ? 'active' : '' }}" data-period="quarter">Q{{ now()->quarter }}</button>
                <button class="btn-filter {{ ($currentPeriod ?? 'ytd') === 'month' ? 'active' : '' }}" data-period="month">{{ now()->format('M') }}</button>
            </div>
        </div>
        <div class="section-body">
            <div class="business-split">
                <div class="business-split-item">
                    <div class="business-split-value">KES {{ number_format(($businessMix['facultative']['total'] ?? 0) / 1000000, 1) }}M</div>
                    <div class="business-split-label">Facultative</div>
                    <div class="business-split-percentage">{{ number_format($businessMix['facultative']['percentage'] ?? 0, 1) }}%</div>
                </div>
                <div class="business-split-divider"></div>
                <div class="business-split-item">
                    <div class="business-split-value">KES {{ number_format(($businessMix['treaty']['total'] ?? 0) / 1000000, 1) }}M</div>
                    <div class="business-split-label">Treaty</div>
                    <div class="business-split-percentage">{{ number_format($businessMix['treaty']['percentage'] ?? 0, 1) }}%</div>
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
                            <div class="portfolio-metric-value">KES {{ number_format(($businessMix['fpr']['gwp'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES {{ number_format(($businessMix['fpr']['income'] ?? 0) / 1000000, 1) }}M</div>
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
                            <div class="portfolio-metric-value">KES {{ number_format(($businessMix['fnp']['gwp'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES {{ number_format(($businessMix['fnp']['income'] ?? 0) / 1000000, 1) }}M</div>
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
                            <div class="portfolio-metric-value">KES {{ number_format(($businessMix['tpr']['gwp'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES {{ number_format(($businessMix['tpr']['income'] ?? 0) / 1000000, 1) }}M</div>
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
                            <div class="portfolio-metric-value">KES {{ number_format(($businessMix['tnp']['gwp'] ?? 0) / 1000000, 1) }}M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES {{ number_format(($businessMix['tnp']['income'] ?? 0) / 1000000, 1) }}M</div>
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
                    url: '{{ route("dashboard.metrics") }}',
                    method: 'GET',
                    data: { period: period },
                    success: function(response) {
                        if (response.success) {
                            updateDashboardMetrics(response.metrics, response.businessMix);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to load metrics:', xhr);
                    },
                    complete: function() {
                        $('.business-split-value, .portfolio-metric-value').removeClass('loading');
                    }
                });
            });

            function updateDashboardMetrics(metrics, businessMix) {
                const facTotal = (businessMix.facultative?.total || 0) / 1000000;
                const treatyTotal = (businessMix.treaty?.total || 0) / 1000000;
                
                $('.business-split-item:first .business-split-value').text('KES ' + facTotal.toFixed(1) + 'M');
                $('.business-split-item:first .business-split-percentage').text((businessMix.facultative?.percentage || 0).toFixed(1) + '%');
                
                $('.business-split-item:last .business-split-value').text('KES ' + treatyTotal.toFixed(1) + 'M');
                $('.business-split-item:last .business-split-percentage').text((businessMix.treaty?.percentage || 0).toFixed(1) + '%');
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
