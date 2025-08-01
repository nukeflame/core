@extends('layouts.app')
<style type="text/css">
    .br span {
        border-radius: 50%;
        padding: 20px;
        text-align: center;
        border: 1px solid;
        color: #b3d9ff;
    }

    .q-link {
        text-decoration: none;
    }

    .q-link p {
        color: black;
    }

    .br span:hover {
        color: #3399ff;
    }

    .ct-series-a .ct-bar {
        stroke-width: 15px;

    }

    .optionFile {
        display: none;
    }
</style>
@section('content')
    <br />
    @if ($message = Session::get('success'))
    @endif

    <div class="card mt-3 border">
        <div class="card-header ">
            <div>

                <strong>
                    <h5>Pipeline Details</h5>
                </strong>
                <form id="pip_year_form" action="{{ route('treaty.pipeline.view') }}" method="get">
                    <input type="hidden" id="opp_id" name="opp_id">
                    {{-- <input type="text" name="stage" id="stage"> --}}
                    <div class="row">
                        <div class="col-md-3">
                            <x-SearchableSelect id="pip_year_select" req="" inputLabel="" name="pipeline">
                                @foreach ($pipelines as $pip_year)
                                    <option @if ($pip_year->id == $pip) selected @endif value="{{ $pip_year->id }}">
                                        {{ $pip_year->year }}</option>
                                @endforeach
                            </x-SearchableSelect>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-2">
            <div class="row" style="height:300px;">
                <div class="col-md-8">
                    <div class="ct-chart-ranking ct-golden-section ct-series-a"></div>
                </div>
                <div class="col-md-4">


                </div>
            </div>
            <div class="row">
                <hr>
                <div class="d-flex justify-content-center flex-wrap">
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="dot rounded-circle me-2"
                            style="background-color: #d70206; width: 12px; height: 12px;"></span>
                        <span class="fw-normal small">Proposal</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="dot rounded-circle me-2"
                            style="background-color: #f05b4f; width: 12px; height: 12px;"></span>
                        <span class="fw-normal small">Negotiation</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="dot rounded-circle me-2"
                            style="background-color: #f4c63d; width: 12px; height: 12px;"></span>
                        <span class="fw-normal small">Lead</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="dot rounded-circle me-2"
                            style="background-color: #d17905; width: 12px; height: 12px;"></span>
                        <span class="fw-normal small">Won</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="dot rounded-circle me-2"
                            style="background-color: #453d3f; width: 12px; height: 12px;"></span>
                        <span class="fw-normal small">Lost</span>
                    </div>
                    <div class="d-flex align-items-center me-3 mb-2">
                        <span class="dot rounded-circle me-2"
                            style="background-color: #59922b; width: 12px; height: 12px;"></span>
                        <span class="fw-normal small">Final Stage</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="bs-example">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#general_details" role="tab">
                        All
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#q1_details" role="tab">
                        Quarter One
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#q2_details" role="tab">
                        Quarter Two
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#q3_details" role="tab">
                        Quarter Three
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#q4_details" role="tab">
                        Quarter Four
                    </a>
                </li>
                <br>

            </ul>
            <div class="tab-content p-3 text-muted">
                <div class="tab-pane active" id="general_details">
                    <div class="row table-responsive">

                        <table id="all_opps" class="table table-striped" style="width:100%">
                            <thead>
                                <th>id</th>
                                <th>Insured name</th>
                                <th>Division</th>
                                <th>Business class</th>
                                <th>Currency</th>
                                <th>Sum Insured</th>
                                <th>Premium</th>
                                <th>Effective date</th>
                                <th>Closing date</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Category</th>
                                <th>Approval Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="q1_details">
                    <div class="row table-responsive">
                        <table id="q1_opps" class="table table-striped" style="width:100%">
                            <thead>
                                <th>id</th>
                                <th>Insured name</th>
                                <th>Division</th>
                                <th>Business Class</th>
                                <th>Currency</th>
                                <th>Premium</th>
                                <th>Sum Insured</th>
                                <th>Effective date</th>
                                <th>Closing date</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Category</th>
                                <th>Approval Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="q2_details">
                    <div class="row table-responsive">
                        <table id="q2_opps" class="table table-striped" style="width:100%">
                            <thead>
                                <th>id</th>
                                <th>Insured name</th>
                                <th>Division</th>
                                <th>Business Class</th>
                                <th>Currency</th>
                                <th>Premium</th>
                                <th>Sum Insured</th>
                                <th>Effective date</th>
                                <th>Closing date</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Category</th>
                                <th>Approval Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="q3_details">
                    <div class="row table-responsive">
                        <table id="q3_opps" class="table table-striped" style="width:100%">
                            <thead class="mt-2">
                                <th>id</th>
                                <th>Insured name</th>
                                <th>Division</th>
                                <th>Business Class</th>
                                <th>Currency</th>
                                <th>Premium</th>
                                <th>Sum Insured</th>
                                <th>Effective date</th>
                                <th>Closing date</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Category</th>
                                <th>Approval Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="q4_details">
                    <div class="row table-responsive">
                        <table id="q4_opps" class="table table-striped" style="width:100%">
                            <thead>
                                <th>id</th>
                                <th>Insured name</th>
                                <th>Division</th>
                                <th>Business Class</th>
                                <th>Currency</th>
                                <th>Premium</th>
                                <th>Sum Insured</th>
                                <th>Effective date</th>
                                <th>Closing date</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Category</th>
                                <th>Approval Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- update Quote -->
    <div id="updateStatusQuote">
        <div class="modal fade" id="editStatusQuoteModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white"><span id="ed_status_name"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-header bg-primary text-white" id="quote_slip" style="display:none">
                            Treaty Docs
                        </div>
                        <div class="modal-header bg-primary text-white" id="qt_cedant_slip" style="display:none">
                            Data Analysis
                        </div>
                        <div class="modal-header bg-primary text-white" id="final_document" style="display:none">
                            Final Document
                        </div>
                        <form id="statusUpdateForm" action="{{ route('update.opp.status') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="opp_id" id="update_opp">
                            <input type="hidden" name="pip_id" id="update_pip">
                            <input type="hidden" name="division" id="update_division">
                            <input type="hidden" name="category_type" id="category_type">
                            <input type="hidden" name="commission_rate_type" value="R">
                            <input type="hidden" name="bus_type" value="TRT">

                            <div class="row" id="quote_div" style="display: none">
                                <div class="col-12">
                                    <x-BoldLabelTextArea name="quote_title_intro" id="quote_title_intro" req=""
                                        inputLabel="" oninput="this.value = this.value.toUpperCase();"
                                        style="font-weight: bold;" value="REQUEST FOR TERMS" readonly hidden>
                                    </x-BoldLabelTextArea>
                                </div>
                                <div class="modal-header">
                                    <h5 class="modal-title dc-modal-title" id="staticScheduleDetailsLabel">Schedule
                                        Details
                                    </h5>
                                    <h6 class="modal-title" style="font-weight:boild;" id="cedant"></h6>
                                    <button aria-label="Close" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body mb-3 p-3 border rounded shadow-sm" id="qt_schedule_div">

                                    @if (isset($schedule))
                                        @foreach ($schedule as $key => $sch)
                                            <div class="mb-1 p-1 border rounded shadow-sm">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <input type="hidden" id="qtclassname{{ $key }}"
                                                        value="{{ $sch->class }}" data-key="{{ $key }}" />
                                                    <input type="hidden" id="qtclassgroup{{ $key }}"
                                                        value="{{ $sch->class_group }}" data-key="{{ $key }}" />
                                                    <input type="hidden" class="schedule-sum-insured-qt"
                                                        data-key="{{ $key }}"
                                                        value="{{ $sch->sum_insured_type }}">
                                                    <input type="hidden" id="qtname{{ $key }}"
                                                        value="{{ $sch->name }}" data-key="{{ $key }}" />
                                                    <h6 class="ms-2 schedule-name-qt" data-key="{{ $key }}">
                                                        {{ firstUpper($sch->name) }}
                                                        @if (strtolower($sch->name) == 'allowed commission')
                                                            (%)
                                                        @endif
                                                    </h6>
                                                    <span class="toggle-icon" role="button" aria-expanded="true"
                                                        aria-label="Toggle Content"
                                                        data-target="#optional1-{{ $key }}">
                                                        <strong><i class='bx bx-plus'></i></strong>
                                                    </span>
                                                </div>

                                                <!-- The hidden optional content that will be toggled -->
                                                <div id="optional1-{{ $key }}" class="row"
                                                    style="display: none; margin-top: 10px; border-top: 2px solid #f0f0f0; padding-top: 10px;">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fs-14">Current & Proposed</label>
                                                        </div>
                                                        <div class="col-md-6 text-end">
                                                            <button type="button" class="fullscreen-exit-btn"
                                                                style="display: none;">
                                                                <div
                                                                    style="display: inline-block; line-height: 16px; padding-right: 4px;">
                                                                    <svg width="12px" height="12px"
                                                                        viewBox="0 0 20 20"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <rect x="0" fill="none" width="20"
                                                                            height="20" />
                                                                        <g>
                                                                            <path
                                                                                d="M3.4 2L2 3.4l2.8 2.8L3 8h5V3L6.2 4.8 3.4 2zm11.8 4.2L18 3.4 16.6 2l-2.8 2.8L12 3v5h5l-1.8-1.8zM4.8 13.8L2 16.6 3.4 18l2.8-2.8L8 17v-5H3l1.8 1.8zM17 12h-5v5l1.8-1.8 2.8 2.8 1.4-1.4-2.8-2.8L17 12z" />
                                                                        </g>
                                                                    </svg>
                                                                </div>
                                                            </button>
                                                            <div class="col-4 ms-auto">
                                                                <button
                                                                    class="form-control sm-btn bg-dark text-white  schedule-qt check-schedule"
                                                                    data-key="{{ $key }}">Load data</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Schedule Description Section -->
                                                    @if (!empty($sch->amount_field) && $sch->amount_field == 'Y')
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <input type="hidden"
                                                                    id="data_determinant{{ $key }}"
                                                                    value="{{ $sch->data_determinant }}"
                                                                    data-key="{{ $key }}" />
                                                                <label class="form-label">Current</label>
                                                                <input type="text"
                                                                    class="amount current_proposed form-control"
                                                                    name="schedule_details[{{ $key }}][current_amount]"
                                                                    data-key="{{ $key }}"
                                                                    placeholder="Enter current amount" />
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Proposed</label>
                                                                <input type="text"
                                                                    class="amount current_proposed form-control"
                                                                    name="schedule_details[{{ $key }}][proposed_amount]"
                                                                    data-key="{{ $key }}"
                                                                    placeholder="Enter proposed amount" />
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Final</label>
                                                                <input type="text"
                                                                    class="amount current_final form-control"
                                                                    name="schedule_details[{{ $key }}][final_amount]"
                                                                    data-key="{{ $key }}"
                                                                    placeholder="Enter final amount" />
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="card-md">
                                                            <label class="form-label">Current</label>
                                                            <textarea id="schedule-descr-current-{{ $key }}" class="form-control schedule-descr"
                                                                data-key="{{ $key }}" rows="4" placeholder="Enter current description..."></textarea>
                                                            <label class="form-label mt-2">Proposed</label>
                                                            <textarea id="schedule-descr-proposed-{{ $key }}" class="form-control schedule-descr"
                                                                data-key="{{ $key }}" rows="4" placeholder="Enter proposed description..."></textarea>
                                                            <label class="form-label mt-2">Final</label>
                                                            <textarea id="schedule-descr-final-{{ $key }}" class="form-control schedule-descr"
                                                                data-key="{{ $key }}" rows="4" placeholder="Enter final description..."></textarea>
                                                        </div>
                                                    @endif
                                                    <input type="hidden"
                                                        name="schedule_details[{{ $key }}][id]"
                                                        value="{{ $sch->id }}" />
                                                    <input type="hidden"
                                                        name="schedule_details[{{ $key }}][name]"
                                                        value="{{ $sch->name }}" />
                                                    <input type="hidden"
                                                        name="schedule_details[{{ $key }}][details]"
                                                        id="sched-details-{{ $key }}" />
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <input type="hidden" id="sum_insured_type">
                                    <input type="hidden" name="stagecycleqt" id="stagecycleqt">
                                </div>
                            </div>
                            <div class="col-12" style="display:none" id="stage_div">
                                <x-SearchableSelect name="stage_cycle" id="stage_cycle" req="required"
                                    inputLabel="Quotation Cycle Stage">
                                    <option value="">Select stage</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}">
                                            {{ $status->status_name }}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </div>

                            <!-- Search Bar -->
                            <div class="row" style="display: none" id="quote_search_reinsurer">
                                <div class="col-11">
                                    <div class="label">Reinsurer<font style="color:red;">*</font>
                                    </div>
                                    <input type="text" id="quote-search-bar" class="form-control"
                                        placeholder="---Search for Reinsurer---" />
                                </div>
                            </div>

                            <div id="quote-search-results" class="mt-2">
                            </div>

                            <!-- List of Selected Customers -->
                            <div id="quote-selected-customers-list" class="mt-4">
                                <div class="row p-3 mt-3 border rounded shadow-sm bg-light" id="qt_total_written_sh"
                                    style="display:none">
                                    <div class="col-4">
                                        <label for="" class="fw-bold ">Total Written Share</label>
                                        <input type="number" id="qt_written_share_total" name="written_share_total"
                                            class="form-control border-primary" />
                                    </div>
                                    <div class="col-4">
                                        <label for="" class="fw-bold ">Distributed</label>
                                        <input type="number" id="distributed" name="distributed"
                                            class="form-control  bg-white" required readonly />
                                    </div>
                                    <div class="col-4">
                                        <label class="fw-bold ">Undistributed</label>
                                        <input type="number" id="undistributed" name="undistributed"
                                            class="form-control  bg-white text-danger" required readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="row p-3 mt-3 border rounded shadow-sm bg-light" id="qt_total_placed_unplaced"
                                style="display:none">
                                <input type="number" class="updated_written_share_total" id="updated_written_share_qt"
                                    name="updated_written_share_total" class="form-control border-primary" hidden />
                                {{-- <div class="col-4" style="display: none;">
                                    <label for="" class="fw-bold ">Placed Share</label>
                                    <input type="number" id="qt_placed" name="placed" class="form-control  bg-white"
                                        required readonly />
                                </div>
                                <div class="col-4" style="display:none">
                                    <label class="fw-bold ">Unplaced Share</label>
                                    <input type="number" id="qt_unplaced" name="unplaced"
                                        class="form-control  bg-white text-danger" required readonly />
                                </div> --}}
                            </div>
                            <div id="contacts-wrapper-qt">
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="cedant_name" class="form-label fw-medium">Cedant</label>
                                        <input type="text" id="cedant_name_qt" class="form-control"
                                            placeholder="Enter cedant name">
                                    </div>
                                </div>
                            </div>
                            <div class="financial_statement_div" id="financial_statement_div" style="display: none;">
                                <!-- Collapsible Section Header -->
                                <div class="row mb-3">
                                    <div class="col-5">
                                        <button
                                            class="btn btn-outline-primary w-100 d-flex justify-content-between align-items-center"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#financialSection"
                                            aria-expanded="true" aria-controls="financialSection">
                                            <span class="fw-medium">Financial Statement Calculator</span>
                                            <i class="bi bi-chevron-down" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Collapsible Content -->
                                <div class="collapse show" id="financialSection">
                                    <!-- Input Section - All in One Row -->
                                    <div class="row g-3 mb-4" id="financialInputs">
                                        <!-- Solvency CR -->
                                        <div class="col-md-4">
                                            <label for="solvency_cr" class="form-label fw-medium">
                                                Solvency CR <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" id="solvency_cr" name="solvency_cr"
                                                    class="form-control" placeholder="Enter value" step="0.01"
                                                    min="0" required />
                                                <span class="input-group-text">Ksh</span>
                                            </div>
                                        </div>

                                        <!-- MCR -->
                                        <div class="col-md-4">
                                            <label for="mcr" class="form-label fw-medium">
                                                MCR <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" id="mcr" name="mcr" class="form-control"
                                                    placeholder="Enter value" step="0.01" min="0" required />
                                                <span class="input-group-text">Ksh</span>
                                            </div>
                                        </div>

                                        <!-- Ratio Result -->
                                        <div class="col-md-4">
                                            <label for="ratio_result" class="form-label fw-medium">
                                                Ratio Result
                                            </label>
                                            <div class="input-group">
                                                <input type="number" id="ratio_result" name="ratio_result"
                                                    class="form-control bg-light" readonly
                                                    placeholder="Auto calculated" />
                                                <span class="input-group-text">ratio</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Calculate Button -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-primary" id="calculateBtn" hidden>
                                                Calculate Ratio
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary ms-2" id="clearBtn">
                                                Clear
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Result Section -->
                                    <div class="row" id="resultSection" style="display: none;">
                                    </div>
                                </div>
                            </div>

                            <div id="checklist_of_operations_div" style="display: none;">
                                <br>
                                <hr>
                                <div> <span class="fw-medium text-danger">Task Confirmation</span></div>
                                <div id="checklist_of_operations">


                                </div>
                                <hr>
                            </div>


                            <div id="quoteFile" class="col-12">
                            </div>

                            <div class="row mt-1" id="qt_email_attachment_file" style="padding-left: 10px;">
                                <div class="row mt-1 qt_email_attachment_file">
                                    <div class="col-auto" id="email_file_name">
                                        <div class="row">
                                            <x-Input req="" inputLabel="Document Title"
                                                value="Email Attachment File" name="document_name_email_attachment[]" />
                                        </div>
                                    </div>
                                    <div class="col-auto" id="email_file_attach">
                                        <label for="document_file">File</label>
                                        <div class="input-group">
                                            <input type="file" name="document_file_email_attachment[]"
                                                class="form-control document_file " />
                                            <button id="add_attachment_qt" class="btn btn-primary" type="button">
                                                <i class="bx bx-plus add_attachment_qt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-auto" style="margin-top: 30px;">
                                        <i class="bx bx-show preview  optionFile" id="preview"
                                            style="cursor:pointer;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" id="updateQuoteFooter">
                                <a href="#" class="btn btn-outline-dark btn btn-wave waves-effect waves-light"
                                    id="generate_slip">
                                    <i class="bx bx-analyse me-1 align-middle"></i> Preview
                                </a>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bx bx-x-circle"></i> Close
                                </button>
                                <button id="updateStatusBtn" type="submit" class="btn btn-outline-success">
                                    <i class="bx bx-check-circle"></i> Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        {{-- update Facultative --}}
        <div id="updateStatusFacultative">
            <div class="modal fade" id="editStatusFacultativeModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white"><span id="fac_ed_status_name"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="modal-header bg-primary text-white" id="fac_slip" style="display:none">
                                FACULTATIVE SLIP
                            </div>
                            <div class="modal-header bg-primary text-white" id="fac_pl_det" style="display:none">
                                FACULTATIVE PLACEMENT DETAILS
                            </div>
                            <div class="modal-header bg-primary text-white" id="tender_upload_doc" style="display:none">
                                TENDER RECEIVED DOCUMENTS UPLOAD
                            </div>
                            <div class="modal-header bg-primary text-white" id="fac_pl_update" style="display:none">
                                TENDER PIPELINE UPDATE
                            </div>
                            <div class="modal-header bg-primary text-white" id="fac_pl_ced" style="display:none">
                                TENDER REQUEST FOR DOCUMENTS
                            </div>
                            <form id="facultative-statusUpdateForm" action="{{ route('update.opp.status') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="opp_id" id="update_oppfac">
                                <input type="hidden" name="pip_id" id="update_pipfac">
                                <input type="hidden" name="division" id="update_divisionfac">
                                <input type="hidden" name="category_type" id="category_type2">
                                <input type="hidden" name="commission_rate_type" value="C">
                                <input type="hidden" name="bus_type" value="TRT">

                                <div class="mb-4" id="tender_status" style="display:none;">
                                    <label for="category_type" class="form-label">Tender Status</label>
                                    <select class="form-control form-control-sm" name="tender_status" required>
                                        <option value="">Select Tender Status</option>
                                        <option value="3">Proceed</option>
                                        <option value="10">lost</option>
                                    </select>
                                </div>




                                <div id="pipeline_update" style="display: none;">
                                    <div class="form-group">
                                        <B class="primary-color">Insurance Details</B>
                                        <x-OnboardingInputDiv id="prequalification_div">
                                            <x-SearchableSelect name="type_of_bus" id="type_of_bus" req="required"
                                                inputLabel="Type of Business">
                                                <option value="">--Select type of business--</option>
                                                @foreach ($types_of_bus as $type_of_bus)
                                                    @if (in_array($type_of_bus->bus_type_id, ['TPR', 'TNP']))
                                                        <option value="{{ $type_of_bus->bus_type_id }}">
                                                            {{ firstUpper($type_of_bus->bus_type_name) }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        <div class="form-group treaty_grp" id="treaty_grp">
                                            <div id="trt_common">
                                                <div class="row row-cols-12">
                                                    {{-- <div class="col-sm-3 quota_treaty_limit_div tpr_section_div"
                                                    id="quota_treaty_limit_div">
                                                    <label for="reins_division">Reins Division</label>
                                                    <select class="form-inputs select2" name="reins_division"
                                                        id="reins_division">
                                                        <option value="">Select</option>
                                                        @foreach ($reins_divisions as $reins_division)
                                                            <option value="{{ $reins_division->division_code }}">
                                                                {{ $reins_division->division_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div> --}}
                                                    <!--treaty type-->
                                                    <div class="col-sm-3 treatytype_div trt_common_div">
                                                        <label class="form-label required">Treaty Type</label>
                                                        <select class="form-inputs section treatytype trt_common"
                                                            name="treatytype" id="treatytype" required>
                                                            @foreach ($treatytypes as $treatytype)
                                                                <option value="{{ $treatytype->treaty_code }}"
                                                                    @if ($trans_type != 'NEW' && $treatytype->treaty_code == $old_endt_trans->treaty_code) selected @endif>
                                                                    {{ $treatytype->treaty_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!--date offered-->
                                                    <div class="col-sm-3 date_offered_div trt_common_div">
                                                        <label class="form-label required">Date Offered</label>
                                                        @if ($trans_type == 'NEW')
                                                            <input type="date"
                                                                class="form-inputs date_offered trt_common"
                                                                aria-label="date_offered" id="date_offered"
                                                                name="date_offered" required>
                                                        @else
                                                            <input type="date"
                                                                class="form-inputs date_offered trt_common"
                                                                value="{{ $old_endt_trans->date_offered }}"
                                                                aria-label="date_offered" id="date_offered"
                                                                name="date_offered" required>
                                                        @endif
                                                    </div>
                                                    <div class="col-sm-3 trt_common_div">
                                                        <label class="form-label required">Expected Closure
                                                            Date</label>
                                                        <input type="date" class="form-inputs trt_common"
                                                            aria-label="expected_closure_date" id="expected_closure_date"
                                                            name="expected_closure_date" required>
                                                    </div>

                                                    <!--share offered-->
                                                    <div class="col-sm-2 share_offered_div trt_common_div">
                                                        <label class="required ">Share Offered(%)</label>
                                                        <input type="text" class="form-inputs share_offered trt_common"
                                                            @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->share_offered, 2) }} @endif"
                                                            aria-label="share_offered" id="share_offered"
                                                            name="share_offered" required>
                                                    </div>

                                                    <div class="col-sm-3 trt_common_div">
                                                        <label class="form-label required">Currency</label>
                                                        <div class="cover-card">
                                                            <select class="form-inputs select2" name="currency_code"
                                                                id="currency_code" required>
                                                                <option selected value="">Choose Currency
                                                                </option>
                                                                @foreach ($currencies as $currency)
                                                                    <option value="{{ $currency->currency_code }}">
                                                                        {{ firstUpper($currency->currency_name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <div class="text-danger">
                                                                {{ $errors->first('currency_code') }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--Premium Tax  (%)-->
                                                    <div class="col-sm-2 prem_tax_rate_div trt_common_div">
                                                        <label class="required ">Premium Tax Rate (%)</label>
                                                        <input type="number" class="form-inputs prem_tax_rate trt_common"
                                                            @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->prem_tax_rate, 2) }} @endif"
                                                            aria-label="prem_tax_rate" id="prem_tax_rate"
                                                            name="prem_tax_rate" required>
                                                    </div>

                                                    <!--RI Tax (%)-->
                                                    <div class="col-sm-2 ri_tax_rate_div prem_tax_rate_div trt_common_div">
                                                        <label class="required ">Reinsurance Tax Rate (%)</label>
                                                        <input type="number" class="form-inputs ri_tax_rate trt_common"
                                                            @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->ri_tax_rate, 2) }} @endif"
                                                            aria-label="ri_tax_rate" id="ri_tax_rate" name="ri_tax_rate"
                                                            min="0" max="100" required>
                                                    </div>

                                                    <!--Brokerage Comm (%)-->
                                                    <div class="col-sm-2 brokerage_comm_rate_div trt_common_div">
                                                        <label class="required ">Brokerage Commission Rate (%)</label>
                                                        <input type="number"
                                                            class="form-inputs brokerage_comm_rate trt_common"
                                                            @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->brokerage_comm_rate, 2) }}" @endif
                                                            aria-label="brokerage_comm_rate" id="brokerage_comm_rate"
                                                            name="brokerage_comm_rate" min="0" max="100"
                                                            required>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <!--Capture shared reinsurer-->
                                                    <div class="col-sm-2 reinsurer_per_treaty_div tpr_section_div">
                                                        <label class="required ">Reinsurers are captured per treaty
                                                            ?</label>
                                                        <select class="form-inputs reinsurer_per_treaty tpr_section"
                                                            name="reinsurer_per_treaty" id="reinsurer_per_treaty"
                                                            required>
                                                            <option value=""> Select Option </option>
                                                            @if (in_array($trans_type, ['NEW', 'REN']))
                                                                <option value="N"selected> No </option>
                                                                <option value="Y"> Yes </option>
                                                            @else
                                                                <option value="N"
                                                                    @if ($old_endt_trans->reinsurer_per_treaty == 'N') selected @endif>
                                                                    No
                                                                </option>
                                                                <option value="Y"
                                                                    @if ($old_endt_trans->reinsurer_per_treaty == 'Y') selected @endif>
                                                                    Yes
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-group mt-3">
                                                <div id="tpr_section">
                                                    <div class="row">

                                                        <!--premium rate-->
                                                        <div class="col-sm-3 port_prem_rate_div tpr_section_div">
                                                            <label class="required ">Portfolio Premium Rate(%)</label>
                                                            <input type="number"
                                                                class="form-inputs port_prem_rate tpr_section"
                                                                aria-label="port_prem_rate" id="port_prem_rate"
                                                                name="port_prem_rate" data-counter="0" max="100"
                                                                min="0" required
                                                                @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->port_prem_rate, 2) }}" @endif>
                                                        </div>

                                                        <!--loss rate-->
                                                        <div class="col-sm-3 port_loss_rate_div tpr_section_div">
                                                            <label class="required ">Portfolio Loss Rate(%)</label>
                                                            <input type="number"
                                                                class="form-inputs port_loss_rate tpr_section"
                                                                aria-label="port_loss_rate" id="port_loss_rate"
                                                                name="port_loss_rate" data-counter="0" max="100"
                                                                min="0" required
                                                                @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->port_loss_rate, 2) }}" @endif>
                                                        </div>

                                                        <!--profit comm rate-->
                                                        <div class="col-sm-3 profit_comm_rate_div tpr_section_div">
                                                            <label class="form-label required">Profit Comm
                                                                Rate(%)</label>
                                                            <input type="number"
                                                                class="form-inputs profit_comm_rate tpr_section"
                                                                aria-label="profit_comm_rate" id="profit_comm_rate"
                                                                name="profit_comm_rate" data-counter="0" max="100"
                                                                min="0" required
                                                                @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->profit_comm_rate, 2) }}" @endif>
                                                        </div>

                                                        <!--mgnt expense rate-->
                                                        <div class="col-sm-3 mgnt_exp_rate_div tpr_section_div">
                                                            <label class="required ">Mgnt Expense Rate(%)</label>
                                                            <input type="number"
                                                                class="form-inputs mgnt_exp_rate tpr_section"
                                                                aria-label="mgnt_exp_rate" id="mgnt_exp_rate"
                                                                name="mgnt_exp_rate" data-counter="0" max="100"
                                                                min="0" required
                                                                @if ($trans_type != 'NEW') value="{{ number_format($old_endt_trans->mgnt_exp_rate, 2) }}" @endif>
                                                        </div>

                                                    </div>

                                                    <div class="row">
                                                        <!--Deficit c/f (yrs)-->
                                                        <div class="col-sm-3 deficit_yrs_div tpr_section_div">
                                                            <label class="required_div deficit_yrs">Deficit C/F
                                                                (yrs)</label>
                                                            <input type="number" class="form-inputs  tpr_section"
                                                                aria-label="" class="deficit_yrs" id="deficit_yrs"
                                                                name="deficit_yrs" data-counter="0" min="0"
                                                                max="10" required
                                                                @if ($trans_type != 'NEW') value="{{ $old_endt_trans->deficit_yrs }}" @endif>
                                                        </div>



                                                    </div>

                                                    <div class="row mb-2 tpr_section_div">
                                                        <div class="col-md-4">
                                                            <button type="button" class="btn btn-primary tpr_section"
                                                                id="add_rein_class"> Add
                                                                Class </button>
                                                        </div>
                                                    </div>
                                                    @if ($trans_type != 'EDIT')
                                                        <div class="row reinclass-section " id="reinclass-section-0"
                                                            data-counter="0">
                                                            <h6 class="section-title tpr_section_div">Section A</h6>

                                                            <div class="row mb-2">
                                                                <!--reinsurance main class-->
                                                                <div class="col-sm-3 treaty_reinclass tpr_section_div">
                                                                    <label class="form-label required">Reinsurance
                                                                        Class</label>
                                                                    <select
                                                                        class="form-inputs select2 treaty_reinclass tpr_section"
                                                                        name="treaty_reinclass[]" id="treaty_reinclass-0"
                                                                        data-counter="0" required>
                                                                        @if ($trans_type == 'NEW')
                                                                            <option value="">Choose Reinsurance
                                                                                Class</option>
                                                                            @foreach ($reinsclasses as $reinsclass)
                                                                                <option
                                                                                    value="{{ $reinsclass->class_code }}">
                                                                                    {{ $reinsclass->class_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        @elseif(
                                                                            $trans_type == 'EXT' ||
                                                                                $trans_type == 'CNC' ||
                                                                                $trans_type == 'REN' ||
                                                                                $trans_type == 'RFN' ||
                                                                                $trans_type == 'NIL' ||
                                                                                $trans_type == 'INS' ||
                                                                                $trans_type == 'EDIT')
                                                                            @foreach ($reinsclasses as $reinsclass)
                                                                                @if ($reinsclass->class_code == $old_endt_trans->reinsclass_code)
                                                                                    <option
                                                                                        value="{{ $reinsclass->class_code }}"
                                                                                        selected>
                                                                                        {{ $reinsclass->class_name }}
                                                                                    </option>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="row quota_header_div tpr_section_div"
                                                                style="display: none">
                                                                <h6> Quota Share </h6>
                                                            </div>
                                                            <div class="row">

                                                                <!--quota limit-->
                                                                <div class="col-sm-2 quota_share_total_limit_div tpr_section_div"
                                                                    id="quota_share_total_limit_div">

                                                                    <label class="form-label required">100% Quota Share
                                                                        Limit</label>
                                                                    <input type="text"
                                                                        class="form-inputs quota_share_total_limit tpr_section"
                                                                        aria-label="quota_share_total_limit"
                                                                        id="quota_share_total_limit-0" data-counter="0"
                                                                        name="quota_share_total_limit[]"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required>
                                                                </div>

                                                                <!--Retention (%)-->
                                                                <div class="col-sm-1 retention_per_div tpr_section_div"
                                                                    id="retention_per_div">
                                                                    <label class="form-label required">Retention(%)</label>
                                                                    <input type="number"
                                                                        class="form-inputs retention_per tpr_section"
                                                                        aria-label="retention_per" id="retention_per-0"
                                                                        data-counter="0" name="retention_per[]"
                                                                        min="0" max="100" required>
                                                                </div>

                                                                <!--Retention Amount-->
                                                                <div class="col-sm-3 quota_retention_amt_div tpr_section_div"
                                                                    id="quota_retention_amt_div" style="display: none">
                                                                    <label class="form-label required">Retention
                                                                        Amount</label>
                                                                    <input type="text"
                                                                        class="form-inputs quota_retention_amt tpr_section"
                                                                        aria-label="quota_retention_amt"
                                                                        id="quota_retention_amt-0"
                                                                        name="quota_retention_amt[]" data-counter="0"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required disabled>
                                                                </div>

                                                                <!--treaty share(%)-->
                                                                <div class="col-sm-2 treaty_reice_div tpr_section_div"
                                                                    id="treaty_reice_div">
                                                                    <label class="form-label required">Treaty
                                                                        (%)</label>
                                                                    <input type="number"
                                                                        class="form-inputs treaty_reice tpr_section"
                                                                        aria-label="treaty_reice" id="treaty_reice-0"
                                                                        name="treaty_reice[]" data-counter="0"
                                                                        min="0" max="100" required>
                                                                </div>

                                                                <!--treaty limit-->
                                                                <div class="col-sm-3 quota_treaty_limit_div tpr_section_div"
                                                                    id="quota_treaty_limit_div" style="display: none">
                                                                    <label class="form-label required">Treaty
                                                                        Limit</label>
                                                                    <input type="text"
                                                                        class="form-inputs quota_treaty_limit tpr_section"
                                                                        aria-label="quota_treaty_limit"
                                                                        class="quota_treaty_limit"
                                                                        id="quota_treaty_limit-0"
                                                                        name="quota_treaty_limit[]" data-counter="0"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required disabled>
                                                                </div>

                                                            </div>

                                                            <div class="row surp_header_div tpr_section_div"
                                                                style="display: none" data-counter="0">
                                                                <h6> Surplus </h6>
                                                            </div>

                                                            <div class="row">

                                                                <!--Retention Amount-->
                                                                <div class="col-sm-3 surp_retention_amt_div tpr_section_div"
                                                                    id="surp_retention_amt_div " style="display: none">
                                                                    {{-- <h6> Surplus </h6> --}}
                                                                    <label class="form-label required">Retention
                                                                        Amount</label>
                                                                    <input type="text"
                                                                        class="form-inputs surp_retention_amt tpr_section"
                                                                        aria-label="surp_retention_amt"
                                                                        id="surp_retention_amt-0"
                                                                        name="surp_retention_amt[]" data-counter="0"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required disabled>
                                                                </div>

                                                                <!--no of lines-->
                                                                <div class="col-sm-3 no_of_lines_div tpr_section_div"
                                                                    id="no_of_lines_div">
                                                                    <label class="form-label required">No of
                                                                        Lines</label>
                                                                    <input type="number"
                                                                        class="form-inputs no_of_lines tpr_section"
                                                                        aria-label="no_of_lines" id="no_of_lines-0"
                                                                        data-counter="0" name="no_of_lines[]" required>
                                                                </div>

                                                                <!--treaty limit-->
                                                                <div class="col-sm-3 surp_treaty_limit_div tpr_section_div"
                                                                    id="surp_treaty_limit_div" style="display: none">
                                                                    <label class="form-label required">Treaty
                                                                        Limit</label>
                                                                    <input type="text"
                                                                        class="form-inputs surp_treaty_limit tpr_section"
                                                                        aria-label="surp_treaty_limit"
                                                                        class="surp_treaty_limit" id="surp_treaty_limit-0"
                                                                        name="surp_treaty_limit[]" data-counter="0"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required disabled>
                                                                </div>

                                                            </div>
                                                            <div class="row">
                                                                <!--Estimated Income-->
                                                                <div class="col-sm-3 estimated_income_div tpr_section_div">
                                                                    <label class="required ">Estimated Income</label>
                                                                    <input type="text"
                                                                        class="form-inputs estimated_income tpr_section"
                                                                        aria-label="estimated_income" class=""
                                                                        id="estimated_income-0" name="estimated_income[]"
                                                                        data-counter="0"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required>
                                                                </div>

                                                                <!--Cash Loss Limit-->
                                                                <div class="col-sm-3 cashloss_limit_div tpr_section_div">
                                                                    <label class="required ">Cash Loss Limit</label>
                                                                    <input type="text"
                                                                        class="form-inputs cashloss_limit tpr_section"
                                                                        aria-label="cashloss_limit" id="cashloss_limit-0"
                                                                        name="cashloss_limit[]" data-counter="0"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <div class="mt-2 comm-section tpr_section_div"
                                                                id="comm-section-0">
                                                                <h7> Commission Section </h7>
                                                                <div class="row comm-sections tpr_section_div"
                                                                    id="comm-section-0-0" data-class-counter="0"
                                                                    data-counter="0">

                                                                    <!--treaty-->

                                                                    <div
                                                                        class="col-sm-3 prem_type_treaty_div tpr_section_div">
                                                                        <label class="required ">Treaty</label>
                                                                        <select
                                                                            class="form-inputs select2 prem_type_treaty tpr_section"
                                                                            name="prem_type_treaty[]" data-reinclass=""
                                                                            data-class-counter="0" data-counter="0"
                                                                            id="prem_type_treaty-0-0" required>
                                                                        </select>
                                                                    </div>
                                                                    <!--reinsurance premium types-->
                                                                    <div
                                                                        class="col-sm-3 prem_type_code_div tpr_section_div">
                                                                        <label class="required ">Premium Type</label>
                                                                        <input type="hidden"
                                                                            class="form-inputs prem_type_reinclass tpr_section"
                                                                            id="prem_type_reinclass-0-0"
                                                                            name="prem_type_reinclass[]" data-counter="0">

                                                                        <select
                                                                            class="form-inputs select2 prem_type_code tpr_section"
                                                                            name="prem_type_code[]" data-counter="0"
                                                                            id="prem_type_code-0-0" data-reinclass=""
                                                                            data-class-counter="0" data-treaty=""
                                                                            required>
                                                                        </select>
                                                                    </div>

                                                                    <div
                                                                        class="col-sm-3 prem_type_comm_rate_div tpr_section_div">
                                                                        <label class="required ">Commision(%)</label>
                                                                        <div class="input-group mb-3">
                                                                            <input type="text"
                                                                                class="form-control prem_type_comm_rate tpr_section"
                                                                                name="prem_type_comm_rate[]"
                                                                                data-counter="0"
                                                                                id="prem_type_comm_rate-0-0" required>
                                                                            <button
                                                                                class="btn btn-primary add-comm-section"
                                                                                type="button" id="add-comm-section-0-0"
                                                                                data-counter="0">
                                                                                <i class="fa fa-plus"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        @php
                                                            $sections = [
                                                                'A',
                                                                'B',
                                                                'C',
                                                                'D',
                                                                'E',
                                                                'F',
                                                                'G',
                                                                'H',
                                                                'I',
                                                                'J',
                                                                'K',
                                                                'L',
                                                                'M',
                                                                'N',
                                                            ];
                                                        @endphp
                                                        @foreach ($coverreinpropClasses as $index => $coverreinpropCls)
                                                            <div class="row reinclass-section "
                                                                id="reinclass-section-{{ $loop->index }}"
                                                                data-counter="{{ $loop->index }}">
                                                                <h6 class="section-title tpr_section_div">Section
                                                                    {{ $sections[$index] }}</h6>

                                                                <div class="row mb-2">

                                                                    <!--reinsurance main class-->
                                                                    <div class="col-sm-3 treaty_reinclass tpr_section_div">
                                                                        <label class="form-label required">Reinsurance
                                                                            Class</label>
                                                                        <select
                                                                            class="form-inputs select2 treaty_reinclass tpr_section"
                                                                            name="treaty_reinclass[]"
                                                                            id="treaty_reinclass-{{ $loop->index }}"
                                                                            data-counter="{{ $loop->index }}" required>
                                                                            <option value="">Choose Reinsurance
                                                                                Class</option>
                                                                            @foreach ($reinsclasses as $reinsclass)
                                                                                <option
                                                                                    value="{{ $reinsclass->class_code }}"
                                                                                    @if ($reinsclass->class_code == $coverreinpropCls->reinclass) selected @endif>
                                                                                    {{ $reinsclass->class_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                @php
                                                                    $quotaCoverreinprop = $coverreinpropCls
                                                                        ->where(
                                                                            'reinclass',
                                                                            $coverreinpropCls->reinclass,
                                                                        )
                                                                        ->where('item_description', 'QUOTA')
                                                                        ->first();

                                                                    $surpCoverreinprop = $coverreinpropCls
                                                                        ->where(
                                                                            'reinclass',
                                                                            $coverreinpropCls->reinclass,
                                                                        )
                                                                        ->where('item_description', 'SURPLUS')
                                                                        ->first();
                                                                    $classPremTypes = $premtypes
                                                                        ->where(
                                                                            'reinclass',
                                                                            $coverreinpropCls->reinclass,
                                                                        )
                                                                        ->all();
                                                                    $reinClassPremTypes = $reinPremTypes
                                                                        ->where(
                                                                            'reinclass',
                                                                            $coverreinpropCls->reinclass,
                                                                        )
                                                                        ->all();
                                                                @endphp
                                                                <div class="row quota_header_div tpr_section_div"
                                                                    style="display: none">
                                                                    <h6> Quota Share </h6>
                                                                </div>
                                                                <div class="row">
                                                                    <!--quota limit-->
                                                                    <div class="col-sm-2 quota_share_total_limit_div tpr_section_div"
                                                                        id="quota_share_total_limit_div"
                                                                        @if ($quotaCoverreinprop->item_description == 'SURP') hidden @endif>

                                                                        <label class="form-label required">100% Quota
                                                                            Share Limit</label>
                                                                        <input type="text"
                                                                            class="form-inputs quota_share_total_limit tpr_section"
                                                                            aria-label="quota_share_total_limit"
                                                                            id="quota_share_total_limit-{{ $loop->index }}"
                                                                            data-counter="{{ $loop->index }}"
                                                                            name="quota_share_total_limit[]"
                                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                                            value="{{ number_format($quotaCoverreinprop->treaty_limit, 2) }}"
                                                                            required>
                                                                    </div>

                                                                    <!--Retention (%)-->
                                                                    <div class="col-sm-1 retention_per_div tpr_section_div"
                                                                        id="retention_per_div">
                                                                        <label
                                                                            class="form-label required">Retention(%)</label>
                                                                        <input type="number"
                                                                            class="form-inputs retention_per tpr_section"
                                                                            aria-label="retention_per"
                                                                            id="retention_per-{{ $loop->index }}"
                                                                            data-counter="{{ $loop->index }}"
                                                                            name="retention_per[]" min="0"
                                                                            max="100"
                                                                            value="{{ number_format($quotaCoverreinprop->retention_rate, 2) }}"
                                                                            required>
                                                                    </div>

                                                                    <!--Retention Amount-->
                                                                    <div class="col-sm-3 quota_retention_amt_div tpr_section_div"
                                                                        id="quota_retention_amt_div"
                                                                        @if ($quotaCoverreinprop->item_description != 'QUOTA') hidden @endif>
                                                                        <label class="form-label required">Retention
                                                                            Amount</label>
                                                                        <input type="text"
                                                                            class="form-inputs quota_retention_amt tpr_section"
                                                                            aria-label="quota_retention_amt"
                                                                            id="quota_retention_amt-{{ $loop->index }}"
                                                                            name="quota_retention_amt[]"
                                                                            data-counter="{{ $loop->index }}"
                                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                                            value="{{ number_format($quotaCoverreinprop->retention_amount, 2) }}"
                                                                            required disabled>
                                                                    </div>

                                                                    <!--treaty share(%)-->
                                                                    <div class="col-sm-2 treaty_reice_div tpr_section_div"
                                                                        id="treaty_reice_div">
                                                                        <label class="form-label required">Treaty
                                                                            (%)
                                                                        </label>
                                                                        <input type="number"
                                                                            class="form-inputs treaty_reice tpr_section"
                                                                            aria-label="treaty_reice"
                                                                            id="treaty_reice-{{ $loop->index }}"
                                                                            name="treaty_reice[]"
                                                                            data-counter="{{ $loop->index }}"
                                                                            min="0" max="100"
                                                                            value="{{ number_format($quotaCoverreinprop->treaty_rate, 2) }}"
                                                                            required>
                                                                    </div>

                                                                    <!--treaty limit-->
                                                                    <div class="col-sm-3 quota_treaty_limit_div tpr_section_div"
                                                                        id="quota_treaty_limit_div"
                                                                        @if ($quotaCoverreinprop->item_description == 'SURP') hidden @endif>
                                                                        <label class="form-label required">Treaty
                                                                            Limit</label>
                                                                        <input type="text"
                                                                            class="form-inputs quota_treaty_limit tpr_section"
                                                                            aria-label="quota_treaty_limit"
                                                                            class="quota_treaty_limit"
                                                                            id="quota_treaty_limit-{{ $loop->index }}"
                                                                            name="quota_treaty_limit[]"
                                                                            data-counter="{{ $loop->index }}"
                                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                                            value="{{ number_format($quotaCoverreinprop->treaty_amount, 2) }}"required
                                                                            disabled>
                                                                    </div>

                                                                </div>

                                                                <div class="row surp_header_div tpr_section_div"
                                                                    @if ($surpCoverreinprop->item_description != 'SURPLUS') hidden @endif
                                                                    data-counter="{{ $loop->index }}">
                                                                    <h6> Surplus </h6>
                                                                </div>

                                                                <div class="row">

                                                                    <!--Retention Amount-->
                                                                    <div class="col-sm-3 surp_retention_amt_div tpr_section_div"
                                                                        id="surp_retention_amt_div "
                                                                        @if ($surpCoverreinprop->item_description != 'SURPLUS') hidden @endif>
                                                                        {{-- <h6> Surplus </h6> --}}
                                                                        <label class="form-label required">Retention
                                                                            Amount</label>
                                                                        <input type="text"
                                                                            class="form-inputs surp_retention_amt tpr_section"
                                                                            aria-label="surp_retention_amt"
                                                                            id="surp_retention_amt-{{ $loop->index }}"
                                                                            name="surp_retention_amt[]"
                                                                            data-counter="{{ $loop->index }}"
                                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                                            value="{{ number_format($surpCoverreinprop->retention_amount, 2) }}"required
                                                                            disabled>
                                                                    </div>

                                                                    <!--no of lines-->
                                                                    <div class="col-sm-3 no_of_lines_div tpr_section_div"
                                                                        id="no_of_lines_div"
                                                                        @if ($surpCoverreinprop->item_description != 'SURPLUS') hidden @endif>
                                                                        <label class="form-label required">No of
                                                                            Lines</label>
                                                                        <input type="number"
                                                                            class="form-inputs no_of_lines tpr_section"
                                                                            aria-label="no_of_lines"
                                                                            id="no_of_lines-{{ $loop->index }}"
                                                                            data-counter="{{ $loop->index }}"
                                                                            name="no_of_lines[]"
                                                                            value="{{ number_format($surpCoverreinprop->no_of_lines, 2) }}"required>
                                                                    </div>

                                                                    <!--treaty limit-->
                                                                    <div class="col-sm-3 surp_treaty_limit_div tpr_section_div"
                                                                        id="surp_treaty_limit_div"
                                                                        @if ($surpCoverreinprop->item_description != 'SURPLUS') hidden @endif>
                                                                        <label class="form-label required">Treaty
                                                                            Limit</label>
                                                                        <input type="text"
                                                                            class="form-inputs surp_treaty_limit tpr_section"
                                                                            aria-label="surp_treaty_limit"
                                                                            class="surp_treaty_limit"
                                                                            id="surp_treaty_limit-{{ $loop->index }}"
                                                                            name="surp_treaty_limit[]"
                                                                            data-counter="{{ $loop->index }}"
                                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                                            value="{{ number_format($surpCoverreinprop->treaty_amount, 2) }}"required
                                                                            disabled>
                                                                    </div>

                                                                </div>
                                                                <div class="row">
                                                                    <!--Estimated Income-->
                                                                    <div
                                                                        class="col-sm-3 estimated_income_div tpr_section_div">
                                                                        <label class="required ">Estimated
                                                                            Income</label>
                                                                        <input type="text"
                                                                            class="form-inputs estimated_income tpr_section"
                                                                            aria-label="estimated_income" class=""
                                                                            id="estimated_income-{{ $loop->index }}"
                                                                            name="estimated_income[]"
                                                                            data-counter="{{ $loop->index }}"
                                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                                            value="{{ number_format($surpCoverreinprop->estimated_income, 2) }}"
                                                                            required>
                                                                    </div>

                                                                    <!--Cash Loss Limit-->
                                                                    <div
                                                                        class="col-sm-3 cashloss_limit_div tpr_section_div">
                                                                        <label class="required ">Cash Loss
                                                                            Limit</label>
                                                                        <input type="text"
                                                                            class="form-inputs cashloss_limit tpr_section"
                                                                            aria-label="cashloss_limit"
                                                                            id="cashloss_limit-{{ $loop->index }}"
                                                                            name="cashloss_limit[]"
                                                                            data-counter="{{ $loop->index }}"
                                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                                            value="{{ number_format($surpCoverreinprop->cashloss_limit, 2) }}"
                                                                            required>
                                                                    </div>
                                                                </div>

                                                                <div class="mt-2 comm-section tpr_section_div"
                                                                    id="comm-section-{{ $loop->index }}">
                                                                    <h7> Commission Section </h7>
                                                                    @foreach ($classPremTypes as $premType)
                                                                        <div class="row comm-sections tpr_section_div"
                                                                            id="comm-section-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                            data-class-counter="{{ $loop->parent->index }}"
                                                                            data-counter="{{ $loop->index }}">

                                                                            <!--treaty-->
                                                                            <div
                                                                                class="col-sm-3 prem_type_treaty_div tpr_section_div">
                                                                                <label class="required ">Treaty</label>
                                                                                <select
                                                                                    class="form-inputs select2 prem_type_treaty tpr_section"
                                                                                    name="prem_type_treaty[]"
                                                                                    data-reinclass=""
                                                                                    data-class-counter="{{ $loop->parent->index }}"
                                                                                    data-counter="{{ $loop->index }}"
                                                                                    id="prem_type_treaty-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                                    required>
                                                                                    @foreach ($treatytypes as $treatytype)
                                                                                        @if ($treatytype->treaty_code == $premType->treaty)
                                                                                            <option
                                                                                                value="{{ $treatytype->treaty_code }}"
                                                                                                selected>
                                                                                                {{ $treatytype->treaty_name }}
                                                                                            </option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <!--reinsurance premium types-->
                                                                            <div
                                                                                class="col-sm-3 prem_type_code_div tpr_section_div">
                                                                                <label class="required ">Premium
                                                                                    Type</label>
                                                                                <input type="hidden"
                                                                                    class="form-inputs prem_type_reinclass tpr_section"
                                                                                    id="prem_type_reinclass-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                                    name="prem_type_reinclass[]"
                                                                                    data-counter="{{ $loop->index }}">

                                                                                <select
                                                                                    class="form-inputs select2 prem_type_code tpr_section"
                                                                                    name="prem_type_code[]"
                                                                                    data-counter="{{ $loop->index }}"
                                                                                    id="prem_type_code-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                                    data-reinclass=""
                                                                                    data-class-counter="{{ $loop->parent->index }}"
                                                                                    data-treaty="" required>
                                                                                    @foreach ($reinClassPremTypes as $reinPremType)
                                                                                        <option
                                                                                            value="{{ $reinPremType->premtype_code }}"
                                                                                            @if ($premType->premtype_code == $reinPremType->premtype_code) selected @endif>
                                                                                            {{ $reinPremType->premtype_name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div
                                                                                class="col-sm-3 prem_type_comm_rate_div tpr_section_div">
                                                                                <label
                                                                                    class="required ">Commision(%)</label>
                                                                                <div class="input-group mb-3">
                                                                                    <input type="text"
                                                                                        class="form-control prem_type_comm_rate tpr_section"
                                                                                        name="prem_type_comm_rate[]"
                                                                                        data-counter="{{ $loop->index }}"
                                                                                        id="prem_type_comm_rate-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                                        value="{{ number_format($premType->comm_rate, 2) }}">
                                                                                    @if ($loop->first)
                                                                                        <button
                                                                                            class="btn btn-primary add-comm-section"
                                                                                            type="button"
                                                                                            id="add-comm-section-{{ $loop->parent->index }}-{{ $loop->index }}"
                                                                                            data-counter="{{ $loop->parent->index }}">
                                                                                            <i class="fa fa-plus"></i>
                                                                                        </button>
                                                                                    @else
                                                                                        <button
                                                                                            class="btn btn-danger remove-comm-section"
                                                                                            type="button"
                                                                                            id="remove-comm-section-{{ $loop->parent->index }}-{{ $loop->index }}"><i
                                                                                                class="fa fa-minus"></i></button>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <div id="tnp_section">
                                                <div class="row">
                                                    <!--reinsurance main class-->
                                                    <div class="col-sm-3 reinclass_code tnp_section_div">
                                                        <label class="form-label required">Reinsurance Class</label>
                                                        <select class="form-inputs select2 reinclass_code tnp_section"
                                                            name="reinclass_code[]" id="tnp_reinclass_code" multiple
                                                            required>
                                                            @if ($trans_type == 'NEW')
                                                                {{-- <option value="">Choose Reinsurance Class</option> --}}
                                                                @foreach ($reinsclasses as $reinsclass)
                                                                    <option value="{{ $reinsclass->class_code }}">
                                                                        {{ $reinsclass->class_name }}</option>
                                                                @endforeach
                                                            @elseif(
                                                                $trans_type == 'EXT' ||
                                                                    $trans_type == 'CNC' ||
                                                                    $trans_type == 'REN' ||
                                                                    $trans_type == 'RFN' ||
                                                                    $trans_type == 'NIL' ||
                                                                    $trans_type == 'INS' ||
                                                                    $trans_type == 'EDIT')
                                                                @foreach ($reinsclasses as $reinsclass)
                                                                    @if ($reinsclass->class_code == $old_endt_trans->reinsclass_code)
                                                                        <option value="{{ $reinsclass->class_code }}"
                                                                            selected>
                                                                            {{ $reinsclass->class_name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <!--Burning Cost (B) / Flat Rate (F)-->
                                                    <div class="col-sm-3 method tnp_section_div">
                                                        <label class="required method ">Burning Cost (B) / Flat Rate
                                                            (F)</label>
                                                        <select name="method" id="method"
                                                            class="form-inputs method tnp_section">
                                                            <option value="">-- Select Method --</option>
                                                            <option value="B"
                                                                @if (!empty($old_endt_trans) && $old_endt_trans->method == 'B') selected @endif>
                                                                Burning
                                                                Cost (B)</option>
                                                            <option value="F"
                                                                @if (!empty($old_endt_trans) && $old_endt_trans->method == 'F') selected @endif>Flat
                                                                Rate (F)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group tnp_section_div" id="layer-section">
                                                <h4> Layers Section </h4>
                                                <button class="btn btn-primary" type="button" id="add-layer-section">
                                                    <i class="fa fa-plus"></i>Add Layer
                                                </button>
                                                @if ($trans_type != 'EDIT')
                                                    <h6> Layer: 1 </h6>
                                                    <div class="layer-sections" id="layer-section-0" data-counter="0">
                                                        <div class="row">
                                                            <div class="col-sm-2 limit_per_reinclass_div">
                                                                <label class="form-label required">Capture Limits per
                                                                    Class?</label>
                                                                <select class="form-inputs limit_per_reinclass"
                                                                    name="limit_per_reinclass"
                                                                    id="limit_per_reinclass-0-0" required>
                                                                    <option value="">Select Option</option>
                                                                    <option value="N" selected>No</option>
                                                                    <option value="Y">Yes</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-1">
                                                                <label class="form-label required">Reinclass</label>
                                                                <input type="hidden" class="form-control layer_no"
                                                                    id="layer_no-0-0" name="layer_no[]" value="1"
                                                                    readonly>
                                                                <input type="hidden"
                                                                    class="form-control nonprop_reinclass"
                                                                    id="nonprop_reinclass-0-0"
                                                                    name="nonprop_reinclass[]" value="ALL" readonly>
                                                                <input type="text"
                                                                    class="form-control nonprop_reinclass_desc"
                                                                    id="nonprop_reinclass_desc-0-0"
                                                                    name="nonprop_reinclass_desc[]" value="ALL"
                                                                    readonly>
                                                            </div>

                                                            <!-- Indemnity -->
                                                            <div class="col-sm-2 indemnity_treaty_limit_div">
                                                                <label class="form-label required">Limit</label>
                                                                <input type="text"
                                                                    class="form-inputs indemnity_treaty_limit"
                                                                    id="indemnity_treaty_limit-0-0"
                                                                    name="indemnity_treaty_limit[]"
                                                                    onkeyup="this.value=numberWithCommas(this.value)">
                                                            </div>


                                                            <!-- Deductible Amount -->
                                                            <div class="col-sm-2 underlying_limit_div tnp_section_div">
                                                                <label class="form-label required">Deductible
                                                                    Amount</label>
                                                                <input type="text"
                                                                    class="form-inputs underlying_limit tnp_section"
                                                                    aria-label="underlying_limit"
                                                                    id="underlying_limit-0-0" name="underlying_limit[]"
                                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                                    required>
                                                            </div>
                                                            {{-- added this remove wen necessary --}}
                                                            <!-- EGNPI (Estimated Premium) -->
                                                            <div class="col-sm-2 egnpi_div tnp_section_div">
                                                                <label class="form-label required">EGNPI</label>
                                                                <input type="text"
                                                                    class="form-inputs egnpi tnp_section"
                                                                    aria-label="egnpi" id="egnpi-0-0" name="egnpi[]"
                                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                                    required>
                                                            </div>

                                                            <!-- Burning Cost - Minimum Rate -->
                                                            <div class="col-sm-3 burning_rate_div tnp_section_div">
                                                                <label class="form-label required">Burning Cost-Minimum
                                                                    Rate(%)</label>
                                                                <input type="text" name="min_bc_rate[]"
                                                                    id="min_bc_rate-0-0"
                                                                    class="form-inputs burning_rate tnp_section" required>
                                                            </div>

                                                            <!-- Maximum Rate -->
                                                            <div class="col-sm-2 burning_rate_div tnp_section_div">
                                                                <label class="form-label required">Maximum Rate:
                                                                    (%)</label>
                                                                <input type="text" name="max_bc_rate[]"
                                                                    id="max_bc_rate-0-0"
                                                                    class="form-inputs burning_rate tnp_section" required>
                                                            </div>

                                                            <!-- Flat Rate -->
                                                            <div class="col-sm-2 flat_rate_div tnp_section_div">
                                                                <label class="form-label required">For Flat Rate:
                                                                    (%)</label>
                                                                <input type="text" name="flat_rate[]"
                                                                    id="flat_rate-0-0"
                                                                    class="form-inputs flat_rate tnp_section" required>
                                                            </div>

                                                            <!-- Upper Adjustable Annually Rate -->
                                                            <div class="col-sm-3 burning_rate_div tnp_section_div">
                                                                <label class="form-label required">Upper Adjust.
                                                                    Annually Rate</label>
                                                                <input type="text" name="upper_adj[]"
                                                                    id="upper_adj-0-0"
                                                                    class="form-inputs burning_rate tnp_section" required>
                                                            </div>

                                                            <!-- Lower Adjustable Annually Rate -->
                                                            <div class="col-sm-3 burning_rate_div tnp_section_div">
                                                                <label class="form-label required">Lower Adjust.
                                                                    Annually Rate</label>
                                                                <input type="text" name="lower_adj[]"
                                                                    id="lower_adj-0-0"
                                                                    class="form-inputs burning_rate tnp_section" required>
                                                            </div>

                                                            <!-- Minimum Premium Deposit -->
                                                            <div class="col-sm-3 min_deposit_div tnp_section_div">
                                                                <label class="form-label required">Minimum Deposit
                                                                    Premium</label>
                                                                <div class="input-group mb-3">
                                                                    <input type="text" name="min_deposit[]"
                                                                        id="min_deposit-0-0"
                                                                        class="form-control min_deposit tnp_section"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required>
                                                                </div>
                                                            </div>

                                                            <!-- Reinstatement Type -->
                                                            <div class="col-sm-3 reinstatement_type_div tnp_section_div">
                                                                <label class="form-label required">Reinstatement
                                                                    Type</label>
                                                                <div class="input-group mb-3">
                                                                    <select name="reinstatement_type[]"
                                                                        id="reinstatement_type-0-0"
                                                                        class="form-inputs select2">
                                                                        <option value="">Please Select</option>
                                                                        <option value="NOR">Number of Reinstatement
                                                                        </option>
                                                                        <option value="AAL">Annual Aggregate Limit
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <!-- Reinstatement Value -->
                                                            <div class="col-sm-3 reinstatement_value_div tnp_section_div">
                                                                <label class="form-label required">Reinstatement
                                                                    Value</label>
                                                                <div class="input-group mb-3">
                                                                    <input type="text" name="reinstatement_value[]"
                                                                        id="reinstatement_value-0-0"
                                                                        class="form-control reinstatement_value tnp_section"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            {{-- end of remove wen necessary --}}

                                                        </div>
                                                    </div>
                                                @else
                                                    @foreach ($coverReinLayers as $reinLayer)
                                                        @php
                                                            $perClass =
                                                                $coverReinLayers
                                                                    ->where('reinclass', '<>', 'ALL')
                                                                    ->count() > 0
                                                                    ? 'Y'
                                                                    : 'N';
                                                        @endphp
                                                        <h6> Layer: {{ $loop->iteration }} </h6>
                                                        <div class="layer-sections"
                                                            id="layer-section-{{ $loop->index }}"
                                                            data-counter="{{ $loop->index }}">
                                                            <div class="row">
                                                                <div class="col-sm-2 limit_per_reinclass_div">
                                                                    <label class="form-label required">Capture Limits
                                                                        per Class?</label>
                                                                    <select class="form-inputs limit_per_reinclass"
                                                                        name="limit_per_reinclass"
                                                                        id="limit_per_reinclass-{{ $loop->index }}-0"
                                                                        required>
                                                                        <option value="">Select Option</option>
                                                                        <option value="N"
                                                                            @if ($perClass == 'N') selected @endif>
                                                                            No
                                                                        </option>
                                                                        <option value="Y"
                                                                            @if ($perClass == 'Y') selected @endif>
                                                                            Yes
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-1">
                                                                    <label class="form-label required">Reinclass</label>
                                                                    <input type="hidden" class="form-control layer_no"
                                                                        id="layer_no-{{ $loop->index }}-0"
                                                                        name="layer_no[]" value="1" readonly>
                                                                    <input type="hidden"
                                                                        class="form-control nonprop_reinclass"
                                                                        id="nonprop_reinclass-{{ $loop->index }}-0"
                                                                        name="nonprop_reinclass[]"
                                                                        value="{{ $reinLayer->reinclass }}" readonly>
                                                                    <input type="text"
                                                                        class="form-control nonprop_reinclass_desc"
                                                                        id="nonprop_reinclass_desc-{{ $loop->index }}-0"
                                                                        name="nonprop_reinclass_desc[]"
                                                                        value="{{ $reinLayer->reinclass }}" readonly>
                                                                </div>

                                                                <!-- Indemnity -->
                                                                <div class="col-sm-2 indemnity_treaty_limit_div">
                                                                    <label class="form-label required">Limit</label>
                                                                    <input type="text"
                                                                        class="form-inputs indemnity_treaty_limit"
                                                                        id="indemnity_treaty_limit-{{ $loop->index }}-0"
                                                                        value="{{ number_format($reinLayer->indemnity_limit, 2) }}"
                                                                        name="indemnity_treaty_limit[]"
                                                                        onkeyup="this.value=numberWithCommas(this.value)">
                                                                </div>

                                                                <!--Underlying Limit-->
                                                                <div
                                                                    class="col-sm-2 underlying_limit_div tnp_section_div">
                                                                    <label class="form-label required">Deductible
                                                                        Amount</label>
                                                                    <input type="text"
                                                                        class="form-inputs underlying_limit tnp_section"
                                                                        aria-label="underlying_limit"
                                                                        id="underlying_limit-{{ $loop->index }}-0"
                                                                        value="{{ number_format($reinLayer->underlying_limit, 2) }}"
                                                                        name="underlying_limit[]"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required>
                                                                </div>

                                                                <!--EGNPI (Estimated Premium)-->
                                                                <div class="col-sm-2 egnpi_div tnp_section_div">
                                                                    <label class="form-label required">EGNPI</label>
                                                                    <input type="text"
                                                                        class="form-inputs egnpi tnp_section"
                                                                        aria-label="egnpi"
                                                                        id="egnpi-{{ $loop->index }}-0"
                                                                        value="{{ number_format($reinLayer->egnpi, 2) }}"
                                                                        name="egnpi[]"
                                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                                        required>
                                                                </div>

                                                                <!--For Burning Cost (B) --- Minimum Rate: (%)-->
                                                                <div class="col-sm-3 burning_rate_div tnp_section_div">
                                                                    <label class="form-label required">Burning
                                                                        Cost-Minimum Rate(%)</label>
                                                                    <input type="text" name="min_bc_rate[]"
                                                                        id="min_bc_rate-{{ $loop->index }}-0"
                                                                        class="form-inputs burning_rate tnp_section"
                                                                        value="{{ number_format($reinLayer->min_bc_rate, 2) }}">
                                                                </div>

                                                                <!--Maximum Rate: (%)-->
                                                                <div class="col-sm-2 burning_rate_div tnp_section_div">
                                                                    <label class="form-label required">Maximum Rate:
                                                                        (%)
                                                                    </label>
                                                                    <input type="text" name="max_bc_rate[]"
                                                                        id="max_bc_rate-{{ $loop->index }}-0"
                                                                        class="form-inputs burning_rate tnp_section"
                                                                        value="{{ number_format($reinLayer->max_bc_rate, 2) }}">
                                                                </div>

                                                                <!--For Flat Rate: (%)-->
                                                                <div class="col-sm-2 flat_rate_div tnp_section_div ">
                                                                    <label class="form-label required">For Flat Rate:
                                                                        (%)</label>
                                                                    <input type="text" name="flat_rate[]"
                                                                        id="flat_rate-{{ $loop->index }}-0"
                                                                        class="form-inputs flat_rate tnp_section"
                                                                        value="{{ number_format($reinLayer->flat_rate, 2) }}"
                                                                        @if ($old_endt_trans->method == 'B') required readonly @endif>
                                                                </div>

                                                                <!--Adjustable Annually Rate-->
                                                                <div class="col-sm-3 burning_rate_div tnp_section_div">
                                                                    <label class="form-label required">Upper Adjust.
                                                                        Annually Rate</label>
                                                                    <input type="text" name="upper_adj[]"
                                                                        id="upper_adj-{{ $loop->index }}-0"
                                                                        class="form-inputs burning_rate tnp_section"
                                                                        value="{{ number_format($reinLayer->upper_adj, 2) }}"
                                                                        required>
                                                                </div>

                                                                <!--Adjustable Annually Rate-->
                                                                <div class="col-sm-3 burning_rate_div tnp_section_div">
                                                                    <label class="form-label required">Lower Adjust.
                                                                        Annually Rate</label>
                                                                    <input type="text" name="lower_adj[]"
                                                                        id="lower_adj-{{ $loop->index }}-0"
                                                                        class="form-inputs burning_rate tnp_section"
                                                                        value="{{ number_format($reinLayer->lower_adj, 2) }}"
                                                                        required>
                                                                </div>

                                                                <!--Minimum Premium Deposit-->
                                                                <div class="col-sm-3 min_deposit_div tnp_section_div">
                                                                    <label class="form-label required">Minimum Deposit
                                                                        Premium </label>
                                                                    <div class="input-group mb-3">
                                                                        <input type="text" name="min_deposit[]"
                                                                            id="min_deposit-{{ $loop->index }}-0"
                                                                            class="form-control min_deposit tnp_section"
                                                                            value="{{ number_format($reinLayer->min_deposit, 2) }}"
                                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                                            required>
                                                                    </div>
                                                                </div>

                                                                {{-- Reinstatement Type Arrangement --}}
                                                                <div
                                                                    class="col-sm-3 reinstatement_type_div tnp_section_div">
                                                                    <label class="form-label required"> Reinstatement
                                                                        Type </label>
                                                                    <div class="input-group mb-3">
                                                                        <select name="reinstatement_type[]"
                                                                            id="reinstatement_type-{{ $loop->index }}-0"
                                                                            class="form-inputs select2" required>
                                                                            <option value="">Please Select
                                                                            </option>
                                                                            <option value="NOR"
                                                                                @if ($reinLayer->reinstatement_type == 'NOR') selected @endif>
                                                                                Number
                                                                                of
                                                                                Reinstatement</option>
                                                                            <option value="AAL"
                                                                                @if ($reinLayer->reinstatement_type == 'AAL') selected @endif>
                                                                                Annual
                                                                                Aggregate Limit</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Reinstatement Type Value --}}
                                                                <div
                                                                    class="col-sm-3 reinstatement_value_div tnp_section_div">
                                                                    <label class="form-label required"> Reinstatement
                                                                        Value </label>
                                                                    <div class="input-group mb-3">
                                                                        <input type="text"
                                                                            name="reinstatement_value[]"
                                                                            id="reinstatement_value-{{ $loop->index }}-0"
                                                                            class="form-control reinstatement_value tnp_section"
                                                                            value="{{ $reinLayer->reinstatement_value }}"
                                                                            onkeyup="this.value=numberWithCommas(this.value)"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12" style="display:none" id="facstage_div">
                                    <x-SearchableSelect name="stage_cycle_fac" id="stage_cycle_fac" req="required"
                                        inputLabel="Facultative Cycle Stage">
                                        <option value="">Select stage</option>
                                        @foreach ($statuses as $status)
                                            <option data-value="{{ $status->lead_id }}" value="{{ $status->id }}">
                                                {{ $status->status_name }}</option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </div>
                                <div id="facfinalStage_div"style="display:none">


                                </div>

                                <!-- Search Bar -->
                                <div class="row" style="display: none" id="fac_search_reinsurer">
                                    <div class="col-11">
                                        <div class="label">Reinsurer<font style="color:red;">*</font>
                                        </div>

                                        <input type="text" id="facultative-search-bar" class="form-control"
                                            placeholder="Search for reinsurer..." />
                                    </div>
                                </div>
                                <div id="facultative-search-results" class="mt-2">

                                </div>

                                <!-- List of Selected Customers -->
                                <div id="facultative-selected-customers-list" class="mt-4">
                                    <div class="row p-3 mt-3 border rounded shadow-sm bg-light"
                                        id="fac_total_written_sh" style="display:none">
                                        <div #class="col-4">
                                            <label for="" class="fw-bold ">Total Written Share</label>
                                            <input type="number" id="written_share_total"
                                                name="updated_written_share_total" class="form-control " />
                                        </div>
                                        <div class="col-4">
                                            <label for="" class="fw-bold ">Distributed</label>
                                            <input type="number" id="fac_distributed" name="distributed"
                                                class="form-control" required readonly />
                                        </div>
                                        <div class="col-4">
                                            <label class="fw-bold">Undistributed</label>
                                            <input type="number" id="fac_undistributed" name="undistributed"
                                                class="form-control  bg-white text-danger" required readonly />
                                        </div>
                                    </div>


                                </div>
                                <div class="row p-3 mt-3 border rounded shadow-sm bg-light"
                                    id="fac_total_placed_unplaced" style="display:none">

                                    <input type="hidden" id="fac_written_share_total"
                                        class="form-control border-primary" required />

                                    {{-- <div class="col-4">
                                        <label for="" class="fw-bold ">Placed Share</label>
                                        <input type="number" id="placed" name="placed" class="form-control "
                                            required readonly />
                                    </div>
                                    <div class="col-4">
                                        <label class="fw-bold">Unplaced Share</label>
                                        <input type="number" id="unplaced" name="unplaced"
                                            class="form-control  bg-white text-danger" required readonly />
                                    </div> --}}
                                </div>
                                <div id="contacts-wrapper-fac">
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <label for="cedant_name" class="form-label fw-medium">Cedant</label>
                                            <input type="text" id="cedant_name_fac" class="form-control"
                                                placeholder="Enter cedant name">
                                        </div>
                                    </div>

                                </div>
                                <div id="facultativeFile" class="col-12">

                                </div>
                                <div class="row mt-1" id="email_attachment_file"
                                    style="padding-left: 10px; display:none;">
                                    <div class="row mt-1 email_attachment_file">
                                        <div class="col-auto" id="email_file_name">
                                            <div class="row">
                                                <x-Input req="" inputLabel="Document Title"
                                                    value="Email Attachment File"
                                                    name="document_name_email_attachment[]" />

                                            </div>
                                        </div>

                                        <div class="col-auto" id="email_file_attach">
                                            <label for="document_file">File</label>
                                            <div class="input-group">
                                                <input type="file" name="document_file_email_attachment[]"
                                                    class="form-control document_file " />

                                                <button id="add_attachment" class="btn btn-primary" type="button">
                                                    <i class="bx bx-plus add_attachment"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-auto" style="margin-top: 30px;">
                                            <i class="bx bx-show preview  optionFile" id="preview"
                                                style="cursor:pointer;"></i>
                                        </div>
                                    </div>
                                </div>


                                <div class="modal-footer">
                                    <button class="btn btn-outline-dark btn btn-wave waves-effect waves-light"
                                        id="printout_preview" style="display: none;" id="facultative_generate_slip"
                                        style="display: none">
                                        <i class="bx bx-analyse me-1 align-middle"></i> Preview
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="bx bx-x-circle"></i> Close
                                    </button>
                                    <button id="facultativeUpdateStatusBtn" type="submit"
                                        class="btn btn-outline-success">
                                        <i class="bx bx-check-circle"></i> Submit
                                    </button>

                                </div>
                            </form>
                        </div>



                    </div>
                </div>
            </div>
        </div>




        {{-- update category type --}}
        <div class="modal fade" id="updateCategoryTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Update Category Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('update.category_type') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="opportunity_id" id="opportunity_id" />
                            <div class="mb-3">
                                <label for="category_type" class="form-label">Category Type</label>
                                <select class="form-control form-control-sm" name="category_type" required>
                                    <option value="">Select Category</option>
                                    <option value="1">Normal</option>
                                    <option value="2">Tender</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="position: relative;">
                        <iframe id="docIframe" src=""
                            style="width: 100%; height: 80vh; border: none; overflow: auto;"></iframe>
                        <div class="iframe-overlay"></div>
                    </div>
                </div>
            </div>
        </div>




        <!-- send email Modal -->
        <div class="modal fade" id="sendEmail" tabindex="-1" role="dialog" aria-labelledby="sendemailLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Send Email Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Do you want to send an email?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary confirm-email" data-value="0"
                            data-dismiss="modal">No</button>
                        {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editEmail">
                        Edit Email
                    </button> --}}
                        <button type="button" class="btn btn-primary confirm-email" data-value="1">Yes</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editEmail" tabindex="-1" aria-labelledby="editEmailLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="firstModalLabel">Cedant Email</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <div class="container">
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="cc" class="form-label">Contact name</label>
                                        <input type="text" id="edit_contact_name" name="edit_contact_name"
                                            class="form-control" placeholder="Enter contact name">
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="cc" class="form-label">Primary Email</label>
                                        <input type="text" id="cedant_main_email" name="cedant_main_email"
                                            class="form-control" placeholder="Enter main email">
                                    </div>
                                    <div class="col-md-8 mb-3">
                                        <label for="cc" class="form-label">CC</label>
                                        <input type="text" id="ccInput" class="form-control"
                                            placeholder="Enter CC emails and press Enter">
                                        <div id="ccEmailsContainer" name="emailCC" class="mt-2"></div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success" id="editEmailBtn">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- printout type --}}
        <div class="modal fade" id="printoutType" tabindex="-1" role="dialog"
            aria-labelledby="printoutTypeLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Printout Type Confirmation</h5>
                        <button type="button" class="btn-close printtypeClose" data-bs-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>which printout do you want?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary confirm_print_type" data-value="1"
                            data-dismiss="modal">Word</button>
                        <button type="button" class="btn btn-primary confirm_print_type" data-value="2">Pdf</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <iframe id="filePreviewFrame" style="width:100%; height:80vh;" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="rej-text" tabindex="-1" aria-labelledby="filePreviewModalLabel1"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filePreviewModalLabel">Rejection Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea name="rejection_message" id="rejection_message" class="form-control" rows="10"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <form id="quoteSlipForm" method="POST" action="{{ route('docs.treatyBdPrintout') }}" target="_blank"
            style="display: none;">
            @csrf
        </form>



    @endsection
    @push('script')
        <script>
            $(document).ready(function() {

                var lead = {!! $pip !!};
                $('#optional1').hide(); // Initially hide the element
                $('#optional').hide();
                const customers = @json($customers);
                const Users = @json($users ?? []);
                const checklist_of_operations = @json($treaty_operation_checklists ?? []);




                // $('#optionFile').hide();

                $('.toggle-icon').click(function() {
                    var targetId = $(this).data('target'); // Get the ID of the target element
                    $(targetId).toggle(); // Toggle visibility of the corresponding content 
                    var icon = $(this).find('i'); // Find the icon inside the clicked span
                    // Toggle between plus and minus icon
                    if ($(targetId).is(":visible")) {
                        icon.removeClass('bx-plus').addClass('bx-minus');
                    } else {
                        icon.removeClass('bx-minus').addClass('bx-plus');
                    }
                });
                $('.facultative-toggle-icon').click(function() {
                    var targetId = $(this).data('target'); // Get the ID of the target element
                    $(targetId).toggle(); // Toggle visibility of the corresponding content 

                    var icon = $(this).find('i'); // Find the icon inside the clicked span

                    // Toggle between plus and minus icon
                    if ($(targetId).is(":visible")) {
                        icon.removeClass('bx-plus').addClass('bx-minus');
                    } else {
                        icon.removeClass('bx-minus').addClass('bx-plus');
                    }
                });
                let status;
                let sum_insured_type;
                let classgroup;
                let classname;
                let classcode;
                let category_type;
                let type_of_business;

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

                $('body').on('click', 'table tr .update_status', function() {
                    let opp_id = $(this).attr('data-opp')
                    let total_sum_insured = $(this).attr('data-sum-insured')
                    let premium = $(this).attr('data-premium')
                    let reins_comm_rate = $(this).attr('data-reins-comm-rate')
                    let cedant_comm_rate = $(this).attr('data-cedant-comm-rate')
                    let written_share_total = $(this).attr('data-fac-share-offered')
                    let data_exists_flag = $(this).attr('data-data-exist-flag')
                    let div_id = $(this).attr('data-division')
                    let stage_id = $(this).attr('data-stage')
                    $('#stage').val(stage_id)
                    classcode = $(this).attr('data-classcode')
                    classgroup = $(this).attr('data-classgroup')
                    let pip = lead
                    category_type = $(this).attr('data-category_type')
                    status = $(this).attr('data-status')
                    type_of_business = $(this).attr('data-type-of-bus')
                    sum_insured_type = $(this).attr('data-sum-insured-type')
                    let insured_name = $(this).attr('data-insured-name')
                    let cedant_name = $(this).attr('data-cedant')
                    $('#insured_name_qt').text(insured_name)
                    $('#insured_name_fc').text(insured_name)
                    $('#cedant').text(cedant_name)
                    let req = "";
                    if (stage_id < 4) {
                        $(document).on('change', '#sum_insured_type', function() {
                            setTimeout(function() {
                                filterSchedules();
                            }, 110);
                        });
                        $(document).on('change', ' #sum_insured_type_fac', function() {
                            setTimeout(function() {
                                filterSchedulesFac();
                            }, 110);
                        });
                    }

                    if (category_type == 1) {
                        $('#sum_insured_type').val(sum_insured_type).trigger('change');
                    } else if (category_type == 2) {
                        $('#sum_insured_type_fac').val(sum_insured_type).trigger('change');
                    }

                    $('#written_share_total').val(written_share_total)
                    $('.written_share_total').val(written_share_total)
                    $('#fac_written_share_total').val(written_share_total)
                    $('#qt_written_share_total').val(written_share_total)
                    $('#updated_written_share_qt').val(written_share_total)
                    $('#update_pip').val(pip)
                    $('#update_division').val(div_id)
                    $('#update_opp').val(opp_id)
                    $('#update_pipfac').val(pip)
                    $('#update_divisionfac').val(div_id)
                    $('#update_oppfac').val(opp_id)
                    $('#category_type').val(category_type)
                    $('#category_type2').val(category_type)
                    $('#stagecycleqt').val(stage_id)
                    $('#stage_cycle_fac').val(parseInt(stage_id) + 1).trigger('change');
                    $('#stage_cycle').val(parseInt(stage_id) + 1).trigger('change');

                    $('#qt_schedule_div').find('input, select, textarea, button').prop('disabled', false);
                    $('#qt_schedule_div').show();
                    if (stage_id == 1) {

                        $('.current_proposed').removeAttr('name');
                        $('.current_final').removeAttr('name');
                        $('#solvency_cr').removeAttr('name').removeAttr('required');
                        $('#mcr').removeAttr('name').removeAttr('required');
                        $('.operation-checkbox').removeAttr('name').removeAttr('required');
                        $('#quote_slip, #quote_div, #facultative_div, #fac_slip, #fac_search_reinsurer, #facultative_generate_slip,#fac_total_written_sh,#facultative-selected-customers-list')
                            .show();
                        $('#facfinalStage_div', '#fac_pl_update').hide();
                        $('#fac_pl_det').hide();
                        $('#qt_schedule_div').hide();
                        // $('#pipeline_update').hide();
                        $('#qt_schedule_div').find('input, select, textarea, button').prop('disabled', true);
                        processSections('.updated_written_share_total', '#qt_total_placed_unplaced', 'disable');
                        $('#pipeline_update').find('input, select, button, textarea').prop('disabled', true)
                            .removeAttr('required');
                    } else {
                        $('#quote_search_reinsurer, #facultative_div, #fac_slip, #fac_search_reinsurer,#quote_search_reinsurer, #quote_slip ,#fac_total_written_sh')
                            .hide();
                        processSections('.updated_written_share_total', '#qt_total_placed_unplaced', 'enable');

                    }
                    if (stage_id == 2) {
                        $('#facultative-selected-customers-list, #fac_pl_update').hide();
                        $('#tender_status').show();
                        $('#pipeline_update').find('input, select, button, textarea').prop('disabled', true)
                            .removeAttr('required');
                        // $('#pipeline_update').hide();

                    } else {
                        $('#facfinalStage_div, #fac_pl_update,#fac_total_placed_unplaced,#qt_total_written_sh,#fac_pl_ced,#qt_cedant_slip,#financial_statement_div,#checklist_of_operations_div,#email_attachment_file')
                            .hide();
                        $('.operation-checkbox').removeAttr('name').removeAttr('required');
                        $('#tender_status').find('input,select').prop('disabled', true)
                        .removeAttr('required');
                    }
                    if (stage_id == 3) {
                        $('#qt_cedant_slip, #quote_div, #facultative_div, #facultative_generate_slip, #fac_pl_ced,#fac_total_placed_unplaced ,#qt_total_placed_unplaced, #financial_statement_div,#financial_statement_div,#checklist_of_operations_div')
                            .show();
                        $('#facultative-selected-customers-list, #fac_pl_update').hide();
                        $('#pipeline_update').find('input, select, button, textarea').prop('disabled', true)
                            .removeAttr('required');
                      
                        // $('#pipeline_update').hide();

                    } else {
                        $('#facfinalStage_div, #fac_pl_update,#fac_total_placed_unplaced,#qt_total_written_sh,#contacts-wrapper-fac,#fac_pl_ced,#qt_cedant_slip,#financial_statement_div,#checklist_of_operations_div,#email_attachment_file')
                            .hide();
                        $('.operation-checkbox').removeAttr('name').removeAttr('required');

                    }

                    // if (stage_id == 4) {
                    //     $('#solvency_cr').removeAttr('name').removeAttr('required');
                    //     $('#mcr').removeAttr('name').removeAttr('required');
                    //     $('.operation-checkbox').removeAttr('name').removeAttr('required');
                    //     $('.operation-checkbox').prop('disabled', true)
                    //     $('#quote_div,#facultative_div,#quote_slip,#facultative_generate_slip,#tender_upload_doc,#qt_total_placed_unplaced,#fac_total_placed_unplaced,#final_document,#quote_search_reinsurer')
                    //         .show();
                    //     $('#fac_pl_det,#quote_slip,#contacts-wrapper-qt,#email_attachment_file').hide();
                    //     $('#pipeline_update').find('input, select, button, textarea').prop('disabled', true)
                    //         .removeAttr('required');
                        // $('#pipeline_update').hide();
                    // } else {

                        // $('#facstage_div').hide()

                        // $('#facstage_div, #fac_pl_update').hide();
                    // }

                    if (stage_id == 4) {
                        $('#solvency_cr').removeAttr('name').removeAttr('required');
                        $('#mcr').removeAttr('name').removeAttr('required');
                        $('.operation-checkbox').removeAttr('name').removeAttr('required');
                        $('#quote_slip,#facultative_div, #stage_div,#fac_pl_update,#pipeline_update')
                            .show();
                        $('#quote_div,#generate_slip,#fac_search_reinsurer,#quote_search_reinsurer,#qt_email_attachment_file,#facultative_div,#email_attachment_file,#contacts-wrapper-qt')
                            .hide();

                    } else {
                        $('#stage_div,#facstage_div').hide();
                        $('#qt_email_attachment_file').show();
                        // $('#email_attachment_file').show();
                    }

                    if (stage_id == 5) {
                        $('#solvency_cr').removeAttr('name').removeAttr('required');
                        $('#mcr').removeAttr('name').removeAttr('required');
                        $('.operation-checkbox').removeAttr('name').removeAttr('required');
                        $('#editStatusQuoteModal,#contacts-wrapper-qt').hide()
                        $('#facstage_div').show();

                        $('#quote_div').hide()
                        $('#updateQuoteFooter').hide()



                        // $('#editStatusFacultativeModal').modal('hide')

                    }






                    $('#stage_cycle option').each(function() {
                        var value = parseInt($(this).val());
                        if (value <= stage_id) {
                            $(this).prop('disabled', true);
                        }

                    });



                    $('#stage_cycle').select2();



                    $('#stage_cycle_fac option').each(function() {
                        var value = parseInt($(this).val());
                        if (value <= stage_id) {
                            $(this).prop('disabled', true);
                        }

                    });


                    $('#stage_cycle_fac').select2();


                    let prospect_name = $(this).closest('tr').find("td:first").text()
                    $('#ed_status_name').text(prospect_name)
                    $('#fac_ed_status_name').text(prospect_name)
                    if (stage_id == 5 && category_type == 1) {
                        toastr.warning('Cannot change status of a WON prospect.', {
                            timeOut: 5000
                        });
                        $('#updateStatusQuote').style('display:none');
                    }
                    if (stage_id == 6 && category_type == 1) {
                        toastr.warning('Cannot change status of a LOST prospect.', {
                            timeOut: 5000
                        });
                        $('#updateStatusQuote').style('display:none');
                    }

                    if (stage_id == 9 && category_type == 2) {
                        toastr.warning('Cannot change status of a WON prospect.', {
                            timeOut: 5000
                        });
                        $('#updateStatusFacultative').style('display:none');
                    }
                    if (stage_id == 10 && category_type == 2) {
                        toastr.warning('Cannot change status of a LOST prospect.', {
                            timeOut: 5000
                        });
                        $('#updateStatusFacultative').style('display:none');
                    } else {

                        if (category_type == 1) {
                            $('#editStatusQuoteModal').modal('show');
                        } else if (category_type == 2 && stage_id == 1) {
                            window.location.href = "{{ route('tender.list') }}" + "?opportunity_id=" + opp_id;

                            // $('#editStatusFacultativeModal').modal('show');
                        } else if (category_type == 2 && stage_id == 3 ){
                            window.location.href = "{{ route('tender.docs') }}" + "?opportunity_id=" + opp_id;

                        }else if (category_type == 2 && stage_id == 2  ||
                            category_type == 2 && stage_id == 4 || category_type == 2 && stage_id == 5 ) {
                            $('#editStatusFacultativeModal').modal('show');
                        }

                    }


                    if (stage_id < 4) {
                        if (category_type == 1) {
                            setTimeout(function() {
                                quote_classicEditor();
                            }, 100);

                        } else if (category_type == 2) {
                            setTimeout(function() {
                                fac_classicEditor();
                            }, 100);

                        }
                    }

                    $.ajax({
                        type: "get",
                        url: "{{ route('bus_type') }}",
                        data: {
                            opportunity_id: opp_id,

                        },
                        success: function(response) {
                            const selectedType = response.type_of_bus[0].type_of_bus;

                            // Set the value in the searchable select dropdown
                            const optionExists = $('#type_of_bus option').filter(function() {
                                return $(this).val() === selectedType;
                            }).length > 0;

                            if (optionExists) {
                                $('#type_of_bus').val(selectedType).trigger('change');

                            } else {
                                alert("No matching option found for:", selectedType);
                            }
                        }


                    });
                    modal_script()
                    // filter schedule heaers based on class 




                    //impliments autopopulate based on  dataType
                    function formatNumberJS(number) {
                        return new Intl.NumberFormat(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(number);
                    }
                    let formated_total_sum_insured = formatNumberJS(total_sum_insured);

                    let formated_premium = formatNumberJS(premium);


                    if (category_type == 1 && data_exists_flag == 'N') {
                        schedule.forEach(function(sch, index) {
                            const key = index;
                            let dataDeterminant = $('#data_determinant' + key).val();
                            if (dataDeterminant === 'SI') {
                                $('[name="schedule_details[' + key + '][amount]"]').val(
                                    formated_total_sum_insured);
                            } else if (dataDeterminant === 'PREM') {
                                $('[name="schedule_details[' + key + '][amount]"]').val(
                                    formated_premium);
                            } else if (dataDeterminant === 'COM' && stage_id == 2 && category_type ==
                                1) {
                                $('[name="schedule_details[' + key + '][amount]"]').val(
                                    cedant_comm_rate);
                                $('[name="schedule_details[' + key + '][name]"]').val(
                                    "Cedant Commission Rate");

                                // Update visible header with formatted text
                                let displayText = "Cedant Commission Rate";
                                if (displayText.toLowerCase().includes('commission')) {
                                    displayText += " (%)";
                                }
                                $('.schedule-name-qt[data-key="' + key + '"]').text(displayText);
                            } else if (dataDeterminant === 'COM') {
                                let displayText = "Reinsurer Commission Rate";
                                $('[name="schedule_details[' + key + '][amount]"]').val(
                                    reins_comm_rate);
                                $('[name="schedule_details[' + key + '][name]"]').val(displayText);

                                if (displayText.toLowerCase().includes('commission')) {
                                    displayText += " (%)";
                                }
                                $('.schedule-name-qt[data-key="' + key + '"]').text(displayText);

                            }

                        });
                    } else if (category_type == 2 && data_exists_flag == 'N') {
                        schedule.forEach(function(sch, index) {
                            const key = index;
                            let dataDeterminant = $('#data_determinant_fac' + key).val();
                            if (dataDeterminant === 'SI') {
                                $('[name="facschedule_details[' + key + '][amount]"]').val(
                                    formated_total_sum_insured);
                            } else if (dataDeterminant === 'PREM') {
                                $('[name="facschedule_details[' + key + '][amount]"]').val(
                                    formated_premium);
                            } else if (dataDeterminant === 'COM' && stage_id == 2 && category_type ==
                                2) {
                                $('[name="facschedule_details[' + key + '][amount]"]').val(
                                    cedant_comm_rate);
                                $('[name="facschedule_details[' + key + '][name]"]').val(
                                    "Cedant Commission Rate");

                                // Update visible header with formatted text
                                let displayText = "Cedant Commission Rate";
                                if (displayText.toLowerCase().includes('commission')) {
                                    displayText += " (%)";
                                }
                                $('.schedule-name[data-key="' + key + '"]').text(displayText);
                            } else if (dataDeterminant === 'COM') {
                                let displayText = "Reinsurer Commission Rate";
                                $('[name="facschedule_details[' + key + '][amount]"]').val(
                                    reins_comm_rate);
                                $('[name="facschedule_details[' + key + '][name]"]').val(displayText);

                                if (displayText.toLowerCase().includes('commission')) {
                                    displayText += " (%)";
                                }
                                $('.schedule-name[data-key="' + key + '"]').text(displayText);

                            }

                        });

                    }

                    function fetchQuoteSchedules(opp_id, stage_id, callback) {
                        $.ajax({
                            type: "get",
                            url: "{{ route('get_quote_schedules') }}",
                            data: {
                                'opportunity_id': opp_id,
                                'stage_id': stage_id
                            },
                            success: function(response) {
                                callback(response.quote_schedules);
                            }
                        });
                    }






                    //autopopulating data entered in the stage
                    setTimeout(function() {
                        if (category_type == 1 && data_exists_flag == 'Y') {
                            fetchQuoteSchedules(opp_id, stage_id, function(data) {
                                let quote_schedules_data = data;


                                schedule.forEach(function(sch, index) {
                                    const key = index;
                                    const editorKey = `editor` + key;
                                    let dataDeterminant = $('#data_determinant' + key)
                                        .val();
                                    const amountField = sch.amount_field === 'Y';
                                    const currentAmountField = $(
                                        '[name="schedule_details[' + key +
                                        '][current_amount]"]');
                                    const proposedAmountField = $(
                                        '[name="schedule_details[' + key +
                                        '][proposed_amount]"]');
                                    const finalAmountField = $(
                                        '[name="schedule_details[' + key +
                                        '][final_amount]"]');
                                    // if (dataDeterminant === 'SI') {
                                    //     $('[name="schedule_details[' + key +
                                    //         '][amount]"]').val(
                                    //         formated_total_sum_insured);
                                    // } else if (dataDeterminant === 'PREM') {
                                    //     $('[name="schedule_details[' + key +
                                    //         '][amount]"]').val(
                                    //         formated_premium);
                                    // } else if (dataDeterminant === 'COM' && stage_id ==
                                    //     2 &&
                                    //     category_type ==
                                    //     1) {
                                    //     $('[name="schedule_details[' + key +
                                    //         '][amount]"]').val(
                                    //         cedant_comm_rate);
                                    //     $('[name="schedule_details[' + key +
                                    //         '][name]"]').val(
                                    //         "Cedant Commission Rate");

                                    //     // Update visible header with formatted text
                                    //     let displayText = "Cedant Commission Rate";
                                    //     if (displayText.toLowerCase().includes(
                                    //             'commission')) {
                                    //         displayText += " (%)";
                                    //     }
                                    //     $('.schedule-name-qt[data-key="' + key + '"]')
                                    //         .text(
                                    //             displayText);
                                    // } else if (dataDeterminant === 'COM') {
                                    //     let displayText = "Reinsurer Commission Rate";
                                    //     $('[name="schedule_details[' + key +
                                    //         '][amount]"]').val(
                                    //         reins_comm_rate);
                                    //     $('[name="schedule_details[' + key +
                                    //         '][name]"]').val(
                                    //         displayText);

                                    //     if (displayText.toLowerCase().includes(
                                    //             'commission')) {
                                    //         displayText += " (%)";
                                    //     }
                                    //     $('.schedule-name-qt[data-key="' + key + '"]')
                                    //         .text(
                                    //             displayText);

                                    // } else 
                                    if (amountField) {
                                        let matchedItems = quote_schedules_data.filter(
                                            ({
                                                name,
                                                schedule_id
                                            }) => {
                                                return name === sch.name &&
                                                    schedule_id === sch
                                                    .id;
                                            });
                                        let combinedCurrent = matchedItems.map(item =>
                                            item
                                            .current).join("\n\n");
                                        let combinedProposed = matchedItems.map(item =>
                                            item
                                            .proposed).join("\n\n");

                                        let combinedFinal = matchedItems.map(item =>
                                            item
                                            .final).join("\n\n");

                                        currentAmountField.val(combinedCurrent ||
                                            '');
                                        proposedAmountField.val(combinedProposed ||
                                            '');
                                        finalAmountField.val(combinedFinal || '');
                                    } else {
                                        let matchedItems = quote_schedules_data.filter(
                                            ({
                                                name,
                                                schedule_id
                                            }) => {
                                                return name === sch.name &&
                                                    schedule_id === sch
                                                    .id;
                                            });

                                        if (matchedItems.length) {
                                            let combinedText = matchedItems.map(item =>
                                                item
                                                .details).join("\n\n");
                                            if (window[editorKey]) {
                                                window[editorKey].setData(
                                                    combinedText);
                                            } else {
                                                // console.log(
                                                //     `CKEditor instance ${editorKey} not found.`
                                                // );
                                            }

                                        }
                                    }

                                });
                            });
                        } else if (category_type == 2 && data_exists_flag == 'Y') {
                            fetchQuoteSchedules(opp_id, stage_id, function(data) {
                                let quote_schedules_data = data;
                                schedule.forEach(function(sch, index) {
                                    const key = index;
                                    const editorKey = 'fac_editor' + key;
                                    let dataDeterminant = $('#data_determinant_fac' +
                                        key).val();
                                    if (dataDeterminant === 'SI') {
                                        $('[name="facschedule_details[' + key +
                                            '][amount]"]').val(
                                            formated_total_sum_insured);
                                    } else if (dataDeterminant === 'PREM') {
                                        $('[name="facschedule_details[' + key +
                                            '][amount]"]').val(
                                            formated_premium);
                                    } else if (dataDeterminant === 'COM' && stage_id ==
                                        2 &&
                                        category_type ==
                                        2) {
                                        $('[name="facschedule_details[' + key +
                                            '][amount]"]').val(
                                            cedant_comm_rate);
                                        $('[name="facschedule_details[' + key +
                                            '][name]"]').val(
                                            "Cedant Commission Rate");

                                        // Update visible header with formatted text
                                        let displayText = "Cedant Commission Rate";
                                        if (displayText.toLowerCase().includes(
                                                'commission')) {
                                            displayText += " (%)";
                                        }
                                        $('.schedule-name[data-key="' + key + '"]')
                                            .text(
                                                displayText);
                                    } else if (dataDeterminant === 'COM') {
                                        let displayText = "Reinsurer Commission Rate";
                                        $('[name="facschedule_details[' + key +
                                            '][amount]"]').val(
                                            reins_comm_rate);
                                        $('[name="facschedule_details[' + key +
                                            '][name]"]').val(
                                            displayText);

                                        if (displayText.toLowerCase().includes(
                                                'commission')) {
                                            displayText += " (%)";
                                        }
                                        $('.schedule-name[data-key="' + key + '"]')
                                            .text(
                                                displayText);

                                    } else {
                                        let matchedItems = quote_schedules_data.filter(
                                            ({
                                                name
                                            }) => name === sch.name);

                                        if (matchedItems.length) {
                                            let combinedText = matchedItems.map(item =>
                                                item
                                                .details).join("\n\n");
                                            if (window[editorKey]) {
                                                window[editorKey].setData(
                                                    combinedText);
                                            } else {
                                                // console.log(
                                                //     `CKEditor instance ${editorKey} not found.`
                                                // );
                                            }

                                        }
                                    }
                                });


                            });

                        }
                    }, 200);


                    if (stage_id == 3 && category_type == 2) {
                        setTimeout(function() {
                            $(document).on('input change', '[id^="signed_share"]',
                                calculatefacUnplacedShares);
                            calculatefacUnplacedShares();

                            function calculatefacUnplacedShares() {
                                let totalSignedShare = 0;
                                $('[id^="signed_share"]').each(function() {
                                    let value = parseFloat($(this).val()) || 0;
                                    // console.log("Signed Share Value:", value); 
                                    totalSignedShare += value;
                                });

                                $('#placed').val(totalSignedShare);

                                let updatedWrittenShareTotal = parseFloat($('#fac_written_share_total')
                                    .val()) || 0;
                                let unplacedShare = updatedWrittenShareTotal - totalSignedShare;

                                $('#unplaced').val(unplacedShare);
                            }
                        }, 200);
                    }

                    if (stage_id == 3 && category_type == 1) {
                        setTimeout(function() {
                            $(document).on('input change', '.signed_share', calculateQtUnplacedShares);
                            calculateQtUnplacedShares();

                            function calculateQtUnplacedShares() {
                                let totalSignedShare = 0;

                                $('.signed_share').each(function() {
                                    let value = parseFloat($(this).val()) || 0;
                                    totalSignedShare += value;
                                });

                                $('#qt_placed').val(totalSignedShare);

                                let updatedWrittenShareTotal = parseFloat($('#qt_written_share_total')
                                    .val()) || 0;
                                let unplacedShare = updatedWrittenShareTotal - totalSignedShare;
                                $('#qt_unplaced').val(unplacedShare);

                            }
                        }, 200);
                    }

                    if (stage_id == 1 && category_type == 2) {
                        setTimeout(function() {
                            $(document).on('input change', '.written-share',
                                calculateUndistributedShares);
                            calculateUndistributedShares();

                            function calculateUndistributedShares() {
                                let totalDistributed = 0;

                                // Loop through all signed share inputs and sum up the values
                                $('.written-share').each(function() {
                                    let val = parseFloat($(this).val()) || 0;
                                    totalDistributed += val;
                                });

                                $('#distributed').val(totalDistributed);

                                let qt_written_share_total = parseFloat($('#qt_written_share_total')
                                        .val()) ||
                                    0;
                                let undistributedShare = qt_written_share_total - totalDistributed;

                                $('#undistributed').val(undistributedShare);

                            }
                        }, 200);
                    } else if (stage_id == 2 && category_type == 1) {
                        setTimeout(function() {
                            $(document).on('input change', '.written-share',
                                calculateUndistributedShares);
                            calculateUndistributedShares();

                            function calculateUndistributedShares() {
                                let totalDistributed = 0;

                                // Loop through all signed share inputs and sum up the values
                                $('.written-share').each(function() {
                                    let val = parseFloat($(this).val()) || 0;
                                    totalDistributed += val;
                                });

                                $('#qt_placed').val(totalDistributed);

                                let qt_written_share_total = parseFloat($('#qt_written_share_total')
                                    .val()) || 0;
                                let undistributedShare = qt_written_share_total - totalDistributed;

                                $('#qt_unplaced').val(undistributedShare);


                            }
                        }, 200);
                    }

                    if (stage_id == 2 && category_type == 2) {
                        setTimeout(function() {
                            $(document).on('change input', '.written-share',
                                calculateUndistributedShares);
                            calculateUndistributedShares();

                            function calculateUndistributedShares() {
                                let totalDistributed = 0;

                                // Loop through all signed share inputs and sum up the values
                                $('.written-share').each(function() {
                                    let val = parseFloat($(this).val()) || 0;
                                    totalDistributed += val;
                                });

                                $('#placed').val(totalDistributed);

                                let qt_written_share_total = parseFloat($('#fac_written_share_total')
                                    .val()) || 0;
                                let undistributedShare = qt_written_share_total - totalDistributed;

                                $('#unplaced').val(undistributedShare);


                            }
                        }, 200);
                    }

                    $('.check-schedule').click(function(event) {
                        event.preventDefault();

                        var key = $(this).data('key');
                        var classCode, name;

                        if ($(this).hasClass('schedule-qt')) {
                            classCode = $('#qtclassname' + key).val();
                            name = $('#qtname' + key).val();
                        } else if ($(this).hasClass('schedule-fac')) {
                            classCode = $('#facclassname' + key).val();
                            name = $('#facname' + key).val();
                        }
                        var formattedName = name.trim().toLowerCase();

                        // Send an AJAX request
                        $.ajax({
                            method: 'GET',
                            url: "{{ route('get.schedules.data') }}",
                            data: {
                                classCode: classCode,
                                name: formattedName,
                                key: key,
                                type_of_bus: type_of_business
                            },
                            success: function(response) {
                                var key = response.key;
                                var value = response.value;
                                if (category_type == 2) {

                                    const editorKey = 'fac_editor' + key;
                                    if (window[editorKey]) {
                                        window[editorKey].setData(
                                            value);
                                    }
                                } else {
                                    const editorKey = 'editor' + key;
                                    if (window[editorKey]) {
                                        window[editorKey].setData(
                                            value);
                                    }
                                }

                            },
                            error: function(xhr, status, error) {

                                // console.log(error);
                            }
                        });
                    });
                    let initialUsers = 1;
                    let isRendering = false;
                    let hasRendered = false;

                    function appendDepartmentEmails() {
                        let targetContainer, targetId;

                        if (category_type == 1) {
                            targetContainer = $('#quote-selected-customers-list .customer-entry');
                            targetId = $('#quote-selected-customers-list');
                        } else if (category_type == 2) {
                            targetContainer = $('#facultative-selected-customers-list .customer-entry');
                            targetId = $('#facultative-selected-customers-list');
                        }

                        if (isRendering) return;
                        isRendering = true;

                        if (targetContainer.length === 0) {
                            hasRendered = false;
                            isRendering = false;
                            return;
                        }

                        if (Users.length === 0) {
                            isRendering = false;
                            return;
                        }

                        $('.department-emails-section').remove();

                        let html = `
                <div class="department-emails-section">
                    <label class="col-form-label fw-bold text-danger">Department Emails:</label>`;

                        Users.forEach((contact, index) => {
                            html += `
                    <div class="row dept-contact-row" data-index="${index}" ${index < initialUsers ? '' : 'style="display:none"'}>                      
                        <div class="col-4 mt-1">
                            <label>Contact Name</label>
                            <input type="text" class="form-control dept_user_name" name="dept_contact_name[]" value="${contact.name}" data-index="${index}" required>
                        </div>
                        <div class="col-4 mt-1">
                            <label>Email</label>
                            <input type="email" class="form-control dept_user_email" name="dept_email[]" value="${contact.email}" data-index="${index}" required>
                        </div>
                        <div class="col-2 mt-3">
                            <div><label>CC Email</label></div>
                            <input type="checkbox" name="dept_select_contact_main[]" value="${index}" data-index="${index}" class="form-check-input dept-row-checkbox">
                        </div>
                    </div>`;
                        });

                        if (Users.length > initialUsers) {
                            html += `
                <div class="row mt-2 show-more-container">
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-outline-primary dept-show-more-btn">
                            Show More Department Contacts (${Users.length - initialUsers} more)
                        </button>
                    </div>
                </div>`;
                        }

                        html += '</div>';

                        targetId.append(html);

                        hasRendered = true;
                        isRendering = false;
                    }

                    $(document).off('click', '.dept-show-more-btn').on('click', '.dept-show-more-btn', function(
                        e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $('.department-emails-section .dept-contact-row').show();
                        $(this).closest('.show-more-container').hide();
                    });

                    $(document).off('change', '.dept-row-checkbox').on('change', '.dept-row-checkbox', function(
                        e) {
                        e.stopPropagation();
                        const index = $(this).data('index');
                        // console.log('Checkbox changed for contact ' + index);
                    });

                    $(document).off('click', '.dept-row-checkbox').on('click', '.dept-row-checkbox', function(
                        e) {
                        e.stopPropagation();
                    });

                    function setupObserver(elementId) {
                        const targetNode = document.getElementById(elementId);
                        if (targetNode) {
                            const observer = new MutationObserver(function(mutations) {
                                if ($(`#${elementId} .customer-entry`).length === 0) {
                                    hasRendered = false;
                                }

                                if ($(`#${elementId} .customer-entry`).length > 0 && !hasRendered) {
                                    setTimeout(appendDepartmentEmails, 100);
                                }
                            });

                            observer.observe(targetNode, {
                                childList: true,
                                subtree: true
                            });

                            if ($(`#${elementId} .customer-entry`).length > 0) {
                                setTimeout(appendDepartmentEmails, 100);
                            }

                            return observer;
                        }
                        return null;
                    }

                    const quoteObserver = setupObserver('quote-selected-customers-list');
                    const facultativeObserver = setupObserver('facultative-selected-customers-list');

                    $(window).on('beforeunload', function() {
                        if (quoteObserver) quoteObserver.disconnect();
                        if (facultativeObserver) facultativeObserver.disconnect();
                    });




                })

                function filterSchedules() {
                    let sumInsuredType = $('#sum_insured_type').val().trim();

                    let referenceClassGroup = classgroup;
                    let referenceClassName = classcode;



                    $('.schedule-sum-insured-qt').each(function() {
                        let key = $(this).data('key');
                        let scheduleSumInsured = $(this).val().trim();
                        let scheduleNameElement = $('.schedule-name-qt[data-key="' + key + '"]')
                            .closest('.mb-1');
                        let scheduleData = $('[name^="schedule_details["][data-key="' + key + '"]');

                        let scheduleClass = $('#qtclassname' + key).val();

                        let scheduleClassGroup = $('#qtclassgroup' + key).val();

                        scheduleNameElement.show();
                        scheduleNameElement.prop('disabled', false);
                        scheduleData.prop('disabled', false);
                        // console.log(
                        //     `Key: ${key}, Class: ${scheduleClass}, Group: ${scheduleClassGroup}, SumInsured: ${scheduleSumInsured}`
                        // );
                        let classGroupMatches = (scheduleClassGroup.toLowerCase() === referenceClassGroup
                            .toLowerCase());
                        let classMatches = (scheduleClass.toLowerCase() === referenceClassName.toLowerCase());
                        if (!classGroupMatches || !classMatches) {
                            scheduleData.prop('disabled', true);
                            scheduleNameElement.prop('disabled', true);
                            scheduleNameElement.hide();
                        } else if (scheduleSumInsured && scheduleSumInsured !== sumInsuredType) {
                            scheduleData.prop('disabled', true);
                            scheduleNameElement.prop('disabled', true);
                            scheduleNameElement.hide();
                        }




                    });
                }


                function filterSchedulesFac() {
                    let sumInsuredType = $('#sum_insured_type_fac').val().trim();

                    let referenceClassGroup = classgroup;
                    let referenceClassName = classcode;



                    $('.schedule-sum-insured-fac').each(function() {
                        let fackey = $(this).data('key');
                        let scheduleSumInsured = $(this).val().trim();
                        let scheduleNameElement = $('.schedule-name[data-key="' + fackey + '"]')
                            .closest('.mb-1');
                        let scheduleData = $('[name^="facschedule_details["][data-key="' + fackey +
                            '"]');

                        let scheduleClass = $('#facclassname' + fackey).val();

                        let scheduleClassGroup = $('#facclassgroup' + fackey).val();

                        scheduleNameElement.show();
                        scheduleData.prop('disabled', false);



                        let classGroupMatches = (scheduleClassGroup.toLowerCase() === referenceClassGroup
                            .toLowerCase());

                        let classMatches = (scheduleClass.toLowerCase() === referenceClassName.toLowerCase());

                        if (!classGroupMatches || !classMatches) {
                            scheduleData.prop('disabled', true);
                            scheduleNameElement.prop('disabled', true);
                            scheduleNameElement.hide();
                        } else if (scheduleSumInsured && scheduleSumInsured !== sumInsuredType) {
                            scheduleData.prop('disabled', true);
                            scheduleNameElement.prop('disabled', true);
                            scheduleNameElement.hide();
                        }
                    });
                }



                $('body').on('click', 'table tr .rej-text', function() {
                    $('#rej-text').modal('show');
                    let rejectText = $(this).attr('data-rej-text');
                    $('#rejection_message').text(rejectText);


                });
                $('body').on('click', 'table tr .update_proposal', function() {
                    let opp_id = $(this).attr('data-opp');
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to reset the stage? This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, reset it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            });

                            $.ajax({
                                type: "POST",
                                url: "{{ route('get-edit-data') }}",
                                data: {
                                    'pipeline': opp_id,
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Reset!',
                                        'The stage has been reset successfully.',
                                        'success'
                                    );
                                    location.reload();
                                },
                                error: function(xhr) {
                                    Swal.fire(
                                        'Error!',
                                        'Something went wrong. Please try again.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });


                $('body').on('click', 'table tr .update_category', function() {
                    let opp_id = $(this).data('opp')
                    let req = "";
                    $('#opportunity_id').val(opp_id);

                    // $('#stage_cycle').trigger('change');
                    $('#updateCategoryTypeModal').modal('show')

                })

                $('#stage_cycle_fac').on('change', function() {
                    let pip = $('#update_pip').val()
                    let opp_id = $('#update_opp').val()
                    let div_id = $('#update_division').val()
                    let stage_id = $('#stage_cycle_fac option:selected').val()
                    let category_type = $("#category_type").val()
                    let req = ""
                    let categoryTypeFile = '';
                    if (category_type == 1) {
                        categoryTypeFile = 'quoteFile';
                    } else {
                        categoryTypeFile = 'facultativeFile';
                    }


                    if (stage_id == 4 || stage_id == 3) {
                        $.ajax({
                            type: "GET",
                            url: "{{ route('cedant_details') }}",
                            data: {
                                'prospect': opp_id
                            },
                            success: function(response) {

                                let contactContainer = "";
                                if (category_type == 1) {
                                    $('#cedant_name_qt').val(response.cedantDetails);
                                    contactContainer = $("#contacts-wrapper-qt");
                                    $("#contacts-wrapper-qt").find(
                                        ".contacts-container, .no-contact-msg").remove();
                                    $("#contacts-wrapper-fac").find(
                                        ".contacts-container, .no-contact-msg").remove();

                                } else {
                                    $('#cedant_name_fac').val(response.cedantDetails);
                                    contactContainer = $("#contacts-wrapper-fac");
                                    $("#contacts-wrapper-qt").find(
                                        ".contacts-container, .no-contact-msg").remove();
                                    $("#contacts-wrapper-fac").find(
                                        ".contacts-container, .no-contact-msg").remove();
                                }


                                if (response.contact_person && Array.isArray(response
                                        .contact_person) && response.contact_person.length > 0) {
                                    let contactsHtml =
                                        '<div id="cedant-contacts-container" class="contacts-container">';

                                    const initialVisibleContacts = 1;
                                    response.contact_person.forEach((contact, index) => {
                                        const displayStyle = index <
                                            initialVisibleContacts ? '' : 'display: none;';

                                        contactsHtml += `
                                    <div class="row cedant-contact-row mb-3" data-index="${index}" style="${displayStyle}">
                                        <input type="hidden" name="customer" value="${response.cedantDetails}"/>
                                        <input type="hidden" name="customer_id" value="${contact.customer_id}"/>
                                        <div class="col-4">
                                            <label class="form-label fw-medium">Contact Name</label>
                                            <input type="text" class="form-control shadow-sm contact_name" name="contact_name[]" value="${contact.contact_name}" data-index="${index}" required>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label fw-medium">Email</label>
                                            <input type="email" class="form-control shadow-sm contact_email" name="contact_email[]" value="${contact.contact_email}" data-index="${index}" required>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Primary Email</label>
                                            <div class="form-check mt-2">
                                                <input type="radio" name="select_contact_main_${contact.customer_id}" value="${index}" data-index="${index}" class="form-check-input row-radio">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label ">CC Email</label>
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="select_contact_cc[]" value="${index}" data-index="${index}" class="form-check-input row-checkbox">
                                            </div>
                                        </div>
                                    </div>`;
                                    });

                                    if (response.contact_person.length > initialVisibleContacts) {
                                        contactsHtml += `
                                    <div class="row mt-2 mb-3">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm btn-outline-primary show-more-cedant-contacts-btn">
                                                Show More Contacts (${response.contact_person.length - initialVisibleContacts} more)
                                            </button>
                                        </div>
                                    </div>`;
                                    }

                                    contactsHtml += '</div>';
                                    contactContainer.append(contactsHtml);
                                    contactContainer.append(`<hr>`);

                                    $(document).off('click', '.show-more-cedant-contacts-btn').on(
                                        'click', '.show-more-cedant-contacts-btn',
                                        function() {
                                            const contactRows = $(
                                                '#cedant-contacts-container .cedant-contact-row'
                                            );
                                            const $button = $(this);
                                            const allContactsShown = contactRows.length ===
                                                contactRows.filter(':visible').length;

                                            if (allContactsShown) {
                                                contactRows.slice(initialVisibleContacts)
                                                    .hide();
                                                $button.text(
                                                    `Show More Contacts (${contactRows.length - initialVisibleContacts} more)`
                                                );
                                            } else {
                                                const nextHiddenContact = contactRows.filter(
                                                    ':hidden').first();
                                                nextHiddenContact.show();
                                                if (contactRows.filter(':hidden').length ===
                                                    0) {
                                                    $button.text('Hide Additional Contacts');
                                                } else {
                                                    $button.text(
                                                        `Show More Contacts (${contactRows.filter(':hidden').length} more)`
                                                    );
                                                }
                                            }
                                        });

                                } else {
                                    contactContainer.append(
                                        '<div class=" -info py-2 mt-2 no-contact-msg"><i class="bi bi-info-circle me-2"></i>No contact person for cedant</div>'
                                    );
                                }


                            }
                        });

                    }
                    var baseAssetUrl = "{{ Storage::disk('s3')->url('uploads') }}";
                    // var baseAssetUrl = "{{ asset('uploads') }}";

                    if (stage_id == 2 && category_type == 1 || stage_id == 4 && category_type == 2) {
                        $.ajax({
                            type: "GET",
                            data: {
                                'pipeline': pip,
                                'prospect': opp_id,
                                'divisions': div_id,
                                'stage': stage_id,
                                'category_type': category_type,
                                'type_of_business': type_of_business,


                            },
                            url: "{{ route('get_stage_documents') }}",
                            success: function(resp) {
                                if (resp.status == 1) {
                                    existingCheckbox = resp.prosp_doc;
                                    console.log(resp);


                                    $(`#${categoryTypeFile}`).empty();
                                    let cedantTitleAdded = false;
                                    let ourTitleAdded = false;
                                    let receivedTitleAdded = false;
                                    let cedantCheckboxes = '';
                                    let ourCheckboxes = '';
                                    let receivedCheckboxes = '';

                                    // Function to generate checkbox HTML
                                    function getCheckboxHtml(docType, id, isChecked, isDisabled,
                                        cedantfileUrl = null) {
                                        return `
                                        <div class="col-md-4 mt-3">
                                             <input type="checkbox" name="cedant_checkbox_docs_id[]" value="${id}" class="form-check-input cedant-checkbox first-checkbox" hidden>
                                            <label>${docType}</label>
                                            <input type="checkbox" name="cedant_checkbox_docs[]" value="${docType}" class="form-check-input cedant-checkbox second-checkbox"
                                                ${isChecked ? 'checked' : ''} ${isDisabled ? 'disabled' : ''}/>
                                                     ${cedantfileUrl ? `<a href="${cedantfileUrl}" target="_blank">
                                                                                                                                                                                                                                                                                                      <i class="bx bx-show"></i> </a>` : ''}
                                                 

                                        </div>`;
                                    }
                                    $(document).on('change', '.second-checkbox', function() {
                                        var $firstCheckbox = $(this).closest('.col-md-4')
                                            .find('.first-checkbox');
                                        $firstCheckbox.prop('checked', $(this).prop(
                                            'checked'));
                                    });

                                    function getCheckboxHtml2(docType, id, isChecked, isDisabled) {
                                        return `
                                        <div class="col-md-4 mt-3">
                                            <label>${docType}</label>
                                            <input type="checkbox" name="our_checkbox_docs[]" value="${docType}" class="form-check-input cedant-checkbox"
                                                ${isChecked ? 'checked' : ''} ${isDisabled ? 'disabled' : ''}/>
                                                 
                                        </div>`;
                                    }

                                    function getCheckboxHtml3(docType, id) {
                                        return `
                                        <div class="col-md-4 mt-3">
                                            <label>${docType}</label>
                                            <input type="checkbox" name="received_docs_checkboxes[]" value="${docType}" class="form-check-input cedant-checkbox" />
                                                 
                                        </div>`;
                                    }

                                    // Collect checkboxes
                                    $.each(resp.docs, function() {
                                        if (this.mandatory == 'Y' && stage_id != 3) {
                                            req = 'required';
                                        }

                                        let isChecked = false;
                                        let isDisabled = false;
                                        cedantfileUrl = this.file_name ?
                                            `${baseAssetUrl}/cedant_docs/` + this
                                            .file_name :
                                            '';
                                        if (existingCheckbox) {
                                            existingCheckbox.forEach((existingFile) => {
                                                if (this.doc_type == existingFile
                                                    .description) {
                                                    isChecked = true;
                                                    isDisabled = true;
                                                }
                                            });
                                        }

                                        if (this.checkbox_doc == 1) {
                                            cedantCheckboxes += getCheckboxHtml(this
                                                .doc_type, this.id, isChecked,
                                                isDisabled, cedantfileUrl);
                                        } else if (this.checkbox_doc == 2) {
                                            ourCheckboxes += getCheckboxHtml2(this.doc_type,
                                                this.id, isChecked, isDisabled);
                                        }
                                        if (this.checkbox_doc == 2) {
                                            receivedCheckboxes += getCheckboxHtml3(this
                                                .doc_type, this.id);
                                        }
                                    });
                                    $('.department-emails-section').remove();
                                    let initialUsers = 1;

                                    let html = `
                                    <div class="department-emails-section">
                                        <label class="col-form-label fw-bold text-danger">Department Emails:</label>`;

                                    Users.forEach((contact, index) => {
                                        html += `
                                        <div class="row dept-contact-row" data-index="${index}" ${index < initialUsers ? '' : 'style="display:none"'}>                      
                                            <div class="col-4 mt-1">
                                                <label>Contact Name</label>
                                                <input type="text" class="form-control dept_user_name" name="dept_contact_name[]" value="${contact.name}" data-index="${index}" required>
                                            </div>
                                            <div class="col-4 mt-1">
                                                <label>Email</label>
                                                <input type="email" class="form-control dept_user_email" name="dept_email[]" value="${contact.email}" data-index="${index}" required>
                                            </div>
                                            <div class="col-2 mt-3">
                                                <div><label>CC Email</label></div>
                                                <input type="checkbox" name="dept_select_contact_main[]" value="${index}" data-index="${index}" class="form-check-input dept-row-checkbox">
                                            </div>
                                        </div>`;
                                    });

                                    if (Users.length > initialUsers) {
                                        html += `
                                    <div class="row mt-2 show-more-container">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm btn-outline-primary dept-show-more-btn">
                                                Show More Department Contacts (${Users.length - initialUsers} more)
                                            </button>
                                        </div>
                                    </div>`;
                                    }

                                    html += '</div>';

                                    $(`#${categoryTypeFile}`).append(html);




                                    // $(`#${categoryTypeFile}`).append(`<hr>`)


                                    // Append Cedant title and checkboxes
                                    // if (cedantCheckboxes && !cedantTitleAdded) {
                                    //     $(`#${categoryTypeFile}`).append(`
                            //     <div class="col-12 mt-3">
                            //           <hr>
                            //         <small><b style="color: #E1251B"><i>Documents Required By Cedant </i></b></small>

                            //     </div>
                            //     <div class="row my-md-3">
                            //         ${cedantCheckboxes}
                            //     </div>
                            // <div class="col-12 mt-3">

                            //     </div>
                            // `);
                                    //     cedantTitleAdded = true;
                                    // }

                                    // Append Our title and checkboxes
                                    if (ourCheckboxes && !ourTitleAdded) {
                                        $(`#${categoryTypeFile}`).append(`
                                        <div class="col-12 mt-3">
                                             <hr>
                                            <small><b style="color: #E1251B"><i>Documents  We Require From Cedant </i></b></small>
                                           
                                        </div>
                                        <div class="row my-md-3">
                                            ${ourCheckboxes}
                                        </div>
                                    `);
                                        ourTitleAdded = true;
                                    }

                                    if (receivedCheckboxes && !receivedTitleAdded) {
                                        $(`#${categoryTypeFile}`).append(`
                                <div class="col-12 mt-3">
                                    <hr>
                                    <small><b style="color: #E1251B"><i>Received Documents</i></b></small>

                                </div>
                                <div class="row my-md-3">
                                    ${receivedCheckboxes}
                                </div>
                            `);
                                        receivedTitleAdded = true;
                                    }
                                    if (Object.keys(resp.docs).length > 0) {
                                        if (category_type != 2) {
                                            $(`#${categoryTypeFile}`).append(
                                                `<div class="col-12 mt-3">
                                        <small><b  style="color: #E1251B"><i>Please Upload files and  Supporting documents</i></b></small>
                                        <hr>
                                    </div>`
                                            );
                                        }
                                    }



                                    $.each(resp.docs, function() {
                                        if (this.mandatory == 'Y' && stage_id != 3) {
                                            req = 'required'
                                        }
                                        let prosp_doc = resp.prosp_doc;
                                        let existingFiles = "";
                                        let fileUrl = "";
                                        existingFiles = prosp_doc;
                                        let fileInputHtml = "";




                                        if (existingFiles != '') {
                                            existingFiles.forEach((existingFile, index) => {
                                                fileUrl = existingFile ?
                                                    `${baseAssetUrl}/${existingFile.file}` :
                                                    '';
                                                if (this.doc_type == existingFile
                                                    .description) {

                                                    fileInputHtml += `
                                            <div class="row mt-2" style="padding-left: 10px;">
                                                <div class="col-5 mt-2">
                                                    <div class="row">
                                                    <input type="text" class="form-control"  value="${existingFile.description}"/>
                                                    </div>
                                                </div>
                                                <div class="col-5 mt-2">
                                                        <input type="text"  value="${existingFile.file}" class="form-control document_file checkempty" />
                                                     </div>
                                                <div class="col-2" style="margin-top: 20px;">
                                                        ${existingFile ? `<a href="${fileUrl}" target="_blank">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="bx bx-show"></i> </a>` : '<span class="text-muted">No file uploaded</span>'}
                                                 </div>
                                            </div>
                                            
                                        `;
                                                }
                                            });
                                        }
                                        if (this.checkbox_doc == null) {
                                            $(`#${categoryTypeFile}`).append(
                                                `<div class="row mt-3" style="padding-left: 10px;">
                                        <div class="col-auto">
                                            <div class="row">
                                                <input type="hidden" name="document_name[]" value="${this.doc_type}" />
                                                <x-Input  class="mt-3" req="" inputLabel="Document Title" value="${this.doc_type}"/>
                                            </div>
                                        </div>
                                        <div class="col-auto mt-3">
                                            <label for="document_file${this.id}">File${fileInputHtml.length < 1  ? '<font style="color:red;">*</font>' : ''}</label>

                                            <div class="input-group">
                                                <input type="file" name="document_file[]" id="document_file${this.id}"
                                                    class="form-control document_file checkempty" ${fileInputHtml.length < 1 ? 'required' : ''}  />
                                                <button class="btn btn-primary add-doc" type="button">
                                                    <i class="bx bx-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Preview Icon -->
                                         <div class="col-auto" style="margin-top: 45px;">
                                                <i class="bx bx-show preview  optionFile${this.id}"" id="preview${this.id}"" style="cursor:pointer;"></i>
                                         </div>                                         
                                                 
                                    </div>
                                 
                                    
                                        
                                            ${fileInputHtml}

                                                               
                                       
    
                                    `
                                            );
                                        }

                                    });






                                    // Handle click event for adding new file inputs
                                    $(document).off('click', '.add-doc').on('click', '.add-doc',
                                        function() {
                                            const parentRow = $(this).closest('.row');
                                            const docType = parentRow.find(
                                                'input[name="document_name[]"]').val();
                                            const newId = Date.now();

                                            const newRow = `
                                                <div class="row mt-3 file-row">
                                                    <div class="col-5">
                                                        <x-Input name="document_name[]" req="" inputLabel="Document Title"  />
                                                    </div>
                                                
                                                    <div class="col-5">
                                                          <label for="document_file${newId}">File</label>
                                                        <div class="input-group">
                                                            <input type="file" name="document_file[]" id="document_file${newId}"
                                                                class="form-control document_file checkempty" required />
                                                            <button class="btn btn-danger remove-doc" type="button">
                                                                <i class="bx bx-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                
                                                    <div class="col-auto" style="margin-top: 30px;">
                                                        <i class="bx bx-show preview" id="preview${newId}" style="cursor:pointer;"></i>
                                                    </div>
                                                </div>
                                            `;

                                            parentRow.after(newRow);
                                        });



                                    // Handle click event for removing file inputs
                                    $(document).on('click', '.remove-doc', function() {
                                        $(this).closest('.file-row').remove();
                                    });

                                }
                            }
                        })
                    }


                    // Tender only
                  
                   
                    if (stage_id == 6) {
                        $.ajax({
                            type: "GET",
                            url: "{{ route('get_stage_documents') }}",
                            data: {
                                'pipeline': pip,
                                'prospect': opp_id,
                                'divisions': div_id,
                                'stage': stage_id,
                                'category_type': category_type,
                                'type_of_business': type_of_business
                            },
                            success: function(resp) {
                                console.log(resp);

                                if (resp.status == 1) {
                                    $(`#${categoryTypeFile}`).empty();

                                    if (Object.keys(resp.docs).length > 0) {
                                        $(`#${categoryTypeFile}`).append(
                                            `<div class="col-12 mt-2">
                                        <small><b  style="color: #E1251B"><i>Please Upload files and  Supporting documents</i></b></small>
                                        <hr>
                                    </div>`
                                        );
                                    }
                                    let prosp_doc = resp.prosp_doc;
                                    let existingFiles = "";
                                    let fileUrl = "";
                                    existingFiles = prosp_doc;
                                    let fileInputHtml = "";
                                    if (existingFiles != '') {
                                        existingFiles.forEach((existingFile, index) => {
                                            fileUrl = existingFile ?
                                                `${baseAssetUrl}/${existingFile.file}` :
                                                '';

                                            if (existingFile.required_doc_from_cedant ==
                                                false) {

                                                fileInputHtml += `
                                            <div class="row mt-2" style="padding-left: 10px;">
                                                <div class="col-5 mt-2">
                                                    <div class="row">
                                                    <input type="text" class="form-control"  value="${existingFile.description}"/>
                                                    </div>
                                                </div>
                                                <div class="col-5 mt-2">
                                                        <input type="text"  value="${existingFile.file}" class="form-control document_file checkempty" />
                                                     </div>
                                                <div class="col-2" style="margin-top: 20px;">
                                                        ${existingFile ? `<a href="${fileUrl}" target="_blank">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="bx bx-show"></i> </a>` : '<span class="text-muted">No file uploaded</span>'}
                                                 </div>
                                            </div>
                                            
                                        `;
                                            }
                                        });
                                    }
                                    $(`#${categoryTypeFile}`).append(
                                        `                             
                                
                                        
                                            ${fileInputHtml}
    
                                    `
                                    );
                                    $.each(resp.docs, function() {
                                        if (this.mandatory == 'Y') {
                                            req = 'required'
                                        }
                                        let prosp_doc = resp.prosp_doc;
                                        let existingFiles = "";
                                        let fileUrl = "";
                                        existingFiles = prosp_doc;
                                        let fileInputHtml = "";




                                        if (existingFiles != '') {
                                            existingFiles.forEach((existingFile, index) => {
                                                fileUrl = existingFile ?
                                                    `${baseAssetUrl}/${existingFile.file}` :
                                                    '';
                                                if (this.doc_type == existingFile
                                                    .description) {

                                                    fileInputHtml += `
                                            <div class="row mt-2" style="padding-left: 10px;">
                                                <div class="col-5 mt-2">
                                                    <div class="row">
                                                    <input type="text" class="form-control"  value="${existingFile.description}"/>
                                                    </div>
                                                </div>
                                                <div class="col-5 mt-2">
                                                        <input type="text"  value="${existingFile.file}" class="form-control document_file checkempty" />
                                                     </div>
                                                <div class="col-2" style="margin-top: 20px;">
                                                        ${existingFile ? `<a href="${fileUrl}" target="_blank">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="bx bx-show"></i> </a>` : '<span class="text-muted">No file uploaded</span>'}
                                                 </div>
                                            </div>
                                            
                                        `;
                                                }
                                            });
                                        }
                                        if (this.checkbox_doc == null) {
                                            $(`#${categoryTypeFile}`).append(
                                                `<div class="row mt-3" style="padding-left: 10px;">
                                        <div class="col-auto">
                                            <div class="row">
                                                <input type="hidden" name="document_name[]" value="${this.doc_type}" />
                                                <x-Input  class="mt-3" req="" inputLabel="Document Title" value="${this.doc_type}"/>
                                            </div>
                                        </div>
                                        <div class="col-auto mt-3">
                                            <label for="document_file${this.id}">File${fileInputHtml.length < 1  ? '<font style="color:red;">*</font>' : ''}</label>

                                            <div class="input-group">
                                                <input type="file" name="document_file[]" id="document_file${this.id}"
                                                    class="form-control document_file checkempty" ${fileInputHtml.length < 1 ? 'required' : ''}  />
                                                <button class="btn btn-primary add-doc" type="button">
                                                    <i class="bx bx-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Preview Icon -->
                                         <div class="col-auto" style="margin-top: 45px;">
                                                <i class="bx bx-show preview  optionFile${this.id}"" id="preview${this.id}"" style="cursor:pointer;"></i>
                                         </div>                                         
                                                 
                                    </div>
                                 
                                    
                                        
                                            ${fileInputHtml}

                                                               
                                       
    
                                    `
                                            );
                                        }

                                    });

                                }


                            }
                        });
                    }



                    if (stage_id == 3 && category_type == 1 || stage_id == 4 && category_type == 1) {
                        $.ajax({
                            type: "GET",
                            data: {
                                'pipeline': pip,
                                'prospect': opp_id,
                                'divisions': div_id,
                                'stage': stage_id,
                                'category_type': category_type,
                                'type_of_business': type_of_business

                            },
                            url: "{{ route('get_stage_documents') }}",
                            success: function(resp) {
                                if (resp.status == 1) {
                                    $(`#${categoryTypeFile}`).empty();
                                    if (stage_id == 2 || stage_id == 5) {
                                        if (Object.keys(resp.docs).length > 0) {
                                            $(`#${categoryTypeFile}`).append(
                                                `<div class="col-12 mt-2">
                                        <small><b  style="color: #E1251B"><i>Please Upload files and  Supporting documents</i></b></small>
                                        <hr>
                                    </div>`
                                            );
                                        }
                                    }
                                    let prosp_doc = resp.prosp_doc;
                                    let existingFiles = "";
                                    let fileUrl = "";
                                    let fileInputHtml = "";
                                    let operations_checklist = $("#checklist_of_operations");
                                    operations_checklist.empty();
                                    existingFiles = prosp_doc;

                                    if (existingFiles != '') {
                                        existingFiles.forEach((existingFile, index) => {
                                            fileUrl = existingFile ?
                                                `${baseAssetUrl}/${existingFile.file}` : '';
                                            filetext = `<div class="col-12 mt-2">
                                        <small><b  style="color: #E1251B"><i>Uploaded files and  Supporting documents</i></b></small>
                                    </div>`
                                            fileInputHtml += `
                                            <div class="row mt-2 optionFile-1"  style="padding-left: 10px; display:none;">
                                                <div class="col-5 mt-2">
                                                    <div class="row">
                                                    <input type="text" class="form-control"  value="${existingFile.description}"/>
                                                    </div>
                                                </div>
                                                <div class="col-5 mt-2">
                                                        <input type="text"  value="${existingFile.file}" class="form-control document_file checkempty" />
                                                     </div>
                                                <div class="col-2" style="margin-top: 20px;">
                                                        ${existingFile ? `<a href="${fileUrl}" target="_blank">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <i class="bx bx-show"></i> </a>` : '<span class="text-muted">No file uploaded</span>'}
                                                 </div>
                                            </div>
                                            
                                        `;
                                        });
                                    }
                                    contactPersonsLabel = '';
                                    if (category_type == 1 && stage_id == 4 || category_type == 2 &&
                                        stage_id == 4) {
                                        contactPersonsLabel = `
                                     <label class="col-form-label fw-bold text-black">Contact Person:</label>
                                     `;

                                    }

                                    $.each(resp.quoteReinsurers, function(reinsurerIndex,
                                        reinsurer) {
                                        let contactPersonsHtml = "";
                                        let initialVisibleContacts = 1;
                                        if (category_type == 1 && stage_id == 4 ||
                                            category_type == 2 && stage_id == 4) {
                                            let customer = customers.find(cust => cust
                                                .customer_id === reinsurer.reinsurer_id);

                                            if (!customer) {
                                                // console.warn(
                                                //     `No customer found for reinsurer_id: ${reinsurer.reinsurer_id}`
                                                // );
                                                return; // Skip if no matching customer
                                            }



                                            try {
                                                contactPersons = typeof customer
                                                    .contact_persons === "string" ?
                                                    JSON.parse(customer.contact_persons ||
                                                        "[]") :
                                                    customer.contact_persons || [];
                                            } catch (error) {
                                                console.error(
                                                    "Error parsing contact_persons for customer:",
                                                    customer.customer_id, error);
                                            }
                                            contactPersonsHtml =
                                                '<div id="contacts-container-' + customer
                                                .customer_id +
                                                '" class="contacts-container">';

                                            if (contactPersons.length > 0) {
                                                contactPersons.forEach((contact, index) => {
                                                    const displayStyle = index <
                                                        initialVisibleContacts ?
                                                        '' : 'display: none;';
                                                    contactPersonsHtml += `
                                                    <div class="row contact-row" data-index="${index}" style="${displayStyle}">
                                                        <input type="hidden" id="reinsurer_id" name="reinsurer_id[]" value="${customer.customer_id}" />
                                                        <input type="hidden" class="main_contact_person" name="main_contact_person[]" value="${contact.main_contact_person || ''}" data-index="${index}" />
                                                        <div class="col-4">
                                                            <label>Contact Name</label>
                                                            <input type="text" class="form-control contact_name" name="contact_name[]" value="${contact.contact_name || ''}" data-index="${index}" required>
                                                        </div>
                                                        <div class="col-4">
                                                            <label>Email</label>
                                                            <input type="email" class="form-control contact_email" name="contact_email[]" value="${contact.contact_email || ''}" data-index="${index}" required>
                                                        </div>
                                                        <div class="col-2 mt-3">
                                                            <label>Primary Email</label>
                                                            <input type="radio" name="select_contact_main_${customer.customer_id}" value="${index}" data-index="${index}" class="form-check-input row-radio">
                                                        </div>
                                                        <div class="col-2 mt-3">
                                                            <label>CC Email</label>
                                                            <input type="checkbox" name="select_contact_cc[]" value="${index}" data-index="${index}" class="form-check-input row-checkbox">
                                                        </div>
                                                    </div>`;
                                                });
                                                if (customer.contact_persons.length >
                                                    initialVisibleContacts) {
                                                    contactPersonsHtml += `
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-sm btn-outline-primary show-more-btn" data-customer-id="${customer.customer_id}">
                                                            Show More Contacts (${contactPersons.length - initialVisibleContacts} more)
                                                        </button>
                                                    </div>
                                                </div>`;
                                                }

                                                contactPersonsHtml += '</div>';
                                            } else {
                                                contactPersonsHtml =
                                                    `<p>No contacts available.</p>`;
                                            }

                                        }


                                        if (category_type == 2) {
                                            let signedShareField = "";
                                            if (stage_id == 4) {
                                                signedShareField = `
                                            <div class="col-3">
                                                <label for="signed_share${reinsurerIndex}">Signed Share (%)<font style="color:red;">*</font></label>
                                                <input type="number" id="signed_share${reinsurerIndex}" 
                                                    name="reinsurers[${reinsurerIndex}][signed_share]"   value="${reinsurer.signed_share || ''}"  class="form-control signed_share" required />
                                            </div>
                                        `;
                                            }
                                            $(`#${categoryTypeFile}`).append(`
                                        <div class="row mt-1">
                                            <!-- Insurer Name -->
                                            <div class="col-3">
                                                <input name="reinsurers[${reinsurerIndex}][reinsurer_id]" id="reinsurer_id${reinsurerIndex}" value="${reinsurer.id}" hidden />
                                                 <input name="reinsurers[${reinsurerIndex}][customer_id]" id="customer_id${reinsurerIndex}" value="${reinsurer.reinsurer_id}" hidden />
                                                <label for="reinsurer_name${reinsurerIndex}">Reinsurer Name</label>
                                                <input type="text" id="reinsurer_name${reinsurerIndex}" 
                                                    name="reinsurers[${reinsurerIndex}][name]" class="form-control" 
                                                    value="${reinsurer.reinsurer_name || 'N/A'}" readonly />
                                            </div>
    
                                             <!-- contact name -->
                                            

                                            <!-- Written Share -->
                                            <div class="col-3">
                                                <label for="written_share${reinsurerIndex}">Written Share (%)<font style="color:red;">*</font></label>
                                                <input type="number" id="written_share${reinsurerIndex}"  
                                                    name="reinsurers[${reinsurerIndex}][written_share]" class="form-control written-share" 
                                                    value="${reinsurer.written_share || ''}" required />
                                            </div>

                                            <!-- Signed Share -->
                                           <!-- <div class="col-3">
                                                <label for="signed_share${reinsurerIndex}">Signed Share (%)<font style="color:red;">*</font></label>
                                                <input type="number" id="signed_share${reinsurerIndex}" 
                                                    name="reinsurers[${reinsurerIndex}][signed_share]" class="form-control" required />
                                            </div>-->
                                             ${signedShareField}

                                            ${contactPersonsLabel}
                                            ${contactPersonsHtml}
                                            
                                        </div>
                                        <hr>
                                        
                                        
                                    `);
                                        }



                                    });



                                    $('.department-emails-section').remove();
                                    let initialUsers = 1;

                                    let html = `
                                    <div class="department-emails-section">
                                        <label class="col-form-label fw-bold text-danger">Department Emails:</label>`;

                                    Users.forEach((contact, index) => {
                                        html += `
                                        <div class="row dept-contact-row" data-index="${index}" ${index < initialUsers ? '' : 'style="display:none"'}>                      
                                            <div class="col-4 mt-1">
                                                <label>Contact Name</label>
                                                <input type="text" class="form-control dept_user_name" name="dept_contact_name[]" value="${contact.name}" data-index="${index}" required>
                                            </div>
                                            <div class="col-4 mt-1">
                                                <label>Email</label>
                                                <input type="email" class="form-control dept_user_email" name="dept_email[]" value="${contact.email}" data-index="${index}" required>
                                            </div>
                                            <div class="col-2 mt-3">
                                                <div><label>CC Email</label></div>
                                                <input type="checkbox" name="dept_select_contact_main[]" value="${index}" data-index="${index}" class="form-check-input dept-row-checkbox">
                                            </div>
                                        </div>`;
                                    });

                                    if (Users.length > initialUsers) {
                                        html += `
                                    <div class="row mt-2 show-more-container">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm btn-outline-primary dept-show-more-btn">
                                                Show More Department Contacts (${Users.length - initialUsers} more)
                                            </button>
                                        </div>
                                    </div>`;
                                    }

                                    html += '</div>';

                                    $(`#${categoryTypeFile}`).append(html);




                                    $(`#${categoryTypeFile}`).append(`<hr>`)

                                    $(`#${categoryTypeFile}`)
                                        .append(`
                                        <div>
                                            
                                            <span class="btn btn-sm btn-outline-primary text-dark facultative-file-toggle-icon mt-2 d-inline-flex align-items-center" 
                                                    role="button" 
                                                    aria-expanded="false"
                                                    data-target=".optionFile-1">
                                                <i class='bx bx-plus me-1'> <strong>Show File</strong></i>
                                            
                                            </span>
                                        </div>
                                        ${filetext}                                       
                                        ${fileInputHtml}
                                            
                                    `);
                                    $(`#${categoryTypeFile}`).append(`<hr>`)
                                    checkbox = '';
                                    checkbox = `<div class="row dept-contact-row">`;
                                    checklist_of_operations.forEach((operation, index) => {
                                        checkbox += `
                                                          
                                           
                                          <div class="col-4 mt-3" data-index="${index}">
                                               <label>${operation.name}<i class="text-danger">*</i></label>
                                                <input type="checkbox" name="operation-checkbox[]" id="operation-checkbox${index}"  value="${index}" data-index="${index}" class="form-check-input operation-checkbox">
                                            </div>
                                       `;

                                    });
                                    checkbox += `</div>`;
                                    operations_checklist.append(checkbox);



                                }

                            }
                        })
                    }

                    let counters = {};
                    $('body').off('click', '.addDocfac').on('click', '.addDocfac', function(event) {
                        event.preventDefault();

                        const closestRow = $(this).closest('.row');
                        const reinsurerIndex = closestRow.attr("data-reinsurer-index");

                        if (!reinsurerIndex) {
                            // console.log("Error: reinsurerIndex is missing!");
                            return;
                        }

                        if (counters[reinsurerIndex] === undefined) {
                            counters[reinsurerIndex] = 0;
                        }
                        counters[reinsurerIndex]++;

                        closestRow.after(`
                        <div class="row mt-3 new-document-row" data-reinsurer-index="${reinsurerIndex}">
                            <div class="col-auto">
                                <label for="document_title${reinsurerIndex}_${counters[reinsurerIndex]}">Document Title</label>
                                <input type="text" id="document_title${reinsurerIndex}_${counters[reinsurerIndex]}" 
                                    name="reinsurers[${reinsurerIndex}][documents][${counters[reinsurerIndex]}][title]"  
                                    class="form-control" value="" placeholder="Enter document title" />
                            </div>

                            <div class="col-auto">
                                <label for="document_file${reinsurerIndex}_${counters[reinsurerIndex]}">File<font style="color:red;">*</font></label>
                                <div class="input-group">
                                    <input type="file" id="document_file${reinsurerIndex}_${counters[reinsurerIndex]}" 
                                    name="reinsurers[${reinsurerIndex}][documents][${counters[reinsurerIndex]}][file]"  
                                    class="form-control document_file" required />
                                    <button class="btn btn-danger remove-file" type="button"><i class="bx bx-minus"></i></button>
                                </div>
                            </div>

                            <div class="col-auto" style="margin-top: 30px;">
                                <i class="bx bx-show preview" id="preview${reinsurerIndex}_${counters[reinsurerIndex]}" style="cursor:pointer;"></i>
                            </div>
                        </div>
                    `);
                    });

                    // Remove dynamically added files
                    $('body').on('click', '.remove-file', function() {
                        $(this).closest('.new-document-row').remove();
                    });



                    // Add click handler for the toggle icon
                    $(document).on('click', '.facultative-file-toggle-icon', function() {
                        const targetClass = $(this).attr('data-target');
                        const targetElements = $(targetClass);
                        const icon = $(this).find('i');
                        const textElement = $(this).find('strong');

                        if (targetElements.css('display') === 'none') {
                            targetElements.css('display',
                                'flex');
                            icon.removeClass('bx-plus').addClass('bx-minus');
                            textElement.text('Hide File');
                        } else {
                            targetElements.css('display', 'none');
                            icon.removeClass('bx-minus').addClass('bx-plus');
                            textElement.text('Show File');
                        }
                        $(`.optionFile-$`).css('display', 'block');
                    });







                    // Initial hide
                    $('.optionFile-').hide();
                    $(document).on('click', '.preview', function() {
                        const fileInput = $(this).closest('.row').find('input[type="file"]');
                        const file = fileInput[0].files[0];

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                $('#filePreviewFrame').attr('src', '');
                                $('#filePreviewFrame').attr('src', e.target.result);
                                $('#filePreviewModal').modal('show');
                            };
                            reader.readAsDataURL(file);
                        } else {
                            toastr.error('Please select a file first');
                        }
                    });

                    // Clear modal when closed
                    $('#filePreviewModal').on('hidden.bs.modal', function() {
                        $('#filePreviewFrame').attr('src', '');
                    });


                })

                const businessTypes = ['TNP', 'TPR'];
                $('#all_opps').DataTable({
                    'dom': 'Bfltip',
                    'pageLength': 100,
                    'buttons': [{
                            extend: 'pdf',
                            text: '<i class="fa fa-file-pdf-o"></i> Export PDF',
                            className: 'btn btn-danger',
                            titleAttr: 'PDF',
                            title: 'LIST OF PROSPECTS',
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                    .length + 1).join('*').split('');

                            },
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i> Print',
                            className: 'btn btn-danger',
                            titleAttr: 'Print',
                            title: 'LIST OF PROSPECTS',
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'csv', // Add Excel button
                            text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                            className: 'btn btn-danger',
                            titleAttr: 'Excel',
                            title: 'LIST OF PROSPECTS',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                    ],

                    'processing': true,
                    'serverSide': true,
                    'ajax': {
                        url: "{{ route('pipeline.activity.treaty') }}" + "?pipe_id=" + encodeURIComponent(
                            lead),
                        type: "get",
                        data: {
                            'business_types': businessTypes,
                        },

                    },
                    'columns': [{
                            data: 'opportunity_id',
                            name: 'opp_id',
                            // visible:false,

                        },
                        {
                            data: 'insured_name',
                            name: "insured_name"
                        },
                        {
                            data: 'division_name',
                            name: 'division_name'
                        },
                        {
                            data: 'business_class',
                            name: 'business_class'
                        },
                        {
                            data: 'currency_code',
                            name: 'currency_code'
                        },

                        {
                            data: 'effective_sum_insured',
                            name: 'effective_sum_insured',
                        },
                        {
                            data: 'cedant_premium',
                            name: 'cedant_premium'
                        },
                        {
                            data: 'effective_date',
                            name: 'effective_date'
                        },
                        {
                            data: 'closing_date',
                            name: 'closing_date'
                        },

                        {
                            data: 'stage',
                            name: 'stage'
                        },
                        {

                            data: 'edit',
                            name: 'edit',
                            className: 'highlight-index'
                        },

                        {
                            data: 'action1',
                            name: 'action1'
                        },
                        {
                            data: 'approval_status',
                            name: 'approval_status'

                        },


                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [7, 'desc']
                    ],





                });

                $('#q1_opps').DataTable({
                    'dom': 'Bfltip',
                    'buttons': [{
                            extend: 'pdf',
                            text: '<i class="fa fa-file-pdf-o"></i> Export PDF',
                            className: 'btn btn-danger',
                            titleAttr: 'PDF',
                            title: 'QUARTER ONE PROSPECTS',
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                    .length + 1).join('*').split('');

                            },
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i> Print',
                            className: 'btn btn-danger',
                            titleAttr: 'Print',
                            title: 'QUARTER ONE PROSPECTS',
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'csv', // Add Excel button
                            text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                            className: 'btn btn-danger',
                            titleAttr: 'Excel',
                            title: 'QUARTER ONE PROSPECTS',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                    ],
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('pipeline.activity.q1.treaty') }}" + "?pipe_id=" + encodeURIComponent(
                            lead),
                        type: "get",
                        data: {
                            'business_types': businessTypes,
                        },

                    },
                    columns: [{
                            data: 'opportunity_id',
                            name: 'opp_id',
                            // visible:false,

                        },
                        {
                            data: 'insured_name',
                            name: "insured_name"
                        },
                        {
                            data: 'division_name',
                            name: 'division_name'
                        },
                        {
                            data: 'business_class',
                            name: 'business_class'
                        },
                        {
                            data: 'currency_code',
                            name: 'currency_code'
                        },
                        {
                            data: 'cedant_premium',
                            name: 'cedant_premium'
                        },
                        {
                            data: 'effective_sum_insured',
                            name: 'effective_sum_insured',
                        },
                        {
                            data: 'effective_date',
                            name: 'effective_date'
                        },
                        {
                            data: 'closing_date',
                            name: 'closing_date'
                        },

                        {
                            data: 'stage',
                            name: 'stage'
                        },
                        {

                            data: 'edit',
                            name: 'edit',
                            className: 'highlight-index'
                        },

                        {
                            data: 'action1',
                            name: 'action1'
                        },
                        {
                            data: 'approval_status',
                            name: 'approval_status'

                        },


                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [7, 'asc']
                    ],
                });

                $('#q2_opps').DataTable({
                    'dom': 'Bfltip',
                    'buttons': [{
                            extend: 'pdf',
                            text: '<i class="fa fa-file-pdf-o"></i> Export PDF',
                            className: 'btn btn-danger',
                            titleAttr: 'PDF',
                            title: 'QUARTER TWO PROSPECTS',
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                    .length + 1).join('*').split('');

                            },
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i> Print',
                            className: 'btn btn-danger',
                            titleAttr: 'Print',
                            title: 'QUARTER TWO PROSPECTS',
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'csv', // Add Excel button
                            text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                            className: 'btn btn-danger',
                            titleAttr: 'Excel',
                            title: 'QUARTER TWO PROSPECTS',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                    ],
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('pipeline.activity.q2.treaty') }}" + "?pipe_id=" + encodeURIComponent(
                            lead),
                        type: "get",
                        data: {
                            'business_types': businessTypes,
                        },

                    },
                    columns: [{
                            data: 'opportunity_id',
                            name: 'opp_id',
                            // visible:false,

                        },
                        {
                            data: 'insured_name',
                            name: "insured_name"
                        },
                        {
                            data: 'division_name',
                            name: 'division_name'
                        },
                        {
                            data: 'business_class',
                            name: 'business_class'
                        },
                        {
                            data: 'currency_code',
                            name: 'currency_code'
                        },
                        {
                            data: 'cedant_premium',
                            name: 'cedant_premium'
                        },
                        {
                            data: 'effective_sum_insured',
                            name: 'effective_sum_insured',
                        },
                        {
                            data: 'effective_date',
                            name: 'effective_date'
                        },
                        {
                            data: 'closing_date',
                            name: 'closing_date'
                        },

                        {
                            data: 'stage',
                            name: 'stage'
                        },
                        {

                            data: 'edit',
                            name: 'edit',
                            className: 'highlight-index'
                        },

                        {
                            data: 'action1',
                            name: 'action1'
                        },
                        {
                            data: 'approval_status',
                            name: 'approval_status'

                        },


                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [7, 'asc']
                    ],

                });

                $('#q3_opps').DataTable({
                    'dom': 'Bfltip',
                    'buttons': [{
                            extend: 'pdf',
                            text: '<i class="fa fa-file-pdf-o"></i> Export PDF',
                            className: 'btn btn-danger',
                            titleAttr: 'PDF',
                            title: 'QUARTER THREE PROSPECTS',
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                    .length + 1).join('*').split('');

                            },
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i> Print',
                            className: 'btn btn-danger',
                            titleAttr: 'Print',
                            title: 'QUARTER THREE PROSPECTS',
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'csv', // Add Excel button
                            text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                            className: 'btn btn-danger',
                            titleAttr: 'Excel',
                            title: 'QUARTER THREE PROSPECTS',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                    ],
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('pipeline.activity.q3.treaty') }}" + "?pipe_id=" + encodeURIComponent(
                            lead),
                        type: "get",
                        data: {
                            'business_types': businessTypes,
                        },

                    },
                    columns: [{
                            data: 'opportunity_id',
                            name: 'opp_id',
                            // visible:false,

                        },
                        {
                            data: 'insured_name',
                            name: "insured_name"
                        },
                        {
                            data: 'division_name',
                            name: 'division_name'
                        },
                        {
                            data: 'business_class',
                            name: 'business_class'
                        },
                        {
                            data: 'currency_code',
                            name: 'currency_code'
                        },
                        {
                            data: 'cedant_premium',
                            name: 'cedant_premium'
                        },
                        {
                            data: 'effective_sum_insured',
                            name: 'effective_sum_insured',
                        },
                        {
                            data: 'effective_date',
                            name: 'effective_date'
                        },
                        {
                            data: 'closing_date',
                            name: 'closing_date'
                        },

                        {
                            data: 'stage',
                            name: 'stage'
                        },
                        {

                            data: 'edit',
                            name: 'edit',
                            className: 'highlight-index'
                        },

                        {
                            data: 'action1',
                            name: 'action1'
                        },
                        {
                            data: 'approval_status',
                            name: 'approval_status'

                        },


                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [7, 'asc']
                    ],
                });

                $('#q4_opps').DataTable({
                    'dom': 'Bfltip',
                    'buttons': [{
                            extend: 'pdf',
                            text: '<i class="fa fa-file-pdf-o"></i> Export PDF',
                            className: 'btn btn-danger',
                            titleAttr: 'PDF',
                            title: 'QUARTER FOUR PROSPECTS',
                            customize: function(doc) {
                                doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                    .length + 1).join('*').split('');

                            },
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i> Print',
                            className: 'btn btn-danger',
                            titleAttr: 'Print',
                            title: 'QUARTER FOUR PROSPECTS',
                            orientation: 'landscape',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                        {
                            extend: 'csv', // Add Excel button
                            text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                            className: 'btn btn-danger',
                            titleAttr: 'Excel',
                            title: 'QUARTER FOUR PROSPECTS',
                            exportOptions: {
                                columns: ':not(:last-child)',
                            },
                        },
                    ],
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('pipeline.activity.q4.treaty') }}" + "?pipe_id=" + encodeURIComponent(
                            lead),
                        type: "get",
                        data: {
                            'business_types': businessTypes,
                        },

                    },
                    columns: [{
                            data: 'opportunity_id',
                            name: 'opp_id',
                            // visible:false,

                        },
                        {
                            data: 'insured_name',
                            name: "insured_name"
                        },
                        {
                            data: 'division_name',
                            name: 'division_name'
                        },
                        {
                            data: 'business_class',
                            name: 'business_class'
                        },
                        {
                            data: 'currency_code',
                            name: 'currency_code'
                        },
                        {
                            data: 'cedant_premium',
                            name: 'cedant_premium'
                        },
                        {
                            data: 'effective_sum_insured',
                            name: 'effective_sum_insured',
                        },
                        {
                            data: 'effective_date',
                            name: 'effective_date'
                        },
                        {
                            data: 'closing_date',
                            name: 'closing_date'
                        },

                        {
                            data: 'stage',
                            name: 'stage'
                        },
                        {

                            data: 'edit',
                            name: 'edit',
                            className: 'highlight-index'
                        },

                        {
                            data: 'action1',
                            name: 'action1'
                        },
                        {
                            data: 'approval_status',
                            name: 'approval_status'

                        },


                        {
                            data: 'action',
                            name: 'action'
                        }
                    ],
                    order: [
                        [7, 'asc']
                    ],
                });

                $('body').on('change', '.document_file, .document_filo', function() {
                    let file = $(this)[0].files[0];
                    let id = $(this).attr('id')
                    let id_length = id.length
                    let rowID = id.slice(13, id_length)
                    let f_type = $(this).attr('data-fo')


                    let formData = new FormData();
                    formData.append('doc', file);


                    $.ajax({
                        type: "POST",
                        data: formData,
                        url: "{{ route('doc_preview') }}",
                        contentType: false,
                        processData: false,
                        success: function(resp) {
                            if (f_type == 'fo') {
                                $("#previewo" + rowID).attr('data-file', resp);
                            } else {
                                $("#preview" + rowID).attr('data-file', resp);
                            }

                        }
                    })
                })

                $('body').on('click', '.preview', function(e) {
                    $('object').attr('data', '');
                    var doc = $(this).attr('data-file');

                    $('#doc_view').html('<iframe src="' + doc + '" width="100%" height="900"></iframe>');

                    $('#v_docs').modal('show');



                });

                var counter = 0;
                $('body').on('click', '#addDoc', function() {
                    if (counter > 0) {
                        var document_title = $('#document_title' + counter).val()
                        var document_file = $('#document_file' + counter).val()
                    } else if (counter == 0) {
                        var document_title = $('#document_title0').val()
                        var document_file = $('#document_file0').val()
                    }
                    if (document_title == '' || document_file == '') {
                        Swal.fire({
                            icon: 'warning',
                            text: 'Please fill all details'
                        });
                    } else {
                        counter = +1;
                        $('#file_details').append(
                            `<div class="row mt-1">

                        <div class="col-auto">
                            <div class="row">
                                <x-Input id="document_name${counter}" name="document_name[]" req=""
                                    inputLabel="Document Title" 
                                    placeholder="Enter document title"
                                    oninput='this.value=this.value.toUpperCase();'/>
                                
                            </div>
                        </div>

                        <div class="col-auto">
                            <label for="document_file">File</label>
                            <div class="input-group">
                                <input type="file" name="document_file[]" id="document_filo${counter}" class="form-control document_filo" data-fo="fo" />
                                <button class="btn btn-danger remove_file" type="button"><i class="fa fa-minus"></i> </button>
                            </div>
                        </div>
                        <div class="col-auto" style="margin-top: 30px">
                            <i class="fa fa-eye preview" id="previewo${counter}"> </i>
                        </div>
                    </div>`
                        );
                    }


                    $('input[type=radio]').change(function() {
                        $('input[type=radio]:checked').not(this).prop('checked', false)
                    })
                });

                $('#file_details').delegate('.remove_file', 'click', function() {
                    $(this).parent().parent().parent().remove();
                });

                $('#pip_year_select').on('change', function() {

                    $("#pip_year_form").submit();

                });




                // var viewData = @json($viewData ?? []);
                var schedule = @json($schedule ?? []);

                function quote_classicEditor() {
                    schedule.forEach(function(sch, index) {
                        const key = index;
                        const editors = [{
                                id: `schedule-descr-current-${key}`,
                                editorKey: `editorCurrent${key}`
                            },
                            {
                                id: `schedule-descr-proposed-${key}`,
                                editorKey: `editorProposed${key}`
                            },
                            {
                                id: `schedule-descr-final-${key}`,
                                editorKey: `editorFinal${key}`
                            }
                        ];

                        editors.forEach(function({
                            id,
                            editorKey
                        }) {
                            const element = document.querySelector(`#${id}`);

                            if (!element) {
                                // console.warn(`Editor target #${id} not found. Skipping.`);
                                return; // Skip initialization for this item
                            }

                            // Destroy existing editor if it exists
                            if (window[editorKey]) {
                                window[editorKey].destroy()
                                    .then(() => {
                                        // console.log(`${editorKey} destroyed.`);
                                    })
                                    .catch(err => {
                                        // console.warn(`Error destroying ${editorKey}:`, err);
                                    });
                            }

                            // Initialize CKEditor
                            ClassicEditor.create(element, {
                                    resizing: false,
                                    toolbar: [
                                        'undo', 'redo',
                                        '|',
                                        'heading',
                                        '|',
                                        'bold', 'italic', 'underline', 'strikethrough',
                                        '|',
                                        'fontSize', 'fontFamily', 'textColor',
                                        'backgroundColor',
                                        '|',
                                        'alignment',
                                        '|',
                                        'insertTable', 'link', 'blockQuote', 'image',
                                        '|',
                                        'bulletedList', 'numberedList',
                                        '|',
                                        'indent', 'outdent',
                                        '|',
                                        'justifyLeft', 'justifyCenter', 'justifyRight',
                                        'justifyBlock',
                                        '|',
                                        'subscript', 'superscript',
                                        '|',
                                        'removeFormat', 'formatPainter',
                                    ],
                                    removePlugins: ['CKEditor5Inspector'],
                                    ui: {
                                        viewportOffset: {
                                            top: 0,
                                        },
                                    },
                                })
                                .then(editor => {
                                    window[editorKey] = editor;
                                    $(editor.ui.view.editable.element).closest('.ck-editor')
                                        .addClass('schedule_editor');
                                })
                                .catch(err => {
                                    console.error(`Error initializing ${editorKey}:`, err);
                                });
                        });
                    });
                }

                function setScheduleDetails() {
                    schedule.forEach(function(sch, index) {
                        const key = index;
                        const field = document.getElementById(`sched-details-${key}`);
                        const currentEditorKey = `editorCurrent${key}`;
                        const proposedEditorKey = `editorProposed${key}`;
                        const proposedFinalKey = `editorFinal${key}`;

                        if (field && !field.disabled) {
                            const currentData = window[currentEditorKey] ? window[currentEditorKey].getData() :
                                '';
                            const proposedData = window[proposedEditorKey] ? window[proposedEditorKey]
                                .getData() : '';
                            // Combine current and proposed data into a single field, e.g., as JSON
                            field.value = JSON.stringify({
                                current: currentData,
                                proposed: proposedData
                            });
                        }
                    });
                }

                function fac_classicEditor() {
                    schedule.forEach(function(sch, index) {
                        const key = index;
                        const editorKey = 'fac_editor' + key;
                        const element = document.querySelector('#facschedule-descr-' + key);

                        if (!element) {
                            // console.warn(`Editor target #facschedule-descr-${key} not found. Skipping.`);
                            return; // Skip initialization for this item
                        }
                        if (window[editorKey]) {
                            window[editorKey].destroy()
                                .then(() => {
                                    // console.log(`Editor${key} destroyed.`);
                                })
                                .catch(err => {
                                    // console.warn(`Error destroying editor${key}:`, err);
                                });
                        }

                        ClassicEditor.create(element, {
                                resizing: false,
                                toolbar: [
                                    'undo', 'redo',
                                    '|',
                                    'heading',
                                    '|',
                                    'bold', 'italic',
                                    '|',
                                    'insertTable',
                                    '|',
                                    'bulletedList', 'numberedList',
                                    '|',
                                    'indent', 'outdent',
                                ],
                                removePlugins: ['CKEditor5Inspector'],
                                ui: {
                                    viewportOffset: {
                                        top: 0,
                                    },
                                },
                            })
                            .then(editor => {
                                window[editorKey] = editor;
                                $(editor.ui.view.editable.element).closest('.ck-editor').addClass(
                                    'facs_chedule_editor');
                            })
                            .catch(err => {
                                console.error(`Error initializing editor${key}:`, err);
                            });
                    });

                    // update hidden field
                }

                function setFacScheduleDetails() {
                    schedule.forEach(function(sch, index) {
                        const key = index;
                        const editorKey = 'fac_editor' + key;
                        // Only update hidden input if the editor is initialized
                        if (window[editorKey]) {
                            document.getElementById('facsched-details-' + key).value = window[editorKey]
                                ?.getData();
                        } else {
                            // console.warn(`Editor${key} is not initialized. Skipping update.`);
                        }
                    });
                }



                // Attach the setScheduleDetails function to the form submissiongenerate_slip
                $('#generate_slip, #updateStatusBtn').on('click', function(event) {
                    event.preventDefault();
                    const form = document.getElementById('statusUpdateForm');
                    let resultsContainer = document.getElementById('quote-search-results');
                    results = resultsContainer.innerHTML.trim();

                    let searchInput = document.getElementById('quote-search-bar');

                    let stage_id = $('#stage_cycle option:selected').val()

                    if (stage_id == 3) {
                        const total = $('.operation-checkbox').length;
                        const checked = $('.operation-checkbox:checked').length;

                        if (checked < total) {
                            toastr.error("Please check all the task confirmations before you continue.");
                            return false;
                        }
                    }

                    // alert(stage_id);
                    if ($(this).attr('id') === 'updateStatusBtn') {
                        setScheduleDetails();

                        if (form.checkValidity()) {
                            $('#editStatusQuoteModal').modal('hide');
                            // $('#editStatusQuoteModal').modal('hide');
                            let selectedContacts = [];
                            $(".dept-row-checkbox:checked").each(function() {
                                let row = $(this).closest('.row');
                                let deptUserEmail = row.find(".dept_user_email").val().trim();
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_dept_user_email[dept_user_email][]',
                                    value: deptUserEmail
                                }).appendTo(form);
                            });

                            $(".row-checkbox:checked").each(function() {
                                // Get the parent row of this checkbox
                                let row = $(this).closest('.row');

                                // Extract contact information from this specific row
                                let contactName = row.find(".contact_name").val().trim();
                                let contactEmail = row.find(".contact_email").val().trim();
                                let reinsurer_id = '';
                                if (stage_id == 4) {
                                    reinsurer_id = row.find("#reinsurer_id").val().trim();
                                }

                                // let mainContactPerson = row.find(".main_contact_person").val().trim();


                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person[contact_name][]',
                                    value: contactName
                                }).appendTo(form);

                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person[contact_email][]',
                                    value: contactEmail
                                }).appendTo(form);
                                if (stage_id == 2 || stage_id == 4) {
                                    $('<input>').attr({
                                        type: 'hidden',
                                        name: 'selected_contact_person[reinsurer_id][]',
                                        value: reinsurer_id
                                    }).appendTo(form);
                                }

                                // $('<input>').attr({
                                //     type: 'hidden',
                                //     name: 'selected_contact_person[main_contact_person][]',
                                //     value: mainContactPerson
                                // }).appendTo(form);

                            });
                            $(".row-radio:checked").each(function() {
                                let row = $(this).closest('.row');

                                let contactName = row.find(".contact_name").val().trim();
                                let contactEmail = row.find(".contact_email").val().trim();
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person_main[contact_name][]',
                                    value: contactName
                                }).appendTo(form);

                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person_main[contact_email][]',
                                    value: contactEmail
                                }).appendTo(form);
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person_main[main_contact_person][]',
                                    value: 'Y'
                                }).appendTo(form);
                            });

                            if (stage_id != 5 && stage_id != 6) {

                                $('#sendEmail').modal('show');
                                $('#sendEmail').on('click', '.confirm-email', function() {
                                    const sendEmailValue = $(this).data('value');
                                    $('<input>').attr({
                                        type: 'hidden',
                                        name: 'send_email_flag',
                                        value: sendEmailValue
                                    }).appendTo(form);
                                    $('#sendEmail').modal('hide');
                                    // $('#editEmail').modal('show');
                                    form.submit();
                                });
                            } else {
                                form.submit();
                            }

                        } else {
                            form.reportValidity();
                        }
                    } else if ($(this).attr('id') === 'generate_slip') {
                        setScheduleDetails();
                    }
                });

                $('#facultative_generate_slip, #facultativeUpdateStatusBtn').on('click', function(e) {
                    e.preventDefault();

                    let stage_id = $('#stage_cycle_fac option:selected').val()

                    if ($("#fac_undistributed").val() < 0 && stage_id == 2) {
                        toastr.error('written share exceeds the total share');
                        return false;

                    }
                    if ($("#unplaced").val() > 0 && stage_id == 4) {
                        toastr.error('Please Place all shares before submitting')
                        return false;
                    }


                    $('#document_file_compulsory').each(function() {
                        if ($(this).hasClass('d-none')) {
                            $(this).prop('disabled', true); // Disable the hidden fields
                        }

                    });
                    $('#document_compulsory_name').each(function() {
                        if ($(this).hasClass('d-none')) {
                            $(this).prop('disabled', true); // Disable the hidden fields
                        }

                    });

                    $('#signed_share').find('input').each(function() {
                        if ($(this).hasClass(':hidden')) {
                            $(this).prop('disabled', true); // Disable the hidden fields
                        }

                    });

                    const form = document.getElementById('facultative-statusUpdateForm');
                    if ($(this).attr('id') === 'facultativeUpdateStatusBtn') {
                        setFacScheduleDetails();
                        const statusSelected = $('input[name="status"]:checked').length > 0;

                        if (form.checkValidity()) {
                            $('#editStatusFacultativeModal').modal('hide');


                            let selectedContacts = [];
                            let stage_id = $('#stage_cycle_fac option:selected').val()


                            $(".dept-row-checkbox:checked").each(function() {
                                let row = $(this).closest('.row');
                                let deptUserEmail = row.find(".dept_user_email").val().trim();
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_dept_user_email[dept_user_email][]',
                                    value: deptUserEmail
                                }).appendTo(form);
                            });


                            $(".row-checkbox:checked").each(function() {
                                // Get the parent row of this checkbox
                                let row = $(this).closest('.row');

                                let contactName = row.find(".contact_name").val().trim();
                                let contactEmail = row.find(".contact_email").val().trim();

                                let reinsurer_id = '';
                                if (stage_id == 2 || stage_id == 4) {
                                    reinsurer_id = row.find("#reinsurer_id").val().trim();
                                }


                                // let mainContactPerson = row.find(".main_contact_person").val().trim();


                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person[contact_name][]',
                                    value: contactName
                                }).appendTo(form);

                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person[contact_email][]',
                                    value: contactEmail
                                }).appendTo(form);
                                if (stage_id == 2 || stage_id == 4) {
                                    $('<input>').attr({
                                        type: 'hidden',
                                        name: 'selected_contact_person[reinsurer_id][]',
                                        value: reinsurer_id
                                    }).appendTo(form);
                                }


                            });
                            $(".row-radio:checked").each(function() {
                                let row = $(this).closest('.row');

                                let contactName = row.find(".contact_name").val().trim();
                                let contactEmail = row.find(".contact_email").val().trim();
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person_main[contact_name][]',
                                    value: contactName
                                }).appendTo(form);

                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person_main[contact_email][]',
                                    value: contactEmail
                                }).appendTo(form);
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: 'selected_contact_person_main[main_contact_person][]',
                                    value: 'Y'
                                }).appendTo(form);
                            });

                            if (stage_id != 5 && stage_id != 6) {

                                $('#sendEmail').modal('show');
                                $('#sendEmail').on('click', '.confirm-email', function() {
                                    const sendEmailValue = $(this).data('value');
                                    $('<input>').attr({
                                        type: 'hidden',
                                        name: 'send_email_flag',
                                        value: sendEmailValue
                                    }).appendTo(form);
                                    $('#sendEmail').modal('hide');
                                    // $('#editEmail').modal('show');
                                    form.submit();
                                });
                            } else {
                                form.submit();
                            }

                        } else {
                            form.reportValidity();
                        }
                    } else if ($(this).attr('id') === 'facultative_generate_slip') {
                        setFacScheduleDetails();
                    }

                });

                $('#generate_slip').click(function(event) {
                    event.preventDefault();
                    $('#editStatusQuoteModal').css({
                        'opacity': 0.0001,
                        'pointer-events': 'none'
                    });

                    $('#printoutType').modal('show');

                    $('.printtypeClose').on('click', function() {
                        $('#editStatusQuoteModal').css({
                            'opacity': 1,
                            'pointer-events': 'auto'
                        });
                    });

                    $('#printoutType').off('click').on('click', '.confirm_print_type', function() {
                        const printoutType = $(this).data('value');

                        const sourceForm = $('#statusUpdateForm');
                        const postForm = $('#quoteSlipForm');
                        postForm.empty(); // Clear previous input fields

                        // Add CSRF token
                        postForm.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: $('meta[name="csrf-token"]').attr('content')
                        }));

                        // Copy all fields from the main form
                        sourceForm.serializeArray().forEach(field => {
                            postForm.append($('<input>', {
                                type: 'hidden',
                                name: field.name,
                                value: field.value
                            }));
                        });

                        // Add the selected printout flag
                        postForm.append($('<input>', {
                            type: 'hidden',
                            name: 'printout_flag',
                            value: printoutType
                        }));

                        $('#editStatusQuoteModal').css({
                            'opacity': 1,
                            'pointer-events': 'auto'
                        });

                        $('#printoutType').modal('hide');

                        // Submit via POST and open PDF in new tab
                        postForm.submit();

                        // Prevent multiple bindings
                        $('#printoutType').off('click', '.confirm_print_type');
                    });
                });

                //facultative
                $('#facultative_generate_slip').click(function(event) {
                    event.preventDefault();

                    $('#editStatusFacultativeModal').css({
                        'opacity': 0.0001,
                        'pointer-events': 'none'
                    });
                    $('.printtypeClose').on('click', function() {
                        $('#editStatusFacultativeModal').css({
                            'opacity': 1,
                            'pointer-events': 'auto'
                        });
                    })
                    $('#printoutType').modal('show');

                    $('#printoutType').on('click', '.confirm_print_type', function() {
                        const printoutType = $(this).data('value');

                        // Add printout type to form
                        const form = $('#facultative-statusUpdateForm');
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'printout_flag',
                            value: printoutType
                        }).appendTo(form);
                        $('#editStatusFacultativeModal').css({
                            'opacity': 1,
                            'pointer-events': 'auto'
                        });

                        // Get form data after adding printout type
                        let formData = form.serialize();

                        $('#printoutType').modal('hide');

                        let slipUrl = "{!! route('docs.quotationCoverSlip') !!}?" + formData;

                        $('#docIframe').attr('src', slipUrl);
                        window.open(slipUrl, '_blank'); // Open the URL in a new tab
                    });
                });

                $('#add-customer').click(function() {
                    var newCustomerRow = `
                    <div class="col-12 customer-row">
                        <div class="row mt-3">
                            <div class="col-5">
                                <x-Input req="" inputLabel="Name" name="customer_name[]" value="" />
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input-group">
                                    <x-Input req="" inputLabel="Email" name="customer_email[]" value="" />
                                </div>
                            </div>

                            <div class="col-1" style="margin-top: 30px;">
                                <i class="bx bx-show preview" style="cursor:pointer;"></i>
                            </div>
                        </div>
                    </div>
                    `;


                    $('#customers-container').append(newCustomerRow);
                });

                //SEARCH REINSURES
                $('#quote-search-bar').on('input', function() {
                    var searchQuery = $(this).val().toLowerCase();

                    // Filter customers based on search query
                    var filteredCustomers = customers.filter(customer => customer.name.toLowerCase()
                        .includes(
                            searchQuery));



                    $('#quote-search-results').empty();

                    if (filteredCustomers.length > 0) {
                        filteredCustomers.forEach(function(customer) {
                            $('#quote-search-results').append(`
                <div class="customer-result" data-id="${customer.customer_id}">
                    <span>${customer.name}</span>
                    <button type="button" class="btn btn-success btn-sm select-customer">+</button>
                </div>
                
                `);
                        });
                    }
                });

                //facultative search
                $('#facultative-search-bar').on('input', function() {
                    var searchQuery = $(this).val().toLowerCase();

                    // Filter customers based on search query
                    var filteredCustomers = customers.filter(customer => customer.name.toLowerCase()
                        .includes(
                            searchQuery));



                    $('#facultative-search-results').empty();

                    if (filteredCustomers.length > 0) {
                        filteredCustomers.forEach(function(customer) {
                            $('#facultative-search-results').append(`
                            <div class="customer-result" data-id="${customer.customer_id}">
                                <span>${customer.name}</span>
                                <button type="button" class="btn btn-success btn-sm select-customer">+</button>
                            </div>
                
                `);
                        });
                    }
                });

                // Add selected customer to the list
                $(document).on('click', '.select-customer', function() {
                    var customerId = $(this).closest('.customer-result').data('id');
                    var category_type = $('#category_type').val();
                    var customer = customers.find(cust => cust.customer_id === customerId);

                    customer.contact_persons = typeof customer.contact_persons === 'string' ?
                        JSON.parse(customer.contact_persons || '[]') :
                        customer.contact_persons || [];



                    let contactPersonsHtml = '';
                    let initialVisibleContacts = 1;


                    if (customer.contact_persons && customer.contact_persons.length > 0) {
                        // Add container for contacts with show more button
                        contactPersonsHtml = '<div id="contacts-container-' + customer.customer_id +
                            '" class="contacts-container">';

                        customer.contact_persons.forEach((contact, index) => {
                            // Set display style based on index
                            const displayStyle = index < initialVisibleContacts ? '' : 'display: none;';

                            contactPersonsHtml += `
                    <div class="row contact-row" data-index="${index}" style="${displayStyle}">
                        <input type="hidden"  id="reinsurer_id" name="reinsurer_id" value="${customer.customer_id}" data-index="${index}"/>
                        <input type="hidden" class="main_contact_person" name="main_contact_person[]" value="${contact.main_contact_person}" data-index="${index}" />
                        <div class="col-4">
                            <label>Contact Name</label>
                            <input type="text" class="form-control contact_name" name="contact_name[]" value="${contact.contact_name}" data-index="${index}" required>
                        </div>
                        <div class="col-4">
                            <label>Email</label>
                            <input type="email" class="form-control contact_email" name="contact_email[]" value="${contact.contact_email}" data-index="${index}" required>
                        </div>
                        <div class="col-2 mt-3">
                            <div>
                            <label>Primary Email</label>
                            </div>
                            <input type="radio" name="select_contact_main_${customer.customer_id}" value="${index}" data-index="${index}" class="form-check-input row-radio">
                        </div>
                        <div class="col-2 mt-3">
                            <div>
                                <label>CC Email</label>
                            </div>
                            <input type="checkbox" name="select_contact_main[]" value="${index}" data-index="${index}" class="form-check-input row-checkbox">
                        </div>
                    </div>`;
                        });

                        // Add show more button only if there are more contacts to show
                        if (customer.contact_persons.length > initialVisibleContacts) {
                            contactPersonsHtml += `
                        <div class="row mt-2">
                            <div class="col-12">
                                <button type="button" class="btn btn-sm btn-outline-primary show-more-btn" data-customer-id="${customer.customer_id}">
                                    Show More Contacts (${customer.contact_persons.length - initialVisibleContacts} more)
                                </button>
                            </div>
                        </div>`;
                        }

                        contactPersonsHtml += '</div>';
                    } else {
                        contactPersonsHtml = `<p>No contacts available.</p>`;
                    }

                    if (category_type == 2 && type_of_business == 'FPR' || type_of_business == 'FNP') {

                        var customer = customers.find(cust => cust.customer_id === customerId);
                        $('#facultative-selected-customers-list').append(`
                     <div class="customer-entry" data-id="${customer.id}">
                         <hr>
                        <div class="row">
                            <div class="col-4">
                                <input type="hidden"  name="customer_id[]" value="${customer.customer_id}" />
                                    <label for="customer_name_${customer.id}">Name</label>
                                    <input type="text" class="form-control" id="customer_name_${customer.id}" name="customer_name[]" value="${customer.name}" required readonly>
                            </div>  
                            <div class="col-3 mt-1">
                                    <label for="written share">Written Share(%)<font style="color:red;">*</font></label>
                                    <div class="input-group">
                                    <input type="number" name="written_share[]" id="written_share_${customer.id}"
                                        class="form-control document_file checkempty" placeholder="" required />
                                    </div>
                                </div>                    
                      

                            <div class="col-4 mt-1">
                                    <label for="customer_email_${customer.id}">Email</label>
                                     <div class="input-group">
                                        <input type="text" class="form-control" id="customer_email_${customer.id}" name="customer_email[]" value="${customer.email}" required>
                                        <button type="button" class="btn btn-danger btn-sm remove-customer">x</button>
                                     </div>
                            </div>
                           <!-- <div  class="col-1 mt-1">
                                    <label for="contact_name_${customer.id}">Contact Name</label>
                                   <div class="input-group">
                                         <input type="text" class="form-control" id="contact_name_${customer.id}" name="contact_name[]" value="${customer.contact_name}" required>
                                        <button type="button" class="btn btn-danger btn-sm remove-customer">x</button>
                                    </div>

                            </div>-->
                        </div>
                     <!--Contact person-->
                        <div class="mt-2">
                            <label class="col-form-label fw-bold text-black">Contact Person:</label>
                            ${contactPersonsHtml}
                           
                        </div>
                      <!--end contact person-->
                         <hr>
                    </div>
                     <div>                   
               
    
                `);

                        // facultative hide search results after selection
                        $('#facultative-search-results').empty();
                    } else if (category_type == 1 && type_of_business == 'FPR' || type_of_business == 'FNP') {
                        var customer = customers.find(cust => cust.customer_id === customerId);
                        $('#quote-selected-customers-list').append(`
                     <div class="customer-entry" data-id="${customer.id}">
                         <hr>
                        <div class="row">
                            <div class="col-4">
                                <input type="hidden" name="customer_id[]" value="${customer.customer_id}" />
                                    <label for="customer_name_${customer.id}">Name</label>
                                    <input type="text" class="form-control" id="customer_name_${customer.id}" name="customer_name[]" value="${customer.name}" required readonly>
                            </div>

                            <div class="col-4 mt-1">
                                    <label for="customer_email_${customer.id}">Email</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer_email_${customer.id}" name="customer_email[]" value="${customer.email}" required>
                                    <button type="button" class="btn btn-danger btn-sm remove-customer">x</button>
                                </div>
                            </div>
                            <!--<div  class="col-3 mt-1">
                                    <label for="contact_name_${customer.id}">Contact Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="contact_name_${customer.id}" name="contact_name[]" value="${customer.contact_name}" required>
                                        <button type="button" class="btn btn-danger btn-sm remove-customer">x</button>
                                    </div>

                            </div>-->
                            <!--Contact person-->
                        <div class="row mt-2">
                             <label class="col-form-label fw-bold text-black">Contact Person:</label>
                            ${contactPersonsHtml}
                           
                        </div>
                      <!--end contact person-->
                            
                        
                        </div>
                        <hr>
                    </div>
                
                `);


                        // hide search results after selection
                        $('#quote-search-results').empty();
                    }
                    if (category_type == 1 && type_of_business != 'FPR' || type_of_business != 'FNP') {
                        var customer = customers.find(cust => cust.customer_id === customerId);
                        $('#quote-selected-customers-list').append(`
                     <div class="customer-entry" data-id="${customer.id}">
                         <hr>
                        <div class="row">
                            <div class="col-4">
                                <input type="hidden" name="customer_id[]" value="${customer.customer_id}" />
                                    <label for="customer_name_${customer.id}">Name</label>
                                    <input type="text" class="form-control" id="customer_name_${customer.id}" name="customer_name[]" value="${customer.name}" required readonly>
                            </div>

                            <div class="col-4 mt-1">
                                    <label for="customer_email_${customer.id}">Email</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer_email_${customer.id}" name="customer_email[]" value="${customer.email}" required>
                                    <button type="button" class="btn btn-danger btn-sm remove-customer">x</button>
                                </div>
                            </div>
                            <!--<div  class="col-3 mt-1">
                                    <label for="contact_name_${customer.id}">Contact Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="contact_name_${customer.id}" name="contact_name[]" value="${customer.contact_name}" required>
                                        <button type="button" class="btn btn-danger btn-sm remove-customer">x</button>
                                    </div>

                            </div>-->
                            <!--Contact person-->
                        <div class="row mt-2">
                             <label class="col-form-label fw-bold text-black">Contact Person:</label>
                            ${contactPersonsHtml}
                           
                        </div>
                      <!--end contact person-->
                            
                        
                        </div>
                        <hr>
                    </div>
                
                `);


                        // hide search results after selection
                        $('#quote-search-results').empty();
                    }
                });

                // Handle show more button for contacts
                $(document).on('click', '.show-more-btn', function() {
                    const customerId = $(this).data('customer-id');
                    $(`#contacts-container-${customerId} .contact-row`).show();
                    $(this).hide();
                });



                $(document).on('click', '.remove-customer', function() {
                    $(this).closest('.customer-entry').remove();
                });

                function updateTotalWrittenShare() {
                    let totalDistributed = 0;
                    $("input[name='written_share[]']").each(function() {
                        let value = parseFloat($(this).val()) || 0;
                        totalDistributed += value;
                    });
                    $("#fac_distributed").val(totalDistributed);
                    let written_share_total = $('#written_share_total').val();
                    let totalUndistributed = parseInt(written_share_total) - parseInt(totalDistributed);
                    $("#fac_undistributed").val(totalUndistributed);
                    if ($("#fac_undistributed").val() < 0) {
                        toastr.error('written share exceeds the total share');


                    }
                }

                // Event listener for changes in written share input fields
                $(document).on("input", "input[name='written_share[]']", function() {
                    updateTotalWrittenShare();
                });
                updateTotalWrittenShare();



                // Event listener for removing customers
                $(document).on("click", ".remove-customer", function() {
                    $(this).closest(".customer-entry").remove();
                    updateTotalWrittenShare();
                });


                //show more contact person--
                $(document).on('click', '.show-more-btn', function(event) {
                    if (event.target.classList.contains('show-more-btn')) {
                        const button = event.target;
                        const customerId = button.getAttribute('data-customer-id');
                        const container = document.getElementById('contacts-container-' + customerId);

                        if (!container) return;

                        const hiddenContacts = [];
                        const allRows = container.querySelectorAll('.contact-row');

                        for (let i = 0; i < allRows.length; i++) {
                            if (allRows[i].style.display === 'none') {
                                hiddenContacts.push(allRows[i]);
                            }
                        }

                        if (hiddenContacts.length > 0) {
                            hiddenContacts[0].style.display = '';

                            if (hiddenContacts.length <= 1) {
                                button.style.display = 'none';
                            } else {
                                button.textContent = `Show More Contacts (${hiddenContacts.length - 1} more)`;
                            }
                        }
                    }
                });

                //----Category sweet ----------
                let categoryMessage = @json(session('success'));
                if (categoryMessage) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: categoryMessage
                    });
                } else {
                    let errorMessage = @json(session('error'));
                    if (errorMessage) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                    }
                }
                $toggleIcon.addClass('bi-chevron-right');

                // Toggle icon rotation
                $financialSection.on('show.bs.collapse', function() {
                    $toggleIcon.removeClass('bi-chevron-right').addClass('bi-chevron-down');
                });

                $financialSection.on('hide.bs.collapse', function() {
                    $toggleIcon.removeClass('bi-chevron-down').addClass('bi-chevron-right');
                });

                function modal_script() {
                    {{-- Steve Start --}}
                    $('#tnp_section').hide();
                    $('#tpr_section').hide();
                    $('#fac_section').hide();
                    $('#trt_common').hide();
                    $('#treaty_grp').hide();
                    $('#eml_rate').hide();
                    $('#eml_amt').hide();
                    $('.eml-div').hide();
                    $('.brokerage_comm_amt_div').hide();
                    $('.brokerage_comm_rate_div').hide();

                    $(document).on('click', '.add-contact', function() {
                        const lastContactSection = $('.contactsContainers').last();
                        let prevCounter = lastContactSection.data('counter');

                        const contact_name = $(`#contact_name-${prevCounter}`).val()
                        const email = $(`#email-${prevCounter}`).val()
                        const phone_number = $(`#phone_number-${prevCounter}`).val()
                        if (contact_name == null || contact_name == '' || contact_name == ' ') {
                            toastr.error('Please capture Contact Name', 'Incomplete data')
                            return false
                        } else if (email == null || email == '' || email == ' ') {
                            toastr.error('Input Email', 'Incomplete data')
                            return false
                        } else if (phone_number == null || phone_number == '' || phone_number == ' ') {
                            toastr.error('Input Mobile Phone Number', 'Incomplete data')
                            return false
                        }
                        let counter = prevCounter + 1;
                        // Validate only contact details before adding a new contact form
                        let isValid = true;

                        $(".contact-form").last().find("input[req='required']").each(function() {
                            if ($(this).val().trim() === "") {
                                isValid = false;
                                $(this).addClass("is-invalid"); // Highlight the empty field
                            } else {
                                $(this).removeClass("is-invalid");
                            }
                        });

                        if (!isValid) {
                            alert("Please fill all required Contact Details before adding another.");
                            return;
                        }

                        // Append new contact form if validation passes
                        $(document).find(`#contactsContainer`).append(`
                        <div class="row mb-4 contactsContainers" data-counter="${counter}">
                            <x-OnboardingInputDiv>
                                <x-Input name="contact_name[]" id="contact_name-${counter}" placeholder="Enter name" inputLabel="Contact Fullname"
                                    req="required" />
                                <div id="contact_name_results" class="dropdown-menu" style="display: none;"></div>
                                <div class="error-message" id="full_name_error"></div>  
                                <div class="error-message" id="full_name_error_${counter}"></div>
                                <div id="full_name_results_${counter}" class="dropdown-menu" style="display: none;"></div>
                            </x-OnboardingInputDiv>
                            <x-OnboardingInputDiv>
                                <x-EmailInput id="email-${counter}" name="email[]" req="required" inputLabel="Email Address"
                                    placeholder="Enter email" />
                            </x-OnboardingInputDiv>
                                <x-OnboardingInputDiv>
                                    <x-NumberInput id="phone_number-${counter}" name="phone_number[]" req="required" inputLabel="Mobile." class="phone"
                                        placeholder="Enter phone number" />
                                </x-OnboardingInputDiv>
                            <div class="col-sm-3">
                                <Label>Telephone</Label>
                                <div class="input-group mb-3">
                                    <input id="telephone-${counter}" class="form-control" name="telephone[]" class="telephone"
                                        placeholder="Enter telephone number" />
                                    <button class="btn btn-primary btn-danger remove-contact" type="button" id="remove-contact-${counter}"
                                        data-counter="${counter}">
                                        <i class="bx bx-minus"></i>
                                    </button>
                                </div>
                            </div>
                        
                        </div>
                `);

                    });
                    $(document).on('click', '.remove-contact', function() {
                        $(this).closest('.contactsContainers').remove();
                    });

                    $("select#type_of_bus").change(function() {
                        var bustype = $("select#type_of_bus option:selected").attr('value');

                        if (bustype == 'FPR' || bustype == 'FNP') {
                            $('#fac_section').show();

                            $('#tpr_section').hide();
                            $('#tnp_section').hide();
                            $('#trt_common').hide();
                            $('#treaty_grp').hide();

                            processSections('.fac_section', '.fac_section_div', 'enable');
                            processSections('.reins_comm_rate', '.reins_comm_rate_div', 'disable');
                            processSections('.tpr_section', '.tpr_section_div', 'disable');
                            processSections('.tnp_section', '.tnp_section_div', 'disable');
                            processSections('.trt_common', '.trt_common_div', 'disable');
                            processSections('.treaty_grp', '.treaty_grp_div', 'disable');
                        } else if (bustype == 'TPR') {
                            $('#treaty_grp').show();
                            $('#trt_common').show();
                            $('#tpr_section').show();
                            $('#fac_section').hide();
                            $('#tnp_section').hide();

                            processSections('.trt_common', '.trt_common_div', 'enable');
                            processSections('.treaty_grp', '.treaty_grp_div', 'enable');
                            processSections('.tpr_section', '.tpr_section_div', 'enable');
                            processSections('.reinsurer_per_treaty', '.reinsurer_per_treaty_div', 'disable');
                            processSections('.fac_section', '.fac_section_div', 'disable');
                            processSections('.tnp_section', '.tnp_section_div', 'disable');



                        } else if (bustype == 'TNP') {
                            $('#treaty_grp').show();
                            $('#trt_common').show();
                            $('#tnp_section').show();
                            $('#tpr_section').hide();
                            $('#fac_section').hide();

                            processSections('.trt_common', '.trt_common_div', 'enable');
                            processSections('.treaty_grp', '.treaty_grp_div', 'enable');
                            processSections('.tnp_section', '.tnp_section_div', 'enable');
                            processSections('.tpr_section', '.tpr_section_div', 'disable');
                            processSections('.fac_section', '.fac_section_div', 'disable');


                        }
                        //     $('#customer_id').on('change', function() {
                        //     var customerId = $(this).val();

                        //     if (customerId) {
                        //     $.ajax({
                        //     url: "{{ route('get-customer-data') }}",
                        //     type: 'GET',
                        //     data: { customer_id: customerId },
                        //     success: function(response) {
                        //         var data = response.data[0];
                        //         console.log(data);
                        //    if (response) {
                        //     $('#contact_name').val(data.name);
                        //     $('#email').val(data.email);
                        //     $('#telephone').val(data.telephone);
                        //     $('#phone_number').val(data.phone); 
                        //     }
                        //     },
                        //     error: function(xhr, status, error) {
                        //     console.error(error);
                        //     }
                        //     });
                        //     } else {
                        //     console.log('No customer selected');
                        //     }
                        //     });
                        let selectedTreatyType = ''
                        $.ajax({
                            url: "{{ route('cover.get_treatyperbustype') }}",
                            data: {
                                "type_of_bus": bustype
                            },
                            type: "get",
                            success: function(resp) {
                                $(`#treatytype`).empty();

                                $(`#treatytype`).append($('<option>').text(
                                        '-- Select Treaty Type--')
                                    .attr('value', ''));
                                $.each(resp, function(i, value) {
                                    $(`#treatytype`).append($('<option>').text(value
                                            .treaty_code + " - " + value.treaty_name)
                                        .attr('value', value.treaty_code)
                                    );
                                });
                                $(`#treatytype option[value='${selectedTreatyType}']`).prop(
                                    'selected',
                                    true)
                                $(`#treatytype`).trigger('change.select2');
                            },
                            error: function(resp) {
                                console.error;
                            }
                        })
                    });

                    $('select#type_of_bus').trigger('change');

                    $("select#class_group").change(function() {
                        var class_group = $("select#class_group option:selected").attr('value');
                        $('#classcode').empty();
                        if ($(this).val() != '') {
                            $('#class').prop('disabled', false)
                            $.ajax({
                                url: "{{ route('get_class') }}",
                                data: {
                                    "class_group": class_group
                                },
                                type: "get",
                                success: function(resp) {
                                    /*remove the choose branch option*/
                                    $('#classcode').empty();
                                    var classes = $.parseJSON(resp);

                                    $('#classcode').append($('<option>').text(
                                            '-- Select Class Name--')
                                        .attr('value', ''));
                                    $.each(classes, function(i, value) {
                                        $('#classcode').append($('<option>').text(value
                                                .class_code + " - " + value.class_name)
                                            .attr('value', value.class_code)
                                        );


                                    });

                                    $('.section').trigger("chosen:updated");
                                },
                                error: function(resp) {
                                    console.error(resp);
                                }
                            })
                        }
                    });

                    $('select#covertype').trigger('change');

                    if ($("select#covertype option:selected").attr('covertype_desc') != 'B') {
                        $('#bindercoversec').hide();
                    }

                    /*** On change of cover Type ***/
                    $("select#covertype").change(function() {
                        // var binder = $("#covertype").val();
                        var binder = $("select#covertype option:selected").attr('covertype_desc')
                        $('#bindercoverno').empty();

                        if (binder == 'B') {
                            $('#bindercoversec').show();

                            $.ajax({
                                url: "{{ route('get_binder_covers') }}",
                                //data:{"branch":branch},
                                type: "get",
                                success: function(resp) {

                                    /*remove the choose branch option*/
                                    $('#bindercoverno').empty();
                                    var binders = $.parseJSON(resp);
                                    $('#bindercoverno').append($('<option>').text(
                                            'Select Binder Cover')
                                        .attr('value', ''));

                                    $.each(binders, function(i, value) {
                                        $('#bindercoverno').append($('<option>').text(value
                                            .binder_cov_no + "  -  " + value
                                            .agency_name
                                        ).attr('value', value.binder_cov_no));
                                    });

                                    $('.section').trigger("chosen:updated");
                                },
                                error: function(resp) {
                                    console.error;
                                }
                            })
                        } else if (binder == 'N') {
                            $('#bindercoverno').empty();
                            $('#bindercoversec').hide();
                            $('.section').trigger("chosen:updated");

                            $('#bindercoverno').prop('required', false);
                        }
                    });

                    /*** On change Pay Method ***/
                    $("select#pay_method").change(function() {
                        var pm = $("select#pay_method option:selected").attr('pay_method_desc');
                        $('#no_of_installments').empty();

                        if (pm === 'I') {
                            $('#no_of_installments_sec').show();
                            $('#fac_installments_box').hide();
                            $('#no_of_installments').prop('required', true);
                            $('#no_of_installments').empty();
                            $('#no_of_installments').val();
                            $('#add_fac_inst_btn_section').show();
                        } else {
                            $('#no_of_installments').val(1);
                            $('#no_of_installments_sec').hide();
                            $('#fac_installments_box').hide();
                            $('#add_fac_inst_btn_section').hide();
                            $('#no_of_installments').prop('required', false);
                        }
                    });

                    $('select#pay_method').trigger('change');

                    /*** On change Broker Flag ***/
                    $("select#broker_flag").change(function() {
                        var broker_flag = $("select#broker_flag option:selected").attr('value');
                        // $('#brokercode').empty();
                        if (broker_flag == 'Y') {
                            $('.brokercode_div').show();
                            $('#brokercode').prop('required', true);
                            $('#brokercode').prop('disabled', false);
                        } else {
                            $('#brokercode').val('');
                            $('.brokercode_div').hide();
                            $('#brokercode').prop('required', false);
                            $('#brokercode').prop('disabled', true);
                        }
                    });
                    $("select#broker_flag").trigger('change')

                    $('#add_fac_instalments').on('click', function() {
                        var noOfInstallments = $("#no_of_installments").val();
                        var businessType = $("#type_of_bus").val();
                        var cedantPremium = $("#cede_premium").val();
                        var facShareOffered = $("#fac_share_offered").val();
                        var commRate = $("#comm_rate").val();

                        if (Boolean(noOfInstallments === '')) {
                            toastr.error(`Please add Installments`, 'Incomplete data')
                            return false
                        } else if (Boolean(businessType === '')) {
                            toastr.error(`Please Select Business Type`, 'Incomplete data')
                            return false
                        } else if (Boolean(cedantPremium === '')) {
                            toastr.error(`Please add Cedant Premium`, 'Incomplete data')
                            return false
                        } else if (Boolean(facShareOffered === '')) {
                            toastr.error(`Please add Share Offered`, 'Incomplete data')
                            return false
                        } else if (Boolean(commRate === '')) {
                            toastr.error(`Please add Commission Rate`, 'Incomplete data')
                            return false
                        }
                        var instalAmount = computateInstalment()
                        // computation for cedant installment amount
                        $("#installment_total_amount").val(instalAmount);

                        $('#fac_installments_box').show();
                        // $('#no_of_installments').trigger('change');
                        $('#fac-installments-section').empty()
                        const totalInstallments = parseInt($('#no_of_installments').val().replace(/,/g, '')) ||
                            0;
                        var no_of_installments = parseInt($('#no_of_installments').val().replace(/,/g, '')) ||
                            0;
                        if (no_of_installments > 0) {
                            const totalAmount = instalAmount;
                            const totalFacAmount = parseFloat(totalAmount) || 0;
                            const totalFacInstAmt = (totalFacAmount / totalInstallments).toFixed(2);

                            installmentTotalAmount = totalFacAmount

                            if (totalInstallments <= 100) {
                                for (let i = 1; i <= totalInstallments; i++) {
                                    $('#fac-installments-section').append(`
                                <div class="row fac-instalament-row" data-count="${i}">
                                    <div class="col-md-3">
                                        <label class="form-label">Installment</label>
                                        <input type="hidden" name="installment_no[]" value="${i}" readonly class="form-inputs"/>
                                        <input type="hidden" name="installment_id[]" value=""/>
                                        <input type="text" value="installment No. ${i}" id="instl_no_${i}" readonly class="form-inputs" required/>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="instl_date_${i}">Installment Due Date</label>
                                        <input type="date" name="installment_date[]" id="instl_date_${i}"  class="form-inputs" required/>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="instl_amnt_${i}">Total Installment Amount</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="installment_amt[]" id="instl_amnt_${i}" value="${numberWithCommas(totalFacInstAmt)}" class="form-inputs form-input-group amount"  onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required/>
                                            <button class="btn btn-danger btn-sm remove-fac-instalment" type="button" id="remove-fac-instalment"><i class="bx bx-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            `);
                                }
                            }
                        }
                    });

                    $('#fac-installments-section').on('click', '.remove-fac-instalment', function() {
                        // const removedIndex = $(this).closest('.fac-instalament-row').data('count');
                        const currentInstallments = $('#no_of_installments').val();
                        const remaingInstalment = currentInstallments >= 1 ? parseInt(currentInstallments - 1) :
                            0;
                        if (remaingInstalment > 0) {
                            $('#no_of_installments').val(remaingInstalment);
                        } else {
                            $('#no_of_installments').val('');
                            $('#fac_installments_box').hide();
                        }
                        $('#no_of_installments').trigger('change');
                        $(this).closest('.fac-instalament-row').remove();
                    });
                    //  $('select#currency_code').trigger('change');
                    /*currency logic*/
                    // $("select#currency_code").change(function() {
                    //     var selected_currency = $("select#currency_code option:selected").attr('value');
                    //     var selected_descr = $("select#currency_code option:selected").text();

                    //     //alert(selected_currency);
                    //     //ajax to check for date
                    //     $.ajax({
                    //         url: "{{ route('get_todays_rate') }}",
                    //         data: {
                    //             'currency_code': selected_currency
                    //         },
                    //         type: "get",
                    //         success: function(resp) {
                    //             var status = $.parseJSON(resp);
                    //             // alert(status.valid);

                    //             if (status.valid == 2) {
                    //                 // alert('test');
                    //                 $('#today_currency').val(1);
                    //                 $('#today_currency').prop('readonly', true)
                    //             } else if (status.valid == 1) {
                    //                 // alert('test');
                    //                 //populate rate field
                    //                 $('#today_currency').val(status.rate);
                    //                 $('#today_currency').prop('readonly', true)
                    //             } else {
                    //                 $('#today_currency').prop('readonly', true)
                    //                 $('#today_currency').val('');
                    //                 alert('Currency rate for the day not yet set');
                    //                 // $.ajax({
                    //                 //     url:" {{ route('yesterdayRate') }}",
                    //                 //     data: {'currency_code':selected_currency},
                    //                 //     type: "GET",
                    //                 //     success: function(resp) {
                    //                 //         // alert(resp);
                    //                 //         if (resp ==  0) {
                    //                 //             $('#today_currency').prop('readonly', true)
                    //                 //             $('#today_currency').val('');
                    //                 //             $.notify({
                    //                 //                 title: "<strong>Today's currency rate not Set </strong><br>",
                    //                 //                 message: "Using yesterday's currency rate. Adjust currency rate to fit today's rate",
                    //                 //             },{
                    //                 //                 type: 'warning'
                    //                 //             });
                    //                 //         } else {
                    //                 //             var rate = resp.currency_rate
                    //                 //             $('#today_currency').val(rate);
                    //                 //             $('#today_currency').prop('readonly', true)
                    //                 //         }
                    //                 //     }
                    //                 // })
                    //             }
                    //         },
                    //         error: function(resp) {
                    //             //alert("Error");
                    //             console.error;
                    //         }
                    //     })
                    // });
                    /*end of currency logic*/

                    // $('#sum_insured_type')
                    $("select#sum_insured_type").change(function() {
                        var label_txt = $("select#sum_insured_type option:selected").text();
                        $('#sum_insured_label').text("(" + label_txt + ")");
                    });

                    $("#comm_rate").keyup(function() {
                        var ratex = $(this).val() || 0;
                        var cede = parseFloat(removeCommas($('#cede_premium').val())) || 0;
                        var commAmount = (ratex / 100) * cede;
                        $('#comm_amt').val(numberWithCommas(commAmount));
                        calculateBrokerageCommRate()
                    });

                    $("#reins_comm_rate").keyup(function() {
                        var ratex = $(this).val() || 0;
                        var cede = parseFloat(removeCommas($('#rein_premium').val())) || 0;
                        var commAmount = (ratex / 100) * cede;
                        $('#reins_comm_amt').val(numberWithCommas(commAmount));
                        calculateBrokerageCommRate()
                    });

                    $("#reins_comm_type").change(function() {
                        var comm_type = $(this).val();
                        // console.log('comm_type:' + comm_type);
                        if (comm_type == 'R') {
                            processSections('.reins_comm_rate', '.reins_comm_rate_div', 'enable');
                            $('#reins_comm_amt').prop('readonly', true)
                        } else {
                            processSections('.reins_comm_rate', '.reins_comm_rate_div', 'disable');
                            $('#reins_comm_amt').prop('readonly', false)
                        }

                    });

                    $("#reins_comm_type").trigger('change');

                    $("#cede_premium").keyup(function() {
                        $("#comm_rate").trigger('keyup');
                        $("#rein_premium").val($(this).val());
                    });

                    $("#rein_premium").keyup(function() {
                        $("#reins_comm_rate").trigger('keyup');
                    });

                    $(document).on('change', ".treaty_reinclass", function() {
                        const treatyType = $(`#treatytype`).val();
                        let counter = $(this).data('counter')
                        const reinclass = $(`#treaty_reinclass-${counter}`).val()

                        if (treatyType == null || treatyType == '' || treatyType == ' ') {

                            $(`#treaty_reinclass-${counter} option:selected`).removeAttr('selected');
                            $(`#treaty_reinclass-${counter}`).val(null)
                            toastr.error('Please Select Treaty Type First', 'Incomplete data')
                            //
                            return false
                        }

                        const premTypeCodeSelect = $(`#prem_type_code-${counter}-0`);
                        premTypeCodeSelect.attr('data-reinclass', reinclass);

                        $(`#prem_type_reinclass-${counter}-0`).val(reinclass);
                        $(`#treaty_grp #prem_type_treaty-${counter}-0`).trigger('change')


                    });

                    $(document).on('change', ".prem_type_code", function() {
                        let prem_type_code = $(this).val();
                        let classcounter = $(this).data('class-counter')
                        let premtypecounter = $(this).data('counter')
                        let treaty = $(`#prem_type_treaty-${classcounter}-${premtypecounter}`).val();
                        let reinclass = $(`#treaty_reinclass-${classcounter}`).val()

                        $(`#prem_type_reinclass-${classcounter}-${premtypecounter}`).val(reinclass);
                        // console.log('log',$(`#prem_type_reinclass-${classcounter}-${premtypecounter}`).val());
                        const premTypeCodeSelect = $(`#prem_type_code-${classcounter}-${premtypecounter}`);
                        premTypeCodeSelect.attr('data-reinclass', reinclass);
                        premTypeCodeSelect.attr('data-treaty', treaty);

                    });
                    $(document).on('change', ".prem_type_treaty", function() {
                        let treaty = $(this).val();
                        let classcounter = $(this).data('class-counter')
                        let premtypecounter = $(this).data('counter')
                        let reinclass = $(`#treaty_reinclass-${classcounter}`).val()
                        // console.log('treaty:' + treaty + ' reinclass:' + reinclass + ' classcounter:' +
                        //     classcounter + ' premcounter:' + premtypecounter);

                        fetchPremTypes(treaty, premtypecounter, classcounter)
                    });

                    function fetchPremTypes(treaty, premCounter, classCounter) {
                        let selectedPremTypes = []
                        const classElem = $(`#treaty_reinclass-${classCounter}`)
                        const reinClass = classElem.val()
                        // $('.prem_type_code[data-reinclass="' + reinClass + '"]').each(function () {
                        $('.prem_type_code[data-reinclass="' + reinClass + '"][data-treaty="' + treaty + '"]').each(
                            function() {
                                const selectedVal = $(this).find('option:selected').val()
                                if (selectedVal != null && selectedVal != '') {
                                    selectedPremTypes.push(selectedVal)
                                }
                            })

                        if (classElem.val() != '') {
                            $(`#prem_type_code-${classCounter}-${premCounter}`).prop('disabled', false)

                            $.ajax({
                                url: "{{ route('cover.get_reinprem_type') }}",
                                data: {
                                    "reinclass": reinClass,
                                    'selectedCodes': selectedPremTypes
                                },
                                type: "get",
                                success: function(resp) {

                                    $(`#prem_type_reinclass-${classCounter}`).val(reinClass);
                                    /*remove the choose branch option*/
                                    $(`#prem_type_code-${classCounter}-${premCounter}`).empty();

                                    $(`#prem_type_code-${classCounter}-${premCounter}`).append($(
                                            '<option>')
                                        .text('-- Select Premium Type--').attr('value', ''));
                                    $.each(resp, function(i, value) {
                                        $(`#prem_type_code-${classCounter}-${premCounter}`)
                                            .append($(
                                                    '<option>').text(value.premtype_code +
                                                    " - " + value
                                                    .premtype_name)
                                                .attr('value', value.premtype_code)
                                                .attr('data-reinclass', reinClass)
                                                .attr('data-treaty', treaty)
                                            );


                                    });
                                    $(`#prem_type_code-${classCounter}-${premCounter}`).trigger(
                                        'change.select2');
                                },
                                error: function(resp) {
                                    console.error;
                                }
                            })
                        }
                    }

                    $(document).on('click', '.add-comm-section', function() {
                        const addSectCounter = $(this).data('counter')

                        const lastCommSection = $(`#comm-section-${addSectCounter}`).find(
                            '.comm-sections:last');

                        const prevCounter = lastCommSection.data('counter')
                        const classCounter = lastCommSection.data('class-counter')
                        const reinClassVal = $(`#treaty_reinclass-${classCounter}`).val()
                        const premTypeVal = $(`#prem_type_code-${classCounter}-${prevCounter}`).val()
                        const premTypeComm = $(`#prem_type_comm_rate-${classCounter}-${prevCounter}`).val()
                        if (reinClassVal == null || reinClassVal == '' || reinClassVal == ' ') {
                            toastr.error('Please Select Reinsurance Class', 'Incomplete data')
                            return false
                        } else if (premTypeVal == null || premTypeVal == '' || premTypeVal == ' ') {
                            toastr.error('Please Select Premium Type', 'Incomplete data')
                            return false
                        } else if (premTypeComm == null || premTypeComm == '' || premTypeComm == ' ') {
                            toastr.error('Input Commission Rate', 'Incomplete data')
                            return false
                        }

                        // Increment the counter
                        let counter = prevCounter + 1;

                        appendCommSection(counter, classCounter)
                        // fetchPremTypes(counter,classCounter)

                        // $(document).find(`#prem_type_code-${classCounter}-${counter}`).select2();

                    });

                    $(document).on('click', '.remove-comm-section', function() {
                        $(this).closest('.comm-sections').remove();
                    });

                    function appendCommSection(premCounter, classCounter) {
                        const reinClassVal = $(`#treaty_reinclass-${classCounter}`).val()
                        const treatytype = $('#treatytype').val();

                        var btn_class = ''
                        var btn_id = ''
                        var fa_class = ''
                        if (premCounter == 0) {
                            btn_class = 'btn-primary add-comm-section'
                            btn_id = 'add-comm-section'
                            fa_class = 'bx-plus'
                        } else {
                            btn_class = 'btn-danger remove-comm-section'
                            btn_id = 'remove-comm-section'
                            fa_class = 'bx-minus'
                        }

                        $(document).find(`#comm-section-${classCounter}`).append(`
                    <div class="row comm-sections" id="comm-section-${classCounter}-${premCounter}" data-class-counter="${classCounter}" data-counter="${premCounter}">
                        <!-- prem_type_treaty -->
                        <div class="col-sm-3 prem_type_treaty_div">
                            <label class="form-label required">Treaty</label>
                            <select class="form-inputs select2 prem_type_treaty" name="prem_type_treaty[]" id="prem_type_treaty-${classCounter}-${premCounter}" data-class-counter="${classCounter}" data-counter="${premCounter}" required>
                                <option value=""> Select Treaty </option>
                                <option value="SURP"> SURPLUS </option>
                                <option value="QUOT"> QUOTA </option>
                            </select>
                        </div>
                        <!-- reinsurance premium types -->
                        <div class="col-sm-3">
                            <label class="form-label required">Premium Type</label>
                            <input type="hidden" class="form-inputs prem_type_reinclass" id="prem_type_reinclass-${classCounter}-${premCounter}" name="prem_type_reinclass[]" data-counter="${premCounter}" value="${reinClassVal}">

                            <select class="form-inputs select2 prem_type_code" name="prem_type_code[]" id="prem_type_code-${classCounter}-${premCounter}" data-reinclass="${reinClassVal}" data-treaty="" data-class-counter="${classCounter}" data-counter="${premCounter}" required>
                                <option value="">--Select Premium Type--</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label required">Commision(%)</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-inputs" name="prem_type_comm_rate[]" id="prem_type_comm_rate-${classCounter}-${premCounter}" data-counter="${premCounter}" required>
                                <button class="btn ${btn_class}" type="button" id="${btn_id}"><i class="bx ${fa_class}"></i></button>
                            </div>
                        </div>
                    </div>
                `);

                        $(`#prem_type_treaty-${classCounter}-${premCounter}`).empty();

                        // console.log('SPQT' + treatytype);
                        if (treatytype == 'SPQT') {
                            $(`.prem_type_treaty_div`).show();
                            $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text(
                                'SURPLUS AND QUOTA').attr('value', 'SPQT')).change();
                        } else if (treatytype == 'QUOT') {
                            $(`.prem_type_treaty_div`).show();
                            $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text('QUOTA')
                                .attr(
                                    'value', 'QUOT')).change();
                        } else if (treatytype == 'SURP') {
                            $(`.prem_type_treaty_div`).show();
                            $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text(
                                'SURPLUS').attr(
                                'value', 'SURP')).change();
                        }

                        $(`#treaty_grp #prem_type_treaty-${classCounter}-${premCounter}`).trigger('change');
                    }
                    $("#method").change(function() {
                        const MethodVal = $(`#method`).val();

                        $(".burning_rate").prop('disabled', true).val('');
                        $(".flat_rate").prop('disabled', true).val('');
                        $(".burning_rate_div").hide();
                        $(".flat_rate_div").hide();

                        if (MethodVal == 'B') {
                            $(".burning_rate_div").show();
                            $(".burning_rate").prop('disabled', false);
                        } else {
                            $(".flat_rate_div").show();
                            $(".flat_rate").prop('disabled', false);
                        }

                    });

                    $("#treatytype").change(function() {
                        let treatytype = $(this).find('option:selected').val()
                        console.log('wewe', treatytype);
                        $(`#prem_type_treaty-0-0`).empty();

                        if (treatytype == 'SURP') {

                            $('.reinsurer_per_treaty_div').hide();
                            $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                            $('.prem_type_treaty_div').show();
                            $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS').attr('value',
                                    'SURP'))
                                .change();

                            $('.no_of_lines_div').show();
                            $('.no_of_lines').prop('disabled', false).val(null);

                            $('.surp_retention_amt_div').show();
                            $('.surp_retention_amt').prop('disabled', false).val(null);

                            $('.surp_treaty_limit_div').show();
                            $('.surp_treaty_limit').prop('disabled', false).val(null);

                            $('.surp_header_div').show();

                            $('.quota_share_total_limit_div').hide();
                            $('.quota_share_total_limit').prop('disabled', true).val(null);

                            $('.retention_per_div').hide();
                            $('.retention_per').prop('disabled', true).val(null);

                            $('.treaty_reice_div').hide();
                            $('.treaty_reice').prop('disabled', true).val(null);

                            $('.quota_retention_amt_div').hide();
                            $('.quota_retention_amt').prop('disabled', true).val(null);

                            $('.quota_treaty_limit_div').hide();
                            $('.quota_treaty_limit').prop('disabled', true).val(null);

                            $('.quota_header_div').hide();

                        } else if (treatytype == 'QUOT') {

                            $('.reinsurer_per_treaty_div').hide();
                            $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                            $('.prem_type_treaty_div').show();
                            $(`#prem_type_treaty-0-0`).append($('<option>').text('QUOTA').attr('value', 'QUOT'))
                                .change();

                            $('.quota_share_total_limit_div').show();
                            $('.quota_share_total_limit').prop('disabled', false).val(null);

                            $('.retention_per_div').show();
                            $('.retention_per').prop('disabled', false).val(null);

                            $('.treaty_reice_div').show();
                            $('.treaty_reice').prop('disabled', false).val(null);

                            $('.quota_retention_amt_div').show();
                            $('.quota_retention_amt').prop('disabled', false).val(null);

                            $('.quota_treaty_limit_div').show();
                            $('.quota_treaty_limit').prop('disabled', false).val(null);

                            $('.quota_header_div').show();
                            //
                            $('.no_of_lines_div').hide();
                            $('.no_of_lines').prop('disabled', true).val(null);

                            $('.surp_retention_amt_div').hide();
                            $('.surp_retention_amt').prop('disabled', true).val(null);

                            $('.surp_treaty_limit_div').hide();
                            $('.surp_treaty_limit').prop('disabled', true).val(null);

                            $('.surp_header_div').hide();
                        } else if (treatytype == 'SPQT') {

                            $('.reinsurer_per_treaty_div').show();
                            $('.reinsurer_per_treaty').prop('disabled', false).val(null);

                            $('.prem_type_treaty_div').show();
                            $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS AND QUOTA').attr(
                                'value',
                                'SPQT')).change();

                            $('.quota_share_total_limit_div').show();
                            $('.quota_share_total_limit').prop('disabled', false).val(null);

                            $('.retention_per_div').show();
                            $('.retention_per').prop('disabled', false).val(null);

                            $('.quota_retention_amt_div').show();
                            $('.quota_retention_amt').prop('disabled', false).val(null);

                            $('.quota_treaty_limit_div').show();
                            $('.quota_treaty_limit').prop('disabled', false).val(null);

                            $('.treaty_reice_div').show();
                            $('.treaty_reice').prop('disabled', false).val(null);

                            $('.no_of_lines_div').show();
                            $('.no_of_lines').prop('disabled', false).val(null);

                            $('.surp_retention_amt_div').show();
                            $('.surp_retention_amt').prop('disabled', false).val(null);

                            $('.surp_treaty_limit_div').show();
                            $('.surp_treaty_limit').prop('disabled', false).val(null);

                            $('.surp_header_div').show();
                            $('.quota_header_div').show();
                        } else {

                            $('.reinsurer_per_treaty_div').hide();
                            $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                            $('.prem_type_treaty_div').show();
                            $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS').attr('value',
                                    'SURP'))
                                .change();

                            $('.no_of_lines_div').hide();
                            $('.no_of_lines').prop('disabled', true).val(null);

                            $('.surp_retention_amt_div').hide();
                            $('.surp_retention_amt').prop('disabled', true).val(null);

                            $('.surp_treaty_limit_div').hide();
                            $('.surp_treaty_limit').prop('disabled', true).val(null);

                            $('.surp_header_div').hide();

                            $('.quota_share_total_limit_div').hide();
                            $('.quota_share_total_limit').prop('disabled', true).val(null);

                            $('.retention_per_div').hide();
                            $('.retention_per').prop('disabled', true).val(null);

                            $('.treaty_reice_div').hide();
                            $('.treaty_reice').prop('disabled', true).val(null);

                            $('.quota_retention_amt_div').hide();
                            $('.quota_retention_amt').prop('disabled', true).val(null);

                            $('.quota_treaty_limit_div').hide();
                            $('.quota_treaty_limit').prop('disabled', true).val(null);

                            $('.quota_header_div').hide();

                        }

                    });

                    $(document).on('keyup', ".no_of_lines", function() {
                        let reinclass_counter = $(`.treaty_reinclass`).data('counter')
                        var lines = $(this).val() || 0;
                        var counter = $(this).data('counter');
                        var ret = parseFloat(removeCommas($(`#surp_retention_amt-${counter}`).val())) || 0;
                        var trt_limit = lines * ret;
                        $(`#surp_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
                    });

                    $(document).on('keyup', ".retention_per", function() {
                        var ret_per = $(this).val() || 0;
                        var counter = $(this).data('counter');
                        var quota_limit_total = parseFloat(removeCommas($(`#quota_share_total_limit-${counter}`)
                            .val())) || 0;
                        var trt_per = 100 - ret_per;
                        var ret_amt = (ret_per / 100) * quota_limit_total;
                        var trt_limit = (trt_per / 100) * quota_limit_total;

                        $(`#treaty_reice-${counter}`).val(trt_per);
                        $(`#quota_retention_amt-${counter}`).val(numberWithCommas(ret_amt));
                        $(`#quota_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
                    });

                    $(document).on('keyup', ".quota_share_total_limit", function() {
                        var ret_per = $(`#treaty_reice-${counter}`).val() || 0;
                        var counter = $(this).data('counter');
                        var quota_limit_total = parseFloat(removeCommas($(`#quota_share_total_limit-${counter}`)
                            .val())) || 0;
                        var trt_per = 100 - ret_per;
                        var ret_amt = (ret_per / 100) * quota_limit_total;
                        var trt_limit = (trt_per / 100) * quota_limit_total;

                        $(`#treaty_reice-${counter}`).val(trt_per);
                        $(`#quota_retention_amt-${counter}`).val(numberWithCommas(ret_amt));
                        $(`#quota_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
                    });

                    // Adding new layer
                    $('#layer-section').on('click', '#add-layer-section', function() {
                        const lastLayerSection = $('#layer-section .layer-sections:last');
                        const MethodVal = $('#method').val();
                        const prevCounter = lastLayerSection.data('counter');
                        const IndemnityTreatyLimit = $(`#indemnity_treaty_limit-${prevCounter}-0`).val();
                        const UnderlyingLimit = $(`#underlying_limit-${prevCounter}-0`).val();
                        const EgnpiVal = $(`#egnpi-${prevCounter}-0`).val();
                        const MinBcRate = $(`#min_bc_rate-${prevCounter}-0`).val();
                        const MaxBcRate = $(`#max_bc_rate-${prevCounter}-0`).val();
                        const FlatRate = $(`#flat_rate-${prevCounter}-0`).val();
                        const UpperAdj = $(`#upper_adj-${prevCounter}-0`).val();
                        const LowerAdj = $(`#lower_adj-${prevCounter}-0`).val();
                        const MinDeposit = $(`#min_deposit-${prevCounter}-0`).val();
                        const limit_per_reinclass = $(`#limit_per_reinclass-${prevCounter}-0`).val();

                        // Validation

                        if (!IndemnityTreatyLimit.trim()) {
                            toastr.error('Please Capture Treaty Limit', 'Incomplete data');
                            return false;
                        } else if (!UnderlyingLimit.trim()) {
                            toastr.error('Please Capture Deductive', 'Incomplete data');
                            return false;
                        } else if (!EgnpiVal.trim()) {
                            toastr.error('Please Capture EGNPI', 'Incomplete data');
                            return false;
                        } else if (!MinBcRate.trim() && MethodVal === 'B') {
                            toastr.error('Input Minimum Burning Cost Rate', 'Incomplete data');
                            return false;
                        } else if (!MaxBcRate.trim() && MethodVal === 'B') {
                            toastr.error('Input Maximum Burning Cost Rate', 'Incomplete data');
                            return false;
                        } else if (!FlatRate.trim() && MethodVal === 'F') {
                            toastr.error('Input Flat Rate', 'Incomplete data');
                            return false;
                        } else if (!UpperAdj.trim() && MethodVal === 'B') {
                            toastr.error('Please Capture Upper Adjustment Rate', 'Incomplete data');
                            return false;
                        } else if (!LowerAdj.trim() && MethodVal === 'B') {
                            toastr.error('Please Capture Lower Adjustment Rate', 'Incomplete data');
                            return false;
                        } else if (!MinDeposit.trim()) {
                            toastr.error('Please Confirm Minimum Deposit Premium(MDP) Amount',
                                'Incomplete data');
                            return false;
                        }

                        if (limit_per_reinclass === 'Y') {
                            const IndemnityTreatyLimit = $(`#indemnity_treaty_limit-${prevCounter}-1`).val();
                            const UnderlyingLimit = $(`#underlying_limit-${prevCounter}-1`).val();
                            const EgnpiVal = $(`#egnpi-${prevCounter}-1`).val();
                            const MinBcRate = $(`#min_bc_rate-${prevCounter}-1`).val();
                            const MaxBcRate = $(`#max_bc_rate-${prevCounter}-1`).val();
                            const FlatRate = $(`#flat_rate-${prevCounter}-1`).val();
                            const UpperAdj = $(`#upper_adj-${prevCounter}-1`).val();
                            const LowerAdj = $(`#lower_adj-${prevCounter}-1`).val();
                            const MinDeposit = $(`#min_deposit-${prevCounter}-1`).val();

                            if (!IndemnityTreatyLimit.trim()) {
                                toastr.error('Please Capture Treaty Limit', 'Incomplete data');
                                return false;
                            } else if (!UnderlyingLimit.trim()) {
                                toastr.error('Please Capture Deductive', 'Incomplete data');
                                return false;
                            } else if (!EgnpiVal.trim()) {
                                toastr.error('Please Capture EGNPI', 'Incomplete data');
                                return false;
                            } else if (!MinBcRate.trim() && MethodVal === 'B') {
                                toastr.error('Input Minimum Burning Cost Rate', 'Incomplete data');
                                return false;
                            } else if (!MaxBcRate.trim() && MethodVal === 'B') {
                                toastr.error('Input Maximum Burning Cost Rate', 'Incomplete data');
                                return false;
                            } else if (!FlatRate.trim() && MethodVal === 'F') {
                                toastr.error('Input Flat Rate', 'Incomplete data');
                                return false;
                            } else if (!UpperAdj.trim() && MethodVal === 'B') {
                                toastr.error('Please Capture Upper Adjustment Rate', 'Incomplete data');
                                return false;
                            } else if (!LowerAdj.trim() && MethodVal === 'B') {
                                toastr.error('Please Capture Lower Adjustment Rate', 'Incomplete data');
                                return false;
                            } else if (!MinDeposit.trim()) {
                                toastr.error('Please Confirm Minimum Deposit Premium(MDP) Amount',
                                    'Incomplete data');
                                return false;
                            }
                        }

                        // Increment the counter
                        let counter = prevCounter + 1;
                        $('#layer-section').append(`
                    <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                        <h6> Layer: ${counter+1} </h6>
                        <div class="row">
                            <!--Flag to show if layers are per class-->
                            <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                <label class="form-label required">Capture Limits per Class ?</label>
                                <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" id="limit_per_reinclass-${counter}-0" value="N" required>
                                    <option value=""> Select Option </option>
                                    <option value="N" selected> No </option>
                                    <option value="Y"> Yes </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1 nonprop_reinclass">
                            <label class="form-label required">Reinclass</label>
                            <input type="hidden" class="form-control layer_no" aria-label="layer_no" data-counter="${counter}" id="layer_no-${counter}-0" name="layer_no[]" value="${counter + 1}" readonly>
                            <input type="hidden" class="form-control nonprop_reinclass" aria-label="nonprop_reinclass" data-counter="${counter}" id="nonprop_reinclass-${counter}-0" name="nonprop_reinclass[]" value="ALL" readonly>
                            <input type="text" class="form-control nonprop_reinclass_desc" aria-label="nonprop_reinclass_desc" data-counter="${counter}" id="nonprop_reinclass_desc-${counter}-0" name="nonprop_reinclass_desc[]" value="ALL" readonly>
                        </div>
                        <!--Indemnity-->
                        <div class="col-sm-2">
                            <label class="form-label required">Limit</label>
                            <input type="text" class="form-inputs" aria-label="indemnity_limit" id="indemnity_treaty_limit-${counter}-0" name="indemnity_treaty_limit[]" onkeyup="this.value=numberWithCommas(this.value)">
                        </div>

                        <!--Underlying Limit-->
                        <div class="col-sm-2">
                            <label class="form-label required">Deductible Amount</label>
                            <input type="text" class="form-inputs" aria-label="underlying_limit" id="underlying_limit-${counter}-0" name="underlying_limit[]" onkeyup="this.value=numberWithCommas(this.value)" >
                        </div>

                        <!--EGNPI (Estimated Premium)-->
                        <div class="col-sm-2">
                            <label class="form-label required">EGNPI</label>
                            <input type="text" class="form-inputs" aria-label="egnpi" id="egnpi-${counter}-0" name="egnpi[]" onkeyup="this.value=numberWithCommas(this.value)" >
                        </div>

                        <!--For Burning Cost (B) --- Minimum Rate: (%)-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Burning Cost-Minimum Rate(%)</label>
                            <input type="text" name="min_bc_rate[]" id="min_bc_rate-${counter}-0" class="form-inputs burning_rate" value="{{ old('min_bc_rate') }}">
                        </div>

                        <!--Maximum Rate: (%)-->
                        <div class="col-sm-2 burning_rate_div">
                            <label class="form-label required">Maximum Rate: (%)</label>
                            <input type="text" name="max_bc_rate[]" id="max_bc_rate-${counter}-0" class="form-inputs burning_rate" value="{{ old('max_bc_rate') }}">
                        </div>

                        <!--For Flat Rate: (%)-->
                        <div class="col-sm-2 flat_rate_div">
                            <label class="form-label required">For Flat Rate: (%)</label>
                            <input type="text" name="flat_rate[]" id="flat_rate-${counter}-0" class="form-inputs flat_rate" value="{{ old('applied_rate') }}">
                        </div>

                        <!--Adjustable Annually Rate-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Upper Adjust. Annually Rate</label>
                            <input type="text" name="upper_adj[]" id="upper_adj-${counter}-0" class="form-inputs burning_rate" value="{{ old('upper_adj') }}">
                        </div>

                        <!--Adjustable Annually Rate-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Lower Adjust. Annually Rate</label>
                            <input type="text" name="lower_adj[]" id="lower_adj-${counter}-0" class="form-inputs burning_rate" value="{{ old('lower_adj') }}">
                        </div>

                        <!--Minimum Deposit Premium -->
                        <div class="col-sm-3">
                            <label class="form-label required">Minimum Deposit Premium </label>
                            <div class="input-group mb-3">
                                <input type="text" name="min_deposit[]" id="min_deposit-${counter}-0" class="form-control" value="{{ old('min_deposit') }}" onkeyup="this.value=numberWithCommas(this.value)">
                                <button class="btn btn-danger remove-layer-section" type="button" id="remove-layer-section"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>

                        {{-- Reinstatement Type Arrangement --}}
                        <div class="col-sm-3 reinstatement_type_div tnp_section_div">
                            <label class="form-label required"> Reinstatement Type </label>
                            <div class="input-group mb-3">
                                <select name="reinstatement_type[]" id="reinstatement_type-${counter}-0" class="form-inputs select2">
                                    <option value="NOR">Number of Reinstatement</option>
                                    <option value="AAL">Annual Aggregate Limit</option>
                                </select>
                            </div>
                        </div>
                        {{-- Reinstatement Type Value --}}
                        <div class="col-sm-3 reinstatement_value_div tnp_section_div">
                            <label class="form-label required"> Reinstatement Value </label>
                            <div class="input-group mb-3">
                                <input type="text" name="reinstatement_value[]" id="reinstatement_value-${counter}-0" class="form-control reinstatement_value tnp_section" value="" onkeyup="this.value=numberWithCommas(this.value)" required>
                            </div>
                        </div>
                    </div>
                `);

                        $(".burning_rate_div").hide();
                        $(".flat_rate_div").hide();

                        if (MethodVal === 'B') {
                            $(".burning_rate_div").show();
                            $(".burning_rate").prop('disabled', false);
                            $(".flat_rate").prop('disabled', true).val('');
                        } else {
                            $(".flat_rate_div").show();
                            $(".flat_rate").prop('disabled', false);
                            $(".burning_rate").prop('disabled', true).val('');
                        }
                    });

                    $('#layer-section').on('click', '.remove-layer-section', function() {
                        $(this).closest('.layer-sections').remove();
                    });

                    $('#add_rein_class').on('click', function() {
                        var $lastSection = $('.reinclass-section').last();

                        const prevCounter = parseInt($lastSection.attr('data-counter'))
                        const reinClassVal = $(`#treaty_reinclass-${prevCounter}`).val()
                        const prevSectionLabel = String.fromCharCode(65 + prevCounter)
                        if (reinClassVal == null || reinClassVal == '' || reinClassVal == ' ') {
                            toastr.error(`Please Select Reinsurance Class in Section ${prevSectionLabel}`,
                                'Incomplete data')
                            return false
                        }

                        var $newSection = $lastSection.clone(); // Clone the last section

                        // Remove select2-related elements
                        $newSection.find('.select2-container').remove();

                        // Increment data-counter attributes for the new section and its children
                        var counter = parseInt($lastSection.attr('data-counter')) + 1;
                        $newSection.attr('id', 'reinclass-section-' + counter);
                        $newSection.attr('data-counter', counter);
                        $newSection.find('[id]').each(function() {
                            var id = $(this).attr('id');
                            $(this).attr('id', id.replace(/-\d$/, '-' + counter));
                            $(this).attr('data-counter', counter);
                        });

                        let selectedReinClasses = []
                        $('.treaty_reinclass').each(function() {
                            const selectedVal = $(this).find('option:selected').val()

                            if (selectedVal != '') {
                                selectedReinClasses.push(selectedVal)
                            }
                        });

                        $newSection.find('.treaty_reinclass').attr('data-counter', counter)
                        $newSection.find('.comm-section').attr('id', `comm-section-${counter}`)

                        $newSection.find('.treaty_reinclass option').each(function() {
                            const val = $(this).val()
                            if (selectedReinClasses.indexOf(val) !== -1) {
                                $(this).remove();
                            }
                        })

                        // remove comm section and add afresh
                        $newSection.find('.comm-sections').remove()

                        // Update the section label (e.g., A, B, C, etc.)
                        const currentSectionLabel = String.fromCharCode(65 + counter); // A: 65
                        $newSection.find('.section-title').text('Section ' + currentSectionLabel);

                        // Reset input values in the new section
                        $newSection.find('input[type="text"], input[type="number"]').val('');

                        // Clear selected options in select elements
                        $newSection.find('select').val('').select2();

                        // Insert the new section after the last section
                        $lastSection.after($newSection);

                        appendCommSection(0, counter)
                    });

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

                    // Adding new item in a layer
                    $('#layer-section').on('change', '.limit_per_reinclass', function() {
                        // alert('in')
                        var lastLayerSection = $('#layer-section .layer-sections:last');
                        var counter = lastLayerSection.data('counter');
                        var itemcounter = 0;
                        var MethodVal = $('#method').val();
                        var limit_per_reinclass = $(`#limit_per_reinclass-${counter}-${itemcounter}`).val();
                        // Remove existing layer sections
                        $('[id^="layer-section-' + counter + '"]').remove();

                        // Add new layers based on the selected limit_per_reinclass value
                        if (limit_per_reinclass === 'Y') {
                            // Get the select element
                            var selectElement = document.getElementById("tnp_reinclass_code");

                            $('#layer-section').append(`
                        <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                            ${ counter !== 0 ? `<h6> Layer: ${counter + 1} </h6>` : '' }
                            <div class="row">
                                <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                    <label class="form-label required">Capture Limits per Class?</label>
                                    <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" data-counter="${counter}" id="limit_per_reinclass-${counter}-${itemcounter}" required>
                                        <option value="">Select Option</option>
                                        <option value="N">No</option>
                                        <option value="Y" selected>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `);

                            // Loop through each option in the selectElement
                            for (var i = 0; i < selectElement.options.length; i++) {
                                var option = selectElement.options[i];
                                if (option.selected) {
                                    var optionValue = option.value;
                                    var optionText = option.text;

                                    if (optionValue != null && optionValue != '') {
                                        $('#layer-section').append(`
                                    <div class="row layer-sections" id="layer-section-${counter}-${itemcounter}" data-counter="${counter}">
                                        <div class="col-sm-1 nonprop_reinclass">
                                            <label class="form-label required">Reinclass</label>
                                            <input type="hidden" class="form-control layer_no" data-counter="${counter}" id="layer_no-${counter}-${itemcounter}" name="layer_no[]" value="${counter + 1}" readonly>
                                            <input type="hidden" class="form-control nonprop_reinclass" data-counter="${counter}" id="nonprop_reinclass-${counter}-${itemcounter}" name="nonprop_reinclass[]" value="${optionValue}" readonly>
                                            <input type="text" class="form-control nonprop_reinclass_desc" data-counter="${counter}" id="nonprop_reinclass_desc-${counter}-${itemcounter}" name="nonprop_reinclass_desc[]" value="${optionText}" readonly>
                                        </div>
                                        <!-- Other inputs go here -->
                                         <!-- Indemnity -->
                                        <div class="col-sm-2 indemnity_treaty_limit_div">
                                            <label class="form-label required">Limit</label>
                                            <input type="text"
                                                class="form-inputs indemnity_treaty_limit"
                                                id="indemnity_treaty_limit-${counter}-${itemcounter}"
                                                name="indemnity_treaty_limit[]"
                                                onkeyup="this.value=numberWithCommas(this.value)">
                                        </div>


                                        <!-- Deductible Amount -->
                                        <div class="col-sm-2 underlying_limit_div tnp_section_div">
                                            <label class="form-label required">Deductible
                                                Amount</label>
                                            <input type="text"
                                                class="form-inputs underlying_limit tnp_section"
                                                aria-label="underlying_limit"
                                                id="underlying_limit-${counter}-${itemcounter}"
                                                name="underlying_limit[]"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                required>
                                        </div>

                                         <!-- EGNPI (Estimated Premium) -->
                                            <div class="col-sm-2 egnpi_div tnp_section_div">
                                                <label class="form-label required">EGNPI</label>
                                                <input type="text"
                                                    class="form-inputs egnpi tnp_section"
                                                    aria-label="egnpi"
                                                    id="egnpi-${counter}-${itemcounter}" name="egnpi[]"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    required>
                                            </div>

                                            <!-- Burning Cost - Minimum Rate -->
                                            <div class="col-sm-3 burning_rate_div tnp_section_div">
                                                <label class="form-label required">Burning Cost-Minimum
                                                    Rate(%)</label>
                                                <input type="text" name="min_bc_rate[]"
                                                    id="min_bc_rate-${counter}-${itemcounter}"
                                                    class="form-inputs burning_rate tnp_section"
                                                    required>
                                            </div>

                                            <!-- Maximum Rate -->
                                            <div class="col-sm-2 burning_rate_div tnp_section_div">
                                                <label class="form-label required">Maximum Rate:
                                                    (%)</label>
                                                <input type="text" name="max_bc_rate[]"
                                                    id="max_bc_rate-${counter}-${itemcounter}"
                                                    class="form-inputs burning_rate tnp_section"
                                                    required>
                                            </div>

                                            <!-- Flat Rate -->
                                            <div class="col-sm-2 flat_rate_div tnp_section_div">
                                                <label class="form-label required">For Flat Rate:
                                                    (%)</label>
                                                <input type="text" name="flat_rate[]"
                                                    id="flat_rate-${counter}-${itemcounter}"
                                                    class="form-inputs flat_rate tnp_section" required>
                                            </div>

                                            <!-- Upper Adjustable Annually Rate -->
                                            <div class="col-sm-3 burning_rate_div tnp_section_div">
                                                <label class="form-label required">Upper Adjust.
                                                    Annually Rate</label>
                                                <input type="text" name="upper_adj[]"
                                                    id="upper_adj-${counter}-${itemcounter}"
                                                    class="form-inputs burning_rate tnp_section"
                                                    required>
                                            </div>

                                            <!-- Lower Adjustable Annually Rate -->
                                            <div class="col-sm-3 burning_rate_div tnp_section_div">
                                                <label class="form-label required">Lower Adjust.
                                                    Annually Rate</label>
                                                <input type="text" name="lower_adj[]"
                                                    id="lower_adj-${counter}-${itemcounter}"
                                                    class="form-inputs burning_rate tnp_section"
                                                    required>
                                            </div>

                                            <!-- Minimum Premium Deposit -->
                                            <div class="col-sm-3 min_deposit_div tnp_section_div">
                                                <label class="form-label required">Minimum Deposit
                                                    Premium</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" name="min_deposit[]"
                                                        id="min_deposit-${counter}-${itemcounter}"
                                                        class="form-control min_deposit tnp_section"
                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                        required>
                                                </div>
                                            </div>

                                            <!-- Reinstatement Type -->
                                            <div
                                                class="col-sm-3 reinstatement_type_div tnp_section_div">
                                                <label class="form-label required">Reinstatement
                                                    Type</label>
                                                <div class="input-group mb-3">
                                                    <select name="reinstatement_type[]"
                                                        id="reinstatement_type-${counter}-${itemcounter}"
                                                        class="form-inputs select2" required>
                                                        <option value="">Please Select</option>
                                                        <option value="NOR">Number of Reinstatement
                                                        </option>
                                                        <option value="AAL">Annual Aggregate Limit
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Reinstatement Value -->
                                            <div
                                                class="col-sm-3 reinstatement_value_div tnp_section_div">
                                                <label class="form-label required">Reinstatement
                                                    Value</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" name="reinstatement_value[]"
                                                        id="reinstatement_value-${counter}-${itemcounter}"
                                                        class="form-control reinstatement_value tnp_section"
                                                        onkeyup="this.value=numberWithCommas(this.value)"
                                                        required>
                                                </div>
                                            </div>
                                            {{-- end of remove wen necessary --}}
                                            </div>

                                    
                                    </div>
                                `);

                                        $(".burning_rate_div").hide();
                                        $(".flat_rate_div").hide();

                                        if (MethodVal === 'B') {
                                            $(".burning_rate_div").show();
                                            $(".burning_rate").prop('disabled', false);
                                            $(".flat_rate").prop('disabled', true).val('');
                                        } else {
                                            $(".flat_rate_div").show();
                                            $(".flat_rate").prop('disabled', false);
                                            $(".burning_rate").prop('disabled', true).val('');
                                        }

                                        itemcounter++;
                                    }
                                }
                            }
                        } else {
                            $('#layer-section').append(`
                        <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                            ${ counter !== 0 ? `<h6> Layer: ${counter + 1} </h6>` : '' }
                            <div class="row">
                                <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                    <label class="form-label required">Capture Limits per Class?</label>
                                    <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" id="limit_per_reinclass-${counter}-0" value="N" required>
                                        <option value="">Select Option</option>
                                        <option value="N" selected>No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Other inputs go here -->
                             <!-- Indemnity -->
                                        <div class="col-sm-2 indemnity_treaty_limit_div">
                                            <label class="form-label required">Limit</label>
                                            <input type="text"
                                                class="form-inputs indemnity_treaty_limit"
                                                id="indemnity_treaty_limit-${counter}-${itemcounter}"
                                                name="indemnity_treaty_limit[]"
                                                onkeyup="this.value=numberWithCommas(this.value)">
                                        </div>


                                        <!-- Deductible Amount -->
                                        <div class="col-sm-2 underlying_limit_div tnp_section_div">
                                            <label class="form-label required">Deductible
                                                Amount</label>
                                            <input type="text"
                                                class="form-inputs underlying_limit tnp_section"
                                                aria-label="underlying_limit"
                                                id="underlying_limit-${counter}-${itemcounter}"
                                                name="underlying_limit[]"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                required>
                                        </div>

                                         <!-- EGNPI (Estimated Premium) -->
                                        <div class="col-sm-2 egnpi_div tnp_section_div">
                                            <label class="form-label required">EGNPI</label>
                                            <input type="text"
                                                class="form-inputs egnpi tnp_section"
                                                aria-label="egnpi"
                                                id="egnpi-${counter}-${itemcounter}" name="egnpi[]"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                required>
                                        </div>

                                        <!-- Burning Cost - Minimum Rate -->
                                        <div class="col-sm-3 burning_rate_div tnp_section_div">
                                            <label class="form-label required">Burning Cost-Minimum
                                                Rate(%)</label>
                                            <input type="text" name="min_bc_rate[]"
                                                id="min_bc_rate-${counter}-${itemcounter}"
                                                class="form-inputs burning_rate tnp_section"
                                                required>
                                        </div>

                                        <!-- Maximum Rate -->
                                        <div class="col-sm-2 burning_rate_div tnp_section_div">
                                            <label class="form-label required">Maximum Rate:
                                                (%)</label>
                                            <input type="text" name="max_bc_rate[]"
                                                id="max_bc_rate-${counter}-${itemcounter}"
                                                class="form-inputs burning_rate tnp_section"
                                                required>
                                        </div>

                                        <!-- Flat Rate -->
                                        <div class="col-sm-2 flat_rate_div tnp_section_div">
                                            <label class="form-label required">For Flat Rate:
                                                (%)</label>
                                            <input type="text" name="flat_rate[]"
                                                id="flat_rate-${counter}-${itemcounter}"
                                                class="form-inputs flat_rate tnp_section" required>
                                        </div>

                                        <!-- Upper Adjustable Annually Rate -->
                                        <div class="col-sm-3 burning_rate_div tnp_section_div">
                                            <label class="form-label required">Upper Adjust.
                                                Annually Rate</label>
                                            <input type="text" name="upper_adj[]"
                                                id="upper_adj-${counter}-${itemcounter}"
                                                class="form-inputs burning_rate tnp_section"
                                                required>
                                        </div>

                                        <!-- Lower Adjustable Annually Rate -->
                                        <div class="col-sm-3 burning_rate_div tnp_section_div">
                                            <label class="form-label required">Lower Adjust.
                                                Annually Rate</label>
                                            <input type="text" name="lower_adj[]"
                                                id="lower_adj-${counter}-${itemcounter}"
                                                class="form-inputs burning_rate tnp_section"
                                                required>
                                        </div>

                                        <!-- Minimum Premium Deposit -->
                                        <div class="col-sm-3 min_deposit_div tnp_section_div">
                                            <label class="form-label required">Minimum Deposit
                                                Premium</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="min_deposit[]"
                                                    id="min_deposit-${counter}-${itemcounter}"
                                                    class="form-control min_deposit tnp_section"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    required>
                                            </div>
                                        </div>

                                        <!-- Reinstatement Type -->
                                        <div
                                            class="col-sm-3 reinstatement_type_div tnp_section_div">
                                            <label class="form-label required">Reinstatement
                                                Type</label>
                                            <div class="input-group mb-3">
                                                <select name="reinstatement_type[]"
                                                    id="reinstatement_type-${counter}-${itemcounter}"
                                                    class="form-inputs select2" required>
                                                    <option value="">Please Select</option>
                                                    <option value="NOR">Number of Reinstatement
                                                    </option>
                                                    <option value="AAL">Annual Aggregate Limit
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Reinstatement Value -->
                                        <div
                                            class="col-sm-3 reinstatement_value_div tnp_section_div">
                                            <label class="form-label required">Reinstatement
                                                Value</label>
                                            <div class="input-group mb-3">
                                                <input type="text" name="reinstatement_value[]"
                                                    id="reinstatement_value-${counter}-${itemcounter}"
                                                    class="form-control reinstatement_value tnp_section"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    required>
                                            </div>
                                        </div>
                                        {{-- end of remove wen necessary --}}

                                    </div>
                        </div>
                    `);

                            $(".burning_rate_div").hide();
                            $(".flat_rate_div").hide();

                            if (MethodVal === 'B') {
                                $(".burning_rate_div").show();
                                $(".burning_rate").prop('disabled', false);
                                $(".flat_rate").prop('disabled', true).val('');
                            } else {
                                $(".flat_rate_div").show();
                                $(".flat_rate").prop('disabled', false);
                                $(".burning_rate").prop('disabled', true).val('');
                            }
                        }
                    });

                    $('#layer-section').on('click', '.remove-layer-section', function() {
                        $(this).closest('.layer-sections').remove();
                    });

                    $('#apply_eml').change(function(e) {
                        e.preventDefault();
                        const applyEML = $(this).val()

                        $('#eml_rate').hide();
                        $('#eml_amt').hide();
                        $('.eml-div').hide();
                        if (applyEML == 'Y') {
                            $('#eml_rate').show();
                            $('#eml_amt').show();
                            $('.eml-div').show();
                        }

                    });

                    $('#eml_rate').keyup(function(e) {
                        const emlRate = $(this).val()
                        const totalSumInsured = parseFloat(removeCommas($('#total_sum_insured').val()))
                        const emlAmt = totalSumInsured * (emlRate / 100)

                        $('#eml_amt').val(numberWithCommas(emlAmt));
                        $('#effective_sum_insured').val(numberWithCommas(emlAmt));
                    });

                    $('#total_sum_insured').keyup(function(e) {
                        const totalSumInsured = removeCommas($(this).val())
                        let effectiveSumInsured = totalSumInsured

                        const emlRate = $('#eml_rate').val()
                        const applyEml = $('#apply_eml').val()

                        if ((emlRate != null && emlRate != '' && applyEml == 'Y') && (totalSumInsured != null &&
                                totalSumInsured != '')) {
                            const emlAmt = effectiveSumInsured = parseFloat(totalSumInsured) * (parseFloat(
                                emlRate) / 100)
                            $('#eml_amt').val(numberWithCommas(emlAmt));
                        }

                        $('#effective_sum_insured').val(numberWithCommas(effectiveSumInsured));
                    });
                    $('#total_sum_insured').trigger('keyup')

                    $('#brokerage_comm_type').change(function(e) {
                        const brokerageCommType = $(this).val()
                        $('.brokerage_comm_amt_div').hide();
                        $('#brokerage_comm_amt').hide();
                        $('#brokerage_comm_rate').hide();
                        $('#brokerage_comm_rate_amt').hide();
                        $('#brokerage_comm_rate_label').hide();
                        $('#brokerage_comm_rate_amount_label').hide();
                        $('.brokerage_comm_rate_div').hide();
                        $('.brokerage_comm_rate_amt_div').hide();
                        $('#brokerage_comm_rate').val(null);
                        $('#brokerage_comm_amt').val(null);
                        $('#brokerage_comm_rate_amt').val(null);

                        if (brokerageCommType == 'R') {
                            $('.brokerage_comm_rate_div').show();
                            $('.brokerage_comm_rate_amt_div').show();
                            $('#brokerage_comm_rate').show();
                            $('#brokerage_comm_rate_amt').show()
                            $('#brokerage_comm_rate_label').show();
                            $('#brokerage_comm_rate_amount_label').show();

                            calculateBrokerageCommRate()
                        } else {
                            $('.brokerage_comm_amt_div').show();
                            $('#brokerage_comm_amt').show().prop('disabled', false);
                        }
                    });
                    $('#brokerage_comm_type').trigger('change')

                    function calculateBrokerageCommRate() {
                        let cedantCommRate = removeCommas($('#comm_rate').val())
                        let reinCommRate = removeCommas($('#reins_comm_rate').val())
                        let cedantPremium = removeCommas($('#cede_premium').val())
                        let brokerageCommRate = 0
                        let brokerage_comm_amt = 0

                        if (cedantCommRate != '' && cedantCommRate != null && reinCommRate != '' && reinCommRate !=
                            null) {
                            brokerageCommRate = parseFloat(reinCommRate) - parseFloat(cedantCommRate)
                        }
                        brokerageCommAmt = parseFloat(cedantPremium) * (parseFloat(brokerageCommRate) / 100)




                        $('#brokerage_comm_rate').val(brokerageCommRate);
                        $('#brokerage_comm_rate_amt').val(numberWithCommas(brokerageCommAmt));

                    }

                    $('#brokerage_comm_type').trigger('change')
                    $('#apply_eml').trigger('change')
                    $('#reins_comm_type').trigger('change')

                    function computateInstalment() {
                        var shareOffered = parseFloat($('#fac_share_offered').val().replace(/,/g, '')) || 0;
                        var rate = parseFloat($('#comm_rate').val().replace(/,/g, '')) || 0;
                        var cedantPremium = parseInt($('#cede_premium').val().replace(/,/g, '')) || 0;
                        var totalDr = parseFloat((shareOffered / 100) * cedantPremium).toFixed(2);
                        var totalCr = parseFloat((rate / 100) * totalDr);
                        return (totalDr - totalCr).toFixed(2);
                    }
                    {{-- Steve End --}}



                    $(document).on('input', '[id^="contact_name-"]', function() {
                        var query = $(this).val().trim();
                        var index = $(this).attr('id').split('-')[1]; // Extract the dynamic index
                        var resultsContainer = $('#full_name_results_' + index); // Target  results div

                        if (query.length < 1) {
                            resultsContainer.hide();
                            return;
                        }
                        $.ajax({
                            url: "{{ route('search-prospect-fullnames') }}",
                            method: 'GET',
                            data: {
                                q: query
                            },
                            success: function(data) {
                                // console.log(data);
                                var results = '';
                                if (data.length > 0) {
                                    data.forEach(function(item) {
                                        results += `<div class="dropdown-item fullname-option" data-id="${item.pipeline_id}" data-email="${item.email}"
                                data-phone="${item.phone}" data-telephone="${item.telephone}" data-contact_name="${item.contact_name}" data-index="${index}">
                                ${item.contact_name}
                            </div>`;

                                    });
                                } else {
                                    results = '<div class="dropdown-item">No results found</div>';
                                }


                                //   var resultsContainer = $('#full_name_results_0');  
                                resultsContainer.html(results).show();
                            },
                            error: function() {
                                resultsContainer.html(
                                        '<div class="dropdown-item">Error fetching data</div>')
                                    .show();
                            }
                        });
                    });
                    $(document).on('click', '.fullname-option', function() {
                        var selectedContact = $(this);
                        var index = selectedContact.data('index'); // Get index to target specific fields
                        var contactName = selectedContact.data('contact_name');
                        var email = selectedContact.data('email');
                        var phone = selectedContact.data('phone');
                        var telephone = selectedContact.data('telephone');
                        console.log(index);
                        // Populate fields based on index
                        $('#contact_name-' + index).val(contactName);
                        $('#email-' + index).val(email);
                        $('#phone_number-' + index).val(phone);
                        $('#telephone-' + index).val(telephone);

                        $('#full_name_results_' + index).hide();
                    });
                    // $('#contact_name-0').val(contactName);
                    // $('#email-0').val(email);
                    // $('#phone_number-0').val(phone);
                    // $('#telephone-0').val(telephone);

                    // $('#full_name_results_0').hide();
                    // });


                    //Insured Name Search
                    $('#insured_name').on('input', function() {

                        var query = $(this).val().trim();
                        if (query.length < 1) {
                            $('#insured_name_results').hide();
                            return;
                        }
                        $.ajax({
                            url: "{{ route('search-insured-names') }}",
                            method: 'GET',
                            data: {
                                q: query
                            },
                            success: function(data) {
                                var results = '';
                                if (data.length > 0) {
                                    data.forEach(function(item) {
                                        results +=
                                            `<div class="dropdown-item insured-option" data-id="${item.pipeline_id}">${item.insured_name}</div>`;
                                    });
                                } else {
                                    error = '<div class="dropdown-item">No results found</div>';
                                    $('#insured_name_error').html(error).show();
                                }
                                $('#insured_name_results').html(results).show();
                            },
                            error: function() {
                                $('#insured_name_results').html(
                                        '<div class="dropdown-item">Error fetching data</div>')
                                    .show();
                            }
                        });
                    });

                    $(document).on('click', '.insured-option', function() {
                        var selectedName = $(this).text();
                        $('#insured_name').val(selectedName); // Set the selected name
                        $('#insured_name_results').hide(); // Hide results
                    });



                    // lead_name search 
                    $('#lead_name').on('input', function() {
                        var query = $(this).val().trim();
                        if (query.length < 1) {
                            $('#lead_name_results').hide();
                            return;
                        }
                        $.ajax({
                            url: "{{ route('search-lead-names') }}",
                            method: 'GET',
                            data: {
                                q: query
                            },
                            success: function(data) {
                                var results = '';
                                if (data.length > 0) {
                                    data.forEach(function(item) {
                                        results +=
                                            `<div class="dropdown-item lead-option" data-id="${item.pipeline_id}">${item.lead_name}</div>`;
                                    });
                                } else {
                                    error = '<div class="dropdown-item">No results found</div>';
                                    $('#lead_name_error').html(error).show();

                                }
                                $('#lead_name_results').html(results).show();
                            },
                            error: function() {
                                $('#lead_name_results').html(
                                    '<div class="dropdown-item text-danger">Error fetching data</div>'
                                ).show();
                            }
                        });
                    });

                    // Click event to select a lead name
                    $(document).on('click', '.lead-option', function() {
                        $('#lead_name').val($(this).text());
                        $('#lead_name_results').hide();
                    });

                    //Populates the  Cover END DATE
                    $('#effective_date').on('change', function() {
                        var effectiveDate = $(this).val();

                        if (effectiveDate) {
                            var date = new Date(effectiveDate);
                            date.setFullYear(date.getFullYear() + 1);
                            date.setDate(date.getDate() - 1);
                            var closingDate = date.toISOString().split('T')[0]; // YYYY-MM-DD format
                            $('#closing_date').val(closingDate);
                        }
                    });






                    let ins_class = ''
                    let pq = ''

                    $('.pq_cost').hide()

                    // if (prospect != null && prospect != '' && prospect != undefined) {

                    //     $.ajax({
                    //         type: "GET",
                    //         data: {
                    //             'prospect': prospect
                    //         },
                    //         url: "{{ route('get_prospect_details') }}",
                    //         success: function(resp) {

                    //             pq = resp.prequalification;
                    //             pq_status = resp.pq_status;

                    //             if (pq == 'Y' && pq_status != 'W') {
                    //                 $('#process_pq').show()
                    //                 $('#sales_mngt').hide()
                    //                 $('.pq_cost').show();
                    //                 $('#prequalification').val(resp.prequalification).trigger('change');
                    //                 $('#prod_cost').val(resp.production_cost);
                    //                 $('#cost_currency').val(resp.prod_currency).trigger('change');
                    //             }
                    //             // $('#postal_address').val(resp.postal_address);
                    //             // $('#postal_code').val(resp.postal_code);
                    //             // $('#lead_year').val(resp.pip_year).trigger('change');
                    //             // $('#client_category').val(resp.client_category).trigger('change');
                    //             // $('#client_type').val(resp.client_type).trigger('change');
                    //             // $('#division').val(resp.division).trigger('change');
                    //             // $('#division').trigger('change');
                    //             // $('#insurance_class').val(resp.insurance_class).trigger('change');
                    //             // $('#currency').val(resp.currency).trigger('change');
                    //             // $('#engage_type').val(resp.engage_type).trigger('change');
                    //             // $('#lead_source').val(resp.lead_source).trigger('change');
                    //             // $('#lead_owner').val(resp.lead_owner).trigger('change');
                    //             // $('#lead_handler').val(resp.lead_handler).trigger('change');
                    //             // $('#industry').val(resp.industry).trigger('change').trigger('change');
                    //             // $('#premium').val(resp.premium);
                    //             // $('#income').val(resp.income).attr('readonly', true);
                    //             // $('#effective_date').val(resp.effective_date);
                    //             // $('#closing_date').val(resp.closing_date);
                    //             // $('#rating').val(resp.rating);
                    //             // $('#email').val(resp.email);
                    //             // $('#source_desc').val(resp.source_desc)
                    //             // $('#contact_name').val(resp.contact_name);
                    //             // $('#phone_number').val(parseInt(resp.phone));
                    //             // $('#physical_address').val(resp.physical_address);
                    //             // $('#full_name_input').val(resp.fullname);
                    //             // $('#contact_position').val(resp.contact_position);
                    //             // $('#country_code').val(resp.country_code);
                    //             // $('#narration').val(resp.narration);
                    //             // $('#town').val(resp.town);
                    //             // $('#telephone').val(resp.telephone);
                    //             // $('#alternative_contact_name').val(resp.alternate_contact);
                    //             // $('#alternative_email').val(resp.alternate_email);
                    //             // $('#alternative_phone_number').val(resp.alternate_phone);
                    //             // $('#alternative_contact_position').val(resp.alternate_position);

                    //             $('#sales_mngt').attr('data-prospect', resp.opportunity_id);


                    //         }
                    //     })

                    //     $('#prequalification_div').hide()
                    // }

                    $('#stage_cycle').on('change', function() {

                        if ($(this).val() != 'P') {
                            $('#locked_uws').hide()
                        } else {
                            $('#locked_uws').show()
                        }
                    })

                    $('#process_pq').on('click', function() {
                        $('#editStatusModal').modal('show')
                    })

                    $('#prequalification').on('change', function() {

                        if ($(this).val() == 'Y') {
                            $('.pq_cost').show()
                            $('#cost_currency').trigger('change')
                        } else {
                            $('.pq_cost').hide()
                            $('#cost_currency').val('')
                            $('#prod_cost').val('')
                        }
                    })



                    $('body').on('change', '.document_file', function() {
                        let file = $(this)[0].files[0];
                        let id = $(this).attr('id')
                        let id_length = id.length
                        let rowID = id.slice(13, id_length)


                        let formData = new FormData();
                        formData.append('doc', file);


                        $.ajax({
                            type: "POST",
                            data: formData,
                            url: "{{ route('doc_preview') }}",
                            contentType: false,
                            processData: false,
                            success: function(resp) {
                                $("#preview" + rowID).attr('data-file', resp);

                            }
                        })
                    })

                    $('body').on('click', '.preview', function(e) {
                        $('object').attr('data', '');
                        var doc = $(this).attr('data-file');

                        $('#doc_view').html('<iframe src="' + doc + '" width="100%" height="900"></iframe>');

                        $('#v_docs').modal('show');



                    });

                    $('body').on('click', '#addDoc', function() {
                        if (counter > 0) {
                            var document_title = $('#document_title' + counter).val()
                            var document_file = $('#document_file' + counter).val()
                        } else if (counter == 0) {
                            var document_title = $('#document_title0').val()
                            var document_file = $('#document_file0').val()
                        }
                        if (document_title == '' || document_file == '') {
                            Swal.fire({
                                icon: 'warning',
                                text: 'Please fill all details'
                            });
                        } else {
                            counter = +1;
                            $('#file_details').append(
                                `<div class="row row-margin mt-1">

                        <div class="col-6">
                            <div class="row">
                                <x-Input id="document_name${counter}" name="document_name[]" req=""
                                    inputLabel="Document Title" 
                                    placeholder="Enter document title"
                                    oninput='this.value=this.value.toUpperCase();'/>
                                
                            </div>
                        </div>

                        <div class="col-5">
                            <label for="document_file">File</label>
                            <div class="input-group">
                                <input type="file" name="document_file[]" id="document_file${counter}" class="form-control document_file" />
                                <button class="btn btn-danger remove_file" type="button"><i class="fa fa-minus"></i> </button>
                            </div>
                        </div>
                        
                        <div class="col-1" style="margin-top: 30px">
                            <i class="fa fa-eye preview" id="preview${counter}"> </i>
                        </div>
                    </div>`
                            );
                        }


                        $('input[type=radio]').change(function() {
                            $('input[type=radio]:checked').not(this).prop('checked', false)
                        })
                    });

                    $('#file_details').delegate('.remove_file', 'click', function() {
                        $(this).parent().parent().parent().remove();
                    });

                    $('#prequalification').trigger('change');

                    $('#organic_growth_div').hide();


                    $('#sales_mngt').on('click', function() {
                        let prospect = $('#prospectId').val()

                        Swal.fire({
                            title: "Warning!",
                            html: "Are You Sure You Want to add this prospect to Sales Management",
                            icon: "warning",
                            confirmButtonText: "Yes",
                            showCancelButton: true
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                $.ajax({
                                    type: 'POST',
                                    data: {
                                        'prospect': prospect
                                    },
                                    url: "{!! route('prospect.add.pipeline') !!}",
                                    success: function(response) {
                                        if (response.status == 1) {
                                            toastr.success(response.message, {
                                                timeOut: 5000
                                            });
                                        }
                                        window.location.href = `/leads_listing`;
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        Swal.fire({
                                            title: "Error",
                                            text: textStatus,
                                            icon: "error"
                                        });
                                    }
                                });
                            }
                        })
                    })

                    // Treaty Copied json
                    const trans_type = $('#trans_type').val();
                    var selected_reinclass = [];
                    const resetableTransTypes = ['NEW']
                    var installmentTotalAmount = 0;

                    $('#tnp_section').hide();
                    $('#tpr_section').hide();
                    $('#fac_section').hide();
                    $('#trt_common').hide();
                    $('#treaty_grp').hide();
                    $('#eml_rate').hide();
                    $('#eml_amt').hide();
                    $('.eml-div').hide();
                    $('.brokerage_comm_amt_div').hide();
                    $('.brokerage_comm_rate_div').hide();
                    $('.brokerage_comm_rate_amnt_div').hide();

                    const coverFromInput = document.getElementById('coverfrom');
                    const coverToInput = document.getElementById('coverto');
                    coverFromInput.addEventListener('change', checkDateValidity);
                    coverToInput.addEventListener('change', checkDateValidity);

                    function checkDateValidity() {
                        const coverFromDate = new Date(coverFromInput.value);
                        const coverToDate = new Date(coverToInput.value);

                        if (coverFromDate >= coverToDate) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Date Range',
                                text: 'Cover from date must be earlier than cover to date',
                            })
                            coverFromInput.value = null;
                            coverToInput.value = null;
                        }
                    }

                    $('#risk_details_content').on('paste', function(e) {
                        const clipboardData = (e.originalEvent || e).clipboardData;
                        const pastedText = clipboardData.getData('text/html');

                        if (pastedText) {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(pastedText, 'text/html');
                            const table = $(doc).find('table');
                            const currentText = $(this).val();

                            if (table.length) {
                                $("hidden_risk_details").val(table);
                            } else {
                                $("hidden_risk_details").val(currentText + pastedText);
                            }
                        }
                    });

                    $("form#register_cover").validate({
                        ignore: ":hidden",
                        rules: {
                            covertype: {
                                required: true
                            },
                            branchcode: {
                                required: true
                            },
                        },
                        messages: {
                            covertype: {
                                required: "Cover Type is required"
                            },
                            branchcode: {
                                required: "Branch is required"
                            },
                        },
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
                    })

                    $("form #save_cover").on('click', function(e) {
                        e.preventDefault()
                        if ($("form#register_cover").valid()) {
                            if ($("select#pay_method option:selected").attr('pay_method_desc') === 'I') {
                                if ($("#fac-installments-section").is(":empty")) {
                                    toastr.error("Please click `Add Installment`");
                                    return false;
                                }
                                $('#fac-installments-section').find('.fac-instalament-row').each(function(
                                    index) {
                                    const idx = index + 1
                                    const noInput = $(`input#instl_no_${idx}`);
                                    const dateInput = $(`input#instl_date_${idx}`);
                                    const amountInput = $(`input#instl_amnt_${idx}`);

                                    if (!dateInput.val()) {
                                        dateInput.attr('required', 'required');
                                        return false;
                                    } else {
                                        dateInput.removeAttr('required');
                                    }

                                    if (!noInput.val()) {
                                        noInput.attr('required', 'required');
                                        return false;
                                    } else {
                                        noInput.removeAttr('required');
                                    }

                                    if (!amountInput.val()) {
                                        amountInput.attr('required', 'required');
                                        return false;
                                    } else {
                                        amountInput.removeAttr('required');
                                    }
                                });

                                if (trans_type == 'EDIT') {
                                    var instalAmount = computateInstalment()
                                    const totalInstallments = parseInt($('#no_of_installments').val().replace(
                                            /,/g,
                                            '')) ||
                                        0;
                                    const totalFacAmount = parseFloat(instalAmount) || 0;
                                    const totalFacInstAmt = (totalFacAmount / totalInstallments).toFixed(2);
                                    installmentTotalAmount = totalFacAmount
                                }
                                var totalInstallment = 0;
                                $("#fac-installments-section input[name='installment_amt[]']").each(function() {
                                    const value = parseFloat($(this).val().replace(/,/g, ''));
                                    if (!isNaN(value)) {
                                        totalInstallment += value;
                                    }
                                });

                                if (!areDecimalsEqual(installmentTotalAmount, totalInstallment)) {
                                    toastr.error("The total installment amount does not match the FAC amount.");
                                    return false;
                                }
                            }

                            Swal.fire({
                                title: 'Are you sure?',
                                text: "Do you want to submit the form?",
                                icon: false,
                                showCancelButton: true,
                                confirmButtonText: 'Yes, submit',
                                cancelButtonText: 'No, cancel',
                                customClass: {
                                    confirmButton: 'custom-confirm',
                                    cancelButton: 'swal2-cancel'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#hidden_risk_details').val($('#risk_details_content').html());
                                    $("form#register_cover").submit();
                                } else {
                                    return false;
                                }
                            });
                        } else {
                            toastr.error("Please correct the errors before submitting.");
                        }
                    })

                    $("select#type_of_bus").change(function() {
                        var bustype = $("select#type_of_bus option:selected").attr('value');

                        if (bustype == 'FPR' || bustype == 'FNP') {
                            $('#fac_section').show();

                            $('#tpr_section').hide();
                            $('#tnp_section').hide();
                            $('#trt_common').hide();
                            $('#treaty_grp').hide();

                            processSections('.fac_section', '.fac_section_div', 'enable');
                            processSections('.reins_comm_rate', '.reins_comm_rate_div', 'disable');
                            processSections('.tpr_section', '.tpr_section_div', 'disable');
                            processSections('.tnp_section', '.tnp_section_div', 'disable');
                            processSections('.trt_common', '.trt_common_div', 'disable');
                            processSections('.treaty_grp', '.treaty_grp_div', 'disable');
                        } else if (bustype == 'TPR') {
                            $('#treaty_grp').show();
                            $('#trt_common').show();
                            $('#tpr_section').show();
                            $('#fac_section').hide();
                            $('#tnp_section').hide();

                            processSections('.trt_common', '.trt_common_div', 'enable');
                            processSections('.treaty_grp', '.treaty_grp_div', 'enable');
                            processSections('.tpr_section', '.tpr_section_div', 'enable');
                            processSections('.reinsurer_per_treaty', '.reinsurer_per_treaty_div', 'disable');
                            processSections('.fac_section', '.fac_section_div', 'disable');
                            processSections('.tnp_section', '.tnp_section_div', 'disable');



                        } else if (bustype == 'TNP') {
                            $('#treaty_grp').show();
                            $('#trt_common').show();
                            $('#tnp_section').show();
                            $('#tpr_section').hide();
                            $('#fac_section').hide();

                            processSections('.trt_common', '.trt_common_div', 'enable');
                            processSections('.treaty_grp', '.treaty_grp_div', 'enable');
                            processSections('.tnp_section', '.tnp_section_div', 'enable');
                            processSections('.tpr_section', '.tpr_section_div', 'disable');
                            processSections('.fac_section', '.fac_section_div', 'disable');


                        }

                        let selectedTreatyType = ''
                        @if (!empty($old_endt_trans))
                            selectedTreatyType = '{!! $old_endt_trans->treaty_type !!}'
                        @endif

                        $.ajax({
                            url: "{{ route('cover.get_treatyperbustype') }}",
                            data: {
                                "type_of_bus": bustype
                            },
                            type: "get",
                            success: function(resp) {
                                $(`#treatytype`).empty();

                                $(`#treatytype`).append($('<option>').text(
                                        '-- Select Treaty Type--')
                                    .attr('value', ''));
                                $.each(resp, function(i, value) {
                                    $(`#treatytype`).append($('<option>').text(value
                                            .treaty_code + " - " + value.treaty_name)
                                        .attr('value', value.treaty_code)
                                    );
                                });
                                $(`#treatytype option[value='${selectedTreatyType}']`).prop(
                                    'selected',
                                    true)
                                $(`#treatytype`).trigger('change.select2');
                            },
                            error: function(resp) {
                                console.error;
                            }
                        })
                    });

                    $('select#type_of_bus').trigger('change');

                    $("select#class_group").change(function() {
                        var class_group = $("select#class_group option:selected").attr('value');
                        $('#classcode').empty();
                        if ($(this).val() != '') {
                            $('#class').prop('disabled', false)
                            $.ajax({
                                url: "{{ route('get_class') }}",
                                data: {
                                    "class_group": class_group
                                },
                                type: "get",
                                success: function(resp) {
                                    $('#classcode').empty();
                                    var classes = resp ? JSON.parse(resp) : [];
                                    $('#classcode').append($('<option>').text(
                                            '-- Select Class Name--')
                                        .attr('value', ''));
                                    $.each(classes, function(i, value) {
                                        $('#classcode').append($('<option>').text(value
                                                .class_code + " - " + value.class_name)
                                            .attr('value', value.class_code)
                                        );
                                    });

                                    $('.section').trigger("chosen:updated");
                                },
                                error: function(resp) {
                                    console.error(resp);
                                }
                            })
                        }
                    });

                    $('select#covertype').trigger('change');

                    if ($("select#covertype option:selected").attr('covertype_desc') != 'B') {
                        $('#bindercoversec').hide();
                    }

                    /*** On change of cover Type ***/
                    $("select#covertype").change(function() {
                        // var binder = $("#covertype").val();
                        var binder = $("select#covertype option:selected").attr('covertype_desc')
                        $('#bindercoverno').empty();

                        if (binder == 'B') {
                            $('#bindercoversec').show();

                            $.ajax({
                                url: "{{ route('get_binder_covers') }}",
                                //data:{"branch":branch},
                                type: "get",
                                success: function(resp) {

                                    /*remove the choose branch option*/
                                    $('#bindercoverno').empty();
                                    var binders = $.parseJSON(resp);
                                    $('#bindercoverno').append($('<option>').text(
                                            'Select Binder Cover')
                                        .attr('value', ''));

                                    $.each(binders, function(i, value) {
                                        $('#bindercoverno').append($('<option>').text(value
                                            .binder_cov_no + "  -  " + value
                                            .agency_name
                                        ).attr('value', value.binder_cov_no));
                                    });

                                    $('.section').trigger("chosen:updated");
                                },
                                error: function(resp) {
                                    console.error;
                                }
                            })
                        } else if (binder == 'N') {
                            $('#bindercoverno').empty();
                            $('#bindercoversec').hide();
                            $('.section').trigger("chosen:updated");

                            $('#bindercoverno').prop('required', false);
                        }
                    });

                    /*** On change Pay Method ***/
                    $("select#pay_method").change(function() {
                        var pm = $("select#pay_method option:selected").attr('pay_method_desc')
                        $('#no_of_installments').empty();
                        if (trans_type === 'NEW') {
                            if (pm === 'I') {
                                $('#no_of_installments_sec').show();
                                $('#fac_installments_box').hide();
                                $('#no_of_installments').prop('required', true);
                                $('#no_of_installments').empty();
                                $('#no_of_installments').val();
                                $('#add_fac_inst_btn_section').show();
                            } else {
                                $('#no_of_installments').val(1);
                                $('#no_of_installments_sec').hide();
                                $('#fac_installments_box').hide();
                                $('#add_fac_inst_btn_section').hide();
                                $('#no_of_installments').prop('required', false);
                            }
                        } else {
                            if (pm === 'I') {
                                $('#fac_installments_box').show();
                                $('#edit_no_of_installments_sec').show();
                                $('#edit_fac_inst_btn_section').show();
                                $('#instalment_sec_div').show();
                            } else {
                                $('#edit_no_of_installments_sec').hide();
                                $('#edit_fac_inst_btn_section').hide();
                                $('#instalment_sec_div').hide();
                                $('#fac_installments_box').hide();
                            }
                        }
                    });

                    $('select#pay_method').trigger('change');

                    /*** On change Broker Flag ***/
                    $("select#broker_flag").change(function() {
                        var broker_flag = $("select#broker_flag option:selected").attr('value');
                        // $('#brokercode').empty();
                        if (broker_flag == 'Y') {
                            $('.brokercode_div').show();
                            $('#brokercode').prop('required', true);
                            $('#brokercode').prop('disabled', false);
                        } else {
                            $('#brokercode').val('');
                            $('.brokercode_div').hide();
                            $('#brokercode').prop('required', false);
                            $('#brokercode').prop('disabled', true);
                        }
                    });
                    $("select#broker_flag").trigger('change')

                    $('#add_fac_instalments').on('click', function() {
                        var noOfInstallments = $("#no_of_installments").val();
                        var businessType = $("#type_of_bus").val();
                        var cedantPremium = $("#cede_premium").val();
                        var facShareOffered = $("#fac_share_offered").val();
                        var commRate = $("#comm_rate").val();

                        if (Boolean(noOfInstallments === '')) {
                            toastr.error(`Please add Installments`, 'Incomplete data')
                            return false
                        } else if (Boolean(businessType === '')) {
                            toastr.error(`Please Select Business Type`, 'Incomplete data')
                            return false
                        } else if (Boolean(cedantPremium === '')) {
                            toastr.error(`Please add Cedant Premium`, 'Incomplete data')
                            return false
                        } else if (Boolean(facShareOffered === '')) {
                            toastr.error(`Please add Share Offered`, 'Incomplete data')
                            return false
                        } else if (Boolean(commRate === '')) {
                            toastr.error(`Please add Commission Rate`, 'Incomplete data')
                            return false
                        }

                        var instalAmount = computateInstalment()
                        // computation for cedant installment amount
                        $("#installment_total_amount").val(instalAmount);

                        $('#fac_installments_box').show();
                        // $('#no_of_installments').trigger('change');
                        $('#fac-installments-section').empty()
                        const totalInstallments = parseInt($('#no_of_installments').val().replace(/,/g, '')) ||
                            0;
                        var no_of_installments = parseInt($('#no_of_installments').val().replace(/,/g, '')) ||
                            0;
                        if (no_of_installments > 0) {
                            const totalAmount = instalAmount;
                            const totalFacAmount = parseFloat(totalAmount) || 0;
                            const totalFacInstAmt = (totalFacAmount / totalInstallments).toFixed(2);

                            installmentTotalAmount = totalFacAmount

                            if (totalInstallments <= 100) {
                                for (let i = 1; i <= totalInstallments; i++) {
                                    $('#fac-installments-section').append(`
                                <div class="row fac-instalament-row" data-count="${i}">
                                    <div class="col-md-3">
                                        <label class="form-label">Installment</label>
                                        <input type="hidden" name="installment_no[]" value="${i}" readonly class="form-inputs"/>
                                        <input type="hidden" name="installment_id[]" value=""/>
                                        <input type="text" value="installment No. ${i}" id="instl_no_${i}" readonly class="form-inputs" required/>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="instl_date_${i}">Installment Due Date</label>
                                        <input type="date" name="installment_date[]" id="instl_date_${i}"  class="form-inputs" required/>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="instl_amnt_${i}">Total Installment Amount</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="installment_amt[]" id="instl_amnt_${i}" value="${numberWithCommas(totalFacInstAmt)}" class="form-inputs form-input-group amount"  onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required/>
                                            <button class="btn btn-danger btn-sm remove-fac-instalment" type="button" id="remove-fac-instalment"><i class="bx bx-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            `);
                                }
                            }
                        }
                    });

                    $('#fac-installments-section').on('click', '.remove-fac-instalment', function() {
                        // const removedIndex = $(this).closest('.fac-instalament-row').data('count');
                        const currentInstallments = $('#no_of_installments').val();
                        const remaingInstalment = currentInstallments >= 1 ? parseInt(currentInstallments - 1) :
                            0;
                        if (remaingInstalment > 0) {
                            $('#no_of_installments').val(remaingInstalment);
                        } else {
                            $('#no_of_installments').val('');
                            $('#fac_installments_box').hide();
                        }
                        $('#no_of_installments').trigger('change');
                        $(this).closest('.fac-instalament-row').remove();
                    });

                    $('select#currency_code').trigger('change');
                    /*currency logic*/
                    $("select#currency_code").change(function() {
                        var selected_currency = $("select#currency_code option:selected").attr('value');
                        var selected_descr = $("select#currency_code option:selected").text();

                        //ajax to check for date
                        $.ajax({
                            url: "{{ route('get_todays_rate') }}",
                            data: {
                                'currency_code': selected_currency
                            },
                            type: "get",
                            success: function(resp) {
                                var status = $.parseJSON(resp);
                                // alert(status.valid);

                                if (status.valid == 2) {
                                    // alert('test');
                                    $('#today_currency').val(1);
                                    $('#today_currency').prop('readonly', true)
                                } else if (status.valid == 1) {
                                    // alert('test');
                                    //populate rate field
                                    $('#today_currency').val(status.rate);
                                    $('#today_currency').prop('readonly', true)
                                } else {
                                    $('#today_currency').prop('readonly', true)
                                    $('#today_currency').val('');
                                    alert('Currency rate for the day not yet set');
                                    // $.ajax({
                                    //     url:" {{ route('yesterdayRate') }}",
                                    //     data: {'currency_code':selected_currency},
                                    //     type: "GET",
                                    //     success: function(resp) {
                                    //         // alert(resp);
                                    //         if (resp ==  0) {
                                    //             $('#today_currency').prop('readonly', true)
                                    //             $('#today_currency').val('');
                                    //             $.notify({
                                    //                 title: "<strong>Today's currency rate not Set </strong><br>",
                                    //                 message: "Using yesterday's currency rate. Adjust currency rate to fit today's rate",
                                    //             },{
                                    //                 type: 'warning'
                                    //             });
                                    //         } else {
                                    //             var rate = resp.currency_rate
                                    //             $('#today_currency').val(rate);
                                    //             $('#today_currency').prop('readonly', true)
                                    //         }
                                    //     }
                                    // })
                                }
                            },
                            error: function(resp) {
                                //alert("Error");
                                console.error;
                            }
                        })
                    });

                    $("select#sum_insured_type").change(function() {
                        var label_txt = $("select#sum_insured_type option:selected").text();
                        $('#sum_insured_label').text("(" + label_txt + ")");
                    });

                    $("#comm_rate").keyup(function() {
                        var ratex = $(this).val() || 0;
                        var cede = parseFloat(removeCommas($('#cede_premium').val())) || 0;
                        var commAmount = (ratex / 100) * cede;
                        $('#comm_amt').val(numberWithCommas(commAmount.toFixed(2)));
                        calculateBrokerageCommRate()
                    });

                    $("#reins_comm_rate").keyup(function() {
                        var ratex = $(this).val() || 0;
                        var cede = parseFloat(removeCommas($('#rein_premium').val())) || 0;
                        var commAmount = (ratex / 100) * cede;
                        $('#reins_comm_amt').val(numberWithCommas(commAmount.toFixed(2)));
                        calculateBrokerageCommRate()
                    });

                    $("#reins_comm_type").change(function() {
                        var comm_type = $(this).val();
                        // console.log('comm_type:' + comm_type);
                        if (comm_type == 'R') {
                            processSections('.reins_comm_rate', '.reins_comm_rate_div', 'enable');
                            $('#reins_comm_amt').prop('readonly', true)
                        } else {
                            processSections('.reins_comm_rate', '.reins_comm_rate_div', 'disable');
                            $('#reins_comm_amt').prop('readonly', false)
                        }
                        resetableTransTypes.includes(trans_type) ? $('#reins_comm_amt').val('') : null;

                    });

                    $("#reins_comm_type").trigger('change');

                    $("#cede_premium").keyup(function() {
                        $("#comm_rate").trigger('keyup');
                        $("#rein_premium").val($(this).val());
                    });

                    $("#rein_premium").keyup(function() {
                        $("#reins_comm_rate").trigger('keyup');
                    });

                    $(document).on('change', ".treaty_reinclass", function() {
                        const treatyType = $(`#treatytype`).val();
                        let counter = $(this).data('counter')
                        const reinclass = $(`#treaty_reinclass-${counter}`).val()

                        if (treatyType == null || treatyType == '' || treatyType == ' ') {

                            $(`#treaty_reinclass-${counter} option:selected`).removeAttr('selected');
                            $(`#treaty_reinclass-${counter}`).val(null)
                            toastr.error('Please Select Treaty Type First', 'Incomplete data')
                            //
                            return false
                        }

                        const premTypeCodeSelect = $(`#prem_type_code-${counter}-0`);
                        premTypeCodeSelect.attr('data-reinclass', reinclass);

                        $(`#prem_type_reinclass-${counter}-0`).val(reinclass);
                        $(`#treaty_grp #prem_type_treaty-${counter}-0`).trigger('change')


                    });

                    $(document).on('change', ".prem_type_code", function() {
                        let prem_type_code = $(this).val();
                        let classcounter = $(this).data('class-counter')
                        let premtypecounter = $(this).data('counter')
                        let treaty = $(`#prem_type_treaty-${classcounter}-${premtypecounter}`).val();
                        let reinclass = $(`#treaty_reinclass-${classcounter}`).val()

                        $(`#prem_type_reinclass-${classcounter}-${premtypecounter}`).val(reinclass);
                        // console.log('log',$(`#prem_type_reinclass-${classcounter}-${premtypecounter}`).val());
                        const premTypeCodeSelect = $(`#prem_type_code-${classcounter}-${premtypecounter}`);
                        premTypeCodeSelect.attr('data-reinclass', reinclass);
                        premTypeCodeSelect.attr('data-treaty', treaty);

                    });

                    if (trans_type == 'EDIT') {
                        $('.prem_type_code').each(function() {
                            $(this).trigger('change')
                        });
                    }

                    $(document).on('change', ".prem_type_treaty", function() {
                        let treaty = $(this).val();
                        let classcounter = $(this).data('class-counter')
                        let premtypecounter = $(this).data('counter')
                        let reinclass = $(`#treaty_reinclass-${classcounter}`).val()
                        // console.log('treaty:' + treaty + ' reinclass:' + reinclass + ' classcounter:' +
                        //     classcounter + ' premcounter:' + premtypecounter);

                        fetchPremTypes(treaty, premtypecounter, classcounter)
                    });

                    function fetchPremTypes(treaty, premCounter, classCounter) {
                        let selectedPremTypes = []
                        const classElem = $(`#treaty_reinclass-${classCounter}`)
                        const reinClass = classElem.val()
                        // $('.prem_type_code[data-reinclass="' + reinClass + '"]').each(function () {
                        $('.prem_type_code[data-reinclass="' + reinClass + '"][data-treaty="' + treaty + '"]').each(
                            function() {
                                const selectedVal = $(this).find('option:selected').val()
                                if (selectedVal != null && selectedVal != '') {
                                    selectedPremTypes.push(selectedVal)
                                }
                            })

                        if (classElem.val() != '') {
                            $(`#prem_type_code-${classCounter}-${premCounter}`).prop('disabled', false)

                            $.ajax({
                                url: "{{ route('cover.get_reinprem_type') }}",
                                data: {
                                    "reinclass": reinClass,
                                    'selectedCodes': selectedPremTypes
                                },
                                type: "get",
                                success: function(resp) {

                                    $(`#prem_type_reinclass-${classCounter}`).val(reinClass);
                                    /*remove the choose branch option*/
                                    $(`#prem_type_code-${classCounter}-${premCounter}`).empty();

                                    $(`#prem_type_code-${classCounter}-${premCounter}`).append($(
                                            '<option>')
                                        .text('-- Select Premium Type--').attr('value', ''));
                                    $.each(resp, function(i, value) {
                                        $(`#prem_type_code-${classCounter}-${premCounter}`)
                                            .append($(
                                                    '<option>').text(value.premtype_code +
                                                    " - " + value
                                                    .premtype_name)
                                                .attr('value', value.premtype_code)
                                                .attr('data-reinclass', reinClass)
                                                .attr('data-treaty', treaty)
                                            );


                                    });
                                    $(`#prem_type_code-${classCounter}-${premCounter}`).trigger(
                                        'change.select2');
                                },
                                error: function(resp) {
                                    console.error;
                                }
                            })
                        }
                    }

                    $(document).on('click', '.add-comm-section', function() {
                        const addSectCounter = $(this).data('counter')

                        const lastCommSection = $(`#comm-section-${addSectCounter}`).find(
                            '.comm-sections:last');

                        const prevCounter = lastCommSection.data('counter')
                        const classCounter = lastCommSection.data('class-counter')
                        const reinClassVal = $(`#treaty_reinclass-${classCounter}`).val()
                        const premTypeVal = $(`#prem_type_code-${classCounter}-${prevCounter}`).val()
                        const premTypeComm = $(`#prem_type_comm_rate-${classCounter}-${prevCounter}`).val()
                        if (reinClassVal == null || reinClassVal == '' || reinClassVal == ' ') {
                            toastr.error('Please Select Reinsurance Class', 'Incomplete data')
                            return false
                        } else if (premTypeVal == null || premTypeVal == '' || premTypeVal == ' ') {
                            toastr.error('Please Select Premium Type', 'Incomplete data')
                            return false
                        } else if (premTypeComm == null || premTypeComm == '' || premTypeComm == ' ') {
                            toastr.error('Input Commission Rate', 'Incomplete data')
                            return false
                        }

                        // Increment the counter
                        let counter = prevCounter + 1;

                        appendCommSection(counter, classCounter)
                        // fetchPremTypes(counter,classCounter)

                        // $(document).find(`#prem_type_code-${classCounter}-${counter}`).select2();

                    });

                    $(document).on('click', '.remove-comm-section', function() {
                        $(this).closest('.comm-sections').remove();
                    });

                    function appendCommSection(premCounter, classCounter) {
                        const reinClassVal = $(`#treaty_reinclass-${classCounter}`).val()
                        const treatytype = $('#treatytype').val();

                        var btn_class = ''
                        var btn_id = ''
                        var fa_class = ''
                        if (premCounter == 0) {
                            btn_class = 'btn-primary add-comm-section'
                            btn_id = 'add-comm-section'
                            fa_class = 'bx-plus'
                        } else {
                            btn_class = 'btn-danger remove-comm-section'
                            btn_id = 'remove-comm-section'
                            fa_class = 'bx-minus'
                        }

                        $(document).find(`#comm-section-${classCounter}`).append(`
                    <div class="row comm-sections" id="comm-section-${classCounter}-${premCounter}" data-class-counter="${classCounter}" data-counter="${premCounter}">
                        <!-- prem_type_treaty -->
                        <div class="col-sm-3 prem_type_treaty_div">
                            <label class="form-label required">Treaty</label>
                            <select class="form-inputs select2 prem_type_treaty" name="prem_type_treaty[]" id="prem_type_treaty-${classCounter}-${premCounter}" data-class-counter="${classCounter}" data-counter="${premCounter}" required>
                                <option value=""> Select Treaty </option>
                                <option value="SURP"> SURPLUS </option>
                                <option value="QUOT"> QUOTA </option>
                            </select>
                        </div>
                        <!-- reinsurance premium types -->
                        <div class="col-sm-3">
                            <label class="form-label required">Premium Type</label>
                            <input type="hidden" class="form-inputs prem_type_reinclass" id="prem_type_reinclass-${classCounter}-${premCounter}" name="prem_type_reinclass[]" data-counter="${premCounter}" value="${reinClassVal}">

                            <select class="form-inputs select2 prem_type_code" name="prem_type_code[]" id="prem_type_code-${classCounter}-${premCounter}" data-reinclass="${reinClassVal}" data-treaty="" data-class-counter="${classCounter}" data-counter="${premCounter}" required>
                                <option value="">--Select Premium Type--</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label required">Commision(%)</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-inputs" name="prem_type_comm_rate[]" id="prem_type_comm_rate-${classCounter}-${premCounter}" data-counter="${premCounter}" required>
                                <button class="btn ${btn_class}" type="button" id="${btn_id}"><i class="bx ${fa_class}"></i></button>
                            </div>
                        </div>
                    </div>
                `);

                        $(`#prem_type_treaty-${classCounter}-${premCounter}`).empty();

                        // console.log('SPQT' + treatytype);
                        if (treatytype == 'SPQT') {
                            $(`.prem_type_treaty_div`).show();
                            $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text(
                                'SURPLUS AND QUOTA').attr('value', 'SPQT')).change();
                        } else if (treatytype == 'QUOT') {
                            $(`.prem_type_treaty_div`).show();
                            $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text('QUOTA')
                                .attr(
                                    'value', 'QUOT')).change();
                        } else if (treatytype == 'SURP') {
                            $(`.prem_type_treaty_div`).show();
                            $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text(
                                'SURPLUS').attr(
                                'value', 'SURP')).change();
                        }

                        $(`#treaty_grp #prem_type_treaty-${classCounter}-${premCounter}`).trigger('change');
                    }

                    $("#coverfrom").change(function() {
                        let start_date = moment($(this).val());
                        let end_date = start_date.add(1, 'years').subtract(1, 'days');
                        $("#coverto").val(end_date.format('YYYY-MM-DD'));
                    });

                    $("#method").change(function() {
                        const MethodVal = $(`#method`).val();

                        $(".burning_rate").prop('disabled', true).val('');
                        $(".flat_rate").prop('disabled', true).val('');
                        $(".burning_rate_div").hide();
                        $(".flat_rate_div").hide();

                        if (MethodVal == 'B') {
                            $(".burning_rate_div").show();
                            $(".burning_rate").prop('disabled', false);
                        } else {
                            $(".flat_rate_div").show();
                            $(".flat_rate").prop('disabled', false);
                        }

                    });

                    $("#treatytype").change(function() {
                        let treatytype = $(this).val();
                        $(`#prem_type_treaty-0-0`).empty();

                        if (treatytype == 'SURP') {

                            $('.reinsurer_per_treaty_div').hide();
                            $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                            $('.prem_type_treaty_div').show();
                            $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS').attr('value',
                                    'SURP'))
                                .change();

                            $('.no_of_lines_div').show();
                            $('.no_of_lines').prop('disabled', false).val(null);

                            $('.surp_retention_amt_div').show();
                            $('.surp_retention_amt').prop('disabled', false).val(null);

                            $('.surp_treaty_limit_div').show();
                            $('.surp_treaty_limit').prop('disabled', false).val(null);

                            $('.surp_header_div').show();

                            $('.quota_share_total_limit_div').hide();
                            $('.quota_share_total_limit').prop('disabled', true).val(null);

                            $('.retention_per_div').hide();
                            $('.retention_per').prop('disabled', true).val(null);

                            $('.treaty_reice_div').hide();
                            $('.treaty_reice').prop('disabled', true).val(null);

                            $('.quota_retention_amt_div').hide();
                            $('.quota_retention_amt').prop('disabled', true).val(null);

                            $('.quota_treaty_limit_div').hide();
                            $('.quota_treaty_limit').prop('disabled', true).val(null);

                            $('.quota_header_div').hide();

                        } else if (treatytype == 'QUOT') {

                            $('.reinsurer_per_treaty_div').hide();
                            $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                            $('.prem_type_treaty_div').show();
                            $(`#prem_type_treaty-0-0`).append($('<option>').text('QUOTA').attr('value', 'QUOT'))
                                .change();

                            $('.quota_share_total_limit_div').show();
                            $('.quota_share_total_limit').prop('disabled', false).val(null);

                            $('.retention_per_div').show();
                            $('.retention_per').prop('disabled', false).val(null);

                            $('.treaty_reice_div').show();
                            $('.treaty_reice').prop('disabled', false).val(null);

                            $('.quota_retention_amt_div').show();
                            $('.quota_retention_amt').prop('disabled', false).val(null);

                            $('.quota_treaty_limit_div').show();
                            $('.quota_treaty_limit').prop('disabled', false).val(null);

                            $('.quota_header_div').show();
                            //
                            $('.no_of_lines_div').hide();
                            $('.no_of_lines').prop('disabled', true).val(null);

                            $('.surp_retention_amt_div').hide();
                            $('.surp_retention_amt').prop('disabled', true).val(null);

                            $('.surp_treaty_limit_div').hide();
                            $('.surp_treaty_limit').prop('disabled', true).val(null);

                            $('.surp_header_div').hide();
                        } else if (treatytype == 'SPQT') {

                            $('.reinsurer_per_treaty_div').show();
                            $('.reinsurer_per_treaty').prop('disabled', false).val(null);

                            $('.prem_type_treaty_div').show();
                            $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS AND QUOTA').attr(
                                'value',
                                'SPQT')).change();

                            $('.quota_share_total_limit_div').show();
                            $('.quota_share_total_limit').prop('disabled', false).val(null);

                            $('.retention_per_div').show();
                            $('.retention_per').prop('disabled', false).val(null);

                            $('.quota_retention_amt_div').show();
                            $('.quota_retention_amt').prop('disabled', false).val(null);

                            $('.quota_treaty_limit_div').show();
                            $('.quota_treaty_limit').prop('disabled', false).val(null);

                            $('.treaty_reice_div').show();
                            $('.treaty_reice').prop('disabled', false).val(null);

                            $('.no_of_lines_div').show();
                            $('.no_of_lines').prop('disabled', false).val(null);

                            $('.surp_retention_amt_div').show();
                            $('.surp_retention_amt').prop('disabled', false).val(null);

                            $('.surp_treaty_limit_div').show();
                            $('.surp_treaty_limit').prop('disabled', false).val(null);

                            $('.surp_header_div').show();
                            $('.quota_header_div').show();
                        } else {

                            $('.reinsurer_per_treaty_div').hide();
                            $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                            $('.prem_type_treaty_div').show();
                            $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS').attr('value',
                                    'SURP'))
                                .change();

                            $('.no_of_lines_div').hide();
                            $('.no_of_lines').prop('disabled', true).val(null);

                            $('.surp_retention_amt_div').hide();
                            $('.surp_retention_amt').prop('disabled', true).val(null);

                            $('.surp_treaty_limit_div').hide();
                            $('.surp_treaty_limit').prop('disabled', true).val(null);

                            $('.surp_header_div').hide();

                            $('.quota_share_total_limit_div').hide();
                            $('.quota_share_total_limit').prop('disabled', true).val(null);

                            $('.retention_per_div').hide();
                            $('.retention_per').prop('disabled', true).val(null);

                            $('.treaty_reice_div').hide();
                            $('.treaty_reice').prop('disabled', true).val(null);

                            $('.quota_retention_amt_div').hide();
                            $('.quota_retention_amt').prop('disabled', true).val(null);

                            $('.quota_treaty_limit_div').hide();
                            $('.quota_treaty_limit').prop('disabled', true).val(null);

                            $('.quota_header_div').hide();

                        }

                    });

                    $(document).on('keyup', ".no_of_lines", function() {
                        let reinclass_counter = $(`.treaty_reinclass`).data('counter')
                        var lines = $(this).val() || 0;
                        var counter = $(this).data('counter');
                        var ret = parseFloat(removeCommas($(`#surp_retention_amt-${counter}`).val())) || 0;
                        var trt_limit = lines * ret;
                        $(`#surp_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
                    });

                    $(document).on('keyup', ".retention_per", function() {
                        var ret_per = $(this).val() || 0;
                        var counter = $(this).data('counter');
                        var quota_limit_total = parseFloat(removeCommas($(`#quota_share_total_limit-${counter}`)
                            .val())) || 0;
                        var trt_per = 100 - ret_per;
                        var ret_amt = (ret_per / 100) * quota_limit_total;
                        var trt_limit = (trt_per / 100) * quota_limit_total;

                        $(`#treaty_reice-${counter}`).val(trt_per);
                        $(`#quota_retention_amt-${counter}`).val(numberWithCommas(ret_amt));
                        $(`#quota_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
                    });

                    $(document).on('keyup', ".quota_share_total_limit", function() {
                        var ret_per = $(`#treaty_reice-${counter}`).val() || 0;
                        var counter = $(this).data('counter');
                        var quota_limit_total = parseFloat(removeCommas($(`#quota_share_total_limit-${counter}`)
                            .val())) || 0;
                        var trt_per = 100 - ret_per;
                        var ret_amt = (ret_per / 100) * quota_limit_total;
                        var trt_limit = (trt_per / 100) * quota_limit_total;

                        $(`#treaty_reice-${counter}`).val(trt_per);
                        $(`#quota_retention_amt-${counter}`).val(numberWithCommas(ret_amt));
                        $(`#quota_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
                    });

                    // Adding new layer
                    $('#layer-section').on('click', '#add-layer-section', function() {
                        const lastLayerSection = $('#layer-section .layer-sections:last');
                        const MethodVal = $('#method').val();
                        const prevCounter = lastLayerSection.data('counter');
                        const IndemnityTreatyLimit = $(`#indemnity_treaty_limit-${prevCounter}-0`).val();
                        const UnderlyingLimit = $(`#underlying_limit-${prevCounter}-0`).val();
                        const EgnpiVal = $(`#egnpi-${prevCounter}-0`).val();
                        const MinBcRate = $(`#min_bc_rate-${prevCounter}-0`).val();
                        const MaxBcRate = $(`#max_bc_rate-${prevCounter}-0`).val();
                        const FlatRate = $(`#flat_rate-${prevCounter}-0`).val();
                        const UpperAdj = $(`#upper_adj-${prevCounter}-0`).val();
                        const LowerAdj = $(`#lower_adj-${prevCounter}-0`).val();
                        const MinDeposit = $(`#min_deposit-${prevCounter}-0`).val();
                        const limit_per_reinclass = $(`#limit_per_reinclass-${prevCounter}-0`).val();

                        // Validation
                        if (!IndemnityTreatyLimit.trim()) {
                            toastr.error('Please Capture Treaty Limit', 'Incomplete data');
                            return false;
                        } else if (!UnderlyingLimit.trim()) {
                            toastr.error('Please Capture Deductive', 'Incomplete data');
                            return false;
                        } else if (!EgnpiVal) {
                            toastr.error('Please Capture EGNPI', 'Incomplete data');
                            return false;
                        } else if (!MinBcRate.trim() && MethodVal === 'B') {
                            toastr.error('Input Minimum Burning Cost Rate', 'Incomplete data');
                            return false;
                        } else if (!MaxBcRate.trim() && MethodVal === 'B') {
                            toastr.error('Input Maximum Burning Cost Rate', 'Incomplete data');
                            return false;
                        } else if (!FlatRate.trim() && MethodVal === 'F') {
                            toastr.error('Input Flat Rate', 'Incomplete data');
                            return false;
                        } else if (!UpperAdj.trim() && MethodVal === 'B') {
                            toastr.error('Please Capture Upper Adjustment Rate', 'Incomplete data');
                            return false;
                        } else if (!LowerAdj.trim() && MethodVal === 'B') {
                            toastr.error('Please Capture Lower Adjustment Rate', 'Incomplete data');
                            return false;
                        } else if (!MinDeposit.trim()) {
                            toastr.error('Please Confirm Minimum Deposit Premium(MDP) Amount',
                                'Incomplete data');
                            return false;
                        }

                        if (limit_per_reinclass === 'Y') {
                            const IndemnityTreatyLimit = $(`#indemnity_treaty_limit-${prevCounter}-1`).val();
                            const UnderlyingLimit = $(`#underlying_limit-${prevCounter}-1`).val();
                            const EgnpiVal = $(`#egnpi-${prevCounter}-1`).val();
                            const MinBcRate = $(`#min_bc_rate-${prevCounter}-1`).val();
                            const MaxBcRate = $(`#max_bc_rate-${prevCounter}-1`).val();
                            const FlatRate = $(`#flat_rate-${prevCounter}-1`).val();
                            const UpperAdj = $(`#upper_adj-${prevCounter}-1`).val();
                            const LowerAdj = $(`#lower_adj-${prevCounter}-1`).val();
                            const MinDeposit = $(`#min_deposit-${prevCounter}-1`).val();

                            if (!IndemnityTreatyLimit.trim()) {
                                toastr.error('Please Capture Treaty Limit', 'Incomplete data');
                                return false;
                            } else if (!UnderlyingLimit.trim()) {
                                toastr.error('Please Capture Deductive', 'Incomplete data');
                                return false;
                            } else if (!EgnpiVal) {
                                toastr.error('Please Capture EGNPI', 'Incomplete data');
                                return false;
                            } else if (!MinBcRate.trim() && MethodVal === 'B') {
                                toastr.error('Input Minimum Burning Cost Rate', 'Incomplete data');
                                return false;
                            } else if (!MaxBcRate.trim() && MethodVal === 'B') {
                                toastr.error('Input Maximum Burning Cost Rate', 'Incomplete data');
                                return false;
                            } else if (!FlatRate.trim() && MethodVal === 'F') {
                                toastr.error('Input Flat Rate', 'Incomplete data');
                                return false;
                            } else if (!UpperAdj.trim() && MethodVal === 'B') {
                                toastr.error('Please Capture Upper Adjustment Rate', 'Incomplete data');
                                return false;
                            } else if (!LowerAdj.trim() && MethodVal === 'B') {
                                toastr.error('Please Capture Lower Adjustment Rate', 'Incomplete data');
                                return false;
                            } else if (!MinDeposit.trim()) {
                                toastr.error('Please Confirm Minimum Deposit Premium(MDP) Amount',
                                    'Incomplete data');
                                return false;
                            }
                        }

                        // Increment the counter
                        let counter = prevCounter + 1;
                        $('#layer-section').append(`
                    <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                        <h6> Layer: ${counter+1} </h6>
                        <div class="row">
                            <!--Flag to show if layers are per class-->
                            <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                <label class="form-label required">Capture Limits per Class ?</label>
                                <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" id="limit_per_reinclass-${counter}-0" value="N" required>
                                    <option value=""> Select Option </option>
                                    <option value="N" selected> No </option>
                                    <option value="Y"> Yes </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1 nonprop_reinclass">
                            <label class="form-label required">Reinclass</label>
                            <input type="hidden" class="form-control layer_no" aria-label="layer_no" data-counter="${counter}" id="layer_no-${counter}-0" name="layer_no[]" value="${counter + 1}" readonly>
                            <input type="hidden" class="form-control nonprop_reinclass" aria-label="nonprop_reinclass" data-counter="${counter}" id="nonprop_reinclass-${counter}-0" name="nonprop_reinclass[]" value="ALL" readonly>
                            <input type="text" class="form-control nonprop_reinclass_desc" aria-label="nonprop_reinclass_desc" data-counter="${counter}" id="nonprop_reinclass_desc-${counter}-0" name="nonprop_reinclass_desc[]" value="ALL" readonly>
                        </div>
                        <!--Indemnity-->
                        <div class="col-sm-2">
                            <label class="form-label required">Limit</label>
                            <input type="text" class="form-inputs" aria-label="indemnity_limit" id="indemnity_treaty_limit-${counter}-0" name="indemnity_treaty_limit[]" onkeyup="this.value=numberWithCommas(this.value)">
                        </div>

                        <!--Underlying Limit-->
                        <div class="col-sm-2">
                            <label class="form-label required">Deductible Amount</label>
                            <input type="text" class="form-inputs" aria-label="underlying_limit" id="underlying_limit-${counter}-0" name="underlying_limit[]" onkeyup="this.value=numberWithCommas(this.value)" >
                        </div>

                        <!--EGNPI (Estimated Premium)-->
                        <div class="col-sm-2">
                            <label class="form-label required">EGNPI</label>
                            <input type="text" class="form-inputs" aria-label="egnpi" id="egnpi-${counter}-0" name="egnpi[]" onkeyup="this.value=numberWithCommas(this.value)" >
                        </div>

                        <!--For Burning Cost (B) --- Minimum Rate: (%)-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Burning Cost-Minimum Rate(%)</label>
                            <input type="text" name="min_bc_rate[]" id="min_bc_rate-${counter}-0" class="form-inputs burning_rate" value="{{ old('min_bc_rate') }}">
                        </div>

                        <!--Maximum Rate: (%)-->
                        <div class="col-sm-2 burning_rate_div">
                            <label class="form-label required">Maximum Rate: (%)</label>
                            <input type="text" name="max_bc_rate[]" id="max_bc_rate-${counter}-0" class="form-inputs burning_rate" value="{{ old('max_bc_rate') }}">
                        </div>

                        <!--For Flat Rate: (%)-->
                        <div class="col-sm-2 flat_rate_div">
                            <label class="form-label required">For Flat Rate: (%)</label>
                            <input type="text" name="flat_rate[]" id="flat_rate-${counter}-0" class="form-inputs flat_rate" value="{{ old('applied_rate') }}">
                        </div>

                        <!--Adjustable Annually Rate-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Upper Adjust. Annually Rate</label>
                            <input type="text" name="upper_adj[]" id="upper_adj-${counter}-0" class="form-inputs burning_rate" value="{{ old('upper_adj') }}">
                        </div>

                        <!--Adjustable Annually Rate-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Lower Adjust. Annually Rate</label>
                            <input type="text" name="lower_adj[]" id="lower_adj-${counter}-0" class="form-inputs burning_rate" value="{{ old('lower_adj') }}">
                        </div>

                        <!--Minimum Deposit Premium -->
                        <div class="col-sm-3">
                            <label class="form-label required">Minimum Deposit Premium </label>
                            <div class="input-group mb-3">
                                <input type="text" name="min_deposit[]" id="min_deposit-${counter}-0" class="form-control" value="{{ old('min_deposit') }}" onkeyup="this.value=numberWithCommas(this.value)">
                                <button class="btn btn-danger remove-layer-section" type="button" id="remove-layer-section"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>

                        {{-- Reinstatement Type Arrangement --}}
                        <div class="col-sm-3 reinstatement_type_div tnp_section_div">
                            <label class="form-label required"> Reinstatement Type </label>
                            <div class="input-group mb-3">
                                <select name="reinstatement_type[]" id="reinstatement_type-${counter}-0" class="form-inputs select2">
                                    <option value="NOR">Number of Reinstatement</option>
                                    <option value="AAL">Annual Aggregate Limit</option>
                                </select>
                            </div>
                        </div>
                        {{-- Reinstatement Type Value --}}
                        <div class="col-sm-3 reinstatement_value_div tnp_section_div">
                            <label class="form-label required"> Reinstatement Value </label>
                            <div class="input-group mb-3">
                                <input type="text" name="reinstatement_value[]" id="reinstatement_value-${counter}-0" class="form-control reinstatement_value tnp_section" value="" onkeyup="this.value=numberWithCommas(this.value)" required>
                            </div>
                        </div>
                    </div>
                `);

                        $(".burning_rate_div").hide();
                        $(".flat_rate_div").hide();

                        if (MethodVal === 'B') {
                            $(".burning_rate_div").show();
                            $(".burning_rate").prop('disabled', false);
                            $(".flat_rate").prop('disabled', true).val('');
                        } else {
                            $(".flat_rate_div").show();
                            $(".flat_rate").prop('disabled', false);
                            $(".burning_rate").prop('disabled', true).val('');
                        }
                    });

                    $('#layer-section').on('click', '.remove-layer-section', function() {
                        $(this).closest('.layer-sections').remove();
                    });

                    $('#add_rein_class').on('click', function() {
                        var $lastSection = $('.reinclass-section').last();

                        const prevCounter = parseInt($lastSection.attr('data-counter'))
                        const reinClassVal = $(`#treaty_reinclass-${prevCounter}`).val()
                        const prevSectionLabel = String.fromCharCode(65 + prevCounter)
                        if (reinClassVal == null || reinClassVal == '' || reinClassVal == ' ') {
                            toastr.error(`Please Select Reinsurance Class in Section ${prevSectionLabel}`,
                                'Incomplete data')
                            return false
                        }

                        var $newSection = $lastSection.clone(); // Clone the last section

                        // Remove select2-related elements
                        $newSection.find('.select2-container').remove();

                        // Increment data-counter attributes for the new section and its children
                        var counter = parseInt($lastSection.attr('data-counter')) + 1;
                        $newSection.attr('id', 'reinclass-section-' + counter);
                        $newSection.attr('data-counter', counter);
                        $newSection.find('[id]').each(function() {
                            var id = $(this).attr('id');
                            $(this).attr('id', id.replace(/-\d$/, '-' + counter));
                            $(this).attr('data-counter', counter);
                        });

                        let selectedReinClasses = []
                        $('.treaty_reinclass').each(function() {
                            const selectedVal = $(this).find('option:selected').val()

                            if (selectedVal != '') {
                                selectedReinClasses.push(selectedVal)
                            }
                        });

                        $newSection.find('.treaty_reinclass').attr('data-counter', counter)
                        $newSection.find('.comm-section').attr('id', `comm-section-${counter}`)

                        $newSection.find('.treaty_reinclass option').each(function() {
                            const val = $(this).val()
                            if (selectedReinClasses.indexOf(val) !== -1) {
                                $(this).remove();
                            }
                        })

                        // remove comm section and add afresh
                        $newSection.find('.comm-sections').remove()

                        // Update the section label (e.g., A, B, C, etc.)
                        const currentSectionLabel = String.fromCharCode(65 + counter); // A: 65
                        $newSection.find('.section-title').text('Section ' + currentSectionLabel);

                        // Reset input values in the new section
                        $newSection.find('input[type="text"], input[type="number"]').val('');

                        // Clear selected options in select elements
                        $newSection.find('select').val('').select2();

                        // Insert the new section after the last section
                        $lastSection.after($newSection);

                        appendCommSection(0, counter)
                    });

                    function processSections(sectionClass, sectionDivClass, action) {
                        if (action == 'enable') {
                            $(sectionClass + ', ' + sectionDivClass).each(function() {
                                if ($(this).hasClass(sectionDivClass.substr(1))) {
                                    $(this).show();
                                } else {
                                    $(this).prop('disabled', false);
                                    resetableTransTypes.includes(trans_type) ? $(this).val(null) : null;

                                }
                            });
                        } else {
                            $(sectionClass + ', ' + sectionDivClass).each(function() {
                                if ($(this).hasClass(sectionDivClass.substr(1))) {
                                    $(this).hide();
                                } else {
                                    $(this).prop('disabled', true);
                                    resetableTransTypes.includes(trans_type) ? $(this).val(null) : null;
                                }
                            });
                        }

                    }

                    // Adding new item in a layer
                    $('#layer-section').on('change', '.limit_per_reinclass', function() {
                        var lastLayerSection = $('#layer-section .layer-sections:last');
                        var counter = lastLayerSection.data('counter');
                        var itemcounter = 0;
                        var MethodVal = $('#method').val();
                        var limit_per_reinclass = $(`#limit_per_reinclass-${counter}-${itemcounter}`).val();
                        // Remove existing layer sections
                        $('[id^="layer-section-' + counter + '"]').remove();

                        // Add new layers based on the selected limit_per_reinclass value
                        if (limit_per_reinclass === 'Y') {
                            // Get the select element
                            var selectElement = document.getElementById("tnp_reinclass_code");

                            $('#layer-section').append(`
                        <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                            ${ counter !== 0 ? `<h6> Layer: ${counter + 1} </h6>` : '' }
                            <div class="row">
                                <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                    <label class="form-label required">Capture Limits per Class?</label>
                                    <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" data-counter="${counter}" id="limit_per_reinclass-${counter}-${itemcounter}" required>
                                        <option value="">Select Option</option>
                                        <option value="N">No</option>
                                        <option value="Y" selected>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `);

                            // Loop through each option in the selectElement
                            for (var i = 0; i < selectElement.options.length; i++) {
                                var option = selectElement.options[i];
                                if (option.selected) {
                                    var optionValue = option.value;
                                    var optionText = option.text;

                                    if (optionValue != null && optionValue != '') {
                                        $('#layer-section').append(`
                                    <div class="row layer-sections" id="layer-section-${counter}-${itemcounter}" data-counter="${counter}">
                                        <div class="col-sm-1 nonprop_reinclass">
                                            <label class="form-label required">Reinclass</label>
                                            <input type="hidden" class="form-control layer_no" data-counter="${counter}" id="layer_no-${counter}-${itemcounter}" name="layer_no[]" value="${counter + 1}" readonly>
                                            <input type="hidden" class="form-control nonprop_reinclass" data-counter="${counter}" id="nonprop_reinclass-${counter}-${itemcounter}" name="nonprop_reinclass[]" value="${optionValue}" readonly>
                                            <input type="text" class="form-control nonprop_reinclass_desc" data-counter="${counter}" id="nonprop_reinclass_desc-${counter}-${itemcounter}" name="nonprop_reinclass_desc[]" value="${optionText}" readonly>
                                        </div>
                                        <!-- Other inputs go here -->
                                    </div>
                                `);

                                        $(".burning_rate_div").hide();
                                        $(".flat_rate_div").hide();

                                        if (MethodVal === 'B') {
                                            $(".burning_rate_div").show();
                                            $(".burning_rate").prop('disabled', false);
                                            $(".flat_rate").prop('disabled', true).val('');
                                        } else {
                                            $(".flat_rate_div").show();
                                            $(".flat_rate").prop('disabled', false);
                                            $(".burning_rate").prop('disabled', true).val('');
                                        }

                                        itemcounter++;
                                    }
                                }
                            }
                        } else {
                            $('#layer-section').append(`
                        <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                            ${ counter !== 0 ? `<h6> Layer: ${counter + 1} </h6>` : '' }
                            <div class="row">
                                <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                    <label class="form-label required">Capture Limits per Class?</label>
                                    <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" id="limit_per_reinclass-${counter}-0" value="N" required>
                                        <option value="">Select Option</option>
                                        <option value="N" selected>No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Other inputs go here -->
                        </div>
                    `);

                            $(".burning_rate_div").hide();
                            $(".flat_rate_div").hide();

                            if (MethodVal === 'B') {
                                $(".burning_rate_div").show();
                                $(".burning_rate").prop('disabled', false);
                                $(".flat_rate").prop('disabled', true).val('');
                            } else {
                                $(".flat_rate_div").show();
                                $(".flat_rate").prop('disabled', false);
                                $(".burning_rate").prop('disabled', true).val('');
                            }
                        }
                    });

                    $('#layer-section').on('click', '.remove-layer-section', function() {
                        $(this).closest('.layer-sections').remove();
                    });

                    $('#apply_eml').change(function(e) {
                        e.preventDefault();
                        $(this).valid();
                        const applyEML = $(this).val()

                        $('#eml_rate').hide();
                        $('#eml_amt').hide();
                        $('.eml-div').hide();
                        if (applyEML == 'Y') {
                            $('#eml_rate').show();
                            $('#eml_amt').show();
                            $('.eml-div').show();
                        }
                    });

                    $('#eml_rate').keyup(function(e) {
                        const emlRate = $(this).val()
                        const totalSumInsured = parseFloat(removeCommas($('#total_sum_insured').val()))
                        const emlAmt = totalSumInsured * (emlRate / 100)

                        $('#eml_amt').val(numberWithCommas(emlAmt));
                        $('#effective_sum_insured').val(numberWithCommas(emlAmt));
                    });

                    $('#total_sum_insured').keyup(function(e) {
                        const totalSumInsured = removeCommas($(this).val())
                        let effectiveSumInsured = totalSumInsured

                        const emlRate = $('#eml_rate').val()
                        const applyEml = $('#apply_eml').val()

                        if ((emlRate != null && emlRate != '' && applyEml == 'Y') && (totalSumInsured != null &&
                                totalSumInsured != '')) {
                            const emlAmt = effectiveSumInsured = parseFloat(totalSumInsured) * (parseFloat(
                                emlRate) / 100)
                            $('#eml_amt').val(numberWithCommas(emlAmt));
                        }

                        $('#effective_sum_insured').val(numberWithCommas(effectiveSumInsured));
                    });
                    $('#total_sum_insured').trigger('keyup')

                    $('#brokerage_comm_type').change(function(e) {
                        const brokerageCommType = $(this).val()
                        $('.brokerage_comm_amt_div').hide();
                        $('#brokerage_comm_amt').hide();
                        $('#brokerage_comm_rate').hide();
                        $('#brokerage_comm_rate_amnt').hide();
                        $('#brokerage_comm_rate_label').hide();
                        $('#brokerage_comm_rate_amnt_label').hide();
                        $('.brokerage_comm_rate_div').hide();
                        $('.brokerage_comm_rate_amnt_div').hide();

                        @if ($trans_type == 'EDIT')
                            var brokerage_comm_rate =
                                '{{ number_format($old_endt_trans->brokerage_comm_rate, 4) }}';
                            var brokerage_comm_amt =
                                '{{ number_format($old_endt_trans->brokerage_comm_amt, 2) }}';
                            $('#brokerage_comm_rate').val(brokerage_comm_rate);
                            $('#brokerage_comm_amt').val(brokerage_comm_amt);
                        @else
                            $('#brokerage_comm_rate').val(null);
                            $('#brokerage_comm_amt').val(null);
                        @endif
                        if (brokerageCommType == 'R') {
                            $('.brokerage_comm_rate_div').show();
                            $('.brokerage_comm_rate_amnt_div').show();
                            $('#brokerage_comm_rate').show();
                            $('#brokerage_comm_rate_amnt').show();
                            $('#brokerage_comm_rate_label').show();
                            $('#brokerage_comm_rate_amnt_label').show();
                            calculateBrokerageCommRate()
                        } else {
                            $('.brokerage_comm_amt_div').show();
                            $('#brokerage_comm_amt').show().prop('disabled', false);
                        }
                    });
                    $('#brokerage_comm_type').trigger('change')

                    function calculateBrokerageCommRate() {
                        let cedantCommRate = removeCommas($('#comm_rate').val())
                        let reinCommRate = removeCommas($('#reins_comm_rate').val())
                        let commAmt = parseFloat(removeCommas($('#comm_amt').val())) || 0;
                        let reinCommAmnt = parseFloat(removeCommas($('#reins_comm_amt').val())) || 0;
                        let brokerageCommRate = 0;
                        if (cedantCommRate !== '' && cedantCommRate !== null && reinCommRate !== '' && reinCommRate !==
                            null) {
                            brokerageCommRate = Math.max(0, parseFloat(reinCommRate) - parseFloat(cedantCommRate));
                        }
                        let brokerageCommRateAmnt = (brokerageCommRate / 100) * reinCommAmnt
                        $('#brokerage_comm_rate').val(numberWithCommas(brokerageCommRate.toFixed(2)));
                        $('#brokerage_comm_rate_amnt').val(numberWithCommas(brokerageCommRateAmnt.toFixed(2)));
                    }

                    if (resetableTransTypes.includes(trans_type)) {
                        processSections('.trt_common', '.trt_common_div', 'disable');
                        processSections('.treaty_grp', '.treaty_grp_div', 'disable');
                        processSections('.tnp_section', '.tnp_section_div', 'disable');
                        processSections('.tpr_section', '.tpr_section_div', 'disable');
                        processSections('.fac_section', '.fac_section_div', 'disable');
                        processSections('.brokercode', '.brokercode_div', 'disable');

                        $('.quota_share_total_limit_div').hide();
                        $('.quota_share_total_limit').prop('disabled', true).val(null);

                        $('.retention_per_div').hide();
                        $('.retention_per').prop('disabled', true).val(null);

                        $('.quota_retention_amt_div').hide();
                        $('.quota_retention_amt').prop('disabled', true).val(null);

                        $('.quota_treaty_limit_div').hide();
                        $('.quota_treaty_limit').prop('disabled', true).val(null);

                        $('.treaty_reice_div').hide();
                        $('.treaty_reice').prop('disabled', true).val(null);

                        $('.no_of_lines_div').hide();
                        $('.no_of_lines').prop('disabled', true).val(null);

                        $('.surp_retention_amt_div').hide();
                        $('.surp_retention_amt').prop('disabled', true).val(null);

                        $('.surp_treaty_limit_div').hide();
                        $('.surp_treaty_limit').prop('disabled', true).val(null);

                        $('.surp_header_div').hide();
                        $('.quota_header_div').hide();
                    } else {
                        $('#brokerage_comm_type').trigger('change')
                        $('#apply_eml').trigger('change')
                        $('#reins_comm_type').trigger('change')
                    }

                    function computateInstalment() {
                        var shareOffered = parseFloat($('#fac_share_offered').val().replace(/,/g, '')) || 0;
                        var rate = parseFloat($('#comm_rate').val().replace(/,/g, '')) || 0;
                        var cedantPremium = parseInt($('#cede_premium').val().replace(/,/g, '')) || 0;
                        var totalDr = parseFloat((shareOffered / 100) * cedantPremium).toFixed(2);
                        var totalCr = parseFloat((rate / 100) * totalDr);
                        return (totalDr - totalCr).toFixed(2);
                    }

                    function toDecimal(number) {
                        return parseFloat(Number(number).toFixed(2));
                    }

                    function areDecimalsEqual(num1, num2, tolerance = 0.1) {}

                    $('#prospect_id').on('change', function() {

                        $('select#type_of_bus').val('FPR');
                        // $('select#branchcode').val('FPR');
                        // toastr.warning('Prospect not found!')
                    })

                    // end treaty copied code 

                    //     placeholder: 'Select Reinsurance Class',
                }
            });




            $(".add_attachment").click(function() {
                let newAttachment = $(".email_attachment_file:first").clone();
                newAttachment.find("input").val("");
                newAttachment.find("#add_attachment")
                    .removeClass("btn-primary")
                    .addClass("btn-danger remove-doc")
                    .html('<i class="bx bx-minus"></i>');

                $("#email_attachment_file").append(newAttachment);
            });


            $(document).on("click", ".remove-doc", function() {
                if ($(".email_attachment_file").length > 1) {
                    $(this).closest(".email_attachment_file").remove();
                }
            });

            $(".add_attachment_qt").click(function() {
                let newAttachment = $(".qt_email_attachment_file").clone();
                newAttachment.find("input").val("");
                newAttachment.find("#add_attachment_qt")
                    .removeClass("btn-primary")
                    .addClass("btn-danger remove-doc-qt")
                    .html('<i class="bx bx-minus"></i>');

                $("#qt_email_attachment_file").append(newAttachment);
            });


            $(document).on("click", ".remove-doc-qt", function() {
                if ($(".qt_email_attachment_file").length > 1) {
                    $(this).closest(".qt_email_attachment_file").remove();
                }
            });


            // EditEmail
            let emailList = [];
            $("#ccInput").on("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    let email = $(this).val().trim();

                    if (validateEmail(email) && !emailList.includes(email)) {
                        emailList.push(email);
                        addEmailBadge(email);
                        $(this).val("");
                    }
                }
            });

            function addEmailBadge(email) {
                let badge = $(`<span class="badge bg-primary me-2 p-2">${email} 
                <span class="ms-2 remove-email" style="cursor:pointer;">&times;</span>
            </span>`);

                badge.find(".remove-email").on("click", function() {
                    emailList = emailList.filter(e => e !== email);
                    badge.remove();
                });

                $("#ccEmailsContainer").append(badge);
            }

            function validateEmail(email) {
                let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                return emailPattern.test(email);
            }


            if (document.querySelector('.ct-chart-ranking')) {
                var chart = new Chartist.Bar('.ct-chart-ranking', {
                    labels: ['Quarter One', 'Quarter Two', 'Quarter Three', 'Quarter Four'],
                    series: @json($data)
                }, {
                    low: 0,
                    showArea: true,
                    height: '300px',
                    plugins: [
                        Chartist.plugins.tooltip()
                    ],
                    axisX: {
                        // On the x-axis start means top and end means bottom
                        position: 'end'
                    },
                    axisY: {
                        // On the y-axis start means left and end means right
                        showGrid: false,
                        showLabel: false,
                        offset: 0
                    }

                });

                chart.on('draw', function(data) {
                    if (data.type === 'line' || data.type === 'area') {
                        data.element.animate({
                            d: {
                                begin: 2000 * data.index,
                                dur: 2000,
                                from: data.path.clone().scale(1, 0).translate(0, data.chartRect
                                        .height())
                                    .stringify(),
                                to: data.path.clone().stringify(),
                                easing: Chartist.Svg.Easing.easeOutQuint
                            }
                        });
                    }
                });
            }

            // solvency calculation in stage 2 
            const $solvencyInput = $('#solvency_cr');
            const $mcrInput = $('#mcr');
            const $calculateBtn = $('#calculateBtn');
            const $clearBtn = $('#clearBtn');
            const $resultSection = $('#resultSection');
            const $toggleIcon = $('#toggleIcon');
            const $financialSection = $('#financialSection');

            // Toggle icon rotation

            // Calculate ratio
            $calculateBtn.on('click', function() {
                const solvencyValue = parseFloat($solvencyInput.val());
                const mcrValue = parseFloat($mcrInput.val());

                if (!solvencyValue || !mcrValue) {
                    alert('Please enter both Solvency CR and MCR values');
                    return;
                }

                if (mcrValue === 0) {
                    alert('MCR cannot be zero');
                    return;
                }

                // Calculate ratio
                const ratio = solvencyValue / mcrValue;

                // Display result in input field
                const $ratioInput = $('#ratio_result');
                $ratioInput.val(ratio.toFixed(4));
            });

            // Clear form
            $clearBtn.on('click', function() {
                $solvencyInput.val('');
                $mcrInput.val('');
                $('#ratio_result').val('');
            });

            // Auto-calculate on input change (optional)
            function autoCalculate() {
                const solvencyValue = parseFloat($solvencyInput.val());
                const mcrValue = parseFloat($mcrInput.val());

                if (solvencyValue && mcrValue && mcrValue !== 0) {
                    $calculateBtn.trigger('click');
                }
            }

            $solvencyInput.on('input', autoCalculate);
            $mcrInput.on('input', autoCalculate);
        </script>
    @endpush
