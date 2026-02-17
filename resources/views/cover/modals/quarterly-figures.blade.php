<div class="modal fade effect-scale md-wrapper" id="createQuarterlyFiguresModal" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="createQuarterlyFiguresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 75%;">
        <div class="modal-content">
            <form method="POST" id="quarterlyFiguresForm" action="{{ route('cover.generate-debit') }}">
                @csrf
                <input type="hidden" name="cover_no" value="{{ $cover->cover_no ?? '' }}" />
                <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no ?? '' }}" />
                <input type="hidden" name="type_of_bus" value="{{ $cover->type_of_bus ?? '' }}" />
                <input type="hidden" name="installment" value="{{ $nextInstallment ?? 1 }}" />
                <input type="hidden" name="amount" value="{{ number_format($installmentAmount ?? 0, 2, '.', '') }}" />
                <input type="hidden" name="treatyClasses" value="{{ json_encode($treatyClasses ?? []) }}"
                    id="treatyClasses" />

                <div class="modal-header">
                    <h5 class="modal-title" id="createQuarterlyFiguresModalLabel">Quarterly Figures</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cover Number</label>
                            <input type="text" class="form-control fw-medium bg-light"
                                value="{{ $cover->cover_no ?? '' }}" readonly />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Endorsement Number</label>
                            <input type="text" class="form-control fw-medium bg-light"
                                value="{{ $cover->endorsement_no ?? '' }}" readonly />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card shadow-sm mb-3">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-semibold">Debit Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label" for="posting_year">
                                                        Underwriting Year <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="posting_year" id="posting_year" class="form-select"
                                                        required>
                                                        <option value="">Select Year</option>
                                                        @for ($year = date('Y') + 1; $year >= date('Y') - 2; $year--)
                                                            <option value="{{ $year }}"
                                                                {{ $year == date('Y') ? 'selected' : '' }}>
                                                                {{ $year }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>

                                                <div class="col-md-8"></div>

                                                <div class="col-md-4">
                                                    <label class="form-label" for="posting_quarter">
                                                        Posting Quarter <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="cover-card">
                                                        <select name="posting_quarter" id="posting_quarter"
                                                            class="form-select" required>
                                                            <option value="">Select Quarter</option>
                                                            <option value="Q1"
                                                                {{ date('n') <= 3 ? 'selected' : '' }}>
                                                                Q1 - First Quarter
                                                            </option>
                                                            <option value="Q2"
                                                                {{ date('n') >= 4 && date('n') <= 6 ? 'selected' : '' }}>
                                                                Q2 - Second Quarter
                                                            </option>
                                                            <option value="Q3"
                                                                {{ date('n') >= 7 && date('n') <= 9 ? 'selected' : '' }}>
                                                                Q3 - Third Quarter
                                                            </option>
                                                            <option value="Q4"
                                                                {{ date('n') >= 10 ? 'selected' : '' }}>
                                                                Q4 - Fourth Quarter
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label" for="posting_date">
                                                        Posting Date <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" name="posting_date" id="posting_date"
                                                        class="form-control" value="{{ date('Y-m-d') }}" required />
                                                </div>

                                                <div class="col-md-4"></div>

                                                <div class="col-md-4">
                                                    <label class="form-label" for="brokerage_rate">Brokerage Rate
                                                        (%)</label>
                                                    <input type="number" name="brokerage_rate" id="brokerage_rate"
                                                        class="form-control" step="0.01"
                                                        value="{{ number_format($cover->brokerage_comm_rate, 2) }}"
                                                        data-default="{{ number_format($cover->brokerage_comm_rate, 2) }}"
                                                        min="0" max="100" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <label class="form-label" for="comments">Comments</label>
                                    <textarea name="comments" id="comments" class="form-control resize-none shadow-sm" rows="3"
                                        placeholder="Enter any additional information or remarks" maxlength="2000"></textarea>
                                    <small class="text-muted">
                                        <span id="comments-count">0</span>/2000 characters
                                    </small>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check form-check-lg custom-checkbox">
                                                <input class="form-check-input" type="checkbox" name="show_cedant"
                                                    id="show_cedant" value="1">
                                                <label class="form-check-label" for="show_cedant">
                                                    Show Cedant on Statement
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-check-lg custom-checkbox">
                                                <input class="form-check-input" type="checkbox" name="show_reinsurer"
                                                    id="show_reinsurer" value="1">
                                                <label class="form-check-label" for="show_reinsurer">
                                                    Show Reinsurer on Statement
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-semibold">Statutory Levies</h6>
                                </div>
                                <div class="card-body">
                                    {{-- Premium Levy --}}
                                    <div class="levy-row mb-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="form-check form-check-lg custom-checkbox mb-0">
                                                <input class="form-check-input levy-checkbox" type="checkbox"
                                                    name="compute_premium_tax" id="compute_premium_tax"
                                                    value="1" data-rate="{{ $taxRates['PREMIUM_LEVY'] }}">
                                                <label class="form-check-label" for="compute_premium_tax">
                                                    Premium Levy
                                                </label>
                                            </div>
                                            <div class="levy-rate-input">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="premium_levy" id="premium_levy"
                                                        class="form-control form-control-sm text-end" step="0.01"
                                                        value="{{ $taxRates['PREMIUM_LEVY'] }}" min="0"
                                                        data-default="{{ $taxRates['PREMIUM_LEVY'] }}" max="100"
                                                        style="width: 75px;" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Reinsurance Levy --}}
                                    <div class="levy-row mb-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="form-check form-check-lg custom-checkbox mb-0">
                                                <input class="form-check-input levy-checkbox" type="checkbox"
                                                    name="compute_reinsurance_tax" id="compute_reinsurance_tax"
                                                    value="1" data-rate="{{ $taxRates['REINSURANCE_LEVY'] }}">
                                                <label class="form-check-label" for="compute_reinsurance_tax">
                                                    Reinsurance Levy
                                                </label>
                                            </div>
                                            <div class="levy-rate-input">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="reinsurance_levy"
                                                        id="reinsurance_levy"
                                                        class="form-control form-control-sm text-end" step="0.01"
                                                        value="{{ $taxRates['REINSURANCE_LEVY'] }}" min="0"
                                                        data-default="{{ $taxRates['REINSURANCE_LEVY'] }}"
                                                        max="100" style="width: 75px;" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Withholding Tax --}}
                                    <div class="levy-row mb-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="form-check form-check-lg custom-checkbox mb-0">
                                                <input class="form-check-input levy-checkbox" type="checkbox"
                                                    name="compute_withholding_tax" id="compute_withholding_tax"
                                                    value="1" data-rate="{{ $taxRates['WITHHOLDING_TAX'] }}">
                                                <label class="form-check-label" for="compute_withholding_tax">
                                                    Withholding Tax
                                                </label>
                                            </div>
                                            <div class="levy-rate-input">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="wht_rate" id="wht_rate"
                                                        class="form-control form-control-sm text-end" step="0.01"
                                                        value="{{ $taxRates['WITHHOLDING_TAX'] }}" min="0"
                                                        data-default="{{ $taxRates['WITHHOLDING_TAX'] }}"
                                                        max="100" style="width: 75px;" />
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-semibold">Variable Commission</h6>
                                </div>
                                <div class="card-body">
                                    {{-- Loss Participation --}}
                                    <div class="levy-row mb-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="form-check form-check-lg custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox"
                                                    name="loss_participation" id="loss_participation" value="1">
                                                <label class="form-check-label" for="loss_participation">
                                                    Loss Participation
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sliding Scale Commission --}}
                                    <div class="levy-row mb-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="form-check form-check-lg custom-checkbox mb-0">
                                                <input class="form-check-input" type="checkbox"
                                                    name="sliding_commission" id="sliding_commission" value="1">
                                                <label class="form-check-label" for="sliding_commission">
                                                    Sliding Scale Commission
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                    <strong>Transaction Line Items</strong>
                                    <button type="button" class="btn btn-sm btn-success" id="add-debit-item">
                                        <i class="fas fa-plus me-1"></i> Add Line Item
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0" id="debit-items-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 10%;">Item Code</th>
                                                    <th style="width: 18%;">
                                                        Transaction Type <span class="text-danger">*</span>
                                                    </th>
                                                    <th style="width: 15%;">
                                                        Class Group <span class="text-danger">*</span>
                                                    </th>
                                                    <th style="width: 17%;">Business Class</th>
                                                    <th style="width: 12%;">Commission (%) <span
                                                            class="text-danger">*</span></th>
                                                    <th style="width: 10%;">Ledger</th>
                                                    <th style="width: 13%;">
                                                        Amount <span class="text-danger">*</span>
                                                    </th>
                                                    <th style="width: 5%;" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="debit-items-body">
                                                <tr id="no-items-row">
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                                        No line items added. Click "Add Line Item" to begin.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" id="debit-save-btn" class="btn btn-primary btn-sm">
                        <i class="fas fa-check me-1"></i> Quarterly Figures
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Item Row Template --}}
<template id="debit-item-row-template">
    <tr class="debit-item-row" data-item-index="INDEX">
        <input type="hidden" class="item-type" name="items[INDEX][item_type]" value="" />
        <td>
            <select class="form-select form-select-sm item-code" name="items[INDEX][item_code]">
                <option value="">--</option>
                @forelse ($itemCodes ?? [] as $code => $data)
                    @if (in_array($code, ['IT01', 'IT02']))
                        <option value="{{ $code }}" data-type="{{ $data['type'] }}"
                            data-description="{{ $data['description'] }}">
                            {{ $code }}
                        </option>
                    @endif
                @empty
                    <option value="">No codes available</option>
                @endforelse
            </select>
        </td>
        <td>
            <select name="items[INDEX][description]" class="form-select form-select-sm item-description" required>
                <option value="">-- Select Type --</option>
                @foreach ($itemCodes ?? [] as $code => $data)
                    @if (in_array($code, ['IT01', 'IT02']))
                        <option value="{{ $code }}" data-type="{{ $data['type'] }}"
                            data-code="{{ $code }}">
                            {{ $data['description'] }}
                        </option>
                    @endif
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[INDEX][class_group]" class="form-select form-select-sm item-class-group" required>
                <option value="">-- Select Group --</option>
                @foreach ($classGroups ?? [] as $group)
                    <option value="{{ $group['group_code'] }}">{{ $group['group_name'] }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="items[INDEX][class_name]" class="form-select form-select-sm item-class-name">
                <option value="">-- Class --</option>
                @if (isset($businessClasses))
                    @foreach ($businessClasses as $category => $classes)
                        @foreach ($classes as $code => $name)
                            <option value="{{ $code }}" data-group="{{ $category }}"
                                style="display: none;">
                                {{ $name }}</option>
                        @endforeach
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            <input type="number" name="items[INDEX][line_rate]"
                class="form-control form-control-sm item-line-rate text-end" step="0.01" placeholder="0.00"
                min="0" max="100" required title="Commission rate is required (0-100%)" />
        </td>
        <td>
            <select name="items[INDEX][ledger]" class="form-select form-select-sm item-ledger">
                <option value="">--</option>
                <option value="DR">DR</option>
                <option value="CR">CR</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-amount text-end" placeholder="0.00"
                inputmode="decimal" required />
            <input type="hidden" name="items[INDEX][amount]" class="item-amount-hidden" />
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" title="Remove item">
                <i class="bx bx-trash-alt"></i>
            </button>
        </td>
    </tr>
</template>

<style>
    #createQuarterlyFiguresModal .modal-body {
        max-height: 75vh;
        overflow-y: auto;
    }

    #createQuarterlyFiguresModal .form-label {
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 13px;
    }

    #createQuarterlyFiguresModal .card {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 0px;
    }

    #createQuarterlyFiguresModal .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    #createQuarterlyFiguresModal .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    #createQuarterlyFiguresModal .levy-row {
        padding: 0px;
    }

    #createQuarterlyFiguresModal .levy-row:last-child {
        border-bottom: none;
    }

    #createQuarterlyFiguresModal .levy-rate-input .form-control {
        width: 75px;
        text-align: right;
    }

    #createQuarterlyFiguresModal .table th {
        font-weight: 600;
        font-size: 0.813rem;
        background-color: #f8f9fa;
    }

    #createQuarterlyFiguresModal .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }

    #createQuarterlyFiguresModal .resize-none {
        resize: none;
    }

    #createQuarterlyFiguresModal .debit-item-row {
        transition: background-color 0.2s ease;
    }

    #createQuarterlyFiguresModal .debit-item-row:hover {
        background-color: #f8f9fa;
    }

    #createQuarterlyFiguresModal .debit-item-row.is-debit {
        background-color: #d1e7dd;
    }

    #createQuarterlyFiguresModal .debit-item-row.is-credit {
        background-color: #fff3cd;
    }

    #createQuarterlyFiguresModal .error-field {
        border-color: #dc3545 !important;
    }

    #createQuarterlyFiguresModal .error-message {
        color: #dc3545;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }

    #createQuarterlyFiguresModal .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    #createQuarterlyFiguresModal .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #createQuarterlyFiguresModal .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    #createQuarterlyFiguresModal .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Visual indicator for item type */
    #createQuarterlyFiguresModal .item-type-badge {
        font-size: 0.65rem;
        padding: 0.15rem 0.4rem;
        border-radius: 0.25rem;
        margin-left: 0.25rem;
    }

    #createQuarterlyFiguresModal .item-type-badge.debit {
        background-color: #198754;
        color: white;
    }

    #createQuarterlyFiguresModal .item-type-badge.credit {
        background-color: #ffc107;
        color: #212529;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

@push('script')
    <script>
        (function($) {
            'use strict';

            const QuarterlyFiguresModal = {
                config: {
                    selectors: {
                        modal: '#createQuarterlyFiguresModal',
                        form: '#quarterlyFiguresForm',
                        itemsBody: '#debit-items-body',
                        template: '#debit-item-row-template',
                        addBtn: '#add-debit-item',
                        saveBtn: '#debit-save-btn',
                        totalAmount: '#total-amount',
                        noItemsRow: '#no-items-row',
                        summarySection: '#summary-section',
                        summaryGross: '#summary-gross',
                        summaryDeductions: '#summary-deductions',
                        summaryNet: '#summary-net',
                        commentsField: '#comments',
                        commentsCount: '#comments-count',
                        postingYear: '#posting_year',
                        postingQuarter: '#posting_quarter',
                        postingDate: '#posting_date',
                        brokerageRate: '#brokerage_rate',
                        commissionRate: '#commission_rate',
                        treatyClasses: "#treatyClasses"
                    },
                    classes: {
                        itemRow: '.debit-item-row',
                        removeBtn: '.remove-item-btn',
                        itemAmount: '.item-amount',
                        itemAmountHidden: '.item-amount-hidden',
                        itemDescription: '.item-description',
                        itemCode: '.item-code',
                        itemType: '.item-type',
                        itemClassGroup: '.item-class-group',
                        itemClassName: '.item-class-name',
                        itemLedger: '.item-ledger',
                        itemLineRate: '.item-line-rate',
                        levyCheckbox: '.levy-checkbox',
                        errorField: 'error-field',
                        errorMessage: 'error-message'
                    },
                    debitTypeCodes: ['IT01', 'IT26'],
                    creditTypeCodes: ['IT02', 'IT03', 'IT04', 'IT05', 'IT06', 'IT07', 'IT08'],
                    allowedKeys: [
                        'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
                        'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                        'Home', 'End'
                    ],
                    calcDebounceMs: 150
                },

                state: {
                    itemIndex: 0,
                    isSubmitting: false,
                    validator: null,
                    calcDebounceTimer: null,
                    isDataLocked: false,
                    loadedQuarter: null,
                    defaultMeta: null
                },

                urls: {
                    getQuarterlyByQuarter: '{{ route('cover.get_quarterly_by_quarter') }}'
                },

                $el: {},

                init: function() {
                    this.cacheElements();

                    if (!this.$el.modal.length) {
                        return;
                    }

                    this.state.defaultMeta = this.getDefaultMeta();
                    this.initValidator();
                    this.bindEvents();
                },

                cacheElements: function() {
                    const s = this.config.selectors;

                    this.$el = {
                        modal: $(s.modal),
                        form: $(s.form),
                        itemsBody: $(s.itemsBody),
                        template: $(s.template),
                        addBtn: $(s.addBtn),
                        saveBtn: $(s.saveBtn),
                        totalAmount: $(s.totalAmount),
                        noItemsRow: $(s.noItemsRow),
                        summarySection: $(s.summarySection),
                        summaryGross: $(s.summaryGross),
                        summaryDeductions: $(s.summaryDeductions),
                        summaryNet: $(s.summaryNet),
                        commentsField: $(s.commentsField),
                        commentsCount: $(s.commentsCount),
                        postingYear: $(s.postingYear),
                        postingQuarter: $(s.postingQuarter),
                        postingDate: $(s.postingDate),
                        brokerageRate: $(s.brokerageRate),
                        commissionRate: $(s.commissionRate)
                    };
                },

                initValidator: function() {
                    const self = this;

                    $.validator.addMethod('quarterMatchesDate', function() {
                        const postingDate = self.$el.postingDate.val();
                        const postingQuarter = self.$el.postingQuarter.val();

                        if (!postingDate || !postingQuarter) return true;

                        const month = new Date(postingDate).getMonth() + 1;
                        const expectedQuarter = self.getQuarterFromMonth(month);

                        return postingQuarter === expectedQuarter;
                    }, 'Quarter does not match posting date');

                    this.state.validator = this.$el.form.validate({
                        rules: {
                            posting_year: {
                                required: true
                            },
                            posting_quarter: {
                                required: true,
                                quarterMatchesDate: true
                            },
                            posting_date: {
                                required: true,
                                date: true
                            },
                            brokerage_rate: {
                                number: true,
                                min: 0,
                                max: 100
                            },
                            commission_rate: {
                                number: true,
                                min: 0,
                                max: 100
                            }
                        },
                        messages: {
                            posting_year: {
                                required: 'Please select a year'
                            },
                            posting_quarter: {
                                required: 'Please select a quarter'
                            },
                            posting_date: {
                                required: 'Please enter a date',
                                date: 'Please enter a valid date'
                            },
                            brokerage_rate: {
                                number: 'Please enter a valid number',
                                min: 'Minimum value is 0%',
                                max: 'Maximum value is 100%'
                            },
                            commission_rate: {
                                number: 'Please enter a valid number',
                                min: 'Minimum value is 0%',
                                max: 'Maximum value is 100%'
                            }
                        },
                        errorElement: 'span',
                        errorClass: this.config.classes.errorMessage,
                        errorPlacement: function(error, element) {
                            if (element.closest(self.config.classes.itemRow).length) return;
                            error.insertAfter(element);
                        },
                        highlight: function(element) {
                            $(element).addClass(self.config.classes.errorField);
                        },
                        unhighlight: function(element) {
                            $(element).removeClass(self.config.classes.errorField);
                        },
                        submitHandler: function() {
                            self.handleSubmit();
                            return false;
                        }
                    });
                },

                bindEvents: function() {
                    const self = this;
                    const c = this.config.classes;

                    this.$el.addBtn.on('click', function(e) {
                        e.preventDefault();
                        self.addItem();
                    });

                    this.$el.itemsBody.on('click', c.removeBtn, function(e) {
                        e.preventDefault();
                        self.removeItem($(this));
                    });

                    this.$el.itemsBody
                        .on('input', c.itemAmount, function() {
                            self.formatAmountInput($(this));
                            self.debouncedCalculate();
                        })
                        .on('blur', c.itemAmount, function() {
                            self.formatAmountOnBlur($(this));
                        })
                        .on('focus', c.itemAmount, function() {
                            $(this).select();
                        })
                        .on('keydown', c.itemAmount, function(e) {
                            return self.validateAmountKeypress(e);
                        })
                        .on('paste', c.itemAmount, function(e) {
                            self.handleAmountPaste(e, $(this));
                        });

                    this.$el.itemsBody
                        .on('change', c.itemCode, function() {
                            self.syncFromItemCode($(this));
                        })
                        .on('change', c.itemDescription, function() {
                            self.syncFromDescription($(this));
                        })
                        .on('change', c.itemLedger, function() {
                            self.syncItemTypeFromLedger($(this));
                        });

                    this.$el.itemsBody.on('change', c.itemClassGroup, function() {
                        self.filterBusinessClassGroup($(this));
                    });

                    this.$el.itemsBody.on('change', c.itemClassName, function() {
                        self.filterBusinessClasses($(this));
                    });

                    $(c.levyCheckbox).on('change', function() {
                        self.debouncedCalculate();
                    });

                    $('#premium_levy').on('input change', function() {
                        var rate = $(this).val() || 0;
                        $('#compute_premium_tax').attr('data-rate', rate);
                        self.debouncedCalculate();
                    });

                    $('#reinsurance_levy').on('input change', function() {
                        var rate = $(this).val() || 0;
                        $('#compute_reinsurance_tax').attr('data-rate', rate);
                        self.debouncedCalculate();
                    });

                    $('#wht_rate').on('input change', function() {
                        var rate = $(this).val() || 0;
                        $('#compute_withholding_tax').attr('data-rate', rate);
                        self.debouncedCalculate();
                    });

                    $('#loss_participation').on('change', function() {
                        self.debouncedCalculate();
                    });

                    $('#sliding_commission').on('change', function() {
                        self.debouncedCalculate();
                    });

                    this.$el.brokerageRate.add(this.$el.commissionRate).on('input change', function() {
                        self.debouncedCalculate();
                    });

                    this.$el.commentsField.on('input', function() {
                        self.$el.commentsCount.text($(this).val().length);
                    });

                    this.$el.postingDate.on('change', function() {
                        self.syncQuarterToDate();
                    });

                    this.$el.postingQuarter.on('change', function() {
                        self.syncDateToQuarter();
                        const quarter = $(this).val();
                        if (quarter) {
                            self.fetchQuarterlyData(quarter);
                        } else {
                            self.setFieldsLocked(false, true);
                        }
                    });

                    this.$el.postingYear.on('change', function() {
                        self.syncDateToQuarter();
                        const quarter = self.$el.postingQuarter.val();
                        if (quarter) {
                            self.fetchQuarterlyData(quarter);
                        }
                    });

                    this.$el.modal
                        .on('hidden.bs.modal', function() {
                            self.resetForm();
                        })
                        .on('shown.bs.modal', function() {
                            self.$el.postingYear.trigger('focus');
                            const quarter = self.$el.postingQuarter.val();
                            if (quarter) {
                                self.fetchQuarterlyData(quarter);
                            }
                        });
                },

                fetchQuarterlyData: function(quarter) {
                    const self = this;
                    const coverNo = $('input[name="cover_no"]').val();
                    const postingYear = this.$el.postingYear.val();

                    if (!coverNo || !quarter) {
                        return;
                    }

                    this.$el.saveBtn.prop('disabled', true);
                    this.$el.addBtn.prop('disabled', true);

                    $.ajax({
                            url: this.urls.getQuarterlyByQuarter,
                            method: 'GET',
                            data: {
                                cover_no: coverNo,
                                quarter: quarter,
                                posting_year: postingYear
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .done(function(response) {
                            const responseMeta = response && response.data ? response.data.meta : null;

                            if (response.success && response.has_data) {
                                self.populateItemsFromData(response.data);
                                self.applyMeta(responseMeta);

                                self.setFieldsLocked(true);
                                self.state.loadedQuarter = quarter;
                            } else if (response.success && response.prefill_from_previous && response
                                .data && Array.isArray(response.data.items) && response.data.items
                                .length) {
                                self.populateItemsFromData(response.data);
                                self.applyMeta(responseMeta);
                                self.setFieldsLocked(false);
                                self.applyPreviousQuarterPrefillMode(response.source_quarter, response
                                    .source_year);
                                self.state.loadedQuarter = null;
                            } else {
                                self.clearTransactionItems();
                                self.applyMeta(responseMeta);
                                self.setFieldsLocked(false, true);
                                self.state.loadedQuarter = null;
                            }
                        })
                        .fail(function(xhr, status, error) {
                            console.error('Error fetching quarterly data:', error);
                            self.setFieldsLocked(false, true);
                            self.state.loadedQuarter = null;
                        })
                        .always(function() {
                            self.$el.saveBtn.prop('disabled', false);
                            if (!self.state.isDataLocked) {
                                self.$el.addBtn.prop('disabled', false);
                            }
                        });
                },

                clearTransactionItems: function() {
                    this.$el.itemsBody.find(this.config.classes.itemRow).remove();

                    this.$el.noItemsRow.show();

                    this.state.itemIndex = 0;

                    this.$el.totalAmount.text('0.00');
                    this.$el.summaryGross.text('0.00');
                    this.$el.summaryDeductions.text('0.00');
                    this.$el.summaryNet.text('0.00');

                    this.updateSummaryVisibility();

                    // Reset metadata fields to defaults if not locked
                    if (!this.state.isDataLocked) {
                        this.applyMeta(null);
                    }
                },

                getDefaultMeta: function() {
                    return {
                        compute_premium_tax: false,
                        compute_reinsurance_tax: false,
                        compute_withholding_tax: false,
                        loss_participation: false,
                        sliding_commission: false,
                        brokerage_rate: this.parseNumberOrZero($('#brokerage_rate').data('default')),
                        premium_levy: this.parseNumberOrZero($('#premium_levy').data('default')),
                        reinsurance_levy: this.parseNumberOrZero($('#reinsurance_levy').data('default')),
                        withholding_tax: this.parseNumberOrZero($('#wht_rate').data('default')),
                        comments: '',
                        show_cedant: false,
                        show_reinsurer: false
                    };
                },

                applyMeta: function(meta) {
                    const defaults = this.state.defaultMeta || this.getDefaultMeta();
                    const resolved = Object.assign({}, defaults, meta || {});

                    $('#compute_premium_tax').prop('checked', !!resolved.compute_premium_tax).trigger('change');
                    $('#compute_reinsurance_tax').prop('checked', !!resolved.compute_reinsurance_tax).trigger(
                        'change');
                    $('#compute_withholding_tax').prop('checked', !!resolved.compute_withholding_tax).trigger(
                        'change');

                    const premiumLevy = this.parseNumberOrZero(resolved.premium_levy);
                    const reinsuranceLevy = this.parseNumberOrZero(resolved.reinsurance_levy);
                    const withholdingTax = this.parseNumberOrZero(resolved.withholding_tax);
                    const brokerageRate = (meta && meta.brokerage_rate !== undefined && meta
                            .brokerage_rate !== null) ?
                        this.parseNumberOrZero(meta.brokerage_rate) :
                        this.parseNumberOrZero(defaults.brokerage_rate);

                    $('#premium_levy').val(premiumLevy.toFixed(2)).trigger('change');
                    $('#reinsurance_levy').val(reinsuranceLevy.toFixed(2)).trigger('change');
                    $('#wht_rate').val(withholdingTax.toFixed(2)).trigger('change');

                    $('#loss_participation').prop('checked', !!resolved.loss_participation).trigger('change');
                    $('#sliding_commission').prop('checked', !!resolved.sliding_commission).trigger('change');

                    $('#brokerage_rate').val(brokerageRate.toFixed(2));

                    const comments = String(resolved.comments || '');
                    $('#comments').val(comments);
                    this.$el.commentsCount.text(comments.length);
                    $('#show_cedant').prop('checked', !!resolved.show_cedant);
                    $('#show_reinsurer').prop('checked', !!resolved.show_reinsurer);
                },

                parseNumberOrZero: function(value) {
                    const parsed = parseFloat(value);
                    return Number.isFinite(parsed) ? parsed : 0;
                },

                populateItemsFromData: function(data) {
                    const self = this;
                    const items = data.items || [];

                    this.$el.itemsBody.find(this.config.classes.itemRow).remove();
                    this.state.itemIndex = 0;

                    if (items.length === 0) {
                        this.$el.noItemsRow.show();
                        return;
                    }

                    this.$el.noItemsRow.hide();

                    items.forEach(function(item) {
                        const html = self.$el.template.html().replace(/INDEX/g, self.state.itemIndex);
                        self.$el.itemsBody.append(html);

                        const $newRow = self.$el.itemsBody.find(self.config.classes.itemRow).last();

                        let itemType = item.item_type;
                        let ledger = item.ledger;
                        let itemCode = item.item_code;
                        let description = item.item_code;

                        if (!itemType && ledger) {
                            itemType = ledger === 'DR' ? 'DEBIT' : 'CREDIT';
                        }

                        if (!ledger && itemType) {
                            ledger = itemType === 'DEBIT' ? 'DR' : 'CR';
                        }
                        if (!itemType && !ledger) {
                            itemType = 'DEBIT';
                            ledger = 'DR';
                        }

                        if (!itemCode) {
                            itemCode = itemType === 'DEBIT' ? 'IT01' : 'IT02';
                        }

                        if (!description) {
                            description = itemCode;
                        }

                        const $itemCodeSelect = $newRow.find(self.config.classes.itemCode);
                        $itemCodeSelect.val(itemCode);

                        const $descriptionSelect = $newRow.find(self.config.classes.itemDescription);
                        $descriptionSelect.val(description);

                        const $itemTypeHidden = $newRow.find(self.config.classes.itemType);
                        $itemTypeHidden.val(itemType);

                        if (itemType === 'DEBIT' || ledger === 'DR') {
                            $newRow.removeClass('is-credit').addClass('is-debit');
                        } else {
                            $newRow.removeClass('is-debit').addClass('is-credit');
                        }

                        $newRow.find(self.config.classes.itemLedger).val(ledger);

                        const $classGroupSelect = $newRow.find(self.config.classes.itemClassGroup);
                        const classGroup = item.class_group;

                        if (classGroup) {
                            $classGroupSelect.val(classGroup);

                            const $classSelect = $newRow.find(self.config.classes.itemClassName);
                            $classSelect.find('option').each(function() {
                                const $option = $(this);
                                const optionValue = $option.val();

                                if (optionValue === '') {
                                    $option.show();
                                } else {
                                    const optionGroup = $option.data('group');
                                    if (Number(optionGroup) === Number(classGroup)) {
                                        $option.show();
                                    } else {
                                        $option.hide();
                                    }
                                }
                            });

                            if (item.class_name) {
                                $classSelect.val(item.class_name);
                            } else {
                                const $firstVisible = $classSelect.find('option:visible').not(
                                    '[value=""]').first();
                                if ($firstVisible.length) {
                                    $classSelect.val($firstVisible.val());
                                }
                            }
                        } else {
                            const $firstGroup = $classGroupSelect.find('option').not('[value=""]')
                                .first();
                            if ($firstGroup.length) {
                                $classGroupSelect.val($firstGroup.val());
                                self.filterBusinessClassGroup($classGroupSelect);
                            }
                        }

                        const lineRate = item.line_rate ?? 0;
                        $newRow.find(self.config.classes.itemLineRate).val(Number(lineRate).toFixed(2));

                        if (itemType === 'CREDIT') {
                            $newRow.find(self.config.classes.itemLineRate).val('0').prop('disabled',
                                true);
                        }

                        const amount = item.amount ?? 0;
                        const formattedAmount = self.formatCurrency(amount);
                        $newRow.find(self.config.classes.itemAmount).val(formattedAmount);
                        $newRow.find(self.config.classes.itemAmountHidden).val(amount);

                        self.state.itemIndex++;
                    });

                    this.calculateTotals();
                    this.updateSummaryVisibility();
                },

                applyPreviousQuarterPrefillMode: function(sourceQuarter, sourceYear) {
                    const self = this;
                    this.state.isDataLocked = false;
                    this.$el.addBtn.prop('disabled', true);

                    this.$el.itemsBody.find(this.config.classes.itemRow).each(function() {
                        const $row = $(this);
                        $row.find('input, select').prop('disabled', true);

                        $row.find(self.config.classes.itemLineRate).prop('disabled', false);
                        $row.find(self.config.classes.itemAmount).prop('disabled', false);
                        $row.find(self.config.classes.itemAmountHidden).prop('disabled', false);
                        $row.find(self.config.classes.removeBtn).hide();

                        $row.find(self.config.classes.itemAmount).val('');
                        $row.find(self.config.classes.itemAmountHidden).val('');
                    });

                    $('#quarterly-data-loaded-info').remove();
                    $('#quarterly-data-prefill-info').remove();

                    const sourceLabel = [sourceQuarter, sourceYear].filter(Boolean).join(' ');
                    const prefillHtml = `
                        <div id="quarterly-data-prefill-info" class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> No saved data found for this quarter. Line items were copied from ${sourceLabel}.
                            Only commission and amount are editable, and amount has been cleared.
                        </div>
                    `;
                    $(this.config.selectors.itemsBody).closest('.card').before(prefillHtml);

                    this.calculateTotals();
                    this.updateSummaryVisibility();
                },

                setFieldsLocked: function(locked, notInserted = false) {
                    const self = this;
                    this.state.isDataLocked = locked;

                    this.$el.addBtn.prop('disabled', locked);

                    this.$el.itemsBody.find(this.config.classes.itemRow).each(function() {
                        const $row = $(this);

                        $row.find('input, select').prop('disabled', locked);

                        if (notInserted) {
                            $row.find('input, select').prop('disabled', true);
                            $row.find(self.config.classes.itemLineRate).prop('disabled', false);
                            $row.find(self.config.classes.itemLedger).prop('disabled', false);
                            $row.find(self.config.classes.itemAmount).prop('disabled', false);
                            $row.find(self.config.classes.itemAmountHidden).prop('disabled', false);

                            $row.find(self.config.classes.itemLineRate).val('');
                            $row.find(self.config.classes.itemAmount).val('');
                        }


                        if (locked) {
                            let $hiddenCode = $row.find('.item-code-hidden');
                            if (!$hiddenCode.length) {
                                const codeName = $row.find(self.config.classes.itemCode).attr('name');
                                $hiddenCode = $('<input type="hidden" class="item-code-hidden">').attr(
                                    'name', codeName);
                                $row.append($hiddenCode);
                            }
                            $hiddenCode.val($row.find(self.config.classes.itemCode).val());

                            $row.find(self.config.classes.removeBtn).hide();
                        } else {
                            $row.find('.item-code-hidden').remove();
                            $row.find(self.config.classes.removeBtn).hide();
                        }
                    });

                    const otherFields = [
                        '#brokerage_rate',
                        '#premium_levy',
                        '#reinsurance_levy',
                        '#wht_rate',
                        '#compute_premium_tax',
                        '#compute_reinsurance_tax',
                        '#compute_withholding_tax',
                        '#loss_participation',
                        '#sliding_commission',
                        '#comments',
                        '#show_cedant',
                        '#show_reinsurer'
                    ];

                    otherFields.forEach(function(selector) {
                        $(selector).prop('disabled', locked);
                    });

                    const $infoMessage = $('#quarterly-data-loaded-info');
                    if (locked) {
                        if (!$infoMessage.length) {
                            const infoHtml = `
                                <div id="quarterly-data-loaded-info" class="alert alert-info alert-dismissible fade show mt-2" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Note:</strong> Data has been loaded from existing quarterly records.
                                    Fields are disabled. Select a different quarter to enter new data.
                                </div>
                            `;
                            $(this.config.selectors.itemsBody).closest('.card').before(infoHtml);
                        }
                    } else {
                        $infoMessage.remove();
                    }
                    $('#quarterly-data-prefill-info').remove();
                },

                addItem: function() {
                    if (!this.$el.template.length) {
                        this.notify('Template not found', 'error');
                        return;
                    }

                    this.$el.noItemsRow.hide();

                    const html = this.$el.template.html().replace(/INDEX/g, this.state.itemIndex);
                    this.$el.itemsBody.append(html);

                    const $newRow = this.$el.itemsBody.find(this.config.classes.itemRow).last();
                    $newRow.find(this.config.classes.itemDescription).trigger('focus');

                    const $amountInput = $newRow.find(this.config.classes.itemAmount);
                    const $hiddenInput = $newRow.find(this.config.classes.itemAmountHidden);
                    $hiddenInput.val('0.00');

                    $newRow.find(this.config.classes.itemDescription).trigger('focus');

                    this.state.itemIndex++;
                    this.updateSummaryVisibility();
                },

                removeItem: function($btn) {
                    const self = this;
                    const $row = $btn.closest(this.config.classes.itemRow);

                    if (!$row.length) return;

                    const confirmRemove = function() {
                        self.performRemoveItem($row);
                    };

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Remove Item?',
                            text: 'This line item will be removed.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            confirmButtonText: 'Yes, remove it'
                        }).then(function(result) {
                            if (result.isConfirmed) confirmRemove();
                        });
                    } else if (confirm('Remove this line item?')) {
                        confirmRemove();
                    }
                },

                performRemoveItem: function($row) {
                    const self = this;

                    $row.fadeOut(200, function() {
                        $(this).remove();
                        self.calculateTotals();
                        self.updateSummaryVisibility();
                        self.refreshAllClassDropdowns();

                        if (self.$el.itemsBody.find(self.config.classes.itemRow).length === 0) {
                            self.$el.noItemsRow.fadeIn(150);
                        }
                    });
                },

                syncFromItemCode: function($itemCode) {
                    const $row = $itemCode.closest(this.config.classes.itemRow);
                    const code = $itemCode.val();
                    const $selectedOption = $itemCode.find('option:selected');
                    const itemType = $selectedOption.data('type') || '';

                    $row.find(this.config.classes.itemDescription).val(code);
                    this.setItemTypeAndLedger($row, code, itemType);
                },

                syncFromDescription: function($description) {
                    const $row = $description.closest(this.config.classes.itemRow);
                    const code = $description.val();
                    const $selectedOption = $description.find('option:selected');
                    const itemType = $selectedOption.data('type') || '';

                    $row.find(this.config.classes.itemCode).val(code);

                    this.setItemTypeAndLedger($row, code, itemType);
                },

                setItemTypeAndLedger: function($row, code, itemType) {
                    const $itemTypeField = $row.find(this.config.classes.itemType);
                    const $ledger = $row.find(this.config.classes.itemLedger);
                    const $commRate = $row.find(this.config.classes.itemLineRate);

                    let resolvedType = itemType;

                    if (!resolvedType && code) {
                        if (this.config.debitTypeCodes.includes(code)) {
                            resolvedType = 'DEBIT';
                        } else if (this.config.creditTypeCodes.includes(code)) {
                            resolvedType = 'CREDIT';
                        } else {
                            return;
                        }
                    }

                    $itemTypeField.val(resolvedType);

                    if (resolvedType === 'DEBIT') {
                        $ledger.val('DR');
                        $commRate.val('');
                        $commRate.prop('disabled', false);
                        $row.removeClass('is-credit').addClass('is-debit');
                    } else if (resolvedType === 'CREDIT') {
                        $ledger.val('CR');
                        $commRate.val('0');
                        $commRate.prop('disabled', true);
                        $row.removeClass('is-debit').addClass('is-credit');
                    } else {
                        $row.removeClass('is-debit is-credit');
                    }

                    this.refreshAllClassDropdowns();
                },

                syncItemTypeFromLedger: function($ledger) {
                    const $row = $ledger.closest(this.config.classes.itemRow);
                    const $itemTypeField = $row.find(this.config.classes.itemType);
                    const ledgerValue = $ledger.val();

                    if (ledgerValue === 'DR') {
                        $itemTypeField.val('DEBIT');
                        $row.removeClass('is-credit').addClass('is-debit');
                    } else if (ledgerValue === 'CR') {
                        $itemTypeField.val('CREDIT');
                        $row.removeClass('is-debit').addClass('is-credit');
                    }

                    this.debouncedCalculate();
                    this.refreshAllClassDropdowns();
                },

                getSelectedCombinations: function($excludeRow) {
                    const self = this;
                    const combinations = [];

                    this.$el.itemsBody.find(this.config.classes.itemRow).each(function() {
                        const $row = $(this);
                        if ($excludeRow && $row.is($excludeRow)) {
                            return;
                        }
                        const typeValue = $row.find(self.config.classes.itemCode).val();
                        const classValue = $row.find(self.config.classes.itemClassName).val();

                        if (typeValue && classValue) {
                            combinations.push(typeValue + '|' + classValue);
                        }
                    });

                    return combinations;
                },

                getValidTreatyClasses: function() {
                    const treatyClassesJson = $(this.config.selectors.treatyClasses).val();
                    if (!treatyClassesJson) return [];

                    try {
                        const treatyClasses = JSON.parse(treatyClassesJson) ?? [];
                        return treatyClasses.map(tc => String(tc.class_code));
                    } catch (e) {
                        console.warn('Failed to parse treatyClasses:', e);
                        return [];
                    }
                },

                filterBusinessClassGroup: function($classGroup) {
                    const $row = $classGroup.closest(this.config.classes.itemRow);
                    const selectedGroup = $classGroup.val();
                    const $classSelect = $row.find(this.config.classes.itemClassName);
                    const selectedType = $row.find(this.config.classes.itemCode).val();

                    $classSelect.val('');

                    if (!selectedGroup) {
                        $classSelect.find('option').each(function() {
                            const $option = $(this);
                            if ($option.val() !== '') {
                                $option.hide();
                            }
                        });
                        return;
                    }

                    const selectedCombinations = this.getSelectedCombinations($row);
                    const validTreatyClasses = this.getValidTreatyClasses();
                    let hasVisibleOptions = false;

                    $classSelect.find('option').each(function() {
                        const $option = $(this);
                        const optionValue = $option.val();

                        if (optionValue === '') {
                            $option.show();
                            return;
                        }

                        const optionGroup = $option.data('group');
                        const isInGroup = Number(optionGroup) === Number(selectedGroup);

                        const comboKey = (selectedType || '') + '|' + optionValue;
                        const isAlreadySelected = selectedType && selectedCombinations.includes(
                            comboKey);

                        const isValidTreatyClass = validTreatyClasses.length === 0 || validTreatyClasses
                            .includes(String(optionValue));

                        if (isInGroup && !isAlreadySelected && isValidTreatyClass) {
                            $option.show();
                            hasVisibleOptions = true;
                        } else {
                            $option.hide();
                        }
                    });

                    if (hasVisibleOptions) {
                        const visibleOptions = $classSelect.find('option:visible').not('[value=""]');
                        if (visibleOptions.length === 1) {
                            $classSelect.val(visibleOptions.val());
                            this.filterBusinessClasses($classSelect);
                        }
                    }
                },

                refreshAllClassDropdowns: function() {
                    const self = this;
                    const validTreatyClasses = this.getValidTreatyClasses();

                    this.$el.itemsBody.find(this.config.classes.itemRow).each(function() {
                        const $row = $(this);
                        const $classGroup = $row.find(self.config.classes.itemClassGroup);
                        const $classSelect = $row.find(self.config.classes.itemClassName);
                        const selectedType = $row.find(self.config.classes.itemCode).val();
                        const currentValue = $classSelect.val();
                        const selectedGroup = $classGroup.val();

                        if (!selectedGroup) return;

                        const selectedCombinations = self.getSelectedCombinations($row);

                        $classSelect.find('option').each(function() {
                            const $option = $(this);
                            const optionValue = $option.val();

                            if (optionValue === '') {
                                $option.show();
                                return;
                            }

                            const optionGroup = $option.data('group');
                            const isInGroup = Number(optionGroup) === Number(selectedGroup);

                            const comboKey = (selectedType || '') + '|' + optionValue;
                            const isAlreadySelected = selectedType && selectedCombinations
                                .includes(comboKey);

                            const isCurrentValue = optionValue === currentValue;
                            const isValidTreatyClass = validTreatyClasses.length === 0 ||
                                validTreatyClasses.includes(String(optionValue));

                            if (isInGroup && (!isAlreadySelected || isCurrentValue) &&
                                isValidTreatyClass) {
                                $option.show();
                            } else {
                                $option.hide();
                            }
                        });
                    });
                },

                filterBusinessClasses: function($classType) {
                    const $row = $classType.closest(this.config.classes.itemRow);
                    const treatyClasses = $(this.config.selectors.treatyClasses).val();
                    const $commRate = $row.find(this.config.classes.itemLineRate);
                    const classItem = $classType.val();

                    if (!classItem) {
                        $commRate.val('');
                        this.refreshAllClassDropdowns();
                        return;
                    }

                    const result = JSON.parse(treatyClasses) ?? [];
                    const comm = result.find((x) => Number(x.class_code) === Number(classItem));

                    const $itemTypeField = $row.find(this.config.classes.itemType);

                    if ($itemTypeField.val() === 'DEBIT') {
                        $commRate.val(comm?.commission ?? '');
                    } else {
                        $commRate.val('0');
                    }

                    this.refreshAllClassDropdowns();
                },

                formatAmountInput: function($input) {
                    let value = $input.val();
                    const cursorPos = $input[0].selectionStart;
                    const originalLength = value.length;

                    value = value.replace(/[^0-9.]/g, '');

                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }

                    if (parts.length === 2 && parts[1].length > 2) {
                        value = parts[0] + '.' + parts[1].substring(0, 2);
                    }

                    let formattedValue;
                    if (value.includes('.')) {
                        const intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        formattedValue = intPart + '.' + (parts[1] || '');
                    } else if (value) {
                        formattedValue = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    } else {
                        formattedValue = '';
                    }

                    $input.val(formattedValue);

                    const newLength = formattedValue.length;
                    const diff = newLength - originalLength;
                    const newCursorPos = Math.max(0, cursorPos + diff);

                    if ($input.is(':focus')) {
                        $input[0].setSelectionRange(newCursorPos, newCursorPos);
                    }

                    this.syncHiddenAmount($input);
                },

                formatAmountOnBlur: function($input) {
                    const value = this.parseFormattedNumber($input.val());

                    if (value > 0) {
                        $input.val(this.formatCurrency(value));
                    } else {
                        $input.val('0');
                    }

                    this.syncHiddenAmount($input);
                },

                syncHiddenAmount: function($input) {
                    const $row = $input.closest(this.config.classes.itemRow);
                    const $hidden = $row.find(this.config.classes.itemAmountHidden);
                    const numericValue = this.parseFormattedNumber($input.val());

                    $hidden.val(numericValue > 0 ? numericValue.toFixed(2) : '');
                },

                handleAmountPaste: function(e, $input) {
                    e.preventDefault();

                    const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
                    const numericValue = this.parseFormattedNumber(pastedText);

                    if (!isNaN(numericValue) && numericValue >= 0) {
                        $input.val(this.formatCurrency(numericValue));
                        this.syncHiddenAmount($input);
                        this.debouncedCalculate();
                    }
                },

                validateAmountKeypress: function(e) {
                    const key = e.key;
                    const $input = $(e.target);
                    const value = $input.val();

                    if (this.config.allowedKeys.includes(key)) return true;
                    if (e.ctrlKey || e.metaKey) return true;
                    if (key === '.') return !value.includes('.');
                    if (/^[0-9]$/.test(key)) return true;

                    e.preventDefault();
                    return false;
                },

                parseFormattedNumber: function(formattedValue) {
                    if (!formattedValue) return 0;

                    const cleaned = String(formattedValue).replace(/[^0-9.-]/g, '');
                    const parsed = parseFloat(cleaned);

                    if (isNaN(parsed) || !isFinite(parsed)) {
                        console.warn('Invalid number:', formattedValue);
                        return 0;
                    }

                    return Math.max(0, parsed);
                },

                formatCurrency: function(amount) {
                    if (amount === null || amount === undefined || amount === '') return '';

                    const num = parseFloat(amount);
                    if (isNaN(num)) return '';

                    return num.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                debouncedCalculate: function() {
                    const self = this;

                    clearTimeout(this.state.calcDebounceTimer);
                    this.state.calcDebounceTimer = setTimeout(function() {
                        self.calculateTotals();
                    }, this.config.calcDebounceMs);
                },

                calculateTotals: function() {
                    const self = this;
                    let grossAmount = 0;
                    let creditAmount = 0;

                    this.$el.itemsBody.find(this.config.classes.itemRow).each(function() {
                        const $row = $(this);
                        const amount = self.parseFormattedNumber($row.find('.item-amount').val());
                        const itemType = $row.find('.item-type').val();
                        const ledger = $row.find('.item-ledger').val();

                        const isDebit = itemType === 'DEBIT' || (!itemType && ledger === 'DR');

                        if (isDebit) {
                            grossAmount += amount;
                        } else {
                            creditAmount += amount;
                        }
                    });

                    const brokerageRate = parseFloat(this.$el.brokerageRate.val()) || 0;
                    const brokerageAmount = grossAmount * (brokerageRate / 100);

                    let levyAmount = 0;
                    const premiumLevyRate = parseFloat($('#premium_levy').val()) || 0;
                    const reinsuranceLevyRate = parseFloat($('#reinsurance_levy').val()) || 0;

                    if (premiumLevyRate > 0) {
                        levyAmount += grossAmount * (premiumLevyRate / 100);
                    }
                    if (reinsuranceLevyRate > 0) {
                        levyAmount += grossAmount * (reinsuranceLevyRate / 100);
                    }

                    const totalDeductions = brokerageAmount + levyAmount + creditAmount;
                    const netAmount = grossAmount - totalDeductions;

                    this.$el.totalAmount.text(this.formatCurrency(grossAmount));
                    this.$el.summaryGross.text(this.formatCurrency(grossAmount));
                    this.$el.summaryDeductions.text(this.formatCurrency(totalDeductions));
                    this.$el.summaryNet.text(this.formatCurrency(netAmount));

                    this.updateSummaryVisibility();
                },

                updateSummaryVisibility: function() {
                    const hasItems = this.$el.itemsBody.find(this.config.classes.itemRow).length > 0;
                    this.$el.summarySection.toggle(hasItems);
                },

                syncDateToQuarter: function() {
                    const quarter = this.$el.postingQuarter.val();
                    const year = this.$el.postingYear.val();

                    if (!quarter || !year) return;

                    const today = new Date();
                    const currentYear = today.getFullYear();
                    const currentMonth = today.getMonth() + 1;
                    const currentQuarter = this.getQuarterFromMonth(currentMonth);

                    let targetDate = '';

                    if (parseInt(year) === currentYear && quarter === currentQuarter) {
                        // If it's the current quarter of current year, use today's date
                        const y = today.getFullYear();
                        const m = String(today.getMonth() + 1).padStart(2, '0');
                        const d = String(today.getDate()).padStart(2, '0');
                        targetDate = `${y}-${m}-${d}`;
                    } else {
                        // Otherwise, use the last day of the quarter
                        const quarterEndDates = {
                            'Q1': '-03-31',
                            'Q2': '-06-30',
                            'Q3': '-09-30',
                            'Q4': '-12-31'
                        };
                        targetDate = year + quarterEndDates[quarter];
                    }

                    if (targetDate) {
                        this.$el.postingDate.val(targetDate);
                        this.validateQuarterDate();
                    }
                },

                syncQuarterToDate: function() {
                    const dateVal = this.$el.postingDate.val();
                    if (!dateVal) return;

                    const date = new Date(dateVal);
                    if (isNaN(date.getTime())) return;

                    const month = date.getMonth() + 1;
                    const quarter = this.getQuarterFromMonth(month);

                    this.$el.postingQuarter.val(quarter);
                    this.validateQuarterDate();
                },

                getQuarterFromMonth: function(month) {
                    if (month <= 3) return 'Q1';
                    if (month <= 6) return 'Q2';
                    if (month <= 9) return 'Q3';
                    return 'Q4';
                },

                validateQuarterDate: function() {
                    const postingDate = this.$el.postingDate.val();
                    const postingQuarter = this.$el.postingQuarter.val();

                    if (!postingDate || !postingQuarter) return true;

                    const month = new Date(postingDate).getMonth() + 1;
                    const expectedQuarter = this.getQuarterFromMonth(month);

                    if (postingQuarter !== expectedQuarter) {
                        return false;
                    }
                    return true;
                },

                validateLineItems: function() {
                    const self = this;
                    const $rows = this.$el.itemsBody.find(this.config.classes.itemRow);
                    const errorClass = this.config.classes.errorField;

                    if ($rows.length === 0) {
                        this.notify('Please add at least one line item', 'warning');
                        return false;
                    }

                    let valid = true;
                    let totalAmount = 0;
                    const errors = [];

                    $rows.each(function(index) {
                        const $row = $(this);
                        const $description = $row.find('.item-description');
                        const $amount = $row.find('.item-amount');
                        const $commissionRate = $row.find('.item-line-rate');
                        const $itemType = $row.find('.item-type');
                        const $classGroup = $row.find('.item-class-group');

                        const description = $description.val();
                        const amount = self.parseFormattedNumber($amount.val());
                        const commissionRateValue = $commissionRate.val();
                        const itemType = $itemType.val();
                        const classGroup = $classGroup.val()

                        if (!description) {
                            $description.addClass(errorClass);
                            errors.push(`Row ${index + 1}: Transaction type is required`);
                            valid = false;
                        } else {
                            $description.removeClass(errorClass);
                        }

                        if (!classGroup) {
                            $classGroup.addClass(errorClass);
                            errors.push(`Row ${index + 1}: Class group is required`);
                            valid = false;
                        } else {
                            $classGroup.removeClass(errorClass);
                        }

                        if (amount <= 0) {
                            $amount.addClass(errorClass);
                            errors.push(`Row ${index + 1}: Amount must be greater than 0`);
                            valid = false;
                        } else {
                            $amount.removeClass(errorClass);
                            totalAmount += amount;
                        }

                        const commissionRate = parseFloat(commissionRateValue);

                        if (!commissionRateValue || commissionRateValue.trim() === '') {
                            $commissionRate.addClass(errorClass);
                            errors.push(`Row ${index + 1}: Commission rate is required`);
                            valid = false;
                        } else if (commissionRate < 0) {
                            $commissionRate.addClass(errorClass);
                            errors.push(`Row ${index + 1}: Commission rate cannot be negative`);
                            valid = false;
                        } else if (commissionRate > 100) {
                            $commissionRate.addClass(errorClass);
                            errors.push(`Row ${index + 1}: Commission rate cannot exceed 100%`);
                            valid = false;
                        } else {
                            $commissionRate.removeClass(errorClass);
                        }

                        if (!itemType) {
                            const ledger = $row.find('.item-ledger').val();
                            if (ledger === 'DR') {
                                $itemType.val('DEBIT');
                            } else if (ledger === 'CR') {
                                $itemType.val('CREDIT');
                            }
                        }
                    });

                    if (!valid) {
                        this.notify(errors[0] || 'Please complete all required fields', 'error');
                    } else if (totalAmount <= 0) {
                        this.notify('Total amount must be greater than 0', 'error');
                        valid = false;
                    }

                    return valid;
                },

                handleSubmit: function() {
                    const self = this;

                    if (this.state.isSubmitting) return;

                    if (!this.validateQuarterDate()) {
                        this.notify('Posting quarter does not match posting date', 'error');
                        return;
                    }

                    this.state.isSubmitting = true;
                    this.setLoadingState(true);

                    const disabledFields = this.$el.form.find(':disabled');
                    disabledFields.prop('disabled', false);

                    const formData = new FormData(this.$el.form[0]);

                    disabledFields.prop('disabled', true);

                    $.ajax({
                            url: this.$el.form.attr('action'),
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            timeout: 30000
                        })
                        .done(function(response) {
                            console.log(response)
                            // self.handleSuccess(response);
                        })
                        .fail(function(xhr, status, error) {
                            self.handleError(xhr, status, error);
                        })
                        .always(function() {
                            self.state.isSubmitting = false;
                            self.setLoadingState(false);
                        });


                    return false;
                },

                getCsrfToken: function() {
                    return $('meta[name="csrf-token"]').attr('content') ||
                        $('input[name="_token"]').val() ||
                        '';
                },

                handleSuccess: function(response) {
                    if (response.success) {
                        this.notify(response.message || 'Debit note generated successfully', 'success');
                        if (response.redirect_url || response.redirectUrl) {
                            const url = response.redirect_url || response.redirectUrl;
                            setTimeout(function() {
                                window.location.href = url;
                            }, 800);
                        } else if (response.debit_note_id) {

                            if (response.view_url) {
                                window.open(response.view_url, '_blank');
                            }
                            this.$el.modal.modal('hide');
                            this.refreshParent();
                        } else {
                            this.$el.modal.modal('hide');
                            this.refreshParent();
                        }
                    } else {
                        this.notify(response.message || 'Operation completed with warnings', 'warning');
                    }
                },

                handleError: function(xhr, status, error) {
                    const messages = {
                        401: 'Session expired. Please refresh the page.',
                        403: 'You do not have permission for this action.',
                        404: 'Resource not found.',
                        422: xhr.responseJSON?.message || 'Please correct the errors and try again.',
                        500: 'Server error. Please contact support.'
                    };

                    let message = messages[xhr.status] ||
                        (status === 'timeout' ? 'Request timed out. Please try again.' :
                            xhr.status === 0 ? 'Network error. Check your connection.' :
                            xhr.responseJSON?.message || 'An error occurred.');

                    if (xhr.status === 422) {
                        this.handleValidationErrors(xhr.responseJSON);
                    }

                    if (xhr.status === 401) {
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    }

                    this.notify(message, 'error');
                },

                handleValidationErrors: function(response) {
                    if (!response?.errors) return;

                    const self = this;
                    const errorClass = this.config.classes.errorField;
                    const errorMsgClass = this.config.classes.errorMessage;

                    this.$el.form.find('.server-error').remove();
                    this.$el.form.find('.' + errorClass).removeClass(errorClass);

                    $.each(response.errors, function(field, messages) {
                        let $field;

                        if (field.includes('[')) {
                            $field = self.$el.form.find('[name="' + field + '"]');
                        } else {
                            $field = self.$el.form.find('#' + field + ', [name="' + field + '"]')
                                .first();
                        }

                        if ($field.length) {
                            $field.addClass(errorClass);

                            if (!$field.closest(self.config.classes.itemRow).length) {
                                $field.after(
                                    '<span class="' + errorMsgClass + ' server-error">' +
                                    messages[0] + '</span>'
                                );
                            }
                        }
                    });
                },

                setLoadingState: function(loading) {
                    const $btn = this.$el.saveBtn;

                    if (loading) {
                        $btn.prop('disabled', true)
                            .data('original-html', $btn.html())
                            .html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');
                    } else {
                        $btn.prop('disabled', false)
                            .html($btn.data('original-html') ||
                                '<i class="fas fa-check me-1"></i> Submit Quarterly Figures');
                    }
                },

                refreshParent: function() {
                    if (typeof window.refreshData === 'function') {
                        window.refreshData();
                    } else if (typeof window.refreshCoverData === 'function') {
                        window.refreshCoverData();
                    } else {
                        window.location.reload();
                    }
                },

                resetForm: function() {
                    clearTimeout(this.state.calcDebounceTimer);

                    if (this.state.validator) {
                        this.state.validator.resetForm();
                    }

                    this.$el.form[0].reset();

                    const errorClass = this.config.classes.errorField;
                    this.$el.form.find('.' + errorClass).removeClass(errorClass);
                    this.$el.form.find('.server-error, .' + this.config.classes.errorMessage).remove();

                    this.$el.itemsBody.find(this.config.classes.itemRow).remove();
                    this.$el.noItemsRow.show();

                    this.$el.totalAmount.text('0.00');
                    this.$el.summaryGross.text('0.00');
                    this.$el.summaryDeductions.text('0.00');
                    this.$el.summaryNet.text('0.00');
                    this.$el.summarySection.hide();

                    this.$el.commentsField.val('');
                    this.$el.commentsCount.text('0');

                    this.state.itemIndex = 0;
                    this.state.isSubmitting = false;
                    this.state.isDataLocked = false;
                    this.state.loadedQuarter = null;

                    $('#quarterly-data-loaded-info').remove();
                    $('#quarterly-data-prefill-info').remove();
                    this.setFieldsLocked(false, true);

                    this.setLoadingState(false);
                },

                notify: function(message, type) {
                    type = type || 'info';
                    const timeout = type === 'error' ? 8000 : 5000;

                    if (typeof toastr !== 'undefined') {
                        toastr[type](message, '', {
                            closeButton: true,
                            progressBar: true,
                            positionClass: 'toast-top-right',
                            timeOut: timeout
                        });
                        return;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: type === 'error' ? 'error' : type,
                            text: message,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: timeout
                        });
                        return;
                    }

                    alert(message);
                },
            };

            $(function() {
                QuarterlyFiguresModal.init();
            });

            window.QuarterlyFiguresModal = QuarterlyFiguresModal;
        })(jQuery);
    </script>
@endpush
