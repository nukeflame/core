@extends('layouts.app')

@push('styles')
    <style>
        .class-group-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .class-group-table tbody td {
            vertical-align: middle;
        }

        .class-group-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .class-group-table .btn:last-child {
            margin-right: 0;
        }

        .class-group-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .class-group-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .class-group-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .class-group-modal .modal-body {
            padding: 16px;
        }

        .class-group-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .class-group-modal .form-control {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .class-group-modal .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Cover Class Groups</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage class group setup and status for cover operations.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Class Groups</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Class Groups</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-class-groups">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Active</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-active-class-groups">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Inactive</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-inactive-class-groups">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-class-groups">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Class Group List</h5>
                        <small class="text-muted">View and manage class groups</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#add_classGroup">
                        <i class='bx bx-plus me-1'></i>Add Class Group
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover class-group-table" id="class-group-table"
                            aria-label="Class group table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Group Code</th>
                                    <th>Group Name</th>
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

    <div class="modal effect-scale md-wrapper class-group-modal" id="add_classGroup" tabindex="-1"
        aria-labelledby="addClassGroupLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addClassGroupLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Class Group
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_classGroup" action="{{ route('classGroup.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Group Name</label>
                                <input type="text" class="form-control" placeholder="Enter group name"
                                    aria-label="group Name" id="group_name" name="group_name">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Group Code</label>
                                <input type="text" class="form-control" placeholder="Enter group code"
                                    aria-label="group Code" inputmode="numeric" maxlength="5" required id="group_code"
                                    name="group_code">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="A" selected>Active</option>
                                    <option value="D">Inactive</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_classGroup_submit">
                            <i class="fas fa-check me-1"></i> Save Class Group
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade class-group-modal" id="edit_classGroupModal" tabindex="-1"
        aria-labelledby="editClassGroupLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editClassGroupLabel">
                        <i class="bx bx-edit me-2"></i>Edit Class Group
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_classGroup" action="{{ route('classGroup.edit') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Class Group</label>
                                <input type="hidden" class="form-control" placeholder="classGroup"
                                    aria-label="classGroup" id="ed_group_code" name="ed_group_code">
                                <input type="text" class="form-control" placeholder="Enter class group name"
                                    aria-label="classGroup" id="ed_group_name" name="ed_group_name">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="ed_status" name="ed_status">
                                    <option value="A">Active</option>
                                    <option value="D">Inactive</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm" id="edit_classGroup_submit">
                                    <i class="fas fa-check me-1"></i> Update Class Group
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('classGroup.delete') }}" method="post" id="del_classGroup_form">
        {{ csrf_field() }}
        <input type="hidden" name="del_group_code" id="del_group_code">
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#class-group-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('classGroup.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'group_code',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'group_name',
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

                    $('#stat-total-class-groups').text(json.recordsTotal || 0);
                    $('#stat-filtered-class-groups').text(json.recordsFiltered || 0);
                    $('#stat-active-class-groups').text(active);
                    $('#stat-inactive-class-groups').text(Math.max(totalVisible - active, 0));
                }
            });

            $('#class-group-table').on('click', '#edit_classGroup', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                var group_code = (rowData.group_code || '').toString().trim();
                var group_name = (rowData.group_name || '').toString().trim();
                var status = (rowData.status || 'A').toString().trim().toUpperCase();
                $("#ed_group_code").val(group_code);
                $("#ed_group_name").val(group_name);
                $("#ed_status").val(status === 'D' ? 'D' : 'A');
                $('#edit_classGroupModal').modal('show');
            });

            $('#class-group-table').on('click', '#activate_classGroup', function() {
                var group_code = $(this).closest('tr').find('td:eq(1)').text();
                var status = $(this).val();
                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to ' + status + ' the classGroup',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#del_group_code").val(group_code);
                        $.ajax({
                            url: $('#del_classGroup_form').attr('action'),
                            method: 'POST',
                            data: {
                                del_group_code: group_code,
                                mode: 'toggle',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(resp) {
                                if (window.toastr) toastr.success(resp.message ||
                                    'Status updated');
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                if (window.toastr) toastr.error(xhr.responseJSON
                                    ?.message ||
                                    'Failed to update status');
                            }
                        });
                    }
                });
            });

            $('#class-group-table').on('click', '#delete_classGroup', function() {
                var group_code = $(this).closest('tr').find('td:eq(1)').text();
                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this classGroup?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#del_group_code").val(group_code);
                        $.ajax({
                            url: $('#del_classGroup_form').attr('action'),
                            method: 'POST',
                            data: {
                                del_group_code: group_code,
                                mode: 'delete',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(resp) {
                                if (window.toastr) toastr.success(resp.message ||
                                    'Class group deleted');
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                if (window.toastr) toastr.error(xhr.responseJSON
                                    ?.message ||
                                    'Failed to delete class group');
                            }
                        });
                    }
                });
            });

            $("#store_classGroup").validate({
                rules: {
                    group_code: {
                        required: true,
                        digits: true,
                        maxlength: 5
                    },
                    group_name: {
                        required: true,
                        maxlength: 100
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    group_code: {
                        required: "Class group ISO is required",
                        digits: "Class group ISO must contain numbers only",
                        maxlength: "Class group ISO must be at most 5 digits"
                    },
                    group_name: {
                        required: "classGroup name is required",
                        maxlength: "classGroup name must be at most 100 characters"
                    },
                    status: {
                        required: "status is required"
                    }
                },
                errorPlacement: function(error, element) {
                    error.addClass("text-danger");
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
                            title: 'Save class group?',
                            text: 'Do you want to submit these changes?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Save',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: $(form).attr('action'),
                                    method: 'POST',
                                    data: $(form).serialize(),
                                    success: function(resp) {
                                        if (window.toastr) toastr.success(resp
                                            .message ||
                                            'Class group saved successfully');
                                        $('#add_classGroup').modal('hide');
                                        form.reset();
                                        $('#status').val('A');
                                        table.ajax.reload(null, false);
                                    },
                                    error: function(xhr) {
                                        if (xhr.status === 422 && xhr.responseJSON
                                            ?.errors) {
                                            $(form).validate().showErrors(xhr
                                                .responseJSON.errors);
                                        } else if (window.toastr) {
                                            toastr.error(xhr.responseJSON
                                                ?.message ||
                                                'Failed to save class group');
                                        }
                                    }
                                });
                            }
                        });
                        return false;
                    }
                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: $(form).serialize(),
                        success: function(resp) {
                            if (window.toastr) toastr.success(resp.message ||
                                'Class group saved successfully');
                            $('#add_classGroup').modal('hide');
                            form.reset();
                            $('#status').val('A');
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                $(form).validate().showErrors(xhr.responseJSON.errors);
                            } else if (window.toastr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to save class group');
                            }
                        }
                    });
                }
            });

            $("#edit_classGroup").validate({
                rules: {
                    ed_group_name: {
                        required: true,
                        maxlength: 100
                    },
                    ed_status: {
                        required: true
                    }
                },
                messages: {
                    ed_group_name: {
                        required: "classGroup name is required",
                        maxlength: "classGroup name must be at most 100 characters"
                    },
                    ed_status: {
                        required: "status is required"
                    }
                },
                errorPlacement: function(error, element) {
                    error.addClass("text-danger");
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
                            title: 'Update class group?',
                            text: 'Do you want to save these changes?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, Update',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: $(form).attr('action'),
                                    method: 'POST',
                                    data: $(form).serialize(),
                                    success: function(resp) {
                                        if (window.toastr) toastr.success(resp
                                            .message ||
                                            'Class group updated successfully');
                                        $('#edit_classGroupModal').modal('hide');
                                        table.ajax.reload(null, false);
                                    },
                                    error: function(xhr) {
                                        if (xhr.status === 422 && xhr.responseJSON
                                            ?.errors) {
                                            $(form).validate().showErrors(xhr
                                                .responseJSON.errors);
                                        } else if (window.toastr) {
                                            toastr.error(xhr.responseJSON
                                                ?.message ||
                                                'Failed to update class group');
                                        }
                                    }
                                });
                            }
                        });
                        return false;
                    }
                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: $(form).serialize(),
                        success: function(resp) {
                            if (window.toastr) toastr.success(resp.message ||
                                'Class group updated successfully');
                            $('#edit_classGroupModal').modal('hide');
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                $(form).validate().showErrors(xhr.responseJSON.errors);
                            } else if (window.toastr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to update class group');
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
