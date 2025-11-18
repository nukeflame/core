@props(['cover', 'reinsurers', 'whtRates' => [], 'paymethods' => []])

<div class="modal effect-scale md-wrapper reinsurer-wrapper-modal" id="addReinsurerModal" data-bs-backdrop="static"
    tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="addReinsurerForm" method="POST">
                @csrf
                <input type="hidden" name="cover_id" value="{{ $cover->id }}">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Add Reinsurer Participation</h5>
                        <p class="fw-bold small mb-0 mt-1 text-white">{{ $cover->cover_no }} - FIRE - SURPLUS- TREATY
                            {{-- {{ $cover->treaty->treaty_name }} --}}
                        </p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
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
                                    <select class="form-sel" name="reinsurer_id" id="reinsurerSelect" required>
                                        <option value="">-- Choose Reinsurer --</option>
                                        @foreach ($reinsurers as $reinsurer)
                                            <option value="{{ $reinsurer->customer_id }}" data-country="Kenya"
                                                data-rating="1.0" data-email="reinsurance@company.com">
                                                {{ $reinsurer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Can't find the reinsurer?
                                        <a href="/" target="_blank">
                                            Add new reinsurer
                                        </a>
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
                                                20%
                                                {{-- {{ number_format($cover->participations->sum('signed_line'), 2) }}% --}}
                                            </div>
                                        </div>
                                        <div>
                                            <small class="text-muted">Remaining to Place</small>
                                            <div class="h5 mb-0 text-success">
                                                80%
                                                {{-- {{ number_format(100 - $cover->participations->sum('signed_line'), 2) }}% --}}
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
                                        <span class="me-2">Order Hereon</span>
                                        <i class="ri-information-line text-muted" data-bs-toggle="tooltip"
                                            title="Priority order of reinsurer participation"></i>
                                    </label>
                                    <input type="number" class="form-control" name="order_hereon" min="1"
                                        value="1" {{-- value="{{ $cover->participations->max('order_hereon') + 1 }}" --}} required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Payment Terms</label>
                                    <select class="form-select" name="payment_terms">
                                        {{-- @if (!empty($paymethods))
                                            @foreach ($paymethods as $method)
                                                <option value="{{ $method->pay_method_code }}">{{ $method-> }}</option>
                                            @endforeach
                                        @else --}}
                                        <option value="Quarterly in Arrears">Quarterly in Arrears</option>
                                        <option value="Annually in Advance">Annually in Advance</option>
                                        <option value="Semi-Annually">Semi-Annually</option>
                                        <option value="Upon Receipt">Upon Receipt</option>
                                        {{-- @endif --}}
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
                                            {{-- {{ number_format($cover->participations->sum('signed_line'), 2) }}% --}}
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
                                                    value="{{ $cover->base_commission_rate ?? 0 }}" required>
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
                                                {{-- @if (!empty($whtRates))
                                                    @foreach ($whtRates as $rate => $label)
                                                        <option value="{{ $rate }}">{{ $label }}
                                                        </option>
                                                    @endforeach
                                                @else --}}
                                                <option value="0">0% - None</option>
                                                <option value="5">5% - Resident</option>
                                                <option value="10">10% - Non-Resident (Treaty)</option>
                                                <option value="15">15% - Non-Resident</option>
                                                <option value="20">20% - Non-Treaty</option>
                                                {{-- @endif --}}
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

@push('styles')
    <style>
        /* Step Indicator Styles */
        .steps-indicator {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 2rem;
        }

        .steps-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .step-label {
            font-size: 0.875rem;
            color: #6c757d;
            text-align: center;
            transition: color 0.3s ease;
        }

        .step.active .step-number {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
            transform: scale(1.1);
        }

        .step.active .step-label {
            color: #0d6efd;
            font-weight: 600;
        }

        .step.completed .step-number {
            background: #198754;
            border-color: #198754;
            color: #fff;
        }

        .step.completed .step-number::after {
            content: '✓';
            font-size: 1.2rem;
        }

        .step-content {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }

        .step-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .steps-indicator {
                flex-wrap: wrap;
            }

            .step {
                flex-basis: 50%;
                margin-bottom: 1rem;
            }

            .step-label {
                font-size: 0.75rem;
            }

            .step-number {
                width: 35px;
                height: 35px;
                font-size: 0.875rem;
            }
        }

        /* Form styling enhancements */
        .form-label.required::after {
            content: ' *';
            color: #dc3545;
        }

        .card.border-0 {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Loading spinner for submit button */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }
    </style>
@endpush
@push('script')
    <script>
        $(document).ready(function() {
            const $modal = $('#addReinsurerModal');
            if ($modal.length === 0) return;

            let currentStep = 1;
            const totalSteps = 4;

            // Cover data for calculations
            const coverData = {
                totalPremium: {{ $cover->total_premium ?? 0 }},
                capacity: {{ $cover->treaty_capacity ?? 0 }},
                currentPlaced: 0, //{{-- {{ $cover->participations->sum('signed_line') ?? 0 }} --}}
            };

            // Initialize Select2
            $('#reinsurerSelect').select2({
                dropdownParent: $modal,
                placeholder: '-- Choose Reinsurer --',
                allowClear: true
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Step navigation
            function showStep(step) {
                $('.step-content').removeClass('active');
                $('.steps-indicator .step').removeClass('active');

                $(`.step-content[data-step="${step}"]`).addClass('active');
                $(`.steps-indicator .step[data-step="${step}"]`).addClass('active');

                // Update button visibility
                $('#prevStepBtn').toggle(step > 1);
                $('#nextStepBtn').toggle(step < totalSteps);
                $('#submitBtn').toggle(step === totalSteps);

                if (step === 4) {
                    populateReviewStep();
                }
            }

            // Reinsurer selection handler
            $('#reinsurerSelect').on('change', function() {
                const $selected = $(this).find(':selected');

                if ($(this).val()) {
                    $('#reinsurerInfoCard').fadeIn(300);
                    $('#reinsurerCountry').text($selected.data('country') || '-');
                    $('#reinsurerRating').text($selected.data('rating') || '-');
                    $('#reinsurerEmail').text($selected.data('email') || '-');
                } else {
                    $('#reinsurerInfoCard').fadeOut(300);
                }
            });

            // Real-time calculations
            function calculateFinancials() {
                const signedLine = parseFloat($('#signedLine').val() || 0);
                const commissionRate = parseFloat($('#commissionRate').val() || 0);
                const brokerageRate = parseFloat($('#brokerageRate').val() || 0);
                const whtRate = parseFloat($('#whtRate').val() || 0);
                const frontingFeeRate = parseFloat($('#frontingFeeRate').val() || 0);
                const applyFronting = $('#applyFrontingFee').is(':checked');

                const premiumShare = (coverData.totalPremium * signedLine) / 100;
                const capacityShare = (coverData.capacity * signedLine) / 100;
                const commission = (premiumShare * commissionRate) / 100;
                const brokerage = (premiumShare * brokerageRate) / 100;
                const wht = (premiumShare * whtRate) / 100;
                const fronting = applyFronting ? (premiumShare * frontingFeeRate) / 100 : 0;
                const netAmount = premiumShare - commission - brokerage - wht - fronting;

                // Update Step 2 calculations
                $('#calculatedPremium').text(premiumShare.toFixed(2));
                $('#calculatedCapacity').text(capacityShare.toFixed(2));
                $('#updatedTotalPlaced').text((coverData.currentPlaced + signedLine).toFixed(2) + '%');

                // Update Step 3 calculations
                $('#commissionAmount').val(commission.toFixed(2));
                $('#brokerageAmount').val(brokerage.toFixed(2));
                $('#whtAmount').val(wht.toFixed(2));
                $('#frontingFeeAmount').val(fronting.toFixed(2));

                // Update financial summary
                $('#summaryPremium').text(premiumShare.toFixed(2));
                $('#summaryCommission').text(commission.toFixed(2));
                $('#summaryBrokerage').text(brokerage.toFixed(2));
                $('#summaryWHT').text(wht.toFixed(2));
                $('#summaryFronting').text(fronting.toFixed(2));
                $('#summaryNetAmount').text(netAmount.toFixed(2));

                $('#summaryFrontingRow').toggle(applyFronting);

                // Validate share doesn't exceed available capacity
                const totalPlaced = coverData.currentPlaced + signedLine;
                const $validation = $('#shareValidation');

                if (totalPlaced > 100) {
                    $validation.text('⚠️ Warning: Total placement exceeds 100%')
                        .removeClass('text-success')
                        .addClass('text-danger');
                } else {
                    $validation.text(`✓ ${(100 - totalPlaced).toFixed(2)}% remaining capacity`)
                        .removeClass('text-danger')
                        .addClass('text-success');
                }
            }

            // Attach calculation listeners
            $('#signedLine, #writtenLine, #commissionRate, #brokerageRate, #whtRate, #frontingFeeRate')
                .on('input', calculateFinancials);

            $('#applyFrontingFee').on('change', function() {
                $('#frontingFeeSection').slideToggle(300);
                calculateFinancials();
            });

            // Populate review step
            function populateReviewStep() {
                const $reinsurerSelect = $('#reinsurerSelect');
                const $selectedOption = $reinsurerSelect.find(':selected');

                $('#reviewReinsurer').text($selectedOption.text());
                $('#reviewCountry').text($selectedOption.data('country') || '-');
                $('#reviewOrder').text($('[name="order_hereon"]').val());
                $('#reviewWrittenLine').text($('#writtenLine').val() + '%');
                $('#reviewSignedLine').text($('#signedLine').val() + '%');
                $('#reviewPaymentTerms').text($('[name="payment_terms"]').val());

                $('#reviewPremiumShare').text($('#calculatedPremium').text());
                $('#reviewCommRate').text($('#commissionRate').val());
                $('#reviewCommAmount').text($('#commissionAmount').val());
                $('#reviewBrokRate').text($('#brokerageRate').val());
                $('#reviewBrokAmount').text($('#brokerageAmount').val());
                $('#reviewWHTRate').text($('#whtRate').val());
                $('#reviewWHTAmount').text($('#whtAmount').val());

                const applyFronting = $('#applyFrontingFee').is(':checked');
                $('#reviewFrontingRow').toggle(applyFronting);

                if (applyFronting) {
                    $('#reviewFrontRate').text($('#frontingFeeRate').val());
                    $('#reviewFrontAmount').text($('#frontingFeeAmount').val());
                }

                $('#reviewNetAmount').text($('#summaryNetAmount').text());
            }

            // Navigation button handlers
            $('#nextStepBtn').on('click', function() {
                if (validateStep(currentStep)) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            $('#prevStepBtn').on('click', function() {
                currentStep--;
                showStep(currentStep);
            });

            // Step validation
            function validateStep(step) {
                if (step === 1) {
                    const reinsurer = $('#reinsurerSelect').val();
                    if (!reinsurer) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validation Error',
                            text: 'Please select a reinsurer',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                }
                if (step === 2) {
                    const writtenLine = $('#writtenLine').val();
                    const signedLine = $('#signedLine').val();

                    if (!writtenLine || !signedLine) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validation Error',
                            text: 'Please enter both written and signed line percentages',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    const totalPlaced = coverData.currentPlaced + parseFloat(signedLine);
                    if (totalPlaced > 100) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Capacity Exceeded',
                            text: 'Total placement exceeds 100%. Do you want to continue?',
                            showCancelButton: true,
                            confirmButtonText: 'Continue',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            return result.isConfirmed;
                        });
                    }
                }
                return true;
            }

            // Form submission with AJAX
            $('#addReinsurerForm').on('submit', function(e) {
                e.preventDefault();

                const $submitBtn = $('#submitBtn');
                const originalText = $submitBtn.html();

                // Disable submit button and show loading state
                $submitBtn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Submitting...');

                // Prepare form data
                const formData = $(this).serialize();

                $.ajax({
                    //{{-- url: '{{ route('cover.participations.store') }}', --}}
                    url: '',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message ||
                                'Reinsurer participation added successfully',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Close modal and reload page or update table
                            $modal.modal('hide');

                            // Option 1: Reload the page
                            window.location.reload();

                            // Option 2: Update DataTable if you're using one
                            // if ($.fn.DataTable.isDataTable('#participationsTable')) {
                            //     $('#participationsTable').DataTable().ajax.reload();
                            // }
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while adding the reinsurer';

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage,
                            confirmButtonText: 'OK'
                        });

                        // Re-enable submit button
                        $submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Reset form when modal is closed
            $modal.on('hidden.bs.modal', function() {
                $('#addReinsurerForm')[0].reset();
                $('#reinsurerSelect').val('').trigger('change');
                $('#reinsurerInfoCard').hide();
                $('#frontingFeeSection').hide();
                currentStep = 1;
                showStep(1);
                calculateFinancials();
            });

            // Initialize
            showStep(1);
            calculateFinancials();
        });
    </script>
@endpush
