{{-- Quarterly Figures Modal --}}
<div class="modal effect-scale md-wrapper" id="quarterly-figures-modal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="quarterlyFiguresLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 900px">
        <div class="modal-content">
            <form method="POST" action="{{ route('cover.save_quaterly_figures') }}" id="QuarterlyFiguresForm">
                @csrf
                <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no }}">
                <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white text-center" id="quarterlyFiguresLabel">
                        <i class="bx bx-stats me-2"></i>Capture Quarterly Figures
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <table>
                            <tr>
                                <td>
                                    <div class="mb-3">
                                        <label for="cover_year" class="form-label">Cover Year</label>
                                        <input type="text" class="form-control" id="cover_year" name="cover_year"
                                            value="{{ $year }}" required readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3">
                                        <label for="quarter" class="form-label">Quarter</label>
                                        <select name="quarter" id="quarter" class="form-select select2" required>
                                            <option value="">--Select Quarter--</option>
                                            <option value="1">Quarter One</option>
                                            <option value="2">Quarter Two</option>
                                            <option value="3">Quarter Three</option>
                                            <option value="4">Quarter Four</option>
                                        </select>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                        <div class="mb-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Rein Class</th>
                                        <th>Treaty</th>
                                        <th>Premium Type</th>
                                        <th>Commission Rate</th>
                                        <th>Premium</th>
                                        <th>Claim Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6">
                                            <h6 class="mb-0">Capture Premiums Below</h6>
                                        </td>
                                    </tr>
                                    @foreach ($coverpremtypes as $coverpremtype)
                                        <tr>
                                            <td>
                                                <input type="hidden" class="form-control" id="reinclass_code"
                                                    name="reinclass_code[]" value="{{ $coverpremtype->reinclass }}">
                                                <input type="text" class="form-control" id="reinclass_name"
                                                    name="reinclass_name[]" value="{{ $coverpremtype->class_name }}"
                                                    required readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" id="treaty"
                                                    name="treaty[]" value="{{ $coverpremtype->treaty }}" required
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="hidden" class="form-control" id="premtype_code"
                                                    name="premtype_code[]" value="{{ $coverpremtype->premtype_code }}"
                                                    required readonly>
                                                <input type="text" class="form-control" id="premtype_name"
                                                    name="premtype_name[]" value="{{ $coverpremtype->premtype_name }}"
                                                    required readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" id="comm_rate"
                                                    name="comm_rate[]" value="{{ $coverpremtype->comm_rate }}" required
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" id="premium_amount"
                                                    name="premium_amount[]"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    onchange="this.value=numberWithCommas(this.value)" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" id="claim_amount"
                                                    name="claim_amount[]"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    onchange="this.value=numberWithCommas(this.value)" required>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="quaterly-figures-save-btn"
                        class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Profit Commission Modal --}}
<div class="modal effect-scale md-wrapper" id="profit-commission-modal" data-bs-backdrop="static"
    data-bs-keyboard="false" tabindex="-1" aria-labelledby="profitCommissionLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 1000px">
        <div class="modal-content ProfitCommissionDiv">
            <form method="POST" action="{{ route('cover.save_profit_commission') }}" id="ProfitCommissionForm">
                @csrf
                <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no }}">
                <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white text-center" id="profitCommissionLabel">
                        <i class="bx bx-pie-chart me-2"></i>Capture Profit Commission
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <table>
                            <tr>
                                <td></td>
                                <td>
                                    <div class="mb-2">
                                        <label for="treaty_year" class="form-label">Cover Year</label>
                                        <select name="treaty_year" id="treaty_year" class="form-inputs select2">
                                            <option value="">--Select Treaty Year--</option>
                                            @foreach ($treaty_years as $treaty_year)
                                                <option value="{{ $treaty_year->account_year }}">
                                                    {{ $treaty_year->account_year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                        <div id="ProfitCommissionDiv"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Portfolios Modal --}}
<div class="modal effect-scale md-wrapper" id="portfolio-modal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="portfolioLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 1000px">
        <div class="modal-content PortfolioDiv">
            <form method="POST" action="{{ route('cover.save_portfolio') }}" id="PortfolioForm">
                @csrf
                <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white text-center" id="portfolioLabel">
                        <i class="bx bx-briefcase me-2"></i>Capture Portfolio IN/OUT
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="portfolio_type" class="form-label">Portfolio Type</label>
                            <select name="portfolio_type" id="portfolio_type" class="form-inputs select2" required
                                style="z-index: 1050;">
                                <option value="">--Select Portfolio Type--</option>
                                <option value="OUT">Portfolio OUT</option>
                                <option value="IN">Portfolio IN</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="portfolio_year" class="form-label">Cover Year</label>
                            <select name="portfolio_year" id="portfolio_year" class="form-inputs select2" required>
                                <option value="">--Select Portfolio Year--</option>
                                @foreach ($treaty_years as $treaty_year)
                                    <option value="{{ $treaty_year->account_year }}">
                                        {{ $treaty_year->account_year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-5">
                            <label for="orig_endorsement" class="form-label">Tied Cover Reference</label>
                            <select name="orig_endorsement" id="orig_endorsement" class="form-inputs select2"
                                required>
                            </select>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-3" id="portfolio_add_reinsurer">
                                <label for="port_reinsurer" class="form-label">Reinsurer</label>
                                <select name="port_reinsurer" id="port_reinsurer" class="form-inputs select2"
                                    required>
                                </select>
                            </div>
                            <div class="col-sm-2 port_share_div" id="port_share_div">
                                <label for="port_share" class="form-label">Affected Share</label>
                                <input type="text" name="port_share" id="port_share" class="form-control"
                                    required>
                            </div>
                            <div class="col-sm-3 port_share_div">
                                <label for="port_amt" class="form-label">Portfolio Amount(100%)</label>
                                <input type="text" name="port_amt" id="port_amt" class="form-control"
                                    onkeyup="this.value=numberWithCommas(this.value)"
                                    onchange="this.value=numberWithCommas(this.value)" required>
                            </div>
                            <div class="col-sm-2 port_share_div">
                                <label for="port_prm_rate" class="form-label">Portfolio Prem Rate</label>
                                <input type="text" name="port_prm_rate" id="port_prm_rate" class="form-control"
                                    required>
                            </div>
                            <div class="col-sm-2 port_share_div">
                                <label for="port_loss_rate" class="form-label">Portfolio Loss Rate</label>
                                <input type="text" name="port_loss_rate" id="port_loss_rate" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="portfolio-save-btn"
                        class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MDP Installment Modal --}}
<div class="modal effect-scale md-wrapper" id="mdpInstallmentModal" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="mdpInstallmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <form method="POST" action="{{ route('cover.mdp_installment_endorsement') }}" id="mdpInstallmentForm">
                @csrf
                <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no }}">
                <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white text-center" id="mdpInstallmentLabel">
                        <i class="bx bx-calendar me-2"></i>MDP Installments
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="mdp-layer">No. of Layers</label>
                            <input type="text" id="mdp-layer" value="{{ $reinLayersCount }}" class="form-inputs"
                                disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="mdp-amount">Total MDP amount</label>
                            <input type="text" id="mdp-amount" class="form-inputs"
                                value="{{ number_format($mdpAmount, 2) }}" disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="mdp-installment">Installment</label>
                            <select name="installment_no" id="mdp-installment" class="form-inputs select2" required>
                                <option value="">--Select Installment--</option>
                                @foreach ($mdpInstallments as $ins)
                                    <option data-total_amt="{{ $ins->installment_amt }}"
                                        value="{{ $ins->installment_no }}">
                                        Ins.{{ $ins->installment_no }}-({{ formatDate($ins->installment_date) }})-({{ number_format($ins->installment_amt, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <hr class="my-3">
                        <div id="mdp-installments-section"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="mdp-inst-save-btn"
                        class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cover Endorsement Modal --}}
<div class="modal effect-scale md-wrapper" id="endorse-cover-modal" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="coverEndorsementLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="width: 80%;">
        <div class="modal-content">
            <form method="POST" action="{{ route('cover.process_cover_endorsement') }}" id="coverEndorsementForm">
                @csrf
                <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no }}">
                <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                <input type="hidden" name="endorsed_effective_sum_insured"
                    value="{{ $latest_endorsement->apply_eml == 'Y' ? number_format($latest_endorsement->eml_amount, 2) : number_format($latest_endorsement->total_sum_insured, 2) }}">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title dc-modal-title text-white" id="coverEndorsementLabel">
                        <i class="bx bx-plus me-2"></i>Cover Endorsement
                    </h5>
                    <button type="button" class="btn-close btn-close-white cancelCoverEndorsementForm"
                        data-bs-dismiss="modal" aria-label="Cancel"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="endorse_type" class="form-label">Endorsement Type</label>
                            <div class="cover-card">
                                <select class="form-inputs select2" name="endorse_type" id="endorse_type" required>
                                    <option value="">-- Select Endorsement Type --</option>
                                    @foreach ($EndorsementTypes as $EndorsementType)
                                        <option value="{{ $EndorsementType->endorse_type_slug }}"
                                            data-trans_type="{{ $EndorsementType->transaction_type }}">
                                            {{ $EndorsementType->endorse_type_descr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="form-group" id="current_section_div">
                        <div class="row">
                            <div class="col-md-3 endorsement_section_div change_in_sum_insured_type_div">
                                <label for="change_in_sum_insured_type" class="form-label">Change type</label>
                                <div class="cover-card">
                                    <select class="form-inputs endorsement_section change_in_sum_insured_type"
                                        name="change_in_sum_insured_type" id="change_in_sum_insured_type" required>
                                        <option value="" selected>--Select--</option>
                                        <option value="increase">Increase</option>
                                        <option value="decrease">Decrease</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 endorsement_section_div current_total_sum_insured_div mb-2">
                                <label class="form-label">Current Sum Insured (100%)</label>
                                <input type="text"
                                    class="form-inputs endorsement_section current_total_sum_insured"
                                    id="current_total_sum_insured" name="current_total_sum_insured"
                                    value="{{ number_format($latest_endorsement->total_sum_insured, 2) }}" required
                                    readonly>
                            </div>
                            <div class="col-md-3 endorsement_section_div effective_sum_insured_div">
                                <label class="form-label">Current Effective Sum Insured</label>
                                <input type="text"
                                    class="form-inputs endorsement_section amount effective_sum_insured"
                                    id="effective_sum_insured" name="effective_sum_insured"
                                    value="{{ $latest_endorsement->apply_eml == 'Y' ? number_format($latest_endorsement->eml_amount, 2) : number_format($latest_endorsement->total_sum_insured, 2) }}"
                                    required readonly>
                            </div>
                            <div class="col-md-3 endorsement_section_div current_fac_share_offered_div mb-2">
                                <label class="form-label">Current Share Offered(%)</label>
                                <input type="number"
                                    class="form-inputs endorsement_section current_fac_share_offered"
                                    id="current_fac_share_offered" name="current_fac_share_offered" max="100"
                                    min="0"
                                    value="{{ $latest_endorsement->share_offered ? number_format($latest_endorsement->share_offered, 2) : '0' }}"
                                    required readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 endorsement_section_div current_cede_premium_div mb-2">
                                <label class="form-label">Current Premium</label>
                                <input type="text"
                                    class="form-inputs endorsement_section amount current_cede_premium"
                                    id="current_cede_premium" name="current_cede_premium"
                                    value="{{ number_format($latest_endorsement->cedant_premium, 2) }}" required
                                    readonly>
                            </div>
                            <div class="col-md-3 endorsement_section_div current_rein_premium_div mb-2">
                                <label class="form-label">Current Reinsurer Premium</label>
                                <input type="text" class="form-inputs endorsement_section current_rein_premium"
                                    id="current_rein_premium" name="current_rein_premium"
                                    value="{{ number_format($latest_endorsement->rein_premium, 2) }}"
                                    onkeyup="this.value=numberWithCommas(this.value)" required readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 endorsement_section_div start_date_div">
                                <label class="form-label">Current Start Date</label>
                                <input type="date" class="form-inputs start_date" id="start_date"
                                    name="start_date" value="{{ $latest_endorsement->cover_to->format('Y-m-d') }}"
                                    required readonly />
                            </div>
                            <div class="col-md-3 endorsement_section_div ppw_days_div">
                                <label class="form-label">Current PPW Days</label>
                                <input type="string" class="form-inputs endorsement_section ppw_days" id="ppw_days"
                                    name="ppw_days" value="{{ $latest_endorsement->premium_payment_days }}" readonly
                                    required />
                            </div>
                            <div class="col-md-3 endorsement_section_div premium_due_date_div">
                                <label class="form-label">Current Premium Due Date</label>
                                <input type="hidden" value="{{ $latest_endorsement->premium_payment_code }}"
                                    name="premium_payment_code" />
                                <input type="date" class="form-inputs endorsement_section premium_due_date"
                                    id="premium_due_date" name="premium_due_date" value="{{ $premium_due_date }}"
                                    required readonly />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 endorsement_section_div coverfrom_div">
                                <label class="form-label">Current Cover Start Date</label>
                                <input type="date" class="form-inputs endorsement_section coverfrom"
                                    id="coverfrom" name="coverfrom"
                                    value="{{ $latest_endorsement->cover_from->format('Y-m-d') }}" readonly required>
                            </div>
                            <div class="col-md-3 endorsement_section_div coverto_div mb-2">
                                <label class="form-label">Current Cover End Date</label>
                                <input type="date" class="form-inputs endorsement_section coverto" id="coverto"
                                    name="coverto" value="{{ $latest_endorsement->cover_to->format('Y-m-d') }}"
                                    readonly required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 endorsement_section_div insured_name_div mb-2">
                                <label class="required">Old Insured Name</label>
                                <div class="cover-card">
                                    <input type="text" class="form-inputs endorsement_section insured_name disable"
                                        id="insured_name" name="insured_name"
                                        value="{{ $latest_endorsement->insured_name }}" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-md-5 endorsement_section_div new_insured_name_div mb-2">
                                <label class="required">New Insured Name</label>
                                <div class="cover-card">
                                    <select class="form-inputs select2 endorsement_section new_insured_name"
                                        name="new_insured_name" id="new_insured_name" required>
                                        <option value="" selected>-- Select --</option>
                                        <option value="{{ $latest_endorsement->insured_name }}">
                                            {{ $latest_endorsement->insured_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 endorsement_section_div endorsed_total_sum_insured_div mb-2">
                                <label class="form-label">Endorsed Sum Insured (100%)</label>
                                <input type="text"
                                    class="form-inputs endorsement_section endorsed_total_sum_insured"
                                    id="endorsed_total_sum_insured" name="endorsed_total_sum_insured"
                                    onkeyup="this.value=numberWithCommas(this.value)" required>
                            </div>
                            <div class="col-md-3 endorsement_section_div endorsed_cede_premium_div mb-2">
                                <label class="form-label">Endorsed Cedant Premium</label>
                                <input type="text" class="form-inputs endorsement_section endorsed_cede_premium"
                                    id="endorsed_cede_premium" name="endorsed_cede_premium" value="0"
                                    onkeyup="this.value=numberWithCommas(this.value)" required>
                            </div>
                            <div class="col-md-3 endorsement_section_div new_fac_share_offered_div mb-2">
                                <label class="form-label">New Share Offered(%)</label>
                                <input type="number" class="form-inputs endorsement_section new_fac_share_offered"
                                    id="new_fac_share_offered" name="new_fac_share_offered" max="100"
                                    min="0"
                                    value="{{ $latest_endorsement->share_offered ? number_format($latest_endorsement->share_offered, 2) : '0' }}"
                                    onkeyup="this.value=numberWithCommas(this.value)" required>
                            </div>
                            <div class="col-md-3 endorsement_section_div apply_eml_div eml-div mb-2">
                                <label for="apply_eml">Apply EML</label>
                                <select name="apply_eml" class="form-inputs select2 endorsement_section apply_eml"
                                    id="apply_eml" required>
                                    <option value="">-- Select --</option>
                                    <option value="Y" @if ($latest_endorsement->apply_eml == 'Y') selected @endif>Yes
                                    </option>
                                    <option value="N" @if ($latest_endorsement->apply_eml == 'N') selected @endif>No
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 eml_rate_div endorsement_section_div eml-div mb-2">
                                <label class="form-label">EML Rate</label>
                                <input type="number" class="form-inputs endorsement_section eml_rate" id="eml_rate"
                                    name="eml_rate" value="{{ $latest_endorsement->eml_rate }}" min="0"
                                    max="100" required>
                            </div>
                            <div class="col-md-3 eml_amt_div endorsement_section_div mb-2">
                                <label class="form-label">EML Amount</label>
                                <input type="text" class="form-inputs endorsement_section amount eml_amt"
                                    id="eml_amt" name="eml_amt"
                                    value="{{ number_format($latest_endorsement->eml_amount, 2) }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 endorsement_section_div new_total_sum_insured_div mb-2">
                                <label class="form-label">New Sum Insured (100%)</label>
                                <input type="text" class="form-inputs endorsement_section new_total_sum_insured"
                                    id="new_total_sum_insured" name="new_total_sum_insured" required>
                            </div>
                            <div class="col-md-3 endorsement_section_div new_cede_premium_div mb-2">
                                <label class="form-label">New Cedant Premium</label>
                                <input type="text" class="form-inputs endorsement_section new_cede_premium"
                                    id="new_cede_premium" name="new_cede_premium" value="0"
                                    onkeyup="this.value=numberWithCommas(this.value)" required>
                            </div>
                            <div class="col-md-3 endorsement_section_div new_effective_sum_insured_div">
                                <label class="form-label">New Effective Sum Insured</label>
                                <input type="text"
                                    class="form-inputs endorsement_section new_effective_sum_insured"
                                    id="new_effective_sum_insured" name="new_effective_sum_insured"
                                    onkeyup="this.value=numberWithCommas(this.value)" required readonly>
                            </div>
                            <div class="col-md-3 endorsement_section_div new_rein_premium_div mb-2">
                                <label class="form-label">New Reinsurer Premium</label>
                                <input type="text" class="form-inputs endorsement_section new_rein_premium"
                                    id="new_rein_premium" name="new_rein_premium"
                                    onkeyup="this.value=numberWithCommas(this.value)" value="0" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 endorsement_section_div brokerage_comm_type_div">
                                <label class="form-label">Brokerage Commission Type</label>
                                <select name="brokerage_comm_type" id="brokerage_comm_type"
                                    class="form-inputs endorsement_section brokerage_comm_type select2" required>
                                    @if ($latest_endorsement->brokerage_comm_type == 'R')
                                        <option value="R" selected>Rate (<small><i>reinsurer rate - cedant
                                                    rate)</i></small></option>
                                    @else
                                        <option value="A" selected>Quoted Amount</option>
                                    @endif
                                </select>
                            </div>
                            @if ($latest_endorsement->brokerage_comm_type == 'R')
                                <div class="col-md-3 endorsement_section_div brokerage_comm_rate_div">
                                    <label class="form-label">Brokerage Comm. Rate</label>
                                    <input type="text" class="form-inputs endorsement_section amount"
                                        id="brokerage_comm_rate" name="brokerage_comm_rate"
                                        value="{{ $latest_endorsement->brokerage_comm_rate }}">
                                </div>
                            @else
                                <div class="col-md-3 endorsement_section_div brokerage_comm_amt_div">
                                    <label class="form-label">Brokerage Comm. Amount</label>
                                    <input type="text"
                                        class="form-inputs endorsement_section amount brokerage_comm_amt"
                                        id="brokerage_comm_amt" name="brokerage_comm_amt"
                                        value="{{ $latest_endorsement->brokerage_comm_amt }}">
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-3 endorsement_section_div new_coverfrom_div">
                                <label class="form-label">New Cover Start Date</label>
                                <input type="date" class="form-inputs endorsement_section new_coverfrom"
                                    id="new_coverfrom" name="new_coverfrom"
                                    value="{{ $latest_endorsement->cover_to->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3 endorsement_section_div new_coverto_div mb-2">
                                <label class="form-label">New Cover End Date</label>
                                <input type="date" class="form-inputs endorsement_section new_coverto"
                                    id="new_coverto" name="new_coverto" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 endorsement_section_div extension_days_div my-2">
                                <label class="form-label">Extension Days</label>
                                <input type="number" class="form-inputs endorsement_section extension_days"
                                    id="extension_days" name="extension_days" required />
                            </div>
                            <div class="col-md-3 endorsement_section_div new_premium_due_date_div my-2">
                                <label class="form-label">New Premium Due Date</label>
                                <input type="date" class="form-inputs endorsement_section new_premium_due_date"
                                    id="new_premium_due_date" name="new_premium_due_date" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 endorse_narration_div">
                                <label class="form-label required">Narration</label>
                                <textarea name="endorse_narration" id="endorse_narration" class="form-inputs resize-none" rows="6"
                                    cols="100%" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm cancelCoverEndorsementForm"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="cover-endorse-save-btn"
                        class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
