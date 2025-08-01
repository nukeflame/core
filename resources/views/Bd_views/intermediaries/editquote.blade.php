@extends('layouts.intermediaries.base')
@section('header', 'EDIT USER')
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
         <div class="card mt-3 mb-3 border border-secondary p-4 step" id="premium_info">
           
         <form id="premiumdetails">
           <div class="card-body">
               <div class="text-center">
               <div class="card text-start">
                   <div class="card-body">
                               <div class="row">
                                   <div class="col-md-9">
                                       <!-- benefits -->
                                       <div class="card" id="benefit_card">
                                           <div class="card-header">
                                               Added Benefits
                                                           
                                           </div>
                                           <div class="card-body">
                                                       <table class="table">
                                                           <thead>
                                                               <tr>
                                                               <th scope="col">Description</th>
                                                               <th scope="col">Amount</th>
                                                               <th scope="col">Select</th>
                                                               </tr>
                                                           </thead>
                                                           <tbody>
                                                           @foreach($sections as $section)
                                                           <tr class="section">
                                                               <td>
                                                                   <div class="benefit_amt">
                                                                           <div class="d-block font-weight-bold benefit_desc">
                                                                               {{$section->description}}
                                                                           </div>
                                                                       </div>
                                                               </td>
                                                               <td>
                                                                   <div class="benefit_amt">
                                                                           
                                                                           <div class="d-block">
                                                                               <span class="benefit_amount"  benefit_rate="{{$section->rate_amount}}"></span>
                                                                           </div>
                                                                       </div>
                                                               </td>
                                                               <td>
                                                                      
                                                                       <div class="form-check">
                                                                           <input  name="bencheck[]" type="checkbox" value="{{$section->item_code}}" benefit_id="{{$section->item_code}}" id="{{$section->item_code}}" benefit_desc="{{$section->description}}" benefit_amount="" @if(in_array($section->item_code, $selected_bens))
                                                                           class="form-check-input remove_benefit" checked
                                                                            @else
                                                                            class="form-check-input add_benefit"
                                                                            @endif>
                                                                       </div>
                                                               </td>

                                                           </tr>
                                                                   
                                                                   
                                                                   
                                                                   
                                                           
                                                           @endforeach
                                                               
                                                           </tbody>
                                                       </table>
                                           <br>

                                           </div>  
                                       </div>
                                       <!--  -->

                                   </div>
                                   
                                   <div class="col-md-3">
                                   
                                           
                                               <div class="card">
                                                   <div class="card-header">
                                                       Premium Summary
                                                                   
                                                   </div>
                                                   <div class="card-body">
                                                       <div class="row">
                                                           <div class="col-sm">
                                                               <div>
                                                                   <small>Basic Premium</small>
                                                               </div>
                                                           </div>
                                                           <div class="col-sm">
                                                                <div>
                                                                  <h6 class="text-success font-weight-bold">Mlw. <span id="basic_premium"></span> </h6>
                                                                </div>
                                                           
                                                           </div>
                                                       
                                                       
                                                       </div>
                                                       <hr>
                                                       <div class="row">
                                                           
                                                           <div class="col-sm">
                                                               <div>
                                                                   <small>Benefit Amount</small>
                                                               </div>
                                                               

                                                           </div>
                                                           <div class="col-sm">
                                                            
                                                               <div>
                                                                   <h6 class="text-success font-weight-bold" >Mlw. <span id="benefit_total"></span> </h6>
                                                               </div>
                                                               <!-- <div>
                                                               <h6 class="text-success font-weight-bold">Mlw. <span id="total_premium"></span></h6>
                                                               </div> -->

                                                           </div>
                                                           
                                                       </div>
                                                       <hr>
                                                       <div class="row">
                                                           
                                                           <div class="col-sm">
                                                               <div>
                                                                   <small>Discount</small>
                                                               </div>
                                                               

                                                           </div>
                                                           <div class="col-sm">
                                                            
                                                               <div>
                                                                   <h6 class="text-success font-weight-bold" >-Mlw. 0<span id=""></span> </h6>
                                                               </div>
                                                              

                                                           </div>
                                                           
                                                       </div>
                                                       <hr>
                                                       <div class="row">
                                                           
                                                           <div class="col-sm">
                                                               <div>
                                                                   <small>Total Premium</small>
                                                               </div>
                                                               

                                                           </div>
                                                           <div class="col-sm"
                                                               <div>
                                                               <h6 class="text-success font-weight-bold">Mlw. <span id="total_premium"></span></h6>
                                                               </div>

                                                           </div>
                                                           
                                                       </div
                                                     
                                                       
                                                   </div>
                                           
                                               
                                               </div>
                                               <!-- <h5>Added benefits</h5> -->
                                               <div class="card" id="ext" style="display:none">
                                                   <div class="card-body" id="extensions">
                                                       <div class="alert alert-info">No added benefits</div>
                                                   </div>
                                               </div>

                                       
                                           
                                               <input type="text" name="quote_no" id="batch_no" value="{{$quote_no}}" hidden>
                                               <input type="text" name="version" id="version" value="{{$quotation->version}}" hidden>
                                               <x-input.text type='hidden'  id="premium_rate" name="premium_rate" value="{{$quotation->rate}}" />
                
                                               <!-- <input type="text" name="total_prem" id="total_prem_field" hidden> -->
                                               <input class="form-control" type="text" id="basic_prem" name="basic_prem" hidden>
                                         
                                       
                                       
                                   </div>
                             
                               </div>
                   </div>
               </div>
                   <div class="row mt-4 fleet">
                     
                           <div class="card" id='rsk-table'>
                               <div class="card-header">
                                  Risk Details
                                   
                                               
                               </div> 
                               <div class="card-body">
                               <!-- <button type="button" class="btn btn-secondary" id ="reuploadrsk">Re Upload</button> -->
                              
                                   <div class="table-responsive">
                                   <x-button.next type="button"   class="btn btn-sm float-end mb-2" data-bs-toggle="modal" data-bs-target="#uploadmod">Re Upload Risk Details</x-button>
                                       <table class="table table- striped  table-hover" id="risks_data_table" width="100%">

                                           <thead class="bg-secondary text-white">
                                               <tr>
                                                   <td>Registration number</td>
                                                   <td>Make</td>   
                                                   <td>Model</td>
                                                   <td>Body Type</td>
                                                   <td>Value</td>
                                                   <td>Action</td>            
                                               </tr>
                                           </thead>
                                       
                                           <tbody>
                                               
                                           </tbody>
                                           
                                       </table>
                                   </div> 

                               </div>
                  
                              
                           </div>
                          
                       
                       
                   </div>
                 
                  
               </div>
           </div>
         </form>
        <div class="row">
        <div class="col-md-7">
        </div>
        <div class="col-md-5">
           <a  id="updatedtls" class="btn  btn-outline-success float-end  "  role="button"><i class="fa fa-save" aria-hidden="true"></i>  Update Risk Details  </a>

        </div>
      
        </div>
       </div>
       @component('components.modal', [
    'id'    => 'exampleModal',
    'class' => 'modal-xl',
    ])
        @slot('title', 'Risk Details')

        @slot('body')
            <!-- Body of the modal -->
            <div class="text-center">
            
                    <div class="row">
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header">
                                Summary
                                                                
                                </div>
                                <div class="card-body">
                                   
                                    <a type="button" class="btn  btn-sm float-end" id='editbtn' style="background-color:transparent">
                                
                                    <i class="fa fa-edit"></i>edit</a><br><br>
                                
                                    <div class="card p-2 mb-2" id='sumdiv'>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12 mt-2">
                                                <div>
                                                    <small>Make</small>
                                                </div>
                                                <div>
                                                    <h6 class="text-success font-weight-bold rsk_make"><span id="rsk_make"></span> </h6>
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-4 col-sm-12 mt-2">
                                                <div>
                                                    <small>Model</small>
                                                </div>
                                                <div>
                                                    <h6 class="text-success font-weight-bold rsk_model"><span id="rsk_model"></span></h6>
                                                </div>
                                                
                                            </div>
                                           
                                            <div class="col-md-4 col-sm-12 mt-2">
                                                <div>
                                                    <small>Registration Number</small>
                                                </div>
                                                <div>
                                                    <h6 class="text-success font-weight-bold rsk_reg"><span id="rsk_reg"></span></h6>
                                                </div>
                                                
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-12 mt-2">
                                                <div>
                                                    <small>Value</small>
                                                </div>
                                                <div>
                                                    <h6 class="text-success font-weight-bold rsk_value"><span id="rsk_value"></span></h6>
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-4 col-sm-12 mt-2">
                                                <div>
                                                <small>Basic Premium</small>
                                                </div>
                                                <div>
                                                    <h6 class="text-success font-weight-bold rsk_prem">Mlw. <span id="rsk_prem"></span></h6>
                                                </div>
                                                
                                            </div>
                                            <div class="col-md-4 col-sm-12 mt-2">
                                                
                                                <div>
                                                    <small>Total Premium</small>
                                                </div>
                                                <div>
                                                    <h6 class="text-success font-weight-bold rsk_tprem">Mlw. <span id="rsk_tprem"></span></h6>
                                                </div>
                                                
                                            </div>

                                        </div>
    
                                    
                                    </div>
                                    <div class="card p-2 mb-2" id='editdiv' style="display:none">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 mt-2">
                                            <label for="make" class="form-label">Make:</label>

                                            <select name="make" id="make"  class="form-control  make"  onchange="changeMake(this.value)" required>
                                                <option value="">Vehicle Make</option>
                                                @foreach ($models as $model)
                                                    <option value="{{ $model->make }}">{{ $model->make }}</option> 
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-sm-12 mt-2">
                                            <label for="model" class="form-label">Model:</label>
                                            <select class="form-select form-control  model" aria-label="Default select example" id="model" name="model"  onchange="changeModel(this.value)" required>
                                                <option value="">Vehicle model</option>
                                            </select>
                                            
                                        </div>
                                        <div class="col-md-4 col-sm-12 mt-2">
                                            <label for="body_type" class="form-label">Body type:</label>

                                            <select name="body_type" id="body_type"  class="form-control  body_type" onchange="changeBody()" required>
                                                <option value="">Body type:</option>
                                            </select>
                                            <x-input.text type='hidden'  id="bdy_type" name="bdy_type" />
                                             
                                        </div>

                                    </div>
                                    <div class="row">
                                    
                                        <div class="col-md-4 col-sm-12 mt-2">
                                        
                                            <x-input.label label="Registration number" for="quote_fleet">
                                                <x-input.text type='text'  id="edit_reg" name="edit_reg"  required/>
                                            </x-input.label>
                                        </div>
                                        <div class="col-md-4 col-sm-12 mt-2">
                            
                                            <x-input.label label="Vehicle value" for="edit_value">
                                                <x-input.text type='text'  id="edit_value" name="edit_value"  required/>
                                            </x-input.label>
                                        </div>
                                        <div class="col-md-4 col-sm-12 mt-2">
                                            <x-input.label label="Basic Premium" for="edit_prem">
                                                <x-input.text type='text'  id="edit_prem" name="edit_prem"  disabled/>
                                            </x-input.label>
                                        </div>

                                    </div>
                                    <br>
                                    <div class="row">
                                            <div class="col-md-4 col-sm-12 mt-2">
                                                <x-button.submit type="button"   class="btn-sm float-end" id="savebtn">Save</x-button>
                                            </div>
                                    
                                            <div class="col-md-4 col-sm-12 mt-2">
                                            <x-button.reject type="button"   class="btn-sm float-end" id="cancelbtn">Cancel</x-button>
                                            
                                            </div>
                                            
                                        
                                            <div class="col-md-4 col-sm-12 mt-2">
                                            </div>

                                    
                                        

                                    </div>

                                        
                                        


                                    </div>
                                        
                                    <!-- <h5>Added benefits</h5> -->
                                    <div class="card" id="ext_risk" style="display:none">
                                        <div class="card-body" id="risk_extensions">
                                            <div class="alert alert-info">No added benefits</div>
                                        </div>
                                    </div>

                                </div>
                                <form id="premiumdetails">
                                    <input type="text" name="total_prem" id="total_prem_field" hidden>
                                    <!-- <input type="text" name="total_prem" id="total_prem_field" hidden> -->
                                    <input class="form-control" type="text" id="basic_prem" name="basic_prem" hidden>
                                </form>
                            
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            
                            <!--  -->
                                            <div class="card">
                                                <div class="card-header">
                                                    Risk Benefits
                                                                
                                                </div>
                                                <div class="card-body">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                    <th scope="col">Description</th>
                                                                    <th scope="col">Amount</th>
                                                                    <th scope="col">Action</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($sections as $section)
                                                                <tr class="section">
                                                                    <td>
                                                                        <div class="benefit_amt">
                                                                                <div class="d-block font-weight-bold benefit_desc">
                                                                                    {{$section->description}}
                                                                                </div>
                                                                            </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="benefit_amt">
                                                                                
                                                                                <div class="d-block">
                                                                                    <span class="benefit_amount"  benefit_rate="{{$section->rate}}"></span>
                                                                                </div>
                                                                            </div>
                                                                    </td>
                                                                    <td>
                                                                        
                                                                            <div class="form-check">
                                                                                <input class="form-check-input add_risk_benefit" type="checkbox" benefit_id="{{$section->item_code}}" benefit_desc="{{$section->item_code}}" benefit_amount="" disabled>
                                                                            </div>
                                                                    </td>

                                                                </tr>
                                                                        
                                                                        
                                                                        
                                                                        
                                                                
                                                                @endforeach
                                                                    
                                                                </tbody>
                                                            </table>
                                                

                                                </div>  
                                            </div>
                            <!--  -->
                        </div>
                    </div>
                
                </div>
        @endslot 

        @slot('footer')
            <!-- Footer of the modal -->
        
            <x-button.back type="button" id="markcomplete">Close</x-button>
       
        @endslot
    @endcomponent
@endsection
@section('page_scripts')
<script>
    $(document).ready(function () {
       
    
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let est ="{{$summary['sumins']}}"
            let basic="{{$summary['annual_prem']}}"
            let benamt="{{$summary['ben_amt']}}"
            calculateBenefitAmount(est)
            $('#basic_premium').text(Number(basic))
            $('#benefit_total').text(Number(benamt))
            $('#total_premium').text(Number(basic) + Number(benamt));
    
             var table=$('#risks_data_table').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    'createdRow': function (row, data, rowIndex)
                        {
                            // Per-cell function to do whatever needed with cells
                            var indx=0;
                            $.each($('td', row), function (colIndex) {
                                console.log($(this).name)
                                
                                var column = ['reg_no','make','model','body_type','value']
                                // For example, adding data-* attributes to the cell
                                $(this).attr('title', column[indx]);
                                indx++
                            });
                        },
                    ajax:{
                            'url' : '{{ route("edit.quote.risks") }}',
                            'data' : function(d){
                                var batch= '{{$quote_no}}'
                                d.batch_no=batch
                                d.version ='{{$quotation->version}}'
                                },
                         },
                    
                    columns: [
                        {data:'reg_no',name:'reg_no', className: 'editable'},
                        {data:'make',name:'make'},
                        {data:'model',name:'model'},
                        {data:'body_type',name:'body_type'},
                        {data:'value',name:'value',className: 'editable',render: $.fn.dataTable.render.number( ',', '.', 2)},
                        {data:'action',name:'action'},
                        
                        ]		
            });
            // 
            // Step 2: Add the 'editable' class to the cells in the column
            table.on('draw.dt', function () {
            $('.editable', this).each(function () {
                $(this).attr('contenteditable', true);
            });
            });
            // Step 3: Add a click event listener to the cells
            $('#risks_data_table tbody').on('click', 'td.editable', function () {
            var $cell = $(this);
            var oldValue = $cell.text().trim();
            var title = $cell.attr('title');
            var reg = $(this).closest('tr').find('td:eq(0)').text()
            
            
            // Step 4: Replace the cell's contents with an input field
            $cell.html('<input type="text" class="' + title + '" id="' + reg + '" value="' + oldValue + '">');
            
            // Set focus to the input field
            $cell.find('input').focus();
            });
            // Step 5: Send an AJAX request to update the database
            $('#risks_data_table tbody').on('blur', 'td.editable input', function () {
            var $input = $(this);
            var newValue = $input.val().trim();
            var reg = $input.attr('id').trim();
            var title = $input.attr('class').trim();
            var batch_no =  $('#batch_no').val();
            var version =  '{{$quotation->version}}';
            var cell = $input.parent();


            
            if (newValue !== cell.text().trim()) {
                // Make an AJAX request to update the database
                $.ajax({
                url: "{!! route('update.datatable')!!}",
                type: 'POST',
                data: {
                    reg: reg,
                    'title':title,
                    batch_no: batch_no,
                    value: newValue,
                    version:version
                },
                success: function (res) {
                    // Step 6: Update the DataTable with the new data
                    cell.text(newValue);
                    //console.log(res)
                    calculateBenefitAmount(Number(res))
                }
                });
            } else {
                // Step 6: Update the DataTable with the new data
                cell.text(newValue);
            }
            });
                     
    });
        $('#risks_data_table2').on('click', 'tbody td', function(e) {
            e.preventDefault()
           
           var itemno = $(this).closest('tr').find('td:eq(0)').text();
           var quote_no =  '{{$quote_no}}';
           var value = $(this).closest('tr').find('td:eq(4)').text();
           amt=value.replace(/\,/g,'')
           amt=Number(amt)
       
          

       
          $.ajax({
              type: 'GET',
              data:{'itemno':itemno,'quote_no':quote_no,'edit_flag':'Y'},
              async: false ,
              url: "{!! route('view.risk')!!}",
              success:function(data){
                 console.log(data)
                 $('#premium_rate').val(data.rate);
                 var rskprem = amt * data.rate/100;
                 $('#rsk_make').text(data.make)
                 $('.make').val(data.make)
                 $('.make').trigger('change'); 
                 $('#rsk_model').text(data.model)
                 $('#bdy_type').val(data.body_type)
                 $('.body_type').val(data.body_type)
                 $('#rsk_reg').text(data.reg_no)
                 $('#edit_reg').val(data.reg_no)
                 $('#rsk_value').text(value)
                 $('#edit_value').val(amt)
                 $('#rsk_prem').text(rskprem)
                 $('#edit_prem').val(rskprem) 
                 $('#rsk_tprem').text(rskprem)
                 console.log("check here"); 
                 console.log(data.Benefits); 
                 $('#risk_extensions').empty()
                 for (const ben of data.Benefits){
                 
                
                //   let tp = Number(ben.premium) +Number(rskprem)
                let tp = Number(rskprem)
                  $('#rsk_tprem').text(tp)

                      //$('#ext_risk').css('display', 'block')
                 $('#risk_extensions').children('.alert').addClass('d-none')
                 $('#risk_extensions').append('<div class="d-flex border border-bottom justify-content-between text-align-center added_benefit"><div><div class="added_desc">' + ben.ben_desc + '</div><div class="fw-bold text-success added_amount">'+ben.ben_amount+'</div></div><div class="float-right"><button class="btn btn-sm remove_risk_benefit" ben_id="'+ben.ben_id+'"><span class="fa fa-times"></span></button></div></div>')
                  }
               
                  
                  
                 
                //      $('#exampleModal').modal('show');
                 
              }
          });

        
          // window.location="{{route('view.risk', '')}}"+"/"+itemno;

        })
        $('.make').on('change', function() {
            
            var cls =$( ".make option:selected" ).text(); 
            var make =$( ".make option:selected" ).val();
         
            console.log(make);
            $("#clss").val(cls)
            $.ajax
            ({
                type: 'GET',
                data:{'make':make},
                url: "{!! route('fetchmodels')!!}",
                success:function(data){
                    $(".model").empty()
                var dropdown1 = $(".model");
                var $dropdown = $("#model");
                console.log(data)
                
                        $.each(data, function() {
                          var val =  $('#rsk_model').text()
                              if(this.model == val){
                                
                                dropdown1.append(`<option selected value="${this.model}">${this.model}</option>`)

                              }else{
                                dropdown1.append($("<option />").val(this.model).text(this.model));
                              }
                           
                     
                        });
                        // $(".model").val('Belta');
                        $('.model').trigger('change');
                                    
                
                
                }
            });
           
            
        });
        $('.model').on('change', function() {
            var cls =$( ".model option:selected" ).text(); 
            var model =$( ".model option:selected" ).val();
            
           
            $("#clss").val(cls)
            $.ajax({
            type: 'GET',
            data:{'model':model},
            url: "{!! route('fetchbody')!!}",
            success:function(data){
               var $dropdown = $("#body_type");
               var dropdown1 = $(".body_type");
                $('#body_type').empty()
                   $dropdown.append($("<option />").val('').text('Choose body'));
                    $.each(data, function() {
                       var val =  $('#bdy_type').val()
                        if(this.bodytype== val){
                             
                                dropdown1.append(`<option selected value="${this.bodytype}">${this.bodytype}</option>`)

                        }else{
                            dropdown1.append($("<option />").val(this.bodytype).text(this.bodytype));
                        }
                  
                    });
                    //$(".body_type").val('SAL')
                                   
                    
               
            }
              });
           
            
        });
            function changeMake(make) {
                $('#v_make').text(make)
            }
            function changeModel(model) {
                $('#v_model').text(model)
            }
            function changeBody() {
                $("#body_type").trigger("chosen:updated");
                let body = $("#body_type").val()
                $('#v_body').text(body)
            }
            function changeYear(year) {
                $('#v_myear').text(year)
            }
            function calculateBenefitAmount(est){
             
                $('#v_value').text(est)
                let prem = est*$('#premium_rate').val()/100
                $('#basic_premium').text(prem)
                $('#qbasic').text(prem)

                $("#basic_prem").val(prem)

                let tot_prem = prem
                if ($('#extensions,#risk_extensions').children('.added_benefit').length < 1) {
                
                    $('#total_premium').text(prem)
                    $('#total_prem_field').val(prem)
                    $('#qtotal').text(prem)
                }else{
                
                    $('.added_benefit').each(function(){
                        let ben = Number($(this).find('.added_amount').text())
                        tot_prem = tot_prem + ben;
                    });

                    $('#total_premium').text(tot_prem)
                    $('#total_prem_field').val(tot_prem)
                    $('#qtotal').text(tot_prem)
                }
                var totbenamt =0
                $('.benefit_amount').each(function(){
                     //console.log(checked);
                     let vehicle_value = est;
                    let ben_rate = $(this).attr('benefit_rate');
                    
                    let benefit_amount = vehicle_value*ben_rate/100;

                    $(this).text(benefit_amount);
                  
                    var checked =$(this).parents('tr.section').find('.remove_benefit')
                    if (checked.is(':checked')){
                        totbenamt = totbenamt+ benefit_amount
                      
                    }
                   
                });
                $('#benefit_total').text(Number(totbenamt))
                
            }
            $('body').on('click','.add_benefit', function(){
               
                $(this).removeClass("add_benefit")
                $(this).addClass("remove_benefit")
                //    $(this).html('<span class="fa fa-times"></span>');
                let ben_id = $(this).attr('benefit_id');

                let ben_desc = $(this).attr('benefit_desc');
                let ben_amount =  $(this).parents('tr.section').find('.benefit_amount').text()


                console.log(ben_amount,ben_desc,ben_id);

                let basic = $('#basic_premium').text()
                let total = $('#total_premium').text()
                $("#basic_prem").val(basic)
                $("#qbasic").text(basic)

                total = Number(total) + Number(ben_amount)

                $('#total_premium').text(total)
                $('#qtotal').text(total)
                $('#total_prem_field').val(total)
                $('#benefit_total').text(Number(total) - Number(basic))
                var quote_no =  '{{$quote_no}}';
                
                // // $('#ext').css('display', 'block')
                // $('#extensions').children('.alert').addClass('d-none')
                // $('#extensions').append('<div class="d-flex border border-bottom justify-content-between text-align-center added_benefit"><div><div class="added_desc">' + ben_desc + '</div><div class="fw-bold text-success added_amount">'+ben_amount+'</div></div><div class="float-right"><button class="btn btn-sm remove_benefit" ben_id="'+ben_id+'"><span class="fa fa-times"></span></button></div></div>')
                // $('#premiumdetails').append('<input class="benefit_'+ben_id+'" name="extensions[]" value="'+ben_amount+'" hidden>')
                // $('#premiumdetails').append('<input class="benefit_'+ben_id+'" name="ext_types[]" value="'+ben_id+'" hidden>')
                $.ajax({
                type: 'GET',
                data:{'ben_id':ben_id,'quote_no':quote_no,'delete_flag':'Y'},
                url: "{!! route('modify.ben')!!}",
                success:function(data){
                    
                   if(data.status == 1){
                    let rate =data.rate;
                    let mimimun = data.mimimun;
                 
                    $('#premium_rate').val(rate)


                   }
                                    
                
                
                }
            });
            })
            $('body').on('click','.remove_benefit', function(){
                
                // $(this).parents('div.added_benefit').removeClass("border border-bottom")
                // $(this).parents('div.added_benefit').addClass("d-none")
                $(this).removeClass("remove_benefit")
                $(this).addClass("add_benefit")
           
                
                let ben_id = $(this).attr('benefit_id')
                $(".benefit_"+ben_id).remove()


                // let ben_amount =  $(this).parents('div.added_benefit').find('.added_amount').text()
                // console.log(ben_desc);
                let ben_amount =  $(this).parents('tr.section').find('.benefit_amount').text()
                let ben_desc = $(this).attr('benefit_desc');
                
                if ($('#extensions').children(':visible.added_benefit').length < 1) {
                    $('#extensions').append('<div class="alert alert-info">No added benefits</div>')
                }
                
                let total = $('#total_premium').text()
                

                total = Number(total) - Number(ben_amount)
                let basic = $('#basic_premium').text()
                $('#total_premium').text(total)
                $('#total_prem_field').val(total)
                    $('#benefit_total').text(Number(total) - Number(basic))

                $(':hidden.section').each(function(){
                    let hidden_desc = $(this).find('.benefit_desc').text()
                    if($.trim(hidden_desc) === $.trim(ben_desc)){
                        $(this).removeClass("d-none")
                        $(this).addClass("d-block border border-bottom")
                    }


                });
            })
            $('#editbtn').on('click',function(){
                $('#editbtn').hide()
                $('#sumdiv').hide()
                $('#editdiv').show()
            

            })
            $('#cancelbtn').on('click',function(){
                $('#editbtn').show()
                $('#sumdiv').show()
                $('#editdiv').hide()
                

            })
            $('#savebtn').on('click',function(){
                var val = $('#edit_value').val()
                var prem = val * $('#premium_rate').val()/100
             
                var batch_no =  $('#batch_no').val();
                var reg_no=$('#edit_reg').val()
                var quote_no =  '{{$quote_no}}';
                var version =  '{{$quotation->version}}';
                var rate = $('#premium_rate').val()
                $('#savebtn').text('updating...').button("refresh");
            
                $.ajax({
                    type: 'GET',
                    data:{'val':val,'reg_no':reg_no,'quote_no':quote_no,'rate':rate,'version':version},
                    url: "{!! route('edit.risk')!!}",
                    success:function(data){
                        console.log(data);
                        if(+data.status == 1){
                            calculateBenefitAmount(Number(data.value))
                            $('#rsk_value').text(val)
                            $('#rsk_prem').text(prem)
                            $('#edit_prem').val(prem) 
                            $('#rsk_tprem').text(Number(prem))
                            $('#total_premium').text(data.value * $('#premium_rate').val()/100 +Number(data.ben_amount))
                            $('#benefit_total').text(Number(data.ben_amount))
                            // $('#rsk_tprem').text(Number(prem)+Number(data.ben_amount))
                            var table = $('#risks_data_table').DataTable()
                            table.ajax.reload();

                        }
                        $('#savebtn').text('save').button("refresh");
                        $('#editbtn').show()
                        $('#sumdiv').show()
                        $('#editdiv').hide()
                
                
                    }
                });
    
    
            })
            
            $('#updatedtls').click(function(){
                let myform = document.getElementById('premiumdetails');
                let formdata = new FormData(myform);
                $.ajax({
                    type: 'post',
                    data: formdata,
                    url: "{{ route('editquote') }}",
                    processData: false,
                    contentType: false,
                    success:function(res){
                        console.log(res)
                        if (res.status == 1) {
                            swal.fire({
                                icon: "success",
                                title: "Quotation details send",
                                text: "Quotations has been registered pending approval"
                            })
              
                            window.location="{{route('Agent.view_quote', '')}}"+"/"+"{{$quote_no}}";
                        } else {
                            toastr.error(res.msg, {
                                timeOut: 5000
                            });
                        }
                    }
                })
            });
</script>
@endsection