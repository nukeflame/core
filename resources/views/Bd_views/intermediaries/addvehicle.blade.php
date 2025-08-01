@extends('layouts.intermediaries.base')

@section('content')
<nav class="page-title fw-semibold fs-18 mb-0 bg-white mt-2 mb-2 p-1" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='currentColor'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Client</li>
            <li class="breadcrumb-item"><a href="#" id="to-customer">{{ Str::ucfirst(strtolower($customer->full_name)) }}</a></li>
            <li class="breadcrumb-item">Cover</li>
            <li class="breadcrumb-item"><a href="#" id="to-cover">{{$policy_dtl->policy_no}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cover Details</li>
            <li class="breadcrumb-item"><a href="#" id="to-cover">{{$policy_dtl->policy_no}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Vehicle(s)</li>
        </ol>
</nav>

<div class="card">
  <div class="card-header">
  <a type="button" class="btn btn-primary  mb-2 text-white" id="openfleetModalBtn" data-bs-toggle="modal" data-bs-target="#fleet_upload_modal">Upload Fleet</a>
  </div>
  <form method="post" action="{{route('policy.postvehicle')}}">
    @csrf
    <div class="card-body">
            
            <div class="row add_vehicle_div">

           
            <input type="hidden" value="{{$policy_dtl->class}}" name="class">
            <x-QuotationInputDiv>
                <x-SelectInput name="covtype" id="covtype" req="required" inputLabel="Cover Type" class="select2">
                 
                    

                </x-SelectInput>
                
            

            </x-QuotationInputDiv>

            <x-QuotationInputDiv>
                <x-SelectInput name="subclass" id="usage" req="required" inputLabel="Usage" class="select2" onchange="getCoverType()">
                    
                </x-SelectInput>
            </x-QuotationInputDiv>
            <x-QuotationInputDiv class="notfleet">
                <x-SelectInput class="make select2" name="make" id="make" req="required" inputLabel="Make">
                    <option value="">Vehicle Make</option>
                    @foreach ($models as $model)
                         @if($riskdtls != '')
                            @if($riskdtls->make ==$model->make)
                            <option value="{{ $model->make }}" selected>{{ $model->make }}</option> 
                            @endif
                         @else
                            <option value="{{ $model->make }}" >{{ $model->make }}</option> 
                         @endif

                    @endforeach
                </x-SelectInput>
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                <x-SelectInput class="model select2" name="model" id="model" req="required" inputLabel="Model">
                    <option value="">Select vehicle model</option>
                </x-SelectInput>
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                <x-SelectInput class="body_type select2" name="body_type" id="body_type" req="required" inputLabel="Body Type">
                    <option value="">Select body type</option>
                </x-SelectInput>
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                @if($riskdtls != '')
                    <x-SelectInput name="manufacture_yr" id="manufacture_yr" req="required" inputLabel="Manufacture Year" class="select2">
                        <option value="">Manufacture year</option>
                        @for ($year = 2024; $year >= 1988; $year--)
                            <option value="{{ $year }}" @if($riskdtls->yom == $year) selected @endif>{{ $year }}</option>
                        @endfor
                    </x-SelectInput>
                @else
                    <x-SelectInput name="manufacture_yr" id="manufacture_yr" req="required" inputLabel="Manufacture Year" class="select2">
                        <option value="">Manufacture year</option>
                        @for ($year = 2024; $year >= 1988; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </x-SelectInput>
                @endif
                <input type="hidden" name="cls" value="" id="cls">
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet non_tpo_div">
                    @if($riskdtls != '')
                         <x-Input name="sum_insured" id="est_value"  placeholder="Enter vehicle value"  inputLabel="Vehicle Value" req="required" value="{{$riskdtls->sum_insured}}" />

                    @else
                      <x-Input name="sum_insured" id="est_value"  placeholder="Enter vehicle value"  inputLabel="Vehicle Value" req="required" />

                    @endif

            </x-QuotationInputDiv>
            <x-QuotationInputDiv class="notfleet non_tpo_div">
            @if($riskdtls != '')
                <x-NumberInput name="premium_rate" id="premium_rate"  placeholder="Enter Premium Rate"  inputLabel="Premium Rate" req="required" value="{{$riskdtls->rate}}" />

            @else
                 <x-NumberInput name="premium_rate" id="premium_rate"  placeholder="Enter Premium Rate"  inputLabel="Premium Rate" req="required" />

            @endif
            </x-QuotationInputDiv>
            <x-QuotationInputDiv class="notfleet non_tpo_div">
            @if($riskdtls != '')
            <x-Input name="premium" id="premium"  placeholder="Premium"  inputLabel="Premium" req="required" value="{{$riskdtls->annual_prem}}" />

            @else
            <x-Input name="premium" id="premium"  placeholder="Premium"  inputLabel="Premium" req="required" />

            @endif
            </x-QuotationInputDiv>
            <x-QuotationInputDiv class="notfleet tpo_div">
                @if($riskdtls != '')
                <x-Input name="tpo_prem" id="tpo_prem"  placeholder="Enter Third party premium"  inputLabel="Premium" req="required" value="{{$riskdtls->tpo_prem}}" />

                @else
                <x-Input name="tpo_prem" id="tpo_prem"  placeholder="Enter Third party premium"  inputLabel="Premium" req="required" />

                @endif
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                @if($riskdtls != '')
                <x-Input name="chasis_no" id="chasis_no"  placeholder="Enter chassis number"  inputLabel="Chassis Number" req="required" value="{{$riskdtls->chasis}}" onkeyup="this.value=this.value.toUpperCase()"/>

                @else
                <x-Input name="chasis_no" id="chasis_no"  placeholder="Enter chassis number"  inputLabel="Chassis Number" req="required" onkeyup="this.value=this.value.toUpperCase()"/>

                @endif
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                @if($riskdtls != '')
                <x-Input name="reg_no" id="reg_no"  placeholder="Enter registration number"  inputLabel="Registration Number" req="required" value="{{$riskdtls->reg_no}}" onkeyup="this.value=this.value.toUpperCase()"/>

                @else
                <x-Input name="reg_no" id="reg_no"  placeholder="Enter registration number"  inputLabel="Registration Number" req="required" onkeyup="this.value=this.value.toUpperCase()"/>

                @endif
                

            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                @if($riskdtls != '')
                <x-Input name="engine_no" id="engine_no"  placeholder="Enter engine number" value="{{$riskdtls->engno}}"  inputLabel="Engine Number" req="required" onkeyup="this.value=this.value.toUpperCase()"/>

                @else
                <x-Input name="engine_no" id="engine_no"  placeholder="Enter engine number"  inputLabel="Engine Number" req="required" onkeyup="this.value=this.value.toUpperCase()"/>

                @endif
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                @if($riskdtls != '')
                <x-NumberInput name="seat_cap" id="seat_cap"  placeholder="Enter seating capacity" value="{{$riskdtls->seat_cap}}" inputLabel="Seat Capacity" req=""/>

                @else
                <x-NumberInput name="seat_cap" id="seat_cap"  placeholder="Enter seating capacity"  inputLabel="Seat Capacity" req=""/>

                @endif
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                @if($riskdtls != '')
                <x-NumberInput name="cc" id="cc"  placeholder="Enter cubic capacity"  value="{{$riskdtls->cc}}" inputLabel="Engine cc" req=""/>

                @else
                <x-NumberInput name="cc" id="cc"  placeholder="Enter cubic capacity"  inputLabel="Engine cc" req=""/>

                @endif
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                <x-SelectInput name="motive_p" id="motive" req="" inputLabel="Motive Power">
                    <option value="">Select motive type</option>
                    <option value="PTR">Petrol</option>
                    <option value="DSL">Diesel</option>
                    <option value="ETC">Electric</option>
                    <option value="HBD">Hybrid</option>
                    
                </x-SelectInput>
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                <x-SelectInput name="condition" id="condition" req="" inputLabel="Condition">
                    <option value="">Select vehicle condition</option>
                    <option value="Good">Good</option>
                    <option value="Average">Average</option>
                    <option value="Poor">Poor</option>
                </x-SelectInput>
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                <x-SelectInput name="met_color" id="met_color" req="" inputLabel="Metallic Color">
                    <option value="">Choose an option</option>
                    <option value="Y">Yes</option>
                    <option value="N">No</option>
                </x-SelectInput>
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
                 @if($riskdtls != '')
                    <x-SearchableSelect name="color" id="color" req="" inputLabel="Color">
                        <option value="">Select vehicle color</option>
                        @foreach($carcolors as $color)
                                @if(trim($riskdtls->color) == trim($color->color) )
                                    <option value="{{$color->color}}" selected>{{$color->color}}</option>
                                @else
                                     <option value="{{$color->color}}">{{$color->color}}</option>

                                @endif

                        @endforeach
                    </x-SearchableSelect>
                @else
                    <x-SearchableSelect name="color" id="color" req="" inputLabel="Color">
                        <option value="">Select vehicle color</option>
                        @foreach($carcolors as $color)
                        <option value="{{$color->color}}">{{$color->color}}</option>
                        @endforeach
                    </x-SearchableSelect>
                @endif
            </x-QuotationInputDiv>
            <x-QuotationInputDiv class="notfleet">
                     <x-Input name="owner" id="owner"  placeholder="" value="{{$customer->full_name}}" inputLabel="Owner" req="required" disabled/>
            </x-QuotationInputDiv>

            <x-QuotationInputDiv class="notfleet">
            @if($riskdtls != '')
            <x-Input name="owner" id="owner"  placeholder="Enter vehicle owner"  inputLabel="Logbook Owner" value="{{$customer->full_name}}" req=""/>

            @else
            <x-Input name="owner" id="owner"  placeholder="Enter vehicle owner"  inputLabel="Logbook Owner" req=""/>

            @endif
            </x-QuotationInputDiv>
            
            
            
            </div><br>
            <input type="text" name="policy_no" id="policy_no" value="{{$endorsement_no}}" hidden> 
            <div class="card-footer">
                    <x-button.back type="button" class="col-2" id="backdiv" ></x-button>
                    <x-button.submit type="submit" class="save_car col-2 float-end" id="add_veh" >Save</x-button>
            </div>
    </div>
  </form>
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
            <input type="hidden" value="{{$endorsement_no}}" name="policy_no">
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
    var covtype = "";
    var make = "";
    var model = "";
    var btype = "";
    var motive = "";
    var condition = "";
    var metallic_color = "";
    var color = "";
      @if(isset($riskdtls))
    var covtype = "{{$riskdtls->covtype}}"
    var make = "{{$riskdtls->make}}"
    var model = "{{$riskdtls->model}}"
    var btype = "{{$riskdtls->body_type}}"
    var motive = "{{$riskdtls->motive_power}}"
    var condition = "{{$riskdtls->condition}}"
    var metallic_color = "{{$riskdtls->metallic_color}}"
    var color = "{{$riskdtls->color}}"
    @endif
    
     $(document).ready(function () {
       // $('#make').trigger("change")
    
       
        $('.tpo_div').css('display', 'none');
        var cls ="{{$policy_dtl->class}}"; 
           
          
            $.ajax({
                type: 'GET',
                data:{'cls':cls},
                url: "{!! route('agent.fetchcovertypes')!!}",
                success:function(data){
                    $("#covtype").empty()
                    $("#covtype").append($("<option />").val('').text('Choose cover type'));
                    $.each(data, function() {
                     
                        
                      
                       if (covtype == this.cover){  
                            $("#covtype").append($("<option />").val(this.cover).text(this.cover_description).prop('selected', true));
                 
                       }else{
                        $("#covtype").append($("<option />").val(this.cover).text(this.cover_description));

                       }
                    });
                }
            });

            $.ajax({
                type: 'GET',
                data:{'cls':cls},
                url: "{!! route('agent.fetchclasstypes')!!}",
                success:function(data){
                    $("#usage").empty()
                    $("#usage").append($("<option />").val('').text('Choose usage type'));
                    
                    $.each(data, function() {
                        
                        if(covtype ==this.classtype){
                           $("#usage").append($("<option />").val(this.classtype).text(this.description).prop('selected', true));

                        }else{
                            $("#usage").append($("<option />").val(this.classtype).text(this.description));

                        }
                    });
                }
            });
        $('#make').on('change', function() {
 
            var cls =$( ".make option:selected" ).text(); 
            var make =$( ".make option:selected" ).val();
     
            $("#clss").val(cls)
            $.ajax
            ({
                type: 'GET',
                data:{'make':make},
                url: "{!! route('fetchmodels')!!}",
                success:function(data){
                    // $(".model").empty()
                    $("#model").empty()
                    // var dropdown1 = $(".model");
                    // var $dropdown = $("#model");
                    $("#model").append($("<option />").val('').text('Choose vehicle model'));
                    $.each(data, function() {
                        if(model ==this.model){
                            $(".model").append($("<option />").val(this.model).text(this.model).prop('selected', true));
                          

                        }else{
                            $(".model").append($("<option />").val(this.model).text(this.model));

                        }
                    });
                @if(isset($riskdtls))

                    $('#model').val(model).trigger('change');
                    $('#motive').val(motive).trigger('change');
                    $('#condition').val(condition).trigger('change');
                    $('#met_color').val(metallic_color).trigger('change');
                @endif
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
                url: "{!! route('fetchbody')!!}",
                success:function(data){
                   
                var $dropdown = $("#body_type");
                var dropdown1 = $(".body_type");
                    $('#body_type').empty()

                    $dropdown.append($("<option />").val('').text('Choose body'));
                        $.each(data, function() {
                            if(this.bodytype == btype){
                                dropdown1.append($("<option />").val(this.bodytype).text(this.bodytype).prop('selected', true));

                                }
                             else{
                               
                                dropdown1.append($("<option />").val(this.bodytype).text(this.bodytype));

                            }
                        });
                }
            });
        });
        $('#classbs').on('change', function() {
            var cls =$(this).val(); 
            $("#cls").val(cls)
          
            $.ajax({
                type: 'GET',
                data:{'cls':cls},
                url: "{!! route('agent.fetchcovertypes')!!}",
                success:function(data){
                    $("#covtype").empty()
                    $("#covtype").append($("<option />").val('').text('Choose cover type'));
                    $.each(data, function() {
                     
                        
                      
                       if (covtype == this.cover){
                            $("#covtype").append($("<option />").val(this.cover).text(this.cover_description).prop('selected', true));
                 
                       }else{
                        $("#covtype").append($("<option />").val(this.cover).text(this.cover_description));

                       }
                    });
                }
            });

            $.ajax({
                type: 'GET',
                data:{'cls':cls},
                url: "{!! route('agent.fetchclasstypes')!!}",
                success:function(data){
                    $("#usage").empty()
                    $("#usage").append($("<option />").val('').text('Choose usage type'));
                    
                    $.each(data, function() {
                        
                        if(covtype ==this.classtype){
                           $("#usage").append($("<option />").val(this.classtype).text(this.description).prop('selected', true));

                        }else{
                            $("#usage").append($("<option />").val(this.classtype).text(this.description));

                        }
                    });
                }
            });
         

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
                        window.history.back();
                    
                       
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
         $('#backdiv').on('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            window.history.back(); // Navigate back
        });
        $('#covtype').on('change', function() {
           
            var covtypeValue =$(this).val();
            if(covtypeValue != 1){
                $('.tpo_div').fadeIn();
                $('.non_tpo_div').fadeOut();
                $('#est_value').removeAttr('required');
                $('#premium_rate').removeAttr('required');
                $('#premium').removeAttr('required');
                $('#tpo_prem').attr('required', 'required');


            }else{
                $('.tpo_div').fadeOut();
                $('.non_tpo_div').fadeIn();
                $('#tpo_prem').removeAttr('required');
                $('#premium_rate').attr('required', 'required');
                $('#premium').attr('required', 'required');



            }
            var manufactureYr = $('#manufacture_yr');
            var options = '';

            if (covtypeValue == 1) {
                for (var year = 2024; year >= 2009; year--) {
                    options += '<option value="' + year + '">' + year + '</option>';
                }
            } else {
                for (var year = 2024; year >= 1950; year--) {
                    options += '<option value="' + year + '">' + year + '</option>';
                }
            }

            manufactureYr.html(options);
        });
         @if(isset($riskdtls))
            $('#make').val(make).trigger('change');
           
        @endif
      
        
     });
     $('#covtype').trigger('change');
 
     $('#est_value').on('keyup', function() {
                var inputVal = $(this).val();
                var numericVal = inputVal.replace(/\D/g, '');
                var rate =  $('#premium_rate').val();
                if (!isNaN(numericVal) && !isNaN(rate)) { // Check if numericVal and itemValue are valid numbers
                var prem = (rate * numericVal) / 100;
                var formattedVal = prem.toLocaleString(); // Use toLocaleString to format numbers with commas

                    $('#premium').val(formattedVal);
                } else {
                    $('#premium').val(''); // Clear the premium field if either input is not a valid number
                }

                var formattedVal = numericVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $(this).val(formattedVal);
      });
      $('#tpo_prem').on('keyup', function() {
                var inputVal = $(this).val();
                var numericVal = inputVal.replace(/\D/g, '');
                var formattedVal = numericVal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $(this).val(formattedVal);
      });
      $('#premium_rate').on('keyup', function() {
            var inputVal = parseFloat($(this).val()); // Parse the input value to a floating point number
            var value = $('#est_value').val();
            var numericVal = value.replace(/,/g, '');
            var itemValue = numericVal;

            if (!isNaN(inputVal) && !isNaN(itemValue)) { // Check if inputVal and itemValue are valid numbers
                var prem = (itemValue * inputVal) / 100;
                var formattedVal = prem.toLocaleString(); // Use toLocaleString to format numbers with commas

                $('#premium').val(formattedVal);
            } else {
                $('#premium').val(''); // Clear the premium field if either input is not a valid number
            }
            
        });

     function getCoverType() {
        let val = $('#covtype option:selected').attr('value')
        let cls = $('#classbs option:selected').attr('value')
        let est = $('#est_value').val()
        let usage =$('#usage').val();

        $.ajax({
            type: 'GET',
            data:{'id':val,'class':cls,'usage':usage},
            url: "{!! route('get.rate')!!}",
            success:function(data){
                if(data.status == 1){
                    let rate =data.rate;
                    let mimimun = data.mimimun;
                    let basis = data.basis;
                    if ($('[name="fleetswitch"]').is(':checked')){ 
                      

                    }else{
                        if(basis == "A"){
                            $('#premium_rate').val(100)
                            $('#est_value').val(0)
                            $('.non_tpo_div').fadeOut();
                            $('.tpo_div').fadeIn();
                            $(".add_benefit").prop("disabled", true);
                            $('.non_tpo_div').find('.checkempty').each(function(){
                                $(this).removeClass('checkempty');
                            });
                        }else{
                            $('#premium_rate').val(rate)
                            $('.non_tpo_div').fadeIn();
                            $('.tpo_div').fadeOut();
                            $('.tpo_div').find('.checkempty').each(function(){
                                $(this).removeClass('checkempty');
                            });

                        }
                    }
                  
                
                    
                }
            }
        });

    }
</script>
@endsection
