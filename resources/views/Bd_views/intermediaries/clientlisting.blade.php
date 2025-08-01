@extends('layouts.intermediaries.base')

@section('header', 'MENU ITEMS')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{-- Error Handling --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Whoops!</strong> There were some problems with your input.
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Main Card --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="clients-tab" data-toggle="tab" href="#client_listing"
                                    role="tab">
                                    Clients Listing
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="prospects-tab" data-toggle="tab" href="#prospects" role="tab">
                                    BD Handovers
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            {{-- Clients Listing Tab --}}
                            <div class="tab-pane fade show active" id="client_listing" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="client-table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Client Number</th>
                                                <th>Full Name</th>
                                                <th>Client Type</th>
                                                <th>Industry</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            {{-- Prospects Tab --}}
                            <div class="tab-pane fade" id="prospects" role="tabpanel">
                                <div class="table-responsive">
                                    <table id="prospects_table" class="table table-striped table-hover" style="width:100%">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Division</th>
                                                <th>Class of Insurance</th>
                                                <th>Currency</th>
                                                <th>Income</th>
                                                <th>Effective date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            // Client Table DataTable
            $('#client-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('client_get') }}",
                    type: "get"
                },
                columns: [{
                        data: 'global_customer_id',
                        name: 'global_customer_id'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'client_type_name',
                        name: 'client_type_name'
                    },
                    {
                        data: 'occupation_code',
                        name: 'occupation_code'
                    },
                    {
                        data: 'action'
                    }
                ]
            });

            // Prospects Table DataTable
            $('#prospects_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('prospects.won') }}",
                    type: "get"
                },
                columns: [{
                        data: 'fullname',
                        name: 'fullname'
                    },
                    {
                        data: 'division',
                        name: 'division'
                    },
                    {
                        data: 'currency',
                        name: 'currency'
                    },
                    {
                        data: 'income',
                        name: 'income'
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
                        data: 'prospect_verification_status',
                        name: 'prospect_verification_status',
                        render: function(data, type, row) {
                            if (data == 0) {
                                return '<button class="btn btn-warning"><i class="fa fa-exclamation-circle"></i> Pending Verification CRM</button>';
                            } else if (data == 1) {
                                return '<button class="btn btn-info"><i class="fa fa-thumbs-up"></i>  CR Assign Pending</button>';
                            } else if (data == 2) {
                                return '<button class="btn btn-warning"><i class="fa fa-thumbs-up"></i>  CR Pending Verification</button>';
                            } else if (data == 3) {
                                return '<button class="btn btn-success"><i class="fa fa-thumbs-up"></i> Client Fully Verified</button>';
                            } else {
                                return '<button class="btn btn-secondary">Unknown</button>';
                            }
                        }
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ]
            });

            // Client row click event
            $('#client-table').on('click', 'tbody td', function() {
                var clientno = $(this).closest('tr').find('td:eq(0)').text();
                var service_flag = "N";
                var url =
                    "{{ route('agent.view.client', ['client' => ':client', 'serviceflag' => ':serviceflag']) }}";
                url = url.replace(':client', clientno).replace(':serviceflag', service_flag);
                window.location = url;
            });
        });
    </script>
@endsection
