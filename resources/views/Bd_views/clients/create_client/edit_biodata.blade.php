@extends('layouts.admincast')

@section('content')

<style>
    input {
        font-family: Tahoma, Geneva, sans-serif;
        font-size: 5px;
    }

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
        box-sizing: border-box;
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

    #msform input,
    #msform textarea {
        padding: 8px 15px 8px 15px;
        border: 1px solid #ccc;
        border-radius: 0px;
        margin-bottom: 25px;
        margin-top: 2px;
        width: 100%;
        box-sizing: border-box;
        font-family: montserrat;
        color: #2C3E50;
        /* background-color: #ECEFF1; */
        font-size: 16px;
        letter-spacing: 1px
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
        /* width: 100px; */
        background: grey;

        /* font-weight: bold; */
        /* color: rgb(255, 255, 255);
        border: 0 none;
        border-radius: 0px;
        cursor: pointer; */
        /* padding: 10px 5px; */
        /* margin: 10px 0px 10px 5px; */
        float: right
    }

    /* #msform .action-button:hover,
    #msform .action-button:focus {
        background-color: grey;
    } */

    #msform .action-button-previous {
        /* width: 100px; */
        background: grey;
        /* font-weight: bold;
        color: white;
        border: 0 none;
        border-radius: 0px;
        cursor: pointer;
        padding: 10px 5px;
        margin: 10px 5px 10px 0px; */
        float: right
    }

    #msform .action-button-previous:hover,
    #msform .action-button-previous:focus {
        background-color: grey;
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
        margin-bottom: 10px;
        font-weight: bold;
        text-align: right
    }

    .fieldlabels {
        color: gray;
        text-align: left
    }

    #progressbar {
        margin-bottom: 30px;
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
        background: lightgray;
        position: absolute;
        left: 0;
        top: 25px;
        z-index: -1
    }

    #progressbar li.active:before,
    #progressbar li.active:after {
        background: darkgrey;
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
    <div class="btn-group btn-breadcrumb mb-3">
        <a href="{{ route('admin.dashboard') }}"><i class="fa fa-home mx-2"></i></a>
        <a href="{{ route('clients.store') }}"> Clients</a>
        <i class="fas fa-caret-right mx-2" aria-hidden="true"></i>
        <a href="#" style="color:blue;"> Edit Client</a>
    </div>

    <div class="row">
        <div class="col-md-12 ">

            @if($message = Session::get('success'))

            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>warning!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card px-0 pt-4 pb-0 mt-3 mb-3">

                <form id="msform" action="{{route('clients.updatebiodata', $global_customer_id->global_customer_id)}}" method="POST">
                    @method('PATCH')
                    @csrf
                    <input type="hidden" name="lob_customer_id" value="{{$global_customer_id->lob_customer_id}}">
                    <fieldset>
                        <div class="form-card">

                            <div class="individual ">
                                <div class="row">

                                    <div class="col-md-4 ">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Client Type<font style="color:red;">*
                                                </font></span>
                                            <select name="client_type" readonly id="client_type" class="form-control" required>

                                                <option value="I" readonly @if($global_customer_id->client_type== "I")
                                                    selected="selected"
                                                    @endif>
                                                    Individual
                                                </option>

                                                <option value="C" readonly @if($global_customer_id->client_type== "C")
                                                    selected="selected"
                                                    @endif>
                                                    Corporate
                                                </option>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 " style="display:none" id="corporate">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Company Name<font style="color:red;">*
                                                </font></span>
                                            <input type="text" name="full_name" value="{{ $global_customer_id->full_name }}" class=" form-control" />

                                        </div>
                                    </div>

                                    <div class="col-md-4 " style="display:none" id="corporate1">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Company Pin<font style="color:red;">*
                                                </font></span>
                                            <input type="text" name="pin_no" value="{{ $global_customer_id->pin_no }}" class=" form-control" />
                                        </div>
                                    </div>


                                    <div class="col-md-4 " id="salutation_code">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Salutation<font style="color:red;">*
                                                </font></span>
                                            <select name="salutation_code" id="salutation_code" class="form-control">

                                                @foreach ($salutations as $salutation)
                                                <option value="{{$salutation -> name}}" @if($salutation -> name == $global_customer_id->salutation_code)
                                                    selected="selected"
                                                    @endif>
                                                    {{ $salutation -> name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 " id="first_name">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">First name<font style="color:red;">*
                                                </font></span>
                                            <input type="text" name="first_name" value="{{ $global_customer_id->first_name }} " id="first_name" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="col-md-4 " id="surname">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Surname<font style="color:red;">*
                                                </font></span>
                                            <input type="text" name="surname" value="{{ $global_customer_id->surname }} " id="surname" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="col-md-4 " id="other_names">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Other Names</span>
                                            <input type="text" name="other_names" value="{{ $global_customer_id->other_names}}" id="other_names" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="col-md-4 " id="idTypeDiv">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">ID Type<font style="color:red;">*
                                                </font>
                                            </span>
                                            <select name="id_type" id="id_type" class="form-control">

                                                <option value="N" @if($global_customer_id->id_type== "N")
                                                    selected="selected"
                                                    @endif>National ID</option>
                                                <option value="P" @if($global_customer_id->id_type== "P")
                                                    selected="selected"
                                                    @endif>Passport ID</option>
                                                <option value="F" @if($global_customer_id->id_type== "F")
                                                    selected="selected"
                                                    @endif>Foreigners ID</option>
                                                <option value="M" @if($global_customer_id->id_type== "M")
                                                    selected="selected"
                                                    @endif>Military ID</option>
                                            </select>
                                        </div>
                                    </div>



                                    <div class="col-md-4" id="idNoDiv">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">National ID<font style="color:red;">*
                                                </font></span>
                                            <input type="text" name="id_value" value="{{ $global_customer_id->id_value }}" id="id_number" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="col-md-4" id="passportDiv">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Passport Number<font style="color:red;">*
                                                </font></span>
                                            <input type="text" name="id_value" value="{{ $global_customer_id->id_value }}" id="passport_no" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="col-md-4" id="foreignerDiv">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Foreigner's ID<font style="color:red;">*
                                                </font></span>
                                            <input type="text" name="id_value" value="{{ $global_customer_id->id_value }}" id="foreigner_id" class="form-control" />
                                        </div>
                                    </div>



                                    <div class="col-md-4" id="militaryDiv">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Military ID<font style="color:red;">*
                                                </font></span>
                                            <input type="text" name="id_value" value="{{ $global_customer_id->id_value }}" id="military_id" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="col-md-4" id="pinDiv">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Pin Number<font style="color:red;">*
                                                </font></span>
                                            <input type="text" name="pin_no" value="{{ $global_customer_id->pin_no }}" id="pin_no" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="col-md-4 " id="genderDiv">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Gender <font style="color:red;">*
                                                </font>
                                            </span>
                                            <select name="gender_code" id="gender_code" class="form-control">

                                                @foreach ($genders as $gender)
                                                <option value="{{$gender -> name}}" @if($global_customer_id->gender_code== $gender -> gender_code)
                                                    selected="selected"
                                                    @endif>{{ $gender -> name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>



                                    <div class="col-md-4 " id="dobDiv">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Date Of Birth <font style="color:red;">*
                                                </font> </span>

                                            <input type="date" name="date_of_birth_registration" value="{{ $global_customer_id->date_of_birth_registration }}" id="date_of_birth_registration" class=" form-control" />
                                        </div>
                                    </div>

                                    {{-- <div class="col-md-4 " id="occupationDiv">
                                        <div class="form-group">
                                            <span class="control-label col-md-12">Occupation</span>
                                            <select name="occupation_code" id="occupation_code" class="form-control">
                                                <option value="{{ $global_customer_id->occupation_code }}">{{ $global_customer_id->occupation->name}} </option>
                                    @foreach ($occupations as $occupation)
                                    <option value="{{$occupation -> occupation_code}}">
                                        {{ $occupation -> name }}
                                    </option>
                                    @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <span class="control-label col-md-12"> Primary Phone <font style="color:red;">*
                                        </font> </span>

                                    <input type="text" name="phone_1" value="{{ $global_customer_id->phone_1}}" id="phone_1" class=" form-control" required />

                                </div>

                            </div>



                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <span class="control-label col-md-12">Secondary Phone <font style="color:red;">*
                                        </font> </span>

                                    <input type="text" name="phone_2" value="{{ $global_customer_id->phone_2 }}" id="phone_2" class=" form-control" required />
                                </div>
                            </div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <span class="control-label col-md-12">Email <font style="color:red;">*
                                        </font>
                                    </span>
                                    <input type="email" name="email" value="{{ $global_customer_id->email }}" id="email" class=" form-control" />
                                </div>
                            </div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <span class="control-label col-md-12">Address 1 <font style="color:red;">*
                                        </font> </span>
                                    <input type="text" name="address_1" value="{{ $global_customer_id->address_1 }}" id="address_1" class=" form-control" required />
                                </div>
                            </div>



                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <span class="control-label col-md-12">Address 2 </span>
                                    <input type="text" name="address_2" value="{{ $global_customer_id->address_2 }}" id="address_2" class=" form-control" />
                                </div>
                            </div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <span class="control-label col-md-12">Address 3 </span>
                                    <input type="text" name="address_3" value="{{ $global_customer_id->address_3 }}" id="address_3" class=" form-control" />
                                </div>
                            </div>

                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <span class="control-label col-md-12"> Town/City <font style="color:red;">*
                                        </font> </span>
                                    <input type="text" name="town_city" value="{{ $global_customer_id->town_city }}" id="town_city" class=" form-control" required />
                                </div>
                            </div>

                            <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <span class="control-label col-md-12"> Associated System <font style="color:red;">*</font> </span>
                                            <select class="selectpicker form-control" multiple data-live-search="true" name="asso_id">

                                                <option value="{{ $global_customer_id->asso_id }}"> </option>

                                                @foreach ( $customerlinksystems as $customerlinksystem)
                                                <option value="{{$customerlinksystem ->asso_id}}"> {{ $customerlinksystem ->business}}</option>
                                                @endforeach

                                            </select>
                                        </div>

                                    </div> -->



                            {{-- <div class="row"></div> --}}

                        </div>
<div class="row col-md-6"></div>

            </div>
        </div>
        <button type="submit" id="submit" class="btn btn-info float-right">Submit</button>

        </fieldset>
   
        </form>
    </div>
</div>
</div>
</div>
@endsection
@section("page_scripts")

<script>
    $(document).ready(function() {
        let iDType = $("#id_type").val();
        
        if (iDType === 'N') {
            
            $('#idNoDiv').show();
            $('#militaryDiv').hide();
            $('#foreignerDiv').hide();
            $('#passportDiv').hide();
        }

        if (iDType === 'M') {
            $('#militaryDiv').show();
            $('#idNoDiv').hide();
            $('#foreignerDiv').hide();
            $('#passportDiv').hide();
        }

        if (iDType === 'P') {
            $('#passportDiv').show();
            $('#idNoDiv').hide();
            $('#foreignerDiv').hide();
            $('#militaryDiv').hide();
        }

        if (iDType === 'F') {
            $('#foreignerDiv').show();
            $('#idNoDiv').hide();
            $('#passportDiv').hide();
            $('#militaryDiv').hide();
        }
        
        /**to enable the corporate fields on edit */
        let ctype = $("#client_type").val();
        if (ctype === "C") {
            $('#corporate').show();
            $('#corporate1').show();
            $('#corporate2').show();
            $('#corporate3').show();
            $('#corporate4').show();
            $('#salutation_code').hide();
            $('#first_name').hide();
            $('#surname').hide();
            $('#other_names').hide();
            //$('#customer_id').hide();
            // $("#id_type").prop('disabled',true);
            // $("#id_number").prop('disabled',true);
            $("#pin_no").prop('disabled', true);
            //$("#gender_code").prop('disabled',true);
            // $('#date_of_birth_registration').prop('disabled',true,'required',false);
            // $('#occupation_code').prop('disabled',true);
            $('#idNoDiv').hide();
            $('#militaryDiv').hide();
            $('#passportDiv').hide();
            $('#foreignerDiv').hide();
            $('#dobDiv').hide();
            $('#genderDiv').hide();
            $('#occupationDiv').hide();
            $('#idTypeDiv').hide();
            $('#pinDiv').hide();
        }
        if (ctype === "I") {
            $('#corporate').hide();
            $('#corporate1').hide();
            $('#salutation_code').show();
            $('#first_name').show();
            $('#surname').show();
            $('#other_names').show();
            $('#customer_id').show();
            $("#id_type").prop('disabled', false);
            //$("#id_number").prop('disabled',false);
            $("#pin_no").prop('disabled', false);
            $("#gender_code").prop('disabled', false);
            $('#date_of_birth_registration').prop('disabled', false);
            $('#occupation_code').prop('disabled', false);
            // $('#idNoDiv').show();
            // $('#militaryDiv').show();
            // $('#passportDiv').show();
            // $('#foreignerDiv').show();
            // $('#id_number').prop('disabled',false);
            // $('#military_id').prop('disabled',false);
            // $('#passport_no').prop('disabled',false);
            // $('#foreigner_id').prop('disabled',false);
            $('#dobDiv').show();
            $('#genderDiv').show();
            $('#occupationDiv').show();
            $('#idTypeDiv').show();
            $('#pinDiv').show();
        }

        // enable disabled corporate fields starts here
        $("select#client_type").change(function() {
            let ctype = $("#client_type").val();
            if (ctype === "C") {
                $('#corporate').show();
                $('#corporate1').show();
                $('#corporate2').show();
                $('#corporate3').show();
                $('#corporate4').show();
                $('#salutation_code').hide();
                $('#first_name').hide();
                $('#surname').hide();
                $('#other_names').hide();
                //$('#customer_id').hide();
                // $("#id_type").prop('disabled',true);
                // $("#id_number").prop('disabled',true);
                $("#pin_no").prop('disabled', true);
                //$("#gender_code").prop('disabled',true);
                // $('#date_of_birth_registration').prop('disabled',true,'required',false);
                // $('#occupation_code').prop('disabled',true);
                $('#idNoDiv').hide();
                $('#militaryDiv').hide();
                $('#passportDiv').hide();
                $('#foreignerDiv').hide();
                $('#dobDiv').hide();
                $('#genderDiv').hide();
                $('#occupationDiv').hide();
                $('#idTypeDiv').hide();
                $('#pinDiv').hide();
            }
            if (ctype === "I") {
                $('#corporate').hide();
                $('#corporate1').hide();
                $('#salutation_code').show();
                $('#first_name').show();
                $('#surname').show();
                $('#other_names').show();
                $('#customer_id').show();
                $("#id_type").prop('disabled', false);
                //$("#id_number").prop('disabled',false);
                $("#pin_no").prop('disabled', false);
                $("#gender_code").prop('disabled', false);
                $('#date_of_birth_registration').prop('disabled', false);
                $('#occupation_code').prop('disabled', false);
                $('#idNoDiv').show();
                $('#militaryDiv').show();
                $('#passportDiv').show();
                $('#foreignerDiv').show();
                // $('#id_number').prop('disabled',false);
                // $('#military_id').prop('disabled',false);
                // $('#passport_no').prop('disabled',false);
                // $('#foreigner_id').prop('disabled',false);
                $('#dobDiv').show();
                $('#genderDiv').show();
                $('#occupationDiv').show();
                $('#idTypeDiv').show();
                $('#pinDiv').show();
            }
        });

        $("select#id_type").change(function() {
            let iDType = $("#id_type").val();

            if (iDType === 'N') {
                $('#idNoDiv').show();
                $('#militaryDiv').hide();
                $('#foreignerDiv').hide();
                $('#passportDiv').hide();
            }

            if (iDType === 'M') {
                $('#militaryDiv').show();
                $('#idNoDiv').hide();
                $('#foreignerDiv').hide();
                $('#passportDiv').hide();
            }

            if (iDType === 'P') {
                $('#passportDiv').show();
                $('#idNoDiv').hide();
                $('#foreignerDiv').hide();
                $('#militaryDiv').hide();
            }

            if (iDType === 'F') {
                $('#foreignerDiv').show();
                $('#idNoDiv').hide();
                $('#passportDiv').hide();
                $('#militaryDiv').hide();
            }

            // else{
            //     $('#foreigner_id').prop('disabled',false);
            //     $('#id_number').prop('disabled',false);
            //     $('#passport_no').prop('disabled',false);
            //     $('#military_id').prop('disabled',false);
            // }
        });



        // $(".submit").click(function() {
        //     return false;
        // })


    });
</script>
@endsection