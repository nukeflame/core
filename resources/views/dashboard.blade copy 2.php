@extends('layouts.app', [
    'pageTitle' => 'Dashboard - ' . $company->company_name,
])

@section('content')
    <style>
        /* ============================================
               BOOTSTRAP 5 ENHANCED REINSURANCE DASHBOARD
               Custom overrides and enhancements only
               ============================================ */

        :root {
            --bs-primary: #0052CC;
            --bs-primary-dark: #1a2332;
            --bs-success: #00875A;
            --bs-warning: #FF8B00;
            --bs-danger: #DE350B;
            --bs-info: #0065FF;
            --bs-gray-50: #FAFBFC;
            --bs-gray-100: #F4F5F7;
            --bs-gray-200: #EBECF0;
        }

        /* Enhanced metric cards */
        .metric-card {
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
            height: 100%;
        }

        .metric-card:hover {
            border-left-color: var(--bs-primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 24px;
        }

        .metric-icon.primary {
            background: rgba(0, 82, 204, 0.1);
            color: var(--bs-primary);
        }

        .metric-icon.success {
            background: rgba(0, 135, 90, 0.1);
            color: var(--bs-success);
        }

        .metric-icon.warning {
            background: rgba(255, 139, 0, 0.1);
            color: var(--bs-warning);
        }

        .metric-icon.danger {
            background: rgba(222, 53, 11, 0.1);
            color: var(--bs-danger);
        }

        .metric-icon.info {
            background: rgba(0, 101, 255, 0.1);
            color: var(--bs-info);
        }

        .metric-value {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 700;
            line-height: 1;
        }

        /* Progress bars */
        .progress-bar-fill {
            background: linear-gradient(90deg, var(--bs-primary) 0%, var(--bs-info) 100%);
            transition: width 0.6s cubic-bezier(0.65, 0, 0.35, 1);
        }

        /* Activity feed items */
        .activity-item {
            transition: background 0.2s ease;
        }

        .activity-item:hover {
            background: var(--bs-gray-50);
        }

        /* Quick action buttons */
        .quick-action-btn {
            border: 1px solid var(--bs-gray-200);
            transition: all 0.2s ease;
            min-height: 100px;
        }

        .quick-action-btn:hover {
            border-color: var(--bs-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Responsive typography */
        .dashboard-title {
            font-size: clamp(1.5rem, 3vw, 2rem);
        }

        /* Chart container responsive height */
        .chart-container {
            height: 300px;
        }

        @media (min-width: 768px) {
            .chart-container {
                height: 380px;
            }
        }
    </style>

    <!-- Dashboard Header -->
    <div class="bg-white border-bottom mb-4">
        <div class="container-fluid py-3 py-md-4">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                    <h1 class="dashboard-title fw-bold mb-1">Reinsurance Portfolio Dashboard</h1>
                    <p class="text-muted mb-0">Real-time insights into your portfolio performance</p>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="ri-calendar-line me-1"></i>Period: 2025
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="ri-time-line me-1"></i>{{ date('M d, Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Key Metrics Row -->
        <div class="row g-3 g-lg-4 mb-4">
            <!-- Gross Written Premium -->
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-2">
                <div class="card metric-card border h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon primary">
                                <i class="ri-money-dollar-circle-line"></i>
                            </div>
                        </div>
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Gross Written Premium</p>
                        <h2 class="metric-value mb-2">
                            <small class="text-muted fs-6">KES</small> {{ number_format(212800000 / 1000000, 1) }}M
                        </h2>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="ri-arrow-up-line"></i> +5.2% vs Last Year
                        </span>
                    </div>
                </div>
            </div>

            <!-- Net Premium Income -->
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-2">
                <div class="card metric-card border h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon success">
                                <i class="ri-hand-coin-line"></i>
                            </div>
                        </div>
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Net Premium Income</p>
                        <h2 class="metric-value mb-2">
                            <small class="text-muted fs-6">KES</small> {{ number_format(31845000 / 1000000, 1) }}M
                        </h2>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="ri-arrow-up-line"></i> +3.8% vs Budget
                        </span>
                    </div>
                </div>
            </div>

            <!-- Commission Income -->
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-2">
                <div class="card metric-card border h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon warning">
                                <i class="ri-percent-line"></i>
                            </div>
                        </div>
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Commission Income</p>
                        <h2 class="metric-value mb-2">
                            <small class="text-muted fs-6">KES</small> {{ number_format(21280000 / 1000000, 1) }}M
                        </h2>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                            <i class="ri-subtract-line"></i> 15% Avg Rate
                        </span>
                    </div>
                </div>
            </div>

            <!-- Loss Ratio -->
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-2">
                <div class="card metric-card border h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon danger">
                                <i class="ri-alert-line"></i>
                            </div>
                        </div>
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Portfolio Loss Ratio</p>
                        <h2 class="metric-value mb-2">63.2<small class="fs-5">%</small></h2>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="ri-arrow-down-line"></i> -4.1% vs Target
                        </span>
                    </div>
                </div>
            </div>

            <!-- Active Covers -->
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-2">
                <div class="card metric-card border h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon info">
                                <i class="ri-file-shield-2-line"></i>
                            </div>
                        </div>
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Active Covers</p>
                        <h2 class="metric-value mb-2">{{ $totalCovers['amount'] ?? 1 }}</h2>
                        <span class="badge bg-info bg-opacity-10 text-info">
                            <i class="ri-arrow-up-line"></i> {{ $totalDebitedCovers['amount'] ?? 0 }} Debited
                        </span>
                    </div>
                </div>
            </div>

            <!-- Renewal Rate -->
            <div class="col-12 col-sm-6 col-lg-4 col-xxl-2">
                <div class="card metric-card border h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon success">
                                <i class="ri-loop-right-line"></i>
                            </div>
                        </div>
                        <p class="text-uppercase text-muted small fw-semibold mb-2">Renewal Rate</p>
                        <h2 class="metric-value mb-2">87<small class="fs-5">%</small></h2>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="ri-arrow-up-line"></i> Above Industry
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Mix Overview -->
        <div class="card border mb-4">
            <div
                class="card-header bg-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h5 class="mb-0 fw-semibold">Business Mix Overview</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-primary">YTD</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Q4</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Nov</button>
                </div>
            </div>
            <div class="card-body">
                <!-- Facultative vs Treaty Split -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="text-center p-4 bg-light rounded">
                            <h3 class="fw-bold mb-2">KES 127.4M</h3>
                            <p class="text-muted mb-2">Facultative</p>
                            <h4 class="text-primary fw-bold">59.8%</h4>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="text-center p-4 bg-light rounded">
                            <h3 class="fw-bold mb-2">KES 85.4M</h3>
                            <p class="text-muted mb-2">Treaty</p>
                            <h4 class="text-primary fw-bold">40.2%</h4>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Breakdown -->
                <div class="row g-3 g-lg-4">
                    <!-- Facultative Proportional -->
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-semibold mb-0">Facultative - Proportional</h6>
                                    <span
                                        class="badge bg-white text-dark border">{{ $totalTPRCovers['amount'] ?? 0 }}</span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <small class="text-uppercase text-muted d-block mb-1">GWP</small>
                                        <h5 class="fw-bold mb-0">KES 75.3M</h5>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-uppercase text-muted d-block mb-1">Income</small>
                                        <h5 class="fw-bold mb-0">KES 11.3M</h5>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>Budget Achievement</span>
                                        <span class="fw-semibold text-dark">72%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar progress-bar-fill" role="progressbar" style="width: 72%"
                                            aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Facultative Non-Proportional -->
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-semibold mb-0">Facultative - Non-Proportional</h6>
                                    <span
                                        class="badge bg-white text-dark border">{{ $totalFacCovers['amount'] ?? 0 }}</span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <small class="text-uppercase text-muted d-block mb-1">GWP</small>
                                        <h5 class="fw-bold mb-0">KES 52.1M</h5>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-uppercase text-muted d-block mb-1">Income</small>
                                        <h5 class="fw-bold mb-0">KES 7.8M</h5>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>Budget Achievement</span>
                                        <span class="fw-semibold text-dark">68%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar progress-bar-fill" role="progressbar" style="width: 68%"
                                            aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Treaty Proportional -->
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-semibold mb-0">Treaty - Proportional</h6>
                                    <span
                                        class="badge bg-white text-dark border">{{ $totalTPRCovers['amount'] ?? 0 }}</span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <small class="text-uppercase text-muted d-block mb-1">GWP</small>
                                        <h5 class="fw-bold mb-0">KES 60.3M</h5>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-uppercase text-muted d-block mb-1">Income</small>
                                        <h5 class="fw-bold mb-0">KES 9.0M</h5>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>Budget Achievement</span>
                                        <span class="fw-semibold text-dark">60%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar progress-bar-fill" role="progressbar" style="width: 60%"
                                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Treaty Non-Proportional -->
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-semibold mb-0">Treaty - Non-Proportional</h6>
                                    <span
                                        class="badge bg-white text-dark border">{{ $totalTNPCovers['amount'] ?? 0 }}</span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <small class="text-uppercase text-muted d-block mb-1">GWP</small>
                                        <h5 class="fw-bold mb-0">KES 25.1M</h5>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-uppercase text-muted d-block mb-1">Income</small>
                                        <h5 class="fw-bold mb-0">KES 3.8M</h5>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>Budget Achievement</span>
                                        <span class="fw-semibold text-dark">70%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar progress-bar-fill" role="progressbar" style="width: 70%"
                                            aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-semibold">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <button
                            class="btn quick-action-btn w-100 h-100 d-flex flex-column align-items-center justify-content-center text-start p-3">
                            <i class="ri-add-circle-line fs-1 text-primary mb-2"></i>
                            <h6 class="fw-semibold mb-1">New Cover</h6>
                            <small class="text-muted">Create facultative</small>
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button
                            class="btn quick-action-btn w-100 h-100 d-flex flex-column align-items-center justify-content-center text-start p-3">
                            <i class="ri-file-list-3-line fs-1 text-primary mb-2"></i>
                            <h6 class="fw-semibold mb-1">View Covers</h6>
                            <small class="text-muted">Browse portfolio</small>
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button
                            class="btn quick-action-btn w-100 h-100 d-flex flex-column align-items-center justify-content-center text-start p-3">
                            <i class="ri-file-chart-line fs-1 text-primary mb-2"></i>
                            <h6 class="fw-semibold mb-1">Reports</h6>
                            <small class="text-muted">Export analytics</small>
                        </button>
                    </div>
                    <div class="col-6 col-md-3">
                        <button
                            class="btn quick-action-btn w-100 h-100 d-flex flex-column align-items-center justify-content-center text-start p-3">
                            <i class="ri-settings-3-line fs-1 text-primary mb-2"></i>
                            <h6 class="fw-semibold mb-1">Settings</h6>
                            <small class="text-muted">Configure system</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card border">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">Recent Activity</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item activity-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="metric-icon primary">
                                    <i class="ri-file-add-line"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-1 fw-semibold">New Facultative Cover Created</h6>
                                <p class="text-muted small mb-1">Property - Commercial Building, Nairobi</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                            <div class="col-auto text-end">
                                <strong class="d-block">KES 15.5M</strong>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item activity-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="metric-icon success">
                                    <i class="ri-repeat-line"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-1 fw-semibold">Treaty Renewed</h6>
                                <p class="text-muted small mb-1">Quota Share Treaty - 30% Participation</p>
                                <small class="text-muted">5 hours ago</small>
                            </div>
                            <div class="col-auto text-end">
                                <strong class="d-block">KES 45.2M</strong>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item activity-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="metric-icon warning">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-1 fw-semibold">Commission Received</h6>
                                <p class="text-muted small mb-1">Q4 Commission Payment - Multiple Treaties</p>
                                <small class="text-muted">1 day ago</small>
                            </div>
                            <div class="col-auto text-end">
                                <strong class="d-block">KES 2.8M</strong>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item activity-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="metric-icon danger">
                                    <i class="ri-alert-line"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-1 fw-semibold">Claim Notification</h6>
                                <p class="text-muted small mb-1">Motor Fleet - Excess of Loss Layer</p>
                                <small class="text-muted">2 days ago</small>
                            </div>
                            <div class="col-auto text-end">
                                <strong class="d-block">KES 8.5M</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Period filter toggle
            $('.btn-group .btn').on('click', function() {
                $('.btn-group .btn').removeClass('btn-primary').addClass('btn-outline-primary');
                $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            });

            // Animate progress bars on load
            setTimeout(function() {
                $('.progress-bar-fill').each(function() {
                    const targetWidth = $(this).attr('style').match(/width:\s*(\d+)%/);
                    if (targetWidth) {
                        $(this).css('width', '0%');
                        setTimeout(() => {
                            $(this).css('width', targetWidth[0]);
                        }, 100);
                    }
                });
            }, 300);
        });
    </script>
@endpush
