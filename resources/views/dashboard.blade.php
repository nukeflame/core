@extends('layouts.app', [
    'pageTitle' => 'Dashboard - ' . $company->company_name,
])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="dashboard-header-title">Portfolio Dashboard</h1>
                <p class="dashboard-header-subtitle">Real-time insights into your portfolio performance</p>
            </div>
            <div class="dashboard-header-meta">
                <div class="period-badge">
                    <i class="ri-calendar-line"></i>
                    <span>Period: 2025</span>
                </div>
                <div class="period-badge">
                    <i class="ri-time-line"></i>
                    <span>Last updated: {{ date('M d, Y H:i') }}</span>
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
                {{ number_format(212800000, 0) }}
            </div>
            <div class="metric-change positive">
                <i class="ri-arrow-up-line"></i>
                <span>+5.2% vs Last Year</span>
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
                {{ number_format(31845000, 0) }}
            </div>
            <div class="metric-change positive">
                <i class="ri-arrow-up-line"></i>
                <span>+3.8% vs Budget</span>
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
                {{ number_format(21280000, 0) }}
            </div>
            <div class="metric-change neutral">
                <i class="ri-subtract-line"></i>
                <span>15% Avg Rate</span>
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
            <div class="metric-value">63.2<span style="font-size: 20px;">%</span></div>
            <div class="metric-change positive">
                <i class="ri-arrow-down-line"></i>
                <span>-4.1% vs Target</span>
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
            <div class="metric-value">{{ $totalCovers['amount'] ?? 1 }}</div>
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
            <div class="metric-value">87<span style="font-size: 20px;">%</span></div>
            <div class="metric-change positive">
                <i class="ri-arrow-up-line"></i>
                <span>Above Industry Avg (84%)</span>
            </div>
        </div>
    </div>

    <!-- Portfolio Mix -->
    <div class="section-card">
        <div class="section-header">
            <h2 class="section-title">Business Mix Overview</h2>
            <div class="section-actions">
                <button class="btn-filter active" data-period="ytd">YTD</button>
                <button class="btn-filter" data-period="quarter">Q4</button>
                <button class="btn-filter" data-period="month">Nov</button>
            </div>
        </div>
        <div class="section-body">
            <div class="business-split">
                <div class="business-split-item">
                    <div class="business-split-value">KES 127.4M</div>
                    <div class="business-split-label">Facultative</div>
                    <div class="business-split-percentage">59.8%</div>
                </div>
                <div class="business-split-divider"></div>
                <div class="business-split-item">
                    <div class="business-split-value">KES 85.4M</div>
                    <div class="business-split-label">Treaty</div>
                    <div class="business-split-percentage">40.2%</div>
                </div>
            </div>

            <div class="portfolio-grid mt-4">
                <!-- Facultative Proportional -->
                <div class="portfolio-item">
                    <div class="portfolio-item-header">
                        <span class="portfolio-type">Facultative - Proportional</span>
                        <span class="portfolio-count">{{ $totalTPRCovers['amount'] ?? 0 }}</span>
                    </div>
                    <div class="portfolio-metrics">
                        <div>
                            <div class="portfolio-metric-label">GWP</div>
                            <div class="portfolio-metric-value">KES 75.3M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES 11.3M</div>
                        </div>
                    </div>
                    <div class="portfolio-progress">
                        <div class="portfolio-progress-label">
                            <span>Budget Achievement</span>
                            <span class="fw-semibold">72%</span>
                        </div>
                        <div class="progress-bar-modern">
                            <div class="progress-bar-fill" style="width: 72%"></div>
                        </div>
                    </div>
                </div>

                <!-- Facultative Non-Proportional -->
                <div class="portfolio-item">
                    <div class="portfolio-item-header">
                        <span class="portfolio-type">Facultative - Non-Proportional</span>
                        <span class="portfolio-count">{{ $totalFacCovers['amount'] ?? 0 }}</span>
                    </div>
                    <div class="portfolio-metrics">
                        <div>
                            <div class="portfolio-metric-label">GWP</div>
                            <div class="portfolio-metric-value">KES 52.1M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES 7.8M</div>
                        </div>
                    </div>
                    <div class="portfolio-progress">
                        <div class="portfolio-progress-label">
                            <span>Budget Achievement</span>
                            <span class="fw-semibold">68%</span>
                        </div>
                        <div class="progress-bar-modern">
                            <div class="progress-bar-fill" style="width: 68%"></div>
                        </div>
                    </div>
                </div>

                <!-- Treaty Proportional -->
                <div class="portfolio-item">
                    <div class="portfolio-item-header">
                        <span class="portfolio-type">Treaty - Proportional</span>
                        <span class="portfolio-count">{{ $totalTPRCovers['amount'] ?? 0 }}</span>
                    </div>
                    <div class="portfolio-metrics">
                        <div>
                            <div class="portfolio-metric-label">GWP</div>
                            <div class="portfolio-metric-value">KES 60.3M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES 9.0M</div>
                        </div>
                    </div>
                    <div class="portfolio-progress">
                        <div class="portfolio-progress-label">
                            <span>Budget Achievement</span>
                            <span class="fw-semibold">60%</span>
                        </div>
                        <div class="progress-bar-modern">
                            <div class="progress-bar-fill" style="width: 60%"></div>
                        </div>
                    </div>
                </div>

                <!-- Treaty Non-Proportional -->
                <div class="portfolio-item">
                    <div class="portfolio-item-header">
                        <span class="portfolio-type">Treaty - Non-Proportional</span>
                        <span class="portfolio-count">{{ $totalTNPCovers['amount'] ?? 0 }}</span>
                    </div>
                    <div class="portfolio-metrics">
                        <div>
                            <div class="portfolio-metric-label">GWP</div>
                            <div class="portfolio-metric-value">KES 25.1M</div>
                        </div>
                        <div>
                            <div class="portfolio-metric-label">Income</div>
                            <div class="portfolio-metric-value">KES 3.8M</div>
                        </div>
                    </div>
                    <div class="portfolio-progress">
                        <div class="portfolio-progress-label">
                            <span>Budget Achievement</span>
                            <span class="fw-semibold">70%</span>
                        </div>
                        <div class="progress-bar-modern">
                            <div class="progress-bar-fill" style="width: 70%"></div>
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
                <div class="activity-item">
                    <div class="activity-icon new-cover">
                        <i class="ri-file-add-line"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">New Facultative Cover Created</div>
                        <div class="activity-description">Property - Commercial Building, Nairobi</div>
                        <div class="activity-time">2 hours ago</div>
                    </div>
                    <div class="activity-amount">KES 15.5M</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon renewal">
                        <i class="ri-repeat-line"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Treaty Renewed</div>
                        <div class="activity-description">Quota Share Treaty - 30% Participation</div>
                        <div class="activity-time">5 hours ago</div>
                    </div>
                    <div class="activity-amount">KES 45.2M</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon payment">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Commission Received</div>
                        <div class="activity-description">Q4 Commission Payment - Multiple Treaties</div>
                        <div class="activity-time">1 day ago</div>
                    </div>
                    <div class="activity-amount">KES 2.8M</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon claim">
                        <i class="ri-alert-line"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Claim Notification</div>
                        <div class="activity-description">Motor Fleet - Excess of Loss Layer</div>
                        <div class="activity-time">2 days ago</div>
                    </div>
                    <div class="activity-amount">KES 8.5M</div>
                </div>
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
                console.log('Loading data for period:', period);
                // Add AJAX call to reload data for selected period
            });

            // Animate progress bars on page load
            setTimeout(function() {
                $('.progress-bar-fill').each(function() {
                    const width = $(this).attr('style').match(/width:\s*(\d+)%/)[1];
                    $(this).css('width', '0%');
                    setTimeout(() => {
                        $(this).css('width', width + '%');
                    }, 100);
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
