@if (!$cover)
    <div class="card-body p-3 mx-0 cover-info-wrapper" style="background-color:var(--cover-bg);border-radius:0.375rem;">
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Verify Details Button --}}
            <button class="btn btn-verify btn-wave waves-effect waves-light" id="verify_details"
                data-pre-verify-url="{{ route('cover.pre_cover_verification') }}">
                <i class="bx bx-plus me-1 align-middle"></i> Verify Details
            </button>

            {{-- Generate Slip Button --}}
            <a href="#" class="btn btn-generate btn-wave waves-effect waves-light" id="generate_slip"
                data-pre-slip-url="{{ route('docs.pre_cover_slip_verification') }}"
                data-slip-url="{{ route('docs.coverslip', ['endorsement_no' => $cover->endorsement_no ?? '']) }}">
                <i class="bx bx-analyse me-1 align-middle"></i> Generate Slip
            </a>

            {{-- Send Email to Reinsurer --}}
            <button class="btn btn-send-email btn-wave waves-effect waves-light send_reinsurer_email"
                data-tran_no="{{ $reinsurer->tran_no ?? '' }}"
                data-debit_url="{{ route('docs.coverdebitnote', ['endorsement_no' => $cover->endorsement_no ?? '']) }}"
                data-claim_notice_url="{{ route('docs.claimnotice', ['endorsement_no' => $cover->endorsement_no ?? '']) }}">
                <i class="bx bx-envelope me-1 align-middle"></i> Send Email
            </button>
        </div>
    </div>
@else
    <div class="row">
        @if ($type_of_bus->bus_type_id == 'FPR' || $type_of_bus->bus_type_id == 'FNP')
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2" id="endorse_cover">
                <i class="bx bx-plus me-1"></i> Endorse Cover</button>
            {{-- <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn" id="debtor_state"> <span class="fa fa-pencil-square-o" onclick="processCNC()"></span>Cancel Cover</button> --}}
            <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                id="process_renew">
                <i class="bx bx-repeat me-1"></i> Renew Cover</button>
            <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                id="generateRenewalNotice">
                <i class="bx bx-file me-1"></i> Renew Notice</button>
        @elseif($type_of_bus->bus_type_id == 'TPR')
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2" data-bs-toggle="modal"
                data-bs-target="#quarterly-figures-modal"> <i class="bx bx-stats me-1"></i> Quarterly Figures</button>
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2" id="profit_commission"
                data-bs-toggle="modal" data-bs-target="#profit-commission-modal"> <i class="bx bx-pie-chart me-1"></i>
                Profit Commission</button>
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2" id="portfolio"
                data-bs-toggle="modal" data-bs-target="#portfolio-modal"> <i class="bx bx-briefcase me-1"></i>
                Portfolio</button>
        @elseif($type_of_bus->bus_type_id == 'TNP')
            <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                data-bs-toggle="modal" data-bs-target="#mdpInstallmentModal"> <i class="bx bx-calendar me-1"></i>
                MDP</button>
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"> <i
                    class="fa fa-pencil-square-o me-1"></i> XOL & Reinstatement</button>
        @else
        @endif
    </div>
@endif
