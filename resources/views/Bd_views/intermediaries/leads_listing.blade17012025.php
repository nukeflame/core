{{-- @extends('layouts.admincast') --}}
@extends('layouts.app')
{{-- @extends('layouts.intermediaries.base') --}}
@section('header', 'MENU ITEMS')
@section('content')


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

            $('#client-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('leads.get') }}",
                    type: "get"

                },
                columns: [
                    {
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
                ]
            });


            

        })
    </script>

@endpush
