@extends('layouts.intermediaries.base')
@section('content')
<div class="card mt-2 mx-2">
        <div class="card-header">
            <h4>Upload </h4>
        </div>

        <div class="card-body p-2 step mb-3" id="">
            <div class="row">
            <div class="col-sm-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadmodal">
               Upload
            </button>
            </div>
                <div class="col-sm-4">
                    
                </div>
            </div>
        </div>
</div> 
<!-- Modal -->
<div class="modal fade" id="uploadmodal" tabindex="-1" aria-labelledby="uploadmodal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadmodal">Modal Title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="submitfleet">
      <div class="modal-body">
        <div class="row">
            <div class="col-sm-6">
                <label for="quote_fleet">Choose file</label>
                <input type='file'  id="quote_fleet"  class="form-control checkempty" name="quote_fleet" />

            </div>
            <div class="col-sm-6">
                <div class="" id="dwn_template"> 
                     <label for="quote_fleet">Download Template </label><br>

                    <a type="button"  class="btn  btn-sm mt-2" href="{{ route('dwnTemplate') }}"> <span class="fa fa-download"></span></a>                        
                </div>
                
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit"  id="submitform" class="btn btn-info">Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div> 
<div class="card mt-2 mx-2">
        <div class="card-header">
            <h4>Risk Details</h4>
        </div>

        <div class="card-body p-2 step mb-3" id="vehinfo">
            <table class="table table- striped  table-hover" id="risks_data_table" width="100%">
                <thead class="">
                    <tr>
                        <td>Registration number</td>
                        <td>Make</td>   
                        <td>Model</td>
                        <td>Body Type</td>
                        <td>Value</td>
                        <td>Action</td>            
                    </tr>
                </thead>
            </table>
        </div>
</div>
@endsection
@section('page_scripts')
<script type="text/javascript">
     $(document).ready(function () {
        var source ="{{$source}}";
        $('#risks_data_table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
           
            ajax:{
                'url' : "{{ route('get.quote.risks',['source'=>$source]) }}",
                'data' : function(d){
                        var quote_no= $('#quote_no').val()
                        d.quote_no=quote_no
                    },
            },
            
            columns: [
                {data:'reg_no',name:'reg_no'},
                {data:'make',name:'make'},
                {data:'model',name:'model'},
                {data:'body_type',name:'body_type'},
                {data:'sum_insured',name:'sum_insured',render: $.fn.dataTable.render.number( ',', '.', 2)},
                {data:'action',name:'action'},
                ]		
            });
        

     });
     $("#submitform").on("click", function (e) {
        e.preventDefault();
        var form = document.getElementById('submitfleet');
        let formData = new FormData(form);
        $.ajax({
                    type: "POST",
                    url: "{{ route('fleet_upload')}}",
                    dataType: "JSON",
                    data: formData,
                    contentType: false,
                    processData: false,
                    async: false,
                    success:function(resp){
                        if(resp.status == 0){
                        
                            $('#est_value').val(resp.value)
                        
                            $('#batch_no').val(resp.batch_no)
                            valid = true;
                     
                            console.log(valid);
            
                            var table = $('#risks_data_table2').DataTable()
                            table.ajax.reload();

                        }else{
                            let final=""
                            for (const key in resp) {
                                final+=`${key}: ${resp[key]}`
                                final+='<br><hr>'
                            
                            }
                            swal.fire({
                                icon: "error",
                                title: "Fleet Error",
                                html:"<h6 class='text-danger'>" +final+"</h6>"
                            })
                        }
                    }
                })

     });
 </script>
@endsection