@php
    $isTemplate = $isTemplate ?? false;

    $rowId = $isTemplate
        ? 'reinsurer-div-TREATY_COUNTER_PLACEHOLDER-COUNTER_PLACEHOLDER'
        : "reinsurer-div-{$treatyCounter}-{$counter}";
    $treatyCounterValue = $isTemplate ? 'TREATY_COUNTER_PLACEHOLDER' : $treatyCounter;
    $counterValue = $isTemplate ? 'COUNTER_PLACEHOLDER' : $counter;
    $reinsurerNumber = $isTemplate ? 'REINSURER_NUMBER_PLACEHOLDER' : $counter + 1;

    $isFacultative = in_array($cover->type_of_bus, ['FPR', 'FNP']);
    $isTreaty = in_array($cover->type_of_bus, ['TPR', 'TNP']);
@endphp

<div id="{{ $rowId }}" data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}"
    class="reinsurer-section mb-3 p-3 border rounded position-relative bg-light">

    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-reinsurer-btn"
        data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" title="Remove Reinsurer">
        <i class="bx bx-trash"></i>
    </button>

    <h6 class="mb-3 text-primary fs-14">
        <i class="bx bx-building me-1"></i>
        Reinsurer # <span class="reinsurer-number">{{ $reinsurerNumber }}</span>
    </h6>

    <div class="row">
        <div class="col-md-6">
            <label for="reinsurer-{{ $treatyCounterValue }}-{{ $counterValue }}" class="form-label required">
                Reinsurer
            </label>
            <div class="cover-card">
                <select name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][reinsurer]"
                    id="reinsurer-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="select2Placement reinsurer reinsurers" data-treaty-counter="{{ $treatyCounterValue }}"
                    data-counter="{{ $counterValue }}" required>
                    <option value="">--Select Reinsurer--</option>
                    @foreach ($reinsurers as $partner)
                        @php
                            $existsInCoverpart = $coverpart->contains('partner_no', $partner->customer_id);
                            $existsInCoverRegister = $cover->customer_id == $partner->customer_id;
                        @endphp
                        @if (!$existsInCoverpart && !$existsInCoverRegister)
                            <option value="{{ $partner->customer_id }}" title="{{ $partner->name }}"
                                data-country="{{ $partner->country ?? '' }}"
                                data-rating="{{ $partner->rating ?? '' }}">
                                {{ $partner->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <small class="form-text text-muted">
                Can't find the reinsurer?
                <a href="{{ route('customer.form') }}" target="_blank" rel="noopener noreferrer" class="text-info">Add
                    new reinsurer</a>
            </small>
        </div>

        <div class="col-md-2">
            <label for="written_share-{{ $treatyCounterValue }}-{{ $counterValue }}" class="form-label required">
                {{ $isTreaty ? 'Signed Lines (%)' : 'Written Lines (%)' }}
            </label>
            <input type="number" step="0.01" min="0" max="100"
                name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][written_share]"
                id="written_share-{{ $treatyCounterValue }}-{{ $counterValue }}"
                class="form-control reinsurer-written-share reinsurers color-blk treaty"
                data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" required />
        </div>

        <div class="col-md-2">
            <label for="wht_rate-{{ $treatyCounterValue }}-{{ $counterValue }}" class="form-label required">
                WHT Rate (%)
            </label>
            <select name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][wht_rate]"
                id="wht_rate-{{ $treatyCounterValue }}-{{ $counterValue }}"
                class="select2Placement reinsurer-wht reinsurers" data-treaty-counter="{{ $treatyCounterValue }}"
                data-counter="{{ $counterValue }}" required>
                <option value="">--Select WHT--</option>
                @foreach ($whtRates as $whtRate)
                    <option value="{{ $whtRate->rate }}" {{ $whtRate->rate == 0 ? 'selected' : '' }}>
                        {{ $whtRate->description }}
                    </option>
                @endforeach
            </select>
        </div>

        @if ($isFacultative)
            <div class="col-md-2">
                <label for="share-{{ $treatyCounterValue }}-{{ $counterValue }}" class="form-label required">
                    Signed Lines (%)
                </label>
                <input type="number" step="0.01" min="0" max="100"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][share]"
                    id="share-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-control reinsurer-share reinsurers color-blk"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" required />
                <div class="invalid-feedback">Signed lines cannot exceed written lines</div>
            </div>
        @endif
    </div>

    @if ($isTreaty)
        <div class="row mt-3">
            <div class="col-md-3">
                <label for="compulsory_acceptance-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label required">
                    Compulsory Acceptance (%)
                </label>
                <input type="number" step="0.01" min="0" max="100"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][compulsory_acceptance]"
                    id="compulsory_acceptance-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-control reinsurer-compulsory-acceptance reinsurers color-blk"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}"
                    data-calculation-field="treaty-share" required />
            </div>

            <div class="col-md-3">
                <label for="optional_acceptance-{{ $treatyCounterValue }}-{{ $counterValue }}" class="form-label">
                    Optional Acceptance (%)
                </label>
                <input type="number" step="0.01" min="0" max="100"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][optional_acceptance]"
                    id="optional_acceptance-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-control reinsurer-optional-acceptance reinsurers color-blk"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}"
                    data-calculation-field="treaty-share" />
            </div>

            <div class="col-md-3">
                <label for="total_acceptance-{{ $treatyCounterValue }}-{{ $counterValue }}" class="form-label">
                    Total Acceptance (%)
                </label>
                <input type="number" step="0.01" min="0" max="100"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][share]"
                    id="total_acceptance-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-control reinsurer-total-acceptance reinsurers color-blk bg-light"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}"
                    data-calculation-field="treaty-share-total" readonly />
                <div class="invalid-feedback">
                    @if ($isTreaty)
                        Total acceptance cannot exceed written lines
                    @else
                        Total acceptance cannot exceed signed lines
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <label for="reins_pay_method-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label required">
                    Payment Method
                </label>
                <div class="cover-card">
                    <select class="select2Placement reins-pay-method reinsurers"
                        name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][pay_method]"
                        id="reins_pay_method-{{ $treatyCounterValue }}-{{ $counterValue }}"
                        data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" required>
                        <option value="">--Select Payment Method--</option>
                        @foreach ($paymethods as $pay_method)
                            <option value="{{ $pay_method->pay_method_code }}"
                                data-pay-method-desc="{{ $pay_method->short_description }}">
                                {{ $pay_method->pay_method_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Brokerage Calculation Basis</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check form-check-lg custom-checkbox">
                                <input class="form-check-input reinsurer-calc-option" type="checkbox"
                                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][net_of_tax]"
                                    id="net_of_tax-{{ $treatyCounterValue }}-{{ $counterValue }}"
                                    data-treaty-counter="{{ $treatyCounterValue }}"
                                    data-counter="{{ $counterValue }}" value="1">
                                <label class="form-check-label"
                                    for="net_of_tax-{{ $treatyCounterValue }}-{{ $counterValue }}">
                                    Net of Tax
                                </label>
                            </div>
                            <div class="form-check form-check-lg custom-checkbox">
                                <input class="form-check-input reinsurer-calc-option" type="checkbox"
                                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][net_of_claims]"
                                    id="net_of_claims-{{ $treatyCounterValue }}-{{ $counterValue }}"
                                    data-treaty-counter="{{ $treatyCounterValue }}"
                                    data-counter="{{ $counterValue }}" value="1">
                                <label class="form-check-label"
                                    for="net_of_claims-{{ $treatyCounterValue }}-{{ $counterValue }}">
                                    Net of Claims
                                </label>
                            </div>
                            <div class="form-check form-check-lg custom-checkbox">
                                <input class="form-check-input reinsurer-calc-option" type="checkbox"
                                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][net_of_commission]"
                                    id="net_of_commission-{{ $treatyCounterValue }}-{{ $counterValue }}"
                                    data-treaty-counter="{{ $treatyCounterValue }}"
                                    data-counter="{{ $counterValue }}" value="1">
                                <label class="form-check-label"
                                    for="net_of_commission-{{ $treatyCounterValue }}-{{ $counterValue }}">
                                    Net of Commission
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Commission Calculation Basis</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check form-check-lg custom-checkbox">
                                <input class="form-check-input reinsurer-calc-option" type="checkbox"
                                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][net_of_premium]"
                                    id="net_of_premium-{{ $treatyCounterValue }}-{{ $counterValue }}"
                                    data-treaty-counter="{{ $treatyCounterValue }}"
                                    data-counter="{{ $counterValue }}" value="1">
                                <label class="form-check-label"
                                    for="net_of_premium-{{ $treatyCounterValue }}-{{ $counterValue }}">
                                    Net of Tax
                                </label>
                            </div>
                            {{-- <div class="form-check form-check-lg custom-checkbox">
                                <input class="form-check-input reinsurer-calc-option" type="checkbox"
                                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][net_withholding_tax]"
                                    id="net_withholding_tax-{{ $treatyCounterValue }}-{{ $counterValue }}"
                                    data-treaty-counter="{{ $treatyCounterValue }}"
                                    data-counter="{{ $counterValue }}" value="1">
                                <label class="form-check-label"
                                    for="net_withholding_tax-{{ $treatyCounterValue }}-{{ $counterValue }}">
                                    Gross of Tax
                                </label>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($isFacultative)
        <div class="row mt-3">
            <div class="col-md-3">
                <label for="reinsurer-sum_insured-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label">
                    Sum Insured
                </label>
                <input type="text"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][sum_insured]"
                    id="reinsurer-sum_insured-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-control reinsurer-sum-insured reinsurers color-blk bg-light"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" readonly />
            </div>

            <div class="col-md-3">
                <label for="reinsurer-premium-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label required">
                    Reinsurer Premium
                </label>
                <input type="text"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][premium]"
                    id="reinsurer-premium-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-control reinsurer-premium reinsurers color-blk"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" required />
            </div>

            <div class="col-md-3">
                <label for="reinsurer-comm_rate-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label required">
                    Commission Rate (%)
                </label>
                <input type="text"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][comm_rate]"
                    id="reinsurer-comm_rate-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-control reinsurer-comm-rate reinsurers color-blk"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" required />
            </div>

            <div class="col-md-3">
                <label for="reinsurer-comm_amt-{{ $treatyCounterValue }}-{{ $counterValue }}" class="form-label">
                    Commission Amount
                </label>
                <input type="text"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][comm_amt]"
                    id="reinsurer-comm_amt-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-control reinsurer-comm-amt reinsurers color-blk bg-light"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" readonly />
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <label class="form-label" for="brokerage_comm_type-{{ $treatyCounterValue }}-{{ $counterValue }}">
                    Brokerage Commission Type
                </label>
                <select
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][brokerage_comm_type]"
                    id="brokerage_comm_type-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="select2Placement brokerage-comm-type reinsurers"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}"
                    @if ($cover->type_of_bus != 'NEW') required @endif>
                    <option value="" @if (!$isTemplate && $cover->type_of_bus != 'NEW' && $cover->brokerage_comm_type == '') selected @endif>
                        --Select Basis--
                    </option>
                    <option value="R" @if (!$isTemplate && $cover->type_of_bus != 'NEW' && $cover->brokerage_comm_type == 'R') selected @endif>
                        Rate
                    </option>
                    <option value="A" @if (!$isTemplate && $cover->type_of_bus != 'NEW' && $cover->brokerage_comm_type == 'A') selected @endif>
                        Quoted Amount
                    </option>
                </select>
            </div>

            <div class="col-md-3 brokerage_comm_amt_div">
                <label for="reinsurer-brokerage_comm_amt-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label">
                    Brokerage Commission Amount
                </label>
                <input type="text"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][brokerage_comm_amt]"
                    id="reinsurer-brokerage_comm_amt-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-control color-blk reinsurers reinsurer-brokerage-comm-amt"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}"
                    {{ !$isTemplate && $cover->brokerage_comm_type == 'A' ? 'required' : '' }} />
            </div>

            <div class="col-md-3 fac_section_div brokerage_comm_rate_div">
                <label class="form-label" for="brokerage_comm_rate-{{ $treatyCounterValue }}-{{ $counterValue }}">
                    Brokerage Commission Rate (%)
                </label>
                <input type="text" class="form-control color-blk reinsurers brokerage-comm-rate"
                    id="brokerage_comm_rate-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][brokerage_comm_rate]"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}">
            </div>

            <div class="col-md-3 fac_section_div brokerage_comm_rate_div">
                <label class="form-label"
                    for="brokerage_comm_rate_amnt-{{ $treatyCounterValue }}-{{ $counterValue }}">
                    Brokerage Commission Rate Amount
                </label>
                <input type="text" class="form-control color-blk reinsurers brokerage-comm-rate-amnt bg-light"
                    id="brokerage_comm_rate_amnt-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][brokerage_comm_rate_amnt]"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" readonly>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <label for="apply_fronting-{{ $treatyCounterValue }}-{{ $counterValue }}" class="form-label">
                    Apply Retro Fee
                </label>
                <select class="select2Placement apply-fronting reinsurers"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][apply_fronting]"
                    id="apply_fronting-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}">
                    <option value="">--Select Option--</option>
                    <option value="Y">Yes</option>
                    <option value="N" selected>No</option>
                </select>
            </div>

            <div class="col-md-3 fronting_div" style="display: none;"
                id="fronting_rate_div-{{ $treatyCounterValue }}-{{ $counterValue }}">
                <label for="reinsurer-fronting_rate-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label">
                    Retro Rate (%)
                </label>
                <input type="number" step="0.01" min="0" max="100"
                    class="form-control reinsurer-fronting-rate reinsurers"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][fronting_rate]"
                    id="reinsurer-fronting_rate-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}"
                    data-calculation-field="fronting" />
            </div>

            <div class="col-md-3 fronting_div" style="display: none;"
                id="fronting_amt_div-{{ $treatyCounterValue }}-{{ $counterValue }}">
                <label for="reinsurer-fronting_amt-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label">
                    Retro Amount
                </label>
                <input type="text" class="form-control reinsurer-fronting-amt reinsurers bg-light"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][fronting_amt]"
                    id="reinsurer-fronting_amt-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}"
                    data-calculation-field="fronting-amount" readonly />
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <label for="reins_pay_method-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label required">
                    Payment Method
                </label>
                <select class="select2Placement reins-pay-method reinsurers"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][pay_method]"
                    id="reins_pay_method-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}" required>
                    <option value="">--Select Payment Method--</option>
                    @foreach ($paymethods as $pay_method)
                        <option value="{{ $pay_method->pay_method_code }}"
                            data-pay-method-desc="{{ $pay_method->short_description }}">
                            {{ $pay_method->pay_method_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 no-of-installments-section" style="display: none">
                <label for="no_of_installments-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    class="form-label required">
                    No. of Installments
                </label>
                <input type="number" min="1" max="12"
                    class="form-control no-of-installments reinsurers"
                    id="no_of_installments-{{ $treatyCounterValue }}-{{ $counterValue }}"
                    name="treaty[{{ $treatyCounterValue }}][reinsurers][{{ $counterValue }}][no_of_installments]"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}"
                    value="1" />
            </div>

            <div class="col-md-3 add-installment-btn-section" style="display: none">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary btn-sm d-block add-reinsurer-installments"
                    data-treaty-counter="{{ $treatyCounterValue }}" data-counter="{{ $counterValue }}">
                    <i class="bx bx-calendar-plus me-1"></i> Generate Installments
                </button>
            </div>
        </div>

        <div class="row mt-3 installments-box" style="display: none">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bx bx-calendar-alt me-2"></i>Installment Schedule
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="reinsurer-plan-section" data-treaty-counter="{{ $treatyCounterValue }}"
                            data-counter="{{ $counterValue }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
