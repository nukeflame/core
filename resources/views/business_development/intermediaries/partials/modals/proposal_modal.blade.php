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
                <input type="hidden" name="slip_type" class="slip_type">
                <input type="hidden" name="category_type" class="category_type">
                <input type="hidden" name="fac_share_offered_expected" class="total_reinsurer_share"
                    id="propFacShareOfferedExpected">

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
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="propCurrencySymbol">KES</span>
                                                <input type="text" class="form-control form-inputs total_sum_insured"
                                                    name="total_sum_insured" required placeholder="0.00"
                                                    aria-label="100% Sum Insured" aria-describedby="propCurrencySymbol"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    onchange="this.value=numberWithCommas(this.value)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Premium
                                                <span class="required-asterisk">*</span>
                                            </label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"
                                                    id="propPremiumCurrencySymbol">KES</span>
                                                <input type="text" class="form-control form-inputs premium"
                                                    name="premium" required placeholder="0.00" aria-label="Premium"
                                                    aria-describedby="propPremiumCurrencySymbol"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    onchange="this.value=numberWithCommas(this.value)">
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
                                {{-- <div class="reinsurer-selection-panel mb-2" id="reinSelectionPlacement">
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
                                </div> --}}
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
                                {{-- <div class="proposal-total-shares-display mt-3 d-block">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="proposal-shares-card proposal-placed-shares">
                                                <div class="proposal-shares-icon">
                                                    <i class="bx bx-check-circle"></i>
                                                </div>
                                                <div class="proposal-shares-info">
                                                    <span class="proposal-shares-label">Placed Shares</span>
                                                    <span
                                                        class="proposal-shares-value proposal-placed-value">0.00%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="proposal-shares-card proposal-unplaced-shares">
                                                <div class="proposal-shares-icon">
                                                    <i class="bx bx-time-five"></i>
                                                </div>
                                                <div class="proposal-shares-info">
                                                    <span class="proposal-shares-label">Unplaced Shares</span>
                                                    <span
                                                        class="proposal-shares-value proposal-unplaced-value">100.00%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="proposal-shares-progress mt-2">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success proposal-placed-progress"
                                                role="progressbar" style="width: 0%" aria-valuenow="0"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
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
                                <div id="documentFields" class="row" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" id="proposal-view-slip">
                                <i class="bx bx-file me-1"></i>Preview Slip
                            </button>
                        </div>
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
                <h5 class="modal-title d-flex align-items-center gap-2" id="staticpreviewPdfModal">
                    <i class="bi bi-files"></i>
                    Document Preview
                </h5>
                <div class="d-flex align-items-center gap-3 ms-auto me-3">
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="pipeline-bar" id="pipelineProgress"></div>

            <div class="modal-body">
                <form id="previewPdfForm">
                    <input type="hidden" name="opportunity_id" class="opportunity_id" id="pdf_opportunity_id" />
                    <input type="hidden" name="current_stage" class="current_stage" id="pdf_current_stage" />
                    <input type="hidden" name="previous_stage" class="previous_stage" id="pdf_previous_stage" />
                </form>

                <ul class="nav nav-tabs nav-fill border-bottom" id="pdfStageTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="lead-tab" data-bs-toggle="tab"
                            data-bs-target="#lead-stage" type="button" role="tab" aria-controls="lead-stage"
                            aria-selected="true">
                            <i class="bi bi-person-check me-1"></i>Lead
                            <span class="tab-badge" id="badge-lead">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="proposal-tab" data-bs-toggle="tab"
                            data-bs-target="#proposal-stage" type="button" role="tab"
                            aria-controls="proposal-stage" aria-selected="false">
                            <i class="bi bi-file-text me-1"></i>Proposal
                            <span class="tab-badge" id="badge-proposal">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="negotiation-tab" data-bs-toggle="tab"
                            data-bs-target="#negotiation-stage" type="button" role="tab"
                            aria-controls="negotiation-stage" aria-selected="false">
                            <i class="bi bi-chat-left-dots me-1"></i>Negotiation
                            <span class="tab-badge" id="badge-negotiation">0</span>
                            <i class="bi bi-star-fill tab-current-star ms-1 d-none" title="Current stage"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="close-won-tab" data-bs-toggle="tab"
                            data-bs-target="#close-won-stage" type="button" role="tab"
                            aria-controls="close-won-stage" aria-selected="false">
                            <i class="bi bi-trophy me-1"></i>Close/Won
                            <span class="tab-badge" id="badge-close-won">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="final-tab" data-bs-toggle="tab" data-bs-target="#final-stage"
                            type="button" role="tab" aria-controls="final-stage" aria-selected="false">
                            <i class="bi bi-check-circle me-1"></i>Final
                            <span class="tab-badge" id="badge-final">0</span>
                        </button>
                    </li>
                </ul>

                <div class="pdf-toolbar">
                    <div class="search-wrap">
                        <i class="bi bi-search"></i>
                        <input class="search-input" id="docSearch" type="text" placeholder="Search documents…"
                            autocomplete="off" />
                    </div>
                    <button type="button" class="filter-pill active" data-filter="all"><i class="bi bi-funnel"></i>
                        All</button>
                    {{-- <button type="button" class="filter-pill" data-filter="new">
                        <span class="filter-dot filter-dot-new"></span> New
                    </button>
                    <button type="button" class="filter-pill" data-filter="review"><i
                            class="bi bi-hourglass-split"></i> Under Review</button>
                    <button type="button" class="filter-pill" data-filter="signed"><i
                            class="bi bi-patch-check"></i>
                        Signed</button> --}}
                    <select class="sort-select" id="docSort">
                        <option value="date_desc">Newest first</option>
                        <option value="date_asc">Oldest first</option>
                        <option value="name_asc">A → Z</option>
                    </select>
                    <span class="result-count" id="resultCount">0 documents</span>
                </div>

                <div class="tab-content pdf-section-box customScrollBar" id="pdfStageContent">
                    <!-- Lead Stage -->
                    <div class="tab-pane fade show active" id="lead-stage" role="tabpanel"
                        aria-labelledby="lead-tab">
                        <div class="pdf-list-container" style="min-height: 400px;">
                            <div class="state-box" id="lead-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs…</p>
                            </div>
                            <div id="lead-pdf-list" class="d-none">
                            </div>
                            <div class="state-box d-none" id="lead-no-pdf">
                                <i class="bi bi-file-earmark-x state-icon"></i>
                                <p class="state-title">No documents yet</p>
                                <p>Documents added to the Lead stage will appear here.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Proposal Stage -->
                    <div class="tab-pane fade" id="proposal-stage" role="tabpanel" aria-labelledby="proposal-tab">
                        <div class="pdf-list-container" style="min-height: 400px;">
                            <div class="state-box" id="proposal-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs…</p>
                            </div>
                            <div id="proposal-pdf-list" class="d-none">
                            </div>
                            <div class="state-box d-none" id="proposal-no-pdf">
                                <i class="bi bi-file-earmark-x state-icon"></i>
                                <p class="state-title">No documents yet</p>
                                <p>Documents added to the Proposal stage will appear here.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Negotiation Stage -->
                    <div class="tab-pane fade" id="negotiation-stage" role="tabpanel"
                        aria-labelledby="negotiation-tab">
                        <div class="pdf-list-container" style="min-height: 400px;">
                            <div class="state-box" id="negotiation-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs…</p>
                            </div>
                            <div id="negotiation-pdf-list" class="d-none">
                            </div>
                            <div class="state-box d-none" id="negotiation-no-pdf">
                                <i class="bi bi-file-earmark-x state-icon"></i>
                                <p class="state-title">No documents yet</p>
                                <p>Documents added to the Negotiation stage will appear here.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Close/Won Stage -->
                    <div class="tab-pane fade" id="close-won-stage" role="tabpanel" aria-labelledby="close-won-tab">
                        <div class="pdf-list-container" style="min-height: 400px;">
                            <div class="state-box" id="close-won-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs…</p>
                            </div>
                            <div id="close-won-pdf-list" class="d-none">
                            </div>
                            <div class="state-box d-none" id="close-won-no-pdf">
                                <i class="bi bi-file-earmark-x state-icon"></i>
                                <p class="state-title">No documents yet</p>
                                <p>Documents added to the Close/Won stage will appear here.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Final Stage -->
                    <div class="tab-pane fade" id="final-stage" role="tabpanel" aria-labelledby="final-tab">
                        <div class="pdf-list-container" style="min-height: 400px;">
                            <div class="state-box" id="final-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading PDFs…</p>
                            </div>
                            <div id="final-pdf-list" class="d-none">
                            </div>
                            <div class="state-box d-none" id="final-no-pdf">
                                <i class="bi bi-file-earmark-x state-icon"></i>
                                <p class="state-title">No documents yet</p>
                                <p>Documents added to the Final stage will appear here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <span class="text-muted me-auto" style="font-size:.75rem;">
                    <i class="bi bi-info-circle me-1"></i>Documents are read-only. Contact your admin to make changes.
                </span>
                {{-- <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDownloadAll">
                    <i class="bi bi-download me-1"></i>Download All
                </button> --}}
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Close
                </button>
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

    .currency-input {
        position: relative;
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

    .insured-email-display {
        text-transform: lowercase !important;
    }

    .insured-contact-name-display {
        text-transform: capitalize !important;
    }

    .documents-section-content {
        padding-top: 10px;
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

    .proposal-shares-card {
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

    .proposal-shares-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .proposal-shares-card.proposal-placed-shares {
        border-left: 2px solid #198754;
    }

    .proposal-shares-card.proposal-unplaced-shares {
        border-left: 2px solid #ffc107;
    }

    .proposal-shares-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .proposal-placed-shares .proposal-shares-icon {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .proposal-unplaced-shares .proposal-shares-icon {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .proposal-shares-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        flex: 1;
    }

    .proposal-shares-label {
        font-size: 13px;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .proposal-shares-value {
        font-size: 13px;
        font-weight: 700;
        line-height: 14px;
    }

    .proposal-shares-progress {
        margin-top: 1rem;
    }

    .proposal-shares-progress .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .proposal-shares-progress .progress-bar {
        transition: width 0.6s ease;
        border-radius: 10px;
    }

    .proposal-total-shares-display {
        margin-top: 1.5rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .proposal-shares-value.text-success {
        color: #198754 !important;
    }

    .proposal-shares-value.text-danger {
        color: #dc3545 !important;
    }

    .proposal-shares-value.text-warning {
        color: #ffc107 !important;
    }

    .proposal-shares-value.text-primary {
        color: #0d6efd !important;
    }

    #previewPdfModal .modal-content {
        border: none;
        border-radius: 14px;
        overflow: hidden;
    }

    #previewPdfModal .modal-title {
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: .2px;
    }

    #previewPdfModal .modal-body {
        padding: 0;
        background: #f8fafc;
    }

    #previewPdfModal .modal-footer {
        background: #f1f5f9;
        border-top: 1px solid #e2e8f0;
        padding: .65rem 1.25rem;
    }

    #previewPdfModal .opp-badge {
        font-size: .68rem;
        font-weight: 700;
        background: rgba(255, 255, 255, .2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .3);
        border-radius: 99px;
        padding: 2px 10px;
        letter-spacing: .4px;
    }

    #previewPdfModal .header-meta {
        font-size: .72rem;
        color: rgba(255, 255, 255, .75);
    }

    #previewPdfModal .pipeline-bar {
        display: flex;
        align-items: flex-start;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: .9rem 1.5rem .65rem;
        overflow-x: auto;
    }

    #previewPdfModal .p-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        min-width: 64px;
        cursor: pointer;
        position: relative;
    }

    #previewPdfModal .p-step::after {
        content: "";
        position: absolute;
        top: 13px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: #e2e8f0;
        z-index: 0;
    }

    #previewPdfModal .p-step:last-child::after {
        display: none;
    }

    #previewPdfModal .p-dot {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .72rem;
        z-index: 1;
        position: relative;
        border: 2px solid transparent;
        transition: background .2s, box-shadow .2s, border-color .2s;
    }

    #previewPdfModal .p-label {
        font-size: .63rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .35px;
        margin-top: .3rem;
        text-align: center;
        white-space: nowrap;
    }

    #previewPdfModal .p-count {
        font-size: .58rem;
        background: #e2e8f0;
        color: #64748b;
        border-radius: 99px;
        padding: 0 5px;
        margin-top: 2px;
        line-height: 1.5;
    }

    #previewPdfModal .p-step.done .p-dot {
        background: #dcfce7;
        color: #16a34a;
        border-color: #86efac;
    }

    #previewPdfModal .p-step.done::after {
        background: linear-gradient(90deg, #86efac, #e2e8f0);
    }

    #previewPdfModal .p-step.active .p-dot {
        background: var(--p-color);
        color: #fff;
        border-color: var(--p-color);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--p-color) 18%, transparent);
    }

    #previewPdfModal .p-step.active .p-label {
        color: var(--p-color);
    }

    #previewPdfModal .p-step.active .p-count {
        background: var(--p-color);
        color: #fff;
    }

    #previewPdfModal #pdfStageTabs {
        background: #fff;
        border-bottom: 2px solid #e2e8f0 !important;
        padding: 0 1rem;
        flex-wrap: nowrap;
        overflow-x: auto;
    }

    #previewPdfModal #pdfStageTabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #64748b;
        font-size: .8rem;
        font-weight: 600;
        padding: .6rem .75rem;
        white-space: nowrap;
        margin-bottom: -2px;
        border-radius: 0;
        transition: color .15s, border-color .15s;
        display: flex;
        align-items: center;
        gap: .35rem;
    }

    #previewPdfModal #pdfStageTabs .nav-link:hover {
        color: #1e40af;
        background: transparent;
    }

    #previewPdfModal #pdfStageTabs .nav-link.active {
        color: var(--tab-c, #1e40af);
        border-bottom-color: var(--tab-c, #1e40af);
        background: transparent;
    }

    #previewPdfModal .tab-badge {
        font-size: .58rem;
        font-weight: 700;
        background: #e2e8f0;
        color: #64748b;
        border-radius: 99px;
        padding: 1px 6px;
        line-height: 1.5;
    }

    #previewPdfModal .nav-link.active .tab-badge {
        background: var(--tab-c, #1e40af);
        color: #fff;
    }

    #previewPdfModal .tab-current-star {
        font-size: .55rem;
        color: #f59e0b;
    }

    #previewPdfModal .pdf-toolbar {
        display: flex;
        align-items: center;
        gap: .6rem;
        flex-wrap: wrap;
        padding: .6rem 1rem;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
    }

    #previewPdfModal .search-wrap {
        position: relative;
        flex: 1;
        min-width: 160px;
        max-width: 770px;
    }

    #previewPdfModal .search-wrap .bi-search {
        position: absolute;
        left: .6rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: .8rem;
        pointer-events: none;
    }

    #previewPdfModal .search-input {
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        padding: .35rem .7rem .35rem 1.9rem;
        font-size: .8rem;
        width: 100%;
        background: #f8fafc;
        font-family: inherit;
    }

    #previewPdfModal .search-input:focus {
        outline: none;
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px #dbeafe;
        background: #fff;
    }

    #previewPdfModal .filter-pill {
        border: 1px solid #e2e8f0;
        border-radius: 99px;
        background: #f8fafc;
        padding: .28rem .75rem;
        font-size: .75rem;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: .3rem;
        white-space: nowrap;
        transition: all .12s;
        font-family: inherit;
    }

    #previewPdfModal .filter-pill:hover,
    #previewPdfModal .filter-pill.active {
        border-color: #93c5fd;
        color: #1d4ed8;
        background: #eff6ff;
    }

    #previewPdfModal .filter-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }

    #previewPdfModal .filter-dot-new {
        background: #3b82f6;
    }

    #previewPdfModal .sort-select {
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        background: #f8fafc;
        padding: .3rem .55rem;
        font-size: .75rem;
        color: #475569;
        font-family: inherit;
        cursor: pointer;
    }

    #previewPdfModal .sort-select:focus {
        outline: none;
        border-color: #93c5fd;
    }

    #previewPdfModal .result-count {
        margin-left: auto;
        font-size: .73rem;
        color: #94a3b8;
        white-space: nowrap;
    }

    #previewPdfModal .pdf-section-box {
        max-height: 50vh;
        overflow-y: auto;
    }

    #previewPdfModal .pdf-section-box::-webkit-scrollbar {
        width: 5px;
    }

    #previewPdfModal .pdf-section-box::-webkit-scrollbar-track {
        background: transparent;
    }

    #previewPdfModal .pdf-section-box::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 99px;
    }

    #previewPdfModal .pdf-card {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: .8rem 1.1rem;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background .12s;
        border-left: 3px solid transparent;
    }

    #previewPdfModal .pdf-card:hover {
        background: #f8fafc;
    }

    #previewPdfModal .pdf-card.active {
        background: #eff6ff;
        border-left-color: var(--stage-c, #3b82f6);
    }

    #previewPdfModal .pdf-card:last-child {
        border-bottom: none;
    }

    #previewPdfModal .pdf-icon-wrap {
        width: 38px;
        height: 46px;
        flex-shrink: 0;
        background: #fef2f2;
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    #previewPdfModal .pdf-icon-wrap i {
        font-size: 1.3rem;
        color: #ef4444;
    }

    #previewPdfModal .pdf-ext {
        position: absolute;
        bottom: 3px;
        background: #ef4444;
        color: #fff;
        font-size: .42rem;
        font-weight: 800;
        padding: 0 3px;
        border-radius: 2px;
        letter-spacing: .4px;
    }

    #previewPdfModal .pdf-meta {
        flex: 1;
        min-width: 0;
    }

    #previewPdfModal .pdf-name {
        font-size: .85rem;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #previewPdfModal .pdf-sub {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem;
        margin-top: .22rem;
    }

    #previewPdfModal .pdf-sub span {
        font-size: .7rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: .2rem;
    }

    #previewPdfModal .status-pill {
        font-size: .62rem;
        font-weight: 700;
        border-radius: 99px;
        padding: 1px 7px;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    #previewPdfModal .s-new {
        background: #dbeafe;
        color: #1d4ed8;
    }

    #previewPdfModal .s-review {
        background: #fef9c3;
        color: #854d0e;
    }

    #previewPdfModal .s-signed {
        background: #dcfce7;
        color: #15803d;
    }

    #previewPdfModal .pdf-actions {
        display: flex;
        gap: .35rem;
        flex-shrink: 0;
        opacity: 0;
        transition: opacity .12s;
    }

    #previewPdfModal .pdf-card:hover .pdf-actions,
    #previewPdfModal .pdf-card.active .pdf-actions {
        opacity: 1;
    }

    #previewPdfModal .act-btn {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        border: 1px solid #e2e8f0;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        color: #64748b;
        cursor: pointer;
        transition: all .12s;
    }

    #previewPdfModal .act-btn:hover {
        border-color: #93c5fd;
        color: #1e40af;
        background: #eff6ff;
    }

    #previewPdfModal .act-btn.dl:hover {
        border-color: #86efac;
        color: #16a34a;
        background: #f0fdf4;
    }

    #previewPdfModal .state-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1rem;
        color: #94a3b8;
        text-align: center;
    }

    #previewPdfModal .state-box .state-icon {
        font-size: 2.2rem;
        margin-bottom: .6rem;
    }

    #previewPdfModal .state-box .state-title {
        font-size: .88rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: .25rem;
    }

    #previewPdfModal .state-box p {
        font-size: .78rem;
        margin: 0;
    }

    #propContactsModal .form-label {
        color: #000;
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
                    <input type="hidden" id="propContactsOpportunityId" value="">
                    <h6 class="text-uppercase fw-bold text-muted mb-3">
                        <i class="bx bx-star text-warning me-2"></i>Primary Contact
                    </h6>
                    <div class="card border-warning">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Contact Name</label>
                                    <input type="text" class="form-control-plaintext prop-primary-name"
                                        value="">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Primary Email</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control-plaintext prop-primary-email"
                                            value="">
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" class="prop-primary-contact_id" name="contact_id">
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
                    <i class="bx bx-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="submitPropContactModal">
                    <i class="bx bx-save me-2"></i>Save Changes
                </button>
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

@push('script')
    <script>
        $(document).ready(function() {
            let bdReinsurers = [];
            let selectedReinsurers = new Set();
            let filterStatus = 'all';
            let pdfSearch = '';
            let pdfSort = 'date_desc';
            let allStageDocs = {};
            let loadedPdfStages = {};
            const TAB_COLORS = {
                lead: '#3b82f6',
                proposal: '#8b5cf6',
                negotiation: '#f59e0b',
                'close-won': '#10b981',
                final: '#ef4444'
            };
            const PDF_STAGES = [{
                    id: 'lead',
                    label: 'Lead',
                    icon: 'bi-person-check'
                },
                {
                    id: 'proposal',
                    label: 'Proposal',
                    icon: 'bi-file-text'
                },
                {
                    id: 'negotiation',
                    label: 'Negotiation',
                    icon: 'bi-chat-left-dots'
                },
                {
                    id: 'close-won',
                    label: 'Close/Won',
                    icon: 'bi-trophy'
                },
                {
                    id: 'final',
                    label: 'Final',
                    icon: 'bi-check-circle'
                }
            ];

            let proposalState = {
                reinsurers: [],
                totalShare: 0,
                isInitialized: false,
                suppressResetOnHide: false
            };

            const PREVIEW_ROUTES = {
                quotation: "{{ route('quote.quotationCoverSlip.quotation') }}",
                facultative: "{{ route('quote.quotationCoverSlip.facultative') }}",
            };

            const $form = $("#proposalForm");
            const $modal = $("#proposalModal");
            const $table = $modal.find("#propReinsurersTable");

            function validateField($field) {
                const fieldName = $field.attr("name") || $field.attr("id");
                const rawValue = $field.val();
                const fieldValue = typeof rawValue === "string" ? rawValue.trim() : "";
                const isRequired = $field.prop("required");

                clearFieldValidation($field);

                if (isRequired && !fieldValue) {
                    showFieldError($field, "This field is required");
                    return false;
                }

                if (fieldValue) {
                    const validation = getFieldValidation($field, fieldValue, fieldName);
                    if (!validation.isValid) {
                        showFieldError($field, validation.message);
                        return false;
                    }
                    $field.addClass("is-v");
                }

                return true;
            }

            function getFieldValidation($field, fieldValue, fieldName) {
                const numericValue = parseFloat(fieldValue.replace(/,/g, ""));

                if (isCurrencyField($field, fieldName)) {
                    if (!/^\d+(\.\d{1,2})?$/.test(fieldValue.replace(/,/g, ""))) {
                        return {
                            isValid: false,
                            message: "Enter a valid amount"
                        };
                    }
                    if (numericValue <= 0) {
                        return {
                            isValid: false,
                            message: "Amount must be greater than 0"
                        };
                    }
                }

                if (isPercentageField(fieldName) && !isNaN(numericValue)) {
                    if (numericValue < 0 || numericValue > 100) {
                        return {
                            isValid: false,
                            message: "Percentage must be between 0 and 100"
                        };
                    }
                }

                if (isEmailField($field, fieldName)) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(fieldValue)) {
                        return {
                            isValid: false,
                            message: "Please enter a valid email address"
                        };
                    }
                }

                if ($field.attr("type") === "number") {
                    const min = parseFloat($field.attr("min"));
                    const max = parseFloat($field.attr("max"));
                    if (!isNaN(min) && numericValue < min) {
                        return {
                            isValid: false,
                            message: `Value must be at least ${min}`
                        };
                    }
                    if (!isNaN(max) && numericValue > max) {
                        return {
                            isValid: false,
                            message: `Value must be at most ${max}`
                        };
                    }
                }

                return {
                    isValid: true
                };
            }

            function isCurrencyField($field, fieldName) {
                return $field.closest(".currency-input, .input-group").length ||
                    fieldName?.includes("premium") ||
                    fieldName?.includes("sum_insured");
            }

            function isPercentageField(fieldName) {
                return fieldName?.toLowerCase().includes("share") || fieldName?.toLowerCase().includes("rate");
            }

            function isEmailField($field, fieldName) {
                return $field.attr("type") === "email" || fieldName?.toLowerCase().includes("email");
            }

            function clearFieldValidation($field) {
                $field.removeClass("is-invalid is-v");
                $field.siblings(".invalid-feedback").remove();
            }

            function showFieldError($field, message) {
                $field.addClass("is-invalid");
                $field.after(`<div class="invalid-feedback">${message}</div>`);
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

                // const writtenShareError = validateTotalWrittenShareIs100();
                // if (writtenShareError) {
                //     isFormValid = false;
                //     errors.push(writtenShareError);
                // }

                const reinsurerValidation = validateReinsurerSelection();
                if (!reinsurerValidation.isValid) {
                    isFormValid = false;
                    errors.push(`<b>Reinsurer Selection:</b> ${reinsurerValidation.message}`);
                }

                const writtenShareMatchError = validateWrittenShareMatchesFacShareOffered();
                if (writtenShareMatchError) {
                    isFormValid = false;
                    errors.push(writtenShareMatchError);
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

                // if (totalWrittenShare !== 100) {
                //     return `<b>Total Written Share:</b> Must be exactly 100%. Current value is ${totalWrittenShare.toFixed(2)}%`;
                // }

                return null;
            }

            function parseShareValue(value) {
                const parsed = parseFloat(String(value || "").replace(/,/g, "").trim());
                return Number.isFinite(parsed) ? parsed : 0;
            }

            function getCurrentTableWrittenShareTotal() {
                if ($.fn.DataTable.isDataTable($table)) {
                    const rowData = $table.DataTable().rows().data().toArray();
                    return rowData.reduce((sum, row) => sum + parseShareValue(row?.written_share), 0);
                }

                let total = 0;
                $table.find("tbody tr").each(function() {
                    const attrShare = $(this).attr("data-written-share");

                    if (typeof attrShare !== "undefined") {
                        total += parseShareValue(attrShare);
                        return;
                    }

                    const cellText = $(this).find("td").eq(1).text();
                    total += parseShareValue(cellText);
                });

                return total;
            }

            function validateWrittenShareMatchesFacShareOffered() {
                const stage = ($("#proposalForm input[name='current_stage']").val() || "").toString().trim()
                    .toLowerCase();
                const categoryType = Number($("#proposalForm .category_type").val() || 0);
                const isQuotationLeadStage = stage === "lead" && categoryType === 1;

                const expectedShare = parseShareValue($("#propFacShareOfferedExpected").val());
                const tableTotalShare = getCurrentTableWrittenShareTotal();
                if (!isQuotationLeadStage) {
                    const difference = Math.abs(tableTotalShare - expectedShare);

                    if (difference > 0.009) {
                        return `<b>Total Written Share:</b> Total on the table (${tableTotalShare.toFixed(2)}%) must match FAC share offered (${expectedShare.toFixed(2)}%).`;
                    }
                }

                proposalState.totalShare = tableTotalShare;
                $("#propPlacedShare").val(tableTotalShare.toFixed(2));
                $("#propUnPlacedShare").val((100 - tableTotalShare).toFixed(2));

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

                            let docType = (file.fileName || 'additionalDocuments').toString()
                                .trim();
                            if (/^(.+)\1$/i.test(docType)) {
                                docType = docType.slice(0, Math.floor(docType.length / 2)).trim();
                            }
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
                    const tableTotalShare = getCurrentTableWrittenShareTotal();
                    proposalState.totalShare = tableTotalShare;
                    $("#propPlacedShare").val(tableTotalShare.toFixed(2));
                    $("#propUnPlacedShare").val((100 - tableTotalShare).toFixed(2));

                    formData.append("reinsurers_data", JSON.stringify(proposalState.reinsurers || []));
                    formData.append("total_placed_shares", tableTotalShare.toFixed(2));
                    formData.append("total_unplaced_shares", (100 - tableTotalShare).toFixed(2));
                }

                return formData;
            }

            function openProposalEmailModal(opportunityId, currentStage) {
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

                const opportunityId = ($("#propOpportunityId").val() ||
                    $("#proposalForm input[name='opportunity_id']").val() || "").toString().trim();
                const currentStage = ($("#proposalForm input[name='current_stage']").val() ||
                    "proposal").toString().trim();

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
                                text: "Your proposal has been submitted. Open email modal now?",
                                showCancelButton: true,
                                confirmButtonText: "Yes, Open Email",
                                cancelButtonText: "No",
                            }).then((result) => {
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

                                if (result.isConfirmed) {
                                    $modal.one("hidden.bs.modal", function() {
                                        openProposalEmailModal(opportunityId,
                                            currentStage || "proposal");
                                    });
                                    $modal.modal("hide");
                                } else {
                                    $modal.modal("hide");
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
                        toggleProposalPreviewSlipButton();
                    } catch (error) {
                        proposalState.reinsurers = [];
                        proposalState.totalShare = 0;
                        $('#totalWrittenReinsurerShare').val('0.00');
                        $('#reinsurerCount').text('0');
                        toggleProposalPreviewSlipButton();
                    }
                }
            });

            $modal.on('hidden.bs.modal', function() {
                if (proposalState.suppressResetOnHide) {
                    return;
                }
                resetProposalModal();
            });

            function resetProposalModal() {
                $form[0].reset();
                $form.find('.is-invalid').removeClass('is-invalid');

                if (typeof proposalState !== 'undefined') {
                    proposalState.reinsurers = [];
                    proposalState.totalShare = 0;
                    proposalState.isInitialized = false; // Reset this flag
                    proposalState.suppressResetOnHide = false;
                }

                if (typeof pipelineManager !== 'undefined' &&
                    typeof pipelineManager.clearAllFiles === 'function') {
                    pipelineManager.clearAllFiles();
                }

                $('#propReinsurersTable tbody').empty();
                $('#reinsurerCount').text('0');
                $('#totalNegReinsurerShare').val('0.00');
                toggleProposalPreviewSlipButton();
            }

            $('#previewPdfModal').on('show.bs.modal', function() {
                const opportunityId = ($('#pdf_opportunity_id').val() || '').toString().trim();
                const initialStage = getCurrentPreviewStage();

                allStageDocs = {};
                loadedPdfStages = {};
                filterStatus = 'all';
                pdfSearch = '';
                pdfSort = 'date_desc';

                $('#docSearch').val('');
                $('#docSort').val('date_desc');
                $('.filter-pill').removeClass('active');
                $('.filter-pill[data-filter="all"]').addClass('active');
                $('#resultCount').text('0 documents');
                $('#previewPdfModal .opp-badge').text(opportunityId || '--');
                $('#previewPdfModal .tab-current-star').addClass('d-none');
                $(`#${initialStage}-tab .tab-current-star`).removeClass('d-none');

                $('#pdfStageTabs button[data-bs-toggle="tab"]').each(function() {
                    bootstrap.Tab.getOrCreateInstance(this);
                });

                const tabEl = document.getElementById(`${initialStage}-tab`) || document.getElementById(
                    'lead-tab');
                if (tabEl) {
                    bootstrap.Tab.getOrCreateInstance(tabEl).show();
                }

                renderPipeline(initialStage);
                updateBadgesAndColors();
                fetchPdfUrls({
                    stage: initialStage,
                    opportunityId
                });
            });

            $('#previewPdfModal').on('hidden.bs.modal', function() {
                allStageDocs = {};
                loadedPdfStages = {};
                filterStatus = 'all';
                pdfSearch = '';
                pdfSort = 'date_desc';

                $('#docSearch').val('');
                $('#docSort').val('date_desc');
                $('.filter-pill').removeClass('active');
                $('.filter-pill[data-filter="all"]').addClass('active');
                $('[id$="-loading"]').removeClass('d-none');
                $('[id$="-pdf-list"]').addClass('d-none').empty();
                $('[id$="-no-pdf"]').addClass('d-none');
                $('#resultCount').text('0 documents');
            });

            $('#pdfStageTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                const stage = ($(e.target).attr('id') || '').replace('-tab', '');
                const opportunityId = ($('#pdf_opportunity_id').val() || '').toString().trim();
                const currentStage = getCurrentPreviewStage();

                renderPipeline(currentStage);
                updateBadgesAndColors();
                if (!loadedPdfStages[stage]) {
                    fetchPdfUrls({
                        stage,
                        opportunityId
                    });
                    return;
                }
                applyFilters();
            });

            $('#docSearch').on('input', function() {
                pdfSearch = ($(this).val() || '').toString().toLowerCase().trim();
                applyFilters();
            });

            $('#docSort').on('change', function() {
                pdfSort = $(this).val() || 'date_desc';
                applyFilters();
            });

            $(document).on('click', '#previewPdfModal .filter-pill', function() {
                filterStatus = ($(this).data('filter') || 'all').toString();
                $('#previewPdfModal .filter-pill').removeClass('active');
                $(this).addClass('active');
                applyFilters();
            });

            $(document).on('click', '#previewPdfModal .p-step', function() {
                const tabId = $(this).data('tab');
                const tabBtn = document.getElementById(tabId);
                if (tabBtn) {
                    bootstrap.Tab.getOrCreateInstance(tabBtn).show();
                }
            });

            $(document).on('click', '#previewPdfModal .act-preview', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const pdfUrl = $(this).data('pdf-url');
                if (pdfUrl) {
                    window.open(pdfUrl, '_blank');
                }
            });

            $(document).on('click', '#previewPdfModal .act-download', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const pdfUrl = $(this).data('pdf-url');
                const filename = $(this).data('pdf-name');
                if (!pdfUrl) {
                    return;
                }
                const link = document.createElement('a');
                link.href = pdfUrl;
                link.download = filename || 'document.pdf';
                link.target = '_blank';
                link.rel = 'noopener';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });

            $(document).on('click', '#previewPdfModal .act-share', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const pdfUrl = $(this).data('pdf-url');
                if (!pdfUrl) {
                    return;
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(pdfUrl).then(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Link copied',
                            text: 'Document URL copied to clipboard',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }).catch(function() {
                        window.open(pdfUrl, '_blank');
                    });
                } else {
                    window.open(pdfUrl, '_blank');
                }
            });

            $(document).on('click', '#previewPdfModal .pdf-card', function(e) {
                if ($(e.target).closest('.act-btn').length) {
                    return;
                }
                $('#previewPdfModal .pdf-card').removeClass('active');
                $(this).addClass('active');
            });

            $('#btnDownloadAll').on('click', function() {
                const currentStage = getCurrentTabStage();
                const docs = getFilteredDocs(currentStage);

                if (!docs.length) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No documents',
                        text: 'There are no documents to download in this view.'
                    });
                    return;
                }

                docs.forEach(function(doc, idx) {
                    if (!doc.url) {
                        return;
                    }
                    setTimeout(function() {
                        const link = document.createElement('a');
                        link.href = doc.url;
                        link.download = doc.filename || `document-${idx + 1}.pdf`;
                        link.target = '_blank';
                        link.rel = 'noopener';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }, idx * 160);
                });
            });

            function fetchPdfUrls(data) {
                const stage = data.stage || 'lead';
                const $listContainer = $(`#${stage}-pdf-list`);
                const $loadingDiv = $(`#${stage}-loading`);
                const $noPdfDiv = $(`#${stage}-no-pdf`);
                const opportunityId = (data.opportunityId || '').toString().trim();

                if (!$listContainer.length || !$loadingDiv.length || !$noPdfDiv.length || !opportunityId) {
                    return;
                }

                $loadingDiv.removeClass('d-none');
                $listContainer.addClass('d-none').empty();
                $noPdfDiv.addClass('d-none');

                $.ajax({
                        url: `{{ url('opportunities') }}/${encodeURIComponent(opportunityId)}/pdfs`,
                        method: 'GET',
                        data: {
                            stage
                        },
                        dataType: 'json'
                    })
                    .done(function(data) {
                        $loadingDiv.addClass('d-none');

                        const pdfs = Array.isArray(data.pdfs) ? data.pdfs : [];
                        allStageDocs[stage] = pdfs.map(function(pdf, index) {
                            return normalizePdfRecord(pdf, stage, index);
                        });
                        loadedPdfStages[stage] = true;

                        applyFilters();
                        updateBadgesAndColors();
                    })
                    .fail(function(xhr, status, error) {
                        console.error('Error loading PDFs:', error);
                        $loadingDiv.addClass('d-none');
                        allStageDocs[stage] = [];
                        loadedPdfStages[stage] = true;
                        applyFilters();
                        updateBadgesAndColors();
                    });
            }

            function applyFilters() {
                let total = 0;
                PDF_STAGES.forEach(function(stageCfg) {
                    const docs = getFilteredDocs(stageCfg.id);
                    total += docs.length;
                    renderDocList(stageCfg.id, docs);
                });
                $('#resultCount').text(`${total} document${total === 1 ? '' : 's'}`);
                updateHeaderMetaDate();
            }

            function getFilteredDocs(stage) {
                let docs = Array.isArray(allStageDocs[stage]) ? [...allStageDocs[stage]] : [];

                if (pdfSearch) {
                    docs = docs.filter(function(d) {
                        return d.name.toLowerCase().includes(pdfSearch) ||
                            d.uploadedBy.toLowerCase().includes(pdfSearch) ||
                            d.kindLabel.toLowerCase().includes(pdfSearch);
                    });
                }

                if (filterStatus !== 'all') {
                    docs = docs.filter(function(d) {
                        return d.status === filterStatus;
                    });
                }

                if (pdfSort === 'date_asc') {
                    docs.sort((a, b) => a.date.localeCompare(b.date));
                } else if (pdfSort === 'name_asc') {
                    docs.sort((a, b) => a.name.localeCompare(b.name));
                } else {
                    docs.sort((a, b) => b.date.localeCompare(a.date));
                }

                return docs;
            }

            function renderDocList(stageId, docs) {
                const $loading = $(`#${stageId}-loading`);
                const $list = $(`#${stageId}-pdf-list`);
                const $noPdf = $(`#${stageId}-no-pdf`);
                const color = TAB_COLORS[stageId] || '#3b82f6';

                if (!$list.length) {
                    return;
                }

                if (!loadedPdfStages[stageId]) {
                    $loading.removeClass('d-none');
                    $list.addClass('d-none').empty();
                    $noPdf.addClass('d-none');
                    return;
                }

                $loading.addClass('d-none');

                if (!docs.length) {
                    $list.addClass('d-none').empty();
                    $noPdf.removeClass('d-none');
                    return;
                }

                $noPdf.addClass('d-none');
                $list.removeClass('d-none').html(docs.map(function(doc) {
                    return createPDFItem(doc, color);
                }).join(''));
            }

            function createPDFItem(doc, stageColor) {
                return `
                    <div class="pdf-card" data-id="${doc.id}" style="--stage-c:${stageColor}">
                        <div class="pdf-icon-wrap">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <span class="pdf-ext">PDF</span>
                        </div>
                        <div class="pdf-meta">
                            <div class="pdf-name" title="${escapeHtml(doc.name)}">${escapeHtml(doc.name)}</div>
                            <div class="pdf-sub">
                                <span><i class="bi bi-calendar3"></i>${formatDate(doc.date)}</span>
                                <span><i class="bi bi-hdd"></i>${doc.size}</span>
                                <span><i class="bi bi-tag"></i>${escapeHtml(firstUpper(doc.kindLabel))}</span>
                            </div>
                        </div>
                        <div class="pdf-actions">
                            <button type="button" class="act-btn act-preview" title="Preview" data-pdf-url="${escapeHtml(doc.url)}"><i class="bi bi-eye"></i></button>
                            <button type="button" class="act-btn dl act-download" title="Download" data-pdf-url="${escapeHtml(doc.url)}" data-pdf-name="${escapeHtml(doc.filename)}"><i class="bi bi-download"></i></button>
                            <button type="button" class="act-btn act-share" title="Share" data-pdf-url="${escapeHtml(doc.url)}"><i class="bi bi-share"></i></button>
                        </div>
                    </div>
                `;
            }

            function normalizePdfRecord(pdf, stage, index) {
                const recordDate = normalizeDate(pdf.upload_date || pdf.date || new Date().toISOString());
                const explicitStatus = (pdf.status || '').toString().toLowerCase();
                const derivedStatus = explicitStatus === 'new' || explicitStatus === 'review' || explicitStatus ===
                    'signed' ? explicitStatus : 'review';
                return {
                    id: pdf.id || `${stage}-${index}`,
                    name: (pdf.description || pdf.name || 'Untitled Document').toString(),
                    filename: (pdf.name || pdf.description || `document-${index + 1}.pdf`).toString(),
                    size: pdf.file_size ? formatFileSize(Number(pdf.file_size)) : 'N/A',
                    date: recordDate,
                    status: derivedStatus,
                    uploadedBy: (pdf.uploaded_by || pdf.uploadedBy || pdf.type || 'System').toString(),
                    kindLabel: (pdf.type || 'general').toString(),
                    url: (pdf.url || '').toString()
                };
            }

            function normalizeDate(input) {
                const d = new Date(input);
                if (Number.isNaN(d.getTime())) {
                    return new Date().toISOString().slice(0, 10);
                }
                return d.toISOString().slice(0, 10);
            }

            function formatDate(dateString) {
                const d = new Date(dateString);
                if (Number.isNaN(d.getTime())) {
                    return 'N/A';
                }
                return d.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function stageIndex(stageId) {
                return PDF_STAGES.findIndex(function(s) {
                    return s.id === stageId;
                });
            }

            function renderPipeline(currentStageId) {
                const curIdx = stageIndex(currentStageId);
                const html = PDF_STAGES.map(function(stage, index) {
                    const css = index < curIdx ? 'done' : (index === curIdx ? 'active' : '');
                    const count = Array.isArray(allStageDocs[stage.id]) ? allStageDocs[stage.id].length : 0;
                    const dotIcon = index < curIdx ? '<i class="bi bi-check-lg"></i>' :
                        `<i class="bi ${stage.icon}"></i>`;
                    return `
                        <div class="p-step ${css}" style="--p-color:${TAB_COLORS[stage.id]}" data-tab="${stage.id}-tab" title="Jump to ${stage.label}">
                            <div class="p-dot">${dotIcon}</div>
                            <div class="p-label">${stage.label}</div>
                            <div class="p-count">${count} doc${count === 1 ? '' : 's'}</div>
                        </div>
                    `;
                }).join('');
                $('#pipelineProgress').html(html);
            }

            function updateBadgesAndColors() {
                PDF_STAGES.forEach(function(stage) {
                    const count = Array.isArray(allStageDocs[stage.id]) ? allStageDocs[stage.id].length : 0;
                    $(`#badge-${stage.id}`).text(count);
                    $(`#${stage.id}-tab`).css('--tab-c', TAB_COLORS[stage.id] || '#1e40af');
                });
                renderPipeline(getCurrentPreviewStage());
            }

            function updateHeaderMetaDate() {
                const allDocs = Object.values(allStageDocs).flat();
                if (!allDocs.length) {
                    $('#previewPdfModal .header-meta').html('<i class="bi bi-clock me-1"></i>Last updated: --');
                    return;
                }
                const latest = allDocs.slice().sort((a, b) => b.date.localeCompare(a.date))[0];
                $('#previewPdfModal .header-meta').html(
                    `<i class="bi bi-clock me-1"></i>Last updated: ${formatDate(latest.date)}`
                );
            }

            function getCurrentTabStage() {
                const activeTab = $('#pdfStageTabs .nav-link.active').attr('id');
                return (activeTab || 'lead-tab').replace('-tab', '');
            }

            function getCurrentPreviewStage() {
                const rawStage = ($('#pdf_current_stage').val() || 'lead').toString().trim().toLowerCase()
                    .replace(/_/g, '-')
                    .replace(/\s+/g, '-');
                return PDF_STAGES.some(stage => stage.id === rawStage) ? rawStage : 'lead';
            }

            function escapeHtml(text) {
                return String(text || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function firstUpper(text) {
                if (!text) return '';
                return String(text)
                    .replace(/[_-]+/g, ' ')
                    .trim()
                    .toLowerCase()
                    .replace(/\b\w/g, function(char) {
                        return char.toUpperCase();
                    });
            }

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
                        if (response.success) {
                            populatePropContactsModal(response, cedantName);
                            $('#propContactsOpportunityId').val(opportunityId);
                            proposalState.suppressResetOnHide = true;
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

            $(document).on('click', '.contacts-reinsurer-btn, .contact-reinsurer-btn', function(e) {
                e.preventDefault();

                const $button = $(this);
                const reinsurerID = $button.data('reinsurer-id');
                const row = $button.closest('tr');
                const reinsurerName = row.find('td:first .fw-medium').text().trim() || row.find('td:first')
                    .text()
                    .trim();
                const opportunityId = $('#propOpportunityId').val();

                if (!reinsurerID) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Reinsurer ID not found'
                    });
                    return;
                }

                if (!opportunityId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Opportunity ID is missing for this proposal.'
                    });
                    return;
                }

                const originalHtml = $button.html();
                $button.html('<i class="bx bx-loader bx-spin"></i>');
                $button.prop('disabled', true);

                $.ajax({
                    url: `/reinsurers/${reinsurerID}/contacts`,
                    method: 'POST',
                    data: {
                        reinsurer_id: reinsurerID,
                        opportunity_id: opportunityId
                    },
                    success: function(response) {
                        if (response.success) {
                            const contactsPayload = response.data || response;
                            const responseReinsurerName = response?.data?.reinsurer?.name ||
                                reinsurerName;
                            populatePropContactsModal(contactsPayload, responseReinsurerName);
                            $('#propContactsOpportunityId').val(opportunityId);
                            proposalState.suppressResetOnHide = true;
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
                        const errorMessages = {
                            404: 'Reinsurer contacts not found',
                            403: 'Access denied to reinsurer contacts'
                        };
                        const errorMessage = errorMessages[xhr.status] ||
                            xhr.responseJSON?.message ||
                            'Failed to fetch reinsurer contacts';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        $button.html(originalHtml);
                        $button.prop('disabled', false);
                    }
                });
            });

            function populatePropContactsModal(contactData, customerName) {
                $('#propContactsModalLabel').html(
                    `<i class="bx bx-building me-1"></i>${customerName} - Contact Management`
                );

                const primaryContact = contactData?.primary_contact || null;
                if (primaryContact) {
                    $('#prop-primary-contacts .prop-primary-name').val(
                        primaryContact.name || primaryContact.contact_name || 'N/A'
                    );
                    $('#prop-primary-contacts .prop-primary-email').val(
                        primaryContact.email || primaryContact.contact_email || 'N/A'
                    );
                    $('#prop-primary-contacts .prop-primary-contact_id').val(
                        primaryContact.id || primaryContact.contact_id || ''
                    );
                } else {
                    $('#prop-primary-contacts .prop-primary-name').val('N/A');
                    $('#prop-primary-contacts .prop-primary-email').val('N/A');
                    $('#prop-primary-contacts .prop-primary-contact_id').val('');
                }

                $('#propDepartmentContacts').empty();

                const departmentContacts = Array.isArray(contactData?.department_contacts) ? contactData
                    .department_contacts : [];
                if (departmentContacts.length > 0) {
                    departmentContacts.forEach(function(contact, index) {
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
                const contactId = contact.id || contact.contact_id || '';
                const contactName = escapeHtml(contact.name || contact.contact_name || '');
                const contactEmail = escapeHtml(contact.email || contact.contact_email || '');
                const isCcEmail = !!contact.cc_email;

                return `
                    <div class="contact-item rounded px-3 pb-1 mb-3" data-contact-id="${contactId}">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                ${showLabels ? '<label class="form-label fw-semibold mb-1">Contact Name</label>' : ''}
                                <input type="text" class="form-control-plaintext prop-contact-name"
                                    value="${contactName}" data-field="name">
                            </div>
                            <div class="col-md-6">
                                ${showLabels ? '<label class="form-label fw-semibold mb-1">Email</label>' : ''}
                                <input type="email" class="form-control-plaintext prop-contact-email"
                                    value="${contactEmail}" data-field="email">
                            </div>
                            <div class="col-md-2">
                                ${showLabels ? '<label class="form-label fw-semibold mb-1">CC Email</label>' : ''}
                                <div class="form-check mt-2 px-0">
                                    <input class="form-check-input mailc-checkbox prop-mailc-checkbox" type="checkbox"
                                        ${isCcEmail ? 'checked' : ''} data-field="cc_email">
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

            function savePropContactsModal() {
                const {
                    contacts,
                    errors
                } = collectPropContacts();

                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: errors[0]
                    });
                    return;
                }

                if (contacts.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please add at least one contact.'
                    });
                    return;
                }

                const $submitBtn = $('#submitPropContactModal');
                $submitBtn.prop('disabled', true);
                const opportunityId = $('#propContactsOpportunityId').val() || $('#propOpportunityId').val();

                if (!opportunityId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Opportunity ID is missing. Please reopen the modal and try again.'
                    });
                    $submitBtn.prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: "{{ route('rein.contacts.update') }}",
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        opportunity_id: opportunityId,
                        contacts: contacts
                    }),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Contact information has been updated.'
                            });
                            $('#propContactsModal').modal('hide');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Contact update error:', error);
                        const validationErrors = xhr.responseJSON?.errors;
                        if (validationErrors) {
                            const firstErrorKey = Object.keys(validationErrors)[0];
                            const firstError = firstErrorKey ? validationErrors[firstErrorKey]?.[0] :
                                null;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: firstError || 'Failed to update contacts'
                            });
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to update contacts'
                        });
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false);
                    }
                });
            }

            function collectPropContacts() {
                const contacts = [];
                const errors = [];

                const isValidEmail = (email) => {
                    if (!email) return false;
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                };

                const normalizeText = (value) => {
                    return (value || '').toString().trim();
                };

                const normalizeId = (value) => {
                    const parsed = parseInt(value, 10);
                    return Number.isInteger(parsed) ? parsed : null;
                };

                const primaryData = {
                    id: normalizeId($('#prop-primary-contacts .prop-primary-contact_id').val()),
                    name: normalizeText($('#prop-primary-contacts .prop-primary-name').val()),
                    email: normalizeText($('#prop-primary-contacts .prop-primary-email').val()),
                    cc_email: false,
                    is_primary: true
                };

                if (primaryData.name.toUpperCase() === 'N/A') {
                    primaryData.name = '';
                }

                if (primaryData.email.toUpperCase() === 'N/A') {
                    primaryData.email = '';
                }

                if (primaryData.name || primaryData.email) {
                    if (!primaryData.name || !primaryData.email) {
                        errors.push('Primary contact must include both name and email.');
                    } else if (!isValidEmail(primaryData.email)) {
                        errors.push('Primary contact email format is invalid.');
                    } else if (primaryData.id !== null) {
                        contacts.push(primaryData);
                    }
                }

                $('#propDepartmentContacts .contact-item').each(function(index) {
                    const name = normalizeText($(this).find('.prop-contact-name').val());
                    const email = normalizeText($(this).find('.prop-contact-email').val());

                    if (!name && !email) {
                        return;
                    }

                    const contactData = {
                        id: normalizeId($(this).data('contact-id')),
                        name,
                        email,
                        cc_email: $(this).find('.prop-mailc-checkbox').is(':checked'),
                        is_primary: false
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

            $(document).on('click', '#submitPropContactModal', function() {
                savePropContactsModal();
            });

            $('#propContactsModal').on('hidden.bs.modal', function() {
                proposalState.suppressResetOnHide = false;
                $('#propContactsOpportunityId').val('');
                $('#proposalModal').modal('show');
            });

            function isReinsurerOnlyEntry(entity) {
                if (!entity || typeof entity !== "object") return false;

                const typeHints = [
                    entity.slug,
                    entity.type_slug,
                    entity.customer_type_slug,
                    entity.type_name,
                    entity.customer_type_name,
                    entity.customer_type,
                    entity.role,
                    entity.category
                ].filter(Boolean).join(" ").toLowerCase();

                return !typeHints || typeHints.includes("reinsurer");
            }

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

                        const reinsurerOnlyResults = data.results.filter(isReinsurerOnlyEntry);
                        bdReinsurers = reinsurerOnlyResults;

                        const results = reinsurerOnlyResults.map(function(reinsurer) {
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
                    destroy: true,
                    columns: [{
                            data: null,
                            defaultContent: ''
                        },
                        {
                            data: null,
                            defaultContent: ''
                        },
                        {
                            data: null,
                            defaultContent: ''
                        }
                    ]
                });
            }

            function getCurrentTotalPlacedShares() {
                const dt = initializeDataTable();
                let totalPlacedShares = 0;

                dt.rows().every(function() {
                    const row = $(this.node());
                    const existingShare = parseFloat(row.attr("data-written-share")) || 0;
                    totalPlacedShares += existingShare;
                });

                return totalPlacedShares;
            }

            function updateProposalCapacityState() {
                const targetShare = 100;
                const currentPlacedShares = getCurrentTotalPlacedShares();
                const remainingCapacity = Math.max(targetShare - currentPlacedShares, 0);
                const $shareInput = $("#propReinShare");

                $shareInput.attr("max", remainingCapacity.toFixed(2));
                $shareInput.attr(
                    "placeholder",
                    remainingCapacity > 0 ?
                    `0.01 - ${remainingCapacity.toFixed(2)}` :
                    "No capacity left",
                );
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

                const currentTotalPlacedShares = getCurrentTotalPlacedShares();
                const targetShare = 100;

                if (currentTotalPlacedShares + writtenSharePercent > targetShare) {
                    const remainingCapacity = Math.max(targetShare - currentTotalPlacedShares, 0);
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
                            <button type="button" class="btn btn-primary btn-sm contacts-reinsurer-btn"
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
                    const dt = initializeDataTable();
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
                toggleProposalPreviewSlipButton();

                updateSharesDisplay();

                $("#propAvailableReinsurers").val(null).trigger('change');
                $("#propReinShare").val('');

                toastr.success(
                    `${reinsurerData.name} has been added with ${writtenSharePercent.toFixed(2)}% written share`,
                    'Reinsurer Added!'
                );
            });

            function updateSharesDisplay() {
                // let totalShare = 0;
                // const dt = initializeDataTable();

                // dt.rows().every(function() {
                //     const row = $(this.node());
                //     const share = parseFloat(row.attr("data-written-share")) || 0;
                //     totalShare += share;
                // });

                // const unplacedShare = 100 - totalShare;
                // const targetShare = 100;
                // const $sharesDisplay = $("#proposalModal .proposal-total-shares-display");

                // updateShareValue($sharesDisplay, totalShare, unplacedShare, targetShare);
                // updateProgressBar($sharesDisplay, totalShare, targetShare);

                // $('#propPlacedShare').val(totalShare.toFixed(2));
                // $('#propUnPlacedShare').val(unplacedShare.toFixed(2));
                // updateProposalCapacityState();
            }

            function updateShareValue($sharesDisplay, totalPlaced, totalUnplaced, targetTotal) {
                const placedValueClass = totalPlaced === targetTotal ? "text-success" :
                    totalPlaced > targetTotal ? "text-danger" : "text-primary";

                $sharesDisplay.find(".proposal-placed-value")
                    .removeClass("text-success text-danger text-primary text-warning")
                    .addClass(placedValueClass)
                    .text(`${totalPlaced.toFixed(2)}%`);

                const unplacedValueClass = totalUnplaced === 0 ? "text-success" :
                    totalUnplaced < 0 ? "text-danger" : "text-warning";

                $sharesDisplay.find(".proposal-unplaced-value")
                    .removeClass("text-success text-danger text-primary text-warning")
                    .addClass(unplacedValueClass)
                    .text(`${totalUnplaced.toFixed(2)}%`);
            }

            function updateProgressBar($sharesDisplay, totalPlaced, targetTotal) {
                let progressWidth = 0;
                if (targetTotal > 0) {
                    progressWidth = Math.min((totalPlaced / targetTotal) * 100, 100);
                }

                const progressClass = totalPlaced === targetTotal ? "bg-success" :
                    totalPlaced > targetTotal ? "bg-danger" : "bg-primary";

                $sharesDisplay.find(".proposal-placed-progress")
                    .removeClass("bg-success bg-danger bg-primary")
                    .addClass(progressClass)
                    .css("width", `${progressWidth}%`)
                    .attr("aria-valuenow", progressWidth);
            }

            function toggleProposalPreviewSlipButton() {
                const badgeCount = parseInt(($("#reinsurerCount").text() || "0").toString(), 10) || 0;
                const tableRows = $("#propReinsurersTable tbody tr").length;
                const hasReinsurer = badgeCount > 0 || selectedReinsurers.size > 0 || (proposalState.reinsurers
                    ?.length || 0) > 0 || tableRows > 0;
                $("#proposal-view-slip").toggle(hasReinsurer);
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function previewCoverSlip(printoutType = 0) {
                const sourceForm = $form
                const postForm = $('#proposal-quoteslip-form');
                const hasReinsurer = (proposalState.reinsurers?.length || 0) > 0 || selectedReinsurers.size > 0;
                if (!hasReinsurer) {
                    toastr.warning('Please add at least one reinsurer before previewing the slip.');
                    return;
                }

                postForm.find('input[type="hidden"]:not([name="_token"])').remove();

                const formData = prepareFormData();
                const categoryType = Number($form.find(".category_type").val() || formData.get("category_type") ||
                    2);
                const targetAction = categoryType === 1 ? PREVIEW_ROUTES.quotation : PREVIEW_ROUTES.facultative;
                postForm.attr("action", targetAction);

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


            $("#proposal-view-slip").on("click", () => previewCoverSlip());

            $(document).on('click', '.remove-reinsurer', function(e) {
                e.preventDefault();

                const reinsurerID = $(this).data('reinsurer-id');
                const row = $(this).closest('tr');

                const dt = initializeDataTable();
                dt.row(row).remove().draw();

                selectedReinsurers.delete(reinsurerID);
                $('#reinsurerCount').text(selectedReinsurers.size);
                toggleProposalPreviewSlipButton();

                updateSharesDisplay();
            });

            updateProposalCapacityState();
            toggleProposalPreviewSlipButton();
        });
    </script>
@endpush
