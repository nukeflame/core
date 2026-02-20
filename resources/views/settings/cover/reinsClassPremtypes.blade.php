@extends('layouts.app')

@push('styles')
    <style>
        .reins-premtype-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .reins-premtype-table tbody td {
            vertical-align: middle;
        }

        .reins-premtype-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .reins-premtype-table .btn:last-child {
            margin-right: 0;
        }

        .reins-premtype-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .reins-premtype-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .reins-premtype-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .reins-premtype-modal .modal-body {
            padding: 1.25rem;
        }

        .reins-premtype-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .reins-premtype-modal .form-control,
        .reins-premtype-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .reins-premtype-modal .form-control:focus,
        .reins-premtype-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Reinsurance Class Premium Types</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage premium-type mapping by reinsurance class and status.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Reins Class Premtypes</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Mappings</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-premtypes">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Active</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-active-premtypes">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Inactive</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-inactive-premtypes">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-premtypes">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Mapping List</h5>
                        <small class="text-muted">View and manage class-premtype mappings</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#reinsClassPremtypes">
                        <i class='bx bx-plus me-1'></i>Add Mapping
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover reins-premtype-table"
                            id="reinsClassPremtypes-table" aria-label="Reinsurance class premtypes table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Rein Class Code</th>
                                    <th>Rein Class Name</th>
                                    <th>Premtype Code</th>
                                    <th>Premtype Name</th>
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

    <div class="modal fade reins-premtype-modal" id="reinsClassPremtypes" tabindex="-1"
        aria-labelledby="reinsClassPremtypesLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="reinsClassPremtypesLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Mapping
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_reinsClassPremtypes" action="{{ route('reinsClassPremtypes.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label" for="reinclass">Rein Class</label>
                                <select name="reinclass" id="reinclass" class="form-select">
                                    <option value="">Select reinclass</option>
                                    @foreach ($reinclasses as $reinclass)
                                        <option value="{{ $reinclass->class_code }}">{{ $reinclass->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Premtype Code</label>
                                <input type="text" class="form-control" placeholder="Enter premtype code"
                                    aria-label="Premtype code" id="premtype_code" name="premtype_code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Premtype Name</label>
                                <input type="text" class="form-control" placeholder="Enter premtype name"
                                    aria-label="Premtype name" id="premtype_name" name="premtype_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_reinsClassPremtypes">
                            <i class="fas fa-check me-1"></i> Save Mapping
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade reins-premtype-modal" id="edit_reinsClassPremtypesModal" tabindex="-1"
        aria-labelledby="editReinsClassPremtypesLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editReinsClassPremtypesLabel">
                        <i class="bx bx-edit me-2"></i>Edit Mapping
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit_reinsClassPremtypes" action="{{ route('reinsClassPremtypes.edit') }}" method="post">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <input type="hidden" id="ed_premtype_code" name="ed_premtype_code">
                            <input type="hidden" id="ed_reinclass" name="ed_reinclass">
                            <div class="col-md-12">
                                <label class="form-label">Rein Class</label>
                                <input type="text" class="form-control" id="ed_reinclass_display" readonly>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Premtype Code</label>
                                <input type="text" class="form-control" id="ed_premtype_code_display" readonly>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Premtype Name</label>
                                <input type="text" class="form-control" placeholder="Enter premtype name"
                                    aria-label="Premtype name" id="ed_premtype_name" name="ed_premtype_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="edit_reinsClassPremtypes_submit">
                            <i class="fas fa-check me-1"></i> Update Mapping
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="{{ route('reinsClassPremtypes.delete') }}" method="post" id="del_reinsClassPremtypes_form">
        {{ csrf_field() }}
        <input type="hidden" name="del_reinclass" id="del_reinclass">
        <input type="hidden" name="del_premtype_code" id="del_premtype_code">
        <input type="hidden" name="mode" id="reins_class_premtype_mode" value="toggle">
    </form>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#reinsClassPremtypes-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('reinsClassPremtypes.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'reinclass',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'reinclass_name',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'premtype_code',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'premtype_name',
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

                    $('#stat-total-premtypes').text(json.recordsTotal || 0);
                    $('#stat-filtered-premtypes').text(json.recordsFiltered || 0);
                    $('#stat-active-premtypes').text(active);
                    $('#stat-inactive-premtypes').text(Math.max(totalVisible - active, 0));
                }
            });

            $('#reinsClassPremtypes-table').on('click', '#edit_reinsClassPremtypes', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const reinclass = (rowData.reinclass || '').toString().trim();
                const reinclassName = (rowData.reinclass_name || '').toString().trim();
                const premtypeCode = (rowData.premtype_code || '').toString().trim();
                const premtypeName = (rowData.premtype_name || '').toString().trim();

                $('#ed_reinclass').val(reinclass);
                $('#ed_premtype_code').val(premtypeCode);
                $('#ed_reinclass_display').val(reinclassName || reinclass);
                $('#ed_premtype_code_display').val(premtypeCode);
                $('#ed_premtype_name').val(premtypeName);
                $('#edit_reinsClassPremtypesModal').modal('show');
            });

            $('#reinsClassPremtypes-table').on('click', '#activate_reinsClassPremtypes', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const reinclass = (rowData.reinclass || '').toString().trim();
                const reinclassName = (rowData.reinclass_name || reinclass).toString().trim();
                const premtypeCode = (rowData.premtype_code || '').toString().trim();
                const premtypeName = (rowData.premtype_name || '').toString().trim();
                const statusLabel = ($(this).val() || 'Update').toString();

                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to ' + statusLabel + ' the ' + reinclassName + '-' + premtypeName + '?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#del_reinclass').val(reinclass);
                        $('#del_premtype_code').val(premtypeCode);
                        $('#reins_class_premtype_mode').val('toggle');
                        $('#del_reinsClassPremtypes_form').submit();
                    }
                });
            });

            $('#reinsClassPremtypes-table').on('click', '#delete_reinsClassPremtypes', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const reinclass = (rowData.reinclass || '').toString().trim();
                const premtypeCode = (rowData.premtype_code || '').toString().trim();
                const reinclassName = (rowData.reinclass_name || reinclass).toString().trim();
                const premtypeName = (rowData.premtype_name || '').toString().trim();

                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to permanently delete ' + reinclassName + '-' + premtypeName + '?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#del_reinclass').val(reinclass);
                        $('#del_premtype_code').val(premtypeCode);
                        $('#reins_class_premtype_mode').val('delete');
                        $('#del_reinsClassPremtypes_form').submit();
                    }
                });
            });

            $('#store_reinsClassPremtypes').validate({
                rules: {
                    reinclass: {
                        required: true
                    },
                    premtype_code: {
                        required: true,
                        maxlength: 20
                    },
                    premtype_name: {
                        required: true,
                        maxlength: 100
                    }
                },
                messages: {
                    reinclass: {
                        required: 'Reins class is required'
                    },
                    premtype_code: {
                        required: 'Premtype code is required',
                        maxlength: 'Premtype code must be at most 20 characters'
                    },
                    premtype_name: {
                        required: 'Premtype name is required',
                        maxlength: 'Premtype name must be at most 100 characters'
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
                            title: 'Save mapping?',
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

            $('#edit_reinsClassPremtypes').validate({
                rules: {
                    ed_premtype_name: {
                        required: true,
                        maxlength: 100
                    }
                },
                messages: {
                    ed_premtype_name: {
                        required: 'Premtype name is required',
                        maxlength: 'Premtype name must be at most 100 characters'
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
                            title: 'Update mapping?',
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
