@extends('layouts.app')

@section('content')
    <style type="text/css">
        #coverlist,
        #claimlist,
        #statement tbody tr {
            cursor: pointer;
        }

        #coverlist tbody tr:hover {
            background-color: rgb(77, 77, 157);
        }

        #claimlist tbody tr:hover {
            background-color: rgb(158, 195, 86);
        }

        #statement tbody tr:hover {
            background-color: rgb(104, 220, 148);
        }
    </style>

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Customer</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::ucfirst(strtolower($customer->name)) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="p-2 card">
        <div class="row row-cols-12 p-2">
            <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                onclick="processCover()">
                <span></span>New Cover</button>
            {{-- <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                onclick="processQuote()">
                <span></span>New Quote</button> --}}
            <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                onclick="processClaim()">
                <span></span>Claim Intimation / Notification</button>
            {{-- <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"> <span
                    class="fa fa-pencil-square-o" onclick="processDebtors()"></span>Debtors Statement</button> --}}
        </div>
        <div class="row row-cols-12 mx-0">
            <div class="ml-0 border col">
                <div class="card-body mt-3">
                    {{ html()->form('POST', '/cover/cover-form')->id('new_cover_form')->open() }}
                    <input type="text" name="customer_id" id="customer_id" value="{{ $customer->customer_id }} " hidden>
                    <input type="text" name="trans_type" id="trans_type" hidden>
                    {{ csrf_field() }}
                    {{ html()->form()->close() }}
                    <form action="{{ route('claim.notification.form') }}" id="new_claim_form">
                        @csrf
                        <input type="text" name="customer_id" id="customer_id" value="{{ $customer->customer_id }} "
                            hidden>
                    </form>
                    <div class="row bg-light p-2">
                        <div class="col-md-3">
                            <strong class="color-blk">Customer Name</strong>
                        </div>
                        <div class="col-md-2">
                            <strong class="text-info">{{ $customer->name }}</strong>
                        </div>
                        <div class="col-md-3">
                            <strong>Customer Postal Address</strong>
                        </div>
                        <div class="col-md-4">
                            <span
                                class="text-info">{{ $customer->postal_address . ', ' . $customer->postal_town . ',  ' . $customer->city }}</span>
                        </div>
                    </div>
                    <div class="row mb-1  p-2">
                        <div class="col-md-3">
                            <strong>Customer Email</strong>
                        </div>
                        <div class="col-md-2">
                            <span class="text-info">{{ $customer->email }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Customer Physical Address</strong>
                        </div>
                        <div class="col-md-2">
                            <span class="text-info">{{ $customer->city . ',' }} {{ $country?->country_name ?? '' }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row-cols-12 mx-0">
        <div class="card mb-2 custom-card border col">
            <div class="card-body pt-0">
                <nav>
                    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                        id="nav-tab" role="tablist">
                        {{-- <button class="nav-link active" id="nav-quotelist-tab" data-bs-toggle="tab"
                            data-bs-target="#quotelist-tab" type="button" role="tab" aria-selected="true"><i
                                class="bx bx-file me-1 align-middle"></i>Quote List</button> --}}
                        <button class="nav-link active" id="nav-coverlist-tab" data-bs-toggle="tab"
                            data-bs-target="#coverlist-tab" type="button" role="tab" aria-selected="false"
                            tabindex="-1"><i class="bx bx-file me-1 align-middle"></i>Cover List</button>
                        <button class="nav-link" id="nav-claimlist-tab" data-bs-toggle="tab" data-bs-target="#claimlist-tab"
                            type="button" role="tab" aria-selected="false" tabindex="-1"><i
                                class="bx bx-medal me-1 align-middle"></i>Claim List</button>
                        {{-- <button class="nav-link" id="nav-statement-tab" data-bs-toggle="tab" data-bs-target="#statement-tab"
                            type="button" role="tab" aria-selected="false" tabindex="-1"><i
                                class="bx bx-file-blank me-1 align-middle"></i>Statement</button> --}}
                    </div>
                </nav>
                <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                    {{-- <div class="tab-pane active show" id="quotelist-tab" role="tabpanel" aria-labelledby="nav-quotelist-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                {{ html()->form('POST', '/cover/endorse_functions')->id('form_quote_datatable')->open() }}
                                <input type="text" name="cover_no" id="cover_no" hidden>
                                <input type="text" name="customer_id" id="customer_id"
                                    value="{{ $customer->customer_id }} " hidden>
                                <table id="quotes-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Quote No.</th>
                                            <th scope="col">Quote Type</th>
                                            <th scope="col">Class Description</th>
                                            <th scope="col">Expiry</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                {{ csrf_field() }}
                                {{ html()->form()->close() }}
                            </div>
                        </div>
                    </div> --}}
                    <div class="tab-pane active show" id="coverlist-tab" role="tabpanel" aria-labelledby="nav-coverlist-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                {{ html()->form('POST', '/cover/endorsements_list')->id('form_cover_datatable')->open() }}
                                <input type="text" name="cover_no" id="cov_cover_no" hidden>
                                <input type="text" name="customer_id" id="customer_id"
                                    value="{{ $customer->customer_id }} " hidden>
                                <table id="coverlist-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Cover No.</th>
                                            <th scope="col">Cover Type</th>
                                            <th scope="col">Class Description</th>
                                            <th scope="col">Expiry</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                {{ csrf_field() }}
                                {{ html()->form()->close() }}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="claimlist-tab" role="tabpanel" aria-labelledby="nav-claimlist-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                {{ html()->form('POST', '/claim/claim_detail')->id('form_claim_datatable')->open() }}
                                <input type="text" name="claim_no" id="clm_claim_no" hidden>
                                <input type="text" name="customer_id" id="customer_id"
                                    value="{{ $customer->customer_id }} " hidden>
                                <table id="claimlist-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%!important;">
                                    <thead>
                                        <tr>
                                            <th scope="col">Claim No.</th>
                                            <th scope="col">Cover No.</th>
                                            <th scope="col">Endorsement No.</th>
                                            <th scope="col">Bus Type</th>
                                            <th scope="col">Class</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>

                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                {{ csrf_field() }}
                                {{ html()->form()->close() }}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="statement-tab" role="tabpanel" aria-labelledby="nav-statement-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                {{ html()->form('POST', '/cover/statement')->id('form_statement_datatable')->open() }}
                                <input type="text" name="cover_no" id="st_cover_no" hidden>
                                <input type="text" name="customer_id" id="customer_id"
                                    value="{{ $customer->customer_id }} " hidden>
                                <table id="statement-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Doc Type</th>
                                            <th scope="col">Cover No.</th>
                                            <th scope="col">Endorsement No.</th>
                                            <th scope="col">Reference</th>
                                            <th scope="col">Entry Type</th>
                                            <th scope="col">Net Amount</th>
                                            <th scope="col">Date Created</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                {{ csrf_field() }}
                                {{ html()->form()->close() }}
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
            var customer_id = $("#customer_id").val();
            // cover table
            $('#coverlist-table').DataTable({
                order: [
                    [4, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: '{!! route('cover.datatable') !!}',
                    data: function(d) {
                        d.customer_id = customer_id;
                    }
                },
                columns: [{
                        data: 'cover_no',
                        searchable: true
                    },
                    {
                        data: 'cover_type',
                        searchable: true
                    },
                    {
                        data: 'class_desc',
                        searchable: false,
                        class: "highlight-description"
                    },
                    {
                        data: 'cover_to',
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        searchable: true,
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return formatDate(data);
                            }
                            return data;
                        }
                    },
                    {
                        data: 'status',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        sortable: false
                    },
                ],
            });

            // claim no.
            $('#claimlist-table').DataTable({
                order: [
                    [5, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: '{!! route('claim.datatable') !!}',
                    data: function(d) {
                        d.customer_id = customer_id;
                    }
                },
                columns: [{
                        data: 'claim_no',
                        searchable: true
                    },
                    {
                        data: 'cover_no',
                        searchable: true
                    },
                    {
                        data: 'endorsement_no',
                        searchable: true
                    },
                    {
                        data: 'type_of_bus',
                        searchable: false,
                        class: 'highlight-2view-point'

                    },
                    {
                        data: 'class_desc',
                        searchable: false,
                        class: 'highlight-description'
                    },
                    {
                        data: 'created_at',
                        searchable: true,
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return formatDate(data);
                            }
                            return data;
                        }
                    },
                    {
                        data: 'status',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        sortable: false
                    },
                ]
            });

            // statement
            $('#statement-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,

                ajax: {
                    url: '{!! route('statement.datatable') !!}',
                    data: function(d) {
                        d.customer_id = customer_id;
                    }
                },
                columns: [

                    {
                        data: 'doc_type',
                        searchable: true
                    },
                    {
                        data: 'cover_no',
                        searchable: true
                    },
                    {
                        data: 'endorsement_no',
                        searchable: true
                    },
                    {
                        data: 'reference',
                        searchable: true
                    },
                    {
                        data: 'entry_type_descr',
                        searchable: true
                    },
                    {
                        data: 'local_nett_amount',
                        searchable: true
                    },
                    // {
                    //   data: 'unallocated_amount',
                    //   searchable: true
                    // },
                    {
                        data: 'created_date',
                        searchable: true
                    },
                ]
            });

            $('#coverlist-table').on('click', 'tbody tr', function() {
                var cover_no = $(this).closest('tr').find('td:eq(0)').text();
                if (cover_no != '') {
                    $("#cov_cover_no").val(cover_no);
                    $("#form_cover_datatable").submit();
                }
            });

            $('#view-coverlist-table').on('click', function(e) {
                e.preventDefault();
                // var cover_no = $(this).closest('tr').find('td:eq(0)').text();
                // if (cover_no != '') {
                //     $("#cov_cover_no").val(cover_no);
                //     $("#form_cover_datatable").submit();
                // }
            });

            $('#claimlist-table').on('click', 'tbody tr', function() {
                var claim_no = $(this).closest('tr').find('td:eq(0)').text();
                if (claim_no != '') {
                    $("#clm_claim_no").val(claim_no);
                    $("#form_claim_datatable").submit();
                }
            });
        });

        function processCover() {
            $("#trans_type").val('NEW');
            $("#new_cover_form").submit();
        }

        function processClaim() {
            $("#new_claim_form").submit();
        }

        function formatDate(dateString) {
            const options = {
                month: "short",
                day: "2-digit",
                year: "numeric"
            }; // 'M d, Y' format
            const date = new Date(dateString);
            return date.toLocaleDateString("en-US", options);
        }
    </script>
@endpush
