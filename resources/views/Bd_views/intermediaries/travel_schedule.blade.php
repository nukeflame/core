@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4">
            <div id="travel_schedule">
                <h5 class="text-start my-2">Travel Schedule</h5>
                <hr>
                <form id="travel_schedule_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="quote_no" value="{{ $quote->quote_no }}" id="quote">
                    <div class="" id="benefit_card">
                        <div class="card-body">
                            <div class="row m-2" id="detail_schedule">
                                <x-QuotationInputDiv>
                                    <x-Input class="travel_sched" name="full_name[]" id="full_name_0"  inputLabel="Full Name" req="required"/>
                                </x-QuotationInputDiv>
                                
                                <x-QuotationInputDiv>
                                    <x-DateInput class="travel_sched departure_date" name="departure_date[]" id="departure_date_0"  inputLabel="Departure Date" req="required"/>
                                </x-QuotationInputDiv>

                                <x-QuotationInputDiv>
                                    <x-Input class="travel_sched" name="passport[]" id="passport_0"  inputLabel="Passport Number" req="required"/>
                                </x-QuotationInputDiv>
                                
                                <x-QuotationInputDiv>
                                    <x-DateInput class="travel_sched dob" name="dob[]" id="dob_0"  inputLabel="Date Of Birth" req="required"/>
                                </x-QuotationInputDiv>

                                <x-QuotationInputDiv id="travel_with_0">
                                    <label for="travel_type">Minor To Travel With Insured?</label>
                                    <select name="travel_with_isured[]" id="travel_with_isured_0" class="form-control travel_with_isured">
                                        <option value="">Select Option</option>
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                    </x-QuotationInputDiv>

                                <x-QuotationInputDiv>
                                    <x-Input class="travel_sched" name="age[]" id="age_0"  inputLabel="Age" req="required" readonly/>
                                </x-QuotationInputDiv>

                                <x-QuotationInputDiv>
                                    <x-NumberInput class="travel_sched" name="duration[]" id="duration_0"  inputLabel="Duration" req="required"/>
                                </x-QuotationInputDiv>

                                <x-QuotationInputDiv>
                                    <label class="required">Premium</label>
                                    <div class="input-group">
                                        <input  name="premium[]" id="premium_0" required class="travel_sched form-control"  req="required" onchange="this.value=numberWithCommas(this.value)" />
                                        <span class="btn btn-primary" id="add_schedule">&plus;</span>
                                    </div>
                                </x-QuotationInputDiv>
                            </div>
                        </div>  
                    </div>

                    <hr>
                    <div>
                        <x-button.submit class="col-md-2 float-end" id="save_travel">Save</x-button>
                    </div>

                </form>
            </div>
        </div> 
      
        <div class="card-footer">
            <div>
                <x-button.back class="col-md-2 float-end mx-3" id="sections_back">Back</x-button>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
<script>
$(document).ready(function(){
    
    $("#travel_with_0").hide();
    let quote_no = $("#quote").val();
    let source = "{{$source}}";
    let location = $("#location").val();
    let plan = {!! $travel_details->plan !!}
    let persons = {!! $travel_details->no_of_persons - $travellers !!}


    $("#sections_back").on('click', function(){
        window.location="{{ route('travel_details')}}"+"?qstring={{Crypt::encrypt("quote_no=$quote->quote_no&source=$source")}}"           
        
    })

    
        
    var counter = 0;
    $('#add_schedule').on('click',function(){
        var age = $('#age_'+counter).val()
        var premium = $('#premium_'+counter).val()
        var dob = $('#dob_'+counter).val()
        var full_name = $('#full_name_' + counter).val()

        if (age == '' || premium == '' || dob == '' || full_name == '') {
            toastr.warning('Kindly fill the fields before moving on to the next row', {timeOut: 5500});
        } else {
            counter += 1;

            if (counter >= persons) {
                toastr.warning('Number of person cannot exceed the number in travel details', {timeOut: 5500});
            }else{

                $('#detail_schedule').append(
                    '<div class="row ml-0 mr-0 form-group">' + 
                        '<div class="col-md-3">' + 
                            '<label class="required">Full Name</label>' + 
                            '<input type="text" name="full_name[]" class="form-control" onkeyup="this.value = this.value.toUpperCase()" id="full_name'+ counter +'" required>' + 
                        '</div>' + 

                        '<div class="col-md-3">' + 
                            '<label class="required">Departure Date</label>' + 
                            '<input type="date" class="form-control departure_date" name="departure_date[]" id="departure_date_'+ counter +'" required>' + 
                        '</div>' + 

                        '<div class="col-md-3">' + 
                            '<label class="required">Passport</label>' + 
                            '<input type="text" class="form-control rate" name="passport[]" id="passport_'+ counter +'" required>' + 
                        '</div>' + 

                        '<div class="col-md-3">' + 
                            '<label class="required">Date of Birth</label>' + 
                            '<input type="date" class="form-control dob" name="dob[]" id="dob_'+ counter +'" required>' + 
                        '</div>' + 
                        '<div class="col-md-3" id="travel_with_'+counter+'">'+
                            ' <label for="travel_type">Minor To Travel With Insured?</label>'+
                            '<select name="travel_with_isured[]" id="travel_with_isured_'+counter+'" class="form-control travel_with_isured">'+
                                '<option value="">Select Option</option>'+
                                '<option value="Y">Yes</option>'+
                                '<option value="N">No</option>'+
                            '</select>'+
                        '</div>'+

                        '<div class="col-md-3">' + 
                            '<label class="required">Age</label>' + 
                            '<input type="text" class="form-control rate" name="age[]" id="age_'+ counter +'" required readonly>' + 
                        '</div>' + 

                        '<div class="col-md-3">' + 
                            '<label class="required">Duration</label>' + 
                            '<input type="number" class="form-control rate" name="duration[]" id="duration_'+ counter +'" required>' + 
                        '</div>' + 

                        '<div class="col-md-3">' + 
                            '<label class="required">Premium</label>' + 
                            '<div class="input-group">' + 
                                '<input type="text" class="form-control premium" name="premium[]" id="premium_'+ counter +'" onchange="this.value=numberWithCommas(this.value) " required>' +
                                '<span class="btn btn-danger" id="remove_section">&minus;</span>'+
                            '</div>' + 
                        '</div>' + 
                    '</div>'
                );
            }
            
            $("#travel_with_"+counter).hide();
        }
    });
        
    $(document).delegate('#remove_section', 'click', function(){
        counter--
        $(this).parent().parent().parent().remove();
    });

    $('.dob').on('change', function(){
        let id = $(this).attr('id')
        let id_length = id.length
        let rowID = id.slice(4, id_length)
        let dob = $(this).val()
        let dep_date = $('#departure_date_'+rowID).val()

        console.log(dob, dep_date);

        getAge(rowID, dep_date, dob)
    })

    $('#save_travel').on('click', function(e){
        e.preventDefault()
        let form = $("#travel_schedule_form")
        form.validate({
            errorElement: 'span',
            errorClass: 'text-danger fst-italic',
            highlight: function(element, errorClass) {
            },
            unhighlight: function(element, errorClass) {
            }
        });
        if (form.valid() === true){
            let data = $('#travel_schedule_form').serialize()
            $.ajax({
                type: 'POST',
                data:data,
                url: "{!! route('add_travel_schedule')!!}",
                success:function(data){
                    if (data.status == 1) {
                        toastr.success('Details saved.', {
                            timeOut: 5000
                        });
                        window.location="{{ route('travel_details')}}"+"?qstring={{Crypt::encrypt("quote_no=$quote->quote_no&source=$source")}}"           
                    }else{
                        swal.fire({
                            icon: "error",
                            title: "Failed",
                            html:"<h6>Check details and try again</h6>"
                        });
                    }

                }
            })
        }
    })


    $(".departure_date").on('change',function () {
        let departureDate = $(this).val();
        let id = $(this).attr('id');
        let id_length= id.length;
        let rowID = id.slice(15,id_length);

        let birthdate = $('#dob_'+rowID).val();
        console.log(rowID,departureDate,birthdate);
        getAge(rowID,departureDate,birthdate);

    });

    // get travel with val
    $(document).on("change",".travel_with_isured", function(){
        let travel_with_isured = $(this).val();
        let id = $(this).attr('id');
        let id_length= id.length;
        let rowID = id.slice(19,id_length);
        let duration = $("#duration_"+rowID).val();
        let age = $('#age_'+rowID).val();
        console.log(rowID,duration,plan,age, travel_with_isured);
        get_premium(rowID,duration,plan,age, travel_with_isured);
    })

    function get_premium(rowID,duration,plan,age, travel_with_isured){

        //ajax to get the total sum insured for the cover
        $.ajax({
            type:"get",
            url:"{{route('get_premium')}}",
            data:{
                'duration':duration,
                'plan':plan,
                'age':age,
                'field_no':rowID,
                'travel_with_isured':travel_with_isured,
                'quote_no':quote_no
            },
            success:function(resp){
                //set value for sum insured
                $('#premium'+rowID).val(resp);
                $('#premium_'+rowID).val(resp);
                
            },
            error: function(data){
                console.log(data);
                $('#premium_'+rowID).val(10);
            }
        })


    }

    function getAge(rowID,departureDate,dateString)
    {    
        let period_to = "{{ $quote->period_to }}"
        var days_covered=((new Date(period_to)- new Date(departureDate))/(1000 * 60 * 60 * 24))+1;

        console.log(days_covered);

        if(departureDate == "" || dateString == "") {
            $('#age_'+rowID).val('');
        }
        else{
            $('#duration_'+rowID).val(days_covered);
            $('#duration_'+rowID).prop('readonly', true);

            var duration = $('#duration_'+rowID).val();
            var plan = {!! $travel_details->plan !!}

            var today = new Date(departureDate);
            var birthDate = new Date(dateString);
            var age = today.getFullYear() - birthDate.getFullYear();
            console.log(age);
            var min_age = '';
            var default_travel_with = "N";
            $.ajax({
                url:"{{route('getMinAge')}}",
                type:"get",
                data:{'plan':plan, 'duration':duration},
                success:function(res){
                    var rec2 = res.group;
                    min_age = res.age_limit;

                    if(age < min_age && rec2 !== 'G'){

                        $("#travel_with_"+rowID).show();
                    }else{
                    get_premium(rowID,days_covered,plan,age, default_travel_with);
                    }
                },
                error:function(err){
                    console.log(err);
                    

                }
            })
            // var m = today.getMonth() - birthDate.getMonth();
            // if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate()))
            // {
            //     age--;
            // }
            $('#age_'+rowID).val(age);

        }
    }
})
</script>
@endsection