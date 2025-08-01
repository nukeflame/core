@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Bd Lead status</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Bd Lead Status</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Bd Lead Status list</div>
                </div>
                <div class="card-body">
                    {{ html()->form('get', route('lead.status.form'))->id('form_add_lead_status')->open() }}
                    <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_lead_status"><i
                            class='bx bx-plus'></i> Add
                        Lead Status</button>
                    <table class="table text-nowrap table-striped table-hover" id="Lead-status-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Stage</th>
                                <th scope="col">Category Type</th>
                                <th>Edit</th>
                                <th>Delete</th>
                                {{-- <th scope="col">Actions</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#Lead-status-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                ajax: "{{ route('lead.status.data') }}",
                columns: [{
                        data: 'lead_id',
                        name: 'lead_id'
                    },
                    {
                        data: 'status_name',
                        name: 'status_name'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        defaultContent: "<b class='dashes'>_</b>"
                    }, 
                    {
                        data: 'category_type',
                        name: 'category_type',
                        defaultContent: "<b class='dashes'>_</b>"
                    },                    
                    {
                        data: 'edit',
                        name: 'edit',
                        className: 'highlight-index'

                    },
                    {
                        data: 'delete',
                        name: 'delete',
                        className: 'highlight-index'
                    },
                ]
            });

            $(document).on('click', '.update_lead_status', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                window.location.href = "{{ route('lead.status.form') }}" + '?id=' + id;
            });
            $(document).on('click', '.delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this lead status?')) {
                    $.ajax({
                        url: "{{ route('delete.lead.status') }}",
                        method: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert('Lead status deleted successfully');
                            window.location.href = "{{ route('lead.status.info') }}";
                        },
                        error: function(xhr, status, error) {
                            alert('Failed to delete lead status');
                        }
                    });
                }
            });


            $("#add_lead_status").click(function() {
                $("#form_add_lead_status").submit();
            });
        });
    </script>
@endpush
