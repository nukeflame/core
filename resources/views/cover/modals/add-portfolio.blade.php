<div class="modal fade effect-scale md-wrapper" id="addPortfolioModal" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="addPortfolioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 75%;">
        <div class="modal-content">
            <form method="POST" action="{{ route('cover.save_portfolio') }}" id="addPortfolioForm">
                @csrf
                <input type="hidden" name="cover_no" value="{{ $cover->cover_no }}">
                <input type="hidden" name="type_of_bus" value="{{ $cover->type_of_bus }}">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title text-white" id="addPortfolioModalLabel">
                        <i class="bi bi-briefcase me-2"></i>Capture Portfolio IN/OUT
                    </h5>
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
                                value="{{ $cover->endorsement_no ?? '' }}" name="orig_endorsement" readonly />
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

                                                <div class="col-md-12">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label" for="posting_date">
                                                                Posting Date <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="date" name="posting_date" id="posting_date"
                                                                class="form-control" value="{{ date('Y-m-d') }}"
                                                                required />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-semibold">Foreign Currency</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $selectedCurrency = $cover->currency_code ?? 'KES';
                                        $defaultExchangeRate = number_format(
                                            (float) ($cover->currency_rate ?? 1),
                                            2,
                                            '.',
                                            '',
                                        );
                                    @endphp

                                    <div class="mb-3">
                                        <label class="form-label" for="pc_currency_code">
                                            Currency <span class="text-danger">*</span>
                                        </label>
                                        <select name="currency_code" id="pc_currency_code"
                                            class="form-select form-select-sm" data-default="{{ $selectedCurrency }}"
                                            required>
                                            <option value="">Select Currency</option>
                                            @if (isset($currencies) && count($currencies))
                                                @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->currency_code }}"
                                                        {{ $selectedCurrency === $currency->currency_code ? 'selected' : '' }}>
                                                        {{ $currency->currency_code }} -
                                                        {{ $currency->currency_name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="{{ $selectedCurrency }}" selected>
                                                    {{ $selectedCurrency }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="pc_exchange_rate">
                                            Exchange Rate <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="today_currency" id="pc_exchange_rate"
                                            class="form-control form-control-sm text-end" step="0.01" min="0.01"
                                            value="{{ $defaultExchangeRate }}"
                                            data-default="{{ $defaultExchangeRate }}" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="portfolio_type" class="form-label required">Portfolio Type</label>
                            <select name="portfolio_type" id="portfolio_type" class="form-select" required>
                                <option value="">-- Select Portfolio Type --</option>
                                <option value="OUT">Portfolio OUT</option>
                                <option value="IN">Portfolio IN</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="port_prm_rate" class="form-label required">Premium Rate (%)</label>
                            <input type="number" name="port_prem_rate" id="port_prem_rate" class="form-control"
                                step="0.01" value="{{ number_format((float) ($cover->port_prem_rate ?? 0), 2) }}"
                                data-default="{{ number_format((float) ($cover->port_prem_rate ?? 0), 2) }}"
                                min="0" max="100" required />
                        </div>
                        <div class="col-md-3">
                            <label for="port_premium_amt" class="form-label required">Premium Amount (100%)</label>
                            <input type="text" name="port_premium_amt" id="port_premium_amt"
                                class="form-control amount" required>
                        </div>
                        <div class="col-md-3">
                            <label for="port_loss_rate" class="form-label required">Loss Rate (%)</label>
                            <input type="number" name="port_loss_rate" id="port_loss_rate" class="form-control"
                                step="0.01" value="{{ number_format((float) ($cover->port_loss_rate ?? 0), 2) }}"
                                data-default="{{ number_format((float) ($cover->port_loss_rate ?? 0), 2) }}"
                                min="0" max="100" required />
                        </div>
                        <div class="col-md-3">
                            <label for="port_outstanding_loss_amt" class="form-label required">Outstanding Losses
                                (100%)</label>
                            <input type="text" name="port_outstanding_loss_amt" id="port_outstanding_loss_amt"
                                class="form-control amount" required>
                        </div>
                    </div>

                    <div class="row g-3">
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

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" id="portfolio-save-btn" class="btn btn-primary btn-sm">
                        <i class="bi bi-check me-1"></i> Submit Portfolio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #addPortfolioModal .required::after {
        content: " *";
        color: #dc3545;
    }
</style>

@push('script')
    <script>
        (function() {
            'use strict';

            const modalId = '#addPortfolioModal';
            const formId = '#addPortfolioForm';
            const $modal = $(modalId);
            const $form = $(formId);
            if (!$form.length) return;

            const $portfolioType = $('#portfolio_type');
            const $portfolioYear = $('#portfolio_year');
            const $origEndorsement = $('#orig_endorsement');
            const $portReinsurer = $('#port_reinsurer');
            const $portShare = $('#port_share');
            const $portPrmRate = $('#port_prm_rate');
            const $portLossRate = $('#port_loss_rate');
            const $premiumBase = $('#port_premium_amt');
            const $lossBase = $('#port_outstanding_loss_amt');
            const $legacyAmount = $('#port_amt');
            const $saveBtn = $('#portfolio-save-btn');

            const $premiumTransfer = $('#port_premium_transfer_amt');
            const $lossTransfer = $('#port_loss_transfer_amt');
            const $premiumShareAmt = $('#port_premium_share_amt');
            const $lossShareAmt = $('#port_loss_share_amt');

            const $reinsurerWrap = $('#portfolio_reinsurer_wrapper');
            const $shareWrap = $('#portfolio_share_wrapper');

            const coverNo = @json($cover->cover_no);
            const getTreatyYearCoverUrl = @json(route('cover.get_treaty_year_cover'));
            const getReinsurersUrl = @json(route('cover.get_reinsurers_orig_endorsement'));
            const saveBtnDefaultHtml = $saveBtn.html();

            function parseAmount(value) {
                if (value === null || value === undefined) return 0;
                const cleaned = String(value).replace(/,/g, '');
                const num = parseFloat(cleaned);
                return Number.isFinite(num) ? num : 0;
            }

            function formatAmount(value) {
                const number = Number.isFinite(value) ? value : 0;
                return number.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function formatAmountWhileTyping(value) {
                const normalized = String(value || '').replace(/,/g, '').replace(/[^\d.]/g, '');
                const parts = normalized.split('.');
                const integerRaw = parts.shift() || '';
                const decimalRaw = parts.join('');
                const integerPart = integerRaw.replace(/^0+(?=\d)/, '');
                const integerFormatted = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                const decimalPart = decimalRaw.slice(0, 2);

                if (parts.length > 0 || normalized.endsWith('.')) {
                    return `${integerFormatted || '0'}.${decimalPart}`;
                }

                return integerFormatted;
            }

            function syncAmountInput($el) {
                const amount = parseAmount($el.val());
                $el.val(amount ? formatAmount(amount) : '');
            }

            function bindTypingAmountFormat($el) {
                if (!$el.length) return;

                $el.on('input', function() {
                    const input = this;
                    const raw = input.value || '';
                    const caret = input.selectionStart ?? raw.length;
                    const tokenCountBeforeCaret = (raw.slice(0, caret).match(/[\d.]/g) || []).length;
                    const formatted = formatAmountWhileTyping(raw);

                    input.value = formatted;

                    let cursor = formatted.length;
                    let tokenCount = 0;
                    for (let i = 0; i < formatted.length; i++) {
                        if (/[\d.]/.test(formatted.charAt(i))) {
                            tokenCount++;
                        }
                        if (tokenCount >= tokenCountBeforeCaret) {
                            cursor = i + 1;
                            break;
                        }
                    }

                    input.setSelectionRange(cursor, cursor);

                    if ($(input).is($premiumBase) && $legacyAmount.length && !$legacyAmount.val()) {
                        $legacyAmount.val($(input).val());
                    }

                    compute();
                });

                $el.on('blur', function() {
                    syncAmountInput($(this));
                    compute();
                });
            }

            function notify(message, type) {
                type = type || 'info';
                if (typeof toastr !== 'undefined') {
                    toastr[type](message);
                    return;
                }
                alert(message);
            }

            function sanitizeMoneyForSubmit(value) {
                const amount = parseAmount(value);
                return amount ? amount.toFixed(2) : '';
            }

            function clearServerErrors() {
                $form.find('.server-error').remove();
                $form.find('.is-invalid').removeClass('is-invalid');
            }

            function markServerErrors(errors) {
                if (!errors) return;
                Object.keys(errors).forEach(function(field) {
                    const messages = errors[field];
                    const message = Array.isArray(messages) ? messages[0] : messages;
                    const $field = $form.find(`[name="${field}"]`);
                    if (!$field.length) return;

                    $field.addClass('is-invalid');
                    const $feedback = $('<div class="invalid-feedback server-error"></div>').text(message);

                    if ($field.hasClass('select2-hidden-accessible') && $field.next('.select2').length) {
                        $feedback.insertAfter($field.next('.select2'));
                    } else {
                        $feedback.insertAfter($field);
                    }
                });
            }

            function setSavingState(isSaving) {
                if (!$saveBtn.length) return;
                $saveBtn.prop('disabled', isSaving);
                $saveBtn.html(isSaving ? '<span class="spinner-border spinner-border-sm me-1"></span>Saving...' :
                    saveBtnDefaultHtml);
            }

            function resetPortfolioForm() {
                if ($form.length && $form[0]) {
                    $form[0].reset();
                }

                clearServerErrors();

                if ($form.data('validator')) {
                    $form.validate().resetForm();
                }

                $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');

                $form.find('[data-default]').each(function() {
                    const $field = $(this);
                    const defaultValue = $field.data('default');
                    if (defaultValue !== undefined) {
                        $field.val(defaultValue);
                    }
                });

                syncAmountInput($premiumBase);
                syncAmountInput($lossBase);
                compute();
                resetDependentDropdowns();

                if ($('#comments-count').length) {
                    $('#comments-count').text(0);
                }

                $form.find('select').trigger('change');
            }

            function submitPortfolioAjax() {
                clearServerErrors();
                setSavingState(true);

                const premiumDisplayValue = $premiumBase.val();
                const lossDisplayValue = $lossBase.val();
                $premiumBase.val(sanitizeMoneyForSubmit(premiumDisplayValue));
                $lossBase.val(sanitizeMoneyForSubmit(lossDisplayValue));

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        const successMessage = response && response.message ? response.message :
                            'Portfolio saved successfully.';
                        notify(successMessage, 'success');
                        resetPortfolioForm();
                        const modalInstance = bootstrap.Modal.getInstance($modal[0]);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const response = xhr.responseJSON || {};
                            markServerErrors(response.errors || {});
                            notify('Please correct the highlighted fields.', 'error');
                            return;
                        }

                        const response = xhr.responseJSON || {};
                        notify(response.message || 'Failed to save portfolio.', 'error');
                    },
                    complete: function() {
                        setSavingState(false);
                        $premiumBase.val(premiumDisplayValue);
                        $lossBase.val(lossDisplayValue);
                        syncAmountInput($premiumBase);
                        syncAmountInput($lossBase);
                    }
                });
            }

            function initValidation() {
                if (!$.fn.validate) {
                    $form.on('submit', function(e) {
                        e.preventDefault();
                        submitPortfolioAjax();
                    });
                    return;
                }

                $.validator.addMethod('formattedAmount', function(value, element) {
                    if (this.optional(element)) return true;
                    const cleaned = String(value).replace(/,/g, '');
                    return /^(\d+)(\.\d{1,2})?$/.test(cleaned);
                }, 'Please enter a valid amount');

                $form.validate({
                    ignore: ':hidden:not(.select2-hidden-accessible)',
                    rules: {
                        posting_year: {
                            required: true
                        },
                        posting_date: {
                            required: true,
                            date: true
                        },
                        currency_code: {
                            required: true
                        },
                        today_currency: {
                            required: true,
                            number: true,
                            min: 0.01
                        },
                        portfolio_type: {
                            required: true
                        },
                        port_prem_rate: {
                            required: true,
                            number: true,
                            min: 0,
                            max: 100
                        },
                        port_premium_amt: {
                            required: true,
                            formattedAmount: true
                        },
                        port_loss_rate: {
                            required: true,
                            number: true,
                            min: 0,
                            max: 100
                        },
                        port_outstanding_loss_amt: {
                            required: true,
                            formattedAmount: true
                        },
                        comments: {
                            maxlength: 2000
                        }
                    },
                    messages: {
                        posting_year: {
                            required: 'Please select underwriting year'
                        },
                        posting_date: {
                            required: 'Please select posting date'
                        },
                        currency_code: {
                            required: 'Please select currency'
                        },
                        today_currency: {
                            required: 'Please enter exchange rate',
                            min: 'Exchange rate must be greater than zero'
                        },
                        portfolio_type: {
                            required: 'Please select portfolio type'
                        },
                        port_prem_rate: {
                            required: 'Please enter premium rate'
                        },
                        port_premium_amt: {
                            required: 'Please enter premium amount'
                        },
                        port_loss_rate: {
                            required: 'Please enter loss rate'
                        },
                        port_outstanding_loss_amt: {
                            required: 'Please enter outstanding losses amount'
                        },
                        comments: {
                            maxlength: 'Comments cannot exceed 2000 characters'
                        }
                    },
                    errorElement: 'span',
                    errorClass: 'invalid-feedback server-error',
                    highlight: function(element) {
                        $(element).addClass('is-invalid').removeClass('is-valid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    },
                    errorPlacement: function(error, element) {
                        if (element.hasClass('select2-hidden-accessible') && element.next('.select2')
                            .length) {
                            error.insertAfter(element.next('.select2'));
                            return;
                        }
                        error.insertAfter(element);
                    },
                    submitHandler: function() {
                        submitPortfolioAjax();
                    }
                });
            }

            function compute() {
                const premiumBase = parseAmount($premiumBase.val());
                const lossBase = parseAmount($lossBase.val());
                const premiumRate = parseAmount($portPrmRate.val());
                const lossRate = parseAmount($portLossRate.val());
                const share = parseAmount($portShare.val());

                const premiumTransfer = premiumBase * (premiumRate / 100);
                const lossTransfer = lossBase * (lossRate / 100);
                const premiumAtShare = premiumTransfer * (share / 100);
                const lossAtShare = lossTransfer * (share / 100);

                $premiumTransfer.val(formatAmount(premiumTransfer));
                $lossTransfer.val(formatAmount(lossTransfer));
                $premiumShareAmt.val(formatAmount(premiumAtShare));
                $lossShareAmt.val(formatAmount(lossAtShare));
            }

            function resetDependentDropdowns() {
                $origEndorsement.empty().append('<option value="">-- Select Cover Reference --</option>');
                $portReinsurer.empty().append('<option value="">-- Select Reinsurer --</option>');
                $reinsurerWrap.addClass('d-none');
                $shareWrap.addClass('d-none');
                $portShare.val('');
                $portReinsurer.prop('disabled', true);
                $portShare.prop('disabled', true);
            }

            function enforceShareLimit() {
                const selectedShare = parseAmount($portShare.val());
                const origShare = parseAmount($portReinsurer.find('option:selected').data('portfolio-share'));

                if ($portfolioType.val() === 'OUT' && origShare > 0 && selectedShare > origShare) {
                    $portShare.val(origShare.toFixed(2));
                    if (typeof toastr !== 'undefined') {
                        toastr.error('OUT share cannot exceed original reinsurer share.');
                    }
                }
                compute();
            }

            function fetchCoverReferences() {
                const treatyYear = $portfolioYear.val();
                if (!treatyYear) {
                    resetDependentDropdowns();
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: getTreatyYearCoverUrl,
                    data: {
                        cover_no: coverNo,
                        treaty_year: treatyYear
                    },
                    cache: false,
                    success: function(response) {
                        resetDependentDropdowns();
                        (response || []).forEach(function(item) {
                            const text =
                                `${item.endorsement_no} - ${item.cover_from} To ${item.cover_to}`;
                            $origEndorsement.append($('<option>', {
                                value: item.endorsement_no,
                                text
                            }));
                        });
                    }
                });
            }

            function fetchReinsurers() {
                const portfolioType = $portfolioType.val();
                const treatyYear = $portfolioYear.val();
                const origEndorsement = $origEndorsement.val();

                if (!portfolioType || !treatyYear || !origEndorsement) {
                    $portReinsurer.empty().append('<option value="">-- Select Reinsurer --</option>');
                    $reinsurerWrap.addClass('d-none');
                    $shareWrap.addClass('d-none');
                    $portReinsurer.prop('disabled', true);
                    $portShare.prop('disabled', true);
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: getReinsurersUrl,
                    data: {
                        portfolio_type: portfolioType,
                        cover_no: coverNo,
                        treaty_year: treatyYear,
                        orig_endorsement: origEndorsement
                    },
                    cache: false,
                    success: function(response) {
                        const reinsurers = (response && response.reinsurers) ? response.reinsurers : [];
                        $portReinsurer.empty().append('<option value="">-- Select Reinsurer --</option>');

                        reinsurers.forEach(function(item) {
                            const $option = $('<option>', {
                                value: item.customer_id,
                                text: `${item.customer_id} - ${item.name}`
                            });

                            if (item.share !== undefined && item.share !== null) {
                                $option.attr('data-portfolio-share', parseFloat(item.share).toFixed(
                                    2));
                            }
                            if (item.port_prem_rate !== undefined && item.port_prem_rate !== null) {
                                $option.attr('data-port-prem-rate', parseFloat(item.port_prem_rate)
                                    .toFixed(2));
                            }
                            if (item.port_loss_rate !== undefined && item.port_loss_rate !== null) {
                                $option.attr('data-port-loss-rate', parseFloat(item.port_loss_rate)
                                    .toFixed(2));
                            }

                            $portReinsurer.append($option);
                        });

                        if (reinsurers.length > 0) {
                            $reinsurerWrap.removeClass('d-none');
                            $shareWrap.removeClass('d-none');
                            $portReinsurer.prop('disabled', false);
                            $portShare.prop('disabled', false);
                        } else {
                            $reinsurerWrap.addClass('d-none');
                            $shareWrap.addClass('d-none');
                            $portReinsurer.prop('disabled', true);
                            $portShare.prop('disabled', true);
                        }
                    }
                });
            }

            $modal.on('shown.bs.modal', function() {
                $('select', this).each(function() {
                    if ($.fn.select2 && !$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            dropdownParent: $(modalId),
                            width: '100%'
                        });
                    }
                });
            });

            $modal.on('hidden.bs.modal', function() {
                resetPortfolioForm();
            });

            $portfolioType.on('change', function() {
                resetDependentDropdowns();
                fetchReinsurers();
            });
            $portfolioYear.on('change', fetchCoverReferences);
            $origEndorsement.on('change', fetchReinsurers);

            $portReinsurer.on('change', function() {
                const selected = $portReinsurer.find('option:selected');
                const share = selected.data('portfolio-share');
                const premRate = selected.data('port-prem-rate');
                const lossRate = selected.data('port-loss-rate');

                if (share !== undefined) {
                    $portShare.val(Number(share).toFixed(2));
                }
                if (premRate !== undefined && !$portPrmRate.val()) {
                    $portPrmRate.val(Number(premRate).toFixed(2));
                }
                if (lossRate !== undefined && !$portLossRate.val()) {
                    $portLossRate.val(Number(lossRate).toFixed(2));
                }

                compute();
            });

            bindTypingAmountFormat($premiumBase);
            bindTypingAmountFormat($lossBase);

            $portPrmRate.on('change keyup', compute);
            $portLossRate.on('change keyup', compute);
            $portShare.on('change keyup', enforceShareLimit);
            initValidation();

            resetDependentDropdowns();
        })();
    </script>
@endpush
