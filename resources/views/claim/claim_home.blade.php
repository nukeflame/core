@extends('layouts.app')

@section('content')
    <style>
        .disabled {
            cursor: none !important;
        }

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
        <h1 class="page-title fw-semibold fs-18 mb-0">Claim Details</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">Client</li>
                    <li class="breadcrumb-item"><a href="#" id="to-customer">{{ $customer->name }}</a></li>
                    <li class="breadcrumb-item">Claim</li>
                    <li class="breadcrumb-item"><a href="#" id="to-cover">{{ $ClaimRegister->claim_no }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Claim Details</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header Close -->

    <form action="{{ route('endorsements_list') }}" method="post" id="coverForm">
        @csrf
        <input type="hidden" name="cover_no" value="{{ $ClaimRegister->claim_no }}">
        <input type="hidden" name="customer_id" value="{{ $ClaimRegister->customer_id }}">
    </form>
    <form action="{{ route('customer.dtl') }}" method="post" id="customerForm">
        @csrf
        <input type="hidden" name="customer_id" value="{{ $ClaimRegister->customer_id }}">
    </form>

    <div class="row-cols-12">
        @if ($ClaimRegister->verified != 'A')
            <div class="card mb-2 border col">
                <div class="card-body">
                    <button type="button" class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light"
                        disabled id="acknowledgements" data-bs-toggle="modal" data-bs-target="#acknowledgement-modal">
                        <i class="bx bx-check me-1 align-middle"></i> Document Acknowledgement
                    </button>
                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" disabled
                        id="attachments" data-bs-toggle="modal" data-bs-target="#attachments-modal">
                        <i class="bx bx-file me-1 align-middle"></i> Upload Document
                    </button>
                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" disabled
                        id="risk-details" data-bs-toggle="modal" data-bs-target="#perilsModal">
                        <i class="bx bx-plus me-1 align-middle"></i> Claim Particulars
                    </button>
                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" disabled
                        id="verify" data-bs-toggle="modal" data-bs-target="#status-modal">
                        <i class="bx bx-edit me-1 align-middle"></i> Update Claim Status
                    </button>
                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" id="verify"
                        data-bs-toggle="modal" data-bs-target="#verify-modal" disabled>
                        <i class="bx bx-check me-1 align-middle"></i> Verify Details
                    </button>
                    <button class="btn btn-outline-dark mr-2 btn-sm btn-wave waves-effect waves-light" id="debit"
                        data-bs-toggle="modal" data-bs-target="#debit-modal">
                        <i class="bx bx-analyse me-1 align-middle"></i> Generate Debit
                    </button>
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
                        <strong>Claim Number</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $ClaimRegister->claim_no }}
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
                    <div class="col-md-2">
                        <strong>Reserve Amount</strong>
                    </div>
                    <div class="col-md-3">
                        {{ number_format($ClaimRegister->reserve_amount, 2) }}
                    </div>
                </div>
                <div class="row mb-2 p-2">
                    @if (in_array($cover->type_of_bus, ['FPR', 'FNP']))
                        <div class="col-md-2 text-left">
                            <strong>Insured</strong>
                        </div>
                        <div class="col-md-3">
                            {{ $ClaimRegister->insured_name }}
                        </div>
                    @endif
                    <div class="col-md-2">
                        <strong>Total Claims Amount</strong>
                    </div>
                    <div class="col-md-3">
                        {{ number_format($nextDebitAmount, 2) }}
                    </div>
                </div>
                <div class="row mb-3 bg-light p-2 text-left">
                    <div class="col-md-2">
                        <strong>Status</strong>
                    </div>
                    <div class="col-md-3">
                        @if ($ClaimRegister->status == 'A')
                            Active
                        @else
                            Not Active
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
                                <div class="table-responsive">
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
                    </div>
                    <div class="tab-pane active show" id="attachments-tab" role="tabpanel"
                        aria-labelledby="nav-attachments-tab" tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
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
                    </div>
                    <div class="tab-pane" id="status-tab" role="tabpanel" aria-labelledby="nav-status-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
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
                    </div>
                    <div class="tab-pane" id="perils-tab" role="tabpanel" aria-labelledby="nav-perils-tab"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">

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
                    </div>
                    <div class="tab-pane" id="debits-tab" role="tabpanel" aria-labelledby="nav-debits-table"
                        tabindex="0">
                        <div class="card">
                            <div class="card-body py-3 px-2">
                                <div class="table-responsive">
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
                                    <i class="bx bx-file"></i> Acknowledgement Letter
                                </a>
                                {{-- $btn .= '<a href="' . $rein_url . '" target="_blank" rel="noopener noreferrer" class="link me-2">
                                    <i class="bx bx-file"></i> Debit Notes
                              </a>'; --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Reinsurer Modal -->
    <div class="modal effect-scale md-wrapper" id="perilsModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <form id="save_peril" action="{{ route('claim.saveperil') }}" method="post">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="claim_no" value="{{ $ClaimRegister->claim_no }}">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white text-center" id="staticBackdropLabel">Capture Claim Perils</h5>
                        <button type="button" class="btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div id="treaty-div">
                            <div class="peril-sections mb-2 mt-2 p-2" id="peril-sections-0" data-counter="0"
                                style="border: 1px solid #333">
                                <div id="peril_section-div">
                                    <div id="peril-section-0" data-counter="0" class="peril-section">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="">Particular Name</label>
                                                <select name="peril_name[]" id="peril_name-0"
                                                    class="form-select form-select-sm peril_name" data-counter="0"
                                                    required>
                                                    <option value="">--Select Particular Name--</option>
                                                    @if ($perilTypes)
                                                        @foreach ($perilTypes as $perilType)
                                                            <option value="{{ $perilType->id }}">
                                                                {{ $perilType->description }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-sm-3 peril_amount">
                                                <label class="required ">100 % Particular Amount</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control peril_amount"
                                                        name="peril_amount[]" data-counter="0" id="peril_amount-0"
                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                        change="this.value=numberWithCommas(this.value)" required>
                                                    <button class="btn btn-primary add-peril-section" type="button"
                                                        id="add-peril-section-0"><i class="fa fa-plus"></i></button>
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
                        <button type="submit" id="peril-save-btn"
                            class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Verify Modal -->
    <div class="modal effect-scale md-wrapper" id="verify-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="verifyForm" action="{{ route('approvals.send-for-approval') }}">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="process" value="{{ $process->id }}">
                    <input type="hidden" name="process_action" value="{{ $verifyprocessAction->id }}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Send to Verifier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <label for="">Comment</label>
                                <textarea name="comment" id="verify-comment" class="form-control form-control-sm" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="verify-save-btn"
                            class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cedant Modal -->
    <div class="modal effect-scale md-wrapper" id="debit-modal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="debitForm" action="{{ route('claim.generate-debit') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white text-center" id="staticBackdropLabel">Create A Credit Note</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="claim_no" class="form-label">Claim Number</label>
                                <input type="text" class="form-inputs" id="claim_no" name="claim_no"
                                    value="{{ $ClaimRegister->claim_no }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="cover_no" class="form-label">Cover</label>
                                <input type="text" class="form-inputs" id="cover_no" name="cover_no"
                                    value="{{ $cover->cover_no }}" readonly>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="endorsement_no" class="form-label">Endorsement</label>
                                <input type="text" class="form-inputs" id="endorsement_no" name="endorsement_no"
                                    value="{{ $cover->endorsement_no }}" readonly>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="" class="form-label">Amount</label>
                                <input type="text" name="amount" id="amount" class="form-inputs amount"
                                    value="{{ number_format($nextDebitAmount, 2) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="debit-save-btn"
                            class="btn btn-dark btn-sm btn-wave waves-effect waves-light">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Attachments preview --}}
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
                    <input type="hidden" name="claim_no" value="{{ $ClaimRegister->claim_no }}">
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="id" id="attachments_id" value="">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Document Upload</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="title" class="form-label">Date Received</label>
                                <input type="date" class="form-control" name="date_received" id="date_received"
                                    required>

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
                            class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
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
                    <input type="hidden" name="claim_no" value="{{ $ClaimRegister->claim_no }}">
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="id" id="id" value="">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white text-center" id="staticBackdropLabel">Claim Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class=" col-md-12 mb-3">
                                <label for="title" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">--Select Status--</option>
                                    @foreach ($clmStatuses as $clmStatus)
                                        <option value="{{ $clmStatus->id }}">{{ $clmStatus->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" col-md-12 mb-3">
                                <label for="title" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" cols="30" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="status-save-btn"
                            class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="acknowledgement-modal" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="acknowledgementForm">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="claim_no" value="{{ $ClaimRegister->claim_no }}">
                    <input type="hidden" name="endorsement_no" value="{{ $cover->endorsement_no }}">
                    <input type="hidden" name="id" id="acknowledgement_id" value="{{ $cover->endorsement_no }}">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-center" id="staticBackdropLabel">Document Checklist</h5>
                        <button type="button" class="btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p><strong>Select the dates when the documents were received</strong></p>
                        <!-- Received Documents Section -->
                        <h6> <b> Received Documents </b> </h6>
                        <table class="table text-nowrap table-bordered table-striped">
                            <thead>
                                <th>Document</th>
                                <th>Date Received</th>
                                {{-- <th>File</th> --}}
                            </thead>
                            <tbody>
                                @foreach ($ClaimAckDocs as $doc)
                                    @if ($doc->date_received && $doc->file)
                                        <tr style="text-align: left; vertical-align: middle; border: 1px solid #ddd">
                                            <td>{{ $doc->doc_name }}</td>
                                            <td>{{ $doc->date_received }}</td>
                                            {{-- <td><a href="{{ asset('storage/' . $doc->file) }}" target="_blank">View File</a></td> --}}
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <hr />
                        <!-- Missing Documents Section -->
                        <h6> <b> Missing Documents </b> </h6>
                        <table class="table text-nowrap table-bordered table-striped">
                            <thead>
                                <th>Select</th>
                                <th>Document</th>
                                <th>Date Received</th>
                                <th>Attach</th>
                            </thead>
                            <tbody>
                                @foreach ($ClaimAckDocs as $doc)
                                    @if (!$doc->date_received || !$doc->file)
                                        <tr style="text-align: left; vertical-align: middle; border: 1px solid #ddd">
                                            <td>
                                                <input type="checkbox" class="check-input ml-3" name="document[]"
                                                    id="ack_doc_{{ $doc->id }}" value="{{ $doc->doc_id }}">
                                            </td>
                                            <td>{{ $doc->doc_name }}</td>
                                            <td>
                                                <input type="date" class="form-control ack_date"
                                                    name="date_received[]" id="date_received_{{ $doc->doc_id }}">
                                            </td>
                                            <td>
                                                <input type="file" class="form-control" id="file_{{ $doc->id }}"
                                                    name="file[]" accept=".pdf, .doc, .docx,.png,.jpg">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <!-- New Documents Section -->
                        <h6> <b> New Documents </b> </h6>
                        <table class="table text-nowrap table-bordered table-striped">
                            <thead>
                                <th>Select</th>
                                <th>Document</th>
                                <th>Date Received</th>
                                <th>Attach</th>
                            </thead>
                            <tbody>
                                @foreach ($ack_docs as $doc)
                                    @if (!in_array($doc->id, $ClaimAckDocs->pluck('doc_id')->toArray()))
                                        <tr style="text-align: left; vertical-align: middle; border: 1px solid #ddd">
                                            <td>
                                                <input type="checkbox" class="check-input ml-3" name="document[]"
                                                    id="ack_doc_{{ $doc->id }}" value="{{ $doc->id }}">
                                            </td>
                                            <td>{{ $doc->doc_name }}</td>
                                            <td>
                                                <input type="date" class="form-control ack_date"
                                                    name="date_received[]" id="date_received_{{ $doc->id }}">
                                            </td>
                                            <td>
                                                <input type="file" class="form-control" id="file_{{ $doc->id }}"
                                                    name="file[]" accept=".pdf, .doc, .docx,.png,.jpg">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="ack-save-btn"
                            class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="sendReinDocumentEmail" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white text-center" id="sendReinDocumentEmailLabel">
                        <i class="bx bx-envelope me-2 fs-15" style="vertical-align: middle"></i>Email Notification (To
                        Reinsurers) - Claim
                        Documentation
                    </h5>
                    <button type="button" class="btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <!-- Navigation Tabs -->
                            <div class="card-header bg-light border-bottom">
                                <ul class="nav nav-tabs card-header-tabs" id="emailTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="compose-tab" data-bs-toggle="tab"
                                            data-bs-target="#compose" type="button" role="tab">
                                            <i class="bx bx-envelope me-2 fs-15"
                                                style="vertical-align: middle"></i>Compose
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="replies-tab" data-bs-toggle="tab"
                                            data-bs-target="#replies" type="button" role="tab">
                                            <i class="bx bx-reply me-2 fs-15" style="vertical-align: middle"></i>Reply to
                                            Messages
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content" id="emailTabContent">
                                <div class="tab-pane fade show active" id="compose" role="tabpanel">
                                    @include('claim.emails.reinsurers.compose-form')
                                </div>

                                <div class="tab-pane fade" id="replies" role="tabpanel">
                                    {{-- @include('claim.emails.reinsurers.messages-list') --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2">Processing...</div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal effect-scale md-wrapper" id="sendCedDocumentEmail" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white text-center" id="sendCedDocumentEmailLabel">
                        <i class="bx bx-envelope me-2 fs-15" style="vertical-align: middle"></i>Email Notification (To
                        Cedant) - Claim
                        Documentation
                    </h5>
                    <button type="button" class="btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <!-- Navigation Tabs -->
                            <div class="card-header bg-light border-bottom">
                                <ul class="nav nav-tabs card-header-tabs" id="emailTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="compose-tab" data-bs-toggle="tab"
                                            data-bs-target="#compose" type="button" role="tab">
                                            <i class="bx bx-envelope me-2 fs-15"
                                                style="vertical-align: middle"></i>Compose
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="replies-tab" data-bs-toggle="tab"
                                            data-bs-target="#replies" type="button" role="tab">
                                            <i class="bx bx-reply me-2 fs-15" style="vertical-align: middle"></i>Reply to
                                            Messages
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content" id="emailTabContent">
                                <!-- Compose Tab -->
                                <div class="tab-pane fade show active" id="compose" role="tabpanel">
                                    @include('claim.emails.cedant.compose-form')
                                </div>

                                <!-- Replies Tab -->
                                <div class="tab-pane fade" id="replies" role="tabpanel">
                                    @include('claim.emails.cedant.messages-list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Conversation Modal --}}
    <div class="modal effect-scale" id="conversationModal" tabindex="-1" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        style="width: 97%; overflow: hidden; white-space: nowrap;text-overflow: ellipsis;">
                        <i class="bx bx-conversation me-2"></i>
                        <span id="conversationTitle">Conversation Title</span>
                        <span class="badge bg-primary ms-2" id="conversationMessageCount">0 messages</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="conversation-thread" id="conversationThread">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="replyToConversationBtn">
                        <i class="bx bx-reply me-1"></i> Reply to Conversation
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal effect-scale md-wrapper" id="sendDocumentEmail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white text-center" id="sendDocumentEmailLabel">
                        <i class="bx bx-envelope me-2"></i>Email Notification - Claim Documentation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
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
                                                {{ $recipient->contact_name }} ({{ $recipient->contact_email }})
                                            </option>
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
                                            <p class="text-muted mb-0">No files attached to this claim notification.
                                            </p>
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
                                            {{-- {{ formatTotalFileSize($attachedFiles) }} --
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
    </div> --}}

    <!-- Confirmation Modal -->
    <div class="modal effect-scale md-wrapper" id="confirmationModal" tabindex="-1"
        aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white text-center" id="confirmationModalLabel">
                        <i class="bx bx-send me-2"></i>Confirm Email Send
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Please review your email details before sending:</p>

                    <div class="confirmation-details p-3 bg-light rounded">
                        <div class="row mb-2">
                            <div class="col-3 fw-bold">To:</div>
                            <div class="col-9">
                                <div id="confirmTo"
                                    style="white-space: pre-wrap; margin: 0; font-family: inherit; overflow-wrap: break-word;">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-3 fw-bold">CC:</div>
                            <div class="col-9" id="confirmCC"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-3 fw-bold">BCC:</div>
                            <div class="col-9" id="confirmBCC"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-3 fw-bold">Subject:</div>
                            <div class="col-9">
                                <div id="confirmSubject"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-3 fw-bold">Priority:</div>
                            <div class="col-9" id="confirmPriority"></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-3 fw-bold">Category:</div>
                            <div class="col-9" id="confirmCategory"></div>
                        </div>
                        <div class="row">
                            <div class="col-3 fw-bold">Attachments:</div>
                            <div class="col-9" id="confirmAttachments"></div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold">Message Preview:</label>
                        <div class="border p-3 bg-light rounded" style="max-height: 200px; overflow-y: auto;">
                            <pre id="confirmMessage" style="white-space: pre-wrap; margin: 0; font-family: inherit;"></pre>
                        </div>
                    </div>

                    <div class="mt-3" id="replyWarning" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="bx bx-warning me-2"></i>
                            <strong>Reply Mode:</strong> This email is a reply to an existing message.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="cancelEmailConfirmation">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmSendBtn">
                        <i class="bx bx-paper-plane me-1"></i>Send Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (!auth()->user()->hasOutlookConnection() && $ClaimRegister->verified === 'A')
        @include('mail.partials.outlook-setup')
    @endif
    {{-- <x-outlook-connection :auto-show="false" :show-cancel-button="true" :fetch-emails-on-connect="false" :show-toast-message="false" /> --}}
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let lastDebitData = {
                claimNo: null,
                ackLetterUrl: null,
                creditNoteUrl: null
            };
            let lastReinData = {
                tranNo: null,
                debitUrl: null,
                claimNoticeUrl: null
            };

            $('#to-customer').click(function(e) {
                $('#customerForm').submit();
            });

            $('.modal').on('shown.bs.modal', function() {
                $('.form-select').select2({
                    dropdownParent: $(this)
                });
            });

            $(document).on('click', '.add-peril-section', function() {
                const lastPerilSection = $('.peril-section:last');
                const prevCounter = lastPerilSection.data('counter')
                const perilName = $(`#peril_name-${prevCounter}`).val()
                const perilAmount = $(`#peril_amount-${prevCounter}`).val()
                if (perilName == null || perilName == '' || perilName == ' ') {
                    toastr.error('Please Type Particular Name', 'Incomplete data')
                    return false
                } else if (perilAmount == null || perilAmount == '' || perilAmount == ' ') {
                    toastr.error('Please Capture Particular Amount', 'Incomplete data')
                    return false
                }

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
                    fa_class = 'fa-plus'
                } else {
                    btn_class = 'btn-danger remove-peril-section'
                    btn_id = 'remove-peril-section'
                    fa_class = 'fa-minus'

                }
                $(document).find(`#peril-section-${prevCounter}`).append(`
                        <div id="peril_section-div">
                            <div id="peril-section-${counter}" data-counter="${counter}" class="peril-section">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="">Particular Name</label>
                                                                    <select name="peril_name[]" id="peril_name-${counter}" class="form-select form-select-sm peril_name" data-counter="${counter}" required>
                                            <option value="">--Select Particular--</option>
                                            @foreach ($perilTypes as $perilType)
                                                <option value="{{ $perilType->id }}">  {{ $perilType->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3 peril_amount">
                                        <label class="required ">100 % Particular Amount</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control peril_amount" name="peril_amount[]" data-counter="${counter}" id="peril_amount-${counter}" onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required>
                                            <button class="btn ${btn_class}" type="button" id="${btn_id}"><i class="fa ${fa_class}"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
            }

            $(document).on('click', '#attachments', function() {
                $(`#attachmentsForm`)[0].reset();
                $(`#attachmentsForm [name="_method"]`).val('POST');
            });

            $(document).on('click', '.edit-attachment', function() {
                const data = $(this).data('data');
                $(`#attachmentsForm #attachments_id`).val(data.id);
                $(`#attachmentsForm #title`).val(data.title);
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
                        claim_no: "{!! $cover->claim_no !!}",
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
                            console.log(error);
                            toastr.error("An internal error occured")
                        });
                });
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

                    let url = ''
                    let HttpMethod = $('#attachmentsForm [name="_method"]').val()
                    if (HttpMethod == 'POST') {
                        url = "{!! route('claim.save_attachment') !!}"
                    } else if (HttpMethod == 'PUT') {
                        url = "{!! route('claim.amend_attachment') !!}"
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
                    let url = ''
                    let HttpMethod = $('#statusForm [name="_method"]').val()
                    if (HttpMethod == 'POST') {
                        url = "{!! route('claim.saveClaimStatus') !!}"
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
                            $('#status-modal').modal('hide');
                            if (data.status == 201) {
                                toastr.success("Status saved Successfully")

                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)
                                $('#status-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to update Claim Status")
                                $('#status-save-btn').prop('disabled', false).text('Submit')
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error("Failed to Update Claim Status")
                            $('#status-save-btn').prop('disabled', false).text('Submit')
                        });
                }
            })

            $.validator.addMethod("dateRequiredIfChecked", function(value, element, params) {
                var checkbox = $(element).closest('tr').find('input[type="checkbox"]');
                return checkbox.is(':checked') ? value.trim() !== "" : true;
            }, "Please enter the date when the document is received.");

            $("#acknowledgementForm").validate({
                errorClass: "errorClass",
                rules: {
                    "document[]": {
                        required: true
                    },
                    "date_received[]": {
                        dateRequiredIfChecked: true
                    },
                    claim_no: {
                        required: true
                    },
                },
                submitHandler: function(form) {
                    $('#ack-save-btn').prop('disabled', true).text('Saving...')
                    let url = ''
                    let HttpMethod = $('#acknowledgementForm [name="_method"]').val()
                    if (HttpMethod == 'POST') {
                        url = "{!! route('claim.save_doc_acknowledgement') !!}"
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
                            $('#acknowledgement-modal').modal('hide');
                            if (data.status == 201) {
                                toastr.success("Document saved Successfully")
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } else if (data.status == 422) {
                                showServerSideValidationErrors(data.errors)
                                $('#ack-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to save document")
                                $('#ack-save-btn').prop('disabled', false).text('Submit')
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error("Failed to save document")
                            $('#ack-save-btn').prop('disabled', false).text('Submit')
                        });
                }
            })

            $("#save_peril").validate({
                errorPlacement: function(error, element) {
                    error.addClass("text-danger");
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass('error').removeClass('valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error').addClass('valid');
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    form.submit();
                }
            });

            $('#perils-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.peril_datatable') !!}",
                    data: function(d) {
                        d.claim_no = "{!! $ClaimRegister->claim_no !!}";
                    }
                },
                columns: [{
                        data: 'tran_no',
                        searchable: false,
                        className: 'highlight-index',
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
                ]
            });

            const reinsurersTable = $('#reinsurers-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.reinsurersDatatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $ClaimRegister->endorsement_no !!}",
                            d.claim_no = "{!! $ClaimRegister->claim_no !!}",
                            d.cover_no = "{!! $ClaimRegister->cover_no !!}"
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
                        render: $.fn.dataTable.render.number(',', '.', 2, ''),
                        className: 'highlight-desc-3'
                    }, {
                        data: 'action',
                        searchable: false,
                        sortable: false,
                        className: 'highlight-description'
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
                        columnsToSum = columnsToSum.concat([4, 5, 6, 7]);
                    } else if (businessType === 'TPR' && !['NEW', 'REN'].includes(transactionType)) {
                        columnsToSum = columnsToSum.concat([4, 5, 6, 7]);
                    } else if (businessType === 'TNP' && !['NEW', 'REN'].includes(transactionType)) {
                        columnsToSum = columnsToSum.concat([3, 4]);
                    }

                    let footerRow = '<tr>';
                    footerRow +=
                        '<td colspan="3" style="text-align:right !important; font-weight:bold; color: #000;">Totals:</td>';
                    const columns = api.columns().nodes().length;
                    for (let i = 3; i < columns - 1; i++) {
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

            $("#debitForm").validate({
                errorClass: "errorClass",
                rules: {
                    claim_no: {
                        required: true
                    },
                    amount: {
                        required: true,
                        normalizer: function(value) {
                            return removeCommas(value)
                        },
                        max: {!! $nextDebitAmount !!},
                        // min: 0
                    },
                },
                submitHandler: function(form) {
                    $('#debit-save-btn').prop('disabled', true).text('Generating...')
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
                                $('#debit-save-btn').prop('disabled', false).text('Submit')

                            } else {
                                toastr.error("Failed to save a credit note")
                                $('#debit-save-btn').prop('disabled', false).text('Generate')
                            }
                        })
                        .catch(error => {
                            toastr.error("Failed to save a credit note")
                            $('#debit-save-btn').prop('disabled', false).text('Generate')
                        });
                }
            })

            const debitsTable = $('#debits-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.debits_datatable') !!}",
                    data: function(d) {
                        d.claim_no = "{!! $ClaimRegister->claim_no !!}";
                        d.endorsement_no = "{!! $ClaimRegister->endorsement_no !!}";
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
                        '<td colspan="4" style="text-align:right !important; font-weight:bold; color: #000;">Totals:</td>';
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

            const attachmentsTable = $('#attachments-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.attachments_datatable') !!}",
                    data: function(d) {
                        d.claim_no = "{!! $ClaimRegister->claim_no !!}";
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

            const statusTable = $('#status-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('claim.claimStatusDatatable') !!}",
                    data: function(d) {
                        d.claim_no = "{!! $ClaimRegister->claim_no !!}";
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

            $('#ack-save-btn').click(function(e) {
                $('#acknowledgementForm').submit()
            });

            $('#status-save-btn').click(function(e) {
                $('#statusForm').submit()
            });

            reinsurersTable.on('click', '.send_rein_email', function(e) {
                e.preventDefault();
                // lastReinData.tranNo = $(this).data('tran_no');
                // lastReinData.debitUrl = $(this).data('debit_url');
                // lastReinData.claimNoticeUrl = $(this).data('claim_notice_url');

                console.log($(this).data())

                // const reinsurers = @json($reinsurers) ?? [];
                // await prepareReinEmailModal(
                //     lastReinData.tranNo,
                //     lastReinData.debitUrl,
                //     lastReinData.claimNoticeUrl,
                //     reinsurers
                // );
            });

            debitsTable.on('click', '.send_debit_letter', async function(e) {
                e.preventDefault();

                lastDebitData.claimNo = $(this).data('claim_no');
                lastDebitData.ackLetterUrl = $(this).data('ack_letter_url');
                lastDebitData.creditNoteUrl = $(this).data('credit_note_url');

                const recipients = @json($recipients) ?? [];
                const customer = @json($customer) ?? [];

                await prepareDebitLetterModal(
                    lastDebitData.claimNo,
                    lastDebitData.ackLetterUrl,
                    lastDebitData.creditNoteUrl,
                    recipients,
                    customer
                );
            });

            async function prepareReinEmailModal(tranNo, debitUrl, claimNoticeUrl, reinsurers) {
                // window.OutlookConnectionManager.showLoading();
                // const emailConnection = await window.OutlookConnectionManager.checkStatus();

                console.log(debitUrl)


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
                    $("a#claimNoticeLink").removeAttr('href').on('click', e => e.preventDefault());
                }

                const reinsurer = reinsurers.find(x => Number(x.tran_no) === Number(tranNo));
                if (!reinsurer) return;

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

            async function prepareDebitLetterModal(claimNo, ackLetterUrl, creditNoteUrl, recipients, customer) {
                // window.OutlookConnectionManager.showLoading();
                // const emailConnection = await window.OutlookConnectionManager.checkStatus();

                // if (!emailConnection.connected) {
                //     window.OutlookConnectionManager.hideLoading();
                //     window.OutlookConnectionManager.show();
                //     return;
                // }

                // window.OutlookConnectionManager.hideLoading();

                // if (ackLetterUrl) {
                //     $("#ackLetterLink").attr('href', ackLetterUrl);
                //     $("#ackLetterFile").val(ackLetterUrl);
                // } else {
                //     $("#ackLetterLink").removeAttr('href').on('click', function(e) {
                //         e.preventDefault();
                //     });
                // }

                // if (creditNoteUrl) {
                //     $("#creditNoteLink").attr('href', creditNoteUrl);
                //     $("#creditNoteFile").val(creditNoteUrl);
                // } else {
                //     $("#creditNoteLink").removeAttr('href').on('click', function(e) {
                //         e.preventDefault();
                //     });
                // }

                // const contacts = recipients ?? [];
                // const cedant = customer ?? [];

                // const $contactsSelect = $(".claimCedEmailForm #contacts");
                // const $ccEmailSelect = $(".claimCedEmailForm #ccEmail");
                // const $bccEmailSelect = $(".claimCedEmailForm #bccEmail");

                // $contactsSelect.empty().append('<option value="" disabled>--Select contacts--</option>');
                // $ccEmailSelect.empty().append('<option value="" disabled>--Select CC emails--</option>');
                // $bccEmailSelect.empty().append('<option value="" disabled>--Select BCC emails--</option>');

                // let contactsSelected = [];

                // if (contacts.length > 0) {
                //     const primaryContacts = [];
                //     const regularContacts = [];

                //     contacts.forEach(function(contact) {
                //         const email = contact.contact_email;
                //         const name = contact.contact_name;
                //         const phone = contact.contact_mobile_no;
                //         const isPrimary = contact.is_primary === true;

                //         if (email) {
                //             let optionText = '';
                //             if (name && email) optionText = `${name} (${email})`;
                //             else optionText = email;
                //             if (phone) optionText += ` - ${phone}`;
                //             if (isPrimary) optionText += ' [Primary]';

                //             const createOption = () => $('<option></option>')
                //                 .attr('value', email)
                //                 .text(optionText)
                //                 .data('contact-data', contact)
                //                 .data('is-primary', isPrimary);

                //             $contactsSelect.append(createOption());
                //             $ccEmailSelect.append(createOption());
                //             $bccEmailSelect.append(createOption());

                //             if (isPrimary) primaryContacts.push(email);
                //             else regularContacts.push(email);
                //         }
                //     });

                //     if (primaryContacts.length > 0) {
                //         contactsSelected = primaryContacts;
                //         $contactsSelect.val(primaryContacts).trigger('change');
                //     } else if (regularContacts.length === 1) {
                //         contactsSelected = [regularContacts[0]];
                //         $contactsSelect.val(regularContacts[0]).trigger('change');
                //     }

                //     [$contactsSelect, $ccEmailSelect, $bccEmailSelect].forEach($select => {
                //         if ($select.hasClass('select2-hidden-accessible')) {
                //             $select.trigger('change.select2');
                //         }
                //     });
                // }

                // const cedantEmail = cedant?.email ?? null;
                // let toEmails = [];
                // if (cedantEmail) toEmails.push(cedantEmail);
                // toEmails = toEmails.concat(contactsSelected);

                // $(".claimCedEmailForm #toEmail").val(toEmails);
                // $(".claimCedEmailForm #cedantToEmail").val(cedantEmail);
                // $("#sendCedDocumentEmail").modal('show');
            }

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
                            // $('#sendDocumentEmail').modal('hide');
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

            $('#conversationModal').on('hidden.bs.modal', async function() {
                if (!lastReinData.tranNo) return;

                const reinsurers = @json($reinsurers) ?? [];
                await prepareReinEmailModal(
                    lastReinData.tranNo,
                    lastReinData.debitUrl,
                    lastReinData.claimNoticeUrl,
                    reinsurers
                );

                // const recipients = @json($recipients) ?? [];
                // const customer = @json($customer) ?? [];
                // await prepareReinEmailModal(
                //     lastDebitData.claimNo,
                //     lastDebitData.ackLetterUrl,
                //     lastDebitData.creditNoteUrl,
                //     recipients,
                //     customer
                // );

            });
        });
    </script>
@endpush
