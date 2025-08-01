@extends('layouts.app')

@section('content')
    <style>
        .search-container {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .search-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .result-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .result-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .contact-badge {
            background: #d4edda;
            color: #155724;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .user-badge {
            background: #e2e3f1;
            color: #383d41;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .search-input {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .search-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .search-btn {
            border-radius: 8px;
            padding: 0.75rem 2rem;
        }

        .result-section-title {
            color: #007bff;
            font-weight: 600;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .no-results {
            text-align: center;
            color: #6c757d;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .loading {
            text-align: center;
            padding: 2rem;
        }

        .remarks-modal .modal-content {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .remarks-modal .modal-header {
            background: #01444d;
        }

        .remarks-modal .form-control {
            border-radius: 8px;
        }
    </style>
    <div>
        {{-- <nav class="breadcrumb">
        <a class="breadcrumb-item" href="#">Tender</a>
        <span> ➤ Tender Details ➤ Tender ➤ {{ $tender->tender_no }}</span>
    </nav> --}}

        <form action="{{ route('tender-printout') }}" method="post" id="TenderPrintForm" target="_blank">
            @csrf
            <input type="hidden" name="print_tender_no" id="print_tender_no" value="{{ $tender->tender_no }}">
            <input type="hidden" name="print_tender_name" id="print_tender_name" value="{{ $tender->tender_name }}">
        </form>
    </div>

    <div class="card">
        <div class="row card-body">
            <div class="col-md-6">
                <!-- Left-side content -->
                <h6>Proposal Details</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <td colspan="3"><span> {{ $tender->tender_no }}</td>
                        </tr>
                        <tr>
                            <th>Cedant Name</th>
                            <td>{{ $tender->client_name }}</td>
                        </tr>
                        <tr>
                            <th>Nature of Proposal</th>
                            <td>{{ $tender->tender_nature }}</td>
                        </tr>
                        {{-- <tr>
                            <th>Proposal Category</th>
                            <td>{{ $tender->tender_category }}</td>
                        </tr> --}}
                        <tr>
                            <th>Tender Description</th>
                            <td colspan="3"><span>{{ $tender->tender_description }} </span></td>
                        </tr>
                        <tr>
                            <th>Closing Date</th>
                            <td colspan="3"><span>{{ formatDate($tender->closing_date) }}</span></td>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="col-md-6">
                <!-- Right-side content -->
                <h6>Table Of Contents</h6>
                <table class="table table-bordered" id="tendertoc_table">
                    <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal"
                        data-bs-target="#tenderModal">
                        + Add TOC Section
                    </button>
                    <thead>
                        <tr>
                            <th>Section No</th>
                            <th>Section Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tenderTocs as $tenderToc)
                            @if ($tenderToc->toc_category == 'SECT')
                                <tr>
                                    <td class="small-td">{{ $tenderToc->sort_no }}</td>
                                    <td class="large-td">{{ $tenderToc->toc_head }}</td>
                                    <td><button id="toc_sec_items" class="btn btn-success btn-sm"
                                            value="{{ $tenderToc->toc_no }}+-{{ $tenderToc->toc_description }}"
                                            data-toc_head="{{ $tenderToc->toc_head }}"> <i class="fa fa-plus"></i> Add
                                            Subcategories</button>
                                        <button type="button" class="btn btn-outline-primary btn-sm edit-toc-btn"
                                            data-toc-id="{{ $tenderToc->id }}" data-toc-head="{{ $tenderToc->toc_head }}"
                                            data-toc-no="{{ $tenderToc->toc_no }}" data-bs-toggle="modal"
                                            data-bs-target="#editTocModal">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        <tr>
                            <td colspan="4">
                                <button class="btn btn-success btn-sm" data-bs-toggle='modal'
                                    data-bs-target='#edtenderModal'>Edit
                                    Tender</button>
                                <button class="btn btn-success btn-sm">Close Tender</button>
                                @if (!empty($approval->file))
                                    <button type="button" class="btn btn-dark btn-sm"
                                        onclick="window.open('{{ Storage::disk('s3')->url($approval->file) }}', '_blank')"
                                        title="View File">
                                        <i class="fas fa-eye"></i>
                                        Preview Tender Invite Letter
                                    </button>
                                @else
                                    <span class="text-muted">No Tender Invite Letter uploaded</span>
                                @endif

                                <button class="btn btn-dark me-2 btn-sm" id="generatePdfButton">View Tender Printout
                                </button>
                                <button type="button" id="previewLetterBtn" class="btn btn-dark btn-sm">
                                    Preview Letter
                                </button>
                                <button class="btn btn-success btn-sm" id="addFooterSettings">Document Settings</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Edit TOC Section Modal -->
        <div class="modal fade" id="editTocModal" tabindex="-1" aria-labelledby="editTocModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="editTocModalLabel">Edit TOC Section</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editTocForm">
                            @csrf
                            <input type="hidden" name="toc_id" id="edit_toc_id">
                            <input type="hidden" name="tender_no" id="edit_tender_no" value="{{ $tender->tender_no }}">
                            <input type="hidden" name="toc_no" id="edit_toc_no">
                            <div class="form-group">
                                <label for="edit_toc_head">Section Description</label>
                                <input type="text" class="form-control" id="edit_toc_head" name="toc_head" required
                                    maxlength="200">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitEditTocForm">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mx-2">

            <div class="col-md-12">

                <h6>Attachments</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Attachment Name</th>
                            {{-- <th>Status</th> --}}
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tenderTocSecs as $tenderTocSec)
                            <tr>
                                <td><b>{{ $tenderTocSec->toc_description }}</b></td>
                                <td></td>
                            </tr>
                            @foreach ($tendersubcats as $tenderTocItem)
                                @if ($tenderTocItem->toc_no == $tenderTocSec->toc_no)
                                    <tr>
                                        <td>{{ $tenderTocItem->subcat_desc }}</td>
                                        <td>
                                            <button type="button" class="btn btn-outline-info btn-sm"
                                                onclick="window.open('{{ route('view.document', $tenderTocItem->doc_id) }}', '_blank')">
                                                <i class="fa fa-file"></i> View Attachment
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm edit-subcat-btn"
                                                data-subcat-id="{{ $tenderTocItem->subcat_id }}"
                                                data-subcat-desc="{{ $tenderTocItem->subcat_desc }}"
                                                data-doc-id="{{ $tenderTocItem->doc_id }}"
                                                data-toc-no="{{ $tenderTocItem->toc_no }}" data-bs-toggle="modal"
                                                data-bs-target="#editSubcatModal">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
                <br>

                <!-- Buttons for PDF and draft bill -->

            </div>


            <!-- Edit Subcategory Modal -->
            <div class="modal fade" id="editSubcatModal" tabindex="-1" aria-labelledby="editSubcatModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="editSubcatModalLabel">Edit Subcategory</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editSubcatForm">
                                @csrf
                                <input type="hidden" name="subcat_id" id="edit_subcat_id">
                                <input type="hidden" name="tender_no" id="edit_tender_no"
                                    value="{{ $tender->tender_no }}">
                                <input type="hidden" name="tender_name" id="edit_tender_name"
                                    value="{{ $tender->tender_name }}">
                                <input type="hidden" name="toc_no" id="edit_toc_no">
                                <div class="form-group">
                                    <label for="edit_subcat_desc">Subcategory Description</label>
                                    <input type="text" class="form-control" id="edit_subcat_desc" name="subcat_desc"
                                        required maxlength="200">
                                </div>
                                <div class="form-group">
                                    <label for="edit_doc_id">Attachment</label>
                                    <select name="doc_id" id="edit_doc_id" class="form-control select2">
                                        <option value="">Select attachment</option>
                                        @foreach ($tenderDocs as $tenderDoc)
                                            <option value="{{ $tenderDoc->doc_id }}"
                                                data-doc-name="{{ $tenderDoc->doc_name }}">
                                                {{ $tenderDoc->doc_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="submitEditSubcatForm">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade tendersModal" id="tenderModal" tabindex="-1" aria-labelledby="tenderModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="tenderModalLabel">Add Table Of Contents</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="tenderForm">
                                @csrf
                                <div class="form-group">
                                    <label for="tenderNo">Tender No</label>
                                    <input type="text" class="form-control" id="tenderNo" name="tender_no"
                                        maxlength="50" value="{{ $tender->tender_no }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="tenderName">Tender Name</label>
                                    <input type="text" class="form-control" id="tenderName" name="tender_name"
                                        maxlength="200" value="{{ $tender->tender_name }}" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="tocCategory">TOC Category</label>
                                    <input type="text" class="form-control" id="tocCategory" name="toc_category"
                                        value="SECT" hidden required>

                                    <input type="text" class="form-control tocHead" id="toc_head" name="toc_head"
                                        value="" required>
                                    <!-- Container for displaying matching suggestions -->
                                    <div id="suggestions" class="list-group" style="display: none;"></div>
                                </div>

                                <div class="row">
                                    <div class="col-5">
                                        <label for="subcat">Attachment</label>
                                        <div class="input-group">
                                            <select name="subattach[]" id="subattach0" class="form-control">
                                                <option value="">Select attachment</option>
                                                @foreach ($tenderDocs as $tenderDoc)
                                                    <option value="{{ $tenderDoc->doc_id }}"
                                                        data-doc-name="{{ $tenderDoc->doc_name }}">
                                                        {{ $tenderDoc->doc_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-5">
                                        <label for="subcat">TOC Subcategories</label>
                                        <input type="text" name="subcat[]" id="subcat0"
                                            class="form-control subcat" />
                                    </div>

                                    <div class="col-2 mt-3">
                                        <button id="addsubcatEdit" class="btn btn-primary" type="button"><i
                                                class="bx bx-plus"></i></button>
                                    </div>
                                </div>
                                <div id="edsubcats">
                                </div>
                                {{-- <div class="col-12" id="subcatdetails">

                                </div> --}}
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="submitTocForm">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOC Section Items Modal -->
            <div class="modal fade" id="TenderTocSecModal" tabindex="-1" aria-labelledby="tenderModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="tenderModalLabel">Edit Section Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="tenderTocItemForm">
                                <div class="form-group">
                                    <label for="tenderNo">Tender No</label>
                                    <input type="text" class="form-control" id="tenderNo" name="tender_no"
                                        maxlength="50" value="{{ $tender->tender_no }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="tenderNo">Tender Name</label>
                                    <input type="text" class="form-control" id="tenderName" name="tender_name"
                                        maxlength="50" value="{{ $tender->tender_name }}" required readonly>
                                </div>
                                <div class="form-group">
                                    <label for="tenderName">TOC category</label>
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" class="form-control" id="item_toc_category"
                                        name="toc_category" value="ITEM" readonly required>
                                    <input type="hidden" class="form-control" id="toc_sec_no" name="toc_section"
                                        maxlength="200" value="" readonly required>
                                    <input type="hidden" class="form-control" id="toc_sec_name" name="tocDescription"
                                        maxlength="200" value="" readonly required>
                                    <input type="text" class="form-control" id="toc_sec_head" name="toc_sec_head"
                                        maxlength="200" value="" required>
                                </div>


                                <div id="edsubcats">
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="submitTocItemForm">Save</button>
                        </div>
                    </div>
                </div>
            </div>



            <div class="modal fade" id="attachmentDocumentModal" tabindex="-1" aria-labelledby="exampleModalLabel"
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


            <!-- Modal -->
            <div class="modal fade" id="edtenderModal" tabindex="-1" aria-labelledby="tenderModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="edtenderModalLabel">Add Proposal</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="edtenderForm" method="POST" action="{{ route('editTender') }}">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="tenderNo">Tender No</label>
                                        <input type="text" class="form-control" id="edtenderNo"
                                            value="{{ $tender->tender_no }}" name="tender_no" maxlength="50" required
                                            readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="tenderName">Tender Name</label>
                                        <input type="text" class="form-control" id="edtenderName"
                                            value="{{ $tender->tender_name }}" name="tender_name" maxlength="200"
                                            required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="client_name">Cedant Name</label>
                                        <input type="text" class="form-control" id="edclient_name"
                                            value="{{ $tender->client_name }}" name="client_name" maxlength="50"
                                            required>
                                    </div>
                                    {{-- <div class="form-group col-md-6">
                                <label for="proposal_category">Category of Proposal</label>
                                <input type="text" class="form-control" id="edproposal_category"
                                    value="{{ $tender->tender_category }}" name="proposal_category" maxlength="50"
                                    required>
                            </div> --}}
                                    <div class="form-group col-md-6">
                                        <label for="proposal_nature">Nature of Proposal</label>
                                        <input type="text" class="form-control" id="edproposal_nature"
                                            value="{{ $tender->tender_nature }}" name="proposal_nature" maxlength="50"
                                            required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="closingDate">Closing Date</label>
                                        <input type="date" class="form-control" id="edclosingDate"
                                            value="{{ $tender->closing_date }}" name="closing_date" required>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label for="tenderDescription">Tender Brief Description</label>
                                        <textarea class="form-control" id="edtenderDescription" value="{{ $tender->tender_description }}"
                                            name="tender_description" maxlength="300" required></textarea>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edsubmitTenderForm">Save</button>
                        </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="footerSettings" tabindex="-1" aria-labelledby="tenderModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="tenderModalLabel">Add Footer Settings
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                        </div>
                        <form id="addFooterSetting">
                            <div class="modal-body">

                                <div class="form-group">
                                    <input type="hidden" class="form-control" id="tenderNo" name="tender_no"
                                        maxlength="50" value="{{ $tender->tender_no }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="footerColor">Separater Color</label>
                                    <br>
                                    <input type="color" id="footerColor" name="footerColor" required />
                                    <input type="text" class="form-control mt-2" id="footerColorText" readonly />
                                </div>


                                <div class="form-group">
                                    <label for="footerContent">Footer Content</label>
                                    <textarea type="text" class="form-control" id="footerContent" name="footerContent" required></textarea>
                                </div>


                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary submitTenderColor"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" id="submitTocItemForm">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <form id="edtenderForm">
                @csrf
                <input type="hidden" name="prospect_id" value="{{ $prospect_id }}">
                <input type="hidden" name="stage_id" value="2">
                <br>
                <h6>Email Data</h6>
                <div class="container-fluid p-4">
                    <div class="form-group">
                        <div class="row">
                            <!-- Left Column - Original Form Fields -->
                            <div class="col-4">
                                <b><label for="email_dated">Letter Dated <span style="color: red;">*</span></label></b>
                                <input type="date" class="form-control" name="email_dated" id="email_dated"
                                    value="{{ isset($approval) ? $approval->email_dated : '' }}" required>
                                <br>

                                <b><label for="commence_year">Treaty Commence Year <span
                                            style="color: red;">*</span></label></b>
                                <input type="text" class="form-control" name="commence_year" id="commence_year"
                                    placeholder="Commence year"
                                    value="{{ isset($approval) ? $approval->commence_year : '' }}" required><br>
                                <br>
                                <b><label for="">Tender Invitation Letter <span
                                            style="color: red;">*</span></label></b>
                                @if (isset($approval) && in_array(Auth::user()->id, $approval->approver_id))
                                    <input type="text" class="form-control" value="Uploaded" readonly>
                                @else
                                    <input type="file" name="tender_invitation_letter" id="tender_invitation_letter"
                                        class="form-control">
                                @endif


                                <b><label for="approver_id">Select Approver <span style="color: red;">*</span></label></b>
                                <select class="form-control select2" name="approver_id[]" id="approver_id" multiple
                                    required>
                                    <option value="">Select Approver</option>
                                    @foreach ($approvers as $approver)
                                        <option value="{{ $approver->id }}"
                                            @if (isset($approval) && is_array($approval->approver_id) && in_array($approver->id, $approval->approver_id)) selected @endif>
                                            {{ $approver->name }} ({{ $approver->email }})
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                            <!-- Right Column - Search Section -->
                            <div class="col-6">
                                <!-- Search Card -->
                                <div class="search-card">
                                    <h5 class="mb-3">
                                        <i class="fas fa-search me-2"></i>
                                        Select Email
                                    </h5>

                                    <form id="searchForm">
                                        <div class="row">
                                            <div class="col-6">
                                                <input type="text" id="searchInput" class="form-control search-input"
                                                    placeholder="type cedant or department" autocomplete="off">
                                                <div class="form-text">
                                                    Try: "Cedant", "Department"
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Email Summary (Persistent) -->
                                <div id="emailSummary"
                                    class="mt-3 p-3 bg-light rounded {{ !isset($approval) ? 'd-none' : '' }}">
                                    <h6 class="mb-2">Selected Emails:</h6>
                                    <div id="mainEmailDisplay" class="mb-2">
                                        @if (isset($approval))
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">Main Email:</span>
                                                <strong>{{ $approval->main_email['name'] }}
                                                    ({{ $approval->main_email['email'] }})</strong>
                                                <span
                                                    class="ms-2 text-muted small">({{ $approval->main_email['type'] }})</span>
                                            </div>
                                        @endif

                                    </div>
                                    <div id="ccEmailsDisplay">
                                        @if (isset($approval) && $approval->cc_emails)
                                            <div class="d-flex align-items-start">
                                                <span class="badge bg-info me-2">CC:</span>
                                                <div>
                                                    @foreach ($approval->cc_emails as $cc)
                                                        <div class="small"> ({{ $cc['email'] }})
                                                            <span class="text-muted">({{ $cc['type'] }})</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Loading -->
                                <div id="loading" class="loading d-none">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted mb-0">Searching...</p>
                                </div>

                                <!-- Results Container -->
                                <div id="resultsContainer" class="d-none">
                                    <div id="resultsHeader" class="result-section-title"></div>
                                    <div id="resultsContent" style="max-height: 400px; overflow-y: auto;"></div>
                                </div>

                                <!-- No Results -->
                                <div id="noResults" class="no-results d-none">
                                    <i class="fas fa-search-minus fa-2x text-muted mb-2"></i>
                                    <h6>No Results Found</h6>
                                    <p class="text-muted mb-0">Please type cedant or department</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        @if (isset($approval) && in_array(Auth::user()->id, $approval->approver_id))
                            <button class="btn btn-success btn-sm me-2" id="sendEmail">Send Email</button>
                            <button class="btn btn-danger btn-sm me-2" id="rejectApproval">Reject</button>
                        @else
                            {{-- @if ($approval->status < 0) --}}
                            <button class="btn btn-success btn-sm" id="submitForApproval">Submit for Approval</button>
                            {{-- @else --}}
                            {{-- <button class="btn btn-success btn-sm" id="submitForApproval">Resubmit for
                                    Approval</button> --}}
                            {{-- @endif --}}
                        @endif


                    </div>

                </div>



            </form>

        </div>



        <!-- Remarks Modal -->
        <div class="modal fade remarks-modal" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="remarksModalLabel">Add Remarks</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="remarksForm">
                            @csrf
                            <input type="hidden" name="approval_id" value="{{ $approval ? $approval->id : '' }}">
                            <input type="hidden" name="action" id="remarksAction" value="">
                            <div class="form-group">
                                <label for="remarks">Remarks <span id="remarksRequired"
                                        class="text-danger"></span></label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="5" maxlength="500"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitRemarks">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script> --}}
        <script src="https://unpkg.com/pdf-lib/dist/pdf-lib.min.js"></script>
        <script>
            /**
             * Capitalizes the first letter of each word in the input value while preserving cursor position
             * @param {Event} event - The input event object
             */
            function capitalizeInput(event) {
                const inputField = event.target;
                const originalValue = inputField.value;
                const cursorPosition = inputField.selectionStart;

                // Skip if the field is empty
                if (!originalValue) return;

                // Capitalize first letter of each word, keeping the rest lowercase
                const capitalizedValue = originalValue
                    .toLowerCase()
                    .replace(/(?:^|\s)\w/g, match => match.toUpperCase());

                // Only update if the value would actually change
                if (capitalizedValue !== originalValue) {
                    inputField.value = capitalizedValue;

                    // Restore cursor position
                    inputField.setSelectionRange(cursorPosition, cursorPosition);
                }
            }

            /**
             * Initialize capitalization listeners on all text inputs and textareas
             */
            function initializeCapitalization() {
                // Select both input and textarea elements that aren't readonly or disabled
                const inputFields = document.querySelectorAll(`
            input[type="text"]:not([readonly]):not([disabled]),
            textarea:not([readonly]):not([disabled])
        `);

                inputFields.forEach(field => {
                    // Remove any existing listeners to prevent duplicates
                    field.removeEventListener('input', capitalizeInput);
                    // Add the new listener
                    field.addEventListener('input', capitalizeInput);
                });
            }

            // Initialize when the DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeCapitalization);
            } else {
                initializeCapitalization();
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Get elements
                const addFooterSettingForm = document.getElementById('addFooterSetting');
                const colorPicker = document.getElementById('footerColor');
                const colorText = document.getElementById('footerColorText');

                // Update text input when color changes
                colorPicker.addEventListener('input', function(e) {
                    colorText.value = e.target.value;
                });

                // Initialize color text
                colorText.value = colorPicker.value;

                // Handle form submission
                if (addFooterSettingForm) { // Check if form exists
                    addFooterSettingForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        fetch('{{ route('save_tendor_color') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content
                                },
                                body: JSON.stringify({
                                    footer_color: colorPicker.value,
                                    tender_no: document.getElementById('tenderNo').value,
                                    footer_content: document.getElementById('footerContent').value
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                $("#footerSettings").modal("hide")
                                if (data.status === 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Tender Document Color',
                                        text: data.message,
                                        confirmButtonText: 'OK'
                                    });

                                } else if (data.status === 203) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Tender Document Color',
                                        text: data.message,
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    alert('Error saving color');
                                }
                            })

                    });
                }
            });

            $("#addFooterSettings").on("click", function() {
                $("#footerSettings").modal("show")
            })
            // Initialize PDF.js worker
            if (typeof pdfjsLib !== 'undefined') {
                pdfjsLib.GlobalWorkerOptions.workerSrc =
                    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
            }

            // Get CSRF token from various possible sources
            function getCsrfToken() {
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) return metaTag.getAttribute('content');

                const cookieToken = document.cookie
                    .split('; ')
                    .find(row => row.startsWith('csrf_token=') || row.startsWith('CSRF-TOKEN='))
                    ?.split('=')[1];
                if (cookieToken) return cookieToken;

                const csrfInput = document.querySelector('input[name="_token"]');
                if (csrfInput) return csrfInput.value;

                return null;
            }

            // Fetch tender data from API
            async function fetchTenderData() {
                // Hardcoded tender values - declared only once

                const tenderNo = $("#print_tender_no").val();
                const tenderName = $("#print_tender_name").val();

                try {

                    const requestData = {
                        print_tender_no: tenderNo,
                        print_tender_name: tenderName
                    };

                    const csrfToken = getCsrfToken();
                    if (!csrfToken) {
                        throw new Error('CSRF token not found. Please refresh the page and try again.');
                    }

                    const response = await fetch('/TenderPrint', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        credentials: 'include',
                        body: JSON.stringify(requestData)
                    });


                    if (!response.ok) {
                        const errorData = await response.text();

                        throw new Error(`Server Error: ${response.status} ${response.statusText}`);
                    }

                    const data = await response.json();

                    if (!data.tender || !data.tenderTocSecs || !data.tenderDocs) {
                        throw new Error('Invalid response format from server');
                    }

                    return data;

                } catch (error) {

                    throw error;
                }
            }


            // Load images from URL
            async function loadImage(url) {
                try {
                    const response = await fetch(url);
                    if (!response.ok) throw new Error(`Failed to load image: ${url}`);
                    const blob = await response.blob();
                    return blob;
                } catch (error) {

                    return null;
                }
            }

            // Create PDF document
            async function createPDF(tenderData) {
                const pdfDoc = await PDFLib.PDFDocument.create();
                const helveticaFont = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);
                const helveticaBold = await pdfDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);

                await createCoverPage(pdfDoc, tenderData, helveticaFont, helveticaBold);
                await createTableOfContents(pdfDoc, tenderData, helveticaFont, helveticaBold);
                await createContentPages(pdfDoc, tenderData, helveticaFont, helveticaBold);
                await addPageNumbers(pdfDoc, helveticaFont);

                return pdfDoc;
            }


            // Store selected emails persistently

            let selectedEmails = {
                mainEmail: {!! isset($approval) && $approval->main_email
                    ? json_encode([
                        'email' => $approval->main_email['email'],
                        'name' => $approval->main_email['name'],
                        'type' => $approval->main_email['type'],
                    ])
                    : 'null' !!},
                ccEmails: {!! isset($approval) && $approval->cc_emails ? json_encode($approval->cc_emails) : '[]' !!}
            };



            function hideAllSections() {
                $('#loading, #resultsContainer, #noResults').addClass('d-none');
            }

            function showLoading() {
                hideAllSections();
                $('#loading').removeClass('d-none');
            }

            function showNoResults() {
                hideAllSections();
                $('#noResults').removeClass('d-none');
            }

            function showResults(data, type, headerText) {
                hideAllSections();

                const displayText = type === 'cedant' ?
                    `<i class="fas fa-building me-2"></i>Cedant Contacts - ${headerText}` :
                    `<i class="fas fa-users me-2"></i>Department Personnel`;

                $('#resultsHeader').html(displayText);

                let html = `
            <div class="email-selection-info mb-3 p-2 bg-light rounded">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Select one main email and multiple CC recipients as needed
                </small>
            </div>
        `;

                data.forEach((person, index) => {
                    const email = person.email;
                    const name = person.name;
                    const role = type === 'cedant' ? person.role : person.position;
                    const isMainSelected = selectedEmails.mainEmail && selectedEmails.mainEmail.email ===
                        email;
                    const isCcSelected = selectedEmails.ccEmails.some(cc => cc.email === email);

                    html += `
                <div class="result-card">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h6 class="mb-1">${name}</h6>
                            <p class="text-primary mb-1 small">${role || 'N/A'}</p>
                            <div class="small text-muted">
                                <i class="fas fa-envelope me-1"></i>${email}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex justify-content-end gap-3">
                                <div class="form-check">
                                    <input class="form-check-input main-email-radio" type="radio" 
                                           name="mainEmail" value="${email}" id="main_${index}"
                                           data-name="${name}" data-type="${type}" 
                                           ${isMainSelected ? 'checked' : ''}>
                                    <label class="form-check-label small text-success fw-bold" for="main_${index}">
                                        Main Email
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input cc-email-checkbox" type="checkbox" 
                                           value="${email}" id="cc_${index}" data-name="${name}" data-type="${type}"
                                           ${isCcSelected ? 'checked' : ''}>
                                    <label class="form-check-label small text-info" for="cc_${index}">
                                        CC
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                });

                $('#resultsContent').html(html);
                $('#resultsContainer').removeClass('d-none');

                updateEmailSummary();
                setupEmailSelection();
            }

            function performSearch(searchTerm) {
                showLoading();
                const data = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    search_term: searchTerm,
                };

                // Include customer_id for cedant searches
                if (searchTerm.toLowerCase() === 'cedant') {
                    const customerId =
                        '{{ $customer_id }}'; // Replace with $('#customerId').val() if using dropdown
                    if (!customerId) {
                        alert('Customer ID is missing.');
                        hideAllSections();
                        return;
                    }
                    data.customer_id = customerId;
                }

                $.ajax({
                    url: '{{ route('search_tender_emails') }}',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.type === 'cedant') {
                            showResults(response.data, 'cedant', response.customer_name ||
                                'Customer Contacts');
                        } else if (response.type === 'department') {
                            showResults(response.data, 'department', 'Department Personnel');
                        } else {
                            showNoResults();
                        }
                    },
                    error: function(xhr) {
                        showNoResults();
                        console.error('Search failed:', xhr.responseText);
                        if (xhr.status === 422) {
                            alert('Invalid input: Please enter "cedant" or "department".');
                        }
                    }
                });
            }

            // Form submission for search
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                const searchTerm = $('#searchInput').val().trim();

                if (!searchTerm) {
                    hideAllSections();
                    return;
                }

                performSearch(searchTerm);
            });

            // Real-time search
            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val().trim();

                if (searchTerm.length < 2) {
                    hideAllSections();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    performSearch(searchTerm);
                }, 500);
            });

            // Clear results when input is empty
            $('#searchInput').on('keyup', function() {
                if ($(this).val().trim() === '') {
                    hideAllSections();
                }
            });

            // Email selection functionality
            function setupEmailSelection() {
                $('.main-email-radio').off('change').on('change', function() {
                    const email = $(this).val();
                    const name = $(this).data('name');
                    const type = $(this).data('type');
                    selectedEmails.mainEmail = {
                        email,
                        name,
                        type
                    };
                    updateEmailSummary();
                });

                $('.cc-email-checkbox').off('change').on('change', function() {
                    const email = $(this).val();
                    const name = $(this).data('name');
                    const type = $(this).data('type');
                    if ($(this).is(':checked')) {
                        if (!selectedEmails.ccEmails.some(cc => cc.email === email)) {
                            selectedEmails.ccEmails.push({
                                email,
                                name,
                                type
                            });
                        }
                    } else {
                        selectedEmails.ccEmails = selectedEmails.ccEmails.filter(cc => cc.email !== email);
                    }
                    updateEmailSummary();
                });
            }

            function updateEmailSummary() {
                if (selectedEmails.mainEmail || selectedEmails.ccEmails.length > 0) {
                    $('#emailSummary').removeClass('d-none');

                    if (selectedEmails.mainEmail) {
                        $('#mainEmailDisplay').html(`
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2">Main Email:</span>
                        <strong>${selectedEmails.mainEmail.name} (${selectedEmails.mainEmail.email})</strong>
                        <span class="ms-2 text-muted small">(${selectedEmails.mainEmail.type})</span>
                    </div>
                `);
                    } else {
                        $('#mainEmailDisplay').html('');
                    }

                    if (selectedEmails.ccEmails.length > 0) {
                        let ccHtml =
                            '<div class="d-flex align-items-start"><span class="badge bg-info me-2">CC:</span><div>';
                        selectedEmails.ccEmails.forEach((cc) => {
                            ccHtml +=
                                `<div class="small">${cc.name} (${cc.email}) <span class="text-muted">(${cc.type})</span></div>`;
                        });
                        ccHtml += '</div></div>';
                        $('#ccEmailsDisplay').html(ccHtml);
                    } else {
                        $('#ccEmailsDisplay').html('');
                    }
                } else {
                    $('#emailSummary').addClass('d-none');
                }
            }

            // Clear selected emails
            $('#clearSelections').on('click', function() {
                selectedEmails = {
                    mainEmail: null,
                    ccEmails: []
                };
                updateEmailSummary();
                hideAllSections();
            });

            // Form submission for email data


            window.getSelectedEmails = function() {
                return selectedEmails;
            };


            // Create cover page
            async function createCoverPage(pdfDoc, tenderData, regularFont, boldFont) {
                const page = pdfDoc.addPage([612, 792]);
                const {
                    width,
                    height
                } = page.getSize();

                // Header image
                const headerImageBlob = await loadImage(public_path('images/header.png'));
                if (headerImageBlob) {
                    const headerImageBytes = await headerImageBlob.arrayBuffer();
                    const headerImage = await pdfDoc.embedPng(headerImageBytes);
                    page.drawImage(headerImage, {
                        x: 0,
                        y: height - 200,
                        width: width,
                        height: 200
                    });
                }

                // Tender details
                const margin = 30;
                let yPosition = height - 350;
                const lineHeight = 25;

                const tender = tenderData.tender;
                const details = [
                    tender.tender_no,
                    tender.client_name,
                    tender.tender_nature,
                    tender.tender_category,
                    tender.tender_description,
                    new Date(tender.closing_date).toLocaleDateString()
                ];

                details.forEach(detail => {
                    if (detail) {
                        page.drawText(detail.toString(), {
                            x: margin,
                            y: yPosition,
                            size: 12,
                            font: regularFont
                        });
                        yPosition -= lineHeight;
                    }
                });

                await addFooter(page, boldFont, pdfDoc, 'cover');
            }

            // Create table of contents
            async function createTableOfContents(pdfDoc, tenderData, regularFont, boldFont) {
                const page = pdfDoc.addPage([612, 792]);
                const {
                    width,
                    height
                } = page.getSize();
                const margin = 50; // Moved margin declaration to the top
                const lineHeight = 25;
                let pageNumber = 1;
                let subsectionNumber = 1;
                let yPosition = height - 210;

                // Company logo
                const logoBlob = await loadImage(public_path('logo.png'));
                if (logoBlob) {
                    const logoBytes = await logoBlob.arrayBuffer();
                    const logo = await pdfDoc.embedPng(logoBytes);
                    const logoWidth = width * 0.25;
                    const logoHeight = (logoWidth * logo.height) / logo.width;
                    page.drawImage(logo, {
                        x: width - logoWidth - 50,
                        y: height - logoHeight - 50,
                        width: logoWidth,
                        height: logoHeight
                    });
                }

                // Add "TABLE OF CONTENTS" header
                page.drawText('Table Of Contents', {
                    x: margin,
                    y: height - 150,
                    size: 16,
                    font: boldFont
                });

                // Add horizontal line under header
                // page.drawLine({
                //     start: {
                //         x: 50,
                //         y: height - 170
                //     },
                //     end: {
                //         x: width - 50,
                //         y: height - 170
                //     },
                //     thickness: 1
                // });

                // Define different right margins for section and subsection numbers
                const sectionNumberMargin = 20; // Distance from right edge for section numbers
                const subsectionNumberMargin = 40; // Distance from right edge for subsection numbers (more to the left)


                // Draw TOC entries
                for (const section of tenderData.tenderTocSecs) {
                    if (yPosition < 50) {
                        page = pdfDoc.addPage([612, 792]);
                        yPosition = height - 100;

                        page.drawText('Table Of Contents', {
                            x: margin,
                            y: height - 50,
                            size: 14,
                            font: boldFont
                        });
                        yPosition -= 50;
                    }

                    // Draw section title in all caps
                    page.drawText(section.toc_description.toUpperCase(), {
                        x: margin,
                        y: yPosition,
                        size: 12,
                        font: boldFont
                    });

                    // Draw section page number (further right)
                    page.drawText(pageNumber.toString(), {
                        x: width - margin - sectionNumberMargin,
                        y: yPosition,
                        size: 12,
                        font: regularFont
                    });

                    yPosition -= lineHeight;
                    pageNumber++;
                    subsectionNumber = 1;

                    // Process subsections
                    const subs = tenderData.tendersubs.filter(sub => sub.toc_no === section.toc_no);
                    for (const sub of subs) {
                        if (yPosition < 50) {
                            page = pdfDoc.addPage([612, 792]);
                            yPosition = height - 100;

                            page.drawText('Table Of Contents', {
                                x: margin,
                                y: height - 50,
                                size: 14,
                                font: boldFont
                            });
                            yPosition -= 50;
                        }

                        // Draw subsection with number and indent
                        page.drawText(subsectionNumber + '. ' + sub.subcat_desc, {
                            x: margin + 40,
                            y: yPosition,
                            size: 12,
                            font: regularFont
                        });

                        // Draw subsection page number (more to the left)
                        page.drawText(pageNumber.toString(), {
                            x: width - margin - subsectionNumberMargin,
                            y: yPosition,
                            size: 12,
                            font: regularFont
                        });

                        yPosition -= lineHeight;
                        pageNumber++;
                        subsectionNumber++;
                    }

                    // Add extra space after each section
                    yPosition -= lineHeight / 2;
                }

                // await addFooter(page, boldFont, pdfDoc, 'toc');
            }
            // Create content pages
            // Helper function for contrast color calculation
            function getContrastColor(hexcolor) {
                // Remove the # if present
                const color = hexcolor.replace('#', '');
                const r = parseInt(color.substr(0, 2), 16);
                const g = parseInt(color.substr(2, 2), 16);
                const b = parseInt(color.substr(4, 2), 16);

                // Calculate relative luminance
                const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

                // Return white for dark backgrounds, black for light backgrounds
                return luminance > 0.5 ? PDFLib.rgb(0, 0, 0) : PDFLib.rgb(1, 1, 1);
            }

            async function createContentPages(pdfDoc, tenderData, regularFont, boldFont) {
                try {
                    let logo = null;
                    const logoBlob = await loadImage(public_path('logo.png'));
                    if (logoBlob) {
                        const logoBytes = await logoBlob.arrayBuffer();
                        logo = await pdfDoc.embedPng(logoBytes);
                    }

                    const footerSettings = {
                        footer_color: tenderData.tender.footer_color,
                        footer_content: tenderData.tender.footer_content
                    };

                    // Helper function to wrap text
                    const getWrappedTextLines = (text, font, fontSize, maxWidth) => {
                        const words = text.split(' ');
                        const lines = [];
                        let currentLine = words[0];

                        for (let i = 1; i < words.length; i++) {
                            const word = words[i];
                            const width = font.widthOfTextAtSize(currentLine + ' ' + word, fontSize);

                            if (width < maxWidth) {
                                currentLine += ' ' + word;
                            } else {
                                lines.push(currentLine);
                                currentLine = word;
                            }
                        }
                        lines.push(currentLine);
                        return lines;
                    };

                    // Helper function to add footer sections
                    const addFooterSections = (page, font, pageNumber = null) => {
                        const {
                            width,
                            height
                        } = page.getSize();
                        const footerFontSize = 10;
                        const numberingFontSize = 10;
                        const marginX = 50;
                        const numberingSectionHeight = 30;
                        const footerSpacing = 20; // Space between footer content and numbering section
                        const maxWidth = width - (marginX * 2);

                        // Add numbering section with background color at the bottom
                        // if (footerSettings.footer_color) {
                        //     const color = footerSettings.footer_color.replace('#', '');
                        //     const r = parseInt(color.substr(0, 2), 16) / 255;
                        //     const g = parseInt(color.substr(2, 2), 16) / 255;
                        //     const b = parseInt(color.substr(4, 2), 16) / 255;

                        //     page.drawRectangle({
                        //         x: 0,
                        //         y: 0,
                        //         width: width,
                        //         height: numberingSectionHeight,
                        //         color: PDFLib.rgb(r, g, b)
                        //     });
                        // }

                        // Add page number in numbering section if provided
                        // if (pageNumber !== null) {
                        //     const numberText = `Page ${pageNumber}`;
                        //     const numberWidth = font.widthOfTextAtSize(numberText, numberingFontSize);
                        //     page.drawText(numberText, {
                        //         x: width - marginX - numberWidth,
                        //         y: numberingSectionHeight / 2 - numberingFontSize / 2,
                        //         size: numberingFontSize,
                        //         font: font,
                        //         color: PDFLib.rgb(1, 1, 1) // White text for numbering
                        //     });
                        // }

                        // Calculate and add footer content above numbering section
                        const lines = getWrappedTextLines(footerSettings.footer_content, font, footerFontSize,
                            maxWidth);
                        const lineHeight = footerFontSize * 1.2;
                        const totalContentHeight = lines.length * lineHeight;

                        // Draw each line of footer content
                        lines.forEach((line, index) => {
                            const y = numberingSectionHeight + footerSpacing + (lines.length - 1 - index) *
                                lineHeight;
                            page.drawText(line, {
                                x: marginX,
                                y: y,
                                size: footerFontSize,
                                font: font,
                                color: PDFLib.rgb(0, 0, 0) // Black text for footer content
                            });
                        });

                        // Return total height of both sections for content positioning
                        return numberingSectionHeight + footerSpacing + totalContentHeight;
                    };

                    // Get contrast color for text based on background
                    const textColor = getContrastColor(footerSettings.footer_color);
                    let pageNumber = 1;

                    // Process each main section
                    for (const section of tenderData.tenderTocSecs) {
                        const sectionPage = pdfDoc.addPage([612, 792]);
                        const {
                            width,
                            height
                        } = sectionPage.getSize();

                        // Draw page background if color is specified
                        // if (footerSettings.footer_color) {
                        //     const color = footerSettings.footer_color.replace('#', '');
                        //     const r = parseInt(color.substr(0, 2), 16) / 255;
                        //     const g = parseInt(color.substr(2, 2), 16) / 255;
                        //     const b = parseInt(color.substr(4, 2), 16) / 255;

                        //     sectionPage.drawRectangle({
                        //         x: 0,
                        //         y: 0,
                        //         width,
                        //         height,
                        //         color: PDFLib.rgb(r, g, b)
                        //     });
                        // }

                        // Add footer sections and get total height
                        const totalFooterHeight = addFooterSections(sectionPage, boldFont, pageNumber++);

                        // Section title with contrast color
                        sectionPage.drawText(section.toc_description, {
                            x: 200,
                            y: height / 2,
                            size: 18,
                            font: boldFont,
                            color: textColor
                        });

                        // Get subsections for this section
                        const subs = tenderData.tendersubs.filter(sub => sub.toc_no === section.toc_no);
                        let subsectionNumber = 1;

                        // Process each subsection
                        for (const sub of subs) {
                            const subPage = pdfDoc.addPage([612, 792]);
                            const subPageSize = subPage.getSize();

                            // Draw page background if color is specified
                            if (footerSettings.footer_color) {
                                const color = footerSettings.footer_color.replace('#', '');
                                const r = parseInt(color.substr(0, 2), 16) / 255;
                                const g = parseInt(color.substr(2, 2), 16) / 255;
                                const b = parseInt(color.substr(4, 2), 16) / 255;

                                subPage.drawRectangle({
                                    x: 0,
                                    y: 0,
                                    width: subPageSize.width,
                                    height: subPageSize.height,
                                    color: PDFLib.rgb(r, g, b)
                                });
                            }

                            // Add footer sections and get total height
                            const totalFooterHeight = addFooterSections(subPage, boldFont, pageNumber++);

                            // Subsection numbering and title with contrast color
                            subPage.drawText(`${subsectionNumber}.`, {
                                x: 180,
                                y: height / 2,
                                size: 16,
                                font: boldFont,
                                color: textColor
                            });

                            subPage.drawText(sub.subcat_desc, {
                                x: 200,
                                y: height / 2,
                                size: 16,
                                font: boldFont,
                                color: textColor
                            });

                            // Process documents
                            const docs = tenderData.tenderDocs.filter(doc => doc.doc_id == sub.doc_id);
                            for (const doc of docs) {
                                if (doc.base64) {
                                    try {
                                        const docResponse = await fetch(`/uploads/${doc.base64}`);
                                        console.log(docResponse);
                                        if (!docResponse.ok) throw new Error(`Failed to load document: ${doc.base64}`);

                                        const docBytes = await docResponse.arrayBuffer();
                                        const docPdf = await PDFLib.PDFDocument.load(docBytes);
                                        const docPages = await pdfDoc.copyPages(docPdf, docPdf.getPageIndices());

                                        docPages.forEach(p => {
                                            const page = pdfDoc.addPage(p);
                                            addFooterSections(page, boldFont, pageNumber++);
                                        });
                                    } catch (error) {
                                        console.error('Error processing document:', error);
                                    }
                                }
                            }
                            subsectionNumber++;
                        }
                    }
                } catch (error) {
                    console.error('createContentPages error:', error);
                    throw error;
                }
            }


            // Add page numbers
            async function addPageNumbers(pdfDoc, font) {
                const pages = pdfDoc.getPages();

                // Start from index 2 (third page) since array is 0-based
                for (let i = 2; i < pages.length; i++) {
                    const page = pages[i];
                    const {
                        width,
                        height
                    } = page.getSize();

                    // Draw page number in white, positioned on the left side
                    page.drawText(`${i - 1}`, { // Subtract 2 then add 1 to start from 1
                        x: 40, // Position from left margin
                        y: 20, // Height from bottom
                        size: 10,
                        font: font,
                        color: PDFLib.rgb(0, 0, 0)
                    });
                }
            }

            // Helper function to simulate Laravel's public_path
            function public_path(path) {
                return `/${path.replace(/^\//, '')}`;
            }


            // Handle Submit Remarks
            $('#submitRemarks').on('click', async function() {
                const action = $('#remarksAction').val();
                const remarks = $('#remarks').val();
                const approvalId = $('input[name="approval_id"]').val();

                if (!approvalId && (action === 'send_email' || action === 'reject')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Approval ID is missing.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if ((action === 'send_email' || action === 'reject') && !remarks) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Remarks are required for this action.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                try {
                    $('#submitRemarks').prop('disabled', true);

                    if (action === 'send_email') {
                        $('#remarksModal').modal('hide');
                        handlePdfGeneration(true, remarks, approvalId);
                    } else if (action === 'reject') {
                        $('#remarksModal').modal('hide');
                        const response = await $.ajax({
                            url: "{{ route('tender.rejectApproval') }}",
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                tender_no: document.getElementById('print_tender_no')?.value,
                                remarks: remarks,
                                status: '2'
                            }
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Tender Rejected',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route('treaty.pipeline.view') }}';
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to process action.',
                        confirmButtonText: 'OK'
                    });
                } finally {
                    $('#submitRemarks').prop('disabled', false);
                }
            });

            // Main generation function
            async function handlePdfGeneration(sendEmail = false, remarks = null, approvalId = null) {


                const generateButton = document.getElementById('generatePdfButton');
                const sendEmailButton = document.getElementById('sendEmail');

                try {
                    if (generateButton) generateButton.disabled = true;

                    const tenderData = await fetchTenderData();
                    console.log('Tender Data:', tenderData);

                    const pdfDoc = await createPDF(tenderData);
                    const pdfBytes = await pdfDoc.save();
                    const blob = new Blob([pdfBytes], {
                        type: 'application/pdf'
                    });
                    if (sendEmail == true) {
                        // Convert PDF bytes to base64 for sending to backend
                        const base64Pdf = await new Promise((resolve) => {
                            const reader = new FileReader();
                            reader.onload = () => resolve(reader.result.split(',')[
                                1]); // Remove "data:application/pdf;base64," prefix
                            reader.readAsDataURL(blob);
                        });
                        const payload = {
                            prospect_id: document.querySelector('input[name="prospect_id"]')?.value,
                            stage_id: document.querySelector('input[name="stage_id"]')?.value,
                            tender_no: document.getElementById('print_tender_no')?.value,
                            tender_name: document.getElementById('print_tender_name')?.value,
                            pdf_base64: base64Pdf,
                            email_dated: $('#email_dated').val(),
                            commence_year: $('#commence_year').val(),
                            mainEmail: selectedEmails.mainEmail,
                            ccEmails: selectedEmails.ccEmails,
                            remarks: remarks,
                            status: '1'
                        };
                        if (!payload.prospect_id || !payload.tender_no || !payload.tender_name || !payload.email_dated || !
                            payload.commence_year || !payload.mainEmail) {
                            const requiredFields = {
                                prospect_id: 'Prospect ID',
                                tender_no: 'Tender Number',
                                tender_name: 'Tender Name',
                                email_dated: 'Email Dated',
                                commence_year: 'Commence Year',
                                mainEmail: 'Main Email',
                                remarks: 'Remarks',

                            };
                            const missingFields = Object.keys(requiredFields)
                                .filter(key => !payload[key] || (key === 'mainEmail' && !payload[key]?.email))
                                .map(key => requiredFields[key]);
                            throw new Error(`Missing required fields: ${missingFields.join(', ')}`);
                        }


                        Swal.fire({
                            title: 'Processing Data...',
                            text: 'Please wait while we process the data.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        // Send PDF to backend
                        // for emailing
                        const emailResponse = await fetch('{{ route('send.tender.email') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            body: JSON.stringify(payload)
                        });

                        const emailResult = await emailResponse.json();
                        if (emailResult.status === 200) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Email Sent',
                                text: emailResult.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = '{{ route('treaty.pipeline.view') }}';
                            });
                        } else {
                            throw new Error(emailResult.message || 'Failed to send email');
                        }
                    } else {
                        const blobUrl = URL.createObjectURL(blob);

                        window.open(blobUrl, '_blank');

                        setTimeout(() => {
                            URL.revokeObjectURL(blobUrl);

                        }, 3000);
                    }

                } catch (error) {
                    console.log(`Error generating PDF: ${error.message}`);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to generate PDF or send email.',
                        confirmButtonText: 'OK'
                    });
                } finally {
                    if (generateButton) generateButton.disabled = false;
                    if (sendEmailButton) sendEmailButton.disabled = false;
                }
            }
            $('#previewLetterBtn').on('click', previewLetterFromServer)

            function previewLetterFromServer() {
                const payload = {
                    prospect_id: $('input[name="prospect_id"]').val(),
                    tender_no: $('#print_tender_no').val(),
                    email_dated: $('#email_dated').val(),
                    commence_year: $('#commence_year').val(),
                };
                console.log(payload);

                $.ajax({
                    url: "{{ route('tender.letter.preview') }}",
                    type: "POST",
                    data: JSON.stringify(payload),
                    contentType: "application/json",
                    xhrFields: {
                        responseType: 'blob' // Tell jQuery to expect a blob (PDF)
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(blob) {
                        console.log('done');
                        const blobUrl = URL.createObjectURL(blob);
                        window.open(blobUrl, '_blank');

                        setTimeout(() => {
                            URL.revokeObjectURL(blobUrl);
                        }, 3000);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error previewing letter:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Preview Failed',
                            text: 'Could not generate letter preview.',
                        });
                    }
                });
            }






            async function addFooter(page, boldFont, pdfDoc, pageType = 'default', tenderTocSecs = null) {
                const {
                    width,
                    height
                } = page.getSize();
                const footerHeight = 40;
                const redSectionWidth = 60;

                // Add debug logging

                switch (pageType) {
                    case 'subsection':
                        // Check the structure of tenderTocSecs
                        const footerColor = tenderTocSecs?.footer_color;

                        if (footerColor) {
                            // Remove any spaces and ensure proper format
                            const color = footerColor.trim().replace('#', '');

                            try {
                                const r = parseInt(color.substr(0, 2), 16) / 255;
                                const g = parseInt(color.substr(2, 2), 16) / 255;
                                const b = parseInt(color.substr(4, 2), 16) / 255;

                                // Draw the background
                                page.drawRectangle({
                                    x: 0,
                                    y: 0,
                                    width: width,
                                    height: height,
                                    color: PDFLib.rgb(r, g, b),
                                    opacity: 1
                                });
                            } catch {}
                        }
                        // ... rest of the subsection case
                        break;
                    case 'cover':
                        // Default grey and red footer for cover and TOC
                        page.drawRectangle({
                            x: 0,
                            y: 0,
                            width: width - redSectionWidth,
                            height: footerHeight,
                            color: PDFLib.rgb(0xE0 / 255, 0xE0 / 255, 0xE0 / 255)
                        });

                        page.drawRectangle({
                            x: width - redSectionWidth,
                            y: 0,
                            width: redSectionWidth,
                            height: footerHeight,
                            color: PDFLib.rgb(0xED / 255, 0x1C / 255, 0x24 / 255)
                        });

                        page.drawText('Risk & Insurance | Reinsurance | Actuarial | Investments', {
                            x: width / 2 - 150,
                            y: footerHeight / 2 - 6,
                            size: 12,
                            font: boldFont,
                            color: PDFLib.rgb(0, 0, 0)
                        });

                        // Add footer image for cover and TOC
                        try {
                            const footerImageBlob = await loadImage(public_path('images/footer.png'));
                            if (footerImageBlob) {
                                const footerImageBytes = await footerImageBlob.arrayBuffer();
                                const footerImage = await pdfDoc.embedPng(footerImageBytes);
                                page.drawImage(footerImage, {
                                    x: 0,
                                    y: footerHeight,
                                    width: width,
                                    height: 250
                                });
                            }
                        } catch (error) {
                            console.error('Error loading footer image:', error);
                        }
                        break;
                }
            }


            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                const generateButton = document.getElementById('generatePdfButton');
                const sendEmailButton = document.getElementById('sendEmail');
                if (generateButton) {
                    generateButton.addEventListener('click', () => handlePdfGeneration(false));
                }
                // Open remarks modal for Send Email, Reject, or Add Remarks
                function openRemarksModal(action, isRequired) {
                    $('#remarksAction').val(action);
                    // $('#remarksRequired').text(isRequired ? '*' : '(Optional)');
                    $('#remarksRequired').text(isRequired ? '*' :'' );
                    $('#remarks').prop('required', isRequired);
                    $('#remarksModalLabel').text(action === 'send_email' ? 'Add Remarks for Email' : action ===
                        'reject' ? 'Add Remarks for Rejection' : 'Add Remarks');
                    $('#remarks').val(''); // Clear previous remarks
                    $('#remarksModal').modal('show');
                }
                // Handle Reject Approval
                $('#rejectApproval').on('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to reject this tender approval? You will be prompted to enter remarks.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, Proceed',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            openRemarksModal('reject', true); // Remarks required for reject
                        }
                    });
                });
                if (sendEmailButton) {

                    sendEmailButton.addEventListener('click', (e) => {
                        e.preventDefault();
                        openRemarksModal('send_email', false);
                        // handlePdfGeneration(true); // Trigger PDF generation and email sending
                    });
                }
            });

            $(document).ready(function() {

                $("#subattach").on('change', function() {
                    alert()
                })

                $('#subattach0').on('change', function() {
                    var selectedOption = $(this).find('option:selected');
                    var docName = selectedOption.data('doc-name');

                    $('#subcat0').val(docName);
                });


                $('#toc_head').on('input', function() {

                    var details = {
                        query: $(this).val(),
                        tender_no: $("#tenderNo").val(),
                    }


                    if (details.query.length === 0) {
                        $('#suggestions').hide();
                        return;
                    }

                    // Send an AJAX request to search for matching categories
                    $.ajax({
                        url: '{{ route('search.tocCategory') }}', // Laravel route for searching
                        method: 'GET',
                        data: {
                            details: details
                        }, // Send the input value as query
                        success: function(response) {
                            // Clear previous suggestions
                            $('#suggestions').empty();
                            $('.tocHead').css('border-color', '#6b7280');
                            // If there are results, show them
                            if (response.length > 0) {
                                $('#suggestions').show();
                                response.forEach(function(category) {
                                    $('.tocHead').css('border-color', 'red');
                                    // Append each category as a suggestion
                                    $('#suggestions').append(
                                        '<a href="#" class="list-group-item list-group-item-action">' +
                                        category.toc_description + '</a>'
                                    );
                                });
                            } else {
                                // If no results, hide the suggestions
                                $('#suggestions').hide();
                            }
                        }
                    });
                });

                // When a suggestion is clicked, fill the input with the category
                $(document).on('click', '#suggestions .list-group-item', function() {
                    var selectedCategory = $(this).text(); // Get the text of the clicked suggestion
                    $('#toc_head').val(selectedCategory); // Set the input value to the selected category
                    $('#suggestions').hide(); // Hide the suggestions after selection
                });


                // Your JavaScript logic can go here
                // CKEDITOR.replace('toc_description');
                // if (typeof ClassicEditor !== 'undefined') {
                //     ClassicEditor
                //         .create(document.querySelector('#toc_head'))
                //         .then(editor => {
                //             console.log('CKEditor 5 initialized for toc_head');
                //         })
                //         .catch(error => {
                //             console.error('CKEditor 5 Error:', error);
                //         });
                // } else {
                //     console.warn('CKEditor 5 not loaded');
                // }


                $('.modal').on('shown.bs.modal', function() {
                    $('.form-select').select2({
                        dropdownParent: $(this)
                    });
                });

                $('#view_tender').click(function(e) {
                    $('#TenderPrintForm').submit();
                });


                $('#tendertoc_table').on('click', '#toc_sec_items', function() {

                    var value = $(this).val().split('+-');
                    var tocSecNo = value[0];
                    var tochead = value[0];
                    $('#toc_sec_no').val(tocSecNo);
                    $('#toc_sec_name').val(value[1]);
                    $('#toc_sec_head').val($(this).attr('data-toc_head'));

                    var tender_no = '{{ $tender->tender_no }}';
                    var tender_name = '{{ $tender->tender_name }}';

                    // Reset the #edsubcats container before appending new rows
                    $('#edsubcats').empty();

                    $.ajax({
                        url: "{{ route('tenders.getcheckitems') }}",
                        type: 'GET',
                        data: {
                            tender_no: tender_no,
                            tender_name: tender_name,
                            tocSecNo: tocSecNo,
                        },
                        success: function(response) {


                            // Assuming tenderDocs is already defined elsewhere
                            var tenderDocs = {!! json_encode($tenderDocs) !!};
                            var TocSecsChecked = response.TocSecsChecked;
                            // Append label only once
                            $('#edsubcats').append(`<label>TOC Subcategories</label>   <button id="addsubcatEdit" class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus"></i>
                        </button>`);

                            TocSecsChecked.forEach(function(tenderDoc) {

                                var html = '<div class="row mt-2 mx-2">' +
                                    '<div class="col-5">' +
                                    '<input type="hidden" name="subcatid[]" id="subcatid' +
                                    tenderDoc.subcat_id + '" value="' + tenderDoc
                                    .subcat_id + '" class="form-control" />' +
                                    '<input type="text" name="subcat[]" id="subcat' +
                                    tenderDoc.subcat_id + '" value="' + tenderDoc
                                    .subcat_desc + '" class="form-control subcat" />' +
                                    '</div>' +
                                    '<div class="col-5">' +
                                    '<div class="input-group">' +
                                    '<select name="subattach[]" id="subattach' + tenderDoc
                                    .doc_id + '" class="select2 form-control">' +
                                    '<option value="">Select attachment</option>';

                                // Create array of tender docs from PHP to JavaScript
                                var tenderDocs = @json($tenderDocs);

                                // Loop through tender docs in JavaScript
                                tenderDocs.forEach(function(tenderDoci) {
                                    html += '<option value="' + tenderDoci.doc_id +
                                        '" ' +
                                        (tenderDoc.doc_id == tenderDoci.doc_id ?
                                            'selected' : '') +
                                        '>' + tenderDoci.doc_name + '</option>';
                                });

                                html += '</select>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>';

                                $('#edsubcats').append(html);

                                // Initialize Select2 after appending
                                $('#subattach' + tenderDoc.doc_id).select2();

                            });

                            // Show the modal after data has been appended
                            $('#TenderTocSecModal').modal('show');

                        },
                        error: function(xhr, status, error) {

                        }
                    });
                });



                var counter = 0;
                $('body').on('click', '#addsubcat', function() {
                    if (counter > 0) {
                        var subcat = $('#subcat' + counter).val()
                    } else if (counter == 0) {
                        var subcat = $('#subcat0').val()
                    }

                    if (subcat == '') {
                        Swal.fire({
                            icon: 'warning',
                            text: 'Please fill all details'
                        });
                    } else {
                        counter = counter + 1;
                        $('#subcatdetails').append(
                            `<div class="row mt-2 mx-0">
                       
                        <div class="col-6">
                            <div class="input-group">
                                <select name="subattach[]" id="subattach${counter}" class="select2 form-control">
                                    <option value="">Select attachment</option>
                                    @foreach ($tenderDocs as $tenderDoc)
                                        <option value="{{ $tenderDoc->doc_id }}"> {{ $tenderDoc->doc_name }} </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-danger removesubcat" type="button"><i class="fa fa-minus"></i> </button>
                            </div>
                        </div>
                         <div class="col-6">
                                <input type="text" name="subcat[]" id="subcat${counter}" class="form-control subcat" />
                        </div>
                    </div>`
                        );
                    }

                });

                $('body').on('change', 'select[name="subattach[]"]', function() {
                    var docName = $(this).find('option:selected').text(); // Get the selected document name
                    var inputId = $(this).attr('id').replace('subattach',
                        'subcat'); // Get the corresponding input field ID
                    $('#' + inputId).val(docName); // Set the input field value to the document name
                });

                // Remove the subcategory row
                $('body').on('click', '.removesubcat', function() {
                    $(this).closest('.row').remove();
                });


                // edit modals
                var counter = 0;

                $('body').on('click', '#addsubcatEdit', function() {
                alert('in')

                    counter = counter + 1;
                    $('#edsubcats').append(
                        `<div class="row mt-2 mx-0">
                       
                        <div class="col-6">
                            <div class="input-group">
                                <select name="subattach[]" id="subattach${counter}" class="select2 form-control">
                                    <option value="">Select attachment</option>
                                    @foreach ($tenderDocs as $tenderDoc)
                                        <option value="{{ $tenderDoc->doc_id }}"> {{ $tenderDoc->doc_name }} </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-danger removesubcat" type="button"><i class="fa fa-minus"></i> </button>
                            </div>
                        </div>
                         <div class="col-6">
                                <input type="text" name="subcat[]" id="subcat${counter}" class="form-control subcat" />
                        </div>
                    </div>`
                    );
                });


                $('body').on('change', 'select[name="subattach[]"]', function() {
                    var docName = $(this).find('option:selected').text(); // Get the selected document name
                    var inputId = $(this).attr('id').replace('subattach',
                        'subcat'); // Get the corresponding input field ID
                    $('#' + inputId).val(docName); // Set the input field value to the document name
                });

                // Remove the subcategory row
                $('body').on('click', '.removesubcat', function() {
                    $(this).closest('.row').remove();
                });



                $('#subcatdetails').delegate('.removesubcat', 'click', function() {
                    $(this).parent().parent().parent().remove();
                });


                $('#submitTocForm').click(function() {
                    console.log('submitTocForm clicked');
                    // Serialize the form data
                    // for (instance in CKEDITOR.instances) {
                    //     CKEDITOR.instances[instance].updateElement();
                    // }

                    var formData = $('#tenderForm').serialize();

                    $.ajax({
                        url: "{{ route('tender.Tocadd') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {

                            if (response.status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Tender TOC',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                            if (response.status === 203) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Tender TOC',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                            $('#tenderModal').modal('hide');
                            location.reload();
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            // Handle errors here
                            alert('Failed to save tender: ' + textStatus);
                        }
                    });
                });

                $('#submitTocItemForm').click(function() {
                    // Serialize the form data
                    var formData = $('#tenderTocItemForm').serialize();

                    $.ajax({
                        url: "{{ route('tender.TocItemadd') }}", // Ensure this route matches the one in your web.php
                        type: 'POST',
                        data: formData, // Use serialized form data
                        success: function(response) {

                            if (response.status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Tender Item TOC',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                            if (response.status === 203) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Tender TOC',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }

                            $('#TenderTocSecModal').modal('hide');
                            location.reload(); // Reload the page to reflect the new changes
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            // Handle errors here
                            alert('Failed to save tender: ' + textStatus);
                        }
                    });
                });

                $('body').on('change', '.subcat', function() {
                    let sub = $(this).val()
                    let id = $(this).attr('id')
                    let id_length = id.length
                    let rowID = id.slice(6, id_length)


                    $.ajax({
                        type: "GET",
                        data: {
                            'subcat': sub
                        },
                        url: "{{ route('get_subcat_doc') }}",
                        success: function(resp) {
                            if (resp.status == 1) {
                                $("#subattach" + rowID).val(resp.docid).trigger('change');
                            }
                        }
                    })

                })

                $(document).on('click', '.view-attachment', function() {
                    //alert(1)
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

                // Initialize Select2 in the edit modal when it opens
                $('#editSubcatModal').on('shown.bs.modal', function() {
                    $('#edit_doc_id').select2({
                        dropdownParent: $('#editSubcatModal'),
                        placeholder: 'Select attachment',
                        allowClear: true
                    });
                });

                // Populate modal when Edit button is clicked
                $(document).on('click', '.edit-subcat-btn', function() {
                    const subcatId = $(this).data('subcat-id');
                    alert('ireirir')
                    const subcatDesc = $(this).data('subcat-desc');
                    const docId = $(this).data('doc-id');
                    const tocNo = $(this).data('toc-no');

                    // Fill modal fields
                    $('#edit_subcat_id').val(subcatId);
                    $('#edit_subcat_desc').val(subcatDesc);
                    $('#edit_toc_no').val(tocNo);
                    $('#edit_doc_id').val(docId).trigger('change');

                    // Update subcat_desc when doc_id changes
                    $('#edit_doc_id').off('change').on('change', function() {
                        const selectedOption = $(this).find('option:selected');
                        const docName = selectedOption.data('doc-name') || '';
                        if (docName) {
                            $('#edit_subcat_desc').val(docName);
                        }
                    });
                });

                // Handle form submission
                $('#submitEditSubcatForm').on('click', function() {
                    const formData = $('#editSubcatForm').serialize();

                    $.ajax({
                        url: "{{ route('tender.editSubcat') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Subcategory Updated',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Update table row
                                    const subcatId = $('#edit_subcat_id').val();
                                    const row = $(
                                        `#attachments-table tr[data-subcat-id="${subcatId}"]`
                                    );
                                    row.find('.subcat-desc').text($('#edit_subcat_desc')
                                        .val());

                                    // Update View Attachment button
                                    const docId = $('#edit_doc_id').val();
                                    const viewButtonCell = row.find('td:last-child');
                                    if (docId) {
                                        viewButtonCell.find('.text-muted').remove();
                                        if (!viewButtonCell.find('.btn-outline-info')
                                            .length) {
                                            viewButtonCell.prepend(
                                                `<button type="button" class="btn btn-outline-info btn-sm" onclick="window.open('{{ url('document/view') }}/${docId}', '_blank')">
                                        <i class="fa fa-file"></i> View Attachment
                                    </button>`
                                            );
                                        } else {
                                            viewButtonCell.find('.btn-outline-info').attr(
                                                'onclick',
                                                `window.open('{{ url('document/view') }}/${docId}', '_blank')`
                                            );
                                        }
                                    } else {
                                        viewButtonCell.find('.btn-outline-info').remove();
                                        if (!viewButtonCell.find('.text-muted').length) {
                                            viewButtonCell.prepend(
                                                '<span class="text-muted">No attachment</span>'
                                            );
                                        }
                                    }

                                    $('#editSubcatModal').modal('hide');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update subcategory: ' + (xhr.responseJSON
                                    ?.message || 'Server error'),
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });

                // Populate TOC section modal when Edit button is clicked
                $(document).on('click', '.edit-toc-btn', function() {
                    const tocId = $(this).data('toc-id');
                    const tocHead = $(this).data('toc-head');
                    const tocNo = $(this).data('toc-no');

                    // Fill modal fields
                    $('#edit_toc_id').val(tocId);
                    $('#edit_toc_head').val(tocHead);
                    $('#edit_toc_no').val(tocNo);
                });

                // Initialize Select2 for approver dropdown
                $('#approver_id').select2({
                    placeholder: 'Select Approver',
                    allowClear: true
                });

                // Handle Submit for Approval
                $('#submitForApproval').on('click', function(e) {
                    e.preventDefault();
                    handleSubmitForApproval();
                });

                async function handleSubmitForApproval() {
                    try {
                        $('#submitForApproval').prop('disabled', true);

                        const tenderData = await fetchTenderData(); // Existing function
                        const pdfDoc = await createPDF(tenderData); // Existing function
                        const pdfBytes = await pdfDoc.save();
                        const blob = new Blob([pdfBytes], {
                            type: 'application/pdf'
                        });
                        const base64Pdf = await new Promise((resolve) => {
                            const reader = new FileReader();
                            reader.onload = () => resolve(reader.result.split(',')[1]);
                            reader.readAsDataURL(blob);
                        });

                        function getFileData(file) {
                            return new Promise((resolve, reject) => {
                                const reader = new FileReader();
                                reader.readAsDataURL(file);
                                reader.onload = () => {
                                    resolve({
                                        file_attachment: reader.result,
                                        file_name: file.name,
                                        file_type: file.type
                                    });
                                };
                                reader.onerror = error => reject(error);
                            });
                        }

                        // Usage
                        const fileInput = document.getElementById('tender_invitation_letter');

                        if (!fileInput || !fileInput.files[0]) {
                            alert("Please select a file!");
                            return;
                        }
                        const file = fileInput.files[0];
                        const fileData = await getFileData(file);


                        if (!fileInput || !fileInput.files[0]) {
                            alert("Please select a file!");
                            return;
                        }
                        const payload = {
                            approval_id:'{{$approval->id ?? ''}}',
                            tender_id: '{{ $tender->id }}',
                            tender_no: '{{ $tender->tender_no }}',
                            stage_id: $('input[name="stage_id"]').val(),
                            email_dated: $('#email_dated').val(),
                            commence_year: $('#commence_year').val(),
                            mainEmail: selectedEmails.mainEmail,
                            tender_invitation_letter: fileData,
                            ccEmails: selectedEmails.ccEmails || [],
                            pdf_base64: base64Pdf,
                            approver_id: $('#approver_id').val(),

                        };


                        if (!payload.tender_id || !payload.stage_id || !payload.email_dated ||
                            !payload.commence_year || !payload.mainEmail.email || !payload.approver_id || !payload
                            .tender_no) {
                            const requiredFields = {
                                tender_id: 'Tender ID',
                                tender_no: 'Tender No',
                                stage_id: 'Stage ID',
                                email_dated: 'Email Dated',
                                commence_year: 'Commence Year',
                                mainEmail: 'Main Email',
                                approver_id: 'Approver',
                                // tender_invitation_letter: 'Tender invitation letter'
                            };
                            const missingFields = Object.keys(requiredFields)
                                .filter(key => !payload[key] || (key === 'mainEmail' && !payload[key].email))
                                .map(key => requiredFields[key]);
                            throw new Error(`Missing required fields: ${missingFields.join(', ')}`);
                        }
                        Swal.fire({
                            title: 'Processing Data...',
                            text: 'Please wait while we process the data.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const response = await $.ajax({
                            url: "{{ route('tender.submitForApproval') }}",
                            type: 'POST',
                            data: JSON.stringify(payload),
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Submitted for Approval',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route('treaty.pipeline.view') }}';
                        });
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to submit for approval.',
                            confirmButtonText: 'OK'
                        });
                    } finally {
                        $('#submitForApproval').prop('disabled', false);
                    }
                }


                // Handle TOC section form submission
                $('#submitEditTocForm').on('click', function() {
                    const formData = $('#editTocForm').serialize();

                    $.ajax({
                        url: "{{ route('tender.editToc') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status === 200) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Section Updated',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Update table row
                                    location.reload();

                                    $('#editTocModal').modal('hide');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update section: ' + (xhr.responseJSON
                                    ?.message || 'Server error'),
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });





            });
        </script>
    @endpush
