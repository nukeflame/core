<!-- Lead Stage Modal -->
<div id="leadModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticPropoalStageLabel" aria-hidden="true" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 60%;">
        <div class="modal-content">
            <form id="leadForm" action="{{ route('update.opp.status') }}" novalidate>
                <input type="hidden" class="opportunity_id" id="leadOpportunityId" name="opportunity_id" />
                <input type="hidden" id="specialConditionsContent" name="specialConditionsContent" />
                <input type="hidden" id="leadCurrentStage" class="current_stage" name="current_stage" />
                <input type="hidden" name="class_code" class="class_code" id="leadClassCode">
                <input type="hidden" name="class_group_code" class="class_group_code" id="leadClassGroupCode">
                <input type="hidden" name="reinsurers_data" class="reinsurers_data" id="reinsurersData">
                <input type="hidden" name="retained_share" id="retainedShareValue">
                <input type="hidden" name="total_placed_shares" id="totalPlacedShares">
                <input type="hidden" name="total_unplaced_shares" class="reinsurers_data" id="totalUnplacedShares">
                <input type="hidden" class="cedant_id" id="lead_cedant_id" name="cedant_id" />
                <input type="hidden" class="slip_type" id="slipType" name="slip_type" />
                <input type="hidden" class="category_type" id="leadCategoryType" name="category_type" />

                <div class="modal-body fac-slip-container">
                    <div class="fac-slip-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h1 class="slip-title">
                                    <i class="bx bx-shield me-1"></i>Facultative Slip
                                </h1>
                                <p class="slip-subtitle mb-0">Reinsurance Coverage Lead</p>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-light text-dark fs-6 px-3 py-2">
                                    Slip #: <span class="slip-display"></span>
                                </div>
                                <div class="mt-2 text-light opacity-75">
                                    <small>Created: <span class="created_at-display"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="company-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-2 fw-medium" style="font-size: 19px;"><i
                                            class="bx bx-building me-1"></i><span class="insured-name-display"></span>
                                    </h6>
                                    <p class="mb-0 small" style="font-size: 13px;">Insured / Policyholder</p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="mb-0 small">Name: <span class="insured-contact-name-display"></span></p>
                                    <p class="mb-0 small">Contact: <span class="insured-email-display"></span></p>
                                    <p class="mb-0 small">Tel: <span class="insured-phone-display"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card custom-card section-box customScrollBar shadow-none mb-0">
                        <!-- Coverage Details Section -->
                        <div class="form-section">
                            <div class="section-header" data-section="coverage-details">
                                <div class="section-title">
                                    <span>
                                        <i class="bi bi-umbrella section-icon"></i>
                                        Coverage Details
                                    </span>
                                </div>
                            </div>
                            <div class="section-content" id="coverage-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                100% Sum Insured
                                                <span class="sum_insured_type" style="padding-left: 6px;"></span>
                                                <span class="required-asterisk">*</span>
                                                <i class="bx bx-info-circle tooltip-trigger"
                                                    title="Total insured value before share allocation."></i>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="currencySymbol">KES</span>
                                                <input type="text"
                                                    class="form-control form-inputs total_sum_insured"
                                                    name="total_sum_insured" required placeholder="0.00"
                                                    aria-label="100% Sum Insured" aria-describedby="currencySymbol"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    change="this.value=numberWithCommas(this.value)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 fac-rates">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Premium
                                                <span class="required-asterisk">*</span>
                                                <i class="bx bx-info-circle tooltip-trigger"
                                                    title="Gross premium corresponding to the total sum insured."></i>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="premiumCurrencySymbol">KES</span>
                                                <input type="text" class="form-control form-inputs premium"
                                                    name="premium" required placeholder="0.00" aria-label="Premium"
                                                    aria-describedby="premiumCurrencySymbol"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    change="this.value=numberWithCommas(this.value)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row fac-rates">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Reinsurer Commission Rate (%)
                                                <i class="bx bx-info-circle tooltip-trigger"
                                                    title="Commission percentage payable to reinsurer."></i>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="brokerageRateSymbol">%</span>
                                                <input type="text" class="form-control form-inputs brokerage_rate"
                                                    name="brokerage_rate" placeholder="0.00"
                                                    aria-label="Reinsurer Commission Rate"
                                                    aria-describedby="brokerageRateSymbol"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    change="this.value=numberWithCommas(this.value)" value="10">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 deductible_excess_div" style="display: none;">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Deductible/Excess
                                                <span class="required-asterisk">*</span>
                                                <i class="bx bx-info-circle tooltip-trigger"
                                                    title="Amount retained by the insured before claim recovery."></i>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"
                                                    id="deductibleCurrencySymbol">KES</span>
                                                <input type="text" class="form-control form-inputs deductible"
                                                    name="deductible" placeholder="0.00"
                                                    aria-label="Deductible/Excess"
                                                    aria-describedby="deductibleCurrencySymbol"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    change="this.value=numberWithCommas(this.value)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Total sum insured breakdown</label>
                                    <div class="form-textarea-wrapper">
                                        <textarea class="form-inputs breakdown-textarea special_conditions" name="special_conditions" id="specialConditions"
                                            rows="4" maxlength="5000" aria-label="Special Terms and Conditions"
                                            placeholder="Any special terms, conditions, or clauses applicable to this coverage..."></textarea>
                                        <div class="form-text mt-1">
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle"></i>
                                                Click to open the rich text editor.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-0 pt-0" />

                        <!-- Reinsurer Information Section -->
                        <div class="form-section">
                            <div class="section-header mb-2">
                                <div class="section-title">
                                    <span>
                                        <i class="bx bx-disc section-icon"></i>
                                        Reinsurer Placement
                                    </span>
                                </div>
                            </div>
                            <div class="section-content" id="reinsurer-info">
                                <div class="reinsurer-selection-panel mb-2">
                                    <div class="row">
                                        <div class="col-md-6 quote-rein">
                                            <div class="form-group">
                                                <label class="form-label">Add Reinsurer</label>
                                                <select class="sel" id="availableReinsurers"
                                                    placeholder="Search and select reinsurer...">
                                                    <option value="">Search and select reinsurer...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 fac-rates">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    Total Written Share (%)
                                                    <span class="required-asterisk">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number"
                                                        class="form-control form-inputs total_reinsurer_share"
                                                        id="leadTotalReinsurerShare" name="total_reinsurer_share"
                                                        placeholder="100.00" step="0.01" min="0.01"
                                                        max="100" required value="100"
                                                        aria-label="Total Written Share"
                                                        aria-describedby="totalWrittenShareSymbol">
                                                    <span class="input-group-text"
                                                        id="totalWrittenShareSymbol">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 fac-rates">
                                            <div class="form-group">
                                                <label class="form-label">Share (%)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control form-inputs"
                                                        id="reinsurerShare" placeholder="0.00" step="0.01"
                                                        min="0.01" max="100" aria-label="Share"
                                                        aria-describedby="reinsurerShareSymbol">
                                                    <span class="input-group-text" id="reinsurerShareSymbol">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-success w-100"
                                                    id="addReinsurer" style="padding: 2px 0px;">
                                                    <i class="bx bx-plus" style="font-size: 27px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="selected-reinsurers-section">
                                    <h6 class="mb-3">
                                        <i class="bx bx-building me-1"></i>Selected Reinsurers
                                        <span class="badge bg-primary ms-2" id="reinsurerCount">0</span>
                                    </h6>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-stripped selected-reinsurers-table"
                                            id="reinsurersTable">
                                            <thead class="table-d">
                                                <tr>
                                                    <th style="width: 70%">Reinsurer</th>
                                                    <th style="width: 20%">Written Share (%)</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reinsurersTableBody">
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="total-shares-display mt-3 d-block">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="shares-card placed-shares">
                                                    <div class="shares-icon">
                                                        <i class="bx bx-check-circle"></i>
                                                    </div>
                                                    <div class="shares-info">
                                                        <span class="shares-label">Placed Shares</span>
                                                        <span class="shares-value placed-value">0.00%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="shares-card unplaced-shares">
                                                    <div class="shares-icon">
                                                        <i class="bx bx-time-five"></i>
                                                    </div>
                                                    <div class="shares-info">
                                                        <span class="shares-label">Unplaced Shares</span>
                                                        <span class="shares-value unplaced-value">100.00%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="shares-progress mt-2">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success placed-progress"
                                                    role="progressbar" style="width: 0%" aria-valuenow="0"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-0 pt-0" />

                        <!-- Terms and Conditions Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-title">
                                    <div>
                                        <i class="bx bx-file section-icon"></i>
                                        Terms & Conditions
                                    </div>
                                    <div id="termsSubtitle" class="ms-3 fs-12 opacity-75" style="margin-left: 9px;">
                                    </div>
                                </div>
                            </div>
                            <div class="section-content" id="termsConditions"></div>
                        </div>

                        <hr />

                        <!-- Supporting Documents Section -->
                        <div class="form-section">
                            <div class="section-header" data-section="documents">
                                <div class="section-title">
                                    <div>
                                        <i class="bx bx-upload section-icon"></i>
                                        Supporting Documents
                                    </div>
                                    <div id="documentsSubtitle" class="ms-3 fs-12 opacity-75"
                                        style="margin-left: 9px;">
                                    </div>
                                </div>
                            </div>
                            <div class="documents-section-content" id="documentsContent">
                                <div id="documentFields" class="row" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" id="previewSlipBtn">
                                <i class="bx bx-file me-1"></i>Preview Slip
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-dark">
                                <i class="bx bx-save me-1"></i> Send Lead
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Category Modal -->
<div class="modal fade effect-scale md-wrapper" id="updateCategoryTypeModal" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticUpdateCategoryTypeModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="staticUpdateCategoryTypeModalLabel">
                    <i class="bi bi-pencil-square"></i>
                    Update Category Type
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('update.category_type') }}" method="POST" enctype="multipart/form-data"
                id="updateCategoryForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="opportunity_id" id="opportunity_id" />

                    <div class="mb-4">
                        <label for="category_type" class="form-label">
                            Category Type<span class="required-asterisk">*</span>
                        </label>
                        <select class="form-inputs select2" name="category_type" id="category_type"
                            aria-describedby="categoryTypeHelp">
                            <option value="" disabled selected>Select Category</option>
                            <option value="1">Quotation</option>
                            <option value="2">Facultative Offer</option>
                        </select>
                        <div class="form-text" id="categoryTypeHelp">
                            Please select the appropriate category type for this opportunity.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div></div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" id="updateCategorySubmitBtn">
                                <i class="bx bx-check-circle me-1"></i> Update Category
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Contacts Modal -->
<div class="modal fade effect-scale md-wrapper" id="contactsModal" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="contactsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="contactsModalLabel">
                    <i class="bx bx-building me-1"></i>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-4" id="primary-contacts">
                    <input type="hidden" id="contactsOpportunityId" value="">
                    <h6 class="text-uppercase fw-bold text-muted mb-3">
                        <i class="bx bx-star text-warning me-2"></i>Primary Contact
                    </h6>
                    <div class="card border-warning">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Contact Name</label>
                                    <input type="text" class="form-control-plaintext primary-name" value=""
                                        readonly>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Primary Email</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control-plaintext primary-email"
                                            value="" readonly>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" class="primary-contact_id" name="contact_id" readonly>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="department-header rounded mb-3">
                        <h6 class="mb-0 fw-medium">
                            <i class="bx bx-user me-2"></i>Department Contacts
                        </h6>
                    </div>

                    <div id="departmentContacts"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bx bx-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="submitContactModal">
                    <i class="bx bx-save me-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Breakdown Text Editor -->
<div class="modal fade breakdown-modal effect-scale md-wrapper" id="breakdownModal" tabindex="-1"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="breakdownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width: 85%; width: 85%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="breakdownModalLabel">
                    <i class="bx bx-edit-alt me-2"></i>
                    Sum Insured Breakdown Editor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <div class="position-relative">
                    <!-- Loading Overlay -->
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="text-center">
                            <div class="spinner-border spinner-border-custom" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Initializing editor...</p>
                        </div>
                    </div>

                    <div class="p-3">
                        <!-- Quick Templates Section -->
                        <div class="template-section">
                            <h6 class="mb-3 fw-bold text-primary">
                                <i class="bx bx-layout me-2"></i>Editor Actions
                            </h6>
                            <div class="d-flex flex-wrap">
                                <input type="hidden" id="schId" />
                                <button type="button" class="template-btn" data-template="standard">
                                    Load Existing Content
                                </button>
                                <button type="button" class="template-btn" data-template="clear">
                                    Clear Content
                                </button>
                            </div>
                        </div>

                        <div class="quill-container position-relative">
                            <div id="breakdownEditor"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-outline-secondary me-2" id="previewBtn">
                            <i class="bx bx-show me-1"></i>Preview
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="saveBreakdownBtn">
                            <i class="bx bx-save me-1"></i>Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="proposal-quoteslip-form" method="POST" action="{{ route('quote.quotationCoverSlip.facultative') }}"
    data-quotation-action="{{ route('quote.quotationCoverSlip.quotation') }}"
    data-facultative-action="{{ route('quote.quotationCoverSlip.facultative') }}" target="_blank"
    style="display: none;">
    @csrf
</form>

<style>
    .fac-slip-container {
        border-block-end: 1px solid var(--default-border);
        border-top-left-radius: .5rem;
        border-top-right-radius: .5rem;
        padding: 0px !important;
    }

    .fac-slip-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 1rem;
        position: relative;
        overflow: hidden;
        padding-top: 1.5rem;
        border-top-left-radius: .5rem;
        border-top-right-radius: .5rem;
    }

    .fac-slip-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
        animation: float 20s infinite linear;
    }

    @keyframes float {
        0% {
            transform: translate(-50%, -50%) rotate(0deg);
        }

        100% {
            transform: translate(-50%, -50%) rotate(360deg);
        }
    }

    .slip-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
    }

    .slip-subtitle {
        font-size: 14px;
        opacity: 0.9;
        position: relative;
        z-index: 2;
    }

    .company-info {
        background: #0a0a0a0a;
        padding: 1rem;
        border-radius: 8px;
        backdrop-filter: blur(10px);
        margin-top: .5rem;
        position: relative;
        z-index: 2;
    }

    .form-group {
        border: none !important;
        margin-bottom: 0px !important;
    }

    .section-box {
        padding: 1rem;
        padding-top: 0px;
        height: 70vh;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .section-title {
        color: #c02e2e;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 0px;
    }

    .form-label {
        font-weight: 600;
        color: var(--bs-gray-700);
    }

    .required-asterisk {
        color: var(--bs-danger);
        margin-left: 0.25rem;
    }

    .tooltip-trigger {
        cursor: help;
        color: var(--bs-blue);
        margin-left: 0.5rem;
        line-height: 1px;
    }

    .currency-input {
        position: relative;
    }

    .cr-symbl {
        transform: translateY(-51%) !important;
    }

    .currency-symbol {
        position: absolute;
        left: 12px;
        top: 10px;
        color: var(--gray-700);
        font-weight: 600;
        z-index: 10;
        font-size: 15px;
    }

    .currency-input .form-inputs {
        padding-left: 2.5rem !important;
    }

    .form-inputs {
        margin-bottom: 0px;
    }

    .form-section {
        margin-bottom: 15px;
    }

    .selected-reinsurers-table>:not(caption)>*>* {
        padding: .5rem .5rem;
        color: var(--bs-table-color-state, var(--bs-table-color-type, var(--bs-table-color)));
        background-color: var(--bs-table-bg);
        border-bottom-width: var(--bs-border-width);
        box-shadow: inset 0 0 0 9999px var(--bs-table-bg-state, var(--bs-table-bg-type, var(--bs-table-accent-bg)));
    }

    .swal2-popup.swal2-toast .swal2-title {
        padding: 0px;
        text-align: left;
    }

    .swal2-popup.swal2-toast {
        padding: 13px !important;
    }

    .swal2-popup.swal2-toast .swal2-html-container {
        padding: 0px;
        margin-left: 48px;
        margin-top: 0px;
    }

    .select2-container--default .select2-results>.select2-results__options {
        max-height: 600px !important;
    }

    #contactsModal .form-label {
        color: #000;
    }

    .envlope-ico {
        font-size: 21px;
        line-height: 0px;
        vertical-align: -4px;
        margin-left: 10px;
        color: #aaa;
    }

    .insured-email-display {
        text-transform: lowercase !important;
    }

    .insured-contact-name-display {
        text-transform: capitalize !important;
    }

    .file-upload-area {
        position: relative;
        border: 2px dashed #e0e6ed;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        background: linear-gradient(145deg, #fafbfc 0%, #f8f9fa 100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        min-height: 0px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .file-upload-area::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 50% 50%, rgba(13, 110, 253, 0.05) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .file-upload-area:hover {
        border-color: #0d6efd;
        background: linear-gradient(145deg, #f8f9ff 0%, #f0f4ff 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(13, 110, 253, 0.1);
    }

    .file-upload-area:hover::before {
        opacity: 1;
    }

    .file-upload-area.drag-over {
        border-color: #198754;
        background: linear-gradient(145deg, #f8fff9 0%, #f0fff4 100%);
        transform: scale(1.02);
        box-shadow: 0 12px 30px rgba(25, 135, 84, 0.15);
    }

    .file-upload-area.drag-over .upload-icon {
        color: #198754 !important;
        transform: scale(1.1);
    }

    .file-upload-area.has-error {
        border-color: #dc3545;
        background: linear-gradient(145deg, #fff8f8 0%, #fff0f0 100%);
    }

    .file-upload-area.uploading {
        pointer-events: none;
        opacity: 0.7;
    }

    .upload-icon {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        display: block;
    }

    .file-upload-area:hover .upload-icon {
        color: #0d6efd;
        transform: translateY(-3px);
    }

    .upload-text {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .upload-subtext {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .upload-constraints {
        font-size: 0.8rem;
        color: #adb5bd;
        border-top: 1px solid #e9ecef;
        padding-top: 1rem;
        margin-top: 1rem;
        width: 100%;
    }

    .file-preview-container {
        margin-top: 1rem;
    }

    .file-preview-item {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }

    .file-preview-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-color: #dee2e6;
    }

    .file-icon {
        font-size: 1.5rem;
        margin-right: 0.75rem;
        color: #0d6efd;
    }

    .file-info {
        flex: 1;
        min-width: 0;
    }

    .file-name {
        font-weight: 500;
        color: #212529;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-details {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .file-actions {
        display: flex;
        gap: 0.5rem;
    }

    .file-action-btn {
        padding: 0.25rem 0.5rem;
        border: none;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-view {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn-view:hover {
        background: #bbdefb;
    }

    .btn-remove {
        background: #ffebee;
        color: #d32f2f;
    }

    .btn-remove:hover {
        background: #ffcdd2;
    }

    .upload-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: #0d6efd;
        border-radius: 0 0 12px 12px;
        transition: width 0.3s ease;
        opacity: 0;
    }

    .file-upload-area.uploading .upload-progress {
        opacity: 1;
    }

    .file-upload-area.upload-success {
        border-color: #198754;
        background: linear-gradient(145deg, #f8fff9 0%, #f0fff4 100%);
    }

    .file-upload-area.upload-success .upload-icon {
        color: #198754;
    }

    @media (max-width: 768px) {
        .file-upload-area {
            /* padding: 1.5rem;
            min-height: 150px; */
        }

        .upload-icon {
            font-size: 2.5rem;
        }

        .upload-text {
            font-size: 1rem;
        }
    }

    .file-count-badge {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #0d6efd;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .file-count-badge.show {
        opacity: 1;
        transform: scale(1);
    }

    .documents-section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
    }

    .documents-section-content {
        padding-top: 10px;
    }

    .breakdown-textarea {
        cursor: pointer !important;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
        border: 1px solid #ced4da !important;
        transition: all 0.3s ease !important;
        resize: none;
    }

    .breakdown-textarea:hover {
        /* border-color: var(--secondary-color) !important;
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.1) !important;
        transform: translateY(-1px); */
    }

    /* .breakdown-textarea:focus {
        border-color: var(--secondary-color) !important;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2) !important;
    } */

    .breakdown-modal .modal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border-bottom: none;
        position: relative;
        overflow: hidden;
    }

    .breakdown-modal .modal-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
        animation: float 15s infinite linear;
    }

    .breakdown-modal .modal-title {
        position: relative;
        z-index: 2;
        font-weight: 600;
    }

    .breakdown-modal .btn-close-white {
        position: relative;
        z-index: 2;
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .breakdown-modal .btn-close-white:hover {
        opacity: 1;
    }

    .quill-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .ql-toolbar {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 16px;
    }

    .ql-toolbar .ql-formats {
        margin-right: 20px;
    }

    .ql-toolbar button {
        border-radius: 4px;
        padding: 6px 8px;
        margin: 0 2px;
        transition: all 0.2s ease;
    }

    .ql-toolbar button:hover {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .ql-toolbar button.ql-active {
        background: #3498db;
        color: white;
    }

    .ql-editor {
        font-size: 14px;
        line-height: 1.6;
        padding: 20px;
        height: calc(100vh - 400px);
    }

    .ql-editor.ql-blank::before {
        color: #6c757d;
        font-style: normal;
    }

    /* Character Counter */
    .character-counter {
        position: absolute;
        bottom: 15px;
        right: 20px;
        background: rgba(255, 255, 255, 0.9);
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        color: #6c757d;
        border: 1px solid #dee2e6;
        z-index: 10;
    }

    .character-counter.warning {
        color: #f39c12;
        border-color: #f39c12;
    }

    .character-counter.danger {
        color: #e74c3c;
        border-color: #e74c3c;
    }

    .template-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 11px;
        border: 1px solid #ddd;
    }

    .template-btn {
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        padding: 7px 22px;
        margin: 4px;
        font-size: 12px;
        color: #2c3e50;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .template-btn:hover {
        border-color: #3498db;
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
    }

    .template-btn:active {
        transform: translateY(1px);
    }

    .stats-panel {
        background: white;
        border-radius: 8px;
        padding: 16px;
        margin-top: 16px;
        border-left: 4px solid #3498db;
    }

    .stats-item {
        display: inline-block;
        margin-right: 20px;
        margin-bottom: 8px;
    }

    .stats-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-value {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .loading-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .spinner-border-custom {
        width: 3rem;
        height: 3rem;
        color: #3498db;
    }

    .preview-mode .ql-toolbar {
        display: none;
    }

    .preview-mode .ql-editor {
        border: 2px dashed #dee2e6;
        background: #f8f9fa;
    }

    .ql-container.ql-focused {
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    }

    .save-success {
        animation: pulse 0.6s ease-in-out;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    @media (max-width: 768px) {
        .breakdown-modal .modal-dialog {
            margin: 10px;
        }

        .ql-toolbar {
            padding: 8px 12px;
        }

        .ql-toolbar .ql-formats {
            margin-right: 10px;
        }

        .ql-editor {
            height: calc(100vh - 400px);
            padding: 15px;
        }

        .template-btn {
            font-size: 11px;
            padding: 6px 10px;
        }
    }

    .shares-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 9px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .shares-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .shares-card.placed-shares {
        border-left: 2px solid #198754;
    }

    .shares-card.unplaced-shares {
        border-left: 2px solid #ffc107;
    }

    .shares-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .placed-shares .shares-icon {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .unplaced-shares .shares-icon {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .shares-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1;
    }

    .shares-label {
        font-size: 13px;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .shares-value {
        font-size: 13px;
        font-weight: 700;
        line-height: 14px;
    }

    .shares-progress {
        margin-top: 1rem;
    }

    .shares-progress .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .shares-progress .progress-bar {
        transition: width 0.6s ease;
        border-radius: 10px;
    }

    .total-shares-display {
        margin-top: 1.5rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .shares-card {
            padding: 1rem;
        }

        .shares-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }

        .shares-value {
            font-size: 1.5rem;
        }

        .total-shares-display {
            padding: 0.75rem;
        }
    }

    @keyframes valueChange {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .shares-value {
        animation: valueChange 0.3s ease-in-out;
    }

    /* Color transitions for different states */
    .shares-value.text-success {
        color: #198754 !important;
    }

    .shares-value.text-danger {
        color: #dc3545 !important;
    }

    .shares-value.text-warning {
        color: #ffc107 !important;
    }

    .shares-value.text-primary {
        color: #0d6efd !important;
    }

    .reinsurer-selection-panel {
        background: #f8f9fa;
        padding: 1px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        margin-bottom: 1rem;
    }

    .selected-reinsurers-section {
        margin-top: 1.5rem;
    }

    .selected-reinsurers-section h6 {
        color: #495057;
        font-weight: 600;
    }

    .selected-reinsurers-table thead {
        background: #f8f9fa;
    }

    .selected-reinsurers-table tbody tr:hover {
        background: #f8f9fa;
    }

    #reinsurerCount {
        font-size: 0.875rem;
        padding: 0.35em 0.65em;
    }

    .contacts-reinsurer,
    .remove-reinsurer {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .contacts-reinsurer {
        margin-right: 0.25rem;
    }

    .reinsurer-validation-error {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-top: 1rem;
    }

    .reinsurer-validation-error i {
        font-size: 1.25rem;
        vertical-align: middle;
    }

    #addReinsurer {
        height: 100%;
        min-height: 38px;
    }

    #addReinsurer .bx-loader {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .selected-reinsurers-table tbody:empty::after {
        content: "No reinsurers selected yet. Add reinsurers using the form above.";
        display: block;
        text-align: center;
        padding: 2rem;
        color: #6c757d;
        font-style: italic;
    }

    .shares-progress .progress-bar.bg-success::after {
        content: "✓";
        position: absolute;
        right: 10px;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
    }

    @media (max-width: 576px) {
        .reinsurer-selection-panel .row {
            row-gap: 0.5rem;
        }

        .reinsurer-selection-panel .col-md-8,
        .reinsurer-selection-panel .col-md-3,
        .reinsurer-selection-panel .col-md-1 {
            width: 100%;
        }

        #addReinsurer {
            width: 100%;
            padding: 0.5rem 0;
        }
    }
</style>

@push('script')
    <script>
        $(document).ready(function() {
            const VALIDATION_CONFIG = {
                MIN_PERCENTAGE: 0.01,
                MAX_PERCENTAGE: 100,
                MIN_REINSURERS: 1,
                REQUIRED_FIELDS: ["total_sum_insured", "premium"],
                SLIP_TYPE: 'facultative'
            };

            const FIELD_VALIDATORS = {
                currency: {
                    pattern: /^\d+(\.\d{1,2})?$/,
                    message: "Please enter a valid currency amount (e.g., 1000.00)",
                },
                percentage: {
                    pattern: /^\d+(\.\d{1,2})?$/,
                    min: VALIDATION_CONFIG.MIN_PERCENTAGE,
                    max: VALIDATION_CONFIG.MAX_PERCENTAGE,
                    message: "Please enter a valid percentage between 0.01 and 100",
                },
                email: {
                    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                    message: "Please enter a valid email address",
                },
            };

            const state = {
                selectedReinsurers: new Set(),
                uploadedFiles: {},
                currentClass: "",
                documentConfigs: {},
                bdReinsurers: {},
                dataTable: null,
                breakdownEditor: null,
                slipType: '',
                suppressLeadModalReset: false,
                returningFromContacts: false,
                returningFromBreakdown: false,
                pendingDocumentsDraft: null,
            };

            const PREVIEW_ROUTES = {
                quotation: "{{ route('quote.quotationCoverSlip.quotation') }}",
                facultative: "{{ route('quote.quotationCoverSlip.facultative') }}",
            };
            const LEAD_MODAL_DRAFT_STORAGE_KEY = "fac_lead_modal_draft_v1";
            const LEAD_MODAL_DRAFT_PATH = "/pipelines/facultative/view";
            const LEAD_MODAL_DISPLAY_SELECTORS = [
                ".slip-display",
                ".created_at-display",
                ".insured-name-display",
                ".insured-contact-name-display",
                ".insured-email-display",
                ".insured-phone-display",
                ".sum_insured_type",
            ];
            let persistLeadModalTimer = null;

            function canPersistLeadModalDraft() {
                const currentPath = (window.location.pathname || "").replace(/\/+$/, "");
                const expectedPath = LEAD_MODAL_DRAFT_PATH.replace(/\/+$/, "");
                return currentPath === expectedPath && typeof window.localStorage !== "undefined";
            }

            function getLeadModalDisplaySnapshot() {
                const snapshot = {};
                LEAD_MODAL_DISPLAY_SELECTORS.forEach((selector) => {
                    const $el = $("#leadModal").find(selector).first();
                    snapshot[selector] = $el.length ? $el.text() : "";
                });
                return snapshot;
            }

            function applyLeadModalDisplaySnapshot(snapshot) {
                if (!snapshot || typeof snapshot !== "object") {
                    return;
                }

                LEAD_MODAL_DISPLAY_SELECTORS.forEach((selector) => {
                    if (!Object.prototype.hasOwnProperty.call(snapshot, selector)) {
                        return;
                    }
                    $("#leadModal").find(selector).text(snapshot[selector] || "");
                });
            }

            function collectLeadModalFieldSnapshot() {
                const fields = [];

                $("#leadForm").find("input:not([type='file']), select, textarea").each(function() {
                    const $field = $(this);
                    const type = ($field.attr("type") || "").toLowerCase();
                    const fieldSnapshot = {
                        type: type,
                        value: $field.val(),
                        checked: $field.is(":checked"),
                    };
                    fields.push(fieldSnapshot);
                });

                return fields;
            }

            function applyLeadModalFieldSnapshot(fields) {
                if (!Array.isArray(fields) || fields.length === 0) {
                    return;
                }

                $("#leadForm").find("input:not([type='file']), select, textarea").each(function(index) {
                    const snapshot = fields[index];
                    if (!snapshot) {
                        return;
                    }

                    const $field = $(this);
                    const type = (($field.attr("type") || "") + "").toLowerCase();

                    if (type === "checkbox" || type === "radio") {
                        $field.prop("checked", Boolean(snapshot.checked));
                        return;
                    }

                    $field.val(snapshot.value);
                });

                $("#leadForm").find("select").trigger("change");
            }

            function getLeadModalReinsurersSnapshot() {
                if (!state.dataTable) {
                    return [];
                }

                const reinsurers = [];
                state.dataTable.rows().every(function() {
                    const $row = $(this.node());
                    const reinsurerId = $row.data("reinsurer-id");
                    if (!reinsurerId) {
                        return;
                    }

                    const writtenShare = parseFloat($row.attr("data-written-share")) || 0;
                    const metaText = $row.find("td:first small").text().trim();
                    const metaMatch = metaText.match(/^\((.*)\)\s*-\s*(.*)$/);
                    reinsurers.push({
                        id: reinsurerId,
                        reinsurer_name: $row.find("td:first .fw-medium").text().trim() ||
                            "Unknown Reinsurer",
                        email: metaMatch && metaMatch[1] ? metaMatch[1].trim() : "-",
                        country: metaMatch && metaMatch[2] ? metaMatch[2].trim() : "-",
                        written_share: writtenShare,
                    });
                });

                return reinsurers;
            }

            function getLeadModalContextSnapshot() {
                const dealId = ($("#leadModal").attr("data-deal-id") || "").toString().trim();
                const opportunityId = ($("#leadOpportunityId").val() || "").toString().trim();
                const classCode = ($("#leadClassCode").val() || "").toString().trim();
                const classGroupCode = ($("#leadClassGroupCode").val() || "").toString().trim();
                const categoryType = ($("#leadCategoryType").val() || "").toString().trim();
                const currentStage = ($("#leadCurrentStage").val() || "lead").toString().trim();
                const typeOfBus = (
                    window.currentDealInfo?.type_of_business ||
                    window.currentDealInfo?.business_type ||
                    "FPR"
                ).toString().trim();

                return {
                    dealId,
                    opportunityId,
                    classCode,
                    classGroupCode,
                    categoryType,
                    currentStage: currentStage || "lead",
                    typeOfBus: typeOfBus || "FPR",
                };
            }

            function getLeadDocumentsSnapshot() {
                const additionalTitles = [];
                $("#leadModal #documentFields .supporting-doc-title-input[data-additional-title='1']").each(
                    function() {
                        additionalTitles.push(($(this).val() || "").toString());
                    });

                return {
                    additionalTitles,
                };
            }

            function applyLeadDocumentsSnapshot(snapshot) {
                if (!snapshot || typeof snapshot !== "object") {
                    return;
                }

                const titles = Array.isArray(snapshot.additionalTitles) ?
                    snapshot.additionalTitles.map((title) => (title || "").toString()) : [];

                if (titles.length === 0) {
                    return;
                }

                const pipelineManager = window.pipelineManager;
                if (!pipelineManager) {
                    return;
                }

                const $fieldsContainer = $("#leadModal #documentFields");
                if ($fieldsContainer.length === 0 || $fieldsContainer.children().length === 0) {
                    return;
                }

                const $currentInputs = $fieldsContainer.find(
                    ".supporting-doc-title-input[data-additional-title='1']",
                );

                if ($currentInputs.length === 0) {
                    return;
                }

                if (typeof pipelineManager.createAdditionalDocumentRowHtml === "function") {
                    while (
                        $fieldsContainer.find(".supporting-doc-title-input[data-additional-title='1']").length <
                        titles.length
                    ) {
                        const rowHtml = pipelineManager.createAdditionalDocumentRowHtml({
                            defaultTitle: "Additional Documents",
                            showAddButton: false,
                            showRemoveButton: true,
                        });

                        const $lastAdditionalColumn = $fieldsContainer
                            .find(".supporting-doc-title-input[data-additional-title='1']")
                            .last()
                            .closest(".col-12");

                        if ($lastAdditionalColumn.length > 0) {
                            $lastAdditionalColumn.after(rowHtml);
                        } else {
                            $fieldsContainer.append(rowHtml);
                        }
                    }
                }

                if (typeof pipelineManager.initializeFileUploads === "function") {
                    pipelineManager.initializeFileUploads();
                }

                $fieldsContainer
                    .find(".supporting-doc-title-input[data-additional-title='1']")
                    .each(function(index) {
                        const nextTitle = titles[index];
                        if (typeof nextTitle === "undefined") {
                            return;
                        }
                        $(this).val(nextTitle).trigger("input");
                    });
            }

            function restoreLeadDocumentsDraftWhenReady(snapshot, retries = 15) {
                if (!snapshot || typeof snapshot !== "object") {
                    return;
                }

                const $fieldsContainer = $("#leadModal #documentFields");
                const isReady = $fieldsContainer.length > 0 && $fieldsContainer.children().length > 0;

                if (isReady) {
                    applyLeadDocumentsSnapshot(snapshot);
                    state.pendingDocumentsDraft = null;
                    return;
                }

                if (retries <= 0) {
                    return;
                }

                setTimeout(() => {
                    restoreLeadDocumentsDraftWhenReady(snapshot, retries - 1);
                }, 200);
            }

            function saveLeadModalDraft() {
                if (!canPersistLeadModalDraft()) {
                    return;
                }

                try {
                    const payload = {
                        savedAt: Date.now(),
                        isOpen: $("#leadModal").hasClass("show") || state.suppressLeadModalReset,
                        fields: collectLeadModalFieldSnapshot(),
                        display: getLeadModalDisplaySnapshot(),
                        reinsurers: getLeadModalReinsurersSnapshot(),
                        documents: getLeadDocumentsSnapshot(),
                        context: getLeadModalContextSnapshot(),
                    };

                    localStorage.setItem(LEAD_MODAL_DRAFT_STORAGE_KEY, JSON.stringify(payload));
                } catch (error) {
                    console.warn("Unable to save lead modal draft:", error);
                }
            }

            function queueLeadModalDraftSave() {
                if (!canPersistLeadModalDraft()) {
                    return;
                }

                if (persistLeadModalTimer) {
                    clearTimeout(persistLeadModalTimer);
                }

                persistLeadModalTimer = setTimeout(() => {
                    persistLeadModalTimer = null;
                    saveLeadModalDraft();
                }, 200);
            }

            function clearLeadModalDraft() {
                if (!canPersistLeadModalDraft()) {
                    return;
                }
                localStorage.removeItem(LEAD_MODAL_DRAFT_STORAGE_KEY);
            }

            function reloadLeadDynamicSections(overrideContext = null, retries = 10) {
                const context = (overrideContext && typeof overrideContext === "object") ?
                    overrideContext : {};

                const dealId = (
                    context.dealId ||
                    $("#leadModal").attr("data-deal-id") ||
                    context.opportunityId ||
                    $("#leadOpportunityId").val() ||
                    ""
                ).toString().trim();

                const opportunityId = (context.opportunityId || $("#leadOpportunityId").val() || "").toString()
                    .trim();
                if (!dealId && !opportunityId) {
                    return;
                }

                const pipelineManager = window.pipelineManager;
                if (!pipelineManager) {
                    if (retries > 0) {
                        setTimeout(() => reloadLeadDynamicSections(retries - 1), 150);
                    }
                    return;
                }

                const classCode = (context.classCode || $("#leadClassCode").val() || "").toString().trim();
                const classGroupCode = (context.classGroupCode || $("#leadClassGroupCode").val() || "").toString()
                    .trim();
                const categoryType = (context.categoryType || $("#leadCategoryType").val() || "").toString().trim();
                const currentStage = (context.currentStage || $("#leadCurrentStage").val() || "lead").toString()
                    .trim();
                const typeOfBus = (
                    context.typeOfBus ||
                    window.currentDealInfo?.type_of_business ||
                    window.currentDealInfo?.business_type ||
                    "FPR"
                ).toString().trim();

                const reloadData = {
                    dealId: Number(dealId) || opportunityId,
                    opportunityId: opportunityId,
                    modalId: "leadModal",
                    class: classCode,
                    classGroup: classGroupCode,
                    typeOfBus: typeOfBus || "FPR",
                    stage: "proposal",
                    currentStage: currentStage || "lead",
                    categoryType: categoryType || "2",
                    riskType: '',
                    sumInsuredType: ''
                };

                if (typeof pipelineManager.loadScheduleHeaders === "function") {
                    pipelineManager.loadScheduleHeaders(reloadData);
                }

                if (typeof pipelineManager.loadSlipDocuments === "function") {
                    pipelineManager.loadSlipDocuments(reloadData);
                }

                if (typeof pipelineManager.loadBdTerms === "function") {
                    pipelineManager.loadBdTerms(reloadData);
                }
            }

            function restoreLeadModalDraftIfAny() {
                if (!canPersistLeadModalDraft()) {
                    return;
                }

                let rawDraft = null;
                try {
                    rawDraft = localStorage.getItem(LEAD_MODAL_DRAFT_STORAGE_KEY);
                } catch (error) {
                    return;
                }

                if (!rawDraft) {
                    return;
                }

                let draft = null;
                try {
                    draft = JSON.parse(rawDraft);
                } catch (error) {
                    clearLeadModalDraft();
                    return;
                }

                if (!draft || !draft.isOpen) {
                    return;
                }

                applyLeadModalFieldSnapshot(draft.fields || []);
                applyLeadModalDisplaySnapshot(draft.display || {});
                state.pendingDocumentsDraft = draft.documents || null;

                if (Array.isArray(draft.reinsurers) && draft.reinsurers.length > 0) {
                    $("#reinsurersData").val(JSON.stringify(draft.reinsurers));
                    $("#reinsurerCount").text(draft.reinsurers.length);
                }

                if (!$("#leadOpportunityId").val() && !(draft.context && draft.context.dealId)) {
                    return;
                }

                reloadLeadDynamicSections(draft.context || null);

                state.suppressLeadModalReset = false;
                $("#leadModal").modal("show");
            }

            function isQuotationSlipMode(slipType) {
                const normalizedSlipType = (slipType || $("#slipType").val() || state.slipType || "").toString()
                    .toLowerCase();
                const categoryType = Number($("#leadCategoryType").val() || 2);
                return normalizedSlipType === "quotation" || categoryType === 1;
            }

            function shouldShowShares(slipType) {
                const normalizedSlipType = (slipType || $("#slipType").val() || state.slipType || "").toString()
                    .toLowerCase();
                return normalizedSlipType === VALIDATION_CONFIG.SLIP_TYPE && !isQuotationSlipMode(
                    normalizedSlipType);
            }

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            function initializeReinsurerTable() {
                if (state.dataTable) {
                    state.dataTable.destroy();
                }

                state.dataTable = $("#reinsurersTable").DataTable({
                    responsive: true,
                    pageLength: 10,
                    paging: false,
                    searching: false,
                    info: false,
                    order: [
                        [0, "asc"]
                    ],
                    language: {
                        emptyTable: "No reinsurers selected yet. Add reinsurers using the form above.",
                    },
                    columnDefs: [{
                        targets: -1,
                        orderable: false,
                        searchable: false,
                        className: "text-start",
                    }],
                });
            }

            function initializeReinsurerSelect() {
                $("#availableReinsurers").select2({
                    placeholder: "Search and select reinsurer...",
                    allowClear: true,
                    width: "100%",
                    dropdownParent: $("#leadModal"),
                    ajax: {
                        url: "{{ route('pipeline.search_reinsurers') }}",
                        method: "GET",
                        dataType: "json",
                        delay: 300,
                        data: function(params) {
                            return {
                                q: params.term || "",
                                page: params.page || 1,
                                cedantId: $("#lead_cedant_id").val(),
                                stage: 'lead',
                                oppId: $("#leadOpportunityId").val() || '',
                            };
                        },
                        processResults: function(data) {
                            state.bdReinsurers = data.results;
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination && data.pagination.more,
                                },
                            };
                        },
                        cache: true,
                        error: function(xhr, status, error) {
                            console.error("Reinsurer search error:", error);
                            showAlert("Failed to load reinsurers", "error");
                        },
                    },
                    templateResult: formatReinsurerOption,
                    templateSelection: formatReinsurerSelection,
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                });
            }

            function formatReinsurerOption(reinsurer) {
                if (reinsurer.loading) return reinsurer.text;
                if (!reinsurer.name) return reinsurer.text;

                return `
                    <div class="reinsurer-option">
                        <div><strong>${escapeHtml(reinsurer.name)}</strong></div>
                        <div><small class="text-muted">${escapeHtml(reinsurer.country)} | Email: ${escapeHtml(reinsurer.email)}</small></div>
                    </div>
                `;
            }

            function formatReinsurerSelection(reinsurer) {
                if (!reinsurer.id) return reinsurer.text;

                let option = $("#availableReinsurers").find(`option[value='${reinsurer.id}']`);
                option.attr("data-name", reinsurer.name || "");
                option.attr("data-email", reinsurer.email || "");
                option.attr("data-country", reinsurer.country || "");

                return `${escapeHtml(reinsurer.name)} (${escapeHtml(reinsurer.email)}) - ${escapeHtml(reinsurer.country)}`;
            }

            function addReinsurer() {
                const selectedOption = $("#availableReinsurers option:selected");
                const slipType = $("#slipType").val() || state.slipType;
                const writtenSharePercent = parseFloat($("#reinsurerShare").val());
                const showShareColumn = shouldShowShares(slipType);
                const configuredTotalWrittenShare = parseFloat($("#leadTotalReinsurerShare").val()) ||
                    VALIDATION_CONFIG.MAX_PERCENTAGE;

                if (!selectedOption.val()) {
                    toastr.warning('Please select a reinsurer from the dropdown.', 'Select Reinsurer');
                    return;
                }

                if (showShareColumn) {
                    if (!writtenSharePercent || writtenSharePercent <= 0 || writtenSharePercent >
                        configuredTotalWrittenShare) {
                        Swal.fire({
                            icon: "error",
                            title: "Invalid Written Share",
                            text: `Please enter a valid written share percentage between 0.01% and ${configuredTotalWrittenShare.toFixed(2)}%.`,
                            confirmButtonColor: "#3085d6",
                        });
                        $("#reinsurerShare").focus();
                        return;
                    }

                    const currentTotalPlacedShares = calculateTotalPlacedShares();
                    if (currentTotalPlacedShares + writtenSharePercent > configuredTotalWrittenShare) {
                        const remainingCapacity = Math.max(configuredTotalWrittenShare - currentTotalPlacedShares,
                            0);
                        Swal.fire({
                            icon: "warning",
                            title: "Insufficient Capacity",
                            text: `Maximum available share is ${remainingCapacity.toFixed(2)}%. Total placed shares cannot exceed Total Written Share (${configuredTotalWrittenShare.toFixed(2)}%).`,
                            confirmButtonColor: "#f39c12",
                        });
                        return;
                    }
                }

                if (state.selectedReinsurers.has(selectedOption.val())) {
                    Swal.fire({
                        icon: "info",
                        title: "Already Selected",
                        text: "This reinsurer has already been added to the list.",
                        confirmButtonColor: "#3085d6",
                    });
                    return;
                }

                const reinsurerData = {
                    id: selectedOption.val(),
                    name: selectedOption.data("name"),
                    email: selectedOption.data("email"),
                    country: selectedOption.data("country"),
                    writtenShare: showShareColumn ? writtenSharePercent : 0,
                };

                addReinsurerToTable(reinsurerData, slipType);
                state.selectedReinsurers.add(reinsurerData.id);
                updateReinsurerCount();
                resetReinsurerForm();

                if (showShareColumn) {
                    toggleTotalWrittenShareField();
                    updateSharesDisplay();
                }

                const successMessage = showShareColumn ?
                    `${reinsurerData.name} has been successfully added with ${writtenSharePercent.toFixed(2)}% written share.` :
                    `${reinsurerData.name} has been successfully added.`;

                toastr.success(successMessage, 'Reinsurer Added!');
                queueLeadModalDraftSave();
            }

            function updateTableHeader(slipType) {
                const showShareColumn = shouldShowShares(slipType);

                const headerHtml = showShareColumn ? `
                    <tr>
                        <th style="width: 70%">Reinsurer</th>
                        <th style="width: 20%">Written Share (%)</th>
                        <th style="width: 10%">Action</th>
                    </tr>
                ` : `
                    <tr>
                        <th style="width: 80%">Reinsurer</th>
                        <th style="width: 20%">Action</th>
                    </tr>
                `;

                $('#reinsurersTable thead').html(headerHtml);
            }

            function addReinsurerToTable(reinsurerData, slipType) {
                const showShareColumn = shouldShowShares(slipType);
                const isDeclined = reinsurerData.is_declined === true || reinsurerData.is_declined === 1 ||
                    reinsurerData.is_declined === '1';
                const declineReason = (reinsurerData.decline_reason || '').toString().trim();

                const shareColumnHtml = `
                    <td class="text-start share-column" ${!showShareColumn ? 'style="display: none;"' : ''}>
                        <div class="share-display">
                            <strong>${showShareColumn ? (isDeclined ? '--' : reinsurerData.writtenShare.toFixed(2) + '%') : 'N/A'}</strong>
                        </div>
                    </td>
                `;

                const declineNoteHtml = isDeclined ?
                    `<div><small class="text-danger">Declined${declineReason ? `: ${escapeHtml(declineReason)}` : ''}</small></div>` :
                    '';

                const actionHtml = isDeclined ?
                    `<span class="badge bg-danger">Declined</span>` :
                    `
                        <button type="button" class="btn btn-primary btn-sm contacts-reinsurer"
                                data-reinsurer-id="${reinsurerData.id}"
                                title="Contacts">
                            <i class="bx bx-book"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm remove-reinsurer"
                                data-reinsurer-id="${reinsurerData.id}"
                                title="Remove Reinsurer">
                            <i class="bx bx-trash"></i>
                        </button>
                    `;

                const rowHtml = `
                    <tr data-reinsurer-id="${reinsurerData.id}" data-is-declined="${isDeclined ? 1 : 0}" ${showShareColumn ? `data-written-share="${isDeclined ? 0 : reinsurerData.writtenShare}"` : 'data-written-share="0"'}>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="fw-medium">${escapeHtml(reinsurerData.name)}</div>
                                    <small class="text-muted">(${escapeHtml(reinsurerData.email)}) - ${escapeHtml(reinsurerData.country)}</small>
                                    ${declineNoteHtml}
                                </div>
                            </div>
                        </td>
                        ${shareColumnHtml}
                        <td class="text-start">
                            ${actionHtml}
                        </td>
                    </tr>
                `;

                state.dataTable.row.add($(rowHtml)).draw();
            }

            function removeReinsurer(reinsurerID, row) {
                const reinsurerName = row.find("td:first .fw-medium").text();

                Swal.fire({
                    title: "Remove Reinsurer?",
                    text: `Are you sure you want to remove ${reinsurerName} from the list?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, remove it!",
                    cancelButtonText: "Cancel",
                }).then((result) => {
                    if (result.isConfirmed) {
                        state.dataTable.row(row).remove().draw();
                        state.selectedReinsurers.delete(reinsurerID.toString());
                        updateReinsurerCount();
                        updateSharesDisplay();
                        toggleTotalWrittenShareField();

                        toastr.info(`${reinsurerName} has been removed from the list.`, 'Removed!');
                        queueLeadModalDraftSave();
                    }
                });
            }

            function calculateTotalPlacedShares() {
                let total = 0;
                state.dataTable.rows().every(function() {
                    const row = $(this.node());
                    const writtenShare = parseFloat(row.attr("data-written-share")) || 0;
                    total += writtenShare;
                });
                return total;
            }

            function updateSharesDisplay() {
                const leadTotalReinsurerShare = parseFloat($("#leadTotalReinsurerShare").val()) || 0;
                const totalPlacedShares = calculateTotalPlacedShares();
                const totalUnplacedShares = leadTotalReinsurerShare - totalPlacedShares;

                let sharesDisplay = $("#leadModal .total-shares-display");

                if (sharesDisplay.length === 0) {
                    const displayHtml = createSharesDisplayHTML();
                    $("#leadModal .selected-reinsurers-section").append(displayHtml);
                    sharesDisplay = $("#leadModal .total-shares-display");
                }

                updateShareValue(sharesDisplay, totalPlacedShares, totalUnplacedShares, leadTotalReinsurerShare);
                updateProgressBar(sharesDisplay, totalPlacedShares, leadTotalReinsurerShare);

                $("#totalPlacedShares").val(totalPlacedShares.toFixed(2));
                $("#totalUnplacedShares").val(totalUnplacedShares.toFixed(2));
                $("#retainedShareValue").val(totalUnplacedShares.toFixed(2));
            }

            function parseReinsurersPayload(rawPayload) {
                if (!rawPayload) {
                    return [];
                }

                if (Array.isArray(rawPayload)) {
                    return rawPayload;
                }

                if (typeof rawPayload === "string") {
                    try {
                        const parsed = JSON.parse(rawPayload);
                        return Array.isArray(parsed) ? parsed : [];
                    } catch (e) {
                        return [];
                    }
                }

                return [];
            }

            function hydrateLeadReinsurers(reinsurersPayload) {
                const reinsurers = parseReinsurersPayload(reinsurersPayload);
                const slipType = $("#slipType").val() || state.slipType || VALIDATION_CONFIG.SLIP_TYPE;
                const showShareColumn = shouldShowShares(slipType);

                if (!state.dataTable) {
                    initializeReinsurerTable();
                }

                updateTableHeader(slipType);
                state.dataTable.clear().draw();
                state.selectedReinsurers.clear();

                reinsurers.forEach((reinsurer) => {
                    const reinsurerId = reinsurer.reinsurer_id ?? reinsurer.id;
                    if (!reinsurerId) {
                        return;
                    }

                    const writtenShareRaw = parseFloat(reinsurer.written_share ?? 0);
                    const writtenShare = Number.isFinite(writtenShareRaw) ? writtenShareRaw : 0;
                    const isDeclined = reinsurer.is_declined === true || reinsurer.is_declined === 1 ||
                        reinsurer.is_declined === '1';

                    const reinsurerData = {
                        id: reinsurerId,
                        name: reinsurer.reinsurer_name || reinsurer.name || "Unknown Reinsurer",
                        email: reinsurer.email || "-",
                        country: reinsurer.country || "-",
                        writtenShare: showShareColumn ? (isDeclined ? 0 : writtenShare) : 0,
                        is_declined: isDeclined,
                        decline_reason: reinsurer.decline_reason || "",
                    };

                    addReinsurerToTable(reinsurerData, slipType);
                    state.selectedReinsurers.add(reinsurerId.toString());
                });

                updateReinsurerCount();
                toggleTotalWrittenShareField();

                if (showShareColumn) {
                    updateSharesDisplay();
                } else {
                    $("#leadModal .total-shares-display").hide();
                }
            }

            function toggleShareFields(slipType) {
                const showShareColumn = shouldShowShares(slipType);
                const $shareFields = $('.fac-rates');
                const $sharesDisplay = $("#leadModal .total-shares-display");

                if (showShareColumn) {
                    $shareFields.show();
                    $sharesDisplay.show();
                } else {
                    $shareFields.hide();
                    $sharesDisplay.hide();
                }
            }

            function createSharesDisplayHTML() {
                return `
                    <div class="total-shares-display d-block mt-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="shares-card placed-shares">
                                    <div class="shares-icon">
                                        <i class="bx bx-check-circle"></i>
                                    </div>
                                    <div class="shares-info">
                                        <span class="shares-label">Placed Shares</span>
                                        <span class="shares-value placed-value">0.00%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="shares-card unplaced-shares">
                                    <div class="shares-icon">
                                        <i class="bx bx-time-five"></i>
                                    </div>
                                    <div class="shares-info">
                                        <span class="shares-label">Unplaced Shares</span>
                                        <span class="shares-value unplaced-value">100.00%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shares-progress mt-2">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success placed-progress" role="progressbar"
                                    style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            function updateShareValue(sharesDisplay, totalPlaced, totalUnplaced, targetTotal) {
                const placedValueClass = totalPlaced === targetTotal ? "text-success" :
                    totalPlaced > targetTotal ? "text-danger" : "text-primary";

                sharesDisplay.find(".placed-value")
                    .removeClass("text-success text-danger text-primary text-warning")
                    .addClass(placedValueClass)
                    .text(`${totalPlaced.toFixed(2)}%`);

                const unplacedValueClass = totalUnplaced === 0 ? "text-success" :
                    totalUnplaced < 0 ? "text-danger" : "text-warning";

                sharesDisplay.find(".unplaced-value")
                    .removeClass("text-success text-danger text-primary text-warning")
                    .addClass(unplacedValueClass)
                    .text(`${totalUnplaced.toFixed(2)}%`);
            }

            function updateProgressBar(sharesDisplay, totalPlaced, targetTotal) {
                let progressWidth = 0;
                if (targetTotal > 0) {
                    progressWidth = Math.min((totalPlaced / targetTotal) * 100, 100);
                }

                const progressClass = totalPlaced === targetTotal ? "bg-success" :
                    totalPlaced > targetTotal ? "bg-danger" : "bg-primary";

                sharesDisplay.find(".placed-progress")
                    .removeClass("bg-success bg-danger bg-primary")
                    .addClass(progressClass)
                    .css("width", `${progressWidth}%`)
                    .attr("aria-valuenow", progressWidth);
            }

            function clearTermsConditionsValues() {
                const $termsContainer = $("#leadModal #termsConditions");
                if ($termsContainer.length === 0) {
                    return;
                }

                $termsContainer.find("textarea, input, select").each(function() {
                    if ($(this).is(":checkbox, :radio")) {
                        $(this).prop("checked", false);
                        return;
                    }
                    $(this).val("");
                });
            }

            function toggleReinsurerDependentSections() {
                const hasReinsurer = state.selectedReinsurers.size > 0;
                const $termsSection = $("#leadModal #termsConditions").closest(".form-section");
                const $documentsSection = $("#leadModal #documentsContent").closest(".form-section");

                $termsSection.toggle(hasReinsurer);
                $termsSection.prev("hr").toggle(hasReinsurer);

                $documentsSection.toggle(hasReinsurer);
                $documentsSection.prev("hr").toggle(hasReinsurer);
            }

            function togglePreviewSlipButton() {
                const hasReinsurer = state.selectedReinsurers.size > 0;
                $("#previewSlipBtn").toggle(hasReinsurer);
            }

            function updateReinsurerCount() {
                $("#reinsurerCount").text(state.selectedReinsurers.size);
                toggleReinsurerDependentSections();
                togglePreviewSlipButton();

                if (state.selectedReinsurers.size === 0) {
                    clearTermsConditionsValues();
                }
            }

            function resetReinsurerForm() {
                $("#availableReinsurers").val(null).trigger("change");
                $("#reinsurerShare").val("");
            }

            function toggleTotalWrittenShareField() {
                const $totalWrittenShareInput = $("#leadTotalReinsurerShare");
                const reinsurerCount = state.selectedReinsurers.size;

                if (reinsurerCount > 0) {
                    $totalWrittenShareInput.prop("disabled", true).css({
                        "background-color": "#e9ecef",
                        "cursor": "not-allowed",
                        "opacity": "0.6"
                    });
                } else {
                    $totalWrittenShareInput.prop("disabled", false).css({
                        "background-color": "",
                        "cursor": "",
                        "opacity": ""
                    });
                }
            }

            function loadReinsurerContacts(reinsurerID) {
                const row = $(`tr[data-reinsurer-id="${reinsurerID}"]`);
                const reinsurerName = row.find("td:first .fw-medium").text();
                const opportunityId = $("#leadOpportunityId").val() || $("#contactsOpportunityId").val();
                const $button = $(`.contacts-reinsurer[data-reinsurer-id="${reinsurerID}"]`);

                if (!reinsurerID) {
                    showAlert("Reinsurer ID not found", "error");
                    return;
                }

                if (!opportunityId) {
                    showAlert("Opportunity ID is missing for this lead.", "error");
                    return;
                }

                const originalHtml = $button.html();
                $button.html('<i class="bx bx-loader bx-spin"></i>').prop("disabled", true);

                $.ajax({
                    url: `/reinsurers/${reinsurerID}/contacts`,
                    method: "POST",
                    data: {
                        opportunity_id: opportunityId,
                        reinsurer_id: reinsurerID,
                    },
                    success: function(response) {
                        if (response.success) {
                            $("#contactsOpportunityId").val(opportunityId);
                            populateContactsModal(response.data, response.data.reinsurer.name);
                            state.suppressLeadModalReset = true;

                            if ($("#leadModal").hasClass("show")) {
                                $("#leadModal").one("hidden.bs.modal.contacts-transition", function() {
                                    $("#contactsModal").modal("show");
                                });
                                $("#leadModal").modal("hide");
                            } else {
                                $("#contactsModal").modal("show");
                            }
                        } else {
                            showAlert(response.message || "Failed to fetch contacts", "error");
                        }
                    },
                    error: function(xhr) {
                        const errorMessages = {
                            404: "Reinsurer contacts not found",
                            403: "Access denied to reinsurer contacts",
                        };
                        const errorMessage = errorMessages[xhr.status] ||
                            xhr.responseJSON?.message ||
                            "Failed to fetch reinsurer contacts";
                        showAlert(errorMessage, "error");
                    },
                    complete: function() {
                        $button.html(originalHtml).prop("disabled", false);
                    },
                });
            }

            function populateContactsModal(contactData, reinsurerName) {
                $("#contactsModalLabel").html(
                    `<i class="bx bx-building me-1"></i>${escapeHtml(reinsurerName)} - Contact Management`
                );

                if (contactData.primary_contact) {
                    $("#primary-contacts .primary-name").val(contactData.primary_contact.name || "N/A");
                    $("#primary-contacts .primary-email").val(contactData.primary_contact.email || "N/A");
                    $("#primary-contacts .primary-contact_id").val(contactData.primary_contact.id);
                }

                const $departmentContacts = $("#departmentContacts");
                $departmentContacts.empty();

                if (contactData.department_contacts && contactData.department_contacts.length > 0) {
                    contactData.department_contacts.forEach((contact, index) => {
                        const contactHtml = createContactItemHtml(contact, index);
                        $departmentContacts.append(contactHtml);
                    });
                } else {
                    $departmentContacts.html(`
                <div class="text-center py-4">
                    <i class="bx bx-info-circle bx-2x text-muted mb-2 fs-15"></i>
                    <p class="text-muted">No department contacts found for this reinsurer.</p>
                </div>
            `);
                }
            }

            function createContactItemHtml(contact, index) {
                const showLabels = index === 0;

                return `
            <div class="contact-item rounded px-3 pb-1" data-contact-id="${contact.id ?? ""}">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        ${showLabels ? '<label class="form-label fw-semibold mb-1">Contact Name</label>' : ""}
                        <input type="text" class="form-control-plaintext contact-name"
                            value="${escapeHtml(contact.name || "")}" data-field="name">
                    </div>
                    <div class="col-md-6">
                        ${showLabels ? '<label class="form-label fw-semibold mb-1">Email</label>' : ""}
                        <input type="email" class="form-control-plaintext contact-email"
                            value="${escapeHtml(contact.email || "")}" data-field="email">
                    </div>
                    <div class="col-md-2">
                        ${showLabels ? '<label class="form-label fw-semibold mb-1">CC Email</label>' : ""}
                        <div class="form-check mt-2 px-0">
                            <input class="form-check-input mailc-checkbox" type="checkbox"
                                ${contact.cc_email ? "checked" : ""} data-field="cc_email">
                            <label class="form-check-label cc-email-indicator">
                                <i class="bx bx-envelope envlope-ico"></i>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </div>
        `;
            }

            function saveContactsModal() {
                const {
                    contacts,
                    errors
                } = collectContacts();

                if (errors.length > 0) {
                    showAlert(errors[0], "warning");
                    return;
                }

                if (contacts.length === 0) {
                    showAlert("Please add at least one contact.", "warning");
                    return;
                }

                const $submitBtn = $("#submitContactModal");
                $submitBtn.prop("disabled", true);
                const opportunity_id = $("#contactsOpportunityId").val() || $("#leadOpportunityId").val();

                if (!opportunity_id) {
                    showAlert("Opportunity ID is missing. Please reopen the modal and try again.", "error");
                    $submitBtn.prop("disabled", false);
                    return;
                }

                $.ajax({
                    url: "{{ route('rein.contacts.update') }}",
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        opportunity_id: opportunity_id,
                        contacts: contacts
                    }),
                    success: function(response) {
                        if (response.success) {
                            showAlert("Contact information has been updated.", "success");
                            $("#contactsModal").modal("hide");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Contact update error:", error);
                        const validationErrors = xhr.responseJSON?.errors;
                        if (validationErrors) {
                            const firstErrorKey = Object.keys(validationErrors)[0];
                            const firstError = firstErrorKey ? validationErrors[firstErrorKey]?.[0] :
                                null;
                            showAlert(firstError || "Failed to update contacts", "error");
                            return;
                        }

                        showAlert(xhr.responseJSON?.message || "Failed to update contacts", "error");
                    },
                    complete: function() {
                        $submitBtn.prop("disabled", false);
                    }
                });
            }

            function collectContacts() {
                const contacts = [];
                const errors = [];

                const isValidEmail = (email) => {
                    if (!email) return false;
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                };

                const normalizeText = (value) => {
                    return (value || "").toString().trim();
                };

                const normalizeId = (value) => {
                    const parsed = parseInt(value, 10);
                    return Number.isInteger(parsed) ? parsed : null;
                };

                const primaryData = {
                    id: normalizeId($("#primary-contacts .primary-contact_id").val()),
                    name: normalizeText($("#primary-contacts .primary-name").val()),
                    email: normalizeText($("#primary-contacts .primary-email").val()),
                    cc_email: false,
                    is_primary: true,
                };

                if (primaryData.name.toUpperCase() === "N/A") {
                    primaryData.name = "";
                }

                if (primaryData.email.toUpperCase() === "N/A") {
                    primaryData.email = "";
                }

                if (primaryData.name || primaryData.email) {
                    if (!primaryData.name || !primaryData.email) {
                        errors.push("Primary contact must include both name and email.");
                    } else if (!isValidEmail(primaryData.email)) {
                        errors.push("Primary contact email format is invalid.");
                    } else if (primaryData.id !== null) {
                        contacts.push(primaryData);
                    }
                }

                $("#departmentContacts .contact-item").each(function(index) {
                    const name = normalizeText($(this).find(".contact-name").val());
                    const email = normalizeText($(this).find(".contact-email").val());

                    if (!name && !email) {
                        return;
                    }

                    const contactData = {
                        id: normalizeId($(this).data("contact-id")),
                        name,
                        email,
                        cc_email: $(this).find(".mailc-checkbox").is(":checked"),
                        is_primary: false,
                    };

                    if (!contactData.name || !contactData.email) {
                        errors.push(`Department contact ${index + 1} must include both name and email.`);
                        return;
                    }

                    if (!isValidEmail(contactData.email)) {
                        errors.push(`Department contact ${index + 1} email format is invalid.`);
                        return;
                    }

                    if (contactData.id !== null) {
                        contacts.push(contactData);
                    }
                });

                return {
                    contacts,
                    errors
                };
            }

            function validateLeadForm() {
                const errors = [];

                const slipType = $("#slipType").val() || state.slipType;
                const $termsContainer = $("#leadModal #termsConditions");
                const $documentsContent = $("#leadModal #documentsContent");
                const $documentFields = $("#leadModal #documentFields");

                const noHeadersMessage = ($termsContainer.text() || "")
                    .replace(/\s+/g, " ")
                    .trim()
                    .toLowerCase();
                const hasScheduleHeaderFields = $termsContainer.find(".form-group").length > 0;
                const hasNoHeadersPlaceholder = noHeadersMessage.includes("no schedule headers configured");

                const noDocumentsMessage = ($documentsContent.text() || "")
                    .replace(/\s+/g, " ")
                    .trim()
                    .toLowerCase();
                const hasSupportingDocumentFields = $documentFields.find(".document-field-group").length > 0;
                const hasNoDocumentsPlaceholder = noDocumentsMessage.includes(
                    "no documents available for this stage");

                $("#leadForm .form-inputs").each(function() {
                    if (!validateField($(this))) {
                        const fieldLabel = getFieldLabel($(this));
                        errors.push(`${fieldLabel}: Please check the entered value`);
                    }
                });

                if (state.selectedReinsurers.size < VALIDATION_CONFIG.MIN_REINSURERS) {
                    errors.push("<b>Reinsurer Selection:</b> Please add at least one reinsurer");
                }

                const writtenSharePercent = parseFloat($("#reinsurerShare").val());
                const showShareColumn = shouldShowShares(slipType);

                if (showShareColumn) {
                    const totalWrittenShare = parseFloat($("#leadTotalReinsurerShare").val()) || 0;
                    if (totalWrittenShare === 0) {
                        errors.push("<b>Total Written Share:</b> Please enter the total written share percentage");
                    }

                    const totalPlacedShares = calculateTotalPlacedShares();
                    const sharesDifference = Math.abs(totalWrittenShare - totalPlacedShares);
                    const TOLERANCE = 0.01;

                    if (sharesDifference > TOLERANCE) {
                        const totalUnplacedShares = totalWrittenShare - totalPlacedShares;
                        errors.push(
                            `<b>Share Allocation Mismatch:</b> Total placed shares (${totalPlacedShares.toFixed(2)}%) must equal Total Written Share (${totalWrittenShare.toFixed(2)}%). Unplaced: ${totalUnplacedShares.toFixed(2)}%`
                        );
                    }
                }

                if (!hasScheduleHeaderFields || hasNoHeadersPlaceholder) {
                    errors.push(
                        "<b>Schedule Headers:</b> No schedule headers configured for this class/group and business type."
                    );
                }

                if (!hasSupportingDocumentFields || hasNoDocumentsPlaceholder) {
                    errors.push(
                        "<b>Supporting Documents:</b> No supporting documents configured for this class/group and stage."
                    );
                }

                if (typeof window.pipelineManager !== 'undefined' &&
                    typeof window.pipelineManager.getAllUploadedFiles === 'function') {

                    const allUploadedFiles = window.pipelineManager.getAllUploadedFiles();
                    const uploadedFileNames = Object.values(allUploadedFiles)
                        .flatMap(innerArray => Object.values(innerArray).map(fileObj => fileObj.fileName));

                    $('#leadForm input[type="file"][required]').each(function() {
                        const fileName = $(this).attr('name');
                        if (!uploadedFileNames.includes(fileName)) {
                            const fieldLabel = getFieldLabel($(this));
                            errors.push(`<b>Required File:</b> ${fieldLabel || fileName}`);
                        }
                    });
                }

                return {
                    isValid: errors.length === 0,
                    errors: errors,
                };
            }

            function validateField($field) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const rawValue = $field.val();
                const fieldValue = typeof rawValue === "string" ? rawValue.trim() : "";
                const isRequired = $field.prop("required") || VALIDATION_CONFIG.REQUIRED_FIELDS.includes(fieldName);

                clearFieldValidation($field);

                // Commission rate is not validated for quotation slips.
                if (fieldName === "brokerage_rate" && isQuotationSlipMode()) {
                    return true;
                }

                if (isRequired && !fieldValue) {
                    showFieldError($field, "This field is required");
                    return false;
                }

                if (fieldValue) {
                    const validation = getFieldValidation($field, fieldValue);
                    if (!validation.isValid) {
                        showFieldError($field, validation.message);
                        return false;
                    }
                }

                if (fieldValue) {
                    $field.addClass("is-v");
                }

                return true;
            }

            function getFieldValidation($field, fieldValue) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const numericValue = parseFloat(fieldValue.replace(/,/g, ""));

                if (isCurrencyField($field, fieldName)) {
                    if (!FIELD_VALIDATORS.currency.pattern.test(fieldValue.replace(/,/g, ""))) {
                        return {
                            isValid: false,
                            message: FIELD_VALIDATORS.currency.message
                        };
                    }
                    if (numericValue <= 0) {
                        return {
                            isValid: false,
                            message: "Amount must be greater than 0"
                        };
                    }
                }

                if (isPercentageField(fieldName)) {
                    if (!FIELD_VALIDATORS.percentage.pattern.test(fieldValue)) {
                        return {
                            isValid: false,
                            message: FIELD_VALIDATORS.percentage.message
                        };
                    }
                    if (numericValue < FIELD_VALIDATORS.percentage.min ||
                        numericValue > FIELD_VALIDATORS.percentage.max) {
                        return {
                            isValid: false,
                            message: `Percentage must be between ${FIELD_VALIDATORS.percentage.min} and ${FIELD_VALIDATORS.percentage.max}`
                        };
                    }
                }

                if (isEmailField($field, fieldName)) {
                    if (!FIELD_VALIDATORS.email.pattern.test(fieldValue)) {
                        return {
                            isValid: false,
                            message: FIELD_VALIDATORS.email.message
                        };
                    }
                }

                return {
                    isValid: true
                };
            }

            function isCurrencyField($field, fieldName) {
                return $field.closest(".currency-input").length ||
                    fieldName.includes("premium") ||
                    fieldName.includes("sum_insured");
            }

            function isPercentageField(fieldName) {
                return fieldName.includes("rate") || fieldName.includes("Share");
            }

            function isEmailField($field, fieldName) {
                return $field.attr("type") === "email" || fieldName.includes("email");
            }

            function clearFieldValidation($field) {
                $field.removeClass("is-invalid is-v");
                $field.siblings(".invalid-feedback").remove();
            }

            function showFieldError($field, message) {
                $field.addClass("is-invalid");
                $field.after(`<div class="invalid-feedback">${message}</div>`);
            }

            function getFieldLabel($field) {
                return $field.closest(".form-group")
                    .find("label")
                    .first()
                    .text()
                    .replace("*", "")
                    .trim();
            }

            function handleFormSubmission(e) {
                e.preventDefault();

                const validation = validateLeadForm();

                if (!validation.isValid) {
                    displayValidationErrors(validation.errors);
                    scrollToFirstError();
                    return false;
                }

                const $submitBtn = $("#leadForm button[type='submit']");
                const originalBtnContent = $submitBtn.html();

                $submitBtn.html('<i class="bx bx-loader-alt bx-spin me-1"></i> Sending Lead...')
                    .prop("disabled", true);

                const opportunityId = $("#leadOpportunityId").val() ||
                    $("#leadForm input[name='opportunity_id']").val() || "";
                const currentStage = $("#leadCurrentStage").val() ||
                    $("#leadForm input[name='current_stage']").val() || "lead";

                const formData = prepareFormData();

                $.ajax({
                    url: $("#leadForm").attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 30000,
                    success: function(response) {
                        if (response.success) {
                            handleSubmissionSuccess(opportunityId, currentStage);
                        } else {
                            throw new Error(response.message || "Submission failed");
                        }
                    },
                    error: function(xhr, status, error) {
                        handleSubmissionError(xhr, status, error);
                    },
                    complete: function() {
                        $submitBtn.html(originalBtnContent).prop("disabled", false);
                    },
                });
            }

            function displayValidationErrors(errors) {
                let errorHtml = '<ul class="m-0 p-0">';
                errors.forEach((error) => {
                    errorHtml += `<li class="m-0 p-0" style="text-align: start;">${error}</li>`;
                });
                errorHtml += "</ul>";

                Swal.fire({
                    icon: "error",
                    title: "Validation Failed",
                    html: errorHtml,
                    confirmButtonColor: "#dc3545",
                });
            }

            function scrollToFirstError() {
                const $firstError = $("#leadForm .is-invalid").first();
                if ($firstError.length) {
                    $firstError[0].scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                    setTimeout(() => $firstError.focus(), 500);
                }
            }

            function openLeadEmailModal(opportunityId, currentStage) {
                if (!opportunityId) {
                    toastr.warning("Opportunity ID missing. Unable to open email modal.");
                    return;
                }

                if (typeof window.pipelineManager !== "undefined") {
                    if (typeof window.pipelineManager.checkEmailConnectionBeforeLoad === "function") {
                        window.pipelineManager.checkEmailConnectionBeforeLoad(opportunityId, currentStage);
                        return;
                    }

                    if (typeof window.pipelineManager.loadBdEssentials === "function") {
                        window.pipelineManager.loadBdEssentials(opportunityId, currentStage);
                        return;
                    }
                }

                toastr.warning("Email modal is not available right now.");
            }

            function handleSubmissionSuccess(opportunityId, currentStage) {
                Swal.fire({
                    icon: "success",
                    title: "Lead Saved Successfully!",
                    text: "Your lead has been submitted. Open email modal now?",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Open Email",
                    cancelButtonText: "No",
                }).then((result) => {
                    clearLeadModalDraft();

                    if (typeof window.pipelineManager !== 'undefined' &&
                        typeof window.pipelineManager.reloadAllTables === 'function') {
                        window.pipelineManager.reloadAllTables();
                    }

                    if (typeof window.pipelineManager !== 'undefined' &&
                        typeof window.pipelineManager.loadChartData === 'function') {
                        window.pipelineManager.loadChartData();
                    }

                    if (result.isConfirmed) {
                        $("#leadModal").one("hidden.bs.modal", function() {
                            openLeadEmailModal(opportunityId, currentStage || "lead");
                        });
                        $("#leadModal").modal("hide");
                    } else {
                        $("#leadModal").modal("hide");
                    }
                });
            }

            function handleSubmissionError(xhr, status, error) {
                let errorMessage = "An unexpected error occurred while sending the lead.";

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join("<br>");
                } else if (xhr.responseJSON?.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (status === "timeout") {
                    errorMessage = "Request timed out. Please check your connection and try again.";
                } else if (xhr.status === 0) {
                    errorMessage = "Network error. Please check your internet connection.";
                } else if (xhr.status === 404) {
                    errorMessage = "The submission endpoint was not found. Please contact support.";
                } else if (xhr.status === 500) {
                    errorMessage = "Server error occurred. Please try again later or contact support.";
                }

                Swal.fire({
                    icon: "error",
                    title: "Submission Failed",
                    html: errorMessage,
                    confirmButtonColor: "#dc3545",
                });
            }

            function prepareFormData() {
                const formData = new FormData();
                const $form = $("#leadForm");

                $form.find("input:not([type='file']), select, textarea").each(function() {
                    const $element = $(this);
                    const name = $element.attr("name");
                    const type = $element.attr("type");

                    if (!name) return;
                    if (name === "special_conditions" || name === "specialConditionsContent") return;

                    if (type === "checkbox" || type === "radio") {
                        if ($element.is(":checked")) {
                            formData.append(name, $element.val());
                        }
                    } else {
                        const value = $element.val();
                        if (value !== null && value !== "") {
                            formData.append(name, value);
                        }
                    }
                });

                const breakdownHtml = ($("#specialConditionsContent").val() || "").trim();
                const breakdownPlain = ($("#specialConditions").val() || "").trim();
                if (breakdownHtml) {
                    formData.append("special_conditions", breakdownHtml);
                } else if (breakdownPlain) {
                    formData.append("special_conditions", breakdownPlain);
                }

                if (typeof window.pipelineManager !== 'undefined' &&
                    typeof window.pipelineManager.getAllUploadedFiles === 'function') {

                    const allUploadedFiles = window.pipelineManager.getAllUploadedFiles();
                    Object.entries(allUploadedFiles).forEach(([fieldId, filesData]) => {
                        filesData.forEach((file) => {
                            formData.append('facultative_files[]', file);
                            formData.append('facultative_document_types[]', file.documentTypeName ||
                                file.fileName ||
                                'additionalDocuments');
                            if (Number.isInteger(file.documentTypeId)) {
                                formData.append('facultative_document_type_ids[]', file
                                    .documentTypeId);
                            }
                        });
                    });
                }

                const defaultDocs = [];
                $("#leadModal #documentFields .document-field-group").each(function() {
                    const $group = $(this);
                    const isAdditionalDocument = String($group.data("is-additional-document")) === "1";
                    if (isAdditionalDocument) {
                        return;
                    }

                    const rawId = ($group.data("document-id") ?? "").toString().trim();
                    const titleFromInput = ($group.find(".supporting-doc-title-input").first().val() || "")
                        .toString()
                        .trim();
                    const titleFromData = ($group.data("document-name") || "").toString().trim();
                    const documentName = titleFromInput || titleFromData;

                    if (!rawId && !documentName) {
                        return;
                    }

                    defaultDocs.push({
                        id: /^\d+$/.test(rawId) ? parseInt(rawId, 10) : rawId,
                        name: documentName,
                    });
                });

                defaultDocs.forEach((doc, index) => {
                    if (doc.id !== null && doc.id !== undefined && doc.id !== "") {
                        formData.append(`facultative_default_docs[${index}][id]`, doc.id);
                    }
                    if (doc.name) {
                        formData.append(`facultative_default_docs[${index}][name]`, doc.name);
                    }
                });

                const reinsurersData = [];
                let totalPlacedShares = 0;

                state.dataTable.rows().every(function() {
                    const $row = $(this.node());
                    const writtenShare = parseFloat($row.attr("data-written-share")) || 0;
                    const isDeclined = parseInt($row.attr("data-is-declined"), 10) === 1;
                    totalPlacedShares += writtenShare;

                    reinsurersData.push({
                        id: $row.data("reinsurer-id"),
                        written_share: writtenShare,
                        is_declined: isDeclined,
                    });
                });

                formData.append("reinsurers_data", JSON.stringify(reinsurersData));
                formData.append("total_placed_shares", totalPlacedShares.toFixed(2));
                const totalWrittenShare = parseFloat($("#leadTotalReinsurerShare").val()) || 0;
                formData.append("total_unplaced_shares", (totalWrittenShare - totalPlacedShares).toFixed(2));

                return formData;
            }

            function previewCoverSlip(printoutType = 0) {
                const postForm = $('#proposal-quoteslip-form');
                if (state.selectedReinsurers.size === 0) {
                    toastr.warning('Please add at least one reinsurer before previewing the slip.');
                    return;
                }

                if (!postForm.length) {
                    toastr.error('Preview form not found.');
                    return;
                }

                postForm.find('input[type="hidden"]:not([name="_token"])').remove();

                const formData = prepareFormData();
                const categoryType = Number($("#leadCategoryType").val() || formData.get("category_type") || 2);
                const targetAction = categoryType === 1 ? PREVIEW_ROUTES.quotation : PREVIEW_ROUTES.facultative;
                postForm.attr("action", targetAction);

                // if (state.dataTable.rows().length > 0) {
                //     toastr.warning('Please select at least 1 reinsurer', 'Select Reinsurer');
                //     return;
                // }

                for (let [key, value] of formData.entries()) {
                    if (value instanceof File) {
                        continue;
                    }

                    postForm.append($('<input>', {
                        type: 'hidden',
                        name: key,
                        value: value
                    }));
                }

                postForm.append($('<input>', {
                    type: 'hidden',
                    name: 'printout_flag',
                    value: printoutType
                }));

                postForm.submit();
            }

            class BreakdownEditor {
                constructor() {
                    this.quill = null;
                    this.isPreviewMode = false;
                    this.currentTextarea = null;
                    this.textareaId = null;
                    this.currentFieldLabel = "";
                    this.modal = null;
                    this.pendingContent = "";
                    this.initTimer = null;
                    this.initToken = 0;

                    this.templates = {
                        standard: ``,
                    };

                    this.init();
                }

                init() {
                    this.setupEventListeners();
                    this.initializeModal();
                    this.enhanceTextarea();
                }

                enhanceTextarea() {
                    const isInlineEditable = (textareaId) =>
                        (textareaId || "").toLowerCase() === "coveragedetails";

                    $("textarea.breakdown-textarea")
                        .addClass("editor-enabled")
                        .each(function() {
                            const $textarea = $(this);
                            const textareaId = $textarea.attr("id");

                            if (isInlineEditable(textareaId)) {
                                $textarea.removeAttr("readonly").removeClass("editor-enabled");
                                return;
                            }

                            $textarea.attr("readonly", true);
                        })
                        .css({
                            cursor: "pointer",
                            background: "linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%)",
                            border: "2px solid #e9ecef",
                            transition: "all 0.3s ease",
                        });

                    $("#coverageDetails").css({
                        cursor: "text",
                        background: "",
                        border: "",
                        transition: "",
                    });
                }

                setupEventListeners() {
                    $(document).on("click", "textarea.breakdown-textarea", (e) => {
                        const $textarea = $(e.currentTarget);
                        const textareaId = $textarea.attr('id');
                        const schId = $textarea.data('sch_id');

                        if ((textareaId || "").toLowerCase() === "coveragedetails") {
                            return;
                        }

                        e.preventDefault();
                        this.openModal($textarea, textareaId, schId);
                    });

                    $(document).on("click", ".template-btn", (e) => {
                        const template = $(e.target).data("template");
                        this.applyTemplate(template);
                    });

                    $("#saveBreakdownBtn").on("click", () => this.saveChanges());
                    $("#previewBtn").on("click", () => this.togglePreview());

                    $("#previewSlipBtn").on("click", () => previewCoverSlip());

                    $("#breakdownModal")
                        .on("show.bs.modal", () => this.cleanupEditor())
                        .on("shown.bs.modal", () => this.initializeQuill())
                        .on("hidden.bs.modal", () => {
                            this.cleanupEditor();
                            this.currentTextarea = null;
                            this.textareaId = null;
                            this.currentFieldLabel = "";
                            state.returningFromBreakdown = true;
                            $("#leadModal").modal("show");
                        });
                }

                initializeModal() {
                    const modalElement = document.getElementById("breakdownModal");
                    if (!modalElement) {
                        console.error("Breakdown modal not found!");
                        return;
                    }
                    this.modal = new bootstrap.Modal(modalElement);
                }

                openModal($textarea, textareaId, schId) {
                    if (!this.modal) return;

                    this.currentTextarea = $(`#${textareaId}Content`);
                    this.textareaId = textareaId;

                    $("#schId").val(schId)

                    const fieldLabel = $textarea.closest(".form-group")
                        .find("label").first().text().trim();
                    this.currentFieldLabel = fieldLabel || "";

                    $("#breakdownModalLabel").html(
                        `<i class="bx bx-edit-alt me-2"></i>${fieldLabel || ""}`
                    );

                    state.suppressLeadModalReset = true;
                    $("#leadModal").modal("hide");
                    this.showLoading();
                    this.modal.show();
                }

                showLoading() {
                    $("#loadingOverlay").addClass("show");
                }

                hideLoading() {
                    $("#loadingOverlay").removeClass("show");
                }

                cleanupEditor() {
                    if (this.initTimer) {
                        clearTimeout(this.initTimer);
                        this.initTimer = null;
                    }

                    if (this.quill) {
                        try {
                            this.quill.off("text-change");
                            this.quill = null;
                        } catch (error) {
                            console.warn("Error removing Quill listeners:", error);
                        }
                    }

                    const container = document.getElementById("breakdownEditor");
                    if (container) {
                        const wrapper = container.closest(".quill-container");
                        if (wrapper) {
                            wrapper.querySelectorAll(".ql-toolbar").forEach((toolbar) => toolbar.remove());
                        }

                        container.innerHTML = "";
                        container.className = "";
                    }
                }

                initializeQuill() {
                    if (typeof Quill === "undefined") {
                        this.hideLoading();
                        console.error("Quill is not loaded");
                        return;
                    }

                    this.cleanupEditor();

                    const editorContainer = document.getElementById("breakdownEditor");
                    if (!editorContainer) {
                        this.hideLoading();
                        return;
                    }

                    const token = ++this.initToken;
                    this.initTimer = setTimeout(() => {
                        this.initTimer = null;

                        if (token !== this.initToken) {
                            return;
                        }

                        try {
                            this.quill = new Quill("#breakdownEditor", {
                                theme: "snow",
                                modules: {
                                    toolbar: [
                                        [{
                                            font: []
                                        }, {
                                            size: []
                                        }],
                                        [{
                                            header: [1, 2, 3, false]
                                        }],
                                        ["bold", "italic", "underline", "strike"],
                                        [{
                                            color: []
                                        }, {
                                            background: []
                                        }],
                                        [{
                                            script: "super"
                                        }, {
                                            script: "sub"
                                        }],
                                        [{
                                            list: "ordered"
                                        }, {
                                            list: "bullet"
                                        }],
                                        [{
                                            indent: "-1"
                                        }, {
                                            indent: "+1"
                                        }],
                                        [{
                                            align: []
                                        }],
                                        ["link"],
                                        ["clean"],
                                    ],
                                },
                            });

                            this.pendingContent = this.currentTextarea ? (this.currentTextarea.val() ||
                                "") : "";
                            if (this.pendingContent && this.pendingContent.trim()) {
                                this.quill.root.innerHTML = this.pendingContent;
                            }

                            this.quill.on("text-change", () => {
                                this.updateStatistics();
                                this.validateContent();
                            });

                            this.updateStatistics();
                        } catch (error) {
                            console.error("Error initializing Quill:", error);
                        }

                        this.hideLoading();
                    }, 500);
                }

                updateStatistics() {
                    if (!this.quill) return;
                }

                validateContent() {
                    if (!this.quill) return;
                }

                restorePendingContent() {
                    if (this.pendingContent && this.pendingContent.trim()) {
                        this.quill.root.innerHTML = this.pendingContent;
                        this.updateStatistics();
                        toastr.success("Data loaded successfully");
                        return true;
                    }
                    return false;
                }

                resolveHeaderKeyword() {
                    const fieldId = (this.textareaId || "").toLowerCase();
                    const fieldLabel = (this.currentFieldLabel || "").toLowerCase();

                    if (fieldId === "specialconditions" || fieldLabel.includes("sum insured breakdown")) {
                        return "sum insured breakdown";
                    }

                    return (this.currentFieldLabel || "").trim();
                }

                applyTemplate(templateName) {
                    if (!this.quill) return;

                    if (templateName === "standard") {
                        const classGroupCode = $("#leadClassGroupCode").val() || "";
                        const classCode = $("#leadClassCode").val() || "";
                        const opportunityId = $('#leadOpportunityId').val();

                        if (!classGroupCode && !classCode && !opportunityId) {
                            if (this.pendingContent && this.pendingContent.trim()) {
                                this.quill.root.innerHTML = this.pendingContent;
                                this.updateStatistics();
                                toastr.success("Data loaded successfully");
                            } else {
                                toastr.info("No class group/class selected to load template data");
                            }
                            return;
                        }

                        const $btn = $(".template-btn[data-template='standard']");
                        const origText = $btn.html();
                        $btn.html('<i class="bx bx-loader-alt bx-spin"></i> Loading...').prop("disabled", true);
                        const busType = $("#slipType").val() || "facultative";
                        const breakdownLabel = ($("#breakdownModalLabel").text() || this.currentFieldLabel ||
                            "").trim();
                        const headerKeyword = this.resolveHeaderKeyword();
                        const schId = $("#schId").val();

                        $.ajax({
                            url: "{{ route('bd.slip-template.headers') }}",
                            method: "GET",
                            data: {
                                class_group_code: classGroupCode,
                                class_code: classCode,
                                bus_type: busType,
                                class_group: classGroupCode,
                                class: classCode,
                                business_type: busType,
                                header_keyword: headerKeyword,
                                breakdown_label: breakdownLabel,
                                opportunity_id: opportunityId,
                                schedule_id: schId,
                            },
                            success: (response) => {
                                $btn.html(origText).prop("disabled", false);

                                const wording = String(response?.wording || response?.template
                                    ?.wording || "").trim();
                                if (response?.success && wording) {
                                    this.quill.root.innerHTML = wording;
                                    this.updateStatistics();
                                    toastr.success("Slip template wording loaded successfully");
                                    return;
                                }

                                if (!this.restorePendingContent()) {
                                    toastr.info(response?.message ||
                                        "No wording found in slip templates");
                                }
                            },
                            error: () => {
                                $btn.html(origText).prop("disabled", false);
                                if (!this.restorePendingContent()) {
                                    toastr.error("Failed to load template data");
                                }
                            }
                        });
                        return;
                    }

                    if (templateName === "clear") {
                        Swal.fire({
                            title: "Clear Content?",
                            text: "This will remove all current content. Continue?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d33",
                            confirmButtonText: "Yes, clear it!",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.quill.setContents([]);
                                toastr.info("Content cleared");
                            }
                        });
                        return;
                    }

                    if (this.templates[templateName]) {
                        this.quill.root.innerHTML = this.templates[templateName];
                        this.updateStatistics();
                        toastr.success("Template applied successfully");
                    }
                }

                togglePreview() {
                    const container = $(".quill-container");
                    const btn = $("#previewBtn");

                    if (this.isPreviewMode) {
                        container.removeClass("preview-mode");
                        btn.html('<i class="bx bx-show me-1"></i>Preview');
                        this.isPreviewMode = false;
                    } else {
                        container.addClass("preview-mode");
                        btn.html('<i class="bx bx-edit me-1"></i>Edit');
                        this.isPreviewMode = true;
                    }
                }

                saveChanges() {
                    if (!this.quill || !this.currentTextarea) {
                        toastr.error("No content to save");
                        return;
                    }

                    const html = this.quill.root.innerHTML;

                    const saveBtn = $("#saveBreakdownBtn");
                    const originalText = saveBtn.html();

                    saveBtn.html('<i class="bx bx-loader-alt bx-spin me-1"></i>Saving...')
                        .prop("disabled", true);

                    const formData = new FormData();
                    formData.append("breakdown_content", html);
                    formData.append("breakdown_title", this.textareaId);
                    formData.append("_update", true);
                    const leadOppId = $("#leadOpportunityId").val() ||
                        $("#leadForm input[name='opportunity_id']").val() || "";

                    if (!leadOppId) {
                        toastr.error("Opportunity ID not found. Re-open the lead modal and try again.");
                        saveBtn.html(originalText).prop("disabled", false);
                        return;
                    }

                    formData.append("opportunity_id", leadOppId);

                    $.ajax({
                        url: "{{ route('update.opp.status') }}",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: (response) => {
                            if (response.success) {
                                const {
                                    title,
                                    content,
                                    short_content
                                } = response.data;
                                const plainText = $('<div>').html(short_content).text();

                                $(`#${title}`).val(plainText);
                                $(`#${title}Content`).val(content);

                                this.modal.hide();
                                queueLeadModalDraftSave();
                                toastr.success("Saved successfully");
                            } else {
                                throw new Error(response.message || "Save failed");
                            }
                        },
                        error: (xhr) => {
                            let errorMessage = "Failed to save breakdown";
                            if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                    ", ");
                            }
                            toastr.error(errorMessage);
                        },
                        complete: () => {
                            saveBtn.html(originalText).prop("disabled", false);
                        },
                    });
                }
            }

            function toggleShareColumnVisibility(slipType) {
                if (!state.dataTable) return;

                const showShareColumn = shouldShowShares(slipType);

                state.dataTable.column(1).visible(showShareColumn);
            }

            function resetLeadModal() {
                $("#leadForm")[0].reset();
                $("#leadForm .is-invalid, .is-v").removeClass("is-invalid is-v");
                $("#leadForm .invalid-feedback, .reinsurer-validation-error").remove();

                if (state.dataTable) {
                    state.dataTable.clear().draw();
                }
                state.selectedReinsurers.clear();
                updateReinsurerCount();

                $("#availableReinsurers").val(null).trigger("change");

                $("#leadTotalReinsurerShare").val("100").prop("disabled", false).css({
                    "background-color": "",
                    "cursor": "",
                    "opacity": ""
                });
                $("#reinsurerShare, #retainedShareValue, #totalPlacedShares, #totalUnplacedShares").val("");

                toggleShareFields($("#slipType").val() || state.slipType);
                $("#leadModal .placed-value").text("0.00%").removeClass("text-success text-danger text-warning")
                    .addClass("text-primary");
                $("#leadModal .unplaced-value").text("100.00%").removeClass("text-success text-danger text-primary")
                    .addClass("text-warning");
                $("#leadModal .placed-progress").css("width", "0%").attr("aria-valuenow", 0).removeClass(
                        "bg-danger bg-primary")
                    .addClass("bg-success");

                $("#leadOpportunityId, #reinsurersData, #specialConditionsContent, #leadClassCode, #leadClassGroupCode")
                    .val("");
                clearTermsConditionsValues();

                $(".slip-display, .created_at-display, .insured-name-display, .insured-contact-name-display, .insured-email-display, .insured-phone-display, .sum_insured_type")
                    .text("");
                $(".total_sum_insured, .premium, .deductible, .special_conditions, #specialConditions").val("");
                $(".brokerage_rate").val("10");

                $("#documentFields").empty().hide();
                $("#documentsSubtitle").html("");
                $("#termsSubtitle").html("");
                state.uploadedFiles = {};
                state.pendingDocumentsDraft = null;

                if (typeof window.pipelineManager !== 'undefined' &&
                    typeof window.pipelineManager.clearAllFiles === 'function') {
                    window.pipelineManager.clearAllFiles();
                }

                $(".deductible_excess_div").hide();

                state.slipType = VALIDATION_CONFIG.SLIP_TYPE;
                $("#slipType").val(VALIDATION_CONFIG.SLIP_TYPE);
                $("#leadCategoryType").val("2");
            }

            function handleCategoryUpdate(e) {
                e.preventDefault();

                const $categorySelect = $("#category_type");
                const $updateBtn = $("#updateCategorySubmitBtn");

                if (!$categorySelect.val()) {
                    $categorySelect.addClass("is-invalid").focus();
                    return false;
                }

                $categorySelect.removeClass("is-invalid");
                $updateBtn.addClass("btn-loading")
                    .html('<span>Updating...</span>')
                    .prop("disabled", true);

                const formData = new FormData(this);

                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            showAlert("Category type updated successfully!", "success");

                            if (typeof window.pipelineManager !== 'undefined' &&
                                typeof window.pipelineManager.reloadAllTables === 'function') {
                                window.pipelineManager.reloadAllTables();
                            } else {
                                location.reload();
                            }
                        }
                        $("#updateCategoryTypeModal").modal("hide");
                    },
                    error: function(xhr) {
                        let errorMessage = "An error occurred while updating the category.";
                        if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON?.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join("<br>");
                        }
                        showAlert(errorMessage, "error");
                    },
                    complete: function() {
                        $updateBtn.removeClass("btn-loading")
                            .html('<i class="bi bi-check-circle me-1"></i>Update Category')
                            .prop("disabled", false);
                    },
                });
            }

            function showAlert(message, type = "info") {
                const iconMap = {
                    success: "success",
                    warning: "warning",
                    error: "error",
                    info: "info",
                };

                toastr[type](message);
            }

            function escapeHtml(text) {
                if (!text) return "";
                const div = document.createElement("div");
                div.textContent = text;
                return div.innerHTML;
            }

            $("#addReinsurer").on("click", addReinsurer);
            $(document).on("click", ".remove-reinsurer", function() {
                const reinsurerID = $(this).data("reinsurer-id");
                const row = $(this).closest("tr");
                removeReinsurer(reinsurerID, row);
            });
            $(document).on("click", ".contacts-reinsurer", function(e) {
                e.preventDefault();
                loadReinsurerContacts($(this).data("reinsurer-id"));
            });
            $("#submitContactModal").on("click", saveContactsModal);
            $("#updateCategoryForm").on("submit", handleCategoryUpdate);
            $("#leadForm").on("submit", handleFormSubmission);
            $("#leadForm").on("input", ".form-inputs", function() {
                validateField($(this));
            });
            $("#leadForm").on("input change", "input:not([type='file']), select, textarea", function() {
                queueLeadModalDraftSave();
            });
            $("#leadTotalReinsurerShare").on("input", function() {
                const value = parseFloat($(this).val());
                if (value > 100) {
                    $(this).val("100");
                    toastr.warning("Total Written Share adjusted to 100%");
                }
                queueLeadModalDraftSave();
            });

            $("#leadModal").on("shown.bs.modal", function() {
                if (state.returningFromBreakdown) {
                    state.returningFromBreakdown = false;
                    state.suppressLeadModalReset = false;
                    queueLeadModalDraftSave();
                    return;
                }

                if (state.returningFromContacts) {
                    state.returningFromContacts = false;
                    state.suppressLeadModalReset = false;
                    queueLeadModalDraftSave();
                    return;
                }

                const slipType = $("#slipType").val();

                toggleShareFields(slipType);
                updateTableHeader(slipType);
                hydrateLeadReinsurers($("#reinsurersData").val());

                const hasTermsContent = $("#leadModal #termsConditions").children().length > 0;
                const hasDocumentsFields = $("#leadModal #documentFields").children().length > 0;
                const hasDocumentsMessage = ($("#leadModal #documentsContent").text() || "").trim().length >
                    0;
                const hasDocumentsContent = hasDocumentsFields || hasDocumentsMessage;
                if (!hasTermsContent || !hasDocumentsContent) {
                    reloadLeadDynamicSections();
                }
                // restoreLeadDocumentsDraftWhenReady(state.pendingDocumentsDraft);

                $("#leadForm .is-invalid").removeClass("is-invalid");
                $("#leadForm .invalid-feedback, .reinsurer-validation-error").remove();
                queueLeadModalDraftSave();
            });

            $("#leadModal").on("click", ".supporting-doc-add-btn, .supporting-doc-row-remove-btn", function() {
                queueLeadModalDraftSave();
            });

            $("#leadModal").on("pipeline:reinsurers-loaded", function(event, payload = {}) {
                const reinsurers = Array.isArray(payload.reinsurers) ? payload.reinsurers : [];
                hydrateLeadReinsurers(reinsurers);
            });

            $("#contactsModal").on("hidden.bs.modal", function() {
                $("#contactsOpportunityId").val("");

                if (state.suppressLeadModalReset) {
                    state.returningFromContacts = true;
                    $("#leadModal").modal("show");
                    state.suppressLeadModalReset = false;
                }
            });

            $("#leadModal").on("hidden.bs.modal", function() {
                if (state.suppressLeadModalReset) {
                    queueLeadModalDraftSave();
                    return;
                }

                resetLeadModal();
                clearLeadModalDraft();

                toggleShareColumnVisibility(VALIDATION_CONFIG.SLIP_TYPE);
                toggleShareFields(VALIDATION_CONFIG.SLIP_TYPE);

                if (state.dataTable) {
                    state.dataTable.clear().draw();
                }

                state.slipType = VALIDATION_CONFIG.SLIP_TYPE;
                state.selectedReinsurers.clear();
                updateReinsurerCount();

                toggleShareFields($("#slipType").val() || state.slipType);

                $(".quote-rein").removeClass("col-md-8 col-md-6").addClass("col-md-6");
                $(".quote-rein").attr("data-quote", "false");
            });

            $("#updateCategoryTypeModal").on("hidden.bs.modal", resetLeadModal);

            try {
                initializeReinsurerTable();
                initializeReinsurerSelect();
                state.breakdownEditor = new BreakdownEditor();
                updateReinsurerCount();
                restoreLeadModalDraftIfAny();
            } catch (error) {
                showAlert("Failed to initialize components. Please refresh the page.", "error");
            }
        });
    </script>
@endpush
