<!-- Proposal Stage Modal -->
<div id="proposalModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticPropoalStageLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form id="proposalForm" action="{{ route('update.opp.status') }}" novalidate>
                <input type="hidden" class="opportunity_id" name="opportunity_id" id="propOpportunityId" />
                <input type="hidden" class="cedant_id" name="cedant_id" id="propCedId" />
                <input type="hidden" class="current_stage" name="current_stage" />
                <input type="hidden" name="class_code" class="class_code">
                <input type="hidden" name="class_group_code" class="class_group_code">
                <input type="hidden" name="total_placed_shares" id="propPlacedShare">
                <input type="hidden" name="total_unplaced_shares" id="propUnPlacedShare">
                <input type="hidden" name="selected_reinsurers" class="selected_reinsurers">

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
                                    <h6 class="mb-2 fw-medium" style="font-size: 19px;">
                                        <i class="bx bx-building me-1"></i>
                                        <span class="insured-name-display"></span>
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
                        <!-- Proposal Details Section -->
                        <div class="form-section">
                            <div class="section-header" data-section="coverage-details">
                                <div class="section-title">
                                    <span>
                                        <i class="bx bx-check section-icon"></i>
                                        Proposal Details
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
                                                    onchange="this.value=numberWithCommas(this.value)" readonly>
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
                                                    onchange="this.value=numberWithCommas(this.value)" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Risk Type</label>
                                            <input type="text" class="form-inputs risk_type" name="risk_type"
                                                readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Last Contact Date
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <input type="date" class="form-inputs last_contact_date"
                                                name="last_contact_date" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cedant Information Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <div class="section-title">
                                    <span>
                                        <i class="bi bi-shield-check section-icon"></i>
                                        Cedant Details
                                    </span>
                                </div>
                            </div>
                            <div class="section-content" id="cedant-info">
                                <div class="cedant-selection-panel mb-2">
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="form-group">
                                                <label class="form-label">Cedant</label>
                                                <small class="form-text form-inputs cedant_name"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button"
                                                    class="btn btn-primary add_cedant_contacts btn-sm w-100"
                                                    style="padding: 2px 0px;">
                                                    <i class="bx bx-book" style="font-size: 27px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                <div class="reinsurer-selection-panel mb-2" id="reinSelectionPlacement">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label class="form-label">Add Reinsurer</label>
                                                <select class="sel" id="propAvailableReinsurers"
                                                    placeholder="Search and select reinsurer...">
                                                    <option value="">Search and select reinsurer...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="form-label">Share (%)</label>
                                                <input type="number" class="form-inputs" id="propReinShare"
                                                    placeholder="0.00" step="0.01" min="0.01" max="100">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-success w-100"
                                                    id="addPropReinsurer" style="padding: 2px 0px;">
                                                    <i class="bx bx-plus" style="font-size: 27px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="selected-reinsurers-section">
                                    <h6 class="mb-3">
                                        <i class="bi bi-people-fill me-1"></i>Selected Reinsurers
                                        <span class="badge bg-primary ms-2" id="reinsurerCount">0</span>
                                    </h6>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped selected-reinsurers-table"
                                            id="propReinsurersTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th style="width: 70%">Reinsurer</th>
                                                    <th style="width: 20%">Written Share (%)</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
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
                                            <div class="progress-bar bg-success placed-progress" role="progressbar"
                                                style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                    <div id="documentsSubtitle" class="ms-3 fs-12 opacity-75">
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

<!-- PDF Preview Modal -->
<div class="modal fade effect-scale md-wrapper" id="previewPdfModal" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticpreviewPdfModal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="staticpreviewPdfModal">
                    <i class="bi bi-files"></i>
                    Document Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <form id="previewPdfForm">
                    <input type="hidden" name="opportunity_id" class="opportunity_id" id="pdf_opportunity_id" />
                    <input type="hidden" name="current_stage" class="current_stage" id="pdf_current_stage" />
                    <input type="hidden" name="previous_stage" class="previous_stage" id="pdf_previous_stage" />
                </form>

                <!-- Stage Tabs -->
                <ul class="nav nav-tabs nav-fill border-bottom" id="pdfStageTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="lead-tab" data-bs-toggle="tab"
                            data-bs-target="#lead-stage" type="button" role="tab" aria-controls="lead-stage"
                            aria-selected="true">
                            <i class="bi bi-person-check me-1"></i>
                            Lead
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="proposal-tab" data-bs-toggle="tab"
                            data-bs-target="#proposal-stage" type="button" role="tab"
                            aria-controls="proposal-stage" aria-selected="false">
                            <i class="bi bi-file-text me-1"></i>
                            Proposal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="negotiation-tab" data-bs-toggle="tab"
                            data-bs-target="#negotiation-stage" type="button" role="tab"
                            aria-controls="negotiation-stage" aria-selected="false">
                            <i class="bi bi-chat-left-dots me-1"></i>
                            Negotiation
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="close-won-tab" data-bs-toggle="tab"
                            data-bs-target="#close-won-stage" type="button" role="tab"
                            aria-controls="close-won-stage" aria-selected="false">
                            <i class="bi bi-trophy me-1"></i>
                            Close/Won
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="final-tab" data-bs-toggle="tab" data-bs-target="#final-stage"
                            type="button" role="tab" aria-controls="final-stage" aria-selected="false">
                            <i class="bi bi-check-circle me-1"></i>
                            Final
                        </button>
                    </li>
                </ul>

                <!-- Stage Content -->
                <div class="tab-content pdf-section-box customScrollBar" id="pdfStageContent">
                    <!-- Lead Stage -->
                    <div class="tab-pane fade show active" id="lead-stage" role="tabpanel"
                        aria-labelledby="lead-tab">
                        <div class="pdf-list-container p-4" style="min-height: 400px;">
                            <div class="text-center py-5" id="lead-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs...</p>
                            </div>
                            <div id="lead-pdf-list" class="d-none">
                                <!-- PDF list will be populated here -->
                            </div>
                            <div class="text-center py-5 d-none" id="lead-no-pdf">
                                <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">No PDFs available for this stage</p>
                            </div>
                        </div>
                    </div>

                    <!-- Proposal Stage -->
                    <div class="tab-pane fade" id="proposal-stage" role="tabpanel" aria-labelledby="proposal-tab">
                        <div class="pdf-list-container p-4" style="min-height: 400px;">
                            <div class="text-center py-5" id="proposal-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs...</p>
                            </div>
                            <div id="proposal-pdf-list" class="d-none">
                                <!-- PDF list will be populated here -->
                            </div>
                            <div class="text-center py-5 d-none" id="proposal-no-pdf">
                                <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">No PDFs available for this stage</p>
                            </div>
                        </div>
                    </div>

                    <!-- Negotiation Stage -->
                    <div class="tab-pane fade" id="negotiation-stage" role="tabpanel"
                        aria-labelledby="negotiation-tab">
                        <div class="pdf-list-container p-4" style="min-height: 400px;">
                            <div class="text-center py-5" id="negotiation-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs...</p>
                            </div>
                            <div id="negotiation-pdf-list" class="d-none">
                                <!-- PDF list will be populated here -->
                            </div>
                            <div class="text-center py-5 d-none" id="negotiation-no-pdf">
                                <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">No PDFs available for this stage</p>
                            </div>
                        </div>
                    </div>

                    <!-- Close/Won Stage -->
                    <div class="tab-pane fade" id="close-won-stage" role="tabpanel" aria-labelledby="close-won-tab">
                        <div class="pdf-list-container p-4" style="min-height: 400px;">
                            <div class="text-center py-5" id="close-won-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs...</p>
                            </div>
                            <div id="close-won-pdf-list" class="d-none">
                                <!-- PDF list will be populated here -->
                            </div>
                            <div class="text-center py-5 d-none" id="close-won-no-pdf">
                                <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">No PDFs available for this stage</p>
                            </div>
                        </div>
                    </div>

                    <!-- Final Stage -->
                    <div class="tab-pane fade" id="final-stage" role="tabpanel" aria-labelledby="final-tab">
                        <div class="pdf-list-container p-4" style="min-height: 400px;">
                            <div class="text-center py-5" id="final-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs...</p>
                            </div>
                            <div id="final-pdf-list" class="d-none">
                                <!-- PDF list will be populated here -->
                            </div>
                            <div class="text-center py-5 d-none" id="final-no-pdf">
                                <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-2 text-muted">No PDFs available for this stage</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .pdf-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s ease;
        background: white;
    }

    .pdf-item:hover {
        border-color: #0d6efd;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
        /* transform: translateY(-2px); */
    }

    .pdf-item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .pdf-item-title {
        font-weight: 600;
        color: #212529;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pdf-item-meta {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 12px;
    }

    .pdf-item-actions {
        display: flex;
        gap: 8px;
    }

    .badge-reinsurer {
        background-color: #0dcaf0;
    }

    .badge-cedant {
        background-color: #198754;
    }

    .badge-general {
        background-color: #6c757d;
    }

    .pdf-section-box {
        padding-top: 0px;
        height: 80vh;
        overflow-x: hidden;
        overflow-y: auto;
    }
</style>

<!-- Contacts Modal -->
<div class="modal fade effect-scale md-wrapper" id="propContactsModal" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="propContactsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="propContactsModalLabel">
                    <i class="bx bx-building me-1"></i>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-4" id="prop-primary-contacts">
                    <h6 class="text-uppercase fw-bold text-muted mb-3">
                        <i class="bx bx-star text-warning me-2"></i>Primary Contact
                    </h6>
                    <div class="card border-warning">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Contact Name</label>
                                    <input type="text" class="form-control-plaintext prop-primary-name"
                                        value="" readonly>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Primary Email</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control-plaintext prop-primary-email"
                                            value="" readonly>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" class="prop-primary-contact_id" name="contact_id" readonly>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="department-header rounded mb-3">
                        <h6 class="mb-0 fw-medium">
                            <i class="bx bx-user me-2"></i>Department Contacts
                        </h6>
                    </div>

                    <div id="propDepartmentContacts"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bx bx-times me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            let currentStage = 'lead';
            let pdfUrls = {};
            let bdReinsurers = [];
            let selectedReinsurers = new Set();

            let proposalState = {
                reinsurers: [],
                totalShare: 0,
                isInitialized: false
            };

            const $form = $("#proposalForm");
            const $modal = $("#proposalModal");
            const $table = $modal.find("#propReinsurersTable");

            function validateField($field) {
                const val = $field.val();
                const fieldType = $field.attr('type');
                const isRequired = $field.attr('required') !== undefined;

                $field.removeClass('is-invalid');

                if (isRequired && (!val || val.trim() === '')) {
                    $field.addClass('is-invalid');
                    return false;
                }

                if (fieldType === 'number' && val) {
                    const numVal = parseFloat(val);
                    const min = parseFloat($field.attr('min'));
                    const max = parseFloat($field.attr('max'));

                    if (isNaN(numVal) || (min && numVal < min) || (max && numVal > max)) {
                        $field.addClass('is-invalid');
                        return false;
                    }
                }

                if (fieldType === 'email' && val) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(val)) {
                        $field.addClass('is-invalid');
                        return false;
                    }
                }

                return true;
            }

            function validateReinsurerSelection() {
                if (typeof proposalState === 'undefined') {
                    return {
                        isValid: false,
                        message: 'Proposal state not initialized'
                    };
                }

                const reinsurers = proposalState.reinsurers || [];
                const totalShare = proposalState.totalShare || 0;

                if (reinsurers.length === 0) {
                    return {
                        isValid: false,
                        message: 'At least one reinsurer must be selected'
                    };
                }

                return {
                    isValid: true,
                    message: 'Valid'
                };
            }

            function getUploadedFiles() {
                return new Promise((resolve) => {
                    let attempts = 0;
                    const checkInterval = setInterval(() => {
                        if (typeof window.pipelineManager !== 'undefined' &&
                            typeof window.pipelineManager.getAllUploadedFiles === 'function') {
                            clearInterval(checkInterval);
                            const files = window.pipelineManager.getAllUploadedFiles();
                            resolve(files);
                            return;
                        }
                        attempts++;
                        if (attempts > 30) {
                            clearInterval(checkInterval);
                            console.warn('PipelineManager not available after 3 seconds');
                            resolve({});
                        }
                    }, 100);
                });
            }

            function validateRequiredFiles() {
                const errors = [];

                if (typeof window.pipelineManager === 'undefined' || !window.pipelineManager) {
                    console.warn('pipelineManager is not defined. Skipping file validation.');
                    return errors;
                }

                if (typeof window.pipelineManager.getAllUploadedFiles !== 'function') {
                    console.warn(
                        'pipelineManager.getAllUploadedFiles is not a function. Skipping file validation.');
                    return errors;
                }

                const allUploadedFiles = window.pipelineManager.getAllUploadedFiles();
                const uploadedFileNames = extractUploadedFileNames(allUploadedFiles);
                const missingFiles = findMissingRequiredFiles(uploadedFileNames);

                if (missingFiles.length > 0) {
                    errors.push(`<b>Required Files:</b> Please upload: ${missingFiles.join(', ')}`);
                }

                return errors;
            }

            function extractUploadedFileNames(uploadedFiles) {
                return Object.values(uploadedFiles).flatMap(innerArray =>
                    Object.values(innerArray).map(fileObj => fileObj.fileName)
                );
            }

            function findMissingRequiredFiles(uploadedFileNames) {
                const missingFiles = [];
                const requiredFiles = $('#proposalForm input[type="file"][required]');

                requiredFiles.each(function() {
                    const fileName = $(this).attr('name');

                    const isFileUploaded = uploadedFileNames.some(item =>
                        toCamelCase(item) === toCamelCase(fileName)
                    );

                    if (!isFileUploaded) {
                        const fieldLabel = getFieldLabel($(this));
                        missingFiles.push(fieldLabel || fileName);
                    }
                });

                return missingFiles;
            }

            function getFieldLabel($field) {
                return $field
                    .closest(".form-group")
                    .find("label")
                    .first()
                    .text()
                    .replace("*", "")
                    .trim();
            }


            function validateProposalForm() {
                let isFormValid = true;
                const errors = [];

                $form.find(".form-inputs[required], .form-inputs").each(function() {
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

                const writtenShareError = validateTotalWrittenShareIs100();
                if (writtenShareError) {
                    isFormValid = false;
                    errors.push(writtenShareError);
                }

                const reinsurerValidation = validateReinsurerSelection();
                if (!reinsurerValidation.isValid) {
                    isFormValid = false;
                    errors.push(`<b>Reinsurer Selection:</b> ${reinsurerValidation.message}`);
                }

                const fileErrors = validateRequiredFiles();

                if (fileErrors.length > 0) {
                    isFormValid = false;
                    errors.push(...fileErrors);
                }

                return {
                    isValid: isFormValid,
                    errors: errors,
                };
            }

            function toCamelCase(str) {
                return str
                    .replace(/[^a-zA-Z0-9]+(.)/g, (_, chr) => chr.toUpperCase());
            }

            function validateTotalWrittenShareIs100() {
                const totalWrittenShare = parseFloat($("#propPlacedShare").val()) || 0;

                if (totalWrittenShare === 0) {
                    return "<b>Total Written Share:</b> Please enter the total written share percentage";
                }

                if (totalWrittenShare !== 100) {
                    return `<b>Total Written Share:</b> Must be exactly 100%. Current value is ${totalWrittenShare.toFixed(2)}%`;
                }

                return null;
            }

            function prepareFormData() {
                const formData = new FormData();

                $form.find("input:not([type='file']), select, textarea").each(function() {
                    const $element = $(this);
                    const name = $element.attr("name");
                    const type = $element.attr("type");

                    if (!name) return;

                    if (type === "checkbox" || type === "radio") {
                        if ($element.is(":checked")) {
                            formData.append(name, $element.val());
                        }
                    } else {
                        const value = $element.val();
                        if (value !== null && value !== "") {
                            const cleanValue = (name?.includes('sum_insured') || name?.includes(
                                    'premium')) ?
                                value.replace(/,/g, '') :
                                value;
                            formData.append(name, cleanValue);
                        }
                    }
                });


                if (typeof window.pipelineManager !== 'undefined' && window.pipelineManager && typeof window
                    .pipelineManager
                    .getAllUploadedFiles === 'function') {
                    const allUploadedFiles = window.pipelineManager.getAllUploadedFiles();

                    Object.entries(allUploadedFiles).forEach(([fieldId, filesData]) => {
                        filesData.forEach((file, index) => {
                            formData.append('facultative_files[]', file);

                            const docType = file.fileName || 'additionalDocuments';
                            formData.append('facultative_document_types[]', docType);

                            const docTypeId = file.fileId || null;
                            if (docTypeId) {
                                formData.append('facultative_document_type_ids[]', docTypeId);
                            }
                        });
                    });
                } else {
                    console.warn(
                        'pipelineManager not available in prepareFormData(). File uploads may not be processed.'
                    );
                }

                if (typeof proposalState !== 'undefined') {
                    formData.append("reinsurers_data", JSON.stringify(proposalState.reinsurers || []));
                    formData.append("total_placed_shares", (proposalState.totalShare || 0).toFixed(2));
                    formData.append("total_unplaced_shares", (100 - (proposalState.totalShare || 0)).toFixed(2));
                }

                return formData;
            }

            $form.on('submit', function(e) {
                e.preventDefault();

                const $submitBtn = $form.find("button[type='submit']");
                const originalBtnContent = $submitBtn.html();

                const validation = validateProposalForm();

                if (!validation.isValid) {
                    let errorHtml = '<ul class="text-start mb-0">';
                    validation.errors.forEach((error) => {
                        errorHtml += `<li class="mb-1">${error}</li>`;
                    });
                    errorHtml += "</ul>";

                    Swal.fire({
                        icon: "error",
                        title: "Validation Failed",
                        html: errorHtml,
                        confirmButtonColor: "#dc3545",
                    });

                    const $firstError = $form.find(".is-invalid").first();
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
                    url: $form.attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    timeout: 60000,
                    success: function(response) {
                        if (response.success) {
                            resetProposalModal();

                            Swal.fire({
                                icon: "success",
                                title: "Proposal Sent Successfully!",
                                text: response.message ||
                                    "Your proposal has been submitted",
                                showConfirmButton: true,
                            }).then(() => {
                                $modal.modal("hide");

                                if (typeof pipelineManager !== 'undefined' &&
                                    typeof pipelineManager.reloadAllTables ===
                                    'function') {
                                    pipelineManager.reloadAllTables();
                                }

                                if (typeof pipelineManager !== 'undefined' &&
                                    typeof pipelineManager.loadChartData === 'function'
                                ) {
                                    pipelineManager.loadChartData();
                                }
                            });
                        } else {
                            throw new Error(response.message || "Submission failed");
                        }
                    },
                    error: function(xhr, status, error) {
                        let errorMessage =
                            "An unexpected error occurred while sending the proposal.";

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const serverErrors = xhr.responseJSON.errors;
                            errorMessage = '<ul class="text-start mb-0">';
                            Object.values(serverErrors).flat().forEach(err => {
                                errorMessage += `<li>${err}</li>`;
                            });
                            errorMessage += '</ul>';
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (status === "timeout") {
                            errorMessage =
                                "Request timed out. Please check your connection and try again.";
                        } else if (xhr.status === 0) {
                            errorMessage =
                                "Network error. Please check your internet connection.";
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
            });

            $modal.on('shown.bs.modal', function() {
                $("#reinSelectionPlacement").hide();

                if (!proposalState.isInitialized) {
                    proposalState.isInitialized = true;

                    try {
                        selectedReinsurersValue = $(".selected_reinsurers").val();

                        if (selectedReinsurersValue && selectedReinsurersValue.trim() !== '') {
                            const reinsurers = JSON.parse(selectedReinsurersValue);
                            proposalState.reinsurers = Array.isArray(reinsurers) ? reinsurers : [];
                        } else {
                            proposalState.reinsurers = [];
                        }

                        proposalState.totalShare = $("#propPlacedShare").val() || 0;

                        let $tb = $table.DataTable();
                        var hasDeclined = false;

                        $tb.rows().every(function() {
                            var rowData = this.data();

                            if (rowData.is_declined === true || rowData.is_declined === 1) {
                                hasDeclined = true;
                                return false;
                            }
                        });

                        if (hasDeclined) {
                            $("#reinSelectionPlacement").show();
                        }


                        $('#totalWrittenReinsurerShare').val(proposalState.totalShare.toFixed(2));
                        $('#reinsurerCount').text(proposalState.reinsurers.length);
                    } catch (error) {
                        proposalState.reinsurers = [];
                        proposalState.totalShare = 0;
                        $('#totalWrittenReinsurerShare').val('0.00');
                        $('#reinsurerCount').text('0');
                    }
                }
            });

            $modal.on('hidden.bs.modal', function() {
                resetProposalModal();
            });

            function resetProposalModal() {
                $form[0].reset();
                $form.find('.is-invalid').removeClass('is-invalid');

                if (typeof proposalState !== 'undefined') {
                    proposalState.reinsurers = [];
                    proposalState.totalShare = 0;
                    proposalState.isInitialized = false; // Reset this flag
                }

                if (typeof pipelineManager !== 'undefined' &&
                    typeof pipelineManager.clearAllFiles === 'function') {
                    pipelineManager.clearAllFiles();
                }

                $('#propReinsurersTable tbody').empty();
                $('#reinsurerCount').text('0');
                $('#totalNegReinsurerShare').val('0.00');
            }

            $('#previewPdfModal').on('show.bs.modal', function() {
                const currentStage = $('#pdf_current_stage').val();
                const previousStage = $('#pdf_previous_stage').val();
                const opportunityId = $('#pdf_opportunity_id').val();

                $('#lead-loading').addClass('d-none');
                $('#proposal-loading').addClass('d-none');
                $('#negotiation-loading').addClass('d-none');
                $('#close-won-loading').addClass('d-none');
                $('#final-loading').addClass('d-none');
                $('#lead-tab').tab('show');

                const data = {
                    currentStage,
                    previousStage,
                    opportunityId
                }

                fetchPdfUrls(data);
            });

            $('#previewPdfModal').on('hidden.bs.modal', function() {
                $('[id$="-pdf-viewer"]').attr('src', '').addClass('d-none');
                $('[id$="-loading"]').removeClass('d-none');
                $('[id$="-no-pdf"]').addClass('d-none');
                pdfUrls = {};
            });

            $('#pdfStageTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const currentStage = $(e.target).attr('id').replace('-tab', '');
                const previousStage = $('#pdf_previous_stage').val();
                const opportunityId = $('#pdf_opportunity_id').val();

                const data = {
                    currentStage,
                    previousStage: currentStage,
                    opportunityId
                }

                fetchPdfUrls(data);
            });

            $('#downloadPdfBtn').on('click', function() {
                if (pdfUrls[currentStage]) {
                    window.open(pdfUrls[currentStage], '_blank');
                }
            });

            function fetchPdfUrls(data) {
                const currentStage = data.currentStage
                const previousStage = data.previousStage
                const $listContainer = $(`#${previousStage}-pdf-list`);
                const $loadingDiv = $(`#${previousStage}-loading`);
                const $noPdfDiv = $(`#${previousStage}-no-pdf`);
                const $tabDiv = $(`#${previousStage}-tab`);

                const opportunityId = data.opportunityId

                $loadingDiv.removeClass('d-none');
                $listContainer.addClass('d-none');
                $noPdfDiv.addClass('d-none');
                $tabDiv.tab('show');

                $.ajax({
                        url: `opportunities/${opportunityId}/pdfs`,
                        method: 'GET',
                        data: {
                            stage: previousStage
                        },
                        dataType: 'json'
                    })
                    .done(function(data) {
                        $loadingDiv.addClass('d-none');

                        if (data.pdfs && data.pdfs.length > 0) {
                            renderPDFList($listContainer, data.pdfs);
                            $listContainer.removeClass('d-none');
                        } else {
                            $noPdfDiv.removeClass('d-none');
                        }
                    })
                    .fail(function(xhr, status, error) {
                        console.error('Error loading PDFs:', error);
                        $loadingDiv.addClass('d-none');
                        $noPdfDiv.removeClass('d-none');
                    });
            }

            function renderPDFList($container, pdfs) {
                $container.empty();

                const groupedPDFs = {
                    reinsurer: $.grep(pdfs, function(pdf) {
                        return pdf.type === 'reinsurer';
                    }),
                    cedant: $.grep(pdfs, function(pdf) {
                        return pdf.type === 'cedant';
                    }),
                    general: $.grep(pdfs, function(pdf) {
                        return !pdf.type || pdf.type === 'general';
                    })
                };

                if (groupedPDFs.reinsurer.length > 0) {
                    $container.append(
                        '<h6 class="mb-3 fw-600"><i class="bi bi-building me-2"></i>Reinsurer Documents</h6>');
                    $.each(groupedPDFs.reinsurer, function(index, pdf) {
                        $container.append(createPDFItem(pdf, 'reinsurer'));
                    });
                }

                if (groupedPDFs.cedant.length > 0) {
                    $container.append(
                        '<h6 class="mb-3 mt-4 fw-600"><i class="bi bi-briefcase me-2"></i>Cedant Documents</h6>'
                    );
                    $.each(groupedPDFs.cedant, function(index, pdf) {
                        $container.append(createPDFItem(pdf, 'cedant'));
                    });
                }

                if (groupedPDFs.general.length > 0) {
                    $container.append(
                        '<h6 class="mb-3 mt-4 fw-700"><i class="bi bi-file-earmark-pdf me-2"></i>General Documents</h6>'
                    );
                    $.each(groupedPDFs.general, function(index, pdf) {
                        $container.append(createPDFItem(pdf, 'general'));
                    });
                }
            }

            function createPDFItem(pdf, type) {
                const badgeClass = type === 'reinsurer' ? 'badge-reinsurer' :
                    type === 'cedant' ? 'badge-cedant' : 'badge-general';
                const badgeText = type.charAt(0).toUpperCase() + type.slice(1);

                const uploadDate = (function() {
                    if (!pdf.upload_date) return 'N/A';
                    const d = new Date(pdf.upload_date);
                    if (isNaN(d)) return 'N/A';
                    const day = ('0' + d.getDate()).slice(-2);
                    const month = ('0' + (d.getMonth() + 1)).slice(-2);
                    const year = d.getFullYear();
                    return `${day}/${month}/${year}`;
                })();
                const fileSize = pdf.file_size ? formatFileSize(pdf.file_size) : '';

                return `
                    <div class="pdf-item">
                        <div class="pdf-item-header">
                            <h6 class="pdf-item-title">
                                <i class="bi bi-file-earmark-pdf text-danger"></i>
                                ${pdf.name || 'Untitled Document'}
                            </h6>
                            <span class="badge ${badgeClass}">${badgeText}</span>
                        </div>
                        <div class="pdf-item-meta">
                            ${pdf.description ? `<div class="mb-1">${pdf.description}</div>` : ''}
                            <div>
                                <i class="bi bi-calendar3 me-1"></i> ${uploadDate}
                                ${fileSize ? `<span class="ms-3"><i class="bi bi-file-earmark me-1"></i>${fileSize}</span>` : ''}
                            </div>
                        </div>
                        <div class="pdf-item-actions">
                            <button class="btn btn-sm btn-primary open-pdf-btn" data-pdf-url="${pdf.url}" data-pdf-name="${pdf.name}">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Open in New Tab
                            </button>
                            <button class="btn btn-sm btn-outline-primary download-pdf-btn">
                                <i class="bi bi-download me-1"></i> Download
                            </button>
                        </div>
                    </div>
                `;
            }

            $(document).on('click', '.open-pdf-btn', function(e) {
                e.preventDefault();
                const pdfUrl = $(this).data('pdf-url');

                if (pdfUrl) {
                    window.open(pdfUrl, '_blank');
                }
            });

            $(document).on('click', '.download-pdf-btn', function(e) {
                e.preventDefault();
                const pdfUrl = $(this).data('pdf-url');
                const filename = $(this).data('pdf-name');
                if (pdfUrl) {
                    // console.log(pdfUrl)
                    const link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = filename || 'document.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });


            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            $(document).on('click', '.add_cedant_contacts', function(e) {
                e.preventDefault();

                const cedantName = $('.cedant_name').text();
                const opportunityId = $('#propOpportunityId').val();

                const cedantId = $(this).data('cedant-id');

                if (!cedantId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Cedant information not found'
                    });
                    return;
                }

                const originalHtml = $(this).html();
                $(this).html('<i class="bx bx-loader bx-spin"></i>');
                $(this).prop('disabled', true);

                $.ajax({
                    url: '/customer/contact-info',
                    method: 'GET',
                    data: {
                        customer_id: cedantId,
                        opportunity_id: opportunityId
                    },
                    success: function(response) {
                        console.log(response)

                        // if (response.success) {
                        //     populatePropContactsModal(response, cedantName);
                        //     $('#proposalModal').modal('hide');
                        //     $('#propContactsModal').modal('show');
                        // } else {
                        //     Swal.fire({
                        //         icon: 'error',
                        //         title: 'Error',
                        //         text: response.message || 'Failed to fetch contacts'
                        //     });
                        // }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to fetch cedant contacts';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        $('.add_cedant_contacts').html(originalHtml);
                        $('.add_cedant_contacts').prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.contact-reinsurer-btn', function(e) {
                e.preventDefault();

                const reinsurerID = $(this).data('reinsurer-id');
                const row = $(this).closest('tr');
                let reinsurerName = row.find('td:first').text().trim();
                const opportunityId = $('.opportunity_id').val();

                if (!reinsurerID) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Reinsurer ID not found'
                    });
                    return;
                }

                const originalHtml = $(this).html();
                $(this).html('<i class="bx bx-loader bx-spin"></i>');
                $(this).prop('disabled', true);

                $.ajax({
                    url: '/customer/contact-info',
                    method: 'GET',
                    data: {
                        customer_id: reinsurerID,
                        opportunity_id: opportunityId
                    },
                    success: function(response) {
                        if (response.success) {
                            populatePropContactsModal(response, reinsurerName);
                            $('#proposalModal').modal('hide');
                            $('#propContactsModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to fetch contacts'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Failed to fetch reinsurer contacts';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        $('.contact-reinsurer-btn').html(originalHtml);
                        $('.contact-reinsurer-btn').prop('disabled', false);
                    }
                });
            });

            function populatePropContactsModal(response, customerName) {
                $('#propContactsModalLabel').html(
                    `<i class="bx bx-building me-1"></i>${customerName} - Contact Management`
                );

                if (response.primary_contact) {
                    $('#prop-primary-contacts .prop-primary-name').val(
                        response.primary_contact.contact_name || 'N/A'
                    );
                    $('#prop-primary-contacts .prop-primary-email').val(
                        response.primary_contact.contact_email || 'N/A'
                    );
                    $('#prop-primary-contacts .prop-primary-contact_id').val(
                        response.primary_contact.contact_id
                    );
                } else {
                    $('#prop-primary-contacts .prop-primary-name').val('N/A');
                    $('#prop-primary-contacts .prop-primary-email').val('N/A');
                    $('#prop-primary-contacts .prop-primary-contact_id').val('');
                }

                $('#propDepartmentContacts').empty();

                if (response.department_contacts && response.department_contacts.length > 0) {
                    response.department_contacts.forEach(function(contact, index) {
                        const contactHtml = createPropContactItemHtml(contact, index);
                        $('#propDepartmentContacts').append(contactHtml);
                    });
                } else {
                    $('#propDepartmentContacts').html(`
                        <div class="text-center py-4">
                            <i class="bx bx-info-circle bx-2x text-muted mb-2 fs-15"></i>
                            <p class="text-muted">No department contacts found.</p>
                        </div>
                    `);
                }
            }

            function createPropContactItemHtml(contact, index) {
                const showLabels = index === 0;

                return `
                    <div class="contact-item rounded px-3 pb-1 mb-3" data-contact-id="${contact.contact_id || index}">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                ${showLabels ? '<label class="form-label fw-semibold small">Name</label>' : ''}
                                <input type="text" class="form-control-plaintext"
                                    value="${contact.contact_name || 'N/A'}" readonly>
                            </div>
                            <div class="col-md-4">
                                ${showLabels ? '<label class="form-label fw-semibold small">Email</label>' : ''}
                                <input type="email" class="form-control-plaintext"
                                    value="${contact.contact_email || 'N/A'}" readonly>
                            </div>
                            <div class="col-md-2">
                                ${showLabels ? '<label class="form-label fw-semibold small">Mobile</label>' : ''}
                                <input type="text" class="form-control-plaintext"
                                    value="${contact.contact_mobile_no || 'N/A'}" readonly>
                            </div>
                            <div class="col-md-3">
                                ${showLabels ? '<label class="form-label fw-semibold small">Position</label>' : ''}
                                <input type="text" class="form-control-plaintext"
                                    value="${contact.contact_position || 'N/A'}" readonly>
                            </div>
                        </div>
                    </div>
                `;
            }

            $('#propContactsModal').on('hidden.bs.modal', function() {
                $('#proposalModal').modal('show');
            });

            $("#propAvailableReinsurers").select2({
                placeholder: "Search and select reinsurer...",
                allowClear: true,
                width: "100%",
                dropdownParent: $("#proposalModal"),
                minimumInputLength: 0,
                language: {
                    searching: function() {
                        return "Searching reinsurers...";
                    },
                    noResults: function() {
                        return "No reinsurers found";
                    },
                    errorLoading: function() {
                        return "Error loading reinsurers";
                    }
                },
                ajax: {
                    url: "{{ route('pipeline.search_reinsurers') }}",
                    method: "GET",
                    dataType: "json",
                    delay: 300,
                    data: function(params) {
                        return {
                            q: params.term || "",
                            page: params.page || 1,
                            cedantId: $("#propCedId").val() || '',
                            oppId: $("#propOpportunityId").val() || '',
                            stage: 'proposal',
                        };
                    },
                    processResults: function(data, params) {
                        if (!data || !Array.isArray(data.results)) {
                            return {
                                results: [],
                                pagination: {
                                    more: false
                                }
                            };
                        }

                        bdReinsurers = data.results;

                        const results = data.results.map(function(reinsurer) {
                            return {
                                id: reinsurer.id,
                                text: reinsurer.name || 'Unknown Reinsurer',
                                name: reinsurer.name || 'Unknown',
                                email: reinsurer.email || 'N/A',
                                country: reinsurer.country || 'N/A',
                                rating: reinsurer.rating || 'N/A',
                                full_data: reinsurer
                            };
                        });

                        return {
                            results: results,
                            pagination: {
                                more: data.pagination && data.pagination.more === true
                            }
                        };
                    },
                    // cache: true,
                    error: function(xhr, status, error) {
                        // Swal.fire({
                        //     icon: 'error',
                        //     title: 'Failed to Load Reinsurers',
                        //     text: 'Unable to fetch reinsurer list. Please refresh the page.',
                        //     confirmButtonColor: '#dc3545'
                        // });
                        console.error(error)
                    }
                },
                templateResult: function(reinsurer) {
                    if (reinsurer.loading) return reinsurer.text;
                    if (!reinsurer.name) return reinsurer.text;

                    const email = reinsurer.email;

                    return `
                        <div class="reinsurer-option">
                            <div><strong>${reinsurer.name}</strong>
                            </div>
                            <div><small class="text-muted">${reinsurer.country} | Email: ${email}</small></div>
                        </div>
                    `;
                },
                templateSelection: function(reinsurer) {
                    if (!reinsurer.id) return reinsurer.text;

                    let option = $("#propAvailableReinsurers").find(
                        `option[value='${reinsurer.id}']`
                    );
                    option.attr("data-name", reinsurer.name || "");
                    option.attr("data-email", reinsurer.email || "");
                    option.attr("data-country", reinsurer.country || "");

                    return `${reinsurer.name} (${reinsurer.email}) - ${reinsurer.country}`;
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
            });

            function initializeDataTable() {
                return $table.DataTable({
                    paging: false,
                    searching: false,
                    info: false,
                    ordering: false,
                    destroy: true
                });
            }

            $("#addPropReinsurer").click(function(e) {
                e.preventDefault();

                const selectedOption = $("#propAvailableReinsurers").find("option:selected");
                const reinsurerID = selectedOption.val();

                if (!reinsurerID || reinsurerID.trim() === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please Select a Reinsurer',
                        text: 'Choose a reinsurer from the dropdown list'
                    });
                    return false;
                }

                const $shareInput = $("#propReinShare");
                const shareValue = $shareInput.val();
                const writtenSharePercent = parseFloat(shareValue);

                if (!shareValue || shareValue.trim() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Enter Written Share',
                        text: 'Please enter a share percentage (0.01 - 100)'
                    });
                    $shareInput.focus();
                    return false;
                }

                if (isNaN(writtenSharePercent)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Share Percentage',
                        text: 'Share must be a valid number'
                    });
                    $shareInput.focus();
                    return false;
                }

                if (writtenSharePercent <= 0 || writtenSharePercent > 100) {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Written Share",
                        text: `Share must be between 0.01% and 100%. You entered: ${writtenSharePercent}%`,
                        confirmButtonColor: "#3085d6",
                    });
                    $shareInput.focus();
                    return false;
                }

                if (selectedReinsurers.has(reinsurerID)) {
                    Swal.fire({
                        icon: "info",
                        title: "Already Selected",
                        text: "This reinsurer has already been added to the list.",
                        confirmButtonColor: "#3085d6",
                    });
                    return false;
                }

                const dt = initializeDataTable();
                let currentTotalPlacedShares = 0;

                dt.rows().every(function(index) {
                    const row = $(this.node());
                    const existingShare = parseFloat(row.attr("data-written-share")) || 0;
                    currentTotalPlacedShares += existingShare;
                });

                if (currentTotalPlacedShares + writtenSharePercent > 100) {
                    const remainingCapacity = 100 - currentTotalPlacedShares;
                    Swal.fire({
                        icon: "warning",
                        title: "Insufficient Capacity",
                        text: `Maximum available share is ${remainingCapacity.toFixed(2)}%. You tried to add ${writtenSharePercent}%.`,
                        confirmButtonColor: "#f39c12",
                    });
                    return false;
                }

                const reinsurerData = {
                    id: reinsurerID,
                    name: selectedOption.data("name") || selectedOption.text(),
                    email: selectedOption.data("email") || "N/A",
                    country: selectedOption.data("country") || "N/A",
                    writtenShare: writtenSharePercent
                };

                console.log('Reinsurer Data:', reinsurerData);

                const rowHtml = `
                    <tr data-reinsurer-id="${reinsurerData.id}" data-written-share="${reinsurerData.writtenShare}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="fw-medium">${escapeHtml(reinsurerData.name)}</div>
                                    <small class="text-muted">(${escapeHtml(reinsurerData.email)}) - ${escapeHtml(reinsurerData.country)}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-start">
                            <div class="share-display">
                                <strong>${reinsurerData.writtenShare.toFixed(2)}%</strong>
                            </div>
                        </td>
                        <td class="text-start">
                            <button type="button" class="btn btn-primary btn-sm contact-reinsurer-btn"
                                    data-reinsurer-id="${reinsurerData.id}" title="View Contacts">
                                <i class="bx bx-book"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm remove-reinsurer"
                                    data-reinsurer-id="${reinsurerData.id}" title="Remove">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                try {
                    dt.row.add($(rowHtml)).draw();
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to add reinsurer to table'
                    });
                    return false;
                }

                selectedReinsurers.add(reinsurerID);
                $('#reinsurerCount').text(selectedReinsurers.size);

                // updateSharesDisplay();

                // Reset form fields
                $("#propAvailableReinsurers").val(null).trigger('change');
                $("#propReinShare").val('');

                toastr.success(
                    `${reinsurerData.name} has been added with ${writtenSharePercent.toFixed(2)}% written share`,
                    'Reinsurer Added!'
                );
            });

            function updateSharesDisplay() {
                let totalShare = 0;
                const dt = initializeDataTable();

                dt.rows().every(function() {
                    const row = $(this.node());
                    const share = parseFloat(row.attr("data-written-share")) || 0;
                    totalShare += share;
                });

                const unplacedShare = 100 - totalShare;

                $('.placed-value').text(totalShare.toFixed(2) + '%');
                $('.unplaced-value').text(unplacedShare.toFixed(2) + '%');
                $('.placed-progress').css('width', totalShare + '%');

                $('#propPlacedShare').val(totalShare.toFixed(2));
                $('#propUnPlacedShare').val(unplacedShare.toFixed(2));
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            $(document).on('click', '.remove-reinsurer', function(e) {
                e.preventDefault();

                const reinsurerID = $(this).data('reinsurer-id');
                const row = $(this).closest('tr');

                console.log('Removing reinsurer:', reinsurerID);

                const dt = initializeDataTable();
                dt.row(row).remove().draw();

                selectedReinsurers.delete(reinsurerID);
                $('#reinsurerCount').text(selectedReinsurers.size);

                updateSharesDisplay();

                console.log('✅ Reinsurer removed');
            });


        });
    </script>
@endpush
