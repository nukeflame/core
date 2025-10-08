{{-- <!-- Proposal Stage Modal -->
<div id="finalStageModal" class="modal fade effect-scale md-wrapper" tabindex="-1" data-bs-backdrop="static"
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
                                                placeholder="25.00" onkeyup="this.value=numberWithCommas(this.value)"
                                                change="this.value=numberWithCommas(this.value)" readonly
                                                max="50" value="25">
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
                                                <th style="width: 15%">Written Share (%)</th>
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
                    </div> -
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

<!-- Final Stage Modal -->
<div id="finalModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">✅ Final Stage - Policy Administration</h3>
            <span class="close" onclick="closeModal('finalModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Final Status</label>
                <select class="form-control">
                    <option>Policy Issued & Active</option>
                    <option>Policy Cancelled</option>
                    <option>Policy Expired</option>
                    <option>Under Claims Review</option>
                    <option>Renewal Processing</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Policy Inception</label>
                    <input type="date" class="form-control" value="2025-09-17" />
                </div>
                <div class="form-group">
                    <label class="form-label">Policy Expiry</label>
                    <input type="date" class="form-control" value="2026-09-16" />
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Account Manager</label>
                <select class="form-control">
                    <option>Alice Wilson - Senior Account Manager</option>
                    <option>Robert Taylor - Key Accounts</option>
                    <option>Emma Davis - Commercial Lines</option>
                    <option>James Miller - Strategic Accounts</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Claims History</label>
                <textarea class="form-control" rows="2" placeholder="Record any claims made during policy period..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Renewal Status</label>
                <select class="form-control">
                    <option>Not Due</option>
                    <option>Renewal Notification Sent</option>
                    <option>Under Review</option>
                    <option>Renewed</option>
                    <option>Non-Renewal</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Client Satisfaction Score</label>
                <select class="form-control">
                    <option>5 - Excellent</option>
                    <option>4 - Very Good</option>
                    <option>3 - Good</option>
                    <option>2 - Fair</option>
                    <option>1 - Poor</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Final Notes</label>
                <textarea class="form-control" rows="3"
                    placeholder="Overall deal summary, client relationship notes, and recommendations for future business..."></textarea>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeModal('finalModal')">
                Cancel
            </button>
            <button class="btn btn-primary" onclick="updateStage('final')">
                Complete Final Stage
            </button>
        </div>
    </div>
</div> --}}
