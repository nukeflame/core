@extends('layouts.admincast')

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
        <a href="#" style="color:blue;"> Edit Contacts</a>
    </div>

    <div class="row">
        <div class="col-md-12 ">


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

                <form id="msform" action="{{route('clients.updatecontacts')}}" method="POST">

                    @method('PUT')
                    @csrf
                    <input type="hidden" name="global_customer_id" value="{{$global_customer_id->global_customer_id}}">

                  


                    <fieldset>
                        <div class="form-card">

                            <div class="row mb-3">
                                <div class="col-7">
                                    <div class="col-sm-6">
                                        <button class="btn btn-default" id="addContact">+Add Contact</button>
                                    </div>
                                </div>

                            </div>

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td>Name</td>
                                        <td>Position</td>
                                        <td>Phone Number</td>
                                        <td>Email Address</td>
                                        <td>Remove</td>
                                    </tr>
                                </thead>

                                <tbody id="contact-div">
                                    @foreach($global_customer_id->clientContacts as $contacts)
                                    <input type="hidden" name="c_id[]" value="{{$contacts->id}}">
                                    <tr>

                                        <td class="no">1</td>
                                        <td>
                                            <input type="text" name="c_name[]" value="{{$contacts->c_name}}" id="c_name" class="form-control" required />
                                        </td>

                                        <td>
                                            <input type="text" name="position[]" value="{{$contacts->position}}" id="position" class="form-control" required />
                                        </td>

                                        <td>
                                            <input type="text" name="phone_no[]" value="{{$contacts->phone_no}}" id="phone_no" class="form-control" required />
                                        </td>

                                        <td>
                                            <input type="email" name="c_email[]" value="{{$contacts->c_email}}" id="c_email" class="form-control" required />
                                        </td>

                                        <td>
                                            <button id="removeBtn" class="btn btn-default btn-small"><span class="fa fa-trash"></span></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" id="submit" class="btn btn-info">Submit</button>
                    </fieldset>

                 <!-- <fieldset>
                        <div class="form-card">
                            <div class="row">
                                <div class="col-7">

                                </div>
                                <div class="col-5">
                                    <h4 class="steps">Step 4 - 4</h4>
                                </div>
                            </div> <br><br>
                            <h2 class="purple-text text-center"><strong>SUCCESS !</strong></h2> <br>
                            <div class="row justify-content-center">
                                <div class="col-3"> <img src="https://i.imgur.com/GwStPmg.png" class="fit-image"> </div>
                            </div> <br><br>
                            <div class="row justify-content-center">
                                <div class="col-7 text-center">
                                    <h5 class="purple-text text-center">You Have Successfully created a Client</h5>
                                </div>
                            </div>
                        </div>
                    </fieldset> -->
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section("page_scripts")

<script>
    $(document).ready(function() {
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
            } else {
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
        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
        var current = 1;
        var steps = $("fieldset").length;

        setProgressBar(current);

        $(".next").click(function() {

            current_fs = $(this).parent();
            next_fs = $(this).parent().next();

            //Add Class Active
            $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

            //show the next fieldset
            next_fs.show();
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
            setProgressBar(++current);
        });

        $(".previous").click(function() {

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
            setProgressBar(--current);
        });

        function setProgressBar(curStep) {
            var percent = parseFloat(100 / steps) * curStep;
            percent = percent.toFixed();
            $(".progress-bar")
                .css("width", percent + "%")
        }

        // $(".submit").click(function() {
        //     return false;
        // })

        //banks
        $(function() {
            $('#addBankBtn').click(function() {
                let n = ($('#bank-div tr').length - 0) + 1;
                let tr = '<tr><td class="no">' + n + '</td>' +
                    '<td><select name="bank_name[]" id="bank_type" class="form-control" required>' +
                    '<option value="">Select </option>' +
                    '@foreach ($banks as $bank)' +
                    '<option value="{{$bank -> bank_code }}">' +
                    '{{ $bank -> name }}' +
                    '</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</td>' +

                    '<td><select name="branch[]" id="branch" class="form-control" required>' +
                    '<option value="">Select </option>' +
                    '@foreach ($branches as $branch)' +
                    '<option value="{{ $branch -> branch_code }}">' +
                    '{{ $branch -> name }}' +
                    '</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</td>' +

                    '<td><input type="text" name="account_name[]" id="account_name" class="form-control" required /></td>' +

                    '<td><input type="text" name="account_no[]" id="account_no" class="form-control" required /></td>' +

                    '<td><select name="status[]" id="status" class="form-control" required>' +
                    '<option value="">Select </option>' +
                    '@foreach ($statuses as $status)' +
                    '<option value="{{ $status -> status_description }}">' +
                    '{{ $status ->status_description }}' +
                    '</option>' +
                    '@endforeach' +
                    '</select>' +
                    '</td>' +

                    '<td><select name="default_bank[]" id="default_bank" required class="form-control"><option value="1">Yes</option> <option value="0">No</option></select></td>' +

                    '<td> <button id="deleteBtn" class="btn btn-default btn-small"><span class = "fa fa-trash"></span></button></td></tr>';
                $('#bank-div').append(tr);
            });

            $('#bank-div').delegate('#deleteBtn', 'click', function() {
                $(this).parent().parent().remove();
            });

            $('#bank-div').delegate(
                '#bank_name, #branch,  #account_name, #account_no, #status, #default_bank', 'keyup',
                function() {
                    let tr = $(this).parent().parent();
                    let bank = tr.find('#bank_name').val() - 0;
                    let branch = tr.find('#branch').val() - 0;
                    let accName = tr.find('#account_name').val() - 0;
                    let accNo = tr.find('#account_no').val() - 0;
                    let status = tr.find('#status').val() - 0;
                    let d = tr.find('#default_bank').val() - 0;
                });
        });

        //contacts
        $(function() {
            $('#addContact').click(function() {
                let n = ($('#contact-div tr').length - 0) + 1;
                let tr = '<tr><td class="no">' + n + '</td>' +
                    '<td><input type="text" name="c_name[]" id="c_name" class="form-control" required /></td>' +

                    '<td><input type="text" name="position[]" id="position" class="form-control" required /></td>' +

                    '<td><input type="text" name="phone_no[]" id="phone_no" class="form-control" required /></td>' +

                    '<td><input type="email" name="c_email[]" id="c_email" class="form-control" required /></td>' +

                    '<td> <button id="removeBtn" class="btn btn-default btn-small"><span class = "fa fa-trash"></span></button></td></tr>';
                $('#contact-div').append(tr);
            });

            $('#contact-div').delegate('#removeBtn', 'click', function() {
                $(this).parent().parent().remove();
            });

            $('#contact-div').delegate('#c_name, #position,  #phone_no, #c_email', 'keyup',
                function() {
                    let tr = $(this).parent().parent();
                    let c_name = tr.find('#c_name').val() - 0;
                    let position = tr.find('#position').val() - 0;
                    let phoneNo = tr.find('#phone_no').val() - 0;
                    let cEmail = tr.find('#c_email').val() - 0;
                });
        });
    });
</script>
@endsection