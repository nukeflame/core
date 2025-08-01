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
        <h1 class="page-title fw-semibold fs-18 mb-0">BD Schedule Headers</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Bd Schedules</a></li>
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
                    <div class="card-title">Bd Schedule Headers list</div>
                </div>
                <div class="card-body">
                    {{ html()->form('get', route('schedule.header.form'))->id('form_add_schedule_header')->open() }}
                    <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_schedule_header"><i
                            class='bx bx-plus'></i> Add
                        schedule</button>
                    {{ html()->form()->close() }}
                    <table class="table text-nowrap table-striped table-hover" id="schedule-header-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Business Type</th>
                                <th scope="col">Position</th>
                                <th scope="col">Amount Field</th>
                                <th scope="col">Sum Insured Type</th>
                                <th scope="col">Data Determinant</th>
                                <th scope="col">Class</th>
                                <th scope="col">Class Group</th>
                                <th>Edit</th>
                                <th>Delete</th>
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
            $('#schedule-header-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                ajax: "{{ route('bd.schedule.header.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'bus_type',
                        name: 'bus_type',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'position',
                        name: 'position',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'amount_field',
                        name: 'amount_field',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'sum_insured_type',
                        data: 'sum_insured_type',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'data_determinant',
                        name: 'data_determinant',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'class',
                        name: 'class',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'class_group',
                        name: 'class_group',
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
                    // {
                    //     data: 'process',
                    //     searchable: false,
                    //     defaultContent: "<b style=''>_</b>"
                    // },
                ]
            });

            $(document).on('click', '.update_schedule', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                window.location.href = "{{ route('schedule.header.form') }}" + '?id=' + id;
            });
            $(document).on('click', '.delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this schedule header?')) {
                    $.ajax({
                        url: "{{ route('delete.schedule.header') }}",
                        method: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success('Schedule header deleted successfully');
                            $('#schedule-header-table').DataTable().ajax.reload();

                        },
                        error: function(xhr, status, error) {
                            toastr.error('Failed to delete schedule header');
                            $('#schedule-header-table').DataTable().ajax.reload();

                        }
                    });
                }
            });


            $("#add_schedule_header").click(function() {
                $("#form_add_schedule_header").submit();
            });
        });
    </script>
@endpush
