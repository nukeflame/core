<!--Debit Modal -->
<div class="modal effect-scale md-wrapper" id="debit-modal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticDebitLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" id="debitForm" action="{{ route('cover.generate-debit') }}">
                @csrf
                <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
                <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                <div class="modal-header">
                    <h5 class="modal-title" id="staticDebitLabel">Create A Debit Note</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Main Form Fields -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="description"
                                        class="form-control form-control-sm" />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="underwriting_year">Underwriting Year</label>
                                    <input type="text" name="underwriting_year" id="underwriting_year"
                                        class="form-control form-control-sm" />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="reinsurer_posting">Reinsurer Posting</label>
                                    <select name="reinsurer_posting" id="reinsurer_posting"
                                        class="form-control form-control-sm">
                                        <option value="NET">NET</option>
                                        <option value="GROSS">GROSS</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ppw_terms">PPW Terms</label>
                                    <input type="text" name="ppw_terms" id="ppw_terms"
                                        class="form-control form-control-sm" placeholder="PPW 45" />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="ppw_at_inception"
                                            id="ppw_at_inception" value="1">
                                        <label class="form-check-label" for="ppw_at_inception">
                                            PPW at Inception
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="cover_no_display">Cover No</label>
                                    <input type="text" class="form-control form-control-sm"
                                        value="{{ $coverReg->cover_no }}" readonly />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="posting_quarter">Posting Quarter</label>
                                    <select name="posting_quarter" id="posting_quarter"
                                        class="form-control form-control-sm">
                                        <option value="">Select Quarter</option>
                                        <option value="Q1">Q1</option>
                                        <option value="Q2">Q2</option>
                                        <option value="Q3">Q3</option>
                                        <option value="Q4">Q4</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="posting_date">Posting Date</label>
                                    <input type="date" name="posting_date" id="posting_date"
                                        class="form-control form-control-sm" />
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="brokerage_rate">Brokerage Rate</label>
                                    <input type="number" name="brokerage_rate" id="brokerage_rate"
                                        class="form-control form-control-sm" step="0.01" value="2.50" />
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="comments">Comments</label>
                                    <textarea name="comments" id="comments" class="form-control form-control-sm" rows="3"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="show_cedant"
                                            id="show_cedant" value="1">
                                        <label class="form-check-label" for="show_cedant">
                                            Show Cedant
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="show_reinsurer"
                                            id="show_reinsurer" value="1">
                                        <label class="form-check-label" for="show_reinsurer">
                                            Show Reinsurer
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side Panel -->
                        <div class="col-md-4">
                            <!-- Foreign Currency -->
                            <div class="card mb-3">
                                <div class="card-header py-2">
                                    <strong>Foreign Currency</strong>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="currency">Currency</label>
                                        <select name="currency" id="currency" class="form-control form-control-sm">
                                            <option value="KES">KES</option>
                                            <option value="USD">USD</option>
                                            <option value="EUR">EUR</option>
                                            <option value="GBP">GBP</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="exch_rate">Exchange Rate</label>
                                        <input type="number" name="exch_rate" id="exch_rate"
                                            class="form-control form-control-sm" step="0.01" value="1" />
                                    </div>
                                </div>
                            </div>

                            <!-- Tax Rates -->
                            <div class="card mb-3">
                                <div class="card-header py-2">
                                    <strong>Tax Rates</strong>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label for="vat_rate">VAT Rate</label>
                                        <input type="number" name="vat_rate" id="vat_rate"
                                            class="form-control form-control-sm" step="0.01" value="0.00" />
                                    </div>
                                    <div class="mb-2">
                                        <label for="prm_tax">Premium Tax</label>
                                        <input type="number" name="prm_tax" id="prm_tax"
                                            class="form-control form-control-sm" step="0.01" value="0.00" />
                                    </div>
                                    <div class="mb-2">
                                        <label for="city_levy">City Levy</label>
                                        <input type="number" name="city_levy" id="city_levy"
                                            class="form-control form-control-sm" step="0.01" value="0.00" />
                                    </div>
                                    <div class="mb-2">
                                        <label for="w_tax_rate">W/Tax Rate</label>
                                        <input type="number" name="w_tax_rate" id="w_tax_rate"
                                            class="form-control form-control-sm" step="0.01" value="0.00" />
                                    </div>
                                </div>
                            </div>

                            <!-- Compute Standard Taxes -->
                            <div class="card mb-3">
                                <div class="card-header py-2">
                                    <strong>Compute Standard Taxes</strong>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="compute_premium_tax"
                                            id="compute_premium_tax" value="1">
                                        <label class="form-check-label" for="compute_premium_tax">
                                            Premium Tax
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            name="compute_reinsurance_tax" id="compute_reinsurance_tax"
                                            value="1">
                                        <label class="form-check-label" for="compute_reinsurance_tax">
                                            Reinsurance Tax
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            name="compute_withholding_tax" id="compute_withholding_tax"
                                            value="1">
                                        <label class="form-check-label" for="compute_withholding_tax">
                                            Withholding Tax
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Compute Sliding -->
                            <div class="card mb-3">
                                <div class="card-header py-2">
                                    <strong>Compute Sliding</strong>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="loss_participation"
                                            id="loss_participation" value="1">
                                        <label class="form-check-label" for="loss_participation">
                                            Loss Participation
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="sliding_commission"
                                            id="sliding_commission" value="1">
                                        <label class="form-check-label" for="sliding_commission">
                                            Sliding Commission
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Debit Items Grid -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                    <strong>Debit Items</strong>
                                    <button type="button" class="btn btn-sm btn-success" id="add-debit-item">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0" id="debit-items-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 15%;">Item Code</th>
                                                    <th style="width: 25%;">Description</th>
                                                    <th style="width: 25%;">Extra Description</th>
                                                    <th style="width: 15%;">Ledger</th>
                                                    <th style="width: 10%;">Line Rate</th>
                                                    <th style="width: 10%;">Amount</th>
                                                    <th style="width: 5%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="debit-items-body">
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-3">
                                                        No items added. Click "Add Item" to begin.
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" class="text-end"><strong>Total Amount:</strong>
                                                    </td>
                                                    <td><strong><span id="total-amount">0.00</span></strong></td>
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
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="preview-debit-btn">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button type="button" id="debit-save-btn"
                        class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">
                        <i class="fas fa-check"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Item Row Template (Hidden) -->
<template id="debit-item-row-template">
    <tr class="debit-item-row">
        <td>
            <input type="text" name="items[INDEX][item_code]" class="form-control form-control-sm"
                placeholder="Code" />
        </td>
        <td>
            <input type="text" name="items[INDEX][description]" class="form-control form-control-sm"
                placeholder="Description" required />
        </td>
        <td>
            <input type="text" name="items[INDEX][extra_description]" class="form-control form-control-sm"
                placeholder="Extra Description" />
        </td>
        <td>
            <select name="items[INDEX][ledger]" class="form-control form-control-sm">
                <option value="">Select Ledger</option>
                <option value="PREMIUM">Premium</option>
                <option value="COMMISSION">Commission</option>
                <option value="CLAIM">Claim</option>
                <option value="TAX">Tax</option>
            </select>
        </td>
        <td>
            <input type="number" name="items[INDEX][line_rate]" class="form-control form-control-sm line-rate"
                step="0.01" placeholder="0.00" />
        </td>
        <td>
            <input type="number" name="items[INDEX][amount]" class="form-control form-control-sm item-amount"
                step="0.01" placeholder="0.00" required />
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = 0;

        // Add Item
        document.getElementById('add-debit-item').addEventListener('click', function() {
            const template = document.getElementById('debit-item-row-template');
            const tbody = document.getElementById('debit-items-body');

            // Remove "no items" message if present
            if (tbody.querySelector('td[colspan="7"]')) {
                tbody.innerHTML = '';
            }

            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('tr');

            // Replace INDEX placeholder with actual index
            row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);
            itemIndex++;

            tbody.appendChild(clone);

            // Add event listeners for amount calculation
            row.querySelector('.item-amount').addEventListener('input', calculateTotal);
        });

        // Remove Item
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                e.target.closest('tr').remove();
                calculateTotal();

                // Add "no items" message if table is empty
                const tbody = document.getElementById('debit-items-body');
                if (tbody.children.length === 0) {
                    tbody.innerHTML = `<tr>
                    <td colspan="7" class="text-center text-muted py-3">
                        No items added. Click "Add Item" to begin.
                    </td>
                </tr>`;
                }
            }
        });

        // Calculate Total
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.item-amount').forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            document.getElementById('total-amount').textContent = total.toFixed(2);
        }

        // Preview Button
        document.getElementById('preview-debit-btn').addEventListener('click', function() {
            // [Inference] This would trigger a preview modal or window
            alert('Preview functionality would be implemented here');
        });

        // Generate Button
        document.getElementById('debit-save-btn').addEventListener('click', function() {
            const form = document.getElementById('debitForm');

            // Validate at least one item exists
            const items = document.querySelectorAll('.debit-item-row');
            if (items.length === 0) {
                alert('Please add at least one debit item');
                return;
            }

            // [Inference] Additional validation would occur here
            if (form.checkValidity()) {
                form.submit();
            } else {
                form.reportValidity();
            }
        });
    });
</script>
