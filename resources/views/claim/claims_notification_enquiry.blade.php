@extends('layouts.app')

@section('content')
    <style>
        @media (min-width: 992px) {
            .app-content {
                min-height: calc(100vh - 7.5rem);
            }
        }

        .page-title-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin: 0 0 1.5rem 0;
            padding: 11px;
            border: 1px solid #e2e8f0;
        }

        .stage-indicator {
            margin-bottom: 0;
        }

        .workflow-steps {
            display: flex;
            align-items: center;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .step.active {
            background: #dc2626;
            color: white;
        }

        .step.completed {
            background: #059669;
            color: white;
        }

        .step.pending {
            background: #f3f4f6;
            color: #6b7280;
        }

        .step-arrow {
            color: #9ca3af;
            font-size: 12px;
        }

        .bg-modern-primary {
            background: #3b82f6 !important;
        }

        .bg-modern-warning {
            background: #f59e0b !important;
        }

        .bg-modern-success {
            background: #10b981 !important;
        }

        .bg-modern-secondary {
            background: #8b5cf6 !important;
        }

        .bg-modern-dark {
            background: #000 !important;
        }

        .border-none {
            border: none !important;
        }

        .customer-option {
            padding: 5px 0;
        }

        .customer-name {
            font-size: 14px;
        }

        .select2-results__option--selected .customer-option {
            color: white !important;
        }

        .select2-results__option.select2-results__option--selectable.select2-results__option--selected .customer-option .customer-details {
            color: white !important;
        }

        .customer-details {
            font-size: 12px;
            line-height: 11px;
            color: var(--text-muted);
        }

        .customer-details i {
            width: 14px;
            font-size: 12px;
        }

        .customer-info-panel {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 16px;
            margin-top: 8px;
        }

        .c-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 14px;
            color: #374151;
        }

        .c-detail {
            display: flex;
            justify-content: space-between;
        }

        .c-detail strong {
            color: #1f2937;
        }

        .c-customer-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }
    </style>

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Claims Notifications</h1>
            <p class="text-muted mb-0 pt-1">Manage initial claim notifications before debit creation</p>
        </div>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href>Claims Administration</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Add New Claim
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="page-title-section">
        <div class="stage-indicator">
            <div class="workflow-steps">
                <div class="step active">
                    <i class="bx bx-bell"></i>
                    <span>Notification</span>
                </div>
                <i class="bx bx-right-arrow step-arrow"></i>
                <div class="step">
                    <i class="bx bx-file"></i>
                    <span>Debit Creation</span>
                </div>
                <i class="bx bx-right-arrow step-arrow"></i>
                <div class="step">
                    <i class="bx bx-check-circle"></i>
                    <span>Claims Enquiry</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            {{-- <button class="btn btn-sm btn-secondary">
                <i class="bx bx-download"></i>
                Export
            </button> --}}
            <button type="button" class="btn btn-outline-danger mb-0 d-none" id="bulk-delete-btn">
                <i class='bx bx-trash'></i>
                Delete Selected
            </button>
            <button type="button" class="btn btn-md btn-dark mb-0" id="newClaimBtn">
                <i class='bx bx-plus'></i>
                Add New Claim
            </button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="row" id="dashboard-cards">
                <div class="col-md-3">
                    <div class="card custom-card dashboard-card" data-card-type="active_claims">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Active Claims</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold" id="active-claims-count">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                        </span>
                                        <span class="fs-12 ms-2" id="active-claims-trend">
                                            <span class="text-muted">Loading...</span>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="avatar avatar-md avatar-rounded bg-modern-primary text-white fs-18">
                                        <i class="bi bi-bell fs-16"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card custom-card dashboard-card" data-card-type="pending_settlement">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Pending Settlement</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold" id="pending-settlement-count">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                        </span>
                                        <span class="fs-12 ms-2" id="pending-settlement-trend">
                                            <span class="text-muted">Loading...</span>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="avatar avatar-md avatar-rounded bg-modern-warning text-white fs-18">
                                        <i class="bi bi-clock fs-16"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card custom-card dashboard-card" data-card-type="reserved_claims">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Reserved Claims</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold" id="reserved-claims-count">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                        </span>
                                        <span class="fs-12 ms-2" id="reserved-claims-trend">
                                            <span class="text-muted">Loading...</span>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="avatar avatar-md avatar-rounded bg-modern-success text-white fs-18">
                                        <i class="bi bi-check fs-16"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card custom-card dashboard-card" data-card-type="estimated_reserve">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Estimated Reserve</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold" id="estimated-reserve-count">
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                        </span>
                                        <span class="fs-12 ms-2" id="estimated-reserve-trend">
                                            <span class="text-muted">Loading...</span>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="avatar avatar-md avatar-rounded bg-modern-secondary text-white fs-18">
                                        <i class="bi bi-wallet fs-16"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#claims-list">Claims</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#reserved-list">Reserved</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content border-none">
                        <div class="tab-pane border-none active" id="claims-list">
                            <div class="card border-none">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <table id="claimlist-table"
                                                class="table text-nowrap table-hover table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Notification No</th>
                                                        <th>Cover No</th>
                                                        <th>Endorsement No </th>
                                                        <th>Business Type</th>
                                                        <th>Line of Business</th>
                                                        <th>Created At</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane border-none" id="reserved-list">
                            <div class="card border-none">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <input type="text" name="intimation_no" id="clm_intimation_no" hidden>
                                        <div class="table-responsive">
                                            <table id="reseverd-list-table"
                                                class="table text-nowrap table-hover table-striped" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Notification No</th>
                                                        <th>Cover No</th>
                                                        <th>Endorsement No </th>
                                                        <th>Business Type</th>
                                                        <th>Line of Business</th>
                                                        <th>Created At</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade effect-scale md-wrapper" id="cardDetailsModal" tabindex="-1"
        aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cardDetailsModalLabel">Card Details</h5>
                    <button type="button" class="btn-close btn-close-white text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover card-details-table">
                            <thead>
                                <tr>
                                    <th>Intimation No</th>
                                    <th>Customer</th>
                                    <th>Cover No</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Reserve Amount</th>
                                </tr>
                            </thead>
                            <tbody id="cardDetailsTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade effect-scale md-wrapper" id="claimsNotificationModal" tabindex="-1"
        data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="claimsNotificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="claimsNotificationModalLabel">
                        New Claim Notification</h5>
                    <button type="button" class="btn-close btn-close-white text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-3">
                    <form id="claimsNotificationForm" method="POST" action="{{ route('claim.notification.register') }}"
                        novalidate>
                        @csrf

                        <div class="row mb-2">
                            <div class="col-12">
                                <h6 class="fw-semibold text-dark mb-2 pb-2 border-bottom">Customer Selection</h6>
                                <div class="mb-2">
                                    <label for="customer_id" class="form-label">Select Existing Customer</label>
                                    <select class="form-inputs select2 @error('customer_id') is-invalid @enderror"
                                        id="customer_id" name="customer_id">
                                        <option value="">Select a customer or enter new details below</option>
                                    </select>
                                    <div class="form-text">Choose an existing customer to auto-fill policy and insured
                                        details</div>
                                    @error('customer_id')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="policyAndClaimDetailsSection">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <h6 class="fw-semibold text-dark mb-2 pb-2 border-bottom">Policy Information</h6>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="cover_type" class="form-label">
                                        Cover Policy <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-inputs select2 @error('cover_type') is-invalid @enderror"
                                        id="cover_type" name="cover_type" required placeholder="Select cover policy">
                                        <option value="" disabled>Select cover policy</option>
                                    </select>
                                    @error('cover_type')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="endorsement_number" class="form-label">Endorsement Number <span
                                            class="text-danger">*</span></label>
                                    <select class="form-inputs select2 @error('endorsement_number') is-invalid @enderror"
                                        id="endorsement_number" name="endorsement_number">
                                        <option value="">Select a customer or enter new details below</option>
                                    </select>
                                    @error('endorsement_number')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="cover_from" class="form-label">
                                        Cover Period From
                                    </label>
                                    <input type="date" class="form-inputs @error('cover_from') is-invalid @enderror"
                                        id="cover_from" name="cover_from" value="{{ old('cover_from') }}" required
                                        readonly>
                                    @error('cover_from')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success fw-medium d-none" id="coverFromAutoFill">Auto-filled from
                                        customer profile</small>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="cover_to" class="form-label">
                                        Cover Period To
                                    </label>
                                    <input type="date" class="form-inputs @error('cover_to') is-invalid @enderror"
                                        id="cover_to" name="cover_to" value="{{ old('cover_to') }}" required readonly>
                                    @error('cover_to')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success fw-medium d-none" id="coverToAutoFill">Auto-filled from
                                        customer profile</small>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="insured_name" class="form-label">
                                        Insured Name
                                    </label>
                                    <input type="text" class="form-inputs @error('insured_name') is-invalid @enderror"
                                        id="insured_name" name="insured_name" value="{{ old('insured_name') }}" required
                                        readonly>
                                    @error('insured_name')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success fw-medium d-none" id="insuredNameAutoFill">Auto-filled from
                                        customer profile</small>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-12">
                                    <h6 class="fw-semibold text-dark mb-2 pb-2 border-bottom">Claim Details</h5>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="date_of_loss" class="form-label">
                                        Date of Loss <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-inputs @error('date_of_loss') is-invalid @enderror"
                                        id="date_of_loss" name="date_of_loss" value="{{ old('date_of_loss') }}"
                                        required>
                                    @error('date_of_loss')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="cedant_claim_no" class="form-label">
                                        Cedant Claim Number
                                    </label>
                                    <input type="text"
                                        class="form-inputs @error('cedant_claim_no') is-invalid @enderror"
                                        id="cedant_claim_no" name="cedant_claim_no" value="{{ old('cedant_claim_no') }}"
                                        required placeholder="CLM-2025-001">
                                    @error('cedant_claim_no')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="date_reported" class="form-label">
                                        Date Reported <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-inputs @error('date_reported') is-invalid @enderror"
                                        id="date_reported" name="date_reported"
                                        value="{{ old('date_reported', date('Y-m-d')) }}" required>
                                    @error('date_reported')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="date_notified" class="form-label">Date Notified to Reinsurer <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-inputs @error('date_notified') is-invalid @enderror"
                                        id="date_notified" name="date_notified" value="{{ old('date_notified') }}"
                                        required>
                                    @error('date_notified')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-2">
                                    <label for="cause_of_loss" class="form-label">
                                        Cause of Loss <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-inputs @error('cause_of_loss') is-invalid @enderror" id="cause_of_loss" name="cause_of_loss"
                                        rows="4" required placeholder="Provide detailed description of what caused the loss...">{{ old('cause_of_loss') }}</textarea>
                                    @error('cause_of_loss')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-2">
                                    <label for="loss_description" class="form-label">
                                        Loss Description <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-inputs @error('loss_description') is-invalid @enderror" id="loss_description"
                                        name="loss_description" rows="4" required
                                        placeholder="Provide comprehensive description of the loss, damages, and circumstances...">{{ old('loss_description') }}</textarea>
                                    @error('loss_description')
                                        <div class="invalid-feedback mt-0">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div></div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-dark" onclick="submitForm()">
                                <i class="bx bx-paper-plane me-1"></i> Submit Notification
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let selectedCustomerData = null;
            const $customerId = $("#customer_id");

            filterDateOfLoss()

            initializeDataTables();

            loadDashboardStats();

            setInterval(loadDashboardStats, 30000);

            function initializeDataTables() {
                const commonConfig = {
                    order: [
                        [6, "asc"]
                    ],
                    pageLength: 15,
                    lengthMenu: [15, 30, 50, 100],
                    processing: true,
                    serverSide: true,
                    bAutoWidth: false,
                    lengthChange: true,
                    responsive: true,
                    columns: [{
                            data: "intimation_no",
                            searchable: true,
                            title: "Notification No"
                        },
                        {
                            data: "cover_no",
                            searchable: true,
                            title: "Cover No"
                        },
                        {
                            data: "endorsement_no",
                            searchable: true,
                            title: "Endorsement No"
                        },
                        {
                            data: "type_of_bus",
                            searchable: false,
                            title: "Business Type"
                        },
                        {
                            data: "class_desc",
                            searchable: false,
                            title: "Line of Business"
                        },
                        {
                            data: "created_at",
                            searchable: false,
                            title: "Created At"
                        },
                        {
                            data: "status",
                            searchable: false,
                            sortable: false,
                            title: "Status"
                        },
                        {
                            data: "action",
                            sortable: false,
                            title: "Action",
                            orderable: false
                        }
                    ],
                    language: {
                        processing: "Loading data...",
                        emptyTable: "No data available",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        search: "Search:",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    drawCallback: function(settings) {
                        // Re-initialize tooltips or any other UI components after table redraw
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                };

                if ($.fn.DataTable.isDataTable('#claimlist-table')) {
                    $('#claimlist-table').DataTable().destroy();
                }

                $("#claimlist-table").DataTable({
                    ...commonConfig,
                    ajax: {
                        url: "{{ route('claim.notification.enquiry.datatable') }}",
                        type: "GET",
                        data: function(d) {
                            d.type = 'claims';
                            return d;
                        },
                        error: function(xhr, error, code) {
                            console.error('Claims DataTable Error:', error);
                            toastr.error('Failed to load claims data', 'Error');
                        }
                    },
                });

                if ($.fn.DataTable.isDataTable('#reseverd-list-table')) {
                    $('#reseverd-list-table').DataTable().destroy();
                }

                $("#reseverd-list-table").DataTable({
                    ...commonConfig,
                    ajax: {
                        url: "{{ route('claim.notification.enquiry.datatable') }}",
                        type: "GET",
                        data: function(d) {
                            d.type = 'reserved';
                            return d;
                        },
                        error: function(xhr, error, code) {
                            console.error('Reserved DataTable Error:', error);
                            toastr.error('Failed to load reserved data', 'Error');
                        }
                    },
                });
            }

            // $('#claimlist-table thead tr').prepend('<th><input type="checkbox" id="select-all-claims"></th>');
            // $('#reseverd-list-table thead tr').prepend('<th><input type="checkbox" id="select-all-reserved"></th>');

            // // Handle select all for claims table
            // $(document).on('change', '#select-all-claims', function() {
            //     const isChecked = $(this).is(':checked');
            //     $('#claims-list .row-checkbox').prop('checked', isChecked).trigger('change');
            // });

            // // Handle select all for reserved table
            // $(document).on('change', '#select-all-reserved', function() {
            //     const isChecked = $(this).is(':checked');
            //     $('#reserved-list .row-checkbox').prop('checked', isChecked).trigger('change');
            // });

            // // Update select all checkbox when individual rows are checked/unchecked
            // $(document).on('change', '.row-checkbox', function() {
            //     const activeTab = $('.nav-link.active').attr('href');
            //     let allCheckboxes, selectAllCheckbox;

            //     if (activeTab === '#claims-list') {
            //         allCheckboxes = $('#claims-list .row-checkbox');
            //         selectAllCheckbox = $('#select-all-claims');
            //     } else {
            //         allCheckboxes = $('#reserved-list .row-checkbox');
            //         selectAllCheckbox = $('#select-all-reserved');
            //     }

            //     const checkedCount = allCheckboxes.filter(':checked').length;
            //     const totalCount = allCheckboxes.length;

            //     if (checkedCount === 0) {
            //         selectAllCheckbox.prop('indeterminate', false).prop('checked', false);
            //     } else if (checkedCount === totalCount) {
            //         selectAllCheckbox.prop('indeterminate', false).prop('checked', true);
            //     } else {
            //         selectAllCheckbox.prop('indeterminate', true);
            //     }
            // });

            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr("href");
                if (target === '#claims-list' && $.fn.DataTable.isDataTable('#claimlist-table')) {
                    $('#claimlist-table').DataTable().columns.adjust().responsive.recalc();
                } else if (target === '#reserved-list' && $.fn.DataTable.isDataTable(
                        '#reseverd-list-table')) {
                    $('#reseverd-list-table').DataTable().columns.adjust().responsive.recalc();
                }
            });

            $("#claimsNotificationModal").on("shown.bs.modal", function() {
                $customerId.select2({
                    dropdownParent: $("#claimsNotificationModal"),
                    placeholder: "Select a customer or enter new details below",
                    allowClear: false,
                    width: "100%",
                    templateResult: formatCustomerOption,
                    templateSelection: formatCustomerSelection,
                    dropdownCssClass: 'custom-dropdown-height',
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    ajax: {
                        url: "{{ route('claim.notification.get-customers') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                            };
                        },
                        beforeSend: function() {},
                        processResults: function(data, params) {
                            let filteredResults = data.data;

                            if (params.term) {
                                const searchTerm = params.term.toLowerCase();
                                filteredResults = data?.data.filter(function(customer) {
                                    return customer.name.toLowerCase().includes(
                                            searchTerm) ||
                                        customer.email.toLowerCase().includes(
                                            searchTerm) ||
                                        (customer.telephone && customer.telephone
                                            .includes(
                                                searchTerm));
                                });
                            }

                            return populateCustomerDropdown(filteredResults);
                        },
                        complete: function() {
                            $('#customer_id').data('select2').$container.removeClass('loading');
                        }
                    },
                    language: {
                        searching: function() {
                            return "Loading…";
                        }
                    }
                });
            });

            function populateCustomerDropdown(customers) {
                if (!customers || !Array.isArray(customers) || customers.length === 0) {
                    return {
                        results: [{
                            id: "",
                            text: "No customers found",
                            disabled: true,
                            customer: null
                        }]
                    };
                }

                const processedResults = customers.map((customer) => {
                    return {
                        id: customer.customer_id,
                        text: customer.name || "",
                        customer: customer
                    };
                });

                return {
                    results: processedResults
                };
            }

            function formatCustomerSelection(customer) {
                return customer.text || "Select a customer";
            }

            function formatCustomerOption(customer) {
                if (!customer.id) {
                    return customer.text;
                }

                const data = customer.customer;

                if (!data) {
                    return customer.text;
                }

                return $(`
                    <div class="customer-option">
                        <div class="customer-name fw-medium">${data.name}</div>
                        <div class="customer-details small">
                            ${
                            data.email
                                ? `<span class="me-2"><i class="bx bx-envelope"></i> ${data.email}</span>`
                                : ""
                            }
                            ${
                            data.telephone
                                ? `<span class="me-2"><i class="bx bx-phone"></i> ${data.telephone}</span>`
                                : ""
                            }
                            ${
                            data.business_type
                                ? `<span><i class="bx bx-briefcase"></i> ${data.business_type}</span>`
                                : ""
                            }
                        </div>
                    </div>
                `);
            }

            $('#customer_id').on('select2:select', function(e) {
                const customerData = e.params.data.customer;
                if (!customerData) {
                    return customer.text;
                }

                selectedCustomerData = customerData;

                showCustomerInfoPanel(customerData);
                populateFormFields(customerData);
            });

            $("#cover_type").on("change", function() {
                if (selectedCustomerData) {
                    populateCoverRelatedFields();
                }
            });

            $("#date_of_loss").on("change", function() {
                if (selectedCustomerData) {
                    updateEndorsementBasedOnDateOfLoss();
                }
            });

            $("#endorsement_number").on("change", function() {
                if (selectedCustomerData) {
                    updateFieldsBasedOnEndorsement();
                }
            });

            function showCustomerInfoPanel(customerData) {
                function formatCovers(covers) {
                    if (!Array.isArray(covers)) return "0";
                    if (covers.length === 0) return "0";
                    if (covers.length === 1) return "1 policy";
                    return covers.length + " policies";
                }

                const customerInfoHtml = `
                    <div class="customer-info-panel">
                        <div class="c-details">
                            <div class="c-detail">
                                <strong>Name:</strong>
                                <span>${customerData.name || "N/A"}</span>
                            </div>
                            <div class="c-detail">
                                <strong>Email:</strong>
                                <span>${customerData.email || "N/A"}</span>
                            </div>
                            <div class="c-detail">
                                <strong>Phone:</strong>
                                <span>${customerData.telephone || "N/A"}</span>
                            </div>
                            <div class="c-detail">
                                <strong>Business Type:</strong>
                                <span>${customerData.business_type || "N/A"}</span>
                            </div>
                            <div class="c-detail">
                                <strong>Covers:</strong>
                                <span>${formatCovers(customerData.covers)}</span>
                            </div>
                        </div>
                        <button type="button" class="c-customer-btn" id="clearCustomerSelection">
                            Clear Selection
                        </button>
                    </div>
                `;

                const $parent = $("#customer_id").parent();
                $parent.find(".customer-info-panel").remove();
                $parent.append(customerInfoHtml);
            }


            function formatCovers(covers) {
                if (!Array.isArray(covers)) return "0";
                if (covers.length === 0) return "0";
                if (covers.length === 1) return "1 policy";
                return covers.length + " policies";
            }

            $("body").on("click", "#clearCustomerSelection", function() {
                selectedCustomerData = null;

                $(".customer-info-panel").remove();

                const fieldsToClear = [
                    "#cover_type",
                    "#endorsement_number",
                    "#insured_name",
                    "#business_type",
                    "#cover_from",
                    "#cover_to",
                ];

                fieldsToClear.forEach((field) => {
                    $(field).val("").trigger("change");
                });

                $("#cover_type").find("option:not(:first)").remove();
                $("#endorsement_number").find("option:not(:first)").remove();

                $(".text-success.fw-medium").addClass("d-none");

                $("#customer_id").val("").trigger("change");
            })

            function populateFormFields(customerData) {
                if (customerData.covers && Array.isArray(customerData.covers)) {
                    populateCoverTypeDropdown(customerData.covers);
                    showAutoFillIndicator("#coverTypeAutoFill");
                }

                if (customerData.covers && Array.isArray(customerData.covers)) {
                    const latestCover = customerData.covers.length > 0 ? customerData.covers[0] : null;
                    populateEndorsementDropdown(customerData.covers, latestCover.cover_no);
                }
            }

            function populateCoverTypeDropdown(covers) {
                const $coverDropdown = $("#cover_type");
                $coverDropdown.find("option:not(:first)").remove();

                if (covers && covers.length > 0) {
                    covers.forEach((cover) => {
                        const displayText =
                            `${cover.cover_no} - ${cover.cover_type || ""} - ${cover.insured_name || ""}`;
                        const option = new Option(displayText, cover.cover_no || "");
                        $(option).attr("data-cover", JSON.stringify(cover));
                        $coverDropdown.append(option);
                    });

                    if ($coverDropdown.hasClass("select2-hidden-accessible")) {
                        $coverDropdown.trigger("change.select2");
                    }
                }
            }

            function populateCoverRelatedFields() {
                const selectedCoverOption = $("#cover_type option:selected");
                const coverDataStr = selectedCoverOption.attr("data-cover");

                if (!coverDataStr) return;

                try {
                    clearSelectedEndorsement()

                    const coverData = JSON.parse(coverDataStr);
                    populateEndorsementDropdown(
                        selectedCustomerData.covers,
                        coverData.cover_no
                    );
                } catch (error) {
                    console.error("Error parsing cover data:", error);
                }
            }

            function populateEndorsementDropdown(covers, selectedCoverNo = null) {
                const $endorsementDropdown = $("#endorsement_number");
                $endorsementDropdown.find("option:not(:first)").remove();

                if (covers && covers.length > 0) {
                    let relevantCovers = covers;

                    if (selectedCoverNo) {
                        relevantCovers = covers.filter(
                            (cover) => cover.cover_no === selectedCoverNo
                        );
                    }

                    relevantCovers.forEach((cover) => {
                        if (cover.endorsements && Array.isArray(cover.endorsements)) {
                            cover.endorsements.forEach((endorsement) => {
                                const displayText =
                                    `${endorsement.endorsement_no} - ${endorsement.description || ""}`;
                                const option = new Option(displayText, endorsement.endorsement_no);
                                $(option).attr(
                                    "data-endorsement",
                                    JSON.stringify({
                                        ...endorsement,
                                        cover_data: cover,
                                    })
                                );
                                $endorsementDropdown.append(option);
                            });
                        }
                    });

                    if ($endorsementDropdown.hasClass("select2-hidden-accessible")) {
                        $endorsementDropdown.trigger("change.select2");
                    }
                }
            }

            function updateEndorsementBasedOnDateOfLoss() {
                const dateOfLoss = new Date($("#date_of_loss").val());
                if (!dateOfLoss || isNaN(dateOfLoss.getTime())) return;

                const selectedCoverNo = $("#cover_type").val();
                if (!selectedCoverNo || !selectedCustomerData) return;

                const selectedCover = selectedCustomerData.covers.find(
                    (cover) => cover.cover_no === selectedCoverNo
                );

                if (!selectedCover) return;

                const validEndorsements = getValidEndorsementsForDate(
                    selectedCover,
                    dateOfLoss
                );

                if (validEndorsements.length > 0) {
                    const bestEndorsement = validEndorsements[0];
                    // console.log(bestEndorsement)
                    populateEndorsementDropdown(
                        selectedCustomerData.covers,
                        bestEndorsement.cover_no
                    );

                    if ($("#endorsement_number").hasClass("select2-hidden-accessible")) {
                        $("#endorsement_number").trigger("change.select2");
                    }
                }
            }

            function filterDateOfLoss() {
                const today = new Date();
                const formattedDate = today.toISOString().split('T')[0];
                const lossDate = document.getElementById('date_of_loss');
                const insurerNotifyDate = document.getElementById('date_reported');
                const reinsurerNotifyDate = document.getElementById('date_notified');
                lossDate.setAttribute('max', formattedDate);
                insurerNotifyDate.setAttribute('max', formattedDate);
                reinsurerNotifyDate.setAttribute('max', formattedDate);
            }

            function updateFieldsBasedOnEndorsement() {
                const selectedEndorsementOption = $("#endorsement_number option:selected");
                const endorsementDataStr =
                    selectedEndorsementOption.attr("data-endorsement");

                if (!endorsementDataStr) return;

                try {
                    const endorsementData = JSON.parse(endorsementDataStr);
                    const coverData = endorsementData.cover_data;

                    if (endorsementData.effective_from) {
                        $("#cover_from").val(
                            formatDateForInput(endorsementData.effective_from)
                        );
                        showAutoFillIndicator("#coverFromAutoFill");
                    }

                    if (endorsementData.effective_to) {
                        $("#cover_to").val(
                            formatDateForInput(endorsementData.effective_to)
                        );
                        showAutoFillIndicator("#coverToAutoFill");
                    }

                    if (endorsementData.insured_name) {
                        $("#insured_name").val(endorsementData.insured_name);
                        showAutoFillIndicator("#insuredNameAutoFill");
                    }

                    $("#date_of_loss").val(formatDateForInput(endorsementData.lossDate));
                } catch (error) {
                    console.error("Error parsing endorsement data:", error);
                }
            }

            function getValidEndorsementsForDate(cover, dateOfLoss) {
                if (!cover.endorsements) return [
                    cover
                ];

                return cover.endorsements.filter((endorsement) => {
                    if (endorsement.effective_from && endorsement.effective_to) {
                        const effectiveFrom = new Date(endorsement.effective_from);
                        const effectiveTo = new Date(endorsement.effective_to);

                        // return effectiveFrom <= dateOfLoss && effectiveTo >= dateOfLoss;
                        return dateOfLoss >= effectiveFrom && dateOfLoss <= effectiveTo;
                    }
                    return true;
                });
            }

            function formatDateForInput(dateString) {
                if (!dateString) return "";

                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return dateString;

                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, "0");
                    const day = String(date.getDate()).padStart(2, "0");
                    return `${year}-${month}-${day}`;
                } catch (error) {
                    console.error("Error formatting date:", error);
                    return dateString;
                }
            }

            function showAutoFillIndicator(selector) {
                $(selector).removeClass("d-none").fadeIn();
            }

            function clearSelectedEndorsement() {

                $("#date_of_loss").val(new Date().toISOString().split('T')[0])

                const fieldsToClear = [
                    "#insured_name",
                    "#cover_from",
                    "#cover_to",
                ];

                fieldsToClear.forEach((field) => {
                    $(field).val("").trigger("change");
                });
            }

            $(document).on('click', '#delete-claim', function(e) {
                e.preventDefault();

                const intimationNo = $(this).data('intimation_no');
                const claimStatus = $(this).data('status');

                if (claimStatus === 'A' || claimStatus === 'C') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Delete',
                        text: 'This claim cannot be deleted as it has already been processed.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete claim notification: ${intimationNo}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteClaim(intimationNo);
                    }
                });
            });

            $(document).on("click", "#view-notf-claimstatus", function(e) {
                e.preventDefault();
                var intimation_no = $(this).data("intimation_no");
                var process_type = $(this).data("process_type");
                const claimDetailUrl = new URL(
                    "{{ route('claim.notification.claim.detail') }}",
                    window.location.origin
                );

                if (intimation_no !== "") {
                    try {
                        claimDetailUrl.searchParams.set("intimation_no", intimation_no);
                        if (process_type) {
                            claimDetailUrl.searchParams.set("process_type", process_type);
                        }
                        window.location.href = claimDetailUrl.toString();
                    } catch (error) {
                        console.error("URL error:", error);
                        Swal.fire("Error", "Invalid URL parameters", "error");
                    }
                } else {
                    Swal.fire("Error", "No intimation number provided.", "error");
                }
            });

            $("#newClaimBtn").click(function(e) {
                e.preventDefault();
                $("#claimsNotificationModal").modal("show");
            });

            $("#new_claim_form").validate({
                errorClass: "errorClass",
                rules: {
                    customer_id: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $("#next-save-btn").prop("disabled", true).text("Submitting...");
                    form.submit();
                    $("#next-save-btn").prop("disabled", false).text("Next");
                },
            });

            function deleteClaim(intimationNo) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the claim notification.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('claim.notification.delete') }}",
                    type: 'DELETE',
                    data: {
                        intimation_no: intimationNo,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message ||
                                    'Claim notification has been deleted successfully.',
                                confirmButtonColor: '#3085d6',
                                timer: 3000,
                                timerProgressBar: true
                            });

                            // Reload both DataTables
                            reloadDataTables();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Delete Failed',
                                text: response.message ||
                                    'Failed to delete claim notification.',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'An error occurred while deleting the claim.';

                        if (xhr.status === 403) {
                            errorMessage = 'You do not have permission to delete this claim.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Claim notification not found.';
                        } else if (xhr.status === 422) {
                            const errors = xhr.responseJSON?.errors;
                            if (errors) {
                                errorMessage = Object.values(errors).flat().join(', ');
                            } else {
                                errorMessage = xhr.responseJSON?.message || errorMessage;
                            }
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error occurred. Please contact support.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Delete Failed',
                            text: errorMessage,
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }

            let selectedRows = [];

            $(document).on('change', '.row-checkbox', function() {
                const intimationNo = $(this).val();

                if ($(this).is(':checked')) {
                    selectedRows.push(intimationNo);
                } else {
                    selectedRows = selectedRows.filter(row => row !== intimationNo);
                }

                // Show/hide bulk delete button
                if (selectedRows.length > 0) {
                    $('#bulk-delete-btn').removeClass('d-none');
                    $('#bulk-delete-btn').text(`Delete Selected (${selectedRows.length})`);
                } else {
                    $('#bulk-delete-btn').addClass('d-none');
                }
            });

            $(document).on('click', '#bulk-delete-btn', function(e) {
                e.preventDefault();

                if (selectedRows.length === 0) {
                    toastr.warning('Please select claims to delete', 'No Selection');
                    return;
                }

                Swal.fire({
                    title: 'Delete Multiple Claims?',
                    text: `You are about to delete ${selectedRows.length} claim notification(s). This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: `Yes, delete ${selectedRows.length} claims!`,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkDeleteClaims(selectedRows);
                    }
                });
            });

            function bulkDeleteClaims(intimationNumbers) {
                Swal.fire({
                    title: 'Deleting Claims...',
                    text: 'Please wait while we delete the selected claims.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('claim.notification.bulk-delete') }}",
                    type: 'DELETE',
                    data: {
                        intimation_nos: intimationNumbers,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: `${response.deleted_count} claim(s) deleted successfully.`,
                                confirmButtonColor: '#3085d6'
                            });

                            // Clear selected rows and hide bulk delete button
                            selectedRows = [];
                            $('#bulk-delete-btn').addClass('d-none');
                            $('.row-checkbox').prop('checked', false);

                            // Reload DataTables
                            reloadDataTables();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Delete Failed',
                                text: response.message || 'Failed to delete selected claims.',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Bulk Delete Failed',
                            text: 'An error occurred while deleting the selected claims.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                });
            }

            $(document).on('click', '.dashboard-card', function() {
                const cardType = $(this).data('card-type');
                showCardDetails(cardType);
            });

            $(document).on('claims:updated', function() {
                loadDashboardStats();
            });
        });

        function loadDashboardStats() {
            $.ajax({
                url: "{{ route('claim.notification.dashboard-stats') }}",
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        updateDashboardCards(response.data);
                    } else {
                        showDashboardError();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Dashboard stats error:', error);
                    showDashboardError();
                }
            });
        }

        function updateDashboardCards(data) {
            // Active Claims Card
            $('#active-claims-count').html(data.active_claims.count);
            const activeClaimsTrendIcon = data.active_claims.trend >= 0 ? 'ti-trending-up' : 'ti-trending-down';
            const activeClaimsTrendClass = data.active_claims.trend >= 0 ? 'text-success' : 'text-danger';
            $('#active-claims-trend').html(
                `<i class="${activeClaimsTrendIcon} me-1 d-inline-block"></i>
             <span class="${activeClaimsTrendClass}">${data.active_claims.trend_text}</span>`
            );

            // Pending Settlement Card
            $('#pending-settlement-count').html(data.pending_settlement.count);
            const settlementIcon = data.pending_settlement.count > 0 ? 'ti-alert-circle' : 'ti-check';
            const settlementClass = data.pending_settlement.count > 0 ? 'text-warning' : 'text-success';
            $('#pending-settlement-trend').html(
                `<i class="${settlementIcon} me-1 d-inline-block"></i>
             <span class="${settlementClass}">${data.pending_settlement.trend_text}</span>`
            );

            // Reserved Claims Card
            $('#reserved-claims-count').html(data.reserved_claims.count);
            const reservedClass = data.reserved_claims.count > 0 ? 'text-info' : 'text-success';
            $('#reserved-claims-trend').html(
                `<span class="${reservedClass}">${data.reserved_claims.trend_text}</span>`
            );

            // Estimated Reserve Card
            $('#estimated-reserve-count').html(data.estimated_reserve.count);
            const reserveIcon = data.estimated_reserve.raw_amount > 0 ? 'ti-wallet' : 'ti-check';
            const reserveClass = data.estimated_reserve.raw_amount > 0 ? 'text-info' : 'text-success';
            $('#estimated-reserve-trend').html(
                `<span class="${reserveClass}">${data.estimated_reserve.trend_text}</span>`
            );

            // Add pulse animation to updated cards
            $('.dashboard-card').addClass('updated-card');
            setTimeout(() => {
                $('.dashboard-card').removeClass('updated-card');
            }, 1000);
        }

        function showDashboardError() {
            $('#active-claims-count').html('<i class="ti ti-alert-triangle text-danger"></i>');
            $('#active-claims-trend').html('<span class="text-muted">Error loading data</span>');

            $('#pending-settlement-count').html('<i class="ti ti-alert-triangle text-danger"></i>');
            $('#pending-settlement-trend').html('<span class="text-muted">Error loading data</span>');

            $('#reserved-claims-count').html('<i class="ti ti-alert-triangle text-danger"></i>');
            $('#reserved-claims-trend').html('<span class="text-muted">Error loading data</span>');

            $('#estimated-reserve-count').html('<i class="ti ti-alert-triangle text-danger"></i>');
            $('#estimated-reserve-trend').html('<span class="text-muted">Error loading data</span>');
        }

        function showCardDetails(cardType) {
            const cardTitles = {
                'active_claims': 'Active Claims Details',
                'pending_settlement': 'Pending Settlement Details',
                'reserved_claims': 'Reserved Claims Details',
                'estimated_reserve': 'Reserve Details'
            };

            $('#cardDetailsModalLabel').text(cardTitles[cardType] || 'Card Details');

            // Show loading in modal
            $('#cardDetailsTableBody').html(`
            <tr>
                <td colspan="6" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `);

            // Show modal
            $('#cardDetailsModal').modal('show');

            // Load details
            $.ajax({
                url: "{{ route('claim.notification.card-details') }}",
                type: 'GET',
                data: {
                    type: cardType
                },
                success: function(response) {
                    if (response.success) {
                        populateCardDetails(response.data);
                    } else {
                        showCardDetailsError();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Card details error:', error);
                    showCardDetailsError();
                }
            });
        }

        function populateCardDetails(data) {
            if (data.length === 0) {
                $('#cardDetailsTableBody').html(`
                <tr>
                    <td colspan="6" class="text-center text-muted">No data available</td>
                </tr>
            `);
                return;
            }

            let html = '';
            data.forEach(function(item) {
                const statusBadge = getStatusBadge(item.status);
                const reserveAmount = item.reserve_amount ? `$${item.reserve_amount}` : '-';

                html += `
                <tr class="clickable-row" data-intimation="${item.intimation_no}">
                    <td>${item.intimation_no}</td>
                    <td>${item.customer_name}</td>
                    <td>${item.cover_no}</td>
                    <td>${statusBadge}</td>
                    <td>${item.created_at}</td>
                    <td>${reserveAmount}</td>
                </tr>
            `;
            });

            $('#cardDetailsTableBody').html(html);
        }

        function showCardDetailsError() {
            $('#cardDetailsTableBody').html(`
            <tr>
                <td colspan="6" class="text-center text-danger">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Error loading details
                </td>
            </tr>
        `);
        }

        function getStatusBadge(status) {
            const badges = {
                'P': '<span class="badge bg-warning">Pending</span>',
                'A': '<span class="badge bg-success">Approved</span>',
                'R': '<span class="badge bg-info">Reserved</span>',
                'C': '<span class="badge bg-secondary">Closed</span>',
                'X': '<span class="badge bg-danger">Cancelled</span>'
            };
            return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
        }

        // Handle clicks on detail rows to navigate to claim details
        $(document).on('click', '.clickable-row', function() {
            const intimationNo = $(this).data('intimation');
            if (intimationNo) {
                window.location.href = "{{ route('claim.notification.claim.detail') }}?intimation_no=" +
                    intimationNo;
            }
        });

        function reloadDataTables() {
            if ($.fn.DataTable.isDataTable('#claimlist-table')) {
                $('#claimlist-table').DataTable().ajax.reload(null, false);
            }
            if ($.fn.DataTable.isDataTable('#reseverd-list-table')) {
                $('#reseverd-list-table').DataTable().ajax.reload(null, false);
            }
        }

        function submitForm() {
            const requiredFields = [
                "#customer_id",
                "#cover_type",
                "#date_of_loss",
                "#date_reported",
                "#date_notified",
                "#cause_of_loss",
                "#loss_description",
            ];
            let isValid = true;

            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").hide();

            requiredFields.forEach((field) => {
                const $field = $(field);
                const value = $field.val();

                if (!value || value.trim() === "") {
                    $field.addClass("is-invalid");
                    isValid = false;
                } else {
                    $field.removeClass("is-invalid");
                }
            });

            if (!isValid) {
                toastr.error("Please fill in all required fields", "Validation Error");
                return;
            }

            const $submitButton = $('[onclick="submitForm()"]');
            const originalText = $submitButton.html();
            $submitButton.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Submitting...');

            const formData = new FormData($('#claimsNotificationForm')[0]);

            $.ajax({
                url: $('#claimsNotificationForm').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Claim notification submitted successfully!', 'Success');

                        $('#claimsNotificationModal').modal('hide');

                        $('#claimsNotificationForm')[0].reset();
                        $('.select2').val('').trigger('change');
                        $('.customer-info-panel').remove();
                        selectedCustomerData = null;

                        $(document).trigger('claims:updated');
                        loadDashboardStats(); // Immediate refresh

                        reloadDataTables();

                        // console.log(response.redirect_url);

                        setTimeout(() => {
                            window.location.href = response.redirect_url;
                        }, 1500);
                    } else {
                        toastr.error('Something went wrong', 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            const $field = $(`#${field}`);
                            $field.addClass('is-invalid');

                            let $feedback = $field.siblings('.invalid-feedback');
                            if ($feedback.length === 0) {
                                $feedback = $('<div class="invalid-feedback mt-0"></div>');
                                $field.after($feedback);
                            }
                            $feedback.text(errors[field][0]).show();
                        });

                        toastr.error('Please correct the validation errors', 'Validation Failed');
                    } else if (xhr.status === 500) {
                        toastr.error('Internal server error. Please try again.', 'Server Error');
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'An error occurred. Please try again.',
                            'Error');
                    }
                },
                complete: function() {
                    $submitButton.prop('disabled', false).html(originalText);
                }
            });
        }
    </script>
@endpush
