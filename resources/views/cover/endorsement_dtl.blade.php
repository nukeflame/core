@extends('layouts.app')

@section('content')
    <style type="text/css">
        #endorselist tbody tr {
            cursor: pointer;
        }

        #endorselist tbody tr:hover {
            background-color: rgb(77, 77, 157);
        }
    </style>

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Policies</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href>{{ $customer->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $cover_no }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <div class="row">
        @if ($type_of_bus->bus_type_id == 'FPR' || $type_of_bus->bus_type_id == 'FNP')
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2" id="endorse_cover"> Endorse
                Cover</button>
            {{-- <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn" id="debtor_state"> <span class="fa fa-pencil-square-o" onclick="processCNC()"></span>Cancel Cover</button> --}}
            <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                id="process_renew">
                <span></span>Renew Cover</button>
            <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                id="generateRenewalNotice">
                <span></span>Renew Notice</button>
        @elseif($type_of_bus->bus_type_id == 'TPR')
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2" data-bs-toggle="modal"
                data-bs-target="#quarterly-figures-modal"> Quarterly Figures</button>
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2" id="profit_commission"
                data-bs-toggle="modal" data-bs-target="#profit-commission-modal"> Profit Commission</button>
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2" id="portfolio"
                data-bs-toggle="modal" data-bs-target="#portfolio-modal"> Portfolio</button>
        @elseif($type_of_bus->bus_type_id == 'TNP')
            <button class="process_cover btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"
                data-bs-toggle="modal" data-bs-target="#mdpInstallmentModal"> <span></span>MDP</button>
            <button class="btn btn-sm btn-dark btn-wave waves-effect waves-light col-md-2 m-2 custom-btn"> <span
                    class="fa fa-pencil-square-o" onclick=""></span>XOL & Reinstatement</button>
        @else
        @endif
    </div>

    <div class="form-group">
        <div class="gy-4">
            <form method="POST" action="{{ route('cover.form') }}" id="new_cover_form">
                {{ csrf_field() }}
                <input type="text" name="customer_id" id="customer_id" value="{{ $customer->customer_id }}" hidden>
                <input type="text" name="trans_type" id="trans_type" hidden>
                <input type="text" name="endorse_type_slug" id="endorse_type_slug" hidden>
                <input type="text" name="cover_no" id="cover_no" value="{{ $cover_no }}" hidden>
                <input type="text" name="endorsement_no" id="endorsement_no"
                    value="{{ $latest_endorsement->endorsement_no }}" hidden>
            </form>
            <form method="POST" action="{{ route('cover.renewal_notice') }}" id="new_renewal_notice">
                {{ csrf_field() }}
                <input type="text" name="customer_id" id="customer_id" value="{{ $customer->customer_id }}" hidden>
                <input type="text" name="cover_no" id="cover_no" value="{{ $cover_no }}" hidden>
            </form>
            <div class="customer-endorsement row">
                <div class="col-sm-7" style="margin-top: 0px;">
                    <table class="table table-responsive">
                        <tbody>
                            <tr>
                                <th id="tline"><strong>Cover Number</strong></th>
                                <th>
                                    {{ $cover_no }}
                                </th>
                            </tr>

                            <tr>
                                <th><strong>Business Type</strong></th>
                                <td>
                                    {{ $type_of_bus->bus_type_name }}
                                </td>
                            </tr>
                            @if ($type_of_bus->bus_type_id == 'FPR' || $type_of_bus->bus_type_id == 'FNP')
                                <tr>
                                    <th><strong>Class</strong></th>
                                    <td>
                                        {{ $class->class_name }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-5" style="margin-top: 0px;">
                    <table class="table table-responsive">
                        <tr>
                            <th><strong>Current Cover From</strong></th>
                            <td>
                                {{ formatDate($latest_endorsement->cover_from) }}
                            </td>
                        </tr>
                        <tr>
                            <th><strong>Current Cover To</strong></th>
                            <td>
                                {{ formatDate($latest_endorsement->cover_to) }}
                            </td>
                        </tr>
                        <tr>
                            <th><strong>Current Status</strong></th>
                            <td>
                                @if ($latest_endorsement->status == 'A')
                                    <span class="badge bg-success-gradient col-md-4">Active</span>
                                @else
                                    <span class="badge bg-danger-gradien col-md-4">Not Active</span>
                                @endif
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="row-cols-12">
        <div class="card mb-2 custom-card border col">
            <div class="card-body pt-0">
                <nav>
                    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                        id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-endorsement-list" data-bs-toggle="tab"
                            data-bs-target="#endorsement-list" type="button" role="tab" aria-selected="true"><i
                                class="bx bx-file me-1 align-middle"></i>Endorsement List</button>
                        <button class="nav-link" id="nav-coverlist-tab" data-bs-toggle="tab" data-bs-target="#coverlist-tab"
                            type="button" role="tab" aria-selected="false" tabindex="-1" style="visibility:hidden"><i
                                class="bx bx-file me-1 align-middle"></i>Cover List</button>
                        <button class="nav-link" id="nav-claimlist-tab" data-bs-toggle="tab"
                            data-bs-target="#claimlist-tab" type="button" role="tab" aria-selected="false"
                            tabindex="-1" style="visibility:hidden"><i class="bx bx-medal me-1 align-middle"></i>Claim
                            List</button>
                        <button class="nav-link" id="nav-statement-tab" data-bs-toggle="tab"
                            data-bs-target="#statement-tab" type="button" role="tab" aria-selected="false"
                            tabindex="-1" style="visibility:hidden"><i
                                class="bx bx-file-blank me-1 align-middle"></i>Statement</button>
                    </div>
                </nav>
                <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                    <div class="tab-pane active show" id="endorsement-list" role="tabpanel"
                        aria-labelledby="nav-endorsement-list" tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                {{ html()->form('POST', '/cover/cover-home')->id('form_endorse_datatable')->open() }}
                                <input type="text" name="cover_no" id="cov_cover_no" value="{{ $cover_no }}"
                                    hidden>
                                <input type="text" name="endorsement_no" id="cov_endorse_no" hidden>
                                <input type="text" name="customer_id" id="customer_id"
                                    value="{{ $customer->customer_id }} " hidden>
                                <table id="endorsement-list-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID No.</th>
                                            <th scope="col">Endorsement No.</th>
                                            <th scope="col">Transaction Type</th>
                                            <th scope="col">Cover From</th>
                                            <th scope="col">Expiry Date</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                {{ csrf_field() }}
                                {{ html()->form()->close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Quarterly Figures Modal -->
    <div class="modal effect-scale md-wrapper" id="quarterly-figures-modal" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 900px">
            <div class="modal-content">
                <form method="POST" action="{{ route('cover.save_quaterly_figures') }}" id="QuarterlyFiguresForm">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no }}">
                    <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                    <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Capture Quarterly
                            Figures</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <table>
                                <tr>
                                    <td>
                                        <div class="mb-3">
                                            <label for="treaty" class="form-label">Cover Year</label>
                                            <input type="text" class="form-control" id="cover_year" name="cover_year"
                                                value="{{ $year }}" required readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="mb-3">
                                            <label for="reinclass" class="form-label">Quarter</label>
                                            <select name="quarter" id="quarter" class="form-select select2" required>
                                                <option value="">--Select Quarter--</option>

                                                <option value="1">Quarter One</option>
                                                <option value="2">Quarter Two</option>
                                                <option value="3">Quarter Three</option>
                                                <option value="4">Quarter Four</option>

                                            </select>
                                        </div>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                            </table>
                            <div class="mb-3">
                                <table>
                                    <th>Rein Class </th>
                                    <th>Treaty </th>
                                    <th> Premium Type</th>
                                    <th> Commission Rate</th>
                                    <th> Premium</th>
                                    <th> Claim Amount</th>
                                    <tbody>

                                        <h6>Capture Premiums Below</h6>
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
                                                        name="premtype_code[]"
                                                        value="{{ $coverpremtype->premtype_code }}" required readonly>
                                                    <input type="text" class="form-control" id="premtype_name"
                                                        name="premtype_name[]"
                                                        value="{{ $coverpremtype->premtype_name }}" required readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="comm_rate"
                                                        name="comm_rate[]" value="{{ $coverpremtype->comm_rate }}"
                                                        required readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="premium_amount"
                                                        name="premium_amount[]"
                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                        change="this.value=numberWithCommas(this.value)" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="claim_amount"
                                                        name="claim_amount[]"
                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                        change="this.value=numberWithCommas(this.value)" required>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="quaterly-figures-save-btn"
                            class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Profit Commission Modal -->
    <div class="modal effect-scale md-wrapper" id="profit-commission-modal" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 1000px">
            <div class="modal-content ProfitCommissionDiv">
                <form method="POST" action="{{ route('cover.save_profit_commission') }}" id="ProfitCommissionForm">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no }}">
                    <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                    <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Capture Profit
                            Commission</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <table>
                                <tr>
                                    <td> </td>
                                    <td>
                                        <div class="mb-2">
                                            <label for="treaty" class="form-label">Cover Year</label>
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
                                    <td> </td>
                                    <td> </td>
                                </tr>
                            </table>
                            <div id="ProfitCommissionDiv">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!--Portfolios Modal -->
    <div class="modal effect-scale md-wrapper" id="portfolio-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 1000px">
            <div class="modal-content PortfolioDiv">
                <form method="POST" action="{{ route('cover.save_portfolio') }}" id="PortfolioForm">
                    @csrf
                    {{-- <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no}}"> --}}
                    <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                    <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Capture Portfolio
                            IN/OUT</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <label for="treaty" class="form-label">Portfolio Type</label>
                                <select name="portfolio_type" id="portfolio_type" class="form-inputs select2" required
                                    style="z-index: 1050;">
                                    <option value="">--Select Portfolio Type--</option>
                                    <option value="OUT">Portfolio OUT</option>
                                    <option value="IN">Portfolio IN</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label for="treaty" class="form-label">Cover Year</label>
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
                                <label for="treaty" class="form-label">Tied Cover Reference</label>
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
                                    <label for="port_share" class="form-label">Affected Share </label>
                                    <input type="text" name="port_share" id="port_share" class="form-control"
                                        required>
                                </div>

                                <div class="col-sm-3 port_share_div" id="port_share_div">
                                    <label for="port_amt" class="form-label">Portfolio Amount(100%)</label>
                                    <input type="text" name="port_amt" id="port_amt" class="form-control"
                                        onkeyup="this.value=numberWithCommas(this.value)"
                                        change="this.value=numberWithCommas(this.value)" required>
                                </div>
                                <div class="col-sm-2 port_share_div" id="port_share_div">
                                    <label for="port_prm_rate" class="form-label">Portfolio Prem Rate </label>
                                    <input type="text" name="port_prm_rate" id="port_prm_rate" class="form-control"
                                        required>
                                </div>
                                <div class="col-sm-2 port_share_div" id="port_share_div">
                                    <label for="port_loss_rate" class="form-label">Portfolio Loss Rate </label>
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

    <!--Reinsurer Modal -->
    <div class="modal effect-scale md-wrapper" id="mdpInstallmentModal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <form method="POST" action="{{ route('cover.mdp_installment_endorsement') }}" id="mdpInstallmentForm">
                    @csrf
                    <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                    <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no }}">
                    <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">MDP installments</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <label for="installments">Installment</label>
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
                            <hr>
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

    <!--Endorsements Modal -->
    <div class="modal effect-scale md-wrapper" id="endorse-cover-modal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="coverBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" style="width: 80%;">
            <div class="modal-content">
                <form method="POST" action="{{ route('cover.process_cover_endorsement') }}" id="coverEndorsementForm">
                    @csrf
                    <input type="hidden" name="cover_no" value="{{ $latest_endorsement->cover_no }}">
                    <input type="hidden" name="endorsement_no" value="{{ $latest_endorsement->endorsement_no }}">
                    <input type="hidden" name="type_of_bus" value="{{ $latest_endorsement->type_of_bus }}">
                    <input type="hidden" name="endorsed_effective_sum_insured"
                        value="{{ $latest_endorsement->apply_eml == 'Y' ? number_format($latest_endorsement->eml_amount, 2) : number_format($latest_endorsement->total_sum_insured, 2) }}">

                    <div class="modal-header">
                        <h5 class="modal-title dc-modal-title" id="coverBackdropLabel">Cover Endorsement
                        </h5>
                        <button type="button" class="btn-close btn-close-white cancelCoverEndorsementForm"
                            data-bs-dismiss="modal" aria-label="Cancel"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="form-label mdp-layer">Endorsement Type</label>
                                <div class="cover-card">
                                    <select class="form-inputs select2" name="endorse_type" id="endorse_type" required>
                                        <option value=""> -- Select Endorsement Type -- </option>
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
                                    <label for="form-label">Change type</label>
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
                                    <label class="form-label">Current Sum Insured (100%)<span
                                            id="current_total_sum_insured"></span></label>
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
                                        aria-label="effective_sum_insured" id="effective_sum_insured"
                                        name="effective_sum_insured"
                                        value="{{ $latest_endorsement->apply_eml == 'Y' ? number_format($latest_endorsement->eml_amount, 2) : number_format($latest_endorsement->total_sum_insured, 2) }}"
                                        required readonly>
                                </div>
                                <div class="col-md-3 endorsement_section_div current_fac_share_offered_div mb-2">
                                    <label class="form-label">Current Share Offered(%)</label>
                                    <input type="number"
                                        class="form-inputs endorsement_section current_fac_share_offered"
                                        aria-label="current_fac_share_offered" id="current_fac_share_offered"
                                        name="current_fac_share_offered" max="100" min="0"
                                        value="{{ $latest_endorsement->share_offered ? number_format($latest_endorsement->share_offered, 2) : '0' }}"
                                        required readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 endorsement_section_div current_cede_premium_div mb-2">
                                    <label class="form-label">Current Premium</label>
                                    <input type="text"
                                        class="form-inputs endorsement_section amount current_cede_premium"
                                        aria-label="current_cede_premium" id="current_cede_premium"
                                        name="current_cede_premium"
                                        value="{{ $latest_endorsement->apply_eml == 'Y' ? number_format($latest_endorsement->cedant_premium, 2) : number_format($latest_endorsement->cedant_premium, 2) }}"
                                        required readonly>
                                </div>
                                <div class="col-md-3 endorsement_section_div current_rein_premium_div mb-2">
                                    <label class="form-label">Current Reinsurer Premium</label>
                                    <input type="text" class="form-inputs endorsement_section current_rein_premium"
                                        aria-label="current_rein_premium" id="current_rein_premium"
                                        name="current_rein_premium"
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
                                    <input type="string" class="form-inputs endorsement_section ppw_days"
                                        aria-label="ppw_days" id="ppw_days" name="ppw_days"
                                        value="{{ $latest_endorsement->premium_payment_days }}" readonly required />
                                </div>
                                <div class="col-md-3 endorsement_section_div premium_due_date_div">
                                    <label class="form-label">Current Premium Due Date</label>
                                    <input type="hidden" value="{{ $latest_endorsement->premium_payment_code }}"
                                        name="premium_payment_code" />
                                    <input type="date" class="form-inputs endorsement_section premium_due_date"
                                        aria-label="premium_due_date" id="premium_due_date" name="premium_due_date"
                                        value="{{ $premium_due_date }}" required readonly />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 endorsement_section_div coverfrom_div">
                                    <label class="form-label">Current Cover Start Date</label>
                                    <input type="date" class="form-inputs endorsement_section coverfrom"
                                        aria-label="covstartdate" id="coverfrom" name="coverfrom"
                                        value="{{ $latest_endorsement->cover_from->format('Y-m-d') }}" readonly required>
                                </div>
                                <div class="col-md-3 endorsement_section_div coverto_div mb-2">
                                    <label class="form-label">Current Cover End Date</label>
                                    <input type="date" class="form-inputs endorsement_section coverto"
                                        aria-label="covenddate" id="coverto" name="coverto"
                                        value="{{ $latest_endorsement->cover_to->format('Y-m-d') }}" readonly required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5 endorsement_section_div insured_name_div mb-2">
                                    <label class="required ">Old Insured Name</label>
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
                                    <label class="required ">New Insured Name</label>
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
                                    <label class="form-label">Endorsed Sum Insured (100%)<span
                                            id="endorsed_sum_insured_label">
                                        </span></label>
                                    <input type="text"
                                        class="form-inputs endorsement_section endorsed_total_sum_insured"
                                        aria-label="endorsed_total_sum_insured" id="endorsed_total_sum_insured"
                                        name="endorsed_total_sum_insured"
                                        onkeyup="this.value=numberWithCommas(this.value)" required>
                                </div>
                                <div class="col-md-3 endorsement_section_div endorsed_cede_premium_div mb-2">
                                    <label class="form-label">Endorsed Cedant Premium</label>
                                    <input type="text" class="form-inputs endorsement_section endorsed_cede_premium"
                                        aria-label="endorsed_cede_premium" id="endorsed_cede_premium"
                                        name="endorsed_cede_premium" value="0"
                                        onkeyup="this.value=numberWithCommas(this.value)" required>
                                </div>
                                <div class="col-md-3 endorsement_section_div new_fac_share_offered_div mb-2">
                                    <label class="form-label">New Share Offered(%)</label>
                                    <input type="number" class="form-inputs endorsement_section new_fac_share_offered"
                                        aria-label="new_fac_share_offered" id="new_fac_share_offered"
                                        name="new_fac_share_offered" max="100" min="0"
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
                                    <label class="form-label"> EML Rate</label>
                                    <input type="number" class="form-inputs endorsement_section eml_rate"
                                        aria-label="eml_rate" id="eml_rate" name="eml_rate"
                                        value="{{ $latest_endorsement->eml_rate }}" min="0" max="100"
                                        required>
                                </div>
                                <div class="col-md-3 eml_amt_div endorsement_section_div mb-2">
                                    <label class="form-label">EML Amount</label>
                                    <input type="text" class="form-inputs endorsement_section amount eml_amt"
                                        aria-label="eml_amt" id="eml_amt" name="eml_amt"
                                        value="{{ number_format($latest_endorsement->eml_amount, 2) }}" @required(true)>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 endorsement_section_div new_total_sum_insured_div mb-2">
                                    <label class="form-label">New Sum Insured (100%)<span id="new_sum_insured_label">
                                        </span></label>
                                    <input type="text" class="form-inputs endorsement_section new_total_sum_insured"
                                        aria-label="new_total_sum_insured" id="new_total_sum_insured"
                                        name="new_total_sum_insured" required>
                                </div>
                                <div class="col-md-3 endorsement_section_div new_cede_premium_div mb-2">
                                    <label class="form-label">New Cedant Premium</label>
                                    <input type="text" class="form-inputs endorsement_section new_cede_premium"
                                        aria-label="new_cede_premium" id="new_cede_premium" name="new_cede_premium"
                                        value="0" onkeyup="this.value=numberWithCommas(this.value)" required>
                                </div>
                                <div class="col-md-3 endorsement_section_div new_effective_sum_insured_div">
                                    <label class="form-label">New Effective Sum Insured</label>
                                    <input type="text"
                                        class="form-inputs endorsement_section new_effective_sum_insured"
                                        aria-label="new_effective_sum_insured" id="new_effective_sum_insured"
                                        name="new_effective_sum_insured" onkeyup="this.value=numberWithCommas(this.value)"
                                        required readonly>
                                </div>
                                <div class="col-md-3 endorsement_section_div new_rein_premium_div mb-2">
                                    <label class="form-label">New Reinsurer Premium</label>
                                    <input type="text" class="form-inputs endorsement_section new_rein_premium"
                                        aria-label="new_rein_premium" id="new_rein_premium" name="new_rein_premium"
                                        onkeyup="this.value=numberWithCommas(this.value)" value="0" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 endorsement_section_div brokerage_comm_type_div">
                                    <label class="form-label">Brokerage Commission Type</label>
                                    <select name="brokerage_comm_type" id="brokerage_comm_type"
                                        class="form-inputs endorsement_section brokerage_comm_type select2" required>
                                        @if ($latest_endorsement->brokerage_comm_type == 'R')
                                            <option value="R" selected>Rate (<small><i>reinsurer rate - cedant rate)
                                            </option>
                                        @else
                                            <option value="A" selected> Quoted Amount</option>
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
                                        aria-label="new_covstartdate" id="new_coverfrom" name="new_coverfrom"
                                        value="{{ $latest_endorsement->cover_to->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-3 endorsement_section_div new_coverto_div mb-2">
                                    <label class="form-label">New Cover End Date</label>
                                    <input type="date" class="form-inputs endorsement_section new_coverto"
                                        aria-label="new_covenddate" id="new_coverto" name="new_coverto" required>
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
                                    <label class="form-label required">Narration </label>
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
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#portfolio_add_reinsurer').hide();
            $('.port_share_div').hide();

            $('#current_section_div').hide();

            $('#quarterly-figures-modal').on('shown.bs.modal', function() {
                $('.select2.form-inputs').select2({
                    dropdownParent: $('#quarterly-figures-modal')
                });
            });

            $('#profit-commission-modal').on('shown.bs.modal', function() {
                $('.select2.form-inputs').select2({
                    dropdownParent: $('#profit-commission-modal')
                });
            });

            $('#portfolio-modal').on('shown.bs.modal', function() {
                $('.select2.form-inputs').select2({
                    dropdownParent: $('#portfolio-modal')
                });
            });

            $('#endorse-cover-modal').on('shown.bs.modal', function() {
                $('.select2.form-inputs').select2({
                    dropdownParent: $('#endorse-cover-modal')
                });
            });

            var customer_id = '{{ $customer->customer_id }}';
            var cover_no = '{{ $cover_no }}';
            $('.process_customer').on('click', function() {});

            // endorsment-table
            const endorsementListTbl = $('#endorsement-list-table').DataTable({
                columnDefs: [{
                    targets: 0,
                    orderable: false
                }],
                order: [
                    [1, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                language: {
                    processing: "Proccessing..."
                },
                ajax: {
                    url: '{!! route('endorse.datatable') !!}',
                    data: function(d) {
                        d.customer_id = customer_id;
                        d.cover_no = cover_no;
                    }
                },
                columns: [{
                        data: 'id_no',
                        searchable: false,
                        className: 'highlight-idx',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'endorsement_no',
                        searchable: true
                    },
                    {
                        data: 'transaction_type',
                        searchable: true
                    },
                    {
                        data: 'cover_from',
                        searchable: false
                    },
                    {
                        data: 'cover_to',
                        searchable: false
                    },
                    {
                        data: 'status_verification',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'actions',
                        searchable: false,
                        sortable: false,
                    },
                ]
            });

            $('#process_renew').on('click', function() {
                $("#trans_type").val('REN');
                $("#new_cover_form").submit();
            });

            $(document).on('click', '.remove-endorsement-table', function(e) {
                e.preventDefault()
                const endorsement_no = $(this).data("endorsement_no")
                const cover_no = $(this).data("cover_no")
                const customer_id = $(this).data("customer_id")

                Swal.fire({
                    title: 'Remove Item',
                    text: `This action will remove this item from this cover`,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    const data = {
                        cover_no,
                        endorsement_no,
                        customer_id
                    }
                    if (result.isDismissed) {
                        return false;
                    }
                    fetchWithCsrf("{!! route('cover.delete_cover') !!}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(data),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == 201) {
                                toastr.success("Action was successful", 'Successful')
                                endorsementListTbl.ajax.reload()
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)
                            } else {
                                toastr.error("Failed to remove details")
                            }
                        })
                        .catch(error => {
                            toastr.error("An internal error occured")
                        });
                });
            })

            $(document).on('click', '.view-endorsement-table', function(e) {
                e.preventDefault()
                const endorsement_no = $(this).data("endorsement_no")
                const cover_no = $(this).data("cover_no")
                const customer_id = $(this).data("customer_id")

                const baseUrl = $("#form_endorse_datatable").attr('action');
                const newUrl =
                    `${baseUrl}?endorsement_no=${encodeURIComponent(endorsement_no)}`;

                $.ajax({
                    url: newUrl,
                    type: 'POST',
                    data: {
                        cover_no,
                        endorsement_no,
                        customer_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response) {
                            window.location.href = newUrl;
                        }
                    },
                    error: function(xhr, status, error) {}
                });

            })

            $('#portfolio_year').on('change', function() {
                var treaty_year = $(this).val() || 0;
                var cover_no = $("#cov_cover_no").val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('cover.get_treaty_year_cover') }}",
                    data: {
                        cover_no: cover_no,
                        treaty_year: treaty_year
                    },
                    cache: false,
                    success: function(response) {
                        console.log(response);
                        $(`#orig_endorsement`).empty();

                        $(`#orig_endorsement`).append($('<option>').text(
                            '-- Select Cover Reference--').attr('value', ''));
                        $.each(response, function(i, value) {
                            $(`#orig_endorsement`).append($('<option>').text(value
                                    .endorsement_no + '-' + value.cover_from +
                                    ' To ' + value.cover_to)
                                .attr('value', value.endorsement_no)
                            );


                        });
                        $(`#orig_endorsement`).trigger('change.select2');
                    }
                });

            });

            $('#orig_endorsement').on('change', function() {
                var orig_endorsement = $(this).val();
                var treaty_year = $('#portfolio_year').val();
                var portfolio_type = $('#portfolio_type').val();
                var cover_no = $('#cov_cover_no').val();
                var portfolio_share = 0;
                $.ajax({
                    type: "GET",
                    url: "{{ route('cover.get_reinsurers_orig_endorsement') }}",
                    data: {
                        portfolio_type: portfolio_type,
                        cover_no: cover_no,
                        treaty_year: treaty_year,
                        orig_endorsement: orig_endorsement
                    },
                    cache: false,
                    success: function(response) {
                        var count = response.count
                        var reinsurers = response.reinsurers
                        if (count > 0) {
                            $('#portfolio_add_reinsurer').show();

                            $(`#port_reinsurer`).empty();
                            $(`#port_reinsurer`).append($('<option>').text(
                                '-- Select Reinsurer--').attr('value', ''));
                            $.each(reinsurers, function(i, value) {
                                if (portfolio_type == 'OUT') {
                                    portfolio_share = value.share
                                    port_prem_rate = value.port_prem_rate
                                    port_loss_rate = value.port_loss_rate
                                }
                                $(`#port_reinsurer`).append($('<option>').text(value
                                        .customer_id + " - " + value.name)
                                    .attr('value', value.customer_id)
                                    .attr('portfolio_share', parseFloat(
                                        portfolio_share).toFixed(2))
                                    .attr('port_prem_rate', parseFloat(
                                        port_prem_rate).toFixed(2))
                                    .attr('port_loss_rate', parseFloat(
                                        port_loss_rate).toFixed(2))
                                );
                            });
                            $(`#port_reinsurer`).trigger('change.select2');
                        }
                    }
                });

            });

            $('#port_reinsurer').on('change', function() {
                var share = $("select#port_reinsurer option:selected").attr('portfolio_share');
                var port_prem_rate = $("select#port_reinsurer option:selected").attr('port_prem_rate');
                var port_loss_rate = $("select#port_reinsurer option:selected").attr('port_loss_rate');
                $('.port_share_div').show();
                $("#port_share").val(share);
                $("#port_prm_rate").val(port_prem_rate);
                $("#port_loss_rate").val(port_loss_rate);
            });

            $('#port_share').on('keyup', function() {
                var new_share = $(this).val();
                new_share = parseFloat(new_share).toFixed(2);

                var portfolio_type = $('#portfolio_type').val();
                if (new_share < 0) {
                    toastr.error('You cannot have share less than zero', 'Incomplete data')
                    return false
                }
                if (portfolio_type == 'OUT') {
                    var orig_share = $("select#port_reinsurer option:selected").attr('portfolio_share');
                    // console.log(parseInt(orig_share),parseInt(new_share));
                    if (parseInt(orig_share) < parseInt(new_share)) {
                        $(this).val(orig_share);
                        toastr.error(
                            'Please Adjust share, You cannot have OUT GO share more than Original Share',
                            'Incomplete data')
                        return false
                    }
                }
            });

            $('#treaty_year').on('change', function() {
                // alert('pull quartely figures captured');
                var treaty_year = $(this).val() || 0;
                var cover_no = $("#cov_cover_no").val();
                $(this).closest('#pc_qtr_details').remove();

                $.ajax({
                    type: "GET",
                    url: "{{ route('cover.get_quarterly_figures') }}",
                    data: {
                        cover_no: cover_no,
                        treaty_year: treaty_year
                    },
                    cache: false,
                    success: function(response) {
                        console.log(response);
                        if (response.length > 0) {
                            $('#pc_qtr_details').empty();

                            var html = `
                      <div class="mb-3" id="pc_qtr_details">
                          <table>
                              <thead>
                                  <tr>
                                      <th>Quarter</th>
                                      <th>Rein Class</th>
                                      <th>Treaty</th>
                                      <th>Premium</th>
                                      <th>Commission</th>
                                      <th>Premium Tax</th>
                                      <th>Reinsurance Tax</th>
                                      <th>Claim Amount</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <h6>Summary Quarterly Figures Below</h6>
                                  `;

                            var prevQuarter =
                                null; // Variable to store the previous quarter value
                            var totalRowAdded =
                                false; // Flag to indicate if a total row has been added
                            var totalPremium = 0;
                            var totalCommission = 0;
                            var totalPremiumTax = 0;
                            var totalReinsuranceTax = 0;
                            var totalClaims = 0;
                            $.each(response, function(i, value) {

                                // Check if quarter is different from previous
                                if (value.quarter !== prevQuarter) {

                                    // If previous quarter exists and a total row hasn't been added yet
                                    if (prevQuarter !== null && !totalRowAdded) {
                                        html += `
                                  <tr class="total-row" style="font-weight: bold;">
                                      <td>Total</td>
                                      <td></td>
                                      <td></td>
                                      <td>${numberWithCommas(totalPremium)}</td>
                                      <td>${numberWithCommas(totalCommission)}</td>
                                      <td>${numberWithCommas(totalPremiumTax)}</td>
                                      <td>${numberWithCommas(totalReinsuranceTax)}</td>
                                      <td>${numberWithCommas(totalClaims)}</td>
                                  </tr>
                              `;
                                        totalRowAdded = true; // Set flag to true
                                    }
                                    // Reset total variables
                                    totalPremium = 0;
                                    totalCommission = 0;
                                    totalPremiumTax = 0;
                                    totalReinsuranceTax = 0;
                                    totalClaims = 0;

                                    prevQuarter = value
                                        .quarter; // Update previous quarter
                                    totalRowAdded = false; // Reset total row flag
                                }

                                // Accumulate totals
                                totalPremium += parseFloat(value.premium);
                                totalCommission += parseFloat(value.commission);
                                totalPremiumTax += parseFloat(value.premium_tax);
                                totalReinsuranceTax += parseFloat(value
                                    .reinsurance_tax);
                                totalClaims += parseFloat(value.claims);

                                html += `
                                    <tr>
                                        <td><input type="text" class="form-control pc_quarter" id="pc_quarter-${i}" name="pc_quarter[]" value="${value.quarter}" required readonly></td>
                                        <td><input type="text" class="form-control pc_reinclass" id="pc_reinclass-${i}" name="pc_reinclass[]" value="${value.class_code}" required readonly></td>
                                        <td><input type="text" class="form-control pc_treaty" id="pc_treaty-${i}" name="pc_treaty[]" value="${value.treaty}" required readonly></td>
                                        <td><input type="text" class="form-control pc_premium" id="pc_premium-${i}" name="pc_premium[]" value="${numberWithCommas(value.premium)}" required readonly></td>
                                        <td><input type="text" class="form-control pc_commission" id="pc_commission-${i}" name="pc_commission[]" value="${numberWithCommas(value.commission)}" required readonly></td>
                                        <td><input type="text" class="form-control pc_premium_tax" id="pc_premium_tax-${i}" name="pc_premium_tax[]" value="${numberWithCommas(value.premium_tax)}" required readonly></td>
                                        <td><input type="text" class="form-control pc_reinsurance_tax" id="pc_reinsurance_tax-${i}" name="pc_reinsurance_tax[]" value="${numberWithCommas(value.reinsurance_tax)}" required readonly></td>
                                        <td><input type="text" class="form-control pc_claim_amount" id="pc_claim_amount-${i}" name="pc_claim_amount[]" value="${numberWithCommas(value.claims)}" required readonly></td>
                                    </tr>
                                `;
                            });

                            // Add total row for the last quarter if not added already
                            if (!totalRowAdded && prevQuarter !== null) {
                                html += `
                                    <tr class="total-row" style="font-weight: bold;">
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td>${numberWithCommas(totalPremium)}</td>
                                        <td>${numberWithCommas(totalCommission)}</td>
                                        <td>${numberWithCommas(totalPremiumTax)}</td>
                                        <td>${numberWithCommas(totalReinsuranceTax)}</td>
                                        <td>${numberWithCommas(totalClaims)}</td>
                                    </tr>
                                `;
                            }

                            html += `
                              </tbody>
                              </table>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Portfolio Entry Premium</th>
                                            <th>Portfolio Entry Loss</th>
                                            <th>Portfolio Withdrawal Premium</th>
                                            <th>Portfolio Withdrawal Loss</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <h6>Capture Portfolios Below</h6>
                                        <tr>
                                            <td><input type="text" class="form-inputs" id="port_entry_prem" name="port_entry_prem" value="" onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required></td>
                                            <td><input type="text" class="form-inputs" id="port_entry_loss" name="port_entry_loss" value="" onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required></td>
                                            <td><input type="text" class="form-inputs" id="port_withdrawal_prem" name="port_withdrawal_prem" value="" onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required></td>
                                            <td><input type="text" class="form-inputs" id="port_withdrawal_loss" name="port_withdrawal_loss" value="" onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required></td>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="quaterly-figures-save-btn" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                                </div>
                            `;
                            $('#ProfitCommissionForm').append(html);
                            // Trigger event when input field value changes
                            $('.pc_quarter, .pc_reinclass, .pc_treaty, .pc_premium, .pc_commission, .pc_claim_amount,.total-row')
                                .change(function() {
                                    // Your event handling code here
                                    console.log('Input value changed');
                                });
                        }
                    }
                });
            });

            $('#mdp-installment').change(function(e) {
                e.preventDefault();
                const selectedInstallment = $(this).val();
                parseFloat($(this).data('')) || 0;
                let selectedInstallmentTotalAmt = $("select#mdp-installment option:selected").data(
                    'total_amt') || 0;
                selectedInstallmentTotalAmt = parseFloat(selectedInstallmentTotalAmt);
                $('#mdp-installments-section').empty()
                const mdpInstStr = '@json($mdpInsLayerwise)'
                if (mdpInstStr.length > 0) {
                    const mdpInstallments = JSON.parse(mdpInstStr)

                    selectedinstallemntLayers = mdpInstallments.filter(inst => inst.installment_no ==
                        selectedInstallment)
                    selectedinstallemntLayers.forEach(Inslayer => {
                        console.log('Inslayer', Inslayer);
                        const layerMdpAmt = parseFloat(Inslayer.installment_amt) || 0

                        $('#mdp-installments-section').append(`

                  <div class="row installment-section">
                      <div class="col-md-4">
                          <label for="layer_no">Layer no.</label>
                          <input type="text" name="layer_no[]" id="layer_no" value="${Inslayer.layer_no}" readonly class="form-control" readonly required>
                      </div>
                      <div class="col-md-4">
                          <label for="min_deposit">Total Installment Amount</label>
                          <input type="text" name="min_deposit[]" id="min_deposit" value="${numberWithCommas(selectedInstallmentTotalAmt)}" readonly class="form-control"readonly required>
                      </div>
                      <div class="col-md-4">
                          <label for="installment_amt">Installment Amount</label>
                          <input type="text" name="installment_amt[]" id="installment_amt" value="${numberWithCommas(layerMdpAmt)}" class="form-control amount"readonly required>
                      </div>
                  </div>
              `);
                    });
                }
            });

            $('#endorse_cover').on('click', function() {
                processSections('.endorsement_section', '.endorsement_section_div', 'disable');
                $('#endorse-cover-modal').modal('show')
            });

            $('#endorse_type').on('change', function() {
                $(this).valid();
                let slug = $(this).val();
                if (slug != 'cancel-policy') {
                    $('#current_section_div').show();
                } else {
                    $('#current_section_div').hide();
                }

                if (slug == 'change-brokerage-rate') {
                    processSections('.brokerage_comm_type', '.brokerage_comm_type_div', 'enable');
                    processSections('.brokerage_comm_rate', '.brokerage_comm_rate_div', 'enable');
                    processSections('.brokerage_comm_amt', '.brokerage_comm_amt_div', 'enable');
                    $('#brokerage_comm_type').trigger('change')

                    //disable
                    processSections('.start_date', '.start_date_div', 'disable');
                    processSections('.ppw_days', '.ppw_days_div', 'disable');
                    processSections('.extension_days', '.extension_days_div', 'disable');
                    processSections('.premium_due_date', '.premium_due_date_div', 'disable');
                    processSections('.new_premium_due_date', '.new_premium_due_date_div', 'disable');
                    processSections('.coverfrom', '.coverfrom_div', 'disable');
                    processSections('.coverto', '.coverto_div', 'disable');
                    processSections('.insured_name', '.insured_name_div', 'disable');
                    processSections('.new_insured_name', '.new_insured_name_div', 'disable');
                    processSections('.cede_premium', '.cede_premium_div', 'disable');
                    processSections('.rein_premium', '.rein_premium_div', 'disable');
                    processSections('.fac_share_offered', '.fac_share_offered_div', 'disable');
                    processSections('.current_total_sum_insured', '.current_total_sum_insured_div',
                        'disable');
                    processSections('.new_total_sum_insured', '.new_total_sum_insured_div', 'disable');
                    processSections('.apply_eml', '.apply_eml_div', 'disable');
                    processSections('.eml_rate', '.eml_rate_div', 'disable');
                    processSections('.eml_amt', '.eml_amt_div', 'disable');
                    processSections('.effective_sum_insured', '.effective_sum_insured_div', 'disable');
                    processSections('.new_effective_sum_insured', '.new_effective_sum_insured_div',
                        'disable');
                    processSections('.new_coverfrom', '.new_coverfrom_div', 'disable');
                    processSections('.new_coverto', '.new_coverto_div', 'disable');
                    processSections('.new_rein_premium', '.new_rein_premium_div',
                        'disable');
                    processSections('.endorsed_total_sum_insured', '.endorsed_total_sum_insured_div',
                        'disable');
                    processSections('.current_rein_premium', '.current_rein_premium_div',
                        'disable');
                    processSections('.endorsed_cede_premium', '.endorsed_cede_premium_div', 'disable');
                    processSections('.change_in_sum_insured_type', '.change_in_sum_insured_type_div',
                        'disable');
                } else if (slug == 'change-due-date') {
                    processSections('.start_date', '.start_date_div', 'enable');
                    processSections('.ppw_days', '.ppw_days_div', 'enable');
                    processSections('.extension_days', '.extension_days_div', 'enable');
                    processSections('.premium_due_date', '.premium_due_date_div', 'enable');
                    processSections('.new_premium_due_date', '.new_premium_due_date_div', 'enable');

                    //disable
                    processSections('.coverfrom', '.coverfrom_div', 'disable');
                    processSections('.coverto', '.coverto_div', 'disable');
                    processSections('.brokerage_comm_type', '.brokerage_comm_type_div', 'disable');
                    processSections('.brokerage_comm_rate', '.brokerage_comm_rate_div', 'disable');
                    processSections('.brokerage_comm_amt', '.brokerage_comm_amt_div', 'disable');
                    processSections('.insured_name', '.insured_name_div', 'disable');
                    processSections('.new_insured_name', '.new_insured_name_div', 'disable');
                    processSections('.new_cede_premium', '.new_cede_premium_div', 'disable');
                    processSections('.current_cede_premium', '.current_cede_premium_div', 'disable');
                    processSections('.rein_premium', '.rein_premium_div', 'disable');
                    processSections('.current_fac_share_offered', '.current_fac_share_offered_div',
                        'disable');
                    processSections('.new_fac_share_offered', '.new_fac_share_offered_div',
                        'disable');
                    processSections('.current_rein_premium', '.current_rein_premium_div',
                        'disable');
                    processSections('.new_rein_premium', '.new_rein_premium_div',
                        'disable');
                    processSections('.current_total_sum_insured', '.current_total_sum_insured_div',
                        'disable');
                    processSections('.new_total_sum_insured', '.new_total_sum_insured_div', 'disable');
                    processSections('.apply_eml', '.apply_eml_div', 'disable');
                    processSections('.eml_rate', '.eml_rate_div', 'disable');
                    processSections('.eml_amt', '.eml_amt_div', 'disable');
                    processSections('.effective_sum_insured', '.effective_sum_insured_div', 'disable');
                    processSections('.new_effective_sum_insured', '.new_effective_sum_insured_div',
                        'disable');
                    processSections('.new_coverfrom', '.new_coverfrom_div', 'disable');
                    processSections('.new_coverto', '.new_coverto_div', 'disable');
                    processSections('.endorsed_total_sum_insured', '.endorsed_total_sum_insured_div',
                        'disable');
                    processSections('.endorsed_cede_premium', '.endorsed_cede_premium_div', 'disable');
                    processSections('.change_in_sum_insured_type', '.change_in_sum_insured_type_div',
                        'disable');
                } else if (slug == 'change-premium') {
                    processSections('.current_total_sum_insured', '.current_total_sum_insured_div',
                        'enable');
                    processSections('.new_total_sum_insured', '.new_total_sum_insured_div', 'enable');
                    processSections('.apply_eml', '.apply_eml_div', 'enable');
                    processSections('.eml_rate', '.eml_rate_div', 'enable');
                    processSections('.eml_amt', '.eml_amt_div', 'enable');
                    processSections('.effective_sum_insured', '.effective_sum_insured_div', 'enable');
                    processSections('.new_effective_sum_insured', '.new_effective_sum_insured_div',
                        'enable');
                    processSections('.current_cede_premium', '.current_cede_premium_div', 'enable');
                    processSections('.new_cede_premium', '.new_cede_premium_div', 'enable');
                    processSections('.endorsed_total_sum_insured', '.endorsed_total_sum_insured_div',
                        'enable');
                    processSections('.endorsed_cede_premium', '.endorsed_cede_premium_div', 'enable');
                    processSections('.current_fac_share_offered', '.current_fac_share_offered_div',
                        'enable');
                    processSections('.current_rein_premium', '.current_rein_premium_div',
                        'enable');
                    processSections('.change_in_sum_insured_type', '.change_in_sum_insured_type_div',
                        'enable');
                    processSections('.new_fac_share_offered', '.new_fac_share_offered_div',
                        'enable');
                    $('#apply_eml').trigger('change')
                    $('#endorsed_cede_premium').val('')
                    $('#new_cede_premium').val('0')

                    //disable
                    processSections('.start_date', '.start_date_div', 'disable');
                    processSections('.ppw_days', '.ppw_days_div', 'disable');
                    processSections('.extension_days', '.extension_days_div', 'disable');
                    processSections('.premium_due_date', '.premium_due_date_div', 'disable');
                    processSections('.new_premium_due_date', '.new_premium_due_date_div', 'disable');
                    processSections('.insured_name', '.insured_name_div', 'disable');
                    processSections('.new_insured_name', '.new_insured_name_div', 'disable');
                    processSections('.coverfrom', '.coverfrom_div', 'disable');
                    processSections('.coverto', '.coverto_div', 'disable');
                    processSections('.brokerage_comm_type', '.brokerage_comm_type_div', 'disable');
                    processSections('.brokerage_comm_rate', '.brokerage_comm_rate_div', 'disable');
                    processSections('.brokerage_comm_amt', '.brokerage_comm_amt_div', 'disable');
                    processSections('.rein_premium', '.rein_premium_div', 'disable');
                    processSections('.fac_share_offered', '.fac_share_offered_div', 'disable');
                    processSections('.new_coverfrom', '.new_coverfrom_div', 'disable');
                    processSections('.new_coverto', '.new_coverto_div', 'disable');
                    processSections('.new_rein_premium', '.new_rein_premium_div',
                        'disable');

                } else if (slug == 'change-inception-date') {
                    processSections('.coverfrom', '.coverfrom_div', 'enable');
                    processSections('.coverto', '.coverto_div', 'enable');
                    processSections('.new_coverfrom', '.new_coverfrom_div', 'enable');
                    processSections('.new_coverto', '.new_coverto_div', 'enable');

                    //disable
                    processSections('.start_date', '.start_date_div', 'disable');
                    processSections('.ppw_days', '.ppw_days_div', 'disable');
                    processSections('.extension_days', '.extension_days_div', 'disable');
                    processSections('.premium_due_date', '.premium_due_date_div', 'disable');
                    processSections('.new_premium_due_date', '.new_premium_due_date_div', 'disable');
                    processSections('.brokerage_comm_type', '.brokerage_comm_type_div', 'disable');
                    processSections('.brokerage_comm_rate', '.brokerage_comm_rate_div', 'disable');
                    processSections('.brokerage_comm_amt', '.brokerage_comm_amt_div', 'disable');
                    processSections('.insured_name', '.insured_name_div', 'disable');
                    processSections('.new_insured_name', '.new_insured_name_div', 'disable');
                    processSections('.fac_share_offered', '.fac_share_offered_div', 'disable');
                    processSections('.current_total_sum_insured', '.current_total_sum_insured_div',
                        'disable');
                    processSections('.new_total_sum_insured', '.new_total_sum_insured_div', 'disable');
                    processSections('.apply_eml', '.apply_eml_div', 'disable');
                    processSections('.eml_rate', '.eml_rate_div', 'disable');
                    processSections('.eml_amt', '.eml_amt_div', 'disable');
                    processSections('.effective_sum_insured', '.effective_sum_insured_div', 'disable');
                    processSections('.new_effective_sum_insured', '.new_effective_sum_insured_div',
                        'disable');
                    processSections('.rein_premium', '.rein_premium_div', 'disable');
                    processSections('.current_fac_share_offered', '.current_fac_share_offered_div',
                        'disable');
                    processSections('.new_fac_share_offered', '.new_fac_share_offered_div',
                        'disable');
                    processSections('.current_rein_premium', '.current_rein_premium_div',
                        'disable');
                    processSections('.new_rein_premium', '.new_rein_premium_div',
                        'disable');
                    processSections('.endorsed_total_sum_insured', '.endorsed_total_sum_insured_div',
                        'disable');
                    processSections('.endorsed_cede_premium', '.endorsed_cede_premium_div', 'disable');
                    processSections('.change_in_sum_insured_type', '.change_in_sum_insured_type_div',
                        'disable');
                } else if (slug == 'change-insured') {
                    processSections('.insured_name', '.insured_name_div', 'enable');
                    processSections('.new_insured_name', '.new_insured_name_div', 'enable');

                    //disable
                    processSections('.start_date', '.start_date_div', 'disable');
                    processSections('.ppw_days', '.ppw_days_div', 'disable');
                    processSections('.extension_days', '.extension_days_div', 'disable');
                    processSections('.premium_due_date', '.premium_due_date_div', 'disable');
                    processSections('.new_premium_due_date', '.new_premium_due_date_div', 'disable');
                    processSections('.coverfrom', '.coverfrom_div', 'disable');
                    processSections('.coverto', '.coverto_div', 'disable');
                    processSections('.brokerage_comm_type', '.brokerage_comm_type_div', 'disable');
                    processSections('.brokerage_comm_rate', '.brokerage_comm_rate_div', 'disable');
                    processSections('.brokerage_comm_amt', '.brokerage_comm_amt_div', 'disable');
                    processSections('.rein_premium', '.rein_premium_div', 'disable');
                    processSections('.current_fac_share_offered', '.current_fac_share_offered_div',
                        'disable');
                    processSections('.new_fac_share_offered', '.new_fac_share_offered_div',
                        'disable');
                    processSections('.current_rein_premium', '.current_rein_premium_div',
                        'disable');
                    processSections('.new_rein_premium', '.new_rein_premium_div',
                        'disable');
                    processSections('.current_total_sum_insured', '.current_total_sum_insured_div',
                        'disable');
                    processSections('.apply_eml', '.apply_eml_div', 'disable');
                    processSections('.eml_rate', '.eml_rate_div', 'disable');
                    processSections('.eml_amt', '.eml_amt_div', 'disable');
                    processSections('.effective_sum_insured', '.effective_sum_insured_div', 'disable');
                    processSections('.new_effective_sum_insured', '.new_effective_sum_insured_div',
                        'disable');
                    processSections('.new_coverfrom', '.new_coverfrom_div', 'disable');
                    processSections('.new_coverto', '.new_coverto_div', 'disable');
                    processSections('.endorsed_total_sum_insured', '.endorsed_total_sum_insured_div',
                        'disable');
                    processSections('.endorsed_cede_premium', '.endorsed_cede_premium_div', 'disable');
                    processSections('.change_in_sum_insured_type', '.change_in_sum_insured_type_div',
                        'disable');
                } else if (slug == 'cancel-policy') {
                    //disable
                    processSections('.new_cede_premium', '.new_cede_premium_div', 'disable');
                    processSections('.current_cede_premium', '.current_cede_premium_div', 'disable');
                    processSections('.rein_premium', '.rein_premium_div', 'disable');
                    processSections('.current_fac_share_offered', '.current_fac_share_offered_div',
                        'disable');
                    processSections('.new_fac_share_offered', '.new_fac_share_offered_div',
                        'disable');
                    processSections('.current_rein_premium', '.current_rein_premium_div',
                        'disable');
                    processSections('.new_rein_premium', '.new_rein_premium_div',
                        'disable');
                    processSections('.start_date', '.start_date_div', 'disable');
                    processSections('.ppw_days', '.ppw_days_div', 'disable');
                    processSections('.extension_days', '.extension_days_div', 'disable');
                    processSections('.premium_due_date', '.premium_due_date_div', 'disable');
                    processSections('.new_premium_due_date', '.new_premium_due_date_div', 'disable');
                    processSections('.insured_name', '.insured_name_div', 'disable');
                    processSections('.new_insured_name', '.new_insured_name_div', 'disable');
                    processSections('.coverfrom', '.coverfrom_div', 'disable');
                    processSections('.coverto', '.coverto_div', 'disable');
                    processSections('.brokerage_comm_type', '.brokerage_comm_type_div', 'disable');
                    processSections('.brokerage_comm_rate', '.brokerage_comm_rate_div', 'disable');
                    processSections('.brokerage_comm_amt', '.brokerage_comm_amt_div', 'disable');
                    processSections('.new_total_sum_insured', '.new_total_sum_insured_div', 'disable');
                    processSections('.apply_eml', '.apply_eml_div', 'disable');
                    processSections('.eml_rate', '.eml_rate_div', 'disable');
                    processSections('.eml_amt', '.eml_amt_div', 'disable');
                    processSections('.effective_sum_insured', '.effective_sum_insured_div', 'disable');
                    processSections('.new_effective_sum_insured', '.new_effective_sum_insured_div',
                        'disable');
                    processSections('.new_coverfrom', '.new_coverfrom_div', 'disable');
                    processSections('.new_coverto', '.new_coverto_div', 'disable');
                    processSections('.endorsed_total_sum_insured', '.endorsed_total_sum_insured_div',
                        'disable');
                    processSections('.endorsed_cede_premium', '.endorsed_cede_premium_div', 'disable');
                    processSections('.change_in_sum_insured_type', '.change_in_sum_insured_type_div',
                        'disable');
                } else if (slug == 'change-sum-insured') {
                    processSections('.current_total_sum_insured', '.current_total_sum_insured_div',
                        'enable');
                    processSections('.new_total_sum_insured', '.new_total_sum_insured_div', 'enable');
                    processSections('.apply_eml', '.apply_eml_div', 'enable');
                    processSections('.eml_rate', '.eml_rate_div', 'enable');
                    processSections('.eml_amt', '.eml_amt_div', 'enable');
                    processSections('.effective_sum_insured', '.effective_sum_insured_div', 'enable');
                    processSections('.new_effective_sum_insured', '.new_effective_sum_insured_div',
                        'enable');
                    processSections('.current_cede_premium', '.current_cede_premium_div', 'enable');
                    processSections('.new_cede_premium', '.new_cede_premium_div', 'enable');
                    processSections('.endorsed_total_sum_insured', '.endorsed_total_sum_insured_div',
                        'enable');
                    processSections('.endorsed_cede_premium', '.endorsed_cede_premium_div', 'enable');
                    processSections('.current_fac_share_offered', '.current_fac_share_offered_div',
                        'enable');
                    processSections('.current_rein_premium', '.current_rein_premium_div',
                        'enable');
                    processSections('.change_in_sum_insured_type', '.change_in_sum_insured_type_div',
                        'enable');
                    processSections('.new_fac_share_offered', '.new_fac_share_offered_div',
                        'enable');
                    $('#apply_eml').trigger('change')

                    //disable
                    processSections('.start_date', '.start_date_div', 'disable');
                    processSections('.ppw_days', '.ppw_days_div', 'disable');
                    processSections('.extension_days', '.extension_days_div', 'disable');
                    processSections('.premium_due_date', '.premium_due_date_div', 'disable');
                    processSections('.new_premium_due_date', '.new_premium_due_date_div', 'disable');
                    processSections('.insured_name', '.insured_name_div', 'disable');
                    processSections('.new_insured_name', '.new_insured_name_div', 'disable');
                    processSections('.coverfrom', '.coverfrom_div', 'disable');
                    processSections('.coverto', '.coverto_div', 'disable');
                    processSections('.brokerage_comm_type', '.brokerage_comm_type_div', 'disable');
                    processSections('.brokerage_comm_rate', '.brokerage_comm_rate_div', 'disable');
                    processSections('.brokerage_comm_amt', '.brokerage_comm_amt_div', 'disable');
                    processSections('.rein_premium', '.rein_premium_div', 'disable');
                    processSections('.fac_share_offered', '.fac_share_offered_div', 'disable');
                    processSections('.new_coverfrom', '.new_coverfrom_div', 'disable');
                    processSections('.new_coverto', '.new_coverto_div', 'disable');
                    processSections('.new_rein_premium', '.new_rein_premium_div',
                        'disable');
                } else if (slug == 'refund-endorsement') {
                    processSections('.current_total_sum_insured', '.current_total_sum_insured_div',
                        'enable');
                    processSections('.new_total_sum_insured', '.new_total_sum_insured_div', 'enable');
                    processSections('.apply_eml', '.apply_eml_div', 'enable');
                    processSections('.eml_rate', '.eml_rate_div', 'enable');
                    processSections('.eml_amt', '.eml_amt_div', 'enable');
                    processSections('.effective_sum_insured', '.effective_sum_insured_div', 'enable');
                    processSections('.new_effective_sum_insured', '.new_effective_sum_insured_div',
                        'enable');
                    processSections('.current_cede_premium', '.current_cede_premium_div', 'enable');
                    processSections('.new_cede_premium', '.new_cede_premium_div', 'enable');
                    processSections('.endorsed_total_sum_insured', '.endorsed_total_sum_insured_div',
                        'enable');
                    processSections('.endorsed_cede_premium', '.endorsed_cede_premium_div', 'enable');
                    processSections('.current_fac_share_offered', '.current_fac_share_offered_div',
                        'enable');
                    processSections('.current_rein_premium', '.current_rein_premium_div',
                        'enable');

                    processSections('.new_fac_share_offered', '.new_fac_share_offered_div',
                        'enable');
                    $('#apply_eml').trigger('change')

                    //disable
                    processSections('.change_in_sum_insured_type', '.change_in_sum_insured_type_div',
                        'disable');
                    processSections('.start_date', '.start_date_div', 'disable');
                    processSections('.ppw_days', '.ppw_days_div', 'disable');
                    processSections('.extension_days', '.extension_days_div', 'disable');
                    processSections('.premium_due_date', '.premium_due_date_div', 'disable');
                    processSections('.new_premium_due_date', '.new_premium_due_date_div', 'disable');
                    processSections('.insured_name', '.insured_name_div', 'disable');
                    processSections('.new_insured_name', '.new_insured_name_div', 'disable');
                    processSections('.coverfrom', '.coverfrom_div', 'disable');
                    processSections('.coverto', '.coverto_div', 'disable');
                    processSections('.brokerage_comm_type', '.brokerage_comm_type_div', 'disable');
                    processSections('.brokerage_comm_rate', '.brokerage_comm_rate_div', 'disable');
                    processSections('.brokerage_comm_amt', '.brokerage_comm_amt_div', 'disable');
                    processSections('.rein_premium', '.rein_premium_div', 'disable');
                    processSections('.fac_share_offered', '.fac_share_offered_div', 'disable');
                    processSections('.new_coverfrom', '.new_coverfrom_div', 'disable');
                    processSections('.new_coverto', '.new_coverto_div', 'disable');
                    processSections('.new_rein_premium', '.new_rein_premium_div',
                        'disable');
                }
            });

            $('#brokerage_comm_type').change(function(e) {
                const brokerageCommType = $(this).val()
                $('.brokerage_comm_amt_div').hide();
                $('#brokerage_comm_amt').hide();
                $('#brokerage_comm_rate').hide();
                $('.brokerage_comm_rate_div').hide();

                $('#brokerage_comm_rate').val(null);
                $('#brokerage_comm_amt').val(null);
                if (brokerageCommType == 'R') {
                    $('.brokerage_comm_rate_div').show();
                    $('#brokerage_comm_rate').show();
                    calculateBrokerageCommRate()
                } else {
                    $('.brokerage_comm_amt_div').show();
                    $('#brokerage_comm_amt').show().prop('disabled', false);
                }
            });

            $('#brokerage_comm_type').trigger('change')

            $('#apply_eml').change(function() {
                $(this).validate()
                const applyEML = $(this).val()
                processSections('.eml_rate', '.eml_rate_div', 'disable');
                processSections('.eml_amt', '.eml_amt_div', 'disable');
                if (applyEML == 'Y') {
                    processSections('.eml_rate', '.eml_rate_div', 'enable');
                    processSections('.eml_amt', '.eml_amt_div', 'enable');
                }

                const type = $("#change_in_sum_insured_type").val();
                calculateNewValues(type);
            });

            $('#eml_rate').on('keyup change', function(e) {
                const emlRate = $(this).val()

                if (emlRate > 100) {
                    emlRate = 100;
                }

                if (emlRate < 0) {
                    emlRate = 0;
                }

                const totalSumInsured = parseFloat(removeCommas($('#new_total_sum_insured').val()))
                const emlAmt = totalSumInsured * (emlRate / 100)

                const endorsedSumInsured = parseFloat(removeCommas($('#endorsed_total_sum_insured')
                    .val()))
                const endorsedEmlAmt = endorsedSumInsured * (emlRate / 100)


                $('#eml_amt').val(numberWithCommas(emlAmt));
                $('#new_effective_sum_insured').val(numberWithCommas(emlAmt));
                $('#endorsed_effective_sum_insured').val(numberWithCommas(endorsedEmlAmt));
            });

            $('#eml_amt').on('keyup change', function(e) {
                var emlAmt = parseFloat(removeCommas($(this).val()))
                var totalSumInsured = parseFloat(removeCommas($('#new_total_sum_insured').val()))

                if (emlAmt > totalSumInsured) {
                    emlAmt = totalSumInsured;
                }
                if (emlAmt < 0) {
                    emlAmt = 0;
                }

                var emlRate = parseFloat((emlAmt / totalSumInsured) * 100)

                $('#eml_rate').val(emlRate);
                $('#eml_amt').val(numberWithCommas(emlAmt));

                $('#new_effective_sum_insured').val(numberWithCommas(emlAmt));
            });

            function calculateBrokerageCommRate() {
                let cedantCommRate = removeCommas($('#comm_rate').val())
                let reinCommRate = removeCommas($('#reins_comm_rate').val())
                let brokerageCommRate = 0

                if (cedantCommRate != '' && cedantCommRate != null && reinCommRate != '' && reinCommRate !=
                    null) {
                    brokerageCommRate = parseFloat(reinCommRate) - parseFloat(cedantCommRate)
                }

                $('#brokerage_comm_rate').val(brokerageCommRate);
            }

            $('#generateRenewalNotice').on('click', async function(e) {
                e.preventDefault();
                $("#new_renewal_notice").submit();
            });


            $('#premium_due_date').on('change', function() {
                var startDate = $(this).val();
                var endDate = $("#new_premium_due_date").val();

                if (startDate && endDate) {
                    var extensionDays = calculateExtensionDays(startDate, endDate);
                    $("#extension_days").val(extensionDays);
                }
            });

            $('#new_premium_due_date').on('change', function() {
                var startDate = $("#premium_due_date").val();
                var endDate = $(this).val();

                if (startDate && endDate) {
                    var extensionDays = calculateExtensionDays(startDate, endDate);
                    $("#extension_days").val(extensionDays);
                }
            });

            $('#extension_days').on('change keyup', function() {
                var startDate = $("#premium_due_date").val();
                var extensionDays = parseInt($(this).val()) || 0;
                var endDate = calculateEndDate(startDate, extensionDays);

                $("#new_premium_due_date").val(endDate);
            });

            $("#coverEndorsementForm").validate({
                errorClass: "errorClass",
                rules: {
                    endorse_type: {
                        required: true,
                    },
                    endorse_narration: {
                        required: true,
                    }
                },
                messages: {
                    endorse_type: {
                        required: "Select Endorsement Type",
                    },
                    endorse_narration: {
                        required: "Narration is required",
                    }
                },
                submitHandler: function(form) {
                    if (validateInputs()) {
                        form.submit();
                    }
                }
            });

            function validateEndorsementInputs() {
                const endorsedSumInsured = parseFloat(removeCommas($('#endorsed_total_sum_insured').val())) || 0;
                const type = $('#change_in_sum_insured_type').val();

                if (type === 'decrease' && endorsedSumInsured > currentSumInsured) {
                    toastr.error('Decrease amount cannot be greater than current sum insured');
                    $('#endorsed_total_sum_insuredd').val('');
                    return false;
                }

                return true;
            }

            function processSections(sectionClass, sectionDivClass, action) {
                if (action == 'enable') {
                    $(sectionClass + ', ' + sectionDivClass).each(function() {
                        if ($(this).hasClass(sectionDivClass.substr(1))) {
                            $(this).show();
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });
                } else {
                    $(sectionClass + ', ' + sectionDivClass).each(function() {
                        if ($(this).hasClass(sectionDivClass.substr(1))) {
                            $(this).hide();
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });
                }
            }

            $("#new_coverfrom").change(function() {
                let start_date = moment($(this).val());
                let end_date = start_date.add(1, 'years').subtract(1, 'days');
                $("#new_coverto").val(end_date.format('YYYY-MM-DD'));
            });

            $('.cancelCoverEndorsementForm').on('click', function() {
                $("#coverEndorsementForm")[0].reset();

                processSections('.coverfrom', '.coverfrom_div', 'disable');
                processSections('.coverto', '.coverto_div', 'disable');
                processSections('.new_coverfrom', '.new_coverfrom_div', 'disable');
                processSections('.new_coverto', '.new_coverto_div', 'disable');
                processSections('.start_date', '.start_date_div', 'disable');
                processSections('.ppw_days', '.ppw_days_div', 'disable');
                processSections('.extension_days', '.extension_days_div', 'disable');
                processSections('.premium_due_date', '.premium_due_date_div', 'disable');
                processSections('.new_premium_due_date', '.new_premium_due_date_div', 'disable');
                processSections('.brokerage_comm_type', '.brokerage_comm_type_div', 'disable');
                processSections('.brokerage_comm_rate', '.brokerage_comm_rate_div', 'disable');
                processSections('.brokerage_comm_amt', '.brokerage_comm_amt_div', 'disable');
                processSections('.insured_name', '.insured_name_div', 'disable');
                processSections('.new_insured_name', '.new_insured_name_div', 'disable');
                processSections('.cede_premium', '.cede_premium_div', 'disable');
                processSections('.rein_premium', '.rein_premium_div', 'disable');
                processSections('.fac_share_offered', '.fac_share_offered_div', 'disable');
                processSections('.current_total_sum_insured', '.current_total_sum_insured_div',
                    'disable');
                processSections('.new_total_sum_insured', '.new_total_sum_insured_div', 'disable');
                processSections('.apply_eml', '.apply_eml_div', 'disable');
                processSections('.eml_rate', '.eml_rate_div', 'disable');
                processSections('.eml_amt', '.eml_amt_div', 'disable');
                processSections('.effective_sum_insured', '.effective_sum_insured_div', 'disable');
                processSections('.new_effective_sum_insured', '.new_effective_sum_insured_div',
                    'disable');
            });

            $('#endorsed_total_sum_insured').on('change keyup', function() {
                const type = $("#change_in_sum_insured_type").val();
                calculateNewValues(type);
            });

            $('#endorsed_cede_premium').on('change keyup', function() {
                const type = $("#change_in_sum_insured_type").val();
                calculateNewValues(type);
            });

            let currentSumInsured = {!! json_encode($latest_endorsement->total_sum_insured ?? 0) !!};
            let currentPremium = {!! json_encode($latest_endorsement->cedant_premium ?? 0) !!};
            let currentReinsurerPremium = {!! json_encode($latest_endorsement->cedant_premium ?? 0) !!};

            $('#change_in_sum_insured_type').on('change keup', function() {
                const type = $(this).val();
                calculateNewValues(type);
                $(this).valid();
            });

            function calculateNewValues(type) {
                const endorsedSumInsured = parseFloat(removeCommas($('#endorsed_total_sum_insured').val())) || 0;
                const endorsedCedPremium = parseFloat(removeCommas($('#endorsed_cede_premium').val())) || 0;
                const emlRate = parseFloat($('#eml_rate').val()) || 0;
                const applyEml = $('#apply_eml').val();
                const currentPremium = {!! json_encode($latest_endorsement->cedant_premium ?? 0) !!};

                let newSumInsured = 0;
                let newCedantPremium = 0;

                if (type === 'increase') {
                    newSumInsured = Math.ceil(Math.max(0, parseFloat(currentSumInsured) + endorsedSumInsured));
                    newCedantPremium = Math.ceil(Math.max(0, parseFloat(currentPremium) + endorsedCedPremium));
                } else if (type === 'decrease') {
                    newSumInsured = Math.ceil(Math.max(0, parseFloat(currentSumInsured) - endorsedSumInsured));
                    newCedantPremium = Math.ceil(Math.max(0, parseFloat(currentPremium) - endorsedCedPremium));
                }

                let effectiveSumInsured = newSumInsured;

                if (applyEml === 'Y' && emlRate > 0) {
                    effectiveSumInsured = parseFloat(newSumInsured) * (emlRate / 100);
                    $('#eml_amt').val(numberWithCommas(effectiveSumInsured));
                }

                $('#new_cede_premium').val(numberWithCommas(endorsedCedPremium === 0 ? 0 : newCedantPremium));
                $('#new_total_sum_insured').val(numberWithCommas(newSumInsured));
                $('#new_effective_sum_insured').val(numberWithCommas(effectiveSumInsured));
                $('#endorsed_effective_sum_insured').val(numberWithCommas(effectiveSumInsured));
            }

            function calculateEndDate(startDate, extensionDays) {
                var start = new Date(startDate);
                start.setDate(start.getDate() + extensionDays);
                var endDate = start.toISOString().split('T')[0];
                return endDate;
            }

            function calculateExtensionDays(startDate, endDate) {
                var start = new Date(startDate);
                var end = new Date(endDate);

                var differenceMs = end - start;
                var extensionDays = Math.max(0, Math.ceil(differenceMs / (1000 * 60 * 60 * 24)));
                return extensionDays;
            }
        });
    </script>
@endpush
