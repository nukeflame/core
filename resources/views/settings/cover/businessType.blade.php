@extends('layouts.app')

@push('styles')
    <style>
        .business-type-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .business-type-table tbody td {
            vertical-align: middle;
        }

        .business-type-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .business-type-table .btn:last-child {
            margin-right: 0;
        }

        .business-type-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .business-type-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .business-type-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .business-type-modal .modal-body {
            padding: 1.25rem;
        }

        .business-type-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .business-type-modal .form-control,
        .business-type-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .business-type-modal .form-control:focus,
        .business-type-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Business Types</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage business type setup for cover operations.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Business Types</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Types</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-business-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-business-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Visible Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-visible-business-types">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Highest ID (Visible)</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-max-business-type-id">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Business Type List</h5>
                        <small class="text-muted">View and manage business types</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#businessType">
                        <i class='bx bx-plus me-1'></i>Add Business Type
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover business-type-table" id="bus-type-table"
                            aria-label="Business type table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Type Name</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade business-type-modal" id="businessType" tabindex="-1" aria-labelledby="businessTypeLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="businessTypeLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Business Type
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_businessType" action="{{ route('businessType.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Business Type ID</label>
                                <input type="text" class="form-control" placeholder="Enter business type ID"
                                    aria-label="Business Type ID" id="bus_type_id" name="bus_type_id">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Business Type Name</label>
                                <input type="text" class="form-control" placeholder="Enter business type name"
                                    aria-label="Business Type Name" id="bus_type_name" name="bus_type_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_businessType">
                            <i class="fas fa-check me-1"></i> Save Business Type
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade business-type-modal" id="edit_businessTypeModal" tabindex="-1"
        aria-labelledby="editBusinessTypeLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editBusinessTypeLabel">
                        <i class="bx bx-edit me-2"></i>Edit Business Type
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit_businessType" action="{{ route('businessType.edit') }}" method="post">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <input type="hidden" id="ed_bus_type_id" name="ed_bus_type_id">
                            <div class="col-md-12">
                                <label class="form-label">Business Type Name</label>
                                <input type="text" class="form-control" placeholder="Enter business type name"
                                    aria-label="Business Type" id="ed_bus_type_name" name="ed_bus_type_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="edit_businessType_submit">
                            <i class="fas fa-check me-1"></i> Update Business Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#bus-type-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('businessType.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'bus_type_id',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'bus_type_name',
                        defaultContent: "<b class='dashes'>_</b>"
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
                    const ids = rows
                        .map(row => Number(row.bus_type_id || 0))
                        .filter(num => !Number.isNaN(num));

                    $('#stat-total-business-types').text(json.recordsTotal || 0);
                    $('#stat-filtered-business-types').text(json.recordsFiltered || 0);
                    $('#stat-visible-business-types').text(rows.length || 0);
                    $('#stat-max-business-type-id').text(ids.length ? Math.max(...ids) : 0);
                }
            });

            $('#bus-type-table').on('click', '#edit_businessType', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const busTypeId = (rowData.bus_type_id || '').toString().trim();
                const busTypeName = (rowData.bus_type_name || '').toString().trim();
                $('#ed_bus_type_id').val(busTypeId);
                $('#ed_bus_type_name').val(busTypeName);
                $('#edit_businessTypeModal').modal('show');
            });

            $('#store_businessType').validate({
                rules: {
                    bus_type_id: {
                        required: true,
                        maxlength: 3,
                        digits: true
                    },
                    bus_type_name: {
                        required: true,
                        maxlength: 100
                    }
                },
                messages: {
                    bus_type_id: {
                        required: 'Business type ID is required',
                        maxlength: 'Business type ID must be at most 3 digits',
                        digits: 'Business type ID must contain numbers only'
                    },
                    bus_type_name: {
                        required: 'Business type name is required',
                        maxlength: 'Business type name must be at most 100 characters'
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
                            title: 'Save business type?',
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

            $('#edit_businessType').validate({
                rules: {
                    ed_bus_type_name: {
                        required: true,
                        maxlength: 100
                    }
                },
                messages: {
                    ed_bus_type_name: {
                        required: 'Business type name is required',
                        maxlength: 'Business type name must be at most 100 characters'
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
                            title: 'Update business type?',
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
