@extends('layouts.app', [
    'pageTitle' => 'Debtors - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Debtors</p>
            <span class="fs-semibold text-muted">Track key metrics and performance across your reinsurance portfolio.</span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Business Intelligence</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Debtors</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            // Sample data - in a real app, this would be passed from the controller
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

            const lossRatioData = {
                yearly: {
                    labels: ['2020', '2021', '2022', '2023', '2024'],
                    values: [66.7, 53.8, 60.3, 52.5, 54.4]
                },
                quarterly: {
                    labels: ['Q1 2024', 'Q2 2024', 'Q3 2024', 'Q4 2024'],
                    values: [53.3, 52.8, 46.9, 63.2]
                }
            };

            // Premium vs Claims Chart
            const premiumClaimsOptions = {
                series: [{
                        name: 'Premium (KES millions)',
                        data: premiumData.yearly.premium
                    },
                    {
                        name: 'Claims (KES millions)',
                        data: premiumData.yearly.claims
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 350,
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
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: premiumData.yearly.labels,
                },
                yaxis: {
                    title: {
                        text: 'KES (millions)'
                    },
                    labels: {
                        formatter: function(value) {
                            return 'KES ' + value + 'M';
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'KES ' + val + ' million';
                        }
                    }
                },
                colors: ['#4299e1', '#f56565']
            };

            const premiumClaimsChart = new ApexCharts(document.querySelector("#premiumClaimsChart"),
                premiumClaimsOptions);
            premiumClaimsChart.render();

            // Loss Ratio Chart
            const lossRatioOptions = {
                series: [{
                    name: 'Loss Ratio (%)',
                    data: lossRatioData.yearly.values
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                    colors: ['#8884d8']
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    },
                    colors: ['#8884d8']
                },
                xaxis: {
                    categories: lossRatioData.yearly.labels,
                },
                yaxis: {
                    min: 40,
                    max: 80,
                    title: {
                        text: 'Loss Ratio (%)'
                    },
                    labels: {
                        formatter: function(value) {
                            return value.toFixed(1) + '%';
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

            const lossRatioChart = new ApexCharts(document.querySelector("#lossRatioChart"), lossRatioOptions);
            lossRatioChart.render();

            // Line of Business Chart
            const lineOfBusinessOptions = {
                series: [35, 25, 15, 10],
                chart: {
                    type: 'pie',
                    height: 360
                },
                labels: ['Facultative', 'Treaty', 'Specialty Lines', 'International Market'],
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

            // Treaty Type Chart
            const treatyTypeOptions = {
                series: [1, 1, 1, 1],
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: ['Quota Share', 'Excess of Loss', 'Stop Loss', 'Facultative'],
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

            const treatyTypeChart = new ApexCharts(document.querySelector("#treatyTypeChart"), treatyTypeOptions);
            treatyTypeChart.render();

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

                // Update Loss Ratio Chart
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
        });
    </script>
@endpush
