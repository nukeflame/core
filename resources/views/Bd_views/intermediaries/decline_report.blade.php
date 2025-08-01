{{-- @extends('layouts.admincast') --}}
@extends('layouts.app')
{{-- @extends('layouts.intermediaries.base') --}}
@section('header', 'MENU ITEMS')
@section('content')
    <style>
        #client-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
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
    </style>.
    <div class="card mt-3 border">
        <div class="card-header ">
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
        </div>

        <div class="m-2">

            <div class="card table-responsive">
                <div class="card-body">

                    <div class="tab-content p-3 text-muted">
                        <div class="tab-pane active" id="client_listing">

                            <table class="table table-striped table-hover" id="client-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Pipeline ID</th>
                                        <th>Customer</th>
                                        <th>Reason</th>
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
                //     alert('clicked');
                // });
                let year = $('#pip_year_select').val(); 

                $('#filterbtn').on('click', function() {
                    year = $('#pip_year_select').val(); // Update year on button click
                    table.ajax.url("{{ route('decline.report.data') }}?year=" + year).load();
                });


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
                        text: '<i class="fa fa-file-excel-o"></i> Generate Excel Report',
                        className: 'btn btn-success',
                        titleAttr: 'Export to Excel',
                        action: function(e, dt, button, config) {
                            const year = $('#pip_year_select').val();
                            if (!year || isNaN(year)) {
                                toastr.error('Please select a valid year.');
                                return;
                            }

                            // Trigger backend Excel export
                            window.location.href =
                                "{{ route('decline.report.data') }}?export=true&year=" +
                                encodeURIComponent(year);
                        }
                    }],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('decline.report.data') }}?year=" + year,
                        type: "GET"
                    },
                    columns: [{
                            data: 'opportunity_id',
                            name: 'opportunity_id'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name',
                            render: function(data) {
                                return firstUpper(data);
                            }
                        },
                        {
                            data: 'reason',
                            name: 'reason',
                        },
                    ],
                    order: [
                        [2, 'asc']
                    ]

                });
            })
        </script>
    @endpush
