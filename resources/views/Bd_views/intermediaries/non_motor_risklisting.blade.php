@extends('layouts.intermediaries.base')

@section('content')
    <nav class="page-title fw-semibold fs-18 mb-0 bg-white mt-2 mb-2 p-1" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Client</li>
            <li class="breadcrumb-item"><a href="{{ route('agent.view.client', ['client' => $customer->global_customer_id,'serviceflag' => 'N']) }}" id="to-customer">{{ Str::ucfirst(strtolower($customer->full_name)) }}</a></li>
            <li class="breadcrumb-item">Cover</li>
            <li class="breadcrumb-item"><a href="{{route('view.policies',['policy_no'=>$policy_dtl->policy_no])}}" id="to-cover">{{$policy_dtl->policy_no}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cover Details</li>
            @if($policy_dtl->trans_type=="POL")
            <li class="breadcrumb-item active" aria-current="page">New Business</li>
            @endif
            @if($policy_dtl->trans_type=="EXT")
            <li class="breadcrumb-item active" aria-current="page">Extra Endorsement</li>
            @endif
            @if($policy_dtl->trans_type=="RFN")
            <li class="breadcrumb-item active" aria-current="page">Refund Endorsement</li>
            @endif
            @if($policy_dtl->trans_type=="REN")
            <li class="breadcrumb-item active" aria-current="page">Policy Renewal</li>
            @endif
        </ol>
    </nav>
    <div class="row">
        <div class="card col-md-12">
        <div class="card-header d-flex flex-wrap align-items-center gap-2">
    @if($policy_dtl->verified == 'A')
        @if(!$debitted)
            <a id="debit-pol" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">
                <i class="fa-solid fa-plus"></i> Debit Transaction
            </a>
        @endif
    @else
        @if($class->motor_flag == "Y")
            <a href="{{route('add.vehicle',['endorsement_no'=>$policy_dtl->endorsement_no])}}" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">
                <i class="fa-solid fa-plus"></i> Add/Upload Vehicle(s)
            </a>
        @else
            <a href="{{ route('add.insured.items',['endorsement_no'=>$policy_dtl->endorsement_no]) }}" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">
                <i class="fa-solid fa-plus"></i> Add Insured Items
            </a>
        @endif

        <button class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light" id="schedule-details" data-bs-toggle="modal" data-bs-target="#schedulesModal">
            <i class="fa-solid fa-plus"></i> Add Cover Details
        </button>
        
        <button class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light" id="attachments" data-bs-toggle="modal" data-bs-target="#attachments-modal">
            <i class="fa-solid fa-paperclip"></i> Add/View Attachments
        </button>

        <div class="dropdown">
            <button class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light dropdown-toggle" type="button" id="invoiceDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-plus"></i> Invoicing
            </button>
            <ul class="dropdown-menu" aria-labelledby="invoiceDropdown">
                <li><a class="dropdown-item" id="invoice" href="#">Standard Calculator</a></li>
                <li><a class="dropdown-item" href="{{route('detailedcalculator',['endt_renewal_no'=>$policy_dtl->endorsement_no])}}">Detail Calculator</a></li>
            </ul>
        </div>

        <button class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light" id="prem_payment">
            <i class="fa-solid fa-plus"></i> Premium Payment
        </button>

        @if(isset($approvals) && $approvals !== null)
            @if($approvals->send_to == auth()->user()->id)
                <button class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#approvalModal">
                    <i class="fa-solid fa-circle-check"></i> Verify Details
                </button>
            @endif
        @else
            <button class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light" id="escalatebtn">
                <i class="fa-solid fa-circle-check"></i> Verify Details
            </button>
        @endif
    @endif
</div>

        </div>
    </div>
    <div class="row">
            <div class="card p-4">
                <div class="row mb-1  bg-light">
                   <div class="col-md-3">
                        <strong>Client Name</strong>
                    </div>
                    <div class="col-md-3">
                        {{ Str::ucfirst(strtolower($customer->full_name)) }}
                    </div>
                    <div class="col-md-3">
                        <strong>Cover Period</strong>
                    </div>
                    <div class="col-md-3">
                        {{formatDate($policy_dtl->period_from)}} to {{formatDate($policy_dtl->period_to)}}
                    </div>
                    
                   
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <strong>Class Category</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $classcateg->description}}
                    </div>
                    <div class="col-md-3">
                        <strong>Class</strong>
                    </div>
                    <div class="col-md-3">
                        {{ $class->class_description}}
                    </div>
                  
                </div>
                
                   
                    <div class="row mb-1 bg-light">
                        <div class="col-md-3">
                            <strong>Basic Premium</strong>
                        </div>
                        <div class="col-md-3">
                            {{ number_format($policy_sum->total_annual_premium,2)}}
                        </div>
                        <div class="col-md-3">
                            <strong>Endorse Amount</strong>
                        </div>
                        <div class="col-md-3">
                            {{ number_format($policy_sum->endorse_amount,2)}}
                        </div>
                    </div>
                    <div class="row mb-1 bg-light">
                        <div class="col-md-3">
                            <strong>Renewal Premium</strong>
                        </div>
                        <div class="col-md-3">
                            {{ number_format($policy_sum->total_renewal_premium,2)}}
                        </div>
                        <div class="col-md-3">
                            <strong>Total Premium</strong>
                        </div>
                        <div class="col-md-3">
                            {{ number_format($policy_sum->total_annual_premium,2)}}
                        </div>
                    </div>
                    <div class="row mb-1 bg-light">
                        
                        <div class="col-md-3">
                            <strong>Verification Status</strong>
                        </div>
                        <div class="col-md-3">
                            @switch($policy_dtl->verified)
                                @case(null)
                                @case('P')
                                <span class="badge bg-primary"> Pending</span>
                                    @break
                                @case('A')
                                    <span class="badge bg-success"> Approved</span>
                                    @break
                                @case('R')
                                    
                                    <span class="badge bg-danger"> Rejected</span>
                                    @break
                                @default
                                    
                            @endswitch
                        </div>
                    </div>
                    
                
               
            </div>
    </div>
 <nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <button class="nav-link active" id="insured_items" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Insured Items</button>
    <button class="nav-link" id="nav-cover_summary-tab" data-bs-toggle="tab" data-bs-target="#nav-cover_summary" type="button" role="tab" aria-controls="nav-cover_summary" aria-selected="false">Risk Summary</button>
    <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Attachments</button>
    <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-paymentplan" type="button" role="tab" aria-controls="nav-paymentplan" aria-selected="false">Payment Plan</button>
    <button class="nav-link" id="nav-documents-tab" data-bs-toggle="tab" data-bs-target="#nav-documents" type="button" role="tab" aria-controls="nav-documents" aria-selected="false">Policy Slips</button>
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="insured_items">
     <div class="card ">
        <div class="card-body">
            <div class="table-responsive">
                @if($class->motor_flag == "Y")
                    <table class="table table-striped  table-hover" id="risks_data_table" width="100%">
                        <thead class="table-primary">
                            <tr>
                                <th>Reg No</th>
                                <th>Make</th>   
                                <th>Model</th>
                                <th>Premium</th>
                                <th>Action</th>            
                            </tr>
                        </thead>
                    </table>
                @else
                    <table class="table text-nowrap table-striped table-hover" id="schedules-table">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">Name/Title</th>
                                <th scope="col">Details</th>
                                <th scope="col">Sum Insured</th>
                                <!-- <th scope="col">Premium</th>  -->
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                    </table>
                @endif
            </div>
        </div>
    </div>
  </div>
  <!--  -->
  <div class="tab-pane fade" id="nav-cover_summary" role="tabpanel" aria-labelledby="nav-cover_summary-tab">
        <div class="card">
            <div class="card-body">

                <!-- Accordion Structure for sections -->
                <div class="accordion" id="accordionSections">

                    <!-- Cover Summary Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCoverSummary">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCoverSummary" aria-expanded="true" aria-controls="collapseCoverSummary">
                                Cover Summary
                            </button>
                        </h2>
                        <div id="collapseCoverSummary" class="accordion-collapse collapse show" aria-labelledby="headingCoverSummary" data-bs-parent="#accordionSections">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table text-nowrap table-striped table-hover" id="cover_summary">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Name/Title</th>
                                                <th scope="col">Details</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clauses Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingClauses">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseClauses" aria-expanded="false" aria-controls="collapseClauses">
                                Clauses
                            </button>
                        </h2>
                        <div id="collapseClauses" class="accordion-collapse collapse" aria-labelledby="headingClauses" data-bs-parent="#accordionSections">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table text-nowrap table-striped table-hover" id="clauses-table">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Name/Title</th>
                                                <th scope="col">Details</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Extension Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingExtensions">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExtensions" aria-expanded="false" aria-controls="collapseExtensions">
                                Extensions
                            </button>
                        </h2>
                        <div id="collapseExtensions" class="accordion-collapse collapse" aria-labelledby="headingExtensions" data-bs-parent="#accordionSections">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table text-nowrap table-striped table-hover" id="extension-table">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Name/Title</th>
                                                <th scope="col">Details</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Limits Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingLimits">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLimits" aria-expanded="false" aria-controls="collapseLimits">
                                Limits
                            </button>
                        </h2>
                        <div id="collapseLimits" class="accordion-collapse collapse" aria-labelledby="headingLimits" data-bs-parent="#accordionSections">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table text-nowrap table-striped table-hover" id="limits-table">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Name/Title</th>
                                                <th scope="col">Details</th>
                                                <th scope="col">Schedule Value</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments Section -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingAttachments">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAttachments" aria-expanded="false" aria-controls="collapseAttachments">
                                Attachments
                            </button>
                        </h2>
                        <div id="collapseAttachments" class="accordion-collapse collapse" aria-labelledby="headingAttachments" data-bs-parent="#accordionSections">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table text-nowrap table-striped table-hover" id="attachments-table">
                                        <thead class="table-primary">
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Title</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

  <!--  -->

  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                     <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table text-nowrap table-striped table-hover" id="attachments-table">
                                    <thead class="table-primary">
                                        <tr>
                                            <th scope="col">id</th>
                                            <th scope="col">Title</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
  </div> 
  <div class="tab-pane fade" id="nav-paymentplan" role="tabpanel" aria-labelledby="nav-paymentplan-tab">
                     <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table text-nowrap table-striped table-hover" id="paymentplan-table">
                                    <thead class="table-primary">
                                        <tr>
                                            <th scope="col">Plan</th>
                                            <th scope="col">Total Amount</th>
                                            <th scope="col">Created by</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
  </div> 
  <div class="tab-pane fade" id="nav-documents" role="tabpanel" aria-labelledby="nav-documents-tab">
                     <div class="card">
                     <div class="card-body">
                         @if($debitted)
                            <div class="row">
                                <!-- Share buttons on the first row -->
                                <div class="col-md-3 mb-2">
                                
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary w-100" onclick="confirminsurerShare()">
                                        <span class="fa fa-share"></span> Share Documents with Insurer
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary w-100" onclick="confirmShare()">
                                        <span class="fa fa-share"></span> Share Documents with Client
                                    </a>

                                </div>
                            </div>
                            
                            <div class="row">
                            
                                    <!-- Document-related buttons on the second row -->
                                    <div class="col-md-2 mb-2">
                                        <a href="{{ route('generateDebit', ['policy_no' => $policy_dtl->endorsement_no]) }}" class="btn btn-sm border border-warning w-100" target="_blank">
                                            <span class="fa fa-file-pdf"></span> Debit Note
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <a href="{{ route('generaterisknote', ['policy_no' => $policy_dtl->endorsement_no]) }}" class="btn btn-sm border border-warning w-100" target="_blank">
                                            <span class="fa fa-file-pdf"></span> Risk Note
                                        </a>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <a href="{{ route('generateinvoice', ['policy_no' => $policy_dtl->endorsement_no]) }}" class="btn btn-sm border border-warning w-100" target="_blank">
                                            <span class="fa fa-file-pdf"></span> Invoice
                                        </a>
                                    </div>
                            
                            </div>
                        @else
                        <p>Transaction Not Debitted</p>
                        @endif
                    </div>

                    </div>
  </div>
</div>
    <!--Attachments Modal -->
    <div class="modal fade" id="attachments-modal" data-bs-backdrop="static" data-bs-keyboard="false" 
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('cover.save_attachment')}}" id="attachmentsForm">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $policy_dtl->endorsement_no}}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Attachments</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">File</label>
                                <input type="file" class="form-control" id="file" name="file" accept=".pdf, .doc, .docx,.png,.jpg" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="attachments-save-btn" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
     <!--Attachments Modal -->
     <div class="modal fade" id="invoice-modal" data-bs-backdrop="static"  data-bs-keyboard="false" 
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <form method="POST" action="{{ route('policy.savepremiums')}}" id="attachmentsForm">
                @csrf
                <input type="hidden" name="endorsement_no" value="{{ $policy_dtl->endorsement_no }}">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-center w-100" id="staticBackdropLabel">Premium Computation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Premium Details Card -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Premium Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="sum_insured_amt" class="form-label">Sum Insured</label>
                                        <input type="text" class="form-control" id="sum_insured_amt" name="sum_insured_amt" value="{{$policy_sum->total_sum_insured}}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rate_basis" class="form-label required-label">Basis</label>
                                        <select name="rate_basis" id="rate_basis" class="form-select" required>
                                            <option value="" selected disabled>--Select Basis--</option>
                                            <option value="PCT">Percentage</option>
                                            <option value="MLE">Mile</option>
                                            <option value="NRA">No Rate</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 prem_rate_div" style="display:none;">
                                        <label for="prem_rate" class="form-label">Premium Rate</label>
                                        <input type="number" class="form-control" id="prem_rate" name="prem_rate">
                                    </div>
                                    <div class="mb-3 basic_prem_div">
                                        <label for="basic_prem" class="form-label">Basic Premium</label>
                                        <input type="text" class="form-control" id="basic_prem" name="basic_prem" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Levies Details Card -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Levies Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 stmp_div">
                                        <label for="stamp_duty_amt" class="form-label">Stamp Duty</label>
                                        <input type="text" class="form-control" id="stamp_duty_amt" name="stamp_duty_amt" required>
                                    </div>
                                    <div class="mb-3 lev_div">
                                        <label for="tlevy" class="form-label">Training Levy</label>
                                        <input type="text" class="form-control" id="tlevy" name="tlevy" required>
                                    </div>
                                    <div class="mb-3 phcf_prem_div">
                                        <label for="phcf_prem" class="form-label">Phcfund</label>
                                        <input type="text" class="form-control" id="phcf_prem" name="phcf_prem" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Details Card -->
                        <div class="col-md-6">
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Commission Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 g_comm_div">
                                            <label for="g_comm" class="form-label">Gross Commission</label>
                                            <input type="text" class="form-control" id="g_comm" name="g_comm" required>
                                        </div>
                                        <div class="col-md-6 whttax_div">
                                            <label for="whttax" class="form-label">Withholding Tax (%)</label>
                                            <input type="text" class="form-control" id="whttax" name="whttax" required>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6 net_comm_div">
                                            <label for="net_comm" class="form-label">Nett Commission </label>
                                            <input type="text" class="form-control" id="net_comm" name="net_comm" required>
                                        </div>
                                        <div class="col-md-6 wht_amt_div">
                                            <label for="wht_amt" class="form-label">With Holding Tax Amount</label>
                                            <input type="text" class="form-control" id="wht_amt" name="wht_amt" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- VAT Details Card -->
                        <div class="col-md-6">
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Value Added Tax Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 vatamt_div">
                                            <label for="vatamt" class="form-label">Commission VAT (%)</label>
                                            <input type="text" class="form-control" value="0" id="vatamt" name="vatamt" required>
                                        </div>
                                        <div class="col-md-6 vatamt_div">
                                            <label for="vatamt" class="form-label">Commission VAT Amount</label>
                                            <input type="text" class="form-control" id="vatamt" value="0" name="vatamt" required>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6 vatamt_div">
                                            <label for="vatamt" class="form-label">Premium VAT (%)</label>
                                            <input type="text" class="form-control" value="0" id="vatamt" name="vatamt" required>
                                        </div>
                                        <div class="col-md-6 vatamt_div">
                                            <label for="vatamt" class="form-label">Premium VAT Amount</label>
                                            <input type="text" class="form-control" id="vatamt" value="0" name="vatamt" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payable Amount Card -->
                        <div class="col-md-12">
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Payable Amount</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 totalprem_div">
                                            <label for="totalprem" class="form-label">By Client</label>
                                            <input type="text" class="form-control" id="totalprem" name="totalprem" required>
                                        </div>
                                        <div class="col-md-6 insurer_payable_div">
                                            <label for="insurer_payable" class="form-label">To Insurer</label>
                                            <input type="text" class="form-control" id="insurer_payable" name="insurer_payable" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="attachments-save-btn" class="btn btn-outline-primary btn-sm">Submit</button>
                </div>
            </form>


            </div>
        </div>
    </div>
    <!--  -->
    <div class="modal fade" id="premcollection-modal" data-bs-backdrop="static"  data-bs-keyboard="false" 
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{route('save.premplan')}}" id="premcollectionForm">
                    @csrf
                    <input type="hidden" name="endorsement_no" value="{{ $policy_dtl->endorsement_no }}">
                    @if(isset($debitrec) && $debitrec->total_premium)
                    <input type="hidden" name="total_amount" id="total_amount" value="{{ $debitrec->total_premium }}">
                    @endif

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-center w-100" id="staticBackdropLabel">Premium Collection</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group col-md-4">
                            <label for="payment-plan">Payment Plan</label>
                            <select name="payment_plan" id="payment-plan" class="form-select" required>
                                <option value="" selected disabled>--Select Payment Plan--</option>
                                <option value="cash_and_carry">Cash and Carry</option>
                                <option value="credit_period">Credit Period</option>
                                <option value="installments">Installments</option>
                                <option value="ipf">Insurance Premium Financing</option>
                            </select>
                        </div>

                        <div id="payment-plan-details" class="mt-3" style="display:none;">
                            <!-- Credit Period Details -->
                            <div id="credit-period-details" style="display:none;">
                                <label for="credit-period">Credit Period</label>
                                <select name="credit_period" id="credit-period" class="form-select">
                                    <option value="30">30 Days</option>
                                    <option value="60">60 Days</option>
                                    <option value="90">90 Days</option>
                                </select>
                            </div>

                            <!-- Installment Details -->
                            <div id="installment-details" style="display:none;">
                                <label>Installments</label>
                                <div id="installment-container">
                                <div class="installment-item mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="installments-number-1">No of Installments</label>
                                            <select name="installments_number[]" id="installments-number-1" class="form-select" required>
                                                <option value="" selected disabled>--Select Installment--</option>
                                                <option value="1">1st Installment</option>
                                                <option value="2">2nd Installment</option>
                                                <option value="3">3rd Installment</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="installment-percentage-1">Percentage</label>
                                            <input type="text" name="installment_percentage[]" id="installment-percentage-1" class="form-control installment-percentage" placeholder="e.g., 20%" required>
                                        </div>
                                        <div class="col-md-2 mt-2">
                                            <label for="installment-amount-1">Amount</label>
                                            <input type="text" name="installment_amount[]" id="installment-amount-1" class="form-control installment-amount" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="installment-dates-1">Date Due</label>
                                            <input type="date" name="installment_dates[]" id="installment-dates-1" class="form-control" required>
                                        </div>
                                    
                                        <div class="col-md-12 mt-2">
                                            <button type="button" class="btn btn-success btn-sm add-installment">+</button>
                                        </div>
                                    </div>
                                </div>

                                </div>
                            </div>

                            <!-- IPF Details -->
                            <div id="ipf-details" style="display:none;">
                                <div class="row">
                                    <!-- Bank Selection -->
                                    <div class="col-md-4">
                                        <label for="ipf-bank">Select Bank</label>
                                        <select name="ipf_bank" id="ipf_bank" class="form-select" required>
                                            <option value="" disabled selected>Select Bank</option>
                                            @foreach($bankrecs as $bankrec)
                                            <option value="{{$bankrec->bank_code}}">{{$bankrec->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Installments Selection -->
                                    <div class="col-md-4">
                                        <label for="ipf-installments">Monthly Installments (Max 10)</label>
                                        <select name="ipf_installments" id="ipf-installments" class="form-select" required>
                                            @for ($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}">{{ $i }} Installments</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <!-- Rate Input -->
                                    <div class="col-md-4">
                                        <label for="ipf-rate">Rate (%)</label>
                                        <input type="number" step="0.01" name="ipf_rate" id="ipf_rate" class="form-control" placeholder="Enter rate" required>
                                    </div>
                                
                                
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="ipf-amount">Amount</label>
                                        <input type="text"  name="ipf_amount" id="ipf_amount" class="form-control" placeholder="Enter amount" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="total_ipf-amount">Total Amount</label>
                                        <input type="text"  name="total_ipf_amount" id="total_ipf_amount" class="form-control" placeholder="Enter amount" required>
                                    </div>
                                </div>
                            </div>
                
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="prempay-save-btn" class="btn btn-outline-primary btn-sm">Submit</button>
                    </div>
                </form>


            </div>
        </div>
    </div>

   


<!-- Modal -->
<div class="modal fade" id="escalatemodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{route('escalatereq')}}" method="post">
         @csrf
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Escalate Transaction</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mb-3">
            <label for="exampleFormControlSelect1" class="col-sm-2 col-form-label">User</label>
            <div class="col-sm-10">
                <select class="form-select" id="user_id" name="user_id">
                    <option value="" selected disabled>Select a user...</option>
                    @foreach($users as $user)
                    <option value="{{$user->id}}">{{$user->username}}</option>
                    @endforeach
                
                </select>
            </div>
            </div>
            <div class="row mb-3">
            <label for="exampleFormControlInput1" class="col-sm-2 col-form-label">Narration</label>
            <input type="hidden" value="ESCALATE-DEBIT" name="process_id">
            <input type="hidden" value="{{$policy_dtl->policy_no}}" name="policy_no">
            <input type="hidden" value="{{$policy_dtl->endorsement_no}}" name="endorsement_no">
            <div class="col-sm-10">
                <textarea class="form-control" id="narration" name="narration" rows="3"></textarea>
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="escalateprocess">Submit</button>
        </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approveLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{route('approvereq')}}" method="post">
         @csrf
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Approve/Reject Transaction</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mb-3">
            <label for="exampleFormControlSelect1" class="col-sm-2 col-form-label">Action</label>
            <div class="col-sm-10">
            <div class="row">
                <div class="col-auto">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="action_approved" id="action_approved" value="A">
                        <label class="form-check-label" for="action_approved">Approve</label>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="action_rejected" id="action_rejected" value="R">
                        <label class="form-check-label" for="action_rejected">Reject</label>
                    </div>
                </div>
            </div>

            </div>
            </div>
            <div class="row mb-3">
            <label for="exampleFormControlInput1" class="col-sm-2 col-form-label">Narration</label>
            <input type="hidden" value="ESCALATE-DEBIT" name="process_id">
            <input type="hidden" value="{{$policy_dtl->policy_no}}" name="policy_no">
            <input type="hidden" value="{{$policy_dtl->endorsement_no}}" name="endorsement_no">
            <div class="col-sm-10">
                <textarea class="form-control" id="narration" name="narration" rows="3"></textarea>
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        </div>
    </form>
  </div>
</div>
<!--****************modal for debiting****************-->
<div class="modal fade" id="debit_modal" tabindex="-1" role="dialog">
 
    <div class="modal-dialog modal-lg" role="document" style="width: 600px;">

      <div class="modal-content">
           <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Debit Transaction</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
        <table class="table">
      <tbody>
      <tr>
          <th scope="row">Sum Insured</th>
          <td id ='sumins'></td>
        </tr>
        <tr>
          <th scope="row">Premium</th>
          <td id='premiums'></td>
        </tr>
        <tr id="sticker_div">
          <th scope="row">Sticker Fees</th>
          <td id="sticker_fees"></td>
        </tr>
        <tr id="stamp_div">
          <th scope="row">Stamp Duty</th>
          <td id="stamp_duty"></td>
        </tr>
        <tr id="phcfund_div">
          <th scope="row">PhcFund</th>
          <td id="phcfund"></td>
        </tr>
        <tr id="levy_div">
          <th scope="row">Levy</th>
          <td id="levy"></td>
        </tr>
        <tr id="vat_div">
          <th scope="row">VAT Amount</th>
          <td id="vat_amount"></td>
        </tr>
        
        <tr>
          <th scope="row">Total Premium</th>
          <td id="total_prem"></td>
        </tr>
        <tr></tr>
        <tr>
          <th scope="row">Commission Amount</th>
          <td id="comm_amt"></td>
        </tr>
        
      </tbody>
    </table>

        </div>

        <div class="modal-footer">
          <input type="submit" id="process_debit" class="btn btn-info" value="Debit" />

        </div>
      </div><!-- /.modal-content -->

    </div><!-- /.modal-dialog -->

</div>
<!-- /.modal -->
  <!--Schedules Modal -->
    <div class="modal fade" id="schedulesModal" data-bs-backdrop="static" data-bs-keyboard="false" 
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="" id="schedulesForm">
                    @csrf
                    <input type="hidden" name="policy_no" value="{{ $policy_dtl->policy_no}}">
                    <input type="hidden" name="endorsement_no" value="{{ $policy_dtl->endorsement_no}}">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title  text-white text-center" id="staticBackdropLabel">Cover Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <button class="btn btn-primary btn-sm add-item mb-4">Add Item</button>
                       <div class="formdatas">            
                            <div class="formdata" style="background-color: #f5f5f5;">
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label class="required-label" for="shed-header">Schedule Title</label>
                                        <select name="header[]" id="sched-header0" class="form-select" required>
                                            <option value="" selected disabled>--Select Schedule--</option>
                                            <option value="3">Summary Of Cover</option>
                                                <option value="1">Clauses</option>
                                                <option value="2">Limits</option>
                                                <option value="4">Extension</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <h5>Item 1</h5>
                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <label class="required-label">Item Name/Title</label>
                                        <input type="text" name="title[]" id="title0" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-4 valdiv" style="display:none;">
                                        <label class="required-label">Item Value</label>
                                        <input type="text" name="schedule_value[]" id="schedule_value0" class="form-control form-control-sm amount" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="">Details</label>
                                        <div id="schedule-descr0" class="schedule-descr0"></div>
                                        <input type="hidden" name="details[]" id="sched-details0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" id="schedule-save-btn" class="btn btn-outline-primary btn-sm btn-wave waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Button trigger modal -->
     <!-- Modal Structure -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="viewModalLabel">Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Endorsement No:</strong> <span id="modal-endorsement-no"></span></p>
                <p><strong>Payment Plan:</strong> <span id="modal-payment-plan"></span></p>
                <p><strong>Amount:</strong> <span id="modal-amount"></span></p>
                 <!-- Installment Details -->
                 <div id="installmentdata-details" style="display:none;">
                    <h6>Installments</h6>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Installment Number</th>
                                <th>Percentage</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody id="installment-table-body">
                            <!-- Installments will be loaded here -->
                        </tbody>
                    </table>
                </div>
                 <!-- Credit Period Details -->
                <div id="creditdtl-period-details" style="display:none;">
                    <h6>Credit Period Details</h6>
                    <p><strong>Credit Period:</strong> <span id="credit-perioddtl"></span> days</p>
                    <p><strong>Created At:</strong> <span id="credit-period-created-at"></span></p>
                </div>
              
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




@endsection
@section('page_scripts')

<script>
    var endt_renewal_no ="{!! $policy_dtl->endorsement_no !!}"
    var cls ="{!! $policy_dtl->class !!}"
    var company ="{!! $policy_dtl->company_code !!}"
    var motor_flag="{!!$class->motor_flag!!}"
    var ckeditors = {};
    var counter=0;
    var installmentCounter = 1;
    const totalValue = "{{ $debitrec ? $debitrec->total_premium : 0 }}";

    function confirmShare() {
        Swal.fire({
            title: 'Select Documents to Share with Client',
            html: `
                <form id="documentForm">
                    <div style="display: flex; justify-content: space-around; gap: 20px;">
                        <div>
                            <input type="checkbox" id="debit" name="documents" value="debit-note">
                            <label for="doc1">Debit note</label>
                        </div>
                        <div>
                            <input type="checkbox" id="doc2" name="documents" value="risk-note">
                            <label for="doc2">Risk Note</label>
                        </div>
                        <div>
                            <input type="checkbox" id="doc3" name="documents" value="invoice">
                            <label for="doc3">Invoice</label>
                        </div>
                    </div>
                </form>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, share it!',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const selectedDocuments = Array.from(document.querySelectorAll('input[name="documents"]:checked'))
                    .map(checkbox => checkbox.value);
                    
                if (selectedDocuments.length === 0) {
                    Swal.showValidationMessage('Please select at least one document');
                    return false;
                }

                return selectedDocuments;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedDocuments = result.value;

                // Sending the selected documents via POST request to the Laravel route
                fetch("{{ route('shareWithClient', ['policy_no' => $policy_dtl->endorsement_no]) }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF Token for security
                    },
                    body: JSON.stringify({
                        selectedDocuments: selectedDocuments
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Success:", data);
                    Swal.fire(
                        'Shared!',
                        'Documents have been shared with the insurer.',
                        'success'
                    );
                })
                .catch((error) => {
                    console.error("Error:", error);
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    );
                });
            }
        });
    }
    function confirminsurerShare() {
        Swal.fire({
            title: 'Select Documents to Share',
            html: `
                <form id="documentForm">
                    <div style="display: flex; justify-content: space-around; gap: 20px;">
                        <div>
                            <input type="checkbox" id="debit" name="documents" value="debit-note">
                            <label for="doc1">Debit note</label>
                        </div>
                        <div>
                            <input type="checkbox" id="doc2" name="documents" value="risk-note">
                            <label for="doc2">Risk Note</label>
                        </div>
                        <div>
                            <input type="checkbox" id="doc3" name="documents" value="invoice">
                            <label for="doc3">Invoice</label>
                        </div>
                    </div>
                </form>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, share it!',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const selectedDocuments = Array.from(document.querySelectorAll('input[name="documents"]:checked'))
                    .map(checkbox => checkbox.value);
                    
                if (selectedDocuments.length === 0) {
                    Swal.showValidationMessage('Please select at least one document');
                    return false;
                }

                return selectedDocuments;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedDocuments = result.value;

                // Sending the selected documents via POST request to the Laravel route
                fetch("{{ route('shareWithInsurer', ['policy_no' => $policy_dtl->endorsement_no]) }}", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF Token for security
                    },
                    body: JSON.stringify({
                        selectedDocuments: selectedDocuments
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Success:", data);
                    Swal.fire(
                        'Shared!',
                        'Documents have been shared with the insurer.',
                        'success'
                    );
                })
                .catch((error) => {
                    console.error("Error:", error);
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    );
                });
            }
        });
    }



    $(document).ready(function () {
        initializeCKEditor(0);
   
             const schedulesTable = $('#schedules-table').DataTable({
                order:[[0,'desc']],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.schedules_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $policy_dtl->endorsement_no !!}";
                    }
                },
                columns: [
                // { data: 'id' , searchable: true },
                { data: 'name' , searchable: true },
                { data: 'details' , searchable: true },
                { data: 'sum_insured' , searchable: false ,  render: function (data, type, row) {
                    // Format the 'sum_insured' data with commas and to two decimal places
                    var formattedSumInsured = parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2});
                    return formattedSumInsured;
                }},
                //  { data: 'total_premium' , searchable: false ,render: function (data, type, row) {
                //     // Format the 'sum_insured' data with commas and to two decimal places
                //     var formattedSumInsured = parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2});
                //     return formattedSumInsured;
                // }},
                { data: 'action', searchable: false  },
                ]
            });
            const clausesTable = $('#clauses-table').DataTable({
                order:[[0,'desc']],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.clause_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $policy_dtl->endorsement_no !!}";
                    }
                },
                columns: [
          
                { data: 'title' , searchable: true },
                { data: 'details' , searchable: true },
                { data: 'action', searchable: false  },
                ]
            });
            const extensionTable = $('#extension-table').DataTable({
                order:[[0,'desc']],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.extension_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $policy_dtl->endorsement_no !!}";
                    }
                },
                columns: [
          
                { data: 'title' , searchable: true },
                { data: 'details' , searchable: true },
                { data: 'action', searchable: false  },
                ]
            });
            const limitsTable = $('#limits-table').DataTable({
                order:[[0,'desc']],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.limits_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $policy_dtl->endorsement_no !!}";
                    }
                },
                columns: [
          
                { data: 'title' , searchable: true },
                { data: 'details' , searchable: true },
                { data: 'value' , searchable: false ,render: function (data, type, row) {
                    // Format the 'sum_insured' data with commas and to two decimal places
                    var formattedSumInsured = parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2});
                    return formattedSumInsured;
                }},
                { data: 'action', searchable: false  },
                ]
            });
            const cover_summary = $('#cover_summary').DataTable({
                order:[[0,'desc']],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.cover_summary_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $policy_dtl->endorsement_no !!}";
                    }
                },
                columns: [
          
                { data: 'title' , searchable: true },
                { data: 'details' , searchable: true },
                { data: 'action', searchable: false  },
                ]
            });
            const attachmentsTable = $('#attachments-table').DataTable({
                order:[[0,'desc']],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.attachments_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $policy_dtl->endorsement_no !!}";
                    }
                },
                columns: [
                { data: 'id' , searchable: true },
                { data: 'title' , searchable: true },
                { data: 'description' , searchable: true },
                { data: 'action', searchable: false  },
                ]
            });
            const paymentplanTable = $('#paymentplan-table').DataTable({
                order:[[0,'desc']],
                processing: true,
                serverSide: true,
                bAutoWidth: false,
                lengthChange: false,
                ajax: {
                    url: "{!! route('cover.paymentplan_datatable') !!}",
                    data: function(d) {
                        d.endorsement_no = "{!! $policy_dtl->endorsement_no !!}";
                    }
                },
                columns: [
                { data: 'plan_type' , searchable: true },
                { data: 'total_amount' , searchable: true },
                { data: 'created_by' , searchable: true },
                { data: 'action', searchable: false  },
                ]
            });
          const motordatable=  $('#risks_data_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax:{
                    'url' : '{{ route("get.motor.risks",["source"=>"client"]) }}',
                    'data' : function(d){
                            var endorsement_no= "{{$endorsement_no}}"
                            d.endorsement_no=endorsement_no
                        },
                },
                
                columns: [
                    {data:'reg_no',name:'reg_no'},
                    {data:'make',name:'make'},
                    {data:'model',name:'model'},
                    // {data:'body_type',name:'body_type'},
                    {data:'annual_prem',name:'annual_prem',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'action',name:'action'},
                    ]		
            });
            $('#ipf_rate').on('keyup', function() {
                updateAmounts();
            });
            function updateAmounts() {
                var rate = parseFloat($('#ipf_rate').val()) || 0;
                var totalAmount = parseFloat($('#total_amount').val()) || 0;

                // Calculate ipf_amount
                var ipfAmount = (rate / 100) * totalAmount;
                $('#ipf_amount').val(ipfAmount.toFixed(2));

                // Calculate total_ipf_amount
                var totalIpfAmount = ipfAmount + totalAmount;
                $('#total_ipf_amount').val(totalIpfAmount.toFixed(2));
            }
            $('#risks_data_table').on('click', '.deletedetails', function() {
                var itemno = $(this).closest('tr').find('td:eq(0)').text();
                var policy_no =  "{{$endorsement_no}}";
                Swal.fire({
                    title: "Warning!",
                    html: "Are You Sure You Want to delete this Vehicle?",
                    icon: "warning",
                    confirmButtonText: "Yes"
                }).then(function(result) {
                if (result.isConfirmed) {
                        $.ajax({
                            type: 'GET',
                            data:{'itemno':itemno,'policy_no':policy_no},
                            url: "{!! route('policy.delete.risk')!!}",
                            success: function(response) {
                            
                                toastr.success("Vehicle Deleted Successfully");
                                var table = $('#risks_data_table').DataTable()
                                table.ajax.reload();
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


            });
        $('#attachments-save-btn').click(function (e) {
                $('#attachmentsForm').submit()
        });
        $('#prempay-save-btn').click(function (e) {
                $('#premcollectionForm').submit()
        });
        $("#premcollectionForm").validate({
                errorClass: "errorClass",
                rules: {
                    title: {
                        required: true
                    },
                    address: {
                        required: true
                    },
                },
                submitHandler: function (form) {

                    $('#prempay-save-btn').prop('disabled', true).text('Saving...')


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
                            console.log(data.message);
                            $('#premcollection-modal').modal('hide');
                            toastr.success(data.message);

                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                             if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)
                                $('#prempay-save-btn').prop('disabled', false).text('Submit')

                            }
                            // else {
                            //     toastr.error("Failed to save attachment")
                            //     $('#prempay-save-btn').prop('disabled', false).text('Submit')
                            // }
                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error:', error);
                            toastr.error("Failed to save plan")
                            $('#attachments-save-btn').prop('disabled', false).text('Submit')
                        });
                }
        })
        $(document).on('click', '.delete-plan', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure you want to delete this record?')) {
                $.ajax({
                    url: '{{ route("delete.payment.plan") }}', // Route to handle the deletion
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // CSRF token for security
                        id: id
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success("Record deleted successfully!")
                            $('#paymentplan-table').DataTable().ajax.reload();
                        } else {
                             toastr.error('Failed to delete record.');
                        }
                    },
                    error: function(xhr, status, error) {
                         toastr.error('An error occurred: ' + error);
                    }
                });
            }
        });
        $(document).on('click', '.view-plan', function() {
            $('#installmentdata-details').hide();
            var id = $(this).data('id');
            $.ajax({
                url: '{{ route("view.payment.plan") }}', // Route to fetch the details
                type: 'GET',
                data: {
                    id: id
                },
                success: function(response) {
                    if (response.status == 1) {
                        // Populate the modal with the record details
                        $('#modal-endorsement-no').text(response.plan.endorsement_no);
                        $('#modal-payment-plan').text(response.plan.plan_type);
                        $('#modal-amount').text(response.plan.total_amount);
                        if (response.plan.plan_type === 'installments') {
                            $('#installmentdata-details').show();
                            $('#installment-table-body').empty();
                            $.each(response.recs, function(index, installment) {
                          
                                $('#installment-table-body').append(
                                    `<tr>
                                        <td>${installment.installment_number}</td>
                                        <td>${installment.percentage}</td>
                                        <td>${installment.amount}</td>
                                        <td>${installment.due_date}</td>
                                    </tr>`
                                );
                            });
                         }else if(response.plan.plan_type === 'credit_period'){
                            $('#creditdtl-period-details').show();
                            $('#installmentdata-details').hide(); 
                            let creditPeriodData = response.recs[0];
                            $('#credit-perioddtl').text(creditPeriodData.credit_period);
                            $('#credit-period-created-at').text(creditPeriodData.created_at);


                        }
                        else {
                            $('#installmentdata-details').hide();
                        }

                        // Show the modal
                        $('#viewModal').modal('show');
                    } else {
                        alert('Failed to fetch record details.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });
       
        $("#attachmentsForm").validate({
                errorClass: "errorClass",
                rules: {
                    title: {
                        required: true
                    },
                    address: {
                        required: true
                    },
                },
                submitHandler: function (form) {

                    $('#attachments-save-btn').prop('disabled', true).text('Saving...')


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
                            console.log(data.message);
                            $('#attachmentsModal').modal('hide');
                            if (data.status == 1) {
                                toastr.success("Attachment saved Successfully")

                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } 
                            else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)
                                $('#attachments-save-btn').prop('disabled', false).text('Submit')

                            }
                            else {
                                toastr.error("Failed to save attachment")
                                $('#attachments-save-btn').prop('disabled', false).text('Submit')
                            }
                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error:', error);
                            toastr.error("Failed to save attachment")
                            $('#attachments-save-btn').prop('disabled', false).text('Submit')
                        });
                }
        })
        $('.add-item').click(function(e) {
                        e.preventDefault()
        

                        if (counter > 0) {
                            var item_name = $('#item_name' + counter).val()
                        } else if (counter == 0) {
                            var item_name = $('#item_name0').val()
                        }
                        if (item_name == '') {
                            Swal.fire({
                                icon: 'warning',
                                text: 'Please fill all details'
                            });
                        } else {
                            counter++;

                        
                            $('.formdatas').append(`<div class="formdata mt-4" style="background-color: #f5f5f5;">
                                <h5>Item ${counter+1}</h5>\
                                <button class="btn btn-danger btn-sm remove-item mb-3" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;">Remove Item</button> \
                                  <div class="row mb-2">
                                    <div class="col-md-12">
                                        <label class="required-label" for="shed-header">Schedule Title</label>
                                        <select name="header[]" id="sched-header${counter}" class="form-select" required>
                                            <option value="" selected disabled>--Select Schedule--</option>
                                            <option value="3">Summary Of Cover</option>
                                                <option value="1">Clauses</option>
                                                <option value="2">Limits</option>
                                                <option value="4">Extension</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <label class="required-label">Item Name/Title</label>
                                        <input type="text" name="title[]" id="title${counter}" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-4 valdiv" style="display:none;">
                                        <label class="required-label">Item Value</label>
                                        <input type="text" name="schedule_value[]" id="schedule_value${counter}" class="form-control form-control-sm amount" required >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="">Details</label>
                                        <div class="schedule-descr${counter}" id="schedule-descr${counter}" ></div>
                                        <input type="hidden" name="details[]" id="sched-details${counter}">
                                    </div>
                                </div>
                            </div>`);
                            initializeCKEditor(counter);
                            $('#sched-header'+counter).trigger("change")

                        }
                     
        });
        // 
            // Function to remove selected options from subsequent dropdowns
    function removeSelectedOptions() {
        // Get all the selected options
        var selectedOptions = [];
        $('select[name="header[]"]').each(function() {
            var selectedValue = $(this).val();
            
            if (selectedValue) {
                selectedOptions.push(selectedValue);
            }
        });

        // Disable selected options in all dropdowns
        $('select[name="header[]"]').each(function() {
            var $select = $(this);
            $select.find('option').each(function() {
                var $option = $(this);
                if (selectedOptions.includes($option.val())) {
         
                    $option.prop('disabled', true);
                } else {
             
                    $option.prop('disabled', false);
                }
            });
        });
    }

    // Event listener for selecting an option in any dropdown
    // $(document).on('change', 'select[name="header[]"]', function() {
 
    //     removeSelectedOptions();
    // });

        // 
        $('#debit-pol').on('click',function(){
            var sent_to = "{{ optional($approved_rec)->send_to }}";
            var user ="{{auth()->user()->id}}"
            if(sent_to ==user){
                toastr.warning('Failed! Cant approve and debit same Transaction', 'warning', {timeOut: 1500});
                return

            }
          var debitted = '{{$debitted}}'
            if(debitted){
                toastr.warning('Can Debit Policy, transaction already debitted', 'warning', {timeOut: 1500});
                return
                
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to Debit policy '+endt_renewal_no+'. Once Debitted it can\'nt be amended',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Debit!'
                }).then((result) => {
                if (result.isConfirmed) {
                        $.ajax({
                            type: "get",
                            url: "{{ route('verify_debit') }}",
                            data: {'endorse_no':endt_renewal_no},
                            success: function (resp) {
                                console.log(resp.sum_insured);


                               // $('#sumins').text(resp.sum_insured)
                                $('#sumins').text(parseFloat(resp.sum_insured).toLocaleString());
                                $('#sumins').text(parseFloat(resp.sum_insured).toLocaleString());
                                $('#premiums').text(parseFloat(resp.premium).toLocaleString());
                                $('#sticker_fees').text(parseFloat(resp.sticker_fees).toLocaleString());
                                $('#levy').text(parseFloat(resp.levy).toLocaleString());
                                $('#vat_amount').text(parseFloat(resp.vat_amount).toLocaleString());
                                $('#total_prem').text(parseFloat(resp.total_premium).toLocaleString());
                                $('#comm_amt').text(parseFloat(resp.comm_amount).toLocaleString());
                                $('#stamp_duty').text(parseFloat(resp.stamp_duty).toLocaleString());
                                $('#phcfund').text(parseFloat(resp.phcfund).toLocaleString());
                                if (parseFloat(resp.stamp_duty) === 0) {
                                    $('#stamp_div').hide(); 
                                } else {
                                    $('#stamp_div').show(); 
                                }
                                if (parseFloat(resp.levy) === 0) {
                                    $('#levy_div').hide(); 
                                } else {
                                    $('#levy_div').show(); 
                                }
                                if (parseFloat(resp.phcfund) === 0) {
                                    $('#phcfund_div').hide(); 
                                } else {
                                    $('#phcfund_div').show(); 
                                }
                                if (parseFloat(resp.sticker_fees) === 0) {
                                    $('#sticker_div').hide(); // Hide the div with id "sticker"
                                } else {
                                    $('#sticker_div').show(); // Show the div with id "sticker"
                                }
                                if (parseFloat(resp.vat_amount) === 0) {
                                    $('#vat_div').hide(); 
                                } else {
                                    $('#vat_div').show(); 
                                }
                                $('#debit_modal').modal({backdrop: 'static', keyboard: false});
                                $('#debit_modal').modal('show');
                                
                            
                            },

                            error: function (err) {

                            }
                        })
                            
                        

                //
                }
            })

        });
        $('#prem_payment').on('click', function() {
            var debitted = '{{$debitted}}'
            if(!debitted){
                toastr.warning('Please Complete the invoicing process', 'warning', {timeOut: 1500});
                return
                
            }
            $('#premcollection-modal').modal('show'); 
        });

        $('#invoice').on('click', function() {
            $.ajax({
                            type: 'GET',
                            data:{'cls':cls,'company':company},
                            async: false,
                            url: "{!! route('agent.checkcommission')!!}",
                            success:function(data){
                                console.log(data)
                                if (data.status == 1) {
                                    if (data.exists ==true) {
                                        toastr.error('Commission rate not  Set, Please Set Commission parameter To Proceed!', {timeOut: 5000});
                                        $('#escalateprocess').prop('disabled', true);
                                        return false
                                    
                                        
                                    }else{
                                        $('#invoice-modal').modal('show'); 

                                    }
                                    
                                }else{
                                    toastr.error('Error Occurred when checking commission rate', {timeOut: 5000});
                                    $('#escalateprocess').prop('disabled', true);
                                    return false


                                }
                                
                            }
                            });
            

        });

        $('#process_debit').on('click', function() {
            $(this).val('Processing..');
                // Disable the button
            $(this).prop('disabled', true);
                    $.ajax({
                            type: "get",
                            url: "{{ route('process_debit') }}",
                            data: {'endorse_no':endt_renewal_no},
                            success: function (resp) {
                                $(this).prop('disabled', false);
                                $('#debit_modal').modal('hide');
                                toastr.success('Policy Debited Successfully', 'success', {timeOut: 1500});
                                window.location.reload(); 
                                
                                
                            
                            },

                            error: function (err) {
                                $(this).prop('disabled', false);
                                $('#debit_modal').modal('hide');
                                toastr.error('An Error occurred While debitting', 'success', {timeOut: 1500});
                            }
                    })


        });
  
        $('#schedule-save-btn').click(function () {
                // const editorData = editor.getData()
              
                
                for (var i = 0; i <= counter; i++) {
                    $(`#sched-details${i}`).val(ckeditors[i].getData());


                }
               $('#schedulesForm').submit()
        });
         $(document).on('change', 'select[name="header[]"]', function() {


            var selectedOption = $(this).val();
            let selectedText = $(this).find('option:selected').text(); 
            
            $(`#title${counter}`).val("")
            $(`#title${counter}`).val(selectedText)
         
               ckeditors[counter].setData('');
          
            var classcode ="{{$policy_dtl->class}}"
                $.ajax({
                    type: 'GET',
                    data:{'selectedOption':selectedOption,'classcode':classcode},
                    url: "{!! route('policy.shcheduledetails')!!}",
                    success: function(response) {
                        if (response && response.data && response.data.details){
                            setTimeout(function() {
                                setCKEditorValue(counter, response.data.details);
                            }, 1000);

                        }
                     
                        
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                        title: "Error",
                        text: textStatus,
                        icon: "error"
                        });
                    }
                });
        });
        $('#rate_basis').change(function () {
            var selectedOption = $(this).val();
            if(selectedOption == 'PCT'){
                // percentage rate
             
                $('.prem_rate_div').show();
                


            }else if(selectedOption == 'MLE'){
                //mile rate
                $('.prem_rate_div').show();

            }else{
                $('.prem_rate_div').hide();

            }
        })
        $('#sum_insured_amt').on('keyup',  function(){
                var inputVal = $(this).val();
                var numericVal = inputVal.replace(/\D/g, '');
                var formattedVal = numericVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $(this).val(formattedVal);
        });
        $('#sum_insured_amt').on('keyup', function() {
      

           // Get the rate and item value
           var rate = parseFloat($('#prem_rate').val());
           var numericVal = $(this).val().replace(/,/g, '');
           var itemValue = parseFloat(numericVal);
           var basis = $('#rate_basis').val()
           if(basis == 'PCT'){
                // percentage rate         
                rate =rate/100

            }else if(basis == 'MLE'){
                //mile rate
                rate =rate/1000


            }else{
                rate =0

            }


           // Calculate premium
           var premium = rate * itemValue;
           if (!isNaN(premium) && premium > 0) {
                computelevies(endt_renewal_no,premium)
            }
           
           // var numericVal = premium.toFixed(2).replace(/\D/g, '');
           var formattedVal = premium.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
           // Update the "Premium" input field with the calculated premium
           $('#basic_prem').val(formattedVal); // Adjust decimal places as needed
        });
        $("#basic_prem").on('keyup',  function(){
            var inputVal = $(this).val();
            var numericVal = inputVal.replace(/\D/g, '');
            if (!isNaN(numericVal) && numericVal > 0) {
                computelevies(endt_renewal_no,numericVal)
            }
            var formattedVal = numericVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            $(this).val(formattedVal);
        });
        $('#prem_rate').on('keyup', function() {
      
            // Get the rate and item value
            var rate = parseFloat($('#prem_rate').val());
            var numericVal = $("#sum_insured_amt").val().replace(/,/g, '');
            var itemValue = parseFloat(numericVal);
            var basis = $('#rate_basis').val()
            if(basis == 'PCT'){
                // percentage rate         
                rate =rate/100

            }else if(basis == 'MLE'){
                //mile rate
                rate =rate/1000


            }else{
                rate =0

            }


            // Calculate premium
            var premium = rate * itemValue;
            if (!isNaN(premium) && premium > 0) {
                computelevies(endt_renewal_no,premium)
            }
            
            // var numericVal = premium.toFixed(2).replace(/\D/g, '');
            var formattedVal = premium.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            // Update the "Premium" input field with the calculated premium
            $('#basic_prem').val(formattedVal); // Adjust decimal places as needed
        });
        $("#schedulesForm").validate({
                errorClass: "errorClass",
                rules: {
                    title: {
                        required: true
                    },
                    details: {
                        required: true
                    },
                },
                submitHandler: function (form) {

                    $('#schedule-save-btn').prop('disabled', true).text('Saving...')

                    // Get form data
                    var formData = new FormData(form);
                    // var formData = $(this).serialize();

                    // Make a fetch request
                    fetch("{!! route('cover.add_polattachement') !!}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams(formData),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == 200) {
                                toastr.success("Schedule Successfully saved")

                                form.reset()

                                // schedulesTable.ajax.reload();
                                setTimeout(() => {
                                    location.reload();
                                }, 3000);
                            } 
                            else if (data.status == 422) {
                                // Validation errors
                                showServerSideValidationErrors(data.errors)
                                $('#schedule-save-btn').prop('disabled', false).text('Submit')

                            }
                            else {
                                toastr.error("Failed to save details")
                            }
                            $('#schedule-save-btn').prop('disabled', false).text('Save')
                        })
                        .catch(error => {
                            $('#schedule-save-btn').prop('disabled', false).text('Save')
                        });
                }
        })
        $('.formdatas').delegate('.remove-item', 'click', function(e) {
            e.preventDefault()
            $(this).closest('.formdata').remove();
        });
        function initializeCKEditor(counter) {
   
            ClassicEditor
                .create( document.querySelector( `.schedule-descr${counter}` ), {
                    // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
                } )
                .then( editor => {
                    ckeditors[counter] = editor;
                } )
                .catch( err => {
                    console.error( err.stack );
                } );
        }
        $('#escalatebtn').click(function (e) {
              if(motor_flag =="Y"){
                if (motordatable.rows().count() < 1) {
                    toastr.error("Risk Details Not Captured")
                    return false
                }  

              }else{
                if (schedulesTable.rows().count() < 1) {
                    toastr.error("Risk Details Not Captured")
                    return false
                }  

              }
           
            if (cover_summary.rows().count() < 1) {
                toastr.error("Cover Summary Not Captured")
                return false
            } 
            if (clausesTable.rows().count() < 1) {
                toastr.error("Clauses Not Captured")
                return false
            } 
            if (limitsTable.rows().count() < 1) {
                toastr.error("Limits Not Captured")
                return false
            } 
            var endorse_amt = "{{$policy_sum->endorse_amount}}"
            var trans_type = "{{$policy_dtl->trans_type}}"
            if(trans_type == "RFN"){
                if (+endorse_amt> 0) {
                    toastr.error("Endorse Amount Cant be Positive In Refund Endorsements")
                    return false
                } 

            }
            if(trans_type == "EXT"){
                if (+endorse_amt < 0) {
                    toastr.error("Endorse Amount Cant be Negative In Extra Endorsements")
                    return false
                } 

            }

            $('#escalateprocess').prop('disabled', false);
             
                            
                            // $.ajax({
                            //     type: 'GET',
                            //     data:{'itemno':itemno,'policy_no':policy_no},
                            //     url: "{!! route('policy.delete.risk')!!}",
                            //     success: function(response) {
                                
                            //         toastr.success("Vehicle Deleted Successfully");
                            //         var table = $('#risks_data_table').DataTable()
                            //         table.ajax.reload();
                            //     },
                            //     error: function(jqXHR, textStatus, errorThrown) {
                            //         Swal.fire({
                            //         title: "Error",
                            //         text: textStatus,
                            //         icon: "error"
                            //         });
                            //     }
                            // });
                            $.ajax({
                            type: 'GET',
                            data:{'cls':cls,'company':company},
                            async: false,
                            url: "{!! route('agent.checkcommission')!!}",
                            success:function(data){
                                console.log(data)
                                if (data.status == 1) {
                                    if (data.exists !=true) {
                                        toastr.error('Commission rate not  Set, Please Set Commission parameter To Proceed!', {timeOut: 5000});
                                        $('#escalateprocess').prop('disabled', true);
                                        return false
                                    
                                        
                                    }else{
                                        $('#escalateprocess').prop('disabled', false);

                                    }
                                    
                                }else{
                                    toastr.error('Error Occurred when checking commission rate', {timeOut: 5000});
                                    $('#escalateprocess').prop('disabled', true);
                                    return false


                                }
                                
                            }
                            });
                
            
                $('#escalatemodal').modal('show');

            });
       

    })
    $('#payment-plan').change(function() {
    var selectedPlan = $(this).val();

        // Hide all detail sections
        $('#payment-plan-details').hide();
        $('#credit-period-details').hide();
        $('#installment-details').hide();
        $('#ipf-details').hide();

        // Show the relevant detail section based on the selected plan
        if (selectedPlan === 'cash_and_carry') {
            // No additional details needed for Cash and Carry
        } else if (selectedPlan === 'credit_period') {
            $('#payment-plan-details').show();
            $('#credit-period-details').show();
        } else if (selectedPlan === 'installments') {
            $('#payment-plan-details').show();
            $('#installment-details').show();
        } else if (selectedPlan === 'ipf') {
            $('#payment-plan-details').show();
            $('#ipf-details').show();
        }
    });

    // Add a new installment
    $(document).on('click', '.add-installment', function() {
        installmentCounter++; // Increment the counter

        var newInstallment = `
            <div class="installment-item mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label for="installments-number-${installmentCounter}">No of Installments</label>
                        <select name="installments_number[]" id="installments-number-${installmentCounter}" class="form-select" required>
                            <option value="" selected disabled>--Select Installment--</option>
                            <option value="1">First Installment</option>
                            <option value="2">Second Installment</option>
                            <option value="3">Third Installment</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="installment-percentage-${installmentCounter}">Percentage</label>
                        <input type="text" name="installment_percentage[]" id="installment-percentage-${installmentCounter}" class="form-control installment-percentage" placeholder="e.g., 20%" required>
                    </div>
                    <div class="col-md-2 mt-2">
                        <label for="installment-amount-${installmentCounter}">Amount</label>
                        <input type="text" name="installment_amount[]" id="installment-amount-${installmentCounter}" class="form-control installment-amount" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="installment-dates-${installmentCounter}">Date Due</label>
                        <input type="date" name="installment_dates[]" id="installment-dates-${installmentCounter}" class="form-control" required>
                    </div>
                    <div class="col-md-12 mt-2">
                        <button type="button" class="btn btn-success btn-sm add-installment">+</button>
                        <button type="button" class="btn btn-danger btn-sm remove-installment">-</button>
                    </div>
                </div>
            </div>
        `;

        $('#installment-container').append(newInstallment);

        // Hide the add button of the previous installment and show the remove button
        $(this).hide();
        $(this).siblings('.remove-installment').show();
    });

    // Remove an installment
    $(document).on('click', '.remove-installment', function() {
        $(this).closest('.installment-item').remove();
        installmentCounter--; // Decrement the counter
    });
    // Attach event listener to the percentage input fields
 
    $(document).on('input', '.installment-percentage', function() {
        updateAmountFields();
        validatePercentageTotal();
    });
    $(document).on('click', '.remove-risk', function() {
        var riskId = $(this).data('id'); // Get the risk ID from the data-id attribute
        
        // Show Swal confirmation popup
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, make the AJAX call
                $.ajax({
                    url: "{!! route('deleterisk')!!}", // Adjust the URL according to your route
                    type: 'DELETE', // Assuming you're using DELETE method
                    data: {
                        _token: '{{ csrf_token() }}',
                        id:riskId
                    },
                    success: function(response) {
                        // Show success message and remove the risk from the UI
                        Swal.fire(
                            'Deleted!',
                            'The risk has been removed.',
                            'success'
                        );
                        // Optionally, remove the deleted row from the table
                        $('button[data-id="'+riskId+'"]').closest('tr').remove();
                    },
                    error: function(xhr) {
                        // Show error message if something goes wrong
                        Swal.fire(
                            'Error!',
                            'There was a problem removing the risk. Please try again.',
                            'error'
                        );
                    }
                });
            }
        });
    });



    function updateAmountFields() {
        $('.installment-item').each(function() {
            const percentage = $(this).find('.installment-percentage').val();
            const amount = (percentage / 100) * totalValue;
            $(this).find('.installment-amount').val(amount.toFixed(2)); // Display the amount with two decimal places
        });
    }

    function validatePercentageTotal() {
        let totalPercentage = 0;
        $('.installment-percentage').each(function() {
            totalPercentage += parseFloat($(this).val()) || 0;
        });

        if (totalPercentage !== 100) {
            toastr.warning('The total percentage must equal 100%. Currently, it equals ' + totalPercentage + '%.');
        }
    }

    function setCKEditorValue(counter, value) {
        if (ckeditors[counter]) {
            ckeditors[counter].setData(value);
        } else {
            console.error(`CKEditor instance for counter ${counter} is not initialized.`);
        }
    }
    function computelevies(endorseno,premium){
        $.ajax({
                    type: 'GET',
                    data:{'endorse_no':endorseno,'premium':premium},
                    url: "{!! route('policy.computelevies')!!}",
                    success: function(response) {
                        console.log(response)
                        var comm_amount =response.comm_amount;
                        var comm_rate =response.comm_rate;
                        var levy =response.levy;
                        var phcfund =response.phcfund;
                        var premium =response.premium;
                        var stamp_duty =response.stamp_duty;
                        var sum_insured =response.sum_insured;
                        var total_premium =response.total_premium;
                        var vat_amount =response.vat_amount;
                        var wht =response.wht;
                        var wht_amt =response.wht_amt;
                        var nett_comm_amt =response.nett_comm_amt;
                        var insurer_payable =response.insurer_payable;

                        $('#stamp_duty_amt').val(stamp_duty)
                        $('#tlevy').val(levy)
                        $('#phcf_prem').val(phcfund)
                         $('#vatamt').val(vat_amount)
                         $('#totalprem').val(total_premium)
                         $('#g_comm').val(comm_amount)
                         $('#whttax').val(wht)
                         $('#wht_amt').val(wht_amt)
                         $('#insurer_payable').val(insurer_payable)
                         $('#net_comm').val(nett_comm_amt)



                     
                        
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
</script>


@endsection
