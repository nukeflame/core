@extends('layouts.app', [
    'pageTitle' => 'Business Development - ' . $company->company_name,
])

@section('content')
    <style>
        .dashboard-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            margin-bottom: 1rem;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
            margin-bottom: 20px;
        }

        .dashboard-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .dashboard-subtitle {
            color: #7f8c8d;
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .chart-container {
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .chart-title {
            font-size: 18px;
            margin-bottom: 15px;
            color: #34495e;
            font-weight: 500;
        }

        .chart-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-column {
            flex: 1;
        }

        .stat-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            flex: 1;
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin: 5px 0;
        }

        .stat-label {
            font-size: 14px;
            color: #7f8c8d;
        }

        .stat-trend {
            font-size: 12px;
            margin-top: 5px;
        }

        .trend-up {
            color: #27ae60;
        }

        .trend-down {
            color: #e74c3c;
        }

        .comparison-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 8px;
        }

        .comparison-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 8px;
        }

        .bar-chart {
            height: 250px;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            padding-top: 20px;
        }

        .bar {
            position: relative;
            width: 50px;
            background-color: #3498db;
            border-radius: 4px 4px 0 0;
            transition: all 0.3s ease;
        }

        .bar:hover {
            background-color: #2980b9;
        }

        .bar-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #7f8c8d;
        }

        .bar-value {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            background-color: #34495e;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            display: none;
        }

        .bar:hover .bar-value {
            display: block;
        }

        .accounts-badge {
            position: absolute;
            top: -48px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #f1c40f;
            color: #34495e;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .status-bar {
            height: 40px;
            display: flex;
            width: 100%;
            border-radius: 4px;
            overflow: hidden;
        }

        .status-segment {
            position: relative;
            height: 100%;
        }

        .status-segment:hover .segment-tooltip {
            display: block;
        }

        .segment-tooltip {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #34495e;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            display: none;
            white-space: nowrap;
        }

        .status-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            font-size: 12px;
            color: #7f8c8d;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eaeaea;
        }

        .data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #34495e;
        }

        .data-table tr:hover {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-green {
            color: #27ae60;
        }

        .text-red {
            color: #e74c3c;
        }

        .text-blue {
            color: #3498db;
        }

        .forecast-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
        }

        .forecast-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .forecast-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .forecast-info {
            margin-left: 15px;
        }

        .forecast-label {
            font-size: 13px;
            color: #7f8c8d;
        }

        .forecast-value {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        @media (max-width: 768px) {

            .stat-container,
            .chart-row {
                flex-direction: column;
            }

            .status-legend {
                justify-content: flex-start;
            }
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Business Development</p>
            <span class="fs-semibold text-muted">Track key metrics and performance across your reinsurance portfolio.</span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Business Intelligence</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Business Development</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Quotation Analysis Dashboard - {{ now()->year }}</h1>
                <p class="dashboard-subtitle">Performance trends for Jan - Dec {{ now()->year }}</p>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="stat-container">
            <div class="stat-card">
                <div class="stat-label">Latest Total Quotes</div>
                <div class="stat-value">{{ $latestTotalQuotes }}</div>
                <div class="stat-trend trend-up">{{ $latestTotalQuotesChange }}% vs March 2025</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Latest Win Rate</div>
                <div class="stat-value">{{ $latestWinRate }}%</div>
                <div class="stat-trend {{ $latestWinRateChange > 0 ? 'trend-up' : 'trend-down' }}">
                    {{ $latestWinRateChange }}pp vs March 2025</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Latest Lost Rate</div>
                <div class="stat-value">{{ $latestLostRate }}%</div>
                <div class="stat-trend {{ $latestLostRateChange > 0 ? 'trend-up' : 'trend-down' }}">
                    {{ $latestLostRateChange }}pp vs March 2025</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Average Monthly Quotes</div>
                <div class="stat-value">{{ $averageMonthlyQuotes }}</div>
                <div class="comparison-label">January - June 2025</div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="chart-row">
            <div class="chart-column">
                <div class="chart-container">
                    <div class="chart-title">Quote Volume Trend (2025)</div>
                    <div id="volumeChart"></div>
                </div>
            </div>
            <div class="chart-column">
                <div class="chart-container">
                    <div class="chart-title">Win Rate vs Lost Rate (2025)</div>
                    <div id="winLossChart"></div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="chart-row">
            <div class="chart-column">
                <div class="chart-container">
                    <div class="chart-title">Quotation Status Distribution (2025)</div>
                    <div id="stackedBarChart"></div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="chart-row">
            <div class="chart-column">
                <div class="chart-container">
                    <div class="chart-title">June 2025 Status Distribution</div>
                    <div id="pieChart"></div>
                </div>
            </div>
            <div class="chart-column">
                <div class="chart-container">
                    <div class="chart-title">March 2025 Status Distribution</div>
                    <div id="pieChart2"></div>
                </div>
            </div>
        </div>

        <!-- Charts Row 4 -->
        <div class="chart-row">
            <div class="chart-column">
                <div class="chart-container">
                    <div class="chart-title">Status Percentage Trend (2025)</div>
                    <div id="lineChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-container mb-3">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Pipeline Analysis Dashboard - {{ now()->year }}</h1>
                <p class="dashboard-subtitle">Performance trends for Jan - Dec {{ now()->year }}</p>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="stat-container">
            <div class="stat-card">
                <div class="stat-label">Total Premium Pipeline</div>
                <div class="stat-value" id="totalPremium">KES 0</div>
                <div class="stat-trend" id="premiumTrend"></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Accounts</div>
                <div class="stat-value" id="totalAccounts">0</div>
                <div class="stat-trend" id="accountsTrend"></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Win Rate</div>
                <div class="stat-value" id="winRate">0%</div>
                <div class="stat-label">Based on Jan 2025 data</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Avg Premium per Account</div>
                <div class="stat-value" id="avgPremium">LES 0</div>
                <div class="stat-label">Based on Jan 2025 data</div>
            </div>
        </div>

        <!-- Premium and Account Growth Chart -->
        <div class="chart-container">
            <div class="chart-title">Premium and Account Growth</div>
            <div class="bar-chart" id="growthChart"></div>
        </div>

        <!-- Premium Pipeline by Status Table -->
        <div class="chart-container">
            <div class="chart-title">Premium Pipeline by Status</div>
            <div class="table-responsive">
                <table class="data-table" id="pipelineTable">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-right">Total Premium</th>
                            <th class="text-right">Accounts</th>
                            <th class="text-right">Won</th>
                            <th class="text-right">Lost</th>
                            <th class="text-right">Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Will be populated by jQuery -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="chart-row">
            <div class="chart-column">
                <div class="chart-container">
                    <div class="chart-title">Latest Status Distribution</div>
                    <div class="status-bar" id="statusBar">
                        <!-- Will be populated by jQuery -->
                    </div>
                    <div class="status-legend" id="statusLegend">
                        <!-- Will be populated by jQuery -->
                    </div>
                </div>
            </div>

            <div class="chart-column">
                <div class="chart-container">
                    <div class="chart-title">Premium Pipeline Forecast</div>
                    <div class="forecast-container">
                        <div class="forecast-item">
                            <div class="forecast-circle" style="background-color: #3498db;" id="currentPipelineCircle">
                                0M
                            </div>
                            <div class="forecast-info">
                                <div class="forecast-label">Current Pipeline</div>
                                <div class="forecast-value" id="currentPipeline">$0</div>
                            </div>
                        </div>

                        <div class="forecast-item">
                            <div class="forecast-circle" style="background-color: #27ae60;" id="projectedWonCircle">
                                0M
                            </div>
                            <div class="forecast-info">
                                <div class="forecast-label">Projected Won Premium</div>
                                <div class="forecast-value" id="projectedWon">$0</div>
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
            // Parse data from Laravel backend
            const data2024 = @json($data2024);

            // Extract just the months we actually have data for
            const months2024WithData = data2024.filter(item => item.total !== null);

            // All months in 2024
            const allMonths2024 = @json($allMonths2024);

            // Volume Chart
            const volumeChart = new ApexCharts(document.querySelector("#volumeChart"), {
                series: [{
                    name: 'Total Quotes',
                    data: data2024.map(item => item.total)
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Segoe UI, sans-serif'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: allMonths2024
                },
                colors: ['#3498db'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100]
                    }
                },
                markers: {
                    size: 5,
                    hover: {
                        size: 7
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val !== null ? val + " quotes" : "No data";
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Number of Quotes'
                    },
                    min: 0
                }
            });
            volumeChart.render();

            // Win Loss Chart
            const winLossChart = new ApexCharts(document.querySelector("#winLossChart"), {
                series: [{
                    name: 'Win Rate',
                    data: data2024.map(item => item.wonRisks.percent)
                }, {
                    name: 'Lost Rate',
                    data: data2024.map(item => item.lostAccounts.percent)
                }],
                chart: {
                    height: 300,
                    type: 'line',
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Segoe UI, sans-serif'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 3,
                    curve: 'smooth',
                    dashArray: [0, 0]
                },
                colors: ['#2ecc71', '#e74c3c'],
                markers: {
                    size: 5,
                    hover: {
                        size: 7
                    }
                },
                xaxis: {
                    categories: allMonths2024
                },
                yaxis: {
                    title: {
                        text: 'Percentage (%)'
                    },
                    min: 0
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val !== null ? val + "%" : "No data";
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                }
            });
            winLossChart.render();

            // Stacked Bar Chart - using only months with data
            const stackedBarChart = new ApexCharts(document.querySelector("#stackedBarChart"), {
                series: [{
                    name: 'Won Risks',
                    data: months2024WithData.map(item => item.wonRisks.count)
                }, {
                    name: 'Lost Accounts',
                    data: months2024WithData.map(item => item.lostAccounts.count)
                }, {
                    name: 'Not Quoted (UW Info)',
                    data: months2024WithData.map(item => item.notQuoted.count)
                }, {
                    name: 'Reinsurers Declined',
                    data: months2024WithData.map(item => item.reinsurersDeclined.count)
                }, {
                    name: 'Pending Confirmation',
                    data: months2024WithData.map(item => item.pendingConfirmation.count)
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
                    stackType: 'normal',
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Segoe UI, sans-serif'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '60%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: months2024WithData.map(item => item.month)
                },
                yaxis: {
                    title: {
                        text: 'Number of Quotes'
                    },
                    min: 0
                },
                fill: {
                    opacity: 1
                },
                colors: ['#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#3498db'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'center'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " quotes";
                        }
                    }
                }
            });
            stackedBarChart.render();

            // Pie Chart for June 2024
            const pieChart = new ApexCharts(document.querySelector("#pieChart"), {
                series: [
                    data2024[5].wonRisks.percent,
                    data2024[5].lostAccounts.percent,
                    data2024[5].notQuoted.percent,
                    data2024[5].reinsurersDeclined.percent,
                    data2024[5].pendingConfirmation.percent
                ],
                chart: {
                    width: '100%',
                    height: 350,
                    type: 'pie',
                    fontFamily: 'Segoe UI, sans-serif'
                },
                labels: ['Won Risks', 'Lost Accounts', 'Not Quoted (UW Info)', 'Reinsurers Declined',
                    'Pending Confirmation'
                ],
                colors: ['#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#3498db'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + "%";
                    }
                },
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                title: {
                    text: "June",
                    align: "center",
                    style: {
                        fontSize: "16px"
                    }
                }
            });
            pieChart.render();

            // Pie Chart for March 2024
            const pieChart2 = new ApexCharts(document.querySelector("#pieChart2"), {
                series: [
                    data2024[2].wonRisks.percent,
                    data2024[2].lostAccounts.percent,
                    data2024[2].notQuoted.percent,
                    data2024[2].reinsurersDeclined.percent,
                    data2024[2].pendingConfirmation.percent
                ],
                chart: {
                    width: '100%',
                    height: 350,
                    type: 'pie',
                    fontFamily: 'Segoe UI, sans-serif'
                },
                labels: ['Won Risks', 'Lost Accounts', 'Not Quoted (UW Info)', 'Reinsurers Declined',
                    'Pending Confirmation'
                ],
                colors: ['#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#3498db'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + "%";
                    }
                },
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                title: {
                    text: "March",
                    align: "center",
                    style: {
                        fontSize: "16px"
                    }
                }
            });
            pieChart2.render();

            // Line Chart for Status Percentage Trend
            const lineChart = new ApexCharts(document.querySelector("#lineChart"), {
                series: [{
                    name: 'Won Risks',
                    data: data2024.map(item => item.wonRisks.percent)
                }, {
                    name: 'Lost Accounts',
                    data: data2024.map(item => item.lostAccounts.percent)
                }, {
                    name: 'Not Quoted (UW Info)',
                    data: data2024.map(item => item.notQuoted.percent)
                }, {
                    name: 'Reinsurers Declined',
                    data: data2024.map(item => item.reinsurersDeclined.percent)
                }, {
                    name: 'Pending Confirmation',
                    data: data2024.map(item => item.pendingConfirmation.percent)
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Segoe UI, sans-serif'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [3, 3, 3, 3, 3],
                    curve: 'smooth'
                },
                colors: ['#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#3498db'],
                xaxis: {
                    categories: allMonths2024
                },
                yaxis: {
                    title: {
                        text: 'Percentage (%)'
                    },
                    min: 0
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val !== null ? val + "%" : "No data";
                        }
                    }
                },
                markers: {
                    size: 4,
                    hover: {
                        size: 6
                    }
                }
            });
            lineChart.render();

            // pipeline
            const pipelineData = [{
                    month: 'Jan 2024',
                    premiumValue: 1250000,
                    accountsCount: 42,
                    wonRisks: {
                        count: 12,
                        percent: 10.5
                    },
                    lostAccounts: {
                        count: 28,
                        percent: 23.8
                    },
                    notQuoted: {
                        count: 16,
                        percent: 20.2
                    },
                    reinsurersDeclined: {
                        count: 8,
                        percent: 10.1
                    },
                    pendingConfirmation: {
                        count: 20,
                        percent: 35.4
                    },
                    totalQuotes: 84
                },
                {
                    month: 'Feb 2024',
                    premiumValue: 1485000,
                    accountsCount: 51,
                    wonRisks: {
                        count: 14,
                        percent: 12.8
                    },
                    lostAccounts: {
                        count: 29,
                        percent: 24.0
                    },
                    notQuoted: {
                        count: 15,
                        percent: 21.4
                    },
                    reinsurersDeclined: {
                        count: 7,
                        percent: 9.9
                    },
                    pendingConfirmation: {
                        count: 19,
                        percent: 31.9
                    },
                    totalQuotes: 84
                },
                {
                    month: 'Mar 2024',
                    premiumValue: 1680000,
                    accountsCount: 62,
                    wonRisks: {
                        count: 9,
                        percent: 14.5
                    },
                    lostAccounts: {
                        count: 15,
                        percent: 24.2
                    },
                    notQuoted: {
                        count: 14,
                        percent: 22.6
                    },
                    reinsurersDeclined: {
                        count: 6,
                        percent: 9.7
                    },
                    pendingConfirmation: {
                        count: 18,
                        percent: 29.0
                    },
                    totalQuotes: 62
                },
                {
                    month: 'Apr 2024',
                    premiumValue: 1950000,
                    accountsCount: 89,
                    wonRisks: {
                        count: 13,
                        percent: 14.0
                    },
                    lostAccounts: {
                        count: 32,
                        percent: 30.5
                    },
                    notQuoted: {
                        count: 18,
                        percent: 20.0
                    },
                    reinsurersDeclined: {
                        count: 10,
                        percent: 11.5
                    },
                    pendingConfirmation: {
                        count: 21,
                        percent: 24.0
                    },
                    totalQuotes: 94
                },
                {
                    month: 'May 2024',
                    premiumValue: 2240000,
                    accountsCount: 104,
                    wonRisks: {
                        count: 16,
                        percent: 14.2
                    },
                    lostAccounts: {
                        count: 40,
                        percent: 35.4
                    },
                    notQuoted: {
                        count: 20,
                        percent: 18.9
                    },
                    reinsurersDeclined: {
                        count: 14,
                        percent: 12.0
                    },
                    pendingConfirmation: {
                        count: 22,
                        percent: 19.5
                    },
                    totalQuotes: 112
                },
                {
                    month: 'Jun 2024',
                    premiumValue: 2650000,
                    accountsCount: 135,
                    wonRisks: {
                        count: 19,
                        percent: 14.1
                    },
                    lostAccounts: {
                        count: 52,
                        percent: 38.5
                    },
                    notQuoted: {
                        count: 24,
                        percent: 17.8
                    },
                    reinsurersDeclined: {
                        count: 17,
                        percent: 12.6
                    },
                    pendingConfirmation: {
                        count: 23,
                        percent: 17.0
                    },
                    totalQuotes: 135
                }
            ];

            // For a real Laravel app, you would get this data from your controller
            //{{-- const data = {!! json_encode($premiumData) !!}; --}}

            const latestData = pipelineData[pipelineData.length - 1];
            const previousData = pipelineData[pipelineData.length - 2];

            // Status colors
            const statusColors = {
                wonRisks: '#27ae60',
                lostAccounts: '#e74c3c',
                notQuoted: '#f39c12',
                reinsurersDeclined: '#9b59b6',
                pendingConfirmation: '#3498db'
            };

            // Status display names
            const statusNames = {
                wonRisks: 'Won Risks',
                lostAccounts: 'Lost Accounts',
                notQuoted: 'Not Quoted (UW Info)',
                reinsurersDeclined: 'Reinsurers Declined',
                pendingConfirmation: 'Pending Confirmation'
            };

            // Format currency
            function formatCurrency(value) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'KES',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            }

            // Calculate growth percentage
            function calculateGrowth(current, previous) {
                if (!previous) return null;
                return ((current - previous) / previous * 100).toFixed(1);
            }

            // Update KPIs
            $('#totalPremium').text(formatCurrency(latestData.premiumValue));
            $('#totalAccounts').text(latestData.accountsCount);
            $('#winRate').text(latestData.wonRisks.percent + '%');

            const avgPremium = (latestData.premiumValue / latestData.accountsCount).toFixed(0);
            $('#avgPremium').text(formatCurrency(avgPremium));

            const premiumGrowth = calculateGrowth(latestData.premiumValue, previousData.premiumValue);
            const accountsGrowth = calculateGrowth(latestData.accountsCount, previousData.accountsCount);

            if (premiumGrowth > 0) {
                $('#premiumTrend').addClass('trend-up').html('↑ ' + Math.abs(premiumGrowth) +
                    '% vs previous month');
            } else {
                $('#premiumTrend').addClass('trend-down').html('↓ ' + Math.abs(premiumGrowth) +
                    '% vs previous month');
            }

            if (accountsGrowth > 0) {
                $('#accountsTrend').addClass('trend-up').html('↑ ' + Math.abs(accountsGrowth) +
                    '% vs previous month');
            } else {
                $('#accountsTrend').addClass('trend-down').html('↓ ' + Math.abs(accountsGrowth) +
                    '% vs previous month');
            }

            const maxPremium = Math.max(...pipelineData.map(item => item.premiumValue));
            pipelineData.forEach(function(item) {
                const barHeight = (item.premiumValue / maxPremium) * 200;
                const monthLabel = item.month.split(' ')[0];

                const bar = $('<div>')
                    .addClass('bar')
                    .css('height', barHeight + 'px')
                    .append(
                        $('<div>').addClass('bar-value').text(formatCurrency(item.premiumValue)),
                        $('<div>').addClass('accounts-badge').text(item.accountsCount + ' accts'),
                        $('<div>').addClass('bar-label').text(monthLabel)
                    );

                $('#growthChart').append(bar);
            });


            // Build pipeline table
            pipelineData.forEach(function(item) {
                $('#pipelineTable tbody').append(`
            <tr>
                <td>${item.month}</td>
                <td class="text-right">${formatCurrency(item.premiumValue)}</td>
                <td class="text-right">${item.accountsCount}</td>
                <td class="text-right text-green">${item.wonRisks.count}</td>
                <td class="text-right text-red">${item.lostAccounts.count}</td>
                <td class="text-right text-blue">${item.pendingConfirmation.count}</td>
            </tr>
        `);
            });

            // Build status distribution bar
            const statusOrder = ['wonRisks', 'pendingConfirmation', 'notQuoted', 'reinsurersDeclined',
                'lostAccounts'
            ];
            statusOrder.forEach(function(status) {
                const width = latestData[status].percent + '%';
                $('#statusBar').append(`
            <div class="status-segment" style="width: ${width}; background-color: ${statusColors[status]};">
                <div class="segment-tooltip">${statusNames[status]}: ${latestData[status].percent}%</div>
            </div>
        `);
            });

            // Build status legend
            statusOrder.forEach(function(status) {
                $('#statusLegend').append(`
            <div class="legend-item">
                <div class="legend-color" style="background-color: ${statusColors[status]};"></div>
                ${statusNames[status]}
            </div>
        `);
            });

            // Update forecast
            const currentPipelineInMillions = (latestData.premiumValue / 1000000).toFixed(1);
            const projectedWonPremium = latestData.premiumValue * (latestData.wonRisks.percent / 100);
            const projectedWonInMillions = (projectedWonPremium / 1000000).toFixed(1);

            $('#currentPipelineCircle').text(currentPipelineInMillions + 'M');
            $('#currentPipeline').text(formatCurrency(latestData.premiumValue));

            $('#projectedWonCircle').text(projectedWonInMillions + 'M');
            $('#projectedWon').text(formatCurrency(projectedWonPremium));
        });
    </script>
@endpush
