<!-- Proposal Stage Modal -->
<div id="proposalModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="staticPropoalStageLabel" aria-hidden="true" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
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
                                        class="bx bx-building me-1"></i><span class="insured-name-display"></span></h6>
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

                <div class="card custom-card section-box customScrollBar">
                    <!-- Coverage Details Section -->
                    <div class="form-section d-none">
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
                                            Total Sum Insured
                                            <span class="required-asterisk">*</span>
                                            <i class="bx bx-info-circle tooltip-trigger"
                                                title="Total coverage amount"></i>
                                        </label>
                                        <div class="currency-input">
                                            <span class="currency-symbol cr-symbl" id="currencySymbol">KES</span>
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
                                            <span class="currency-symbol cr-symbl">KES</span>
                                            <input type="text" class="form-inputs premium" name="premium" required
                                                placeholder="0.00" onkeyup="this.value=numberWithCommas(this.value)"
                                                change="this.value=numberWithCommas(this.value)" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Deductible/Excess
                                            <i class="bx bx-info-circle tooltip-trigger"
                                                title="Amount to be borne by insured"></i>
                                        </label>
                                        <div class="currency-input">
                                            <span class="currency-symbol">KES</span>
                                            <input type="text" class="form-inputs deductible" name="deductible"
                                                placeholder="0.00" onkeyup="this.value=numberWithCommas(this.value)"
                                                change="this.value=numberWithCommas(this.value)">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Reinsurer Commission Rate (%)</label>
                                        <input type="text" class="form-inputs brokerage_rate" name="brokerage_rate"
                                            placeholder="0.00" onkeyup="this.value=numberWithCommas(this.value)"
                                            change="this.value=numberWithCommas(this.value)" readonly value="10">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Interest Rate (%)</label>
                                        <input type="text" class="form-inputs interest_rate" name="interest_rate"
                                            placeholder="0.00" onkeyup="this.value=numberWithCommas(this.value)"
                                            change="this.value=numberWithCommas(this.value)">
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
                            <div class="reinsurer-selection-panel mb-2">
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="form-group">
                                            <label class="form-label">Add Reinsurer</label>
                                            <select class="sel" id="availableReinsurers"
                                                placeholder="Search and select reinsurer...">
                                                <option value="">Search and select reinsurer...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-success w-100" id="addReinsurer"
                                                style="padding: 2px 0px;">
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
                                                <th style="width: 45%">Reinsurer</th>
                                                <th style="width: 35%">Written Share (%)</th>
                                                <th style="width: 20%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reinsurersTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Risk Distribution Analysis -->
                            <div class="risk-distribution mt-4" id="riskDistribution" style="display: none;">
                                <h6 class="mb-3"><i class="bx fa-chart-pie me-2"></i>Risk Distribution Analysis
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

                            <!-- Hidden inputs for form submission -->
                            <input type="hidden" name="reinsurers_data" id="reinsurersData">
                            <input type="hidden" name="total_reinsurer_share" id="totalReinsurerShare">
                            <input type="hidden" name="retained_share" id="retainedShareValue">
                        </div>
                    </div>

                    <!-- Terms and Conditions Section -->
                    <div class="form-section d-none">
                        <div class="section-header" data-section="terms-conditions">
                            <div class="section-title">
                                <span>
                                    <i class="bx bx-file section-icon"></i>
                                    Terms & Conditions
                                </span>
                            </div>
                        </div>
                        <div class="section-content" id="terms-conditions">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Policy Wording</label>
                                        <select class="form-select" name="policy_wording">
                                            <option value="">Select Policy Wording</option>
                                            <option value="iua">IUA Standard Wording</option>
                                            <option value="lloyd">Lloyd's Policy Form</option>
                                            <option value="custom">Custom Policy Terms</option>
                                            <option value="local">Local Market Wording</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Governing Law</label>
                                        <select class="form-select" name="governing_law">
                                            <option value="">Select Governing Law</option>
                                            <option value="kenya" selected>Laws of Kenya</option>
                                            <option value="uganda">Laws of Uganda</option>
                                            <option value="rwanda">Laws of Rwanda</option>
                                            <option value="england">English Law</option>
                                            <option value="singapore">Singapore Law</option>
                                            <option value="new_york">New York Law</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Special Conditions</label>
                                <textarea class="form-inputs" name="special_conditions" rows="4" style="resize: none;"
                                    placeholder="Any special terms, conditions, or clauses applicable to this coverage..."></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Exclusions</label>
                                <textarea class="form-inputs" name="exclusions" rows="4" style="resize: none;"
                                    placeholder="List any exclusions or limitations to coverage..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Supporting Documents Section -->
                    <div class="form-section">
                        <div class="section-header" data-section="documents">
                            <div class="section-title">
                                <span>
                                    <i class="bx bx-upload section-icon"></i>
                                    Supporting Documents
                                </span>
                            </div>
                        </div>
                        <div class="section-content" id="documents">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Policy Schedule
                                            <span class="required-asterisk">*</span>
                                        </label>
                                        <div class="file-upload-area" data-field="policy_schedule">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <p class="mb-2">Drag & drop your policy schedule here</p>
                                            <p class="text-muted small mb-2">or click to browse</p>
                                            <input type="file" class="d-none" name="policy_schedule" required
                                                accept=".pdf,.doc,.docx,.xls,.xlsx">
                                            <div class="mt-2">
                                                <small class="text-muted">Max size: 10MB | Formats: PDF, DOC, DOCX,
                                                    XLS, XLSX</small>
                                            </div>
                                        </div>
                                        <div class="file-preview-container"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Loss Experience/Claims History</label>
                                        <div class="file-upload-area" data-field="loss_experience">
                                            <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                                            <p class="mb-2">Upload loss experience data</p>
                                            <p class="text-muted small mb-2">or click to browse</p>
                                            <input type="file" class="d-none" name="loss_experience"
                                                accept=".pdf,.doc,.docx,.xls,.xlsx">
                                            <div class="mt-2">
                                                <small class="text-muted">Max size: 10MB | Formats: PDF, DOC, DOCX,
                                                    XLS, XLSX</small>
                                            </div>
                                        </div>
                                        <div class="file-preview-container"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Risk Survey/Inspection Report</label>
                                        <div class="file-upload-area" data-field="risk_survey">
                                            <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                            <p class="mb-2">Upload risk survey report</p>
                                            <p class="text-muted small mb-2">or click to browse</p>
                                            <input type="file" class="d-none" name="risk_survey"
                                                accept=".pdf,.doc,.docx">
                                            <div class="mt-2">
                                                <small class="text-muted">Max size: 10MB | Formats: PDF, DOC,
                                                    DOCX</small>
                                            </div>
                                        </div>
                                        <div class="file-preview-container"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Additional Documents</label>
                                        <div class="file-upload-area" data-field="additional_docs">
                                            <i class="fas fa-file-plus fa-2x text-muted mb-2"></i>
                                            <p class="mb-2">Upload any additional documents</p>
                                            <p class="text-muted small mb-2">or click to browse</p>
                                            <input type="file" class="d-none" name="additional_docs" multiple
                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                            <div class="mt-2">
                                                <small class="text-muted">Max size: 5MB per file | Multiple files
                                                    allowed</small>
                                            </div>
                                        </div>
                                        <div class="file-preview-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Proposal Number</label>
                            <input type="text" class="form-inputs" value="PROP-2025-001625" readonly />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Submission Date</label>
                            <input type="date" class="form-inputs" value="2025-09-15" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Technical Premium (KES)</label>
                            <input type="number" class="form-inputs" value="6000000" step="0.01" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Commission Rate (%)</label>
                            <input type="number" class="form-inputs" placeholder="15.00" step="0.01" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Reinsurer Participants</label>
                        <textarea class="form-inputs" rows="2"
                            placeholder="Lloyd's Syndicate 123 (40%), Swiss Re (30%), Munich Re (30%)..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Policy Terms & Conditions</label>
                        <select class="form-inputs">
                            <option>Standard Lloyd's Wording</option>
                            <option>Munich Re Standard Terms</option>
                            <option>Swiss Re Global Terms</option>
                            <option>Custom Terms</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Proposal Notes</label>
                        <textarea class="form-inputs" rows="3"
                            placeholder="Include risk assessment, coverage details, exclusions, and client feedback..."></textarea>
                    </div> --}}
                </div>
            </div>

            <div class="modal-footer bg-light">
                <div class="d-flex justify-content-between w-100">
                    <div></div>
                    <div>
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-dark">
                            <i class="bx bx-paper-plane me-1"></i> Update Proposal
                        </button>
                    </div>
                </div>
            </div>
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
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
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
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-700);
        font-weight: 600;
        z-index: 10;
        font-size: 14px;
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

    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        color: white !important;
    }
</style>

@push('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
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
                    className: 'text-center'
                }, {
                    targets: [1],
                    className: 'text-end'
                }]
            });

            let selectedReinsurers = new Set();

            $('#addReinsurer').click(function() {
                const selectedOption = $('#availableReinsurers option:selected');
                // const reinsurerShare = parseFloat($('#reinsurerShare').val());
                // const reinsurerCommission = parseFloat($('#reinsurerCommission').val());

                if (!selectedOption.val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Select Reinsurer',
                        text: 'Please select a reinsurer from the dropdown.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                // if (!reinsurerShare || reinsurerShare <= 0 || reinsurerShare > 100) {
                //     Swal.fire({
                //         icon: 'error',
                //         title: 'Invalid Share',
                //         text: 'Please enter a valid share percentage between 0.01% and 100%.',
                //         confirmButtonColor: '#3085d6'
                //     }).then(() => {
                //         $('#reinsurerShare').focus();
                //     });
                //     return;
                // }

                // if (isNaN(reinsurerCommission) || reinsurerCommission < 0 || reinsurerCommission > 50) {
                //     Swal.fire({
                //         icon: 'error',
                //         title: 'Invalid Commission',
                //         text: 'Please enter a valid commission percentage between 0% and 50%.',
                //         confirmButtonColor: '#3085d6'
                //     }).then(() => {
                //         $('#reinsurerCommission').focus();
                //     });
                //     return;
                // }

                // Check if reinsurer already selected
                if (selectedReinsurers.has(selectedOption.val())) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Already Selected',
                        text: 'This reinsurer has already been added to the list.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                const reinsurerData = {
                    id: selectedOption.val(),
                    name: selectedOption.data('name'),
                    rating: selectedOption.data('rating'),
                    capacity: selectedOption.data('capacity'),
                    country: selectedOption.data('country'),
                    share: reinsurerShare,
                    commission: reinsurerCommission
                };

                const totalPremium = getTotalPremium();
                const premiumAmount = (totalPremium * reinsurerShare / 100);

                // const rowHtml = `
            //     <tr data-reinsurer-id="${reinsurerData.id}">
            //         <td>
            //             <div class="d-flex align-items-center">
            //                 <div>
            //                     <div class="fw-medium">${reinsurerData.name}</div>
            //                     <small class="text-muted">${reinsurerData.country}</small>
            //                 </div>
            //             </div>
            //         </td>
            //         <td class="text-end">${reinsurerData.share.toFixed(2)}%</td>
            //         <td class="text-end">${reinsurerData.commission.toFixed(2)}%</td>
            //         <td class="text-end">$${premiumAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            //         <td class="text-center">
            //             <button type="button" class="btn btn-danger btn-sm remove-reinsurer"
            //                     data-reinsurer-id="${reinsurerData.id}"
            //                     title="Remove Reinsurer">
            //                 <i class="bx bx-trash"></i>
            //             </button>
            //         </td>
            //     </tr>
            // `;

                // table.row.add($(rowHtml)).draw();

                // // Add to selected reinsurers set
                // selectedReinsurers.add(reinsurerData.id);

                // // Update reinsurer count
                // updateReinsurerCount();

                // // Reset form
                // resetForm();

                // // Show success message
                // Swal.fire({
                //     icon: 'success',
                //     title: 'Reinsurer Added!',
                //     text: `${reinsurerData.name} has been successfully added to the list.`,
                //     timer: 2000,
                //     showConfirmButton: false,
                //     toast: true,
                //     position: 'top-end'
                // });

                // // Update total shares display
                // updateTotalShares();
            });

            // Remove reinsurer functionality
            $(document).on('click', '.remove-reinsurer', function() {
                const reinsurerID = $(this).data('reinsurer-id');
                const row = $(this).closest('tr');
                const reinsurerName = row.find('td:first .fw-medium').text();

                // Confirm deletion with SweetAlert
                Swal.fire({
                    title: 'Remove Reinsurer?',
                    text: `Are you sure you want to remove ${reinsurerName} from the list?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Remove from DataTable
                        table.row(row).remove().draw();

                        // Remove from selected reinsurers set
                        selectedReinsurers.delete(reinsurerID.toString());

                        // Update reinsurer count
                        updateReinsurerCount();

                        // Update total shares display
                        updateTotalShares();

                        // Show success message
                        Swal.fire({
                            icon: 'info',
                            title: 'Removed!',
                            text: `${reinsurerName} has been removed from the list.`,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                });
            });

            // Helper functions
            function updateReinsurerCount() {
                const count = selectedReinsurers.size;
                $('#reinsurerCount').text(count);
            }

            function resetForm() {
                $('#availableReinsurers').val('');
                $('#reinsurerShare').val('');
                $('#reinsurerCommission').val('25'); // Reset to default
            }

            function showAlert(message, type = 'info') {
                // Using SweetAlert2 for better UX
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
                // This should return the total premium amount for the policy
                // You'll need to implement this based on your application logic
                // For now, returning a placeholder value
                const totalPremiumInput = $('#totalPremium'); // Assuming you have this field
                if (totalPremiumInput.length && totalPremiumInput.val()) {
                    return parseFloat(totalPremiumInput.val());
                }
                return 1000000; // Default placeholder value
            }

            function updateTotalShares() {
                let totalShares = 0;

                // Calculate total shares from all selected reinsurers
                table.rows().every(function() {
                    const rowData = $(this.node());
                    const shareText = rowData.find('td:nth-child(3)').text();
                    const share = parseFloat(shareText.replace('%', ''));
                    if (!isNaN(share)) {
                        totalShares += share;
                    }
                });

                // Update display (you might want to add a total shares display element)
                updateTotalSharesDisplay(totalShares);

                // Validate total shares don't exceed 100%
                if (totalShares > 100) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Share Limit Exceeded',
                        text: `Total shares (${totalShares.toFixed(2)}%) exceed 100%. Please review the allocation.`,
                        confirmButtonColor: '#f39c12'
                    });
                }
            }

            function updateTotalSharesDisplay(totalShares) {
                // Find or create total shares display
                let totalSharesDisplay = $('.total-shares-display');
                if (totalSharesDisplay.length === 0) {
                    const displayHtml = `
                <div class="total-shares-display mt-2">
                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                        <span class="fw-medium">Total Written Shares:</span>
                        <span class="badge bg-primary total-shares-value">0.00%</span>
                    </div>
                </div>
            `;
                    $('.selected-reinsurers-section').append(displayHtml);
                    totalSharesDisplay = $('.total-shares-display');
                }

                // Update the value
                const badgeClass = totalShares > 100 ? 'bg-danger' : totalShares === 100 ? 'bg-success' :
                    'bg-primary';
                totalSharesDisplay.find('.total-shares-value')
                    .removeClass('bg-primary bg-success bg-danger')
                    .addClass(badgeClass)
                    .text(`${totalShares.toFixed(2)}%`);
            }

            // Validate share input to prevent exceeding remaining capacity
            $('#reinsurerShare').on('input', function() {
                const currentValue = parseFloat($(this).val());
                if (isNaN(currentValue)) return;

                // Calculate remaining capacity
                let totalShares = 0;
                table.rows().every(function() {
                    const rowData = $(this.node());
                    const shareText = rowData.find('td:nth-child(3)').text();
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
                    // cache: true,
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                },
                templateResult: function(reinsurer) {
                    if (reinsurer.loading) return reinsurer.text;
                    if (!reinsurer.name) return reinsurer.text;

                    // console.log(reinsurer)
                    // const capacity = reinsurer.capacity ?
                    //     '$' + (reinsurer.capacity / 1000000).toLocaleString() + 'M' : 'N/A';
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
                    return reinsurer.name ?
                        `${reinsurer.name} (${reinsurer.email}) - ${reinsurer.country}` :
                        reinsurer.text;
                },
                escapeMarkup: function(markup) {
                    return markup;
                }
            });
        });
    </script>
@endpush
