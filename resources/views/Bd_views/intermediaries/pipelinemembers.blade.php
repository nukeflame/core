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
        color: #673AB7;
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
        color: blue
    }

    #progressbar li {
        list-style-type: none;
        font-size: 13px;
        width: 25%;
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
        background: #5e72e4;
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

    .primary-color{
        color: #E1251B;
    }
</style>
@php
    if(!is_null($client)) {
        $pros_email= $client->email;
        $pros_phone= $client->phone_1;
        $pros_address= $client->address_3;
    }else{
        $pros_email= '';
        $pros_phone= '';
        $pros_address= '';

    }
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 ">
            <div class="card px-0 pt-4 pb-0 mb-3">
                <h4 class="text-center text-lg-start mb-0 mx-2">Add pipeline prospect</h4>
                <hr>
                <div class="card-body">
                    <form id="leads_form">
                        @csrf
                        <input type="hidden" name="agent_onboard_client" value="Y">
                        <fieldset>
                            <div class="form-card">
                                <div class="individual">
                                    <B class="primary-color">Insurance Details</B>
                                    <hr>
                                    <div class="row mb-4">
                                        <input type="hidden" name="pipeline_id" value="{{ $pip_id }}">
                                        @if(is_null($prospect))
                                            <input type="hidden" name="opportunity_created" value="N">  
                                            <x-OnboardingInputDiv id="opportunity_div" class="col-md-4">
                                                <x-SearchableSelect name="opportunity" id="opportunity" req="required" inputLabel="Prospect">
                                                    <option value="">Select prospect</option>
                                                        @foreach ($opps as $opp)
                                                            <option value="{{$opp -> code}}">{{ $opp -> full_name }}</option>
                                                        @endforeach
                                                </x-SearchableSelect>  
                                            </x-OnboardingInputDiv>
                                        @else
                                            <input type="hidden" name="opportunity_created" value="Y">  
                                            <input type="hidden" name="opportunity" value="{{ $prospect->code }}">      
                                        @endif
                                        
                                        <x-OnboardingInputDiv id="division" class="col-md-4">
                                            <x-SearchableSelect name="division" id="division" req="required" inputLabel="Division">
                                                <option value="">Select division</option>
                                                    @foreach ($divisions as $division)
                                                        <option value="{{$division -> id}}" data-rate="{{$division->income_rate}}">{{ $division -> name }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-SearchableSelect name="insurance_class" id="insurance_class" req="required" inputLabel="Class of Insurance">
                                                <option value="">Select class of insurance </option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-SearchableSelect name="currency" id="currency" req="required" inputLabel="Currency">
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
                                        <x-OnboardingInputDiv id="premium_div" class="col-md-4">
                                            <x-NumberInput name="premium" id="premium"  inputLabel="Premium" req="required" />
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="income_div" class="col-md-4">
                                            <x-NumberInput name="income" id="income"  inputLabel="Tentative Income" req="required" />
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-Input name="contact_name" id="contact_name"  placeholder="Enter name"  inputLabel="Contact Name" req="required"/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-EmailInput id="email" name="email" req="required" 
                                                    inputLabel="Email" value="{{ $pros_email }}" 
                                                    placeholder="Enter email"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-NumberInput id="phone_number" name="phone_number" req="required" 
                                                    inputLabel="Primary Phone" value="{{ $pros_phone }}" 
                                                    placeholder="Enter phone number"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-Input name="physical_address" id="physical_address"  placeholder="Enter address"  
                                                    inputLabel="Physical Address" 
                                                    value="{{ $pros_address }}"
                                                    req="required"/>           
                                        </x-OnboardingInputDiv>
                                    </div>
                                    <B class="primary-color">Engagement Details</B>
                                    <hr>
                                    <div class="row">
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-SearchableSelect name="engage_type" id="engage_type" req="required" inputLabel="Nature of engagement">
                                                <option value="">Select engagement type </option>
                                                    @foreach ($engage_types as $type)
                                                        <option value="{{$type->id}}">{{ $type->name }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="date_effective_div" class="col-md-4">
                                            <x-DateInput name="effective_date" id="effective_date"  placeholder="Enter cover start date"  inputLabel="Cover Start Date" req="required"/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="date_closing_div" class="col-md-4">
                                            <x-DateInput name="closing_date" id="closing_date"  placeholder="Enter bid closing date"  inputLabel="Bid Closing Date" req="required"/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="lead_owner" class="col-md-4">
                                            <x-SearchableSelect name="lead_owner" id="lead_owner" req="required" inputLabel="Prospect Lead">
                                                <option value="">Select prospect lead</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{$user->username}}">{{ strtoupper($user->firstname) }}  {{ strtoupper($user->lastname) }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>                                         
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="lead_source" class="col-md-4">
                                            <x-SearchableSelect name="lead_source" id="lead_source" req="required" inputLabel="Prospect Source">
                                                <option value="">Select prospect source</option>
                                                    @foreach ($leadsources as $source)
                                                        <option value="{{$source->id}}">{{ $source->name }}</option>
                                                    @endforeach
                                            </x-SearchableSelect>                                         
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-Input name="source_desc" id="source_desc"  placeholder="Enter source details"  inputLabel="Source Details" req=""/>           
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="rating" class="col-md-4">
                                            <x-SelectInput name="rating" id="rating" req="" inputLabel="Rating">
                                                <option value="">Select rating</option>
                                                <option value="High">High</option>
                                                <option value="Middle">Middle</option>
                                                <option value="Low">Low</option>
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div style="float:right">
                                <button type="button" name="previous" class="btn btn-outline-secondary" id="cancel">Cancel</button>
                                <button type="submit" id="submit" class="btn btn-success"><span class="fa fa-save"></span> Submit</button>
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
     $("select#client_type").change(function() {
            let ctype = $("#client_type").val();

            var selectedValue = this.value;

            // Get the full_name input field and its parent div
            var fullNameInput = document.getElementById('full_name_input');
            var first_name = document.getElementById('first_name');
            var second_name = document.getElementById('second_name');
            var fullNameDiv = document.getElementById('full_name_div');
            if (ctype === "C") {
                $('#salutation').hide();
                $('#first_name').hide();
                $('#second_name').hide();
                $('#full_name_div').show();
                first_name.setAttribute('req','')
                second_name.setAttribute('req','')

                fullNameInput.setAttribute('req', 'required');

            } else {
                $('#salutation').show();
                $('#first_name').show();
                $('#second_name').show();
                $('#full_name_div').hide();
                first_name.setAttribute('req','required')
                second_name.setAttribute('req','required')
                fullNameInput.setAttribute('req', '');
            }
        });

        $("#submit").click(function(e) {
            e.preventDefault()
           
            let myform = document.getElementById("leads_form");
            let formData = new FormData(myform);
            var form = $("#leads_form");
            form.validate({
                    errorElement: 'span',
                    errorClass: 'text-danger fst-italic',
                    highlight: function(element, errorClass) {
                    },
                    unhighlight: function(element, errorClass) {
                    }
                });

            if (form.valid() === true){
                $(this).attr('disabled', true).text('Saving...')
                $.ajax({
                    type: 'post',
                    data: formData,
                    url: "{{ route('pipeline.create.opportunity') }}",
                    processData: false,
                    contentType: false,
                    success:function(res){
                        if (res.status == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: '' ,
                                text: 'Prospect successfully added to pipeline'
                            })
                            setTimeout(function(){
                                // location.reload();
                                window.location.href =  `/brokerage/pipelines`;
                            }, 2000); 

                            $('#submit').attr('disabled', false).text('Submit')
                        }else{
                            $('#submit').attr('disabled', false).text('Submit')
                            displayValidationErrors(res.errors); // Call the function to display validation errors

                        }
                    },
                    error: function(xhr, textStatus, error) {
                        // Handle any other error that may occur during form submission
                        $('#submit').attr('disabled', false).text('Submit')
                        Swal.fire({
                            icon: 'error',
                            text: 'An error occurred. Please try again later.'
                        });
                    }
                });
            }
        })
        
    function displayValidationErrors(errors) {
        
        // Clear any existing error messages
        $('.error-message').remove();

        // Loop through the validation errors and display them next to the corresponding input fields
        $.each(errors, function(field, messages) {
            var inputField = $(`[name="${field}"]`);
            var errorMessage = '<div class="error-message">' + messages.join('<br>') + '</div>';
            inputField.after(errorMessage);
        });
    }

    $('#cancel').click(function(e){
        window.location.href =  `/brokerage/pipelines`;

    })


    $('#premium').on('change', function(e){
        let premium = $(this).val()

        let division = $('#division option:selected').val()

        if (division != '') {
            let income_rate = $('#division option:selected').attr('data-rate')

            let income = (income_rate*premium)/100;

            $('#income').val(income)
            $('#income').attr('readonly', true)

        }

    })

    $('#division').on('change', function(){
        $('#premium').trigger('change')
        let division = parseInt($('#division option:selected').val())

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


</script>
@endsection