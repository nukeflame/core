<div class="modal fade" id="adjustCommissionModal" tabindex="-1" aria-labelledby="adjustCommissionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title fw-semibold" id="adjustCommissionModalLabel">
                    <i class="fas fa-sliders-h me-2"></i>Adjust Commission
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjustCommissionForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="treaty_id" id="ac_treaty_id" value="{{ $treaty->id ?? '' }}">
                <input type="hidden" name="transaction_id" id="ac_transaction_id" value="{{ $transaction->id ?? '' }}">
                {{-- Hidden fields to store current values for calculations --}}
                <input type="hidden" id="ac_gross_premium_value" value="0">
                <input type="hidden" id="ac_current_rate_value" value="0">
                <input type="hidden" id="ac_current_commission_value" value="0">
                <input type="hidden" id="ac_current_brokerage_value" value="0">
                <input type="hidden" id="ac_current_brokerage_rate_value" value="0">

                <div class="modal-body p-4">
                    {{-- Treaty Info Summary --}}
                    <div class="alert alert-light border mb-4">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <small class="text-muted d-block">Treaty</small>
                                <strong id="ac_treaty_name">{{ $treaty->treaty_type ?? 'SURPLUS' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Insured</small>
                                <strong id="ac_insured_name">{{ $insured->insured_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Currency</small>
                                <strong id="ac_currency">{{ $transaction->currency ?? 'KES' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Reference</small>
                                <strong id="ac_reference">{{ $transaction->reference ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Current Commission Info --}}
                    <div class="card bg-light border mb-4">
                        <div class="card-header bg-secondary text-white py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Current Commission Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Gross Premium</small>
                                    <strong id="ac_current_gross_premium">KES 0.00</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Current Commission Rate</small>
                                    <strong id="ac_current_rate">0.00%</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Current Commission Amount</small>
                                    <strong id="ac_current_commission">KES 0.00</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Current Brokerage</small>
                                    <strong id="ac_current_brokerage">KES 0.00</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Adjustment Type --}}
                        <div class="col-md-6">
                            <label for="ac_adjustment_type" class="form-label fw-medium">
                                Adjustment Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="ac_adjustment_type" name="adjustment_type" required>
                                <option value="">Select Type</option>
                                <option value="rate_change">Rate Change</option>
                                <option value="amount_adjustment">Amount Adjustment</option>
                                <option value="override">Full Override</option>
                                <option value="correction">Error Correction</option>
                            </select>
                            <div class="invalid-feedback">Please select adjustment type</div>
                        </div>

                        <div class="col-md-6">
                            <label for="ac_adjustment_reason" class="form-label fw-medium">
                                Adjustment Reason <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="ac_adjustment_reason" name="adjustment_reason" required>
                                <option value="">Select Reason</option>
                                <option value="rate_negotiation">Rate Negotiation</option>
                                <option value="treaty_amendment">Treaty Amendment</option>
                                <option value="calculation_error">Calculation Error</option>
                                <option value="premium_adjustment">Premium Adjustment</option>
                                <option value="retroactive_change">Retroactive Change</option>
                                <option value="management_override">Management Override</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback">Please select adjustment reason</div>
                        </div>

                        {{-- Commission Adjustment Section --}}
                        <div class="col-12 mt-3">
                            <h6 class="text-warning border-bottom pb-2 mb-3">
                                <i class="fas fa-percentage me-2"></i>Commission Adjustment
                            </h6>
                        </div>

                        {{-- Rate Change Fields --}}
                        <div class="col-md-6 rate-change-field">
                            <label for="ac_new_commission_rate" class="form-label fw-medium">
                                New Commission Rate (%)
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control text-end" id="ac_new_commission_rate"
                                    name="new_commission_rate" step="0.01" min="0" max="100"
                                    placeholder="0.00">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                            <small class="text-muted">Leave blank to keep current rate</small>
                        </div>

                        <div class="col-md-6 rate-change-field">
                            <label for="ac_new_brokerage_rate" class="form-label fw-medium">
                                New Brokerage Rate (%)
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control text-end" id="ac_new_brokerage_rate"
                                    name="new_brokerage_rate" step="0.01" min="0" max="100"
                                    placeholder="0.00">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                            <small class="text-muted">Leave blank to keep current rate</small>
                        </div>

                        {{-- Amount Adjustment Fields (hidden by default) --}}
                        <div class="col-md-6 amount-adjustment-field" style="display: none;">
                            <label for="ac_commission_adjustment_amount" class="form-label fw-medium">
                                Commission Adjustment Amount
                            </label>
                            <div class="input-group">
                                <select class="input-group-text bg-light" id="ac_commission_sign"
                                    name="commission_adjustment_sign" style="width: auto;">
                                    <option value="+">+ Add</option>
                                    <option value="-">- Deduct</option>
                                </select>
                                <span class="input-group-text bg-light ac-currency-label">KES</span>
                                <input type="number" class="form-control text-end"
                                    id="ac_commission_adjustment_amount" name="commission_adjustment_amount"
                                    step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-6 amount-adjustment-field" style="display: none;">
                            <label for="ac_brokerage_adjustment_amount" class="form-label fw-medium">
                                Brokerage Adjustment Amount
                            </label>
                            <div class="input-group">
                                <select class="input-group-text bg-light" id="ac_brokerage_sign"
                                    name="brokerage_adjustment_sign" style="width: auto;">
                                    <option value="+">+ Add</option>
                                    <option value="-">- Deduct</option>
                                </select>
                                <span class="input-group-text bg-light ac-currency-label">KES</span>
                                <input type="number" class="form-control text-end"
                                    id="ac_brokerage_adjustment_amount" name="brokerage_adjustment_amount"
                                    step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>

                        {{-- New Values Preview --}}
                        <div class="col-12">
                            <div class="card bg-warning bg-opacity-10 border-warning mt-3">
                                <div class="card-header bg-warning text-dark py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-eye me-2"></i>New Values After Adjustment
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">New Commission Rate</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control text-end bg-light"
                                                    id="ac_preview_new_rate" readonly value="0.00">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">New Commission Amount</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text ac-currency-label">KES</span>
                                                <input type="text" class="form-control text-end bg-light"
                                                    id="ac_preview_new_commission" readonly value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">New Brokerage Rate</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control text-end bg-light"
                                                    id="ac_preview_new_brokerage_rate" readonly value="0.00">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">New Brokerage Amount</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text ac-currency-label">KES</span>
                                                <input type="text" class="form-control text-end bg-light"
                                                    id="ac_preview_new_brokerage" readonly value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Commission Difference</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text ac-currency-label">KES</span>
                                                <input type="text" class="form-control text-end bg-light"
                                                    id="ac_preview_commission_diff" readonly value="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small text-muted">Brokerage Difference</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text ac-currency-label">KES</span>
                                                <input type="text" class="form-control text-end bg-light"
                                                    id="ac_preview_brokerage_diff" readonly value="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Effective Date & Approval Reference --}}
                        <div class="col-md-6 mt-3">
                            <label for="ac_effective_date" class="form-label fw-medium">
                                Effective Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="ac_effective_date" name="effective_date"
                                required value="{{ date('Y-m-d') }}">
                            <div class="invalid-feedback">Please select effective date</div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="ac_approval_reference" class="form-label fw-medium">Approval Reference</label>
                            <input type="text" class="form-control" id="ac_approval_reference"
                                name="approval_reference" placeholder="Enter approval reference number">
                        </div>

                        {{-- Justification --}}
                        <div class="col-12 mt-3">
                            <label for="ac_justification" class="form-label fw-medium">
                                Justification <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="ac_justification" name="justification" rows="3" required minlength="20"
                                placeholder="Provide detailed justification for this adjustment..."></textarea>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Minimum 20 characters required for audit purposes</small>
                                <small class="text-muted"><span id="ac_char_count" class="text-danger">0</span>/20
                                    characters</small>
                            </div>
                            <div class="invalid-feedback">Please provide justification (min 20 characters)</div>
                        </div>

                        {{-- Supporting Document Upload --}}
                        <div class="col-12 mt-3">
                            <label for="ac_supporting_doc" class="form-label fw-medium">Supporting Document</label>
                            <input type="file" class="form-control" id="ac_supporting_doc"
                                name="supporting_document" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png">
                            <small class="text-muted">Optional: Upload approval letter or supporting documentation
                                (PDF, DOC, XLS, or image - max 5MB)</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-outline-info" id="ac_preview_btn">
                        <i class="fas fa-eye me-1"></i>Preview Changes
                    </button>
                    <button type="submit" class="btn btn-warning" id="ac_submit_btn">
                        <i class="fas fa-check me-1"></i>Apply Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            // Cache DOM elements
            const $modal = $('#adjustCommissionModal');
            const $form = $('#adjustCommissionForm');
            const $submitBtn = $('#ac_submit_btn');
            const $adjustmentType = $('#ac_adjustment_type');
            const $justification = $('#ac_justification');
            const $charCount = $('#ac_char_count');

            // Rate fields
            const $newCommissionRate = $('#ac_new_commission_rate');
            const $newBrokerageRate = $('#ac_new_brokerage_rate');

            // Amount fields
            const $commissionAdjustmentAmount = $('#ac_commission_adjustment_amount');
            const $brokerageAdjustmentAmount = $('#ac_brokerage_adjustment_amount');
            const $commissionSign = $('#ac_commission_sign');
            const $brokerageSign = $('#ac_brokerage_sign');

            // Preview fields
            const $previewNewRate = $('#ac_preview_new_rate');
            const $previewNewCommission = $('#ac_preview_new_commission');
            const $previewNewBrokerageRate = $('#ac_preview_new_brokerage_rate');
            const $previewNewBrokerage = $('#ac_preview_new_brokerage');
            const $previewCommissionDiff = $('#ac_preview_commission_diff');
            const $previewBrokerageDiff = $('#ac_preview_brokerage_diff');

            // Format number for display
            function formatNumber(num) {
                return parseFloat(num).toLocaleString('en-KE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Toggle adjustment fields based on type
            function toggleAdjustmentFields() {
                const adjustmentType = $adjustmentType.val();
                const $rateFields = $('.rate-change-field');
                const $amountFields = $('.amount-adjustment-field');

                if (adjustmentType === 'rate_change' || adjustmentType === 'override') {
                    $rateFields.show();
                    $amountFields.hide();
                } else if (adjustmentType === 'amount_adjustment' || adjustmentType === 'correction') {
                    $rateFields.hide();
                    $amountFields.show();
                } else {
                    $rateFields.show();
                    $amountFields.hide();
                }

                calculatePreview();
            }

            // Calculate and update preview
            function calculatePreview() {
                const grossPremium = parseFloat($('#ac_gross_premium_value').val()) || 0;
                const currentRate = parseFloat($('#ac_current_rate_value').val()) || 0;
                const currentCommission = parseFloat($('#ac_current_commission_value').val()) || 0;
                const currentBrokerage = parseFloat($('#ac_current_brokerage_value').val()) || 0;
                const currentBrokerageRate = parseFloat($('#ac_current_brokerage_rate_value').val()) || 0;

                const adjustmentType = $adjustmentType.val();
                let newCommissionRate = currentRate;
                let newCommission = currentCommission;
                let newBrokerageRate = currentBrokerageRate;
                let newBrokerage = currentBrokerage;

                if (adjustmentType === 'rate_change' || adjustmentType === 'override') {
                    // Rate-based calculation
                    if ($newCommissionRate.val()) {
                        newCommissionRate = parseFloat($newCommissionRate.val()) || 0;
                        newCommission = (grossPremium * newCommissionRate) / 100;
                    }

                    if ($newBrokerageRate.val()) {
                        newBrokerageRate = parseFloat($newBrokerageRate.val()) || 0;
                        newBrokerage = (grossPremium * newBrokerageRate) / 100;
                    }
                } else if (adjustmentType === 'amount_adjustment' || adjustmentType === 'correction') {
                    // Amount-based calculation
                    const commAdjustment = parseFloat($commissionAdjustmentAmount.val()) || 0;
                    const brokAdjustment = parseFloat($brokerageAdjustmentAmount.val()) || 0;
                    const commSign = $commissionSign.val() === '+' ? 1 : -1;
                    const brokSign = $brokerageSign.val() === '+' ? 1 : -1;

                    newCommission = currentCommission + (commAdjustment * commSign);
                    newBrokerage = currentBrokerage + (brokAdjustment * brokSign);

                    // Calculate implied rate
                    if (grossPremium > 0) {
                        newCommissionRate = (newCommission / grossPremium) * 100;
                        newBrokerageRate = (newBrokerage / grossPremium) * 100;
                    }
                }

                // Update preview fields
                $previewNewRate.val(newCommissionRate.toFixed(2));
                $previewNewCommission.val(formatNumber(newCommission));
                $previewNewBrokerageRate.val(newBrokerageRate.toFixed(2));
                $previewNewBrokerage.val(formatNumber(newBrokerage));

                // Calculate differences
                const commDiff = newCommission - currentCommission;
                const brokDiff = newBrokerage - currentBrokerage;

                $previewCommissionDiff.val(formatNumber(commDiff));
                $previewBrokerageDiff.val(formatNumber(brokDiff));

                // Style differences
                $previewCommissionDiff.css('color', commDiff >= 0 ? 'green' : 'red');
                $previewBrokerageDiff.css('color', brokDiff >= 0 ? 'green' : 'red');
            }

            // Character count for justification
            function updateCharCount() {
                const count = $justification.val().length;
                $charCount.text(count);
                $charCount.css('color', count >= 20 ? 'green' : 'red');
            }

            // Event listeners
            $adjustmentType.on('change', toggleAdjustmentFields);
            $newCommissionRate.on('input change', calculatePreview);
            $newBrokerageRate.on('input change', calculatePreview);
            $commissionAdjustmentAmount.on('input change', calculatePreview);
            $brokerageAdjustmentAmount.on('input change', calculatePreview);
            $commissionSign.on('change', calculatePreview);
            $brokerageSign.on('change', calculatePreview);
            $justification.on('input', updateCharCount);

            // Preview button
            $('#ac_preview_btn').on('click', function() {
                calculatePreview();

                const commDiff = $previewCommissionDiff.val();
                const brokDiff = $previewBrokerageDiff.val();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Adjustment Preview',
                        html: `
                        <div class="text-start">
                            <p><strong>New Commission Rate:</strong> ${$previewNewRate.val()}%</p>
                            <p><strong>New Commission Amount:</strong> KES ${$previewNewCommission.val()}</p>
                            <p><strong>Commission Change:</strong> KES ${commDiff}</p>
                            <hr>
                            <p><strong>New Brokerage Rate:</strong> ${$previewNewBrokerageRate.val()}%</p>
                            <p><strong>New Brokerage Amount:</strong> KES ${$previewNewBrokerage.val()}</p>
                            <p><strong>Brokerage Change:</strong> KES ${brokDiff}</p>
                        </div>
                    `,
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert(
                        `Commission Change: KES ${commDiff}\nBrokerage Change: KES ${brokDiff}`
                    );
                }
            });

            // Reset form when modal closes
            $modal.on('hidden.bs.modal', function() {
                $form[0].reset();
                toggleAdjustmentFields();
                calculatePreview();
                updateCharCount();
                $form.find('.is-invalid').removeClass('is-invalid');
            });

            // Form submission with jQuery AJAX
            $form.on('submit', function(e) {
                e.preventDefault();

                // Remove previous validation states
                $form.find('.is-invalid').removeClass('is-invalid');

                // Validate required fields
                let isValid = true;
                $form.find('[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    }
                });

                // Validate justification length
                if ($justification.val().length < 20) {
                    $justification.addClass('is-invalid');
                    isValid = false;
                }

                if (!isValid) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Validation Error', 'Please fill in all required fields correctly.',
                            'error');
                    } else {
                        alert('Please fill in all required fields correctly.');
                    }
                    return;
                }

                // Validate file size
                const fileInput = document.getElementById('ac_supporting_doc');
                if (fileInput.files.length > 0) {
                    const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
                    if (fileSize > 5) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Validation Error', 'File size must be less than 5MB.', 'error');
                        } else {
                            alert('File size must be less than 5MB.');
                        }
                        return;
                    }
                }

                // Show loading state
                const originalBtnText = $submitBtn.html();
                $submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i>Applying...');

                // Create FormData for file upload
                const formData = new FormData($form[0]);

                // AJAX request
                $.ajax({
                    url: "{{ route('treaty.commission.adjust') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $modal.modal('hide');

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message ||
                                    'Commission adjustment applied successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            alert(response.message ||
                                'Commission adjustment applied successfully!');
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr.responseJSON);

                        let errorMessage = 'An error occurred while saving.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                errorMessage = Object.values(errors).flat().join('\n');

                                $.each(errors, function(field, messages) {
                                    $form.find('[name="' + field + '"]').addClass(
                                        'is-invalid');
                                });
                            }
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', errorMessage, 'error');
                        } else {
                            alert('Error: ' + errorMessage);
                        }
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });
        });

        // Function to load commission data into modal
        function loadAdjustCommissionModal(data) {
            if (!data) return;

            // Set hidden fields
            if (data.treaty_id) $('#ac_treaty_id').val(data.treaty_id);
            if (data.transaction_id) $('#ac_transaction_id').val(data.transaction_id);

            // Set display fields
            if (data.treaty_name) $('#ac_treaty_name').text(data.treaty_name);
            if (data.insured_name) $('#ac_insured_name').text(data.insured_name);
            if (data.currency) {
                $('#ac_currency').text(data.currency);
                $('.ac-currency-label').text(data.currency);
            }
            if (data.reference) $('#ac_reference').text(data.reference);

            // Format number helper
            function formatNum(num) {
                return parseFloat(num || 0).toLocaleString('en-KE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Set current values for calculations
            const grossPremium = data.gross_premium || 0;
            const commissionRate = data.commission_rate || 0;
            const commissionAmount = data.commission_amount || 0;
            const brokerageAmount = data.brokerage_amount || 0;
            const brokerageRate = data.brokerage_rate || 0;

            $('#ac_current_gross_premium').text(`KES ${formatNum(grossPremium)}`);
            $('#ac_gross_premium_value').val(grossPremium);

            $('#ac_current_rate').text(`${parseFloat(commissionRate).toFixed(2)}%`);
            $('#ac_current_rate_value').val(commissionRate);

            $('#ac_current_commission').text(`KES ${formatNum(commissionAmount)}`);
            $('#ac_current_commission_value').val(commissionAmount);

            $('#ac_current_brokerage').text(`KES ${formatNum(brokerageAmount)}`);
            $('#ac_current_brokerage_value').val(brokerageAmount);
            $('#ac_current_brokerage_rate_value').val(brokerageRate);

            // Initialize preview
            $('#ac_preview_new_rate').val(parseFloat(commissionRate).toFixed(2));
            $('#ac_preview_new_commission').val(formatNum(commissionAmount));
            $('#ac_preview_new_brokerage_rate').val(parseFloat(brokerageRate).toFixed(2));
            $('#ac_preview_new_brokerage').val(formatNum(brokerageAmount));
            $('#ac_preview_commission_diff').val('0.00');
            $('#ac_preview_brokerage_diff').val('0.00');
        }
    </script>
@endpush
