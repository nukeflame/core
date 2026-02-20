@extends('layouts.app')

@push('styles')
    <style>
        .treaty-type-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .treaty-type-table tbody td {
            vertical-align: middle;
        }

        .treaty-type-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .treaty-type-table .btn:last-child {
            margin-right: 0;
        }

        .treaty-type-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .treaty-type-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .treaty-type-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .treaty-type-modal .modal-body {
            padding: 1.25rem;
        }

        .treaty-type-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .treaty-type-modal .form-control,
        .treaty-type-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .treaty-type-modal .form-control:focus,
        .treaty-type-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Cover Treaty Types</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage treaty type setup and status for cover operations.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Treaty Types</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Treaty Types</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-treaty-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Active</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-active-treaty-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Inactive</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-inactive-treaty-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-treaty-types">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Treaty Type List</h5>
                        <small class="text-muted">View and manage treaty type records</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#treatyType">
                        <i class='bx bx-plus me-1'></i>Add Treaty Type
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover treaty-type-table" id="treatyType-table"
                            aria-label="Treaty type table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type of Business</th>
                                    <th>Treaty Code</th>
                                    <th>Treaty Name</th>
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

    <div class="modal fade treaty-type-modal" id="treatyType" tabindex="-1" aria-labelledby="treatyTypeLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="treatyTypeLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Treaty Type
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_treatyType" action="{{ route('treatyType.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Type of Business</label>
                                <select class="form-select" name="type_of_bus" id="type_of_bus">
                                    <option value="">Choose Type Of Business</option>
                                    <option value="TPR">Treaty Proportional</option>
                                    <option value="TNP">Treaty Non Proportional</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Treaty Code</label>
                                <input type="text" class="form-control" placeholder="Enter treaty code"
                                    aria-label="Treaty code" id="treaty_code" name="treaty_code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Treaty Name</label>
                                <input type="text" class="form-control" placeholder="Enter treaty name"
                                    aria-label="Treaty name" id="treaty_name" name="treaty_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_treatyType">
                            <i class="fas fa-check me-1"></i> Save Treaty Type
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade treaty-type-modal" id="edit_treatyTypeModal" tabindex="-1"
        aria-labelledby="editTreatyTypeLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editTreatyTypeLabel">
                        <i class="bx bx-edit me-2"></i>Edit Treaty Type
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_treatyType" action="{{ route('treatyType.edit') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <input type="hidden" id="ed_treaty_code" name="ed_treaty_code">
                            <div class="col-md-12">
                                <label class="form-label">Treaty Name</label>
                                <input type="text" class="form-control" placeholder="Enter treaty name"
                                    aria-label="Treaty name" id="ed_treaty_name" name="ed_treaty_name">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm" id="edit_treatyType_submit">
                                    <i class="fas fa-check me-1"></i> Update Treaty Type
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('treatyType.delete') }}" method="post" id="del_treatyType_form">
        {{ csrf_field() }}
        <input type="hidden" name="del_treaty_code" id="del_treaty_code">
        <input type="hidden" name="mode" id="treaty_type_mode" value="toggle">
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#treatyType-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, 'asc']
                ],
                ajax: "{{ route('treatyType.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'type_of_bus',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'treaty_code',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'treaty_name',
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
                    const active = rows.filter(row => (row.status || '').toString().toUpperCase() ===
                            'A')
                        .length;
                    const totalVisible = rows.length;

                    $('#stat-total-treaty-types').text(json.recordsTotal || 0);
                    $('#stat-filtered-treaty-types').text(json.recordsFiltered || 0);
                    $('#stat-active-treaty-types').text(active);
                    $('#stat-inactive-treaty-types').text(Math.max(totalVisible - active, 0));
                }
            });

            $('#treatyType-table').on('click', '#edit_treatyType', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const treatyCode = (rowData.treaty_code || '').toString().trim();
                const treatyName = (rowData.treaty_name || '').toString().trim();
                $('#ed_treaty_code').val(treatyCode);
                $('#ed_treaty_name').val(treatyName);
                $('#edit_treatyTypeModal').modal('show');
            });

            $('#treatyType-table').on('click', '#activate_treatyType', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const treatyCode = (rowData.treaty_code || '').toString().trim();
                const actionLabel = ($(this).val() || 'update').toString();

                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to ' + actionLabel + ' this treaty type?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#del_treaty_code').val(treatyCode);
                        $('#treaty_type_mode').val('toggle');
                        $('#del_treatyType_form').submit();
                    }
                });
            });

            $('#treatyType-table').on('click', '#delete_treatyType', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const treatyCode = (rowData.treaty_code || '').toString().trim();

                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to permanently delete this treaty type?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#del_treaty_code').val(treatyCode);
                        $('#treaty_type_mode').val('delete');
                        $('#del_treatyType_form').submit();
                    }
                });
            });

            $('#store_treatyType').validate({
                rules: {
                    type_of_bus: {
                        required: true
                    },
                    treaty_code: {
                        required: true,
                        maxlength: 20
                    },
                    treaty_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    type_of_bus: {
                        required: 'Type of business is required'
                    },
                    treaty_code: {
                        required: 'Treaty code is required',
                        maxlength: 'Treaty code must be at most 20 characters'
                    },
                    treaty_name: {
                        required: 'Treaty name is required',
                        maxlength: 'Treaty name must be at most 80 characters'
                    }
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
                            title: 'Save treaty type?',
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

            $('#edit_treatyType').validate({
                rules: {
                    ed_treaty_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    ed_treaty_name: {
                        required: 'Treaty name is required',
                        maxlength: 'Treaty name must be at most 80 characters'
                    }
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
                            title: 'Update treaty type?',
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
