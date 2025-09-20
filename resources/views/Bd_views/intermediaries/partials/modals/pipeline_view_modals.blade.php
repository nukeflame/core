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
                                Slip #: FAC-2024-001847
                            </div>
                            <div class="mt-2 text-light opacity-75">
                                <small>Created: September 20, 2025</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3">
                    <div class="company-info">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-2 fw-medium"><i class="bx bx-building me-1"></i>Pointmarc Holding Ltd</h6>
                                <p class="mb-0 small">Insured / Policyholder</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-0 small">Contact: insured@pointmarc.com</p>
                                <p class="mb-0 small">Tel: +254 20 1234567</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card custom-card section-box">
                    <!-- Coverage Details Section -->
                    <div class="form-section">
                        <div class="section-header" data-section="coverage-details">
                            <div class="section-title">
                                <span>
                                    <i class="bx bx-umbrella section-icon"></i>
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
                                            <input type="number" class="form-inputs" name="total_sum_insured" required
                                                placeholder="0.00" step="0.01" min="0">
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
                                            <input type="number" class="form-inputs" name="premium" required
                                                placeholder="0.00" step="0.01" min="0">
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
                                            <input type="number" class="form-inputs" name="deductible"
                                                placeholder="0.00" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Premium Rate (%)</label>
                                        <input type="number" class="form-inputs" name="premium_rate" placeholder="0.00"
                                            step="0.01" min="0" max="100">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Brokerage Rate (%)</label>
                                        <input type="number" class="form-inputs" name="brokerage_rate"
                                            placeholder="0.00" step="0.01" min="0" max="100"
                                            value="10">
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="form-group">
                                <label class="form-label">
                                    Coverage Description
                                    <span class="required-asterisk">*</span>
                                </label>
                                <textarea class="form-inputs" name="coverage_description" rows="4" required
                                    placeholder="Describe the coverage, risks covered, and any specific conditions..."></textarea>
                            </div> --}}
                        </div>
                    </div>

                    <!-- Reinsurer Information Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-title">
                                <span>
                                    <i class="bx bx-handshake section-icon"></i>
                                    Reinsurer Placement
                                </span>
                            </div>
                        </div>
                        <div class="section-content" id="reinsurer-info">
                            <div class="reinsurer-selection-panel mb-2">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="form-label">Add Reinsurer</label>
                                            <select class="form-select" id="availableReinsurers"
                                                placeholder="Search and select reinsurer...">
                                                <option value="">Search and select reinsurer...</option>
                                                <option value="1" data-name="Swiss Re" data-capacity="500000000"
                                                    data-rating="AA-" data-country="Switzerland">Swiss Re (AA-) -
                                                    Switzerland</option>
                                                <option value="2" data-name="Munich Re"
                                                    data-capacity="750000000" data-rating="AA"
                                                    data-country="Germany">Munich Re (AA) - Germany
                                                </option>
                                                <option value="3" data-name="Lloyd's of London"
                                                    data-capacity="1000000000" data-rating="A+"
                                                    data-country="United Kingdom">Lloyd's of London (A+) - UK</option>
                                                <option value="4" data-name="Berkshire Hathaway Re"
                                                    data-capacity="1200000000" data-rating="AA"
                                                    data-country="United States">Berkshire Hathaway Re (AA) - USA
                                                </option>
                                                <option value="5" data-name="Hannover Re"
                                                    data-capacity="400000000" data-rating="AA-"
                                                    data-country="Germany">Hannover Re (AA-) - Germany
                                                </option>
                                                <option value="6" data-name="SCOR SE" data-capacity="300000000"
                                                    data-rating="A+" data-country="France">SCOR SE (A+) - France
                                                </option>
                                                <option value="7" data-name="Everest Re"
                                                    data-capacity="250000000" data-rating="A" data-country="Bermuda">
                                                    Everest Re (A) - Bermuda
                                                </option>
                                                <option value="8" data-name="PartnerRe"
                                                    data-capacity="350000000" data-rating="A+"
                                                    data-country="Bermuda">PartnerRe (A+) - Bermuda
                                                </option>
                                                <option value="9" data-name="RenaissanceRe"
                                                    data-capacity="200000000" data-rating="A" data-country="Bermuda">
                                                    RenaissanceRe (A) - Bermuda</option>
                                                <option value="10" data-name="Axis Re" data-capacity="180000000"
                                                    data-rating="A+" data-country="Bermuda">Axis Re (A+) - Bermuda
                                                </option>
                                                <option value="11" data-name="QBE Re" data-capacity="150000000"
                                                    data-rating="A" data-country="Australia">QBE Re (A) - Australia
                                                </option>
                                                <option value="12" data-name="Korean Re"
                                                    data-capacity="120000000" data-rating="A-"
                                                    data-country="South Korea">Korean Re (A-) - South
                                                    Korea</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Share (%)</label>
                                            <input type="number" class="form-inputs" id="reinsurerShare"
                                                placeholder="0.00" step="0.01" min="0.01" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Commission (%)</label>
                                            <input type="number" class="form-inputs" id="reinsurerCommission"
                                                placeholder="25.00" step="0.01" min="0" max="50"
                                                value="25">
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
                                    <table class="table table-hover table-striped selected-reinsurers-table"
                                        id="reinsurersTable">
                                        <thead class="table-d">
                                            <tr>
                                                <th style="width: 30%">Reinsurer</th>
                                                <th style="width: 15%">Rating</th>
                                                <th style="width: 15%">Share (%)</th>
                                                <th style="width: 15%">Commission (%)</th>
                                                <th style="width: 15%">Premium Amount</th>
                                                <th style="width: 10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reinsurersTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{--
                            <!-- Share Summary -->
                            <div class="share-summary mt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="share-calculator">
                                            <h6 class="mb-3"><i class="bx bx-calculator me-2"></i>Share
                                                Distribution Summary</h6>
                                            <div class="share-item">
                                                <span>Total Written Share:</span>
                                                <span id="totalWrittenShare">100.00%</span>
                                            </div>
                                            <div class="share-item">
                                                <span>Total Allocated:</span>
                                                <span id="totalAllocatedShare" class="text-primary">0.00%</span>
                                            </div>
                                            <div class="share-item">
                                                <span>Remaining (Retained):</span>
                                                <span id="remainingShare" class="text-success">100.00%</span>
                                            </div>
                                            <div class="share-item border-top pt-2">
                                                <span><strong>Allocation Status:</strong></span>
                                                <span id="allocationStatus" class="badge bg-warning">Incomplete</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="share-calculator">
                                            <h6 class="mb-3"><i class="bx fa-money-bill-wave me-2"></i>Premium
                                                Distribution</h6>
                                            <div class="share-item">
                                                <span>Total Premium:</span>
                                                <span id="totalPremiumDisplay">KES 0.00</span>
                                            </div>
                                            <div class="share-item">
                                                <span>Reinsurers' Premium:</span>
                                                <span id="reinsurersPremiumTotal" class="text-primary">KES 0.00</span>
                                            </div>
                                            <div class="share-item">
                                                <span>Retained Premium:</span>
                                                <span id="retainedPremiumTotal" class="text-success">KES 0.00</span>
                                            </div>
                                            <div class="share-item border-top pt-2">
                                                <span><strong>Total Commission:</strong></span>
                                                <span id="totalCommissionAmount" class="text-info">KES 0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- <!-- Placement Strategy Options -->
                            <div class="placement-strategy mt-4">
                                <h6 class="mb-3"><i class="bx fa-chess me-2"></i>Placement Strategy</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Placement Type</label>
                                            <select class="form-select" name="placement_type">
                                                <option value="quota_share">Quota Share</option>
                                                <option value="surplus" selected>Surplus Share</option>
                                                <option value="excess_of_loss">Excess of Loss</option>
                                                <option value="risk_excess">Risk Excess</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Lead Reinsurer</label>
                                            <select class="form-select" name="lead_reinsurer"
                                                id="leadReinsurerSelect">
                                                <option value="">Select Lead Reinsurer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Minimum Acceptance (%)</label>
                                            <input type="number" class="form-control" name="minimum_acceptance"
                                                placeholder="75.00" step="0.01" min="0" max="100"
                                                value="75">
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

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
                        <button type="button" class="btn btn-dark" onclick="submitForm()">
                            <i class="bx bx-paper-plane me-1"></i> Update Proposal
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
        color: #000;
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
                    zeroRecords: "No matching reinsurers found",
                    emptyTable: "No reinsurers selected yet. Add reinsurers using the form above."
                },
                columnDefs: [{
                        targets: -1,
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        targets: [2, 3, 4],
                        className: 'text-end'
                    }
                ]
            });

            // Store selected reinsurers to prevent duplicates
            let selectedReinsurers = new Set();

            // Add reinsurer functionality
            $('#addReinsurer').click(function() {
                const selectedOption = $('#availableReinsurers option:selected');
                const reinsurerShare = parseFloat($('#reinsurerShare').val());
                const reinsurerCommission = parseFloat($('#reinsurerCommission').val());

                // Validation
                if (!selectedOption.val()) {
                    showAlert('Please select a reinsurer', 'warning');
                    return;
                }

                if (!reinsurerShare || reinsurerShare <= 0 || reinsurerShare > 100) {
                    showAlert('Please enter a valid share percentage (0.01 - 100)', 'warning');
                    $('#reinsurerShare').focus();
                    return;
                }

                if (isNaN(reinsurerCommission) || reinsurerCommission < 0 ||
                    reinsurerCommission > 50) {
                    showAlert('Please enter a valid commission percentage (0 - 50)', 'warning');
                    $('#reinsurerCommission').focus();
                    return;
                }

                // Check if reinsurer already selected
                if (selectedReinsurers.has(selectedOption.val())) {
                    showAlert('This reinsurer has already been selected', 'warning');
                    return;
                }

                // Get reinsurer data
                const reinsurerData = {
                    id: selectedOption.val(),
                    name: selectedOption.data('name'),
                    rating: selectedOption.data('rating'),
                    capacity: selectedOption.data('capacity'),
                    country: selectedOption.data('country'),
                    share: reinsurerShare,
                    commission: reinsurerCommission
                };

                // Calculate premium amount (you might need to adjust this calculation based on your business logic)
                const totalPremium =
                    getTotalPremium(); // You'll need to implement this function
                const premiumAmount = (totalPremium * reinsurerShare / 100);

                // Add row to table
                const rowHtml = `
            <tr data-reinsurer-id="${reinsurerData.id}">
                <td>
                    <div class="d-flex align-items-center">
                        <div>
                            <div class="fw-medium">${reinsurerData.name}</div>
                            <small class="text-muted">${reinsurerData.country}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-info">${reinsurerData.rating}</span>
                </td>
                <td class="text-end">${reinsurerData.share.toFixed(2)}%</td>
                <td class="text-end">${reinsurerData.commission.toFixed(2)}%</td>
                <td class="text-end">$${premiumAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-reinsurer"
                            data-reinsurer-id="${reinsurerData.id}"
                            title="Remove Reinsurer">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;

                // Add row to DataTable
                table.row.add($(rowHtml)).draw();

                // Add to selected reinsurers set
                selectedReinsurers.add(reinsurerData.id);

                // Update reinsurer count
                updateReinsurerCount();

                // Reset form
                resetForm();

                // Show success message
                showAlert(`${reinsurerData.name} has been added successfully`, 'success');

                // Update total shares display
                updateTotalShares();
            });

            // Remove reinsurer functionality
            $(document).on('click', '.remove-reinsurer', function() {
                const reinsurerID = $(this).data('reinsurer-id');
                const row = $(this).closest('tr');
                const reinsurerName = row.find('td:first .fw-medium').text();

                // Confirm deletion
                if (confirm(`Are you sure you want to remove ${reinsurerName}?`)) {
                    // Remove from DataTable
                    table.row(row).remove().draw();

                    // Remove from selected reinsurers set
                    selectedReinsurers.delete(reinsurerID.toString());

                    // Update reinsurer count
                    updateReinsurerCount();

                    // Update total shares display
                    updateTotalShares();

                    // Show success message
                    showAlert(`${reinsurerName} has been removed`, 'info');
                }
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
                // Create alert element
                const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

                // Find or create alert container
                let alertContainer = $('.alert-container');
                if (alertContainer.length === 0) {
                    alertContainer = $('<div class="alert-container mb-3"></div>');
                    $('.reinsurer-selection-panel').before(alertContainer);
                }

                // Add alert
                alertContainer.html(alertHtml);

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    alertContainer.find('.alert').fadeOut();
                }, 5000);
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
                    showAlert(`Warning: Total shares (${totalShares.toFixed(2)}%) exceed 100%`,
                        'warning');
                }
            }

            function updateTotalSharesDisplay(totalShares) {
                // Find or create total shares display
                let totalSharesDisplay = $('.total-shares-display');
                if (totalSharesDisplay.length === 0) {
                    const displayHtml = `
                <div class="total-shares-display mt-2">
                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                        <span class="fw-medium">Total Shares:</span>
                        <span class="badge bg-primary total-shares-value">0.00%</span>
                    </div>
                </div>
            `;
                    $('.selected-reinsurers-section').append(displayHtml);
                    totalSharesDisplay = $('.total-shares-display');
                }

                // Update the value
                const badgeClass = totalShares > 100 ? 'bg-danger' : totalShares === 100 ?
                    'bg-success' : 'bg-primary';
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
                    showAlert(`Maximum available share is ${remainingCapacity.toFixed(2)}%`,
                        'warning');
                    $(this).val(remainingCapacity.toFixed(2));
                }
            });
        });
    </script>
@endpush
