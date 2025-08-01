@extends('layouts.app', [
    'pageTitle' => 'Analytics - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Analytics</p>
            <span class="fs-semibold text-muted">Track key metrics and performance across your reinsurance portfolio.</span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Business Intelligence</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Analytics</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row info-widget mb-4">
        <div class="col-xl-12">
            <div class="widget widget-full">
                <h2>Portfolio Overview</h2>
                <div class="kpi-container">
                    <div class="kpi-box">
                        <div class="kpi-title">Total Premium</div>
                        <div class="kpi-value">KES 0.00</div>
                        <div class="kpi-change positive">0% YoY</div>
                    </div>
                    <div class="kpi-box">
                        <div class="kpi-title">Total Claims</div>
                        <div class="kpi-value">KES 0.00</div>
                        <div class="kpi-change positive">0% YoY</div>
                    </div>
                    <div class="kpi-box warning">
                        <div class="kpi-title">Loss Ratio</div>
                        <div class="kpi-value">0%</div>
                        <div class="kpi-change negative">0% YoY</div>
                    </div>
                    <div class="kpi-box">
                        <div class="kpi-title">Expense Ratio</div>
                        <div class="kpi-value">0%</div>
                        <div class="kpi-change positive">0% YoY</div>
                    </div>
                    <div class="kpi-box success">
                        <div class="kpi-title">Renewal Retention</div>
                        <div class="kpi-value">0%</div>
                        <div class="kpi-change positive">0% YoY</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-3">Total New Business GWP</h6>
                                    <span class="fs-25 fw-semibold"><span class="fs-13">KES</span>
                                        {{ number_format(0, 0) }}</span>
                                    {{-- <span class="d-block text-success fs-12">+12% from target<i
                                                    class="ti ti-trending-up ms-1"></i></span> --}}
                                </div>
                                <div id="analytics-users"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center text-success justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-3">Total New Business Income</h6>
                                    <span class="fs-25 fw-semibold"><span class="fs-13">KES</span>
                                        {{ number_format(0, 0) }}</span>
                                    {{-- <span class="d-block text-success fs-12">+8% from target<i
                                                    class="ti ti-trending-down ms-1 d-inline-flex"></i></span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-3">Total Renewals GWP</h6>
                                    <span class="fs-25 fw-semibold"><span class="fs-13">KES</span>
                                        {{ number_format(0, 0) }}</span>
                                    {{-- <span class="d-block text-danger fs-12">-2% from target<i
                                                    class="ti ti-trending-down ms-1 d-inline-flex"></i></span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center text-success justify-content-between">
                                <div>
                                    <h6 class="fw-semibold mb-3">Total Renewals Income</h6>
                                    <span class="fs-25 fw-semibold"><span class="fs-13">KES</span>
                                        {{ number_format(0, 0) }}</span>
                                    {{-- <span class="d-block text-danger fs-12">-5% from target<i
                                                    class="ti ti-trending-down ms-1 d-inline-flex"></i></span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total GWP (Combined)</h6>
                    <h3 class="card-title">KES {{ number_format(0, 2) }}</h3>
                    <p class="card-text text-success mb-0">
                        <i class="bi bi-arrow-up-right"></i> 0% vs Previous Period
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Income (Combined)</h6>
                    <h3 class="card-title">KES {{ number_format(0, 2) }}</h3>
                    <p class="card-text text-success mb-0">
                        <i class="bi bi-arrow-up-right"></i> 0% vs Previous Period
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Commission Rate</h6>
                    <h3 class="card-title">0%</h3>
                    <p class="card-text text-danger mb-0">
                        <i class="bi bi-arrow-down-right"></i> 0% vs Previous Period
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Premium, Claims & Loss Ratio</div>
                </div>
                <div class="card-body">
                    <div id="combinedPremiumClaimsChart" style="height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Income & Expenses</div>
                </div>
                <div class="card-body">
                    <div id="profitLossChart" style="height: 365px;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Line of Business Distribution</div>
                </div>
                <div class="card-body">
                    <div id="lineOfBusinessChart" style="height: 365px;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">International Market Performance</div>
                </div>
                <div class="card-body">
                    <div id="marketPerfomanceChart" style="height: 365px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">New Business GWP by Category</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="newBusinessChart" style="width: 100%; height: 300px; min-width: 0px;"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="mb-2 text-muted small">Category Breakdown:</div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="category-indicator bg-primary"></span>
                                    <span>Facultative: KES 0</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="category-indicator bg-success"></span>
                                    <span>Special Line: KES 0</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="category-indicator bg-warning"></span>
                                    <span>Treaty: KES 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Renewals GWP by Category</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="renewalsChart" style="width: 100%; height: 300px; min-width: 0px;"></canvas>
                    </div>
                    <div class="mt-4">
                        <div class="mb-2 text-muted small">Category Breakdown:</div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="category-indicator bg-primary"></span>
                                    <span>Facultative: KES 0</span>
                                </div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="category-indicator bg-warning"></span>
                                    <span>Treaty: KES 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <!-- Performance Chart -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card mb-4">
                        <div class="card-header">
                            <div class="card-title">Performance Breakdown</div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="performanceChart"
                                    style="width: 100%; height: 300px; min-width: 0px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Breakdown Table -->
            <div class="card custom-card mb-4">
                <div class="card-header bg-light">
                    <div class="card-title">Performance Details</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>New Business GWP</th>
                                    <th>New Business Income</th>
                                    <th>Renewal GWP</th>
                                    <th>Renewal Income</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Facultative</td>
                                    <td>KES {{ number_format(246000000, 2) }}</td>
                                    <td>KES {{ number_format(15000000, 2) }}</td>
                                    <td>KES {{ number_format(0, 2) }}</td>
                                    <td>KES {{ number_format(0, 2) }}</td>
                                    <td><span class="badge bg-success">On Target</span></td>
                                </tr>
                                <tr>
                                    <td>Special Lines</td>
                                    <td>KES {{ number_format(60000000, 2) }}</td>
                                    <td>KES {{ number_format(3000000, 2) }}</td>
                                    <td>KES {{ number_format(0, 2) }}</td>
                                    <td>KES {{ number_format(0, 2) }}</td>
                                    <td><span class="badge bg-warning text-dark">Below Target</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Treaty</td>
                                    <td>KES {{ number_format(120000000, 2) }}</td>
                                    <td>KES {{ number_format(9900000, 2) }}</td>
                                    <td>KES {{ number_format(103030554, 2) }}</td>
                                    <td>KES {{ number_format(2575764, 2) }}</td>
                                    <td><span class="badge bg-success">On Target</span></td>
                                </tr>
                                <tr>
                                    <td>Market Exapnsion</td>
                                    <td>KES {{ number_format(120000000, 2) }}</td>
                                    <td>KES {{ number_format(9900000, 2) }}</td>
                                    <td>KES {{ number_format(103030554, 2) }}</td>
                                    <td>KES {{ number_format(2575764, 2) }}</td>
                                    <td><span class="badge bg-success">On Target</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Staff Performance Table -->
            <div class="card custom-card mb-4">
                <div class="card-header bg-light">
                    <div class="card-title">Staff Performance</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Staff</th>
                                    <th>New Business GWP</th>
                                    <th>New Business Income</th>
                                    <th>Renewal GWP</th>
                                    <th>Renewal Income</th>
                                    <th>Achievement</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach ($staffPerformance as $staff)
                                                        <tr>
                                                            <td>{{ $staff->name }}</td>
                                                            <td>KES {{ number_format($staff->new_business_gwp, 2) }}</td>
                                                            <td>KES {{ number_format($staff->new_business_income, 2) }}</td>
                                                            <td>KES {{ number_format($staff->renewal_gwp, 2) }}</td>
                                                            <td>KES {{ number_format($staff->renewal_income, 2) }}</td>
                                                            <td>{{ $staff->achievement }}%</td>
                                                        </tr>
                                                    @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            const premiumData = {
                yearly: {
                    labels: ['2020', '2021', '2022', '2023', '2024'],
                    premium: [45, 52, 58, 61, 68],
                    claims: [30, 28, 35, 32, 37]
                },
                quarterly: {
                    labels: ['Q1 2024', 'Q2 2024', 'Q3 2024', 'Q4 2024'],
                    premium: [15, 18, 16, 19],
                    claims: [8, 9.5, 7.5, 12]
                }
            };

            // Line of Business Chart
            const lineOfBusinessOptions = {
                series: [35, 25, 15, 10],
                chart: {
                    type: 'pie',
                    height: 360
                },
                labels: ['Facultative(Offers & Quotations)', 'Special Lines', 'Treaties',
                    'International Market'
                ],
                colors: ['#0088FE', '#00C49F', '#FFBB28', '#FF8042'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + '%';
                        }
                    }
                }
            };

            const lineOfBusinessChart = new ApexCharts(document.querySelector("#lineOfBusinessChart"),
                lineOfBusinessOptions);
            lineOfBusinessChart.render();

            const profitLossOptions = {
                series: [40, 20],
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: ['Income', 'Expenses'],
                colors: ['#00C49F', '#FFBB28'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + '%';
                        }
                    }
                }
            };

            const profitLossChart = new ApexCharts(document.querySelector("#profitLossChart"), profitLossOptions);
            profitLossChart.render();

            // Filter Period Change Handler
            $('#periodFilter').on('change', function() {
                const period = $(this).val();

                // Update Premium vs Claims Chart
                premiumClaimsChart.updateOptions({
                    xaxis: {
                        categories: premiumData[period].labels
                    },
                    series: [{
                            name: 'Premium ($ millions)',
                            data: premiumData[period].premium
                        },
                        {
                            name: 'Claims ($ millions)',
                            data: premiumData[period].claims
                        }
                    ]
                });

                lossRatioChart.updateOptions({
                    xaxis: {
                        categories: lossRatioData[period].labels
                    },
                    series: [{
                        name: 'Loss Ratio (%)',
                        data: lossRatioData[period].values
                    }]
                });
            });

            //
            // Data from the charts
            const years = ['2020', '2021', '2022', '2023', '2024'];
            const lossRatioData = [65, 53, 60, 54, 54.5];
            const lossPremiumData = [42, 48, 55, 58, 65];
            const claimsData = [28, 28, 33, 32, 35];

            // Calculate derived values for summary cards
            const lossRatioChange = ((lossRatioData[lossRatioData.length - 1] - lossRatioData[0]) / lossRatioData[
                0] * 100).toFixed(1);
            const premiumGrowth = ((lossPremiumData[lossPremiumData.length - 1] - lossPremiumData[0]) /
                    lossPremiumData[0] * 100)
                .toFixed(1);
            const claimsGrowth = ((claimsData[claimsData.length - 1] - claimsData[0]) / claimsData[0] * 100)
                .toFixed(1);
            const currentGap = lossPremiumData[lossPremiumData.length - 1] - claimsData[claimsData.length - 1];
            const initialGap = lossPremiumData[0] - claimsData[0];
            const gapGrowth = ((currentGap - initialGap) / initialGap * 100).toFixed(0);

            // Update summary cards with calculated values
            $('#summary-cards .card:nth-child(1) h3').text(lossRatioData[lossRatioData.length - 1] + '%');
            $('#summary-cards .card:nth-child(1) p').html(
                `<small><i class="fas fa-arrow-${lossRatioChange < 0 ? 'down' : 'up'}"></i> ${lossRatioChange}% since 2020</small>`
            );
            $('#summary-cards .card:nth-child(1) p').addClass(lossRatioChange < 0 ? 'text-success' : 'text-danger');

            $('#summary-cards .card:nth-child(2) h3').text('+' + premiumGrowth + '%');
            $('#summary-cards .card:nth-child(3) h3').text('+' + claimsGrowth + '%');
            $('#summary-cards .card:nth-child(4) h3').text('KES ' + currentGap + 'M');
            $('#summary-cards .card:nth-child(4) p').html(`<small>+${gapGrowth}% since 2020</small>`);

            // Loss Ratio Chart
            const lossRatioOptions = {
                series: [{
                    name: 'Loss Ratio',
                    data: lossRatioData
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#6c5ce7'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100]
                    }
                },
                xaxis: {
                    categories: years
                },
                yaxis: {
                    min: 40,
                    max: 80,
                    title: {
                        text: 'Loss Ratio (%)'
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(1) + '%';
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(1) + '%';
                        }
                    }
                }
            };

            // Premium vs Claims Chart
            const premiumClaimsOptions = {
                series: [{
                    name: 'Premium',
                    data: lossPremiumData
                }, {
                    name: 'Claims',
                    data: claimsData
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                colors: ['#3498db', '#e74c3c'],
                xaxis: {
                    categories: years
                },
                yaxis: {
                    title: {
                        text: 'KES (millions)'
                    },
                    labels: {
                        formatter: function(val) {
                            return 'KES ' + val + 'M';
                        }
                    }
                },
                legend: {
                    position: 'top'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'KES ' + val + ' million';
                        }
                    }
                }
            };

            // Combined Chart
            const combinedOptions = {
                series: [{
                    name: 'Premium',
                    type: 'column',
                    data: lossPremiumData
                }, {
                    name: 'Claims',
                    type: 'column',
                    data: claimsData
                }, {
                    name: 'Loss Ratio',
                    type: 'line',
                    data: lossRatioData
                }],
                chart: {
                    height: 400,
                    type: 'line',
                    stacked: false,
                    toolbar: {
                        show: true
                    }
                },
                stroke: {
                    width: [0, 0, 3],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%'
                    }
                },
                colors: ['#3498db', '#e74c3c', '#6c5ce7'],
                fill: {
                    opacity: [0.85, 0.85, 1],
                    gradient: {
                        inverseColors: false,
                        shade: 'light',
                        type: "vertical",
                        opacityFrom: 0.85,
                        opacityTo: 0.55,
                        stops: [0, 100, 100, 100]
                    }
                },
                labels: years,
                markers: {
                    size: 0
                },
                xaxis: {
                    type: 'category'
                },
                yaxis: [{
                        title: {
                            text: 'KES (millions)',
                        },
                        labels: {
                            formatter: function(val) {
                                return 'KES ' + val + 'M';
                            }
                        }
                    },
                    {
                        opposite: true,
                        title: {
                            text: 'Loss Ratio (%)'
                        },
                        min: 40,
                        max: 80,
                        labels: {
                            formatter: function(val) {
                                return val.toFixed(1) + '%';
                            }
                        }
                    }
                ],
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: [{
                        formatter: function(y) {
                            if (typeof y !== "undefined") {
                                return "KES " + y + " million";
                            }
                            return y;
                        }
                    }, {
                        formatter: function(y) {
                            if (typeof y !== "undefined") {
                                return "KES " + y + " million";
                            }
                            return y;
                        }
                    }, {
                        formatter: function(y) {
                            if (typeof y !== "undefined") {
                                return y.toFixed(1) + "%";
                            }
                            return y;
                        }
                    }]
                },
                legend: {
                    position: 'top'
                }
            };

            const combinedChart = new ApexCharts(document.querySelector("#combinedPremiumClaimsChart"),
                combinedOptions);
            combinedChart.render();

            const marketPerformanceOptions = {
                series: [35, 25, 15, 10],
                chart: {
                    type: 'pie',
                    height: 360
                },
                labels: ['Kenya', 'Uganda', 'Rwanda', 'South Sudan', 'Tanzania'],
                colors: ['#0088B0', '#21B49F', '#1FBB28', '#AF8042', '#536536'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + '%';
                        }
                    }
                }
            };

            const marketPerfomanceChart = new ApexCharts(document.querySelector("#marketPerfomanceChart"),
                marketPerformanceOptions);
            marketPerfomanceChart.render();

            const ctx = document.getElementById('performanceChart').getContext('2d');
            window.performanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Facultative', 'Special Lines', 'Treaty', 'Market Exp.'],
                    datasets: [{
                        label: 'New Business GWP',
                        data: [246000000, 60000000, 120000000, 0],
                        backgroundColor: 'rgba(52, 152, 219, 0.5)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Renewal GWP',
                        data: [0, 0, 103030554, 0],
                        backgroundColor: 'rgba(46, 204, 113, 0.5)',
                        borderColor: 'rgba(46, 204, 113, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'KES ' + (value / 1000000) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
