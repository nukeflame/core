@extends('layouts.app')

@push('styles')
    <style>
        .reins-class-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .reins-class-table tbody td {
            vertical-align: middle;
        }

        .reins-class-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .reins-class-table .btn:last-child {
            margin-right: 0;
        }

        .reins-class-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .reins-class-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .reins-class-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .reins-class-modal .modal-body {
            padding: 1.25rem;
        }

        .reins-class-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .reins-class-modal .form-control,
        .reins-class-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .reins-class-modal .form-control:focus,
        .reins-class-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Reinsurance Classes</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage reinsurance class setup, grouping and status for cover operations.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Reinsurance Classes</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Classes</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-reins-classes">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Active</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-active-reins-classes">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Inactive</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-inactive-reins-classes">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-reins-classes">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Reinsurance Class List</h5>
                        <small class="text-muted">View and manage reinsurance classes</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#reinsClass">
                        <i class='bx bx-plus me-1'></i>Add Reinsurance Class
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover reins-class-table" id="reinsClass-table"
                            aria-label="Reinsurance class table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Group Code</th>
                                    <th>Class Code</th>
                                    <th>Class Description</th>
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

    <div class="modal fade reins-class-modal" id="reinsClass" tabindex="-1" aria-labelledby="reinsClassLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="reinsClassLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Reinsurance Class
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_reinsClass" action="{{ route('reinsClass.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Class Group</label>
                                <select id="class_group" name="class_group" class="form-select" required>
                                    <option selected value="">Choose Class Group</option>
                                    @foreach ($classGroups as $classGroup)
                                        <option value="{{ $classGroup->group_code }}">{{ $classGroup->group_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Reins Class Code</label>
                                <input type="text" class="form-control" placeholder="Enter reins class code"
                                    aria-label="Reins class code" id="class_code" name="class_code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Reins Class Name</label>
                                <input type="text" class="form-control" placeholder="Enter reins class name"
                                    aria-label="Reins class name" id="class_name" name="class_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_reinsClass">
                            <i class="fas fa-check me-1"></i> Save Reins Class
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade reins-class-modal" id="edit_reinsClassModal" tabindex="-1"
        aria-labelledby="editReinsClassLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editReinsClassLabel">
                        <i class="bx bx-edit me-2"></i>Edit Reinsurance Class
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit_reinsClass" action="{{ route('reinsClass.edit') }}" method="post">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <input type="hidden" id="ed_class_code" name="ed_class_code">

                            <div class="col-md-12">
                                <label class="form-label">Class Group</label>
                                <select id="ed_class_group" name="ed_class_group" class="form-select" required>
                                    <option value="">Choose Class Group</option>
                                    @foreach ($classGroups as $classGroup)
                                        <option value="{{ $classGroup->group_code }}">{{ $classGroup->group_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Reins Class Name</label>
                                <input type="text" class="form-control" placeholder="Enter reins class name"
                                    aria-label="Reins class name" id="ed_class_name" name="ed_class_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="edit_reinsClass_submit">
                            <i class="fas fa-check me-1"></i> Update Reins Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="{{ route('reinsClass.delete') }}" method="post" id="del_reinsClass_form">
        {{ csrf_field() }}
        <input type="hidden" name="del_class_code" id="del_class_code">
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#reinsClass').on('shown.bs.modal', function() {
                if (!$('#class_group').hasClass('select2-hidden-accessible')) {
                    $('#class_group').select2({
                        dropdownParent: $('#reinsClass'),
                        width: '100%'
                    });
                }
            });

            $('#edit_reinsClassModal').on('shown.bs.modal', function() {
                if (!$('#ed_class_group').hasClass('select2-hidden-accessible')) {
                    $('#ed_class_group').select2({
                        dropdownParent: $('#edit_reinsClassModal'),
                        width: '100%'
                    });
                }
            });

            const table = $('#reinsClass-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, 'asc']
                ],
                ajax: "{{ route('reinsClass.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'group_name',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'class_code',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'class_name',
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

                    $('#stat-total-reins-classes').text(json.recordsTotal || 0);
                    $('#stat-filtered-reins-classes').text(json.recordsFiltered || 0);
                    $('#stat-active-reins-classes').text(active);
                    $('#stat-inactive-reins-classes').text(Math.max(totalVisible - active, 0));
                }
            });

            $('#reinsClass-table').on('click', '#edit_reinsClass', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const classCode = (rowData.class_code || '').toString().trim();
                const className = (rowData.class_name || '').toString().trim();
                const classGroupCode = (rowData.class_group || '').toString().trim();

                $('#ed_class_code').val(classCode);
                $('#ed_class_name').val(className);
                $('#ed_class_group').val(classGroupCode).trigger('change');
                $('#edit_reinsClassModal').modal('show');
            });

            $('#reinsClass-table').on('click', '#activate_reinsClass', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const classCode = (rowData.class_code || '').toString().trim();
                const status = ($(this).val() || 'Update').toString();

                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to ' + status + ' this reins class?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#del_class_code').val(classCode);
                        $('#del_reinsClass_form').submit();
                    }
                });
            });

            $('#store_reinsClass').validate({
                rules: {
                    class_group: {
                        required: true
                    },
                    class_code: {
                        required: true,
                        maxlength: 20
                    },
                    class_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    class_group: {
                        required: 'Class group is required'
                    },
                    class_code: {
                        required: 'Reins class code is required',
                        maxlength: 'Reins class code must be at most 20 characters'
                    },
                    class_name: {
                        required: 'Reins class name is required',
                        maxlength: 'Reins class name must be at most 80 characters'
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
                            title: 'Save reins class?',
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

            $('#edit_reinsClass').validate({
                rules: {
                    ed_class_group: {
                        required: true
                    },
                    ed_class_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    ed_class_group: {
                        required: 'Class group is required'
                    },
                    ed_class_name: {
                        required: 'Reins class name is required',
                        maxlength: 'Reins class name must be at most 80 characters'
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
                            title: 'Update reins class?',
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
