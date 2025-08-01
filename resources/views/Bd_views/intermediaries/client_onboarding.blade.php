@extends('layouts.intermediaries.base')
@section('content')

<style>
    {
        margin: 0;
        padding: 0
    }

    html {
        height: 100%
    }

    p {
        color: grey
    }


    .card {
        z-index: 0;
        border: none;
        position: relative
    }

    .primary-color{
        color: #E1251B;
    }
    hr{
        margin: 20px 0px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 ">
            <div class="card px-0 pt-4 pb-0 mb-3">
                <h5 class="text-center text-lg-start mb-0 mx-2">Client Onboarding <span class="primary-color" id="prospect_name"></span></h5>
                <hr>
                <div class="card-body">
                    <form id="msform">
                        @csrf
                        <input type="hidden" name="agent_onboard_client" value="Y">
                        </ul>
                        <fieldset>
                            <div class="form-card">
                                <div class="individual">
                                    <div class="row">
                                        <B class="primary-color">Client Details</B>
                                        <div class="m-0"><hr></div>
                                        <input type="hidden" name="prospect_id" id="prospect_id" value="">
                                        <x-OnboardingInputDiv class=" col-md-3 col-sm-12 mt-2">
                                            <x-SearchableSelect name="client_type" id="client_type" req="required" inputLabel="Client Type">
                                                <option value="">Select client type</option>
                                                <option value="C">Corporate</option>
                                                <option value="I">Retail</option>
                                                <option value="N">NGO</option>
                                                <option value="G">Government</option>
                                                <option value="S">SME's</option>
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-SearchableSelect name="client_category" id="client_category" req="required" inputLabel="Client Category">
                                                <option value="">Select prospect category</option>
                                                <option value="N">New prospect</option>
                                                <option value="O">Organic growth</option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv style="display:none" id="corporate">
                                            <x-Input name="corporate_name" placeholder="Enter company name"
                                            id="corporate_name" value="{{ old('corporate_name') }}" inputLabel="Company Name" req="required"  onkeyup="this.value=this.value.toUpperCase();" />
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="salutation_code">
                                            <x-SearchableSelect name="salutation_code" id="salutation_code" req="" inputLabel="Salutation">
                                                <option value="">Select salutation</option>
                                                    @foreach ($salutations as $salutation)
                                                        <option value="{{$salutation -> name}}">{{ $salutation -> name }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="full_name_div">
                                            <x-Input id="full_name" name="fname" req="required" inputLabel="Full Name" value="{{ old('fname') }}" placeholder="Enter full name" onkeyup='this.value=this.value.toUpperCase();'/>
                                        </x-OnboardingInputDiv>




                                        <x-OnboardingInputDiv id="idTypeDiv">
                                            <x-SelectInput name="id_type" id="id_type" req="required" inputLabel="ID Type">
                                                <option value="">Select ID Type</option>
                                                <option value="N">National ID</option>
                                                <option value="P">Passport ID</option>
                                                <option value="F">Foreigners ID</option>
                                                <option value="M">Military ID</option>
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="genderDiv">
                                            <x-SelectInput name="gender" id="gender_code" class="form-control checkempty" req="required" inputLabel="Gender">
                                                <option value="">Select gender</option>
                                                @foreach ($genders as $gender)
                                                    <option value="{{$gender -> gender_code}}">{{ $gender -> name }}</option>
                                                @endforeach
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="occupationDiv">
                                            <x-SearchableSelect name="occupation_code" id="occupation_code" req="required" inputLabel="Industry">
                                                <option value="">Select occupation</option>
                                                @foreach ($occupations as $occupation)
                                                    <option value="{{$occupation ->name}}">
                                                        {{ $occupation -> name }}
                                                    </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="division_div" >
                                            <x-SearchableSelect name="division" id="division" req="required" inputLabel="Division">
                                                <option value="">Select division</option>
                                                    @foreach ($divisions as $division)
                                                        <option value="{{$division -> id}}">{{ $division -> name }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv >
                                            <x-SearchableSelect name="insurance_class" id="insurance_class" req="required" inputLabel="Class of Insurance">
                                                <option value="">Select class of insurance </option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-SearchableSelect name="engage_type" id="engage_type" req="required" inputLabel="Nature of engagement">
                                                <option value="">Select engagement type </option>
                                                <option value="1">Direct</option>
                                                <option value="2">Broker</option>
                                            
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv>
                                            <x-SearchableSelect name="lead_handler" id="lead_handler" req="required" inputLabel="CR Lead">
                                                <option value="">Select lead</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{$user->username}}">{{ strtoupper($user->firstname) }}  {{ strtoupper($user->lastname) }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>                                         
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv>
                                            <x-SearchableSelect name="bd_lead" id="bd_lead" req="required" inputLabel="BD Lead">
                                                <option value="">Select lead</option>
                                                    @foreach ($bd_users as $user)
                                                        <option value="{{$user->username}}">{{ strtoupper($user->firstname) }}  {{ strtoupper($user->lastname) }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>                                         
                                        </x-OnboardingInputDiv>
                                    </div>
                                    
                                    <div class="row my-md-3">
                                        <B class="primary-color">Contact Details</B>
                                        <div class="m-0"><hr></div>
                                        <x-OnboardingInputDiv >
                                            <x-SearchableSelect name="contact_salutation" id="contact_salutation" req="" inputLabel="Contact Salutation">
                                                <option value="">Select salutation</option>
                                                    @foreach ($salutations as $salutation)
                                                        <option value="{{$salutation -> name}}">{{ $salutation -> name }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-Input name="contact_name" id="contact_name"  placeholder="Enter name"  inputLabel="Contact Fullname" req="required"/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-EmailInput id="email" name="email" req="required" 
                                                    inputLabel="Email Address"  
                                                    placeholder="Enter email"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="countryDiv">
                                            <x-SearchableSelect name="country_code" id="country" req="required" inputLabel="Country">
                                                <option value="">Select country code</option>
                                                @foreach($countries as $country)
                                                    <option @if($country->iso=='KE') selected @endif value="{{$country->id}}">{{$country->name}}  +{{$country->country_code}}</option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-Input name="town" id="town"  placeholder="Enter town/city"  
                                                    inputLabel="Town/City" 
                                                    req="required"/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-Input name="postal_address" id="postal_address"  placeholder="Enter postal address"  
                                                    inputLabel="Postal Address" 
                                                    req=""/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-Input name="postal_code" id="postal_code"  placeholder="Enter Postal Code"  
                                                    inputLabel="Postal Code" 
                                                    req=""/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-NumberInput id="telephone" name="telephone" req="required" 
                                                    inputLabel="Telephone"  
                                                    class="telephone" 
                                                    placeholder="Enter telephone number"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv>
                                            <x-NumberInput id="phone_1" name="phone_1" req="required"
                                                    inputLabel="Primary Phone" 
                                                    class="phone"
                                                    placeholder="Enter phone number"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv>
                                            <x-Input id="address_3" name="address_3" req="required"
                                                    inputLabel="Physical Address" 
                                                    placeholder="Enter physical address"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-Input name="contact_position" id="contact_position"  placeholder="Enter position"  
                                                    inputLabel="Contact Position" 
                                                    req="required"/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-SearchableSelect name="alternative_salutation" id="alternative_salutation" req="" inputLabel="Alternative Contact Salutation">
                                                <option value="">Select salutation</option>
                                                    @foreach ($salutations as $salutation)
                                                        <option value="{{$salutation -> name}}">{{ $salutation -> name }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-Input name="alternative_contact_name" id="alternative_contact_name"  placeholder="Enter name"  inputLabel="Alternative Contact Fullname" req=""/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-EmailInput id="alternative_email" name="alternative_email" req="" 
                                                    inputLabel="Alternative Contact Email"  
                                                    placeholder="Enter email"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-NumberInput id="alternative_phone_number" name="alternative_phone_number" req="" 
                                                    inputLabel="Alternative Phone No."  
                                                    class="phone" 
                                                    placeholder="Enter phone number"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-Input name="alternative_contact_position" id="alternative_contact_position"  placeholder="Enter position"  
                                                    inputLabel="Alternative Contact Position" 
                                                    req=""/>           
                                        </x-OnboardingInputDiv>

                                    </div>
                                    
                                    <div class="row my-md-3">
                                        <B class="primary-color">Quotation Details</B>
                                        <div class="m-0"><hr></div>

                                        <x-OnboardingInputDiv >
                                            <x-SearchableSelect name="quote_currency" id="quote_currency" req="required" inputLabel="Currency">
                                                <option value="">Select Currency</option>
                                                @foreach ($currencies as $currency)
                                                    @if($currency->base_currency == "Y")
                                                    <option value="{{ $currency->currency }}" shortcode="{{$currency->short_description }}" selected>
                                                        {{ $currency->description }}</option> 
                                                    @else
                                                    <option value="{{ $currency->currency }}" shortcode="{{$currency->short_description }}">
                                                        {{ $currency->description }}</option> 
                                                    @endif
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        
                                        <x-OnboardingInputDiv >
                                            <x-Input name="final_premium" id="final_premium"  placeholder="Enter final premium"  inputLabel="Final Premium" req="required"/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-Input id="final_commission" name="final_commission" req="required" 
                                                    inputLabel="Final Commission"  
                                                    placeholder="Enter final commission"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv >
                                            <x-textArea style="height: 1.2em;" name="remarks" id="remarks"  placeholder="Enter remarks" rows="1"  inputLabel="Remarks" req="required"/>   
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-DateInput id="incept_date" name="incept_date" req="required" inputLabel="Inception Date" value="{{ old('incept_date') }}" placeholder="Date of Inception" />
                                        </x-OnboardingInputDiv> 

                                        <x-OnboardingInputDiv>
                                            <x-Input id="agent_name" name="agent_name" req="required" inputLabel="Agent Name" value="{{ old('agent_name') }}" placeholder="Agent Name" />
                                        </x-OnboardingInputDiv> 

                                        <x-OnboardingInputDiv>
                                            <x-NumberInput id="ag_comm_rate" name="ag_comm_rate" req="required" inputLabel="Agent Rate(%)" value="{{ old('ag_comm_rate') }}" placeholder="Agent Rate" />
                                        </x-OnboardingInputDiv> 

                                    </div>

                                    <div class="row my-md-3">
                                        <B class="primary-color">KYC Details</B>
                                        <div class="m-0"><hr></div>


                                        <x-OnboardingInputDiv>
                                            <x-Input id="pin_no" name="pin_no" req="required" inputLabel="Pin/Tin Number" value="{{ old('pin_no') }}" placeholder="Enter pin/tin number"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="idNoDiv">
                                            <label for="id_number"><span id="id_num_type"></span><font style="color:red;">*</font></label>
                                            <input type="text" name="identity_no" class="form-control" id="identity_no" placeholder="Enter identity number" required>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="incorporation_div">
                                            <x-Input id="incorporation_cert" name="incorporation_cert" req="" inputLabel="Cetificate Of Incorporation Number" value="{{ old('incorporation_cert') }}" placeholder="Enter cert. number" />
                                        </x-OnboardingInputDiv> 

                                        <x-OnboardingInputDiv id="dob_div">
                                            <x-DateInput id="dob" name="dob" req="required" inputLabel="Date of Registration" value="{{ old('dob') }}" placeholder="Date of Birth" />
                                        </x-OnboardingInputDiv> 
                                        
                                        <x-OnboardingInputDiv id="">
                                            <x-Input id="cr12" name="cr12" req="" inputLabel="CR12" value="{{ old('cr12') }}" placeholder="Enter CR12" />
                                        </x-OnboardingInputDiv> 
                                    </div>
                                    
                                    <div class="row my-md-3">
                                        <B class="primary-color">Document Attachments</B>
                                        <div class="m-0"><hr></div>

                                        

                                    </div>
                                </div>
                            </div>
                            <hr>
                            <!-- <button class="btn btn-outline-success next action-button" name="next">Next</button> -->
                            <div class="text-right">
                                <button type="submit" id="submit" class="btn btn-success text-white">
                                    <span class="fa fa-save "></span> Submit Details
                                </button>
                            </div>

                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="v_docs" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="min-width:70%">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #cfd7e0 ">
                <h5 class="modal-title" id="adminClaimModalLabel">Document</span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="Xcontainer">
                <div class="modal-body">
                    <div class="embed-responsive embed-responsive-16by9">
                    <object id="doc_view" data="base64"  height="900" width="100%" style="border: solid 1px #DDD;"></object>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section("page_scripts")
<script>
    $(document).ready(function() {

        

        $('body').on('change', '.document_file', function(){
            
            let file = $(this)[0].files[0];
            let id = $(this).attr('id')
            let id_length = id.length
            let rowID = id.slice(13, id_length)
            

            let formData = new FormData();
            formData.append('doc', file);

            $.ajax({
                type: "POST",
                data: formData,
                url: "{{ route('doc_preview')}}",
                contentType: false,
                processData: false,
                success:function(resp){
                    $("#preview"+rowID).attr('data-file', resp);

                }
            })
        })

        $('body').on('click', '.preview', function(e){
            $('object').attr('data','');
            var doc = $(this).attr('data-file');

            $('#doc_view').html('<iframe src="' + doc + '" width="100%" height="900"></iframe>');

            $('#v_docs').modal('show');

           

        });


        $('#incorporation_div').hide();

        // enable disabled corporate fields starts here
        $("select#client_type").change(function() {
            let ctype = $("#client_type").val();
            if (ctype === "I") {
                $('#dob_div label').text('Date of Birth')
                $('#corporate').hide();
                $('#occupationDiv').show();
                $('#salutation_code').show();
                $('#full_name_div').show();
                $('#customer_id').show();
                $("#id_type").prop('disabled', false);
                $("#pin_no").prop('disabled', false);
                $("#gender_code").prop('disabled', false);
                $('#occupation_code').prop('disabled', false);
                $('#dobDiv').show();
                $('#genderDiv').show();
                $('#idTypeDiv').show();
                $('#idNoDiv').show();
                $('#incorporation_div').hide();
                
            } else {
                $('#dob_div label').text('Date of Registration')
                $('#corporate').show();
                $('#salutation_code').hide();
                $('#full_name_div').hide();
                $('#dobDiv').hide();
                $('#genderDiv').hide();
                $('#idTypeDiv').hide();
                $('#idNoDiv').hide();
                $('#occupationDiv').show();
                $('#incorporation_div').show();
            }
        });

        //set identity number label on load
        $('#id_num_type').text('National ID/Passport No.');

        //set identity number label on change of type
        $("select#id_type").change(function() {
            let iDType = $("#id_type").val();

            if (iDType === 'N') {
                $('#id_num_type').text('National ID');
            }

            else if (iDType === 'M') {
                $('#id_num_type').text('Millitary ID');
            }

            else if (iDType === 'P') {
                $('#id_num_type').text('Passport Number');
            }

            else if (iDType === 'F') {
                $('#id_num_type').text('Foreigners ID');
            }
            else{
                $('#id_num_type').text('National ID');
            }

            $(this).trigger("chosen:updated");
        });
        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
        var current = 1;
        var steps = $("fieldset").length;
  

        $("#submit").click(function(e) {
            e.preventDefault()
            $(this).attr('disabled', true)
            var form = $("#msform");

            form.validate({
                errorElement: 'span',
                errorClass: 'text-danger fst-italic small',
                highlight: function(element, errorClass) {
                },
                unhighlight: function(element, errorClass) {
                },
                rules: {
                    first_name: {
                        minlength: 6,
                    }
                }
            });

            if (form.valid() === true){
                
                let myform = document.getElementById("msform");
                let formData = new FormData(myform);

                $.ajax({
                    type: 'post',
                    data: formData,
                    url: "{{ route('client.onboard') }}",
                    processData: false,
                    contentType: false,
                    success:function(res){
                        if (res.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                text: 'Client successfully Onboarded'
                            })
                            
                            setTimeout(function(){
                            // location.reload();
                            window.location.href =  `agent/view/${res.global_id}/N`;
                        }, 2000);
                        }else{
                            $('#submit').attr('disabled', false)
                            Swal.fire({
                                icon: 'error',
                                text: res.message
                            });
                        }
                    }
                });

            }else{
                $(this).attr('disabled', false)

            }
        })

        $('#division').on('change', function(){
            let division = parseInt($('#division option:selected').val())


            $('.division_doc').each(function() {
                let id = $(this).attr('id');

                if (id) {
                    let id_length = id.length;
                    let rowID = id.slice(3, id_length);
                    console.log(rowID, division);
                    
                    if (rowID == division) {
                        $('#doc'+rowID).removeClass('d-none')
                    } else {
                        $('#doc'+rowID).addClass('d-none')
                    }
                }
            });

            
            $('#doc'+division).show()
                $.ajax({
                    type: "GET",
                    data: {'division': division},
                    url: "{{ route('get_division_classes')}}",
                    success:function(resp){
                        console.log(resp.classes);
                        if (resp.status == 1) {
                            $('#insurance_class').empty()
                            $('#insurance_class').append($("<option />").val('').text('Select class'));
                            $.each(resp.classes, function() {
                                $('#insurance_class').append($("<option />").val(this.id).text(this.class_name));
                            });
                        }
                    }
                })

            
        })



    });
</script>
@endsection
