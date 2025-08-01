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
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 ">
            <div class="card px-0 pt-4 pb-0 mb-3">
                <h4 class="text-center text-lg-start mb-0 mx-2">Leads Onboarding</h4>
                <hr>
                <div class="card-body">
                    <form id="leads_form" action="{{ route('edit_lead', ['lead' => $lead->code]) }}">
                        @csrf
                        <fieldset>
                            <div class="form-card">
                                <div class="individual">
                                    <div class="row">
                                        <x-OnboardingInputDiv id="first_name" class="col-md-4">
                                            <x-Input id="first_name" name="first_name" req="required" inputLabel="First Name" value="{{ $lead->first_name}}" placeholder="Enter first name" onkeyup='this.value=this.value.toUpperCase();'/>
                                                @if($errors->has('first_name'))
                                                    <div class="alert alert-danger">{{ $errors->first('first_name') }}</div>
                                                @endif
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="second_name" class="col-md-4">
                                            <x-Input id="surname" name="second_name" req="required" inputLabel="Last Name" value="{{  $lead->second_name}}" placeholder="Enter surname" onkeyup='this.value=this.value.toUpperCase();'/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-EmailInput id="email" name="email" req="required" 
                                                    inputLabel="Email" value="{{  $lead->email }}" 
                                                    placeholder="Enter email"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv class="col-md-4">
                                            <x-NumberInput id="phone_number" name="phone_number" req="required" 
                                                    inputLabel="Primary Phone" value="{{ $lead->phone_number }}" 
                                                    placeholder="Enter phone number"/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="salutation_code" class="col-md-4">
                                            <x-SelectInput name="salutation" id="salutation" req="required" inputLabel="Salutation">
                                                <option value="">Select salutation</option>
                                                    @foreach ($salutations as $salutation)
                                                        <option value="{{$salutation -> name}}" @if ($lead->salutation == $salutation->name) selected @endif>{{ $salutation -> name }}</option>
                                                    @endforeach
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="industry" class="col-md-4">
                                            <x-Input id="industry" name="industry" req="required" inputLabel="Industry" value="{{  $lead->industry}}" placeholder="Enter industry" onkeyup='this.value=this.value.toUpperCase();'/>

                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="status" class="col-md-4">
                                            <x-SelectInput name="status" id="status" req="required" inputLabel="Status">
                                                <option value="">Select status</option>
                                                    @foreach ($statuses as $status)
                                                        <option value="{{$status -> status_name}}"  @if ($lead->status == $status->status_name) selected @endif>{{ $status -> status_name }}</option>
                                                    @endforeach
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="source" class="col-md-4">
                                            <x-Input id="source" name="source" req="required" inputLabel="Source" value="{{ $lead->source }}" placeholder="Enter surname" onkeyup='this.value=this.value.toUpperCase();'/>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="rating" class="col-md-4">
                                            <x-SelectInput name="rating" id="rating" req="required" inputLabel="Rating">
                                                <option value="">Select rating</option>
                                                <option value="1" @if ($lead->rating == 1) selected @endif>High</option>
                                                <option value="2" @if ($lead->rating == 2) selected @endif>Middle</option>
                                                <option value="3" @if ($lead->rating == 3) selected @endif>Low</option>
                                                    {{-- @foreach ($statuses as $status)
                                                        <option value="{{$status -> status_code}}">{{ $status -> status_description }}</option>
                                                    @endforeach --}}
                                            </x-SelectInput>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv id="lead_owner" class="col-md-4">
                                            <x-Input id="lead_owner" name="lead_owner" req="required" inputLabel="Lead Owner" value="{{ $lead->lead_owner }}" placeholder="Enter surname" onkeyup='this.value=this.value.toUpperCase();'/>
                                        </x-OnboardingInputDiv>
                                    </div>
                                </div>
                            </div>
                            <div style="float:right">
                                <button name="previous" type="button" class="btn btn-outline-secondary" id="cancel">Cancel</button>
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
@push("script")
<script>
    $("#submit").click(function(e) {
            e.preventDefault()
            $(this).attr('disabled', true);
            let myform = document.getElementById("leads_form");
            let formData = new FormData(myform);
            var url = $(myform).attr('action');

            $.ajax({
                type: 'post',
                data: formData,
                url:url,
                processData: false,
                contentType: false,
                success:function(res){
                    if (res.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: '' ,
                            text: 'Lead successfully updated'
                        })
                        setTimeout(function(){
                            window.location.href =  `/intermediary/leads_listing`;
                        }, 2000);
                    }
                },
                error:function(error){
                    $('#submit').attr('disabled', false)
                    if (error.responseJSON.validation_errors) {
                        var validationErrors = error.responseJSON.validation_errors;
                        console.log(validationErrors);

                        // Handle validation errors
                        for (var field in validationErrors) {
                            var $input = $('[name="' + field + '"]');
                            $input.after('<span class="text-danger">' + validationErrors[field].join('<br>') + '</span>');
                        }
                        
                    } else {
                        $('#submit').attr('disabled', false)
                            Swal.fire({
                                icon: 'error',
                                text: res.message
                        });
                    }
                }
            });
        })

    $('#cancel').click(function(e){
        window.location.href =  `/intermediary/leads_listing`;

    })

</script>
@endpush