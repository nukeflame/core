@extends('layouts.app')

@section('content')
    <style>
        #docIframe {
            width: 100%;
            height: 80px;
            border: none;
            overflow: auto;
        }

        .iframe-overlay {
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 100;
            background: rgba(255, 255, 255, 0);
            pointer-events: all;
        }

        #docIframe:hover+.iframe-overlay {
            pointer-events: none;
        }

        #emailTabContent {
            min-height: 50vh;
        }

        #emailTabContent .tab-pane {
            padding-top: 1rem;
            border: none;
        }
    </style>

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Cover Details</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Clients</a></li>
                    <li class="breadcrumb-item"><a href="#"
                            id="to-customer">{{ Str::ucfirst(strtolower($customer->name)) }}</a>
                    </li>
                    <li class="breadcrumb-item"><a href="#">Covers</a></li>
                    <li class="breadcrumb-item"><a href="#" id="to-cover">{{ $coverReg->cover_no }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cover Details</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <form action="{{ route('endorsements_list') }}" method="post" id="coverForm">
        @csrf
        <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
        <input type="hidden" name="customer_id" value="{{ $coverReg->customer_id }}" />
    </form>
    <form action="{{ route('customer.dtl') }}" method="post" id="customerForm">
        @csrf
        <input type="hidden" name="customer_id" value="{{ $coverReg->customer_id }}" />
    </form>
    <form action="{{ route('cover.editCoverForm') }}" method="post" id="editCoverForm">
        @csrf
        <input type="hidden" name="trans_type" value="EDIT" />
        <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
        <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
        <input type="hidden" name="customer_id" value="{{ $coverReg->customer_id }}" />
    </form>

    <!--Page Modal -->
    <div class="row row-cols-12">
        <div class="card mb-2 border col" style="background-color:transparent;">
            @if ($actionable)
                <div class="card-body p-3 mx-0 cover-info-wrapper"
                    style="background-color:var(--cover-bg);border-radius:0.375rem;">
                    @switch ($coverReg->verified)
                        @case(null)
                        @case('R')
                            @switch ($coverReg->type_of_bus)
                                @case('FPR')
                                @case('FNP')
                                    @if (count($endorsementNarration) > 0)
                                        @if (
                                            !in_array($endorsementNarration[0]?->endorse_type_slug, [
                                                'change-due-date',
                                                'change-sum-insured',
                                                'change-premium',
                                            ]))
                                            <button type="button"
                                                class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" id="edit-cover">
                                                <i class="bx bx-edit me-1 align-middle"></i> Edit Cover Details
                                            </button>
                                            <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                                id="schedule-details" data-bs-toggle="modal" data-bs-target="#schedulesModal">
                                                <i class="bx bx-plus me-1 align-middle"></i> Add Schedule Details
                                            </button>
                                            <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                                id="policy-clauses" data-bs-toggle="modal" data-bs-target="#clauses-modal">
                                                <i class="bx bx-file me-1 align-middle"></i> Add Policy Clauses
                                            </button>
                                        @endif
                                        <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" id="attachments"
                                            data-bs-toggle="modal" data-bs-target="#attachments-modal">
                                            <i class="bx bx-file me-1 align-middle"></i> Add File & Supporting Docs
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                            id="edit-cover">
                                            <i class="bx bx-edit me-1 align-middle"></i> Edit Cover Details
                                        </button>

                                        <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                            id="schedule-details" data-bs-toggle="modal" data-bs-target="#schedulesModal">
                                            <i class="bx bx-plus me-1 align-middle"></i> Add Schedule Details
                                        </button>
                                        <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" id="attachments"
                                            data-bs-toggle="modal" data-bs-target="#attachments-modal">
                                            <i class="bx bx-file me-1 align-middle"></i> Add File & Supporting Docs
                                        </button>
                                        <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                            id="policy-clauses" data-bs-toggle="modal" data-bs-target="#clauses-modal">
                                            <i class="bx bx-file me-1 align-middle"></i> Add Policy Clauses
                                        </button>
                                    @endif
                                @break

                                @case('TNP')
                                    @if (in_array($coverReg->transaction_type, ['NEW', 'REN']))
                                        <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                            id="insurance_classes" data-bs-toggle="modal" data-bs-target="#insurance-class-modal">
                                            <i class="bx bx-plus me-1 align-middle"></i> Classes of Insurance
                                        </button>
                                        <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                            id="mdp_installments" data-bs-toggle="modal" data-bs-target="#mdpInstallmentModal">
                                            <i class="bx bx-plus me-1 align-middle"></i> MDP Installments
                                        </button>
                                    @endif
                                @break

                                @case('TPR')
                                    @if (in_array($coverReg->transaction_type, ['NEW', 'REN']))
                                        <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                            id="insurance_classes" data-bs-toggle="modal" data-bs-target="#insurance-class-modal">
                                            <i class="bx bx-check-circle me-1 align-middle"></i> Classes of Insurance
                                        </button>
                                    @endif
                                @break
                            @endswitch

                            @if (count($endorsementNarration) > 0)
                                @if (!in_array($endorsementNarration[0]?->endorse_type_slug, ['change-due-date']))
                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                        id="reinsurers" data-bs-toggle="modal" data-bs-target="#reinsurer-modal">
                                        <i class="bx bx-plus me-1 align-middle"></i> Add Reinsurers
                                    </button>
                                    <a href="#" class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                        id="generate_slip">
                                        <i class="bx bx-analyse me-1 align-middle"></i> Generate Slip
                                    </a>
                                @endif
                                <iframe id="slipIframe" style="display: none; width: 100%; height: 600px;"></iframe>
                                <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                    id="verify_details">
                                    <i class="bx bx-plus me-1 align-middle"></i> <span id="verify-text">Verify
                                        Details</span>
                                </button>
                            @else
                                @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']) ||
                                        (!in_array($coverReg->type_of_bus, ['FPR', 'FNP']) && in_array($coverReg->transaction_type, ['NEW', 'REN'])))
                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                        id="reinsurers" data-bs-toggle="modal" data-bs-target="#reinsurer-modal">
                                        <i class="bx bx-plus me-1 align-middle"></i> Add Reinsurers
                                    </button>
                                @endif
                                <a href="#" class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                    id="generate_slip">
                                    <i class="bx bx-analyse me-1 align-middle"></i> Generate Slip
                                </a>
                                <iframe id="slipIframe" style="display: none; width: 100%; height: 600px;"></iframe>
                                <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                    id="verify_details">
                                    <i class="bx bx-plus me-1 align-middle"></i> <span id="verify-text">Verify
                                        Details</span>
                                </button>
                            @endif
                        @break

                        @case('P')
                            <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                id="verify_details">
                                <i class="bx bx-check me-1 align-middle"></i> <span id="verify-re-text">Re-escalate
                                    Verification</span>
                            </button>
                            <button class="btn btn-outline-danger mr-2 btn-sm btn-wave waves-effect waves-light" disabled
                                style="color: #ff0000;">
                                <i class="bx bx-check-circle me-1 align-middle"></i>Pending Verification
                            </button>
                        @break

                        @case('A')
                            @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']) ||
                                    (!in_array($coverReg->type_of_bus, ['FPR', 'FNP']) && !in_array($coverReg->transaction_type, ['NEW', 'REN'])))
                                <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                    data-bs-toggle="modal" data-bs-target="#debit-modal">
                                    <i class="bx bx-credit-card"></i> Generate Debit
                                </button>
                            @elseif(!in_array($coverReg->type_of_bus, ['FPR', 'FNP']) && in_array($coverReg->transaction_type, ['NEW', 'REN', 'EXT']))
                                <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                    id="commit-cover">
                                    <i class="bx bx-save"></i> Commit
                                </button>
                            @endif
                        @break

                    @endswitch
                </div>
            @else
            @endif
        </div>
        <div class="row-cols-12">
            <div class="card mb-2 custom-card border col">
                <div class="card-body">
                    <div class="row mb-1  bg-light p-2">
                        <div class="col-md-3">
                            <strong>Verification Status</strong>
                        </div>
                        <div class="col-md-2">
                            @switch($coverReg->verified)
                                @case(null)
                                @case('P')
                                    <span class="badge bg-danger-gradient"> Pending</span>
                                @break

                                @case('A')
                                    <span class="badge bg-success-gradient"> Approved</span>
                                @break

                                @case('R')
                                    <span class="badge bg-danger-gradient"> Rejected</span>
                                @break

                                @default
                            @endswitch
                        </div>
                        <div class="col-md-3">
                            <strong>Cover</strong>
                        </div>
                        <div class="col-md-4">
                            {{ $type_of_bus->bus_type_name }}
                        </div>
                    </div>
                    <div class="row mb-1  p-2">
                        <div class="col-md-3">
                            <strong>Client</strong>
                        </div>
                        <div class="col-md-2">
                            {{ firstUpper($customer->name) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Cover Period</strong>
                        </div>
                        <div class="col-md-2">
                            {{ formatDate($coverReg->cover_from) }} to {{ formatDate($coverReg->cover_to) }}
                        </div>
                    </div>
                    @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                        <div class="row mb-1 bg-light p-2">
                            <div class="col-md-3">
                                <strong>Total Sum Insured</strong>
                            </div>
                            <div class="col-md-2">
                                {{ number_format($coverReg->total_sum_insured, 2) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Effective Sum Insured</strong>
                            </div>
                            <div class="col-md-3">
                                {{ number_format($coverReg->effective_sum_insured, 2) }}
                            </div>
                        </div>
                    @endif
                    @switch ($coverReg->type_of_bus)
                        @case('FPR')
                        @case('FNP')
                            <div class="row mb-3  p-2">
                                <div class="col-md-3">
                                    <strong>Number of Installments</strong>
                                </div>
                                <div class="col-md-2">
                                    {{ $coverReg->no_of_installments }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Cedant Premium</strong>
                                </div>
                                <div class="col-md-2">
                                    {{ number_format($coverReg->cedant_premium, 2) }}
                                </div>
                            </div>
                            <div class="row mb-3 bg-light p-2">
                                <div class="col-md-3">
                                    <strong>Total Debits</strong>
                                </div>
                                <div class="col-md-2">
                                    1
                                </div>
                                <div class="col-md-3">
                                    <strong>Reinsurer Premium</strong>
                                </div>
                                <div class="col-md-2">
                                    {{ number_format($coverReg->rein_premium, 2) }}
                                </div>
                            </div>
                            <div class="row mb-3 bg-light p-2">
                                <div class="col-md-3">
                                    <strong>Reinsurer Commission ({{ number_format($coverReg->rein_comm_rate, 2) }}%)</strong>
                                </div>
                                <div class="col-md-2">
                                    {{ number_format($coverReg->rein_comm_amount, 2) }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Cedant Commission ({{ number_format($coverReg->cedant_comm_rate, 2) }}%)</strong>
                                </div>
                                <div class="col-md-2">
                                    {{ number_format($coverReg->cedant_comm_amount, 2) }}
                                </div>
                            </div>
                            <div class="row mb-3 p-2">
                                <div class="col-md-3">
                                    <strong>Our Share({{ number_format($coverReg->share_offered, 2) }}%)</strong>
                                </div>
                                <div class="col-md-2">
                                    {{ number_format(($coverReg->share_offered / 100) * $coverReg->cedant_premium, 2) }}
                                    {{-- {{ number_format($coverReg->cedant_premium,2)}} --}}
                                </div>
                                <div class="col-md-3">
                                    <strong>Brokerage Amount @if ($coverReg->brokerage_comm_type === 'R')
                                            ( {{ number_format($coverReg->brokerage_comm_rate, 2) }}% )
                                        @endif </strong>
                                </div>
                                <div class="col-md-2">
                                    {{-- @if ($coverReg->brokerage_comm_type === 'R')
                        {{ number_format($coverReg->brokerage_comm_amt,2)}}
                        @else --}}
                                    {{-- @endif --}}
                                </div>
                            </div>
                        @break

                        @case('TPR')
                        @case('TNP')
                            <div class="row mb-1 bg-light">
                                <div class="col-md-3">
                                    <strong>Cover Title</strong>
                                </div>
                                <div class="col-md-3">
                                    {{ $coverReg->cover_title }}
                                </div>

                                {{-- <div class="col-md-3">
                        <strong>Treaty Limit</strong>
                    </div>
                    <div class="col-md-2">
                        {{ number_format($coverReg->treaty_limit,2)}}
                    </div> --}}
                            </div>
                            @if (!in_array($coverReg->transaction_type, ['NEW', 'REN']))
                                @if (in_array($coverReg->transaction_type, ['QTR']))
                                    <div class="row mb-1">
                                        <div class="col-md-3">
                                            <strong>Premium</strong>
                                        </div>
                                        <div class="col-md-3">
                                            {{ number_format($TPRTotalPrem, 2) }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Commission</strong>
                                        </div>
                                        <div class="col-md-3">
                                            {{ number_format($TPRTotalCom, 2) }}
                                        </div>
                                    </div>
                                    <div class="row mb-1 bg-light">
                                        <div class="col-md-3">
                                            <strong>Claim</strong>
                                        </div>
                                        <div class="col-md-3">
                                            {{ number_format($TPRTotalClaim, 2) }}
                                        </div>
                                    </div>
                                @elseif (in_array($coverReg->transaction_type, ['MDP']))
                                    <div class="row mb-1">
                                        <div class="col-md-3">
                                            <strong>MDP</strong>
                                        </div>
                                        <div class="col-md-3">
                                            {{ number_format($TNPTotalMdp, 2) }}
                                        </div>
                                    </div>
                                @endif
                                <div class="row mb-1 bg-light">
                                    <div class="col-md-3">
                                        <strong>Premium Tax</strong>
                                    </div>
                                    <div class="col-md-3">
                                        {{ number_format($TPRTotalPremTax, 2) }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Reinsurance Tax</strong>
                                    </div>
                                    <div class="col-md-3">
                                        {{ number_format($TPRTotalRiTax, 2) }}
                                    </div>
                                </div>
                            @endif
                        @break
                    @endswitch
                </div>
            </div>
        </div>
        <div class="row-cols-12">
            <div class="card mb-2 custom-card border col">
                <div class="card-body pt-0">
                    <nav>
                        <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                            id="nav-tab" role="tablist">
                            @switch ($coverReg->type_of_bus)
                                @case('FPR')
                                @case('FNP')
                                    <button class="nav-link active" id="nav-schedules-tab" data-bs-toggle="tab"
                                        data-bs-target="#schedules-tab" type="button" role="tab" aria-selected="true"><i
                                            class="bx bx-table me-1 align-middle"></i>Schedule Details</button>
                                    <button class="nav-link" id="nav-attachments-tab" data-bs-toggle="tab"
                                        data-bs-target="#attachments-tab" type="button" role="tab" aria-selected="false"
                                        tabindex="-1"><i class="bx bx-file me-1 align-middle"></i>File & Support Docs</button>
                                    <button class="nav-link" id="nav-clauses-tab" data-bs-toggle="tab"
                                        data-bs-target="#clauses-tab" type="button" role="tab" aria-selected="false"
                                        tabindex="-1"><i class="bx bx-medal me-1 align-middle"></i>Policy Clauses</button>
                                    <button class="nav-link" id="nav-reinsurers-tab" data-bs-toggle="tab"
                                        data-bs-target="#reinsurers-tab" type="button" role="tab" aria-selected="false"
                                        tabindex="-1"><i class="bx bx-palette me-1 align-middle"></i>Reinsurers</button>
                                    @if ($coverReg->no_of_installments > 1)
                                        <button class="nav-link" id="nav-installments-tab" data-bs-toggle="tab"
                                            data-bs-target="#installments-tab" type="button" role="tab"
                                            aria-selected="false" tabindex="-1"><i
                                                class="bi bi-archive me-1 align-middle"></i>Installment Details</button>
                                    @endif
                                @break

                                @case('TPR')
                                @case('TNP')
                                    <button class="nav-link" id="nav-reinsurers-tab" data-bs-toggle="tab"
                                        data-bs-target="#reinsurers-tab" type="button" role="tab" aria-selected="false"
                                        tabindex="-1"><i class="bx bx-palette me-1 align-middle"></i>Reinsurers</button>
                                    <button class="nav-link" id="nav-ins-classes-tab" data-bs-toggle="tab"
                                        data-bs-target="#ins-classes-tab" type="button" role="tab" aria-selected="false"
                                        tabindex="-1"><i class="bx bx-award me-1 align-middle"></i>Insurance Classes</button>
                                @break
                            @endswitch
                            @if (count($endorsementNarration) > 0)
                                <button class="nav-link" id="nav-endorse-narration-tab" data-bs-toggle="tab"
                                    data-bs-target="#endorse-narration-tab" type="button" role="tab"
                                    aria-selected="false" tabindex="-1"><i
                                        class="bx bx-file-blank me-1 align-middle"></i>Narration</button>
                            @endif
                            <button class="nav-link" id="nav-approvals-tab" data-bs-toggle="tab"
                                data-bs-target="#approvals-tab" type="button" role="tab" aria-selected="false"
                                tabindex="-1"><i class="bx bx-check me-1 align-middle"></i>Approvals</button>
                            @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                <button class="nav-link" id="nav-debits-tab" data-bs-toggle="tab"
                                    data-bs-target="#debits-tab" type="button" role="tab" aria-selected="false"
                                    tabindex="-1"><i class="bx bx-credit-card me-1 align-middle"></i>Cedant</button>
                            @endif
                            <button class="nav-link" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#docs-tab"
                                type="button" role="tab" aria-selected="false" tabindex="-1"><i
                                    class="bx bx-file-blank me-1 align-middle"></i>Print-outs</button>
                        </div>
                    </nav>
                    <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
                        <div class="tab-pane active show" id="schedules-tab" role="tabpanel"
                            aria-labelledby="nav-schedules-tab" tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    <table id="schedules-table"
                                        class="table table-striped text-nowrap table-hover table-responsive"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">No.</th>
                                                <th scope="col">Title</th>
                                                <th scope="col">Details</th>
                                                <th scope="col">Position</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="attachments-tab" role="tabpanel" aria-labelledby="nav-attachments-tab"
                            tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    <table id="attachments-table"
                                        class="table table-striped text-nowrap table-hover table-responsive"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Title</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="clauses-tab" role="tabpanel" aria-labelledby="nav-clauses-tab"
                            tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    <table id="clauses-table"
                                        class="table table-striped text-nowrap table-hover table-responsive"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">Clause ID</th>
                                                <th scope="col">Clause Title</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="reinsurers-tab" role="tabpanel" aria-labelledby="nav-reinsurers-tab"
                            tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    <table id="reinsurers-table"
                                        class="table table-striped text-nowrap table-hover table-responsive"
                                        style="width: 100%!important;">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Reinsurer</th>
                                                <th scope="col">Share(%)</th>
                                                @switch ($coverReg->type_of_bus)
                                                    @case('FPR')
                                                    @case('FNP')
                                                        <th scope="col">Sum insured</th>
                                                        <th scope="col">Premium</th>
                                                        <th scope="col">Commission rate</th>
                                                        <th scope="col">Commission</th>
                                                        <th scope="col">Brokerage Commission</th>
                                                        <th scope="col">WHT Amount</th>
                                                        <th scope="col">Retro Amount</th>
                                                    @break

                                                    @case('TPR')
                                                        @if (!in_array($coverReg->transaction_type, ['NEW', 'REN']))
                                                            {{-- <th scope="col">Sum insured</th> --}}
                                                            <th scope="col">Premium</th>
                                                            <th scope="col">Commission</th>
                                                            <th scope="col">Claim Amt</th>
                                                            <th scope="col">Premium Tax</th>
                                                            <th scope="col">Reinsurance Tax</th>
                                                        @endif
                                                    @break

                                                    @case('TNP')
                                                        @if (!in_array($coverReg->transaction_type, ['NEW', 'REN']))
                                                            <th scope="col">Total MDP</th>
                                                            <th scope="col">MDP</th>
                                                        @endif
                                                    @break
                                                @endswitch
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="installments-tab" role="tabpanel"
                            aria-labelledby="nav-installments-tab" tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    <table id="installments-table"
                                        class="table table-striped text-nowrap table-hover table-responsive"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">No.</th>
                                                <th scope="col">Due Date</th>
                                                <th scope="col">Installment Amount</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="ins-classes-tab" role="tabpanel" aria-labelledby="nav-ins-classes-tab"
                            tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    <table id="insclass-table"
                                        class="table table-striped text-nowrap table-hover table-responsive"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">No.</th>
                                                <th scope="col">Reinsurance Class</th>
                                                <th scope="col">Class Code</th>
                                                <th scope="col">Class Name</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="endorse-narration-tab" role="tabpanel"
                            aria-labelledby="nav-endorse-narration-tab" tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    <table id="endorse-narration-table"
                                        class="table table-striped text-nowrap table-hover table-responsive"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">No.</th>
                                                <th scope="col">Endorsment Type</th>
                                                <th scope="col">Narration</th>
                                                @if ($endorsementNarration)
                                                    @foreach ($endorsementNarration as $endorsementNarratio)
                                                    @endforeach
                                                    <th scope="col">Extension Days</th>
                                                @endif
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="approvals-tab" role="tabpanel" aria-labelledby="nav-approvals-tab"
                            tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    <table id="approvals-table"
                                        class="table table-striped text-nowrap table-hover table-responsive"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Approver</th>
                                                <th scope="col">Comment</th>
                                                <th scope="col">Approver Comment</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="debits-tab" role="tabpanel" aria-labelledby="nav-debits-table"
                            tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    <table id="debits-table"
                                        class="table table-striped text-nowrap table-hover table-responsive"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID.</th>
                                                <th scope="col">Cedant</th>
                                                <th scope="col">Debit No.</th>
                                                <th scope="col">Installment</th>
                                                <th scope="col">Share(%)</th>
                                                <th scope="col">Sum insured</th>
                                                <th scope="col">Premium</th>
                                                {{-- <th scope="col">Commission</th> --}}
                                                <th scope="col">Gross</th>
                                                <th scope="col">Net Amount</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="docs-tab" role="tabpanel" aria-labelledby="nav-docs-tab"
                            tabindex="0">
                            <div class="card">
                                <div class="card-body py-3 px-2">
                                    @if (in_array($coverReg->type_of_bus, ['TPR', 'TNP']) && in_array($coverReg->transaction_type, ['NEW', 'REN', 'EXT']))
                                    @else
                                        <a href="{{ route('docs.coverdebitnote', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                                            target="_blank" rel="noopener noreferrer" class="print-out-link pr-3"
                                            id="generateDebitNote">
                                            <i class="bx bx-file me-1 align-middle"></i>Debit Note
                                        </a>
                                        <a href="{{ route('docs.coverdebitnote', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                                            id="generateCreditNote" rel="noopener noreferrer"
                                            data-endorsementno="{{ $coverReg->endorsement_no }}"
                                            class="print-out-link pr-3">
                                            <i class="bx bx-file me-1 align-middle"></i>Credit Notes
                                        </a>
                                    @endif
                                    <a href="{{ route('docs.coverslip', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                                        target="_blank" rel="noopener noreferrer" class="print-out-link pr-3"
                                        id="generateCoverSlip"><i class="bx bx-file me-1 align-middle"></i>Cover Slip
                                    </a>

                                    @if (count($endorsementNarration) > 0)
                                        <a href="{{ route('docs.endorsementslip', ['endorsement_no' => $coverReg->endorsement_no]) }}"
                                            target="_blank" rel="noopener noreferrer" class="print-out-link"
                                            id="generateEndorsementSlip">
                                            <i class="bx bx-file me-1 align-middle"></i> <span>Endorsement
                                                Notice Slip</span></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Schedules Modal -->
    <div class="modal effect-scale md-wrapper" id="schedulesModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticScheduleDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="" id="schedulesForm">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" required />
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" required />
                    <input type="hidden" name="id" id="id" />
                    <input type="hidden" name="schedule_id" id="schedule_id" />
                    <div class="modal-header">
                        <h5 class="modal-title dc-modal-title" id="staticScheduleDetailsLabel">Schedule Details
                        </h5>
                        <button type="button" aria-label="Close" class="btn-close btn-close-white closeScheduleForm"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label class="form-label fs-14" for="shed-header">Schedule item</label>
                                <div class="card-md">
                                    <select name="header" id="sched-header" class="form-inputs select2" required>
                                        <option value="">--Select Schedule items--</option>
                                        @foreach ($schedHeaders as $hdr)
                                            <option value="{{ $hdr->id }}" data-name="{{ $hdr->name }}">
                                                {{ $hdr->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label class="form-label fs-14" for="shed-header">Position</label>
                                <div class="card-md">
                                    <input type="number" name="schedule_position" id="schedule_position"
                                        class="form-control color-blk" />
                                </div>
                            </div>
                        </div>
                        <hr>
                        <input type="hidden" name="title" id="title" class="form-control color-blk" required />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label fs-14" for="schedule_description">Details</label>
                                    </div>
                                </div>
                                <div class="form-control section fac_section" id="schedule_description"
                                    contenteditable="true"
                                    style="border: 1px solid #363434; padding: 8px; min-height: 400px; resize: none; width:100%; overflow: auto; max-height: 500px; background-color: var(--input-bg-color); color: var(--input-text-color); border-radius: 0px;">
                                </div>
                                <textarea id="hidden_schedule_description" name="details" class="form-control resize-none d-none" rows="10"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm closeScheduleForm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="schedule-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Attachments Modal -->
    <div class="modal effect-scale md-wrapper" id="attachments-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticAttachemntLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="attachmentsForm">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <input type="hidden" name="id" id="attachments_id" value="{{ $coverReg->endorsement_no }}" />
                    <div class="modal-header">
                        <h5 class="modal-title dc-modal-title" id="staticAttachemntLabel">File & Supporting
                            Docs
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label fs-14" for="title">Title</label>
                                <div class="card-md">
                                    <select class="form-inputs select2" id="title" name="title">
                                        <option>--Select title--</option>
                                        <option value="Policy Schedule">Policy Schedule</option>
                                        <option value="Closings">Closings</option>
                                        <option value="Insured Items">Insured Items</option>
                                        <option value="Survey Report">Survey Report</option>
                                    </select>
                                </div>
                                {{-- <input type="text" class="form-control" id="title" name="title" required /> --}}
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-14" for="file">File</label>
                                <input type="file" class="form-control" id="file" name="file"
                                    accept=".pdf, .doc, .docx,.png,.jpg" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="attachments-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Policy clauses Modal -->
    <div class="modal effect-scale md-wrapper" id="clauses-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticPolicyLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="clausesForm">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <div class="modal-header">
                        <h5 class="modal-title dc-modal-title" id="staticPolicyLabel">Policy Clauses</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label fs-14">Select Clauses</label>
                                <select class="form-inputs select2" id="clauses" name="clauses[]" multiple="multiple"
                                    placeholder="Select clauses">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="clauses-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Reinsurer Modal -->
    <div class="modal effect-scale md-wrapper reinsurer-wrapper-modal" id="reinsurer-modal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%">
            <div class="modal-content">
                <form method="POST" action="" id="reinsurerForm">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            Reinsurer</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        @isset($coverTreaties)
                            @if ($coverTreaties->count() > 1)
                                <div class="col-md-4 mb-2">
                                    <label for="add-treaty-reinsurer" class="form-label">&nbsp;</label>
                                    <button class="btn btn-primary btn-sm" type="button" id="add-treaty-reinsurer">
                                        <i class="fa fa-plus"></i> Additional treaty-reinsurer section
                                    </button>
                                </div>
                            @endif
                        @endisset
                        <div id="treaty-div" class="reinsure-model-container">
                            <div class="treaty-div-section mb-2 mt-2 p-2" id="treaty-div-section-0" data-counter="0"
                                style="border: 1px solid #333;">
                                <div class="p-2 mb-2" style="border: 1px solid #333;">
                                    <div class="row">
                                        @if (in_array($coverReg->type_of_bus, ['TPR']))
                                            <div class="col-md-3">
                                                <label for="reinsurer-treaty-0" class="form-label">Treaty</label>
                                                @isset($coverTreaties)
                                                    @if ($coverTreaties->count() == 1)
                                                        <input class="form-control color-blk"
                                                            value="{{ $coverTreaties[0]->treaty_dtl->treaty_name }}"
                                                            readonly />
                                                        <input type="hidden" name="treaty[0][treaty]"
                                                            class="form-control treaties"
                                                            value="{{ $coverTreaties[0]->treaty }}" readonly />
                                                    @else
                                                        <select name="treaty[0][treaty]" id="reinsurer-treaty-0"
                                                            class="form-select reinsurer-treaty treaties" data-counter="0">
                                                            <option value="">--Select Treaty--</option>
                                                            @foreach ($coverTreaties as $coverTreaty)
                                                                <option value="{{ $coverTreaty->treaty }}"
                                                                    title="{{ $coverTreaty->treaty_name }}">
                                                                    {{ $coverTreaty->treaty_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                @endisset
                                            </div>
                                        @endif
                                        <div class="col-md-3">
                                            <label for="share_offered-0" class="form-label">Offered share&nbsp;(%)</label>
                                            <input type="text" name="treaty[0][share_offered]" id="share_offered-0"
                                                value="{{ number_format($coverReg->share_offered, 2) }}"
                                                class="form-control color-blk share_offered treaties disabled" readonly />
                                        </div>
                                        <div class="col-md-3">
                                            <label for="distributed_share-0"
                                                class="form-label">Distributed&nbsp;(%)</label>
                                            <input type="text" name="treaty[0][distributed_share]"
                                                id="distributed_share-0" class="form-control color-blk treaties disabled"
                                                readonly />
                                        </div>
                                        <div class="col-md-3">
                                            <label for="rem_share-0" class="form-label">Undistributed&nbsp;(%)</label>
                                            <input type="text" name="treaty[0][rem_share]" id="rem_share-0"
                                                class="form-control color-blk treaties disabled" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="reinsurer-total_sum_insured-0" class="form-label">Total
                                                        Effective
                                                        Sum Insured</label>
                                                    <input type="text" name="total_sum_insured"
                                                        id="reinsurer-total_sum_insured-0"
                                                        value="{{ number_format($coverReg->effective_sum_insured, 2) }}"
                                                        class="form-control color-blk disabled" readonly />
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="reinsurer-total_rein_premium-0" class="form-label">Total
                                                        Cedant
                                                        Premium</label>
                                                    <input type="text" name="total_rein_premium"
                                                        id="reinsurer-total_rein_premium-0"
                                                        value="{{ number_format($coverReg->rein_premium, 2) }}"
                                                        class="form-control color-blk disabled" readonly />
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="reinsurer-rein_comm_amt-0" class="form-label">Total
                                                        Reinsurers
                                                        Commission</label>
                                                    <input type="text" name="rein_comm_amt"
                                                        id="reinsurer-rein_comm_amt-0"
                                                        value="{{ number_format($coverReg->rein_comm_amount, 2) }}"
                                                        class="form-control color-blk disabled" readonly />
                                                </div>
                                                @if ($coverReg->brokerage_comm_type == 'A')
                                                    <div class="col-md-3">
                                                        <label for="total_brokerage_comm_amt-0" class="form-label">Total
                                                            Brokerage
                                                            Commission Amount</label>
                                                        <input type="text" name="treaty[0][total_brokerage_comm_amt]"
                                                            id="total_brokerage_comm_amt-0"
                                                            value="{{ number_format($coverReg->brokerage_comm_amt, 2) }}"
                                                            class="form-control color-blk treaties disabled" readonly />
                                                    </div>
                                                @elseif(in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                                    <div class="col-md-3">
                                                        <label for="total_brokerage_comm_rate-0" class="form-label">Total
                                                            Brokerage
                                                            Commission Rate(%)</label>
                                                        <input type="text" name="treaty[0][total_brokerage_comm_rate]"
                                                            id="total_brokerage_comm_rate-0"
                                                            value="{{ number_format($coverReg->brokerage_comm_rate, 2) }}"
                                                            class="form-control color-blk disabled treaties" readonly />
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-2" style="border: 1px solid #333;">
                                    <div id="reinsurer-div">
                                        <div id="reinsurer-div-0-0" data-treaty-counter="0" data-counter="0"
                                            class="reinsurer-section">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="einsurer-0-0" class="form-label">Reinsurer</label>
                                                    <div class="reinsurer-card">
                                                        <select name="treaty[0][reinsurers][0][reinsurer]"
                                                            id="reinsurer-0-0"
                                                            class="form-inputs select2 reinsurer reinsurers"
                                                            data-treaty-counter="0" data-counter="0" required>
                                                            <option value="">--Select Reinsurer--</option>
                                                            @foreach ($reinsurers as $partner)
                                                                @php
                                                                    $existsInCoverpart = $coverpart->contains(
                                                                        'partner_no',
                                                                        $partner->customer_id,
                                                                    );
                                                                    $existsInCoverRegister =
                                                                        $coverReg->customer_id == $partner->customer_id;
                                                                @endphp
                                                                @if (!$existsInCoverpart && !$existsInCoverRegister)
                                                                    <option value="{{ $partner->customer_id }}"
                                                                        title="{{ $partner->name }}">{{ $partner->name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="col-md-2">
                                                    <label for="written_share-0-0" class="form-label">Written
                                                        Lines(%)</label>
                                                    <input type="number" name="treaty[0][reinsurers][0][written_share]"
                                                        id="written_share-0-0"
                                                        class="form-control color-blk reinsurer-written-share reinsurers"
                                                        data-treaty-counter="0" data-counter="0" required />
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="" class="form-label">Signed Lines(%)</label>
                                                    <input type="number" name="treaty[0][reinsurers][0][share]"
                                                        id="share-0-0"
                                                        class="form-control color-blk reinsurer-share reinsurers"
                                                        data-treaty-counter="0" data-counter="0" required />
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="" class="form-label">WHT. Rate(%)</label>
                                                    <div class="reinsurer-card">
                                                        <select name="treaty[0][reinsurers][0][wht_rate]"
                                                            id="wht_rate-0-0" class="form-inputs select2" required>
                                                            <option value="">--Select WHT--</option>
                                                            @foreach ($whtRates as $whtRate)
                                                                <option value="{{ $whtRate->rate }}">
                                                                    {{ $whtRate->description }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="" class="form-label">Sum Insured</label>
                                                        <input type="text" name="treaty[0][reinsurers][0][sum_insured]"
                                                            data-counter="0" id="reinsurer-sum_insured-0"
                                                            class="form-control color-blk reinsurers disabled" required
                                                            readonly />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="reinsurer-premium-0" class="form-label">Reinsurer
                                                            Premium</label>
                                                        <input type="text" name="treaty[0][reinsurers][0][premium]"
                                                            id="reinsurer-premium-0"
                                                            class="form-control color-blk reinsurers reinsurer-premium"
                                                            data-counter="0"
                                                            onkeyup="this.value=numberWithCommas(this.value)" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="reinsurer-comm_rate-0" class="form-label">Reinsurer
                                                            Commission
                                                            Rate(%)</label>
                                                        <input type="text" name="treaty[0][reinsurers][0][comm_rate]"
                                                            data-counter="0" id="reinsurer-comm_rate-0"
                                                            class="form-control color-blk reinsurers reinsurer-comm-rate"
                                                            onkeyup="this.value=numberWithCommas(this.value)" required />
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label for="reinsurer-comm_amt-0" class="form-label">Reinsurer
                                                            Commission
                                                            Amount</label>
                                                        <input type="text" name="treaty[0][reinsurers][0][comm_amt]"
                                                            data-counter="0" id="reinsurer-comm_amt-0"
                                                            class="form-control color-blk reinsurers reinsurer-comm-amt"
                                                            onkeyup="this.value=numberWithCommas(this.value)" required />
                                                    </div>
                                                    <div class="col-md-3 fac_section_div">
                                                        <label class="form-label"
                                                            for="einsurer-brokerage_comm_amt-0">Brokerage Commission
                                                            Type</label>
                                                        <div class="cover-card">
                                                            <select name="brokerage_comm_type" id="brokerage_comm_type"
                                                                class="form-inputs section select2"
                                                                @if ($coverReg->type_of_bus != 'NEW') required @endif>
                                                                <option value=""
                                                                    @if ($coverReg->type_of_bus != 'NEW' && $coverReg->brokerage_comm_type == '') selected @endif>
                                                                    --Select basis--</option>
                                                                <option value="R"
                                                                    @if ($coverReg->type_of_bus != 'NEW' && $coverReg->brokerage_comm_type == 'R') selected @endif>Rate
                                                                </option>
                                                                <option value="A"
                                                                    @if ($coverReg->type_of_bus != 'NEW' && $coverReg->brokerage_comm_type == 'A') selected @endif>
                                                                    Quoted Amount</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 brokerage_comm_amt_div">
                                                        <label for="reinsurer-brokerage_comm_amt-0"
                                                            class="form-label">Brokerage Commission Amount</label>
                                                        <input type="text"
                                                            name="treaty[0][reinsurers][0][brokerage_comm_amt]"
                                                            data-counter="0" id="reinsurer-brokerage_comm_amt-0"
                                                            class="form-control color-blk reinsurers reinsurer-brokerage-comm-amt"
                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                            {{ $coverReg->brokerage_comm_type == 'A' ? 'required' : '' }} />
                                                    </div>
                                                    <div class="col-md-3 fac_section_div brokerage_comm_rate_div">
                                                        <label class="form-label" for="brokerage_comm_rate">Brokerage
                                                            Commission
                                                            Rate</label>
                                                        <input type="text"
                                                            class="form-control color-blk reinsurers brokerage_comm_rate"
                                                            id="brokerage_comm_rate" name="treaty[0][brokerage_comm_rate]"
                                                            onkeyup="this.value=numberWithCommas(this.value)">
                                                    </div>
                                                    <div class="col-md-3 fac_section_div brokerage_comm_rate_div">
                                                        <label class="form-label">Brokerage
                                                            Commission
                                                            Rate Amount</label>
                                                        <input type="text"
                                                            class="form-control color-blk reinsurers brokerage_comm_rate_amnt"
                                                            id="brokerage_comm_rate_amnt"
                                                            name="treaty[0][reinsurers][0][brokerage_comm_rate_amnt]">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="reinsurer-apply_fronting-0" class="form-label">Apply
                                                            Retro Fee </label>
                                                        <div class="reinsurer-card">
                                                            <select name="trevaty[0][reinsurers][0][apply_fronting]"
                                                                class="form-inputs select2 apply_fronting"
                                                                data-counter="0" id="reinsurer-apply_fronting-0" required>
                                                                <option value="">--Select option--</option>
                                                                <option value="Y">Yes</option>
                                                                <option value="N">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 fronting_div" id="fronting_rate_div-0">
                                                        <label for="reinsurer-fronting_rate-0" class="form-label">Retro
                                                            Rate(%)</label>
                                                        <input type="number"
                                                            name="treaty[0][reinsurers][0][fronting_rate]"
                                                            data-counter="0" id="reinsurer-fronting_rate-0"
                                                            class="form-control color-blk reinsurers reinsurer-fronting_rate" />
                                                    </div>
                                                    <div class="col-md-3 fronting_div" id="fronting_amt_div-0">
                                                        <label for="reinsurer-fronting_amt-0" class="form-label">Retro
                                                            Amount</label>
                                                        <input type="text"
                                                            name="treaty[0][reinsurers][0][fronting_amt]" data-counter="0"
                                                            id="reinsurer-fronting_amt-0"
                                                            class="form-control color-blk reinsurers disabled" readonly />
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="reins_pay_method" class="required">Payment Method</label>
                                                    <select class="form-inputs section" name="pay_method"
                                                        id="reins_pay_method" required>
                                                        <option selected value="">Choose Payment Method
                                                        </option>
                                                        @foreach ($paymethods as $pay_method)
                                                            @if ($coverReg->pay_method_code == $pay_method->pay_method_code)
                                                                <option value="{{ $pay_method->pay_method_code }}"
                                                                    pay_method_desc="{{ $pay_method->short_description }}"
                                                                    selected>
                                                                    {{ $pay_method->pay_method_name }}</option>
                                                            @else
                                                                <option value="{{ $pay_method->pay_method_code }}"
                                                                    pay_method_desc="{{ $pay_method->short_description }}">
                                                                    {{ $pay_method->pay_method_name }}</option>
                                                            @endif;
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">{{ $errors->first('pay_method') }}</span>
                                                </div>
                                                {{-- no of installments --}}
                                                <div class="col-md-2" id="no_of_installments_section"
                                                    style="{{ $isInstallment ? '' : 'display: none' }}">
                                                    <label class="required" id="no_of_installments_label">No. of
                                                        Installments</label>
                                                    <input type="number" class="form-control color-blk"
                                                        id="no_of_installments" name="no_of_installments"
                                                        value="{{ $isInstallment ? $coverReg->no_of_installments : 1 }}"
                                                        required />
                                                </div>
                                                <div class="col-md-2" id="add_reinsurer_btn_section"
                                                    style="{{ $isInstallment ? '' : 'display: none' }}">
                                                    <label style="height: 20px"></label></br>
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        id="add_reinsurer_instalments"> Add Installment </button>
                                                </div>
                                                <div class="row" id="add_installments_box"
                                                    style="{{ $isInstallment ? '' : 'display: none' }}">
                                                    <div class="col-md-12">
                                                        <h6>Installment plans</h6>
                                                        @if ($isInstallment)
                                                            <div id="reinsurer_plan_section">
                                                                @foreach ($coverInstallments as $installment)
                                                                    <div class="row reinsurer-instalament-row"
                                                                        data-count="{{ $installment->installment_no }}">
                                                                        <div class="col-md-3">
                                                                            <label class="">Installment</label>
                                                                            <input type="hidden" name="installment_no[]"
                                                                                value="{{ $installment->installment_no }}"
                                                                                readonly class="form-control color-blk" />
                                                                            <input type="text"
                                                                                value="Installment No. {{ $installment->installment_no }}"
                                                                                id="instl_no_{{ $installment->installment_no }}"
                                                                                readonly class="form-control color-blk"
                                                                                required />
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label
                                                                                for="instl_date_{{ $installment->installment_no }}">Installment
                                                                                Due Date</label>
                                                                            <input type="date"
                                                                                name="installment_date[]"
                                                                                id="instl_date_{{ $installment->installment_no }}"
                                                                                value="{{ $installment->installment_date }}"
                                                                                class="form-control color-blk" required />
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label
                                                                                for="instl_amnt_{{ $installment->installment_no }}">Total
                                                                                Installment Amount</label>
                                                                            <div class="input-group mb-3">
                                                                                <input type="text"
                                                                                    name="installment_amt[]"
                                                                                    id="instl_amnt_{{ $installment->installment_no }}"
                                                                                    value=""
                                                                                    class="form-control color-blk amount"
                                                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                                                    required />
                                                                                <button class="btn btn-danger btn-sm"
                                                                                    type="button"
                                                                                    id="remove_reinsurer_instalment">
                                                                                    <i
                                                                                        class="bx bx-minus align-middle"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div id="reinsurer_plan_section"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal"
                            id="dismiss-partner-btn">Close</button>
                        <button type="button" id="partner-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">
                            <span>Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Edit Reinsurer Modal -->
    <div class="modal effect-scale md-wrapper reinsurer-wrapper-modal" id="edit-reinsurer-modal"
        data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%">
            <div class="modal-content">
                <form method="POST" action="" id="EditReinsurerForm">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">
                            Reinsurer</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        @isset($coverTreaties)
                            @if ($coverTreaties->count() > 1)
                                <div class="col-md-4 mb-2">
                                    <label for="add-treaty-reinsurer" class="form-label">&nbsp;</label>
                                    <button class="btn btn-primary btn-sm" type="button" id="add-treaty-reinsurer">
                                        <i class="fa fa-plus"></i> Additional treaty-reinsurer section
                                    </button>
                                </div>
                            @endif
                        @endisset
                        <div id="treaty-div" class="reinsure-model-container">
                            <div class="treaty-div-section mb-2 mt-2 p-2" id="treaty-div-section-0" data-counter="0"
                                style="border: 1px solid #333;">
                                <div class="row">
                                    @if (in_array($coverReg->type_of_bus, ['TPR']))
                                        <div class="col-md-3">
                                            <label for="reinsurer-treaty-0" class="form-label">Treaty</label>
                                            @isset($coverTreaties)
                                                @if ($coverTreaties->count() == 1)
                                                    <input class="form-control color-blk"
                                                        value="{{ $coverTreaties[0]->treaty_dtl->treaty_name }}" readonly />
                                                    <input type="hidden" name="treaty[0][treaty]"
                                                        class="form-control treaties"
                                                        value="{{ $coverTreaties[0]->treaty }}" readonly />
                                                @else
                                                    <select name="treaty[0][treaty]" id="reinsurer-treaty-0"
                                                        class="form-select reinsurer-treaty treaties" data-counter="0">
                                                        <option value="">--Select Treaty--</option>
                                                        @foreach ($coverTreaties as $coverTreaty)
                                                            <option value="{{ $coverTreaty->treaty }}"
                                                                title="{{ $coverTreaty->treaty_name }}">
                                                                {{ $coverTreaty->treaty_name }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            @endisset
                                        </div>
                                    @endif
                                    <div class="col-md-3">
                                        <label for="share_offered-0" class="form-label">Offered share&nbsp;(%)</label>
                                        <input type="text" name="treaty[0][share_offered]" id="edshare_offered"
                                            value="{{ number_format($coverReg->share_offered, 2) }}"
                                            class="form-control color-blk share_offered treaties disabled" readonly />
                                    </div>
                                    <div class="col-md-3">
                                        <label for="distributed_share-0" class="form-label">Distributed&nbsp;(%)</label>
                                        <input type="text" name="distributed_share" id="eddistributed_share"
                                            class="form-control color-blk treaties disabled" readonly />
                                    </div>
                                    <div class="col-md-3">
                                        <label for="rem_share-0" class="form-label">Undistributed&nbsp;(%)</label>
                                        <input type="text" name="rem_share" id="edrem_share"
                                            class="form-control color-blk treaties disabled" readonly />
                                    </div>
                                </div>
                                <div class="row">
                                    @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                        <div class="row">
                                            <div class="col-m d-3">
                                                <label for="edreinsurer-total_sum_insured" class="form-label">Effective
                                                    Sum Insured</label>
                                                <input type="text" name="total_sum_insured"
                                                    id="edreinsurer-total_sum_insured"
                                                    value="{{ number_format($coverReg->effective_sum_insured, 2) }}"
                                                    class="form-control color-blk disabled" readonly />
                                            </div>
                                            <div class="col-md-3">
                                                <label for="edreinsurer-rein_premium" class="form-label">Reinsurers
                                                    Premium</label>
                                                <input type="text" name="rein_premium"
                                                    id="edreinsurer-rein_premium"
                                                    value="{{ number_format($coverReg->rein_premium, 2) }}"
                                                    class="form-control color-blk disabled" readonly />
                                            </div>
                                            <div class="col-md-3">
                                                <label for="edreinsurer-rein_comm_amt" class="form-label">Reinsurers
                                                    Commission</label>
                                                <input type="text" name="rein_comm_amt"
                                                    id="edreinsurer-rein_comm_amt"
                                                    value="{{ number_format($coverReg->rein_comm_amount, 2) }}"
                                                    class="form-control color-blk disabled" readonly />
                                            </div>
                                            @if ($coverReg->brokerage_comm_type == 'A')
                                                <div class="col-md-3">
                                                    <label for="total_brokerage_comm_amt-0" class="form-label">Brokerage
                                                        Commission Amount</label>
                                                    <input type="text" name="treaty[0][total_brokerage_comm_amt]"
                                                        id="total_brokerage_comm_amt-0"
                                                        value="{{ number_format($coverReg->brokerage_comm_amt, 2) }}"
                                                        class="form-control color-blk treaties disabled" readonly />
                                                </div>
                                            @elseif(in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                                <div class="col-md-3">
                                                    <label for="total_brokerage_comm_rate-0"
                                                        class="form-label">Brokerage
                                                        Commission Rate(%)</label>
                                                    <input type="text" name="treaty[0][total_brokerage_comm_rate]"
                                                        id="total_brokerage_comm_rate-0"
                                                        class="form-control color-blk disabled treaties" readonly />
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div id="reinsurer-div">
                                        <div id="reinsurer-div-0-0" data-treaty-counter="0" data-counter="0"
                                            class="reinsurer-section">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="edreinsurer_name" class="form-label">Reinsurer</label>
                                                    <div class="reinsurer-card">
                                                        <select name="reinsurer" id="edreinsurer"
                                                            class="form-inputs select2 reinsurer reinsurers"
                                                            data-treaty-counter="0" data-counter="0" required>
                                                            <option value="">--Select Reinsurer--</option>
                                                            @foreach ($reinsurers as $partner)
                                                                @php
                                                                    $existsInCoverpart = $coverpart->contains(
                                                                        'partner_no',
                                                                        $partner->customer_id,
                                                                    );
                                                                    $existsInCoverRegister =
                                                                        $coverReg->customer_id == $partner->customer_id;
                                                                @endphp
                                                                @if (!$existsInCoverpart && !$existsInCoverRegister)
                                                                    <option value="{{ $partner->customer_id }}"
                                                                        title="{{ $partner->name }}">
                                                                        {{ $partner->name }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <input type="hidden" name="tran_no" id="edtran_no"
                                                            readonly />
                                                        {{-- <input type="hidden" name="reinsurer" id="edreinsurer" readonly /> --}}
                                                    </div>

                                                </div>
                                                <div class="col-md-2">
                                                    <label for="edreinsurer-written-share" class="form-label">Written
                                                        Lines(%)</label>
                                                    <input type="number" name="written_share"
                                                        id="edreinsurer-written-share"
                                                        class="form-control color-blk reinsurer-written-share reinsurers"
                                                        data-treaty-counter="0" data-counter="0" required />
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="" class="form-label">Signed Lines(%)</label>
                                                    <input type="number" name="share" id="edreinsurer-share"
                                                        class="form-control color-blk reinsurer-share reinsurers"
                                                        data-treaty-counter="0" data-counter="0" required />
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="" class="form-label">WHT. Rate(%)</label>
                                                    <div class="reinsurer-card">
                                                        <select name="treaty[0][reinsurers][0][wht_rate]"
                                                            id="edreinsurer-wht_rate" class="form-inputs select2"
                                                            required>
                                                            <option value="">--Select WHT--</option>
                                                            @foreach ($whtRates as $whtRate)
                                                                <option value="{{ $whtRate->rate }}">
                                                                    {{ $whtRate->description }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (in_array($coverReg->type_of_bus, ['FPR', 'FNP']))
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label for="" class="form-label">Sum Insured</label>
                                                        <input type="text" name="sum_insured" data-counter="0"
                                                            id="edreinsurer-sum_insured"
                                                            class="form-control color-blk reinsurers disabled" required
                                                            readonly />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="" class="form-label">Premium</label>
                                                        <input type="number" name="premium" id="edreinsurer-premium"
                                                            class="form-control color-blk reinsurers reinsurer-premium"
                                                            onkeyup="this.value=numberWithCommas(this.value)" required />
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label for="reinsurer-comm_rate-0" class="form-label">Reinsurer
                                                            Commission Rate(%)</label>
                                                        <input type="number" name="comm_rate" data-counter="0"
                                                            id="edreinsurer-comm_rate"
                                                            class="form-control color-blk reinsurers reinsurer-comm-rate"
                                                            onkeyup="this.value=numberWithCommas(this.value)" required />
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label for="reinsurer-comm_amt-0" class="form-label">Reinsurer
                                                            Commission
                                                            Amt</label>
                                                        <input type="text" name="comm_amt" data-counter="0"
                                                            id="edreinsurer-comm_amt"
                                                            class="form-control color-blk reinsurers reinsurer-comm-amt"
                                                            onkeyup="this.value=numberWithCommas(this.value)" required />
                                                    </div>
                                                    @if ($coverReg->brokerage_comm_type == 'A')
                                                        <div class="col-md-3">
                                                            <label for="einsurer-brokerage_comm_amt-0"
                                                                class="form-label">Brokerage Commission
                                                                Amount</label>
                                                            <input type="text" name="brokerage_comm_amt"
                                                                data-counter="0" id="edreinsurer-brokerage_comm_amt"
                                                                class="form-control color-blk reinsurers reinsurer-brokerage-comm-amt disabled"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                required />
                                                        </div>
                                                    @else
                                                        <div class="col-md-3">
                                                            <label for="reinsurer-brokerage_comm_amt-0"
                                                                class="form-label">Brokerage Commission
                                                                Amt</label>
                                                            <input type="number" name="brokerage_comm_amt"
                                                                data-counter="0" id="edreinsurer-brokerage_comm_amt"
                                                                class="form-control color-blk reinsurers reinsurer-brokerage-comm-amt disabled"
                                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                                readonly />
                                                        </div>
                                                    @endif
                                                    <div class="col-md-3">
                                                        <label for="reinsurer-apply_fronting-0" class="form-label">Apply
                                                            Retro Fee </label>
                                                        <div class="reinsurer-card">
                                                            <select name="apply_fronting"
                                                                class="form-inputs select2 apply_fronting"
                                                                data-counter="0" id="edreinsurer-apply_fronting"
                                                                required>
                                                                <option value="">--Select option--</option>
                                                                <option value="Y">Yes</option>
                                                                <option value="N">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 fronting_div" id="fronting_rate_div-0">
                                                        <label for="reinsurer-fronting_rate-0" class="form-label">Retro
                                                            Rate(%)</label>
                                                        <input type="number" name="fronting_rate" data-counter="0"
                                                            id="edreinsurer-fronting_rate"
                                                            class="form-control color-blk reinsurers reinsurer-fronting_rate" />
                                                    </div>
                                                    <div class="col-md-3 fronting_div" id="fronting_amt_div-0">
                                                        <label for="reinsurer-fronting_amt-0" class="form-label">Retro
                                                            Amount</label>
                                                        <input type="text" name="fronting_amt" data-counter="0"
                                                            id="edreinsurer-fronting_amt"
                                                            class="form-control color-blk reinsurers disabled" readonly />
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="reins_pay_method" class="required">Payment
                                                        Method</label>
                                                    <select class="form-inputs section" name="pay_method"
                                                        id="reins_pay_method" required>
                                                        <option selected value="">Choose Payment Method
                                                        </option>
                                                        @foreach ($paymethods as $pay_method)
                                                            @if ($isInstallment)
                                                                <option value="{{ $pay_method->pay_method_code }}"
                                                                    pay_method_desc="{{ $pay_method->short_description }}"
                                                                    selected>
                                                                    {{ $pay_method->pay_method_name }}</option>
                                                            @else
                                                                @if ($pay_method->short_description == 'A')
                                                                    <option value="{{ $pay_method->pay_method_code }}"
                                                                        pay_method_desc="{{ $pay_method->short_description }}"
                                                                        selected>
                                                                        {{ $pay_method->pay_method_name }}</option>
                                                                @else
                                                                    <option value="{{ $pay_method->pay_method_code }}"
                                                                        pay_method_desc="{{ $pay_method->short_description }}">
                                                                        {{ $pay_method->pay_method_name }}</option>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger">{{ $errors->first('pay_method') }}</span>
                                                </div>
                                                {{-- no of installments --}}
                                                <div class="col-md-2" id="no_of_installments_section"
                                                    style="{{ $isInstallment ? '' : 'display: none' }}">
                                                    <label class="required" id="no_of_installments_label">No. of
                                                        Installments</label>
                                                    <input type="number" class="form-control color-blk"
                                                        id="no_of_installments" name="no_of_installments"
                                                        value="{{ $isInstallment ? $coverReg->no_of_installments : 1 }}"
                                                        required />
                                                </div>
                                                <div class="col-md-2" id="add_reinsurer_btn_section"
                                                    style="{{ $isInstallment ? '' : 'display: none' }}">
                                                    <label style="height: 20px"></label></br>
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        id="add_reinsurer_instalments"> Add Installment </button>
                                                </div>
                                                <div class="row" id="add_installments_box"
                                                    style="{{ $isInstallment ? '' : 'display: none' }}">
                                                    <div class="col-md-12">
                                                        <h6>Installment plans</h6>
                                                        @if ($isInstallment)
                                                            <div id="reinsurer_plan_section">
                                                                @foreach ($coverInstallments as $installment)
                                                                    <div class="row reinsurer-instalament-row"
                                                                        data-count="{{ $installment->installment_no }}">
                                                                        <div class="col-md-3">
                                                                            <label class="">Installment</label>
                                                                            <input type="hidden"
                                                                                name="installment_no[]"
                                                                                value="{{ $installment->installment_no }}"
                                                                                readonly class="form-control color-blk" />
                                                                            <input type="text"
                                                                                value="Installment No. {{ $installment->installment_no }}"
                                                                                id="instl_no_{{ $installment->installment_no }}"
                                                                                readonly class="form-control color-blk"
                                                                                required />
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label
                                                                                for="instl_date_{{ $installment->installment_no }}">Installment
                                                                                Due Date</label>
                                                                            <input type="date"
                                                                                name="installment_date[]"
                                                                                id="instl_date_{{ $installment->installment_no }}"
                                                                                value="{{ $installment->installment_date }}"
                                                                                class="form-control color-blk" required />
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label
                                                                                for="instl_amnt_{{ $installment->installment_no }}">Total
                                                                                Installment Amount</label>
                                                                            <div class="input-group mb-3">
                                                                                <input type="text"
                                                                                    name="installment_amt[]"
                                                                                    id="instl_amnt_{{ $installment->installment_no }}"
                                                                                    value=""
                                                                                    class="form-control color-blk amount"
                                                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                                                    required />
                                                                                <button class="btn btn-danger btn-sm"
                                                                                    type="button"
                                                                                    id="remove_reinsurer_instalment">
                                                                                    <i
                                                                                        class="bx bx-minus align-middle"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div id="reinsurer_plan_section"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal"
                            id="dismiss-partner-btn">Close</button>
                        <button type="button" id="partner-edit-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">
                            <span>Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Generate Slip -->
    <div class="modal effect-scale md-wrapper" id="generateSlipModal" tabindex="-1"
        aria-labelledby="staticGenerateSlip" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticGenerateSlip">Generated Slip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="position: relative;">
                    <iframe id="docIframe" src=""
                        style="width: 100%; height: 80vh; border: none; overflow: auto;"></iframe>
                    <div class="iframe-overlay"></div>
                </div>
            </div>
        </div>
    </div>

    <!--Verify Modal -->
    <div class="modal effect-scale md-wrapper verify-modal-wrapper" id="verify-modal" tabindex="-1"
        aria-labelledby="verifyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="verifyForm" action="{{ route('approvals.send-for-approval') }}">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
                    <input type="hidden" name="process" value="{{ $process?->id ?? '' }}" />
                    <input type="hidden" name="process_action" value="{{ $verifyprocessAction?->id ?? '' }}" />
                    <div class="modal-header">
                        <h5 class="modal-title" id="verifyModalLabel">Send to Verifier</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="approver" class="form-label">Approver</label>
                                <div class="verify-modal-card">
                                    <select name="approver" id="approver" class="form-inputs select2" required>
                                        <option value="">--Select Approver--</option>
                                        @foreach ($verifiers as $verifier)
                                            <option value="{{ $verifier->id }}">{{ $verifier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority</label>
                                <div class="verify-modal-card">
                                    <select name="priority" id="priority" class="form-inputs select2" required>
                                        <option value="">--Select Priority--</option>
                                        <option value="critical">Critical</option>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low" selected>Low</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="verify-comment" class="form-label">Comment</label>
                                <textarea name="comment" id="verify-comment" rows="4"
                                    class="form-control form-control-sm resize-none color-blk" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="verify-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Insurance classes Modal -->
    <div class="modal effect-scale md-wrapper" id="insurance-class-modal" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('cover.save_insurance_class') }}" id="insuranceClassForm">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
                    <div class="modal-header">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Insurance classes</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="treaty" class="form-label">Treaty Name</label>
                                <input type="text" class="form-control" id="treaty" name="treaty"
                                    value="{{ $coverReg->cover_title }}" readonly />
                            </div>
                            <div class="mb-3">
                                <label for="reinclass" class="form-label">Reinsurance Class</label>
                                <select name="reinclass" id="reinclass" class="form-select" required>
                                    <option value="">--Select class--</option>
                                    @foreach ($coverReinclass as $reinclass)
                                        <option value="{{ $reinclass->reinclass }}">
                                            {{ $reinclass->rein_class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="class" class="form-label">class</label>
                                <select name="class[]" id="insurance_class" class="form-select" multiple required>
                                    <option value="">--Select class--</option>
                                    @foreach ($ins_classes as $ins_cls)
                                        <option value="{{ $ins_cls->class_code }}">{{ $ins_cls->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="ins-class-save-btn"
                            class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Debit Modal -->
    <div class="modal effect-scale md-wrapper" id="debit-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticDebitLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="debitForm" action="{{ route('cover.generate-debit') }}">
                    @csrf
                    <input type="hidden" name="cover_no" value="{{ $coverReg->cover_no }}" />
                    <input type="hidden" name="endorsement_no" value="{{ $coverReg->endorsement_no }}" />
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticDebitLabel">Create A Debit Note
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Cover</label>
                                <input type="text" class="form-control form-control-sm"
                                    value="{{ $coverReg->cover_no }}" readonly required />
                            </div>
                            <div class="col-md-6">
                                <label for="">Endorsement</label>
                                <input type="text" class="form-control form-control-sm"
                                    value="{{ $coverReg->endorsement_no }}" readonly required />
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="">Installment</label>
                                <input type="text" name="installment" id="installment"
                                    class="form-control form-control-sm" value="{{ $nextInstallment }}" readonly
                                    required />
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="">Amount</label>
                                <input type="text" name="amount" id="amount"
                                    class="form-control form-control-sm amount"
                                    value="{{ number_format($installmentAmount, 2) }}" readonly required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="debit-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Document attachement Modal  -->
    <div class="modal effect-scale md-wrapper" id="attachment-document-modal" aria-labelledby="staticBackdropLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="preview-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Email doc -->
    <div class="modal effect-scale md-wrapper" id="sendReinDocumentEmail" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white text-center" id="sendReinDocumentEmailLabel">
                        <i class="bx bx-envelope me-2 fs-15" style="vertical-align: middle"></i>Facultative Submission
                        (To
                        Reinsurer) - Email Composer
                    </h5>
                    <button type="button" class="btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-header bg-light border-bottom">
                                <ul class="nav nav-tabs card-header-tabs" id="emailTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="compose-tab" data-bs-toggle="tab"
                                            data-bs-target="#compose" type="button" role="tab">
                                            <i class="bx bx-envelope me-2 fs-15"
                                                style="vertical-align: middle"></i>Compose
                                        </button>
                                    </li>
                                    {{-- <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="replies-tab" data-bs-toggle="tab"
                                            data-bs-target="#replies" type="button" role="tab">
                                            <i class="bx bx-reply me-2 fs-15" style="vertical-align: middle"></i>Reply
                                            to
                                            Messages
                                        </button>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content" id="emailTabContent">
                                <div class="tab-pane fade show active" id="compose" role="tabpanel">
                                    @include('cover.emails.reinsurers.compose-form')
                                </div>

                                {{-- <div class="tab-pane fade" id="replies" role="tabpanel">
                                    @include('cover.emails.reinsurers.messages-list')
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal effect-scale md-wrapper" id="send-email-modal" tabindex="-1" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-hidden="true" aria-labelledby="staticReinsuranceLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticReinsuranceLabel"
                        style="font-size: 19px; vertical-align: -3px;"><i class="bx bx-envelope"></i> Compose Mail
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body hiiden">
                    <form id="reinsurerEmailForm" action="{{ route('cover.sendreinsurer.email') }}" method="POST">
                        @csrf
                        <input type="hidden" name="coverNo" id="coverNo" />
                        <input type="hidden" name="endorsementNo" id="endorsementNo" />
                        <div class="row">
                            <div class="col-xl-4">
                                <div class="mb-2">
                                    <label for="fromMail" class="form-label">From</label>
                                    <input type="email" class="form-inputs" id="fromMail"
                                        value="reinsurance@acentriagroup.com">
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <div class="mb-2">
                                    <label for="emailTo" class="form-label">Email To</label>
                                    <select class="form-inputs select2" id="emailTo" multiple name="emailTo"
                                        required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="emailCc" class="form-label">CC</label>
                                    <input type="email" class="form-inputs color-blk" id="emailCc"
                                        name="email_cc" placeholder="Separate multiple emails with semicolons">
                                    <small class="form-text text-muted mb-2">For multiple CC recipients, separate emails
                                        with
                                        semicolons (;)</small>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="emailSubject" class="form-label">Subject</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-tag"></i>
                                        </span>
                                        <input type="text" class="form-control color-blk" name="emailSubject"
                                            id="emailSubject" placeholder="Enter email subject" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="emailContent" class="form-label">Content</label>
                                    <div class="card">
                                        <div class="card-header bg-white p-0">
                                            {{-- <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-light border-radius-0"><i
                                                        class="bi bi-type-bold"></i></button>
                                                <button type="button" class="btn btn-light"><i
                                                        class="bi bi-type-italic"></i></button>
                                                <button type="button" class="btn btn-light"><i
                                                        class="bi bi-list-ul"></i></button>
                                                <button type="button" class="btn btn-light"><i
                                                        class="bi bi-link"></i></button>
                                            </div> --
                                        </div>
                                        <div class="card-body p-0">
                                            <textarea class="form-control form-control-sm resize-none color-blk" id="reinsurer-email" rows="7"
                                                placeholder="Enter email content" name="emailContent" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 hidden">
                                <div class="mails-information mb-2">
                                    <div class="mail-attachments">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="mb-0">
                                                <span class="fs-14 fw-semibold"><i
                                                        class="ri-attachment-2 me-1 align-middle"></i>Attachments <span
                                                        id="attachment_status"></span></span>
                                            </div>
                                            <div>
                                            </div>
                                        </div>
                                        <div class="mt-2 d-flex" id="attachments-container">

                                            <a href="javascript:void(0);"
                                                class="mail-attachement-sendbtn btn btn-icon btn-outline-light ms-2 btn-lg border"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-title="Upload">
                                                <i class="ri-attachment-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                    {{-- <label class="form-label">Attachments</label>
                                    <div class="attachment-box">
                                        <label
                                            class="d-flex align-items-center justify-content-center gap-2 cursor-pointer text-dark">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="text-secondary">
                                                <path
                                                    d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48">
                                                </path>
                                            </svg>
                                            <span class="text-dark fs-14">Choose files</span>
                                            <input type="file" class="d-none" multiple>
                                        </label>
                                    </div> --
                                </div>

                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex gap-2">
                        {{-- <button type="button" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">
                            <i class="bi bi-eye"></i> Preview
                        </button> --
                        <button type="button" class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light"
                            id="sendButton">
                            <i class="bi bi-send"></i> Send Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Confirmation Modal -->
    <div class="modal effect-scale md-wrapper" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Email</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-15 p-0 m-0">Are you sure you want to send the email?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light btn-wave waves-effect waves-light"
                        id="cancelRenewalEmail" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success label-btn label-end" id="confirmReinEmail">
                        Confirm Send
                        <i class="ri-mail-send-line label-btn-icon ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Custom Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-default">
                <img class="bd-placeholder-img rounded me-2" src="../assets/images/brand-logos/favicon.ico"
                    alt="...">
                <strong class="me-auto">Notify</strong>
                <small>11 mins ago</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Approved Notification
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.brokerage_comm_amt_div').hide();
            $('.brokerage_comm_rate_div').hide();
            // $('.brokerage_comm_rate_amnt_div').hide();
            let defaultEmailTemplate = null;

            //clauses Fields
            var clauses = {!! json_encode($clauses) !!};
            var selected_clauses = {!! json_encode($selected_clauses) !!};
            var installmentTotalAmount = 0;

            populateSelect('clauses', clauses, selected_clauses);

            $('#clauses-modal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#clauses-modal')
                });
            });

            $('#attachments-modal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#attachments-modal')
                });
            });

            $('#verify-modal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#verify-modal')
                });
            });

            $('#insurance-class-modal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#insurance-class-modal')
                });
            });

            $('#reinsurer-modal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#reinsurer-modal')
                });
            });

            $('#edit-reinsurer-modal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#edit-reinsurer-modal')
                });
            });

            $('#schedulesModal').on('shown.bs.modal', function() {
                $('.form-inputs').select2({
                    dropdownParent: $('#schedulesModal')
                });
            });

            // Set Nav active
            function setActiveLink() {
                var hash = window.location.hash;
                $('.reinsurers-details-card .nav-link')
                    .attr('aria-selected', 'false')
                    .attr('tabindex', '-1')
                    .removeClass('active');
                $('.reinsurers-tabpane-card .tab-pane').removeClass('active').removeClass('show');
                if (hash) {
                    $('.reinsurers-details-card .nav-link#nav-' + hash.substring(1)).attr('data-bs-target', hash);
                    $('.reinsurers-details-card .nav-link#nav-' + hash.substring(1)).removeAttr('tabindex')
                    $('.reinsurers-details-card .nav-link#nav-' + hash.substring(1)).addClass('active')
                    $('.reinsurers-details-card .nav-link#nav-' + hash.substring(1)).attr('aria-selected', 'true')

                    $(`.reinsurers-tabpane-card .tab-pane[aria-labelledby="nav-${hash.substring(1)}"]`).addClass(
                        'active').addClass('show');
                } else {
                    window.history.pushState(null, null, window.location.pathname + window.location.search +
                        '#nav-schedules-tab');
                }
            }
            setActiveLink();

            $('.reinsurers-details-card .nav-link').on('click', function() {
                const hash = $(this).data('bs-target');
                // Update the URL hash
                if (hash) {
                    window.history.pushState(null, null, window.location.pathname + window.location.search +
                        hash);
                }
                $('.reinsurers-details-card .nav-link').removeClass('active');
                $(this).addClass('active');
            });

            $(window).on('hashchange', function() {
                setActiveLink();
            });

            const coverpart = '{!! $coverpart !!}';
            const coverpartners = JSON.parse(coverpart);

            const reinsurersStr = '@json($reinsurers)';
            const REINSURERS = JSON.parse(reinsurersStr);

            var distributedShare = origDistributedShare = 0;
            coverpartners.forEach(partner => {
                distributedShare += parseFloat(partner.share)
                origDistributedShare += parseFloat(partner.share)
            });

            const share_offeredStr = '{!! $coverReg->share_offered !!}' || 0
            const TYPE_OF_BUS = '{!! $coverReg->type_of_bus !!}' || 0
            const share_offered = parseFloat(share_offeredStr)
            let rem_share = share_offered - origDistributedShare
            $('#distributed_share').val(origDistributedShare);
            $('#rem_share').val(rem_share);

            $('.fronting_div').hide();


            function ComputeShareAmounts(elem, counter, isEdit = 'N') {
                if (isEdit != 'Y') {
                    let total_share = 0
                    $('.reinsurer-share').each(function() {
                        const itemCounter = $(this).data('counter');
                        const itemTreatyCounter = $(this).data('treaty-counter');
                        if (itemCounter != counter && itemTreatyCounter == treatyCounter) {
                            total_share += parseFloat($(this).val()) || 0
                        }
                    });
                }

                const currDistributedShare = origDistributedShare + total_share
                // Retrieve other relevant values from the modal
                let rem_share = share_offered - currDistributedShare;
                // let totalSumInsured = removeCommas($(`#reinsurer-modal #reinsurer-total_sum_insured-${counter}`).val()) || 0;
                let totalSumInsured = '{!! $coverReg->total_sum_insured !!}' || 0;
                // let reinPremium = removeCommas($(`#reinsurer-modal #reinsurer-rein_premium-${counter}`).val()) || 0;
                let reinPremium = '{!! $coverReg->rein_premium !!}' || 0;
                // let reinCommAmount = removeCommas($(`#reinsurer-modal #reinsurer-rein_comm_amt-${counter}`).val()) || 0;
                let reinCommAmount = '{!! $coverReg->rein_comm_amount !!}' || 0;

                distributedShare = currDistributedShare + sharePercentage
                const new_rem_share = share_offered - distributedShare

                rem_share = parseFloat(rem_share)
                totalSumInsured = parseFloat(totalSumInsured)
                reinPremium = parseFloat(reinPremium)
                reinCommAmount = parseFloat(reinCommAmount)

                // Calculate values based on the entered share
                const sumInsured = (sharePercentage / 100) * removeCommas(totalSumInsured);
                const premium = (sharePercentage / 100) * removeCommas(reinPremium);
                const commAmount = (commRate / 100) * premium;

                return {
                    'sumInsured': sumInsured,
                    'premium': premium,
                    'commAmount': commAmount
                }
            }

            $('#brokerage_comm_type').change(function(e) {
                const brokerageCommType = $(this).val()

                $('.brokerage_comm_amt_div').hide();
                $('.brokerage_comm_rate_div').hide();
                // $('.brokerage_comm_rate_amnt_div').hide();

                @if ($coverReg->type_of_bus == 'EDIT')
                    var brokerage_comm_rate =
                        {!! json_encode(number_format($coverReg->brokerage_comm_rate, 4)) !!};
                    var brokerage_comm_amt =
                        {!! json_encode(number_format($coverReg->brokerage_comm_amt, 2)) !!};
                    $('#brokerage_comm_rate').val(brokerage_comm_rate);
                    $('#reinsurer-brokerage_comm_amt-0').val(brokerage_comm_amt);
                @else
                    $('#brokerage_comm_rate').val(null);
                    $('#reinsurer-brokerage_comm_amt-0').val(null);
                @endif
                if (brokerageCommType == 'R') {
                    $('.brokerage_comm_rate_div').show();
                    $('.brokerage_comm_rate_amnt_div').show();
                    $('#brokerage_comm_rate').show();
                    $('#brokerage_comm_rate_amnt').show();
                    calculateBrokerageCommRate()
                } else {
                    $('.brokerage_comm_amt_div').show();
                    $('#reinsurer-brokerage_comm_amt-0').show().prop('disabled', false);
                }
            });
            $('#brokerage_comm_type').trigger('change')

            function calculateBrokerageCommRate() {
                let cedantCommRate = {!! $coverReg->cedant_comm_rate !!};
                let reinCommRate = removeCommas($('#reinsurer-comm_rate-0').val())
                let commAmt = parseFloat(removeCommas($('#reinsurer-premium-0').val())) || 0;
                let reinCommAmnt = parseFloat(removeCommas($('#reinsurer-comm_amt-0').val())) || 0;

                let brokerageCommRate = 0
                if (cedantCommRate != '' && cedantCommRate != null && reinCommRate != '' && reinCommRate !=
                    null) {
                    brokerageCommRate = Math.max(0, parseFloat(reinCommRate) - parseFloat(cedantCommRate));
                }
                let brokerageCommRateAmnt = (brokerageCommRate / 100) * commAmt

                $('#brokerage_comm_rate').val(numberWithCommas(brokerageCommRate.toFixed(2)));
                $('#brokerage_comm_rate_amnt').val(numberWithCommas(brokerageCommRateAmnt.toFixed(2)));
            }

            // Event listener for share input
            $('#reinsurer-modal').on('input', '.reinsurer-share', function() {
                const isEdit = $(this).data('edit');
                // const ReinsurerAmts =ComputeShareAmounts(this,counter,isEdit);
                // Get the entered share value
                const brokerageCommType = '{{ $coverReg->brokerage_comm_type }}'
                const sharePercentage = parseFloat($(this).val()) || 0;
                const counter = $(this).data('counter');
                const treatyCounter = $(this).data('treaty-counter');
                const treatyCode = $(`#reinsurer-treaty-${treatyCounter}`).val();
                const commRate = parseFloat($(`#reinsurer-modal #reinsurer-comm_rate-${counter}`).val()) ||
                    '{{ $coverReg->rein_comm_rate }}' || 0;

                $(`#reinsurer-modal #reinsurer-sum-insured-${counter}`).val('');
                $(`#reinsurer-modal #reinsurer-premium-${counter}`).val('');
                $(`#reinsurer-modal #reinsurer-comm_amt-${counter}`).val('');
                $(`#reinsurer-modal #reinsurer-brokerage_comm_amt-${counter}`).val('');
                $(`#reinsurer-modal #reinsurer-rein_premium-${counter}`).val('');
                $(`#reinsurer-modal #reinsurer-cedant_premium-${counter}`).val('');

                let total_share = 0
                $('.reinsurer-share').each(function() {
                    const itemCounter = $(this).data('counter');
                    const itemTreatyCounter = $(this).data('treaty-counter');
                    if (itemCounter != counter && itemTreatyCounter == treatyCounter) {
                        total_share += parseFloat($(this).val()) || 0
                    }
                });

                const currDistributedShare = origDistributedShare + total_share
                let rem_share = share_offered - currDistributedShare;
                // let totalSumInsured = removeCommas($(`#reinsurer-modal #reinsurer-total_sum_insured-${counter}`).val()) || 0;
                let totalSumInsured = '{!! $coverReg->total_sum_insured !!}' || 0;
                let totalReinPremium = '{!! $coverReg->rein_premium !!}' || 0;
                // let totalReinPremium = '{!! $coverReg->rein_premium !!}' || 0;
                let reinPremium = removeCommas($(`#reinsurer-modal #reinsurer-rein_premium-${counter}`)
                    .val()) || 0;
                // let reinCommAmount = removeCommas($(`#reinsurer-modal #reinsurer-rein_comm_amt-${counter}`).val()) || 0;
                let reinCommAmount = '{!! $coverReg->rein_comm_amount !!}' || 0;
                let cedantPremium = '{!! $coverReg->rein_premium !!}' || removeCommas($(
                        `#reinsurer-modal #reinsurer-cedant_premium-${counter}`)
                    .val()) || 0;

                distributedShare = currDistributedShare + sharePercentage
                const new_rem_share = share_offered - distributedShare

                rem_share = parseFloat(rem_share)
                totalSumInsured = parseFloat(totalSumInsured)
                reinPremium = parseFloat(reinPremium)
                reinCommAmount = parseFloat(reinCommAmount)
                totalReinPremium = parseFloat(totalReinPremium)
                cedantPremium = parseFloat(cedantPremium)

                // Calculate values based on the entered share
                const sumInsured = (sharePercentage / 100) * removeCommas(totalSumInsured);
                const premium = (sharePercentage / 100) * removeCommas(totalReinPremium);
                const cPremium = (sharePercentage / 100) * removeCommas(totalReinPremium);
                const commAmount = (commRate / 100) * premium;

                const shareReinPremium = (sharePercentage / 100) * premium;

                // Call the function to compute commission
                computeCommissionAmt(counter);

                // Check if the total share exceeds 100%
                if (sharePercentage > share_offered || sharePercentage > rem_share) {
                    alert(`Total share cannot exceed ${rem_share}%.`);
                    $(this).val(''); // Clear the input
                    $(`#distributed_share-${treatyCounter}`).val(currDistributedShare);
                    $(`#rem_share-${treatyCounter}`).val(rem_share);
                    return;
                }

                // Check if the total amounts exceed respective totals
                if (sumInsured > totalSumInsured) {
                    alert(`Total amounts cannot exceed respective totals.total Sum Insured: ${totalSumInsured},
                reinsurer sum insured: ${sumInsured} `);
                    $(this).val(''); // Clear the input
                    return;
                }
                if (premium > totalReinPremium) {
                    alert(
                        `Reinsurer's premium cannot exceed respective total premium.total premium: ${reinPremium},reinsurer Premium: ${premium}`
                    );
                    $(this).val('');
                    return;
                }

                // rem_share = rem_share-sharePercentage;
                // Update the corresponding input fields with the calculated values
                $(`#distributed_share-${treatyCounter}`).val(distributedShare);
                $(`#rem_share-${treatyCounter}`).val(new_rem_share);
                $(`#reinsurer-modal #reinsurer-sum_insured-${treatyCounter}`).val(numberWithCommas(
                    sumInsured.toFixed(2)));
                $(`#reinsurer-modal #reinsurer-premium-${treatyCounter}`).val(numberWithCommas(premium
                    .toFixed(2)));
                $(`#reinsurer-modal #reinsurer-comm_amt-${treatyCounter}`).val(numberWithCommas(commAmount
                    .toFixed(2)));
                $(`#reinsurer-modal #reinsurer-rein_premium-${treatyCounter}`).val(numberWithCommas(premium
                    .toFixed(2)));
                $(`#reinsurer-modal #reinsurer-cedant_premium-${treatyCounter}`).val(numberWithCommas(
                    premium.toFixed(2)));
                $(`#reinsurer-modal #reinsurer-comm_rate-${treatyCounter}`).val(numberWithCommas(parseFloat(
                    commRate).toFixed(2)));

                calculateBrokerageCommRate()
            });

            $('#edit-reinsurer-modal').on('input', '#edreinsurer-share', function() {
                const isEdit = $(this).data('edit');
                // const ReinsurerAmts =ComputeShareAmounts(this,counter,isEdit);

                // Get the entered share value
                const sharePercentage = parseFloat($(this).val()) || 0;
                const counter = $(this).data('counter');
                const treatyCounter = $(this).data('treaty-counter');
                const treatyCode = $(`#edreinsurer-treaty`).val();
                const commRate = parseFloat($(`#edit-reinsurer-modal #edreinsurer-comm_rate`).val()) || 0;

                $(`#edit-reinsurer-modal #reinsurer-sum-insured`).val('');
                $(`#edit-reinsurer-modal #reinsurer-premium`).val('');
                $(`#edit-reinsurer-modal #reinsurer-comm_amt`).val('');

                const orig_share = $(`#edreinsurer-orig-share`).val();
                const currDistributedShare = origDistributedShare - orig_share

                let rem_share = share_offered - currDistributedShare;
                // let totalSumInsured = removeCommas($(`#reinsurer-modal #reinsurer-total_sum_insured-${counter}`).val()) || 0;
                let totalSumInsured = '{!! $coverReg->total_sum_insured !!}' || 0;
                // let reinPremium = removeCommas($(`#reinsurer-modal #reinsurer-rein_premium-${counter}`).val()) || 0;
                let reinPremium = '{!! $coverReg->rein_premium !!}' || 0;
                // let reinCommAmount = removeCommas($(`#reinsurer-modal #reinsurer-rein_comm_amt-${counter}`).val()) || 0;
                let reinCommAmount = '{!! $coverReg->rein_comm_amount !!}' || 0;

                distributedShare = currDistributedShare + sharePercentage
                const new_rem_share = share_offered - distributedShare

                rem_share = parseFloat(rem_share)
                totalSumInsured = parseFloat(totalSumInsured)
                reinPremium = parseFloat(reinPremium)
                reinCommAmount = parseFloat(reinCommAmount)

                // Calculate values based on the entered share
                const sumInsured = (sharePercentage / 100) * removeCommas(totalSumInsured);
                const premium = (sharePercentage / 100) * removeCommas(reinPremium);
                const commAmount = (commRate / 100) * premium;

                // Check if the total share exceeds 100%
                if (sharePercentage > share_offered || sharePercentage > rem_share) {
                    alert(`Total share cannot exceed ${rem_share}%.`);
                    $(this).val(''); // Clear the input
                    $(`#eddistributed_share`).val(currDistributedShare);
                    $(`#edrem_share`).val(rem_share);
                    return;
                }

                // Check if the total amounts exceed respective totals
                if (sumInsured > totalSumInsured) {
                    alert(`Total amounts cannot exceed respective totals.total Sum Insured: ${totalSumInsured},
                    reinsurer sum insured: ${sumInsured} `);
                    $(this).val(''); // Clear the input
                    return;
                }
                if (premium > reinPremium) {
                    alert(`Reinsurer's premium cannot exceed respective total premium.total premium: ${reinPremium},
                    reinsurer Premium: ${premium}`);
                    $(this).val(''); // Clear the input
                    return;
                }

                // rem_share = rem_share-sharePercentage;
                // Update the corresponding input fields with the calculated values
                $(`#eddistributed_share`).val(distributedShare);
                $(`#edrem_share`).val(new_rem_share);
                $(`#edit-reinsurer-modal #edreinsurer-sum_insured`).val(numberWithCommas(sumInsured.toFixed(
                    2)));
                $(`#edit-reinsurer-modal #edreinsurer-premium`).val(numberWithCommas(premium.toFixed(2)));
                $(`#edit-reinsurer-modal #edreinsurer-comm_amt`).val(numberWithCommas(commAmount.toFixed(
                    2)));
            });

            $(document).on('click', '.edit-reinsurer', function() {
                const data = $(this).data('data');
                const reinsurer = $(this).data('reinsurer');
                distributedShare = origDistributedShare = $(this).data('distributed-share');
                const rem_share = share_offered - distributedShare
                const sum_insured = parseFloat(data.sum_insured);
                const premium = parseFloat(data.premium);
                const fronting_rate = parseFloat(data.fronting_rate);
                const fronting_amt = parseFloat(data.fronting_amt);
                const brokerage_comm_amt = parseFloat(data.brokerage_comm_amt);
                const comm_rate = parseFloat(data.comm_rate);
                const commission = parseFloat(data.commission);
                const share = parseFloat((data.share)).toFixed(2)
                const written_lines = parseFloat((data.written_lines)).toFixed(2)
                const tran_no = data.tran_no
                let apply_fronting = 'N'
                if (fronting_rate > 0) {
                    apply_fronting = 'Y';
                }

                // edshare-0-0
                $(`#edtran_no`).val(tran_no);
                // $('#edreinsurer-0-0').val('someValue').trigger('change');
                $(`#eddistributed_share`).val(distributedShare);
                $(`#edrem_share`).val(rem_share);
                $(`#edreinsurer-share`).val(share);
                $(`#edreinsurer-written-share`).val(written_lines);
                $(`#edreinsurer-orig-share`).val(share);
                $(`#edreinsurer-wht_rate`).val(parseFloat(data.wht_rate).toFixed(2)).trigger('change');
                $(`#edreinsurer-apply_fronting`).val(apply_fronting);
                $(`#edreinsurer-fronting_rate`).val(data.fronting_rate);
                $(`#edreinsurer-fronting_amt`).val(data.fronting_amt);
                $(`#edreinsurer-brokerage_comm_amt`).val(data.brokerage_comm_amt);
                $(`#edreinsurer`).val(reinsurer.customer_id);
                // $(`#edreinsurer_name`).val(reinsurer.customer_id).trigger('change');
                // $(`#edreinsurer_name`).attr('title', 'WAICA REINSURANCE COMPANY');
                $(`#edreinsurer-sum_insured`).val(numberWithCommas(sum_insured
                    .toFixed(2)));
                $(`#edreinsurer-premium`).val(premium.toFixed(2));
                $(`#edreinsurer-comm_rate`).val(numberWithCommas(comm_rate.toFixed(
                    2)));
                $(`#edreinsurer-comm_amt`).val(commission.toFixed(2));
            })

            $(`#edreinsurer-wht_rate`).trigger('change');

            $('#reinsurer-modal').on('keyup', 'input[name="comm_rate"]', function() {
                const counter = $(this).data('counter');
                const commRate = parseFloat($(`#reinsurer-modal #reinsurer-comm_rate-${counter}`).val()) ||
                    0;
                const premium = parseFloat($(`#reinsurer-modal #reinsurer-premium-${counter}`).val()) || 0;

                // Calculate values based on the entered share
                const commAmount = (commRate / 100) * premium;
                $(`#reinsurer-modal #reinsurer-comm_amt-${counter}`).val(numberWithCommas(commAmount
                    .toFixed(2)));

                calculateBrokerageCommRate()
            })

            $('#reinsurer-modal input[name="comm_rate"]').trigger('keyup');

            $('#edit-reinsurer-modal').on('keyup change',
                'input[name="comm_rate"], input[name="premium"], input[name="apply_fronting"]',
                function() {
                    // const counter = $(this).data('counter');
                    const reinsurercommRate = parseFloat(removeCommas($(
                        `#edit-reinsurer-modal #edreinsurer-comm_rate`).val())) || 0;
                    const premium = parseFloat(removeCommas($(`#edit-reinsurer-modal #edreinsurer-premium`)
                        .val())) || 0;
                    let whtRate = $(`#edreinsurer-wht_rate`).val();
                    const cedantCommRate = {!! $coverReg->cedant_comm_rate !!};
                    const brokerageCommRate = reinsurercommRate - cedantCommRate;
                    let applyFronting = $(`#edreinsurer-apply_fronting`).val();
                    let frontingRate = $(`#edreinsurer-fronting_rate`).val();
                    let frontingAmt = 0;
                    let whtAmt = 0;
                    // Calculate values based on the entered share
                    const reinsurercommAmount = (reinsurercommRate / 100) * premium;
                    const brokerageCommAmt = (brokerageCommRate / 100) * premium;
                    // if(whtRate > 0){
                    //     whtAmt = (whtRate / 100) * (premium - reinsurercommAmount);
                    // }
                    if (frontingRate > 0 && applyFronting == 'Y') {
                        frontingAmt = (frontingRate / 100) * (premium - reinsurercommAmount);
                    } else {
                        applyFronting = 'N'
                        frontingRate = 0;
                    }

                    // $(`#edit-reinsurer-modal #edreinsurer-premium`).val(numberWithCommas(premium.toFixed(2)));
                    $(`#edit-reinsurer-modal #edreinsurer-comm_amt`).val(numberWithCommas(reinsurercommAmount
                        .toFixed(2)));
                    $(`#edit-reinsurer-modal #edreinsurer-brokerage_comm_amt`).val(numberWithCommas(
                        brokerageCommAmt.toFixed(2)));
                    $(`#edit-reinsurer-modal #edreinsurer-apply_fronting`).val(applyFronting);
                    $(`#edit-reinsurer-modal #edreinsurer-fronting_rate`).val(frontingRate);
                    $(`#edit-reinsurer-modal #edreinsurer-fronting_amt`).val(numberWithCommas(frontingAmt
                        .toFixed(2)));
                })

            $('#edit-reinsurer-modal input[name="comm_rate"], #edit-reinsurer-modal input[name="premium"], #edit-reinsurer-modal input[name="apply_fronting"]')
                .trigger('keyup');

            $(document).on('click', '.remove-reinsurer', function() {
                const shareData = $(this).data('data');
                const reinsurer = $(this).data('reinsurer');
                swal.fire({
                    title: 'Remove Reinsurer',
                    text: `This action will remove the reinsurer ${reinsurer.name} from this cover and his share`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    const data = {
                        endorsement_no: "{!! $coverReg->endorsement_no !!}",
                        tran_no: shareData.tran_no,
                        reinsurer: shareData.partner_no,
                    }
                    if (result.isDismissed) {
                        return false;
                    }
                    // subit commit request
                    fetchWithCsrf("{!! route('cover.delete_reinsurance_data') !!}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(data),
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data)
                            if (data.status == 201) {
                                toastr.success("Action was successful", 'Successful')
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)

                            } else {
                                toastr.error("Failed to save details")
                            }
                        })
                        .catch(error => {
                            toastr.error("An internal error occured")
                        });
                });
            })

            $('#add-treaty-reinsurer').click(function(e) {
                var $lastSection = $(`#treaty-div .treaty-div-section`).last();

                const currCounter = parseInt($lastSection.attr('data-counter'))

                const currTreaty = $(`#reinsurer-treaty-${currCounter}`).val()
                if (currTreaty == null || currTreaty == '' || currTreaty == ' ') {
                    toastr.error(`Please Select Treaty`, 'Incomplete data')
                    return false
                }

                var $newSection = $lastSection.clone(); // Clone the last section
                var counter = currCounter + 1;

                $newSection.find('.select2-container').remove();
                $newSection.find('#data-select2-id').remove();

                $newSection.find('input:not(.share_offered)').val('');

                $newSection.attr('data-counter', counter);
                $newSection.attr('id', `treaty-div-section-${counter}`);

                // remove all reinsurer sections apart from the first one
                $newSection.find('.reinsurer-section:not(:first)').remove()

                // Update ids and counters
                // $newSection.find('data-counter').attr('data-counter', counter);
                $newSection.find('[id]').each(function() {
                    var id = $(this).attr('id');
                    // $(this).attr('id', id.replace(/-\d$/, '-' + counter));
                    // $(this).attr('id', id.replace(/-\d-\d$/,`-${counter}-${counter}`));
                    $(this).attr('id', id.replace(/(-\d)(-\d)?$/, function(match, p1, p2) {
                        return p2 ? `-${counter}-${counter}` : `-${counter}`;
                    }));
                    $(this).attr('data-counter', counter);
                    $(this).attr('data-treaty-counter', counter);
                });

                $newSection.find('.treaties').each(function() {
                    const newName = $(this).attr('name').replace(`[${currCounter}]`, `[${counter}]`)
                    $(this).attr('name', newName);
                });

                $newSection.find('.reinsurers').each(function() {
                    const newName = $(this).attr('name').replace(/(\[\d+\])/g, function(match,
                        capture, offset) {
                        if (offset === 6) {
                            return `[${counter}]`;
                        } else {
                            return `[${counter}]`;
                        }
                    });
                    $(this).attr('name', newName);
                });

                $lastSection.after($newSection);

                $('#treaty-div .form-select').select2({
                    dropdownParent: $('#reinsurer-modal')
                });
            });

            $(document).on('click', '.add-reinsurer', function(e) {
                const currTreatyCounter = $(this).data('counter');
                var $lastSection = $(
                    `#treaty-div #treaty-div-section-${currTreatyCounter} .reinsurer-section`).last();

                const prevCounter = parseInt($lastSection.attr('data-counter'))

                const currReinsurer = $(`#reinsurer-${currTreatyCounter}-${prevCounter}`).val()

                if (currReinsurer == null || currReinsurer == '' || currReinsurer == ' ') {
                    toastr.error(`Please Select Reinsurer`, 'Incomplete data')
                    return false
                }

                var $newSection = $lastSection.clone(); // Clone the last section
                var counter = prevCounter + 1;

                const clonedSelect = $newSection.find('.reinsurer')
                clonedSelect.select2('destroy');

                // Remove the Select2 classes and attributes manually
                clonedSelect.next('.select2-container').remove();

                // Also, remove any other Select2-related attributes or classes from the original select element
                clonedSelect.removeAttr('data-select2-id'); // Remove the data-select2-id attribute
                clonedSelect.removeClass('select2-hidden-accessible');

                $newSection.attr('data-counter', counter);
                $newSection.attr('id', `reinsurer-div-${currTreatyCounter}-${counter}`);

                $newSection.find('input').val('');

                // Update ids and counters
                $newSection.find('[id]').each(function() {
                    var id = $(this).attr('id');
                    $(this).attr('id', id.replace(/-\d$/, '-' + counter));
                    $(this).attr('data-counter', counter);
                });

                $newSection.find('.reinsurers').each(function() {
                    const newName = $(this).attr('name').replace(/(\[\d+\])/g, function(match,
                        capture, offset) {
                        if (offset === 6) {
                            return `[${currTreatyCounter}]`;
                        } else {
                            return `[${counter}]`;
                        }
                    });
                    $(this).attr('name', newName);
                });

                $lastSection.after($newSection);

                // filter reinsurers
                let selectedReinsurers = []
                const selectedTreaty = $(`#reinsurer-treaty-${currTreatyCounter}`).val()
                appendReinsurers(currTreatyCounter, selectedTreaty)
                // remove selected reinsurers if treaty is one
                // @if (in_array($coverReg->type_of_bus, ['TNP', 'TPR']))
                //     const treatyCount = parseInt('{!! $coverTreaties->count() !!}')

                //     if(treatyCount == 1)
                //     {
                //         appendReinsurers(counter)
                //     }
                // @endif


                $('#reinsurer-div .form-select').select2({
                    dropdownParent: $('#reinsurer-modal')
                });

            });

            let selectedReinsurers = []
            $(document).on('change', '.reinsurer-treaty', function(e) {
                const selectedTreaty = $(this).val();
                const id = $(this).attr('id');
                const counter = $(`#${id}`).data('counter');

                distributedShare = origDistributedShare = 0;
                coverpartners.forEach(partner => {
                    if (partner.treaty_code == selectedTreaty) {
                        distributedShare += parseFloat(partner.share)
                        origDistributedShare += parseFloat(partner.share)
                    }
                });

                let rem_share = share_offered - origDistributedShare

                $(`#distributed_share-${counter}`).val(origDistributedShare);
                $(`#rem_share-${counter}`).val(rem_share);

                appendReinsurers(counter, selectedTreaty)
            });

            $(document).on('change', '#sched-header', function() {
                const sched_title = $(this).find('option:selected').data('name');
                $('#title').val(sched_title);
            })

            $(document).on('change', '.apply_fronting', function() {
                const counter = $(this).data('counter')
                const option = $(this).val()
                // console.log('option', option, 'counter', counter);

                $(`#fronting_amt_div-${counter}`).hide();
                $(`#fronting_rate_div-${counter}`).hide();
                $(`#fronting_rate-${counter}`).val(null);
                $(`#fronting_amt-${counter}`).val(null);

                if (option == 'Y') {
                    $(`#fronting_amt_div-${counter}`).show();
                    $(`#fronting_rate_div-${counter}`).show();
                }
            })

            $(document).on('change', '.edapply_fronting', function() {
                // const counter = $(this).data('counter')
                const option = $(this).val()

                $(`#edfronting_amt_div`).hide();
                $(`#edfronting_rate_div`).hide();
                $(`#edfronting_rate`).val(null);
                $(`#edfronting_amt`).val(null);

                if (option == 'Y') {
                    $(`#edfronting_amt_div`).show();
                    $(`#edfronting_rate_div`).show();
                }
            })

            $(document).on('keyup', '#edreinsurer-fronting_rate', function() {
                const counter = $(this).data('counter')
                const frontingRate = $(this).val()
                const premium = parseFloat($("#edreinsurer-premium").val().replace(/,/g, '')) || 0;
                const reinsurerCommAmt = parseFloat($("#edreinsurer-comm_amt").val().replace(/,/g, '')) ||
                    0;
                const frontingAmt = ((frontingRate / 100) * (premium - reinsurerCommAmt)) || 0;
                $(`#edreinsurer-fronting_amt`).val(numberWithCommas(frontingAmt))
            })

            $(document).on('keyup', '.reinsurer-fronting_rate', function() {
                const counter = $(this).data('counter');
                const frontingRate = parseFloat($(this).val()) || 0;
                const premium = parseFloat(removeCommas($("#reinsurer-premium-" + counter).val())) || 0;
                const cedantPremium = parseFloat(removeCommas($("#reinsurer-cedant_premium-" + counter)
                    .val())) || 0;

                const reinsurerCommAmt = parseFloat(removeCommas($("#reinsurer-comm_amt-" + counter)
                    .val())) || 0;
                const frontingAmt = ((frontingRate / 100) * (premium - reinsurerCommAmt)) || 0;

                $(`#reinsurer-fronting_amt-${counter}`).val(numberWithCommas(frontingAmt.toFixed(2)));
            });


            function appendReinsurers(treatyCounter, treaty = null) {
                var $lastSection = $(`#treaty-div #treaty-div-section-${treatyCounter} .reinsurer-section`).last();
                const counter = $lastSection.data('counter')

                $(`#reinsurer-${treatyCounter}-${counter}`).empty();
                $(`#reinsurer-${treatyCounter}-${counter}`).append($('<option>').text('-- Select Reinsurer--').attr(
                    'value', ''));
                let selectedReinsurers = []

                $('#treaty-div .reinsurer').each(function() {
                    let reinsurer = $(this).val();
                    if (reinsurer == '') {
                        return false;
                    }
                    reinsurer = parseInt(reinsurer)
                    const riTreatyCounter = $(this).data('treaty-counter');
                    if (treaty !== null) {
                        const riTreaty = $(`#reinsurer-treaty-${riTreatyCounter}`).val();
                        if (treaty == riTreaty) {
                            selectedReinsurers.push(reinsurer)
                        }
                    } else {
                        selectedReinsurers.push(reinsurer)
                    }
                });

                REINSURERS.forEach(function(val) {
                    if (selectedReinsurers.indexOf(val.customer_id) == -1) {
                        $(`#reinsurer-${treatyCounter}-${counter}`).append($('<option>')
                            .text(val.name)
                            .attr('value', val.customer_id)
                            .attr('title', val.name)
                        )
                    }
                })

                $(`#reinsurer-${treatyCounter}-${counter}`).trigger('change:select2');
            }

            $('#reinsurer-div ').on('click', '.remove-reinsurer', function() {
                const counter = $(this).data('counter');
                $(`#reinsurer-div-${counter}`).remove()
            })

            $('#total-mdp-installments').change(function(e) {
                const totalInstallments = parseInt($(this).val());
                $('#mdp-installments-section').empty()
                const reinLayerStr = '@json($reinLayers)'
                if (reinLayerStr.length > 0) {
                    const reinLayers = JSON.parse(reinLayerStr)
                    const totalmdpAmount = parseFloat('{!! $mdpAmount !!}') || 0
                    const totalmdpInstAmt = (totalmdpAmount / totalInstallments).toFixed(2)

                    $('#mdp-installments-section').append(`<hr>`)
                    reinLayers.forEach(nonPropLayer => {
                        const layerMdpAmt = parseFloat(nonPropLayer.min_deposit) || 0
                        let InstallmentAmt = (layerMdpAmt / totalInstallments).toFixed(2)
                        InstallmentAmt = numberWithCommas(InstallmentAmt)

                        $('#mdp-installments-section').append(`

                            <div class="row installment-section">
                                <div class="col-md-4">
                                    <label for="layer_no">Layer no.</label>
                                    <input type="text" name="layer_no[]" id="layer_no" value="${nonPropLayer.layer_no}" readonly class="form-control" readonly required/>
                                </div>
                                <div class="col-md-4">
                                    <label for="min_deposit">Minimum Deposit</label>
                                    <input type="text" name="min_deposit[]" id="min_deposit" value="${numberWithCommas(layerMdpAmt)}" readonly class="form-control"readonly required/>
                                </div>
                                <div class="col-md-4">
                                    <label for="installment_amt">Installment Amount</label>
                                    <input type="text" name="installment_amt[]" id="installment_amt" value="${InstallmentAmt}" class="form-control amount"readonly required/>
                                </div>
                            </div>
                        `);
                    });
                    $('#mdp-installments-section').append(`<hr>`)

                    for (let i = 1; i <= totalInstallments; i++) {
                        $('#mdp-installments-section').append(`
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="">Installment</label>
                                    <input type="hidden" name="installment_no[]" value="${i}" readonly class="form-control" required/>
                                    <input type="text" value="installment No. ${i}" readonly class="form-control"/>
                                </div>
                                <div class="col-md-4">
                                    <label for="installment_date">Installment Due Date</label>
                                    <input type="date" name="installment_date[]" id="installment_date" class="form-control" required/>
                                </div>
                                <div class="col-md-4">
                                    <label for="installment_amt">Total Installment Amount</label>
                                    <input type="text" name="installment_amt[]" id="installment_amt" value="${numberWithCommas(totalmdpInstAmt)}" class="form-control amount"readonly required/>
                                </div>
                            </div>
                        `);
                    }
                }

            });

            $('#total-mdp-installments').trigger('change');

            //Start of Facultative installment
            $('#no_of_installments').change(function(e) {
                const totalInstallments = parseInt($(this).val());
                $('#fac-installments-section').empty()
                if (totalInstallments > 0) {
                    const totalFacAmount = 0; //amount
                    const totalFacInstAmt = (totalFacAmount / totalInstallments).toFixed(2)

                    for (let i = 1; i <= totalInstallments; i++) {
                        $('#fac-installments-section').append(`
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="">Installment</label>
                                    <input type="hidden" name="installment_no[]" value="${i}" readonly class="form-control" required/>
                                    <input type="text" value="installment No. ${i}" readonly class="form-control"/>
                                </div>
                                <div class="col-md-3">
                                    <label for="installment_date">Installment Due Date</label>
                                    <input type="date" name="installment_date[]" id="installment_date" class="form-control" required/>
                                </div>
                                <div class="col-md-3">
                                    <label for="installment_amt">Total Installment Amount</label>
                                    <input type="text" name="installment_amt[]" id="installment_amt" value="${numberWithCommas(totalFacInstAmt)}" class="form-control amount"  onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)"  required/>
                                </div>
                            </div>
                        `);
                    }
                }
            });
            $('#no_of_installments').trigger('change');

            $(document).on('click', '#schedule-details', function() {
                $(`#schedulesForm`)[0].reset();
                $(`#schedulesForm [name="_method"]`).val('POST');
            });

            $('#schedule_description').on('paste', function(e) {
                const clipboardData = (e.originalEvent || e).clipboardData;
                const pastedText = clipboardData.getData('text/html');
                if (pastedText) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(pastedText, 'text/html');
                    const table = $(doc).find('table');
                    const currentText = $(this).val();

                    if (table.length) {
                        $("hidden_schedule_description").val(table);
                    } else {
                        $("hidden_schedule_description").val(currentText + pastedText);
                    }
                }
            });

            $(document).on('click', '.edit-schedule', function() {
                $(`#schedulesForm #title`).val('');
                $(`#schedulesForm #id`).val('');
                $(`#schedulesForm #schedule_id`).val('');
                $(`#schedulesForm #schedule_position`).val('');
                $(`#schedulesForm #schedule_value`).val('');
                $(`#schedulesForm #sched-header`).val('')
                $("#schedule_description").html('');

                const id = $(this).data('id');
                const schedule_id = $(this).data('schedule_id');
                const title = $(this).data('title');
                const header = $(this).data('header');
                const details = $(this).data('details');
                const sum_insured = $(this).data('sum_insured');

                $(`#schedulesForm #title`).val(title);
                $(`#schedulesForm #id`).val(id);
                $(`#schedulesForm #schedule_id`).val(schedule_id);
                $(`#schedulesForm #schedule_position`).val(schedule_id);
                $(`#schedulesForm #schedule_value`).val(numberWithCommas(sum_insured));
                $(`#schedulesForm [name="_method"]`).val('PUT');
                $("#schedulesForm #schedule_description").html(details);
                $(`#schedulesForm #sched-header`).val(schedule_id).trigger('change');
            });

            $(document).on('click', '.remove-schedule', function() {
                const dataId = $(this).data('id');
                const dataName = $(this).data('name');
                swal.fire({
                    title: 'Remove Schedule Item',
                    text: `This action will remove the Item ${dataName} from this cover `,
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    const data = {
                        cover_no: "{!! $coverReg->cover_no !!}",
                        endorsement_no: "{!! $coverReg->endorsement_no !!}",
                        id: dataId,
                    }
                    if (result.isDismissed) {
                        return false;
                    }
                    fetchWithCsrf("{!! route('cover.delete_schedule') !!}", {
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
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)

                            } else {
                                toastr.error("Failed to save details")
                            }
                        })
                        .catch(error => {
                            toastr.error("An internal error occured")
                        });
                });
            })

            $(document).on('click', '#attachments', function() {
                $(`#attachmentsForm`)[0].reset();
                $(`#attachmentsForm [name="_method"]`).val('POST');
            });

            $(document).on('click', '.edit-attachment', function() {
                const title = $(this).data('title');
                const id = $(this).data('id');

                $(`#attachmentsForm #attachments_id`).val(id);
                $(`#attachmentsForm #title`).val(title).trigger('change');
                $(`#attachmentsForm [name="_method"]`).val('PUT');
                // // $(`#attachmentsForm #description`).val(data.description);
            });

            $(document).on('click', '.view-attachment', function() {
                const base64Data = $(this).data('base64');
                const mimeType = $(this).data('mime');
                if (mimeType.startsWith('image/')) {
                    element =
                        `<img src="data:${mimeType};base64,${base64Data}" width="100%"  alt="Document Image"/>`;
                } else if (mimeType === 'application/pdf') {
                    element =
                        `<iframe src="data:${mimeType};base64,${base64Data}" width="100%" height="800"></iframe>`;
                } else if (mimeType.startsWith('text/')) {
                    element =
                        `<iframe src="data:${mimeType};base64,${base64Data}" width="100%" height="800"></iframe>`;
                } else {
                    element =
                        `<a href="data:${mimeType};base64,${base64Data}" download="document" style="color:blue;text-decoration:underline;width: 100%;">Download Document</a>`;
                }
                $('#attachment-document-modal #preview-container').html(element);
            });

            $(document).on('click', '.remove-attachment', function() {
                const title = $(this).data('title');
                const id = $(this).data('id');
                swal.fire({
                    title: 'Remove Attachment ',
                    text: `This action will remove ${title} from this cover`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    const data = {
                        cover_no: "{!! $coverReg->cover_no !!}",
                        endorsement_no: "{!! $coverReg->endorsement_no !!}",
                        id: id,
                    }
                    if (result.isDismissed) {
                        return false;
                    }
                    // subit commit request
                    fetchWithCsrf("{!! route('cover.delete_attachment') !!}", {
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
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)

                            } else {
                                toastr.error("Failed to remove attachment")
                            }
                        })
                        .catch(error => {
                            toastr.error("An internal error occured")
                        });
                });
            })

            $(document).on('click', '.remove-clause', function() {
                const title = $(this).data('title');
                const id = $(this).data('id');
                swal.fire({
                    title: 'Remove Clause ',
                    text: `This action will remove ${title} from this cover`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Remove',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    const data = {
                        cover_no: "{!! $coverReg->cover_no !!}",
                        endorsement_no: "{!! $coverReg->endorsement_no !!}",
                        clause_id: id,
                    }
                    if (result.isDismissed) {
                        return false;
                    }
                    // subit commit request
                    fetchWithCsrf("{!! route('cover.delete_clause') !!}", {
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
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)

                            } else {
                                toastr.error("Failed to remove clause")
                            }
                        })
                        .catch(error => {
                            toastr.error("An internal error occured")
                        });
                });
            })

            $(document).on('click', '#verify_details', function(e) {
                e.preventDefault();
                const data = {
                    'endorsement_no': '{!! $coverReg->endorsement_no !!}'
                }

                fetchWithCsrf("{!! route('cover.pre_cover_verification') !!}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            for (const msg of data) {
                                toastr.error(msg);
                            }
                        } else {
                            $('#verify-modal').modal('show')
                        }
                    })
                    .catch(error => {
                        toastr.error("Failed to load pre-verification checks")
                    })
            });

            $(document).on('click', '#generate_slip', function(e) {
                e.preventDefault();
                const data = {
                    'endorsement_no': '{!! $coverReg->endorsement_no !!}'
                }

                fetchWithCsrf("{!! route('docs.pre_cover_slip_verification') !!}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data),
                    })
                    .then(response => response.json())
                    .then(r => {
                        if (r.data.length > 0) {
                            for (const msg of r.data) {
                                toastr.error(msg);
                            }
                        } else {
                            var slipUrl =
                                "{!! route('docs.coverslip', ['endorsement_no' => $coverReg->endorsement_no, 'pre_debit' => 'Y']) !!}";
                            // $('#docIframe').attr('src', slipUrl);
                            // $('#generateSlipModal').modal('show');
                            window.open(slipUrl, '_blank');
                        }
                    })
                    .catch(error => {
                        toastr.error("Failed to load pre-verification checks")
                    })
            });

            $("#reinsurerForm").validate({
                errorClass: "errorClass",
                rules: {
                    'treaty[*][reinsurers][*][reinsurer]': {
                        required: true
                    },
                    'treaty[*][reinsurers][*][share]': {
                        required: true
                    },
                    endorsement_no: {
                        required: true
                    },
                    'treaty[*][reinsurers][*][comm_rate]': {
                        required: true
                    },
                    'treaty[*][reinsurers][*][comm_amt]': {
                        required: true
                    },
                    'treaty[*][reinsurers][*][premium]': {
                        required: true
                    },
                    'treaty[*][reinsurers][*][sum_insured]': {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    if ($("#reinsurerForm").valid()) {
                        $('#partner-save-btn')
                            .prop('disabled', true)
                            .html(`<span class="me-2">Saving...</span><div class="loading"></div>`);
                        if ($("select#reins_pay_method option:selected").attr(
                                'pay_method_desc') === 'I') {
                            if ($("#reinsurer_plan_section").is(":empty")) {
                                toastr.error("Please click `Add Installment`");
                                $('#partner-save-btn').prop('disabled', false).text('Save')
                                return false;
                            }
                            $('#reinsurer_plan_section').find('.reinsurer-instalament-row')
                                .each(function(index) {
                                    const idx = index + 1
                                    const noInput = $(
                                        `.reinsurer-instalament-row input#instl_no_${idx}`);
                                    const dateInput = $(
                                        `.reinsurer-instalament-row input#instl_date_${idx}`);
                                    const amountInput = $(
                                        `.reinsurer-instalament-row input#instl_amnt_${idx}`);

                                    if (!dateInput.val()) {
                                        dateInput.attr('required', 'required');
                                        $('#partner-save-btn').prop('disabled', false).text('Save')
                                        return false;

                                    } else {
                                        dateInput.removeAttr('required');
                                    }

                                    if (!noInput.val()) {
                                        noInput.attr('required', 'required');
                                        $('#partner-save-btn').prop('disabled', false).text('Save')

                                        return false;

                                    } else {
                                        noInput.removeAttr('required');
                                    }

                                    if (!amountInput.val()) {
                                        amountInput.attr('required', 'required');
                                        $('#partner-save-btn').prop('disabled', false).text('Save')
                                        return false;

                                    } else {
                                        amountInput.removeAttr('required');
                                    }
                                });

                            var totalInstallment = 0;
                            $("#reinsurer_plan_section input[name='installment_amt[]']").each(
                                function() {
                                    const value = parseFloat($(this).val().replace(/,/g, ''));
                                    if (!isNaN(value)) {
                                        totalInstallment += value;
                                    }
                                });

                            if (!areDecimalsEqual(totalInstallment, installmentTotalAmount)) {
                                toastr.error(
                                    "The total installment amount does not match the FAC amount."
                                );
                                $('#partner-save-btn').prop('disabled', false).text('Save')
                                return false;
                            }
                        }
                        fetch("{!! route('cover.save_reinsurance_data') !!}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams(formData),
                            })
                            .then(response => response.json())
                            .then(data => {
                                $('#partner-save-btn').prop('disabled', false).text('Save')
                                if (data.status == 201) {
                                    toastr.success(data.message)
                                    form.reset()
                                    reinsurersTable.ajax.reload();
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                } else if (data.status == 422) {
                                    // Validation errors
                                    showServerSideValidationErrors(data.errors)
                                } else {
                                    toastr.error("Failed to save details")
                                }
                            })
                            .catch(error => {
                                toastr.error("Failed to save details")
                                $('#partner-save-btn').prop('disabled', false).text('Save')

                            })
                    } else {
                        toastr.error("Please correct the errors before submitting.");
                    }
                }
            })

            $("#EditReinsurerForm").validate({
                errorClass: "errorClass",
                rules: {
                    'treaty[*][reinsurers][*][reinsurer]': {
                        required: true
                    },
                    'treaty[*][reinsurers][*][share]': {
                        required: true
                    },
                    endorsement_no: {
                        required: true
                    },
                    'treaty[*][reinsurers][*][comm_rate]': {
                        required: true
                    },
                    'treaty[*][reinsurers][*][comm_amt]': {
                        required: true
                    },
                    'treaty[*][reinsurers][*][premium]': {
                        required: true
                    },
                    'treaty[*][reinsurers][*][sum_insured]': {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    if ($("#EditReinsurerForm").valid()) {
                        $('#partner-edit-btn')
                            .prop('disabled', true)
                            .html(`<span class="me-2">Saving...</span><div class="loading"></div>`);
                        if ($("select#reins_pay_method option:selected").attr(
                                'pay_method_desc') === 'I') {
                            if ($("#reinsurer_plan_section").is(":empty")) {
                                toastr.error("Please click `Add Installment`");
                                $('#partner-edit-btn').prop('disabled', false).text('Save')
                                return false;
                            }
                            $('#reinsurer_plan_section').find('.reinsurer-instalament-row')
                                .each(function(index) {
                                    const idx = index + 1
                                    const noInput = $(
                                        `.reinsurer-instalament-row input#instl_no_${idx}`);
                                    const dateInput = $(
                                        `.reinsurer-instalament-row input#instl_date_${idx}`);
                                    const amountInput = $(
                                        `.reinsurer-instalament-row input#instl_amnt_${idx}`);

                                    if (!dateInput.val()) {
                                        dateInput.attr('required', 'required');
                                        $('#partner-edit-btn').prop('disabled', false).text('Save')
                                        return false;

                                    } else {
                                        dateInput.removeAttr('required');
                                    }

                                    if (!noInput.val()) {
                                        noInput.attr('required', 'required');
                                        $('#partner-edit-btn').prop('disabled', false).text('Save')

                                        return false;

                                    } else {
                                        noInput.removeAttr('required');
                                    }

                                    if (!amountInput.val()) {
                                        amountInput.attr('required', 'required');
                                        $('#partner-edit-btn').prop('disabled', false).text('Save')
                                        return false;

                                    } else {
                                        amountInput.removeAttr('required');
                                    }
                                });

                            var totalInstallment = 0;
                            $("#reinsurer_plan_section input[name='installment_amt[]']").each(
                                function() {
                                    const value = parseFloat($(this).val().replace(/,/g, ''));
                                    if (!isNaN(value)) {
                                        totalInstallment += value;
                                    }
                                });

                            if (!areDecimalsEqual(totalInstallment, installmentTotalAmount)) {
                                toastr.error(
                                    "The total installment amount does not match the FAC amount."
                                );
                                $('#partner-edit-btn').prop('disabled', false).text('Save')
                                return false;
                            }
                        }
                        fetch("{!! route('cover.edit_reinsurance_data') !!}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams(formData),
                            })
                            .then(response => response.json())
                            .then(data => {
                                $('#partner-edit-btn').prop('disabled', false).text('Save')
                                if (data.status == 201) {
                                    toastr.success(data.message)
                                    form.reset()
                                    reinsurersTable.ajax.reload();
                                    setTimeout(() => {
                                        location.reload();
                                    }, 3000);
                                } else if (data.status == 422) {
                                    // Validation errors
                                    showServerSideValidationErrors(data.errors)
                                } else {
                                    toastr.error("Failed to save details")
                                }
                            })
                            .catch(error => {
                                toastr.error("Failed to save details")
                                $('#partner-edit-btn').prop('disabled', false).text('Save')

                            })
                    } else {
                        toastr.error("Please correct the errors before submitting.");
                    }
                }
            })

            // tinymce.init({
            //     selector: 'textarea#schedule-description',
            //     setup: function(editor) {
            //         editor.on('change', function(e) {
            //             editor.save();
            //         });
            //     },
            //     plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars accordion',
            //     editimage_cors_hosts: ['picsum.photos'],
            //     menubar: 'edit view insert format table',
            //     toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",
            //     autosave_ask_before_unload: true,
            //     autosave_interval: '30s',
            //     autosave_prefix: '{path}{query}-{id}-',
            //     autosave_restore_when_empty: false,
            //     autosave_retention: '2m',
            //     image_advtab: false,
            //     importcss_append: true,
            //     automatic_uploads: false,
            //     branding: false,
            //     file_picker_types: "image",
            //     file_picker_callback: function(cb, value, meta) {
            //         var input = document.createElement("input");
            //         input.setAttribute("type", "file");
            //         input.setAttribute("accept", "image/*");
            //         input.onchange = function() {
            //             var file = this.files[0];
            //             var reader = new FileReader();
            //             reader.onload = function() {
            //                 var id = "blobid" + new Date().getTime();
            //                 var blobCache = tinymce.activeEditor.editorUpload.blobCache;
            //                 var base64 = reader.result.split(",")[1];
            //                 var blobInfo = blobCache.create(id, file, base64);
            //                 blobCache.add(blobInfo);
            //                 cb(blobInfo.blobUri(), {
            //                     title: file.name
            //                 });
            //             };
            //             reader.readAsDataURL(file);
            //         };
            //         input.click();
            //     },
            //     height: 550,
            //     image_caption: true,
            //     quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            //     noneditable_class: 'mceNonEditable',
            //     toolbar_mode: 'sliding',
            //     contextmenu: 'link image table',
            //     skin: 'oxide',
            //     content_css: 'default',
            //     content_style: 'body { font-family:Aptos,Arial,sans-serif; font-size:15px }'
            // });

            $("#schedulesForm").validate({
                errorClass: "errorClass",
                rules: {
                    title: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    // $('#schedule-save-btn')
                    //     .prop('disabled', true)
                    //     .html(`<span class="me-2">Submiting...</span><div class="loading"></div>`);
                    var url = ''
                    let method = $('#schedulesForm [name="_method"]').val()
                    if (method == 'POST') {
                        url = "{!! route('cover.add_schedule') !!}"
                    } else if (method == 'PUT') {
                        url = "{!! route('cover.amend_schedule') !!}"
                    }

                    var formData = new FormData(form);
                    formData.append('details', $('#schedule_description').html());
                    fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams(formData),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == 201) {
                                toastr.success("Schedule Successfully saved")
                                form.reset()
                                window.location.reload();
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)
                                $('#schedule-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to save details")
                            }
                            $('#schedule-save-btn').prop('disabled', false).text('Submit')
                        })
                        .completed(() => {
                            $("#schedulesForm")[0].reset()
                        })
                        .catch(error => {
                            $('#schedule-save-btn').prop('disabled', false).text('Submit')
                        });
                }
            })

            // insurance class schedule
            $("#insuranceClassForm").validate({
                errorClass: "errorClass",
                rules: {
                    reinclass: {
                        required: true
                    },
                    class: {
                        required: true
                    },
                    cover_no: {
                        required: true
                    },
                    endorsement_no: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#ins-class-save-btn').prop('disabled', true).text('Saving...')
                    // Make a fetch request
                    let url = $(form).attr('action');
                    let method = $(form).attr('method');
                    let formData = new FormData(form);

                    fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                            },
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Handle success
                            $('#insuranceClassForm').modal('hide');
                            if (data.status == 201) {
                                toastr.success("Class(es) saved Successfully")
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)
                                $('#ins-class-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to save class(es)")
                                $('#ins-class-save-btn').prop('disabled', false).text('Submit')
                            }
                        })
                        .catch(error => {
                            // Handle error
                            console.error(error);
                            toastr.error("Failed to save class(es)")
                            $('#ins-class-save-btn').prop('disabled', false).text('Submit')
                        });
                }
            })

            // attachments form
            $("#attachmentsForm").validate({
                errorClass: "errorClass",
                rules: {
                    title: {
                        required: true
                    },
                    file: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#attachments-save-btn')
                        .prop('disabled', true)
                        .html(`<span class="me-2">Submiting...</span><div class="loading"></div>`);
                    let url = ''
                    let HttpMethod = $('#attachmentsForm [name="_method"]').val()
                    if (HttpMethod == 'POST') {
                        url = "{!! route('cover.save_attachment') !!}"
                    } else if (HttpMethod == 'PUT') {
                        url = "{!! route('cover.amend_attachment') !!}"
                    }
                    var formData = new FormData(form);
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                            },
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            $('#attachments-save-btn').prop('disabled', false).text('Submit')
                            $('#attachmentsModal').modal('hide');
                            if (data.status == 201) {
                                toastr.success("Attachment saved Successfully")
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)

                            } else {
                                toastr.error("Failed to save attachment")
                            }
                        })
                        .catch(error => {
                            toastr.error("Failed to save attachment")
                            $('#attachments-save-btn').prop('disabled', false).text('Submit')
                        });
                }
            })

            // verify schedule
            $("#verifyForm").validate({
                errorClass: "errorClass",
                rules: {
                    process: {
                        required: true
                    },
                    process_action: {
                        required: true
                    },
                    comment: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#verify-save-btn').prop('disabled', true).html(
                        `<span class="me-2">Submitting...</span><div class="loading"></div>`);

                    let url = $(form).attr('action');
                    let method = $(form).attr('method');
                    let formData = new FormData(form);

                    fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                            },
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data)

                            if (data.status == 201) {
                                toastr.success("Verification request Successfully sent")
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)
                                $('#verify-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to send verification request")
                                $('#verify-save-btn').prop('disabled', false).text('Submit')
                            }
                        })
                        .catch(error => {
                            console.log(error)
                            toastr.error("Failed to send verification request")
                        })
                        .finally(() => {
                            $('#verify-save-btn').prop('disabled', false).text('Submit')
                        })
                }
            })

            $("#clausesForm").validate({
                errorClass: "errorClass",
                rules: {
                    clause_id: {
                        required: true
                    },
                    file: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#clauses-save-btn').prop('disabled', true).text('Saving...')
                    let url = ''
                    let HttpMethod = $('#clausesForm [name="_method"]').val()
                    if (HttpMethod == 'POST') {
                        url = "{!! route('cover.save_clauses') !!}"
                    } else if (HttpMethod == 'PUT') {
                        url = "{!! route('cover.amend_clauses') !!}"
                    }

                    let formData = new FormData(form);
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                            },
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            $('#clausesModal').modal('hide');
                            if (data.status == 201) {
                                toastr.success("Clauses saved Successfully")
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)
                                $('#clauses-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to save clauses")
                                $('#clauses-save-btn').prop('disabled', false).text('Submit')
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error("Failed to save clauses")
                            $('#clauses-save-btn').prop('disabled', false).text('Submit')
                        });
                }
            })

            $("#debitForm").validate({
                errorClass: "errorClass",
                rules: {
                    endorsement_no: {
                        required: true
                    },
                    installment: {
                        required: true,
                        min: 1
                    },
                    amount: {
                        required: true,
                        normalizer: function(value) {
                            return removeCommas(value)
                        },
                        max: {!! $installmentAmount !!},
                        // min: 0
                    },
                },
                submitHandler: function(form) {
                    $('#debit-save-btn').prop('disabled', true).text('Generating...')
                    // Make a fetch request
                    let url = $(form).attr('action');
                    let method = $(form).attr('method');
                    let formData = new FormData(form);
                    fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                            },
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Handle success
                            if (data.status == 201) {
                                toastr.success("Verification request Successfully sent")

                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)
                                $('#debit-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to send verification request")
                                $('#debit-save-btn').prop('disabled', false).text('Generate')
                            }

                        })
                        .catch(error => {
                            toastr.error("Failed to send verification request")
                            $('#debit-save-btn').prop('disabled', false).text('Generate')
                        });
                }
            })

            $("#mdpInstallmentForm").validate({
                errorClass: "errorClass",
                rules: {
                    endorsement_no: {
                        required: true
                    },
                    'total-installments': {
                        required: true,
                        min: 1
                    },
                    'installment_no[]': {
                        required: true,
                        min: 1
                    },
                    'installment_date[]': {
                        required: true,
                    },
                    'installment_amt[]': {
                        required: true,
                        normalizer: function(value) {
                            return removeCommas(value)
                        },
                        max: {!! $mdpAmount !!},
                        min: 0
                    },
                },
                submitHandler: function(form) {
                    $('#mdp-inst-save-btn').prop('disabled', true).text('Generating...')
                    // Make a fetch request
                    let url = $(form).attr('action');
                    let method = $(form).attr('method');
                    let formData = new FormData(form);

                    fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                            },
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Handle success
                            if (data.status == 201) {
                                toastr.success("Installments Successfully saved")

                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)
                                $('#mdp-inst-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to save installments")
                                $('#mdp-inst-save-btn').prop('disabled', false).text('Generate')
                            }

                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error:', error);
                            toastr.error("Failed to send save installments")
                            $('#mdp-inst-save-btn').prop('disabled', false).text('Generate')
                        });
                }
            })

            $(document).on("keyup", ".reinsurer-premium", function() {
                var counter = $(this).data("counter");
                computeCommissionAmt(counter);
            });

            $(".closeScheduleForm").on("click", function(e) {
                e.preventDefault();
                $("#schedulesForm")[0].reset()
            });

            $(document).on("keyup change", ".reinsurer-comm-rate", function() {
                var counter = $(this).data("counter");
                computeCommissionAmt(counter);
            });

            $(document).on("keyup", ".reinsurer-comm-amt", function() {
                var counter = $(this).data("counter");
                computeCommissionRate(counter);
            });

            $("#facInstallmentForm").validate({
                errorClass: "errorClass",
                rules: {
                    endorsement_no: {
                        required: true
                    },
                    'total-installments': {
                        required: true,
                        min: 1
                    },
                    'installment_no[]': {
                        required: true,
                        min: 1
                    },
                    'installment_date[]': {
                        required: true,
                    },
                    'installment_amt[]': {
                        required: true,
                        normalizer: function(value) {
                            return removeCommas(value);
                        },
                        min: 0
                    },
                },
                submitHandler: function(form) {
                    $('#fac-inst-save-btn').prop('disabled', true).text('Generating...')
                    // Calculate the total of installment amounts
                    let totalInstallmentAmount = null;
                    $('.installment-section input[name="installment_amt[]"]').each(function() {
                        let installmentAmt = parseFloat($(this).val().replace(/,/g, '')) || 0;
                        totalInstallmentAmount += installmentAmt;
                    });

                    // Get the totalFacAmount
                    const totalFacAmount = {!! $installmentAmount !!};
                    // Check if totalInstallmentAmount matches totalFacAmount
                    if (!areDecimalsEqual(totalInstallmentAmount, totalFacAmount)) {
                        toastr.error("The total installment amount does not match the FAC amount.");
                        $('#fac-inst-save-btn').prop('disabled', false).text('Submit');
                        return false;
                    }
                    // Make a fetch request
                    let url = $(form).attr('action');
                    let method = $(form).attr('method');
                    let formData = new FormData(form);

                    fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                            },
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Handle success
                            if (data.status == 201) {
                                toastr.success("Installments Successfully saved")

                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)
                                $('#fac-inst-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to save installments")
                                $('#fac-inst-save-btn').prop('disabled', false).text('Generate')
                            }

                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error:', error);
                            toastr.error("Failed to send save installments")
                            $('#fac-inst-save-btn').prop('disabled', false).text('Generate')
                        });
                }
            })

            const reinsurersTable = $('#reinsurers-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.reinsurers_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $coverReg->endorsement_no !!}";
                    }
                },
                columns: [{
                        data: 'tran_no',
                        searchable: true,
                        className: 'highlight-index',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'partner_name',
                        searchable: true,
                        className: 'highlight-view-point'
                    },
                    {
                        data: 'share',
                        searchable: true,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    @switch($coverReg->type_of_bus)
                        @case('FPR')
                        @case('FNP') {
                                data: 'sum_insured',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'premium',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'comm_rate',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'commission',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'brokerage_comm_amt',
                                searchable: false,
                                className: "highlight-2view-point",
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'wht_amt',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'fronting_amt',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            },
                            @break

                        @case('TPR')
                        @if (!in_array($coverReg->transaction_type, ['NEW', 'REN']))
                            // { data: 'sum_insured' , searchable: false,render: $.fn.dataTable.render.number(',', '.', 2, '') },
                            {
                                data: 'premium',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'commission',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'claim_amt',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'prem_tax',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'ri_tax',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            },
                        @endif
                        @break

                        @case('TNP')
                        @if (!in_array($coverReg->transaction_type, ['NEW', 'REN']))
                            // { data: 'sum_insured' , searchable: false,render: $.fn.dataTable.render.number(',', '.', 2, '') },
                            {
                                data: 'total_mdp_amt',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            }, {
                                data: 'mdp_amt',
                                searchable: false,
                                render: $.fn.dataTable.render.number(',', '.', 2, '')
                            },
                        @endif
                        @break
                    @endswitch {
                        data: 'action',
                        searchable: false,
                        sortable: false,
                        className: 'highlight-view-more2'
                    },
                ],
                paging: false,
                drawCallback: function(settings) {
                    $('#reinsurers-table tfoot').empty();
                    const api = this.api();
                    const businessType = "{!! $coverReg->type_of_bus !!}";
                    const transactionType = "{!! $coverReg->transaction_type !!}";

                    let columnsToSum = [2];
                    if (businessType === 'FPR' || businessType === 'FNP') {
                        // Sum insured, premium, commission, brokerage_comm_amt, wht_amt, fronting_amt
                        columnsToSum = columnsToSum.concat([3, 4, 6, 7, 8, 9]);
                    } else if (businessType === 'TPR' && !['NEW', 'REN'].includes(transactionType)) {
                        // Premium, commission, claim_amt, prem_tax, ri_tax
                        columnsToSum = columnsToSum.concat([3, 4, 5, 6, 7]);
                    } else if (businessType === 'TNP' && !['NEW', 'REN'].includes(transactionType)) {
                        // total_mdp_amt, mdp_amt
                        columnsToSum = columnsToSum.concat([3, 4]);
                    }

                    let footerRow = '<tr>';
                    footerRow +=
                        '<td colspan="2" style="text-align:right !important; font-weight:bold; color: #000; padding: 6px 8px; font-size: 13px;">Totals:</td>';
                    // Calculate the sum for each column and add to footer
                    const columns = api.columns().nodes().length;
                    for (let i = 2; i < columns - 1; i++) {
                        if (columnsToSum.includes(i)) {
                            const sum = api
                                .column(i, {
                                    search: 'applied'
                                })
                                .data()
                                .reduce(function(a, b) {
                                    // Convert string with commas to float
                                    const aFloat = parseFloat(a.toString().replace(/,/g, '')) || 0;
                                    const bFloat = parseFloat(b.toString().replace(/,/g, '')) || 0;
                                    return aFloat + bFloat;
                                }, 0);

                            const formattedSum = $.fn.dataTable.render.number(',', '.', 2, '').display(
                                sum);

                            footerRow +=
                                '<td style="font-weight:bold; padding: 6px 8px; color: #000;">' +
                                formattedSum + '</td>';
                        } else {
                            footerRow += '<td></td>';
                        }
                    }
                    footerRow += '<td></td>';
                    footerRow += '</tr>';

                    if (!$('#reinsurers-table tfoot').length) {
                        $('#reinsurers-table').append('<tfoot></tfoot>');
                    }
                    $('#reinsurers-table tfoot').html(footerRow);

                    $('#reinsurers-table tfoot tr').css({
                        'background-color': '#f5f5f5',
                        'border-top': '2px solid #ddd'
                    });
                }
            });

            const schedulesTable = $('#schedules-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.schedules_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $coverReg->endorsement_no !!}";
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'highlight-idx',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                    }, {
                        data: 'title',
                        searchable: true,
                        class: 'highlight-view-point'
                    },
                    {
                        data: 'details',
                        searchable: true,
                        className: "highlight-description clamp-text",
                    }, {
                        data: 'schedule_position',
                        searchable: false,
                    },
                    {
                        data: 'action',
                        class: 'highlight-action',
                        searchable: false,
                        sortable: false,
                    },
                ]
            });

            //installments schedule
            const installmentsTable = $('#installments-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.installments_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $coverReg->endorsement_no !!}";
                    }
                },
                columns: [{
                        data: 'installment_no',
                        searchable: true
                    },
                    {
                        data: 'installment_date',
                        searchable: true
                    },
                    {
                        data: 'installment_amt',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    {
                        data: 'action',
                        searchable: false
                    },
                ]
            });

            //attachments schedule
            const attachmentsTable = $('#attachments-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.attachments_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $coverReg->endorsement_no !!}";
                    }
                },
                columns: [{
                        data: 'id',
                        searchable: true
                    },
                    {
                        data: 'title',
                        searchable: true
                    },
                    // { data: 'description' , searchable: true },
                    {
                        data: 'action',
                        searchable: false
                    },
                ]
            });

            //clauses schedule
            const clausesTable = $('#clauses-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.clauses_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $coverReg->endorsement_no !!}";
                    }
                },
                columns: [{
                        data: 'clause_id',
                        className: 'highlight-idx',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                    },
                    {
                        data: 'clause_title',
                        searchable: true
                    },
                    {
                        data: 'clause_wording',
                        searchable: true,
                        className: "highlight-description clamp-text",
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false
                    },
                ]
            });

            //classes schedule
            const classesTable = $('#insclass-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.classes_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $coverReg->endorsement_no !!}";
                    }
                },
                columns: [{
                        data: 'id',
                        searchable: true
                    },
                    {
                        data: 'reinclass_name',
                        searchable: true
                    },
                    {
                        data: 'class',
                        searchable: true
                    },
                    {
                        data: 'class_name',
                        searchable: true
                    },
                    {
                        data: 'action',
                        searchable: false
                    },
                ]
            });

            const approvalsTable = $('#approvals-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.approvals_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $coverReg->endorsement_no !!}";
                    }
                },
                columns: [{
                        data: 'id',
                        searchable: true
                    },
                    {
                        data: 'approver',
                        searchable: true
                    },
                    {
                        data: 'comment',
                        searchable: true
                    },
                    {
                        data: 'approver_comment',
                        searchable: true
                    },
                    {
                        data: 'status',
                        searchable: false
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false
                    },
                ]
            });

            const debitsTable = $('#debits-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.debits_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $coverReg->endorsement_no !!}";
                    }
                },
                columns: [{
                        data: 'id',
                        searchable: false,
                        className: 'highlight-idx',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'cedant',
                        searchable: true,
                        className: 'highlight-2view-point'
                    },
                    {
                        data: 'dr_no',
                        searchable: true
                    },
                    {
                        data: 'installment',
                        searchable: true,
                    },
                    {
                        data: 'share',
                        searchable: true
                    },
                    {
                        data: 'sum_insured',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    {
                        data: 'premium',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    // {
                    //     data: 'commission',
                    //     searchable: false,
                    //     render: $.fn.dataTable.render.number(',', '.', 2, '')
                    // },
                    {
                        data: 'gross',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    {
                        data: 'net_amt',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false,
                        className: 'highlight-view-more'
                    },
                ],
                paging: false,
                drawCallback: function(settings) {
                    $('#debits-table tfoot').empty();
                    const api = this.api();
                    const columnsToSum = [4, 5, 6, 7, 8];

                    let footerRow = '<tr>';
                    footerRow +=
                        '<td colspan="4" style="text-align:right !important; font-weight:bold; color: #000; padding: 6px 8px; font-size: 13px;">Totals:</td>';

                    const columns = api.columns().nodes().length;
                    for (let i = 4; i < columns - 1; i++) {
                        if (columnsToSum.includes(i)) {
                            const sum = api
                                .column(i, {
                                    search: 'applied'
                                })
                                .data()
                                .reduce(function(a, b) {
                                    const aFloat = parseFloat(a.toString().replace(/,/g, '')) || 0;
                                    const bFloat = parseFloat(b.toString().replace(/,/g, '')) || 0;
                                    return aFloat + bFloat;
                                }, 0);

                            const formattedSum = $.fn.dataTable.render.number(',', '.', 2, '').display(
                                sum);

                            footerRow +=
                                '<td style="font-weight:bold; padding: 6px 8px; color: #000;">' +
                                formattedSum + '</td>';
                        } else {
                            footerRow += '<td></td>';
                        }
                    }

                    footerRow += '<td></td>';
                    footerRow += '</tr>';

                    if (!$('#debits-table tfoot').length) {
                        $('#debits-table').append('<tfoot></tfoot>');
                    }
                    $('#debits-table tfoot').html(footerRow);

                    $('#debits-table tfoot tr').css({
                        'background-color': '#f5f5f5',
                        'border-top': '2px solid #ddd'
                    });
                }
            });

            $('#endorse-narration-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.endorse_narration_datatable') !!}",
                    data: function(d) {
                        d.cover_no = "{!! $coverReg->cover_no !!}";
                        d.endorsement_no = "{!! $coverReg->endorsement_no !!}";
                        d.endorse_type_slug = "change-sum-insured";
                    }
                },
                columns: [{
                        data: 'document_no',
                        className: 'highlight-idx'
                    },
                    {
                        data: 'endorsement_type',
                        searchable: true
                    },
                    {
                        data: 'narration',
                        searchable: true
                    },
                    {
                        data: 'extension_days',
                        searchable: false,
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false
                    },
                ]
            });

            $('#to-cover').click(function(e) {
                $('#coverForm').submit();
            });

            $('#edit-cover').click(function(e) {
                $('#editCoverForm').submit();
            });

            $('#to-customer').click(function(e) {
                $('#customerForm').submit();
            });

            $('#partner-save-btn').click(function() {
                $('#reinsurerForm').submit()
            });

            $('#dismiss-partner-btn').click(function() {
                $('#reinsurerForm')[0].reset()
            });

            $('#partner-edit-btn').click(function() {
                $('#EditReinsurerForm').submit()
            });

            $('#attachments-save-btn').click(function(e) {
                $('#attachmentsForm').submit()
            });

            $('#clauses-save-btn').click(function(e) {
                $('#clausesForm').submit()
            });

            $('#verify-save-btn').click(function(e) {
                $('#verifyForm').submit()
            });

            $('#debit-save-btn').click(function(e) {
                $('#debitForm').submit()
            });

            $('#ins-class-save-btn').click(function(e) {
                $('#insuranceClassForm').submit()
            });

            $('#mdp-inst-save-btn').click(function(e) {
                $('#mdpInstallmentForm').submit()
            });

            $('#fac-inst-save-btn').click(function(e) {
                $('#facInstallmentForm').submit()
            });

            tinymce.init({
                selector: 'textarea#reinsurer-email',
                setup: function(editor) {
                    editor.on('change', function(e) {
                        editor.save();
                    });
                },
                plugins: 'preview importcss searchreplace autolink autosave save directionality visualchars fullscreen link charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap quickbars accordion',
                // editimage_cors_hosts: ['picsum.photos'],
                menubar: false,
                toolbar: "bold italic underline strikethrough | align numlist bullist | fullscreen preview ",
                autosave_ask_before_unload: true,
                autosave_interval: '30s',
                autosave_prefix: '{path}{query}-{id}-',
                autosave_restore_when_empty: false,
                autosave_retention: '2m',
                image_advtab: false,
                importcss_append: true,
                automatic_uploads: false,
                branding: false,
                height: 350,
                image_caption: false,
                quickbars_selection_toolbar: 'bold italic | h2 h3 blockquote',
                noneditable_class: 'mceNonEditable',
                toolbar_mode: 'sliding',
                skin: 'oxide',
                content_css: 'default',
                quickbars_insert_toolbar: false,
                quickbars_selection_toolbar: false,
                content_style: 'body { font-family:Aptos,Arial,sans-serif; font-size:15px } p,h1 {margin: 0px; padding:0px;}'
            });

            $(document).on('click', '.send_reinsurer_email', async function(e) {
                e.preventDefault()

                lastReinData.tranNo = $(this).data('tran_no');
                lastReinData.debitUrl = $(this).data('debit_url');
                lastReinData.claimNoticeUrl = $(this).data('claim_notice_url');

                const reinsurers = @json($coverpart) ?? [];

                await facReinEmailModal(
                    lastReinData.tranNo,
                    lastReinData.debitUrl,
                    lastReinData.claimNoticeUrl,
                    reinsurers
                );

                $("#sendReinDocumentEmail").modal('show')

                // console.log()
                // const endorsementNo = $(this).data('endorsement_no')
                // const coverNo = $(this).data('cover_no')

                // const doc_email = $(this).data('client_emails')
                // const client_name = $(this).data('client_name')

                // defaultEmailTemplate =
                //     "<p>Dear {name},</p><br>" +
                //     "<p>Greetings,</p><br>" +
                //     "<p>We are pleased to present the subject facultative placement for your consideration. Please find attached the placement slip and supporting documents outlining the risk details.</p><br>" +
                //     "<p>Kindly review the offer and confirm your maximum line of support.</p><br>" +
                //     "<p>Looking forward to your feedback.</p><br><br>" +
                //     "<p>Best Regards,<br>[Your Name]<br>[Your Position]</p>";
                // if (doc_email) {
                //     $('#emailTo').empty();
                //     $.each(doc_email, function(index, email) {
                //         $('#emailTo').append($('<option>', {
                //             value: email,
                //             text: email
                //         }));
                //     });
                // }
                // tinymce.get('reinsurer-email').setContent(defaultEmailTemplate);
                // $('#emailTo').select2('destroy').find('option').prop('selected', true).end().select2();
                // $('#emailSubject').val(
                //     `Facultative Submission - [Risk Reference] - [Brief Risk Description]`);
                // $('#send-email-modal').modal('show')

                // $('#coverNo').val(coverNo);
                // $('#endorsementNo').val(endorsementNo);
                // // loadAttachements(client_docs?.attachments, policy_no, selectedValue)
            })

            async function facReinEmailModal(tranNo, debitUrl, claimNoticeUrl, reinsurers) {
                // window.OutlookConnectionManager.showLoading();
                // const emailConnection = await window.OutlookConnectionManager.checkStatus();
                // if (!emailConnection.connected) {
                //     window.OutlookConnectionManager.hideLoading();
                //     window.OutlookConnectionManager.show();
                //     return;
                // }
                // window.OutlookConnectionManager.hideLoading();

                if (debitUrl) {
                    $("#debitNoteLink").attr('href', debitUrl);
                    $("#debitNoteFile").val(debitUrl);
                } else {
                    $("#debitNoteLink").removeAttr('href').on('click', e => e.preventDefault());
                }

                if (claimNoticeUrl) {
                    $("#claimNoticeLink").attr('href', claimNoticeUrl);
                    $("#claimNoticeFile").val(claimNoticeUrl);
                } else {
                    $("#claimNoticeLink").removeAttr('href').on('click', e => e.preventDefault());
                }

                const reinsurer = reinsurers.find(x => Number(x.tran_no) === Number(tranNo));
                if (!reinsurer) {
                    toastr.info('No reinsurer found for the selected transaction.');
                    return;
                }

                const contacts = reinsurer?.contacts || [];
                const $contactsSelect = $(".claimReinEmailForm #contacts");
                const $ccEmailSelect = $(".claimReinEmailForm #ccEmail");
                const $bccEmailSelect = $(".claimReinEmailForm #bccEmail");

                $contactsSelect.empty().append('<option value="" disabled>--Select contacts--</option>');
                $ccEmailSelect.empty().append('<option value="" disabled>--Select CC emails--</option>');
                $bccEmailSelect.empty().append('<option value="" disabled>--Select BCC emails--</option>');

                let contactsSelected = [];
                if (contacts.length > 0) {
                    const primaryContacts = [];
                    const regularContacts = [];

                    contacts.forEach(contact => {
                        const email = contact.contact_email;
                        if (!email) return;

                        let optionText = contact.contact_name ? `${contact.contact_name} (${email})` :
                            email;
                        if (contact.contact_mobile_no) optionText += ` - ${contact.contact_mobile_no}`;
                        if (contact.is_primary) optionText += ' [Primary]';

                        const createOption = () => $('<option></option>')
                            .attr('value', email)
                            .text(optionText)
                            .data('contact-data', contact)
                            .data('is-primary', contact.is_primary);

                        $contactsSelect.append(createOption());
                        $ccEmailSelect.append(createOption());
                        $bccEmailSelect.append(createOption());

                        if (contact.is_primary) primaryContacts.push(email);
                        else regularContacts.push(email);
                    });

                    if (primaryContacts.length > 0) {
                        contactsSelected = primaryContacts;
                        $contactsSelect.val(primaryContacts).trigger('change');
                    } else if (regularContacts.length === 1) {
                        contactsSelected = [regularContacts[0]];
                        $contactsSelect.val(regularContacts[0]).trigger('change');
                    }

                    [$contactsSelect, $ccEmailSelect, $bccEmailSelect].forEach($select => {
                        if ($select.hasClass('select2-hidden-accessible')) {
                            $select.trigger('change.select2');
                        }
                    });
                }

                const partnerEmail = reinsurer?.partner?.email ?? null;
                let toEmails = [];
                if (partnerEmail) toEmails.push(partnerEmail);
                toEmails = toEmails.concat(contactsSelected);

                $(".claimReinEmailForm #toEmail").val(toEmails);
                $(".claimReinEmailForm #partnerToEmail").val(partnerEmail);
                $("#sendReinDocumentEmail").modal('show');
            }


            $(document).on('click', '.send-cedant-email', function(e) {
                e.preventDefault()
                const endorsementNo = $(this).data('endorsement_no')
                const coverNo = $(this).data('cover_no')

                const doc_email = $(this).data('client_emails')
                const client_name = $(this).data('client_name')
                const user = $(this).data('user')

                defaultEmailTemplate =
                    "<p>Dear {name},</p><br>" +
                    "<p>Greetings,</p><br>" +
                    "<p>We are pleased to present the subject facultative placement for your consideration. Please find the placement slip attached.</p><br>" +
                    "<p>Kindly review the placement and confirm we can proceed to bind cover.</p><br>" +
                    "<p>Looking forward to your feedback.</p><br><br>" +
                    "<p>Best Regards,<br>" + user + "<br>[Your Position]</p>";
                if (doc_email) {
                    $('#emailTo').empty();
                    $.each(doc_email, function(index, email) {
                        $('#emailTo').append($('<option>', {
                            value: email,
                            text: email
                        }));
                    });
                }

                tinymce.get('reinsurer-email').setContent(defaultEmailTemplate);
                $('#emailTo').select2('destroy').find('option').prop('selected', true).end().select2();
                $('#emailSubject').val(
                    `Placement of a facultative offer to Cedant`);
                $('#send-email-modal').modal('show')

                $('#coverNo').val(coverNo);
                $('#endorsementNo').val(endorsementNo);
                // loadAttachements(client_docs?.attachments, policy_no, selectedValue)
            })

            function loadAttachements(attachments, policy_no, recipient_type) {
                let container = $('#attachments-container');
                container.html('');
                let totalSize = 0;
            }


            function populateSelect(elementId, optionsArray, selectedClauses) {
                var selectElement = $('#' + elementId);
                if (!selectElement.length) {
                    console.error('Element with ID ' + elementId + ' not found.');
                    return;
                }

                selectElement.empty();

                var label = getLabelForElement(elementId);

                if (!selectElement.is('[multiple]')) {
                    selectElement.append('<option value="" disabled selected>Choose ' + label + '</option>');
                }

                optionsArray.forEach(function(option) {
                    var value = option.clause_id.trim();
                    var text = option.clause_title.trim();
                    // Check if the clause_id is in the selectedClauses array
                    var isSelected = selectedClauses.some(function(selectedClause) {
                        return selectedClause.clause_id === value;
                    });

                    var selected = isSelected ? ' selected' : '';
                    selectElement.append('<option value="' + value + '"' + selected + '>' + text +
                        '</option>');
                });
            }

            function getLabelForElement(elementId) {
                var label = $('#' + elementId + '_label');
                if (label.length > 0) {
                    return label.text().trim();
                } else {
                    return '';
                }
            }

            function computeCommissionAmt(counter) {
                // Get the premium value
                var premium = parseFloat($("#reinsurer-premium-" + counter).val().replace(/,/g, '')) || 0;
                var cedantCommRate = {!! $coverReg->cedant_comm_rate !!};
                // Get the commission rate value
                var reinsurerCommRate = parseFloat($("#reinsurer-comm_rate-" + counter).val()) || 0;
                // Calculate the commission amount
                var reinsurerCommAmt = (premium * reinsurerCommRate) / 100;
                $("#reinsurer-comm_amt-" + counter).val(numberWithCommas(reinsurerCommAmt.toFixed(2)));

                var brokerageCommAmt = (reinsurerCommRate - cedantCommRate) / 100 * premium;
                if (brokerageCommAmt < 0) {
                    brokerageCommAmt = 0;
                }
                $("#reinsurer-brokerage_comm_amt-" + counter).val(numberWithCommas(brokerageCommAmt.toFixed(2)));

                calculateBrokerageCommRate()
            }

            function computeCommissionRate(counter) {
                var premium = parseFloat($("#reinsurer-premium-" + counter).val().replace(/,/g, '')) || 0;
                var commAmt = parseFloat($("#reinsurer-comm_amt-" + counter).val().replace(/,/g, '')) || 0;

                var commRate = (commAmt / premium) * 100;

                $("#reinsurer-comm_rate-" + counter).val(commRate.toFixed(2));
            }

            function askBrokingCommission(endorsementNo, partnerNo = null) {
                let baseUrl = "{{ route('docs.reincreditnotes', ['endorsement_no' => '__ENDORSEMENT_NO__']) }}";
                baseUrl = baseUrl.replace('__ENDORSEMENT_NO__', endorsementNo);
                let url = baseUrl;
                if (partnerNo) {
                    url += `&partner_no=${partnerNo}`;
                }

                Swal.fire({
                    title: 'Include Broking Commission?',
                    // text: "Click OK for Yes, Cancel for No.",
                    icon: 'question',
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: 'Yes',
                    denyButtonText: 'No',
                    width: '450px',
                    // cancelButtonText: 'Cancel',
                    customClass: {
                        actions: 'swal_actions_btn',
                        // cancelButton: 'order-1 right-gap btn-cancel',
                        confirmButton: 'order-2 btn-confirm',
                        denyButton: 'order-3 btn-deny',
                    },
                    buttonsStyling: false, // disables default styling for full custom control
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            showConfirmButton: false,
                            confirmButtonText: 'OK',
                            title: 'Generating with Brokerage...',
                            text: '',
                            timer: 1500,
                            timerProgressBar: true
                        });
                        url += "&include_broking_commission=yes";
                        viewCreditNotePdf(url)
                    } else if (result.isDenied) {
                        Swal.fire({
                            icon: 'success',
                            showConfirmButton: false,
                            confirmButtonText: 'OK',
                            title: 'Generating without Brokerage...',
                            text: '',
                            timer: 1500,
                            timerProgressBar: true
                        });
                        url += "&include_broking_commission=no";
                        viewCreditNotePdf(url)
                    }
                })
            }

            // pay method
            $("select#reins_pay_method").change(function() {
                var pay_method = $("select#reins_pay_method option:selected").attr('pay_method_desc')
                if (pay_method === 'I') {
                    $('#no_of_installments_section').css({
                        display: "block"
                    });
                    $('#add_reinsurer_btn_section').css({
                        display: "block"
                    });
                } else {
                    $('#no_of_installments_section').css({
                        display: "none"
                    });
                    $('#add_reinsurer_btn_section').css({
                        display: "none"
                    });
                    $('#add_installments_box').hide();
                }
            });

            $('#no_of_installments').on("change keyup", function(e) {
                var inst = $(this).val()
                if (!inst) {
                    $('#add_installments_box').hide();
                }
            })

            $('#add_reinsurer_instalments').on('click', function() {
                const noOfInstallments = $("#no_of_installments").val().replace(/,/g, '') || 0;
                const reinCommRate = $("#reinsurer-comm_rate-0").val().replace(/,/g, '') || 0;
                const reinCommAmnt = $("#reinsurer-comm_amt-0").val().replace(/,/g, '') || 0;
                const reinfrontingRate = $("#reinsurer-fronting_rate-0").val().replace(/,/g, '') || 0;

                const shareOffered = {!! $coverReg->share_offered !!};
                const reinPremium = {!! $coverReg->rein_premium !!};
                // const isInstallment = {{ isset($isInstallment) ? $isInstallment : false }};
                const coverInstallments = {!! $coverInstallments !!} || [];

                $('#add_installments_box').show();
                // computation for reinsurer installment amount
                var totalDr = $("#reinsurer-premium-0").val().replace(/,/g, '') || 0;
                var whtRate = $("#wht_rate-0-0").val().replace(/,/g, '') || 0;

                var totalCr = parseFloat((reinCommRate / 100) * totalDr);
                var totalDeducted = 0;
                var totalAdded = 0;
                // Withholding tax
                if (whtRate > 0) {
                    totalDeducted += (whtRate / 100) * (totalDr - reinCommAmnt);
                }
                // Fronting fees
                if (reinfrontingRate > 0) {
                    totalDeducted += (reinfrontingRate / 100) * (totalDr - reinCommAmnt);
                }
                var balanceDue = totalDr - totalDeducted;
                var instalAmount = ((balanceDue - totalCr) + totalAdded).toFixed(2);

                $('#reinsurer_plan_section').empty();
                if (noOfInstallments > 0) {
                    const totalFacAmount = parseFloat(instalAmount) || 0;
                    const totalFacInstAmt = (totalFacAmount / noOfInstallments).toFixed(2);
                    installmentTotalAmount = totalFacAmount;
                    if (coverInstallments.length > 1) {
                        if (coverInstallments?.length > 0) {

                            for (let x = 1; x <= noOfInstallments; x++) {
                                const c = coverInstallments?.find(j => j.installment_no == x)
                                $('#reinsurer_plan_section').append(`
                                    <div class="row reinsurer-instalament-row" data-count="${typeof c !== 'undefined' ? c.installment_no : x}">
                                        <div class="col-md-3">
                                            <label class="">Installment</label>
                                            <input type="hidden" name="installment_no[]" value="${typeof c !== 'undefined' ? c.installment_no : x}" readonly class="form-control"/>
                                            <input type="text" value="installment No. ${typeof c !== 'undefined' ? c.installment_no : x}" id="instl_no_${typeof c !== 'undefined' ? c.installment_no : x}" readonly class="form-control" required/>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="instl_date_${typeof c !== 'undefined' ? c.installment_no : x}">Installment Due Date</label>
                                            <input type="date" name="installment_date[]" id="instl_date_${typeof c !== 'undefined' ? c.installment_no : x}" value="${typeof c !== 'undefined' ? c.installment_date : x}"  class="form-control" required/>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="instl_amnt_${typeof c !== 'undefined' ? c.installment_no : x}">Total Installment Amount</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="installment_amt[]" id="instl_amnt_${typeof c !== 'undefined' ? c.installment_no : x}" value="${totalFacInstAmt != 0 ? numberWithCommas(totalFacInstAmt): ''}" class="form-control amount"  onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required/>
                                                <button class="btn btn-danger btn-sm" type="button" id="remove_reinsurer_instalment"><i class="bx bx-minus align-middle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            }
                        }
                    } else {
                        if (noOfInstallments <= 100) {
                            for (let i = 1; i <= noOfInstallments; i++) {
                                $('#reinsurer_plan_section').append(`
                                    <div class="row reinsurer-instalament-row" data-count="${i}">
                                        <div class="col-md-3">
                                            <label class="">Installment</label>
                                            <input type="hidden" name="installment_no[]" value="${i}" readonly class="form-control"/>
                                            <input type="text" value="installment No. ${i}" id="instl_no_${i}" readonly class="form-control" required/>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="instl_date_${i}">Installment Due Date</label>
                                            <input type="date" name="installment_date[]" id="instl_date_${i}"  class="form-control" required/>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="instl_amnt_${i}">Total Installment Amount</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="installment_amt[]" id="instl_amnt_${i}" value="${numberWithCommas(totalFacInstAmt)}" class="form-control amount"  onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required/>
                                                <button class="btn btn-danger btn-sm" type="button" id="remove_reinsurer_instalment"><i class="bx bx-minus align-middle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                `);
                            }
                        }
                    }

                }
            });

            $('#reinsurer_plan_section').on('click', '#remove_reinsurer_instalment', function() {
                const removedIndex = $(this).closest('.reinsurer-instalament-row').data('count');
                const currentInstallments = $('#no_of_installments').val();
                const remaingInstalment = currentInstallments >= 1 ? parseInt(currentInstallments - 1) : 0;

                if (remaingInstalment > 0) {
                    $('#no_of_installments').val(remaingInstalment);
                } else {
                    $('#no_of_installments').val('');
                    $("#reinsurer_plan_section").empty();
                    $('#add_installments_box').hide();
                }
                $('#no_of_installments').trigger('change');
                $(this).closest('.reinsurer-instalament-row').remove();
            });

            async function viewDebitNotePdf(url) {
                const response = await fetch("{!! route('docs.coverdebitnote', ['endorsement_no' => $coverReg->endorsement_no]) !!}", {
                    method: 'GET',
                });
                if (response.ok) {
                    window.open(url, '_blank', 'noopener,noreferrer');
                } else {
                    toastr.error("This transaction is not yet debited", 'Debit Note')
                }
            }

            $('#generateDebitNote').on('click', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                viewDebitNotePdf(url);
            });

            async function viewCreditNotePdf(url) {
                const response = await fetch(url, {
                    method: 'GET'
                });

                if (response.ok) {
                    window.open(url, '_blank', 'noopener,noreferrer');
                } else {
                    toastr.error("This transaction is not yet debited", 'Credit Note')
                }
            }

            async function checkDebitExists(url, endorseNo, partnerNo = null) {
                const response = await fetch(url, {
                    method: 'GET',
                });
                if (response.ok) {
                    askBrokingCommission(endorseNo, partnerNo)
                } else {
                    toastr.error("This transaction is not yet debited", 'Credit Note')
                }
            }

            $('#generateCreditNote').on('click', function(e) {
                e.preventDefault();
                const url = $("#generateDebitNote").attr('href');
                var endorseNo = $(this).data("endorsementno")
                checkDebitExists(url, endorseNo);
            });

            $(document).on('click', '.rein_credit_note_btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                var endorseNo = $(this).data("endorsementno")
                var partnerNo = $(this).data("partnerno")

                checkDebitExists(url, endorseNo, partnerNo);
            });

            async function viewCoverSlipPdf(url) {
                const response = await fetch(url, {
                    method: 'GET',
                });

                if (response.ok) {
                    window.open(url, '_blank', 'noopener,noreferrer');
                } else {
                    toastr.error("This transaction is not yet debited", 'Cover Slip')
                }
            }

            $('#generateCoverSlip').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr("href")
                viewCoverSlipPdf(url);
            });

            async function viewEndorsementSlipPdf(url) {
                const response = await fetch(url, {
                    method: 'GET',
                });

                if (response.ok) {
                    console.log(response)
                    window.open(url, '_blank', 'noopener,noreferrer');
                } else {
                    toastr.error("This transaction is not yet debited", 'Endorsement Notice Slip')
                }
            }

            $('#generateEndorsementSlip').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr("href")
                viewEndorsementSlipPdf(url);
            });

            function toDecimal(number) {
                return parseFloat(Number(number).toFixed(2));
            }

            function areDecimalsEqual(num1, num2, tolerance = 0.1) {
                return Math.abs(toDecimal(num1) - toDecimal(num2)) <= tolerance;
            }

            function validateEmailContent() {
                const content = tinymce.get('reinsurer-email').getContent();
                const requiredPlaceholders = [
                    "[Your Name]",
                    "[Your Position]",
                ];

                const missingPlaceholders = requiredPlaceholders.filter(placeholder =>
                    content.includes(placeholder)
                );

                if (missingPlaceholders.length > 0) {
                    return {
                        valid: false,
                        message: `Please replace the following:<br/> ${missingPlaceholders.join(', <br/>')}`
                    };
                }

                const textContent = content.replace(/<[^>]*>/g, '').trim();
                if (textContent.length < 10) {
                    return {
                        valid: false,
                        message: 'Email content is too short. Please provide more detailed information.'
                    };
                }

                return {
                    valid: true
                };
            }

            $('#sendButton').click(function(e) {
                e.preventDefault()
                $('#confirmModal').modal('hide')

                // const validation = validateEmailContent();
                // if (!validation.valid) {
                //     toastr.error(validation.message, 'Incomplete Data')
                // } else {
                $('#send-email-modal').modal('hide')
                $('#confirmModal').modal('show')
                // }
            })

            $('#confirmReinEmail').click(function(e) {
                e.preventDefault()
                const form = $('#reinsurerEmailForm');
                const formData = new FormData(form[0]);
                formData.set('emailContent', tinymce.get('reinsurer-email').getContent());

                $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(function(response) {
                        console.log(response)
                        if (response.status == 201) {
                            $('#confirmModal').modal('hide')
                            $('#send-email-modal').modal('hide')
                            toastr.success('Email sent successfully!')
                        } else {
                            if (response.status == 201) {
                                toastr.success(response.message)
                            } else {
                                toastr.error('Failed to send Email')
                            }
                        }
                    })
                    .fail(function(xhr) {
                        toastr.error('An internal error occured', 'Error')
                    });
            })

        })
    </script>
@endpush
