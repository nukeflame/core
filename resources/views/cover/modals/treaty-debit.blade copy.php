<div class="modal fade effect-scale md-wrapper" id="treatyDebitModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticDebitLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 70%;">
        <div class="modal-content">
            <form method="POST" id="treatyDebitForm" action="{{ route('cover.generate-debit') }}">
                @csrf
                <input type="hidden" name="cover_no" value="{{ $cover->cover_no }}" />
                <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}" />
                <input type="hidden" name="type_of_bus" value="{{ $cover->type_of_bus }}" />
                <input type="hidden" name="installment" value="{{ $nextInstallment }}" />
                <input type="hidden" name="amount" value="{{ number_format($installmentAmount, 2) }}" />

                <div class="modal-header">
                    <h5 class="modal-title" id="staticDebitLabel">Generate Debit Note</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Cover & Endorsement Info -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cover Number</label>
                            <input type="text" class="form-control fw-medium bg-light" value="{{ $cover->cover_no }}"
                                readonly required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Endorsement Number</label>
                            <input type="text" class="form-control fw-medium bg-light"
                                value="{{ $cover->endorsement_no }}" readonly required />
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left Column: Main Form Fields -->
                        <div class="col-lg-8">
                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-semibold">Debit Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="reinsurer_posting">Settlement Method</label>
                                            <select name="reinsurer_posting" id="reinsurer_posting"
                                                class="form-inputs select2">
                                                <option value="NET">Net Amount</option>
                                                <option value="GROSS">Gross Amount</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="premium_pay_terms">Payment Terms</label>
                                            <div class="cover-card">
                                                <select class="form-control select2" name="premium_pay_terms"
                                                    id="premium_pay_terms" required>
                                                    <option value="">-- Select --</option>
                                                    @foreach ($premiumPayTerms as $term)
                                                        <option value="{{ $term->pay_term_code }}"
                                                            data-description="{{ $term->pay_term_desc }}"
                                                            {{ isset($old_endt_trans) && $old_endt_trans->premium_payment_code == $term->pay_term_code ? 'selected' : '' }}>
                                                            {{ $term->pay_term_desc }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Posting Quarter & Year -->
                                        <div class="col-md-4">
                                            <label class="form-label" for="posting_year">Fiscal Year</label>
                                            <select name="posting_year" id="posting_year"
                                                class="form-select form-select-sm">
                                                <option value="">Select Year</option>
                                                @for ($year = date('Y') + 1; $year >= date('Y') - 2; $year--)
                                                    <option value="{{ $year }}"
                                                        {{ $year == date('Y') ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label" for="posting_quarter">Accounting Period
                                                (Quarter)</label>
                                            <select name="posting_quarter" id="posting_quarter"
                                                class="form-select form-select-sm">
                                                <option value="">Select Period</option>
                                                <option value="Q1">Q1 (Jan - Mar)</option>
                                                <option value="Q2">Q2 (Apr - Jun)</option>
                                                <option value="Q3">Q3 (Jul - Sep)</option>
                                                <option value="Q4">Q4 (Oct - Dec)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label" for="posting_date">Transaction Date</label>
                                            <input type="date" name="posting_date" id="posting_date"
                                                class="form-control" value="{{ date('Y-m-d') }}" />
                                        </div>

                                        <!-- Brokerage Rate -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="brokerage_rate">Brokerage Rate
                                                (%)</label>
                                            <input type="number" name="brokerage_rate" id="brokerage_rate"
                                                class="form-control" step="0.01" value="2.50" min="0"
                                                max="100" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-semibold">Statutory Levies</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="compute_premium_tax"
                                            id="compute_premium_tax" value="1">
                                        <label class="form-check-label small" for="compute_premium_tax">
                                            Apply Premium Levy
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            name="compute_reinsurance_tax" id="compute_reinsurance_tax"
                                            value="1">
                                        <label class="form-check-label small" for="compute_reinsurance_tax">
                                            Apply Reinsurance Levy
                                        </label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox"
                                            name="compute_withholding_tax" id="compute_withholding_tax"
                                            value="1">
                                        <label class="form-check-label small" for="compute_withholding_tax">
                                            Apply Withholding Tax (WHT)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Compute Sliding -->
                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-semibold">Variable Commission</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="loss_participation"
                                            id="loss_participation" value="1">
                                        <label class="form-check-label small" for="loss_participation">
                                            Include Loss Participation
                                        </label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="sliding_commission"
                                            id="sliding_commission" value="1">
                                        <label class="form-check-label small" for="sliding_commission">
                                            Apply Sliding Scale Commission
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- Comments -->
                        <div class="col-12 mb-2">
                            <label class="form-label" for="comments">Additional Notes & Remarks</label>
                            <textarea name="comments" id="comments" class="form-control resize-none" rows="4"
                                placeholder="Enter any additional information, special terms, or remarks"></textarea>
                        </div>

                        <!-- Display Options -->
                        <div class="col-12">
                            <label class="form-label d-block mb-2">Note Visibility Options</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="show_cedant"
                                            id="show_cedant" value="1">
                                        <label class="form-check-label" for="show_cedant">
                                            Display Notes on Cedant Statement
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="show_reinsurer"
                                            id="show_reinsurer" value="1">
                                        <label class="form-check-label" for="show_reinsurer">
                                            Display Notes on Reinsurer Statement
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Debit Items Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                    <strong>Transaction Line Items</strong>
                                    <button type="button" class="btn btn-sm btn-success" id="add-debit-item">
                                        <i class="fas fa-plus"></i> Add Line Item
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0" id="debit-items-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 12%;">Item Code</th>
                                                    <th style="width: 23%;">Transaction Type <span
                                                            class="text-danger">*</span></th>
                                                    <th style="width: 23%;">Business Class</th>
                                                    <th style="width: 18%;">Fee Rate (%)</th>
                                                    <th style="width: 19%;">Transaction Amount <span
                                                            class="text-danger">*</span>
                                                    </th>
                                                    <th style="width: 5%;" class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="debit-items-body">
                                                <tr id="no-items-row">
                                                    <td colspan="7" class="text-center text-muted py-3">
                                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                        No line items added. Click "Add Line Item" to begin.
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bold">Total Transaction
                                                        Value:</td>
                                                    <td class="fw-bold">
                                                        <span id="total-amount">0.00</span>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" id="debit-save-btn" class="btn btn-primary btn-sm">
                        <i class="fas fa-check"></i> Generate Debit Note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Item Row Template (Hidden) -->
<template id="debit-item-row-template">
    <tr class="debit-item-row" data-item-index="INDEX">
        <td>
            <select class="form-select select2" name="items[INDEX][item_code]">
                <option value="">-- Select Code --</option>
                <option value="IT01">IT01</option>
                <option value="IT02">IT02</option>
                <option value="IT03">IT03</option>
                <option value="IT04">IT04</option>
                <option value="IT05">IT05</option>
                <option value="IT06">IT06</option>
                <option value="IT26">IT26</option>
                <option value="IT27">IT27</option>
                <option value="IT29">IT29</option>
            </select>
        </td>
        <td>
            <select name="items[INDEX][description]" class="form-select select2">
                <option value="">-- Select Type --</option>
                <option value="IT01" title="GROSS PREMIUM">Gross Premium</option>
                <option value="IT02" title="CLAIMS">Claims Payment</option>
                <option value="IT03" title="COMMISSION">Commission Allowance</option>
                <option value="IT04" title="REINSURANCE TAX">Reinsurance Levy</option>
                <option value="IT05" title="PREMIUM TAX">Premium Levy</option>
                <option value="IT06" title="BROKERAGE">Brokerage Fee</option>
                <option value="IT26" title="PREMIUM PORTFOLIO ENTRY">Premium Portfolio Entry</option>
                <option value="IT27" title="LOSS PORTFOLIO ENTRY">Loss Portfolio Entry</option>
                <option value="IT29" title="WITHHOLDING TAX">Withholding Tax (WHT)</option>
            </select>
        </td>
        <td>
            <select name="items[INDEX][class_name]" class="form-select select2">
                <option value="">-- Select Class --</option>

                <!-- Fire Classes -->
                <optgroup label="Fire & Property">
                    <option value="FC01" title="FIRE">Fire Insurance</option>
                    <option value="FC02" title="FIRE INDUSTRIAL">Industrial Fire</option>
                    <option value="FC03" title="FIRE DOMESTIC">Domestic Fire</option>
                </optgroup>

                <!-- Engineering Classes -->
                <optgroup label="Engineering">
                    <option value="EC01" title="ENGINEERING">Contractors All Risks (CAR)</option>
                    <option value="EC02" title="ENGINEERING">Erection All Risks (EAR)</option>
                    <option value="EC03" title="ENGINEERING">Machinery Breakdown</option>
                </optgroup>

                <!-- Marine Classes -->
                <optgroup label="Marine">
                    <option value="MC01" title="MARINE CARGO">Marine Cargo</option>
                    <option value="MC02" title="MARINE HULL">Marine Hull</option>
                </optgroup>

                <!-- Motor Classes -->
                <optgroup label="Motor Vehicle">
                    <option value="MT01" title="MOTOR PRIVATE">Motor Private</option>
                    <option value="MT02" title="MOTOR COMMERCIAL">Motor Commercial</option>
                    <option value="MT03" title="MOTOR PSV">Motor PSV</option>
                </optgroup>

                <!-- Aviation Classes -->
                <optgroup label="Aviation">
                    <option value="AV01" title="AVIATION">Aviation Hull</option>
                    <option value="AV02" title="AVIATION">Aviation Liability</option>
                </optgroup>

                <!-- Miscellaneous Classes -->
                <optgroup label="Miscellaneous">
                    <option value="MS01" title="THEFT / BURGLARY">Burglary & Theft</option>
                    <option value="MS02" title="PUBLIC LIABILITY">Public Liability</option>
                    <option value="MS03" title="EMPLOYERS LIABILITY">Employer's Liability</option>
                </optgroup>
            </select>
        </td>
        <td>
            <input type="number" name="items[INDEX][line_rate]" class="form-control item-line-rate" step="0.01"
                placeholder="0.00" min="0" max="100" />
        </td>
        <td>
            <input type="number" name="items[INDEX][amount]" class="form-control item-amount" step="0.01"
                placeholder="0.00" required min="0.01" />
            <div class="invalid-feedback">Amount must be greater than 0</div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" title="Remove item"
                data-bs-toggle="tooltip">
                <i class="bx bx-trash-alt"></i>
            </button>
        </td>
    </tr>
</template>

<style>
    /* Debit Modal Styles */
    #treatyDebitModal .form-label {
        margin-bottom: 0.25rem;
        font-weight: 500;
        font-size: 0.875rem;
    }

    #treatyDebitModal .card {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    #treatyDebitModal .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    #treatyDebitModal .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    #treatyDebitModal .table th {
        font-weight: 600;
        font-size: 0.813rem;
        background-color: #f8f9fa;
    }

    #treatyDebitModal .modal-body {
        max-height: 75vh;
        overflow-y: auto;
    }

    #treatyDebitModal .resize-none {
        resize: none;
    }

    #treatyDebitModal .text-danger {
        color: #dc3545;
    }

    #treatyDebitModal .debit-item-row {
        transition: background-color 0.2s ease;
    }

    #treatyDebitModal .debit-item-row:hover {
        background-color: #f8f9fa;
    }

    #treatyDebitModal .invalid-feedback {
        display: none;
        font-size: 0.75rem;
    }

    #treatyDebitModal .is-invalid~.invalid-feedback {
        display: block;
    }

    #treatyDebitModal .table-responsive {
        max-height: 400px;
        overflow-y: auto;
    }

    /* Scrollbar Styling */
    #treatyDebitModal .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    #treatyDebitModal .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #treatyDebitModal .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    #treatyDebitModal .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

@push('script')
    <script>
        let debitItemIndex = 0;
        let isDebitFormSubmitting = false;

        const $debitModal = $('#treatyDebitModal');
        const $debitForm = $('#treatyDebitForm');
        const $debitItemsBody = $('#debit-items-body');
        const $debitItemTemplate = $('#debit-item-row-template');
        const $addDebitItemBtn = $('#add-debit-item');
        const $debitSaveBtn = $('#debit-save-btn');
        const $totalAmount = $('#total-amount');
        const $noItemsRow = $('#no-items-row');

        function initDebitModal() {
            if (typeof jQuery === 'undefined') {
                return;
            }

            if ($debitModal.length === 0) {
                return;
            }

            bindDebitModalEvents();
        }

        function bindDebitModalEvents() {
            $addDebitItemBtn.on('click', function(e) {
                e.preventDefault();
                addDebitItem();
            });

            $debitItemsBody.on('click', '.remove-item-btn', function(e) {
                e.preventDefault();
                removeDebitItem($(this));
            });

            $debitItemsBody.on('input', '.item-amount', function() {
                calculateDebitTotal();
            });

            $debitForm.on('submit', function(e) {
                e.preventDefault();
                handleDebitFormSubmit();
            });

            $debitModal.on('hidden.bs.modal', function() {
                resetDebitForm();
            });

            $debitModal.on('shown.bs.modal', function() {
                const $description = $('#description');
                if ($description.length > 0) {
                    setTimeout(function() {
                        $description.focus();
                    }, 100);
                }
            });
        }

        function addDebitItem() {
            if ($debitItemTemplate.length === 0) {
                showToast('Error: Template not found', 'error');
                return;
            }

            $noItemsRow.hide();

            const template = $debitItemTemplate.html();
            if (!template) {
                return;
            }

            const itemHtml = template.replace(/INDEX/g, debitItemIndex);
            $debitItemsBody.append(itemHtml);

            debitItemIndex++;

            const $newRow = $debitItemsBody.find('.debit-item-row:last');

            if (typeof $.fn.tooltip !== 'undefined') {
                $newRow.find('[data-bs-toggle="tooltip"]').tooltip();
            }

            setTimeout(function() {
                $newRow.find('.item-code').focus();
            }, 50);
        }

        function removeDebitItem($removeBtn) {
            if (!confirm('Are you sure you want to remove this line item?')) {
                return;
            }

            const $row = $removeBtn.closest('.debit-item-row');

            if ($row.length === 0) {
                console.error('Row not found');
                return;
            }

            $row.fadeOut(300, function() {
                $(this).remove();
                calculateDebitTotal();

                const itemCount = $debitItemsBody.find('.debit-item-row').length;
                if (itemCount === 0) {
                    $noItemsRow.fadeIn(200);
                }
            });
        }

        function calculateDebitTotal() {
            let total = 0;

            $debitItemsBody.find('.item-amount').each(function() {
                const value = parseFloat($(this).val());
                if (!isNaN(value) && value > 0) {
                    total += value;
                }
            });

            $totalAmount.text(formatCurrency(total));
        }

        /**
         * Format number as currency
         * @param {number} amount
         * @returns {string}
         */
        function formatCurrency(amount) {
            if (isNaN(amount)) {
                return '0.00';
            }
            return amount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function validateDebitForm() {
            let isValid = true;
            const errors = [];

            if ($debitForm[0] && !$debitForm[0].checkValidity()) {
                $debitForm[0].reportValidity();
                return false;
            }

            // Check if items exist
            const itemCount = $debitItemsBody.find('.debit-item-row').length;
            if (itemCount === 0) {
                errors.push('Please add at least one transaction line item');
                isValid = false;
            }

            // Validate each item
            $debitItemsBody.find('.debit-item-row').each(function(index) {
                const $row = $(this);
                const $itemDescription = $row.find('.item-description');
                const $amount = $row.find('.item-amount');

                // Check item description
                const itemDesc = $itemDescription.val() ? $itemDescription.val().trim() : '';
                if (!itemDesc) {
                    $itemDescription.addClass('is-invalid');
                    errors.push(`Line ${index + 1}: Transaction type is required`);
                    isValid = false;
                } else {
                    $itemDescription.removeClass('is-invalid');
                }

                // Check item amount
                const amount = parseFloat($amount.val());
                if (isNaN(amount) || amount <= 0) {
                    $amount.addClass('is-invalid');
                    errors.push(`Line ${index + 1}: Amount must be greater than 0`);
                    isValid = false;
                } else {
                    $amount.removeClass('is-invalid');
                }
            });

            // Display errors if any
            if (errors.length > 0) {
                showValidationErrors(errors);
            }

            return isValid;
        }

        /**
         * Display validation errors to the user
         * @param {Array} errors - Array of error messages
         */
        function showValidationErrors(errors) {
            if (!errors || errors.length === 0) {
                return;
            }

            const errorMessage = 'Please correct the following:\n\n' + errors.join('\n');

            // Use toastr if available, otherwise alert
            if (typeof toastr !== 'undefined') {
                toastr.error(errorMessage, 'Validation Required', {
                    timeOut: 5000,
                    closeButton: true
                });
            } else {
                alert(errorMessage);
            }
        }

        function handleDebitFormSubmit() {
            if (isDebitFormSubmitting) {
                return;
            }

            isDebitFormSubmitting = true;
            setDebitLoadingState(true);

            const formData = new FormData($debitForm[0]);

            $.ajax({
                url: $debitForm.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    // console.log(response)
                    if (response.success) {
                        window.location.href = response.redirectUrl;
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'An error occurred while generating the debit note.';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;

                        Object.keys(errors).forEach(field => {
                            const $field = $(`#${field}`);
                            $field.addClass('is-invalid');

                            const errorMessages = errors[field].join(', ');
                            $field.after(
                                `<div class="error-message invalid-feedback">${errorMessages}</div>`
                            );
                        });

                        errorMessage = xhr.responseJSON.message ||
                            'Please correct the highlighted fields and try again.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 0) {
                        errorMessage = 'Network connection error. Please check your internet and try again.';
                    } else if (xhr.status >= 500) {
                        errorMessage =
                            'Server error occurred. Please try again later or contact technical support.';
                    }

                    showAlert('error', errorMessage);
                },
                complete: function() {
                    isDebitFormSubmitting = false;
                    setDebitLoadingState(false);
                }
            });

            return false;
        }

        function setDebitLoadingState(loading) {
            if ($debitSaveBtn.length === 0) {
                return;
            }

            if (loading) {
                $debitSaveBtn
                    .prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            } else {
                $debitSaveBtn
                    .prop('disabled', false)
                    .html('<i class="fas fa-check"></i> Generate Debit Note');
            }
        }

        function resetDebitForm() {
            if ($debitForm.length === 0) {
                console.warn('Form not found for reset');
                return;
            }

            $debitForm[0].reset();

            $debitForm.find('.is-invalid').removeClass('is-invalid');

            $debitItemsBody.find('.debit-item-row').remove();
            $noItemsRow.show();

            $totalAmount.text('0.00');

            debitItemIndex = 0;
            isDebitFormSubmitting = false;

            setDebitLoadingState(false);
        }

        function showToast(message, type) {
            type = type || 'info';

            if (typeof toastr !== 'undefined' && toastr[type]) {
                toastr[type](message);
            }
        }

        $(document).ready(function() {
            setTimeout(function() {
                initDebitModal();
            }, 100);
        });
    </script>
@endpush
