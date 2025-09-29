<!-- Proposal Stage Modal -->
<div id="proposalModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticPropoalStageLabel" aria-hidden="true" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="proposalForm" action="{{ route('update.opp.status') }}" novalidate>
                <div class="modal-body fac-slip-container">
                    <div class="fac-slip-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h1 class="slip-title">
                                    <i class="bx bx-shield me-1"></i>Facultative Slip
                                </h1>
                                <p class="slip-subtitle mb-0">Reinsurance Coverage Proposal</p>
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
                                            </label>
                                            <div class="currency-input">
                                                <div class="currency-symbol" id="currencySymbol">KES</div>
                                                <input type="text" class="form-inputs total_sum_insured"
                                                    name="total_sum_insured" required placeholder="0.00"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    change="this.value=numberWithCommas(this.value)" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Premium
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="currency-input">
                                                <div class="currency-symbol">KES</div>
                                                <input type="text" class="form-inputs premium" name="premium"
                                                    required placeholder="0.00"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    change="this.value=numberWithCommas(this.value)" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Reinsurer Commission Rate (%)</label>
                                            <input type="text" class="form-inputs brokerage_rate"
                                                name="brokerage_rate" placeholder="0.00"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                change="this.value=numberWithCommas(this.value)" readonly
                                                value="10">
                                        </div>
                                    </div>
                                    <div class="col-md-6 deductible_excess_div" style="display: none;">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Deductible/Excess
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="currency-input">
                                                <span class="currency-symbol">KES</span>
                                                <input type="text" class="form-inputs deductible" name="deductible"
                                                    placeholder="0.00"
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
                                                Click to open the rich text editor. Maximum 5000 characters.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="class_code" id="classCodeValue">
                                <input type="hidden" name="class_group_code" id="classGroupCodeValue">
                            </div>
                        </div>

                        <hr class="mt-0 pt-0" />

                        <!-- Reinsurer Information Section -->
                        <div class="form-section">
                            <div class="section-header">
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Add Reinsurer</label>
                                                <select class="sel" id="availableReinsurers"
                                                    placeholder="Search and select reinsurer...">
                                                    <option value="">Search and select reinsurer...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Total Written Share (%)</label>
                                                <input type="number" class="form-inputs" id="totalReinsurerShare"
                                                    name="total_reinsurer_share" placeholder="0.00" step="0.01"
                                                    min="0.01" max="100">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Share (%)</label>
                                                <input type="number" class="form-inputs" id="reinsurerShare"
                                                    placeholder="0.00" step="0.01" min="0.01" max="100">
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

                                <!-- Selected Reinsurers Table -->
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
                                </div>

                                <!-- Risk Distribution Analysis -->
                                <div class="risk-distribution mt-4" id="riskDistribution" style="display: none;">
                                    <h6 class="mb-3"><i class="bx bx-chart-pie me-2"></i>Risk Distribution Analysis
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <canvas id="riskDistributionChart" height="200"></canvas>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="risk-metrics">
                                                <div class="metric-item">
                                                    <span class="metric-label">Largest Single Exposure:</span>
                                                    <span class="metric-value" id="largestExposure">0%</span>
                                                </div>
                                                <div class="metric-item">
                                                    <span class="metric-label">Geographic Diversification:</span>
                                                    <span class="metric-value" id="geoDiversification">Low</span>
                                                </div>
                                                <div class="metric-item">
                                                    <span class="metric-label">Rating Weighted Average:</span>
                                                    <span class="metric-value" id="avgRating">-</span>
                                                </div>
                                                <div class="metric-item">
                                                    <span class="metric-label">Capacity Utilization:</span>
                                                    <span class="metric-value" id="capacityUtil">0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="reinsurers_data" id="reinsurersData">
                                <input type="hidden" name="retained_share" id="retainedShareValue">
                            </div>
                        </div>

                        <hr class="mt-0 pt-0" />

                        <!-- Terms and Conditions Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-title">
                                    <span>
                                        <i class="bx bx-file section-icon"></i>
                                        Terms & Conditions
                                    </span>
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
                                        {{-- <small>Please select insurance class first</small> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="documents-section-content" id="documentsContent">
                                <div id="documentFields" class="row g-4" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div></div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-dark">
                                <i class="bx bx-paper-plane me-1"></i> Send Proposal
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
                <button type="button" class="btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <i class="bx bx-building me-1"></i>GA Insurance - Contact Management
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-4">
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
                        </div>
                    </div>
                </div>

                <!-- Department Emails Section -->
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

<!-- Text Editor -->
<div class="modal fade breakdown-modal effect-scale md-wrapper" id="breakdownModal" tabindex="-1"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="breakdownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
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
                                <i class="bx bx-layout me-2"></i>Quick Templates
                            </h6>
                            <div class="d-flex flex-wrap">
                                <button type="button" class="template-btn" data-template="standard">
                                    Standard Coverage
                                </button>
                                {{-- <button type="button" class="template-btn" data-template="property">
                                    Property Insurance
                                </button>
                                <button type="button" class="template-btn" data-template="marine">
                                    Marine Insurance
                                </button>
                                <button type="button" class="template-btn" data-template="aviation">
                                    Aviation Coverage
                                </button>
                                <button type="button" class="template-btn" data-template="liability">
                                    Liability Insurance
                                </button> --}}
                                <button type="button" class="template-btn" data-template="clear">
                                    Clear All
                                </button>
                            </div>
                        </div>

                        <div class="quill-container position-relative">
                            <div id="breakdownEditor"></div>
                            {{-- <div id="quill-wrapper">
                                <div id="breakdownEditor"></div>
                            </div> --}}
                            <div class="character-counter" id="characterCounter">
                                0 / 5000 characters
                            </div>
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
                        {{-- <button type="button" class="btn btn-outline-info btn-sm" id="exportBtn">
                            <i class="bx bx-download me-1"></i>Export HTML
                        </button> --}}
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
        top: 20px;
        transform: translateY(-50%);
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
        padding: 16px;
        margin-bottom: 20px;
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
</style>

@push('script')
    <script>
        $(document).ready(function() {
            const VALIDATION_CONFIG = {
                MIN_PERCENTAGE: 0.01,
                MAX_PERCENTAGE: 100,
                MIN_REINSURERS: 1,
                REQUIRED_FIELDS: ["total_sum_insured", "premium"],
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

            let uploadedFiles = {};
            let currentClass = '';
            let documentConfigs = {};

            const fileIcons = {
                'pdf': 'bx-file-pdf',
                'doc': 'bx-file-doc',
                'docx': 'bx-file-doc',
                'xls': 'bx-file-excel',
                'xlsx': 'bx-file-excel',
                'jpg': 'bx-image',
                'jpeg': 'bx-image',
                'png': 'bx-image',
                'default': 'bx-file'
            };

            $('.file-upload-area').each(function() {
                initializeFileUpload($(this));
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const table = $('#reinsurersTable').DataTable({
                responsive: true,
                pageLength: 10,
                paging: false,
                searching: false,
                info: false,
                order: [
                    [0, 'asc']
                ],
                language: {
                    search: "Search reinsurers:",
                    lengthMenu: "Show _MENU_ reinsurers per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ reinsurers",
                    infoEmpty: "No reinsurers available",
                    infoFiltered: "(filtered from _MAX_ total reinsurers)",
                    zeroRecords: "No matching reinsurers found",
                    emptyTable: "No reinsurers selected yet. Add reinsurers using the form above."
                },
                columnDefs: [{
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    className: 'text-start'
                }]
            });


            let selectedReinsurers = new Set();

            $("#addReinsurer").click(function() {
                const selectedOption = $("#availableReinsurers option:selected");
                const individualSharePercent = parseFloat($("#reinsurerShare").val());
                const totalReinsurerSharePercent = parseFloat(
                    $("#totalReinsurerShare").val()
                );

                if (!selectedOption.val()) {
                    Swal.fire({
                        icon: "warning",
                        title: "Select Reinsurer",
                        text: "Please select a reinsurer from the dropdown.",
                        confirmButtonColor: "#3085d6",
                    });
                    return;
                }

                if (!totalReinsurerSharePercent || totalReinsurerSharePercent <= 0) {
                    Swal.fire({
                        icon: "error",
                        title: "Missing Total Share",
                        text: "Please enter the Total Written Share percentage first.",
                        confirmButtonColor: "#3085d6",
                    }).then(() => {
                        $("#totalReinsurerShare").focus();
                    });
                    return;
                }

                if (totalReinsurerSharePercent > 100) {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Total Share",
                        text: "Total Written Share cannot exceed 100%.",
                        confirmButtonColor: "#3085d6",
                    }).then(() => {
                        $("#totalReinsurerShare").focus();
                    });
                    return;
                }

                if (
                    !individualSharePercent ||
                    individualSharePercent <= 0 ||
                    individualSharePercent > 100
                ) {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Individual Share",
                        text: "Please enter a valid individual share percentage between 0.01% and 100%.",
                        confirmButtonColor: "#3085d6",
                    }).then(() => {
                        $("#reinsurerShare").focus();
                    });
                    return;
                }

                let currentTotalIndividualShares = 0;
                table.rows().every(function() {
                    const row = $(this.node());
                    const individualShare =
                        parseFloat(row.attr("data-individual-share")) || 0;
                    currentTotalIndividualShares += individualShare;
                });

                if (currentTotalIndividualShares + individualSharePercent > 100) {
                    const remainingCapacity = 100 - currentTotalIndividualShares;
                    Swal.fire({
                        icon: "warning",
                        title: "Insufficient Capacity",
                        text: `Maximum available individual share is ${remainingCapacity.toFixed(2)}%. Total individual shares cannot exceed 100%.`,
                        confirmButtonColor: "#f39c12",
                    });
                    return;
                }

                if (selectedReinsurers.has(selectedOption.val())) {
                    Swal.fire({
                        icon: "info",
                        title: "Already Selected",
                        text: "This reinsurer has already been added to the list.",
                        confirmButtonColor: "#3085d6",
                    });
                    return;
                }

                const writtenShare = calculateWrittenShare(
                    individualSharePercent,
                    totalReinsurerSharePercent
                );

                const reinsurerData = {
                    id: selectedOption.val(),
                    name: selectedOption.data("name"),
                    email: selectedOption.data("email"),
                    country: selectedOption.data("country"),
                    individualShare: individualSharePercent,
                    writtenShare: writtenShare,
                };

                const rowHtml = `
                    <tr data-reinsurer-id="${
                      reinsurerData.id
                    }" data-individual-share="${reinsurerData.individualShare}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="fw-medium">${
                                      reinsurerData.name
                                    }</div>
                                    <small class="text-muted">(${
                                      reinsurerData.email
                                    }) - ${reinsurerData.country}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-start">
                            <div class="share-display">
                                <strong>${reinsurerData.writtenShare.toFixed(
                                  2
                                )}%</strong>
                                <br><small class="text-muted">(${
                                  reinsurerData.individualShare
                                }% of ${totalReinsurerSharePercent}%)</small>
                            </div>
                        </td>
                        <td class="text-start">
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
                        </td>
                    </tr>
                `;

                table.row.add($(rowHtml)).draw();
                selectedReinsurers.add(reinsurerData.id);
                updateReinsurerCount();
                resetForm();

                Swal.fire({
                    icon: "success",
                    title: "Reinsurer Added!",
                    text: `${reinsurerData.name} has been successfully added with ${writtenShare.toFixed(2)}% written share.`,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: "top-end",
                });

                updateTotalSharesDisplay();
            });

            $(document).on("click", ".remove-reinsurer", function() {
                const reinsurerID = $(this).data("reinsurer-id");
                const row = $(this).closest("tr");
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
                        table.row(row).remove().draw();
                        selectedReinsurers.delete(reinsurerID.toString());
                        updateReinsurerCount();
                        updateTotalSharesDisplay();

                        Swal.fire({
                            icon: "info",
                            title: "Removed!",
                            text: `${reinsurerName} has been removed from the list.`,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: "top-end",
                        });
                    }
                });
            });

            function updateReinsurerCount() {
                const count = selectedReinsurers.size;
                $("#reinsurerCount").text(count);
            }

            $(document).on('click', '.contacts-reinsurer', function(e) {
                e.preventDefault()

                const reinsurerID = $(this).data('reinsurer-id');
                const row = $(this).closest('tr');
                let reinsurerName = row.find('td:first .fw-medium').text();

                if (!reinsurerID) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Reinsurer ID not found',
                        timer: 3000,
                        toast: true,
                        position: 'top-end'
                    });
                    return;
                }

                const originalHtml = $(this).html();
                $(this).html('<i class="bx bx-loader bx-spin"></i>');
                $(this).prop('disabled', true);

                $.ajax({
                    url: `/reinsurers/${reinsurerID}/contacts`,
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            reinsurerName = response?.data?.reinsurer?.name;
                            populateContactsModal(response.data, reinsurerName);
                            $('#proposalModal').modal('hide');
                            $('#contactsModal').modal('show');
                        } else {
                            showContactError(response.message || 'Failed to fetch contacts');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to fetch reinsurer contacts';

                        if (xhr.status === 404) {
                            errorMessage = 'Reinsurer contacts not found';
                        } else if (xhr.status === 403) {
                            errorMessage = 'Access denied to reinsurer contacts';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        showContactError(errorMessage);
                    },
                    complete: function() {
                        $('.contacts-reinsurer').html(originalHtml);
                        $('.contacts-reinsurer').prop('disabled', false);
                    }
                });
            });

            function populateContactsModal(contactData, reinsurerName) {
                $('#contactsModalLabel').html(`
                    <i class="bx bx-building me-1"></i>${reinsurerName} - Contact Management
                `);

                if (contactData.primary_contact) {
                    $('.primary-name').val(contactData.primary_contact.name || 'N/A');
                    $('.primary-email').val(contactData.primary_contact.email || 'N/A');
                }

                $('#departmentContacts').empty();

                if (contactData.department_contacts && contactData.department_contacts.length > 0) {
                    contactData.department_contacts.forEach(function(contact, index) {
                        const contactHtml = createContactItemHtml(contact, index);
                        $('#departmentContacts').append(contactHtml);
                    });
                } else {
                    $('#departmentContacts').html(`
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
                    <div class="contact-item rounded px-3 pb-1" data-contact-id="${contact.id || index}">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                ${showLabels ? '<label class="form-label fw-semibold mb-1">Contact Name</label>' : ''}
                                <input type="text" class="form-control-plaintext contact-name"
                                    value="${contact.name || ''}" data-field="name">
                            </div>
                            <div class="col-md-6">
                                ${showLabels ? '<label class="form-label fw-semibold mb-1">Email</label>' : ''}
                                <input type="email" class="form-control-plaintext contact-email"
                                    value="${contact.email || ''}" data-field="email">
                            </div>
                            <div class="col-md-2">
                                ${showLabels ? '<label class="form-label fw-semibold mb-1">CC Email</label>' : ''}
                                <div class="form-check mt-2 px-0">
                                    <input class="form-check-input mailc-checkbox" type="checkbox"
                                        ${contact.cc_email ? 'checked' : ''} data-field="cc_email">
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

            function showContactError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Contact Fetch Error',
                    text: message,
                    timer: 4000,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false
                });
            }

            $('#contactsModal').on('hidden.bs.modal', function() {
                $('#proposalModal').modal('show')
            });

            function updateReinsurerCount() {
                const count = selectedReinsurers.size;
                $('#reinsurerCount').text(count);
            }

            function resetForm() {
                $('#availableReinsurers').val(null).trigger('change');
                $('#reinsurerShare').val('');
                $('#reinsurerCommission').val('');
            }

            function showAlert(message, type = 'info') {
                let icon = 'info';
                let title = 'Information';

                switch (type) {
                    case 'success':
                        icon = 'success';
                        title = 'Success';
                        break;
                    case 'warning':
                        icon = 'warning';
                        title = 'Warning';
                        break;
                    case 'error':
                        icon = 'error';
                        title = 'Error';
                        break;
                }

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message,
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }

            function getTotalPremium() {
                const totalPremiumInput = $('#totalPremium');
                if (totalPremiumInput.length && totalPremiumInput.val()) {
                    return parseFloat(totalPremiumInput.val());
                }
                return 1000000;
            }

            function updateTotalShares() {
                let totalShares = 0;

                table.rows().every(function() {
                    const rowData = $(this.node());
                    const shareText = rowData.find('td:nth-child(2)').text();
                    const share = parseFloat(shareText.replace('%', ''));
                    if (!isNaN(share)) {
                        totalShares += share;
                    }
                });

                updateTotalSharesDisplay(totalShares);

                if (totalShares > 100) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Share Limit Exceeded',
                        text: `Total shares (${totalShares.toFixed(2)}%) exceed 100%. Please review the allocation.`,
                        confirmButtonColor: '#f39c12'
                    });
                }
            }

            $('#reinsurerShare').on('input', function() {
                const currentValue = parseFloat($(this).val());
                if (isNaN(currentValue)) return;

                let totalShares = 0;
                table.rows().every(function() {
                    const rowData = $(this.node());
                    const shareText = rowData.find('td:nth-child(2)').text();
                    const share = parseFloat(shareText.replace('%', ''));

                    if (!isNaN(share)) {
                        totalShares += share;
                    }
                });

                const remainingCapacity = 100 - totalShares;

                if (currentValue > remainingCapacity) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Insufficient Capacity',
                        text: `Maximum available share is ${remainingCapacity.toFixed(2)}%. The value has been adjusted.`,
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                    $(this).val(remainingCapacity.toFixed(2));
                }
            });

            const $categorySelect = $('#category_type');
            const $updateCategoryTypeModal = $('#updateCategoryTypeModal');
            const $updateCategorySubmitBtn = $('#updateCategorySubmitBtn');

            $('#updateCategoryForm').on('submit', function(e) {
                e.preventDefault();

                if (!$categorySelect.val()) {
                    $categorySelect.addClass('is-invalid');
                    $categorySelect.focus();
                    return false;
                } else {
                    $categorySelect.removeClass('is-invalid');
                }

                $updateCategorySubmitBtn.addClass('btn-loading');
                $updateCategorySubmitBtn.html('<span class="">Updating...</span>');
                $updateCategorySubmitBtn.prop('disabled', true);

                const formData = new FormData(this);
                const actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Category type updated successfully!',
                                timer: 2000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            }).then(() => {
                                location.reload();
                            });
                        }
                        $updateCategoryTypeModal.modal('hide');

                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'An error occurred while updating the category.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }

                        toastr.error(errorMessage, 'error');
                    },
                    complete: function() {
                        $updateCategorySubmitBtn.removeClass('btn-loading');
                        $updateCategorySubmitBtn.html(
                            '<i class="bi bi-check-circle me-1"></i>Update Category');
                        $updateCategorySubmitBtn.prop('disabled', false);
                    }
                });
            });

            $updateCategoryTypeModal.on('hidden.bs.modal', function() {
                $('#updateCategoryForm')[0].reset();
                $updateCategorySubmitBtn.removeClass('btn-loading');
                $updateCategorySubmitBtn.html('<i class="bi bi-check-circle me-1"></i>Update Category');
                $updateCategorySubmitBtn.prop('disabled', false);
                $categorySelect.removeClass('is-invalid');
            });

            $('#availableReinsurers').select2({
                placeholder: 'Search and select reinsurer...',
                allowClear: true,
                minimumInputLength: 0,
                width: '100%',
                dropdownParent: $('#proposalModal'),
                ajax: {
                    url: "{{ route('pipeline.search_reinsurers') }}",
                    method: 'GET',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination && data.pagination.more
                            }
                        };
                    },
                    cache: true,
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                },
                templateResult: function(reinsurer) {
                    if (reinsurer.loading) return reinsurer.text;
                    if (!reinsurer.name) return reinsurer.text;

                    const email = reinsurer.email

                    return `
                        <div class="reinsurer-option">
                            <div><strong>${reinsurer.name}</strong>
                                <span class="badge bg-secondary ms-1">${reinsurer.rating}</span>
                            </div>
                            <div><small class="text-muted">${reinsurer.country} | Email: ${email}</small></div>
                        </div>
                    `;
                },
                templateSelection: function(reinsurer) {
                    if (!reinsurer.id) return reinsurer.text;

                    let option = $('#availableReinsurers').find(`option[value='${reinsurer.id}']`);
                    option.attr('data-name', reinsurer.name || '');
                    option.attr('data-email', reinsurer.email || '');
                    option.attr('data-country', reinsurer.country || '');

                    return `${reinsurer.name} (${reinsurer.email}) - ${reinsurer.country}`;
                },
                escapeMarkup: function(markup) {
                    return markup;
                }
            });

            $(document).on('click', '#submitContactModal', function() {
                const contacts = [];

                $('#departmentContacts .contact-item').each(function() {
                    const contactData = {
                        id: $(this).data('contact-id'),
                        name: $(this).find('.contact-name').val(),
                        email: $(this).find('.contact-email').val(),
                        cc_email: $(this).find('.mailc-checkbox').is(':checked')
                    };

                    if (contactData.name || contactData.email) {
                        contacts.push(contactData);
                    }
                });

                if (contacts.length > 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Changes Saved!',
                        text: 'Contact information has been updated.',
                        timer: 2000,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false
                    });
                }

                $("#contactsModal").modal('hide')
            });

            $("#totalReinsurerShare").on("input", function() {
                const value = parseFloat($(this).val());

                if (value > 100) {
                    $(this).val("100");
                    Swal.fire({
                        icon: "warning",
                        title: "Maximum Share Exceeded",
                        text: "Total Written Share cannot exceed 100%. Value has been adjusted to 100%.",
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: "top-end",
                    });
                }

                if (value < 0) {
                    $(this).val("0");
                }

                recalculateIndividualShares();
            });

            function calculateWrittenShare(
                individualSharePercent,
                totalReinsurerSharePercent
            ) {
                if (!totalReinsurerSharePercent || totalReinsurerSharePercent === 0) {
                    return 0;
                }
                return (individualSharePercent / 100) * totalReinsurerSharePercent;
            }

            function recalculateIndividualShares() {
                const totalReinsurerShare =
                    parseFloat($("#totalReinsurerShare").val()) || 0;

                table.rows().every(function() {
                    const row = $(this.node());
                    const individualSharePercent =
                        parseFloat(row.attr("data-individual-share")) || 0;
                    const writtenShare = calculateWrittenShare(
                        individualSharePercent,
                        totalReinsurerShare
                    );

                    row.find("td:nth-child(2)").html(`
                        <div class="share-display">
                            <strong>${writtenShare.toFixed(2)}%</strong>
                            <br><small class="text-muted">(${individualSharePercent}% of ${totalReinsurerShare}%)</small>
                        </div>
                    `);
                });

                updateTotalSharesDisplay();
            }

            function updateTotalSharesDisplay() {
                const totalReinsurerShare =
                    parseFloat($("#totalReinsurerShare").val()) || 0;
                let totalIndividualShares = 0;
                let totalWrittenShares = 0;

                table.rows().every(function() {
                    const row = $(this.node());
                    const individualShare =
                        parseFloat(row.attr("data-individual-share")) || 0;
                    const writtenShare = calculateWrittenShare(
                        individualShare,
                        totalReinsurerShare
                    );

                    totalIndividualShares += individualShare;
                    totalWrittenShares += writtenShare;
                });

                let totalSharesDisplay = $(".total-shares-display");
                if (totalSharesDisplay.length === 0) {
                    const displayHtml = `
                        <div class="total-shares-display mt-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <span class="fw-medium">Individual Shares Total:</span>
                                        <span class="badge bg-info individual-shares-value">0.00%</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <span class="fw-medium">Written Shares Total:</span>
                                        <span class="badge bg-primary written-shares-value">0.00%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $(".selected-reinsurers-section").append(displayHtml);
                    totalSharesDisplay = $(".total-shares-display");
                }

                const individualBadgeClass =
                    totalIndividualShares > 100 ?
                    "bg-danger" :
                    totalIndividualShares === 100 ?
                    "bg-success" :
                    "bg-info";
                totalSharesDisplay
                    .find(".individual-shares-value")
                    .removeClass("bg-info bg-success bg-danger")
                    .addClass(individualBadgeClass)
                    .text(`${totalIndividualShares.toFixed(2)}%`);

                const writtenBadgeClass =
                    totalWrittenShares > totalReinsurerShare ?
                    "bg-danger" :
                    totalWrittenShares === totalReinsurerShare ?
                    "bg-success" :
                    "bg-primary";
                totalSharesDisplay
                    .find(".written-shares-value")
                    .removeClass("bg-primary bg-success bg-danger")
                    .addClass(writtenBadgeClass)
                    .text(`${totalWrittenShares.toFixed(2)}%`);
            }

            $("#proposalModal").on("shown.bs.modal", function() {
                $("#proposalForm .is-invalid").removeClass(
                    "is-invalid"
                );
                $("#proposalForm .invalid-feedback").remove();
                $("#proposalForm .reinsurer-validation-error").remove();
            });

            $("#proposalForm").on("submit", handleFormSubmission);

            $("#proposalForm").on("input blur", ".form-inputs", function() {
                validateField($(this));
            });

            function validateField($field) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const fieldValue = $field.val().trim();
                const isRequired =
                    $field.prop("required") ||
                    VALIDATION_CONFIG.REQUIRED_FIELDS.includes(fieldName);

                $field.removeClass("is-invalid");
                $field.siblings(".invalid-feedback").remove();

                let isValid = true;
                let errorMessage = "";

                if (isRequired && !fieldValue) {
                    isValid = false;
                    errorMessage = "This field is required";
                } else if (fieldValue) {
                    const validation = getFieldValidation($field);
                    if (validation && !validation.isValid) {
                        isValid = false;
                        errorMessage = validation.message;
                    }
                }

                if (isValid && fieldValue) {
                    $field.addClass("is-validate");
                } else if (!isValid) {
                    $field.addClass("is-invalid");
                    $field.after(`<div class="invalid-feedback">${errorMessage}</div>`);
                }

                return isValid;
            }

            function getFieldValidation($field) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const fieldValue = $field.val();
                const numericValue = parseFloat(fieldValue.replace(/,/g, ""));

                if (
                    $field.closest(".currency-input").length ||
                    fieldName.includes("premium") ||
                    fieldName.includes("sum_insured")
                ) {
                    if (
                        !FIELD_VALIDATORS.currency.pattern.test(fieldValue.replace(/,/g, ""))
                    ) {
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

                if (fieldName.includes("rate") || fieldName.includes("Share")) {
                    if (!FIELD_VALIDATORS.percentage.pattern.test(fieldValue)) {
                        return {
                            isValid: false,
                            message: FIELD_VALIDATORS.percentage.message
                        };
                    }
                    if (
                        numericValue < FIELD_VALIDATORS.percentage.min ||
                        numericValue > FIELD_VALIDATORS.percentage.max
                    ) {
                        return {
                            isValid: false,
                            message: `Percentage must be between ${FIELD_VALIDATORS.percentage.min} and ${FIELD_VALIDATORS.percentage.max}`,
                        };
                    }
                }

                if ($field.attr("type") === "email" || fieldName.includes("email")) {
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

            function calculateTotalShares() {
                let total = 0;
                $("#reinsurersTable tbody tr").each(function() {
                    const shareText = $(this).find("td:nth-child(2) strong").text();
                    const shareValue = parseFloat(shareText.replace("%", "")) || 0;
                    total += shareValue;
                });
                return total;
            }

            function handleFormSubmission(e) {
                e.preventDefault();

                const $submitBtn = $("#proposalForm button[type='submit']");
                const originalBtnContent = $submitBtn.html();

                const validation = validateProposalForm();

                if (!validation.isValid) {
                    let errorHtml = '<ul class="mb-0">';
                    validation.errors.forEach((error) => {
                        errorHtml += `<li>${error}</li>`;
                    });
                    errorHtml += "</ul>";

                    Swal.fire({
                        icon: "error",
                        title: "Validation Failed",
                        html: errorHtml,
                        confirmButtonColor: "#dc3545",
                    });

                    const $firstError = $("#proposalForm .is-invalid").first();
                    if ($firstError.length) {
                        $firstError[0].scrollIntoView({
                            behavior: "smooth",
                            block: "center",
                        });
                        setTimeout(() => $firstError.focus(), 500);
                    }

                    return false;
                }

                $submitBtn
                    .html('<i class="bx bx-loader-alt bx-spin me-1"></i> Sending Proposal...')
                    .prop("disabled", true);

                const formData = prepareFormData();

                $.ajax({
                    url: $("#proposalForm").attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    timeout: 30000,

                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Proposal Saved Successfully!",
                                text: response.message || "Your proposal has been submitted",
                                showConfirmButton: true,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    handleSendBDNotification(response)
                                } else {
                                    $("#proposalModal").modal("hide");
                                }
                            });
                        } else {
                            throw new Error(response.message || "Submission failed");
                        }
                    },

                    error: function(xhr, status, error) {
                        console.error("Proposal submission error:", {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error,
                        });

                        let errorMessage =
                            "An unexpected error occurred while sending the proposal.";

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const serverErrors = xhr.responseJSON.errors;
                            errorMessage = Object.values(serverErrors).flat().join("<br>");
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (status === "timeout") {
                            errorMessage =
                                "Request timed out. Please check your connection and try again.";
                        } else if (xhr.status === 0) {
                            errorMessage = "Network error. Please check your internet connection.";
                        } else if (xhr.status === 404) {
                            errorMessage =
                                "The submission endpoint was not found. Please contact support.";
                        } else if (xhr.status === 500) {
                            errorMessage =
                                "Server error occurred. Please try again later or contact support.";
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Submission Failed",
                            html: errorMessage,
                            confirmButtonColor: "#dc3545",
                        });
                    },

                    complete: function() {
                        $submitBtn.html(originalBtnContent).prop("disabled", false);
                    },
                });
            }

            function handleSendBDNotification() {
                $("#proposalModal").modal("hide");
                $("#sendBDEmail").modal('show')
            }

            function prepareFormData() {
                const formData = new FormData();

                const $form = $("#proposalForm");
                const formElements = $form.find("input, select, textarea");

                formElements.each(function() {
                    const $element = $(this);
                    const name = $element.attr("name");
                    const type = $element.attr("type");

                    if (!name) return;

                    if (type === "file") {
                        const files = this.files;
                        for (let i = 0; i < files.length; i++) {
                            formData.append(name + "[]", files[i]);
                        }
                    } else if (type === "checkbox") {
                        if ($element.is(":checked")) {
                            formData.append(name, $element.val());
                        }
                    } else if (type === "radio") {
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

                const reinsurersData = [];
                $("#reinsurersTable tbody tr").each(function() {
                    const $row = $(this);
                    const reinsurerData = {
                        id: $row.data("reinsurer-id"),
                        individual_share: $row.data("individual-share"),
                    };
                    reinsurersData.push(reinsurerData);
                });

                formData.append("reinsurers_data", JSON.stringify(reinsurersData));

                // console.log("Prepared form data:", formData); // Debug log
                return formData;
            }

            function validateProposalForm() {
                let isFormValid = true;
                const errors = [];

                $("#proposalForm .form-inputs").each(function() {
                    if (!validateField($(this))) {
                        isFormValid = false;
                        const fieldLabel = $(this)
                            .closest(".form-group")
                            .find("label")
                            .text()
                            .replace("*", "")
                            .trim();
                        errors.push(`${fieldLabel}: Please check the entered value`);
                    }
                });

                if (!validateReinsurerSelection()) {
                    isFormValid = false;
                    errors.push("Reinsurer Selection: Please add at least one reinsurer");
                }

                // const requiredFiles = $('#proposalForm input.file-input[required]');

                // requiredFiles.each(function() {
                //     if (!this.files.length) {
                //         isFormValid = false;
                //         const fieldLabel = $(this)
                //             .closest(".form-group")
                //             .find("label")
                //             .text()
                //             .replace("*", "")
                //             .trim();

                //         const docError = `${fieldLabel}: Please upload the required document`
                //         errors.push(docError);
                //         validateDocInputs(docError)
                //     }
                // });

                return {
                    isValid: isFormValid,
                    errors: errors,
                };
            }

            function validateDocInputs(docError) {
                const $docSection = $("#documentsContent");
                const errorHtml = `
                        <div class="alert alert-danger reinsurer-validation-error mt-2">
                            <i class="bx bx-error-circle me-2"></i>
                           ${docError}
                        </div>
                    `;
                $docSection.append(errorHtml);
                return false;
            }

            function validateReinsurerSelection() {
                const reinsurerCount = selectedReinsurers.size;
                const $reinsurerSection = $("#reinsurer-info");

                $reinsurerSection.find(".reinsurer-validation-error").remove();

                if (reinsurerCount < VALIDATION_CONFIG.MIN_REINSURERS) {
                    const errorHtml = `
                        <div class="alert alert-danger reinsurer-validation-error mt-2">
                            <i class="bx bx-error-circle me-2"></i>
                            At least ${VALIDATION_CONFIG.MIN_REINSURERS} reinsurer must be selected
                        </div>
                    `;
                    $reinsurerSection.append(errorHtml);
                    return false;
                }

                const totalShares = calculateTotalShares();
                if (totalShares === 0) {
                    const errorHtml = `
                        <div class="alert alert-danger reinsurer-validation-error mt-2">
                            <i class="bx bx-error-circle me-2"></i>
                            Total reinsurer shares cannot be 0%
                        </div>
                    `;
                    $reinsurerSection.append(errorHtml);
                    return false;
                }

                return true;
            }

            class BreakdownEditor {
                constructor() {
                    this.quill = null;
                    this.maxCharacters = 5000;
                    this.isPreviewMode = false;
                    this.currentTextarea = null;
                    this.templates = {
                        standard: `
                        <h3>Standard Coverage Breakdown</h3>
                        <ul>
                            <li><strong>Building Structure:</strong> Coverage for physical damage to buildings</li>
                            <li><strong>Contents:</strong> Protection for business equipment and inventory</li>
                            <li><strong>Business Interruption:</strong> Loss of income coverage</li>
                            <li><strong>Public Liability:</strong> Third party claims protection</li>
                        </ul>
                        <p><em>All amounts subject to policy terms and conditions.</em></p>
                    `,
                        property: `
                        <h3>Property Insurance Coverage</h3>
                        <ol>
                            <li><strong>Real Estate Value:</strong> Market value of land and buildings</li>
                            <li><strong>Replacement Cost:</strong> Cost to rebuild at current prices</li>
                            <li><strong>Personal Property:</strong> Furniture, fixtures, and equipment</li>
                            <li><strong>Additional Living Expenses:</strong> Temporary accommodation costs</li>
                        </ol>
                        <blockquote>Coverage limits may vary based on property location and risk assessment.</blockquote>
                    `,
                        marine: `
                        <h3>Marine Insurance Coverage</h3>
                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #dee2e6;">
                            <tr style="background: #f8f9fa;"><td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Hull & Machinery:</strong></td><td style="padding: 8px; border: 1px solid #dee2e6;">Vessel physical damage</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Cargo:</strong></td><td style="padding: 8px; border: 1px solid #dee2e6;">Goods in transit</td></tr>
                            <tr style="background: #f8f9fa;"><td style="padding: 8px; border: 1px solid #dee2e6;"><strong>Freight:</strong></td><td style="padding: 8px; border: 1px solid #dee2e6;">Loss of freight charges</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #dee2e6;"><strong>P&I Coverage:</strong></td><td style="padding: 8px; border: 1px solid #dee2e6;">Protection & Indemnity</td></tr>
                        </table>
                    `,
                        aviation: `
                        <h3>Aviation Coverage Breakdown</h3>
                        <p>Comprehensive coverage including:</p>
                        <ul>
                            <li>Aircraft hull damage (all risks)</li>
                            <li>Third party liability (passengers & ground)</li>
                            <li>Crew and passenger accident coverage</li>
                            <li>Search and rescue expenses</li>
                            <li>War risks and hijacking protection</li>
                        </ul>
                    `,
                        liability: `
                        <h3>Liability Insurance Coverage</h3>
                        <p><strong>Coverage Scope:</strong></p>
                        <ul>
                            <li>General Public Liability</li>
                            <li>Product Liability</li>
                            <li>Professional Indemnity</li>
                            <li>Employers Liability</li>
                            <li>Directors & Officers Liability</li>
                        </ul>
                        <p><em>Subject to applicable deductibles and exclusions.</em></p>
                    `
                    };

                    this.init();
                }

                init() {
                    this.setupEventListeners();
                    this.initializeModal();
                    this.enhanceTextarea();
                }

                enhanceTextarea() {
                    $("textarea.breakdown-textarea")
                        .addClass("editor-enabled")
                        .attr({
                            readonly: true,
                        })
                        .css({
                            cursor: "pointer",
                            background: "linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%)",
                            border: "2px solid #e9ecef",
                            transition: "all 0.3s ease",
                        });
                }

                setupEventListeners() {
                    $(document).on('click', 'textarea.breakdown-textarea', (e) => {
                        e.preventDefault();
                        const $textarea = $(e.currentTarget);
                        this.openModal($textarea);
                    });

                    $(document).on('click', '.template-btn', (e) => {
                        const template = $(e.target).data('template');
                        this.applyTemplate(template);
                    });

                    $('#saveBreakdownBtn').on('click', () => {
                        this.saveChanges();
                    });

                    $('#previewBtn').on('click', () => {
                        this.togglePreview();
                    });

                    $('#breakdownModal').on('show.bs.modal', () => {
                        this.cleanupEditor();
                    });

                    $('#breakdownModal').on('shown.bs.modal', () => {
                        this.initializeQuill();
                    });

                    $('#breakdownModal').on('hidden.bs.modal', () => {
                        this.cleanupEditor();
                        this.currentTextarea = null;
                        $('#proposalModal').modal('show');
                    });

                    $('#breakdownModal').on('hide.bs.modal', () => {
                        this.cleanupEditor();
                    });
                }

                initializeModal() {
                    if (!document.getElementById('breakdownModal')) {
                        console.error("Breakdown modal not found!");
                        return;
                    }
                    this.modal = new bootstrap.Modal(document.getElementById('breakdownModal'));
                }

                cleanupEditor() {
                    if (this.quill) {
                        try {
                            this.quill.off('text-change');
                            this.quill = null;
                        } catch (error) {
                            console.warn('Error removing Quill listeners:', error);
                        }
                    }

                    const container = document.getElementById('breakdownEditor');
                    if (container) {
                        container.innerHTML = '';
                        container.className = '';
                        container.removeAttribute('style');
                    }

                    const quillContainer = $('.quill-container');
                    quillContainer.find('.ql-toolbar').remove();
                    quillContainer.find('.ql-container').remove();
                }

                openModal($textarea) {
                    if (!this.modal) {
                        return;
                    }

                    this.currentTextarea = $textarea;

                    const fieldLabel = $textarea
                        .closest(".form-group")
                        .find("label")
                        .first()
                        .text()
                        .trim();
                    $("#breakdownModalLabel").html(
                        `<i class="bx bx-edit-alt me-2"></i>${fieldLabel || "Sum Insured Breakdown Editor"}`
                    );

                    $("#proposalModal").modal("hide");
                    this.showLoading();
                    this.modal.show();
                }

                showLoading() {
                    $('#loadingOverlay').addClass('show');
                }

                hideLoading() {
                    $('#loadingOverlay').removeClass('show');
                }

                initializeQuill() {
                    if (typeof Quill === 'undefined') {
                        this.hideLoading();
                        return;
                    }

                    this.cleanupEditor();


                    if (this.quill) {
                        try {
                            this.quill.off('text-change');
                            const container = document.getElementById('breakdownEditor');
                            if (container) {
                                container.innerHTML = '';
                                container.className = '';
                            }
                            this.quill = null;
                        } catch (error) {
                            console.warn('Error destroying Quill instance:', error);
                        }
                    }

                    const editorContainer = document.getElementById('breakdownEditor');
                    if (!editorContainer) {
                        this.hideLoading();
                        return;
                    }

                    editorContainer.innerHTML = '';
                    editorContainer.className = '';

                    setTimeout(() => {
                        const toolbarOptions = [
                            [{
                                'header': [1, 2, 3, false]
                            }],
                            ['bold', 'italic', 'underline'],
                            [{
                                'color': []
                            }, {
                                'background': []
                            }],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            [{
                                'indent': '-1'
                            }, {
                                'indent': '+1'
                            }],
                            [{
                                'align': []
                            }],
                            ['link'],
                            ['clean']
                        ];

                        try {
                            this.quill = new Quill('#breakdownEditor', {
                                theme: 'snow',
                                modules: {
                                    toolbar: toolbarOptions
                                },
                            });

                            if (this.currentTextarea) {
                                const existingContent = this.currentTextarea.val();
                                if (existingContent && existingContent.trim()) {
                                    this.quill.root.innerHTML = existingContent;
                                }
                            }

                            this.quill.on('text-change', () => {
                                this.updateStatistics();
                                this.validateContent();
                            });

                            this.updateStatistics();
                        } catch (error) {
                            console.error('Error initializing Quill:', error);
                        }

                        this.hideLoading();
                    }, 500);
                }

                updateStatistics() {
                    if (!this.quill) return;

                    const text = this.quill.getText();
                    const charCount = text.trim().length;

                    const counter = $("#characterCounter");
                    counter.text(`${charCount} / ${this.maxCharacters} characters`);

                    counter.removeClass("warning danger");
                    if (charCount > this.maxCharacters * 0.9) {
                        counter.addClass("warning");
                    }
                    if (charCount > this.maxCharacters) {
                        counter.addClass("danger");
                    }
                }


                validateContent() {
                    if (!this.quill) return;

                    const text = this.quill.getText();
                    const saveBtn = $('#saveBreakdownBtn');

                    if (text.length > this.maxCharacters) {
                        saveBtn.addClass('disabled');
                        saveBtn.attr('title', 'Content exceeds maximum character limit');
                    } else {
                        saveBtn.removeClass('disabled');
                        saveBtn.removeAttr('title');
                    }
                }

                applyTemplate(templateName) {
                    if (!this.quill) return;

                    if (templateName === 'clear') {
                        Swal.fire({
                            title: 'Clear Content?',
                            text: 'This will remove all current content. Continue?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, clear it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.quill.setContents([]);
                                this.showToast('Content cleared', 'info');
                            }
                        });
                        return;
                    }

                    if (this.templates[templateName]) {
                        Swal.fire({
                            title: 'Apply Template?',
                            text: 'This will replace your current content with the selected template.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Apply Template'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.quill.root.innerHTML = this.templates[templateName];
                                this.updateStatistics();
                                this.showToast('Template applied successfully', 'success');
                            }
                        });
                    }
                }

                togglePreview() {
                    const container = $('.quill-container');
                    const btn = $('#previewBtn');

                    if (this.isPreviewMode) {
                        container.removeClass('preview-mode');
                        btn.html('<i class="bx bx-show me-1"></i>Preview');
                        this.isPreviewMode = false;
                    } else {
                        container.addClass('preview-mode');
                        btn.html('<i class="bx bx-edit me-1"></i>Edit');
                        this.isPreviewMode = true;
                    }
                }

                exportHTML() {
                    if (!this.quill) return;

                    const html = this.quill.root.innerHTML;
                    const blob = new Blob([html], {
                        type: 'text/html'
                    });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'sum-insured-breakdown.html';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);

                    this.showToast('HTML exported successfully', 'success');
                }

                saveChanges() {
                    if (!this.quill || !this.currentTextarea) {
                        this.showToast("No content to save", "error");
                        return;
                    }

                    const text = this.quill.getText().trim();
                    const html = this.quill.root.innerHTML;

                    if (text.length > this.maxCharacters) {
                        this.showToast("Content exceeds maximum character limit", "error");
                        return;
                    }

                    this.currentTextarea.val(html);

                    const saveBtn = $("#saveBreakdownBtn");
                    const originalText = saveBtn.html();

                    saveBtn.html('<i class="bx bx-loader-alt bx-spin me-1"></i>Saving...');
                    saveBtn.prop("disabled", true);

                    setTimeout(() => {
                        saveBtn.html('<i class="bx bx-check me-1"></i>Saved!');
                        saveBtn.addClass("save-success");

                        setTimeout(() => {
                            saveBtn.html(originalText);
                            saveBtn.prop("disabled", false);
                            saveBtn.removeClass("save-success");
                            this.modal.hide();

                            this.showToast("Breakdown saved successfully", "success");
                        }, 1000);
                    }, 500);
                }


                showToast(message, type = 'info') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    Toast.fire({
                        icon: type,
                        title: message
                    });
                }
            }

            let breakdownEditor;
            try {
                breakdownEditor = new BreakdownEditor();
            } catch (error) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to initialize the BreakdownEditor application. Please refresh the page.');
                } else {
                    alert('Failed to initialize the application. Please refresh the page.');
                }
            }

        });
    </script>
@endpush
