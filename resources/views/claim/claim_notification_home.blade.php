@extends('layouts.app')

@section('content')
    <style>
        .file-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
        }

        .file-item:hover {
            background-color: #e9ecef;
            border-color: #0d6efd;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .file-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 16px;
        }

        .file-icon.pdf {
            background-color: #dc3545;
            color: white;
        }

        .file-icon.doc {
            background-color: #0d6efd;
            color: white;
        }

        .file-icon.docx {
            background-color: #0d6efd;
            color: white;
        }

        .file-icon.xlsx {
            background-color: #198754;
            color: white;
        }

        .file-icon.xls {
            background-color: #198754;
            color: white;
        }

        .file-icon.img {
            background-color: #fd7e14;
            color: white;
        }

        .file-icon.jpg {
            background-color: #fd7e14;
            color: white;
        }

        .file-icon.jpeg {
            background-color: #fd7e14;
            color: white;
        }

        .file-icon.png {
            background-color: #fd7e14;
            color: white;
        }

        .file-icon.gif {
            background-color: #fd7e14;
            color: white;
        }

        .file-icon.zip {
            background-color: #6f42c1;
            color: white;
        }

        .file-icon.default {
            background-color: #6c757d;
            color: white;
        }

        .file-info h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #212529;
        }

        .file-meta {
            font-size: 12px;
            color: #6c757d;
            margin: 2px 0;
        }

        .file-actions .btn {
            padding: 4px 12px;
            font-size: 12px;
        }

        .no-files {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
        }

        #fileViewerContent {
            position: relative;
        }

        .file-preview-placeholder {
            padding: 60px 20px;
            color: #6c757d;
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
    </style>

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Claim Notification Enquiry</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">Client</a></li>
                    <li class="breadcrumb-item"><a href="#"
                            id="to-customer">{{ Str::ucfirst(strtolower($customer->name)) }}</a>
                    </li>
                    <li class="breadcrumb-item"><a href="#">Covers</a></li>
                    <li class="breadcrumb-item"><a href="#" id="to-cover">{{ $ClaimRegister->intimation_no }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Claim Notification Enquiry</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('endorsements_list') }}" method="post" id="notificationForm">
        @csrf
        <input type="hidden" name="intimation_no" value="{{ $ClaimRegister->intimation_no }}">
        <input type="hidden" name="customer_id" value="{{ $ClaimRegister->customer_id }}">
    </form>
    <form action="{{ route('customer.dtl') }}" method="post" id="customerForm">
        @csrf
        <input type="hidden" name="customer_id" value="{{ $ClaimRegister->customer_id }}">
    </form>
    <form action="{{ route('claim_detail') }}" method="post" id="claimForm">
        @csrf
        <input type="hidden" name="claim_no" id="clm_claim_no">
        <input type="hidden" name="process_type" id="process_type">
    </form>

    <div class="row-cols-12">
        @if ($ClaimRegister->status !== 'A')
            <div class="card mb-2 border col">
                <div class="card-body">
                    <button class="btn btn-secondary mr-2 btn-sm btn-wave waves-effect waves-light px-4 processReserve">
                        <i class='bx bx-analyse'></i> Process as Reserve
                    </button>
                    <button class="btn btn-success mr-2 btn-sm btn-wave waves-effect waves-light px-4 processClaim">
                        <i class='bx bx-analyse'></i> Process as Claim
                    </button>
                    <div class="status-content">
                        <div class="reserve-status-content">
                            @switch ($ClaimRegister->reserve_approval_status)
                                @case('P')
                                    <button class="btn btn-outline-danger mr-2 btn-sm btn-wave waves-effect waves-light" disabled>
                                        <i class="bx bx-time"></i> Pending Verification
                                    </button>
                                @break

                                @case('A')
                                    <span class="badge bg-success-transparent"> Claim Reserve Approved</span>
                                @break

                                @default
                                    {{-- <button
                                        class="btn btn-secondary mr-2 btn-sm btn-wave waves-effect waves-light px-4 cancelProcessReserve">
                                        <i class='bx bx-analyse'></i> Process as Reserve
                                    </button>
                                    <button
                                        class="btn btn-success mr-2 btn-sm btn-wave waves-effect waves-light px-4 cancelProcessClaim">
                                        <i class='bx bx-analyse'></i> Process as Claim
                                    </button> --}}
                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                        id="acknowledgements" data-bs-toggle="modal" data-bs-target="#acknowledgement-modal">
                                        <i class="fa-solid fa-check"></i> Document Acknowledgement
                                    </button>
                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                        data-bs-toggle="modal" data-bs-target="#status-modal">
                                        <i class="fa-solid fa-pencil"></i> Update Claim Status
                                    </button>

                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                        id="reserve-details" data-bs-toggle="modal" data-bs-target="#reserveModal">
                                        <i class="fa-solid fa-plus"></i> Claim Reserves
                                    </button>
                                    {{-- <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                        id="verify-reserve">
                                        <i class="fa-solid fa-file-circle-check"></i> Verify Details
                                    </button> --}}
                                @break
                            @endswitch
                        </div>
                        <div class="claim-status-content">
                            @switch ($ClaimRegister->approval_status)
                                @case('P')
                                    <button class="btn btn-outline-danger mr-2 btn-sm btn-wave waves-effect waves-light" disabled>
                                        <i class="bx bx-time"></i> Pending Verification
                                    </button>
                                @break

                                @case('A')
                                    @if ($ClaimRegister->converted_claim_no == null)
                                        <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                                            id="convert-to-claim">
                                            <i class='bx bx-credit-card'></i> Convert to Claim
                                        </button>
                                    @else
                                        <span class="badge bg-success-transparent"> Converted to a Claim</span>
                                    @endif
                                @break

                                @default
                                    {{-- <button
                                        class="btn btn-secondary mr-2 btn-sm btn-wave waves-effect waves-light px-4 cancelProcessReserve">
                                        <i class='bx bx-analyse'></i> Process as Reserve
                                    </button>
                                    <button
                                        class="btn btn-success mr-2 btn-sm btn-wave waves-effect waves-light px-4 cancelProcessClaim">
                                        <i class='bx bx-analyse'></i> Process as Claim
                                    </button> --}}
                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light px-4"
                                        id="acknowledgements" data-bs-toggle="modal" data-bs-target="#acknowledgement-modal">
                                        <i class="bx bx-check"></i> Document Acknowledgement
                                    </button>
                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light px-4"
                                        data-bs-toggle="modal" data-bs-target="#status-modal">
                                        <i class="bx bx-edit"></i> Update Claim Status
                                    </button>
                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light px-4"
                                        id="risk-details">
                                        <i class="bx bx-plus"></i> Claim Particulars
                                    </button>
                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light px-4"
                                        id="verify-claim">
                                        <i class="bx bx-check-circle"></i> Verify Details
                                    </button>
                                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light px-4"
                                        id="convert-to-claim">
                                        <i class='bx bx-credit-card'></i> Convert to Claim
                                    </button>
                                @break
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card mb-2 custom-card border col">
            <div class="card-body">
                <div class="row mb-3 bg-light p-2">
                    <div class="col-md-2">
                        <strong>Cover Number</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $ClaimRegister->cover_no }}
                    </div>
                    <div class="col-md-2 text-left">
                        <strong>Endorsement Number</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $ClaimRegister->endorsement_no }}
                    </div>
                </div>
                <div class="row mb-2 p-2">
                    <div class="col-md-2 text-left">
                        <strong>Claim Intimation Number</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $ClaimRegister->intimation_no }}
                    </div>
                    <div class="col-md-2 text-left">
                        <strong>Cedant</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $customer->name }}
                    </div>
                </div>
                <div class="row mb-3 bg-light p-2">
                    <div class="col-md-2 text-left">
                        <strong>Cover Period</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $ClaimRegister->cover_from }} to {{ $ClaimRegister->cover_to }}
                    </div>
                    @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                        <div class="col-md-2 text-left">
                            <strong>Insured</strong>
                        </div>
                        <div class="col-md-3">
                            {{ $ClaimRegister->insured_name }}
                        </div>
                    @endif
                </div>
                <div class="row mb-3 bg-light p-2 text-left">
                    <div class="col-md-2">
                        <strong>Status</strong>
                    </div>
                    <div class="col-md-3">
                        @if ($ClaimRegister->status == 'A')
                            <span class="badge bg-success-transparent">Approved</span>
                        @elseif($ClaimRegister->status == 'R')
                            <span class="badge bg-danger-transparent">Rejected</span>
                        @else
                            <span class="badge bg-warning-transparent">Pending</span>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <strong>Currency</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $ClaimRegister->currency_code }}
                    </div>

                </div>
                <div class="row mb-2 p-2">
                    @if ($ClaimRegister->converted_claim_no != null)
                        <div class="col-md-2 text-left">
                            <strong>Converted Claim No</strong>
                        </div>
                        <div class="col-md-3">
                            {{ $ClaimRegister->converted_claim_no }}
                        </div>
                    @endif
                    <div class="col-md-2">
                        <strong>Type of Claim</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $type_of_bus->bus_type_name }} CLAIM
                    </div>
                </div>
                <div class="row mb-1 mt-3 bg-light p-2">
                    <div class="col-md-2 text-left">
                        <strong>Reserve Details:</strong>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-2"><strong>Settlement Details:</strong></div>
                    <div class="col-md-3"></div>
                </div>
                <div class="row mb-1 pt-0 p-2">
                    <div class="col-md-2 text-left">
                        <strong>Reserve Amount</strong>
                    </div>
                    <div class="col-md-3">{{ number_format($ClaimRegister->reserve_amount, 2) }}</div>
                    <div class="col-md-2"><strong>Claim Amount</strong></div>
                    <div class="col-md-3">{{ number_format($nextDebitAmount, 2) }}</div>
                </div>
                <div class="row mb-2 pt-0 p-2">
                    <div class="col-md-2 text-left">
                        <strong>Reserve Status</strong>
                    </div>
                    <div class="col-md-3">
                        @if ($ClaimRegister->reserve_approval_status == 'A')
                            <span class="badge bg-success-transparent">Approved</span>
                        @elseif($ClaimRegister->reserve_approval_status == 'R')
                            <span class="badge bg-danger-transparent">Rejected</span>
                        @else
                            <span class="badge bg-warning-transparent">Pending</span>
                        @endif
                    </div>
                    <div class="col-md-2"><strong>Claim Status</strong></div>
                    <div class="col-md-3">
                        @if ($ClaimRegister->approval_status == 'A')
                            <span class="badge bg-success-transparent">Approved</span>
                        @elseif($ClaimRegister->approval_status == 'R')
                            <span class="badge bg-danger-transparent">Rejected</span>
                        @else
                            <span class="badge bg-danger-transparent">Awaiting</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-2 custom-card border col">
            <div class="card-body pt-0">
                <nav>
                    <div class="nav nav-tabs nav-justified tab-style-4 d-sm-flex d-block reinsurers-details-card"
                        id="nav-tab" role="tablist">

                        <button class="nav-link active" id="nav-attachments-tab" data-bs-toggle="tab"
                            data-bs-target="#attachments-tab" type="button" role="tab" aria-selected="false"
                            tabindex="-1"><i class="bx bx-file me-1 align-middle"></i>Document Attachments</button>
                        <button class="nav-link" id="nav-status-tab" data-bs-toggle="tab" data-bs-target="#status-tab"
                            type="button" role="tab" aria-selected="true"><i
                                class="bx bx-table me-1 align-middle"></i>Claim Status</button>
                        <button class="nav-link" id="nav-perils-tab" data-bs-toggle="tab" data-bs-target="#perils-tab"
                            type="button" role="tab" aria-selected="false" tabindex="-1"><i
                                class="bx bx-medal me-1 align-middle"></i>Particulars Details</button>
                        <button class="nav-link" id="nav-reinsurers-tab" data-bs-toggle="tab"
                            data-bs-target="#reinsurers-tab" type="button" role="tab" aria-selected="false"
                            tabindex="-1"><i class="bx bx-palette me-1 align-middle"></i>Reinsurers</button>
                        <button class="nav-link" id="nav-debits-tab" data-bs-toggle="tab" data-bs-target="#debits-tab"
                            type="button" role="tab" aria-selected="false" tabindex="-1"><i
                                class="bx bx-credit-card me-1 align-middle"></i>Cedant</button>
                        <button class="nav-link" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#docs-tab"
                            type="button" role="tab" aria-selected="false" tabindex="-1"><i
                                class="bx bx-file-blank me-1 align-middle"></i>Print-outs</button>
                    </div>
                </nav>
                <div class="tab-content reinsurers-tabpane-card" id="tab-style-4">
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
                                            <th scope="col">Credit No.</th>
                                            <th scope="col">Share(%)</th>
                                            <th scope="col">Sum insured</th>
                                            <th scope="col">Premium</th>
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
                    <div class="tab-pane active show" id="attachments-tab" role="tabpanel"
                        aria-labelledby="nav-attachments-tab" tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <table id="attachments-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Filename</th>
                                            <th scope="col">Date Received</th>
                                            <th scope="col">Document Type</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="status-tab" role="tabpanel" aria-labelledby="nav-status-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <table id="status-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Reason</th>
                                            <th scope="col">Narration</th>
                                            <th scope="col">Updated By</th>
                                            <th scope="col">Time</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="perils-tab" role="tabpanel" aria-labelledby="nav-perils-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <table id="perils-table"
                                    class="table table-striped text-nowrap table-hover table-responsive"
                                    style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">Tran No.</th>
                                            <th scope="col">Peril Name</th>
                                            <th scope="col">Basic Amount</th>
                                            <th scope="col">Share(%)</th>
                                            <th scope="col">Share Amount</th>
                                            {{-- <th scope="col">Action</th> --}}
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
                    <div class="tab-pane" id="docs-tab" role="tabpanel" aria-labelledby="nav-docs-tab" tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <a class="print-out-link pr-3"
                                    href="{{ route('docs.claimntf-docs-notc-letter', ['intimation_no' => $ClaimRegister->intimation_no]) }}"
                                    target="_blank" rel="noopener noreferrer">
                                    <i class="bx bx-file"></i> Claim Notices
                                </a>
                                <a href="{{ route('docs.claimntf-docs-ack-letter', ['intimation_no' => $ClaimRegister->intimation_no]) }}"
                                    class='print-out-link' target="_blank" rel="noopener noreferrer">
                                    <i class="bx bx-eye"></i> Acknowledgement Letter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="perilsModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticCaptureClaimLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <form id="save_peril" action="{{ route('claim.notification.saveperil') }}" method="post">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="intimation_no" value="{{ $ClaimRegister->intimation_no }}">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="staticCaptureClaimLabel">Capture Claim Particulars
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="treaty-div">
                            <div class="peril-sections mb-2 mt-2 p-2 pb-3" id="peril-sections-0" data-counter="0"
                                style="border: 1px solid #333">
                                <div id="peril_section-div">
                                    <div id="peril-section-0" data-counter="0" class="peril-section">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="peril_name-0" class="form-label">Particular Name</label>
                                                <div class="card-md">
                                                    <select name="peril_name[]" id="peril_name-0"
                                                        class="form-inputs select2 peril_name" data-counter="0" required>
                                                        <option value="">--Select Particular Name--</option>
                                                        @foreach ($perilTypes as $perilType)
                                                            <option value="{{ $perilType->id }}">
                                                                {{ $perilType->description }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 peril_amount">
                                                <label class="form-label">100 % Particular Amount</label>
                                                <div class="card-md">
                                                    <div class="input-group">
                                                        <input type="text"
                                                            class="form-inputs form-input-group peril_amount"
                                                            style="width: 80%;" name="peril_amount[]" data-counter="0"
                                                            id="peril_amount-0"
                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                            change="this.value=numberWithCommas(this.value)" required>
                                                        <button class="btn btn-danger add-peril-section" type="button"
                                                            style="line-height: 0px; border-radius: 0px;"
                                                            id="add-peril-section-0"><i class="bx bx-plus"></i></button>
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
                        <button type="button" class="btn btn-outline-danger btn-sm" id="cancel-peril-data"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="peril-save-btn"
                            class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="reserveModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <form id="save_reserve" action="{{ route('claim.notification.saveReserve') }}" method="post">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="intimation_no" value="{{ $ClaimRegister->intimation_no }}">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title" id="staticBackdropLabel">Capture Claim Reserve
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="treaty-div">
                            <div class="reserve-sections mb-2 mt-2 p-2" data-counter="0" style="border: 1px solid #333">
                                <div id="reserve_section-div">
                                    <div id="reserve-section">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="reserve_name" class="form-label"> Name</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-inputs" name="reserve_name"
                                                        id="reserve_name" value="Loss Reserve" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 reserve_amount">
                                                <label class="form-label required">100 % Reserve Amount</label>
                                                <div class="input-group mb-0">
                                                    <input type="text" class="form-inputs reserve_amount"
                                                        name="reserve_amount" id="reserve_amount"
                                                        value="{{ number_format($ClaimRegister->reserve_amount, 2) }}"
                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                        change="this.value=numberWithCommas(this.value)" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="reserve-save-btn"
                            class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Verify Modal -->
    <div class="modal effect-scale md-wrapper" id="verify-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="verifyForm" action="{{ route('approvals.send-for-approval') }}">
                    @csrf
                    <input type="hidden" name="intimation_no" value="{{ $ClaimRegister->intimation_no }}">
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="process" id="process">
                    <input type="hidden" name="process_action" id="process_action">
                    <input type="hidden" name="process_type" id="clm_process_type">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="staticBackdropLabel">Send to Verifier</h5>
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
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="">Comment</label>
                                <textarea name="comment" id="verify-comment" rows="4"
                                    class="form-control form-control-sm resize-none color-blk" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="verify-save-btn"
                            class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Attachment Modal -->
    <div class="modal effect-scale md-wrapper" id="attachmentDocumentModal" aria-labelledby="staticBackdropLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{-- <div class="modal-header">&nbsp;</div> --}}
                <div class="modal-body">
                    <div id="preview-container"></div>
                </div>
            </div>
        </div>
    </div>

    <!--Attachments Modal -->
    <div class="modal effect-scale md-wrapper" id="attachments-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="attachmentsForm">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="intimation_no" value="{{ $ClaimRegister->intimation_no }}">
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="id" id="attachments_id" value="">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="staticBackdropLabel">Document Upload</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <select name="title" id="title" class="form-select" required>
                                    <option value="">--Select Document--</option>
                                    @if ($all_docs_param)
                                        @foreach ($all_docs_param as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->doc_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">File</label>
                                <input type="file" class="form-control" id="file" name="file"
                                    accept=".pdf, .doc, .docx,.png,.jpg" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="attachments-save-btn"
                            class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Convert to claim Modal -->
    <div class="modal effect-scale md-wrapper" id="converToClaimModal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="convertToClaimForm"
                    action="{{ route('claim.notification.convertNotificationToClaim') }}">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="staticBackdropLabel">Convert Notification to Claim</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3  col-md-3">
                                <label for="intimation_no" class="form-label">Intimation No.</label>
                                <input type="text" id="intimation_no" name="intimation_no" class="form-control"
                                    value="{{ $ClaimRegister->intimation_no }}" readonly>
                            </div>
                            <div class="mb-3  col-md-3">
                                <label for="cover_no" class="form-label">Cover No.</label>
                                <input type="text" class="form-control" id="cover_no" name="cover_no"
                                    value="{{ $ClaimRegister->cover_no }}" readonly>
                            </div>
                            <div class="mb-3  col-md-3">
                                <label for="date_of_loss" class="form-label">Date of Loss</label>
                                <input type="text" class="form-control" name="date_of_loss"
                                    value="{{ $ClaimRegister->date_of_loss }}" id="date_of_loss" readonly>
                            </div>
                            <div class="mb-3  col-md-3">
                                <label for="total_claim_amnt" class="form-label">Total Claim Amount</label>
                                <input type="text" class="form-control" id="total_claim_amnt" name="total_claim_amnt"
                                    value="{{ number_format($nextDebitAmount, 2) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="convert-clm-save-btn"
                            class="btn btn-outline-dark btn-sm btn-wave waves-effect waves-light">Convert to
                            Claim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Claim Status Modal -->
    <div class="modal effect-scale md-wrapper" id="status-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <form id="statusForm">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="intimation_no" value="{{ $ClaimRegister->intimation_no }}">
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="staticBackdropLabel">Claim Status</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class=" col-md-4 mb-3">
                                <label for="title" class="form-label">Status</label>
                                <select class="form-inputs select2" id="status" name="status" required>
                                    <option value="">--Select Status--</option>
                                    <option value="O">Open</option>
                                    <option value="C">Closed</option>
                                </select>
                            </div>
                            <div class=" col-md-8 mb-3">
                                <label for="title" class="form-label">Reason</label>
                                <select class="form-inputs select2" id="status_reason" name="status_reason" required>
                                    <option value="">--Select Reason--</option>
                                    @foreach ($clmStatuses as $clmStatus)
                                        <option value="{{ $clmStatus->id }}">{{ $clmStatus->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-12 mb-3">
                                <label for="title" class="form-label">Narration</label>
                                <textarea class="form-inputs" name="description" id="description" cols="30" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="status-save-btn"
                            class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="acknowledgement-modal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="acknowledgementForm">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="intimation_no" value="{{ $ClaimRegister->intimation_no }}">
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="id" id="acknowledgement_id" value="{{ $cover->endorsement_no }}">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-center" id="staticBackdropLabel">Document Checklist</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class=""><strong>Select the dates when the documents were received</strong></p>

                        <!-- Received Documents Section -->
                        <h6 class="my-3"><b>Received Documents</b></h6>
                        <table class="table text-nowrap table-bordered table-striped" id="received-docs-table">
                            <thead>
                                <tr>
                                    <th>Document</th>
                                    <th>Date Received</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($ClaimNtfAckDocs && $ClaimNtfAckDocs->contains('document_type', 'received_doc'))
                                    @foreach ($ClaimNtfAckDocs as $doc)
                                        @if ($doc->document_type === 'received_doc')
                                            <tr id="received_{{ $doc->id }}" class="received-doc-row"
                                                style="width: 100%;">
                                                <td style="width:50%;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-file-text w-4 h-4" aria-hidden="true">
                                                        <path
                                                            d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z">
                                                        </path>
                                                        <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                                        <path d="M10 9H8"></path>
                                                        <path d="M16 13H8"></path>
                                                        <path d="M16 17H8"></path>
                                                    </svg>
                                                    <span style="vertical-align: -1px">{{ $doc->doc_name }}</span>
                                                    <input type="hidden" class="ack_received_doc"
                                                        name="received_document[]" id="received_doc_{{ $doc->id }}"
                                                        value="{{ $doc->doc_id }}" data-doc-id="{{ $doc->id }}">
                                                    <input type="hidden" class="ack_received_doc" name="date_received[]"
                                                        id="date_received{{ $doc->id }}"
                                                        value="{{ $doc->date_received }}"
                                                        data-doc-id="{{ $doc->id }}">
                                                </td>
                                                <td style="width:25%;">{{ $doc->date_received }}</td>
                                                <td style="width:25%;" class="received-success">
                                                    <div class="received-success-raw">
                                                        <div>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                                height="15" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-circle-check-big w-4 h-4"
                                                                aria-hidden="true">
                                                                <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                                                                <path d="m9 11 3 3L22 4"></path>
                                                            </svg>
                                                            <span style="vertical-align: -1px;">Uploaded</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr class="empty-received-docs">
                                        <td colspan="3" class="text-center">No received documents available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <!-- Missing Documents Section -->
                        <h6 class="my-3"><b>Missing Documents</b></h6>
                        <table class="table text-nowrap table-bordered table-striped" id="missing-docs-table">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Document</th>
                                    <th>Date Received</th>
                                    <th>Attach</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($ClaimNtfAckDocs && $ClaimNtfAckDocs->contains('document_type', 'missing_doc'))
                                    @foreach ($ClaimNtfAckDocs as $doc)
                                        @if ($doc->document_type === 'missing_doc')
                                            <tr style="text-align: left; vertical-align: middle; border: 1px solid #ddd"
                                                id="missing_{{ $doc->id }}" class="missing-doc-row">
                                                <td>
                                                    <input type="checkbox" class="check-input ml-3 doc-select"
                                                        name="missing_document[]"
                                                        id="missing_ack_doc_{{ $doc->id }}"
                                                        value="{{ $doc->doc_id }}" data-doc-id="{{ $doc->id }}">

                                                    <input type="hidden" name="missing_document_ids[]"
                                                        id="missing_document_ids_{{ $doc->id }}"
                                                        value="{{ $doc->doc_id }}" data-doc-id="{{ $doc->id }}" />


                                                </td>
                                                <td style="width:30%;">{{ $doc->doc_name }}</td>
                                                <td>
                                                    <input type="date" class="form-control ack_date"
                                                        name="missing_date_received[]"
                                                        id="missing_date_received_{{ $doc->id }}"
                                                        data-doc-id="{{ $doc->id }}">
                                                </td>
                                                <td>
                                                    <input type="file" class="form-control doc-file"
                                                        id="missing_file_{{ $doc->id }}" name="missing_file[]"
                                                        accept=".pdf,.doc,.docx,.png,.jpg"
                                                        data-doc-id="{{ $doc->id }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-dark revert-to-new"
                                                        data-doc-id="{{ $doc->id }}"
                                                        data-doc-name="{{ $doc->doc_name }}">
                                                        <i class="bi bi-arrow-down"></i> New
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr class="empty-missing-docs">
                                        <td colspan="5" class="text-center">No missing documents found</td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>

                        <!-- New Documents Section -->
                        <h6 class="my-3"><b>New Documents</b></h6>
                        <table class="table text-nowrap table-bordered table-striped" id="new-docs-table">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Document</th>
                                    <th>Date Received</th>
                                    <th>Attach</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($all_docs_param)
                                    @foreach ($all_docs_param as $doc)
                                        @if (!in_array($doc->id, $ClaimNtfAckDocs->pluck('doc_id')->toArray()))
                                            <tr style="text-align: left; vertical-align: middle; border: 1px solid #ddd"
                                                id="new_{{ $doc->id }}" class="new-doc-row">
                                                <td>
                                                    <input type="checkbox" class="check-input ml-3 doc-select"
                                                        name="new_document[]" id="new_ack_doc_{{ $doc->id }}"
                                                        value="{{ $doc->id }}" data-doc-id="{{ $doc->id }}">
                                                </td>
                                                <td style="width:30%;">{{ $doc->doc_name }}</td>
                                                <td>
                                                    <input type="date" class="form-control ack_date"
                                                        name="new_date_received[]"
                                                        id="new_date_received_{{ $doc->id }}"
                                                        data-doc-id="{{ $doc->id }}">
                                                </td>
                                                <td>
                                                    <input type="file" class="form-control doc-file"
                                                        id="new_file_{{ $doc->id }}" name="new_file[]"
                                                        accept=".pdf,.doc,.docx,.png,.jpg"
                                                        data-doc-id="{{ $doc->id }}">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger move-to-missing"
                                                        data-doc-id="{{ $doc->id }}"
                                                        data-doc-name="{{ $doc->doc_name }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                            height="15" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="lucide lucide-circle-alert w-4 h-4" aria-hidden="true">
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                            <line x1="12" x2="12" y1="8"
                                                                y2="12"></line>
                                                            <line x1="12" x2="12.01" y1="16"
                                                                y2="16"></line>
                                                        </svg> <span style="vertical-align: -1px">Missing</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-default btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="ack-later-btn">Save
                            &amp;
                            Continue Later</button>
                        <button type="submit" id="ack-save-btn"
                            class="btn btn-dark mr-2 btn-sm btn-wave waves-effect waves-light">Submit
                            Document</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="sendDocumentEmail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendDocumentEmailLabel">
                        <i class="bx bx-envelope me-2"></i>Email Notification - Claim Documentation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="claimNotificationForm" action="{{ route('claim.notification.sendDocumentEmail') }}"
                    method="POST">
                    @csrf

                    <input type="hidden" name="intimation_no" value="{{ $ClaimRegister->intimation_no }}">
                    <input type="hidden" name="customer_id" value="{{ $ClaimRegister->customer_id }}">

                    <div class="modal-body">
                        <!-- Recipients Section -->
                        <div class="row mb-3">
                            <label for="from" class="col-sm-3 col-form-label fw-bold">
                                <i class="fas fa-users me-1"></i>From:
                            </label>
                            <div class="col-sm-9">
                                <div class="recipient-container">
                                    <i class="bx bx-envelope me-1"></i> {{ $emailFrom }}
                                </div>
                            </div>
                        </div>

                        <!-- Cedant Section -->
                        <div class="row mb-3">
                            <label for="cedant" class="col-sm-3 col-form-label fw-bold">
                                <i class="fas fa-shield-alt me-1"></i>Cedant:
                            </label>
                            <div class="col-sm-9">
                                <span class="recipient-badge cedant-badge"> <i class="bx bx-envelope me-1"></i>
                                    {{ $cedant }} </span>
                            </div>
                        </div>

                        <!-- Contacts Section -->
                        <div class="row mb-3">
                            <label for="recipients" class="col-sm-3 col-form-label fw-bold">
                                <i class="bx bx-user me-1"></i>Recipients:
                            </label>
                            <div class="col-sm-9">
                                <select class="form-inputs select2" name="recipients[]" multiple
                                    placeholder="Select contact">
                                    <option value="" disabled>--Select--</option>
                                    @if ($recipients)
                                        @foreach ($recipients as $recipient)
                                            <option value="{{ $recipient->contact_email }}">
                                                {{ $recipient->contact_name }} ({{ $recipient->contact_email }})</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- Subject Section -->
                        <div class="row mb-3">
                            <label for="subject" class="col-sm-3 col-form-label fw-bold">
                                <i class="bx bx-tag me-1"></i>Subject:
                            </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-inputs" id="subject" name="subject"
                                    value="{{ is_array($claimSubject) ? implode(' ', $claimSubject) : $claimSubject }}" />
                            </div>
                        </div>

                        <!-- Message Section -->
                        <div class="row mb-3">
                            <label for="message" class="col-sm-3 col-form-label fw-bold">
                                <i class="bx bx-comment me-1"></i>Message:
                            </label>
                            <div class="col-sm-9">
                                <textarea class="form-inputs" id="message" name="message" rows="10">{{ $defaultMessage }}</textarea>
                            </div>
                        </div>

                        <!-- Attached Files Section -->
                        <div class="row mb-3">
                            <label for="message" class="col-sm-3 col-form-label fw-bold">
                                <i class="bx bx-paperclip me-1"></i>Attached Files:
                            </label>
                            <div class="col-sm-9">
                                <div id="attachedFilesList" class="attached-files-container">
                                    @if (isset($attachedFiles) && count($attachedFiles) > 0)
                                        @foreach ($attachedFiles as $file)
                                            <div class="file-item d-flex align-items-center mb-2"
                                                data-file-id="{{ $file->id }}">
                                                <div class="file-icon me-3" data-extension={{ $file->extension }}>
                                                    <i class="bx bx-file"></i>
                                                </div>
                                                <div class="file-info flex-grow-1">
                                                    <h6 class="mb-1">{{ $file->original_name }}</h6>
                                                    <div class="file-meta">
                                                        {{ strtoupper($file->extension) }} Document •
                                                        {{ number_format($file->size / 1024, 2) }} KB •
                                                        Uploaded {{ $file->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                                <div class="file-actions">
                                                    <a href="{{ route('docs.claimntf-docs-ack-letter', ['intimation_no' => $ClaimRegister->intimation_no]) }}"
                                                        class='btn btn-outline-secondary btn-sm' target="_blank"
                                                        rel="noopener noreferrer">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div id="noFilesMessage" class="no-files text-center py-4">
                                            <i class="fas fa-paperclip fa-2x mb-2 text-muted"></i>
                                            <p class="text-muted mb-0">No files attached to this claim notification.</p>
                                        </div>
                                    @endif
                                </div>

                                @if (isset($attachedFiles) && count($attachedFiles) > 0)
                                    <!-- File count summary -->
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            <span id="fileCount">{{ count($attachedFiles) }}
                                                file{{ count($attachedFiles) > 1 ? 's' : '' }} attached</span> •
                                            Total size: <span id="totalSize"></span>
                                            {{-- {{ formatTotalFileSize($attachedFiles) }} --}}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-default btn-sm" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="sendNotification">
                            <i class="bx bx-paper-plane me-1"></i>Send Email & Complete
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            var approved = '{!! $ClaimRegister->approval_status !!}';
            var ClaimPerilsCount = '{!! $ClaimPerilsCount !!}';

            $('.status-content').hide();
            $('.reserve-status-content').hide();
            $('.claim-status-content').hide();

            $('.file-icon[data-extension]').each(function() {
                const $container = $(this);
                const extension = $container.data('extension');
                const $icon = $container.find('i.bx');

                // Clear any existing classes and add new ones
                $icon.removeClass().addClass('bx');
                $container.removeClass(function(index, className) {
                    return (className.match(/(^|\s)file-\S+/g) || []).join(' ');
                });

                // Add appropriate icon and styling classes
                const iconClass = getFileIcon(extension);
                const fileClass = getFileIconClass(extension);

                $icon.addClass(iconClass);
                $container.addClass(fileClass);
            });

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            var processType = getUrlParameter('process_type');

            var reserveAmount = '{!! $ClaimRegister->reserve_amount !!}';

            if (parseFloat(reserveAmount) > 0 && processType === 'reserve') {
                $('.cancelProcessClaim').hide();
            }

            $('#process_type').val(processType);
            $('#clm_process_type').val(processType);

            if (processType === 'reserve') {
                $('.status-content').show();
                $('.reserve-status-content').show();
                $('.processReserve').hide();
                $('.processClaim').hide();
                $('.cancelProcessReserve').hide();
            } else if (processType === 'claim') {
                $('.status-content').show();
                $('.claim-status-content').show();
                $('.processReserve').hide();
                $('.processClaim').hide();
                $('.cancelProcessClaim').hide();
            }

            $('#risk-details').click(function(e) {
                $('#perilsModal').modal('show');
                // if (approved == 'A') {
                //     $('#perilsModal').modal('show');
                // } else {
                //     Swal.fire({
                //         title: "Verification Errors",
                //         icon: "error",
                //         html: "Verify first, you have to get approval before capturing claim particulars",
                //         showCloseButton: true,
                //     });
                // }
            });

            function checkDocumentComplete(docId) {
                const newDateReceived = $(`#new_date_received_${docId}`).val();
                const newfileUploaded = $(`#new_file_${docId}`)[0]?.files?.length > 0;

                const missingDateReceived = $(`#missing_date_received_${docId}`).val();
                const missingFileUploaded = $(`#missing_file_${docId}`)[0]?.files?.length > 0;

                const newIsSelected = $(`#new_ack_doc_${docId}`).is(':checked');
                const missingIsSelected = $(`#missing_ack_doc_${docId}`).is(':checked');

                return (newDateReceived && newfileUploaded && newIsSelected) ||
                    (missingDateReceived && missingFileUploaded && missingIsSelected);
            }

            function moveToReceived(docId, docName, dateReceived, fileInput) {
                const clonedFileInput = $(fileInput).clone();
                clonedFileInput
                    .attr('id', `received_file${docId}`)
                    .attr('name', 'received_file[]')
                    .attr('class', 'ack_received_doc')
                    .attr('data-doc-id', docId)
                    .hide();
                const newRow = `
                    <tr id="received_${docId}" class="received-doc-row" style="width: 100%;">
                        <td style="width:50%;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-4 h-4" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg> <span style="vertical-align: -1px">${docName}</span>
                            <input type="hidden" class="ack_received_doc" name="received_document[]"
                            id="received_doc_${docId}" value="${docId}"
                            data-doc-id="${docId}">
                            <input type="hidden" class="ack_received_doc" name="date_received[]"
                            id="date_received${docId}" value="${dateReceived}"
                            data-doc-id="${docId}">
                        <td style="width:25%;">${dateReceived}</td>
                        <td style="width:25%;" class="received-success">
                            <div class="received-success-raw">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big w-4 h-4" aria-hidden="true"><path d="M21.801 10A10 10 0 1 1 17 3.335"></path><path d="m9 11 3 3L22 4"></path></svg> <span style="verticle-align: -1px;">Uploaded</span></div>
                                <div><button class="btn_cancel_upload" type="button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-4 h-4" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button></div>
                                </div>
                            </td>
                    </tr>
                `;
                const $newRow = $(newRow);
                $newRow.find('td:first').append(clonedFileInput);
                const receivedTable = $('#received-docs-table tbody').append($newRow);

                $(`#missing_${docId}, #new_${docId}`).remove();
            }

            function moveToMissing(docId, docName) {
                const newRow = `
                    <tr id="missing_${docId}" class="missing-doc-row">
                        <td>
                            <input type="checkbox" class="check-input ml-3 doc-select"
                                name="missing_document[]"
                                id="missing_ack_doc_${docId}"
                                value="${docId}"
                                data-doc-id="${docId}"/>
                                <input type="hidden" class="missing_document_ids" name="missing_document_ids[]"
                                 id="missing_document_ids_${docId}" value="${docId}" data-doc-id="${docId}"/>
                        </td>
                        <td style="width:30%;">${docName}</td>
                        <td>
                            <input type="date" class="form-control ack_date"
                                name="missing_date_received[]"
                                id="missing_date_received_${docId}"
                                data-doc-id="${docId}">
                        </td>
                        <td>
                            <input type="file" class="form-control doc-file"
                                id="missing_file_${docId}"
                                name="missing_file[]"
                                accept=".pdf,.doc,.docx,.png,.jpg"
                                data-doc-id="${docId}">
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-dark revert-to-new"
                               data-doc-id="${docId}"data-doc-name="${docName}">
                               <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-4 h-4" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg> <span style="vertical-align: -1px">New</span>
                            </button>
                        </td>
                    </tr>
                `;
                $('#missing-docs-table tbody').append(newRow);
                $(`#new_${docId}`).remove();
            }

            function revertToNew(docId, docName) {
                const newRow = `
                    <tr id="new_${docId}" class="new-doc-row">
                        <td>
                            <input type="checkbox" class="check-input ml-3 doc-select"
                                name="new_document[]"
                                id="new_ack_doc_${docId}"
                                value="${docId}"
                                data-doc-id="${docId}">
                        </td>
                        <td style="width:30%;">${docName}</td>
                        <td>
                            <input type="date" class="form-control ack_date"
                                name="new_date_received[]"
                                id="new_date_received_${docId}"
                                data-doc-id="${docId}">
                        </td>
                        <td>
                            <input type="file" class="form-control doc-file"
                                id="file_${docId}"
                                name="new_file[]"
                                accept=".pdf,.doc,.docx,.png,.jpg"
                                data-doc-id="${docId}">
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger move-to-missing"
                                data-doc-id="${docId}" data-doc-name="${docName}">
                                <i class="bi bi-exclamation"></i> Missing
                            </button>
                        </td>
                    </tr>
                `;
                $('#new-docs-table tbody').append(newRow);
                $(`#missing_${docId}`).remove();
            }

            function getDocumentValues(docId) {
                return {
                    dateReceived: $(`#new_date_received_${docId}`).val() || $(`#missing_date_received_${docId}`)
                        .val(),
                    fileInput: $(`#new_file_${docId}`)[0] || $(`#missing_file_${docId}`)[
                        0]
                };
            }

            $(document).on('change', '.doc-select, .ack_date, .doc-file', function() {
                const docId = $(this).data('doc-id');
                const docName = $(this).closest('tr').find('td:eq(1)').text();
                $('.empty-received-docs').hide();

                if (checkDocumentComplete(docId)) {
                    const {
                        dateReceived,
                        fileInput
                    } = getDocumentValues(docId);

                    moveToReceived(docId, docName, dateReceived, fileInput);
                }
            });

            let moveToMissingDocId = [];

            $(document).on('click', '.move-to-missing', function() {
                const docId = $(this).data('doc-id');
                const docName = $(this).data('doc-name');
                moveToMissingDocId = docId;

                $('.empty-missing-docs').hide();

                moveToMissing(docId, docName);
            });

            let revertToNewDocId = [];
            $(document).on('click', '.revert-to-new', function() {
                const docId = $(this).data('doc-id');
                const docName = $(this).data('doc-name');
                revertToNewDocId = docId;
                revertToNew(docId, docName);
            });

            $("#ack-save-btn").on('click', function(e) {
                e.preventDefault();
                const $form = $('#acknowledgementForm')[0];
                const formData = new FormData($form);
                const url = "{!! route('claim.notification.save_doc_acknowledgement') !!}";

                if (revertToNewDocId?.length > 0) {
                    formData.append('revert_to_new[]', revertToNewDocId);
                }
                if (moveToMissingDocId?.length > 0) {
                    formData.append('move_to_missing[]', moveToMissingDocId);
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $("#ack-save-btn").prop('disabled', true).text('Saving...');
                    },
                    success: function(response) {
                        if (response.status === 201) {
                            toastr.success(response.message || "Documents saved");
                            $('#acknowledgementForm').trigger("reset");
                            $("#acknowledgement-modal").modal('hide');
                            $("#sendDocumentEmail").modal('show');
                        } else {
                            toastr.error(response.message ||
                                "Failed to save documents acknowledgement");
                        }
                    },
                    error: function(error) {
                        if (error.status === 422) {
                            const errors = error.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                "An error occurred while saving documents acknowledgement");
                        }
                    },
                    complete: function() {
                        $("#ack-save-btn").prop('disabled', false).text('Submit Document');
                    }
                });
            });

            $("#ack-later-btn").on('click', function(e) {
                e.preventDefault();
                const $form = $('#acknowledgementForm')[0];
                const formData = new FormData($form);
                const url = "{!! route('claim.notification.save_doc_acknowledgement') !!}";

                if (revertToNewDocId?.length > 0) {
                    formData.append('revert_to_new[]', revertToNewDocId);
                }
                if (moveToMissingDocId?.length > 0) {
                    formData.append('move_to_missing[]', moveToMissingDocId);
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $("#ack-later-btn").prop('disabled', true).text('Saving...');
                    },
                    success: function(response) {
                        if (response.status === 201) {
                            toastr.success(response.message || "Documents saved");
                            $('#acknowledgementForm').trigger("reset");
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            toastr.error(response.message ||
                                "Failed to save documents acknowledgement");
                        }
                    },
                    error: function(error) {
                        if (error.status === 422) {
                            const errors = error.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                "An error occurred while saving documents acknowledgement");
                        }
                    },
                    complete: function() {
                        $("#ack-later-btn").prop('disabled', false).text('Submit Document');
                    }
                });
            });


            $(document).on('click', '.btn_cancel_upload', function() {
                const $row = $(this).closest('tr');
                const docId = $row.find('input[name="received_document[]"]').val();
                $row.remove();
            });

            // $('#acknowledgementForm').on('submit', function(e) {
            //     e.preventDefault();
            //     const formData = new FormData(this);
            //     url = "{!! route('claim.notification.save_doc_acknowledgement') !!}"
            //     if (revertToNewDocId?.length > 0) {
            //         formData.append('revert_to_new[]', revertToNewDocId);
            //     }
            //     if (moveToMissingDocId?.length > 0) {
            //         formData.append('move_to_missing[]', moveToMissingDocId);
            //     }

            //     $.ajax({
            //         url,
            //         type: 'POST',
            //         data: formData,
            //         processData: false,
            //         contentType: false,
            //         success: function(response) {
            //             if (response.status === 201) {
            //                 toastr.success(response.message ||
            //                     "Documents acknowledgement saved successfully");
            //                 $('#acknowledgementForm').trigger("reset");
            //                 setTimeout(() => {
            //                     location.reload();
            //                 }, 2000);
            //             } else {
            //                 toastr.error(response.message ||
            //                     "Failed to save documents acknowledgement");
            //             }
            //         },
            //         error: function(error) {
            //             if (error.status === 422) {
            //                 const errors = error.responseJSON.errors;
            //                 Object.keys(errors).forEach(key => {
            //                     toastr.error(errors[key][0]);
            //                 });
            //             } else {
            //                 toastr.error(
            //                     "An error occurred while saving documents acknowledgement");
            //             }
            //         }
            //     });
            // });

            $('#convert-to-claim').click(function(e) {
                try {
                    // Ensure variables are defined and of correct type
                    const isApproved = approved === 'A';
                    const perilsCount = parseInt(ClaimPerilsCount) || 0;
                    // Check conditions in sequence
                    if (!isApproved) {
                        Swal.fire({
                            title: "Verification Required",
                            icon: "warning",
                            html: "Please verify and get approval before converting to claim",
                            showCloseButton: true,
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    if (perilsCount === 0) {
                        Swal.fire({
                            title: "Missing Particulars",
                            icon: "warning",
                            html: "Please capture claim particulars before converting to claim",
                            showCloseButton: true,
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // All conditions met, show modal
                    $('#converToClaimModal').modal('show');

                } catch (error) {
                    Swal.fire({
                        title: "Error",
                        icon: "error",
                        html: "An error occurred while processing your request",
                        showCloseButton: true,
                        confirmButtonText: 'OK'
                    });
                }
            });

            $('#to-customer').click(function(e) {
                $('#customerForm').submit();
            });

            $('.modal').on('shown.bs.modal', function() {
                $('.form-select').select2({
                    dropdownParent: $(this)
                });
            });

            $(document).on('click', '#cancel-peril-data', function() {
                const perilSectionCount = $('.peril-section').length;
                if (perilSectionCount > 1) {
                    // $('#peril_name-0').val('').trigger('change'); // Clear and trigger select2 update
                    // $('#peril_amount-0').val('');

                    // // Remove all appended sections
                    // $('div[id^="peril_section-div"]').remove();
                }
            });

            $(document).on('change', '#peril_name-0', function() {
                $(this).valid()
            });

            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            const ackDate = document.querySelectorAll('.ack_date');
            ackDate.forEach(datePicker => {
                datePicker.setAttribute('max', formattedDate);
            });

            $(document).on('click', '.add-peril-section', function() {
                const lastPerilSection = $('.peril-section:last');
                const prevCounter = lastPerilSection.data('counter')
                const perilName = $(`#peril_name-${prevCounter}`).val()
                const perilAmount = $(`#peril_amount-${prevCounter}`).val()
                if (perilName == null || perilName == '' || perilName == ' ') {
                    toastr.error('Please Type Peril Name', 'Incomplete data')
                    return false
                } else if (perilAmount == null || perilAmount == '' || perilAmount == ' ') {
                    toastr.error('Please Capture Peril Amount', 'Incomplete data')
                    return false
                }

                // Increment the counter
                let counter = prevCounter + 1;
                appendPerilSection(counter, prevCounter)

            });

            $(document).on('click', '.remove-peril-section', function() {
                $(this).closest('.peril-section').remove();
            });

            function appendPerilSection(counter, prevCounter) {
                var btn_class = ''
                var btn_id = ''
                var fa_class = ''
                if (counter == 0) {
                    btn_class = 'btn-primary add-peril-section'
                    btn_id = 'add-peril-section'
                    fa_class = 'bx-plus'
                } else {
                    btn_class = 'btn-danger remove-peril-section'
                    btn_id = 'remove-peril-section'
                    fa_class = 'bx-minus'

                }

                $(document).find(`#peril-section-${prevCounter}`).append(`
                    <div id="peril_section-div">
                        <div id="peril-section-${counter}" data-counter="${counter}" class="peril-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="peril_name-${counter}" class="form-label required">Particular Name</label>
                                    <select name="peril_name[]" id="peril_name-${counter}" class="form-inputs select2 peril_name" data-counter="${counter}" required>
                                        <option value="">--Select Particular--</option>
                                        @foreach ($perilTypes as $perilType)
                                            <option value="{{ $perilType->id }}">{{ $perilType->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 peril_amount">
                                    <label class="form-label required">100 % Particular Amount</label>
                                    <div class="input-group mb-3">
                                        <input type="text" style="width: 80%;" class="form-inputs form-input-group peril_amount" name="peril_amount[]" data-counter="${counter}" id="peril_amount-${counter}" onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required>
                                        <button class="btn ${btn_class}" type="button" id="${btn_id}" style="line-height: 0px; border-radius: 0px;"><i class="bx ${fa_class}"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                $(`#peril_name-${counter}`).select2();
            }


            // Claim documents
            $('#title').change(function(e) {
                e.preventDefault();
                const titleText = $(`#title option:selected`).text()
                $('#attachments-modal #description').val(titleText);

            });

            $(document).on('click', '#attachments', function() {
                $(`#attachmentsForm`)[0].reset();
                $(`#attachmentsForm [name="_method"]`).val('POST');
            });

            $(document).on('click', '.edit-attachment', function() {
                const data = $(this).data('data');
                $(`#attachmentsForm #attachments_id`).val(data.id);
                $(`#attachmentsForm #title`).val(data.doc_id).trigger('change.select2');
                $(`#attachmentsForm #description`).val(data.description);
                $(`#attachmentsForm [name="_method"]`).val('PUT');
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

                $('#attachmentDocumentModal #preview-container').html(element);
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
                        cover_no: "{!! $cover->cover_no !!}",
                        endorsement_no: "{!! $cover->endorsement_no !!}",
                        intimation_no: "{!! $cover->intimation_no !!}",
                        id: id,
                    }
                    if (result.isDismissed) {
                        return false;
                    }
                    // subit commit request
                    fetchWithCsrf("{!! route('claim.notification.delete_attachment') !!}", {
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

            $(document).on('click', '#verify-reserve, #verify-claim', function(e) {
                e.preventDefault();
                const buttonTxt = $(this).text();
                const processType = $('#process_type').val();

                $(this).prop('disabled', true).text('Pre-verification check...');
                const data = {
                    'intimation_no': '{!! $ClaimRegister->intimation_no !!}',
                    'process_type': processType
                };

                fetchWithCsrf("{!! route('claim.notification.preNotificationVerification') !!}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.pending.length > 0) {
                            let errors = `<ul class="ver_error-list">`
                            for (const msg of data.pending) {
                                errors += `<li class="ver_error-item">${msg}</li>`
                            }
                            errors += `</ul>`

                            Swal.fire({
                                title: "Pre-verification Errors",
                                icon: "error",
                                html: errors,
                                showCloseButton: true,
                            });
                            // $('#verify').prop('disabled', false).text(buttonTxt);
                        } else {
                            if (data.reversal_status == 'A') {
                                $('#approver').hide().next(".select2-container").hide();
                                $('#approved').show();
                                $('#approved_hidden').val('Y');
                            } else if (data.verifiers.length > 0) {
                                $('#approved').hide();
                                $('#approved_hidden').val('N');
                                $('#approver').empty(); // Clear existing options
                                $('#approver').append(`<option value="">--Select Approver --</option>`);
                                data.verifiers.forEach(verifier => {
                                    $('#approver').append(
                                        `<option value="${verifier.id}">${verifier.name}</option>`
                                    );
                                });
                                $('#approver').trigger('change.select2')
                            }
                            $('#verify-modal').modal('show');

                            // Set process and process_action if needed
                            if (data.process && data.verifyprocessAction) {
                                $('#process').val(data.process.id);
                                $('#process_action').val(data.verifyprocessAction.id);
                            }
                        }
                    })
                    .catch(error => {
                        toastr.error('Failed to load pre-verification checks.');
                    })
                    .finally(() => {
                        $(this).prop('disabled', false).text(buttonTxt);
                    });
            });

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
                    approver: {
                        required: true
                    },
                    comment: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#verify-save-btn').prop('disabled', true).text('Saving...')
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
                            toastr.error("Failed to send verification request")
                            $('#verify-save-btn').prop('disabled', false).text('Submit')
                        });
                }
            })

            // attachments schedule
            $("#attachmentsForm").validate({
                errorClass: "errorClass",
                rules: {
                    title: {
                        required: true
                    },
                    description: {
                        required: true
                    },
                    file: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#attachments-save-btn').prop('disabled', true).text('Saving...')
                    // Make a fetch request
                    let url = '';
                    let HttpMethod = $('#attachmentsForm [name="_method"]').val()
                    if (HttpMethod == 'POST') {
                        url = "{!! route('claim.notification.save_attachment') !!}"
                    } else if (HttpMethod == 'PUT') {
                        url = "{!! route('claim.notification.amend_attachment') !!}"
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
                            // Handle success
                            $('#attachmentsModal').modal('hide');
                            if (data.status == 201) {
                                toastr.success("Document saved Successfully")

                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)
                                $('#attachments-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to save document")
                                $('#attachments-save-btn').prop('disabled', false).text('Submit')
                            }
                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error:', error);
                            toastr.error("Failed to save document")
                            $('#attachments-save-btn').prop('disabled', false).text('Submit')
                        });
                }
            })

            $("#statusForm").validate({
                errorClass: "errorClass",
                rules: {
                    status: {
                        required: true
                    },
                    description: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#status-save-btn').prop('disabled', true).text('Saving...')
                    // Make a fetch request
                    let url = ''
                    let HttpMethod = $('#statusForm [name="_method"]').val()
                    if (HttpMethod == 'POST') {
                        url = "{!! route('claim.notification.saveClaimStatus') !!}"
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
                            // Handle success
                            $('#status-modal').modal('hide');
                            if (data.status == 201) {
                                toastr.success("Status saved Successfully")

                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)
                                $('#status-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to update Claim Status")
                                $('#status-save-btn').prop('disabled', false).text('Submit')
                            }
                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error:', error);
                            toastr.error("Failed to Update Claim Status")
                            $('#status-save-btn').prop('disabled', false).text('Submit')
                        });
                }
            })

            $("#save_peril").validate({
                errorClass: "errorClass",
                rules: {
                    "peril_name[]": {
                        required: true
                    },
                    "peril_amount[]": {
                        required: true,
                        min: 0.01,
                        normalizer: function(value) {
                            return value.replace(/,/g, '');
                        }
                    }
                },
                messages: {
                    "peril_amount[]": {
                        required: "Please specify particular amount",
                        min: "Please specify a valid particular amount greater than zero"
                    },
                    "peril_name[]": {
                        required: "Please select particular name",
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.card-md'));
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form, e) {
                    e.preventDefault();
                    const submitBtn = $("#peril-save-btn");
                    const originalText = submitBtn.html();
                    submitBtn.html(
                        '<span class="me-2">Processing ...</span><div class="loading"></div>'
                    );
                    submitBtn.prop('disabled', true);
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Claim particulars saved successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        },
                        error: function(xhr) {
                            let errorMessage =
                                'An error occurred while saving the perils amount';
                            Swal.fire({
                                title: 'Error',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        },
                        complete: function() {
                            submitBtn.html(originalText);
                            submitBtn.prop('disabled', false);
                        }
                    });
                }
            });

            $("#save_peril").on("input", function() {
                let value = $(this).val().replace(/,/g, '');
                $(this).val(numberWithCommas(value));
            });

            $("#save_reserve").validate({
                rules: {
                    reserve_amount: {
                        required: true,
                        min: 0.01,
                        normalizer: function(value) {
                            return value.replace(/,/g, '');
                        }
                    }
                },
                messages: {
                    reserve_amount: {
                        required: "Please specify a reserve amount",
                        min: "Please specify a valid reserve amount greater than zero"
                    }
                },
                errorPlacement: function(error, element) {
                    error.addClass("text-danger small mt-1");
                    var $group = element.closest('.input-group');
                    if ($group.length) {
                        error.insertAfter($group);
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form, e) {
                    e.preventDefault();
                    const submitBtn = $("#reserve-save-btn");
                    const originalText = submitBtn.html();
                    submitBtn.html(
                        '<span class="me-2">Processing ...</span><div class="loading"></div>'
                    );
                    submitBtn.prop('disabled', true);

                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Reserve amount saved successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        },
                        error: function(xhr) {
                            let errorMessage =
                                'An error occurred while saving the reserve amount';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                const errorList = Object.values(errors).flat();

                                Swal.fire({
                                    title: 'Pre-verification Errors',
                                    icon: 'error',
                                    html: `
                                <ul class="error-list">
                                    ${errorList.map(error => `<li class="error-item">${error}</li>`).join('')}
                                </ul>
                                `,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        popup: 'modal',
                                        title: 'modal-title',
                                        htmlContainer: 'modal-body',
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        complete: function() {
                            submitBtn.html(originalText);
                            submitBtn.prop('disabled', false);
                        }
                    });
                }
            });

            $.validator.addMethod("decimalWithCommas", function(value, element) {
                return this.optional(element) || /^(\d{1,3}(,\d{3})*|\d+)(\.\d+)?$/.test(value);
            }, "Please enter a valid number");

            $("#reserve_amount").on("input", function() {
                let value = $(this).val().replace(/,/g, '');
                $(this).val(numberWithCommas(value));
            });

            // $("#save_reserve").validate({
            //     errorPlacement: function(error, element) {
            //         error.addClass("text-danger"); // Add red color to the error message
            //         error.insertAfter(element);
            //     },
            //     highlight: function(element) {
            //         $(element).addClass('error').removeClass('valid');
            //     },
            //     unhighlight: function(element) {
            //         $(element).removeClass('error').addClass('valid');
            //     },
            //     submitHandler: function(form, e) {
            //         e.preventDefault();
            //         form.submit();
            //     }
            // });

            $('#perils-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.notification.peril_datatable') !!}",
                    data: function(d) {
                        d.intimation_no = "{!! $ClaimRegister->intimation_no !!}";
                    }
                },
                columns: [{
                        data: 'tran_no',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'peril_name',
                        searchable: true
                    },
                    {
                        data: 'basic_amount',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    {
                        data: 'rate',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    {
                        data: 'final_amount',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    // { data: 'action', searchable: false  },
                ]
            });

            $('#reinsurers-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.notification.reinsurers_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $ClaimRegister->endorsement_no !!}";
                    }
                },
                columns: [{
                        data: 'tran_no',
                        searchable: true,
                        className: 'highlight-idx',
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
                        data: 'credit_no',
                        searchable: true,
                    },
                    {
                        data: 'share',
                        searchable: true,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    {
                        data: 'sum_insured',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    }, {
                        data: 'premium',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    },
                    {
                        data: 'premium',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    }, {
                        data: 'commission',
                        searchable: false,
                        render: $.fn.dataTable.render.number(',', '.', 2, '')
                    }, {
                        data: 'action',
                        searchable: false,
                        sortable: false,
                        className: 'highlight-view'
                    },
                ],
                paging: false,
                drawCallback: function(settings) {
                    $('#reinsurers-table tfoot').empty();
                    const api = this.api();
                    const businessType = "{!! $cover->type_of_bus !!}";
                    const transactionType = "{!! $cover->transaction_type !!}";

                    let columnsToSum = [3];
                    if (businessType === 'FPR' || businessType === 'FNP') {
                        // Sum insured, premium, commission, brokerage_comm_amt, wht_amt, fronting_amt
                        columnsToSum = columnsToSum.concat([4, 5, 6, 7]);
                    } else if (businessType === 'TPR' && !['NEW', 'REN'].includes(transactionType)) {
                        // Premium, commission, claim_amt, prem_tax, ri_tax
                        columnsToSum = columnsToSum.concat([4, 5, 6, 7]);
                    } else if (businessType === 'TNP' && !['NEW', 'REN'].includes(transactionType)) {
                        // total_mdp_amt, mdp_amt
                        columnsToSum = columnsToSum.concat([3, 4]);
                    }

                    // Create the footer row HTML
                    let footerRow = '<tr>';
                    footerRow +=
                        '<td colspan="3" style="text-align:right !important; font-weight:bold; color: #000;">Totals:</td>';
                    // Calculate the sum for each column and add to footer
                    const columns = api.columns().nodes().length;
                    for (let i = 3; i < columns - 1; i++) {
                        if (columnsToSum.includes(i)) {
                            // Sum this column
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

                            // Format the sum with commas
                            const formattedSum = $.fn.dataTable.render.number(',', '.', 2, '').display(
                                sum);

                            footerRow +=
                                '<td style="font-weight:bold; padding: 6px 8px; color: #000;">' +
                                formattedSum + '</td>';
                        } else {
                            // Empty cell for non-summed columns
                            footerRow += '<td></td>';
                        }
                    }

                    // Add empty cell for action column
                    footerRow += '<td></td>';
                    footerRow += '</tr>';

                    // Add the footer row
                    if (!$('#reinsurers-table tfoot').length) {
                        $('#reinsurers-table').append('<tfoot></tfoot>');
                    }
                    $('#reinsurers-table tfoot').html(footerRow);

                    // Style the footer
                    $('#reinsurers-table tfoot tr').css({
                        'background-color': '#f5f5f5',
                        'border-top': '2px solid #ddd'
                    });
                }
            });

            $('#debits-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.notification.debit_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $ClaimRegister->endorsement_no !!}",
                            d.cover_no = "{!! $ClaimRegister->cover_no !!}";
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
                        className: 'highlight-view'
                    },
                ],
                paging: false,
                drawCallback: function(settings) {
                    $('#debits-table tfoot').empty();
                    const api = this.api();
                    // Define columns to sum (numeric columns only)
                    // Column indices: share(4), sum_insured(5), premium(6), gross(7), net_amt(8)
                    const columnsToSum = [4, 5, 6, 7, 8];

                    // Create the footer row HTML
                    let footerRow = '<tr>';
                    footerRow +=
                        '<td colspan="4" style="text-align:right !important; font-weight:bold; color: #000;">Totals:</td>';

                    // Calculate the sum for each column and add to footer
                    const columns = api.columns().nodes().length;
                    for (let i = 4; i < columns - 1; i++) {
                        if (columnsToSum.includes(i)) {
                            // Sum this column
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

                            // Format the sum with commas
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

            const $attTbl = $('#attachments-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.notification.attachments_datatable') !!}",
                    data: function(d) {
                        d.intimation_no = "{!! $ClaimRegister->intimation_no !!}";
                    }
                },
                columns: [{
                        data: 'id',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        className: 'highlight-idx'
                    },
                    {
                        data: 'title',
                        searchable: true,
                        className: 'highlight-description'
                    },
                    {
                        data: 'filename',
                        searchable: true,
                    },
                    {
                        data: 'recieved_date',
                        searchable: true
                    },
                    {
                        data: 'type',
                        searchable: true
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false
                    },
                ]
            });

            $attTbl.on('click', '.view-document', function(e) {
                e.preventDefault();

                const $button = $(this);
                const documentId = $(this).data('document-id');
                const filename = $(this).data('filename');
                const url = $(this).data('url');

                if (!documentId) {
                    toastr.error('Document ID is missing');
                    return;
                }

                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

                try {
                    const newTab = window.open('', '_blank');
                    if (!newTab) {
                        toastr.error('Popup blocked. Please allow popups for this site.');
                        return;
                    }

                    if (filename) {
                        newTab.document.title = filename;
                    }

                } catch (error) {
                    console.error('Error opening document viewer:', error);
                    toastr.error('Failed to open document viewer');
                } finally {
                    $button.prop('disabled', false).html('<i class="fas fa-eye"></i> View');
                }
            });

            const statusTable = $('#status-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.notification.claimStatusDatatable') !!}",
                    data: function(d) {
                        d.intimation_no = "{!! $ClaimRegister->intimation_no !!}";
                    }
                },
                columns: [{
                        data: 'status_id',
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'status',
                        searchable: true
                    },
                    {
                        data: 'status_reason.description',
                        searchable: true
                    },
                    {
                        data: 'description',
                        searchable: true
                    },
                    {
                        data: 'created_by',
                        searchable: true
                    },
                    {
                        data: 'created_at',
                        searchable: true
                    },
                    {
                        data: 'action',
                        searchable: false,
                        sortable: false
                    },
                ]
            });

            $('#attachments-save-btn').click(function(e) {
                $('#attachmentsForm').submit()
            });

            $('#status-save-btn').click(function(e) {
                $('#statusForm').submit()
            });

            $("#convertToClaimForm").validate({
                errorClass: "errorClass",
                rules: {
                    intimation_no: {
                        required: true
                    },
                    cover_no: {
                        required: true
                    },
                    date_of_loss: {
                        required: true
                    },
                    total_claim_amnt: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    let url = $(form).attr('action');
                    let formData = new FormData(form);
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#convert-clm-save-btn').prop('disabled', true)
                                .text(
                                    'Converting...');
                        },
                        success: function(response) {
                            if (response.status === 201) {
                                $('#converToClaimModal').modal('hide');
                                toastr.success(
                                    "Successfully converted to claim");
                                $("#clm_claim_no").val(response.claim_no);
                                $('#claimForm').submit();
                            } else if (response.status === 422) {
                                showServerSideValidationErrors(response.errors);
                            } else {
                                toastr.error(response.message ||
                                    "Failed to convert to claim");
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error(
                                "An error occurred while converting to claim"
                            );
                        },
                        complete: function() {
                            $('#convert-clm-save-btn').prop('disabled', false)
                                .text(
                                    'Convert to Claim');
                        }
                    });
                }
            })

            $(document).on('click', '.processReserve, .cancelProcessReserve', function(e) {
                e.preventDefault();
                let currentUrl = new URL(window.location.href);
                let processType = currentUrl.searchParams.get('process_type');
                currentUrl.searchParams.set('process_type', 'reserve');

                $('.status-content').show();
                // $('.processReserve').hide();
                // $('.processClaim').hide();
                $('.cancelProcessClaim').hide();

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to process as reserve, the page will reload?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    reverseButtons: true,
                    focusCancel: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.history.pushState({}, '', currentUrl);
                        window.location.href = currentUrl;
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // $('.cancelProcessClaim').show();
                        // $('.cancelProcessReserve').show();
                        Swal.fire({
                            title: 'Cancelled',
                            icon: 'info',
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true
                        });
                    }
                });
            });

            $(document).on('click', '.processClaim, .cancelProcessClaim', function(e) {
                e.preventDefault();
                let currentUrl = new URL(window.location.href);
                let processType = currentUrl.searchParams.get('process_type');
                currentUrl.searchParams.set('process_type', 'claim');

                $('.status-content').show();
                // $('.processReserve').hide();
                // $('.processClaim').hide();
                $('.cancelProcessReserve').hide();

                // Show confirmation message with toastr
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to process as claim, the page will reload?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    reverseButtons: true,
                    focusCancel: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.history.pushState({}, '', currentUrl);
                        window.location.href = currentUrl;
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // $('.cancelProcessClaim').show();
                        // $('.cancelProcessReserve').show();
                        Swal.fire({
                            title: 'Cancelled',
                            icon: 'info',
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true
                        });
                    }
                });
            });

            $('#claimNotificationForm').validate({
                rules: {
                    subject: {
                        required: true
                    },
                    message: {
                        required: true
                    },
                    recipients: {
                        required: true
                    }
                },
                messages: {
                    recipients: {
                        required: "Please select at least one recipient."
                    }
                },
                errorPlacement: function(error, element) {
                    error.addClass("text-danger small mt-1");
                    var $group = element.closest('.input-group');
                    if ($group.length) {
                        error.insertAfter($group);
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        url: $(form).attr('action'),
                        type: $(form).attr('method'),
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#sendNotification').prop('disabled', true)
                                .html(
                                    '<span class="spinner-border spinner-border-sm me-2"></span>Sending...'
                                );
                        },
                        success: function(response) {
                            toastr.success(response.message ||
                                'Notification sent successfully');
                            $('#sendNotification').prop('disabled', false)
                                .html(
                                    '<i class="bx bx-paper-plane me-1"></i>Send Email & Complete'
                                );
                            $('#claimNotificationForm')[0].reset();
                            $('#sendDocumentEmail').modal('hide');
                            window.location.reload()
                        },
                        error: function(xhr) {
                            toastr.error('An error occurred while submitting the form');
                            $('#sendNotification').prop('disabled', false)
                                .html(
                                    '<i class="bx bx-paper-plane me-1"></i>Send Email & Complete'
                                );
                        },
                        complete: function() {
                            $('#sendNotification').prop('disabled', false)
                                .html(
                                    '<i class="bx bx-paper-plane me-1"></i>Send Email & Complete'
                                );
                        }
                    });
                    return false;
                }
            });

            function getFileIconClass($extension) {
                const iconClasses = {
                    pdf: 'text-danger',
                    doc: 'text-primary',
                    docx: 'text-primary',
                    xls: 'text-success',
                    xlsx: 'text-success',
                    jpg: 'text-warning',
                    jpeg: 'text-warning',
                    png: 'text-warning',
                    gif: 'text-warning',
                    txt: 'text-secondary',
                    zip: 'text-info',
                    rar: 'text-info',
                };

                return iconClasses[String($extension).toLowerCase()] ?? 'text-muted';
            }

            function getFileIcon(extension) {
                const icons = {
                    pdf: 'bx-file',
                    doc: 'bx-file',
                    docx: 'bx-file',
                    xls: 'bx-file',
                    xlsx: 'bx-file',
                    jpg: 'bx-image',
                    jpeg: 'bx-image',
                    png: 'bx-image',
                    gif: 'bx-image',
                    txt: 'bx-file',
                    zip: 'bx-archive',
                    rar: 'bx-archive',
                };

                return icons[String(extension).toLowerCase()] || 'fa-file';
            }

            function formatFileSize(bytes) {
                if (bytes >= 1073741824) {
                    return (bytes / 1073741824).toFixed(2) + ' GB';
                } else if (bytes >= 1048576) {
                    return (bytes / 1048576).toFixed(2) + ' MB';
                } else if (bytes >= 1024) {
                    return (bytes / 1024).toFixed(2) + ' KB';
                } else {
                    return bytes + ' bytes';
                }
            }

            function downloadFile(fileId, fileName) {
                // Create download link
                const link = document.createElement('a');
                link.href = `/files/${fileId}/download`;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        })
    </script>
@endpush
