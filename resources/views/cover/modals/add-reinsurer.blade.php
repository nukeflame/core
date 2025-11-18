<div class="modal fade" id="addReinsurerModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="addReinsurerForm" method="POST">
                @csrf
                <input type="hidden" name="cover_id" value="{{ $cover->id }}">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Add Reinsurer Participation</h5>
                        <p class="text-muted small mb-0">{{ $cover->cover_no }} - {{ $cover->treaty->treaty_name }}</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Step Indicator --}}
                    <div class="steps-indicator mb-4">
                        <div class="step active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-label">Reinsurer Selection</div>
                        </div>
                        <div class="step" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-label">Share & Terms</div>
                        </div>
                        <div class="step" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-label">Financial Details</div>
                        </div>
                        <div class="step" data-step="4">
                            <div class="step-number">4</div>
                            <div class="step-label">Review & Confirm</div>
                        </div>
                    </div>

                    {{-- Step 1: Reinsurer Selection --}}
                    <div class="step-content active" data-step="1">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label required">Select Reinsurer</label>
                                    <select class="form-select select2" name="reinsurer_id" id="reinsurerSelect"
                                        required>
                                        <option value="">-- Choose Reinsurer --</option>
                                        {{-- @foreach ($availableReinsurers as $reinsurer)
                                            <option value="{{ $reinsurer->customer_id }}"
                                                data-country="{{ $reinsurer->country }}"
                                                data-rating="{{ $reinsurer->credit_rating }}"
                                                data-email="{{ $reinsurer->email }}">
                                                {{ $reinsurer->name }}
                                            </option>
                                        @endforeach --}}
                                    </select>
                                    <small class="form-text text-muted">
                                        Can't find the reinsurer?
                                        <a href="{{ route('customers.create', ['type' => 'reinsurer']) }}"
                                            target="_blank">Add new reinsurer</a>
                                    </small>
                                </div>

                                {{-- Reinsurer Info Card (appears after selection) --}}
                                <div id="reinsurerInfoCard" class="card border-0 bg-light" style="display: none;">
                                    <div class="card-body">
                                        <h6 class="mb-3">Reinsurer Information</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted">Country</small>
                                                <div class="fw-semibold" id="reinsurerCountry">-</div>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Credit Rating</small>
                                                <div class="fw-semibold" id="reinsurerRating">-</div>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Contact</small>
                                                <div class="fw-semibold" id="reinsurerEmail">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-0 bg-primary-subtle">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3">
                                            <i class="ri-information-line me-2"></i>Available Capacity
                                        </h6>
                                        <div class="mb-3">
                                            <small class="text-muted">Treaty Capacity</small>
                                            <div class="h5 mb-0">{{ number_format($cover->treaty_capacity, 2) }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">Already Placed</small>
                                            <div class="h5 mb-0">
                                                {{ number_format($cover->participations->sum('signed_line'), 2) }}%
                                            </div>
                                        </div>
                                        <div>
                                            <small class="text-muted">Remaining to Place</small>
                                            <div class="h5 mb-0 text-success">
                                                {{ number_format(100 - $cover->participations->sum('signed_line'), 2) }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Share & Terms --}}
                    <div class="step-content" data-step="2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">
                                        Order Hereon
                                        <i class="ri-information-line text-muted" data-bs-toggle="tooltip"
                                            title="Priority order of reinsurer participation"></i>
                                    </label>
                                    <input type="number" class="form-control" name="order_hereon" min="1"
                                        value="{{ $cover->participations->max('order_hereon') + 1 }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Payment Terms</label>
                                    <select class="form-select" name="payment_terms">
                                        <option value="Quarterly in Arrears">Quarterly in Arrears</option>
                                        <option value="Annually in Advance">Annually in Advance</option>
                                        <option value="Semi-Annually">Semi-Annually</option>
                                        <option value="Upon Receipt">Upon Receipt</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">
                                        Written Line (%)
                                        <i class="ri-information-line text-muted" data-bs-toggle="tooltip"
                                            title="Initial offer percentage"></i>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="written_line"
                                            id="writtenLine" step="0.0001" min="0" max="100" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label required">
                                        Signed Line (%)
                                        <i class="ri-information-line text-muted" data-bs-toggle="tooltip"
                                            title="Accepted/confirmed percentage"></i>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="signed_line"
                                            id="signedLine" step="0.0001" min="0" max="100" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text" id="shareValidation"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Real-time Calculation Display --}}
                        <div class="card border-0 bg-light mt-3">
                            <div class="card-body">
                                <h6 class="mb-3">Calculated Share</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Share of Premium</small>
                                        <div class="h5 mb-0" id="calculatedPremium">0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Share of Capacity</small>
                                        <div class="h5 mb-0" id="calculatedCapacity">0.00</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Updated Total Placed</small>
                                        <div class="h5 mb-0" id="updatedTotalPlaced">
                                            {{ number_format($cover->participations->sum('signed_line'), 2) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Financial Details --}}
                    <div class="step-content" data-step="3">
                        <div class="row">
                            {{-- Commission Section --}}
                            <div class="col-md-12 mb-4">
                                <h6 class="mb-3">
                                    <i class="ri-money-dollar-circle-line me-2"></i>Commission Structure
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label required">Commission Rate (%)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="commission_rate"
                                                    id="commissionRate" step="0.0001" min="0" max="100"
                                                    value="{{ $cover->base_commission_rate }}" required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Commission Amount</label>
                                            <input type="text" class="form-control" id="commissionAmount"
                                                value="0.00" readonly>
                                            <small class="form-text text-muted">Auto-calculated based on rate</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Brokerage Section --}}
                            <div class="col-md-12 mb-4">
                                <h6 class="mb-3">
                                    <i class="ri-briefcase-line me-2"></i>Brokerage
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Brokerage Rate (%)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="brokerage_rate"
                                                    id="brokerageRate" step="0.0001" min="0" max="100"
                                                    value="0">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Brokerage Amount</label>
                                            <input type="text" class="form-control" id="brokerageAmount"
                                                value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Taxes & Deductions Section --}}
                            <div class="col-md-12">
                                <h6 class="mb-3">
                                    <i class="ri-file-list-3-line me-2"></i>Taxes & Deductions
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">WHT Rate (%)</label>
                                            <select class="form-select" name="wht_rate" id="whtRate">
                                                <option value="0">0% - None</option>
                                                <option value="5">5% - Resident</option>
                                                <option value="10">10% - Non-Resident (Treaty)</option>
                                                <option value="15">15% - Non-Resident</option>
                                                <option value="20">20% - Non-Treaty</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">WHT Amount</label>
                                            <input type="text" class="form-control" id="whtAmount" value="0.00"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" name="apply_fronting_fee"
                                                id="applyFrontingFee">
                                            <label class="form-check-label" for="applyFrontingFee">
                                                Apply Fronting Fee
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div id="frontingFeeSection" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Fronting Fee Rate (%)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control"
                                                        name="fronting_fee_rate" id="frontingFeeRate" step="0.01"
                                                        min="0" max="10" value="0">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Fronting Fee Amount</label>
                                                <input type="text" class="form-control" id="frontingFeeAmount"
                                                    value="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Financial Summary --}}
                        <div class="card border-0 bg-light mt-4">
                            <div class="card-body">
                                <h6 class="mb-3">Financial Summary</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Premium Share:</td>
                                            <td class="text-end fw-semibold" id="summaryPremium">0.00</td>
                                        </tr>
                                        <tr>
                                            <td>Commission:</td>
                                            <td class="text-end fw-semibold" id="summaryCommission">0.00</td>
                                        </tr>
                                        <tr>
                                            <td>Brokerage:</td>
                                            <td class="text-end" id="summaryBrokerage">0.00</td>
                                        </tr>
                                        <tr>
                                            <td>WHT:</td>
                                            <td class="text-end text-danger" id="summaryWHT">0.00</td>
                                        </tr>
                                        <tr id="summaryFrontingRow" style="display: none;">
                                            <td>Fronting Fee:</td>
                                            <td class="text-end text-danger" id="summaryFronting">0.00</td>
                                        </tr>
                                        <tr class="table-light">
                                            <td><strong>Net Amount Due:</strong></td>
                                            <td class="text-end"><strong id="summaryNetAmount">0.00</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Step 4: Review & Confirm --}}
                    <div class="step-content" data-step="4">
                        <div class="alert alert-info mb-4">
                            <i class="ri-information-line me-2"></i>
                            Please review all details before submitting. You can edit this information later if needed.
                        </div>

                        <div class="card border-0">
                            <div class="card-body">
                                <h6 class="mb-4">Participation Summary</h6>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Reinsurer Details</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="150">Reinsurer:</td>
                                                <td class="fw-semibold" id="reviewReinsurer">-</td>
                                            </tr>
                                            <tr>
                                                <td>Country:</td>
                                                <td id="reviewCountry">-</td>
                                            </tr>
                                            <tr>
                                                <td>Order Hereon:</td>
                                                <td id="reviewOrder">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">Share Details</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="150">Written Line:</td>
                                                <td class="fw-semibold" id="reviewWrittenLine">-</td>
                                            </tr>
                                            <tr>
                                                <td>Signed Line:</td>
                                                <td class="fw-semibold" id="reviewSignedLine">-</td>
                                            </tr>
                                            <tr>
                                                <td>Payment Terms:</td>
                                                <td id="reviewPaymentTerms">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <h6 class="text-muted mb-3">Financial Breakdown</h6>
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td width="250">Share of Premium:</td>
                                            <td class="text-end fw-semibold" id="reviewPremiumShare">-</td>
                                        </tr>
                                        <tr>
                                            <td>Commission (<span id="reviewCommRate">-</span>%):</td>
                                            <td class="text-end" id="reviewCommAmount">-</td>
                                        </tr>
                                        <tr>
                                            <td>Brokerage (<span id="reviewBrokRate">-</span>%):</td>
                                            <td class="text-end" id="reviewBrokAmount">-</td>
                                        </tr>
                                        <tr>
                                            <td>WHT (<span id="reviewWHTRate">-</span>%):</td>
                                            <td class="text-end text-danger" id="reviewWHTAmount">-</td>
                                        </tr>
                                        <tr id="reviewFrontingRow" style="display: none;">
                                            <td>Fronting Fee (<span id="reviewFrontRate">-</span>%):</td>
                                            <td class="text-end text-danger" id="reviewFrontAmount">-</td>
                                        </tr>
                                        <tr class="table-light">
                                            <td><strong>Net Amount Due to Reinsurer:</strong></td>
                                            <td class="text-end"><strong id="reviewNetAmount">-</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevStepBtn" style="display: none;">
                        <i class="ri-arrow-left-line me-2"></i>Previous
                    </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="nextStepBtn">
                        Next<i class="ri-arrow-right-line ms-2"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                        <i class="ri-check-line me-2"></i>Add Reinsurer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
