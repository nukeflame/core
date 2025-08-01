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

    #heading {
        text-transform: uppercase;
        color: #673AB7;
        font-weight: normal
    }

    #msform {
        text-align: center;
        position: relative;
        margin-top: 20px
    }

    #msform fieldset {
        background: white;
        border: 0 none;
        border-radius: 0.5rem;
        /* box-sizing: border-box; */
        width: 100%;
        margin: 0;
        padding-bottom: 20px;
        position: relative
    }

    .form-card {
        text-align: left
    }

    #msform fieldset:not(:first-of-type) {
        display: none
    }


    #msform input:focus,
    #msform textarea:focus {
        -moz-box-shadow: none !important;
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
        border: 1px solid #673AB7;
        outline-width: 0
    }

    #msform .action-button {
        float: right
    }

    #msform .action-button-previous {
        float: left
    }

    .card {
        z-index: 0;
        border: none;
        position: relative
    }

    .fs-title {
        font-size: 25px;
        color: gray;
        margin-bottom: 15px;
        font-weight: normal;
        text-align: left
    }

    .purple-text {
        color: #fe6b4d;
        font-weight: normal
    }

    .steps {
        font-size: 13px;
        color: gray;
        margin-bottom: 5px;
        font-weight: bold;
        text-align: right
    }

    .fieldlabels {
        color: gray;
        text-align: left
    }

    #progressbar {
        margin-bottom: 10px;
        overflow: hidden;
        color: lightgrey
    }

    #progressbar .active {
        color: #000000;
    }

    #progressbar li {
        list-style-type: none;
        font-size: 13px;
        width: 40%;
        float: left;
        position: static;
        font-weight: 400
    }

    #progressbar #bio:before {
        font-family: FontAwesome;
        content: "\f2be  "
    }

    #progressbar #bank:before {
        font-family: FontAwesome;
        content: "\f19c "
    }

    #progressbar #contact:before {
        font-family: FontAwesome;
        content: "\f2bb "
    }

    #progressbar #finish:before {
        font-family: FontAwesome;
        content: "\f00c"
    }

    #progressbar li:before {
        width: 50px;
        height: 50px;
        line-height: 45px;
        display: block;
        font-size: 20px;
        color: #ffffff;
        background: lightgray;
        border-radius: 50%;
        margin: 0 auto 10px auto;
        padding: 2px
    }

    #progressbar li:after {
        content: '';
        width: 100%;
        height: 2px;
        background: #b3b300;
        position: absolute;
        left: 0;
        top: 25px;
        z-index: -1
    }

    #progressbar li.active:before,
    #progressbar li.active:after {
        background: #feaa9a;
    }

    .progress {
        height: 20px
    }

    .progress-bar {
        background-color: #673AB7
    }

    .fit-image {
        width: 100%;
        object-fit: cover
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 ">
            <div class="card px-0 pt-4 pb-0 mb-3">
                <h4 class="text-center text-lg-start mb-0 mx-2">Client Onboarding</h4>
                <hr>
                <div class="card-body">
                    <form id="msform">
                        @csrf
                        <input type="hidden" name="agent_onboard_client" value="Y">
                        <!-- progressbar -->
                        <!-- /<ul id="progressbar"> -->
                            <!-- <li class="active" id="bio">Personal details</li> -->
                            <!-- <li id="bank">Bank Details</li> -->
                            <!-- <li id="contact">Contact Details</li> -->
                        </ul> <br>
                         <!-- fieldsets -->
                        <fieldset>
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-7"></div>
                                    <div class="col-5">
                                        <!-- <h4 class="steps">Step 1</h4> -->
                                    </div>
                                </div>
                                <div class="individual">
                                    <div class="row">
                                        <h5>Identity Details</h5>
                                        <hr>

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

                                        <x-OnboardingInputDiv id="lead">
                                            <x-SearchableSelect name="lead" id="lead_select" req="" inputLabel="Select Prospect">
                                                <option value="none" selected>Select Prospect</option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="subtype_div" style="display:none">
                                            <x-SelectInput name="sub_client_type" id="sub_client_type" req="required" inputLabel="Client Sub-type">
                                                <option value="">Select client sub-type</option>
                                                <option value="CT">Citizen</option>
                                                <option value="FR">Foreigner - Resident</option>
                                                <option value="FN">Foreigner - Non-resident</option>
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="division" >
                                            <x-SearchableSelect name="division" id="division" req="required" inputLabel="Division">
                                                <option value="">Select division</option>
                                                    @foreach ($divisions as $division)
                                                        <option value="{{$division -> id}}" data-rate="{{$division->income_rate}}">{{ $division -> name }}</option>
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

                                        <x-OnboardingInputDiv style="display:none" id="corporate">
                                            <x-Input name="corporate_name" placeholder="Enter company name"
                                            id="full_name" value="{{ old('corporate_name') }}" inputLabel="Company Name" req="required"  onkeyup="this.value=this.value.toUpperCase();" />
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-Input id="pin_no" name="pin_no" req="required" inputLabel="Pin/Tin Number" value="{{ old('pin_no') }}" placeholder="Enter pin/tin number"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="salutation_code">
                                            <x-SelectInput name="salutation_code" id="salutation_code" req="" inputLabel="Salutation">
                                                <option value="">Select salutation</option>
                                                    @foreach ($salutations as $salutation)
                                                        <option value="{{$salutation -> name}}">{{ $salutation -> name }}</option>
                                                    @endforeach
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="first_name">
                                            <x-Input id="first_name" name="fname" req="required" inputLabel="First Name" value="{{ old('fname') }}" placeholder="Enter first name" onkeyup='this.value=this.value.toUpperCase();'/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="surname">
                                            <x-Input id="surname" name="surname" req="required" inputLabel="Surname" value="{{ old('surname') }}" placeholder="Enter surname" onkeyup='this.value=this.value.toUpperCase();'/>
                                        </x-OnboardingInputDiv>


                                        <x-OnboardingInputDiv id="other_names">
                                            <x-Input id="other_name" name="other_names" req="" inputLabel="Other Names" value="{{ old('other_names') }}" placeholder="Enter other names" onkeyup='this.value=this.value.toUpperCase();'/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="incorporation_div">
                                            <x-Input id="incorporation_cert" name="incorporation_cert" req="" inputLabel="Cetificate Of Incorporation Number" value="{{ old('incorporation_cert') }}" placeholder="Enter cert. number" />
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

                                        <x-OnboardingInputDiv id="idNoDiv">
                                            <label for="id_number"><span id="id_num_type"></span><font style="color:red;">*</font></label>
                                            <input type="text" name="identity_no" class="form-control" id="identity_no" placeholder="Enter identity number" required>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="genderDiv">
                                            <x-SelectInput name="gender" id="gender_code" class="form-control checkempty" req="required" inputLabel="Gender">
                                                <option value="">Select gender</option>
                                                @foreach ($genders as $gender)
                                                    <option value="{{$gender -> gender_code}}">{{ $gender -> name }}</option>
                                                @endforeach
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="dobDiv">
                                            <x-Input id="date_of_birth" name="date_of_birth" req="required"
                                                    inputLabel="Date Of Birth" value="{{ old('date_of_birth') }}"
                                                    placeholder="Date of birth" type="date"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="occupationDiv">
                                            <x-SearchableSelect name="occupation_code" id="occupation_code" req="required" inputLabel="Industry">
                                                <option value="">Select occupation</option>
                                                @foreach ($occupations as $occupation)
                                                    <option value="{{$occupation->name}}">
                                                        {{ $occupation -> name }}
                                                    </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                    </div>

                                    <div class="row my-md-3">
                                        <h5>Address Details</h5>
                                        <hr>
                                        <x-OnboardingInputDiv id="countryDiv">
                                            <x-SearchableSelect name="country_code" id="country" req="required" inputLabel="Country">
                                                <option value="">Select country code</option>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->id}}">{{$country->name}}  +{{$country->country_code}}</option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>


                                        <x-OnboardingInputDiv>
                                            <x-NumberInput id="phone_1" name="phone_1" req="required"
                                                    inputLabel="Primary Phone" value="{{ old('phone_1') }}"
                                                    placeholder="Enter phone number"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-NumberInput id="phone_2" name="phone_2" req=""
                                                    inputLabel="Secondary Phone" value="{{ old('phone_2') }}"
                                                    placeholder="Enter phone number"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-EmailInput id="email" name="email" req="required"
                                                    inputLabel="Email" value="{{ old('email') }}"
                                                    placeholder="Enter email"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-Input id="address_1" name="address_1" req=""
                                                    inputLabel="Postal Address" value="{{ old('address_1') }}"
                                                    placeholder="P.O BOX/Private Bag"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-NumberInput id="address_2" name="address_2" req=""
                                                    inputLabel="Postal Code" value="{{ old('address_2') }}"
                                                    placeholder="Enter postal code"/>
                                        </x-OnboardingInputDiv>


                                        <x-OnboardingInputDiv>
                                            <x-Input id="address_3" name="address_3" req="required"
                                                    inputLabel="Physical Address" value="{{ old('address_3') }}"
                                                    placeholder="Enter physical address"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-Input id="street" name="street" req=""
                                                    inputLabel="Street" value="{{ old('street') }}"
                                                    placeholder="Enter street"/>
                                        </x-OnboardingInputDiv>



                                    </div>

                                    <div class="row my-md-3">
                                        <h5>Contact Details</h5>
                                        <hr>
                                        <x-OnboardingInputDiv>
                                            <x-Input id="c_name0" name="contact_firstname" req=""
                                                    inputLabel="First Name" value="{{ old('contact_firstname') }}"
                                                    placeholder="Enter first name" onkeyup="this.value=this.value.toUpperCase();"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-Input id="c_surname0" name="contact_surname" req=""
                                                    inputLabel="Surname" value="{{ old('contact_surname') }}"
                                                    placeholder="Enter surname" onkeyup="this.value=this.value.toUpperCase();"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-Input id="c_othername0" name="contact_othername" req=""
                                                    inputLabel="Other Names" value="{{ old('contact_othername') }}"
                                                    placeholder="Enter other names" onkeyup="this.value=this.value.toUpperCase();"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-Input id="position0" name="contact_position" req=""
                                                    inputLabel="Position" value="{{ old('contact_position') }}"
                                                    placeholder="Enter position" onkeyup="this.value=this.value.toUpperCase();"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-Input id="phone_no0" name="contact_phone_no" req=""
                                                    inputLabel="Phone Number" value="{{ old('contact_phone_no') }}"
                                                    placeholder="Enter phone number"/>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv>
                                            <x-Input id="c_email0" name="contact_email" req=""
                                                    inputLabel="Email" value="{{ old('contact_email') }}"
                                                    placeholder="Enter email"/>
                                        </x-OnboardingInputDiv>

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
@endsection
@section("page_scripts")
<script>
    $(document).ready(function() {
        $("select#client_type").change(function() {
            let ctype = $("#client_type").val();

            $.ajax({
            type: 'GET',
            url: "{{ route('get_leads') }}",
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {

                    let leadSelect = document.getElementById('lead_select');
                    // Clear existing options
                    leadSelect.innerHTML = '<option value="">Select Prospect</option>';

                    let leads = response.data.filter((lead) => lead.client_type == ctype );

                    console.log(leads)

                    // Add new options based on the fetched leads
                    leads.forEach(function(lead) {
                        let option = document.createElement('option');
                        option.value = lead.prospect_id; // Assuming the lead has an ID property
                        option.textContent = lead.full_name; // Replace with the appropriate property for the lead name
                        leadSelect.appendChild(option);
                    });
                } else {
                    console.log('Failed to fetch leads');
                }
            },
            error: function(xhr, textStatus, error) {
                console.log('An error occurred while fetching leads');
            }
        })
    });

    $("#region").on('change', function(){
        var region = $('#region').val();

        var decodedRegion = decodeURIComponent(region);

        $.ajax({
            url:"{{route('fetch_districts_ar')}}",
            data:{'region':decodedRegion},
            type:"get",
            success:function(districts){
                //alert('success');
                $('#district').empty();
                $('#district').append($('<option>').text('Select District').attr('value', ''));
                $.each(districts, function(i, value){
                    $('#district').append($('<option>').text(value.DISTRICT_NAME).attr('value', value.DISTRICT_NAME));
                });
                $('.section').trigger("chosen:updated");
            },
            error:function(resp){
                //alert('error');
                console.error;
            }
        });
    });

        $('.bank_code').on('change', function(){
            let code = $(this).val()
            var id = $(this).attr('id')
            var id_length = id.length
            var rowID = id.slice(10, id_length)

            $.ajax({
                type: "GET",
                data: {'bank': code},
                url: "{{ route('get_bank_branches')}}",
                success:function(resp){

                    if (resp.status == 1) {
                        $("#bank_branch_"+rowID).empty()
                        $("#bank_branch_"+rowID).append($("<option />").val('').text('Select Bank Branch'));
                        $.each(resp.branches, function() {
                            $("#bank_branch_"+rowID).append($("<option />").val(this.branch_code).text(this.name));
                        });
                    }
                }
            })

        });

       $("select#lead_select").change(function() {
            var selectedLeadId = this.value;
            var url = "{{ route('getLeadDetails', ':leadId') }}";
            url = url.replace(':leadId', selectedLeadId);

            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function(response) {

                    if (response.status === 'success' && response.data) {
                        var firstNameInput = $('[name="fname"]');
                        var secondNameInput = $('[name="other_names"]');
                        var emailInput = $('[name="email"]');
                        var phoneInput = $('[name="phone_1"]');
                        var telInput = $('[name="phone_2"]');
                        var companyInput = $('[name="corporate_name"]');

                        firstNameInput.val(response.data.first_name);
                        secondNameInput.val(response.data.second_name);
                        emailInput.val(response.data.email);
                        phoneInput.val(response.data.phone_1);
                        telInput.val(response.data.telephone);
                        companyInput.val(response.data.full_name);
                        console.log(response.data.occupation_code, "giurhghrgiuhu");

                        $('#salutation_code').val(response.data.salutation);
                        $('#pin_no').val(response.data.pin_no);
                        $('#incorporation_cert').val(response.data.incorporation_cert);
                        $('#country').val(response.data.country).trigger('change');
                        $('#division').val(response.data.division).trigger('change');
                        $('#insurance_class').val(response.data.class_of_insurance).trigger('change');
                        $('#engage_type').val(response.data.nature_of_engagement).trigger('change');
                        $('#email').val(response.data.email);
                        $('#phone_1').val(response.data.phone);
                        $('#occupation_code').val(response.data.occupation_code).trigger('change');

                        // ('.select2').select2()
                    } else {
                        console.log('Failed to fetch lead details');
                    }
                },
                error: function(xhr, textStatus, error) {
                    console.log('An error occurred while fetching lead details');
                }
            });
       });

        $('#incorporation_div').hide();

        // enable disabled corporate fields starts here
        $("select#client_type").change(function() {
            let ctype = $("#client_type").val();

            if (ctype === "I") {

                $('#corporate').hide();
                $('#occupationDiv').show();
                $('#salutation_code').show();
                $('#first_name').show();
                $('#surname').show();
                $('#other_names').show();
                $('#customer_id').show();
                $("#id_type").prop('disabled', false);
                $("#pin_no").prop('disabled', false);
                $("#gender_code").prop('disabled', false);
                $('#occupation_code').prop('disabled', false);
                $('#subtype_div').show();
                $('#dobDiv').show();
                $('#genderDiv').show();
                $('#idTypeDiv').show();
                $('#idNoDiv').show();
                $('#incorporation_div').hide();

            } else {
                $('#corporate').show();
                $('#salutation_code').hide();
                $('#first_name').hide();
                $('#surname').hide();
                $('#other_names').hide();
                $('#subtype_div').hide();
                $('#dobDiv').hide();
                $('#genderDiv').hide();
                $('#idTypeDiv').hide();
                $('#idNoDiv').hide();
                $('#occupationDiv').show();
                $('#incorporation_div').show();
            }
        });

        //set identity number label on load
        $('#id_num_type').text('National ID');

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

        $(".next").click(function(e) {
            e.preventDefault()
            var form = $("#msform");
            form.validate({
                    errorElement: 'span',
                    errorClass: 'text-danger fst-italic',
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

            // end validation
            if (form.valid() === true){
                current_fs = $(this).parent();
                next_fs = $(this).parent().next();

                //Add Class Active
                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

                //show the next fieldset
                next_fs.show();

            }


            //hide the current fieldset with style
            current_fs.animate({
                opacity: 0
            }, {
                step: function(now) {
                    // for making fielset appear animation
                    opacity = 1 - now;


                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    next_fs.css({
                        'opacity': opacity
                    });
                },
                duration: 500
            });
        });

        $(".previous").click(function(e) {
            e.preventDefault();

            current_fs = $(this).parent();
            previous_fs = $(this).parent().prev();

            //Remove class active
            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

            //show the previous fieldset
            previous_fs.show();

            //hide the current fieldset with style
            current_fs.animate({
                opacity: 0
            }, {
                step: function(now) {
                    // for making fielset appear animation
                    opacity = 1 - now;

                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    previous_fs.css({
                        'opacity': opacity
                    });
                },
                duration: 500
            });
        });

        $("#submit").click(function(e) {
            e.preventDefault()
            $(this).attr('disabled', true)
            console.log('ian');
            let myform = document.getElementById("msform");
            let formData = new FormData(myform);

            $.ajax({
                type: 'post',
                data: formData,
                url: "{{ route('clients.save') }}",
                processData: false,
                contentType: false,
                success:function(res){
                    if (res.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: `Client Number: ${res.client_number}` ,
                            text: 'Client successfully created'
                        })
                        setTimeout(function(){
                            // location.reload();
                            window.location.href =  `agent/view/${res.global_id}`;
                        }, 2000);
                    }else if(res.status == 201){
                        Swal.fire({
                            icon: 'success',
                            text: 'Client successfully created'
                        })
                        setTimeout(function(){
                            // location.reload();
                            window.location.href =  `agent/view/${res.global_id}`;
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
        })

        //banks
        $(function() {
            var counter=0;
            $('#addbank').click(function() {

                if (counter > 0) {
                    var account_name = $('#account_name' + counter).val()
                    var account_no = $('#account_no' + counter).val()
                } else if (counter == 0) {
                    var account_name = $('#account_name0').val()
                    var account_no = $('#account_no0').val()
                }
                if (account_name == '' || account_no == '') {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Please fill all details'
                    });
                } else {
                    counter =  + 1;
                    $('#bank_details').append(
                        `<div class="row">
                            <x-OnboardingInputDiv>
                                <x-SelectInput name="bank_code[]" id="bank_type${counter}" req="required" inputLabel="Bank Name">
                                    @foreach ($banks as $bank)
                                        <option value="{{$bank -> bank_code }}">
                                            {{ $bank -> name }}
                                        </option>
                                    @endforeach
                                </x-SelectInput>
                            </x-OnboardingInputDiv>

                            <x-OnboardingInputDiv>
                                <x-SelectInput name="branch[]" id="branch${counter}" req="required" inputLabel="Bank Branch">
                                    @foreach ($branches as $branch)
                                    <option value="{{$branch -> branch_code}}">
                                        {{ $branch -> name }}
                                    </option>
                                    @endforeach
                                </x-SelectInput>
                            </x-OnboardingInputDiv>

                            <x-OnboardingInputDiv>
                                <div class="row">
                                    <div class="col-10">
                                    <x-Input id="account_name${counter}" name="account_name[]" req="required"
                                        inputLabel="Account Name" value="{{ old('account_name') }}"
                                        placeholder="Enter account name"
                                        oninput='this.value=this.value.toUpperCase();'/>
                                    </div>
                                    <div class="col-2">
                                        <label>Default</label>
                                        <input name="default_bank[]" type="radio" value="1">
                                    </div>
                                </div>
                            </x-OnboardingInputDiv>

                            <x-OnboardingInputDiv>
                                <label for="account_no">Account Number<font style="color:red;">*</font></label>
                                <div class="input-group">
                                    <input type="text" name="account_no[]" id="account_no${counter}" class="form-control checkempty" />
                                    <button class="btn btn-danger remove_bank" type="button">Remove </button>
                                </div>
                            </x-OnboardingInputDiv>
                        </div>`
                    );
                }


                $('input[type=radio]').change(function(){
                    $('input[type=radio]:checked').not(this).prop('checked',false)
                })
            });

            $('#bank_details').delegate('.remove_bank', 'click', function() {
                $(this).parent().parent().parent().remove();
            });

        });
        $('#division').on('change', function(){
            // $('#premium').trigger('change')
            let division = parseInt($('#division option:selected').val())

                $.ajax({
                    type: "GET",
                    data: {'division': division},
                    url: "{{ route('get_division_classes')}}",
                    success:function(resp){

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
