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
        <h1 class="page-title fw-semibold fs-18 mb-0">BD Treaty Operation Checklist</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Bd Treaty Operation Checklist</a></li>
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
                    <div class="card-title">BD Treaty Operation Checklist</div>
                </div>
                <div class="card-body">
                    {{ html()->form('get', route('operationchecklist.form'))->id('form_add_doc_type')->open() }}
                    <button type="button" class="btn btn-primary btn-sm custom-btn" id="add_doc_type"><i
                            class='bx bx-plus'></i> Add
                        Bd document</button>
                    {{ html()->form()->close() }}
                    <table class="table text-nowrap table-striped table-hover" id="stage-document-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
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
            $('#stage-document-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                ajax: "{{ route('operationchecklist.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
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

            $(document).on('click', '.update_doc_type', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                window.location.href = "{{ route('operationchecklist.form') }}" + '?id=' + id;
            });
            $(document).on('click', '.delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this doc type?')) {
                    $.ajax({
                        url: "{{ route('delete.operationchecklist') }}",
                        method: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success('doc type deleted successfully');
                            $('#stage-document-table').DataTable().ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Failed to delete doc type');
                             $('#stage-document-table').DataTable().ajax.reload();
                        }
                    });
                }
            });


            $("#add_doc_type").click(function() {
                $("#form_add_doc_type").submit();
            });
        });
    </script>
@endpush
