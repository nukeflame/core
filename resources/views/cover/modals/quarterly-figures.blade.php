<div class="modal fade effect-scale md-wrapper" id="createQuarterlyFiguresModal" tabindex="-1"
    aria-labelledby="createQuarterlyFiguresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h6 class="modal-title fw-semibold" id="createQuarterlyFiguresModalLabel">
                    <i class="bx bx-calendar-alt me-2"></i>Create Quarterly Figures
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="quarterlyFiguresForm">
                @csrf
                <input type="hidden" name="treaty_id" id="qf_treaty_id" value="{{ $treaty->id ?? '' }}">
                <input type="hidden" name="transaction_id" id="qf_transaction_id" value="{{ $transaction->id ?? '' }}">

                <div class="modal-body p-4">
                    {{-- Treaty Info Summary --}}
                    <div class="alert alert-light border mb-4">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Treaty</small>
                                <strong id="qf_treaty_name">{{ $treaty->treaty_type ?? 'SURPLUS' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Insured</small>
                                <strong id="qf_insured_name">{{ $insured->insured_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Currency</small>
                                <strong id="qf_currency">{{ $transaction->currency ?? 'KES' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Quarter Selection --}}
                        <div class="col-md-6">
                            <label for="qf_quarter" class="form-label fw-medium">
                                Quarter <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="qf_quarter" name="quarter" required>
                                <option value="">Select Quarter</option>
                                <option value="Q1">Q1 (Jan - Mar)</option>
                                <option value="Q2">Q2 (Apr - Jun)</option>
                                <option value="Q3">Q3 (Jul - Sep)</option>
                                <option value="Q4">Q4 (Oct - Dec)</option>
                            </select>
                            <div class="invalid-feedback">Please select a quarter</div>
                        </div>

                        {{-- Year Selection --}}
                        <div class="col-md-6">
                            <label for="qf_year" class="form-label fw-medium">
                                Year <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="qf_year" name="year" required>
                                <option value="">Select Year</option>
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <div class="invalid-feedback">Please select a year</div>
                        </div>

                        {{-- Premium Section --}}
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bx bx-coin me-2"></i>Premium Details
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label for="qf_gross_premium" class="form-label fw-medium">
                                Gross Premium <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light qf-currency-label">KES</span>
                                <input type="number" class="form-control color-blk text-end" id="qf_gross_premium"
                                    name="gross_premium" step="0.01" min="0" required placeholder="0.00">
                            </div>
                            <div class="invalid-feedback">Please enter gross premium</div>
                        </div>

                        <div class="col-md-6">
                            <label for="qf_return_premium" class="form-label fw-medium">Return Premium</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light qf-currency-label">KES</span>
                                <input type="number" class="form-control text-end color-blk" id="qf_return_premium"
                                    name="return_premium" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="qf_net_premium" class="form-label fw-medium">Net Premium</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light qf-currency-label">KES</span>
                                <input type="number" class="form-control text-end bg-light color-blk"
                                    id="qf_net_premium" name="net_premium" step="0.01" readonly placeholder="0.00">
                            </div>
                            <small class="text-muted">Auto-calculated: Gross - Return</small>
                        </div>

                        {{-- Commission Section --}}
                        <div class="col-12 mt-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bx bx-percent me-2"></i>Commission Details
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label for="qf_commission_rate" class="form-label fw-medium">
                                Commission Rate (%)
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control text-end color-blk" id="qf_commission_rate"
                                    name="commission_rate" step="0.01" min="0" max="100"
                                    placeholder="0.00">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="qf_commission_amount" class="form-label fw-medium">Commission Amount</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light qf-currency-label">KES</span>
                                <input type="number" class="form-control text-end bg-light color-blk"
                                    id="qf_commission_amount" name="commission_amount" step="0.01" readonly
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="qf_brokerage_rate" class="form-label fw-medium">Brokerage Rate (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control text-end color-blk" id="qf_brokerage_rate"
                                    name="brokerage_rate" step="0.01" min="0" max="100"
                                    placeholder="0.00">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="qf_brokerage_amount" class="form-label fw-medium">Brokerage Amount</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light qf-currency-label">KES</span>
                                <input type="number" class="form-control text-end bg-light color-blk"
                                    id="qf_brokerage_amount" name="brokerage_amount" step="0.01" readonly
                                    placeholder="0.00">
                            </div>
                        </div>

                        {{-- Claims Section --}}
                        <div class="col-12 mt-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bx bx-file-invoice me-2"></i>Claims Details
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label for="qf_claims_paid" class="form-label fw-medium">Claims Paid</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light qf-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="qf_claims_paid"
                                    name="claims_paid" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="qf_claims_outstanding" class="form-label fw-medium">Claims Outstanding</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light qf-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="qf_claims_outstanding"
                                    name="claims_outstanding" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        {{-- Remarks --}}
                        <div class="col-12 mt-3">
                            <label for="qf_remarks" class="form-label fw-medium">Remarks</label>
                            <textarea class="form-control" id="qf_remarks" name="remarks" rows="2"
                                placeholder="Enter any additional notes or remarks..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-outline-warning" id="qf_reset_btn">
                        <i class="bx bx-redo me-1"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary" id="qf_submit_btn">
                        <i class="bx bx-save me-1"></i>Create Quarterly Figures
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {

            const $modal = $('#createQuarterlyFiguresModal');
            const $form = $('#quarterlyFiguresForm');
            const $grossPremium = $('#qf_gross_premium');
            const $returnPremium = $('#qf_return_premium');
            const $netPremium = $('#qf_net_premium');
            const $commissionRate = $('#qf_commission_rate');
            const $commissionAmount = $('#qf_commission_amount');
            const $brokerageRate = $('#qf_brokerage_rate');
            const $brokerageAmount = $('#qf_brokerage_amount');
            const $submitBtn = $('#qf_submit_btn');

            function calculateNetPremium() {
                const grossPremium = parseFloat($grossPremium.val()) || 0;
                const returnPremium = parseFloat($returnPremium.val()) || 0;
                const netPremium = grossPremium - returnPremium;
                $netPremium.val(netPremium.toFixed(2));
                calculateCommission();
                calculateBrokerage();
            }

            // Calculate Commission Amount
            function calculateCommission() {
                const netPremium = parseFloat($netPremium.val()) || 0;
                const commissionRate = parseFloat($commissionRate.val()) || 0;
                const commissionAmount = (netPremium * commissionRate) / 100;
                $commissionAmount.val(commissionAmount.toFixed(2));
            }

            // Calculate Brokerage Amount
            function calculateBrokerage() {
                const netPremium = parseFloat($netPremium.val()) || 0;
                const brokerageRate = parseFloat($brokerageRate.val()) || 0;
                const brokerageAmount = (netPremium * brokerageRate) / 100;
                $brokerageAmount.val(brokerageAmount.toFixed(2));
            }

            // Event listeners for calculations
            $grossPremium.on('input change', calculateNetPremium);
            $returnPremium.on('input change', calculateNetPremium);
            $commissionRate.on('input change', calculateCommission);
            $brokerageRate.on('input change', calculateBrokerage);

            // Reset button handler
            $('#qf_reset_btn').on('click', function() {
                $form[0].reset();
                $netPremium.val('');
                $commissionAmount.val('');
                $brokerageAmount.val('');
                $form.find('.is-invalid').removeClass('is-invalid');
            });

            // Reset form when modal closes
            $modal.on('hidden.bs.modal', function() {
                $form[0].reset();
                $netPremium.val('');
                $commissionAmount.val('');
                $brokerageAmount.val('');
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

                if (!isValid) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Validation Error', 'Please fill in all required fields.', 'error');
                    } else {
                        alert('Please fill in all required fields.');
                    }
                    return;
                }

                // Show loading state
                const originalBtnText = $submitBtn.html();
                $submitBtn.prop('disabled', true).html(
                    '<i class="bx bx-loader-alt bx-spin me-1"></i>Saving...');

                // AJAX request
                $.ajax({
                    url: "{{ route('treaty.quarterly-figures.store') }}",
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Close modal
                        $modal.modal('hide');

                        // Show success message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message ||
                                    'Quarterly figures created successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            alert(response.message ||
                                'Quarterly figures created successfully!');
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

                                // Highlight invalid fields
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

        // Function to load data into modal (called when opening)
        function loadQuarterlyFiguresModal(data) {
            if (!data) return;

            if (data.treaty_id) $('#qf_treaty_id').val(data.treaty_id);
            if (data.transaction_id) $('#qf_transaction_id').val(data.transaction_id);
            if (data.treaty_name) $('#qf_treaty_name').text(data.treaty_name);
            if (data.insured_name) $('#qf_insured_name').text(data.insured_name);
            if (data.currency) {
                $('#qf_currency').text(data.currency);
                $('.qf-currency-label').text(data.currency);
            }
        }
    </script>
@endpush
