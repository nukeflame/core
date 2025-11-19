{{-- resources/views/cover/partials/action-buttons.blade.php --}}
<div class="card-body p-3 mx-0 cover-info-wrapper" style="background-color:var(--cover-bg);border-radius:0.375rem;">

    {{-- Verify Details Button --}}
    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" id="verify_details"
        data-pre-verify-url="{{ route('cover.pre_cover_verification') }}">
        <i class="bx bx-plus me-1 align-middle"></i>
        <span id="verify-text">Verify Details</span>
    </button>

    {{-- Generate Slip Button --}}
    <a href="#" class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" id="generate_slip"
        data-pre-slip-url="{{ route('docs.pre_cover_slip_verification') }}"
        data-slip-url="{{ route('docs.coverslip', ['endorsement_no' => $cover->endorsement_no, 'pre_debit' => 'Y']) }}">
        <i class="bx bx-analyse me-1 align-middle"></i> Generate Slip
    </a>

    {{-- Send Email to Reinsurer --}}
    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light send_reinsurer_email"
        data-tran_no="{{ $reinsurer->tran_no }}"
        data-debit_url="{{ route('docs.coverdebitnote', ['endorsement_no' => $cover->endorsement_no]) }}"
        data-claim_notice_url="{{ route('docs.claimnotice', ['endorsement_no' => $cover->endorsement_no]) }}">
        <i class="bx bx-envelope me-1 align-middle"></i> Send Email
    </button>
</div>
