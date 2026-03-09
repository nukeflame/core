{{-- @extends('layouts.admincast') --}}
@extends('layouts.app')
{{-- @extends('layouts.intermediaries.base') --}}
@section('header', 'MENU ITEMS')
@section('content')
    <style>
        /* Table row styles */
        #client-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Aptos', Arial, sans-serif;
            font-size: 14px;
            color: #333;
            background-color: #fff;
        }

        #client-table thead.thead-light {
            background-color: #e9ecef;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
        }

        #client-table th,
        #client-table td {
            padding: 12px;
            border: 1px solid #dee2e6;
            text-align: left;
            vertical-align: middle;
        }

        #client-table th {
            font-size: 13px;
        }

        #client-table tbody td {
            font-size: 13px;
        }

        #client-table.table-striped tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
        }

        #client-table.table-hover tbody tr:hover {
            background-color: #e6f3ff;
        }

        /* Optional highlight classes from original code */
        #client-table tbody tr.highlight-danger {
            background-color: #ffebee !important;
        }

        #client-table tbody tr.highlight-warning {
            background-color: #fff3e0 !important;
        }

        #client-table tbody tr.highlight-info {
            background-color: #e3f2fd !important;
        }

        #client-table tbody tr.highlight-danger:hover {
            background-color: #ffcdd2 !important;
        }

        #client-table tbody tr.highlight-warning:hover {
            background-color: #ffe0b2 !important;
        }

        #client-table tbody tr.highlight-info:hover {
            background-color: #bbdefb !important;
        }




        /* Legend styles */
    </style>
    {{-- <div class="card-header ">
        <div>

            <strong>
                <h5>Pipeline Details</h5>
            </strong>
            <form id="pip_year_form" action="{{ route('pipeline.view') }}" method="">
                <input type="hidden" id="opp_id" name="opp_id">
                <div class="row">
                    <div class="col-md-3">
                        <x-SearchableSelect id="pip_year_select" req="" inputLabel="" name="pip_year_id">
                            @foreach ($pipelines as $pip_year)
                                <option value="{{ $pip_year->id }}">{{ $pip_year->year }}</option>
                            @endforeach
                        </x-SearchableSelect>
                    </div>
                    <div class="col-md-3 mt-3">
                        <button id="filterbtn" type="button" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}
    <div class="card mt-3 border">
        <div class="card-header">
            <strong>
                <h5>Pipeline Opportunities Report</h5>
            </strong>
            <form id="pip_year_form" class="filter-form" action="{{ route('pipeline.view') }}" method="">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from_year">Pipeline Year (From)</label>
                            <select id="from_year" name="from_year" class="form-control">
                                <option value="">---All Years---</option>
                                @foreach ($pipelines as $pip_year)
                                    <option value="{{ $pip_year->id }}">{{ $pip_year->year }}</option>
                                @endforeach
                            </select>
                            <label for="to_year">Pipeline Year (To)</label>
                            <select id="to_year" name="to_year" class="form-control">
                                <option value="">---All Years---</option>
                                @foreach ($pipelines as $pip_year)
                                    <option value="{{ $pip_year->id }}">{{ $pip_year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="client_category_select">Expected Closure Date</label>
                            <input type="date" id="closure_date" name="closure_date" class="form-control">
                        </div>
                    </div>
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                            <label for="lead_status_select">Lead Status</label>
                            <select id="lead_status_select" name="lead_status" class="form-control">
                                <option value="">All Statuses</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="country_code_select">Country</label>
                            <select id="country_code_select" name="country_code" class="form-control">
                                <option value="">All Countries</option>
                                <!-- Populate dynamically or add common codes -->
                                <option value="US">United States</option>
                                <option value="UK">United Kingdom</option>
                                <option value="KE">Kenya</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="col-md-3">
                        <div class="date-range-group">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control">


                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                            <label for="industry_select">Industry</label>
                            <select id="industry_select" name="industry" class="form-control">

                            </select>
                        </div>
                    </div> --}}
                    <div class="col-md-3 mt-4">
                        <button id="filterbtn" type="button" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="m-2">

            <div class="card table-responsive">
                <div class="card-body">

                    <div class="tab-content p-3 text-muted">
                        <div class="tab-pane active" id="client_listing">

                            <table class="table table-striped table-hover" id="client-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Pipeline Code</th>
                                        <th>Insured name</th>
                                        <th>Insured Category</th>
                                        <th>Cedant</th>
                                        <th>Lead Type</th>
                                        <th>Lead Name</th>
                                        <th>Effective Closure Date</th>
                                        {{-- <th>Division</th> --}}

                                        <th>Premium</th>
                                        {{-- <th>Income</th> --}}
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Currency</th>
                                        <th>Sum Insured Type</th>
                                        <th>Total Sum Insured</th>
                                        <th>Total Gross Premium</th>
                                        <th>Cedant Commissions</th>
                                        <th>Reinsurance Commission</th>
                                        <th>Total Brokerage</th>
                                        <th>Reverted To Pipeline</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>

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
                $("#myInput").on("change", function() {
                    var value = $(this).val().toLowerCase();

                    $("#client-table > tbody > tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
                // $('#filterbtn').onclick(function() {
                //    buildAjaxUrl();
                // });
                let year = $('#pip_year_select').val();

                function buildAjaxUrl() {
                    const params = {
                        from_year: $('#from_year').val(),
                        to_year: $('#to_year').val(),
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val(),
                        closure_date: $('#closure_date').val(),
                        client_category: $('#client_category_select').val(),
                        lead_status: $('#lead_status_select').val(),
                        country_code: $('#country_code_select').val(),
                        industry: $('#industry_select').val()
                    };
                    return "{{ route('report.data') }}?" + $.param(params);
                }

                function updateReportTitle() {
                    const now = new Date();
                    const title =
                        `Pipeline Report - ${now.getHours().toString().padStart(2, '0')}${now.getMinutes().toString().padStart(2, '0')}${now.getSeconds().toString().padStart(2, '0')}${now.getMilliseconds().toString().padStart(3, '0')}`;
                    return title;
                }

                function firstUpper(text) {
                    if (!text) return '';
                    return text
                        .toLowerCase()
                        .replace(/\b\w/g, char => char.toUpperCase());
                }

                function formatNumber(value) {
                    if (!value || isNaN(value)) return value;
                    return parseFloat(value.replace(/,/g, '')).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }

                function stripHtml(html) {
                    return $("<div>").html(html).text();
                }
                const table = $('#client-table').DataTable({
                    dom: 'Bfrtip',
                    buttons: [{
                        text: '<i class="fa fa-file-excel-o"></i> Export To Excel',
                        className: 'btn btn-success',
                        titleAttr: 'Export to Excel',
                        action: function(e, dt, button, config) {
                            window.location.href = buildAjaxUrl() + "&export=true";
                        }
                    }],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('report.data') }}?year=" + year,
                        type: "GET"
                    },
                    columns: [{
                            data: 'opportunity_id',
                            name: 'opp_id'
                        },
                        {
                            data: 'insured_name',
                            name: "insured_name",
                            render: function(data) {
                                return firstUpper(
                                    data);
                            }

                        },
                        {
                            data: 'client_category',
                            name: 'client_category',
                            render: function(data) {
                                return firstUpper(
                                    data);
                            }

                        },
                        {
                            data: 'cedant',
                            name: 'cedant',
                            render: function(data) {
                                return firstUpper(
                                    data);
                            }

                        },
                        {
                            data: 'client_type',
                            name: 'client_type',
                            render: function(data) {
                                return firstUpper(
                                    data);
                            }
                        },
                        {
                            data: 'lead_name',
                            name: 'lead_name',
                            render: function(data) {
                                return firstUpper(
                                    data);
                            }

                        },
                        {
                            data: 'fac_date_offered',
                            name: 'fac_date_offered'
                        },
                        {
                            data: 'cedant_premium',
                            name: 'cedant_premium',

                        },
                        {
                            data: 'effective_date',
                            name: 'effective_date'
                        },
                        {
                            data: 'closing_date',
                            name: 'closing_date'
                        },
                        {
                            data: 'currency_code',
                            name: 'currency_code'
                        },
                        {
                            data: 'sum_insured_type',
                            name: 'sum_insured_type',
                            render: function(data) {
                                return firstUpper(
                                    data);
                            }
                        },
                        {
                            data: 'total_sum_insured',
                            name: 'total_sum_insured',
                            render: function(data) {
                                if (!data) return '';
                                return Number(data).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        },
                        {
                            data: 'cede_premium',
                            name: 'cede_premium',
                            render: function(data) {
                                if (!data) return '';
                                return Number(data).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        },
                        {
                            data: 'comm_rate',
                            name: 'comm_rate'
                        },
                        {
                            data: 'reins_comm_rate',
                            name: 'reins_comm_rate'
                        },
                        {
                            data: 'brokerage_comm_amt',
                            name: 'brokerage_comm_amt',
                            render: function(data) {
                                if (!data) return '';
                                return Number(data).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        },
                        {
                            data: 'reverted_to_pipeline',
                            name: 'reverted_to_pipeline',
                            render: function(data) {
                                return firstUpper(
                                    data);
                            }
                        }
                    ],
                    order: [
                        [6, 'asc']
                    ]
                });
                $('#filterbtn').on('click', function() {
                    const fromYear = $('#from_year').val();
                    const toYear = $('#to_year').val();
                    if (fromYear && toYear && parseInt(fromYear) > parseInt(toYear)) {
                        alert('From Year cannot be greater than To Year.');
                        return;
                    }
                    $(this).prop('disabled', true).text('Loading...');
                    table.ajax.url(buildAjaxUrl()).load(function() {
                        $('#filterbtn').prop('disabled', false).text('Filter');
                    });
                });
            })
        </script>
    @endpush
