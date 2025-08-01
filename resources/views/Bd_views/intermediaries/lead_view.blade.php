@extends('layouts.intermediaries.base')
<style type="text/css">
    .br span{
        border-radius: 50%;
        padding: 20px;
        text-align:center;
        border: 1px solid;
        color: #b3d9ff;
    }
    .q-link{
        text-decoration: none;
    }

    .q-link p{
        color: black;
    }
    .br span:hover{
        color: #3399ff;
    }
</style>
@section('content')
<br />
@if ($message = Session::get('success'))

@endif
<!--  -->
<div class="card mt-3 border">
    <div class="card-header">
    <strong>Lead Details</strong>
    </div>
    <div class="card-body mb-0 pb-0">
        <div class="dropdown">
            @if ($lead->customer_id == null)
                <!-- <button class="btn btn-outline-success dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    New Quotation
                </button> -->
                
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="{{route('agent.add_risk_quote',['qstring'=>Crypt::encrypt('lead='.$lead->code.'&source=lead&motorflag=Y&quote_no='.null.'')])}}">Motor insurance</a></li>
                    <li><a class="dropdown-item" href="{{route('add_risk_quote_nonmotor',['qstring'=>Crypt::encrypt('lead='.$lead->code.'&source=lead&motorflag=N&quote_no='.null.'')])}}">Non-motor insurance</a></li>
    
                </ul>
                <button class="btn btn-outline-success" id="book_activity">Book activity</button>
            @endif
        </div>
        <div class="row">
            <table class="table">
                <tr>
                    <th>Lead Name</th>
                    <td>{{$lead->full_name}}</td>
                    <th>Email</th>
                    <td>{{$lead->email}}</td>
                    
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td>{{$lead->phone_number}}</td>
                    <th>Source</th>
                    <td> {{$lead->source }}</td>
                </tr>
                <tr>
                    <th>Industry</th>
                    <td>{{$lead->industry }}</td>
                    <th>Status </th>
                    <td style="display:flex;align-items:baseline;">
                        <select style="margin-right: 5px;" name="lead_status" id="lead_status" class="form-control" required>
                        <option value="">Select status</option>
                        @foreach ($lead_status as  $status)
                        <option value="{{ $status->status_name}}"{{ $lead->status === $status->status_name ? 'selected' : '' }}>{{ $status->status_name }}</option>
    
                        @endforeach
                    </select>
                   <span id ="updateStatusBtn" onClick="updateStatus()"  style="display: none;margin-right: 5px;">
                    <i style="color: cornflowerblue;" class="fa fa-check-circle" aria-hidden="true"></i></span>
                    <span id="updateStatusLoader" style="display: none;">
                        Updating...
                    </span>
                    <span id="cancelStatusBtn" class="cancel-icon" style="display: none;">
                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                    </span>
                </td>
                </tr>
                <tr>
                    <th>Rating</th>
                    <td>{{$lead->rating }}</td>
                    <th>Lead Owner </th>
                    <td>{{$lead_owner}}</td>
                </tr>
                <tr>
                    <th>Customer ID</th>
                    <td>{{$lead->customer_id }}</td>
                </tr>
            </table>
            <br><br> 
        </div>
    </div>
    <div class="modal fade" id="book_activity_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="exampleModalLongTitle">New acitivty</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('lead.create.activity')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="lead_id" class="form-control" id="lead_id" value="{{ $lead->code }}" required >
                        <div class="col-md-6 col-sm-12 mt-2">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" id="title" value="" required >
                        </div>
                        <div class="col-md-6 col-sm-12 mt-2">
                            <label for="attendees">Email</label>
                            <input type="email" id="attendees" name="email" class="form-control" multiple value="{{ $lead->email }}"
                            placeholder="Enter multiple emails or phone numbers separated by commas">
                        </div>
                       
                        <div class="col-md-6 col-sm-12 mt-2">
                            <label for="date_from">From</label>
                            <input type="datetime-local" name="date_from" class="form-control" id="date_from" value="" required>
                        </div>
                        <div class="col-md-6 col-sm-12 mt-2">
                            <label for="date_to">To</label>
                            <input type="datetime-local" name="date_to" class="form-control" id="date_to" value="" required>
                        </div>
                        <div class="col-md-6 col-sm-12 mt-2">
                            <label for="activity_type" class="form-label">Select a type</label>
                            <select name="activity_type" id="activity_type" class="form-control" required>
                                <option value="">Select activity type</option>
                                <option value="meeting">Meeting</option>
                                <option value="phone_call">Phone Call</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-12 mt-2">
                            <label for="location">Location</label>
                            <input type="text" name="location" class="form-control" id="location" value="" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="">
                            <label for="description">Notes <b class="text-danger">*</b></label>
                            <textarea class="form-control" id="notes" name="notes" rows="4"
                                placeholder="description goes here" onkeyup="this.value=this.value.toUpperCase()" required></textarea>
                          </div>
                    </div><br>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="card mt-3">
    <div class="bs-example">
        <ul class="nav nav-tabs">
        <!-- <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#quotations" role="tab">
            Quotations
            </a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#general_details" role="tab">
            Activities
            </a>
        </li>
        </ul>
        <div class="tab-content p-3 text-muted">
            <!-- <div class="tab-pane active" id="quotations">
                <div class="row  table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="lead_quotation_data_table" style="width:100%">
                        <thead class="">
                            <tr>
                                <th>Quote number</th>                      
                                <th>Class</th>  
                                <th>Quote Date</th> 
                                <th>Expected Amount</th>
                                <th>Action</th>                 
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div> -->
            <div class="tab-pane active" id="general_details">
                <div class="row table-responsive">
                    <table id="activities" class="table table-striped" style="width:100%">
                        <thead>
                        <th>Title</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Notes</th>
                        <th>Activity type</th>
                        <th>Status</th>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


</div>
@push("script")

<script>
    $(document).ready(function () {
        

        $('#lead_quotation_data_table').on('click', 'tbody td', function() {
            var qtno = $(this).closest('tr').find('td:eq(0)').text();
            // window.location="/brokerage/intermediary/Agent/quot/view/"+qtno+"/client"
            window.location="{{route('Agent.view_quote', '')}}"+"/"+qtno+"?source=lead";

        })
        $('#date_from, #date_to').on('change', function() {
        var startDate = new Date($('#date_from').val());
        var expiryDate = new Date($('#date_to').val());

        if (startDate > expiryDate) {
            toastr.warning('Date to cannot be greater than date from');
            $('#date_to').val('');
        } 
        });
        var lead = {!! json_encode($lead)  !!}
        var url = "{{ route('lead.activity') }}"; 
        url += "?lead_id=" + encodeURIComponent(lead.code); 
        console.log(lead);
        $('#activities').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: url,
            type: "get"
                        
        },
        columns: [
            {data:'title',name:'title'},
            {data:'from',name:'from'},
            {data:'date_to',name:'date_to'},
            {data:'email',name:'email'}, 
            {data:'activity_location',name:'activity_location'},    
            {data:'notes',name:'notes'},
            {data:'activity_type',name:'activity_type'},
            {data:'status',name:'status'},
            ]		
        });
    $('#book_activity').click(function(){
        $('#book_activity_modal').modal('show');
    });

    var updateStatusBtn = $('#updateStatusBtn');
    $('#lead_status').on('click', function () {
        updateStatusBtn.toggle();
    });
    var cancelStatusBtn = $('#cancelStatusBtn');
    var originalValue = $('#lead_status').val(); // Store the original value

    $('#lead_status').on('click', function () {
        updateStatusBtn.show();
        cancelStatusBtn.show();
    });

    $('#cancelStatusBtn').on('click', function () {
        // $('#lead_status').val(originalValue);
        updateStatusBtn.hide();
        cancelStatusBtn.hide();
    });

  
});
function updateStatus() {
        var selectedValue = $('#lead_status').val(); // Get the selected value from the <select>
        var updateStatusBtn = $('#updateStatusBtn');
        var updateStatusLoader = $('#updateStatusLoader');

        updateStatusBtn.hide();
        updateStatusLoader.show();
        // Make an AJAX call to update the status on the server
        $.ajax({
            url: "{{ route('update.lead.status') }}", // Replace with your actual route
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                lead_id: '{{ $lead->code }}',
                status: selectedValue
            },
            success: function(response) {
                toastr.success(response.message);
                updateStatusBtn.show();
                updateStatusLoader.hide();
                // Optionally, update any UI elements based on the response
            },
            error: function(xhr, status, error) {
                toastr.error("Unable to update status");
                updateStatusBtn.show();
                updateStatusLoader.hide();
            }
        });
    }

</script>
    
@endpush
@endsection