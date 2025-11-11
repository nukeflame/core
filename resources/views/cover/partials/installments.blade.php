<div id="fac-installments-section">
    @if (isset($coverInstallments) && count($coverInstallments) > 0)
        @foreach ($coverInstallments as $index => $installment)
            @php
                $idx = $index + 1;
            @endphp
            <div class="row g-3 mb-3 installment-row" data-count="{{ $idx }}">
                <div class="col-md-3">
                    <label class="form-label">Installment</label>
                    <input type="hidden" name="installment_no[]" value="{{ $idx }}">
                    <input type="hidden" name="installment_id[]" value="{{ $installment->id }}">
                    <input type="text" value="Installment No. {{ $idx }}" id="instl_no_{{ $idx }}"
                        class="form-control" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="instl_date_{{ $idx }}">Due Date</label>
                    <input type="date" name="installment_date[]" value="{{ $installment->installment_date }}"
                        id="instl_date_{{ $idx }}" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label" for="instl_amnt_{{ $idx }}">Amount</label>
                    <input type="text" name="installment_amt[]" id="instl_amnt_{{ $idx }}"
                        value="{{ number_format($installment->installment_amt, 2) }}" class="form-control amount"
                        required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-danger w-100 remove-installment" type="button">
                        <i class="bx bx-minus me-1"></i> Remove
                    </button>
                </div>
            </div>
        @endforeach
    @endif
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="bx bx-info-circle me-2"></i>
            <strong>Note:</strong> The sum of all installment amounts must equal the total premium amount.
        </div>
    </div>
</div>
