@extends('layouts.app', [
    'pageTitle' => 'Analytics - ' . $company->company_name,
])

@section('styles')
    <style>
        .grid {
            display: grid;
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        @media (min-width: 1024px) {
            .lg\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .gap-6 {
            gap: 24px;
            /* gap-6 (6 * 4px) */
        }

        .mb-8 {
            margin-bottom: 32px;
            /* mb-8 (8 * 4px) */
        }

        .card {
            background-color: #ffffff;
            /* bg-white */
            border-radius: 12px;
            /* rounded-lg (large ~12px) */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1),
                0 1px 2px rgba(0, 0, 0, 0.06);
            /* shadow */
            padding: 24px;
            /* p-6 (6 * 4px) */
        }

        .card h2 {
            font-size: 1.25rem;
            /* text-xl */
            font-weight: 600;
            /* font-semibold */
            margin-bottom: 16px;
            /* mb-4 (4 * 4px) */
        }

        .h-80 {
            height: 20rem;
            /* 80 * 0.25rem = 20rem */
        }

        .h-96 {
            height: 24rem;
            /* 96 * 0.25rem = 24rem */
        }
    </style>
@endsection

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Cover Administration</p>
            <span class="fs-semibold text-muted">Track key metrics and performance across your reinsurance portfolio.</span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Business Intelligence</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cover Administration</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="py-3">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <h2>Top 10 Cedants by GWP (KES)</h2>
                <div id="topCedantsPremiumChart" class="h-80"></div>
            </div>
            <div class="card">
                <h2>Top 10 Cedants by Income (KES)</h2>
                <div id="topCedantsRevenueChart" class="h-80"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <h2>GWP vs Income Comparison</h2>
                <div id="premiumVsRevenueChart" class="h-80"></div>
            </div>
            <div class="card">
                <h2>Income Generation Percentage</h2>
                <div id="incomePercentageChart" class="h-80"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 mb-8">
            <div class="card">
                <h2>GWP to Income Efficiency Ratio</h2>
                <div id="efficiencyRatioChart" class="h-80"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <h2>Market Share Distribution (GWP)</h2>
                <div id="marketShareChart" class="h-96"></div>
            </div>
            <div class="card">
                <h2>Income vs GWP Scatter Analysis</h2>
                <div id="scatterChart" class="h-96"></div>
            </div>
        </div>

        <div class="card">
            <h2>GWP and Income Distribution by Top Cedants</h2>
            <div id="stackedBarChart" class="h-96"></div>
        </div>
    </div>

    <div class="pb-3">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <h2>Top 10 Reinsurers by GWP (KES)</h2>
                <div id="topReinsPremiumChart" class="h-80"></div>
            </div>
        </div>

        <div class="card">
            <h2>GWP Distribution by Top Reinsurers</h2>
            <div id="stackedReinBarChart" class="h-96"></div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            // Get data from Laravel backend
            const cedantData = @json($cedantData);

            // Sort data by premium amount descending
            const sortedByPremium = [...cedantData].sort((a, b) => b.premium - a.premium);
            const top10ByPremium = sortedByPremium.slice(0, 10);

            // Sort data by revenue amount descending
            const sortedByRevenue = [...cedantData].sort((a, b) => b.revenue - a.revenue);
            const top10ByRevenue = sortedByRevenue.slice(0, 10);

            // Top 10 Cedants by Premium Chart (GWP)
            const topCedantsPremiumOptions = {
                series: [{
                    name: 'GWP (KES)',
                    data: top10ByPremium.map(item => item.premium /
                        1000000) // Convert to millions for better display
                }],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: top10ByPremium.map(item => {
                        // Shorten company names for better display
                        const name = item.name;
                        return name.length > 25 ? name.substring(0, 25) + '...' : name;
                    }),
                    title: {
                        text: 'GWP Amount (Millions KES)'
                    }
                },
                colors: ['#1A56DB'],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + ' Million KES';
                        }
                    }
                }
            };

            const topCedantsPremiumChart = new ApexCharts(document.querySelector("#topCedantsPremiumChart"),
                topCedantsPremiumOptions);
            topCedantsPremiumChart.render();

            // Top 10 Cedants by Revenue Chart (Income)
            const topCedantsRevenueOptions = {
                series: [{
                    name: 'Income (KES)',
                    data: top10ByRevenue.map(item => item.revenue /
                        1000000) // Convert to millions for better display
                }],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: top10ByRevenue.map(item => {
                        // Shorten company names for better display
                        const name = item.name;
                        return name.length > 25 ? name.substring(0, 25) + '...' : name;
                    }),
                    title: {
                        text: 'Income Amount (Millions KES)'
                    }
                },
                colors: ['#FF6B3D'],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + ' Million KES';
                        }
                    }
                }
            };

            const topCedantsRevenueChart = new ApexCharts(document.querySelector("#topCedantsRevenueChart"),
                topCedantsRevenueOptions);
            topCedantsRevenueChart.render();

            // GWP vs Income Comparison Chart
            const premiumVsRevenueOptions = {
                series: [{
                    name: 'GWP',
                    type: 'column',
                    data: top10ByPremium.map(item => item.premium / 1000000)
                }, {
                    name: 'Income',
                    type: 'line',
                    data: top10ByPremium.map(item => item.revenue / 1000000)
                }],
                chart: {
                    height: 320,
                    type: 'line',
                    toolbar: {
                        show: false
                    }
                },
                stroke: {
                    width: [0, 4]
                },
                dataLabels: {
                    enabled: false,
                    enabledOnSeries: [1]
                },
                labels: top10ByPremium.map(item => {
                    const name = item.name;
                    return name.substring(0, 10) + '...';
                }),
                xaxis: {
                    type: 'category',
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Amount (Millions KES)'
                    }
                },
                colors: ['#1A56DB', '#FF6B3D'],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + ' Million KES';
                        }
                    }
                }
            };

            const premiumVsRevenueChart = new ApexCharts(document.querySelector("#premiumVsRevenueChart"),
                premiumVsRevenueOptions);
            premiumVsRevenueChart.render();

            // Income Generation Percentage Chart
            const incomePercentageOptions = {
                series: sortedByPremium.slice(0, 10).map(item => item.percentage),
                chart: {
                    type: 'donut',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },
                labels: sortedByPremium.slice(0, 10).map(item => {
                    const name = item.name;
                    return name.length > 20 ? name.substring(0, 20) + '...' : name;
                }),
                colors: ['#1A56DB', '#FF6B3D', '#32C27D', '#F7B84B', '#A855F7', '#F43F5E', '#0891B2', '#0D9488',
                    '#6366F1', '#8B5CF6'
                ],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + '%';
                    }
                },
                title: {
                    text: 'Income Generation as % of Total',
                    align: 'center'
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(1) + '%';
                        }
                    }
                }
            };

            const incomePercentageChart = new ApexCharts(document.querySelector("#incomePercentageChart"),
                incomePercentageOptions);
            incomePercentageChart.render();

            // Efficiency Ratio Chart (Income / GWP)
            const efficiencyData = cedantData.map(item => {
                return {
                    name: item.name,
                    ratio: (item.revenue / item.premium) * 100 // Convert to percentage
                };
            }).sort((a, b) => b.ratio - a.ratio).slice(0, 15); // Top 15 by efficiency

            const efficiencyRatioOptions = {
                series: [{
                    name: 'Income/GWP Ratio',
                    data: efficiencyData.map(item => parseFloat(item.ratio.toFixed(2)))
                }],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: '70%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: efficiencyData.map(item => {
                        const name = item.name;
                        return name.substring(0, 10) + '...';
                    }),
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Income/GWP Ratio (%)'
                    }
                },
                colors: ['#32C27D'],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + '%';
                        }
                    }
                }
            };

            const efficiencyRatioChart = new ApexCharts(document.querySelector("#efficiencyRatioChart"),
                efficiencyRatioOptions);
            efficiencyRatioChart.render();

            // Market Share Distribution Chart (GWP)
            const marketShareData = sortedByPremium.map(item => {
                // Calculate market share as percentage of total premium
                const totalPremium = cedantData.reduce((sum, item) => sum + item.premium, 0);
                return {
                    name: item.name,
                    marketShare: (item.premium / totalPremium) * 100
                };
            });

            // Group small shares as "Others"
            const significantShares = marketShareData.filter(item => item.marketShare > 1);
            const otherShareSum = marketShareData
                .filter(item => item.marketShare <= 1)
                .reduce((sum, item) => sum + item.marketShare, 0);

            const marketShareOptions = {
                series: [
                    ...significantShares.map(item => parseFloat(item.marketShare.toFixed(2))),
                    parseFloat(otherShareSum.toFixed(2))
                ],
                chart: {
                    type: 'treemap',
                    height: 400,
                    toolbar: {
                        show: false
                    }
                },
                legend: {
                    show: false
                },
                title: {
                    text: 'Market Share by GWP Volume',
                    align: 'center'
                },
                colors: [
                    '#1A56DB', '#FF6B3D', '#32C27D', '#F7B84B', '#A855F7',
                    '#F43F5E', '#0891B2', '#0D9488', '#6366F1', '#8B5CF6',
                    '#777777'
                ],
                labels: [
                    ...significantShares.map(item => {
                        const name = item.name;
                        return name.length > 25 ? name.substring(0, 25) + '...' : name;
                    }),
                    'Others'
                ],
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + '%';
                        }
                    }
                }
            };

            const marketShareChart = new ApexCharts(document.querySelector("#marketShareChart"),
                marketShareOptions);
            marketShareChart.render();

            // Income vs GWP Scatter Analysis
            const scatterData = cedantData.map(item => {
                return {
                    x: item.premium / 1000000, // GWP in millions
                    y: item.revenue / 1000000, // Income in millions
                    name: item.name
                };
            });

            const scatterOptions = {
                series: [{
                    name: 'Cedants',
                    data: scatterData
                }],
                chart: {
                    height: 400,
                    type: 'scatter',
                    zoom: {
                        enabled: true,
                        type: 'xy'
                    },
                    toolbar: {
                        show: false
                    }
                },
                xaxis: {
                    title: {
                        text: 'GWP (Millions KES)'
                    },
                    tickAmount: 10
                },
                yaxis: {
                    title: {
                        text: 'Income (Millions KES)'
                    },
                    tickAmount: 7
                },
                colors: ['#1A56DB'],
                markers: {
                    size: 6
                },
                tooltip: {
                    custom: function({
                        series,
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        const data = w.config.series[seriesIndex].data[dataPointIndex];
                        return `<div class="p-2">
                        <div class="font-bold">${data.name}</div>
                        <div>GWP: ${data.x.toFixed(2)} Million KES</div>
                        <div>Income: ${data.y.toFixed(2)} Million KES</div>
                    </div>`;
                    }
                }
            };

            const scatterChart = new ApexCharts(document.querySelector("#scatterChart"), scatterOptions);
            scatterChart.render();

            // GWP and Income Distribution by Top Cedants
            const stackedBarOptions = {
                series: [{
                    name: 'GWP',
                    data: top10ByPremium.map(item => item.premium / 1000000)
                }, {
                    name: 'Income',
                    data: top10ByPremium.map(item => item.revenue / 1000000)
                }],
                chart: {
                    type: 'bar',
                    height: 400,
                    stacked: false,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '70%'
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
                    categories: top10ByPremium.map(item => {
                        const name = item.name;
                        return name.substring(0, 8) + '...';
                    }),
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Amount (Millions KES)'
                    }
                },
                colors: ['#1A56DB', '#FF6B3D'],
                legend: {
                    position: 'top'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toFixed(2) + ' Million KES';
                        }
                    }
                }
            };

            const stackedBarChart = new ApexCharts(document.querySelector("#stackedBarChart"), stackedBarOptions);
            stackedBarChart.render();
        });
    </script>
@endpush
