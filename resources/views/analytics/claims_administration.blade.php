@extends('layouts.app', [
    'pageTitle' => 'Claims - ' . $company->company_name,
])

@section('styles')
    <style>
        .stat-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            border-left: 4px solid #003366;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #003366;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .chart-container {
            min-height: 350px;
            position: relative;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .risk-alert {
            border-left: 5px solid #ffc107;
        }

        .dashboard-header {
            background-color: #003366;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .table th {
            background-color: #003366;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 51, 102, 0.05);
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }

        .card-header {
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Claims</p>
            <span class="fs-semibold text-muted">Track key metrics and performance across your reinsurance portfolio.</span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Business Intelligence</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Claims</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="py-4">

        <!-- Key Performance Indicators -->
        <div class="row mb-4">
            <div class="col-md-15 col-lg">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-label">Total Claims</div>
                        <div class="stat-value" id="totalClaimsCounter">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-15 col-lg">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-label">Total Claim Amount</div>
                        <div class="stat-value" id="totalAmountCounter">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-15 col-lg">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-label">Amount Paid</div>
                        <div class="stat-value" id="amountPaidCounter">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-15 col-lg">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-label">Outstanding Amount</div>
                        <div class="stat-value" id="outstandingCounter">0</div>
                    </div>
                </div>
            </div>
            <div class="col-md-15 col-lg">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-label">Collection Rate</div>
                        <div class="stat-value" id="collectionRateCounter">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Alert -->
        {{-- <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning risk-alert">
                    <strong>Key Risk Alert:</strong> 6% of outstanding claims are over 703 days old, primarily from Britam
                    KPA (declined) and GA claims (queried).
                </div>
            </div>
        </div> --}}

        <!-- Charts Row 1 -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-center mb-0">Outstanding Claims Aging Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <div id="agingChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-center mb-0">Top 5 Cedants by Outstanding Amount</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <div id="cedantsChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-center mb-0">Claims Count by Aging Period</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <div id="claimsCountChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-center mb-0">Collection Rate Trend</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <div id="collectionRateChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cedant Performance Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-center mb-0">Cedant Performance Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="cedantTable">
                                <thead>
                                    <tr>
                                        <th>Cedant</th>
                                        <th>No. of Claims</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Outstanding</th>
                                        <th>Collection %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- <tr>
                                        <td>APA INSURANCE LTD</td>
                                        <td>3</td>
                                        <td>63,171,977</td>
                                        <td>40,671,977</td>
                                        <td>22,500,000</td>
                                        <td>64.38%</td>
                                    </tr>
                                    <tr>
                                        <td>GA INSURANCE LIMITED</td>
                                        <td>281</td>
                                        <td>144,157,602</td>
                                        <td>112,372,937</td>
                                        <td>31,764,665</td>
                                        <td>77.95%</td>
                                    </tr>
                                    <tr>
                                        <td>ICEA LION GENERAL INSURANCE</td>
                                        <td>20</td>
                                        <td>16,801,431</td>
                                        <td>5,401,943</td>
                                        <td>11,399,487</td>
                                        <td>32.15%</td>
                                    </tr>
                                    <tr>
                                        <td>GEMINIA INSURANCE COMPANY LIMITED</td>
                                        <td>85</td>
                                        <td>27,802,878</td>
                                        <td>24,450,610</td>
                                        <td>3,352,268</td>
                                        <td>87.94%</td>
                                    </tr>
                                    <tr>
                                        <td>BRITAM GENERAL INSURANCE COMPANY</td>
                                        <td>28</td>
                                        <td>26,994,487</td>
                                        <td>21,669,275</td>
                                        <td>5,253,212</td>
                                        <td>80.27%</td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aging Analysis Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-center mb-0">Aging Analysis of Outstanding Claims</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="agingTable">
                                <thead>
                                    <tr>
                                        <th>Aging Period</th>
                                        <th>Claims Count</th>
                                        <th>% of Total</th>
                                        <th>Balance %</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- <tr>
                                        <td>30 days</td>
                                        <td>4</td>
                                        <td>2%</td>
                                        <td>8.67%</td>
                                        <td>Recent claims under processing</td>
                                    </tr>
                                    <tr>
                                        <td>60 days</td>
                                        <td>7</td>
                                        <td>3%</td>
                                        <td>15.3%</td>
                                        <td>84% are GA claims under review</td>
                                    </tr>
                                    <tr>
                                        <td>90 days</td>
                                        <td>25</td>
                                        <td>12%</td>
                                        <td>36.67%</td>
                                        <td>Largest share - APA Kartasi (70% settled)</td>
                                    </tr>
                                    <tr>
                                        <td>120 days</td>
                                        <td>30</td>
                                        <td>14%</td>
                                        <td>10%</td>
                                        <td>ICEA Lion has largest share (6M)</td>
                                    </tr>
                                    <tr>
                                        <td>180 days</td>
                                        <td>30</td>
                                        <td>14%</td>
                                        <td>9.37%</td>
                                        <td>ICEA Lion has largest share</td>
                                    </tr>
                                    <tr>
                                        <td>365 days</td>
                                        <td>26</td>
                                        <td>12%</td>
                                        <td>13.38%</td>
                                        <td>ICEA and GA claims (7.6M Post-Bankix)</td>
                                    </tr>
                                    <tr>
                                        <td>730 days</td>
                                        <td>35</td>
                                        <td>16%</td>
                                        <td>4.84%</td>
                                        <td>GA and Britam KPA claims (ex-gratia)</td>
                                    </tr>
                                    <tr>
                                        <td>Over 730 days</td>
                                        <td>3</td>
                                        <td>1%</td>
                                        <td>1.01%</td>
                                        <td>GA queried, Cannon Life offset arrangement</td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Counter animations
            const totalClaimsCounter = new CountUp('totalClaimsCounter', 457, {
                duration: 2.5
            });
            const totalAmountCounter = new CountUp('totalAmountCounter', 0, {
                duration: 2.5,
                prefix: 'KES ',
                suffix: 'M',
                decimalPlaces: 1,
                decimal: '.'
            });
            const amountPaidCounter = new CountUp('amountPaidCounter', 0, {
                duration: 2.5,
                prefix: 'KES ',
                suffix: 'M',
                decimalPlaces: 1,
                decimal: '.'
            });
            const outstandingCounter = new CountUp('outstandingCounter', 0, {
                duration: 2.5,
                prefix: 'KES ',
                suffix: 'M',
                decimalPlaces: 1,
                decimal: '.'
            });
            const collectionRateCounter = new CountUp('collectionRateCounter', 0, {
                duration: 2.5,
                suffix: '%',
                decimalPlaces: 2,
                decimal: '.'
            });

            totalClaimsCounter.start();
            totalAmountCounter.update(293.9);
            amountPaidCounter.update(218.7);
            outstandingCounter.update(75.2);
            collectionRateCounter.update(74.42);

            // Initialize DataTables
            $('#cedantTable').DataTable({
                responsive: true,
                paging: true,
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 25, -1],
                    [5, 10, 25, "All"]
                ]
            });

            $('#agingTable').DataTable({
                responsive: true,
                paging: true,
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 25, -1],
                    [5, 10, 25, "All"]
                ]
            });

            // ApexCharts - Outstanding Claims Aging Distribution
            const agingChartOptions = {
                series: [8.67, 15.3, 36.67, 10, 9.37, 13.38, 4.84, 1.01],
                chart: {
                    type: 'donut',
                    height: 350,
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                labels: ['30 days', '60 days', '90 days', '120 days', '180 days', '365 days', '730 days',
                    'Over 730 days'
                ],
                colors: ['#4CAF50', '#8BC34A', '#CDDC39', '#FFC107', '#FF9800', '#FF5722', '#F44336',
                    '#D32F2F'
                ],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '50%',
                            labels: {
                                show: true,
                                name: {
                                    show: true
                                },
                                value: {
                                    show: true,
                                    formatter: function(val) {
                                        return val + '%'
                                    }
                                },
                                total: {
                                    show: true,
                                    formatter: function() {
                                        return '100%'
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                legend: {
                    position: 'right',
                    offsetY: 0,
                    height: 230
                }
            };

            const agingChart = new ApexCharts(document.querySelector("#agingChart"), agingChartOptions);
            agingChart.render();

            // ApexCharts - Top 5 Cedants by Outstanding Amount
            const cedantsChartOptions = {
                series: [{
                    name: 'Outstanding Amount (KES millions)',
                    data: [31.76, 22.5, 11.4, 5.25, 3.35]
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                colors: ['#003366'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + 'M';
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },
                xaxis: {
                    categories: ['GA Insurance', 'APA Insurance', 'ICEA Lion', 'Britam', 'Geminia'],
                    position: 'bottom',
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        show: true,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Amount in KES (millions)'
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(1);
                        }
                    }
                },
                fill: {
                    opacity: 1,
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: "horizontal",
                        shadeIntensity: 0.5,
                        gradientToColors: undefined,
                        inverseColors: true,
                        opacityFrom: 0.8,
                        opacityTo: 0.9,
                        stops: [0, 50, 100],
                        colorStops: []
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "KES " + val.toFixed(2) + " million";
                        }
                    }
                }
            };

            const cedantsChart = new ApexCharts(document.querySelector("#cedantsChart"), cedantsChartOptions);
            cedantsChart.render();

            // ApexCharts - Claims Count by Aging Period
            const claimsCountChartOptions = {
                series: [{
                    name: 'Number of Claims',
                    data: [4, 7, 25, 30, 30, 26, 35, 3]
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                colors: ['#1976D2'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val;
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },
                xaxis: {
                    categories: ['30 days', '60 days', '90 days', '120 days', '180 days', '365 days',
                        '730 days', 'Over 730 days'
                    ],
                    position: 'bottom',
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        show: true,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Number of Claims'
                    }
                },
                fill: {
                    opacity: 1,
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        shadeIntensity: 0.4,
                        gradientToColors: undefined,
                        inverseColors: false,
                        opacityFrom: 0.9,
                        opacityTo: 0.8,
                        stops: [0, 50, 100],
                        colorStops: []
                    }
                }
            };

            const claimsCountChart = new ApexCharts(document.querySelector("#claimsCountChart"),
                claimsCountChartOptions);
            claimsCountChart.render();

            // ApexCharts - Collection Rate Trend
            const collectionRateChartOptions = {
                series: [{
                    name: 'Collection Rate',
                    data: [72.5, 71.8, 73.2, 72.9, 73.8, 74.42]
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: true
                    },
                    dropShadow: {
                        enabled: true,
                        color: '#000',
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2
                    },
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                colors: ['#00796B'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(2) + '%';
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                title: {
                    text: '6-Month Collection Rate Trend',
                    align: 'left',
                    style: {
                        fontSize: '14px',
                        fontWeight: 'normal'
                    }
                },
                grid: {
                    borderColor: '#e7e7e7',
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    }
                },
                markers: {
                    size: 6,
                    colors: ['#00796B'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 8
                    }
                },
                xaxis: {
                    categories: ['Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
                    title: {
                        text: 'Month'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Collection Rate (%)'
                    },
                    min: 70,
                    max: 80
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    floating: true,
                    offsetY: -25,
                    offsetX: -5
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + "%";
                        }
                    }
                }
            };

            const collectionRateChart = new ApexCharts(document.querySelector("#collectionRateChart"),
                collectionRateChartOptions);
            collectionRateChart.render();

            // Add hover effects to the stat cards using jQuery
            $('.stat-card').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );
        });
    </script>
@endsection
