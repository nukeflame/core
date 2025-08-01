@extends('layouts.app', [
    'pageTitle' => 'Production Summary Reports - ' . $company->company_name,
])

@include('reports._report_styles')

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <p class="fw-semibold fs-18 mb-0">Production Summary Reports</p>
            <span class="fs-semibold text-muted">Analyze production data by reporting across different time periods</span>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pipeline.report') }}">Reports</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('production-reports.index') }}">Production</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Summary Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" action="{{ route('production-reports.index') }}" method="GET">
                        <div class="filter-bar">
                            <div class="filter-item">
                                <label for="date_range">Date Range:</label>
                                <select id="date_range" name="date_range" class="form-inputs">
                                    <option value="year-to-date"
                                        {{ request('date_range', 'year-to-date') == 'year-to-date' ? 'selected' : '' }}>Year
                                        to Date</option>
                                    <option value="last-quarter"
                                        {{ request('date_range') == 'last-quarter' ? 'selected' : '' }}>Last Quarter
                                    </option>
                                    <option value="last-6-months"
                                        {{ request('date_range') == 'last-6-months' ? 'selected' : '' }}>Last 6 Months
                                    </option>
                                    <option value="last-12-months"
                                        {{ request('date_range') == 'last-12-months' ? 'selected' : '' }}>Last 12 Months
                                    </option>
                                    <option value="custom-range"
                                        {{ request('date_range') == 'custom-range' ? 'selected' : '' }}>Custom Range
                                    </option>
                                </select>
                            </div>

                            <div class="filter-item customDateContainer"
                                style="{{ request('date_range') == 'custom-range' ? '' : 'display: none;' }}">
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" class="form-inputs"
                                    value="{{ request('start_date', now()->subYear()->format('Y-m-d')) }}">
                            </div>
                            <div class="filter-item customDateContainer"
                                style="{{ request('date_range') == 'custom-range' ? '' : 'display: none;' }}">
                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" name="end_date" class="form-inputs"
                                    value="{{ request('end_date', now()->format('Y-m-d')) }}">
                            </div>

                            <div class="filter-item">
                                <label for="currency">Currency:</label>
                                <select id="currency" name="currency" class="form-inputs">
                                    <option value="KES" {{ request('currency', 'KES') == 'KES' ? 'selected' : '' }}>KES
                                    </option>
                                    <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>USD
                                    </option>
                                    <option value="EUR" {{ request('currency') == 'EUR' ? 'selected' : '' }}>EUR
                                    </option>
                                    <option value="GBP" {{ request('currency') == 'GBP' ? 'selected' : '' }}>GBP
                                    </option>
                                </select>
                            </div>

                            <div class="filter-item">
                                <label for="business_class">Business Class:</label>
                                <select id="business_class" name="business_class" class="form-inputs"
                                    style="min-width: 255px;">
                                    <option value="all"
                                        {{ request('business_class', 'all') == 'all' ? 'selected' : '' }}>
                                        All Classes
                                    </option>
                                    @foreach ($businessClasses ?? [] as $class)
                                        <option value="{{ $class->id }}"
                                            {{ request('business_class') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                        </div>
                    </form>

                    <div class="metrics">
                        <div class="metric-card">
                            <div class="metric-title">Total Premium ({{ $metrics['total_premium']['currency'] ?? 'KES' }})
                            </div>
                            <div class="metric-value">{{ number_format($metrics['total_premium']['value'] ?? 0, 2) }}</div>
                            <div class="metric-trend trend-{{ $metrics['total_premium']['trend_direction'] ?? 'up' }}">
                                {{ number_format(abs($metrics['total_premium']['trend'] ?? 0), 1) }}% from last period
                            </div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-title">Commission Earned</div>
                            <div class="metric-value">{{ number_format($metrics['commission_earned']['value'] ?? 0, 2) }}
                            </div>
                            <div class="metric-trend trend-{{ $metrics['commission_earned']['trend_direction'] ?? 'up' }}">
                                {{ number_format(abs($metrics['commission_earned']['trend'] ?? 0), 1) }}% from last period
                            </div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-title">Claims Ratio</div>
                            <div class="metric-value">{{ number_format($metrics['claims_ratio']['value'] ?? 0, 1) }}%</div>
                            <div class="metric-trend trend-{{ $metrics['claims_ratio']['trend_direction'] ?? 'down' }}">
                                {{ number_format(abs($metrics['claims_ratio']['trend'] ?? 0), 1) }}% from last period
                            </div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-title">Active Policies</div>
                            <div class="metric-value">{{ number_format($metrics['active_policies']['value'] ?? 0) }}</div>
                            <div class="metric-trend trend-{{ $metrics['active_policies']['trend_direction'] ?? 'up' }}">
                                {{ ($metrics['active_policies']['trend'] ?? 0) > 0 ? '+' : '' }}{{ number_format($metrics['active_policies']['trend'] ?? 0) }}
                                from last period
                            </div>
                        </div>
                    </div>

                    <p class="mb-2 fw-medium" style="color:#333335;">Summary Production By :-</p>

                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'debit-type' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', array_merge(request()->except('tab'), ['tab' => 'debit-type'])) }}">
                                Debit Type
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'cedant' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', array_merge(request()->except('tab'), ['tab' => 'cedant'])) }}">
                                Cedant
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'ceding-broker' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', array_merge(request()->except('tab'), ['tab' => 'ceding-broker'])) }}">
                                Ceding Broker
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'reinsurer' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', array_merge(request()->except('tab'), ['tab' => 'reinsurer'])) }}">
                                Reinsurer
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'insured' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', array_merge(request()->except('tab'), ['tab' => 'insured'])) }}">
                                Insured
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'class' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', array_merge(request()->except('tab'), ['tab' => 'class'])) }}">
                                Class
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'class-group' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', array_merge(request()->except('tab'), ['tab' => 'class-group'])) }}">
                                Class Group
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'risk-location' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', array_merge(request()->except('tab'), ['tab' => 'risk-location'])) }}">
                                Risk Location
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'region' ? 'active' : '' }}"
                                href="{{ route('production-reports.index', array_merge(request()->except('tab'), ['tab' => 'region'])) }}">
                                Region
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        @if ($tab == 'debit-type')
                            @include('reports.production-reports.debit_type_summary')
                        @elseif($tab == 'cedant')
                            @include('reports.production-reports.cedant_summary')
                        @elseif($tab == 'ceding-broker')
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-info-circle me-2"></i> Production by ceding broker content will be
                                displayed here
                            </div>
                        @elseif($tab == 'reinsurer')
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-info-circle me-2"></i> Production by reinsurer content will be displayed
                                here
                            </div>
                        @elseif($tab == 'insured')
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-info-circle me-2"></i> Production by insured content will be displayed here
                            </div>
                        @elseif($tab == 'class')
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-info-circle me-2"></i> Production by class content will be displayed here
                            </div>
                        @elseif($tab == 'class-group')
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-info-circle me-2"></i> Production by class group content will be displayed
                                here
                            </div>
                        @elseif($tab == 'risk-location')
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-info-circle me-2"></i> Production by risk location content will be
                                displayed here
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                <i class="fa fa-info-circle me-2"></i> Production by region content will be displayed here
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('.customDateContainer').hide();

            $('#date_range').change(function() {
                if ($(this).val() === 'custom-range') {
                    $('.customDateContainer').show();
                } else {
                    $('.customDateContainer').hide();
                }
            });

            $('#businessPerformanceTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('production-reports.debit-type.business') }}",
                    data: function(d) {
                        d.date_range = $('#date_range').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.currency = $('#currency').val();
                        d.business_class = $('#business_class').val();
                    }
                },
                columns: [{
                        data: 'index',
                        className: "highlight-index",
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    }, {
                        data: 'type_code',
                    },
                    {
                        data: 'type_name'
                    },
                    {
                        data: 'premium_kes',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'commission_kes',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'prm_tax_kes',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'rein_tax_kes',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'w_tax_kes',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'vat_kes',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'claims_kes',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'ue_kes',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    },
                    {
                        data: 'comm_percent',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 1,
                                maximumFractionDigits: 1
                            }) + '%';
                        }
                    }
                ],
            });

            if ($('#debitTypeFinancialTable').length) {
                $('#debitTypeFinancialTable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: {
                        url: "{{ route('production-reports.debit-type.financial') }}",
                        data: function(d) {
                            d.date_range = $('#date_range').val();
                            d.start_date = $('#start_date').val();
                            d.end_date = $('#end_date').val();
                            d.currency = $('#currency').val();
                            d.business_class = $('#business_class').val();
                        }
                    },
                    columns: [{
                            data: 'category'
                        },
                        {
                            data: 'subcategory'
                        },
                        {
                            data: 'type'
                        },
                        {
                            data: 'month'
                        },
                        {
                            data: 'budgeted',
                            render: function(data) {
                                return parseFloat(data).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        },
                        {
                            data: 'actual',
                            render: function(data) {
                                return parseFloat(data).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        },
                        {
                            data: 'variance',
                            render: function(data) {
                                var className = parseFloat(data) >= 0 ? 'text-success' :
                                    'text-danger';
                                return '<span class="' + className + '">' +
                                    parseFloat(data).toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }) + '</span>';
                            }
                        },
                        {
                            data: 'achievement_percent',
                            render: function(data) {
                                var className = parseFloat(data) >= 100 ? 'text-success' :
                                    'text-danger';
                                return '<span class="' + className + '">' +
                                    parseFloat(data).toLocaleString('en-US', {
                                        minimumFractionDigits: 1,
                                        maximumFractionDigits: 1
                                    }) + '%</span>';
                            }
                        }
                    ],
                });
            }

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true,
                    reload: true
                })
            });

            const currentYear = new Date().getFullYear();
            let yearOptions = '';
            for (let year = currentYear; year >= currentYear - 9; year--) {
                yearOptions += `<option value="${year}">${year}</option>`;
            }

            $('#printReportBtn').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Budget Report Parameters',
                    icon: 'info',
                    html: `
                        <div class="budget-selection-form">
                            <div class="mb-3">
                                <label for="budgetYear">Select Budget Year:</label>
                                <select id="budgetYear" class="form-select">
                                    ${yearOptions}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="budgetQuarter">Select Quarter:</label>
                                <select id="budgetQuarter" class="form-select">
                                    <option value="1">Q1 (Jan-Mar)</option>
                                    <option value="2">Q2 (Apr-Jun)</option>
                                    <option value="3">Q3 (Jul-Sep)</option>
                                    <option value="4">Q4 (Oct-Dec)</option>
                                    <option value="all">All Quarters</option>
                                </select>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Generate Report',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    focusConfirm: false,
                    allowOutsideClick: false,
                    preConfirm: () => {
                        const year = document.getElementById('budgetYear').value;
                        const quarter = document.getElementById('budgetQuarter').value;

                        if (!year || !quarter) {
                            Swal.showValidationMessage('Please select all required fields');
                            return false;
                        }

                        return {
                            year,
                            quarter
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        let reportUrl = "{{ route('production-reports.export') }}";
                        const allParams = {
                            tab: 'debit-type',
                            report: 'financial-summary',
                            format: 'excel',
                            year: result.value.year,
                            quarter: result.value.quarter
                        };

                        const queryString = Object.entries(allParams)
                            .map(([key, value]) => {
                                if (value !== null && typeof value === 'object') {
                                    return Object.entries(value)
                                        .map(([k, v]) =>
                                            `${encodeURIComponent(key)}[${encodeURIComponent(k)}]=${encodeURIComponent(v)}`
                                        )
                                        .join('&');
                                }
                                return `${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
                            })
                            .join('&');

                        const fullUrl = '/reports/production-reports/export' + (queryString ?
                            `?${queryString}` : '');

                        Swal.fire({
                            title: 'Generating Report',
                            text: `Preparing Financial Summary for ${result.value.quarter === 'all' ? 'All Quarters' : 'Q' + result.value.quarter} ${result.value.year}...`,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                                const redirectTimeout = setTimeout(() => {
                                    window.location.href = fullUrl;
                                    Swal.close();
                                }, 3000);

                                Swal.getPopup().addEventListener('swal2-hide', () => {
                                    clearTimeout(redirectTimeout);
                                    Swal.close();
                                });
                            }
                        });
                    }
                });
            });

            // {{-- $('#cedantTable').DataTable({
            //     processing: true,
            //     serverSide: true,
            //     ajax: "{{ route('production-reports.cedant-summary.data') }}",
            //     columns: [{
            //             data: 'DT_RowIndex',
            //             name: 'DT_RowIndex'
            //         },
            //         {
            //             data: 'cedant_name',
            //             name: 'cedant_name'
            //         },
            //         {
            //             data: 'premium',
            //             name: 'premium',
            //             render: function(data) {
            //                 return parseFloat(data).toLocaleString('en-US', {
            //                     minimumFractionDigits: 2,
            //                     maximumFractionDigits: 2
            //                 });
            //             }
            //         },
            //         {
            //             data: 'revenue',
            //             name: 'revenue',
            //             render: function(data) {
            //                 return parseFloat(data).toLocaleString('en-US', {
            //                     minimumFractionDigits: 2,
            //                     maximumFractionDigits: 2
            //                 });
            //             }
            //         },
            //         {
            //             data: 'income_percentage',
            //             name: 'income_percentage',
            //             render: function(data) {
            //                 return parseFloat(data).toFixed(2) + '%';
            //             }
            //         }
            //     ],
            //     drawCallback: function(settings) {
            //         var api = this.api();
            //         var totalPremium = api.column(2).data().reduce(function(a, b) {
            //             return parseFloat(a) + parseFloat(b);
            //         }, 0);
            //         var totalRevenue = api.column(3).data().reduce(function(a, b) {
            //             return parseFloat(a) + parseFloat(b);
            //         }, 0);

            //         $('#totalPremium').text(totalPremium.toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         }));
            //         $('#totalRevenue').text(totalRevenue.toLocaleString('en-US', {
            //             minimumFractionDigits: 2,
            //             maximumFractionDigits: 2
            //         }));
            //     }
            // --}} });
        });
    </script>
@endpush
