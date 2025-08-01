@extends('layouts.app', [
    'pageTitle' => 'Budget Tracker - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Budget Tracker</p>
            <span class="fs-semibold text-muted">Track key metrics and performance across your reinsurance portfolio.</span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Business Intelligence</a></li>
                    <li class="breadcrumb-item active" aria-current="page">>Budget Tracker</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-xl-3 border-end border-inline-end-dashed">
                            <div class="d-flex flex-wrap align-items-top p-4">
                                <div class="me-3 lh-1">
                                    <span class="avatar avatar-md avatar-rounded bg-primary shadow-sm">
                                        <i class="bx bx-bar-chart-alt-2 fs-18"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <h5 class="fw-semibold mb-1">KES {{ number_format($totals['gwp2024'] / 1000000, 2) }}M
                                    </h5>
                                    <p class="text-muted mb-0 fs-15 text-bold">Total GWP 2025</p>
                                </div>
                                <div>
                                    <span class="badge bg-success-transparent"><i
                                            class="ri-arrow-up-s-line align-middle me-1 d-inline-block"></i>+{{ number_format($totals['gwpChange'] - 100, 2) }}%
                                        vs 2024</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 border-end border-inline-end-dashed">
                            <div class="d-flex flex-wrap align-items-top p-4">
                                <div class="me-3 lh-1">
                                    <span class="avatar avatar-md avatar-rounded bg-secondary shadow-sm">
                                        <i class="ti ti-rocket fs-18"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <h5 class="fw-semibold mb-1">KES {{ number_format($totals['comm2024'] / 1000000, 2) }}M
                                    </h5>
                                    <p class="text-muted mb-0 fs-15 text-bold">Total Commission 2025</p>
                                </div>
                                <div>
                                    <span class="badge bg-danger-transparent"><i
                                            class="ri-arrow-down-s-line align-middle me-1"></i>+{{ number_format($totals['commChange'] - 100, 2) }}%
                                        vs 2024</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 border-end border-inline-end-dashed">
                            <div class="d-flex flex-wrap align-items-top p-4">
                                <div class="me-3 lh-1">
                                    <span class="avatar avatar-md avatar-rounded bg-success shadow-sm">
                                        <i class="bx bx-line-chart-down fs-18"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <h5 class="fw-semibold mb-1">Special Lines</h5>
                                    <p class="text-muted mb-0 fs-15 text-bold">Highest Growth Sector</p>
                                </div>
                                <div>
                                    <span class="badge bg-success-transparent"><i
                                            class="ri-arrow-up-s-line align-middle me-1 d-inline-block"></i>+{{ number_format($sectorData[1]['gwpChange'] - 100, 2) }}%
                                        GWP</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="d-flex flex-wrap align-items-top p-4">
                                <div class="me-3 lh-1">
                                    <span class="avatar avatar-md avatar-rounded bg-warning shadow-sm">
                                        <i class="ti ti-packge-import fs-18"></i>
                                    </span>
                                </div>
                                <div class="flex-fill">
                                    <h5 class="fw-semibold mb-1">Facultative</h5>
                                    <p class="text-muted mb-0 fs-15 text-bold">Dominant Sector</p>
                                </div>
                                <div>
                                    <span class="badge bg-success-transparent"><i
                                            class="ri-arrow-up-s-line align-middle me-1 d-inline-block"></i>{{ $sectorData[0]['gwpContribution'] }}%
                                        of GWP</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Quarterly GWP - Budgeted vs. Achieved (2025)</div>
                </div>
                <div class="card-body">
                    <div id="budgetAchievedCumulativeChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        {{-- <div class="col-sm-5">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Key Performance Indicators</div>
                </div>
                <div class="card-body">
                    <div id="kpiBudget" style="height: 365px;">
                        <table class="table table-striped text-nowrap table-hover table-responsive" id="budget-kpi-table"
                            style="width: 100%">
                            <thead>
                                <tr>
                                    <th>KPI</th>
                                    <th>Current Value</th>
                                    <th>Target</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Budget vs. Actual Income Variance by Month</div>
                </div>
                <div class="card-body">
                    <div id="budgetActualIncomeChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">GWP & Hit Rate Analysis by Month</div>
                </div>
                <div class="card-body">
                    <div id="hit-rate-chart"></div>
                </div>

                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:200px; width:100%">
                        <canvas id="hitRateChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            const budgetAchievedCumulativeChartOptions = {
                series: [],
                chart: {
                    height: 350,
                    type: 'bar',
                    stacked: true,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        columnWidth: '60%',
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [3, 3, 4]
                },
                xaxis: {
                    categories: [],
                    labels: {
                        rotate: 0
                    }
                },
                yaxis: [{
                    axisTicks: {
                        show: true,
                    },
                    axisBorder: {
                        show: true,
                    },
                    title: {
                        text: "GWP Amount"
                    },
                    labels: {
                        formatter: function(val) {
                            return 'KES ' + (val / 1000).toFixed(0) + 'K';
                        }
                    }
                }],
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(y) {
                            if (typeof y !== "undefined") {
                                return "KES " + y.toLocaleString();
                            }
                            return y;
                        }
                    }
                },
                legend: {
                    position: 'top'
                },
                colors: ['#2E86C1', '#8E44AD', '#EE44AD'],
                annotations: {
                    yaxis: [{
                        y: 0,
                        strokeDashArray: 0,
                        borderColor: '#000',
                        borderWidth: 1,
                        opacity: 0.3
                    }]
                },
            };

            const chart01 = new ApexCharts(document.querySelector("#budgetAchievedCumulativeChart"),
                budgetAchievedCumulativeChartOptions);
            chart01.render();

            $.ajax({
                url: '{{ route('budegetAchievedGWPData.data') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    const monthlyData = response.data;

                    // const differences = monthlyData.map(item => {
                    //     return {
                    //         x: item.month,
                    //         y: item.achieved - item.budgeted,
                    //         fillColor: item.achieved >= item.budgeted ? '#50C878' : '#FF6B6B'
                    //     };
                    // });

                    let cumulative = 0;
                    const cumulativeData = monthlyData.map(item => {
                        cumulative += item.achieved;
                        return {
                            x: item.month,
                            y: cumulative
                        };
                    });

                    const baseData = monthlyData.map(item => ({
                        x: item.month,
                        y: item.budgeted
                    }));

                    chart01.updateOptions({
                        xaxis: {
                            categories: monthlyData.map(item => item.month)
                        }
                    });

                    chart01.updateSeries([{
                            name: 'Budgeted',
                            type: 'column',
                            data: baseData
                        },
                        // {
                        //     name: 'Variance',
                        //     type: 'column',
                        //     data: differences
                        // },
                        {
                            name: 'Achieved',
                            type: 'line',
                            data: cumulativeData
                        }
                    ]);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching GWP data:", error);
                }
            });

            var table = $('#budget-kpi-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kpis.getKpis') }}",
                searching: false,
                paging: false,
                info: false,
                columns: [{
                        data: 'kpi',
                        name: 'kpi',
                        class: 'fs-18'
                    },
                    {
                        data: 'current_value',
                        name: 'current_value',
                        sortable: false

                    },
                    {
                        data: 'target',
                        name: 'target',
                        sortable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        sortable: false
                    },
                ]
            });

            // Sample data for budget vs. actual income
            const data = {
                months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                budget: [50000, 45000, 60000, 55000, 65000, 70000, 75000, 72000, 68000, 70000, 65000, 80000],
                actual: [48000, 46500, 65000, 50000, 68000, 67000, 80000, 79000, 62000, 74000, 61000, 85000]
            };

            // Calculate variance
            const variance = data.months.map((month, index) => {
                return {
                    x: month,
                    y: data.actual[index] - data.budget[index]
                };
            });

            const incomeOptions = {
                series: [{
                    name: 'Variance (Actual - Budget)',
                    data: variance
                }],
                chart: {
                    type: 'bar',
                    height: 400,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 3,
                        dataLabels: {
                            position: 'top',
                        },
                        colors: {
                            ranges: [{
                                from: -Infinity,
                                to: 0,
                                color: '#F15B46' // Red for negative variance
                            }, {
                                from: 0,
                                to: Infinity,
                                color: '#47B39C' // Green for positive variance
                            }]
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return '$' + val.toLocaleString();
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },
                xaxis: {
                    categories: data.months,
                    title: {
                        text: 'Month'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Variance (in $)'
                    },
                    labels: {
                        formatter: function(val) {
                            return '$' + val.toLocaleString();
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return '$' + val.toLocaleString();
                        }
                    }
                },
                annotations: {
                    yaxis: [{
                        y: 0,
                        strokeDashArray: 0,
                        borderColor: '#A9A9A9',
                        borderWidth: 1
                    }]
                },
                subtitle: {
                    text: 'Positive values indicate performance above budget, negative values indicate performance below budget',
                    align: 'center'
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 5,
                }
            };

            const bdgt_income_chart = new ApexCharts(document.querySelector("#budgetActualIncomeChart"),
                incomeOptions);
            bdgt_income_chart.render();

            //

            const months = {!! json_encode($months) !!};
            const lineOfBusiness = {!! json_encode($lineOfBusiness) !!};
            const monthlyData = {!! json_encode($monthlyData) !!};

            const series = [];
            lineOfBusiness.forEach((lob) => {
                series.push({
                    name: lob + ' Budget',
                    type: 'line',
                    data: months.map(month => monthlyData[lob][month]['budget'])
                });
                series.push({
                    name: lob + ' Achieved',
                    type: 'line',
                    data: months.map(month => monthlyData[lob][month]['achieved']),
                    dashArray: 5
                });
            });
            lineOfBusiness.forEach((lob) => {
                series.push({
                    name: lob + ' Hit Rate',
                    type: 'column',
                    data: months.map(month => {
                        const budget = monthlyData[lob][month]['budget'];
                        const achieved = monthlyData[lob][month]['achieved'];
                        return budget > 0 ? parseFloat(((achieved / budget) * 100).toFixed(
                            1)) : 0;
                    })
                });
            });

            const colors = [
                '#1E90FF', '#00C49F', '#845EC2', '#FFC75F', // Budget lines
                '#00B8A9', '#2C497F', '#D65DB1', '#FF6F61', // Achieved lines
                '#63E2FF', '#82FF9E', '#FFA07A', '#F9F871' // Hit Rate bars
            ];

            const hitRateAnalysisChartOptions = {
                series: series,
                chart: {
                    height: 500,
                    type: 'line',
                    stacked: false,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                colors: colors,
                stroke: {
                    width: [3, 3, 3, 3, 3, 3, 3, 3, 0, 0, 0, 0],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%',
                        borderRadius: 3
                    }
                },
                fill: {
                    opacity: [0.85, 0.85, 0.85, 0.85, 0.85, 0.85, 0.85, 0.85, 1, 1, 1, 1],
                    gradient: {
                        inverseColors: false,
                        shade: 'light',
                        type: "vertical",
                        opacityFrom: 0.85,
                        opacityTo: 0.55,
                    }
                },
                markers: {
                    size: 4,
                    strokeWidth: 0,
                    hover: {
                        size: 7
                    }
                },
                xaxis: {
                    categories: months,
                    title: {
                        text: 'Month',
                        style: {
                            fontSize: '12px',
                            fontWeight: 500
                        }
                    }
                },
                yaxis: [{
                        title: {
                            text: "GWP (in millions KES)",
                            style: {
                                fontSize: '12px',
                                fontWeight: 500
                            }
                        },
                        labels: {
                            formatter: function(val) {
                                return "KES " + val + "M";
                            }
                        },
                        // Optional min/max
                        min: 0,
                        max: undefined // Remove or set as appropriate
                    },
                    {
                        opposite: true,
                        title: {
                            text: "Hit Rate (%)",
                            style: {
                                fontSize: '12px',
                                fontWeight: 500
                            }
                        },
                        min: 0,
                        max: 150,
                        labels: {
                            formatter: function(val) {
                                return val.toFixed(0) + "%";
                            }
                        }
                    }
                ],
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(y, {
                            series,
                            seriesIndex,
                            dataPointIndex,
                            w
                        }) {
                            const seriesName = w.globals.seriesNames[seriesIndex];

                            if (seriesName.includes('Hit Rate')) {
                                return y.toFixed(1) + "%";
                            } else {
                                return "$" + y.toFixed(1) + "M";
                            }
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                    fontSize: '12px',
                    markers: {
                        width: 12,
                        height: 12
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: '#f1f1f1',
                    row: {
                        colors: ['transparent', 'transparent'],
                        opacity: 0.5
                    }
                }
            };

            const chart02 = new ApexCharts(document.querySelector("#hitRateAnalysisChart"),
                hitRateAnalysisChartOptions);
            chart02.render();

            const monthlyData1 = @json($monthlyData1);

            const options2 = {
                chart: {
                    type: 'line',
                    height: 200
                },
                series: [{
                    name: 'Hit Rate %',
                    data: monthlyData1.map(item => item.hitRate)
                }],
                xaxis: {
                    categories: monthlyData1.map(item => item.name)
                },
                yaxis: {
                    min: 60,
                    max: 100
                },
                tooltip: {
                    enabled: true
                },
                grid: {
                    strokeDashArray: 3
                },
                stroke: {
                    curve: 'monotone'
                },
                colors: ['#ff7300'],
                legend: {
                    show: true
                }
            };

            const chart09 = new ApexCharts(document.querySelector("#hit-rate-chart"), options2);
            chart09.render();

            const ctx = document.getElementById('hitRateChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyData1.map(item => item.name),
                    datasets: [{
                        label: 'Hit Rate %',
                        data: monthlyData1.map(item => item.hitRate),
                        backgroundColor: 'rgba(255, 115, 0, 0.2)',
                        borderColor: '#ff7300',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 60,
                            max: 100,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.1)',
                                lineWidth: 1,
                                drawTicks: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 3
                        }
                    }
                }
            });
        });
    </script>
@endpush
