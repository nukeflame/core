{{-- @extends('layouts.admincast') --}}
@extends('layouts.app')
{{-- @extends('layouts.intermediaries.base') --}}
@section('header', 'MENU ITEMS')
@section('content')
    <style>
        /* Table row styles */
        #client-table tbody tr.highlight-danger {
            background-color: #ffebee !important;
        }

        #client-table tbody tr.highlight-warning {
            background-color: #fff3e0 !important;
        }

        #client-table tbody tr.highlight-info {
            background-color: #e3f2fd !important;
        }

        /* Hover states */
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
        .legend {
            display: flex;
            gap: 20px;
            margin: 10px 0;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .color-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        /* Match legend colors with table colors */
        .color-box.highlight-danger {
            background-color: #ffebee;
            border: 1px solid #ffcdd2;
        }

        .color-box.highlight-warning {
            background-color: #fff3e0;
            border: 1px solid #ffe0b2;
        }

        .color-box.highlight-info {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
        }
    </style>


    <div class="mt-3">
        @if ($message = Session::get('success'))
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="m-2">

        <div class="table-responsive justify-content-between">
            <div class="d-flex justify-content-between">
                <div class="m-2">

                    <a href="{{ route('leads.onboarding') }}" class="btn btn-outline-success" role="button">
                        <i class="fa-solid fa-user"></i>
                        Onboard Prospect
                    </a>

                </div>
                <div class="m-2">
                    <h6></h6>
                </div>
            </div>
        </div>
        <div class="legend">
            <div class="legend-item">
                <span class="color-box highlight-danger"></span>
                <span>Urgent (≤ 14 days)</span>
            </div>
            <div class="legend-item">
                <span class="color-box highlight-warning"></span>
                <span>Upcoming (15-30 days)</span>
            </div>
            <div class="legend-item">
                <span class="color-box highlight-info"></span>
                <span>Planning (31-60 days)</span>
            </div>
        </div>


        <div class="card table-responsive">
            <div class="card-body">

                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#client_listing" role="tab">
                            Prospect Listing
                        </a>
                    </li>
                </ul>

                <div class="tab-content p-3 text-muted">
                    <div class="tab-pane active" id="client_listing">

                        <table class="table table-striped table-hover" id="client-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Insured ID</th>
                                    <th>Insured Name</th>
                                    <th>Client Category</th>
                                    <th>Division</th>
                                    <th>Class of Insurance</th>
                                    <th>Expected Closure Date</th>
                                    <th>Cover Start date</th>
                                    <th>Cover End date</th>
                                    <th>Action</th>

                                </tr>
                            </thead>

                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
@push('script')
    <script>
        window.checkSendToSales = function(prospect) {
            // Show confirmation alert
            Swal.fire({
                title: 'Confirm?',
                text: 'Do you want to send this client to sales?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with the request
                    $.ajax({
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}', // Ensure CSRF token is included
                            'prospect': prospect
                        },
                        url: "{!! route('prospect.add.pipeline') !!}",
                        success: function(response) {
                            if (response.status == 1) {
                                Swal.fire(
                                    'Success!',
                                    'Client has been sent to sales.',
                                    'success'
                                );

                                window.location.href = `/leads_listing`;
                            }
                        },
                        error: function(error) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while sending sales.',
                                'error'
                            );
                        }
                    });
                } else {
                    Swal.fire(
                        'Cancelled',
                        'sending to sales was canceled.',
                        'info'
                    );
                }
            });
        };

        $(document).ready(function() {

            $("#myInput").on("change", function() {
                var value = $(this).val().toLowerCase();

                $("#client-table > tbody > tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            const table = $('#client-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                 'pageLength': 100,
                ajax: {
                    url: "{{ route('leads.get') }}",
                    type: "get"
                },
                columns: [{
                        data: 'opportunity_id',
                        name: 'opportunity_id'
                    },
                    {
                        data: 'insured_name',
                        name: 'insured_name'
                    },
                    {
                        data: 'client_category',
                        name: 'client_category'
                    },
                    {
                        data: 'divisions',
                        name: 'divisions'
                    },
                    {
                        data: 'class',
                        name: 'class'
                    },
                    {
                        data: 'fac_date_offered',
                        name: 'fac_date_offered'
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
                        data: 'action',
                        name: 'action'
                    },
                ],
                order: [
                    [0, 'desc']
                ],
                createdRow: function(row, data, dataIndex) {
                    // Handle date highlighting when each row is created
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    const dateStr = data.effective_date;
                    if (dateStr) {
                        const [year, month, day] = dateStr.split('-').map(Number);
                        const effectiveDate = new Date(year, month - 1, day);

                        if (!isNaN(effectiveDate.getTime())) {
                            const diffTime = effectiveDate - today;
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                            console.log('Row:', dataIndex, 'Days:', diffDays);

                            if (diffDays > 0) {
                                if (diffDays <= 14) {
                                    $(row).addClass('highlight-danger');
                                } else if (diffDays <= 30) {
                                    $(row).addClass('highlight-warning');
                                } else if (diffDays <= 60) {
                                    $(row).addClass('highlight-info');
                                }
                            }
                        }
                    }
                }

            });






        })
    </script>
@endpush
