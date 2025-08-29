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

        .select2-results__option.select2-results__option--selectable.select2-results__option--selected .customer-option {
            color: white !important;
        }

        .customer-details {
            font-size: 13px;
            line-height: 11px;
            color: var(--text-muted);
        }

        .customer-details i {
            width: 14px;
            font-size: 13px;
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

        .policyAndClaimDetailsSection {
            /* height: calc(100vh - 32rem);
                                                                        overflow-x: hidden;
                                                                        overflow-y: auto; */
        }
    </style>

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">Claims Enquiry</h1>
            <p class="text-muted mb-0 pt-1">Manage active claims after debit creation</p>
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
            <button type="button" class="btn btn-md btn-dark mb-0" id="newClaimBtn">
                <i class='bx bx-plus'></i>
                Add New Claim
            </button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Active Claims</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold">2</span>
                                        <span class="fs-12 text-success ms-2"><i
                                                class="ti ti-trending-up me-1 d-inline-block"></i>+2 this week</span>
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
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Pending Settlement</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold">2</span>
                                        <span class="fs-12 text-success ms-2"><i
                                                class="ti ti-alert-circle me-1 d-inline-block"></i>Requires attention</span>
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
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Reserved Claims</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold">12</span>
                                        <span class="fs-12 text-success ms-2">Can proceed to next stage</span>
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
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-top justify-content-between">
                                <div class="flex-fill">
                                    <p class="mb-0 text-muted">Estimated Reserve</p>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-5 fw-semibold">773</span>
                                        <span class="fs-12 text-success ms-2">Pending notifications</span>
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
            {{-- <ul class="nav nav-pills mb-3" role="tablist" id="approvalTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page" href="#claims-list"
                        data-type="claims-list" aria-selected="true">Claims</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#reserve-tab"
                        data-type="fac" aria-selected="false">Reserved</a>
                </li>
            </ul> --}}

            {{-- <div class="tab-content" id="approvalTabsContent">
                <div class="tab-pane show active" id="claims-list" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Claims Overview</div>
                        </div>
                        <div class="card-body py-3 px-2">
                            {!! html()->form('GET', route('claim.notification.claim_detail'))->id('form_claim_datatable')->open() !!}
                            <input type="text" name="intimation_no" id="clm_intimation_no" hidden>
                            <div class="table-responsive">
                                <table id="claimlist-table" class="table text-nowrap table-hover table-striped"
                                    style="width:100%">
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
                            {{ csrf_field() }}
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                </div>
                <div class="tab-pane text-muted" id="reserve-tab" role="tabpanel">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Reserved List</div>
                        </div>
                        <div class="card-body py-3 px-2">
                            {!! html()->form('GET', route('claim.notification.claim_detail'))->id('form_claim_datatable')->open() !!}
                            <input type="text" name="intimation_no" id="clm_intimation_no" hidden>
                            <div class="table-responsive">
                                <table id="reseverd-list-table" class="table text-nowrap table-hover table-striped"
                                    style="width:100%">
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
                            {{ csrf_field() }}
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                </div>
            </div> --}}

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
                                        {{-- {!! html()->form('GET', route('claim.notification.claim_detail'))->id('form_claim_datatable')->open() !!} --}}
                                        {{-- <input type="text" name="intimation_no" id="clm_intimation_no" hidden> --}}
                                        <div class="table-responsive">
                                            <table id="claimlist-table" class="table text-nowrap table-hover table-striped"
                                                style="width:100%">
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
                                        {{-- {{ csrf_field() }} --}}
                                        {{-- {{ html()->form()->close() }} --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane border-none" id="reserved-list">
                            <div class="card border-none">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        {{-- {!! html()->form('GET', route('claim.notification.claim_detail'))->id('form_claim_datatable')->open() !!} --}}
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
                                        {{-- {{ csrf_field() }} --}}
                                        {{-- {{ html()->form()->close() }} --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="claimsNotificationModal" tabindex="-1" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="claimsNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="claimsNotificationModalLabel">
                        New Claim Notification</h5>
                    <button type="button" class="btn-close btn-close-white text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-3">
                    {{-- {{ route('claims.store') }} --}}

                    <form id="claimsNotificationForm" method="POST" action="" novalidate>
                        @csrf

                        <div class="row mb-2">
                            <div class="col-12">
                                <h6 class="fw-semibold text-dark mb-2 pb-2 border-bottom">Customer Selection</h6>
                                <div class="mb-2">
                                    <label for="customer_id" class="form-label">Select Existing Customer</label>
                                    <select class="form-inputs select2 @error('customer_id') is-invalid @enderror"
                                        id="customer_id" name="customer_id" onchange="populateCustomerData()">
                                        <option value="">Select a customer or enter new details below</option>
                                    </select>
                                    <div class="form-text">Choose an existing customer to auto-fill policy and insured
                                        details</div>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="policyAndClaimDetailsSection customScrollBar">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <h6 class="fw-semibold text-dark mb-2 pb-2 border-bottom">Policy Information</h6>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="cover_type" class="form-label">
                                        Cover Policy <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-inputs select2 @error('cover_type') is-invalid @enderror"
                                        id="cover_type" name="cover_type" required>
                                        <option value="">Select Cover Policy</option>
                                    </select>
                                    @error('cover_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success fw-medium d-none" id="coverTypeAutoFill">Auto-filled from
                                        customer profile</small>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="endorsement_number" class="form-label">Endorsement Number <span
                                            class="text-danger">*</span></label>
                                    <select class="form-inputs select2 @error('endorsement_number') is-invalid @enderror"
                                        id="endorsement_number" name="endorsement_number">
                                        <option value="">Select a customer or enter new details below</option>
                                    </select>
                                    @error('endorsement_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success fw-medium d-none" id="coverTypeAutoFill">Auto-filled from
                                        customer profile</small>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="cover_from" class="form-label">
                                        Cover Period From
                                    </label>
                                    <input type="date" class="form-inputs @error('cover_from') is-invalid @enderror"
                                        id="cover_from" name="cover_from" value="{{ old('cover_from') }}" required>
                                    @error('cover_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success fw-medium d-none" id="coverFromAutoFill">Auto-filled from
                                        customer profile</small>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="cover_to" class="form-label">
                                        Cover Period To
                                    </label>
                                    <input type="date" class="form-inputs @error('cover_to') is-invalid @enderror"
                                        id="cover_to" name="cover_to" value="{{ old('cover_to') }}" required>
                                    @error('cover_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
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
                                        placeholder="Enter full name of insured party">
                                    @error('insured_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success fw-medium d-none" id="insuredNameAutoFill">Auto-filled from
                                        customer profile</small>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="business_type" class="form-label">
                                        Type of Business
                                    </label>
                                    <input type="text" class="form-inputs @error('business_type') is-invalid @enderror"
                                        id="business_type" name="business_type" value="{{ old('business_type') }}"
                                        required placeholder="e.g., Manufacturing, Retail, Construction">
                                    @error('business_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success fw-medium d-none" id="businessTypeAutoFill">Auto-filled
                                        from
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
                                        <div class="invalid-feedback">{{ $message }}</div>
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
                                        <div class="invalid-feedback">{{ $message }}</div>
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
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="date_notified" class="form-label">Date Notified to Reinsurer <span
                                            class="text-danger">*</span></label>
                                    <input type="date"
                                        class="form-inputs @error('date_notified') is-invalid @enderror"
                                        id="date_notified" name="date_notified" value="{{ old('date_notified') }}">
                                    <div class="form-text">Leave blank if not yet notified</div>
                                    @error('date_notified')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="cause_of_loss" class="form-label">
                                        Cause of Loss <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-inputs @error('cause_of_loss') is-invalid @enderror" id="cause_of_loss" name="cause_of_loss"
                                        rows="4" required placeholder="Provide detailed description of what caused the loss...">{{ old('cause_of_loss') }}</textarea>
                                    @error('cause_of_loss')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="loss_description" class="form-label">
                                        Loss Description <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-inputs @error('loss_description') is-invalid @enderror" id="loss_description"
                                        name="loss_description" rows="4" required
                                        placeholder="Provide comprehensive description of the loss, damages, and circumstances...">{{ old('loss_description') }}</textarea>
                                    @error('loss_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        {{-- <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                            <i class="fas fa-save me-1"></i> Save Draft
                        </button> --}}
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
            let customersLoaded = false;
            const $customerId = $('#customer_id');

            $('#claimsNotificationModal').on('shown.bs.modal', function() {
                if ($customerId.hasClass('select2-hidden-accessible')) {
                    $customerId.select2('destroy');
                }

                $customerId.select2({
                    dropdownParent: $('#claimsNotificationModal'),
                    placeholder: 'Select a customer or enter new details below',
                    allowClear: true,
                    width: '100%'
                });
            });

            $('#customer_id').on('select2:open', function() {
                if (!customersLoaded) {
                    fetchCustomers();
                }
            });

            function fetchCustomers() {
                // $customerId.prop('disabled', true);

                if ($('#customer_id').hasClass('select2-hidden-accessible')) {

                    $('#claimsNotificationModal #customer_id').find('option:not(:first)').remove();
                    $('#claimsNotificationModal #customer_id').append(
                        '<option value="">Loading customers...</option>');
                    $('#claimsNotificationModal #customer_id').trigger('change');
                }

                $.ajax({
                    url: "{{ route('claim.notification.get-customers') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            if (Array.isArray(response.data) && response.data.length > 0) {
                                populateCustomerDropdown(response.data);
                                customersLoaded = true;
                            } else {
                                $('#customer_id').find('option:contains("Loading")').remove();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching customers:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });
                        handleFetchError(xhr);
                    },
                    complete: function() {
                        // $('#customer_id').prop('disabled', false);
                    }
                });

            }

            function handleFetchError(xhr) {
                const $dropdown = $('#customer_id');
                $dropdown.find('option:not(:first)').remove();
                $dropdown.append('<option value="">❌ Failed to load - Click to retry</option>');
                customersLoaded = false;
            }

            $('#customer_id').on('change', function() {
                const selectedValue = $(this).val();

                if (selectedValue) {
                    if ($(this).find('option:selected').text().includes('Failed to load')) {
                        fetchCustomers();
                        return;
                    }

                    populateCustomerData();
                } else {
                    clearCustomerSelection();
                }
            });







            $('#claimlist-table').DataTable({
                order: [
                    [6, 'asc']
                ],
                pageLength: 15,
                lengthMenu: [15, 30, 50, 100],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: true,
                ajax: {
                    url: "{{ route('claim.notification.enquiry.datatable') }}",

                },
                columns: [{
                        data: 'intimation_no',
                        searchable: true
                    },
                    {
                        data: 'cover_no',
                        searchable: true
                    },
                    {
                        data: 'endorsement_no',
                        searchable: true
                    },
                    {
                        data: 'type_of_bus',
                        searchable: false
                    },
                    {
                        data: 'class_desc',
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        searchable: false
                    },
                    {
                        data: 'status',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'action',
                        sortable: false
                    },
                ],
            });

            $('#reseverd-list-table').DataTable({
                order: [
                    [6, 'asc']
                ],
                pageLength: 15,
                lengthMenu: [15, 30, 50, 100],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: true,
                ajax: {
                    url: "{{ route('claim.notification.enquiry.datatable') }}",

                },
                columns: [{
                        data: 'intimation_no',
                        searchable: true
                    },
                    {
                        data: 'cover_no',
                        searchable: true
                    },
                    {
                        data: 'endorsement_no',
                        searchable: true
                    },
                    {
                        data: 'type_of_bus',
                        searchable: false
                    },
                    {
                        data: 'class_desc',
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        searchable: false
                    },
                    {
                        data: 'status',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'action',
                        sortable: false
                    },
                ]
            });

            $(document).on('click', '#view-notf-claimstatus', function(e) {
                e.preventDefault()
                var intimation_no = $(this).data('intimation_no');
                var process_type = $(this).data('process_type');
                const claimDetailUrl = new URL("{{ route('claim.notification.claim_detail') }}", window
                    .location.origin);
                if (intimation_no !== '') {
                    try {
                        claimDetailUrl.searchParams.set('intimation_no', intimation_no);
                        if (process_type) {
                            claimDetailUrl.searchParams.set('process_type', process_type);
                        }
                        window.location.href = claimDetailUrl.toString();
                    } catch (error) {
                        console.error('URL error:', error);
                        Swal.fire('Error', 'Invalid URL parameters', 'error');
                    }
                } else {
                    Swal.fire('Error', 'No intimation number provided.', 'error');
                }
            })

            $('#newClaimBtn').click(function(e) {
                e.preventDefault();
                $('#claimsNotificationModal').modal('show');
            });

            $("#new_claim_form").validate({
                errorClass: "errorClass",
                rules: {
                    customer_id: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#next-save-btn').prop('disabled', true).text('Submitting...');
                    form.submit()
                    $('#next-save-btn').prop('disabled', false).text('Next');
                }
            });
        });

        function populateCustomerData() {
            const selectedOption = $('#customer_id option:selected');
            const customerDataStr = selectedOption.attr('data-customer');

            if (!customerDataStr) {
                return;
            }

            try {
                const customerData = JSON.parse(customerDataStr);
                $('#customerInfoPanel').removeClass('d-none');

                const detailsHtml = `
                    <div class="c-detail">
                        <strong>Name:</strong>
                        <span>${customerData.name}</span>
                    </div>
                      <div class="c-detail">
                        <strong>Email:</strong>
                        <span>${customerData.email}</span>
                    </div>
                     <div class="c-detail">
                        <strong>Phone:</strong>
                        <span>${customerData.phone}</span>
                    </div>
                    <div class="c-detail">
                        <strong>Business Type:</strong>
                        <span>${customerData.business_type}</span>
                    </div>
                    <div class="c-detail">
                        <strong>Covers:</strong>
                        <span>${Array.isArray(customerData.covers) ? customerData.covers.length + ' policies' : ''}</span>
                    </div>
                `;

                $('#customerDetails').html(detailsHtml);

                populateFormFields(customerData);

            } catch (error) {
                console.error('Error parsing customer data:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Error loading customer data', 'Error');
                }
            }
        }

        function populateFormFields(customerData) {
            if (customerData.covers && Array.isArray(customerData.covers)) {
                populateCoverTypeDropdown(customerData.covers);
                $('#coverTypeAutoFill').removeClass('d-none');
            }

            console.log(customerData)

            const textFields = [{
                    field: '#insured_name',
                    value: customerData.name,
                    indicator: '#insuredNameAutoFill'
                },
                {
                    field: '#business_type',
                    value: customerData.business_type,
                    indicator: '#businessTypeAutoFill'
                },
                {
                    field: '#cover_from',
                    value: customerData.cover_from,
                    indicator: '#coverFromAutoFill'
                },
                {
                    field: '#cover_to',
                    value: customerData.cover_to,
                    indicator: '#coverToAutoFill'
                },
                {
                    field: '#endorsement_number',
                    value: customerData.endorsement,
                    indicator: '#endorsementAutoFill'
                }
            ];

            textFields.forEach(({
                field,
                value,
                indicator
            }) => {
                if (value && value.toString().trim() !== '') {
                    $(field).val(value);
                    if (indicator) {
                        $(indicator).removeClass('d-none');
                    }
                }
            });
        }

        function populateCoverTypeDropdown(covers) {
            const $coverDropdown = $('#cover_type');

            console.log(covers)

            $coverDropdown.find('option:not(:first)').remove();

            if (covers && covers.length > 0) {
                covers.forEach(cover => {
                    const displayText = `${cover.cover_no} - ${cover.cover_type} - ${cover.insured_name}`;
                    const option = new Option(displayText, cover.cover_no || '');
                    $coverDropdown.append(option);
                });

                if ($coverDropdown.hasClass('select2-hidden-accessible')) {
                    $coverDropdown.trigger('change.select2');
                }
            }
        }

        function populateCustomerDropdown(customers) {
            const $dropdown = $('#customer_id');

            $dropdown.find('option:not(:first)').remove();
            $dropdown.find('option:contains("Loading")').remove();


            if (!customers || !Array.isArray(customers) || customers.length === 0) {
                const noDataOption = new Option('No customers found', '', false, false);
                $(noDataOption).attr('disabled', true);
                $dropdown.append(noDataOption);
                return;
            }

            const processedOptions = customers.map(customer => {
                let displayName = customer.name || 'Unknown Customer';

                const secondaryInfo = [];
                if (customer.email) secondaryInfo.push(customer.email);
                if (customer.phone) secondaryInfo.push(customer.phone);
                if (customer.business_type) secondaryInfo.push(customer.business_type);

                if (secondaryInfo.length > 0) {
                    displayName += ` (${secondaryInfo.join(' | ')})`;
                }

                const option = new Option(displayName, customer.customer_id, false, false);

                $(option).attr('data-customer', JSON.stringify({
                    id: customer.customer_id,
                    name: customer.name || '',
                    email: customer.email || '',
                    phone: customer.phone || '',
                    business_type: customer.business_type || '',
                    cover_type: customer.cover_type || '',
                    cover_from: customer.cover_from || '',
                    cover_to: customer.cover_to || '',
                    covers: customer.covers || []
                }));

                $(option).attr('data-name', customer.name || '');
                $(option).attr('data-email', customer.email || '');
                $(option).attr('data-phone', customer.phone || '');

                return option;
            });

            $dropdown.append(processedOptions);

            $dropdown.trigger('change.select2');

            if (!$dropdown.hasClass('select2-template-applied')) {

                $dropdown.select2({
                    dropdownParent: $('#claimsNotificationModal'),
                    placeholder: 'Select a customer or enter new details below',
                    allowClear: true,
                    width: '100%',
                    templateResult: formatCustomerOption,
                    templateSelection: formatCustomerSelection,
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });

                $dropdown.addClass('select2-template-applied');
            }
        }

        function clearCustomerSelection() {
            $('#customerInfoPanel').addClass('d-none');

            const fieldsToClear = [
                '#cover_type', '#insured_name', '#business_type',
                '#cover_from', '#cover_to'
            ];

            fieldsToClear.forEach(field => {
                $(field).val('');
            });

            $('.fw-medium.text-success').addClass('d-none');
        }

        function formatCustomerOption(customer) {
            if (!customer.id) {
                return customer.text;
            }

            const $customer = $(customer.element);
            const customerData = $customer.attr('data-customer');

            if (!customerData) {
                return customer.text;
            }

            try {
                const data = JSON.parse(customerData);
                return $(`
                    <div class="customer-option">
                        <div class="customer-name fw-medium">${data.name}</div>
                        <div class="customer-details small">
                            ${data.email ? `<span class="me-2"><i class="bx bx-envelope"></i> ${data.email}</span>` : ''}
                            ${data.phone ? `<span class="me-2"><i class="bx bx-phone"></i> ${data.phone}</span>` : ''}
                            ${data.business_type ? `<span><i class="bx bx-briefcase"></i> ${data.business_type}</span>` : ''}
                        </div>
                    </div>
                `);
            } catch (e) {
                return customer.text;
            }
        }

        function formatCustomerSelection(customer) {
            if (!customer.id) {
                return customer.text;
            }

            const $customer = $(customer.element);
            const name = $customer.attr('data-name');

            return name || customer.text;
        }
    </script>
@endpush
