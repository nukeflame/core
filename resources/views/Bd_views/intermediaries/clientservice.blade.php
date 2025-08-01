@extends('layouts.intermediaries.base')
@section('header', 'CLIENT SERVICE')
@section('content')
    <!-- Buttons on Top -->
    <div class="mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clientEngagementModal">
            Underwriter Engagement
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#underwritingModal">
            Underwriting
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deepdiveModal">
            Deep Dive Sessions
        </button>
        <!-- Button to Trigger the Modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#educationModal">
            Member Education
        </button>     
        <button type="button" class="btn btn-primary" id="monitoringEvaluationBtn">
            Monitoring and Evaluation
        </button>
    </div>
    <div class="card mt-3 border">
        <div class="card-header">
        <strong>Client Servicing Details</strong>
        </div>
        <div class="card-body mb-0 pb-0">
            <div class="row">
                <table class="table">
                    <tr>
                        <th>Insured</th>
                        <td>{{$polrec->insured}}</td>
                        <th>Product</th>
                        <td>{{$polrec->classList->class_description}}</td>
                        
                    </tr>
                    <tr>
                        <th>Start Date</th>
                        <td>{{$polrec->period_from }}</td>
                        <th>End Date</th>
                        <td> {{$polrec->period_to }}</td>
                    </tr>
                 
                    
                
                </table>
                
                <br><br>
                        
            </div>
        </div>
    </div>


 

    <!-- Modal for Client Engagement Form -->
    <div class="modal fade" id="clientEngagementModal" tabindex="-1" aria-labelledby="clientEngagementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="clientEngagementModalLabel">Underwriter Engagement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Underwriter Engagements Form -->
                    <form action="{{ route('store.engagements') }}" method="POST" enctype="multipart/form-data">
                        @csrf   
                        <input type="hidden" name="policy_no" value="{{$polrec->policy_no}}">
                        <div class="mb-3">
                            <label for="engagement_type" class="form-label">Engagement Type:</label>
                            <select name="engagement_type" id="engagement_type" class="form-select">
                                <option value="" selected disabled>Select an Option..</option>
                                <option value="cover-instruction">Cover Instruction</option>
                                <option value="confirmation">Underwriter Confirmation</option>
                            </select>
                        </div>
                        <input type="hidden" name="global_customer_id" value="{{$client->global_customer_id}}">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="attachment" class="form-label">Attachment:</label>
                                <input type="file" name="attachment" id="attachment" class="form-control">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="attachment_desc" class="form-label">Attachment Description</label>
                                <textarea name="attachment_desc" id="attachment_desc" class="form-control" rows="4"></textarea>
                            </div>

                        </div>
                       

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Engagement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Deep Dive Sessions -->  
<div class="modal fade" id="deepdiveModal" tabindex="-1" aria-labelledby="deepdiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- modal-lg for a larger modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deepdiveModalLabel">Deep Dive Sessions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Deep Dive Sessions Form -->
                <form action="{{ route('store.deepdive') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="global_customer_id" value="{{$client->global_customer_id}}">
                    <input type="hidden" name="policy_no" value="{{$polrec->policy_no}}">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="session_title" class="form-label">Session Title:</label>
                            <input type="text" name="session_title" id="session_title" class="form-control" placeholder="Enter session title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="session_description" class="form-label">Session Description:</label>
                            <textarea name="session_description" id="session_description" class="form-control" rows="2" placeholder="Enter session description" required></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="attachment" class="form-label">Actual Meeting Date</label>
                            <input type="date" name="meet_date" id="meet_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="attachment" class="form-label">Attachment:
                                <a href="{{route('downloaddeepdive')}}" download class="btn btn-sm btn-outline-primary ms-2">
                                    Download Template
                                </a>
                            </label>
                            <input type="file" name="attachment" id="attachment" class="form-control">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit Session</button>
                    </div>
                </form>
             
            </div>
        </div>
    </div>
</div>
  <!-- end deep dive -->
    <div class="modal fade" id="educationModal" tabindex="-1" aria-labelledby="educationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="educationModalLabel">Member Education</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- Form Container -->
                <form method="POST" action="{{ route('store.membereducation') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Session Title -->
                    <div class="mb-3">
                        <label for="session_title" class="form-label">Session Title</label>
                        <input type="text" name="session_title" id="session_title" class="form-control" required>
                    </div>
                     <input type="hidden" name="global_customer_id" value="{{$client->global_customer_id}}">

                    <!-- Session Description -->
                    <div class="mb-3">
                        <label for="session_description" class="form-label">Session Description</label>
                        <textarea name="session_description" id="session_description" class="form-control" rows="3" required></textarea>
                    </div>

                    <!-- Actual Meeting Date -->
                    <div class="mb-3">
                        <label for="meeting_date" class="form-label">Actual Meeting Date</label>
                        <input type="date" name="meeting_date" id="meeting_date" class="form-control" required>
                    </div>

                    <!-- Download Member Education Templates -->
                    <div class="mb-3">
                        <label class="form-label">Download Member Education Templates</label>
                        <div>
                            <button type="button" class="btn btn-secondary mb-2" onclick="window.location.href='{{ route('downloadGeneralTemplate') }}'">General</button>
                            <button type="button" class="btn btn-secondary mb-2" onclick="window.location.href='{{ route('downloadMedicalTemplate') }}'">Medical</button>
                            <button type="button" class="btn btn-secondary mb-2" onclick="window.location.href='{{ route('downloadLifeTemplate') }}'">Life</button>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Attachments</label>
                        <select name="attachment_type" id="attachment_type" class="form-control mb-3" required>
                            <option value="" selected disabled>Select Attachment Type</option>
                            <option value="slides">Member Education Slides</option>
                            <option value="minutes">Minutes</option>
                        </select>
                        <input type="file" name="attachment_file" id="attachment_file" class="form-control" required>
                        <small class="form-text text-muted">Note: It's compulsory to deactivate the prompts when attaching files.</small>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="underwritingModal" tabindex="-1" aria-labelledby="underwritingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="underwritingModalLabel">Underwriting Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="underwritingForm" method="POST" action="{{ route('submitunderwriting') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Underwriting Type Selection -->
                    <div class="mb-3">
                        <label for="underwriting_type" class="form-label">Underwriting Type</label>
                        <select name="underwriting_type" id="underwriting_type" class="form-control" required>
                            <option value="" selected disabled>Select Underwriting Type</option>
                            <option value="medical">Medical Underwriting</option>
                            <option value="motor">Motor Valuation</option>
                        </select>
                    </div>

                    <!-- Medical Underwriting Fields -->
                    <div id="medical_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="no_of_staff" class="form-label">Number of Staff Due for Medical</label>
                            <input type="number" name="no_of_staff" id="no_of_staff" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="adhered_to_medical" class="form-label">Number of Staff Adhered to Medical</label>
                            <input type="number" name="adhered_to_medical" id="adhered_to_medical" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="attachments" class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                            <small class="form-text text-muted">Upload the list of members in Excel, respective medical letters, and medical compliance letter.</small>
                        </div>
                        <div class="mb-3">
                            <label for="medical_letters_date" class="form-label">Date Medical Letters Shared</label>
                            <input type="date" name="medical_letters_date" id="medical_letters_date" class="form-control">
                        </div>
                    </div>

                    <!-- Motor Valuation Fields -->
                    <div id="motor_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="underwriter" class="form-label">Underwriter</label>
                            <input type="text" name="underwriter" id="underwriter" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="valuation_letter" class="form-label">Download Underwriter Valuation Letter</label>
                            <button type="button" class="btn btn-secondary" id="download_valuation_letter">Download Letter</button>
                        </div>
                        <div class="mb-3">
                            <label for="valuation_report" class="form-label">Upload Valuation Report</label>
                            <input type="file" name="valuation_report" id="valuation_report" class="form-control">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Structure -->
<div class="modal fade" id="monitoringEvaluationModal" tabindex="-1" role="dialog" aria-labelledby="monitoringEvaluationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="monitoringEvaluationModalLabel">Monitoring and Evaluation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="monitoringEvaluationForm">
                    <div class="mb-3">
                        <label for="session_title" class="form-label">Session Title</label>
                        <input type="text" name="session_title" id="session_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="session_description" class="form-label">Session Description</label>
                        <textarea name="session_description" id="session_description" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="meeting_type" class="form-label">Select Meeting Type</label>
                        <select name="meeting_type" id="meeting_type" class="form-control" required>
                            <option value="">Choose...</option>
                            <option value="1st Quarterly Meeting">1st Quarterly Meeting</option>
                            <option value="2nd Quarterly Meeting">2nd Quarterly Meeting</option>
                            <option value="3rd Quarterly Meeting">3rd Quarterly Meeting</option>
                            <option value="Renewal Meeting">Renewal Meeting</option>
                            <option value="Client Score Card">Client Score Card</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="actual_meeting_date" class="form-label">Actual Meeting Date</label>
                        <input type="date" name="actual_meeting_date" id="actual_meeting_date" class="form-control" required>
                    </div>
                    <div class="mb-3" id="templateButtons" style="display:none;">
                        <label class="form-label">Download Template</label>
                        <div id="templateLinks"></div>
                    </div>
                    <!-- <div class="mb-3">
                        <label class="form-label">Attachments – Select</label>
                        <div>
                            <input type="checkbox" id="reports_minutes" name="attachments[]" value="Reports and Minutes for all meetings">
                            <label for="reports_minutes">Reports and Minutes for all meetings (1st, 2nd, 3rd Quarterly meeting, Renewal meeting)</label>
                        </div>
                        <div>
                            <input type="checkbox" id="client_score_card" name="attachments[]" value="Client Score Card">
                            <label for="client_score_card">Client Score Card</label>
                        </div>
                        <div>
                            <input type="checkbox" id="client_survey_report" name="attachments[]" value="Client Survey Report">
                            <label for="client_survey_report">Client Survey Report</label>
                        </div>
                    </div> -->
                    <div class="mb-3">
                        <label for="file_upload" class="form-label">Choose File</label>
                        <input type="file" name="file_upload" id="file_upload" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>






    <!-- Tabs Section -->
    <ul class="nav nav-tabs" id="clientServiceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="tab1-tab" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true"> Underwriter Engagement</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="tab2-tab" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">  Deep Dive Sessions</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="tab3-tab" data-bs-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false"> Member Education</a>
        </li>
      
    </ul>

    <div class="tab-content" id="clientServiceTabsContent">
        <!-- Tab 1 Content -->
        <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
            <table id="engagementsTable" class="display" style="width: 100%;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Engagement Type</th>
                        <th>Insured</th>
                        <th>Attachment</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be inserted here by DataTables -->
                </tbody>
            </table>
        </div>

        <!-- Tab 2 Content -->
        <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
            <table id="deepDiveSessionsTable" class="display" style="width: 100%;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Insured</th>
                        <th>Session Title</th>
                        <th>Session Description</th>
                        <th>Attachment</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be inserted here by DataTables -->
                </tbody>
            </table>
        </div>

        <!-- Tab 3 Content -->
        <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
            <table id="educationTable" class="display" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Global Customer ID</th>
                        <th>Meeting Date</th>
                        <th>Session Title</th>
                        <th>Session Description</th>
                        <th>Attachment Type</th>
                        <th>Attachment File</th>
                        <th>Actioned By</th>
                        <th>Created Date</th>
                        <th>Updated Date</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>     
        </div>

    </div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#underwriting_type').change(function () {
            var type = $(this).val();
            if (type === 'medical') {
                $('#medical_fields').show();
                $('#motor_fields').hide();
            } else if (type === 'motor') {
                $('#medical_fields').hide();
                $('#motor_fields').show();
            } else {
                $('#medical_fields').hide();
                $('#motor_fields').hide();
            }
        });
        $('#monitoringEvaluationBtn').on('click', function() {
            $('#monitoringEvaluationModal').modal('show');
        });
            // Handle change event on meeting type selection
    $('#meeting_type').on('change', function() {
        const selectedType = $(this).val();
        const templateLinks = $('#templateLinks');
        
        // Clear existing template links
        templateLinks.empty();
        $('#templateButtons').hide(); // Hide buttons by default
        
        // Show buttons based on selected meeting type
        if (selectedType) {
            $('#templateButtons').show();
            switch (selectedType) {
                case "1st Quarterly Meeting":
                    templateLinks.append('<a href="/path-to-1st-quarterly-template" class="btn btn-secondary">Download 1st Quarterly Meeting Template</a>');
                    break;
                case "2nd Quarterly Meeting":
                    templateLinks.append('<a href="/path-to-2nd-quarterly-template" class="btn btn-secondary">Download 2nd Quarterly Meeting Template</a>');
                    break;
                case "3rd Quarterly Meeting":
                    templateLinks.append('<a href="/path-to-3rd-quarterly-template" class="btn btn-secondary">Download 3rd Quarterly Meeting Template</a>');
                    break;
                case "Renewal Meeting":
                    templateLinks.append('<a href="/path-to-renewal-template" class="btn btn-secondary">Download Renewal Meeting Template</a>');
                    break;
                case "Client Score Card":
                    templateLinks.append('<a href="/path-to-client-score-card-template" class="btn btn-secondary">Download Client Score Card Template</a>');
                    break;
                default:
                    break;
            }
        }
    });

    // Handle form submission
    $('#monitoringEvaluationForm').on('submit', function(e) {
        e.preventDefault();
        // Add your form submission logic here (e.g., AJAX request)
        // For now, just log the form data
        console.log($(this).serialize());
    });
        $('#engagementsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                    url: "{!! route('engagements.data') !!}",
                    data: function(d) {
                        d.policy_no = "{!! $polrec->policy_no !!}";
                    }
                },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'engagement_type', name: 'engagement_type' },
                { data: 'insured', name: 'insured' },
                { data: 'attachment', name: 'attachment', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' }
            ]
        });
        $('#educationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! route('education.data') !!}",
                type: 'GET',
                data: function(d) {
                    d.customeid = "{!! $client->global_customer_id !!}"; // Passing the policy_no
                }
            },
            columns: [
                { data: 'global_customer_id', name: 'global_customer_id' },
                { data: 'meeting_date', name: 'meeting_date' },
                { data: 'session_title', name: 'session_title' },
                { data: 'session_description', name: 'session_description' },
                { data: 'attachment_type', name: 'attachment_type' },
                { data: 'attachment_file', name: 'attachment_file' }, // You might want to format this as a link
                { data: 'actioned_by', name: 'actioned_by' },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' }
            ],
            order: [[7, 'desc']], // Default sorting by created_at descending
            pageLength: 10 // Set default records per page
        });
        $('#deepDiveSessionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                    url: "{!! route('deepdive.sessions.data') !!}",
                    data: function(d) {
                        d.policy_no = "{!! $polrec->policy_no !!}";
                    }
                },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'insured', name: 'insured' },
                { data: 'session_title', name: 'session_title' },
                { data: 'session_description', name: 'session_description' },
                { data: 'attachment', name: 'attachment', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' }
            ]
        });
    });
</script>
@endsection
