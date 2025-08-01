@extends('layouts.intermediaries.base')
@section('header', 'Claims')
@section('content')

@if ($message = Session::get('success'))

    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
<blockquote class="blockquote">
      <p class="mb-2 text-secondary"><h5 class="text-secondary">Vehicle Details</h5></p>
</blockquote>
<div class="row float-right mr-3 mb-2">
            <div class="float-right"><br>
                        <!-- <a href="#" class="btn btn-default float-right"  id="quotation" role="button" style="cursor: pointer;" data-toggle="#discount_modal">Add Vehicle</a> -->
                        <a class="btn btn-info float-right text-light" href="#" id="next" style="cursor: pointer;" >Next Page</a>
            </div>
</div>

<div class="row float-left ml-3 mb-2">
            <div class="float-left"><br>
                        <!-- <a href="#" class="btn btn-default float-right"  id="quotation" role="button" style="cursor: pointer;" data-toggle="#discount_modal">Add Vehicle</a> -->
                        <a class="btn btn-info float-left text-light" id="add_discount" style="cursor: pointer;" data-toggle="#discount_modal">Add Vehicle<i class="fa fa-plus ml-1" aria-hidden="true"></i> </a>
            </div>
</div>

<br>



<!--****************modal for attachments****************-->
<div class="modal fade" id="discountsdata_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" style="width: 999px; max-height: 96vh;
    overflow-y: scroll !important;">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#3e43b0">
        <h4 class="modal-title text-light">Risk details</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
          <table class="table table-black table-borderless" cellspacing="0" width="100%" id="unts-table">
            <thead >
             <tr>
                <th>Make</th>
                <th>Registration Number</th>
                <th>Rate</th>
                <th>Value</th>
                <th>Annual Premium</th>
                <th>Quotation Date</th>
                <th>Action</th>
              </tr>

            </thead>
          </table>
      </div>

      <div class="modal-footer">
        <!--<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>-->

      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--****************End of modal for attachments****************-->
<div class="modal fade" id="discount_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#3e43b0">
         
          <h4 class="modal-title text-light">Add Vehicle</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form id="risk_form">
                    <div class="row" style="margin-top:10px;">
               
                                <div class="col-sm-4">
                                    <label>Make <span style="color:#ff0000">*</span></label>
                                            <select class="form-select form-control" aria-label="Default select example" id="make" name="make">
                                                <option selected disabled>Select Make</option>
                                                @foreach($models as $model)
                                                <option value="{{$model->make}}">{{$model->make}}</option>
                                                @endforeach
                                                
                                        </select>
                                </div>
                                <div class="col-sm-4">
                                    <label>Model <span style="color:#ff0000">*</span></label>
                                    <select class="form-select form-control" aria-label="Default select example" id="model" name="model">
                                    </select>
                                            
                                </div>
                                <div class="col-sm-4">
                                    <label>Body Type <span style="color:#ff0000">*</span></label>
                                    <select class="form-select form-control" aria-label="Default select example" id="body_type" name="body_type" required>
                                    </select>
                                    <input type="hidden" class="form-control" id="quote_no" name="quote_no"
                                            value="{{$quote_no}}" required>
                                            <input type="hidden" class="form-control" id="cls_select" name="class"
                                            value="{{$class}}" required>      
                                </div>
                             
                              
                                <div class="col-sm-4">
                                    <label>Registration Number: <span style="color:#ff0000">*</span></label>
                                    <input type="text" class="form-control" id="reg_no" name="reg_no"
                                            value="" required>
                                </div>
                                <div class="col-sm-4">
                                    <label>Year of Manufacture: <span style="color:#ff0000">*</span></label>
                                    <input type="text" class="form-control" id="yom" name="yom"
                                            value="" required>
                                </div>
                                <div class="col-sm-4">
                                    <label>Cover Type <span style="color:#ff0000">*</span></label>
                                            <select class="form-select form-control" id="covtype" name="covtype" aria-label="Default select example" required>
                                                <option selected disabled>Select Cover Type</option>
                                                
                                                <option value="1">Comprehesive</option>
                                                <option value="2">THIRD PARTY FIRE & THEFT</option>
                                                <option value="3">THIRD PARTY ONLY</option>
                                                
                                                
                                            </select>
                                </div>
                              
                                    <div class="col-sm-4" id="comp">
                                        <label>(Total) Sum Insured <span style="color:#ff0000">*</span></label>
                                        <input type="text" class="form-control" id="sumins" name="sumins"
                                                value="">
                                    </div>
                                    <div class="col-sm-4" id="comp1">
                                        <label>Rate <span style="color:#ff0000">*</span></label>
                                        <input type="text" class="form-control" id="rate" name="rate"
                                                value="" readonly>
                                    </div>
                                    <div class="col-sm-4" id="tpo">
                                        <label>Amount</label>
                                        <input type="text" class="form-control" id="tpo_amount" name="tpo_amount"
                                                value="" readonly>
                                    </div>
                               
                                
                                
                        

                    </div>           
                    <div class="modal-footer col-xs-12 col-sm-12 col-md-12 text-center">
                                <button type="submit" id="submit_risk" class="btn btn-info text-light">Save</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                            </div>
            </form>

     
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  <!--****************End of modal for extension addition****************-->

<div class="card  mt-5">
        <div class="table-responsive card-body">
            
            <table class="table table-striped table-bordered table-hover" id="discounts-table">
            <thead >
             <tr>
                <th>Make</th>
                <th>Registration Number</th>
                <th>Cover Type</th>
                <!-- <th>Rate</th> -->
                <th>Value</th>
                <th>Annual Premium</th>
                <th>Quotation Date</th>
                <th><a class="openmod" id="addiscount" style="cursor: pointer;" data-toggle="#discount_modal"><i
                      class="fa fa-plus-square-o"></i> Add</a></th>
              </tr>

            </thead>
          </table>
        </div>
    </div>


    @endsection
    @section('page_scripts')
    <script src="{{ asset('admincast/js/datatable.js') }}" ></script>
    <script>
      
        $(document).ready(function () {
            
            $("#myInput").on("change", function() {
            var value = $(this).val().toLowerCase();

            $("#data-table > tbody > tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
            });
            $("#quot_details").hide();
        
            $('#discounts-table').DataTable({
                
                processing: true,
                serverSide: true,
                ajax: {
                                url: "{{ route('Agent.qouterisk.get')}}",
                                type: "get",
                                data: function (d) {
                                    d.quote_no = $('#quote_no').val();
                                },
                        },
                columns: [
                    {data:'make',name:'make'},
                    {data:'reg_no',name:'reg_no'}, 
                    {data:'cover',name:'cover'},        
                    // {data:'rate',name:'rate'},
                    {data:'sum_insured',name:'sum_insured',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'annual_prem',name:'annual_prem',render: $.fn.dataTable.render.number( ',', '.', 2)},
                    {data:'quote_date',name:'quote_date'},
                    {data:'namelink',name:'namelink'}
                    // {data:'total_240_days',name:'total_240_days'},
                    // {data:'total_270_days',name:'total_270_days'},
                    // {data:'total_300_days',name:'total_300_days'},
                    // {data:'total_330_days',name:'total_330_days'},
                    // {data:'total_365_days',name:'total_365_days'},
                    // {data:'over_365_days',name:'over_365_days'}
                    ]		
		    });
            //hidden fields
            $("#tpo").hide();
            $("input[required]").parent("label").addClass("required");

        });
       
        $("#next").on("click", function(e){

           e.preventDefault()
           
           var table = $('#discounts-table').DataTable();

            if ( ! table.data().any() ) {
                toastr.error('Please add atleast one vehicle', {timeOut: 5000});
            }else{
            window.location.href = "{{ route('Agent.view_quote',$quote_no)}}";
            }
            
        })
        
       
        $('#make').on('change', function() {
            var cls =$( "#make option:selected" ).text(); 
            var make =$( "#make option:selected" ).val();
   
            $("#clss").val(cls)
            $.ajax({
            type: 'GET',
            data:{'make':make},
            url: "{!! route('agent.fetchmodels')!!}",
            success:function(data){
               console.log(data)
               var $dropdown = $("#model");
                $('#model').empty()
                    $.each(data, function() {
                        $dropdown.append($("<option />").val(this.model).text(this.model));
                    });
                                   
               
               
            }
              });
           
            
        });
        $('#model').on('change', function() {
            var cls =$( "#model option:selected" ).text(); 
            var model =$( "#model option:selected" ).val();
   
            $("#clss").val(cls)
            $.ajax({
            type: 'GET',
            data:{'model':model},
            url: "{!! route('agent.fetchbody')!!}",
            success:function(data){
               console.log(data)
               var $dropdown = $("#body_type");
                $('#body_type').empty()
                    $.each(data, function() {
                        $dropdown.append($("<option />").val(this.bodytype).text(this.bodytype));
                    });
                                   
               
               
            }
              });
           
            
        });
        //get motorrates
        $('#covtype').on('change', function() {
            var cls =$( "#covtype option:selected" ).text(); 
            var covtype_val =$( "#covtype option:selected" ).val();
            var cls_val =$( "#cls_select" ).val();
            
            $.ajax(
                {
                type: 'GET',
                data:{'covertype':covtype_val,'class':cls_val},
                url: "{!! route('agent.fetchmotorates')!!}",
                success:function(data){
                    var data = data[0]
                    var minrate = data.basic
                    var minvalue = data.minimum
                    var cover = data.cover
                    $('#rate').val(minrate)
                    console.log(minrate)
                    
                    if (cover == 3) {
                        $("#comp").hide();
                        $("#comp1").hide();
                        $('#tpo_amount').val(minvalue)
                        $("#tpo").show();
                        
                    }else{
                        $("#comp").show();
                        $("#comp1").show();
                        $("#tpo").hide();
                    }
                                    
                
                
                }
            });
           
            
        });
        $('#view_disc').on('click',function(){
         
            var cls_val =$( "#cls_select option:selected" ).val();
            if(cls_val==0 || typeof cls_val == 'undefined' ){
            toastr.warning('You have not entered a valid vehicle registration number', {timeOut: 5000});
            }else{
            $('#discountsdata_modal').modal({backdrop: 'static'});
            $('#discountsdata_modal').modal('show');
            }
        });
        $('#add_discount').on('click',function(){
           
           
                $('#discount_modal').modal({backdrop: 'static'});
                $('#discount_modal').modal('show');

        });
        $('#submit_risk').on('click',function(e){
            e.preventDefault();
            $("#submit_risk").html("Saving...");
            $.ajax({
            url: "{!! route('Agent.postrisk')!!}",
            type: 'post',
            dataType: 'json',
            data: $('#risk_form').serialize(),
            success: function(data) {
                var number = (data[0].sum + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
                    // ... do something with the data...
                    $('#discounts-table').DataTable().ajax.reload();
                    $('#total_discounts').val(number);
                    $('#discount_modal').modal('hide');
                    toastr.success('Vehicle added successfully', {timeOut: 5000});
                    console.log(data[0].sum)
                
                    }
        })
        $("#submit_risk").html("Save");

        }) 
        $('#remove_risk').on('click',function(e){
            alert($('#remove_risk').val())
            e.preventDefault();
            $.ajax({
            url: "{!! route('client.postrisk')!!}",
            type: 'post',
            dataType: 'json',
            data: $('#risk_form').serialize(),
            success: function(data) {
                    // ... do something with the data...
                    $('#discounts-table').DataTable().ajax.reload();
                    $('#discount_modal').modal('hide');
                    toastr.success('Vehicle added successfully', {timeOut: 5000});
                    console.log(data)
                  
                    }
        });

        }) 
       
        $('#removerisko').on("click",function(){
  //post code
})
    </script>
    <style>
        .required:before{
        content:"*";
        font-weight:bold;
        }
    </style>
    @endsection
  