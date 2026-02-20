@extends('layouts.app')

@push('styles')
    <style>
        .customer-type-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .customer-type-table tbody td {
            vertical-align: middle;
        }

        .customer-type-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .customer-type-table .btn:last-child {
            margin-right: 0;
        }

        .customer-type-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .customer-type-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .customer-type-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .customer-type-modal .modal-body {
            padding: 1.25rem;
        }

        .customer-type-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .customer-type-modal .form-control,
        .customer-type-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .customer-type-modal .form-control:focus,
        .customer-type-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Customer Types</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage customer type setup and status for cover operations.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Customer Types</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Types</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-customer-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Active</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-active-customer-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Inactive</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-inactive-customer-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-customer-types">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Customer Type List</h5>
                        <small class="text-muted">View and manage customer types</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#customerType">
                        <i class='bx bx-plus me-1'></i>Add Customer Type
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover customer-type-table" id="customerType-table"
                            aria-label="Customer type table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type ID</th>
                                    <th>Type Slug</th>
                                    <th>Type Name</th>
                                    <th>Status</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade customer-type-modal" id="customerType" tabindex="-1" aria-labelledby="customerTypeLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="customerTypeLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Customer Type
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_customerType" action="{{ route('customerType.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Customer Type Slug</label>
                                <input type="text" class="form-control" placeholder="Enter customer type slug"
                                    aria-label="Customer Type Slug" id="code" name="code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Customer Type Name</label>
                                <input type="text" class="form-control" placeholder="Enter customer type name"
                                    aria-label="Customer Type" id="type_name" name="type_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_customerType">
                            <i class="fas fa-check me-1"></i> Save Customer Type
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade customer-type-modal" id="edit_cust_typeModal" tabindex="-1"
        aria-labelledby="editCustomerTypeLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editCustomerTypeLabel">
                        <i class="bx bx-edit me-2"></i>Edit Customer Type
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit_customerType" action="{{ route('customerType.edit') }}" method="post">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <input type="hidden" id="ed_type_id" name="ed_type_id">
                            <input type="hidden" id="ed_code" name="ed_code">
                            <div class="col-md-12">
                                <label class="form-label">Customer Type Name</label>
                                <input type="text" class="form-control" placeholder="Enter customer type"
                                    aria-label="Customer Type" id="ed_type_name" name="ed_type_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="edit_customerType_submit">
                            <i class="fas fa-check me-1"></i> Update Customer Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="{{ route('customerType.delete') }}" method="post" id="cust_type_delete">
        {{ csrf_field() }}
        <input type="hidden" name="del_type_id" id="del_type_id">
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#customerType-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('customerType.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'type_id',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'code',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'type_name',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'status',
                        defaultContent: "<b class='dashes'>_</b>",
                        render: function(data) {
                            const value = (data || '').toString().toUpperCase();
                            if (value === 'A') {
                                return '<span class="badge bg-success-transparent text-success">Active</span>';
                            }
                            return '<span class="badge bg-danger-transparent text-danger">Inactive</span>';
                        }
                    },
                    {
                        data: 'action',
                        defaultContent: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                drawCallback: function(settings) {
                    const json = settings.json || {};
                    const rows = this.api().rows({
                        page: 'current'
                    }).data().toArray();
                    const active = rows.filter(row => (row.status || '').toString().toUpperCase() === 'A').length;
                    const totalVisible = rows.length;

                    $('#stat-total-customer-types').text(json.recordsTotal || 0);
                    $('#stat-filtered-customer-types').text(json.recordsFiltered || 0);
                    $('#stat-active-customer-types').text(active);
                    $('#stat-inactive-customer-types').text(Math.max(totalVisible - active, 0));
                }
            });

            $('#customerType-table').on('click', '#edit_cust_type', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const typeId = (rowData.type_id || '').toString().trim();
                const code = (rowData.code || '').toString().trim();
                const typeName = (rowData.type_name || '').toString().trim();
                $('#ed_type_id').val(typeId);
                $('#ed_code').val(code);
                $('#ed_type_name').val(typeName);
                $('#edit_cust_typeModal').modal('show');
            });

            $('#customerType-table').on('click', '#activate_cust_type', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const typeId = (rowData.type_id || '').toString().trim();
                const status = ($(this).val() || 'Update').toString();
                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to ' + status + ' this customer type?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#del_type_id').val(typeId);
                        $('#cust_type_delete').submit();
                    }
                });
            });

            $('#store_customerType').validate({
                rules: {
                    code: {
                        required: true,
                        maxlength: 10
                    },
                    type_name: {
                        required: true,
                        maxlength: 100
                    },
                },
                messages: {
                    code: {
                        required: 'Customer type slug is required',
                        maxlength: 'Customer type slug must be at most 10 characters'
                    },
                    type_name: {
                        required: 'Customer type is required',
                        maxlength: 'Customer type must be at most 100 characters'
                    },
                },
                errorPlacement: function(error, element) {
                    error.addClass('text-danger');
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('error').removeClass('valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error').addClass('valid');
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Save customer type?',
                            text: 'Do you want to submit these changes?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Save',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                        return false;
                    }
                    form.submit();
                }
            });

            $('#edit_customerType').validate({
                rules: {
                    ed_type_name: {
                        required: true,
                        maxlength: 100
                    },
                },
                messages: {
                    ed_type_name: {
                        required: 'Customer type is required',
                        maxlength: 'Customer type must be at most 100 characters'
                    },
                },
                errorPlacement: function(error, element) {
                    error.addClass('text-danger');
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('error').removeClass('valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error').addClass('valid');
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Update customer type?',
                            text: 'Do you want to save these changes?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Update',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                        return false;
                    }
                    form.submit();
                }
            });
        });
    </script>
@endpush
