@extends('layouts.intermediaries.base')

@section('content')
<div class="card">
  <div class="card-header">
  <h5 class="card-title">Add Location Details</h5>
  </div>
  <form method="get" action="{{route('policy.postlocation')}}">
    <div class="card-body">
            
            <div class="row add_vehicle_div">

           
            <input type="hidden" value="{{$policy_dtl->class}}" name="class">
            <div id="location_div">
                        <div class="row">
                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="location" id="location"  placeholder="Enter location"  inputLabel="Location" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>
                        
                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="plot" id="plot"  placeholder="Enter plot number"  inputLabel="Plot Number" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                                <input type="hidden" name="bypasslocation" value="" id="bypasslocation">
                                <input type="hidden" name="engineering_project" value="" id="engineering_project">
                            </x-QuotationInputDiv>
                        
                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="town" id="town"  placeholder="Enter town"  inputLabel="Town" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="street" id="street"  placeholder="Enter street"  inputLabel="Street" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-SelectInput class="loc_det" name="earthquake" id="earthquake" req="required" inputLabel="Apply Earthquake">  
                                    <option value="">Select an option</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </x-SelectInput>
                            </x-QuotationInputDiv>

                            <div class="col-md-6  mt-2">
                                <x-TextArea class="loc_det" name="locdescription" id="locdescription"  inputLabel="Description" req="required" onkeyup="this.value=this.value.toUpperCase()">

                                </x-TextArea>
                            </div>
                        </div>
                    </div>
            
            
            
            </div><br>
            <input type="text" name="policy_no" id="policy_no" value="{{$policy_no}}" hidden> 
            <div class="card-footer">
                    <x-button.back type="button" class="save_car col-2" id="backdiv" ></x-button>
                    <x-button.submit type="submit" class="save_car col-2 float-end" id="add_veh" >Save</x-button>
            </div>
    </div>
  </form>
</div>
@endsection
@section('page_scripts')
<script>
     $(document).ready(function () {
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
                    // $(".model").empty()
                    $("#model").empty()
                    // var dropdown1 = $(".model");
                    // var $dropdown = $("#model");
                    $("#model").append($("<option />").val('').text('Choose vehicle model'));
                    $.each(data, function() {
                        $(".model").append($("<option />").val(this.model).text(this.model));
                    });
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
                        dropdown1.append($("<option />").val(this.bodytype).text(this.bodytype));
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
        $('#covtype').on('change', function() {
            var cls =$(this).val(); 
            if(cls == 2){
                $('.tpo_div').fadeIn();
                $('.non_tpo_div').fadeOut();

            }else{
                $('.tpo_div').fadeOut();
                $('.non_tpo_div').fadeIn();


            }
        });
      
        
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
