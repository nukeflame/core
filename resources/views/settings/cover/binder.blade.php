@extends('layouts.app')

@push('styles')
    <style>
        .binder-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .binder-table tbody td {
            vertical-align: middle;
        }

        .binder-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .binder-table .btn:last-child {
            margin-right: 0;
        }

        .binder-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .binder-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .binder-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .binder-modal .modal-body {
            padding: 1.25rem;
        }

        .binder-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .binder-modal .form-control,
        .binder-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .binder-modal .form-control:focus,
        .binder-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Cover Binders</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Manage binder setup and insured-agency mapping for cover operations.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Cover Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Binders</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Binders</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-binders">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-binders">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Visible Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-visible-binders">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Agencies (Visible)</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-visible-agencies">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Binder List</h5>
                        <small class="text-muted">View and manage binder records</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#binder">
                        <i class='bx bx-plus me-1'></i>Add Binder
                    </button>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover binder-table" id="binder-table"
                            aria-label="Binder table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Binder No</th>
                                    <th>Insured Name</th>
                                    <th>Agency Name</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade binder-modal" id="binder" tabindex="-1" aria-labelledby="binderLabel" data-bs-keyboard="false"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="binderLabel">
                        <i class="bx bx-plus-circle me-2"></i>Create Binder
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_binder" action="{{ route('binder.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Binder Number</label>
                                <input type="text" class="form-control" placeholder="Enter binder number"
                                    aria-label="Binder Number" id="binder_cov_no" name="binder_cov_no">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Insured Name</label>
                                <input type="text" class="form-control" placeholder="Enter insured name"
                                    aria-label="Insured Name" id="insured_name" name="insured_name">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Agency Name</label>
                                <input type="text" class="form-control" placeholder="Enter agency name"
                                    aria-label="Agency Name" id="agency_name" name="agency_name">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_binder">
                            <i class="fas fa-check me-1"></i> Save Binder
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade binder-modal" id="edit_binderModal" tabindex="-1" aria-labelledby="editBinderLabel"
        data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editBinderLabel">
                        <i class="bx bx-edit me-2"></i>Edit Binder
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_binder" action="{{ route('binder.edit') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Insured Name</label>
                                <input type="hidden" class="form-control" aria-label="binder" id="ed_binder_cov_no"
                                    name="ed_binder_cov_no">
                                <input type="text" class="form-control" placeholder="Enter insured name"
                                    aria-label="insured" id="ed_insured_name" name="ed_insured_name">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Agency Name</label>
                                <input type="text" class="form-control" placeholder="Enter agency name"
                                    aria-label="agency" id="ed_agency_name" name="ed_agency_name">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm" id="edit_binder_submit">
                                    <i class="fas fa-check me-1"></i> Update Binder
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
            const table = $('#binder-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'asc']
                ],
                ajax: "{{ route('binder.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'binder_cov_no',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'insured_name',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'agency_name',
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
                    const agencies = new Set(rows
                        .map(row => (row.agency_name || '').toString().trim())
                        .filter(name => name.length > 0)
                    );

                    $('#stat-total-binders').text(json.recordsTotal || 0);
                    $('#stat-filtered-binders').text(json.recordsFiltered || 0);
                    $('#stat-visible-binders').text(rows.length || 0);
                    $('#stat-visible-agencies').text(agencies.size || 0);
                }
            });

            $('#binder-table').on('click', '#edit_binder', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                const binderCovNo = (rowData.binder_cov_no || '').toString().trim();
                const insuredName = (rowData.insured_name || '').toString().trim();
                const agencyName = (rowData.agency_name || '').toString().trim();

                $('#ed_binder_cov_no').val(binderCovNo);
                $('#ed_insured_name').val(insuredName);
                $('#ed_agency_name').val(agencyName);
                $('#edit_binderModal').modal('show');
            });

            $('#store_binder').validate({
                rules: {
                    binder_cov_no: {
                        required: true,
                        maxlength: 20
                    },
                    insured_name: {
                        required: true,
                        maxlength: 80
                    },
                    agency_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    binder_cov_no: {
                        required: 'Binder number is required',
                        maxlength: 'Binder number must be at most 20 characters'
                    },
                    insured_name: {
                        required: 'Insured name is required',
                        maxlength: 'Insured name must be at most 80 characters'
                    },
                    agency_name: {
                        required: 'Agency name is required',
                        maxlength: 'Agency name must be at most 80 characters'
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
                            title: 'Save binder?',
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

            $('#edit_binder').validate({
                rules: {
                    ed_insured_name: {
                        required: true,
                        maxlength: 80
                    },
                    ed_agency_name: {
                        required: true,
                        maxlength: 80
                    }
                },
                messages: {
                    ed_insured_name: {
                        required: 'Insured name is required',
                        maxlength: 'Insured name must be at most 80 characters'
                    },
                    ed_agency_name: {
                        required: 'Agency name is required',
                        maxlength: 'Agency name must be at most 80 characters'
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
                            title: 'Update binder?',
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
