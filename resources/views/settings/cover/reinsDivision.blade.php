@extends('layouts.app')

@push('styles')
    <style>
        .reins-division-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .reins-division-table tbody td {
            vertical-align: middle;
        }

        .reins-division-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .reins-division-table .btn:last-child {
            margin-right: 0;
        }

        .reins-division-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .reins-division-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .reins-division-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .reins-division-modal .modal-body {
            padding: 1.25rem;
        }

        .reins-division-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .reins-division-modal .form-control,
        .reins-division-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .reins-division-modal .form-control:focus,
        .reins-division-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Reinsurance Divisions</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage reinsurance division setup and status for cover operations.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Reinsurance Divisions</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Divisions</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-divisions">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Active</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-active-divisions">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Inactive</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-inactive-divisions">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-divisions">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Division List</h5>
                        <small class="text-muted">View and manage reinsurance divisions</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#reinsDivision">
                        <i class='bx bx-plus me-1'></i>Add Division
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover reins-division-table" id="reinsDivision-table"
                            aria-label="Reinsurance division table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Division Code</th>
                                    <th>Division Name</th>
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

    <div class="modal fade reins-division-modal" id="reinsDivision" tabindex="-1" aria-labelledby="reinsDivisionLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="reinsDivisionLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Reinsurance Division
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_reinsDivision" action="{{ route('reinsDivision.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Division Code</label>
                                <input type="text" class="form-control" placeholder="Enter division code"
                                    aria-label="Division code" id="division_code" name="division_code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Division Name</label>
                                <input type="text" class="form-control" placeholder="Enter division name"
                                    aria-label="Division name" id="division_name" name="division_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_reinsDivision">
                            <i class="fas fa-check me-1"></i> Save Division
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade reins-division-modal" id="edit_reinsDivisionModal" tabindex="-1"
        aria-labelledby="editReinsDivisionLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editReinsDivisionLabel">
                        <i class="bx bx-edit me-2"></i>Edit Reinsurance Division
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_reinsDivision" action="{{ route('reinsDivision.edit') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <input type="hidden" id="ed_division_code" name="ed_division_code">
                            <div class="col-md-12">
                                <label class="form-label">Division Name</label>
                                <input type="text" class="form-control" placeholder="Enter division name"
                                    aria-label="Division name" id="ed_division_name" name="ed_division_name">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm" id="edit_reinsDivision_submit">
                                    <i class="fas fa-check me-1"></i> Update Division
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('reinsDivision.delete') }}" method="post" id="del_reinsDivision_form">
        {{ csrf_field() }}
        <input type="hidden" name="del_division_code" id="del_division_code">
        <input type="hidden" name="mode" id="reins_division_mode" value="toggle">
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#reinsDivision-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('reinsDivision.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'division_code',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'division_name',
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

                    $('#stat-total-divisions').text(json.recordsTotal || 0);
                    $('#stat-filtered-divisions').text(json.recordsFiltered || 0);
                    $('#stat-active-divisions').text(active);
                    $('#stat-inactive-divisions').text(Math.max(totalVisible - active, 0));
                }
            });

            $('#reinsDivision-table').on('click', '#edit_reinsDivision', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const divisionCode = (rowData.division_code || '').toString().trim();
                const divisionName = (rowData.division_name || '').toString().trim();

                $('#ed_division_code').val(divisionCode);
                $('#ed_division_name').val(divisionName);
                $('#edit_reinsDivisionModal').modal('show');
            });

            $('#reinsDivision-table').on('click', '#activate_reinsDivision', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const divisionCode = (rowData.division_code || '').toString().trim();
                const actionLabel = ($(this).val() || 'update').toString();

                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to ' + actionLabel + ' this division?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#del_division_code').val(divisionCode);
                        $('#reins_division_mode').val('toggle');
                        $('#del_reinsDivision_form').submit();
                    }
                });
            });

            $('#reinsDivision-table').on('click', '#delete_reinsDivision', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const divisionCode = (rowData.division_code || '').toString().trim();

                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to permanently delete this division?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#del_division_code').val(divisionCode);
                        $('#reins_division_mode').val('delete');
                        $('#del_reinsDivision_form').submit();
                    }
                });
            });

            $('#store_reinsDivision').validate({
                rules: {
                    division_code: {
                        required: true,
                        maxlength: 20
                    },
                    division_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    division_code: {
                        required: 'Division code is required',
                        maxlength: 'Division code must be at most 20 characters'
                    },
                    division_name: {
                        required: 'Division name is required',
                        maxlength: 'Division name must be at most 80 characters'
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
                            title: 'Save division?',
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

            $('#edit_reinsDivision').validate({
                rules: {
                    ed_division_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    ed_division_name: {
                        required: 'Division name is required',
                        maxlength: 'Division name must be at most 80 characters'
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
                            title: 'Update division?',
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
