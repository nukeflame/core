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
<style>
    /* Modern Cover Endorsement Modal Styles */
    /* #endorse-cover-modal .modal-content {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: none;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    #endorse-cover-modal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-bottom: none;
        padding: 1.25rem 1.5rem;
        position: relative;
    }

    #endorse-cover-modal .modal-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
    }

    #endorse-cover-modal .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    #endorse-cover-modal .modal-body {
        padding: 1.5rem;
        max-height: 70vh;
        overflow-y: auto;
    } */

    /* Endorsement Type Selector */
    #endorse-cover-modal .endorsement-type-section {
        /* background: linear-gradient(135deg, #e0e5ec 0%, #f5f7fa 100%); */
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(102, 126, 234, 0.2);
    }

    #endorse-cover-modal .endorsement-type-section label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    /* Section Headers */
    #endorse-cover-modal .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.625rem 1rem;
        border-radius: 8px;
        margin: 1.25rem 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.25);
    }

    #endorse-cover-modal .section-header i {
        font-size: 1rem;
    }

    /* Form Field Cards */
    #endorse-cover-modal .field-card {
        background: white;
        border-radius: 10px;
        padding: 0.875rem;
        margin-bottom: 0.75rem;
        border: 1px solid #3634346e;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    #endorse-cover-modal .field-card:hover {
        border-color: #3634346e;
    }

    #endorse-cover-modal .field-card label {
        font-weight: 500;
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    #endorse-cover-modal .field-card label.required::after {
        content: '*';
        color: #ef4444;
        margin-left: 2px;
    }

    /* Premium Fields - Special Styling */
    #endorse-cover-modal .premium-field {
        background: transparent;
        border-color: #3634346e;
    }

    #endorse-cover-modal .premium-field:hover {
        border-color: #22c55e;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
    }

    #endorse-cover-modal .premium-field label {
        color: #64748b;
    }

    #endorse-cover-modal .premium-field .form-inputs {
        background: #fafbfc;
        font-weight: 600;
        color: #374151;
    }

    /* Endorsed/New Fields - Highlighted */
    /* #endorse-cover-modal .endorsed-field {
        background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%);
        border-color: #fde047;
    }

    #endorse-cover-modal .endorsed-field:hover {
        border-color: #eab308;
        box-shadow: 0 4px 12px rgba(234, 179, 8, 0.2);
    }

    #endorse-cover-modal .endorsed-field label {
        color: #854d0e;
    } */

    #endorse-cover-modal .new-value-field {
        background: transparent;
        border-color: #3634346e;
    }

    #endorse-cover-modal .new-value-field:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }

    #endorse-cover-modal .new-value-field label {
        color: #64748b;
    }

    /* Input Styling */
    #endorse-cover-modal .form-inputs,
    #endorse-cover-modal .form-control,
    #endorse-cover-modal .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background: #fafbfc;
    }

    #endorse-cover-modal .form-inputs:focus,
    #endorse-cover-modal .form-control:focus,
    #endorse-cover-modal .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        background: white;
    }

    #endorse-cover-modal .form-inputs[readonly] {
        background: #f1f5f9;
        color: #64748b;
        font-style: italic;
    }

    /* Select2 Container Styling */
    #endorse-cover-modal .select2-container--default .select2-selection--single {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        height: 42px;
        background: #fafbfc;
    }

    #endorse-cover-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
        color: #374151;
    }

    #endorse-cover-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }

    /* Narration Textarea */
    #endorse-cover-modal #endorse_narration {
        min-height: 100px;
        resize: vertical;
        background: linear-gradient(135deg, #fafbfc 0%, #f1f5f9 100%);
    }

    #endorse-cover-modal #endorse_narration:focus {
        background: white;
    }

    /* Modal Footer */
    #endorse-cover-modal .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        gap: 0.75rem;
    }

    #endorse-cover-modal .btn-cancel {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
        border: 1px solid #fca5a5;
        border-radius: 8px;
        padding: 0.625rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    #endorse-cover-modal .btn-cancel:hover {
        background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 38, 38, 0.2);
    }

    #endorse-cover-modal .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.625rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    #endorse-cover-modal .btn-submit:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    #endorse-cover-modal .btn-submit:active {
        transform: translateY(0);
    }

    /* Field Group Grid */
    #endorse-cover-modal .fields-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
    }

    /* Premium Summary Card */
    #endorse-cover-modal .premium-summary {
        background: transparent;
        border-radius: 12px;
        padding: 1rem;
        margin: 1rem 0;
        border: 1px solid #e2e8f0;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        #endorse-cover-modal .modal-dialog {
            width: 95% !important;
            margin: 0.5rem auto;
        }

        #endorse-cover-modal .fields-grid {
            grid-template-columns: 1fr;
        }

        #endorse-cover-modal .modal-body {
            padding: 1rem;
        }
    }

    /* Animation for field visibility */
    #endorse-cover-modal [class*="_div"] {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    /* Loading State */
    #endorse-cover-modal .btn-submit.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    #endorse-cover-modal .btn-submit.loading::after {
        content: '';
        width: 16px;
        height: 16px;
        border: 2px solid transparent;
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        display: inline-block;
        margin-left: 8px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div class="modal effect-scale md-wrapper" id="endorse-cover-modal" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="coverEndorsementLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="width: 85%; max-width: 1200px;">
        <div class="modal-content">
            <form method="POST" action="{{ route('cover.process_cover_endorsement') }}" id="coverEndorsementForm">
                @csrf
                <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no }}">
                <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                <input type="hidden" name="endorsed_effective_sum_insured"
                    value="{{ $latest_endorsement->apply_eml == 'Y' ? number_format($latest_endorsement->eml_amount, 2) : number_format($latest_endorsement->total_sum_insured, 2) }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="coverEndorsementLabel">
                        <i class="bx bx-edit-alt"></i>
                        <span>Cover Endorsement</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white cancelCoverEndorsementForm"
                        data-bs-dismiss="modal" aria-label="Cancel"></button>
                </div>

                <div class="modal-body">
                    {{-- Endorsement Type Selection --}}
                    <div class="endorsement-type-section">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label for="endorse_type" class="form-label">
                                    <i class="bx bx-list-check me-1"></i>Endorsement Type
                                </label>
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
                            <div class="col-md-6 d-none" id="change_type_wrapper">
                                <div class="field-card endorsed-field mb-0">
                                    <label for="change_in_sum_insured_type">Change Direction</label>
                                    <select class="form-inputs endorsement_section change_in_sum_insured_type"
                                        name="change_in_sum_insured_type" id="change_in_sum_insured_type" required>
                                        <option value="" selected>-- Select --</option>
                                        <option value="increase">Increase</option>
                                        <option value="decrease">Decrease</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Current Values Section --}}
                    <div class="form-group" id="current_section_div">
                        <div class="section-header">
                            <i class="bx bx-info-circle"></i>
                            <span>Current Values</span>
                        </div>

                        {{-- Sum Insured Fields --}}
                        <div class="fields-grid">
                            <div class="field-card endorsement_section_div current_total_sum_insured_div">
                                <label>Current Sum Insured (100%)</label>
                                <input type="text"
                                    class="form-inputs endorsement_section current_total_sum_insured"
                                    id="current_total_sum_insured" name="current_total_sum_insured"
                                    value="{{ number_format($latest_endorsement->total_sum_insured, 2) }}" required
                                    readonly>
                            </div>
                            <div class="field-card endorsement_section_div effective_sum_insured_div">
                                <label>Current Effective Sum Insured</label>
                                <input type="text"
                                    class="form-inputs endorsement_section amount effective_sum_insured"
                                    id="effective_sum_insured" name="effective_sum_insured"
                                    value="{{ $latest_endorsement->apply_eml == 'Y' ? number_format($latest_endorsement->eml_amount, 2) : number_format($latest_endorsement->total_sum_insured, 2) }}"
                                    required readonly>
                            </div>
                            <div class="field-card endorsement_section_div current_fac_share_offered_div">
                                <label>Current Share Offered (%)</label>
                                <input type="number"
                                    class="form-inputs endorsement_section current_fac_share_offered"
                                    id="current_fac_share_offered" name="current_fac_share_offered" max="100"
                                    min="0"
                                    value="{{ $latest_endorsement->share_offered ? number_format($latest_endorsement->share_offered, 2) : '0' }}"
                                    required readonly>
                            </div>
                        </div>

                        {{-- Premium Fields --}}
                        <div class="fields-grid">
                            <div class="field-card premium-field endorsement_section_div current_cede_premium_div">
                                <label><i class="bx bx-dollar-circle me-1"></i>Current Premium</label>
                                <input type="text"
                                    class="form-inputs endorsement_section amount current_cede_premium"
                                    id="current_cede_premium" name="current_cede_premium"
                                    value="{{ number_format($latest_endorsement->cedant_premium, 2) }}" required
                                    readonly>
                            </div>
                            <div class="field-card premium-field endorsement_section_div current_rein_premium_div">
                                <label><i class="bx bx-transfer me-1"></i>Current Reinsurer Premium</label>
                                <input type="text" class="form-inputs endorsement_section current_rein_premium"
                                    id="current_rein_premium" name="current_rein_premium"
                                    value="{{ number_format($latest_endorsement->rein_premium, 2) }}"
                                    onkeyup="this.value=numberWithCommas(this.value)" required readonly>
                            </div>
                        </div>

                        {{-- Date Fields --}}
                        <div class="fields-grid">
                            <div class="field-card endorsement_section_div start_date_div">
                                <label><i class="bx bx-calendar me-1"></i>Current Start Date</label>
                                <input type="date" class="form-inputs start_date" id="start_date"
                                    name="start_date" value="{{ $latest_endorsement->cover_to->format('Y-m-d') }}"
                                    required readonly />
                            </div>
                            <div class="field-card endorsement_section_div ppw_days_div">
                                <label><i class="bx bx-time me-1"></i>Current PPW Days</label>
                                <input type="string" class="form-inputs endorsement_section ppw_days" id="ppw_days"
                                    name="ppw_days" value="{{ $latest_endorsement->premium_payment_days }}" readonly
                                    required />
                            </div>
                            <div class="field-card endorsement_section_div premium_due_date_div">
                                <label><i class="bx bx-calendar-check me-1"></i>Current Premium Due Date</label>
                                <input type="hidden" value="{{ $latest_endorsement->premium_payment_code }}"
                                    name="premium_payment_code" />
                                <input type="date" class="form-inputs endorsement_section premium_due_date"
                                    id="premium_due_date" name="premium_due_date" value="{{ $premium_due_date }}"
                                    required readonly />
                            </div>
                        </div>

                        {{-- Cover Period --}}
                        <div class="fields-grid">
                            <div class="field-card endorsement_section_div coverfrom_div">
                                <label><i class="bx bx-log-in me-1"></i>Current Cover Start Date</label>
                                <input type="date" class="form-inputs endorsement_section coverfrom"
                                    id="coverfrom" name="coverfrom"
                                    value="{{ $latest_endorsement->cover_from->format('Y-m-d') }}" readonly required>
                            </div>
                            <div class="field-card endorsement_section_div coverto_div">
                                <label><i class="bx bx-log-out me-1"></i>Current Cover End Date</label>
                                <input type="date" class="form-inputs endorsement_section coverto" id="coverto"
                                    name="coverto" value="{{ $latest_endorsement->cover_to->format('Y-m-d') }}"
                                    readonly required>
                            </div>
                        </div>

                        {{-- Insured Name --}}
                        <div class="row">
                            <div class="col-md-6 endorsement_section_div insured_name_div">
                                <div class="field-card">
                                    <label><i class="bx bx-user me-1"></i>Old Insured Name</label>
                                    <input type="text" class="form-inputs endorsement_section insured_name disable"
                                        id="insured_name" name="insured_name"
                                        value="{{ $latest_endorsement->insured_name }}" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- New/Endorsed Values Section --}}
                    <div class="form-group mb-0" id="endorsed_section_div">
                        <div class="section-header">
                            <i class="bx bx-edit"></i>
                            <span>Endorsed / New Values</span>
                        </div>

                        {{-- New Insured Name --}}
                        <div class="row">
                            <div class="col-md-6 endorsement_section_div new_insured_name_div">
                                <div class="field-card new-value-field">
                                    <label class="required"><i class="bx bx-user-plus me-1"></i>New Insured
                                        Name</label>
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

                        {{-- Endorsed Values --}}
                        <div class="fields-grid">
                            <div
                                class="field-card endorsed-field endorsement_section_div endorsed_total_sum_insured_div">
                                <label class="required">Endorsed Sum Insured (100%)</label>
                                <input type="text"
                                    class="form-inputs endorsement_section endorsed_total_sum_insured"
                                    id="endorsed_total_sum_insured" name="endorsed_total_sum_insured"
                                    onkeyup="this.value=numberWithCommas(this.value)" required
                                    placeholder="Enter amount...">
                            </div>
                            <div
                                class="field-card endorsed-field premium-field endorsement_section_div endorsed_cede_premium_div">
                                <label class="required"><i class="bx bx-dollar me-1"></i>Endorsed Cedant
                                    Premium</label>
                                <input type="text" class="form-inputs endorsement_section endorsed_cede_premium"
                                    id="endorsed_cede_premium" name="endorsed_cede_premium" value="0"
                                    onkeyup="this.value=numberWithCommas(this.value)" required
                                    placeholder="Enter premium...">
                            </div>
                            <div class="field-card new-value-field endorsement_section_div new_fac_share_offered_div">
                                <label class="required">New Share Offered (%)</label>
                                <input type="number" class="form-inputs endorsement_section new_fac_share_offered"
                                    id="new_fac_share_offered" name="new_fac_share_offered" max="100"
                                    min="0"
                                    value="{{ $latest_endorsement->share_offered ? number_format($latest_endorsement->share_offered, 2) : '0' }}"
                                    onkeyup="this.value=numberWithCommas(this.value)" required>
                            </div>
                        </div>

                        {{-- EML Fields --}}
                        <div class="fields-grid">
                            <div class="field-card endorsement_section_div apply_eml_div eml-div">
                                <label for="apply_eml"><i class="bx bx-check-circle me-1"></i>Apply EML</label>
                                <select name="apply_eml" class="form-inputs select2 endorsement_section apply_eml"
                                    id="apply_eml" required>
                                    <option value="">-- Select --</option>
                                    <option value="Y" @if ($latest_endorsement->apply_eml == 'Y') selected @endif>Yes
                                    </option>
                                    <option value="N" @if ($latest_endorsement->apply_eml == 'N') selected @endif>No
                                    </option>
                                </select>
                            </div>
                            <div class="field-card eml_rate_div endorsement_section_div eml-div">
                                <label><i class="bx bx-percentage me-1"></i>EML Rate</label>
                                <input type="number" class="form-inputs endorsement_section eml_rate" id="eml_rate"
                                    name="eml_rate" value="{{ $latest_endorsement->eml_rate }}" min="0"
                                    max="100" required>
                            </div>
                            <div class="field-card eml_amt_div endorsement_section_div">
                                <label>EML Amount</label>
                                <input type="text" class="form-inputs endorsement_section amount eml_amt"
                                    id="eml_amt" name="eml_amt"
                                    value="{{ number_format($latest_endorsement->eml_amount, 2) }}" required>
                            </div>
                        </div>

                        {{-- Calculated New Values --}}
                        <div class="premium-summary">
                            <div class="row g-3">
                                <div class="col-md-3 endorsement_section_div new_total_sum_insured_div">
                                    <div class="field-card new-value-field mb-0">
                                        <label><i class="bx bx-calculator me-1"></i>New Sum Insured (100%)</label>
                                        <input type="text"
                                            class="form-inputs endorsement_section new_total_sum_insured"
                                            id="new_total_sum_insured" name="new_total_sum_insured" required readonly
                                            style="font-weight: 700; font-size: 1rem;">
                                    </div>
                                </div>
                                <div class="col-md-3 endorsement_section_div new_cede_premium_div">
                                    <div class="field-card premium-field new-value-field mb-0">
                                        <label><i class="bx bx-calculator me-1"></i>New Cedant Premium</label>
                                        <input type="text" class="form-inputs endorsement_section new_cede_premium"
                                            id="new_cede_premium" name="new_cede_premium" value="0"
                                            onkeyup="this.value=numberWithCommas(this.value)" required
                                            style="font-weight: 700; font-size: 1rem;">
                                    </div>
                                </div>
                                <div class="col-md-3 endorsement_section_div new_effective_sum_insured_div">
                                    <div class="field-card new-value-field mb-0">
                                        <label><i class="bx bx-target-lock me-1"></i>New Effective Sum Insured</label>
                                        <input type="text"
                                            class="form-inputs endorsement_section new_effective_sum_insured"
                                            id="new_effective_sum_insured" name="new_effective_sum_insured"
                                            onkeyup="this.value=numberWithCommas(this.value)" required readonly
                                            style="font-weight: 700; font-size: 1rem;">
                                    </div>
                                </div>
                                <div class="col-md-3 endorsement_section_div new_rein_premium_div">
                                    <div class="field-card premium-field new-value-field mb-0">
                                        <label><i class="bx bx-transfer me-1"></i>New Reinsurer Premium</label>
                                        <input type="text" class="form-inputs endorsement_section new_rein_premium"
                                            id="new_rein_premium" name="new_rein_premium"
                                            onkeyup="this.value=numberWithCommas(this.value)" value="0" required
                                            style="font-weight: 700; font-size: 1rem;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Brokerage Commission --}}
                        <div class="fields-grid">
                            <div class="field-card endorsement_section_div brokerage_comm_type_div">
                                <label><i class="bx bx-money me-1"></i>Brokerage Commission Type</label>
                                <select name="brokerage_comm_type" id="brokerage_comm_type"
                                    class="form-inputs endorsement_section brokerage_comm_type select2" required>
                                    @if ($latest_endorsement->brokerage_comm_type == 'R')
                                        <option value="R" selected>Rate (reinsurer rate - cedant rate)</option>
                                    @else
                                        <option value="A" selected>Quoted Amount</option>
                                    @endif
                                </select>
                            </div>
                            @if ($latest_endorsement->brokerage_comm_type == 'R')
                                <div class="field-card endorsement_section_div brokerage_comm_rate_div">
                                    <label>Brokerage Comm. Rate (%)</label>
                                    <input type="text" class="form-inputs endorsement_section amount"
                                        id="brokerage_comm_rate" name="brokerage_comm_rate"
                                        value="{{ $latest_endorsement->brokerage_comm_rate }}">
                                </div>
                            @else
                                <div class="field-card endorsement_section_div brokerage_comm_amt_div">
                                    <label>Brokerage Comm. Amount</label>
                                    <input type="text"
                                        class="form-inputs endorsement_section amount brokerage_comm_amt"
                                        id="brokerage_comm_amt" name="brokerage_comm_amt"
                                        value="{{ $latest_endorsement->brokerage_comm_amt }}">
                                </div>
                            @endif
                        </div>

                        {{-- New Cover Dates --}}
                        <div class="fields-grid">
                            <div class="field-card new-value-field endorsement_section_div new_coverfrom_div">
                                <label><i class="bx bx-calendar-plus me-1"></i>New Cover Start Date</label>
                                <input type="date" class="form-inputs endorsement_section new_coverfrom"
                                    id="new_coverfrom" name="new_coverfrom"
                                    value="{{ $latest_endorsement->cover_to->format('Y-m-d') }}" required>
                            </div>
                            <div class="field-card new-value-field endorsement_section_div new_coverto_div">
                                <label><i class="bx bx-calendar-x me-1"></i>New Cover End Date</label>
                                <input type="date" class="form-inputs endorsement_section new_coverto"
                                    id="new_coverto" name="new_coverto" required>
                            </div>
                            <div class="field-card endorsement_section_div extension_days_div">
                                <label><i class="bx bx-plus-circle me-1"></i>Extension Days</label>
                                <input type="number" class="form-inputs endorsement_section extension_days"
                                    id="extension_days" name="extension_days" required placeholder="0" />
                            </div>
                            <div class="field-card new-value-field endorsement_section_div new_premium_due_date_div">
                                <label><i class="bx bx-calendar-event me-1"></i>New Premium Due Date</label>
                                <input type="date" class="form-inputs endorsement_section new_premium_due_date"
                                    id="new_premium_due_date" name="new_premium_due_date" required />
                            </div>
                        </div>

                        {{-- Narration --}}
                        <div class="row mt-3">
                            <div class="col-12 endorse_narration_div">
                                <div class="field-card">
                                    <label class="required" style="font-size: 0.9rem;">
                                        <i class="bx bx-note me-1"></i>Endorsement Narration
                                    </label>
                                    <textarea name="endorse_narration" id="endorse_narration" class="form-inputs" rows="4"
                                        style="resize: vertical; min-height: 100px;" required
                                        placeholder="Enter detailed narration for this endorsement..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden field for change type that we moved to top --}}
                        <div class="d-none endorsement_section_div change_in_sum_insured_type_div">
                            {{-- This is now rendered at the top, keeping original hidden for JS compatibility --}}
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel cancelCoverEndorsementForm" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="submit" id="cover-endorse-save-btn" class="btn btn-submit">
                        <i class="bx bx-check me-1"></i>Submit Endorsement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
