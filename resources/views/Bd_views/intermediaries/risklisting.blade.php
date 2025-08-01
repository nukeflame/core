@extends('layouts.intermediaries.base')

@section('content')
<div class="col d-flex justify-content-start">
<a type="button" class="btn btn-secondary float-end mb-2" id="upload_screen" href="{{route('add.vehicle',['policy_no'=>$policy_no])}}">Add Vehicle</a>&nbsp;&nbsp;
    <a type="button" class="btn btn-secondary float-end mb-2" id="openfleetModalBtn" data-bs-toggle="modal" data-bs-target="#fleet_upload_modal">Upload Fleet</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
    <x-button.submit type="button" class="btn float-end mb-2" id="finish">
      <a href="{{ route('view.policies', ['policy_no' => $policy_no]) }}" style="text-decoration: none; color: inherit;">Finish</a>
   </x-button.submit>


</div> 
<div class="card">
  <div class="card-header">
    Vehicle Listing
  </div>
  <div class="card-body">
    <!-- <h5 class="card-title">Special title treatment</h5> -->
        <table class="table table-striped  table-hover" id="risks_data_table" width="100%">
            <thead class="">
                <tr>
                    <th>Reg No</th>
                    <th>Make</th>   
                    <th>Model</th>
                    <!-- <th>Body Type</th> -->
                    <th>Premium</th>
                    <th>Action</th>            
                </tr>
            </thead>
        </table>
  </div>
</div>  
 <!--fleet upload Modal  -->
 <div class="modal fade" id="fleet_upload_modal" tabindex="-1" aria-labelledby="fleet_upload_modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="fleet_form" enctype="multipart/form-data">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Upload Fleet</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
            
            
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="inputGroupFile01">Upload</label>
                        <input type="file" class="form-control" id="motorfleet" name="motorfleet">
                    </div>
                </div>
                <div class="col-md-4">
                <a href="{{ route('dwnTemplate') }}" type="button" class=" btn-sm btn-default mb-2"> <span class="fa fa-download"></span>Download Template  </a> 
                </div>
            </div>
            <input type="hidden" value="{{$policy_no}}" name="policy_no">
            <input type="hidden" value="{{$policy_dtl->class}}" name="class">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="uploadfleet" class="btn btn-primary">Save changes</button>
        </div>
        </div>
    </form>
  </div>
</div>
 <!-- end fleet modal -->
@endsection
@section('page_scripts')
<script>
     $(document).ready(function () {
        $('#risks_data_table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax:{
                'url' : '{{ route("get.motor.risks",["source"=>"client"]) }}',
                'data' : function(d){
                        var policy_no= "{{$policy_no}}"
                        d.policy_no=policy_no
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
        $('#risks_data_table').on('click', '.deletedetails', function() {
             var itemno = $(this).closest('tr').find('td:eq(0)').text();
             var policy_no =  "{{$policy_no}}";
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
        $('#uploadfleet').on('click', function(e){
            e.preventDefault()
            // let formdata = document.getElementById('fleet_form')
            // formdata  = new FormData(formdata)
            let formdata = new FormData($('#fleet_form')[0]);
            $.ajax({
                type: 'post',
                data:formdata,
                url: "{!! route('upload_motor_fleet')!!}",
                contentType: false,
                processData: false,
                success:function(data){
                    if (data.status == 0) {
                        toastr.success('Fleet Processed Successful', {
                            timeOut: 5000
                        });
                        location.reload();
                       
                    }else{
                        swal.fire({
                            icon: "error",
                            title: "Failed",
                            html:data.message
                        });
                    }

                }
            })
         })
            
        
     });
</script>
@endsection
