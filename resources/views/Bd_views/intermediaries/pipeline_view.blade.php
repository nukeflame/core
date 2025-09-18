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
                <form id="pip_year_form" action="{{ route('pipeline.view') }}" method="get">
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
                <div class="tab-pane" id="q2_details">
                    <div class="row table-responsive">
                        <table id="q2_opps" class="table table-striped" style="width:100%">
                            <thead>
                                <th>id</th>
                                <th>Insured name</th>
                                <th>Division</th>
                                <th>Business Class</th>
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
                <div class="tab-pane" id="q3_details">
                    <div class="row table-responsive">
                        <table id="q3_opps" class="table table-striped" style="width:100%">
                            <thead class="mt-2">
                                <th>id</th>
                                <th>Insured name</th>
                                <th>Division</th>
                                <th>Business Class</th>
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
                <div class="tab-pane" id="q4_details">
                    <div class="row table-responsive">
                        <table id="q4_opps" class="table table-striped" style="width:100%">
                            <thead>
                                <th>id</th>
                                <th>Insured name</th>
                                <th>Division</th>
                                <th>Business Class</th>
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
            </div>
        </div>
    </div>

    <div id="updateStatusQuote">
        <div class="modal fade" id="editStatusQuoteModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white"><span id="ed_status_name"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-header bg-primary text-white" id="quote_slip" style="display:none">
                            QUOTE SLIP
                        </div>
                        <div class="modal-header bg-primary text-white" id="qt_cedant_slip" style="display:none">
                            QUOTE SLIP CEDANT
                        </div>
                        <form id="statusUpdateForm" action="{{ route('update.opp.status') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="opp_id" id="update_opp">
                            <input type="hidden" name="pip_id" id="update_pip">
                            <input type="hidden" name="division" id="update_division">
                            <input type="hidden" name="category_type" id="category_type">
                            <input type="hidden" name="commission_rate_type" value="R">
                            <input type="hidden" name="bus_type" value="FAC">

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
                                    <h6 class="modal-title" style="font-weight:boild;" id="insured_name_qt"></h6>

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
                                                            <label class="form-label fs-14"
                                                                for="schedule-descr">Details</label>
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
                                                                {{-- <div>Exit Full screen</div> --}}
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
                                                        <div>
                                                            <input type="hidden"
                                                                id="data_determinant{{ $key }}"
                                                                value="{{ $sch->data_determinant }}"
                                                                data-key="{{ $key }}" />
                                                            <!-- Render input field for amount -->
                                                            <input type="text" class="amount form-control"
                                                                name="schedule_details[{{ $key }}][amount]"
                                                                data-key="{{ $key }}"
                                                                id="schedule_details[{{ $key }}][amount]"
                                                                placeholder="Enter amount" />

                                                        </div>
                                                    @else
                                                        <div class="card-md">
                                                            <textarea id="schedule-descr-{{ $key }}" class="form-control schedule-descr"
                                                                data-key="{{ $key }}" rows="4" placeholder="Enter description..."></textarea>
                                                        </div>
                                                    @endif
                                                    <input type="hidden"
                                                        name="schedule_details[{{ $key }}][id]"
                                                        value="{{ $sch->id }}" {{-- id="sched-details-{{ $key }}"  --}} />
                                                    <input type="hidden"
                                                        name="schedule_details[{{ $key }}][name]"
                                                        value="{{ $sch->name }}" {{-- id="sched-details-{{ $key }}"  --}} />
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
                                    <div class="reinsurer_declined" id="reinsurer_declined">


                                    </div>
                                    <div class="label">Reinsurer<font style="color:red;">*</font>
                                    </div>

                                    <input type="text" id="quote-search-bar" class="form-control"
                                        placeholder="Search for reinsurer..." />
                                </div>
                                </strong>
                            </div>


                            <div id="quote-search-results" class="mt-2">

                            </div>
                            <input type="hidden" id="uncheckeddeclinecount">

                            <!-- List of Selected Customers -->
                            <div id="quote-selected-customers-list" class="mt-4">
                                <div class="row p-3 mt-3 border rounded shadow-sm bg-light" id="qt_total_written_sh"
                                    style="display:none">
                                    <div class="col-4">
                                        <label for="" class="fw-bold ">Total Written Share</label>
                                        <input type="number" id="qt_written_share_total" name="written_share_total"
                                            class="form-control border-primary" required />
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
                                    name="updated_written_share_total" class="form-control border-primary" required
                                    hidden />

                                <div class="col-4">
                                    <label for="" class="fw-bold ">Placed Share</label>
                                    <input type="number" id="qt_placed" name="placed" class="form-control  bg-white"
                                        required readonly />
                                </div>
                                <div class="col-4">
                                    <label class="fw-bold ">Unplaced Share</label>
                                    <input type="number" id="qt_unplaced" name="unplaced"
                                        class="form-control  bg-white text-danger" required readonly />
                                </div>
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
                            {{-- end search --}}
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
    </div>

    <div id="updateStatusFacultative">
        <div class="modal fade" id="editStatusFacultativeModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white"><span id="fac_ed_status_name"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-header bg-primary text-white" id="fac_slip" style="display:none">
                            FACULTATIVE SLIP
                        </div>
                        <div class="modal-header bg-primary text-white" id="fac_pl_det" style="display:none">
                            FACULTATIVE PLACEMENT DETAILS
                        </div>
                        <div class="modal-header bg-primary text-white" id="fac_pl_update" style="display:none">
                            FACULTATIVE PLACEMENT UPDATE
                        </div>
                        <div class="modal-header bg-primary text-white" id="fac_pl_ced" style="display:none">
                            FACULTATIVE PLACEMENT CEDANT
                        </div>
                        <form id="facultative-statusUpdateForm" action="{{ route('update.opp.status') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="opp_id" id="update_oppfac">
                            <input type="hidden" name="pip_id" id="update_pipfac">
                            <input type="hidden" name="division" id="update_divisionfac">
                            <input type="hidden" name="category_type" id="category_type2">
                            <input type="hidden" name="commission_rate_type" value="C">
                            <input type="hidden" name="bus_type" value="FAC">





                            <div class="row" id="facultative_div" style="display:none">
                                <div class="col-12">
                                    <input name="quote_title_intro" id="fac_title_intro" inputLabel="Facultative Title"
                                        style="font-weight: bold;" value="FACULTATIVE PLACEMENT" type="hidden" required>
                                    </input>
                                </div>
                                <!-- Start of new code -->
                                <div class="modal-header">
                                    <h5 class="modal-title dc-modal-title" id="staticScheduleDetailsLabel">Schedule
                                        Details
                                    </h5>
                                    <h6 class="modal-title" style="font-weight:boild;" id="insured_name_fc"></h6>

                                    </h5>
                                    <button aria-label="Close" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body mb-3 p-3 border rounded shadow-sm">
                                    @if (isset($schedule))
                                        @foreach ($schedule as $key => $sch)
                                            <div class="mb-1 p-1 border rounded shadow-sm">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <input type="hidden" id="facclassname{{ $key }}"
                                                        value="{{ $sch->class }}" data-key="{{ $key }}" />
                                                    <input type="hidden" id="facname{{ $key }}"
                                                        value="{{ $sch->name }}" data-key="{{ $key }}" />
                                                    <input type="hidden" id="facclassgroup{{ $key }}"
                                                        value="{{ $sch->class_group }}"
                                                        data-key="{{ $key }}" />
                                                    <input type="hidden"
                                                        name="facschedule_details[{{ $key }}][id]"
                                                        value="{{ $sch['id'] }}" data-key="{{ $key }}" />
                                                    <input type="hidden" class="schedule-sum-insured-fac"
                                                        data-key="{{ $key }}"
                                                        value="{{ $sch->sum_insured_type }}">
                                                    <h6 class="ms-2 schedule-name" data-key="{{ $key }}">
                                                        {{ firstUpper($sch->name) }}
                                                        @if (strtolower($sch->name) == 'allowed commission')
                                                            (%)
                                                        @endif
                                                    </h6>
                                                    <span class="facultative-toggle-icon" role="button"
                                                        aria-expanded="true" aria-label="Toggle Content"
                                                        data-target="#optional-{{ $key }}">
                                                        <strong><i class='bx bx-plus'></i></strong>
                                                    </span>
                                                </div>
                                                <div id="optional-{{ $key }}" class="row"
                                                    style="display: none; margin-top: 10px; border-top: 2px solid #f0f0f0; padding-top: 10px;">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fs-14"
                                                                for="facschedule-descr">Details</label>
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
                                                                {{-- <div>Exit Full screen</div> --}}
                                                            </button>
                                                            <div class="col-4 ms-auto">
                                                                <button
                                                                    class="form-control sm-btn bg-dark text-white schedule-fac check-schedule"
                                                                    data-key="{{ $key }}">Load data</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Schedule Description Section -->
                                                    @if (!empty($sch->amount_field) && $sch->amount_field == 'Y')
                                                        <!-- Render input field for amount -->
                                                        <input type="hidden"
                                                            id="data_determinant_fac{{ $key }}"
                                                            value="{{ $sch->data_determinant }}"
                                                            data-key="{{ $key }}" />
                                                        <div class="card-md">
                                                            <input type="text" class="amount form-control trigger"
                                                                name="facschedule_details[{{ $key }}][amount]"
                                                                placeholder="Enter amount"
                                                                id="facschedule_details[{{ $key }}][amount]"
                                                                data-key="{{ $key }}" />
                                                        </div>
                                                    @else
                                                        <div class="card-md">
                                                            <textarea id="facschedule-descr-{{ $key }}" class="form-control" rows="4"
                                                                placeholder="Enter description..."></textarea>
                                                        </div>
                                                    @endif
                                                    <input type="hidden"
                                                        name="facschedule_details[{{ $key }}][id]"
                                                        value="{{ $sch->id }}" data-key="{{ $key }}" />
                                                    <input type="hidden"
                                                        name="facschedule_details[{{ $key }}][name]"
                                                        value="{{ $sch->name }}" data-key="{{ $key }}" />
                                                    <input type="hidden"
                                                        name="facschedule_details[{{ $key }}][details]"
                                                        id="facsched-details-{{ $key }}"
                                                        data-key="{{ $key }}" required />
                                                </div>

                                            </div>
                                        @endforeach
                                    @endif
                                    <input type="hidden" id="sum_insured_type_fac">
                                    <input type="hidden" name="stagecyclefac" id="stagecyclefac">

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
                                    <div class="reinsurer_declined">


                                    </div>
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
                                <div class="row p-3 mt-3 border rounded shadow-sm bg-light" id="fac_total_written_sh"
                                    style="display:none">
                                    <div class="col-4">
                                        <label for="" class="fw-bold ">Total Written Share</label>
                                        <input type="number" id="written_share_total" name="updated_written_share_total"
                                            class="form-control " required />
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
                            <div class="row p-3 mt-3 border rounded shadow-sm bg-light" id="fac_total_placed_unplaced"
                                style="display:none">

                                <input type="hidden" id="fac_written_share_total" class="form-control border-primary"
                                    required />

                                <div class="col-4">
                                    <label for="" class="fw-bold ">Placed Share</label>
                                    <input type="number" id="placed" name="placed" class="form-control " required
                                        readonly />
                                </div>
                                <div class="col-4">
                                    <label class="fw-bold">Unplaced Share</label>
                                    <input type="number" id="unplaced" name="unplaced"
                                        class="form-control  bg-white text-danger" required readonly />
                                </div>
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
                            <div class="row mt-1" id="email_attachment_file" style="padding-left: 10px;">
                                <div class="row mt-1 email_attachment_file">
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
                                    id="facultative_generate_slip" style="display: none">
                                    <i class="bx bx-analyse me-1 align-middle"></i> Preview
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bx bx-x-circle"></i> Close
                                </button>
                                <button id="facultativeUpdateStatusBtn" type="submit" class="btn btn-outline-success">
                                    <i class="bx bx-check-circle"></i> Submit
                                </button>

                            </div>
                        </form>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateCategoryTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Update Category Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('update.category_type') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="opportunity_id" id="opportunity_id" />
                        <div class="mb-3">
                            <label for="category_type" class="form-label">Category Type</label>
                            <select class="form-control form-control-sm" name="category_type" required>
                                <option value="">Select Category</option>
                                <option value="1">Quotation</option>
                                <option value="2">Facultative Offer</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    <div class="modal fade" id="printoutType" tabindex="-1" role="dialog" aria-labelledby="printoutTypeLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Printout Type Confirmation</h5>
                    <button type="button" class="btn-close printtypeClose" data-bs-dismiss="modal" aria-label="Close">
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="filePreviewFrame" style="width:100%; height:80vh;" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rej-text" tabindex="-1" aria-labelledby="filePreviewModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filePreviewModalLabel">Rejection Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="rejection_message"
                        style="min-height: 340px; border: 1px solid #eeeeeef2;padding: 10px;border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <form id="quoteSlipForm" method="POST" action="{{ route('docs.quotationCoverSlip') }}" target="_blank"
        style="display: none;">
        @csrf
    </form>




@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            var lead = {!! $pip !!};
            $('#optional1').hide(); // Initially hide the element
            $('#optional').hide();
            const customers = @json($customers);
            const Users = @json($users ?? []);



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
                $('#insured_name_qt').text(insured_name)
                $('#insured_name_fc').text(insured_name)
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



                if (stage_id == 1) {
                    $('#quote_slip, #quote_div, #facultative_div, #fac_slip, #quote_search_reinsurer, #fac_search_reinsurer, #facultative_generate_slip,#fac_total_written_sh,#facultative-selected-customers-list')
                        .show();
                    $('#facfinalStage_div', '#fac_pl_update').hide();
                    $('#fac_pl_det').hide();
                    processSections('.updated_written_share_total', '#qt_total_placed_unplaced', 'disable');
                } else {
                    $('#quote_search_reinsurer, #facultative_div, #fac_slip, #fac_search_reinsurer,#quote_search_reinsurer, #quote_slip ,#fac_total_written_sh')
                        .hide();
                    processSections('.updated_written_share_total', '#qt_total_placed_unplaced', 'enable');
                }
                if (stage_id == 2) {
                    $('#qt_cedant_slip, #quote_div, #facultative_div, #facultative_generate_slip, #fac_pl_ced,#fac_total_placed_unplaced ,#qt_total_placed_unplaced')
                        .show();
                    $('#facultative-selected-customers-list, #fac_pl_update').hide();

                } else {
                    $('#facfinalStage_div, #facstage_div, #fac_pl_update,#fac_total_placed_unplaced,#qt_total_written_sh,#contacts-wrapper-fac,#contacts-wrapper-qt,#fac_pl_ced,#qt_cedant_slip')
                        .hide();
                }

                if (stage_id == 3) {
                    $('#quote_div,#facultative_div,#quote_slip,#facultative_generate_slip,#fac_pl_update,#qt_total_placed_unplaced,#fac_total_placed_unplaced')
                        .show();
                    $('#fac_pl_det').hide();
                } else {


                    // $('#facstage_div, #fac_pl_update').hide();
                }

                if (stage_id == 4) {
                    $('#quote_slip,#facultative_div, #stage_div,#facstage_div,#fac_pl_update')
                        .show();
                    $('#quote_div,#generate_slip,#fac_search_reinsurer,#quote_search_reinsurer,#qt_email_attachment_file,#facultative_div,#email_attachment_file')
                        .hide();

                } else {
                    $('#stage_div,#facstage_div').hide();
                    $('#qt_email_attachment_file').show();
                    $('#email_attachment_file').show();
                }

                if (stage_id == 5) {
                    $('#editStatusQuoteModal').hide()

                    $('#quote_div').hide()
                    $('#updateQuoteFooter').hide()



                    // $('#editStatusFacultativeModal').modal('hide')

                }
                if (type_of_business == 'TNP' || type_of_business == 'TPR') {
                    $('#qt_schedule_div').hide();
                    $('#qt_schedule_div')
                        .find('input, select, textarea, button')
                        .prop('disabled', true);
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

                if (stage_id == 5 && category_type == 2) {
                    toastr.warning('Cannot change status of a WON prospect.', {
                        timeOut: 5000
                    });
                    $('#updateStatusFacultative').style('display:none');
                }
                if (stage_id == 6 && category_type == 2) {
                    toastr.warning('Cannot change status of a LOST prospect.', {
                        timeOut: 5000
                    });
                    $('#updateStatusFacultative').style('display:none');
                } else {

                    if (category_type == 1) {
                        $('#editStatusQuoteModal').modal('show');
                    } else {
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
                    let uncheckedCount = Number($('#uncheckeddeclinecount').val());
                    schedule.forEach(function(sch, index) {
                        const key = index;
                        let dataDeterminant = $('#data_determinant' + key).val();
                        if (dataDeterminant === 'SI') {
                            $('[name="schedule_details[' + key + '][amount]"]').val(
                                formated_total_sum_insured);
                        } else if (dataDeterminant === 'PREM' && (stage_id == 1 && uncheckedCount >
                                0) ||
                            stage_id > 1) {
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
                            if ((stage_id == 1 && uncheckedCount > 0) || stage_id > 1) {
                                $('[name="schedule_details[' + key + '][amount]"]').val(
                                    reins_comm_rate);
                            }
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
                            // console.log('data:', quote_schedules_data);


                            schedule.forEach(function(sch, index) {
                                const key = index;
                                const editorKey = `editor` + key;
                                let dataDeterminant = $('#data_determinant' + key)
                                    .val();
                                if (dataDeterminant === 'SI') {
                                    $('[name="schedule_details[' + key +
                                        '][amount]"]').val(
                                        formated_total_sum_insured);
                                } else if (
                                    dataDeterminant === 'PREM' &&
                                    (
                                        (stage_id == 1 && $(
                                            '#uncheckeddeclinecount').val() > 0) ||
                                        stage_id > 1
                                    )
                                ) {
                                    $('[name="schedule_details[' + key +
                                        '][amount]"]').val(formated_premium);
                                } else if (dataDeterminant === 'COM' && stage_id ==
                                    2 && category_type == 1) {
                                    $('[name="schedule_details[' + key +
                                        '][amount]"]').val(
                                        cedant_comm_rate);
                                    $('[name="schedule_details[' + key +
                                        '][name]"]').val(
                                        "Cedant Commission Rate");

                                    // Update visible header with formatted text
                                    let displayText = "Cedant Commission Rate";
                                    if (displayText.toLowerCase().includes(
                                            'commission')) {
                                        displayText += " (%)";
                                    }
                                    $('.schedule-name-qt[data-key="' + key + '"]')
                                        .text(
                                            displayText);
                                } else if (dataDeterminant === 'COM' && stage_id !=
                                    2) {
                                    let displayText = "Reinsurer Commission Rate";

                                    if ((stage_id == 1 && $(
                                            '#uncheckeddeclinecount').val() > 0) ||
                                        stage_id != 2 && $('#uncheckeddeclinecount')
                                        .val() == " ") {
                                        $('[name="schedule_details[' + key +
                                            '][amount]"]').val(
                                            reins_comm_rate);
                                    }
                                    $('[name="schedule_details[' + key +
                                        '][name]"]').val(displayText);

                                    if (displayText.toLowerCase().includes(
                                            'commission')) {
                                        displayText += " (%)";
                                    }
                                    $('.schedule-name-qt[data-key="' + key + '"]')
                                        .text(
                                            displayText);

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
                                        $('[name="schedule_details[' + key +
                                            '][amount]"]').val(
                                            combinedText);

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

                                        $('[name="facschedule_details[' + key +
                                            '][amount]"]').val(
                                            combinedText);
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
                            <input type="checkbox" name="dept_select_contact_main[]" value="${index}" data-index="${index}" class="form-check-input dept-row-checkbox" checked>
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
                if (stage_id !== '2') {
                    $('#quote_title_intro').val('QUOTATION TERMS PLACEMENT');

                }

                if (stage_id == 2) {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('reinsurer.declined') }}",
                        data: {
                            'prospect': opp_id
                        },
                        success: function(response) {
                            $('.reinsurer_declined').empty();
                            if (response.declined_reinsurers.length != 0) {


                                let names = [];
                                let uncheckeddeclinecount = null;
                                uncheckeddeclinecount = response.decline_unchecked_count;

                                $.each(response.declined_reinsurers, function(indexInArray,
                                    valueOfElement) {
                                    names.push(valueOfElement.customer_name.name);
                                });

                                $('.reinsurer_declined').html(
                                    '<strong> Declined:  </strong>' +
                                    '<span style="color:red;">' + names.join(', ') +
                                    '</span>'
                                );
                                if (uncheckeddeclinecount > 0) {
                                    $('#uncheckeddeclinecount').val(uncheckeddeclinecount);
                                    $('#quote_title_intro').val('QUOTATION TERMS PLACEMENT');
                                }



                            }

                        }
                    });
                }

                if (stage_id == 3) {
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


                if (stage_id == 2) {
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


                                $(`#${categoryTypeFile}`).empty();
                                let cedantTitleAdded = false;
                                let ourTitleAdded = false;
                                let cedantCheckboxes = '';
                                let ourCheckboxes = '';

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
                                });

                                // Append Cedant title and checkboxes
                                if (cedantCheckboxes && !cedantTitleAdded) {
                                    $(`#${categoryTypeFile}`).append(`
                                        <div class="col-12 mt-3">
                                            <small><b style="color: #E1251B"><i>Cedant Required Documents</i></b></small>
                                            <hr>
                                        </div>
                                        <div class="row my-md-3">
                                            ${cedantCheckboxes}
                                        </div>
                                    <div class="col-12 mt-3">

                                        </div>
                                    `);
                                    cedantTitleAdded = true;
                                }

                                // Append Our title and checkboxes
                                if (ourCheckboxes && !ourTitleAdded) {
                                    $(`#${categoryTypeFile}`).append(`
                                        <div class="col-12 mt-3">
                                            <small><b style="color: #E1251B"><i>Our Required Documents</i></b></small>
                                            <hr>
                                        </div>
                                        <div class="row my-md-3">
                                            ${ourCheckboxes}
                                        </div>
                                    `);
                                    ourTitleAdded = true;
                                }
                                if (Object.keys(resp.docs).length > 0) {
                                    $(`#${categoryTypeFile}`).append(
                                        `<div class="col-12 mt-3">
                                        <small><b  style="color: #E1251B"><i>Please Upload files and  Supporting documents</i></b></small>
                                        <hr>
                                    </div>`
                                    );
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


                if (stage_id == 3 || stage_id == 4) {
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
                                if (stage_id == 2) {
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


                                    if (category_type == 1) {
                                        let signedShareField = "";
                                        let declineCheckbox = "";
                                        if (stage_id == 4) {
                                            signedShareField = `
                                            <div class="col-3">
                                                <label for="signed_share${reinsurerIndex}">Signed Share (%)<font style="color:red;">*</font></label>
                                                <input type="number" id="signed_share${reinsurerIndex}"
                                                name="reinsurers[${reinsurerIndex}][signed_share]" class="form-control signed_share"  value="${reinsurer.signed_share || ''}" required />
                                            </div>
                                        `;
                                        }
                                        if (stage_id == 3) {
                                            declineCheckbox = `
                                             <div class="col-auto mt-3">
                                                <input name="reinsurers[${reinsurerIndex}][decline_inserted]" ${reinsurer.decline_reason ? ' value="YES" ' : ''} hidden/>
                                                <label>Decline</label>
                                                <input type="checkbox" class="form-check-input decline-checkbox" name="reinsurers[${reinsurerIndex}][decline]"
                                                value="${reinsurer.reinsurer_id}"
                                                data-index="${reinsurerIndex}"
                                                 ${reinsurer.decline_reason ? 'checked' : ''}
                                                 onclick="document.getElementById('comment-box-${reinsurerIndex}').style.display = this.checked ? 'block' : 'none';">
                                            </div>
                                            <div class="col-auto mt-3 form-control"  id="comment-box-${reinsurerIndex}" style="display: none;">
                                                <Textarea class="form-control comment-box" name="reinsurers[${reinsurerIndex}][comments]" rows="2" placeholder="Comments">${reinsurer.decline_reason}</textarea>
                                            </div>

                                            `
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

                                            <!-- Written Share -->
                                            <div class="col-3">
                                                <label for="written_share${reinsurerIndex}">Written Share (%)<font style="color:red;">*</font></label>
                                                <input type="number" id="written_share${reinsurerIndex}"
                                                    name="reinsurers[${reinsurerIndex}][written_share]" class="form-control written-share"
                                                    value="${reinsurer.written_share || ''}" required />
                                            </div>
                                            <!--decline checkbox-->
                                            ${declineCheckbox}

                                             <!-- Signed Share -->
                                             ${signedShareField}

                                             <!--Contact person-->
                                              ${contactPersonsLabel}
                                              ${contactPersonsHtml}

                                        </div>
                                        <hr>

                                    `);

                                    } else if (category_type == 2) {
                                        let declineCheckbox = "";
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
                                        if (stage_id == 3) {
                                            declineCheckbox = `
                                             <div class="col-auto mt-3">
                                                <input name="reinsurers[${reinsurerIndex}][decline_inserted]" ${reinsurer.decline_reason ? ' value="YES" ' : ''} hidden/>
                                                <label>Decline</label>
                                                <input type="checkbox" class="form-check-input decline-checkbox" name="reinsurers[${reinsurerIndex}][decline]"
                                                value="${reinsurer.reinsurer_id}"
                                                data-index="${reinsurerIndex}"
                                                 ${reinsurer.decline_reason ? 'checked' : ''}
                                                 onclick="document.getElementById('comment-box-${reinsurerIndex}').style.display = this.checked ? 'block' : 'none';">
                                            </div>
                                            <div class="col-auto mt-3 form-control"  id="comment-box-${reinsurerIndex}" style="display: none;">
                                                <Textarea class="form-control comment-box" name="reinsurers[${reinsurerIndex}][comments]" rows="2" placeholder="Comments">${reinsurer.decline_reason}</textarea>
                                            </div>

                                            `
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
                                            <!--decline checkbox-->
                                            ${declineCheckbox}

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
                                                <input type="checkbox" name="dept_select_contact_main[]" value="${index}" data-index="${index}" class="form-check-input dept-row-checkbox" checked>
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
            const businessTypes = ['FNP', 'FPR'];

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
                    url: "{{ route('pipeline.activity') }}" + "?pipe_id=" + encodeURIComponent(lead),
                    type: "get",
                    data: {
                        'business_types': businessTypes,
                    }

                },
                'columns': [{
                        data: 'opportunity_id',
                        name: 'opportunity_id',
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
                        name: 'action',
                        sortable: false
                    }
                ],
                order: [
                    [0, 'desc']
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
                    url: "{{ route('pipeline.activity.q1') }}" + "?pipe_id=" + encodeURIComponent(
                        lead),
                    type: "get",
                    data: {
                        'business_types': businessTypes,
                    }

                },
                columns: [{
                        data: 'opportunity_id',
                        name: 'opportunity_id',
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
                        name: 'action',
                        sortable: false
                    }
                ],
                order: [
                    [0, 'desc']
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
                    url: "{{ route('pipeline.activity.q2') }}" + "?pipe_id=" + encodeURIComponent(
                        lead),
                    type: "get",
                    data: {
                        'business_types': businessTypes,
                    }

                },
                columns: [{
                        data: 'opportunity_id',
                        name: 'opportunity_id',
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
                        name: 'action',
                        sortable: false
                    }
                ],
                order: [
                    [0, 'desc']
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
                    url: "{{ route('pipeline.activity.q3') }}" + "?pipe_id=" + encodeURIComponent(
                        lead),
                    type: "get",
                    data: {
                        'business_types': businessTypes,
                    }

                },
                columns: [{
                        data: 'opportunity_id',
                        name: 'opportunity_id',
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
                        name: 'action',
                        sortable: false
                    }
                ],
                order: [
                    [0, 'desc']
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
                    url: "{{ route('pipeline.activity.q4') }}" + "?pipe_id=" + encodeURIComponent(
                        lead),
                    type: "get",
                    data: {
                        'business_types': businessTypes,
                    }

                },
                columns: [{
                        data: 'opportunity_id',
                        name: 'opportunity_id',
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
                        name: 'action',
                        sortable: false
                    }
                ],
                order: [
                    [0, 'desc']
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
                    const editorKey = 'editor' + key;
                    const element = document.querySelector('#schedule-descr-' + key);

                    if (!element) {
                        // console.warn(`Editor target #schedule-descr-${key} not found. Skipping.`);
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
                                'bold', 'italic', 'underline', 'strikethrough',
                                '|',
                                'fontSize', 'fontFamily', 'textColor', 'backgroundColor',
                                '|',
                                'alignment',
                                '|',
                                'insertTable', 'link', 'blockQuote', 'image',
                                '|',
                                'bulletedList', 'numberedList',
                                '|',
                                'indent', 'outdent',
                                '|',
                                'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyBlock',
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
                            $(editor.ui.view.editable.element).closest('.ck-editor').addClass(
                                'schedule_editor');
                        })
                        .catch(err => {
                            console.error(`Error initializing editor${key}:`, err);
                        });
                });
            }

            function setScheduleDetails() {
                schedule.forEach(function(sch, index) {
                    const key = index;
                    const editorKey = 'editor' + key;
                    let field = document.getElementById('sched-details-' + key);

                    if (window[editorKey] && field && !field.disabled) {
                        field.value = window[editorKey]?.getData();
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
                            table: {
                                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
                                defaultProperties: {
                                    width: '100%',
                                    borderStyle: 'solid',
                                    borderWidth: '1px',
                                    borderColor: '#ccc',
                                }
                            },
                            ui: {
                                viewportOffset: {
                                    top: 0,
                                },
                            },
                            width: '100%',
                            clipboard: {
                                copyOnSelect: false
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


            let skipReset = false;
            // Attach the setScheduleDetails function to the form submissiongenerate_slip
            $('#generate_slip, #updateStatusBtn').on('click', function(event) {
                event.preventDefault();
                const form = document.getElementById('statusUpdateForm');
                let resultsContainer = document.getElementById('quote-search-results');
                results = resultsContainer.innerHTML.trim();

                let searchInput = document.getElementById('quote-search-bar');

                let stage_id = $('#stage_cycle option:selected').val()
                if ($("#qt_unplaced").val() > 0 && stage_id == 4) {
                    toastr.error('Please Place all shares before submitting')
                    return false;
                }
                if ($("#qt_unplaced").val() < 0 && stage_id == 4) {
                    toastr.error('signed share exceeds the total share')
                    return false;
                }
                switch (stage_id) {
                    case '2':
                        if (!$('.row-radio:checked').length) {
                            toastr.error('Please search for reinsurer and check main contact person');
                            return false;
                        }
                        break;
                    case '3':
                        if (!$('.row-radio:checked').length) {
                            toastr.error('Please  check  cedant main contact person');
                            return false;
                        }
                        break;
                    case '4':
                        if (!$('.row-radio:checked').length) {
                            toastr.error('Please check  main contact person for reinsurer');
                            return false;
                        }
                        break;


                }
                if ($(this).attr('id') === 'updateStatusBtn') {
                    setScheduleDetails();

                    if (form.checkValidity()) {
                        skipReset = true;
                        $('#editStatusQuoteModal').modal('hide');
                        let selectedContacts = [];
                        if (stage_id == 3) {
                            let declineCheckedCount = $('.decline-checkbox:checked').length;
                            let declineUncheckedCount = $('.decline-checkbox').length - declineCheckedCount;
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'declineUncheckedCount',
                                value: declineUncheckedCount
                            }).appendTo(form);
                        }


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
                switch (stage_id) {
                    case '2':
                        if (!$('.row-radio:checked').length) {
                            toastr.error('Please search for reinsurer and check main contact person');
                            return false;
                        }
                        break;
                    case '3':
                        if (!$('.row-radio:checked').length) {
                            toastr.error('Please  check  cedant main contact person');
                            return false;
                        }
                        break;
                    case '4':
                        if (!$('.row-radio:checked').length) {
                            toastr.error('Please check  main contact person for reinsurer');
                            return false;
                        }
                        break;


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
                });

                $('#printoutType').modal('show');

                $('#printoutType').off('click').on('click', '.confirm_print_type', function() {
                    const printoutType = $(this).data('value');

                    const sourceForm = $('#facultative-statusUpdateForm');
                    const postForm = $('#quoteSlipForm');
                    postForm.empty();

                    // Add CSRF token
                    postForm.append($('<input>', {
                        type: 'hidden',
                        name: '_token',
                        value: $('meta[name="csrf-token"]').attr('content')
                    }));

                    // Copy all form data
                    sourceForm.serializeArray().forEach(field => {
                        postForm.append($('<input>', {
                            type: 'hidden',
                            name: field.name,
                            value: field.value
                        }));
                    });

                    // Add selected printout type
                    postForm.append($('<input>', {
                        type: 'hidden',
                        name: 'printout_flag',
                        value: printoutType
                    }));

                    $('#editStatusFacultativeModal').css({
                        'opacity': 1,
                        'pointer-events': 'auto'
                    });

                    $('#printoutType').modal('hide');

                    // Submit to open PDF in new tab
                    postForm.submit();

                    // Remove handler to prevent duplicate bindings
                    $('#printoutType').off('click', '.confirm_print_type');
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

                if (category_type == 2) {

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
                } else if (category_type == 1) {
                    let written_share = '';
                    if ($('#reinsurer_declined').html().trim() !== '' && $('#uncheckeddeclinecount').val() >
                        0) {
                        written_share = `<div class="col-3 mt-1">
                                    <label for="written share">Written Share(%)<font style="color:red;">*</font></label>
                                    <div class="input-group">
                                    <input type="number" name="written_share[]" id="written_share_${customer.id}"
                                        class="form-control document_file checkempty" placeholder="" required />
                                    </div>
                                </div>
                    `
                    }
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
                              ${written_share}

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
        $('#editStatusQuoteModal').on('hidden.bs.modal', function() {
            if (!skipReset) {
                $('#statusUpdateForm')[0].reset(); // Reset the form
                $('#statusUpdateForm').find('input[type="hidden"]').not('[name="_token"]')
                    .remove();
            }
            skipReset = false;
        });

        // $('#editStatusQuoteModal').on('hide.bs.modal', function() {
        //     $("#statusUpdateForm")[0].reset();
        // });
        // $('#editStatusFacultativeModal').on('hide.bs.modal', function() {
        //     $("#facultative-statusUpdateForm")[0].reset();
        // });
    </script>
@endpush
