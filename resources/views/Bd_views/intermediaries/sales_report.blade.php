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
        <div class="card-header">
            <div class="row align-items-center">
                <!-- Pipeline Year -->
                <div class="col-md-2">
                    <label for="pip_year_select"><strong>Pipeline Year</strong></label>
                    <x-SearchableSelect id="pip_year_select" req="" inputLabel="">
                        @foreach ($pipelines as $pip_year)
                            <option value="{{ $pip_year->id }}">{{ $pip_year->year }}</option>
                        @endforeach
                    </x-SearchableSelect>
                </div>

                <!-- Filter Type -->
                <div class="col-md-2">
                    <label for="filter_type"><strong> Generate By </strong></label>
                    <select name="filter_type" id="filter_type" class="form-control form-control-sm">
                        <option value="">-- Select --</option>
                        <option value="category_type" {{ request('filter_type') == 'category_type' ? 'selected' : '' }}>
                            Category Type
                        </option>
                        <option value="lead_status" {{ request('filter_type') == 'lead_status' ? 'selected' : '' }}>
                            Status
                        </option>

                        <option value="class" {{ request('filter_type') == 'class' ? 'selected' : '' }}>Class</option>
                        <option value="class_group" {{ request('filter_type') == 'class_group' ? 'selected' : '' }}>Class
                            Group</option>
                        <option value="customer" {{ request('filter_type') == 'customer' ? 'selected' : '' }}>Cedant
                        </option>
                    </select>
                </div>

                <!-- Select Option -->
                <div class="col-md-2">
                    <label for="selected_id"><strong>Select Option</strong></label>
                    <x-SearchableSelect id="selected_id" req="" inputLabel="">
                        <option value="">-- Select an Option --</option>
                    </x-SearchableSelect>
                </div>
                <div class="col-md-2">
                    <label for="start_date"><strong>Date</strong></label>
                    <input type="date" id="start_date" name="start_date" class="form-control form-control-sm">
                </div>

                <!-- Month -->
                <div class="col-md-2">
                    <label for="month"><strong>Month</strong></label>
                    <select name="month" id="month" class="form-control form-control-sm">
                        <option value="">-- Select Month --</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="col-md-2 mt-4">
                    <button id="filterbtn" type="button" class="btn btn-primary btn-sm">Filter</button>
                </div>
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
                                        <th>Id</th>
                                        <th>Cedant</th>
                                        <th>Insured name</th>
                                        <th>Division</th>
                                        <th>Business class</th>
                                        <th>Currency</th>
                                        <th>Sum Insured(100%)</th>
                                        <th>Premium(100%)</th>
                                        <th>Revenue</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Turn Arround Time</th>
                                        <th>Reverted To Pipeline</th>
                                        <th>Status</th>
                                        <th>Sales Entry Date</th>
                                        <th>Stage Entry Date</th>
                                        <th>Stage Duration</th>

                                        <th>Category</th>






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
                let selectedName = "";

                let year = $('#pip_year_select').val();

                $("#myInput").on("change", function() {
                    var value = $(this).val().toLowerCase();

                    $("#client-table > tbody > tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });

                $('#filterbtn').on('click', function() {
                    $(this).prop('disabled', true).text('Loading...');
                    table.ajax.url(buildAjaxUrl()).load(function() {
                        $('#filterbtn').prop('disabled', false).text('Filter');
                    });
                    // table.ajax.url("{{ route('sales.report.data') }}?year=" + year).load();
                });

                function buildAjaxUrl() {
                    const filterType = $('#filter_type').val();
                    const nameAttributes = {
                        'category_type': 'category_type',
                        'lead_status': 'lead_status',
                        'class': 'business_class',
                        'class_group': 'class_group',
                        'customer': 'cedant'
                    };
                    const paramName = nameAttributes[filterType] || 'selected_id';

                    const params = {
                        year: $('#pip_year_select').val(),
                        start_date: $('#start_date').val(),
                        month: $('#month').val()
                    };

                    // Only add the selected_id value if a filter type is selected
                    if (filterType) {
                        params[paramName] = $('#selected_id').val();
                    }

                    // Include lead_status_category only if filter_type is lead_status
                    if (filterType === 'lead_status') {
                        params.lead_status_category = $('#lead_status_category').val();
                    }

                    return "{{ route('sales.report.data') }}?" + $.param(params);
                }

                function firstUpper(text) {
                    if (!text) return '';
                    return text
                        .toLowerCase()
                        .replace(/\b\w/g, char => char.toUpperCase());
                }

                function formatNumber(value) {
                    if (value === null || value === undefined) return '';
                    let num = parseFloat(value.toString().replace(/,/g, ''));
                    if (isNaN(num)) return value;
                    return num.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }


                function stripHtml(html) {
                    return $("<div>").html(html).text();
                }


                const table = $('#client-table').DataTable({
                    dom: 'Bfrtip',
                    buttons: [{
                        text: '<i class="fa fa-file-excel-o"></i> Generate Report',
                        className: 'btn btn-success',
                        titleAttr: 'Excel',
                        action: function(e, dt, button, config) {
                            window.location.href = buildAjaxUrl() + "&export=true";
                        }
                    }],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('sales.report.data') }}?year=" + year,
                        type: "GET"
                    },
                    columns: [{
                            data: 'opportunity_id',
                            name: 'opp_id',
                            className: 'highlight-index'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name',
                            render: firstUpper
                        },
                        {
                            data: 'insured_name',
                            name: 'insured_name',
                            render: firstUpper
                        },
                        {
                            data: 'division_name',
                            name: 'division_name',
                            render: firstUpper
                        },
                        {
                            data: 'business_class',
                            name: 'business_class',
                            render: firstUpper
                        },
                        {
                            data: 'currency_code',
                            name: 'currency_code'
                        },
                        {
                            data: 'effective_sum_insured',
                            name: 'effective_sum_insured',
                            render: function(data) {
                                if (!data) return '';
                                return Number(data).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        },
                        {
                            data: 'cedant_premium',
                            name: 'cedant_premium'
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
                            data: 'effective_date',
                            name: 'effective_date'
                        },
                        {
                            data: 'closing_date',
                            name: 'closing_date'
                        },
                        {
                            data: 'turnaround_time',
                            name: 'turnaround_time'
                        },
                        {
                            data: 'reverted_to_pipeline',
                            name: 'reverted_to_pipeline'
                        },
                        {
                            data: 'stage',
                            name: 'stage'
                        },
                        {
                            data: 'sales_entry_date',
                            name: 'sales_entry_date'

                        },
                        {
                            data: 'stage_updated_at',
                            name: 'stage_updated_at'
                        },
                        {
                            data: 'current_stage_duration',
                            name: 'current_stage_duration'

                        },
                        {
                            data: 'action1',
                            name: 'action1'
                        },
                    ],
                    order: [
                        [6, 'asc']
                    ]
                });
                let categoryDropdown = `
                <div class="col-md-2" id="category_wrapper" style="display: none;">
                    <label for="category_select"><strong>Select Category</strong></label>
                    <select name="lead_status_category" id="lead_status_category" class="form-control form-control-sm" required>
                        <option value="">-- Select Category --</option>
                        <option value="1">Quotation</option>
                        <option value="2">Facultative Offer</option>
                    </select>
                </div>`;

                // Append category dropdown but keep it hidden
                $('#filter_type').parent().after(categoryDropdown);

                $('#filter_type').on('change', function() {
                    let filterType = $(this).val();
                    let selectedDropdown = $('#selected_id');
                    let label = $('label[for="selected_id"]');

                    // Define dynamic names and labels

                    let nameAttributes = {
                        'category_type': 'category_type',
                        'lead_status': 'lead_status',
                        'class': 'business_class',
                        'class_group': 'class_group',
                        'customer': 'cedant'
                    };

                    let labelNames = {
                        'category_type': 'Select Category type:',
                        'leadStatus': 'Select Lead Status',
                        'class': 'Select Business Class:',
                        'class_group': 'Select Class Group:',
                        'customer': 'Select Cedant:'
                    };
                    selectedName = nameAttributes[filterType] || 'selected_id';


                    // Update label and name dynamically
                    label.text(labelNames[filterType] || 'Select Option:');
                    selectedDropdown.attr('name', nameAttributes[filterType] || 'selected_id');
                    if (filterType === 'lead_status') {
                        $('#category_wrapper').show();
                    } else {
                        $('#category_wrapper').hide();
                        $('#lead_status_category').val('');
                    }
                    fetchFilterData(filterType);
                });
                $('#lead_status_category').on('change', function() {
                    let filterType = $('#filter_type').val();
                    let lead_status_category = $(this).val();


                    if (filterType === 'lead_status' && lead_status_category) {
                        fetchFilterData(filterType, lead_status_category);
                    }

                });


                function fetchFilterData(filterType, lead_status_category = null) {
                    let selectedDropdown = $('#selected_id');
                    selectedDropdown.empty().append('<option value="">-- Loading... --</option>');

                    if (filterType) {
                        $.ajax({
                            url: "{{ route('sales_report_filter') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                filter_type: filterType,
                                lead_status_category: lead_status_category
                            },
                            success: function(response) {
                                selectedDropdown.empty().append(
                                    '<option value="">-- Select an Option --</option>'
                                );

                                if (filterType === 'category_type') {
                                    response.category_type.forEach(item => {
                                        selectedDropdown.append(
                                            `<option value="1">Quotation</option>
                                            <option value="2">Facultative Offer</option>`
                                        );
                                    });
                                } else if (filterType === 'lead_status') {
                                    response.lead_status.forEach(item => {
                                        selectedDropdown.append(
                                            `<option value="${item.id}">${item.status_name}</option>`
                                        );
                                    });
                                } else if (filterType === 'class') {
                                    response.classes.forEach(item => {
                                        selectedDropdown.append(
                                            `<option value="${item.id}">${item.class_name}</option>`
                                        );
                                    });
                                } else if (filterType === 'class_group') {
                                    response.classGroups.forEach(item => {
                                        selectedDropdown.append(
                                            `<option value="${item.id}">${item.group_name}</option>`
                                        );
                                    });
                                } else if (filterType === 'customer') {
                                    response.customers.forEach(item => {
                                        selectedDropdown.append(
                                            `<option value="${item.id}">${item.name}</option>`
                                        );
                                    });
                                }
                            },
                            error: function(error) {
                                console.log("Error fetching data:", error);
                            }
                        });
                    } else {
                        selectedDropdown.empty().append(
                            '<option value="">-- Select an Option --</option>');
                    }
                }



            })
        </script>
    @endpush
