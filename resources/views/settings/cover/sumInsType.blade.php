@extends('layouts.app')

@push('styles')
    <style>
        .sum-insured-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .sum-insured-table tbody td {
            vertical-align: middle;
        }

        .sum-insured-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .sum-insured-table .btn:last-child {
            margin-right: 0;
        }

        .sum-insured-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .sum-insured-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .sum-insured-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .sum-insured-modal .modal-body {
            padding: 1.25rem;
        }

        .sum-insured-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .sum-insured-modal .form-control,
        .sum-insured-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .sum-insured-modal .form-control:focus,
        .sum-insured-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Cover Sum Insured Types</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage sum insured type setup and status for cover operations.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Sum Insured Types</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Types</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-sum-insured-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Active</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-active-sum-insured-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Inactive</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-inactive-sum-insured-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-sum-insured-types">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Sum Insured Type List</h5>
                        <small class="text-muted">View and manage sum insured types</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#sumInsType">
                        <i class='bx bx-plus me-1'></i>Add Sum Insured Type
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover sum-insured-table" id="sumInsType-table"
                            aria-label="Sum insured type table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Insured Code</th>
                                    <th>Insured Name</th>
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

    <div class="modal fade sum-insured-modal" id="sumInsType" tabindex="-1" aria-labelledby="sumInsTypeLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="sumInsTypeLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Sum Insured Type
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_sumInsType" action="{{ route('sumInsType.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Insured Code</label>
                                <input type="text" class="form-control" placeholder="Enter insured code" aria-label="Insured Code"
                                    id="sum_insured_code" name="sum_insured_code">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Insured Name</label>
                                <input type="text" class="form-control" placeholder="Enter insured name" aria-label="Insured Name"
                                    id="sum_insured_name" name="sum_insured_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_sumInsType">
                            <i class="fas fa-check me-1"></i> Save Sum Insured Type
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade sum-insured-modal" id="edit_sumInsTypeModal" tabindex="-1"
        aria-labelledby="editSumInsTypeLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editSumInsTypeLabel">
                        <i class="bx bx-edit me-2"></i>Edit Sum Insured Type
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_sumInsType" action="{{ route('sumInsType.edit') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <input type="hidden" class="form-control" aria-label="sumInsType" id="ed_sum_insured_code"
                                name="ed_sum_insured_code">
                            <div class="col-md-12">
                                <label class="form-label">Insured Name</label>
                                <input type="text" class="form-control" placeholder="Enter insured name" aria-label="insured name"
                                    id="ed_sum_insured_name" name="ed_sum_insured_name">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm" id="edit_sumInsType_submit">
                                    <i class="fas fa-check me-1"></i> Update Sum Insured Type
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#sumInsType-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('sumInsType.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'sum_insured_code',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'sum_insured_name',
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

                    $('#stat-total-sum-insured-types').text(json.recordsTotal || 0);
                    $('#stat-filtered-sum-insured-types').text(json.recordsFiltered || 0);
                    $('#stat-active-sum-insured-types').text(active);
                    $('#stat-inactive-sum-insured-types').text(Math.max(totalVisible - active, 0));
                }
            });

            $('#sumInsType-table').on('click', '#edit_sumInsType', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const sumInsuredCode = (rowData.sum_insured_code || '').toString().trim();
                const sumInsuredName = (rowData.sum_insured_name || '').toString().trim();

                $('#ed_sum_insured_code').val(sumInsuredCode);
                $('#ed_sum_insured_name').val(sumInsuredName);
                $('#edit_sumInsTypeModal').modal('show');
            });

            $('#store_sumInsType').validate({
                rules: {
                    sum_insured_code: {
                        required: true,
                        maxlength: 20
                    },
                    sum_insured_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    sum_insured_code: {
                        required: 'Insured code is required',
                        maxlength: 'Insured code must be at most 20 characters'
                    },
                    sum_insured_name: {
                        required: 'Insured name is required',
                        maxlength: 'Insured name must be at most 80 characters'
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
                            title: 'Save sum insured type?',
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

            $('#edit_sumInsType').validate({
                rules: {
                    ed_sum_insured_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    ed_sum_insured_name: {
                        required: 'Insured name is required',
                        maxlength: 'Insured name must be at most 80 characters'
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
                            title: 'Update sum insured type?',
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
