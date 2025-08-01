{{-- @extends('layouts.admincast') --}}
@extends('layouts.intermediaries.base')
@section('header', 'EDIT USER')
@section('content')
   
<style type="text/css">
	th.dpass, td.dpass {display: none;}
</style>

<div class="btn-group btn-breadcrumb float-left">
        <a href="{{ route('admin.dashboard') }}" style="color: black"><i class="fa fa-home mx-2"></i></a>
        <a href="#" class=" text-primary">Admin</a>
        <a href="#" class=" text-secondary"><i class="fa fa-angles-right mx-2"></i></a>
        <a href="#" class=" text-primary">Edit Client Details</a>
        
</div><br><br> 

<br />

@if($message = Session::get('success'))

@endif


@if ($errors->any())
<div class="alert alert-danger">
    <strong>warning!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif



  <div class="card mt-2">
        <div class="card-header">
           <strong>Client Update Details Approval</strong>
        </div>
    <div class="bs-example">

        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a href="#docs" class="nav-link active" data-toggle="tab">Client Details Requests</a>
            </li>
            <li class="nav-item">
                <a href="#approval" class="nav-link" data-toggle="tab">Pending Approval Client Details</a>

            </li>
        </ul>

        <div class="tab-content">
            <!-- tab 1 -->
            <div class="tab-pane fade show active" id="docs">
                <div class="card-body table-responsive">
                    <table class="table table-striped  table-hover datableta-" id="data-table">
                        <thead class="">
                            <tr>
                                
                                <th>client Id</th>                                      
                                <th class="dpass">transaction Id</th>                                      
                                <th>Global client Id</th>                                      
                                <th>client Name</th>
                                <th>Request type </th>
                                <th>Date Requested </th>
                                <th>Approved </th>
                                <th>Approved By </th>
                                <th>Approved Date </th>
                                <th>Action</th>
                         
                            </tr>
                        </thead>
                        <tbody>
			
						</tbody>
                    </table>
                </div>
            </div>
            <!-- tab 1 ends here -->
            
            <!-- tab 2 -->
            <div class="tab-pane fade" id="approval">
                <div class="card-body table-responsive">
                    <table class="table table-striped table-bordereless table-hover datableta-" id="data-table1" style="width:100%">
                        <thead class="">
                            <tr>
                                <th>client Id</th>                                      
                                <th>Global client Id</th>                                      
                                <th>client Name</th>
                                <th>Request type </th>
                                <th>Date Requested </th>
                                <th>Approved </th>
                                <th>Approved By </th>
                                <th>Approved Date </th>
                                <th>Action</th>
                         
                            </tr>
                        </thead>
                        <tbody>
			
						</tbody>
                    </table>
                </div>
            </div>
            <!-- tab 2 ends here -->
        </div>


    </div>
</div>



<div class="modal fade" id="approverequest_modal" role="dialog" aria-labelledby="adminClaimModalLabel"
  aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #cfd7e0 ">
                <h5 class="modal-title" id="adminClaimModalLabel">Client Details</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div>
                
                <form method="POST" action="{{ route('actionclientdetails') }}">
                    @csrf  
                    <div class="form-group">

                        <div class="row mx-2">
                            <div class="col-md-4 col-lg-4 ">
                                <label > First Name:</label>
                                <input type="text" class="form-control" id="fname"  readonly>
                            </div>
            
                            <div class="col-md-4 col-lg-4">
                                <label > Second Name:</label>
                                <input type="text" class="form-control" id="secname" readonly>
                            </div>
                            
                            <div class="col-md-4 col-lg-4 ">
                                <label > Surname:</label>
                                <input type="text" class="form-control" id="surname" readonly>
                            </div>
                            
                          
                            <div class="col-md-4 col-lg-4 ">
                                <label > Date Of Birth:</label>
                                <input type="text" class="form-control" id="dob" readonly>
                            </div>
                            
                            <div class="col-md-4 col-lg-4 ">
                                <label > Gender:</label>
                                <input type="text" class="form-control" id="gender" readonly>
                            </div>
                            
                            <div class="col-md-4 col-lg-4 ">
                                <label > PIN NO:</label>
                                <input type="text" class="form-control" id="pin_no" readonly>
                            </div>
            
                              <div class="col-md-4 col-lg-4 ">
                                <label > ID Number:</label>
                                <input type="text" class="form-control" id="idno"  readonly>
                            </div>
                          
                            <div class="col-md-3 col-lg-4">
                                <label > Email:</label>
                                <input type="text" class="form-control" id="email" readonly>
                            </div>
                            
            
                            <div class="col-md-3 col-lg-4 ">
                                <label > Phone No(Pri):</label>
                                <input type="text" class="form-control" id="phone1" readonly>
                            </div>
                            
                            <div class="col-md-3 col-lg-4 ">
                                <label >Phone No(Sec):</label>
                                <input type="text" class="form-control" id="phone2"  readonly>
                            </div>            
                            <div class="col-md-3 col-lg-4">
                                <label > Box number:</label>
                                <input type="text" class="form-control" id="box_no"  readonly>
                            </div>
                            
                            <div class="col-md-3 col-lg-4 ">
                                <label > code:</label>
                                <input type="text" class="form-control" id="code" readonly>
                            </div>
                            
                            <div class="col-md-3 col-lg-4 ">
                                <label >Town:</label>
                                <input type="text" class="form-control" id="town"  readonly>
                            </div>
                            
                            <div class="col-md-3 col-lg-4 ">
                                <label >Location:</label>
                                <input type="text" class="form-control" id="location"  readonly>
                            </div>
        
                        </div>

                        <div class="row mx-2">
                            <div class="col-md-3 col-lg-4 ">
                                <label class="required">Approve/Decline</label>
                                <select name="response" class="form-control" required>
                                    <option value="">Choose Option</option>
                                    <option value="A">Approve</option>
                                    <option value="D">Decline</option>
                                </select>
                            </div>
                        </div>
                    </div>

                  
                    <input name="user_id"  type="hidden"  id="user_id" >
                    <input name="trans_id" type="hidden" id="trans_id" >

                    <div class="modal-footer">
                        <span class="input-group-btn">
                            <input type="submit" value="Submit" id="save" class="btn btn-primary">
                        </span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>   
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('page_scripts')
    <script src="{{ asset('admincast/js/datatable.js') }}"></script>
    <script src="{{ asset('admincast/js/disable_date.js') }}"></script>
    <style>
        .bs-example {
            margin: 20px;
        }
    </style>
    <script src="{{ asset('admincast/js/disable_date.js') }}"></script>

    <script>
    $(document).ready(function (){


        $('#data-table').DataTable({
            'destroy': true,
            'paging' : true,
            'processing' : true,
            'serverside' : true,
             ajax:'{{route("admin.updatedetailsclient")}}',
            'columns': [      
                {data:'user_id', searchable: true},
                {data:'trans_id','sClass': 'dpass',searchable: true,},
                {data:'fname', searchable: true},
                {data:'global_customer_id', searchable: true},
                {data:'type', searchable: true},
                {data:'date_requested', searchable: true},
                {data:'approved', searchable: true},
                {data:'approved_by', searchable: true},
                {data:'approved_date', searchable: true},
                {data:'action', searchable: false},
               
            ]
        });
        

        $('#data-table tbody').on('click', '.viewrequest', function(e){

            var row = $(this).closest("tr"),
            userid = row.find("td:nth-child(1)").text(),
            trans_id = row.find("td:nth-child(2)").text();

            $('#user_id').val(userid)
            $('#trans_id').val(trans_id)

            getclientdetails(userid,trans_id)
            $('#approverequest_modal').modal('show');

        });

        function getclientdetails(userid,trans_id){

            $.ajax({
                url: "{{ route('admin.getclientdetails')}}",
                data: {'userid':userid,'trans_id':trans_id},
                type: "get",
                success: function(cldetails){
                   
                    $('#fname').val(cldetails.fname)
                    $('#secname').val(cldetails.secname)
                    $('#surname').val(cldetails.surname)
                    $('#dob').val(cldetails.birth_date)
                    $('#gender').val(cldetails.gender)
                    $('#pin_no').val(cldetails.pin_no)
                    $('#idno').val(cldetails.id_no)
                    $('#email').val(cldetails.email)
                    $('#phone1').val(cldetails.phone_1)
                    $('#phone2').val(cldetails.phone_2)
                    $('#box_no').val(cldetails.address_1)
                    $('#code').val(cldetails.address_2)
                    $('#town').val(cldetails.address_3)
                    $('#location').val(cldetails.town)
                  
                }
            });
        }
        
        $('#data-table1').DataTable({
            'destroy': true,
            'paging' : true,
            'processing' : true,
            'serverside' : true,
             ajax:'{{route("admin.approvedetailsclient")}}',
            'columns': [
                {data:'user_id', searchable: true},
                {data:'fname', searchable: true},
                {data:'global_customer_id', searchable: true},
                {data:'type', searchable: true},
                {data:'date_requested', searchable: true},
                {data:'approved', searchable: true},
                {data:'approved_by', searchable: true},
                {data:'approved_date', searchable: true},
                {data:'action', searchable: false},
               
            ]
        });


        // approverequest_modal

       
    })
    </script>

@endsection
