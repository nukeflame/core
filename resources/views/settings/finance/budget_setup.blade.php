@extends('layouts.app')

@push('styles')
    <style>
        .budget-setup-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 1px solid #dee2e6;
        }

        .budget-setup-table tbody td {
            vertical-align: middle;
        }

        .budget-setup-table .btn {
            margin-right: 0.35rem;
            margin-bottom: 0.2rem;
        }

        .budget-setup-table .btn:last-child {
            margin-right: 0;
        }

        .budget-setup-modal .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            border-bottom: 0;
        }

        .budget-setup-modal .modal-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .budget-setup-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .budget-setup-modal .modal-body {
            padding: 16px;
        }

        .budget-setup-modal .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .budget-setup-modal .form-control,
        .budget-setup-modal .form-select {
            border-radius: 0.45rem;
            border-color: #d8dee6;
        }

        .budget-setup-modal .form-control:focus,
        .budget-setup-modal .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Company Budgets Setup</h1>
            <p class="text-muted mb-0 mt-1 fs-13">Configure budget years and categories for the organization.</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="#">Settings</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Budget Setup</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active fw-semibold" href="{{ route('settings.budgetSetup.index') }}">
                <i class="bx bx-buildings me-1"></i>Company Budgets
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold" href="{{ route('settings.budgetSetup.users') }}">
                <i class="bx bx-user-check me-1"></i>User Budget Setup
            </a>
        </li>
    </ul>

    <div class="row g-3 mb-2">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Total Budgets</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-total-budgets">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Active</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-active-budgets">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Inactive</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-inactive-budgets">0</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-body">
                    <p class="text-muted mb-0">Filtered Rows</p>
                    <h4 class="fw-semibold mt-1 mb-0" id="stat-filtered-budgets">0</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card custom-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Active Budgets</h5>
                        <small class="text-muted">View and manage company budget configurations</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                            data-bs-target="#copy_fiscal_year_modal">
                            <i class='bx bx-copy me-1'></i>Copy from Fiscal Year
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#add_budget_setup">
                            <i class='bx bx-plus me-1'></i>Budget Setup
                        </button>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-striped table-hover budget-setup-table"
                            id="budget-setup-table" aria-label="Budget setup table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Budget Year</th>
                                    <th>Budget Category</th>
                                    <th>Status</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Budget Setup Modal --}}
    <div class="modal effect-scale md-wrapper budget-setup-modal" id="add_budget_setup" tabindex="-1"
        aria-labelledby="addBudgetSetupLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addBudgetSetupLabel">
                        <i class="bx bx-plus-circle me-2"></i>New Budget Setup
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="store_budget_setup" action="{{ route('settings.budgetSetup.store') }}" method="post">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Budget Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" placeholder="e.g. 2025" aria-label="Budget Year"
                                    id="budget_year" name="budget_year" min="2000" max="2100" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Budget Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="budget_category" name="budget_category" required>
                                    <option value="" selected disabled>Select Budget Category</option>
                                    <option value="new_business">New Business</option>
                                    <option value="organic_growth">Organic Growth</option>
                                    <option value="miscellanous_income">Miscellanous Income</option>
                                    <option value="renewal_business">Renewal Business</option>
                                </select>
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
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="add_budget_setup_submit">
                            <i class="fas fa-save me-1"></i> Save
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Budget Setup Modal --}}
    <div class="modal fade budget-setup-modal" id="edit_budget_setup_modal" tabindex="-1"
        aria-labelledby="editBudgetSetupLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editBudgetSetupLabel">
                        <i class="bx bx-edit me-2"></i>Edit Budget Setup
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_budget_setup_form" action="{{ route('settings.budgetSetup.update') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" id="ed_id" name="ed_id">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Budget Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" placeholder="e.g. 2025"
                                    aria-label="Budget Year" id="ed_budget_year" name="ed_budget_year" min="2000"
                                    max="2100" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Budget Category <span class="text-danger">*</span></label>
                                <div>
                                    <select class="form-select" id="ed_budget_category" name="ed_budget_category"
                                        required>
                                        <option value="" disabled>Select Budget Category</option>
                                        <option value="new_business">New Business</option>
                                        <option value="organic_growth">Organic Growth</option>
                                        <option value="miscellanous_income">Miscellanous Income</option>
                                        <option value="renewal_business">Renewal Business</option>
                                    </select>
                                </div>
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
                                <button type="submit" class="btn btn-primary btn-sm" id="edit_budget_setup_submit">
                                    <i class="fas fa-check me-1"></i> Update Budget Setup
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete form --}}
    <form action="{{ route('settings.budgetSetup.destroy') }}" method="post" id="del_budget_setup_form">
        {{ csrf_field() }}
        <input type="hidden" name="del_id" id="del_id">
    </form>

    {{-- Copy from Fiscal Year Modal --}}
    <div class="modal fade budget-setup-modal" id="copy_fiscal_year_modal" tabindex="-1"
        aria-labelledby="copyFiscalYearLabel" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="copyFiscalYearLabel">
                        <i class="bx bx-copy me-2"></i>Copy Budget from Fiscal Year
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="copy_fiscal_year_form">
                    <div class="modal-body">
                        <div class="alert alert-info-transparent py-2 px-3 mb-3">
                            <small><i class="bx bx-info-circle me-1"></i>This will copy all budget setup entries from the
                                selected source year into the target year. Existing entries in the target year will be
                                skipped.</small>
                        </div>
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <label class="form-label">Source Fiscal Year <span class="text-danger">*</span></label>
                                <select class="form-select" id="copy_source_year" name="source_year" required>
                                    <option value="" selected disabled>Select Source Year</option>
                                    @foreach ($fiscalYears as $fy)
                                        <option value="{{ $fy->year }}">{{ $fy->year }} — {{ $fy->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Target Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" placeholder="e.g. 2026"
                                    aria-label="Target Year" id="copy_target_year" name="target_year" min="2000"
                                    max="2100" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Close
                        </button>
                        <button type="submit" class="btn btn-info btn-sm" id="copy_fiscal_year_submit">
                            <i class="bx bx-copy me-1"></i> Copy Budgets
                        </button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            const table = $('#budget-setup-table').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'desc']
                ],
                ajax: "{{ route('settings.budgetSetup.data') }}",
                columns: [{
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'budget_year',
                        defaultContent: "<b class='dashes'>_</b>"
                    },
                    {
                        data: 'budget_category',
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

                    $('#stat-total-budgets').text(json.recordsTotal || 0);
                    $('#stat-filtered-budgets').text(json.recordsFiltered || 0);
                    $('#stat-active-budgets').text(active);
                    $('#stat-inactive-budgets').text(Math.max(totalVisible - active, 0));
                }
            });

            // Edit button click
            $('#budget-setup-table').on('click', '#edit_budget_setup', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                $("#ed_id").val(rowData.id || '');
                $("#ed_budget_year").val(rowData.budget_year || '');
                $("#ed_budget_category").val(rowData.budget_category || '');
                var status = (rowData.status || 'A').toString().trim().toUpperCase();
                $("#ed_status").val(status === 'D' ? 'D' : 'A');
                $('#edit_budget_setup_modal').modal('show');
            });

            // Delete button click
            $('#budget-setup-table').on('click', '#delete_budget_setup', function() {
                const rowData = table.row($(this).closest('tr')).data() || {};
                var id = rowData.id;
                swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this budget setup?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $('#del_budget_setup_form').attr('action'),
                            method: 'POST',
                            data: {
                                del_id: id,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(resp) {
                                if (window.toastr) toastr.success(resp.message ||
                                    'Budget setup deleted');
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                if (window.toastr) toastr.error(xhr.responseJSON
                                    ?.message ||
                                    'Failed to delete budget setup');
                            }
                        });
                    }
                });
            });

            // Add form validation & submit
            $("#store_budget_setup").validate({
                rules: {
                    budget_year: {
                        required: true,
                        digits: true,
                        minlength: 4,
                        maxlength: 4
                    },
                    budget_category: {
                        required: true
                    },
                    status: {
                        required: true
                    }
                },
                messages: {
                    budget_year: {
                        required: "Budget year is required",
                        digits: "Budget year must be a valid number",
                        minlength: "Budget year must be 4 digits",
                        maxlength: "Budget year must be 4 digits"
                    },
                    budget_category: {
                        required: "Budget category is required"
                    },
                    status: {
                        required: "Status is required"
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
                            title: 'Save budget setup?',
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
                                            'Budget setup saved successfully');
                                        $('#add_budget_setup').modal('hide');
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
                                                'Failed to save budget setup');
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
                                'Budget setup saved successfully');
                            $('#add_budget_setup').modal('hide');
                            form.reset();
                            $('#status').val('A');
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                $(form).validate().showErrors(xhr.responseJSON.errors);
                            } else if (window.toastr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to save budget setup');
                            }
                        }
                    });
                }
            });

            // Edit form validation & submit
            $("#edit_budget_setup_form").validate({
                rules: {
                    ed_budget_year: {
                        required: true,
                        digits: true,
                        minlength: 4,
                        maxlength: 4
                    },
                    ed_budget_category: {
                        required: true
                    },
                    ed_status: {
                        required: true
                    }
                },
                messages: {
                    ed_budget_year: {
                        required: "Budget year is required",
                        digits: "Budget year must be a valid number",
                        minlength: "Budget year must be 4 digits",
                        maxlength: "Budget year must be 4 digits"
                    },
                    ed_budget_category: {
                        required: "Budget category is required"
                    },
                    ed_status: {
                        required: "Status is required"
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
                            title: 'Update budget setup?',
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
                                            'Budget setup updated successfully');
                                        $('#edit_budget_setup_modal').modal(
                                            'hide');
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
                                                'Failed to update budget setup');
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
                                'Budget setup updated successfully');
                            $('#edit_budget_setup_modal').modal('hide');
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                $(form).validate().showErrors(xhr.responseJSON.errors);
                            } else if (window.toastr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to update budget setup');
                            }
                        }
                    });
                }
            });
            // Copy from Fiscal Year form submit
            $('#copy_fiscal_year_form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                const sourceYear = $('#copy_source_year').val();
                const targetYear = $('#copy_target_year').val();

                if (!sourceYear || !targetYear) {
                    if (window.toastr) toastr.warning('Please fill in both source and target year.');
                    return;
                }

                if (sourceYear === targetYear) {
                    if (window.toastr) toastr.warning('Source and target years must be different.');
                    return;
                }

                Swal.fire({
                    title: 'Copy budget setups?',
                    html: `Copy all budget entries from <strong>${sourceYear}</strong> to <strong>${targetYear}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#17a2b8',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Copy',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('settings.budgetSetup.copy') }}",
                            method: 'POST',
                            data: $(form).serialize(),
                            success: function(resp) {
                                if (window.toastr) toastr.success(resp.message ||
                                    'Budget setups copied successfully');
                                $('#copy_fiscal_year_modal').modal('hide');
                                form.reset();
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                if (window.toastr) toastr.error(xhr.responseJSON
                                    ?.message || 'Failed to copy budget setups');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
