{{-- Hidden Fields --}}
<input type="hidden" name="customer_id" id="customer_id" value="{{ $customer->customer_id }}">
<input type="hidden" name="trans_type" id="trans_type" value="{{ $trans_type }}">

@if ($trans_type !== 'NEW')
    <input type="hidden" name="cover_no" id="cover_no" value="{{ $old_endt_trans->cover_no }}">
    <input type="hidden" name="endorsement_no" id="endorsement_no" value="{{ $old_endt_trans->endorsement_no }}">
@endif

<input type="hidden" id="installment_total_amount" value="0">
<input type="hidden" id="vat_charged" name="vat_charged" value="0">
<input type="hidden" name="risk_details" id="hidden_risk_details">
