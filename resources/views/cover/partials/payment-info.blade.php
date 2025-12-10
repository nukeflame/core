<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label required">Payment Method</label>
        <div class="cover-card">
            <select class="form-control select2" name="pay_method" id="pay_method" required>
                <option value="">Choose Payment Method</option>
                @foreach ($paymethods as $pay_method)
                    <option value="{{ $pay_method->pay_method_code }}"
                        data-description="{{ $pay_method->short_description }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->pay_method_code == $pay_method->pay_method_code ? 'selected' : '' }}>
                        {{ $pay_method->pay_method_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <small class="text-muted">How premium will be paid (lump sum or installments)</small>
        @error('pay_method')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2" id="installments_count_section" style="display: none;">
        <label class="form-label required">Installments</label>
        <input type="number" class="form-control" id="no_of_installments" name="no_of_installments" min="1"
            max="100" placeholder="Number"
            value="{{ isset($old_endt_trans) ? $old_endt_trans->no_of_installments : '' }}">
        <small class="text-muted">Number of payment splits (1-100)</small>
    </div>

    <div class="col-md-2" id="add_installment_btn_section" style="display: none;">
        <label class="form-label">&nbsp;</label>
        <button type="button" class="btn btn-primary w-100" id="add_fac_instalments">
            <i class="bx bx-plus me-1"></i> Add Installment
        </button>
        <small class="text-muted">Configure installment schedule</small>
    </div>

    <div class="col-md-2">
        <label class="form-label required">Currency</label>
        <div class="cover-card">
            <select class="form-control select2" name="currency_code" id="currency_code" required>
                <option value="">Choose Currency</option>
                @foreach ($currencies as $currency)
                    <option value="{{ $currency->currency_code }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->currency_code == $currency->currency_code ? 'selected' : '' }}>
                        {{ $currency->currency_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <small class="text-muted">Transaction currency</small>
        @error('currency_code')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label required">Exchange Rate</label>
        <input type="text" name="today_currency" id="today_currency" class="form-control"
            value="{{ isset($old_endt_trans) ? $old_endt_trans->currency_rate : '' }}" readonly>
        <small class="text-muted">Current rate to base currency</small>
    </div>

    <div class="col-md-3">
        <label class="form-label required">Premium Payment Terms</label>
        <div class="cover-card">
            <select class="form-control select2" name="premium_payment_term" id="premium_payment_term" required>
                <option value="">Choose Payment Term</option>
                @foreach ($premium_pay_terms as $premium_pay_term)
                    <option value="{{ $premium_pay_term->pay_term_code }}"
                        data-description="{{ $premium_pay_term->pay_term_desc }}"
                        {{ isset($old_endt_trans) && $old_endt_trans->premium_payment_code == $premium_pay_term->pay_term_code ? 'selected' : '' }}>
                        {{ $premium_pay_term->pay_term_desc }}
                    </option>
                @endforeach
            </select>
        </div>
        <small class="text-muted">When premium payment is due</small>
        @error('premium_payment_term')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>
</div>
