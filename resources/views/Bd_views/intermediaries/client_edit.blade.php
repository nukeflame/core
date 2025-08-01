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

        .hide_column {
            display: none;
        }

        .nav-item.active {
            background-color: aliceblue;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card px-0 pt-4 pb-0 mb-3">
                    <h4 class="text-center text-lg-start mb-0 mx-2">Client Details</h4>
                    <hr>
                    <!-- tabs -->
                    <div class="col-12">
                        <ul class="nav nav-tabs">
                            <li class="nav-item" id="p_detail">
                                <a class="nav-link active" data-bs-toggle="tab" href="#general_details" role="tab">
                                    <span class="d-none d-sm-block">General Details</span>
                                </a>
                            </li>
                            <li class="nav-item" id="implementation_checklist">
                                <a class="nav-link" data-bs-toggle="tab" href="#implementation_checklist_content"
                                    role="tab">
                                    <span class="d-none d-sm-block">Implementation CheckList</span>
                                </a>
                            </li>
                            <li class="nav-item" id="value_added">
                                <a class="nav-link" data-bs-toggle="tab" href="#value_added_content" role="tab">
                                    <span class="d-none d-sm-block">Value Addition Checklist</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="general_details" role="tabpanel">
                                <!--  -->
                                <div class="card px-0 pt-4 pb-0 mb-3">

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
                                                            <div class="m-0">
                                                                <hr>
                                                            </div>
                                                            <input type="hidden" name="prospect_id" id="prospect_id"
                                                                value="">
                                                            <x-OnboardingInputDiv class=" col-md-3 col-sm-12 mt-2">
                                                                <x-SearchableSelect name="client_type" id="client_type"
                                                                    req="required" inputLabel="Client Type">
                                                                    <option value="">Select client type</option>
                                                                    <option value="C">Corporate</option>
                                                                    <option value="I">Retail</option>
                                                                    <option value="N">NGO</option>
                                                                    <option value="G">Government</option>
                                                                    <option value="S">SME's</option>
                                                                    </x-SelectInput>
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-SearchableSelect name="client_category"
                                                                    id="client_category" req="required"
                                                                    inputLabel="Client Category">
                                                                    <option value="">Select prospect category</option>
                                                                    <option value="N">New prospect</option>
                                                                    <option value="O">Organic growth</option>
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv style="display:none" id="corporate">
                                                                <x-Input name="corporate_name"
                                                                    placeholder="Enter company name" id="corporate_name"
                                                                    value="{{ old('corporate_name') }}"
                                                                    inputLabel="Company Name" req="required"
                                                                    onkeyup="this.value=this.value.toUpperCase();" />
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv id="salutation_code">
                                                                <x-SearchableSelect name="salutation_code"
                                                                    id="salutation_code" req=""
                                                                    inputLabel="Salutation">
                                                                    <option value="">Select salutation</option>
                                                                    @foreach ($salutations as $salutation)
                                                                        <option value="{{ $salutation->name }}">
                                                                            {{ $salutation->name }}</option>
                                                                    @endforeach
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv id="full_name_div">
                                                                <x-Input id="full_name" name="fname" req="required"
                                                                    inputLabel="Full Name" value="{{ old('fname') }}"
                                                                    placeholder="Enter full name"
                                                                    onkeyup='this.value=this.value.toUpperCase();' />
                                                            </x-OnboardingInputDiv>




                                                            <x-OnboardingInputDiv id="idTypeDiv">
                                                                <x-SelectInput name="id_type" id="id_type" req="required"
                                                                    inputLabel="ID Type">
                                                                    <option value="">Select ID Type</option>
                                                                    <option value="N">National ID</option>
                                                                    <option value="P">Passport ID</option>
                                                                    <option value="F">Foreigners ID</option>
                                                                    <option value="M">Military ID</option>
                                                                </x-SelectInput>
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv id="genderDiv">
                                                                <x-SelectInput name="gender" id="gender_code"
                                                                    class="form-control checkempty" req="required"
                                                                    inputLabel="Gender">
                                                                    <option value="">Select gender</option>
                                                                    @foreach ($genders as $gender)
                                                                        <option value="{{ $gender->gender_code }}">
                                                                            {{ $gender->name }}</option>
                                                                    @endforeach
                                                                </x-SelectInput>
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv id="occupationDiv">
                                                                <x-SearchableSelect name="occupation_code"
                                                                    id="occupation_code" req="required"
                                                                    inputLabel="Industry">
                                                                    <option value="">Select occupation</option>
                                                                    @foreach ($occupations as $occupation)
                                                                        <option value="{{ $occupation->name }}">
                                                                            {{ $occupation->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv id="division_div">
                                                                <x-SearchableSelect name="division" id="division"
                                                                    req="required" inputLabel="Division">
                                                                    <option value="">Select division</option>
                                                                    @foreach ($divisions as $division)
                                                                        <option value="{{ $division->id }}">
                                                                            {{ $division->name }}</option>
                                                                    @endforeach
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv>
                                                                <x-SearchableSelect name="insurance_class"
                                                                    id="insurance_class" req="required"
                                                                    inputLabel="Class of Insurance">
                                                                    <option value="">Select class of insurance
                                                                    </option>
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-SearchableSelect name="engage_type" id="engage_type"
                                                                    req="required" inputLabel="Nature of engagement">
                                                                    <option value="">Select engagement type </option>
                                                                    <option value="1">Direct</option>
                                                                    <option value="2">Broker</option>

                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-SearchableSelect name="lead_handler" id="lead_handler"
                                                                    req="required" inputLabel="CR Lead">
                                                                    <option value="">Select lead</option>
                                                                    @foreach ($users as $user)
                                                                        <option value="{{ $user->username }}">
                                                                            {{ strtoupper($user->firstname) }}
                                                                            {{ strtoupper($user->lastname) }}</option>
                                                                    @endforeach
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-SearchableSelect name="bd_lead" id="bd_lead"
                                                                    req="required" inputLabel="BD Lead">
                                                                    <option value="">Select lead</option>
                                                                    @foreach ($bd_users as $user)
                                                                        <option value="{{ $user->username }}">
                                                                            {{ strtoupper($user->firstname) }}
                                                                            {{ strtoupper($user->lastname) }}</option>
                                                                    @endforeach
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>
                                                        </div>

                                                        <div class="row my-md-3">
                                                            <B class="primary-color">Contact Details</B>
                                                            <div class="m-0">
                                                                <hr>
                                                            </div>
                                                            <x-OnboardingInputDiv>
                                                                <x-SearchableSelect name="contact_salutation"
                                                                    id="contact_salutation" req=""
                                                                    inputLabel="Contact Salutation">
                                                                    <option value="">Select salutation</option>
                                                                    @foreach ($salutations as $salutation)
                                                                        <option value="{{ $salutation->name }}">
                                                                            {{ $salutation->name }}</option>
                                                                    @endforeach
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-Input name="contact_name" id="contact_name"
                                                                    placeholder="Enter name" inputLabel="Contact Fullname"
                                                                    req="required" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-EmailInput id="email" name="email"
                                                                    req="required" inputLabel="Email Address"
                                                                    placeholder="Enter email" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv id="countryDiv">
                                                                <x-SearchableSelect name="country_code" id="country"
                                                                    req="required" inputLabel="Country">
                                                                    <option value="">Select country code</option>
                                                                    @foreach ($countries as $country)
                                                                        <option
                                                                            @if ($country->iso == 'KE') selected @endif
                                                                            value="{{ $country->id }}">
                                                                            {{ $country->name }}
                                                                            +{{ $country->country_code }}</option>
                                                                    @endforeach
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-Input name="town" id="town"
                                                                    placeholder="Enter town/city" inputLabel="Town/City"
                                                                    req="required" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-Input name="postal_address" id="postal_address"
                                                                    placeholder="Enter postal address"
                                                                    inputLabel="Postal Address" req="" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-Input name="postal_code" id="postal_code"
                                                                    placeholder="Enter Postal Code"
                                                                    inputLabel="Postal Code" req="" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-NumberInput id="telephone" name="telephone"
                                                                    req="required" inputLabel="Telephone"
                                                                    class="telephone"
                                                                    placeholder="Enter telephone number" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-NumberInput id="phone_1" name="phone_1"
                                                                    req="required" inputLabel="Primary Phone"
                                                                    class="phone" placeholder="Enter phone number" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-Input id="address_3" name="address_3" req="required"
                                                                    inputLabel="Physical Address"
                                                                    placeholder="Enter physical address" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-Input name="contact_position" id="contact_position"
                                                                    placeholder="Enter position"
                                                                    inputLabel="Contact Position" req="required" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-SearchableSelect name="alternative_salutation"
                                                                    id="alternative_salutation" req=""
                                                                    inputLabel="Alternative Contact Salutation">
                                                                    <option value="">Select salutation</option>
                                                                    @foreach ($salutations as $salutation)
                                                                        <option value="{{ $salutation->name }}">
                                                                            {{ $salutation->name }}</option>
                                                                    @endforeach
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-Input name="alternative_contact_name"
                                                                    id="alternative_contact_name" placeholder="Enter name"
                                                                    inputLabel="Alternative Contact Fullname"
                                                                    req="" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-EmailInput id="alternative_email"
                                                                    name="alternative_email" req=""
                                                                    inputLabel="Alternative Contact Email"
                                                                    placeholder="Enter email" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-NumberInput id="alternative_phone_number"
                                                                    name="alternative_phone_number" req=""
                                                                    inputLabel="Alternative Phone No." class="phone"
                                                                    placeholder="Enter phone number" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-Input name="alternative_contact_position"
                                                                    id="alternative_contact_position"
                                                                    placeholder="Enter position"
                                                                    inputLabel="Alternative Contact Position"
                                                                    req="" />
                                                            </x-OnboardingInputDiv>

                                                        </div>

                                                        <div class="row my-md-3">
                                                            <B class="primary-color">Quotation Details</B>
                                                            <div class="m-0">
                                                                <hr>
                                                            </div>

                                                            <x-OnboardingInputDiv>
                                                                <x-SearchableSelect name="quote_currency"
                                                                    id="quote_currency" req="required"
                                                                    inputLabel="Currency">
                                                                    <option value="">Select Currency</option>
                                                                    @foreach ($currencies as $currency)
                                                                        @if ($currency->base_currency == 'Y')
                                                                            <option value="{{ $currency->currency }}"
                                                                                shortcode="{{ $currency->short_description }}"
                                                                                selected>
                                                                                {{ $currency->description }}</option>
                                                                        @else
                                                                            <option value="{{ $currency->currency }}"
                                                                                shortcode="{{ $currency->short_description }}">
                                                                                {{ $currency->description }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </x-SearchableSelect>
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv>
                                                                <x-Input name="final_premium" id="final_premium"
                                                                    placeholder="Enter final premium"
                                                                    inputLabel="Final Premium" req="required" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-Input id="final_commission" name="final_commission"
                                                                    req="required" inputLabel="Final Commission"
                                                                    placeholder="Enter final commission" />
                                                            </x-OnboardingInputDiv>
                                                            <x-OnboardingInputDiv>
                                                                <x-textArea style="height: 1.2em;" name="remarks"
                                                                    id="remarks" placeholder="Enter remarks"
                                                                    rows="1" inputLabel="Remarks" req="required" />
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv>
                                                                <x-DateInput id="incept_date" name="incept_date"
                                                                    req="required" inputLabel="Inception Date"
                                                                    value="{{ old('incept_date') }}"
                                                                    placeholder="Date of Inception" />
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv>
                                                                <x-Input id="agent_name" name="agent_name" req="required"
                                                                    inputLabel="Agent Name"
                                                                    value="{{ old('agent_name') }}"
                                                                    placeholder="Agent Name" />
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv>
                                                                <x-NumberInput id="ag_comm_rate" name="ag_comm_rate"
                                                                    req="required" inputLabel="Agent Rate(%)"
                                                                    value="{{ old('ag_comm_rate') }}"
                                                                    placeholder="Agent Rate" />
                                                            </x-OnboardingInputDiv>

                                                        </div>

                                                        <div class="row my-md-3">
                                                            <B class="primary-color">KYC Details</B>
                                                            <div class="m-0">
                                                                <hr>
                                                            </div>


                                                            <x-OnboardingInputDiv>
                                                                <x-Input id="pin_no" name="pin_no" req="required"
                                                                    inputLabel="Pin/Tin Number"
                                                                    value="{{ old('pin_no') }}"
                                                                    placeholder="Enter pin/tin number" />
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv id="idNoDiv">
                                                                <label for="id_number"><span id="id_num_type"></span>
                                                                    <font style="color:red;">*</font>
                                                                </label>
                                                                <input type="text" name="identity_no"
                                                                    class="form-control" id="identity_no"
                                                                    placeholder="Enter identity number" required>
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv id="incorporation_div">
                                                                <x-Input id="incorporation_cert" name="incorporation_cert"
                                                                    req=""
                                                                    inputLabel="Cetificate Of Incorporation Number"
                                                                    value="{{ old('incorporation_cert') }}"
                                                                    placeholder="Enter cert. number" />
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv id="dob_div">
                                                                <x-DateInput id="dob" name="dob" req="required"
                                                                    inputLabel="Date of Registration"
                                                                    value="{{ old('dob') }}"
                                                                    placeholder="Date of Birth" />
                                                            </x-OnboardingInputDiv>

                                                            <x-OnboardingInputDiv id="">
                                                                <x-Input id="cr12" name="cr12" req=""
                                                                    inputLabel="CR12" value="{{ old('cr12') }}"
                                                                    placeholder="Enter CR12" />
                                                            </x-OnboardingInputDiv>
                                                        </div>


                                                    </div>
                                                </div>
                                                <hr>
                                                <!-- <button class="btn btn-outline-success next action-button" name="next">Next</button> -->
                                                <div class="text-right">
                                                    <button type="submit" id="submit"
                                                        class="btn btn-success text-white">
                                                        <span class="fa fa-save "></span> Amend Details
                                                    </button>
                                                </div>

                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                                <!--  -->
                            </div>
                            <div class="tab-pane fade" id="bank_details" role="tabpanel">
                                <div class="row">
                                    <table id="bank_table">
                                        <thead>
                                            <tr>
                                                <th class="hide_column">Item No</th>
                                                <th class="hide_column">Bank Code</th>
                                                <th class="hide_column">Branch Code</th>
                                                <th>Bank Name</th>
                                                <th>Branch</th>
                                                <th>Account Name</th>
                                                <th>Account Number</th>
                                                <th>Action</th>
                                                <th><a class="openmod" style="cursor: pointer;"><i
                                                            class="fa fa-plus-square-o"></i> Add</a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="implementation_checklist_content" role="tabpanel">
                                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal"
                                    data-bs-target="#myModal">
                                    Implementation CheckList
                                </button>
                                <table id="implementation_table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Objective</th>
                                            <th>Prompt Date</th>
                                            <th>Status</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>

                            </div>
                            <div class="tab-pane fade" id="value_added_content" role="tabpanel">
                                <!-- Content for Value Adds Checklist -->
                                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal"
                                    data-bs-target="#vaddmodal">
                                    Value Addition Checklist
                                </button>
                                <table id="vadd_table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Cost</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <!--  -->
                            </div>
                        </div>
                    </div>

                    <!-- endtabs -->
                </div>
                <div class="modal fade" id="edit_bank_accounts" data-backdrop="static" data-keyboard="false"
                    role="dialog" tabindex="0">
                    <div class="modal-dialog modal-lg">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">

                                <h4 class="modal-title">Edit Bank Details</h4>
                                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form id="edit-bank-form" method="post" class="form-horizontal">
                                    {{ csrf_field() }}
                                    <fieldset>

                                        <input type="hidden" name="item_no" id="item_no" value="" />
                                        <div class="row">
                                            <x-OnboardingInputDiv class="col-md-6">
                                                <x-SelectInput name="bank_code" id="edit_bank_code" req="required"
                                                    inputLabel="Bank Name">
                                                    <option value="">Select bank</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->bank_code }}">
                                                            {{ $bank->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SelectInput>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv class="col-md-6">
                                                <x-SelectInput name="branch" id="edit_branch" req="required"
                                                    inputLabel="Bank Branch">
                                                    <option value="">Select bank branch</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->branch_code }}">
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SelectInput>
                                            </x-OnboardingInputDiv>
                                        </div>
                                        <div class="row">
                                            <x-OnboardingInputDiv class="col-md-6">
                                                <x-Input id="edit_account_name" name="account_name" req="required"
                                                    inputLabel="Account Name" value=""
                                                    placeholder="Enter account name"
                                                    oninput='this.value=this.value.toUpperCase();' />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv class="col-md-6">
                                                <label for="account_no">Account Number<font style="color:red;">*</font>
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" name="account_no" id="edit_account_no"
                                                        class="form-control checkempty" />
                                                </div>
                                            </x-OnboardingInputDiv>

                                        </div>
                                        <div class="row">
                                            <x-OnboardingInputDiv class="col-md-6">
                                                <label for="default_bank">Set as Default:</label><br>
                                                <input type="radio" id="default_yes" name="default_bank"
                                                    value="1">
                                                <label for="default_yes">Yes</label>
                                                <input type="radio" id="default_no" name="default_bank" value="0"
                                                    checked>
                                                <label for="default_no">No</label>
                                            </x-OnboardingInputDiv>
                                        </div>
                                    </fieldset>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary btn-fill"
                                            id="edit_bank_account_btn">Save</button>
                                        <button type="button" class="btn btn-default btn-red" id="cancel-btn"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="add_bank_account" data-backdrop="static" data-keyboard="false"
                    role="dialog" tabindex="0">
                    <div class="modal-dialog modal-lg">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">

                                <h4 class="modal-title">Add Bank Account</h4>
                                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form id="add-bank-form" method="post" class="form-horizontal">
                                    {{ csrf_field() }}
                                    <fieldset>

                                        <input type="hidden" name="global_customer_id" id="global_customer_id"
                                            value="{{ $client->global_customer_id }}" />
                                        <div class="row">
                                            <x-OnboardingInputDiv class="col-md-6">
                                                <x-SelectInput name="bank_code" id="add_bank_code" req="required"
                                                    inputLabel="Bank Name">
                                                    <option value="">Select bank</option>
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->bank_code }}">
                                                            {{ $bank->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SelectInput>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv class="col-md-6">
                                                <x-SelectInput name="branch" id="add_branch" req="required"
                                                    inputLabel="Bank Branch">
                                                    <option value="">Select bank branch</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->branch_code }}">
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SelectInput>
                                            </x-OnboardingInputDiv>
                                        </div>
                                        <div class="row">
                                            <x-OnboardingInputDiv class="col-md-6">
                                                <x-Input id="add_account_name" name="account_name" req="required"
                                                    inputLabel="Account Name" value=""
                                                    placeholder="Enter account name"
                                                    oninput='this.value=this.value.toUpperCase();' />
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv class="col-md-6">
                                                <label for="account_no">Account Number<font style="color:red;">*</font>
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" name="account_no" id="add_account_no"
                                                        class="form-control checkempty" />
                                                </div>
                                            </x-OnboardingInputDiv>

                                        </div>
                                        <div class="row">
                                            <x-OnboardingInputDiv class="col-md-6">
                                                <label for="default_bank">Set as Default:</label><br>
                                                <input type="radio" id="default_yes" name="default_bank"
                                                    value="1">
                                                <label for="default_yes">Yes</label>
                                                <input type="radio" id="default_no" name="default_bank" value="0"
                                                    checked>
                                                <label for="default_no">No</label>
                                            </x-OnboardingInputDiv>
                                        </div>
                                    </fieldset>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary btn-fill"
                                            id="add_bank_account_btn">Save</button>
                                        <button type="button" class="btn btn-default btn-red" id="cancel-btn"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--  -->
            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Implementation CheckList</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="checklistForm" action="{{ route('submit.checklist') }}" method="POST">
                            <div class="modal-body">
                                @csrf

                                <div class="mb-3">
                                    <label for="taskName" class="form-label">Select Task</label>
                                    <select name="task_slug" id="taskName" class="form-select"
                                        onchange="populateTaskDescription()">
                                        <option value="">-- Select Task --</option>
                                        @foreach ($implementation_checklists as $checklist)
                                            <option value="{{ $checklist->task_slug }}">{{ $checklist->task_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="client_number" value="{{ $client->global_customer_id }}">
                                <div class="mb-3">
                                    <label for="taskDescription" class="form-label">Task Description</label>
                                    <select name="task_description_id" id="taskDescription" class="form-select" disabled>
                                        <option value="">-- Select Description --</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="promptDays" class="form-label">Prompt Days</label>
                                    <input type="text" name="prompt_days" class="form-control" id="promptDays"
                                        readonly>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" form="checklistForm">Save changes</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <!--  -->
            <!-- Modal -->
            <div class="modal fade" id="vaddmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Value Addition CheckList</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="vat" action="{{ route('submit.checklist') }}" method="POST">
                            <div class="modal-body">
                                @csrf

                                <div class="mb-3">
                                    <label for="taskName" class="form-label">Select ...</label>
                                    <select name="task_name" id="valueadd" class="form-select"
                                        onchange="populatePromptDays()">
                                        <option value="">-- Select Task --</option>
                                        <option value="risk-management">Risk Management</option>
                                        <option value="diapers">Diapers</option>
                                    </select>
                                </div>

                                <input type="hidden" name="client_number" value="{{ $client->global_customer_id }}">
                                <input type="hidden" name="type" value="VAC">

                                <div class="mb-3">
                                    <label for="taskDescription" class="form-label">Cost</label>
                                    <input type="number" name="cost" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="due_day" class="form-label">Due Date</label>
                                    <input type="text" name="due_day" class="form-control" id="due_day" readonly>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" form="vat">Save changes</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <!--  -->
        @endsection
        @section('page_scripts')
            <script>
                function valid(element) {
                    var pin_no = $(element).val();
                    var spans = $('.meso');
                    spans.remove();
                    $.ajax({
                        type: "POST",
                        data: {
                            pin_no: pin_no
                        },
                        dataType: "json",
                        success: function(res) {


                        },
                        error: function(err) {

                            if (err.status == 422) { // when status code is 422, it's a validation issue

                                $('#success_message').fadeIn().html(err.responseJSON.message);

                                // you can loop through the errors object and show it to the user

                                // display errors on each form field
                                $.each(err.responseJSON.errors, function(i, error) {
                                    var el = $(document).find('[name="' + i + '"]');

                                    el.after($('<span class="meso" style="color: red;">' + error[0] +
                                        '</span>'));

                                });
                            }
                        }
                    });
                }
                // Fetching the objectives data from Laravel to JavaScript
                const objectivesData = @json(
                    $implementation_checklists->map(function ($checklist) {
                        return [
                            'task_slug' => $checklist->task_slug,
                            'objectives' => $checklist->objectives,
                        ];
                    }));

                function populateTaskDescription() {
                    const taskNameSelect = document.getElementById('taskName');
                    const taskDescriptionSelect = document.getElementById('taskDescription');
                    const promptDaysInput = document.getElementById('promptDays');

                    const selectedTaskSlug = taskNameSelect.value;
                    const selectedTask = objectivesData.find(task => task.task_slug === selectedTaskSlug);

                    // Clear previous options and reset prompt days input
                    taskDescriptionSelect.innerHTML = '<option value="">-- Select Description --</option>';
                    promptDaysInput.value = '';

                    if (selectedTask) {
                        selectedTask.objectives.forEach(objective => {
                            const option = document.createElement('option');
                            option.value = objective.id; // Assuming you want to store the objective ID
                            option.textContent = objective.description; // Display description
                            taskDescriptionSelect.appendChild(option);
                        });

                        // Enable the task description dropdown
                        taskDescriptionSelect.disabled = false;

                        // Add an event listener to populate prompt days when an objective is selected
                        taskDescriptionSelect.onchange = function() {
                            const selectedObjective = selectedTask.objectives.find(obj => obj.id == this.value);
                            if (selectedObjective) {
                                const promptDays = parseInt(selectedObjective.prompt_days) ||
                                    0; // Ensure prompt_days is an integer


                                const today = new Date();

                                // Create a new date for prompt date calculation
                                const promptDate = new Date(today);
                                promptDate.setDate(today.getDate() + promptDays); // Add prompt days to today


                                // Format the date to YYYY-MM-DD
                                const options = {
                                    year: 'numeric',
                                    month: '2-digit',
                                    day: '2-digit'
                                };
                                const formattedDate = promptDate.toLocaleDateString('en-CA', options);
                                promptDaysInput.value = formattedDate; // Set the formatted date
                            } else {
                                promptDaysInput.value = ''; // Clear if no objective is selected
                            }
                        };
                    } else {
                        taskDescriptionSelect.disabled = true; // Disable if no task is selected
                    }
                }

                function populatePromptDays() {
                    // Get the current date
                    let today = new Date();

                    // Add 14 days to the current date
                    today.setDate(today.getDate() + 14);

                    // Format the date as YYYY-MM-DD
                    let year = today.getFullYear();
                    let month = (today.getMonth() + 1).toString().padStart(2, '0'); // Months are 0-based, so we add 1
                    let day = today.getDate().toString().padStart(2, '0');

                    let formattedDate = `${year}-${month}-${day}`;

                    // Set the prompt date field
                    document.getElementById('due_day').value = formattedDate;
                }
            </script>
            <script>
                $(document).ready(function() {
                    let client = "{{ $client->global_customer_id }}"
                    let ins_class = '';

                    if (client != null && client != '' && client != undefined) {
                        $.ajax({
                            type: "GET",
                            data: {
                                'client': client
                            },
                            url: "{{ route('getclientdtls') }}",
                            success: function(resp) {
                                $('#prospect_name').text(resp.full_name);
                                $('#postal_address').text(resp.postal_address);
                                $('#postal_code').text(resp.postal_code);
                                $('#prospect_id').val(resp.opportunity_id);
                                $('#client_type').val(resp.client_type).trigger('change');
                                $('#client_category').val(resp.client_category).trigger('change');
                                $('#division').val(resp.division).trigger('change');
                                $('#division').trigger('change');
                                $('#insurance_class').val(resp.insurance_class).trigger('change');
                                $('#engage_type').val(resp.engage_type).trigger('change')
                                $('#country').val(resp.country_code).trigger('change')
                                $('#full_name').val(resp.full_name);
                                $('#corporate_name').val(resp.full_name);
                                $('#phone_no0').val(resp.phone)
                                $('#phone_1').val(resp.phone)
                                $('#bd_lead').val(resp.lead_handler).trigger('change');
                                $('#occupation_code').val(resp.industry).trigger('change');
                                $('#email').val(resp.email);
                                $('#contact_name').val(resp.contact_name);
                                $('#town').val(resp.town);
                                $('#postal_address').val(resp.postal_address);
                                $('#postal_code').val(resp.postal_code);
                                $('#telephone').val(resp.telephone);
                                $('#contact_position').val(resp.contact_position);
                                $('#address_3').val(resp.physical_address);
                            }
                        })
                    }

                    $("select#client_type").change(function() {
                        let ctype = $("#client_type").val();

                        $.ajax({
                            type: 'GET',
                            url: "{{ route('get_leads') }}",
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {

                                    var leadSelect = document.getElementById('lead_select');
                                    // Clear existing options
                                    leadSelect.innerHTML = '<option value="">Select lead</option>';

                                    var leads = response.data.filter((lead) => lead.client_type ==
                                        ctype);

                                    // Add new options based on the fetched leads
                                    leads.forEach(function(lead) {
                                        var option = document.createElement('option');
                                        option.value = lead
                                            .code; // Assuming the lead has an ID property
                                        option.textContent = lead
                                            .full_name; // Replace with the appropriate property for the lead name
                                        leadSelect.appendChild(option);
                                    });
                                } else {

                                }
                            },
                            error: function(xhr, textStatus, error) {

                            }
                        })
                    });
                    $('#implementation_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('getimplementationdata') }}",
                            type: "get",
                            data: {
                                client_no: "{{ $client->global_customer_id }}",
                            }
                        },
                        columns: [{
                                data: 'task',
                                name: 'task'
                            },
                            {
                                data: 'objective',
                                name: 'objective'
                            },
                            {
                                data: 'prompt_date',
                                name: 'prompt_date'
                            },
                            {
                                data: 'cstatus',
                                name: 'cstatus'
                            },
                            {
                                data: 'action',
                                name: 'action'
                            }
                        ]
                    });
                    $('#vadd_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('getvalueadddata') }}",
                            type: "get",
                            data: {
                                client_no: "{{ $client->global_customer_id }}",
                            }
                        },
                        columns: [{
                                data: 'task',
                                name: 'task'
                            },
                            {
                                data: 'cost',
                                name: 'cost'
                            },
                            {
                                data: 'due_date',
                                name: 'due_date'
                            },
                            {
                                data: 'cstatus',
                                name: 'cstatus'
                            },
                            {
                                data: 'action',
                                name: 'action'
                            }
                        ]
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
                                    var companyInput = $('[name="corporate_name"]');

                                    firstNameInput.val(response.data.first_name);
                                    secondNameInput.val(response.data.second_name);
                                    emailInput.val(response.data.email);
                                    phoneInput.val(response.data.phone_number);
                                    companyInput.val(response.data.full_name);
                                }
                            },

                        });
                    })

                    $('#incorporation_div').hide();

                    // enable disabled corporate fields starts here
                    $("select#client_type").change(function() {
                        let ctype = $("#client_type").val();
                        if (ctype === "C") {
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
                            $('#occupationDiv').hide();
                            $('#incorporation_div').show();
                        } else {
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
                        }
                    });

                    //set identity number label on load
                    $('#id_num_type').text('National ID');

                    //set identity number label on change of type
                    $("select#id_type").change(function() {
                        let iDType = $("#id_type").val();

                        if (iDType === 'N') {
                            $('#id_num_type').text('National ID');
                        } else if (iDType === 'M') {
                            $('#id_num_type').text('Millitary ID');
                        } else if (iDType === 'P') {
                            $('#id_num_type').text('Passport Number');
                        } else if (iDType === 'F') {
                            $('#id_num_type').text('Foreigners ID');
                        } else {
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
                            highlight: function(element, errorClass) {},
                            unhighlight: function(element, errorClass) {},
                            rules: {
                                first_name: {
                                    minlength: 6,
                                }
                            }
                        });

                        // end validation
                        if (form.valid() === true) {

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
                        let myform = document.getElementById("msform");
                        let formData = new FormData(myform);

                        $.ajax({
                            type: 'post',
                            data: formData,
                            url: "{{ route('client.onboard') }}",
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                if (res.status == 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: `Client Number: ${res.client_number}`,
                                        text: 'Client successfully created'
                                    })
                                    setTimeout(function() {
                                        // location.reload();
                                        window.location.href =
                                            `/brokerage/intermediary/agent/view/${res.global_id}`;
                                    }, 2000);
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

                    $("#amend_general").click(function(e) {
                        e.preventDefault()
                        $(this).attr('disabled', true).text("Saving...")

                        let myform = document.getElementById("msform");
                        let formData = new FormData(myform);

                        $.ajax({
                            type: 'post',
                            data: formData,
                            url: "{{ route('client.edit') }}",
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                $('#amend_general').attr('disabled', false).text("Amend")
                                if (res.status == 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: `Client Number: ${res.client_number}`,
                                        text: 'Client successfully edited'
                                    })
                                    setTimeout(function() {
                                        location.reload();
                                        // window.location.href =  `brokerage/intermediary/client_view?client=${res.global_id}`;
                                    }, 2000);
                                } else {
                                    $('#amend_general').attr('disabled', false).text("Amend")
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
                        var counter = 0;
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
                                counter = +1;
                                $('#bank_details').append(
                                    `<div class="row">
                            <x-OnboardingInputDiv>
                                <x-SelectInput name="bank_code[]" id="bank_type${counter}" req="required" inputLabel="Bank Name">
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank->bank_code }}">
                                            {{ $bank->name }}
                                        </option>
                                    @endforeach
                                </x-SelectInput>
                            </x-OnboardingInputDiv>

                            <x-OnboardingInputDiv>
                                <x-SelectInput name="branch[]" id="branch${counter}" req="required" inputLabel="Bank Branch">
                                    @foreach ($branches as $branch)
                                    <option value="{{ $branch->branch_code }}">
                                        {{ $branch->name }}
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


                            $('input[type=radio]').change(function() {
                                $('input[type=radio]:checked').not(this).prop('checked', false)
                            })
                        });

                        $('#bank_details').delegate('.remove_bank', 'click', function() {
                            $(this).parent().parent().parent().remove();
                        });

                    });

                    function toggleFields() {
                        const selectedType = $("#client_type").val();
                        if (selectedType === "C") {
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
                            $('#occupationDiv').hide();
                            $('#incorporation_div').show();
                        } else {
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
                        }
                    }
                    toggleFields();
                    $("#amend_contacts").click(function(e) {
                        e.preventDefault()

                        $(this).attr('disabled', true).text("Saving...")

                        var datastring = $('#contact_edit').serialize();

                        var client = {!! json_encode($client) !!};

                        $.ajax({
                            type: 'post',
                            data: datastring,
                            url: "{{ route('client.contact.edit') }}",
                            success: function(res) {
                                $('#amend_contacts').attr('disabled', false).text("Amend")
                                if (res.status == 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        text: 'Client contacts successfully updated'
                                    })
                                    setTimeout(function() {
                                        location.reload();
                                        var url = "{{ route('client.view') }}";
                                        url += "?client=" + encodeURIComponent(client
                                            .global_customer_id);

                                        window.location.href = url;
                                    }, 2000);
                                } else {
                                    $('#amend_contacts').attr('disabled', false).text("Amend")
                                    Swal.fire({
                                        icon: 'error',
                                        text: res.message
                                    });
                                }
                            }
                        });
                    })

                    $('#bank_table tbody').on('click', '#edit_bank', function() {
                        var rowData = $(this).closest('tr').find('td').map(function() {
                            return $(this).text();
                        }).get();

                        // Populate the modal with the row data
                        var itemNo = rowData[0];
                        var bank = rowData[1];
                        var branch = rowData[2];
                        var accountName = rowData[5];
                        var accountNo = rowData[6];

                        $('#item_no').val(itemNo);
                        $("#edit_bank_code").val(bank.trim()).trigger('chosen:updated');
                        $("#edit_branch").val(branch.trim()).trigger('chosen:updated');
                        $("#edit_account_name").val(accountName);
                        $("#edit_account_no").val(accountNo);

                        $('#edit_bank_accounts').modal('show');
                    })
                    var validator = $('#edit-bank-form').validate({
                        rules: {
                            bank_code: {
                                required: true
                            },
                            branch: {
                                required: true
                            },
                            account_no: {
                                required: true
                            },
                            account_name: {
                                required: true
                            },
                        },
                    })

                    var validate = $('#add-bank-form').validate({
                        rules: {
                            bank_code: {
                                required: true
                            },
                            branch: {
                                required: true
                            },
                            account_no: {
                                required: true
                            },
                            account_name: {
                                required: true
                            },
                        },
                    })

                    $('#edit_bank_account_btn').click(function(e) {
                        e.preventDefault()

                        $('#edit_bank_account_btn').text("Saving....");
                        $('#edit_bank_account_btn').prop("disabled", "disabled");

                        var datastring = $('#edit-bank-form').serialize();

                        $('#edit-bank-form').valid()
                        var isValid = validator.form();

                        var client = {!! json_encode($client) !!};


                        if (isValid) {
                            $.ajax({
                                url: '{{ route('client.edit.bank.account') }}',
                                method: 'POST',
                                data: datastring,
                                success: function(response) {
                                    // Handle success response
                                    $('#edit_bank_account_btn').removeAttr("disabled").text("Save");
                                    if (response.data.status == 1) {

                                        toastr.success('Updated Successfully', {
                                            timeOut: 5000
                                        });

                                        var url = "{{ route('client.view') }}";
                                        url += "?client=" + encodeURIComponent(client
                                            .global_customer_id);

                                        window.location.href = url;
                                    }

                                },
                                error: function(xhr, status, error) {
                                    $('#edit_bank_account_btn').removeAttr("disabled").text("Save");

                                    toastr.error('Failed to update', {
                                        timeOut: 5000
                                    });
                                }
                            })
                        }
                    })

                    $('#add_bank_account_btn').click(function(e) {
                        e.preventDefault()

                        $('#add_bank_account_btn').text("Saving....");
                        $('#add_bank_account_btn').prop("disabled", "disabled");

                        var datastring = $('#add-bank-form').serialize();

                        $('#add-bank-form').valid()
                        var isValid = validate.form();

                        var client = {!! json_encode($client) !!};


                        if (isValid) {
                            $.ajax({
                                url: '{{ route('client.add.bank.account') }}',
                                method: 'POST',
                                data: datastring,
                                success: function(response) {
                                    // Handle success response
                                    $('#add_bank_account_btn').removeAttr("disabled").text("Save");
                                    if (response.data.status == 1) {

                                        toastr.success('Added Successfully', {
                                            timeOut: 5000
                                        });

                                        var url = "{{ route('client.view') }}";
                                        url += "?client=" + encodeURIComponent(client
                                            .global_customer_id);

                                        window.location.href = url;
                                    }

                                },
                                error: function(xhr, status, error) {
                                    $('#add_bank_account_btn').removeAttr("disabled").text("Save");

                                    toastr.error('Failed to add', {
                                        timeOut: 5000
                                    });
                                }
                            })
                        }
                    })

                    $('.openmod').click(function(e) {
                        $('#add_bank_account').modal('show');
                    })

                });
            </script>
            <style>
                .vertical-divider {
                    border-left: 3px solid #ccc;
                    padding-left: 15px;
                    /* Adjust spacing as needed */
                }
            </style>
        @endsection
