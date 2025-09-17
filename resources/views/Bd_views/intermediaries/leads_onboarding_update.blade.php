@extends('layouts.app')

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

        .primary-color {
            color: #E1251B;
        }

        #full_name_results {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            width: 100%;
            background-color: white;
            z-index: 9999;
        }

        .dropdown-item {
            padding: 8px;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card px-0 pt-4 pb-0 mb-3">
                    <h6 class="text-center text-lg-start mb-0 mx-2">Prospect
                        Onboarding Update
                    </h6>
                    <hr>
                    <div class="card-body">

                        <form id="leads_form">
                            @csrf
                            <input type="hidden" name="prospect" value="{{ $prospect }}" id="prospectId" />
                            <input type="text" value="U" name="updateState" hidden />
                            <fieldset>

                                {{-- start of new code --}}

                                <div class="form-card">
                                    <div class="individual">
                                        <b class="primary-color">Cedant Details</b>
                                        <hr>
                                        <div class="row mb-4">

                                            <x-OnboardingInputDiv id="prequalification_div">
                                                <x-SearchableSelect name="type_of_bus" id="type_of_bus" req="required"
                                                    inputLabel="Type of Business">
                                                    <option value="">--Select type of business--</option>
                                                    @foreach ($types_of_bus as $type_of_bus)
                                                        @if (in_array($type_of_bus->bus_type_id, ['FPR', 'FNP']))
                                                            <option value="{{ $type_of_bus->bus_type_id }}"
                                                                {{ old('type_of_bus', $prospProperties->type_of_bus) == $type_of_bus->bus_type_id ? 'selected' : '' }}>
                                                                {{ $type_of_bus->bus_type_name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="customer_id" id="customer_id" req=""
                                                    inputLabel="Cedant">
                                                    <option value="">--Select Cedant--</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->customer_id }}"
                                                            {{ isset($prospProperties) && $prospProperties->customer_id == $customer->customer_id ? 'selected' : '' }}>
                                                            {{ $customer->name }}
                                                            <!-- Access the 'name' property -->
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="countryDiv">
                                                <x-SearchableSelect name="client_type" id="client_type" req="required"
                                                    inputLabel="Lead Type">
                                                    <option value="">Select lead type</option>
                                                    @foreach ($customer_types as $cusType)
                                                        <option value="{{ $cusType->type_name }}"
                                                            {{ isset($prospProperties) && $prospProperties->client_type == $cusType->type_name ? 'selected' : '' }}>
                                                            {{ $cusType->type_name }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>
                                            <x-OnboardingInputDiv id="leadNameDiv">
                                                <x-Input name="lead_name" id="lead_name" req="required"
                                                    value="{{ $prospProperties->lead_name }}" inputLabel="Lead Name"
                                                    oninput="this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());" />
                                                <div id="lead_name_results" class="dropdown-menu" style="display: none;">
                                                </div>
                                                <div class="error-message text-danger" id="lead_name_error"></div>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="lead_year" id="lead_year" req="required"
                                                    inputLabel="Year">
                                                    <option value="">Select year</option>
                                                    @foreach ($pipeYear as $year)
                                                        <option value="{{ $year->id }}"
                                                            {{ (int) old('pip_year', $prospProperties->pip_year) === (int) $year->id ? 'selected' : '' }}>
                                                            {{ $year->year }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>

                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv>
                                                <x-SearchableSelect name="client_category" id="client_category"
                                                    req="required" inputLabel="Insured Category">
                                                    <option value="">Select prospect category</option>
                                                    <option value="N"
                                                        {{ isset($prospProperties) && $prospProperties->client_category == 'N' ? 'selected' : '' }}>
                                                        New
                                                        prospect</option>
                                                    <option value="O"
                                                        {{ isset($prospProperties) && $prospProperties->client_category == 'O' ? 'selected' : '' }}>
                                                        Organic
                                                        growth</option>
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <x-OnboardingInputDiv id="countryDiv">
                                                <x-SearchableSelect name="country_code" id="country" req="required"
                                                    inputLabel="Country">
                                                    <option value="">Select country code</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->country_iso }}"
                                                            {{ isset($prospProperties) && $prospProperties->country_code == $country->country_iso ? 'selected' : '' }}>
                                                            ({{ $country->country_code }})
                                                            {{ $country->country_name }} ({{ $country->country_iso }})
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>

                                            <div class="col-sm-3">
                                                <label class="form-label required">Branch</label>
                                                <div class="cover-card">
                                                    <select class="form-inputs section select2" name="branchcode"
                                                        id="branchcode" required>
                                                        <option value="">Choose Branch</option>
                                                        @foreach ($branches as $branch)
                                                            @if ($branch->status == 'A')
                                                                <option value="{{ $branch->branch_code }}"
                                                                    {{ isset($prospProperties) && $prospProperties->branchcode == $branch->branch_code ? 'selected' : '' }}>
                                                                    {{ firstUpper($branch->branch_name) }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <id="secondary_details">
                                    <b class="primary-color">Contact Details</b>
                                    <hr>
                                    <div id="contactsContainer">
                                        <div class="row mb-4 contactsContainers" data-counter="0">
                                            @foreach ($contacts_det as $index => $contact)
                                                <x-OnboardingInputDiv>
                                                    <x-Input name="contact_name[]" id="contact_name-{{ $index }}"
                                                        placeholder="Enter name" inputLabel="Contact Full Name"
                                                        req="required" value="{{ $contact['contact_name'] ?? '' }}"
                                                        oninput="this.value = this.value.toUpperCase();" />
                                                    <div id="contact_name_results-{{ $index }}"
                                                        class="dropdown-menu" style="display: none;"></div>
                                                    <div class="error-message text-danger "
                                                        id="full_name_error-{{ $index }}"></div>
                                                </x-OnboardingInputDiv>



                                                <x-OnboardingInputDiv>
                                                    <x-EmailInput id="email-{{ $index }}" name="email[]"
                                                        req="required" inputLabel="Email Address" placeholder="Enter email"
                                                        value="{{ $contact['email'] ?? '' }}" />
                                                    <div class="error-message text-danger"
                                                        id="email_error-{{ $index }}"></div>
                                                </x-OnboardingInputDiv>


                                                <x-OnboardingInputDiv>
                                                    <x-NumberInput id="phone_number-{{ $index }}"
                                                        name="phone_number[]" req="required" inputLabel="Mobile"
                                                        class="phone" placeholder="Enter phone number"
                                                        value="{{ $contact['phone'] ?? '' }}" />
                                                    <div class="error-message text-danger"
                                                        id="phone_number_error-{{ $index }}">
                                                    </div>
                                                </x-OnboardingInputDiv>


                                                <div class="col-md-3 col-sm-12 mt-2">
                                                    <label>Telephone</label>
                                                    <div class="input-group mb-3">
                                                        <x-NumberInput id="telephone-{{ $index }}"
                                                            name="telephone[]" req="" inputLabel=""
                                                            class="telephone" placeholder="Enter telephone number"
                                                            value="{{ $contact['telephone'] ?? '' }}" />
                                                        @if ($index === 0)
                                                            <button class="btn btn-primary add-contact" type="button"
                                                                id="add-contact-{{ $index }}"
                                                                data-counter="{{ $index }}">
                                                                <i class="bx bx-plus"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>



                                    {{-- <B class="primary-color">Insurance Details</B>
                                    <hr> --}}
                                    <!-- Steve start -->
                                    <div class="row mb-4">
                                        <!-- Ceding broker -->
                                        {{-- <div class="col-sm-3">
                                            <label class="form-label required"> Ceding Broker Flag</label>
                                            <div class="cover-card">
                                                <select class="form-inputs section select2" name="broker_flag"
                                                    id="broker_flag" required>
                                                    <option value="">Select Option</option>
                                                    <option value="N" {{ old('broker_flag', $prospProperties->
                                                        broker_flag) == 'N' ? 'selected' : '' }}>
                                                        No
                                                    </option>
                                                    <option value="Y" {{ old('broker_flag', $prospProperties->
                                                        broker_flag) == 'Y' ? 'selected' : '' }}>
                                                        Yes
                                                    </option>
                                                </select>
                                            </div>
                                        </div> --}}

                                        {{-- Agency --}}
                                        {{-- <div class="col-sm-3 brokercode_div">
                                            <label class="form-label required">Ceding Broker</label>
                                            <div class="cover-card">
                                                <select class="form-inputs section select2" name="brokercode"
                                                    id="brokercode" required>
                                                    <option value="">Choose Ceding Broker</option>
                                                    @foreach ($brokers as $broker)
                                                    <option value="{{ $broker->broker_code }}" {{
                                                        isset($prospProperties) && $broker->broker_code ==
                                                        $prospProperties->brokercode ? 'selected' : '' }}>
                                                        {{ $broker->broker_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> --}}

                                        {{-- <div class="col-sm-3">
                                            <label class="form-label required">Division</label>
                                            <div class="cover-card">
                                                <select class="form-inputs section select2" name="division"
                                                    id="division" required>
                                                    <option value="">Choose Division</option>
                                                    @foreach ($reinsdivisions as $trtDivision)
                                                    <option value="{{ $trtDivision->division_code }}" {{
                                                        isset($prospProperties) && $trtDivision->division_code ==
                                                        $prospProperties->divisions ? 'selected' : '' }}>
                                                        {{ $trtDivision->division_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> --}}

                                        {{-- <div class="col-sm-3">
                                            <label class="form-label required">Payment Method</label>
                                            <div class="cover-card">
                                                <select class="form-inputs section select2" name="pay_method"
                                                    id="pay_method" required>
                                                    <option value="">Choose Payment Method</option>
                                                    @foreach ($paymethods as $pay_method)
                                                    <option value="{{ $pay_method->pay_method_code }}" {{
                                                        isset($prospProperties) && $pay_method->pay_method_code ==
                                                        $prospProperties->pay_method ? 'selected' : '' }}
                                                        pay_method_desc="{{ $pay_method->short_description }}">
                                                        {{ $pay_method->pay_method_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> --}}

                                        {{-- <div class="col-sm-2">
                                            <label class="form-label required">No of Installments</label>
                                            <input type="number" class="form-control section color-blk"
                                                id="no_of_installments" name="no_of_installments" min="1" max="12"
                                                value="{{ old('no_of_installments', $prospProperties->no_of_installments) }}"
                                                required>
                                        </div> --}}

                                        {{-- Currency --}}
                                        {{-- <div class="col-sm-2">
                                            <label class="form-label required">Currency</label>
                                            <div class="cover-card">
                                                <select class="form-inputs select2" name="currency_code"
                                                    id="currency_code" required>
                                                    <option value="">Choose Currency</option>
                                                    @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->currency_code }}" {{ $currency->
                                                        currency_code == $prospProperties->currency_code ? 'selected' :
                                                        '' }}>
                                                        {{ $currency->currency_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-1">
                                            <label class="form-label required">E. Rate</label>
                                            <input type="text" name="today_currency" id="today_currency"
                                                class="form-control section color-blk"
                                                onkeyup="this.value=numberWithCommas(this.value)"
                                                change="this.value=numberWithCommas(this.value)"
                                                value="{{ old('today_currency', $prospProperties->today_currency) }}"
                                                @readonly(true) />
                                        </div> --}}

                                        {{--
                                        <!-- Premium Payment Terms -->
                                        <div class="col-sm-3">
                                            <label class="form-label required">Premium Payment Terms</label>
                                            <div class="cover-card">
                                                <select class="form-inputs select2" name="premium_payment_term"
                                                    id="premium_payment_term" required>
                                                    <option value="">Choose Payment Term</option>
                                                    @foreach ($premium_pay_terms as $premium_pay_term)
                                                    <option value="{{ $premium_pay_term->pay_term_code }}"
                                                        pay_term_desc="{{ $premium_pay_term->pay_term_desc }}" {{
                                                        $premium_pay_term->pay_term_code ==
                                                        $prospProperties->premium_payment_term ? 'selected' : '' }}>
                                                        {{ $premium_pay_term->pay_term_desc }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <div class="text-danger">{{ $errors->first('pay_method') }}</div>
                                            </div>
                                        </div> --}}

                                        {{-- Installments --}}
                                        <div class="col-sm-3 fac_instalment-section" id="fac_installments_box">
                                            <div class="col-md-12">
                                                <B class="primary-color">Insurance Details</B>

                                                {{-- <h6 class="mt-2">Installment plans</h6> --}}
                                                <input type="hidden" value="0" class="form-control section"
                                                    id="installment_total_amount" />
                                                <div id="fac-installments-section"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="form-group" id="fac_section">
                                        <div class="row row-cols-12">
                                            <div class="col-sm-3 class_group_div fac_section_div">
                                                <label class="form-label required">Division</label>
                                                <div class="cover-card">
                                                    <select class="form-inputs section select2" name="division"
                                                        id="division" required>
                                                        <option value="">Choose Division</option>
                                                        @foreach ($reinsdivisions as $trtDivision)
                                                            <option name="division"
                                                                value="{{ $trtDivision->division_code }}"
                                                                {{ isset($prospProperties) && $trtDivision->division_code == $prospProperties->divisions ? 'selected' : '' }}>
                                                                {{ $trtDivision->division_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Class Groups -->
                                            <div class="col-sm-3 class_group_div fac_section_div">
                                                <label class="form-label required">Class Group</label>
                                                <div class="cover-card">
                                                    <select class="form-inputs section select2 fac_section"
                                                        name="class_group" id="class_group" required>
                                                        <option value="">Choose Class Group</option>
                                                        @foreach ($classGroups as $classGroup)
                                                            <option value="{{ $classGroup->group_code }}"
                                                                {{ $classGroup->group_code == $prospProperties->class_group ? 'selected' : '' }}>
                                                                {{ $classGroup->group_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Class -->
                                            <div class="col-sm-3 fac_section_div">
                                                <label class="form-label required">Class Name</label>
                                                <div class="cover-card">
                                                    <select class="form-inputs section select2 fac_section"
                                                        name="classcode" id="classcode" required>
                                                        <option value="">-- Select Class Name--</option>
                                                        @foreach ($class as $cl)
                                                            @if ($cl->class_code == $prospProperties->classcode)
                                                                <option value="{{ $cl->class_code }}" selected>
                                                                    {{ $cl->class_name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <div class="text-danger">{{ $errors->first('classcode') }}</div>
                                                </div>
                                            </div>

                                            <!-- Insured Name -->
                                            <div class="col-sm-3 fac_section_div">
                                                <label class="form-label required">Insured Name</label>
                                                <div class="cover-card">
                                                    <input type="text" class="form-inputs section  fac_section"
                                                        name="insured_name" value="{{ $prospProperties->insured_name }}"
                                                        id="insured_name" required
                                                        oninput="this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());" />
                                                    {{-- <div id="insured_name_results" class="dropdown-menu"
                                                        style="display: none;"></div> --}}
                                                    <div class="error-message text-danger" id="insured_name_error"></div>
                                                    {{-- <select class="form-inputs section select2 fac_section"
                                                        name="insured_name" id="insured_name" required>
                                                        <option selected value="">Choose Insured</option>
                                                        @foreach ($insured as $insured_names)
                                                        <option value="{{ $insured_names->name }}">
                                                            {{ $insured_names->name }}</option>
                                                        @endforeach
                                                    </select> --}}
                                                </div>
                                            </div>
                                            <x-OnboardingInputDiv id="industry_div">
                                                <x-SearchableSelect name="industry" id="industry" req="required"
                                                    inputLabel="Industry">
                                                    <option value="">Select industry</option>
                                                    @foreach ($industries as $industry)
                                                        <option value="{{ $industry->name }}"
                                                            {{ isset($prospProperties) && $prospProperties->industry == $industry->name ? 'selected' : '' }}>
                                                            {{ $industry->name }}
                                                        </option>
                                                    @endforeach
                                                </x-SearchableSelect>
                                            </x-OnboardingInputDiv>



                                            <!-- Expected Closure Date -->
                                            <div class="col-sm-3 fac_section_div">
                                                <label class="form-label required">Expected Closure Date</label>
                                                <input type="date" class="form-inputs fac_section"
                                                    id="fac_date_offered" name="fac_date_offered"
                                                    value="{{ old('fac_date_offered', $prospProperties->fac_date_offered) }}"
                                                    required />
                                            </div>
                                        </div>

                                        <div class="row row-cols-12">
                                            <div class="col-sm-3 fac_section_div">
                                                <label class="form-label required">Currency</label>
                                                <div class="cover-card">
                                                    <select class="form-inputs select2" name="currency_code"
                                                        id="currency_code" required>
                                                        <option value="">Choose Currency</option>
                                                        @foreach ($currencies as $currency)
                                                            <option value="{{ $currency->currency_code }}"
                                                                {{ $currency->currency_code == $prospProperties->currency_code ? 'selected' : '' }}>
                                                                {{ $currency->currency_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <label class="form-label required">E. Rate</label>
                                                <input type="text" name="today_currency" id="today_currency"
                                                    class="form-control section color-blk"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    change="this.value=numberWithCommas(this.value)" {{-- value="{{ number_format(old('today_currency', $prospProperties->today_currency ?? 0), 2) }}" /> --}}
                                                    value="{{ $prospProperties->today_currency ?? 0 }}" />

                                            </div>
                                            <!-- Sum Insured Type -->
                                            <div class="col-sm-3 fac_section_div">
                                                <label class="form-label required">Sum Insured Type</label>
                                                <div class="cover-card">
                                                    <select class="form-inputs section select2 fac_section"
                                                        name="sum_insured_type" id="sum_insured_type" required>
                                                        <option value="">Choose Sum Insured Type</option>
                                                        @foreach ($types_of_sum_insured as $type_of_sum_insured)
                                                            <option value="{{ $type_of_sum_insured->sum_insured_code }}"
                                                                {{ $type_of_sum_insured->sum_insured_code == $prospProperties->sum_insured_type ? 'selected' : '' }}>
                                                                {{ $type_of_sum_insured->sum_insured_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">100% SUM INSURED<span
                                                        id="sum_insured_label"></span></label>
                                                <input type="text" class="form-inputs fac_section"
                                                    id="total_sum_insured" name="total_sum_insured"
                                                    value="{{ number_format(old('total_sum_insured', $prospProperties->total_sum_insured ?? 0), 2) }}"
                                                    onkeyup="this.value=numberWithCommas(this.value)" required>
                                            </div>
                                            <div class="col-sm-3">
                                                <label for="apply_eml">Apply EML</label>
                                                <div class="cover-card">
                                                    <select name="apply_eml" class="form-inputs section select2"
                                                        id="apply_eml" required>
                                                        <option value="">--select option-</option>
                                                        <option value="Y"
                                                            {{ $prospProperties->apply_eml == 'Y' ? 'selected' : '' }}>
                                                            Yes
                                                        </option>
                                                        <option value="N"
                                                            {{ $prospProperties->apply_eml == 'N' ? 'selected' : '' }}>
                                                            No
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-3 eml-div">
                                                <label class="form-label">EML Rate</label>
                                                <div class="cover-card">
                                                    <input type="number" class="form-inputs fac_section" id="eml_rate"
                                                        name="eml_rate"
                                                        value="{{ old('eml_rate', $prospProperties->eml_rate) }}"
                                                        min="0" max="100" required>
                                                </div>
                                            </div>

                                            <div class="col-sm-3 eml-div">
                                                <label class="form-label">EML Amount</label>
                                                <div class="cover-card">
                                                    <input type="text" class="form-inputs fac_section amount"
                                                        id="eml_amt" name="eml_amt"
                                                        value="{{ number_format(old('eml_amt', $prospProperties->eml_amt ?? 0), 2) }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="form-label">Effective Sum Insured</label>
                                                <input type="text" class="form-inputs fac_section amount"
                                                    id="effective_sum_insured" name="effective_sum_insured"
                                                    value="{{ old('effective_sum_insured', $prospProperties->effective_sum_insured) }}"
                                                    required readonly>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="form-label">Risk Details</label>
                                                <textarea class="form-inputs section fac_section resize-none" id="risk_details" name="risk_details" required>{{ old('risk_details', $prospProperties->risk_details) }}</textarea>
                                            </div>
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Cedant Premium</label>
                                                <input type="text" class="form-inputs fac_section" id="cede_premium"
                                                    name="cede_premium"
                                                    value="{{ number_format(old('cede_premium', $prospProperties->cede_premium ?? 0), 2) }}"
                                                    onkeyup="this.value=numberWithCommas(this.value)" required>
                                            </div>
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Reinsurer Premium</label>
                                                <input type="text" class="form-inputs fac_section" id="rein_premium"
                                                    name="rein_premium"
                                                    value={{ number_format(old('rein_premium', $prospProperties->rein_premium ?? 0), 2) }}
                                                    onkeyup="this.value=numberWithCommas(this.value)" required>
                                            </div>
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Written Share(%)</label>
                                                <input type="number" class="form-inputs fac_section"
                                                    id="fac_share_offered" name="fac_share_offered"
                                                    value="{{ $prospProperties->fac_share_offered }}" max="100"
                                                    min="0" required>
                                            </div>
                                        </div>
                                        <div class="row row-cols-12">
                                            <!-- Cedant Comm Rate -->
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Cedant Comm rate(%)</label>
                                                <input type="text" class="form-inputs fac_section"
                                                    aria-label="comm_rate" id="comm_rate" name="comm_rate"
                                                    value="{{ old('comm_rate', $prospProperties->comm_rate) }}"
                                                    max="100" min="0" required>
                                            </div>

                                            <!-- Cedant Comm Amount -->
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Cedant Comm Amount</label>
                                                <input type="text" class="form-inputs fac_section"
                                                    aria-label="comm_amt" id="comm_amt" name="comm_amt"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    value="{{ number_format(old('comm_amt', $prospProperties->comm_amt ?? 0), 2) }}"
                                                    max="100" min="0" required>
                                            </div>

                                            <!-- Reinsurer Comm Type -->
                                            <div class="col-xl-3 fac_section_div reins_comm_type_div">
                                                <label class="form-label">Reinsurer Comm Type</label>
                                                <div class="cover-card">
                                                    <select class="form-inputs section select2 fac_section reins_comm_type"
                                                        name="reins_comm_type" id="reins_comm_type" required>
                                                        <option value="">Choose Reinsurer Comm Type</option>
                                                        <option value="R"
                                                            {{ $prospProperties->reins_comm_type == 'R' ? 'selected' : '' }}>
                                                            Rate
                                                        </option>
                                                        <option value="A"
                                                            {{ $prospProperties->reins_comm_type == 'A' ? 'selected' : '' }}>
                                                            Amount
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Reinsurer Comm Rate -->
                                            <div class="col-xl-3 fac_section_div reins_comm_rate_div">
                                                <label class="form-label">Reinsurer Comm rate(%)</label>
                                                <input type="text" class="form-inputs fac_section reins_comm_rate"
                                                    aria-label="reins_comm_rate" id="reins_comm_rate"
                                                    name="reins_comm_rate"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    value="{{ old('reins_comm_rate', $prospProperties->reins_comm_rate ?? '') }}"
                                                    required disabled>
                                            </div>

                                            <!-- Reinsurer Comm Amount -->
                                            <div class="col-xl-3 fac_section_div reins_comm_amt_div">
                                                <label class="form-label">Reinsurer Comm Amount</label>
                                                <input type="text" class="form-inputs fac_section reins_comm_amt"
                                                    aria-label="reins_comm_amt" id="reins_comm_amt" name="reins_comm_amt"
                                                    onkeyup="this.value=numberWithCommas(this.value)"
                                                    onchange="this.value=numberWithCommas(this.value)"
                                                    value="{{ number_format(old('reins_comm_amt', $prospProperties->reins_comm_amt ?? 0), 2) }}"
                                                    required>
                                            </div>

                                            <!-- Brokerage Commission Type -->
                                            <div class="col-xl-3 fac_section_div">
                                                <label class="form-label">Brokerage Commission Type</label>
                                                <div class="cover-card">
                                                    <select name="brokerage_comm_type" id="brokerage_comm_type"
                                                        class="form-inputs section select2">
                                                        <option value="">--Select basis--</option>
                                                        <option value="R"
                                                            {{ old('brokerage_comm_type', $prospProperties->brokerage_comm_type) == 'R' ? 'selected' : '' }}>
                                                            Rate (<small><i>reinsurer rate - cedant
                                                                    rate</i></small>)
                                                        </option>
                                                        <option value="A"
                                                            {{ old('brokerage_comm_type', $prospProperties->brokerage_comm_type) == 'A' ? 'selected' : '' }}>
                                                            Quoted Amount
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Brokerage Commission Amount -->
                                            <div class="col-xl-3 fac_section_div brokerage_comm_amt_div">
                                                <label class="form-label">Brokerage Amount</label>
                                                <input type="text" class="form-inputs fac_section amount"
                                                    id="brokerage_comm_amt" name="brokerage_comm_amt"
                                                    value="{{ old('brokerage_comm_amt', isset($prospProperties->brokerage_comm_amt) ? number_format($prospProperties->brokerage_comm_amt, 2) : '') }}" />
                                            </div>

                                            <!-- Brokerage Commission Rate -->
                                            <div class="col-xl-3 fac_section_div brokerage_comm_rate_div">
                                                <label class="form-label" id="brokerage_comm_rate_label">Brokerage
                                                    Rate</label>
                                                <input type="text" class="form-inputs fac_section amount"
                                                    id="brokerage_comm_rate" name="brokerage_comm_rate"
                                                    value="{{ old('brokerage_comm_rate', $prospProperties->brokerage_comm_rate ?? '') }}">
                                            </div>
                                            <div class="col-xl-3 fac_section_div brokerage_comm_rate_amt_div">
                                                <label class="form-label" id="brokerage_comm_rate_amount_label">Brokerage
                                                    Rate Amount</label>
                                                <input type="text" class="form-inputs fac_section amount"
                                                    id="brokerage_comm_rate_amt" name="brokerage_comm_amt"
                                                    value="{{ old('brokerage_comm_rate_amt', $prospProperties->brokerage_comm_amt ?? '') }}"
                                                    readonly>
                                            </div>

                                            <!-- Hidden VAT Charged -->
                                            <input type="hidden" class="vat_charged fac_section" id="vat_charged"
                                                name="vat_charged" value="0">

                                        </div>
                                    </div>
                                    <!-- Treaty Group Section -->
                                    <div class="form-group treaty_grp" id="treaty_grp">
                                        <div id="trt_common">
                                            <div class="row row-cols-12">
                                                <!-- Treaty Type -->
                                                <div class="col-sm-3 treatytype_div trt_common_div">
                                                    <label class="form-label required">Treaty Type</label>
                                                    <select class="form-inputs section treatytype trt_common"
                                                        name="treatytype" id="treatytype" required>
                                                        <option value="">--Select Treaty Type--</option>
                                                        <option value="Proportional"
                                                            {{ old('treatytype', $prospProperties->treatytype ?? '') == 'Proportional' ? 'selected' : '' }}>
                                                            Proportional</option>
                                                        <option value="Non-Proportional"
                                                            {{ old('treatytype', $prospProperties->treatytype ?? '') == 'Non-Proportional' ? 'selected' : '' }}>
                                                            Non-Proportional</option>
                                                    </select>
                                                </div>

                                                <!-- Date Offered -->
                                                <div class="col-sm-3 date_offered_div trt_common_div">
                                                    <label class="form-label required">Date Offered</label>
                                                    <input type="date" class="form-inputs date_offered trt_common"
                                                        aria-label="date_offered" id="date_offered" name="date_offered"
                                                        required>
                                                </div>

                                                <!-- Share Offered -->
                                                <div class="col-sm-2 share_offered_div trt_common_div">
                                                    <label class="required">Share Offered(%)</label>
                                                    <input type="text" class="form-inputs share_offered trt_common"
                                                        aria-label="share_offered" id="share_offered"
                                                        name="share_offered" required>
                                                </div>

                                                <!-- Premium Tax Rate -->
                                                <div class="col-sm-2 prem_tax_rate_div trt_common_div">
                                                    <label class="required">Premium Tax Rate (%)</label>
                                                    <input type="number" class="form-inputs prem_tax_rate trt_common"
                                                        aria-label="prem_tax_rate" id="prem_tax_rate"
                                                        name="prem_tax_rate" required>
                                                </div>

                                                <!-- Reinsurance Tax Rate -->
                                                <div class="col-sm-2 ri_tax_rate_div prem_tax_rate_div trt_common_div">
                                                    <label class="required">Reinsurance Tax Rate (%)</label>
                                                    <input type="number" class="form-inputs ri_tax_rate trt_common"
                                                        aria-label="ri_tax_rate" id="ri_tax_rate" name="ri_tax_rate"
                                                        min="0" max="100" required>
                                                </div>

                                                <!-- Brokerage Commission Rate -->
                                                <div class="col-sm-2 brokerage_comm_rate_div trt_common_div">
                                                    <label class="required">Brokerage Commission Rate
                                                        (%)</label>
                                                    <input type="number"
                                                        class="form-inputs brokerage_comm_rate trt_common"
                                                        aria-label="brokerage_comm_rate" id="brokerage_comm_rate"
                                                        name="brokerage_comm_rate" min="0" max="100"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Reinsurers per Treaty -->
                                                <div class="col-sm-2 reinsurer_per_treaty_div tpr_section_div">
                                                    <label class="required">Reinsurers are captured per
                                                        treaty?</label>
                                                    <select class="form-inputs reinsurer_per_treaty tpr_section"
                                                        name="reinsurer_per_treaty" id="reinsurer_per_treaty" required>
                                                        <option value="">Select Option</option>
                                                        <option value="N" selected>No</option>
                                                        <option value="Y">Yes</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Treaty Sections -->
                                        <div class="form-group mt-3">
                                            <div id="tpr_section">
                                                <div class="row">
                                                    <!-- Portfolio Premium Rate -->
                                                    <div class="col-sm-3 port_prem_rate_div tpr_section_div">
                                                        <label class="required">Portfolio Premium
                                                            Rate(%)</label>
                                                        <input type="number"
                                                            class="form-inputs port_prem_rate tpr_section"
                                                            aria-label="port_prem_rate" id="port_prem_rate"
                                                            name="port_prem_rate" data-counter="0" max="100"
                                                            min="0" required>
                                                    </div>

                                                    <!-- Portfolio Loss Rate -->
                                                    <div class="col-sm-3 port_loss_rate_div tpr_section_div">
                                                        <label class="required">Portfolio Loss Rate(%)</label>
                                                        <input type="number"
                                                            class="form-inputs port_loss_rate tpr_section"
                                                            aria-label="port_loss_rate" id="port_loss_rate"
                                                            name="port_loss_rate" data-counter="0" max="100"
                                                            min="0" required>
                                                    </div>

                                                    <!-- Profit Commission Rate -->
                                                    <div class="col-sm-3 profit_comm_rate_div tpr_section_div">
                                                        <label class="form-label required">Profit Comm
                                                            Rate(%)</label>
                                                        <input type="number"
                                                            class="form-inputs profit_comm_rate tpr_section"
                                                            aria-label="profit_comm_rate" id="profit_comm_rate"
                                                            name="profit_comm_rate" data-counter="0" max="100"
                                                            min="0" required>
                                                    </div>

                                                    <!-- Management Expense Rate -->
                                                    <div class="col-sm-3 mgnt_exp_rate_div tpr_section_div">
                                                        <label class="required">Management Expense
                                                            Rate(%)</label>
                                                        <input type="number"
                                                            class="form-inputs mgnt_exp_rate tpr_section"
                                                            aria-label="mgnt_exp_rate" id="mgnt_exp_rate"
                                                            name="mgnt_exp_rate" data-counter="0" max="100"
                                                            min="0" required>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <!-- Deficit Carry Forward (years) -->
                                                    <div class="col-sm-3 deficit_yrs_div tpr_section_div">
                                                        <label class="required_div deficit_yrs">Deficit C/F
                                                            (yrs)</label>
                                                        <input type="number" class="form-inputs tpr_section"
                                                            aria-label="" class="deficit_yrs" id="deficit_yrs"
                                                            name="deficit_yrs" data-counter="0" min="0"
                                                            max="10" required>
                                                    </div>
                                                </div>

                                                <div class="row mb-2 tpr_section_div">
                                                    <div class="col-md-4">
                                                        <button type="button" class="btn btn-primary tpr_section"
                                                            id="add_rein_class">Add
                                                            Class</button>
                                                    </div>
                                                </div>

                                                <div class="row reinclass-section" id="reinclass-section-0"
                                                    data-counter="0">
                                                    <h6 class="section-title tpr_section_div">Section A</h6>
                                                    <div class="row mb-2">
                                                        <!-- Reinsurance Main Class -->
                                                        <div class="col-sm-3 treaty_reinclass tpr_section_div">
                                                            <label class="form-label required">Reinsurance
                                                                Class</label>
                                                            <select
                                                                class="form-inputs select2 treaty_reinclass tpr_section"
                                                                name="treaty_reinclass[]" id="treaty_reinclass-0"
                                                                data-counter="0" required>
                                                                <option value="">Choose Reinsurance Class
                                                                </option>
                                                                @foreach ($reinsclasses as $reinsclass)
                                                                    <option value="{{ $reinsclass->class_code }}">
                                                                        {{ $reinsclass->class_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="row quota_header_div tpr_section_div"
                                                        style="display: none">
                                                        <h6>Quota Share</h6>
                                                    </div>
                                                    <div class="row">
                                                        <!-- quota limit -->
                                                        <div class="col-sm-2 quota_share_total_limit_div tpr_section_div"
                                                            id="quota_share_total_limit_div">
                                                            <label class="form-label required">100% Quota Share
                                                                Limit</label>
                                                            <input type="text"
                                                                class="form-inputs quota_share_total_limit tpr_section"
                                                                aria-label="quota_share_total_limit"
                                                                id="quota_share_total_limit-0" data-counter="0"
                                                                name="quota_share_total_limit[]"
                                                                onkeyup="this.value=numberWithCommas(this.value)" required>
                                                        </div>

                                                        <!-- Retention (%) -->
                                                        <div class="col-sm-1 retention_per_div tpr_section_div"
                                                            id="retention_per_div">
                                                            <label class="form-label required">Retention(%)</label>
                                                            <input type="number"
                                                                class="form-inputs retention_per tpr_section"
                                                                aria-label="retention_per" id="retention_per-0"
                                                                data-counter="0" name="retention_per[]" min="0"
                                                                max="100" required>
                                                        </div>

                                                        <!-- Retention Amount -->
                                                        <div class="col-sm-3 quota_retention_amt_div tpr_section_div"
                                                            id="quota_retention_amt_div" style="display: none">
                                                            <label class="form-label required">Retention
                                                                Amount</label>
                                                            <input type="text"
                                                                class="form-inputs quota_retention_amt tpr_section"
                                                                aria-label="quota_retention_amt"
                                                                id="quota_retention_amt-0" name="quota_retention_amt[]"
                                                                data-counter="0"
                                                                onkeyup="this.value=numberWithCommas(this.value)" required
                                                                disabled>
                                                        </div>

                                                        <!-- Treaty share(%) -->
                                                        <div class="col-sm-2 treaty_reice_div tpr_section_div"
                                                            id="treaty_reice_div">
                                                            <label class="form-label required">Treaty
                                                                (%)</label>
                                                            <input type="number"
                                                                class="form-inputs treaty_reice tpr_section"
                                                                aria-label="treaty_reice" id="treaty_reice-0"
                                                                name="treaty_reice[]" data-counter="0" min="0"
                                                                max="100" required>
                                                        </div>

                                                        <!-- Treaty Limit -->
                                                        <div class="col-sm-3 quota_treaty_limit_div tpr_section_div"
                                                            id="quota_treaty_limit_div" style="display: none">
                                                            <label class="form-label required">Treaty
                                                                Limit</label>
                                                            <input type="text"
                                                                class="form-inputs quota_treaty_limit tpr_section"
                                                                aria-label="quota_treaty_limit" id="quota_treaty_limit-0"
                                                                name="quota_treaty_limit[]" data-counter="0"
                                                                onkeyup="this.value=numberWithCommas(this.value)" required
                                                                disabled>
                                                        </div>
                                                    </div>

                                                    <div class="row surp_header_div tpr_section_div" style="display: none"
                                                        data-counter="0">
                                                        <h6> Surplus </h6>
                                                    </div>

                                                    <div class="row">
                                                        <!-- Retention Amount -->
                                                        <div class="col-sm-3 surp_retention_amt_div tpr_section_div"
                                                            id="surp_retention_amt_div " style="display: none">
                                                            <label class="form-label required">Retention
                                                                Amount</label>
                                                            <input type="text"
                                                                class="form-inputs surp_retention_amt tpr_section"
                                                                aria-label="surp_retention_amt" id="surp_retention_amt-0"
                                                                name="surp_retention_amt[]" data-counter="0"
                                                                onkeyup="this.value=numberWithCommas(this.value)" required
                                                                disabled>
                                                        </div>

                                                        <!-- No of Lines -->
                                                        <div class="col-sm-3 no_of_lines_div tpr_section_div"
                                                            id="no_of_lines_div">
                                                            <label class="form-label required">No of
                                                                Lines</label>
                                                            <input type="number"
                                                                class="form-inputs no_of_lines tpr_section"
                                                                aria-label="no_of_lines" id="no_of_lines-0"
                                                                data-counter="0" name="no_of_lines[]" required>
                                                        </div>

                                                        <!-- Treaty Limit -->
                                                        <div class="col-sm-3 surp_treaty_limit_div tpr_section_div"
                                                            id="surp_treaty_limit_div" style="display: none">
                                                            <label class="form-label required">Treaty
                                                                Limit</label>
                                                            <input type="text"
                                                                class="form-inputs surp_treaty_limit tpr_section"
                                                                aria-label="surp_treaty_limit" class="surp_treaty_limit"
                                                                id="surp_treaty_limit-0" name="surp_treaty_limit[]"
                                                                data-counter="0"
                                                                onkeyup="this.value=numberWithCommas(this.value)" required
                                                                disabled>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <!-- Estimated Income -->
                                                        <div class="col-sm-3 estimated_income_div tpr_section_div">
                                                            <label class="required ">Estimated Income</label>
                                                            <input type="text"
                                                                class="form-inputs estimated_income tpr_section"
                                                                aria-label="estimated_income" class=""
                                                                id="estimated_income-0" name="estimated_income[]"
                                                                data-counter="0"
                                                                onkeyup="this.value=numberWithCommas(this.value)" required>
                                                        </div>

                                                        <!-- Cash Loss Limit -->
                                                        <div class="col-sm-3 cashloss_limit_div tpr_section_div">
                                                            <label class="required ">Cash Loss Limit</label>
                                                            <input type="text"
                                                                class="form-inputs cashloss_limit tpr_section"
                                                                aria-label="cashloss_limit" id="cashloss_limit-0"
                                                                name="cashloss_limit[]" data-counter="0"
                                                                onkeyup="this.value=numberWithCommas(this.value)" required>
                                                        </div>
                                                    </div>

                                                    <div class="mt-2 comm-section tpr_section_div" id="comm-section-0">
                                                        <h7> Commission Section </h7>
                                                        <div class="row comm-sections tpr_section_div"
                                                            id="comm-section-0-0" data-class-counter="0"
                                                            data-counter="0">
                                                            <!-- Treaty -->
                                                            <div class="col-sm-3 prem_type_treaty_div tpr_section_div">
                                                                <label class="required ">Treaty</label>
                                                                <select
                                                                    class="form-inputs select2 prem_type_treaty tpr_section"
                                                                    name="prem_type_treaty[]" data-reinclass=""
                                                                    data-class-counter="0" data-counter="0"
                                                                    id="prem_type_treaty-0-0" required>
                                                                </select>
                                                            </div>

                                                            <!-- Reinsurance Premium Types -->
                                                            <div class="col-sm-3 prem_type_code_div tpr_section_div">
                                                                <label class="required ">Premium Type</label>
                                                                <input type="hidden"
                                                                    class="form-inputs prem_type_reinclass tpr_section"
                                                                    id="prem_type_reinclass-0-0"
                                                                    name="prem_type_reinclass[]" data-counter="0">
                                                                <select
                                                                    class="form-inputs select2 prem_type_code tpr_section"
                                                                    name="prem_type_code[]" data-counter="0"
                                                                    id="prem_type_code-0-0" data-reinclass=""
                                                                    data-class-counter="0" data-treaty="" required>
                                                                </select>
                                                            </div>

                                                            <div class="col-sm-3 prem_type_comm_rate_div tpr_section_div">
                                                                <label class="required ">Commission(%)</label>
                                                                <div class="input-group mb-3">
                                                                    <input type="text"
                                                                        class="form-control prem_type_comm_rate tpr_section"
                                                                        name="prem_type_comm_rate[]" data-counter="0"
                                                                        id="prem_type_comm_rate-0-0" required>
                                                                    <button class="btn btn-primary add-comm-section"
                                                                        type="button" id="add-comm-section-0-0"
                                                                        data-counter="0">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="tnp_section">
                                            <div class="row">
                                                <!-- Reinsurance Main Class -->
                                                <div class="col-sm-3 reinclass_code tnp_section_div">
                                                    <label class="form-label required">Reinsurance
                                                        Class</label>
                                                    <select class="form-inputs select2 reinclass_code tnp_section"
                                                        name="reinclass_code[]" id="tnp_reinclass_code" multiple required>
                                                        @foreach ($reinsclasses as $reinsclass)
                                                            <option value="{{ $reinsclass->class_code }}">
                                                                {{ $reinsclass->class_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Burning Cost (B) / Flat Rate (F) -->
                                                <div class="col-sm-3 method tnp_section_div">
                                                    <label class="required method ">Burning Cost (B) /
                                                        Flat Rate
                                                        (F)</label>
                                                    <select name="method" id="method"
                                                        class="form-inputs method tnp_section">
                                                        <option value="">-- Select Method --</option>
                                                        <option value="B">Burning Cost (B)</option>
                                                        <option value="F">Flat Rate (F)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group tnp_section_div" id="layer-section">
                                            <h4> Layers Section </h4>
                                            <button class="btn btn-primary" type="button" id="add-layer-section">
                                                <i class="fa fa-plus"></i>Add Layer
                                            </button>
                                            <h6> Layer: 1 </h6>
                                            <div class="layer-sections" id="layer-section-0" data-counter="0">
                                                <div class="row">
                                                    <div class="col-sm-2 limit_per_reinclass_div">
                                                        <label class="form-label required">Capture
                                                            Limits per
                                                            Class?</label>
                                                        <select class="form-inputs limit_per_reinclass"
                                                            name="limit_per_reinclass" id="limit_per_reinclass-0-0"
                                                            required>
                                                            <option value="N"
                                                                {{ old('limit_per_reinclass', $prospProperties->limit_per_reinclass) == 'N' ? 'selected' : '' }}>
                                                                No</option>
                                                            <option value="Y"
                                                                {{ old('limit_per_reinclass', $prospProperties->limit_per_reinclass) == 'Y' ? 'selected' : '' }}>
                                                                Yes</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                        <label class="form-label required">Reinclass</label>
                                                        <input type="hidden" class="form-control layer_no"
                                                            id="layer_no-0-0" name="layer_no[]" value="1" readonly>
                                                        <input type="hidden" class="form-control nonprop_reinclass"
                                                            id="nonprop_reinclass-0-0" name="nonprop_reinclass[]"
                                                            value="ALL" readonly>
                                                        <input type="text" class="form-control nonprop_reinclass_desc"
                                                            id="nonprop_reinclass_desc-0-0"
                                                            name="nonprop_reinclass_desc[]"
                                                            value="{{ old('nonprop_reinclass_desc', $prospProperties->nonprop_reinclass_desc) }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-sm-2 indemnity_treaty_limit_div">
                                                        <label class="form-label required">Limit</label>
                                                        <input type="text" class="form-inputs indemnity_treaty_limit"
                                                            name="indemnity_treaty_limit[]"
                                                            value="{{ old('indemnity_treaty_limit', $prospProperties->indemnity_treaty_limit) }}"
                                                            onkeyup="this.value=numberWithCommas(this.value)" required>
                                                    </div>
                                                    <div class="col-sm-2 underlying_limit_div">
                                                        <label class="form-label required">Deductible
                                                            Amount</label>
                                                        <input type="text" class="form-inputs underlying_limit"
                                                            name="underlying_limit[]"
                                                            value="{{ old('underlying_limit', $prospProperties->underlying_limit) }}"
                                                            onkeyup="this.value=numberWithCommas(this.value)" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Engagement Details -->
                                    <B class="primary-color">Engagement Details</B>
                                    <hr>
                                    <div class="row">
                                        <x-OnboardingInputDiv>
                                            <x-SearchableSelect name="engage_type" id="engage_type" req="required"
                                                inputLabel="Nature of engagement">
                                                <option value="">Select engagement type </option>
                                                @foreach ($engage_types as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ old('engage_type', $prospProperties->engage_type) == $type->id ? 'selected' : '' }}>
                                                        {{ firstUpper($type->name) }}
                                                    </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="lead_owner_div">
                                            <x-SearchableSelect name="lead_owner" id="lead_owner" req="required"
                                                inputLabel="Prospect Lead">
                                                <option value="">Select prospect Lead
                                                </option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('lead_owner', (int) $prospProperties->lead_owner) == $user->id ? 'selected' : '' }}>
                                                        {{ firstUpper($user->name) }}
                                                    </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="date_effective_div">
                                            <x-DateInput name="effective_date" id="effective_date"
                                                placeholder="Enter cover start date" inputLabel="Cover Start Date"
                                                req="required"
                                                value="{{ old('effective_date', $prospProperties->effective_date) }}" />
                                        </x-OnboardingInputDiv>

                                        <x-OnboardingInputDiv id="date_closing_div">
                                            <x-DateInput name="closing_date" id="closing_date"
                                                placeholder="Enter bid closing date" inputLabel="Cover End Date"
                                                req="required" value="{{ $prospProperties->closing_date }}" />
                                        </x-OnboardingInputDiv>
                                    </div>
                                </div>
                                <div>
                                    <hr>
                                </div>
                                <div class="mt-3" style="float:right">
                                    <button type="button" name="previous" class="btn btn-outline-danger"
                                        id="cancel">
                                        <span class="fa fa-times"></span> Cancel
                                    </button>

                                    <button type="submit" id="submit_edit" class="btn btn-outline-success">
                                        <span class="fa fa-save"></span>

                                        Edit Details
                                    </button>

                                    @if (!is_null($prospect))
                                        <button type="button" class="btn btn-outline-primary" data-prospect=""
                                            id="sales_mngt">
                                            <span class="fa fa-arrow-right"></span> Submit to Sales
                                        </button>
                                        <button style="display:none" type="button" class="btn btn-outline-primary"
                                            data-prospect="" id="process_pq">
                                            <span class="fa fa-arrow-right"></span> Update PQ Status
                                        </button>
                                    @endif
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- end --}}


    <!-- Modal -->
    <div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white"><span id="ed_status_name">PQ STATUS
                            UPDATE</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('update.opp.status') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="opp_id" value="{{ $prospect }}">
                        <input type="hidden" name="stage_cycle" value="0">
                        <input type="hidden" name="pq" value="Y">
                        <div class="row ">

                            <div class="col-12" id="stage_div">
                                <x-SearchableSelect name="stage_cycle" id="stage_cycle" req="required"
                                    inputLabel="Cycle Stage">
                                    <option value="">Select stage</option>
                                    <option value="P">Proposal</option>
                                    <option value="W">Won</option>
                                    <option value="L">Lost</option>
                                </x-SearchableSelect>
                            </div>

                            <div class="row mt-3" id="locked_uws">
                                <div class="form-group">
                                    <label for="roles" class="pb-2">Locked
                                        underwriter(s)</label>
                                    <select type="text" class="select2Multi select2" id="underwriters"
                                        name="underwriters[]" multiple="multiple" placeholder="Select roles">
                                        @foreach ($underwriters as $user)
                                            <option value="{{ $user->company_id }}">
                                                {{ $user->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <x-TextArea name="remarks" id="remarks" req="required" inputLabel="Remarks">
                                </x-TextArea>
                            </div>

                            <div id="file_details" class="col-12">
                                <div class="row mt-3">
                                    <div class="col-12 m-0">
                                        <small><b style="color: #E1251B"><i>Documents</i></b></small>

                                    </div>

                                    <div style="float:right" class="mt-3">
                                        <button type="button" name="previous" class="btn btn-outline-danger"
                                            id="cancel"><span class="fa fa-times"></span> Cancel</button>

                                        <button type="submit" id="submit" class="btn btn-outline-success"><span
                                                class="fa fa-save"></span>
                                            {{-- @if ($prospProperties->prequalification === 'N') --}}
                                            Edit Details
                                            {{-- @endif --}}
                                        </button>

                                        <button type="button" class="btn btn-outline-info"
                                            onclick="checkSendToSales($prospProperties.opportunity_id)" data-prospect=""
                                            id="sales_mngt"><span
                                                class="fa
                                        fa-arrow-right"></span>
                                            Submit
                                            Sales</button>
                                    </div>

                                    <div class="col-5">
                                        <label for="document_file">File</label>
                                        <div class="input-group">
                                            <input type="file" name="document_file[]" id="document_file0"
                                                class="form-control document_file" />
                                            <button id="addDoc" class="btn btn-primary" type="button"><i
                                                    class="fa fa-plus"></i></button>
                                        </div>
                                    </div>

                                    <div class="col-1" style="margin-top: 30px">
                                        <i class="fa fa-eye preview" id="preview0"> </i>
                                    </div>
                                </div>
                            </div>

                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>

                </div>
                </form>
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
                        <form action="{{ route('update.opp.status') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="opp_id" value="{{ $prospect }}/">
                            <input type="hidden" name="stage_cycle" value="0" />
                            <input type="hidden" name="pq" value="Y" />
                            <div class="row ">

                                <div class="col-12" id="stage_div">
                                    <x-SearchableSelect name="stage_cycle" id="stage_cycle" req="required"
                                        inputLabel="Cycle Stage">
                                        <option value="">Select stage</option>
                                        <option value="P">Proposal</option>
                                        <option value="W">Won</option>
                                        <option value="L">Lost</option>
                                    </x-SearchableSelect>
                                </div>

                                <div class="row mt-3" id="locked_uws">
                                    <div class="form-group">
                                        <label for="roles" class="pb-2">Locked underwriter(s)</label>
                                        <select type="text" class="select2Multi select2" id="underwriters"
                                            name="underwriters[]" multiple="multiple" placeholder="Select roles">
                                            @foreach ($underwriters as $user)
                                                <option value="{{ $user->company_id }}">
                                                    {{ $user->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <x-TextArea name="remarks" id="remarks" req="required" inputLabel="Remarks">
                                    </x-TextArea>
                                </div>

                                <div id="file_details" class="col-12">
                                    <div class="row mt-3">
                                        <div class="col-12 m-0">
                                            <small><b style="color: #E1251B"><i>Documents</i></b></small>
                                            <hr>
                                        </div>

                                        <div class="col-6">
                                            <x-Input id="document_name0" name="document_name[]" req=""
                                                inputLabel="Document Title" placeholder="Enter document title"
                                                oninput='this.value=this.value.toUpperCase();' />
                                        </div>

                                        <div class="col-5">
                                            <label for="document_file">File</label>
                                            <div class="input-group">
                                                <input type="file" name="document_file[]" id="document_file0"
                                                    class="form-control document_file" />
                                                <button id="addDoc" class="btn btn-primary" type="button"><i
                                                        class="fa fa-plus"></i></button>
                                            </div>
                                        </div>

                                        <div class="col-1" style="margin-top: 30px">
                                            <i class="fa fa-eye preview" id="preview0"> </i>
                                        </div>
                                    </div>
                                </div>

                            </div>

                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>

                    </div>
                    </form>
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
                                <object id="doc_view" data="base64" height="900" width="100%"
                                    style="border: solid 1px #DDD;"></object>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {


            $('#tnp_section').hide();
            $('#tpr_section').hide();
            $('#fac_section').hide();
            $('#trt_common').hide();
            $('#treaty_grp').hide();
            $('#eml_rate').hide();
            $('#eml_amt').hide();
            $('.eml-div').hide();
            $('.brokerage_comm_amt_div').hide();
            $('.brokerage_comm_rate_div').hide();
            $('select#type_of_bus').trigger('change');

            const derro = @json($prospProperties);
            console.log(derro);


            $("select#type_of_bus").change(function() {
                var bustype = $("select#type_of_bus option:selected").attr('value');
                var wewe = {!! json_encode($prospProperties) !!};
                console.log('wewe', bustype, wewe);

                if (bustype == 'FPR' || bustype == 'FNP') {
                    $('#fac_section').show();

                    $('#tpr_section').hide();
                    $('#tnp_section').hide();
                    $('#trt_common').hide();
                    $('#treaty_grp').hide();

                    processSections('.fac_section', '.fac_section_div', 'enable');
                    processSections('.reins_comm_rate', '.reins_comm_rate_div', 'disable');
                    processSections('.tpr_section', '.tpr_section_div', 'disable');
                    processSections('.tnp_section', '.tnp_section_div', 'disable');
                    processSections('.trt_common', '.trt_common_div', 'disable');
                    processSections('.treaty_grp', '.treaty_grp_div', 'disable');
                } else if (bustype == 'TPR') {
                    $('#treaty_grp').show();
                    $('#trt_common').show();
                    $('#tpr_section').show();
                    $('#fac_section').hide();
                    $('#tnp_section').hide();

                    processSections('.trt_common', '.trt_common_div', 'enable');
                    processSections('.treaty_grp', '.treaty_grp_div', 'enable');
                    processSections('.tpr_section', '.tpr_section_div', 'enable');
                    processSections('.reinsurer_per_treaty', '.reinsurer_per_treaty_div', 'disable');
                    processSections('.fac_section', '.fac_section_div', 'disable');
                    processSections('.tnp_section', '.tnp_section_div', 'disable');



                } else if (bustype == 'TNP') {
                    $('#treaty_grp').show();
                    $('#trt_common').show();
                    $('#tnp_section').show();
                    $('#tpr_section').hide();
                    $('#fac_section').hide();

                    processSections('.trt_common', '.trt_common_div', 'enable');
                    processSections('.treaty_grp', '.treaty_grp_div', 'enable');
                    processSections('.tnp_section', '.tnp_section_div', 'enable');
                    processSections('.tpr_section', '.tpr_section_div', 'disable');
                    processSections('.fac_section', '.fac_section_div', 'disable');


                }

                let selectedTreatyType = ''
                $.ajax({
                    url: "{{ route('cover.get_treatyperbustype') }}",
                    data: {
                        "type_of_bus": bustype
                    },
                    type: "get",
                    success: function(resp) {
                        $(`#treatytype`).empty();

                        $(`#treatytype`).append($('<option>').text(
                                '-- Select Treaty Type--')
                            .attr('value', ''));
                        $.each(resp, function(i, value) {
                            $(`#treatytype`).append($('<option>').text(value
                                    .treaty_code + " - " + value.treaty_name)
                                .attr('value', value.treaty_code)
                            );
                        });
                        $(`#treatytype option[value='${selectedTreatyType}']`).prop(
                            'selected',
                            true)
                        $(`#treatytype`).trigger('change.select2');
                    },
                    error: function(resp) {
                        console.error;
                    }
                })
            });

            $('select#type_of_bus').trigger('change');

            // $('select#customer_id').on('change', function() {
            //     var customerId = $("select#customer_id option:selected").attr('value');

            //     if (customerId) {
            //         $.ajax({
            //             url: "{{ route('get-customer-data') }}",
            //             type: 'GET',
            //             data: {
            //                 customer_id: customerId
            //             },
            //             success: function(response) {
            //                 var data = response.data[0];
            //                 console.log(data);
            //                 if (response) {
            //                     $('#contact_name').val(data.name);
            //                     $('#email').val(data.email);
            //                     $('#telephone').val(data.telephone);
            //                     $('#phone_number').val(data.telephone);
            //                 }
            //             },
            //             error: function(xhr, status, error) {
            //                 console.error(error);
            //             }
            //         });
            //     } else {
            //         console.log('No customer selected');
            //     }
            // });

            // $('select#customer_id').trigger('change');

            $("select#class_group").change(function() {
                var class_group = $("select#class_group option:selected").attr('value');
                $('#classcode').empty();
                if ($(this).val() != '') {
                    $('#class').prop('disabled', false)
                    $.ajax({
                        url: "{{ route('get_class') }}",
                        data: {
                            "class_group": class_group
                        },
                        type: "get",
                        success: function(resp) {
                            /*remove the choose branch option*/

                            $('#classcode').empty();
                            var classes = $.parseJSON(resp);

                            $('#classcode').append($('<option>').text(
                                    '-- Select Class Name--')
                                .attr('value', ''));
                            $.each(classes, function(i, value) {
                                $('#classcode').append($('<option>').text(value
                                        .class_code + " - " + value.class_name)
                                    .attr('value', value.class_code)
                                );


                            });

                            $('.section').trigger("chosen:updated");
                        },
                        error: function(resp) {
                            console.error(resp);
                        }
                    })
                }
            });

            $('select#covertype').trigger('change');

            if ($("select#covertype option:selected").attr('covertype_desc') != 'B') {
                $('#bindercoversec').hide();
            }

            /*** On change of cover Type ***/
            $("select#covertype").change(function() {
                // var binder = $("#covertype").val();
                var binder = $("select#covertype option:selected").attr('covertype_desc')
                $('#bindercoverno').empty();

                if (binder == 'B') {
                    $('#bindercoversec').show();

                    $.ajax({
                        url: "{{ route('get_binder_covers') }}",
                        //data:{"branch":branch},
                        type: "get",
                        success: function(resp) {

                            /*remove the choose branch option*/
                            $('#bindercoverno').empty();
                            var binders = $.parseJSON(resp);
                            $('#bindercoverno').append($('<option>').text(
                                    'Select Binder Cover')
                                .attr('value', ''));

                            $.each(binders, function(i, value) {
                                $('#bindercoverno').append($('<option>').text(value
                                    .binder_cov_no + "  -  " + value
                                    .agency_name
                                ).attr('value', value.binder_cov_no));
                            });

                            $('.section').trigger("chosen:updated");
                        },
                        error: function(resp) {
                            console.error;
                        }
                    })
                } else if (binder == 'N') {
                    $('#bindercoverno').empty();
                    $('#bindercoversec').hide();
                    $('.section').trigger("chosen:updated");

                    $('#bindercoverno').prop('required', false);
                }
            });

            /*** On change Pay Method ***/
            $("select#pay_method").change(function() {
                var pm = $("select#pay_method option:selected").attr('pay_method_desc');
                $('#no_of_installments').empty();

                if (pm === 'I') {
                    $('#no_of_installments_sec').show();
                    $('#fac_installments_box').hide();
                    $('#no_of_installments').prop('required', true);
                    $('#no_of_installments').empty();
                    $('#no_of_installments').val();
                    $('#add_fac_inst_btn_section').show();
                } else {
                    $('#no_of_installments').val(1);
                    $('#no_of_installments_sec').hide();
                    $('#fac_installments_box').hide();
                    $('#add_fac_inst_btn_section').hide();
                    $('#no_of_installments').prop('required', false);
                }
            });

            $('select#pay_method').trigger('change');

            /*** On change Broker Flag ***/
            $("select#broker_flag").change(function() {
                var broker_flag = $("select#broker_flag option:selected").attr('value');
                // $('#brokercode').empty();
                if (broker_flag == 'Y') {
                    $('.brokercode_div').show();
                    $('#brokercode').prop('required', true);
                    $('#brokercode').prop('disabled', false);
                } else {
                    $('#brokercode').val('');
                    $('.brokercode_div').hide();
                    $('#brokercode').prop('required', false);
                    $('#brokercode').prop('disabled', true);
                }
            });
            $("select#broker_flag").trigger('change')

            $('#add_fac_instalments').on('click', function() {
                var noOfInstallments = $("#no_of_installments").val();
                var businessType = $("#type_of_bus").val();
                var cedantPremium = $("#cede_premium").val();
                var facShareOffered = $("#fac_share_offered").val();
                var commRate = $("#comm_rate").val();

                if (Boolean(noOfInstallments === '')) {
                    toastr.error(`Please add Installments`, 'Incomplete data')
                    return false
                } else if (Boolean(businessType === '')) {
                    toastr.error(`Please Select Business Type`, 'Incomplete data')
                    return false
                } else if (Boolean(cedantPremium === '')) {
                    toastr.error(`Please add Cedant Premium`, 'Incomplete data')
                    return false
                } else if (Boolean(facShareOffered === '')) {
                    toastr.error(`Please add Share Offered`, 'Incomplete data')
                    return false
                } else if (Boolean(commRate === '')) {
                    toastr.error(`Please add Commission Rate`, 'Incomplete data')
                    return false
                }
                var instalAmount = computateInstalment()
                // computation for cedant installment amount
                $("#installment_total_amount").val(instalAmount);

                $('#fac_installments_box').show();
                // $('#no_of_installments').trigger('change');
                $('#fac-installments-section').empty()
                const totalInstallments = parseInt($('#no_of_installments').val().replace(/,/g, '')) ||
                    0;
                var no_of_installments = parseInt($('#no_of_installments').val().replace(/,/g, '')) ||
                    0;
                if (no_of_installments > 0) {
                    const totalAmount = instalAmount;
                    const totalFacAmount = parseFloat(totalAmount) || 0;
                    const totalFacInstAmt = (totalFacAmount / totalInstallments).toFixed(2);

                    installmentTotalAmount = totalFacAmount

                    if (totalInstallments <= 100) {
                        for (let i = 1; i <= totalInstallments; i++) {
                            $('#fac-installments-section').append(`
                                <div class="row fac-instalament-row" data-count="${i}">
                                    <div class="col-md-3">
                                        <label class="form-label">Installment</label>
                                        <input type="hidden" name="installment_no[]" value="${i}" readonly class="form-inputs"/>
                                        <input type="hidden" name="installment_id[]" value=""/>
                                        <input type="text" value="installment No. ${i}" id="instl_no_${i}" readonly class="form-inputs" required/>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="instl_date_${i}">Installment Due Date</label>
                                        <input type="date" name="installment_date[]" id="instl_date_${i}"  class="form-inputs" required/>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label" for="instl_amnt_${i}">Total Installment Amount</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="installment_amt[]" id="instl_amnt_${i}" value="${numberWithCommas(totalFacInstAmt)}" class="form-inputs form-input-group amount"  onkeyup="this.value=numberWithCommas(this.value)" change="this.value=numberWithCommas(this.value)" required/>
                                            <button class="btn btn-danger btn-sm remove-fac-instalment" type="button" id="remove-fac-instalment"><i class="bx bx-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            `);
                        }
                    }
                }
            });

            $('#fac-installments-section').on('click', '.remove-fac-instalment', function() {
                // const removedIndex = $(this).closest('.fac-instalament-row').data('count');
                const currentInstallments = $('#no_of_installments').val();
                const remaingInstalment = currentInstallments >= 1 ? parseInt(currentInstallments - 1) :
                    0;
                if (remaingInstalment > 0) {
                    $('#no_of_installments').val(remaingInstalment);
                } else {
                    $('#no_of_installments').val('');
                    $('#fac_installments_box').hide();
                }
                $('#no_of_installments').trigger('change');
                $(this).closest('.fac-instalament-row').remove();
            });
            //$('select#currency_code').trigger('change');
            /*currency logic*/
            // $("select#currency_code").change(function() {
            //     var selected_currency = $("select#currency_code option:selected").attr('value');
            //     var selected_descr = $("select#currency_code option:selected").text();

            //     //alert(selected_currency);
            //     //ajax to check for date
            //     $.ajax({
            //         url: "{{ route('get_todays_rate') }}",
            //         data: {
            //             'currency_code': selected_currency
            //         },
            //         type: "get",
            //         success: function(resp) {
            //             var status = $.parseJSON(resp);
            //             // alert(status.valid);

            //             if (status.valid == 2) {
            //                 // alert('test');
            //                 $('#today_currency').val(1);
            //                 $('#today_currency').prop('readonly', true)
            //             } else if (status.valid == 1) {
            //                 // alert('test');
            //                 //populate rate field
            //                 $('#today_currency').val(status.rate);
            //                 $('#today_currency').prop('readonly', true)
            //             } else {
            //                 $('#today_currency').prop('readonly', true)
            //                 $('#today_currency').val('');
            //                 alert('Currency rate for the day not yet set');
            //                 // $.ajax({
            //                 //     url:" {{ route('yesterdayRate') }}",
            //                 //     data: {'currency_code':selected_currency},
            //                 //     type: "GET",
            //                 //     success: function(resp) {
            //                 //         // alert(resp);
            //                 //         if (resp ==  0) {
            //                 //             $('#today_currency').prop('readonly', true)
            //                 //             $('#today_currency').val('');
            //                 //             $.notify({
            //                 //                 title: "<strong>Today's currency rate not Set </strong><br>",
            //                 //                 message: "Using yesterday's currency rate. Adjust currency rate to fit today's rate",
            //                 //             },{
            //                 //                 type: 'warning'
            //                 //             });
            //                 //         } else {
            //                 //             var rate = resp.currency_rate
            //                 //             $('#today_currency').val(rate);
            //                 //             $('#today_currency').prop('readonly', true)
            //                 //         }
            //                 //     }
            //                 // })
            //             }
            //         },
            //         error: function(resp) {
            //             //alert("Error");
            //             console.error;
            //         }
            //     })
            // });
            /*end of currency logic*/

            // $('#sum_insured_type')
            $("select#sum_insured_type").change(function() {
                var label_txt = $("select#sum_insured_type option:selected").text();
                $('#sum_insured_label').text("(" + label_txt + ")");
            });

            $("#comm_rate").keyup(function() {
                var ratex = $(this).val() || 0;
                var cede = parseFloat(removeCommas($('#cede_premium').val())) || 0;
                var commAmount = (ratex / 100) * cede;
                $('#comm_amt').val(numberWithCommas(commAmount));
                calculateBrokerageCommRate()
            });

            $("#reins_comm_rate").keyup(function() {
                var ratex = $(this).val() || 0;
                var cede = parseFloat(removeCommas($('#rein_premium').val())) || 0;
                var commAmount = (ratex / 100) * cede;
                $('#reins_comm_amt').val(numberWithCommas(commAmount));
                calculateBrokerageCommRate()
            });

            $("#reins_comm_type").change(function() {
                var comm_type = $(this).val();
                // console.log('comm_type:' + comm_type);
                if (comm_type == 'R') {
                    processSections('.reins_comm_rate', '.reins_comm_rate_div', 'enable');
                    $('#reins_comm_amt').prop('readonly', true)
                } else {
                    processSections('.reins_comm_rate', '.reins_comm_rate_div', 'disable');
                    $('#reins_comm_amt').prop('readonly', false)
                }

            });

            $("#reins_comm_type").trigger('change');

            $("#cede_premium").keyup(function() {
                $("#comm_rate").trigger('keyup');
                $("#rein_premium").val($(this).val());
            });

            $("#rein_premium").keyup(function() {
                $("#reins_comm_rate").trigger('keyup');
            });

            $(document).on('change', ".treaty_reinclass", function() {
                const treatyType = $(`#treatytype`).val();
                let counter = $(this).data('counter')
                const reinclass = $(`#treaty_reinclass-${counter}`).val()

                if (treatyType == null || treatyType == '' || treatyType == ' ') {

                    $(`#treaty_reinclass-${counter} option:selected`).removeAttr('selected');
                    $(`#treaty_reinclass-${counter}`).val(null)
                    toastr.error('Please Select Treaty Type First', 'Incomplete data')
                    //
                    return false
                }

                const premTypeCodeSelect = $(`#prem_type_code-${counter}-0`);
                premTypeCodeSelect.attr('data-reinclass', reinclass);

                $(`#prem_type_reinclass-${counter}-0`).val(reinclass);
                $(`#treaty_grp #prem_type_treaty-${counter}-0`).trigger('change')


            });

            $(document).on('change', ".prem_type_code", function() {
                let prem_type_code = $(this).val();
                let classcounter = $(this).data('class-counter')
                let premtypecounter = $(this).data('counter')
                let treaty = $(`#prem_type_treaty-${classcounter}-${premtypecounter}`).val();
                let reinclass = $(`#treaty_reinclass-${classcounter}`).val()

                $(`#prem_type_reinclass-${classcounter}-${premtypecounter}`).val(reinclass);
                // console.log('log',$(`#prem_type_reinclass-${classcounter}-${premtypecounter}`).val());
                const premTypeCodeSelect = $(`#prem_type_code-${classcounter}-${premtypecounter}`);
                premTypeCodeSelect.attr('data-reinclass', reinclass);
                premTypeCodeSelect.attr('data-treaty', treaty);

            });
            $(document).on('change', ".prem_type_treaty", function() {
                let treaty = $(this).val();
                let classcounter = $(this).data('class-counter')
                let premtypecounter = $(this).data('counter')
                let reinclass = $(`#treaty_reinclass-${classcounter}`).val()
                // console.log('treaty:' + treaty + ' reinclass:' + reinclass + ' classcounter:' +
                //     classcounter + ' premcounter:' + premtypecounter);

                fetchPremTypes(treaty, premtypecounter, classcounter)
            });

            function fetchPremTypes(treaty, premCounter, classCounter) {
                let selectedPremTypes = []
                const classElem = $(`#treaty_reinclass-${classCounter}`)
                const reinClass = classElem.val()
                // $('.prem_type_code[data-reinclass="' + reinClass + '"]').each(function () {
                $('.prem_type_code[data-reinclass="' + reinClass + '"][data-treaty="' + treaty + '"]').each(
                    function() {
                        const selectedVal = $(this).find('option:selected').val()
                        if (selectedVal != null && selectedVal != '') {
                            selectedPremTypes.push(selectedVal)
                        }
                    })

                if (classElem.val() != '') {
                    $(`#prem_type_code-${classCounter}-${premCounter}`).prop('disabled', false)

                    $.ajax({
                        url: "{{ route('cover.get_reinprem_type') }}",
                        data: {
                            "reinclass": reinClass,
                            'selectedCodes': selectedPremTypes
                        },
                        type: "get",
                        success: function(resp) {

                            $(`#prem_type_reinclass-${classCounter}`).val(reinClass);
                            /*remove the choose branch option*/
                            $(`#prem_type_code-${classCounter}-${premCounter}`).empty();

                            $(`#prem_type_code-${classCounter}-${premCounter}`).append($(
                                    '<option>')
                                .text('-- Select Premium Type--').attr('value', ''));
                            $.each(resp, function(i, value) {
                                $(`#prem_type_code-${classCounter}-${premCounter}`)
                                    .append($(
                                            '<option>').text(value.premtype_code +
                                            " - " + value
                                            .premtype_name)
                                        .attr('value', value.premtype_code)
                                        .attr('data-reinclass', reinClass)
                                        .attr('data-treaty', treaty)
                                    );


                            });
                            $(`#prem_type_code-${classCounter}-${premCounter}`).trigger(
                                'change.select2');
                        },
                        error: function(resp) {
                            console.error;
                        }
                    })
                }
            }

            $(document).on('click', '.add-comm-section', function() {
                const addSectCounter = $(this).data('counter')

                const lastCommSection = $(`#comm-section-${addSectCounter}`).find(
                    '.comm-sections:last');

                const prevCounter = lastCommSection.data('counter')
                const classCounter = lastCommSection.data('class-counter')
                const reinClassVal = $(`#treaty_reinclass-${classCounter}`).val()
                const premTypeVal = $(`#prem_type_code-${classCounter}-${prevCounter}`).val()
                const premTypeComm = $(`#prem_type_comm_rate-${classCounter}-${prevCounter}`).val()
                if (reinClassVal == null || reinClassVal == '' || reinClassVal == ' ') {
                    toastr.error('Please Select Reinsurance Class', 'Incomplete data')
                    return false
                } else if (premTypeVal == null || premTypeVal == '' || premTypeVal == ' ') {
                    toastr.error('Please Select Premium Type', 'Incomplete data')
                    return false
                } else if (premTypeComm == null || premTypeComm == '' || premTypeComm == ' ') {
                    toastr.error('Input Commission Rate', 'Incomplete data')
                    return false
                }

                // Increment the counter
                let counter = prevCounter + 1;

                appendCommSection(counter, classCounter)
                // fetchPremTypes(counter,classCounter)

                // $(document).find(`#prem_type_code-${classCounter}-${counter}`).select2();

            });

            $(document).on('click', '.remove-comm-section', function() {
                $(this).closest('.comm-sections').remove();
            });

            function appendCommSection(premCounter, classCounter) {
                const reinClassVal = $(`#treaty_reinclass-${classCounter}`).val()
                const treatytype = $('#treatytype').val();

                var btn_class = ''
                var btn_id = ''
                var fa_class = ''
                if (premCounter == 0) {
                    btn_class = 'btn-primary add-comm-section'
                    btn_id = 'add-comm-section'
                    fa_class = 'bx-plus'
                } else {
                    btn_class = 'btn-danger remove-comm-section'
                    btn_id = 'remove-comm-section'
                    fa_class = 'bx-minus'
                }

                $(document).find(`#comm-section-${classCounter}`).append(`
                    <div class="row comm-sections" id="comm-section-${classCounter}-${premCounter}" data-class-counter="${classCounter}" data-counter="${premCounter}">
                        <!-- prem_type_treaty -->
                        <div class="col-sm-3 prem_type_treaty_div">
                            <label class="form-label required">Treaty</label>
                            <select class="form-inputs select2 prem_type_treaty" name="prem_type_treaty[]" id="prem_type_treaty-${classCounter}-${premCounter}" data-class-counter="${classCounter}" data-counter="${premCounter}" required>
                                <option value=""> Select Treaty </option>
                                <option value="SURP"> SURPLUS </option>
                                <option value="QUOT"> QUOTA </option>
                            </select>
                        </div>
                        <!-- reinsurance premium types -->
                        <div class="col-sm-3">
                            <label class="form-label required">Premium Type</label>
                            <input type="hidden" class="form-inputs prem_type_reinclass" id="prem_type_reinclass-${classCounter}-${premCounter}" name="prem_type_reinclass[]" data-counter="${premCounter}" value="${reinClassVal}"/>

                            <select class="form-inputs select2 prem_type_code" name="prem_type_code[]" id="prem_type_code-${classCounter}-${premCounter}" data-reinclass="${reinClassVal}" data-treaty="" data-class-counter="${classCounter}" data-counter="${premCounter}" required>
                                <option value="">--Select Premium Type--</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label required">Commision(%)</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-inputs" name="prem_type_comm_rate[]" id="prem_type_comm_rate-${classCounter}-${premCounter}" data-counter="${premCounter}" required/>
                                <button class="btn ${btn_class}" type="button" id="${btn_id}"><i class="bx ${fa_class}"></i></button>
                            </div>
                        </div>
                    </div>
                `);

                $(`#prem_type_treaty-${classCounter}-${premCounter}`).empty();

                // console.log('SPQT' + treatytype);
                if (treatytype == 'SPQT') {
                    $(`.prem_type_treaty_div`).show();
                    $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text(
                        'SURPLUS AND QUOTA').attr('value', 'SPQT')).change();
                } else if (treatytype == 'QUOT') {
                    $(`.prem_type_treaty_div`).show();
                    $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text('QUOTA')
                        .attr(
                            'value', 'QUOT')).change();
                } else if (treatytype == 'SURP') {
                    $(`.prem_type_treaty_div`).show();
                    $(`#prem_type_treaty-${classCounter}-${premCounter}`).append($('<option>').text(
                        'SURPLUS').attr(
                        'value', 'SURP')).change();
                }

                $(`#treaty_grp #prem_type_treaty-${classCounter}-${premCounter}`).trigger('change');
            }
            $("#method").change(function() {
                const MethodVal = $(`#method`).val();

                $(".burning_rate").prop('disabled', true).val('');
                $(".flat_rate").prop('disabled', true).val('');
                $(".burning_rate_div").hide();
                $(".flat_rate_div").hide();

                if (MethodVal == 'B') {
                    $(".burning_rate_div").show();
                    $(".burning_rate").prop('disabled', false);
                } else {
                    $(".flat_rate_div").show();
                    $(".flat_rate").prop('disabled', false);
                }

            });

            $("#treatytype").change(function() {
                let treatytype = $(this).find('option:selected').val()
                console.log('wewe', treatytype);
                $(`#prem_type_treaty-0-0`).empty();

                if (treatytype == 'SURP') {

                    $('.reinsurer_per_treaty_div').hide();
                    $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                    $('.prem_type_treaty_div').show();
                    $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS').attr('value',
                            'SURP'))
                        .change();

                    $('.no_of_lines_div').show();
                    $('.no_of_lines').prop('disabled', false).val(null);

                    $('.surp_retention_amt_div').show();
                    $('.surp_retention_amt').prop('disabled', false).val(null);

                    $('.surp_treaty_limit_div').show();
                    $('.surp_treaty_limit').prop('disabled', false).val(null);

                    $('.surp_header_div').show();

                    $('.quota_share_total_limit_div').hide();
                    $('.quota_share_total_limit').prop('disabled', true).val(null);

                    $('.retention_per_div').hide();
                    $('.retention_per').prop('disabled', true).val(null);

                    $('.treaty_reice_div').hide();
                    $('.treaty_reice').prop('disabled', true).val(null);

                    $('.quota_retention_amt_div').hide();
                    $('.quota_retention_amt').prop('disabled', true).val(null);

                    $('.quota_treaty_limit_div').hide();
                    $('.quota_treaty_limit').prop('disabled', true).val(null);

                    $('.quota_header_div').hide();

                } else if (treatytype == 'QUOT') {

                    $('.reinsurer_per_treaty_div').hide();
                    $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                    $('.prem_type_treaty_div').show();
                    $(`#prem_type_treaty-0-0`).append($('<option>').text('QUOTA').attr('value', 'QUOT'))
                        .change();

                    $('.quota_share_total_limit_div').show();
                    $('.quota_share_total_limit').prop('disabled', false).val(null);

                    $('.retention_per_div').show();
                    $('.retention_per').prop('disabled', false).val(null);

                    $('.treaty_reice_div').show();
                    $('.treaty_reice').prop('disabled', false).val(null);

                    $('.quota_retention_amt_div').show();
                    $('.quota_retention_amt').prop('disabled', false).val(null);

                    $('.quota_treaty_limit_div').show();
                    $('.quota_treaty_limit').prop('disabled', false).val(null);

                    $('.quota_header_div').show();
                    //
                    $('.no_of_lines_div').hide();
                    $('.no_of_lines').prop('disabled', true).val(null);

                    $('.surp_retention_amt_div').hide();
                    $('.surp_retention_amt').prop('disabled', true).val(null);

                    $('.surp_treaty_limit_div').hide();
                    $('.surp_treaty_limit').prop('disabled', true).val(null);

                    $('.surp_header_div').hide();
                } else if (treatytype == 'SPQT') {

                    $('.reinsurer_per_treaty_div').show();
                    $('.reinsurer_per_treaty').prop('disabled', false).val(null);

                    $('.prem_type_treaty_div').show();
                    $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS AND QUOTA').attr(
                        'value',
                        'SPQT')).change();

                    $('.quota_share_total_limit_div').show();
                    $('.quota_share_total_limit').prop('disabled', false).val(null);

                    $('.retention_per_div').show();
                    $('.retention_per').prop('disabled', false).val(null);

                    $('.quota_retention_amt_div').show();
                    $('.quota_retention_amt').prop('disabled', false).val(null);

                    $('.quota_treaty_limit_div').show();
                    $('.quota_treaty_limit').prop('disabled', false).val(null);

                    $('.treaty_reice_div').show();
                    $('.treaty_reice').prop('disabled', false).val(null);

                    $('.no_of_lines_div').show();
                    $('.no_of_lines').prop('disabled', false).val(null);

                    $('.surp_retention_amt_div').show();
                    $('.surp_retention_amt').prop('disabled', false).val(null);

                    $('.surp_treaty_limit_div').show();
                    $('.surp_treaty_limit').prop('disabled', false).val(null);

                    $('.surp_header_div').show();
                    $('.quota_header_div').show();
                } else {

                    $('.reinsurer_per_treaty_div').hide();
                    $('.reinsurer_per_treaty').prop('disabled', true).val(null);

                    $('.prem_type_treaty_div').show();
                    $(`#prem_type_treaty-0-0`).append($('<option>').text('SURPLUS').attr('value',
                            'SURP'))
                        .change();

                    $('.no_of_lines_div').hide();
                    $('.no_of_lines').prop('disabled', true).val(null);

                    $('.surp_retention_amt_div').hide();
                    $('.surp_retention_amt').prop('disabled', true).val(null);

                    $('.surp_treaty_limit_div').hide();
                    $('.surp_treaty_limit').prop('disabled', true).val(null);

                    $('.surp_header_div').hide();

                    $('.quota_share_total_limit_div').hide();
                    $('.quota_share_total_limit').prop('disabled', true).val(null);

                    $('.retention_per_div').hide();
                    $('.retention_per').prop('disabled', true).val(null);

                    $('.treaty_reice_div').hide();
                    $('.treaty_reice').prop('disabled', true).val(null);

                    $('.quota_retention_amt_div').hide();
                    $('.quota_retention_amt').prop('disabled', true).val(null);

                    $('.quota_treaty_limit_div').hide();
                    $('.quota_treaty_limit').prop('disabled', true).val(null);

                    $('.quota_header_div').hide();

                }

            });

            $(document).on('keyup', ".no_of_lines", function() {
                let reinclass_counter = $(`.treaty_reinclass`).data('counter')
                var lines = $(this).val() || 0;
                var counter = $(this).data('counter');
                var ret = parseFloat(removeCommas($(`#surp_retention_amt-${counter}`).val())) || 0;
                var trt_limit = lines * ret;
                $(`#surp_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
            });

            $(document).on('keyup', ".retention_per", function() {
                var ret_per = $(this).val() || 0;
                var counter = $(this).data('counter');
                var quota_limit_total = parseFloat(removeCommas($(`#quota_share_total_limit-${counter}`)
                    .val())) || 0;
                var trt_per = 100 - ret_per;
                var ret_amt = (ret_per / 100) * quota_limit_total;
                var trt_limit = (trt_per / 100) * quota_limit_total;

                $(`#treaty_reice-${counter}`).val(trt_per);
                $(`#quota_retention_amt-${counter}`).val(numberWithCommas(ret_amt));
                $(`#quota_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
            });

            $(document).on('keyup', ".quota_share_total_limit", function() {
                var ret_per = $(`#treaty_reice-${counter}`).val() || 0;
                var counter = $(this).data('counter');
                var quota_limit_total = parseFloat(removeCommas($(`#quota_share_total_limit-${counter}`)
                    .val())) || 0;
                var trt_per = 100 - ret_per;
                var ret_amt = (ret_per / 100) * quota_limit_total;
                var trt_limit = (trt_per / 100) * quota_limit_total;

                $(`#treaty_reice-${counter}`).val(trt_per);
                $(`#quota_retention_amt-${counter}`).val(numberWithCommas(ret_amt));
                $(`#quota_treaty_limit-${counter}`).val(numberWithCommas(trt_limit));
            });

            // Adding new layer
            $('#layer-section').on('click', '#add-layer-section', function() {
                const lastLayerSection = $('#layer-section .layer-sections:last');
                const MethodVal = $('#method').val();
                const prevCounter = lastLayerSection.data('counter');
                const IndemnityTreatyLimit = $(`#indemnity_treaty_limit-${prevCounter}-0`).val();
                const UnderlyingLimit = $(`#underlying_limit-${prevCounter}-0`).val();
                const EgnpiVal = $(`#egnpi-${prevCounter}-0`).val();
                const MinBcRate = $(`#min_bc_rate-${prevCounter}-0`).val();
                const MaxBcRate = $(`#max_bc_rate-${prevCounter}-0`).val();
                const FlatRate = $(`#flat_rate-${prevCounter}-0`).val();
                const UpperAdj = $(`#upper_adj-${prevCounter}-0`).val();
                const LowerAdj = $(`#lower_adj-${prevCounter}-0`).val();
                const MinDeposit = $(`#min_deposit-${prevCounter}-0`).val();
                const limit_per_reinclass = $(`#limit_per_reinclass-${prevCounter}-0`).val();

                // Validation
                if (!IndemnityTreatyLimit.trim()) {
                    toastr.error('Please Capture Treaty Limit', 'Incomplete data');
                    return false;
                } else if (!UnderlyingLimit.trim()) {
                    toastr.error('Please Capture Deductive', 'Incomplete data');
                    return false;
                } else if (!EgnpiVal.trim()) {
                    toastr.error('Please Capture EGNPI', 'Incomplete data');
                    return false;
                } else if (!MinBcRate.trim() && MethodVal === 'B') {
                    toastr.error('Input Minimum Burning Cost Rate', 'Incomplete data');
                    return false;
                } else if (!MaxBcRate.trim() && MethodVal === 'B') {
                    toastr.error('Input Maximum Burning Cost Rate', 'Incomplete data');
                    return false;
                } else if (!FlatRate.trim() && MethodVal === 'F') {
                    toastr.error('Input Flat Rate', 'Incomplete data');
                    return false;
                } else if (!UpperAdj.trim() && MethodVal === 'B') {
                    toastr.error('Please Capture Upper Adjustment Rate', 'Incomplete data');
                    return false;
                } else if (!LowerAdj.trim() && MethodVal === 'B') {
                    toastr.error('Please Capture Lower Adjustment Rate', 'Incomplete data');
                    return false;
                } else if (!MinDeposit.trim()) {
                    toastr.error('Please Confirm Minimum Deposit Premium(MDP) Amount',
                        'Incomplete data');
                    return false;
                }

                if (limit_per_reinclass === 'Y') {
                    const IndemnityTreatyLimit = $(`#indemnity_treaty_limit-${prevCounter}-1`).val();
                    const UnderlyingLimit = $(`#underlying_limit-${prevCounter}-1`).val();
                    const EgnpiVal = $(`#egnpi-${prevCounter}-1`).val();
                    const MinBcRate = $(`#min_bc_rate-${prevCounter}-1`).val();
                    const MaxBcRate = $(`#max_bc_rate-${prevCounter}-1`).val();
                    const FlatRate = $(`#flat_rate-${prevCounter}-1`).val();
                    const UpperAdj = $(`#upper_adj-${prevCounter}-1`).val();
                    const LowerAdj = $(`#lower_adj-${prevCounter}-1`).val();
                    const MinDeposit = $(`#min_deposit-${prevCounter}-1`).val();

                    if (!IndemnityTreatyLimit.trim()) {
                        toastr.error('Please Capture Treaty Limit', 'Incomplete data');
                        return false;
                    } else if (!UnderlyingLimit.trim()) {
                        toastr.error('Please Capture Deductive', 'Incomplete data');
                        return false;
                    } else if (!EgnpiVal.trim()) {
                        toastr.error('Please Capture EGNPI', 'Incomplete data');
                        return false;
                    } else if (!MinBcRate.trim() && MethodVal === 'B') {
                        toastr.error('Input Minimum Burning Cost Rate', 'Incomplete data');
                        return false;
                    } else if (!MaxBcRate.trim() && MethodVal === 'B') {
                        toastr.error('Input Maximum Burning Cost Rate', 'Incomplete data');
                        return false;
                    } else if (!FlatRate.trim() && MethodVal === 'F') {
                        toastr.error('Input Flat Rate', 'Incomplete data');
                        return false;
                    } else if (!UpperAdj.trim() && MethodVal === 'B') {
                        toastr.error('Please Capture Upper Adjustment Rate', 'Incomplete data');
                        return false;
                    } else if (!LowerAdj.trim() && MethodVal === 'B') {
                        toastr.error('Please Capture Lower Adjustment Rate', 'Incomplete data');
                        return false;
                    } else if (!MinDeposit.trim()) {
                        toastr.error('Please Confirm Minimum Deposit Premium(MDP) Amount',
                            'Incomplete data');
                        return false;
                    }
                }

                // Increment the counter
                let counter = prevCounter + 1;
                $('#layer-section').append(`
                    <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                        <h6> Layer: ${counter+1} </h6>
                        <div class="row">
                            <!--Flag to show if layers are per class-->
                            <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                <label class="form-label required">Capture Limits per Class ?</label>
                                <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" id="limit_per_reinclass-${counter}-0" value="N" required>
                                    <option value=""> Select Option </option>
                                    <option value="N" selected> No </option>
                                    <option value="Y"> Yes </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-1 nonprop_reinclass">
                            <label class="form-label required">Reinclass</label>
                            <input type="hidden" class="form-control layer_no" aria-label="layer_no" data-counter="${counter}" id="layer_no-${counter}-0" name="layer_no[]" value="${counter + 1}" readonly>
                            <input type="hidden" class="form-control nonprop_reinclass" aria-label="nonprop_reinclass" data-counter="${counter}" id="nonprop_reinclass-${counter}-0" name="nonprop_reinclass[]" value="ALL" readonly>
                            <input type="text" class="form-control nonprop_reinclass_desc" aria-label="nonprop_reinclass_desc" data-counter="${counter}" id="nonprop_reinclass_desc-${counter}-0" name="nonprop_reinclass_desc[]" value="ALL" readonly>
                        </div>
                        <!--Indemnity-->
                        <div class="col-sm-2">
                            <label class="form-label required">Limit</label>
                            <input type="text" class="form-inputs" aria-label="indemnity_limit" id="indemnity_treaty_limit-${counter}-0" name="indemnity_treaty_limit[]" onkeyup="this.value=numberWithCommas(this.value)"/>
                        </div>

                        <!--Underlying Limit-->
                        <div class="col-sm-2">
                            <label class="form-label required">Deductible Amount</label>
                            <input type="text" class="form-inputs" aria-label="underlying_limit" id="underlying_limit-${counter}-0" name="underlying_limit[]" onkeyup="this.value=numberWithCommas(this.value)"/>
                        </div>

                        <!--EGNPI (Estimated Premium)-->
                        <div class="col-sm-2">
                            <label class="form-label required">EGNPI</label>
                            <input type="text" class="form-inputs" aria-label="egnpi" id="egnpi-${counter}-0" name="egnpi[]" onkeyup="this.value=numberWithCommas(this.value)"/>
                        </div>

                        <!--For Burning Cost (B) --- Minimum Rate: (%)-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Burning Cost-Minimum Rate(%)</label>
                            <input type="text" name="min_bc_rate[]" id="min_bc_rate-${counter}-0" class="form-inputs burning_rate" value="{{ old('min_bc_rate') }}"/>
                        </div>

                        <!--Maximum Rate: (%)-->
                        <div class="col-sm-2 burning_rate_div">
                            <label class="form-label required">Maximum Rate: (%)</label>
                            <input type="text" name="max_bc_rate[]" id="max_bc_rate-${counter}-0" class="form-inputs burning_rate" value="{{ old('max_bc_rate') }}"/>
                        </div>

                        <!--For Flat Rate: (%)-->
                        <div class="col-sm-2 flat_rate_div">
                            <label class="form-label required">For Flat Rate: (%)</label>
                            <input type="text" name="flat_rate[]" id="flat_rate-${counter}-0" class="form-inputs flat_rate" value="{{ old('applied_rate') }}"/>
                        </div>

                        <!--Adjustable Annually Rate-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Upper Adjust. Annually Rate</label>
                            <input type="text" name="upper_adj[]" id="upper_adj-${counter}-0" class="form-inputs burning_rate" value="{{ old('upper_adj') }}"/>
                        </div>

                        <!--Adjustable Annually Rate-->
                        <div class="col-sm-3 burning_rate_div">
                            <label class="form-label required">Lower Adjust. Annually Rate</label>
                            <input type="text" name="lower_adj[]" id="lower_adj-${counter}-0" class="form-inputs burning_rate" value="{{ old('lower_adj') }}"/>
                        </div>

                        <!--Minimum Deposit Premium -->
                        <div class="col-sm-3">
                            <label class="form-label required">Minimum Deposit Premium </label>
                            <div class="input-group mb-3">
                                <input type="text" name="min_deposit[]" id="min_deposit-${counter}-0" class="form-control" value="{{ old('min_deposit') }}" onkeyup="this.value=numberWithCommas(this.value)"/>
                                <button class="btn btn-danger remove-layer-section" type="button" id="remove-layer-section"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>

                        {{-- Reinstatement Type Arrangement --}}
                        <div class="col-sm-3 reinstatement_type_div tnp_section_div">
                            <label class="form-label required"> Reinstatement Type </label>
                            <div class="input-group mb-3">
                                <select name="reinstatement_type[]" id="reinstatement_type-${counter}-0" class="form-inputs select2">
                                    <option value="NOR">Number of Reinstatement</option>
                                    <option value="AAL">Annual Aggregate Limit</option>
                                </select>
                            </div>
                        </div>
                        {{-- Reinstatement Type Value --}}
                        <div class="col-sm-3 reinstatement_value_div tnp_section_div">
                            <label class="form-label required"> Reinstatement Value </label>
                            <div class="input-group mb-3">
                                <input type="text" name="reinstatement_value[]" id="reinstatement_value-${counter}-0" class="form-control reinstatement_value tnp_section" value="" onkeyup="this.value=numberWithCommas(this.value)" required/>
                            </div>
                        </div>
                    </div>
                `);

                $(".burning_rate_div").hide();
                $(".flat_rate_div").hide();

                if (MethodVal === 'B') {
                    $(".burning_rate_div").show();
                    $(".burning_rate").prop('disabled', false);
                    $(".flat_rate").prop('disabled', true).val('');
                } else {
                    $(".flat_rate_div").show();
                    $(".flat_rate").prop('disabled', false);
                    $(".burning_rate").prop('disabled', true).val('');
                }
            });

            $('#layer-section').on('click', '.remove-layer-section', function() {
                $(this).closest('.layer-sections').remove();
            });

            $('#add_rein_class').on('click', function() {
                var $lastSection = $('.reinclass-section').last();

                const prevCounter = parseInt($lastSection.attr('data-counter'))
                const reinClassVal = $(`#treaty_reinclass-${prevCounter}`).val()
                const prevSectionLabel = String.fromCharCode(65 + prevCounter)
                if (reinClassVal == null || reinClassVal == '' || reinClassVal == ' ') {
                    toastr.error(`Please Select Reinsurance Class in Section ${prevSectionLabel}`,
                        'Incomplete data')
                    return false
                }

                var $newSection = $lastSection.clone(); // Clone the last section

                // Remove select2-related elements
                $newSection.find('.select2-container').remove();

                // Increment data-counter attributes for the new section and its children
                var counter = parseInt($lastSection.attr('data-counter')) + 1;
                $newSection.attr('id', 'reinclass-section-' + counter);
                $newSection.attr('data-counter', counter);
                $newSection.find('[id]').each(function() {
                    var id = $(this).attr('id');
                    $(this).attr('id', id.replace(/-\d$/, '-' + counter));
                    $(this).attr('data-counter', counter);
                });

                let selectedReinClasses = []
                $('.treaty_reinclass').each(function() {
                    const selectedVal = $(this).find('option:selected').val()

                    if (selectedVal != '') {
                        selectedReinClasses.push(selectedVal)
                    }
                });

                $newSection.find('.treaty_reinclass').attr('data-counter', counter)
                $newSection.find('.comm-section').attr('id', `comm-section-${counter}`)

                $newSection.find('.treaty_reinclass option').each(function() {
                    const val = $(this).val()
                    if (selectedReinClasses.indexOf(val) !== -1) {
                        $(this).remove();
                    }
                })

                // remove comm section and add afresh
                $newSection.find('.comm-sections').remove()

                // Update the section label (e.g., A, B, C, etc.)
                const currentSectionLabel = String.fromCharCode(65 + counter); // A: 65
                $newSection.find('.section-title').text('Section ' + currentSectionLabel);

                // Reset input values in the new section
                $newSection.find('input[type="text"], input[type="number"]').val('');

                // Clear selected options in select elements
                $newSection.find('select').val('').select2();

                // Insert the new section after the last section
                $lastSection.after($newSection);

                appendCommSection(0, counter)
            });

            function processSections(sectionClass, sectionDivClass, action) {
                if (action == 'enable') {
                    $(sectionClass + ', ' + sectionDivClass).each(function() {
                        if ($(this).hasClass(sectionDivClass.substr(1))) {
                            $(this).show();
                        } else {
                            $(this).prop('disabled', false);

                        }
                    });
                } else {
                    $(sectionClass + ', ' + sectionDivClass).each(function() {
                        if ($(this).hasClass(sectionDivClass.substr(1))) {
                            $(this).hide();
                        } else {
                            $(this).prop('disabled', true);
                        }
                    });
                }

            }

            // Adding new item in a layer
            $('#layer-section').on('change', '.limit_per_reinclass', function() {
                var lastLayerSection = $('#layer-section .layer-sections:last');
                var counter = lastLayerSection.data('counter');
                var itemcounter = 0;
                var MethodVal = $('#method').val();
                var limit_per_reinclass = $(`#limit_per_reinclass-${counter}-${itemcounter}`).val();
                // Remove existing layer sections
                $('[id^="layer-section-' + counter + '"]').remove();

                // Add new layers based on the selected limit_per_reinclass value
                if (limit_per_reinclass === 'Y') {
                    // Get the select element
                    var selectElement = document.getElementById("tnp_reinclass_code");

                    $('#layer-section').append(`
                        <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                            ${ counter !== 0 ? `<h6> Layer: ${counter + 1} </h6>` : '' }
                            <div class="row">
                                <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                    <label class="form-label required">Capture Limits per Class?</label>
                                    <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" data-counter="${counter}" id="limit_per_reinclass-${counter}-${itemcounter}" required>
                                        <option value="">Select Option</option>
                                        <option value="N">No</option>
                                        <option value="Y" selected>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `);

                    // Loop through each option in the selectElement
                    for (var i = 0; i < selectElement.options.length; i++) {
                        var option = selectElement.options[i];
                        if (option.selected) {
                            var optionValue = option.value;
                            var optionText = option.text;

                            if (optionValue != null && optionValue != '') {
                                $('#layer-section').append(`
                                    <div class="row layer-sections" id="layer-section-${counter}-${itemcounter}" data-counter="${counter}">
                                        <div class="col-sm-1 nonprop_reinclass">
                                            <label class="form-label required">Reinclass</label>
                                            <input type="hidden" class="form-control layer_no" data-counter="${counter}" id="layer_no-${counter}-${itemcounter}" name="layer_no[]" value="${counter + 1}" readonly/>
                                            <input type="hidden" class="form-control nonprop_reinclass" data-counter="${counter}" id="nonprop_reinclass-${counter}-${itemcounter}" name="nonprop_reinclass[]" value="${optionValue}" readonly/>
                                            <input type="text" class="form-control nonprop_reinclass_desc" data-counter="${counter}" id="nonprop_reinclass_desc-${counter}-${itemcounter}" name="nonprop_reinclass_desc[]" value="${optionText}" readonly/>
                                        </div>
                                        <!-- Other inputs go here -->
                                    </div>
                                `);

                                $(".burning_rate_div").hide();
                                $(".flat_rate_div").hide();

                                if (MethodVal === 'B') {
                                    $(".burning_rate_div").show();
                                    $(".burning_rate").prop('disabled', false);
                                    $(".flat_rate").prop('disabled', true).val('');
                                } else {
                                    $(".flat_rate_div").show();
                                    $(".flat_rate").prop('disabled', false);
                                    $(".burning_rate").prop('disabled', true).val('');
                                }

                                itemcounter++;
                            }
                        }
                    }
                } else {
                    $('#layer-section').append(`
                        <div class="row layer-sections" id="layer-section-${counter}" data-counter="${counter}">
                            ${ counter !== 0 ? `<h6> Layer: ${counter + 1} </h6>` : '' }
                            <div class="row">
                                <div class="col-sm-2 limit_per_reinclass_div tnp_section_div">
                                    <label class="form-label required">Capture Limits per Class?</label>
                                    <select class="form-inputs limit_per_reinclass tnp_section_div" name="limit_per_reinclass[]" id="limit_per_reinclass-${counter}-0" value="N" required>
                                        <option value="">Select Option</option>
                                        <option value="N" selected>No</option>
                                        <option value="Y">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Other inputs go here -->
                        </div>
                    `);

                    $(".burning_rate_div").hide();
                    $(".flat_rate_div").hide();

                    if (MethodVal === 'B') {
                        $(".burning_rate_div").show();
                        $(".burning_rate").prop('disabled', false);
                        $(".flat_rate").prop('disabled', true).val('');
                    } else {
                        $(".flat_rate_div").show();
                        $(".flat_rate").prop('disabled', false);
                        $(".burning_rate").prop('disabled', true).val('');
                    }
                }
            });

            $('#layer-section').on('click', '.remove-layer-section', function() {
                $(this).closest('.layer-sections').remove();
            });

            $('#apply_eml').change(function(e) {
                e.preventDefault();
                const applyEML = $(this).val()

                $('#eml_rate').hide();
                $('#eml_amt').hide();
                $('.eml-div').hide();
                if (applyEML == 'Y') {
                    $('#eml_rate').show();
                    $('#eml_amt').show();
                    $('.eml-div').show();
                    $('#total_sum_insured').trigger('keyup');
                } else {
                    const totalSumInsured = parseFloat(removeCommas($('#total_sum_insured').val()))
                    $('#effective_sum_insured').val(numberWithCommas(totalSumInsured));

                }
            });

            $('#eml_rate').keyup(function(e) {
                const emlRate = $(this).val()
                const totalSumInsured = parseFloat(removeCommas($('#total_sum_insured').val()))
                const emlAmt = totalSumInsured * (emlRate / 100)

                $('#eml_amt').val(numberWithCommas(emlAmt));
                $('#effective_sum_insured').val(numberWithCommas(emlAmt));
            });

            $('#total_sum_insured').keyup(function(e) {
                const totalSumInsured = removeCommas($(this).val())
                let effectiveSumInsured = totalSumInsured

                const emlRate = $('#eml_rate').val()
                const applyEml = $('#apply_eml').val()

                if ((emlRate != null && emlRate != '' && applyEml == 'Y') && (totalSumInsured != null &&
                        totalSumInsured != '')) {
                    const emlAmt = effectiveSumInsured = parseFloat(totalSumInsured) * (parseFloat(
                        emlRate) / 100)
                    $('#eml_amt').val(numberWithCommas(emlAmt));
                }

                $('#effective_sum_insured').val(numberWithCommas(effectiveSumInsured));
            });
            $('#total_sum_insured').trigger('keyup')

            $('#brokerage_comm_type').change(function(e) {
                const brokerageCommType = $(this).val()
                $('.brokerage_comm_amt_div').hide();
                $('.brokerage_comm_rate_amt_div').hide();
                $('#brokerage_comm_amt').hide();
                $('#brokerage_comm_rate_amt').hide();
                $('#brokerage_comm_rate').hide();
                $('#brokerage_comm_rate_label').hide();
                $('#brokerage_comm_rate_amount_label').hide();
                $('.brokerage_comm_rate_div').hide();
                $('#brokerage_comm_rate').val(null);
                $('#brokerage_comm_amt').val(null); //checkout

                if (brokerageCommType == 'R') {
                    $('.brokerage_comm_rate_div').show();
                    $('.brokerage_comm_rate_amt_div').show();
                    $('#brokerage_comm_rate').show();
                    $('#brokerage_comm_rate_amt').show();
                    $('#brokerage_comm_rate_label').show();
                    $('#brokerage_comm_rate_amount_label').show();
                    calculateBrokerageCommRate()
                } else {
                    $('.brokerage_comm_amt_div').show();
                    $('#brokerage_comm_amt').show().prop('disabled', false);
                }
            });
            $('#brokerage_comm_type').trigger('change')

            function calculateBrokerageCommRate() {
                let cedantCommRate = removeCommas($('#comm_rate').val())
                let reinCommRate = removeCommas($('#reins_comm_rate').val())
                let cedantPremium = removeCommas($('#cede_premium').val())
                let brokerageCommRate = 0

                if (cedantCommRate != '' && cedantCommRate != null && reinCommRate != '' && reinCommRate !=
                    null) {
                    brokerageCommRate = parseFloat(reinCommRate) - parseFloat(cedantCommRate)
                }
                brokerageCommAmt = parseFloat(cedantPremium) * (parseFloat(brokerageCommRate) / 100)

                $('#brokerage_comm_rate').val(brokerageCommRate);
                $('#brokerage_comm_rate_amt').val(numberWithCommas(brokerageCommAmt));
            }

            $('#brokerage_comm_type').trigger('change')
            $('#apply_eml').trigger('change')
            $('#reins_comm_type').trigger('change')

            function computateInstalment() {
                var shareOffered = parseFloat($('#fac_share_offered').val().replace(/,/g, '')) || 0;
                var rate = parseFloat($('#comm_rate').val().replace(/,/g, '')) || 0;
                var cedantPremium = parseInt($('#cede_premium').val().replace(/,/g, '')) || 0;
                var totalDr = parseFloat((shareOffered / 100) * cedantPremium).toFixed(2);
                var totalCr = parseFloat((rate / 100) * totalDr);
                return (totalDr - totalCr).toFixed(2);
            }
            {{-- Steve End --}}
            //Populates the Cover END DATE
            $('#effective_date').on('change', function() {
                var effectiveDate = $(this).val();

                if (effectiveDate) {
                    var date = new Date(effectiveDate);
                    date.setFullYear(date.getFullYear() + 1);
                    date.setDate(date.getDate() - 1);
                    var closingDate = date.toISOString().split('T')[0]; // YYYY-MM-DD format
                    $('#closing_date').val(closingDate);
                }
            });

            // end
            $('#full_name_input').on('input', function() {
                var query = $(this).val();
                if (query.length < 1) {
                    $('#full_name_results').hide();
                    return;
                }

                $.ajax({
                    url: "{{ route('search-prospect-fullnames') }}",
                    method: 'GET',
                    data: {
                        q: query
                    },
                    success: function(data) {
                        var results = '';
                        if (data.length > 0) {
                            data.forEach(function(item) {
                                results +=
                                    `<div class="dropdown-item" data-id="${item.pipeline_id}">${item.fullname}</div>`;
                            });
                        } else {
                            results = '<div class="dropdown-item">No results found</div>';
                        }
                        $('#full_name_results').html(results).show();
                    },
                    error: function() {
                        $('#full_name_results').html(
                            '<div class="dropdown-item">Error fetching data</div>').show();
                    }
                });
            });

            $(document).on('click', '.dropdown-item', function() {
                var selectedName = $(this).text();
                $('#full_name_input').val(selectedName);
                $('#full_name_results').hide();
            });

            // Hide results when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest('#full_name_input').length) {
                    $('#full_name_results').hide();
                }
            });


            let prospect = "{!! $prospect !!}"
            let ins_class = ''
            let pq = ''

            $('.pq_cost').hide()

            // if (prospect != null && prospect != '' && prospect != undefined) {

            //     $.ajax({
            //         type: "GET",
            //         data: {
            //             'prospect': prospect
            //         },
            //         url: "{{ route('get_prospect_details') }}",
            //         success: function(resp) {

            //             pq = resp.prequalification;
            //             pq_status = resp.pq_status;

            //             if (pq == 'Y' && pq_status != 'W') {
            //                 $('#process_pq').show()
            //                 $('#sales_mngt').hide()
            //                 $('.pq_cost').show();
            //                 $('#prequalification').val(resp.prequalification).trigger('change');
            //                 $('#prod_cost').val(resp.production_cost);
            //                 $('#cost_currency').val(resp.prod_currency).trigger('change');
            //             }
            //             // $('#postal_address').val(resp.postal_address);
            //             // $('#postal_code').val(resp.postal_code);
            //             // $('#lead_year').val(resp.pip_year).trigger('change');
            //             // $('#client_category').val(resp.client_category).trigger('change');
            //             // $('#client_type').val(resp.client_type).trigger('change');
            //             // $('#division').val(resp.division).trigger('change');
            //             // $('#division').trigger('change');
            //             // $('#insurance_class').val(resp.insurance_class).trigger('change');
            //             // $('#currency').val(resp.currency).trigger('change');
            //             // $('#engage_type').val(resp.engage_type).trigger('change');
            //             // $('#lead_source').val(resp.lead_source).trigger('change');
            //             // $('#lead_owner').val(resp.lead_owner).trigger('change');
            //             // $('#lead_handler').val(resp.lead_handler).trigger('change');
            //             // $('#industry').val(resp.industry).trigger('change').trigger('change');
            //             // $('#premium').val(resp.premium);
            //             // $('#income').val(resp.income).attr('readonly', true);
            //             // $('#effective_date').val(resp.effective_date);
            //             // $('#closing_date').val(resp.closing_date);
            //             // $('#rating').val(resp.rating);
            //             // $('#email').val(resp.email);
            //             // $('#source_desc').val(resp.source_desc)
            //             // $('#contact_name').val(resp.contact_name);
            //             // $('#phone_number').val(parseInt(resp.phone));
            //             // $('#physical_address').val(resp.physical_address);
            //             // $('#full_name_input').val(resp.fullname);
            //             // $('#contact_position').val(resp.contact_position);
            //             // $('#country_code').val(resp.country_code);
            //             // $('#narration').val(resp.narration);
            //             // $('#town').val(resp.town);
            //             // $('#telephone').val(resp.telephone);
            //             // $('#alternative_contact_name').val(resp.alternate_contact);
            //             // $('#alternative_email').val(resp.alternate_email);
            //             // $('#alternative_phone_number').val(resp.alternate_phone);
            //             // $('#alternative_contact_position').val(resp.alternate_position);

            //             // $('#sales_mngt').attr('data-prospect', resp.opportunity_id);


            //         }
            //     })

            //     // $('#prequalification_div').hide()
            // }

            $('#stage_cycle').on('change', function() {

                if ($(this).val() != 'P') {
                    $('#locked_uws').hide()
                } else {
                    $('#locked_uws').show()
                }
            })

            $('#process_pq').on('click', function() {
                $('#editStatusModal').modal('show')
            })

            $('#prequalification').on('change', function() {

                if ($(this).val() == 'Y') {
                    $('.pq_cost').show()
                    $('#cost_currency').trigger('change')
                } else {
                    $('.pq_cost').hide()
                    $('#cost_currency').val('')
                    $('#prod_cost').val('')
                }
            })



            $('body').on('change', '.document_file', function() {
                let file = $(this)[0].files[0];
                let id = $(this).attr('id')
                let id_length = id.length
                let rowID = id.slice(13, id_length)


                let formData = new FormData();
                formData.append('doc', file);


                $.ajax({
                    type: "POST",
                    data: formData,
                    url: "{{ route('doc_preview') }}",
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        $("#preview" + rowID).attr('data-file', resp);

                    }
                })
            })

            $('body').on('click', '.preview', function(e) {
                $('object').attr('data', '');
                var doc = $(this).attr('data-file');

                $('#doc_view').html('<iframe src="' + doc + '" width="100%" height="900"></iframe>');

                $('#v_docs').modal('show');



            });

            $('body').on('click', '#addDoc', function() {
                if (counter > 0) {
                    var document_title = $('#document_title' + counter).val()
                    var document_file = $('#document_file' + counter).val()
                } else if (counter == 0) {
                    var document_title = $('#document_title0').val()
                    var document_file = $('#document_file0').val()
                }
                if (document_title == '' || document_file == '') {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Please fill all details'
                    });
                } else {
                    counter = +1;
                    $('#file_details').append(
                        `<div class="row row-margin mt-1">

                        <div class="col-6">
                            <div class="row">
                                <x-Input id="document_name${counter}" name="document_name[]" req=""
                                    inputLabel="Document Title"
                                    placeholder="Enter document title"
                                    oninput='this.value=this.value.toUpperCase();'/>

                            </div>
                        </div>

                        <div class="col-5">
                            <label for="document_file">File</label>
                            <div class="input-group">
                                <input type="file" name="document_file[]" id="document_file${counter}" class="form-control document_file" />
                                <button class="btn btn-danger remove_file" type="button"><i class="fa fa-minus"></i> </button>
                            </div>
                        </div>

                        <div class="col-1" style="margin-top: 30px">
                            <i class="fa fa-eye preview" id="preview${counter}"> </i>
                        </div>
                    </div>`
                    );
                }


                $('input[type=radio]').change(function() {
                    $('input[type=radio]:checked').not(this).prop('checked', false)
                })
            });

            $('#file_details').delegate('.remove_file', 'click', function() {
                $(this).parent().parent().parent().remove();
            });

            $('#prequalification').trigger('change');

            $('#organic_growth_div').hide();


            $('#sales_mngt').on('click', function() {
                let prospect = $('#prospectId').val()

                Swal.fire({
                    title: "Warning!",
                    html: "Are You Sure You Want to add this prospect to Sales Management",
                    icon: "warning",
                    confirmButtonText: "Yes",
                    showCancelButton: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: "{!! route('prospect.add.pipeline') !!}",
                            data: {
                                'prospect': prospect
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content'),
                                'Authorization': 'Bearer your-token-here'
                            },

                            success: function(response) {
                                if (response.status == 1) {
                                    toastr.success(response.message, {
                                        timeOut: 5000
                                    });
                                }
                                window.location.href = `/leads_listing`;
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    title: "Error",
                                    text: textStatus,
                                    icon: "error"
                                });
                            }
                        });
                    }
                })
            })

            const insuranceClassInput = document.getElementById('insurance_class');

            if (insuranceClassInput) {
                insuranceClassInput.addEventListener('change', function() {

                    const insuranceClass = this.value;
                    const fullName = $("#full_name_input").val();
                    const division = $("#division").val();
                    const year = $("#lead_year").val();

                    if (insuranceClass && fullName && division && year) {
                        fetch("{{ route('check_user_exists') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    full_name: fullName,
                                    division: division,
                                    year: year,
                                    insurance_class: insuranceClass
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data);
                                if (data.exists) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'User Exists',
                                        text: 'A user with these details already exists in the database.',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            }

            $("select#client_type").change(function() {
                let ctype = $("#client_type").val();

                var selectedValue = this.value;

                // Get the full_name input field and its parent div
                var fullNameInput = document.getElementById('full_name_input');
                var first_name = document.getElementById('first_name');
                var second_name = document.getElementById('second_name');
                var fullNameDiv = document.getElementById('full_name_div');
                // if (ctype === "C" || ctype === "G" || ctype === "N" || ctype === "S") {
                //     $('#salutation').hide();

                // } else {
                //     $('#salutation').show();
                // }
            });




            $(document).on('click', '.add-contact', function() {
                const lastContactSection = $('.contactsContainers').last();
                let prevCounter = lastContactSection.data('counter');

                const contact_name = $(`#contact_name-${prevCounter}`).val()
                const email = $(`#email-${prevCounter}`).val()
                const phone_number = $(`#phone_number-${prevCounter}`).val()
                if (contact_name == null || contact_name == '' || contact_name == ' ') {
                    toastr.error('Please capture Contact Name', 'Incomplete data')
                    return false
                } else if (email == null || email == '' || email == ' ') {
                    toastr.error('Input Email', 'Incomplete data')
                    return false
                } else if (phone_number == null || phone_number == '' || phone_number == ' ') {
                    toastr.error('Input Mobile Phone Number', 'Incomplete data')
                    return false
                }
                let counter = prevCounter + 1;
                console.log('counter', counter);

                // Validate only contact details before adding a new contact form
                let isValid = true;

                $(".contact-form").last().find("input[req='required']").each(function() {
                    if ($(this).val().trim() === "") {
                        isValid = false;
                        $(this).addClass("is-invalid"); // Highlight the empty field
                    } else {
                        $(this).removeClass("is-invalid");
                    }
                });

                if (!isValid) {
                    alert("Please fill all required Contact Details before adding another.");
                    return;
                }

                // Append new contact form if validation passes
                $(document).find(`#contactsContainer`).append(`
                <div class="row mb-4 contactsContainers" data-counter="${counter}">
                    <x-OnboardingInputDiv>
                        <x-Input name="contact_name[]" id="contact_name-${counter}" placeholder="Enter name"
                            inputLabel="Contact Fullname" req="required" />
                        <div id="contact_name_results" class="dropdown-menu" style="display: none;"></div>
                        <div class="error-message" id="full_name_error"></div>
                    </x-OnboardingInputDiv>
                    <x-OnboardingInputDiv>
                        <x-EmailInput id="email-${counter}" name="email[]" req="required" inputLabel="Email Address"
                            placeholder="Enter email" />
                    </x-OnboardingInputDiv>
                    <x-OnboardingInputDiv>
                        <x-NumberInput id="phone_number-${counter}" name="phone_number[]" req="required" inputLabel="Mobile."
                            class="phone" placeholder="Enter phone number" />
                    </x-OnboardingInputDiv>
                    <div class="col-sm-3">
                        <Label>Telephone</Label>
                        <div class="input-group mb-3">
                            <input id="telephone-${counter}" class="form-control" name="telephone[]" class="telephone"
                                placeholder="Enter telephone number" />
                            <button class="btn btn-primary btn-danger remove-contact" type="button" id="remove-contact-${counter}"
                                data-counter="${counter}">
                                <i class="bx bx-minus"></i>
                            </button>
                        </div>
                    </div>

                </div>
                `);

            });
            $(document).on('click', '.remove-contact', function() {
                $(this).closest('.contactsContainers').remove();
            });
            $("#submit_edit").click(function(e) {
                e.preventDefault();

                let form = $("#leads_form");
                form.validate({
                    errorElement: 'span',
                    errorClass: 'text-danger small fst-italic',
                    highlight: function(element, errorClass) {},
                    unhighlight: function(element, errorClass) {}
                });

                let myform = document.getElementById("leads_form");
                let formData = new FormData(myform);


                if (form) {
                    Swal.fire({
                        title: "Do you want to edit this form?",
                        text: "Please confirm if you are ready to update this form.",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Update!",
                        cancelButtonText: "Cancel"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("#submit_edit").attr('disabled', true).text('Updating...');

                            $.ajax({
                                type: 'POST',
                                data: formData,
                                url: "{{ route('pipeline.create.opportunity') }}",
                                processData: false,
                                contentType: false,
                                success: function(res) {
                                    if (res.status == 1) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: res.message
                                        }).then(() => {
                                            window.location.href =
                                                "/leads_listing";
                                        });
                                    } else {
                                        $("#submit_edit").attr('disabled', false).text(
                                            'Submit');
                                        displayValidationErrors(res.errors);
                                    }
                                },
                                error: function() {
                                    $("#submit_edit").attr('disabled', false).text(
                                        'Submit');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops!',
                                        text: 'An error occurred. Please try again later.'
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'You can edit the form.',
                                text: 'Feel free to make changes before submitting.'
                            });
                        }
                    });
                }
            });

            function displayValidationErrors(errors) {
                $('.error-message').remove();
                $.each(errors, function(field, messages) {
                    var inputField = $(`[name="${field}"]`);
                    var errorMessage = '<div class="error-message">' + messages.join('<br>') + '</div>';
                    inputField.after(errorMessage);
                });
            }

            $('#cancel').click(function(e) {
                window.location.href = `/leads_listing`;

            })

            $('#premium').on('keyup', function(e) {
                let premiums = $(this).val()

                let premium = premiums.replace(/,/g, '');

                let division = $('#division option:selected').val();

                let income_rate = $('#division option:selected').attr('data-rate')


                if (division === '') {

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Please select a division.',
                        confirmButtonText: 'OK'
                    });
                } else {


                    if (!isNaN(premium) && premium > 0) {
                        let income = (income_rate * premium) / 100;

                        let formatValue = income.toString().replace(/\D/g, '');
                        let formatsValue = formatValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                        $("#income").val(formatsValue);

                        $('#income').attr('readonly', true);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Premium',
                            text: 'Please enter a valid premium amount.',
                            confirmButtonText: 'OK'
                        });
                    }
                }

            })

            $('#division').on('change', function() {
                $('#premium').trigger('change');
                let division = parseInt($('#division option:selected').val());

                if (division == 6) {
                    $('#narration').attr('req', 'required');
                } else {
                    $('#narration').attr('req', '');
                }

                $.ajax({
                    type: "GET",
                    data: {
                        'division': division
                    },
                    url: "{{ route('get_division_classes') }}",
                    success: function(resp) {
                        if (resp.status == 1) {
                            $('#insurance_class').empty();
                            $('#insurance_class').append($("<option />").val('').text(
                                'Select class'));

                            $.each(resp.classes, function() {
                                $('#insurance_class').append($("<option />").val(this
                                    .id).text(this
                                    .class_name));
                            });

                        }
                    }
                });
            });



        })
    </script>
@endpush
