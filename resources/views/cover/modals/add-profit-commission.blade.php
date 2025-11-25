<div class="modal fade" id="addProfitCommissionModal" tabindex="-1" aria-labelledby="addProfitCommissionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-semibold" id="addProfitCommissionModalLabel">
                    <i class="fas fa-percentage me-2"></i>Add Profit Commission
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="profitCommissionForm">
                @csrf
                <input type="hidden" name="treaty_id" id="pc_treaty_id" value="{{ $treaty->id ?? '' }}">
                <input type="hidden" name="transaction_id" id="pc_transaction_id" value="{{ $transaction->id ?? '' }}">

                <div class="modal-body p-4">
                    {{-- Treaty Info Summary --}}
                    <div class="alert alert-light border mb-4">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <small class="text-muted d-block">Treaty</small>
                                <strong id="pc_treaty_name">{{ $treaty->treaty_type ?? 'SURPLUS' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Insured</small>
                                <strong id="pc_insured_name">{{ $insured->insured_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Currency</small>
                                <strong id="pc_currency">{{ $transaction->currency ?? 'KES' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Period</small>
                                <strong id="pc_period">{{ $transaction->period ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Period Selection --}}
                        <div class="col-md-6">
                            <label for="pc_from_date" class="form-label fw-medium">
                                From Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="pc_from_date" name="from_date" required>
                            <div class="invalid-feedback">Please select from date</div>
                        </div>

                        <div class="col-md-6">
                            <label for="pc_to_date" class="form-label fw-medium">
                                To Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="pc_to_date" name="to_date" required>
                            <div class="invalid-feedback">Please select to date</div>
                        </div>

                        {{-- Premium Income Section --}}
                        <div class="col-12 mt-3">
                            <h6 class="text-success border-bottom pb-2 mb-3">
                                <i class="fas fa-arrow-up me-2"></i>Premium Income
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_premium_income" class="form-label fw-medium">
                                Premium Income <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="pc_premium_income"
                                    name="premium_income" step="0.01" min="0" required placeholder="0.00">
                            </div>
                            <div class="invalid-feedback">Please enter premium income</div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_portfolio_premium_in" class="form-label fw-medium">Portfolio Premium
                                In</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="pc_portfolio_premium_in"
                                    name="portfolio_premium_in" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_portfolio_premium_out" class="form-label fw-medium">Portfolio Premium
                                Out</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="pc_portfolio_premium_out"
                                    name="portfolio_premium_out" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_total_income" class="form-label fw-medium">Total Income</label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end bg-light fw-bold"
                                    id="pc_total_income" name="total_income" step="0.01" readonly
                                    placeholder="0.00">
                            </div>
                        </div>

                        {{-- Deductions Section --}}
                        <div class="col-12 mt-4">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fas fa-arrow-down me-2"></i>Deductions
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_claims_paid" class="form-label fw-medium">Claims Paid</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="pc_claims_paid"
                                    name="claims_paid" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_claims_outstanding" class="form-label fw-medium">Claims Outstanding</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="pc_claims_outstanding"
                                    name="claims_outstanding" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_portfolio_claims_in" class="form-label fw-medium">Portfolio Claims
                                In</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="pc_portfolio_claims_in"
                                    name="portfolio_claims_in" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_portfolio_claims_out" class="form-label fw-medium">Portfolio Claims
                                Out</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="pc_portfolio_claims_out"
                                    name="portfolio_claims_out" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_commission_paid" class="form-label fw-medium">Commission Paid</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="pc_commission_paid"
                                    name="commission_paid" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_management_expenses_rate" class="form-label fw-medium">
                                Management Expenses (%)
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control text-end" id="pc_management_expenses_rate"
                                    name="management_expenses_rate" step="0.01" min="0" max="100"
                                    value="5.00" placeholder="0.00">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_management_expenses_amount" class="form-label fw-medium">
                                Management Expenses Amt
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end bg-light"
                                    id="pc_management_expenses_amount" name="management_expenses_amount"
                                    step="0.01" readonly placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_reserve_rate" class="form-label fw-medium">Reserve Rate (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control text-end" id="pc_reserve_rate"
                                    name="reserve_rate" step="0.01" min="0" max="100" value="0.00"
                                    placeholder="0.00">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_reserve_amount" class="form-label fw-medium">Reserve Amount</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end bg-light" id="pc_reserve_amount"
                                    name="reserve_amount" step="0.01" readonly placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_total_deductions" class="form-label fw-medium">Total Deductions</label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end bg-light fw-bold"
                                    id="pc_total_deductions" name="total_deductions" step="0.01" readonly
                                    placeholder="0.00">
                            </div>
                        </div>

                        {{-- Profit Calculation Section --}}
                        <div class="col-12 mt-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-calculator me-2"></i>Profit Commission Calculation
                            </h6>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_deficit_bf" class="form-label fw-medium">Deficit B/F (Previous
                                Period)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end" id="pc_deficit_bf"
                                    name="deficit_bf" step="0.01" min="0" value="0.00"
                                    placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_profit_balance" class="form-label fw-medium">Profit Balance</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light pc-currency-label">KES</span>
                                <input type="number" class="form-control text-end bg-light" id="pc_profit_balance"
                                    name="profit_balance" step="0.01" readonly placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="pc_profit_commission_rate" class="form-label fw-medium">
                                Profit Commission Rate (%) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control text-end" id="pc_profit_commission_rate"
                                    name="profit_commission_rate" step="0.01" min="0" max="100"
                                    value="20.00" placeholder="0.00" required>
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>

                        {{-- Final Amount --}}
                        <div class="col-12">
                            <div class="card bg-success bg-opacity-10 border-success mt-3">
                                <div class="card-body py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <h6 class="mb-0 text-success">
                                                <i class="fas fa-coins me-2"></i>Profit Commission Amount
                                            </h6>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text bg-success text-white">KES</span>
                                                <input type="number"
                                                    class="form-control form-control-lg text-end fw-bold bg-light"
                                                    id="pc_profit_commission_amount" name="profit_commission_amount"
                                                    step="0.01" readonly placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="pc_deficit_cf" class="form-label fw-medium mb-1">Deficit
                                                C/F</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-warning">KES</span>
                                                <input type="number" class="form-control text-end bg-light"
                                                    id="pc_deficit_cf" name="deficit_cf" step="0.01" readonly
                                                    placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Remarks --}}
                        <div class="col-12 mt-3">
                            <label for="pc_remarks" class="form-label fw-medium">Remarks</label>
                            <textarea class="form-control" id="pc_remarks" name="remarks" rows="2"
                                placeholder="Enter any additional notes..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-outline-info" id="pc_recalculate_btn">
                        <i class="fas fa-calculator me-1"></i>Recalculate
                    </button>
                    <button type="submit" class="btn btn-success" id="pc_submit_btn">
                        <i class="fas fa-save me-1"></i>Add Profit Commission
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
            const $modal = $('#addProfitCommissionModal');
            const $form = $('#profitCommissionForm');
            const $submitBtn = $('#pc_submit_btn');

            // Input elements
            const $premiumIncome = $('#pc_premium_income');
            const $portfolioPremiumIn = $('#pc_portfolio_premium_in');
            const $portfolioPremiumOut = $('#pc_portfolio_premium_out');
            const $totalIncome = $('#pc_total_income');
            const $claimsPaid = $('#pc_claims_paid');
            const $claimsOutstanding = $('#pc_claims_outstanding');
            const $portfolioClaimsIn = $('#pc_portfolio_claims_in');
            const $portfolioClaimsOut = $('#pc_portfolio_claims_out');
            const $commissionPaid = $('#pc_commission_paid');
            const $managementExpensesRate = $('#pc_management_expenses_rate');
            const $managementExpensesAmount = $('#pc_management_expenses_amount');
            const $reserveRate = $('#pc_reserve_rate');
            const $reserveAmount = $('#pc_reserve_amount');
            const $totalDeductions = $('#pc_total_deductions');
            const $deficitBf = $('#pc_deficit_bf');
            const $profitBalance = $('#pc_profit_balance');
            const $profitCommissionRate = $('#pc_profit_commission_rate');
            const $profitCommissionAmount = $('#pc_profit_commission_amount');
            const $deficitCf = $('#pc_deficit_cf');

            // Main calculation function
            function calculateProfitCommission() {
                // Calculate Total Income
                const premiumIncome = parseFloat($premiumIncome.val()) || 0;
                const portfolioPremiumIn = parseFloat($portfolioPremiumIn.val()) || 0;
                const portfolioPremiumOut = parseFloat($portfolioPremiumOut.val()) || 0;
                const totalIncome = premiumIncome + portfolioPremiumIn - portfolioPremiumOut;
                $totalIncome.val(totalIncome.toFixed(2));

                // Calculate Management Expenses Amount
                const managementExpensesRate = parseFloat($managementExpensesRate.val()) || 0;
                const managementExpensesAmount = (premiumIncome * managementExpensesRate) / 100;
                $managementExpensesAmount.val(managementExpensesAmount.toFixed(2));

                // Calculate Reserve Amount
                const reserveRate = parseFloat($reserveRate.val()) || 0;
                const reserveAmount = (premiumIncome * reserveRate) / 100;
                $reserveAmount.val(reserveAmount.toFixed(2));

                // Calculate Total Deductions
                const claimsPaid = parseFloat($claimsPaid.val()) || 0;
                const claimsOutstanding = parseFloat($claimsOutstanding.val()) || 0;
                const portfolioClaimsIn = parseFloat($portfolioClaimsIn.val()) || 0;
                const portfolioClaimsOut = parseFloat($portfolioClaimsOut.val()) || 0;
                const commissionPaid = parseFloat($commissionPaid.val()) || 0;

                const totalDeductions = claimsPaid + claimsOutstanding + portfolioClaimsIn -
                    portfolioClaimsOut + commissionPaid + managementExpensesAmount + reserveAmount;
                $totalDeductions.val(totalDeductions.toFixed(2));

                // Calculate Profit Balance
                const deficitBf = parseFloat($deficitBf.val()) || 0;
                const profitBalance = totalIncome - totalDeductions - deficitBf;
                $profitBalance.val(profitBalance.toFixed(2));

                // Style based on profit/loss
                if (profitBalance >= 0) {
                    $profitBalance.removeClass('text-danger').addClass('text-success');
                } else {
                    $profitBalance.removeClass('text-success').addClass('text-danger');
                }

                // Calculate Profit Commission
                const profitCommissionRate = parseFloat($profitCommissionRate.val()) || 0;
                let profitCommissionAmount = 0;
                let deficitCf = 0;

                if (profitBalance > 0) {
                    profitCommissionAmount = (profitBalance * profitCommissionRate) / 100;
                } else {
                    deficitCf = Math.abs(profitBalance);
                }

                $profitCommissionAmount.val(profitCommissionAmount.toFixed(2));
                $deficitCf.val(deficitCf.toFixed(2));
            }

            // Bind calculation to all editable inputs
            const $editableInputs = $form.find(
                '#pc_premium_income, #pc_portfolio_premium_in, #pc_portfolio_premium_out, ' +
                '#pc_claims_paid, #pc_claims_outstanding, #pc_portfolio_claims_in, ' +
                '#pc_portfolio_claims_out, #pc_commission_paid, #pc_management_expenses_rate, ' +
                '#pc_reserve_rate, #pc_deficit_bf, #pc_profit_commission_rate'
            );

            $editableInputs.on('input change', calculateProfitCommission);

            // Recalculate button
            $('#pc_recalculate_btn').on('click', calculateProfitCommission);

            // Reset form when modal closes
            $modal.on('hidden.bs.modal', function() {
                $form[0].reset();
                // Clear calculated fields
                $totalIncome.val('');
                $managementExpensesAmount.val('');
                $reserveAmount.val('');
                $totalDeductions.val('');
                $profitBalance.val('').removeClass('text-success text-danger');
                $profitCommissionAmount.val('');
                $deficitCf.val('');
                $form.find('.is-invalid').removeClass('is-invalid');
            });

            // Set default dates when modal opens
            $modal.on('shown.bs.modal', function() {
                const today = new Date();
                const $fromDate = $('#pc_from_date');
                const $toDate = $('#pc_to_date');

                if (!$fromDate.val()) {
                    $fromDate.val(today.getFullYear() + '-01-01');
                }
                if (!$toDate.val()) {
                    $toDate.val(today.toISOString().split('T')[0]);
                }
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

                // Validate dates
                const fromDate = new Date($('#pc_from_date').val());
                const toDate = new Date($('#pc_to_date').val());

                if (toDate < fromDate) {
                    $('#pc_to_date').addClass('is-invalid');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Validation Error', 'To Date must be after From Date', 'error');
                    } else {
                        alert('To Date must be after From Date');
                    }
                    return;
                }

                // Show loading state
                const originalBtnText = $submitBtn.html();
                $submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i>Saving...');

                // AJAX request
                $.ajax({
                    url: "{{ route('treaty.profit-commission.store') }}",
                    type: 'POST',
                    data: $form.serialize(),
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
                                    'Profit commission added successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            alert(response.message ||
                                'Profit commission added successfully!');
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

        // Function to load data into modal
        function loadProfitCommissionModal(data) {
            if (!data) return;

            if (data.treaty_id) $('#pc_treaty_id').val(data.treaty_id);
            if (data.transaction_id) $('#pc_transaction_id').val(data.transaction_id);
            if (data.treaty_name) $('#pc_treaty_name').text(data.treaty_name);
            if (data.insured_name) $('#pc_insured_name').text(data.insured_name);
            if (data.currency) {
                $('#pc_currency').text(data.currency);
                $('.pc-currency-label').text(data.currency);
            }
            if (data.period) $('#pc_period').text(data.period);

            // Pre-fill with existing data if available
            if (data.premium_income) $('#pc_premium_income').val(data.premium_income);
            if (data.deficit_bf) $('#pc_deficit_bf').val(data.deficit_bf);
        }
    </script>
@endpush
