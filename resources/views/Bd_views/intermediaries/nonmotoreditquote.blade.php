@extends('layouts.intermediaries.base')

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>NON-MOTOR POLICY</h4>
        </div>
        <div class="card-body p-4 step">
            <h6 class="text-start">Client details</h6>
            <hr>
            <div class="row">
                <x-QuotationInputDiv>
                    <x-Input name="fname" id="" inputLabel="Full Name" req=""
                        value="{{ $clientdtls->full_name }}" readonly />
                </x-QuotationInputDiv>

                <x-QuotationInputDiv>
                    <x-Input name="email" id="" inputLabel="Email" req=""
                        value="{{ $clientdtls->email }}" readonly />
                </x-QuotationInputDiv>

                <x-QuotationInputDiv>
                    <x-Input name="phone" id="" inputLabel="Phone Number" req=""
                        value="{{ $clientdtls->phone_1 }}" readonly />
                </x-QuotationInputDiv>
            </div>
            <div id="location_details">
                <h6 class="text-start mt-3">Cover details</h6>
                <hr>
                <form id="loc_details" class="needs-validation" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <x-QuotationInputDiv>
                            <x-SelectInput name="brk_company" id="brk_company" req="required" inputLabel="Company">
                                <option value="" selected disabled>Select Company</option>
                                <option value="CIC">CIC Insurance</option>
                            </x-SelectInput>
                        </x-QuotationInputDiv>
                        <input type="text" name="batch_no" id="batch_no" hidden>
                        <input type="text" name="quote_no" id="quote_no" value="{{ $quotedtls->quote_no }}" hidden>

                        <x-QuotationInputDiv>
                            <x-SearchableSelect name="classbs" id="classbs" req="required" inputLabel="Class">
                                <option value="">Select class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->class_code }}" bypass_loc="{{ $class->bypass_location }}"
                                        earthquake="{{ $class->earthquake }}" bond="{{ $class->bond }}"
                                        engineering="{{ $class->engineering }}" travel="{{ $class->travel }}"
                                        @if ($class->class_code == $quotedtls->class) selected @endif>
                                        {{ $class->class_description }}</option>
                                @endforeach
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>
                        <input type='hidden' id="client_no" value="{{ $clientdtls->lob_customer_id }}" name="client_no" />
                        <x-QuotationInputDiv>
                            <x-SelectInput name="plan" id="plan" req="required" inputLabel="Plan">
                                <option value="">Select plan</option>
                                <option value="A" @if ('A' == $quotedtls->ast_marker) selected @endif>Annual</option>
                                <option value="S" @if ('S' == $quotedtls->ast_marker) selected @endif>Short term</option>
                            </x-SelectInput>
                        </x-QuotationInputDiv>


                        <x-QuotationInputDiv>
                            <x-NumberInput name="cover_days" id="cover_days" value="{{ $quotedtls->cover_days }}"
                                placeholder="Enter cover days" inputLabel="Cover Days" req="required" />
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-DateInput name="eff_date" id="eff_date" placeholder="Enter effective date"
                                inputLabel="Effective Date" req="required" />
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-SearchableSelect class="select2" name="currency" id="currency" req="required"
                                inputLabel="Currency">
                                <option value="">Select Currency</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->currency_code }}"
                                        shortcode="{{ $currency->short_description }}">
                                        {{ $currency->description }}</option>
                                @endforeach
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>
                    </div>

                    <div style="display:none" id="location_header">
                        <h6 class="text-start mt-3">Location details</h6>
                        <hr>
                    </div>

                    <div style="display:none" id="location_div">
                        <div class="row">
                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="location" id="location" placeholder="Enter location"
                                    inputLabel="Location" req="required" onkeyup="this.value=this.value.toUpperCase()" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="plot" id="plot" placeholder="Enter plot number"
                                    inputLabel="Plot Number" req="required" onkeyup="this.value=this.value.toUpperCase()" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="town" id="town" placeholder="Enter town"
                                    inputLabel="Town" req="required" onkeyup="this.value=this.value.toUpperCase()" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="street" id="street" placeholder="Enter street"
                                    inputLabel="Street" req="required" onkeyup="this.value=this.value.toUpperCase()" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-SelectInput class="loc_det" name="earthquake" id="earthquake" req="required"
                                    inputLabel="Apply Earthquake">
                                    <option value="">Select an option</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </x-SelectInput>
                            </x-QuotationInputDiv>

                            <div class="col-md-6  mt-2">
                                <x-TextArea class="loc_det" name="locdescription" id="locdescription"
                                    inputLabel="Description" req="required"
                                    onkeyup="this.value=this.value.toUpperCase()">

                                </x-TextArea>
                            </div>
                        </div>
                    </div>

                    <div style="display:none" id="project_details">
                        <h6 class="text-start mt-3">Project details</h6>
                        <hr>
                        <div class="row">
                            <x-QuotationInputDiv>
                                <x-TextArea class="proj_det" name="project_nature" id="project_nature"
                                    inputLabel="Project Nature" req="required"
                                    onkeyup="this.value=this.value.toUpperCase()">

                                </x-TextArea>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-TextArea class="proj_det" name="contractor_details" id="contractor_details"
                                    inputLabel="Contractor Details" req="required"
                                    onkeyup="this.value=this.value.toUpperCase()">

                                </x-TextArea>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-TextArea class="proj_det" name="principal" id="principal" inputLabel="Principal"
                                    req="required" onkeyup="this.value=this.value.toUpperCase()">

                                </x-TextArea>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="proj_det" name="principal_address" id="principal_address"
                                    inputLabel="Principal Address" req="required"
                                    onkeyup="this.value=this.value.toUpperCase()" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="proj_det" name="principal_town" id="principal_town"
                                    inputLabel="Principal Town" req="required"
                                    onkeyup="this.value=this.value.toUpperCase()" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="proj_det" name="site" id="site" inputLabel="Construction Site"
                                    req="required" onkeyup="this.value=this.value.toUpperCase()" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="proj_det" name="maintenance_period" id="maintenance_period"
                                    inputLabel="Maintenance Period(Months)" req="required"
                                    onkeyup="this.value=this.value.toUpperCase()" />
                            </x-QuotationInputDiv>

                        </div>
                    </div>
                    <div style="display:none" class="row display_location">
                        <div class="col d-flex justify-content-start">
                            <x-button.next type="button" class="btn btn-sm float-end mb-2" id="add_location_div">Add
                                Location</x-button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </div>


                        <table class="table table- striped  table-hover" id="risks_data_table" width="100%">
                            <thead class="bg-secondary text-white">
                                <tr>
                                    <td>Location</td>
                                    <td>Town</td>
                                    <td>Street</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </form>
            </div>
        </div>

        <div class="p-4 step" style="display: none">
            <div id="bond_details">
                <div class="card-body">
                    <h5 class="text-start my-2">Bond details</h5>
                    <hr>

                    <form id="bond_details_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-2">
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="bond_det" name="bond_type" id="bond_type" req="required"
                                    inputLabel="Bond Type">
                                    <option value="">Select bond type</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                            <div class="col-md-9">
                                <x-TextArea onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="bond_desc" id="bond_desc" inputLabel="Bond Description" req="required">

                                </x-TextArea>
                            </div>
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer"
                                    id="employer" inputLabel="Employer" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="employer_addr1" id="employer_addr1" inputLabel="Employer Address 1"
                                    req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="employer_addr2" id="employer_addr2" inputLabel="Employer Address 2"
                                    req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="employer_addr3" id="employer_addr3" inputLabel="Employer Address 3"
                                    req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal"
                                    id="principal" inputLabel="Principal" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="principal_addr1" id="principal_addr1" inputLabel="Principal Address 1"
                                    req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="principal_addr2" id="principal_addr2" inputLabel="Principal Address 2"
                                    req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="principal_addr3" id="principal_addr3" inputLabel="Principal Address 3"
                                    req="required" />
                            </x-QuotationInputDiv>
                        </div>

                        <hr>
                        <div class="row">
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety"
                                    id="surety" inputLabel="Surety" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="surety_addr2" id="surety_addr2" inputLabel="Surety Address 2"
                                    req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="surety_addr3" id="surety_addr3" inputLabel="Surety  Address 3"
                                    req="required" />
                            </x-QuotationInputDiv>
                        </div>
                        <hr>
                        <div class="row">
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="project_bond" id="project_bond" inputLabel="Project" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="bond_det" name="valid_days" id="valid_days"
                                    inputLabel="Valid Days" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-DateInput class="bond_det" name="signing_date" id="signing_date"
                                    inputLabel="Signing Date" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="bond_det" name="currency_bond" id="currency_bond"
                                    req="required" inputLabel="Currency">
                                    <option value="">Select Currency</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->currency_code }}"
                                            shortcode="{{ $currency->short_description }}"
                                            @if ($currency->base_currency == 'Y') selected @endif>
                                            {{ $currency->description }}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="currency_rate" id="currency_rate" inputLabel="Currency Rate" req="required"
                                    readonly />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="bond_det" name="bond_per" id="bond_per"
                                    inputLabel="% Contract Amount" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="bond_det" name="contract_amt" id="contract_amt"
                                    inputLabel="Contract Amount" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="bond_det" name="bond_sum" id="bond_sum" inputLabel="Sum Insured"
                                    req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="bond_det" name="loc_equiv" id="loc_equiv"
                                    inputLabel="Local Equivalent" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="bond_det" name="bond_security" id="bond_security"
                                    req="required" inputLabel="Security Type">
                                    <option value="">Select security type</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="security_desc" id="security_desc" inputLabel="Security Description"
                                    req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="bond_det" name="security_val" id="security_val"
                                    inputLabel="Security Value" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det"
                                    name="signatority" id="signatority" inputLabel="Signatority" req="required" />
                            </x-QuotationInputDiv>
                        </div>


                        <x-QuotationInputDiv>
                            <x-button.next type="button" id="finish_bond" class="mt-2">Finish</x-button>
                        </x-QuotationInputDiv>
                    </form>
                </div>
            </div>

            <div id="section_details">
                <div class="card-body">
                    <h5 class="text-start my-2">Section details</h5>
                    <hr>
                    <form id="section_details_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div id="sectionrows">
                                <div class="row" style="margin-top: 10px;" id="section0">
                                    <div class="col-md-3">
                                        <x-SelectInput name="locations[]" id="locations" req="required"
                                            inputLabel="Select Location" class="locsect locations">
                                            <option selected disabled>Select Location</option>
                                            @foreach ($locationdtls as $location)
                                                <option value="{{ $location->location }}"
                                                    @if (!empty($sectiondtls)) @if ($sectiondtls->location == $location->location) selected @endif
                                                    @endif >{{ $location->name }}</option>
                                            @endforeach
                                        </x-SelectInput>
                                    </div>
                                    <div class="col-md-3">
                                        <x-SearchableSelect name="classgrp[]" id="classgrp_0" req="required"
                                            inputLabel="Group Section" class="locsect classgroup">
                                            <option selected value="">Select Group Section</option>
                                        </x-SearchableSelect>
                                    </div>

                                    <div class="col-md-3">
                                        <x-SearchableSelect name="section[]" id="section_0" req="required"
                                            inputLabel="Section" class="grpsection locsect">
                                            <option selected value="">Select Section</option>
                                        </x-SearchableSelect>
                                    </div>

                                    <div class="col-md-1">
                                        <x-NumberInput name="units[]" id="units_0" data-counter="0" value="1"
                                            inputLabel="Units" class="locsect units" req="required" />
                                    </div>

                                    <div class="col-md-1">
                                        @if (!empty($sectiondtls))
                                            <x-NumberInput name="rate[]" id="rate_0" data-counter="0"
                                                value="{{ $sectiondtls->rate }}" inputLabel="Rate" class="locsect rate"
                                                req="required" />
                                        @else
                                            <x-NumberInput name="rate[]" id="rate_0" data-counter="0"
                                                inputLabel="Rate" class="locsect rate" req="required" />

                                        @endif
                                    </div>

                                    <div class="col-md-2">
                                        @if (!empty($sectiondtls))
                                            <x-Input name="sum_insured[]" id="sum_insured_0" inputLabel="Sum Insured"
                                                value="{{ $sectiondtls->sum_insured }}" class="locsect sectionsum"
                                                req="required" />
                                        @else
                                            <x-Input name="sum_insured[]" id="sum_insured_0" inputLabel="Sum Insured"
                                                class="locsect sectionsum" req="required" />
                                        @endif
                                    </div>

                                    <div class="premium col-md-2">
                                        <label for="premium">Premium</label>
                                        <div class="input-group">
                                            <input name="premium[]" id="premium_0" required value=""
                                                class="form-control prem field locsect" readonly />
                                            <span class="btn btn-primary" id="add_section">&plus;</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div class="mt-3 card-body">
                <div class="row">
                    <div class="col-md-9">
                        <!-- benefits -->
                        <div class="card" id="benefit_card">
                            <div class="card-header">
                                Optional Benefits
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <x-SelectInput class="locations" name="sec_location" id="sec_location"
                                            req="" inputLabel="Select Location to apply Benefit">
                                            <option selected disabled>Select Location</option>
                                            @foreach ($locationdtls as $location)
                                                <option value="{{ $location->location }}"
                                                    @if (!empty($sectiondtls)) @if ($sectiondtls->location == $location->location) selected @endif
                                                    @endif>{{ $location->name }}</option>
                                            @endforeach
                                        </x-SelectInput>
                                    </div>
                                </div>

                                <table class="table" id="opt_benefit">
                                    <thead>
                                        <tr>
                                            <th scope="col">Benefit</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Select</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <br>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header">
                                Premium Summary
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm">
                                        <div>
                                            <small>Currency</small>
                                        </div>
                                    </div>

                                    <div class="col-sm">
                                        <div>
                                            <h6 class="text-success font-weight-bold"><span
                                                    id="selcurr">{{ $curren->currency }}</span> </h6>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm">
                                        <div>
                                            <small>Basic Premium</small>
                                        </div>
                                    </div>

                                    <div class="col-sm">
                                        <div>
                                            <h6 class="text-success font-weight-bold text-align-right"><span
                                                    id="basic_premium"></span> </h6>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm">
                                        <div>
                                            <small>Benefit Amount</small>
                                        </div>
                                    </div>

                                    <div class="col-sm">
                                        <div>
                                            <h6 class="text-success font-weight-bold"><span id="benefit_total"></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm">
                                        <div>
                                            <small>Discount</small>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div>
                                            <h6 class="text-success font-weight-bold"><span id="discount_total">0</span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm">
                                        <div>
                                            <small>Total Premium</small>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div>
                                            <h6 class="text-success font-weight-bold"><span id="total_premium"></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form id="premiumdetails">
                            <input type="text" name="total_prem" id="total_prem_field" hidden>
                            <input type="text" name="discount" id="discount" hidden>
                            <input type="text" name="client_no" value="{{ $clientdtls->lob_customer_id }}" hidden>
                            <!-- <input type="text" name="total_prem" id="total_prem_field" hidden> -->
                            <input class="form-control" type="text" id="basic_prem" name="basic_prem" hidden>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="card-body p-5 step" style="display: none">
            <h3 class="fw-light text-center text-lg-start">Upload supporting documents</h3>
            <hr>

            <div class="col-md-8" style="border: 1px solid red">
                <ul>
                    <li>
                        <i>Please upload all the documents</i>
                    </li>
                    <li>
                        <i>Documents once uploaded cannot be changed when the quotation is saved </i>
                    </li>
                </ul>
            </div>
            <br>

            <form id="attach_docs">
                @csrf
                <div class="col-md-8">

                    @foreach ($docs as $doc)
                        <label for="">{{ $doc->doc_name }}</label>
                        <input class="form-control checkempty" type="file" name="doc_name[]">
                        <input class="form-control mb-4" name="doc_type[]" type="text" value="{{ $doc->id }}"
                            hidden>
                    @endforeach
                </div>
            </form>
        </div>

        <div class="card-footer">
            <x-button.back style="display: none" class="col-2 back">Back</x-button>
                <x-button.next type="button" class="next col-2 float-end" id="next">Next</x-button>
                    <x-button.submit type="button" class="save_loc col-2 float-end" id="save_loc"
                        style="display: none">Save Location</x-button>
                        <x-button.submit type="button" class="submit col-2 float-end" style="display: none"
                            id="buy_save">Submit</x-button>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        var step = 1;
        $(document).ready(function() {

            let batch_serial = "BN" + Math.floor(100000 + Math.random() * 900000)
            $('#batch_no').val(batch_serial)
            stepProgress(step);
            $('.loc_det').removeClass('checkempty')

            $(".next").on("click", function() {
                console.log(22)
                var nextstep = false;
                console.log(step);


                if (step == 1) {
                    // nextstep = checkForm("loc_details");
                    nextstep = true;
                } else if (step == 2) {
                    nextstep = true;
                    // nextstep = checkForm("section_details_form");
                } else if (step == 4) {
                    nextstep = checkForm("attach_docs");
                } else {
                    nextstep = true;
                }

                if (nextstep == true) {
                    if (step < $(".step").length) {
                        $(".step").show();
                        $(".step")
                            .not(":eq(" + step++ + ")")
                            .hide();
                        stepProgress(step);
                    }
                    hideButtons(step);
                }
            });

            // ON CLICK BACK BUTTON
            $(".back").on("click", function() {
                if (step > 1) {
                    step = step - 2;
                    $(".next").trigger("click");
                }
                hideButtons(step);
            });

            function removeCommas(str) {
                while (str.search(",") >= 0) {
                    str = (str + "").replace(',', '');
                }
                return str;
            };



            // CALCULATE PROGRESS BAR
            function stepProgress(currstep) {
                var percent = parseFloat(100 / $(".step").length) * currstep;
                percent = percent.toFixed();
                $(".progress-bar")
                    .css("width", percent + "%")
                    .html(percent + "%");
            };

            // DISPLAY AND HIDE "NEXT", "BACK" AND "SUMBIT" BUTTONS
            function hideButtons(step) {
                var limit = parseInt($(".step").length);
                $(".action").hide();
                if (step < limit) {
                    $(".next").show();
                }
                if (step > 1) {
                    $(".back").show();
                }
                if (step == limit) {
                    $(".next").hide();
                    $(".submit").show();
                }
            };

            function checkForm(val) {
                // CHECK IF ALL "REQUIRED" FIELD ALL FILLED IN
                var valid = true;
                $("#" + val + " .checkempty").each(function() {
                    if ($(this).val() === "") {
                        $(this).addClass("is-invalid");
                        valid = false;
                    } else {
                        $(this).removeClass("is-invalid");
                    }
                });

                console.log(valid);
                return valid;
            }


            var i = 0

            $('#add_section').on('click', function(e) {
                i++;

                if (i > 1) {
                    var classgrp = $('#classgrp_' + (i - 1)).val()
                    var section = $('#section_' + (i - 1)).val()
                    var rate = $('#rate_' + (i - 1)).val()
                    var sum_insured = $('#sum_insured_' + (i - 1)).val()
                } else if (i == 1) {
                    var classgrp = $('#classgrp_0').val()
                    var section = $('#section_0').val()
                    var rate = $('#rate_0').val()
                    var sum_insured = $('#sum_insured_0').val()
                }
                if (classgrp == '' || section == '' || rate == '' || sum_insured == '') {
                    i--;
                    Swal.fire({
                        icon: 'warning',
                        text: 'Please fill all details'
                    });
                } else {
                    $('#sectionrows').append(
                        '<div class="row" style="margin-top: 10px;" id="section' + i + '">' +
                        '<div class="col-md-3">' +
                        '<label>Location</label>' +
                        '<select name="locations[]" id="locations_' + i +
                        '" class="form-control" required>' +
                        '<option selected value="">Select location</option>' +
                        '</select>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<label>Group Section</label>' +
                        '<select name="classgrp[]" id="classgrp_' + i +
                        '" class="form-control select2 classgroup" required>' +
                        '<option selected value="">Select Group Section</option>' +
                        '@foreach ($classgrp as $grp)' +
                        '<option value="{{ $grp->classgrp }}"  rate="{{ $grp->rate }}">{{ $grp->group_description }}</option>' +
                        '@endforeach' +
                        '</select>' +
                        '</div>' +
                        '<div class="col-md-3" >' +
                        '<label>Section</label>' +
                        '<select name="section[]" id="section_' + i +
                        '" class="form-control select2 grpsection" required>' +
                        '<option selected value="">Select Section</option>' +
                        '</select>' +
                        '</div>' +
                        '<div  class="col-md-1">' +
                        '<label>Units</label>' +
                        '<input type="number" name="units[]" id="units_' + i +
                        '" data-counter="0" value="1" class="form-control units" >' +
                        '</div>' +
                        '<div  class="col-md-1">' +
                        '<label>Rate</label>' +
                        '<input type="number" name="rate[]" id="rate_' + i +
                        '" data-counter="0" class="form-control rate" >' +
                        '</div>' +
                        '<div class="col-md-2">' +
                        '<label>Sum Insured</label>' +
                        '<div class="input-group">' +
                        '<input type="text" name="sum_insured[]"  id="sum_insured_' + i +
                        '" class="form-control sectionsum">' +
                        // '<span class="btn btn-danger" id="remove_section">&minus;</span>'+
                        '</div>' +
                        '</div>' +
                        '<div class="premium col-md-2">' +
                        '<label>Premium</label>' +
                        '<div class="input-group">' +
                        '<input type="text" name="premium[]" id="premium_' + i +
                        '" value=""  class="premfield form-control" readonly>' +
                        '<span class="btn btn-danger" id="remove_section">&minus;</span>' +
                        '</div>' +
                        '</div>' +
                        '</div>'
                    )
                    let classbs = $('#classbs').val()
                    getClassgroups(classbs, "#classgrp_" + i);
                }
            });

            $('body').on('click', '#remove_section', function() {
                $(this).parent().parent().parent().remove();
                sumPremium()
            })

            $('#sectionrows').on('change', 'input, select', function() {
                console.log("change");
                sumPremium()
            })

            $('body').on('click', '.add_benefit', function() {
                $(this).parents('div.section').removeClass("border border-bottom")
                $(this).parents('div.section').addClass("d-none").append('')
                let ben_id = $(this).attr('benefit_id');
                let ben_desc = $(this).attr('benefit_desc');
                let ben_amount = $(this).parents('div.section').find('.benefit_amount').text()
                console.log(ben_amount);

                let basic = $('#basic_premium').text()
                let total = $('#total_premium').text()
                $("#basic_prem").val(basic)

                total = Number(total) + Number(ben_amount)

                $('#total_premium').text(numberWithCommas(total))
                $('#total_prem_field').val(total)

                $('#ext').css('display', 'block')
                $('#extensions').children('.alert').addClass('d-none')
                $('#extensions').append(
                    '<div class="d-flex border border-bottom justify-content-between text-align-center added_benefit"><div><div class="added_desc">' +
                    ben_desc + '</div><div class="fw-bold text-success added_amount">' + ben_amount +
                    '</div></div><div class="float-right"><button class="btn btn-sm remove_benefit" ben_id="' +
                    ben_id + '"><span class="fa fa-times"></span></button></div></div>')
                $('#premiumdetails').append('<input class="benefit_' + ben_id +
                    '" name="extensions[]" value="' + ben_amount + '" hidden>')
                $('#premiumdetails').append('<input class="benefit_' + ben_id +
                    '" name="ext_types[]" value="' + ben_id + '" hidden>')
            })

            $('body').on('click', '.remove_benefit', function() {
                $(this).parents('div.added_benefit').removeClass("border border-bottom")
                $(this).parents('div.added_benefit').addClass("d-none")

                let ben_id = $(this).attr('ben_id')
                $(".benefit_" + ben_id).remove()

                let ben_desc = $(this).parents('div.added_benefit').find('.added_desc').text()
                let ben_amount = $(this).parents('div.added_benefit').find('.added_amount').text()
                // console.log(ben_desc);

                if ($('#extensions').children(':visible.added_benefit').length < 1) {
                    $('#extensions').append('<div class="alert alert-info">No added benefits</div>')
                }

                let total = $('#total_premium').text()

                total = Number(total) - Number(ben_amount)

                $('#total_premium').text(total)
                $('#total_prem_field').val(total)


                $(':hidden.section').each(function() {
                    let hidden_desc = $(this).find('.benefit_desc').text()
                    if ($.trim(hidden_desc) === $.trim(ben_desc)) {
                        $(this).removeClass("d-none")
                        $(this).addClass("d-block border border-bottom")
                    }


                });
            })


            $('.sectionsum').on('change', function(e) {
                let sumins = $(this).val()
                $(this).val(numberWithCommas(sumins))
            })


            $('#buy_save').on('click', function(e) {
                $(this).attr('disabled', 'disabled')
                e.preventDefault();

                let form_data = new FormData();
                let premium_det = new FormData(document.getElementById('section_details_form'));
                let docs = new FormData(document.getElementById('attach_docs'));
                let prem = new FormData(document.getElementById('premiumdetails'));
                let bond = new FormData(document.getElementById('bond_details_form'));
                let location_det = new FormData(document.getElementById('loc_details'));

                for (var [key, value] of location_det.entries()) {
                    form_data.append(key, value);
                }
                for (var [key, value] of bond.entries()) {
                    form_data.append(key, value);
                }
                for (var [key, value] of docs.entries()) {
                    form_data.append(key, value);
                }
                for (var [key, value] of premium_det.entries()) {
                    form_data.append(key, value);
                }
                for (var [key, value] of prem.entries()) {
                    form_data.append(key, value);
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('process_nm_quote') }}",
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        if (resp.status == 1) {
                            swal.fire({
                                icon: "success",
                                title: "Quotation details send",
                                text: "Quotations has been registered pending approval"
                            })
                            window.location = "{{ route('Agent.view_quote', '') }}" + "/" +
                                resp.quote_no;
                        }
                    }
                })
            })

            function sumPremium() {
                let sum = 0

                $('.sectionsum').each(function(i, obj) {

                    var id = $(this).attr('id')
                    var id_length = id.length
                    var rowID = id.slice(11, id_length)
                    let sinsured = $(this).val()
                    sinsured = sinsured.replaceAll(',', '')
                    console.log(sinsured)
                    let rate = $('#rate' + rowID).val();
                    let prem = parseFloat(rate) * parseFloat(sinsured) / 100
                    if (prem > 0) {
                        $('#premium' + rowID).val(numberWithCommas(prem))
                        sum = sum + prem
                    }

                });

                let total = removeCommas($('#total_premium').text())

                $("#basic_premium").text(numberWithCommas(sum.toFixed(2)))
                $("#basic_prem").val(sum)
                $("#total_premium").text(numberWithCommas(sum.toFixed(2)))
                $('#total_prem_field').val(sum)

                // $('.benefit_amount').each(function(){
                //     let ben_rate = $(this).attr('benefit_rate');
                //     let benefit_amount = sum*ben_rate/100;

                //     $(this).text(benefit_amount);
                // });
            }

            $("#currency").change(function() {
                var curr = $(this).find('option:selected');
                var currency_code = curr.attr("shortcode");

                $('#selcurr').text(currency_code);
            });

            $('#classbs').on('change', function() {
                let classbs = $(this).val()
                let bypass_location = $("option:selected", this).attr('bypass_loc');
                let earthquake = $("option:selected", this).attr('earthquake');
                let bond = $("option:selected", this).attr('bond');
                if (bypass_location == 'N') {
                    $('#location_header').css('display', 'block')
                    $('.display_location').css('display', 'block')
                    $('.loc_det').addClass('checkempty')
                } else {
                    $('#location_header').css('display', 'none')
                    $('.display_location').css('display', 'none')
                    $('.loc_det').removeClass('checkempty')
                }

                if (earthquake == 'Y') {
                    $('#earthquake').attr('disabled', false)
                } else {
                    $('#earthquake').attr('disabled', true)
                    $('#earthquake').val('N')
                }

                let project_det = $("option:selected", this).attr('engineering');

                if (project_det == 'Y') {
                    $('#project_details').css('display', 'block')
                    $('.proj_det').addClass('checkempty')
                } else {
                    $('#project_details').css('display', 'none')
                    $('.proj_det').removeClass('checkempty')
                }


                if (bond == 'Y') {
                    $('#section_details').css('display', 'none')
                    $('#bond_details').css('display', 'block')
                    $('.bond_det').addClass('checkempty')
                    $('.locsect').removeClass('checkempty')

                    $.ajax({
                        type: "GET",
                        data: {
                            'class': classbs
                        },
                        url: "{{ route('get_bond_types') }}",
                        success: function(resp) {
                            if (resp.status == 1) {
                                $('#bond_type').empty()
                                $('#bond_type').append($("<option />").val('').text(
                                    'Select bond type'));
                                $.each(resp.bonds, function() {
                                    $('#bond_type').append($("<option />").val(this
                                        .bond_type).text(this.description));
                                });
                            }
                        }
                    })
                } else {
                    $('#section_details').css('display', 'block')
                    $('#bond_details').css('display', 'none')
                    $('.locsect').addClass('checkempty')
                    $('.bond_det').removeClass('checkempty')
                }
                getClassgroups(classbs, '#classgrp_0');



            })

            let complete_flag = "{{ $quotedtls->complete_flag }}"
            if (complete_flag == "N") {
                $('#classbs').trigger('change')
                $('#next').trigger('click')
                if (step == 2) {
                    $(".back").hide()
                }
            }


            $('#finish_bond').on('click', function() {
                $('#section_details').css('display', 'block')
                $('.locsect').addClass('checkempty')
            })
            $('#add_location_div').on('click', function() {

                let project_det = $("option:selected", '#classbs').attr('engineering');
                let bypass_location = $("option:selected", '#classbs').attr('bypass_loc');

                if (project_det == 'Y') {
                    $('#project_details').css('display', 'block')
                    $('.proj_det').addClass('checkempty')
                } else {
                    $('#project_details').css('display', 'none')
                    $('.proj_det').removeClass('checkempty')
                }

                if (bypass_location == 'N') {
                    $('#location_header').css('display', 'block')
                    $('.display_location').css('display', 'block')
                    $('.loc_det').addClass('checkempty')
                } else {
                    $('#location_header').css('display', 'none')
                    $('.display_location').css('display', 'none')
                    $('.loc_det').removeClass('checkempty')
                }

                $('#location_div').css('display', 'block')
                $('.loc_det').addClass('checkempty')
                $('.display_location').css('display', 'none')
                $('#save_loc').show()
                $('.next').hide()

            });

            function getClassgroups(classbs, selector) {

                var sectiondtls = "{{ json_encode($sectiondtls) }}"
                var decodedJson = $("<div/>").html(sectiondtls).text(); // Decodes the HTML-encoded JSON string
                var jsonData = JSON.parse(decodedJson);
                if (jsonData !== undefined && jsonData !== null) {

                    sectiondtls = jsonData.class_grp
                } else {
                    sectiondtls = ""
                }

                $.ajax({
                    type: "GET",
                    data: {
                        'class': classbs
                    },
                    url: "{{ route('get_class_grp') }}",
                    success: function(resp) {
                        if (resp.status == 1) {
                            $(selector).empty()
                            $(selector).append($("<option />").val('').text('Select Section Group'));
                            $.each(resp.grps, function() {
                                if (sectiondtls == this.classgrp) {
                                    $(selector).append($("<option />").val(this.classgrp).text(
                                        this.group_description).prop("selected", true));
                                } else {
                                    $(selector).append($("<option />").val(this.classgrp).text(
                                        this.group_description));

                                }


                            });
                        }
                        $(".classgroup").trigger('change');
                    }
                })

                // $.ajax({
                //     type: 'GET',
                //     data:{'cls':classbs},
                //     url: "{!! route('agent.fetchnmsections') !!}",
                //     success:function(data){
                //         console.log(data)
                //             $("#opt_benefit tbody").empty()
                //         // $("#covtype").append($("<option />").val('').text('Choose cover type'));
                //         $.each(data, function() {
                //             console.log(this.grp_code)
                //             $("#opt_benefit tbody").append(
                //                 '<tr class="section">'
                //                     + '<td>'
                //                         +'<div class="benefit_amt">'
                //                             +'<div class="d-block font-weight-bold benefit_desc">'
                //                                 + this.ext_description
                //                             +'</div>'
                //                         +'</div>'
                //                     +'</td>'
                //                     +'<td align="right">'
                //                         +'<div class="benefit_amt">'
                //                             +'<div class="d-block">'
                //                                 +`<span class="benefit_amount"  benefit_rate=${this.rate} basis=${this.rate_basis}></span>`
                //                             +'</div>'
                //                         +'</div>'
                //                     +'</td>'
                //                     +'<td align="right">'
                //                         +'<div class="form-check">'
                //                             +`<input class="form-check-input add_benefit" type="checkbox" value="" benefit_id=${this.item_code} benefit_desc=${this.description} benefit_amount="">`
                //                         +'</div>'
                //                     +'</td>'
                //                 +'</tr>'
                //                 )
                //         });
                //     }
                // });
            }

            // $('.classgroup').on('change', function(){
            //     console.log("ian");
            //     let grp = $(this).val()
            //     let classbs = $('#classbs').val()
            //     var id = $(this).attr('id')
            //     var id_length = id.length
            //     var rowID = id.slice(8, id_length)
            //     console.log(rowID);

            //     $.ajax({
            //         type: "GET",
            //         data: {'class': classbs, 'classgrp': grp},
            //         url: "{{ route('get_class_sections') }}",
            //         success:function(resp){
            //             if (resp.status == 1) {
            //                 $("#section"+rowID).empty()
            //                 $("#section"+rowID).append($("<option />").val('').text('Select Section'));
            //                 $.each(resp.sections, function() {
            //                     $("#section"+rowID).append($("<option />").val(this.section_no).text(this.description).attr('rate', this.rate).attr('min_rate', this.minimum_rate));
            //                 });
            //             }
            //         }
            //     })

            // })


            $('#sectionrows').on('change', '.classgroup', function() {
                var sectiondtls = "{{ json_encode($sectiondtls) }}"
                var decodedJson = $("<div/>").html(sectiondtls)
            .text(); // Decodes the HTML-encoded JSON string
                var jsonData = JSON.parse(decodedJson);
                if (jsonData !== undefined && jsonData !== null) {


                    sectiondtls = jsonData.section_code
                } else {
                    sectiondtls = ""
                }
                console.log("ian");
                let grp = $(this).val()
                let classbs = $('#classbs').val()
                var id = $(this).attr('id')
                var id_length = id.length
                var rowID = id.slice(8, id_length)
                console.log(rowID);

                $.ajax({
                    type: "GET",
                    data: {
                        'class': classbs,
                        'classgrp': grp
                    },
                    url: "{{ route('get_class_sections') }}",
                    success: function(resp) {
                        if (resp.status == 1) {
                            $("#section" + rowID).empty()
                            $("#section" + rowID).append($("<option />").val('').text(
                                'Select Section'));
                            $.each(resp.sections, function() {
                                if (sectiondtls == this.section_no) {
                                    $("#section" + rowID).append($("<option />").val(
                                            this.section_no).text(this.description)
                                        .prop("selected", true).attr('rate', this
                                            .rate).attr('min_rate', this
                                            .minimum_rate));

                                } else {
                                    $("#section" + rowID).append($("<option />").val(
                                            this.section_no).text(this.description)
                                        .attr('rate', this.rate).attr('min_rate',
                                            this.minimum_rate));

                                }
                            });
                        }
                    }
                })


            })
            // $('.classgroup').trigger('change')

            // $('.grpsection').on('change', function(){
            //     let rate = $("option:selected", this).attr('rate')
            //     let min_rate = $("option:selected", this).attr('min_rate')
            //     // let min_rate = $(this).attr('min_rate')
            //     var id = $(this).attr('id')
            //     var id_length = id.length
            //     var rowID = id.slice(7, id_length)
            //     console.log(rate,min_rate);

            //     $('#rate'+rowID).val(rate)
            //     $('#rate'+rowID).attr('min', min_rate)

            // })

            $('#sectionrows').on('change', '.grpsection', function() {
                let rate = $("option:selected", this).attr('rate')
                let min_rate = $("option:selected", this).attr('min_rate')
                // let min_rate = $(this).attr('min_rate')
                var id = $(this).attr('id')
                var id_length = id.length
                var rowID = id.slice(7, id_length)
                console.log(rate, min_rate);

                $('#rate' + rowID).val(rate)
                $('#rate' + rowID).attr('min', min_rate)

            })

            $('#sectionrows').on('change', '.rate', function() {
                console.log("rate");
                let rate = $(this).val()
                let min_rate = $(this).attr('min')

                if (parseFloat(rate) < parseFloat(min_rate)) {
                    swal.fire({
                        icon: "warning",
                        text: "Minimum rate is " + min_rate
                    })
                }
                $(this).val(min_rate)

            })



            $('#plan').on('change', function() {
                let plan = $(this).val();
                if (plan == 'A') {
                    $('#cover_days').attr('readonly', 'readonly');
                    $('#cover_days').val(365);
                } else {
                    $('#cover_days').removeAttr('readonly');
                    $('#cover_days').val(0);
                }
            });

            $('#risks_data_table').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    'url': '{{ route('get.quote.nmrisks') }}',
                    'data': function(d) {
                        var quote = $('#quote_no').val()
                        d.quote_no = quote
                    },
                },

                columns: [{
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'plot_no',
                        name: 'plot_no'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });
            $('#save_loc').on('click', function() {

                let data = $('#loc_details').serialize()
                $.ajax({
                    type: 'GET',
                    data: data,
                    url: "{!! route('agent.stage_location_process') !!}",
                    success: function(data) {
                        console.log(data)
                        if (data.status == 1) {
                            $(".locations").empty()
                            $(".locations").append($('<option>', {
                                value: '',
                                text: 'Select Location',
                                disabled: true,
                                selected: true
                            }));
                            $.each(data.locations, function() {
                                $(".locations").append($("<option />").val(this
                                    .location).text(this.name));
                            });
                            $('#quote_no').val(data.quote_no)
                            $('#location_div').hide()
                            $('.display_location').show()
                            // $('#est_value').val(data.value)
                            // calculateBenefitAmount(data.value)
                            $('#save_loc').hide()
                            $('.next').show()

                            swal.fire({
                                icon: "success",
                                title: "Success",
                                html: "<h6 class='text-success'>Quotation</h6> " + data
                                    .quote_no +
                                    " <h6 class='text-success'>Generated Continue with the process</h6>"
                            })
                            var table = $('#risks_data_table').DataTable()
                            table.ajax.reload();

                        } else if (data.status == 2) {
                            $('#location_div').hide()
                            $('.display_location').show()
                            $('#save_loc').hide()
                            $('.next').show()
                            swal.fire({
                                icon: "error",
                                title: "Error",
                                html: "<h6 class='text-success'>Location Already Exists </h6>"
                            })

                        } else {
                            $('#location_div').hide()
                            $('.display_location').show()
                            $('#save_loc').hide()
                            $('.next').show()
                            swal.fire({
                                icon: "error",
                                title: "Error",
                                html: "<h6 class='text-success'>Something Went Wrong , Try again </h6>"
                            })

                        }



                    }
                });

            });



        });
        $('#risks_data_table').on('click', '.deletedetails', function(e) {
            e.preventDefault()
            var itemno = $(this).closest('tr').find('td:eq(0)').text();
            var batch_no = $('#batch_no').val();
            Swal.fire({
                title: "Warning!",
                html: "Are You Sure You Want to delete this Location?",
                icon: "warning",
                confirmButtonText: "Yes"
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        data: {
                            'itemno': itemno,
                            'batch_no': batch_no
                        },
                        url: "{!! route('agent.delete.nmrisk') !!}",
                        success: function(response) {
                            // $('#est_value').val(response.value)
                            // calculateBenefitAmount(response.value)
                            Swal.fire({
                                title: "Success",
                                text: "Deleted Successfully",
                                icon: "success"
                            });
                            var table = $('#risks_data_table').DataTable()
                            table.ajax.reload();
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


        });
        $('#sec_location').on('change', function() {

            let sec_id = $(this).val()
            let classbs = $('#classbs').val()
            let quote_no = $('#quote_no').val()
            $.ajax({
                type: 'GET',
                data: {
                    'cls': classbs,
                    'quote_no': quote_no,
                    'location': sec_id
                },
                url: "{!! route('agent.fetchnmsections') !!}",
                success: function(data) {
                    console.log(data)
                    $("#opt_benefit tbody").empty()
                    // $("#covtype").append($("<option />").val('').text('Choose cover type'));
                    $.each(data.sections, function() {
                        console.log(this.grp_code)
                        var exist = $.inArray(this.ext_code, data.choosen);
                        var checkbox = null
                        if (exist != -1) {

                            checkbox =
                                `<input class="form-check-input remove_nm_benefit" type="checkbox" value="" benefit_id=${this.ext_code} benefit_desc=${this.ext_description} benefit_amount=""  benefit_rate=${this.rate} checked>`
                        } else {
                            checkbox =
                                `<input class="form-check-input add_nm_benefit" type="checkbox" value="" benefit_id=${this.ext_code} benefit_desc=${this.ext_description} benefit_amount=""  benefit_rate=${this.rate} >`

                        }
                        console.log(exist)
                        $("#opt_benefit tbody").append(
                            '<tr class="section">' +
                            '<td>' +
                            '<div class="benefit_amt">' +
                            '<div class="d-block font-weight-bold benefit_desc">' +
                            this.ext_description +
                            '</div>' +
                            '</div>' +
                            '</td>' +
                            '<td align="right">' +
                            '<div class="benefit_amt">' +
                            '<div class="d-block">' +
                            `<span class="benefit_amount"  benefit_rate=${this.rate} basis=${this.rate_basis}></span>` +
                            '</div>' +
                            '</div>' +
                            '</td>' +
                            '<td align="right">' +
                            '<div class="form-check">' +
                            checkbox +
                            '</div>' +
                            '</td>' +
                            '</tr>'
                        )
                    });
                    $('.benefit_amount').each(function() {
                        let vehicle_value = 0;
                        let ben_rate = $(this).attr('benefit_rate');
                        let basis = $(this).attr('basis');
                        alert(basis)
                        if (basis == "L" || basis == "S") {
                            vehicle_value = removeCommas($('#sum_insured_0').val())
                        } else if (basis == "B") {
                            vehicle_value = removeCommas($('#premium_0').val())

                        }


                        let benefit_amount = vehicle_value * ben_rate / 100;
                        alert(benefit_amount);

                        $(this).text(numberWithCommas(benefit_amount));
                        // $("#benefit_total").text(numberWithCommas(benefit_amount))



                    });
                }
            });
        })
        $('body').on('click', '.add_nm_benefit', function() {


            $(this).removeClass("add_nm_benefit")
            $(this).addClass("remove_nm_benefit")
            // $(this).html('<span class="fa fa-times"></span>');
            let ben_id = $(this).attr('benefit_id');
            let ben_desc = $(this).attr('benefit_desc');
            let rate = $(this).attr('benefit_rate');
            let ben_amount = removeCommas($(this).parents('tr.section').find('.benefit_amount').text())
            let qtebasic = removeCommas($('#basic_premium').text())
            let qtetotal = $('#total_premium').text()
            let regno = $('#rsk_reg').text()


            let basic = removeCommas($('#basic_premium').text())
            let total = removeCommas($('#total_premium').text())
            //$("#basic_prem").val(basic)
            console.log(ben_amount, basic, total);

            total = Number(total) + Number(ben_amount)
            qtetotal = Number(qtetotal) + Number(ben_amount)

            $('#total_prem_field').val(total)
            $('#total_premium').text(numberWithCommas(total))
            $('#qtotal').text(qtetotal)
            $('#benefit_total').text(numberWithCommas(Number(total) - Number(basic)))
            //$('#total_prem_field').val(total)

            //$('#ext_risk').css('display', 'block')
            $('#risk_extensions').children('.alert').addClass('d-none')
            $('#risk_extensions').append(
                '<div class="d-flex border border-bottom justify-content-between text-align-center added_benefit"><div><div class="added_desc">' +
                ben_desc + '</div><div class="fw-bold text-success added_amount">' + ben_amount +
                '</div></div><div class="float-right"><button class="btn btn-sm remove_risk_benefit" ben_id="' +
                ben_id + '"><span class="fa fa-times"></span></button></div></div>')
            $('#premiumdetails').append('<input class="benefit_' + ben_id + '" name="rsk_extensions[]" value="' +
                ben_amount + '" hidden>')
            $('#premiumdetails').append('<input class="benefit_' + ben_id + '" name="rsk_ext_types[]" value="' +
                ben_id + '" hidden>')
            $('#premiumdetails').append('<input class="benefit_' + ben_id + '" name="rsk_ext_reg[]" value="' +
                regno + '" hidden>')
            var quote_no = $('#quote_no').val()
            var loc = $('#sec_location').val()

            $.ajax({
                type: 'GET',
                data: {
                    'ben_id': ben_id,
                    'ben_desc': ben_desc,
                    'ben_amount': ben_amount,
                    'location': loc,
                    'quote_no': quote_no,
                    'rate': rate
                },
                url: "{!! route('stage.nm_ben') !!}",
                success: function(data) {


                }
            });
        })
        $('body').on('click', '.remove_nm_benefit', function() {

            $(this).removeClass("remove_nm_benefit")
            $(this).addClass("add_nm_benefit")

            let ben_id = $(this).attr('benefit_id')
            var quote_no = $('#quote_no').val()
            $(".benefit_" + ben_id).remove()
            $.ajax({
                type: 'GET',
                data: {
                    'ben_id': ben_id,
                    'delete_flag': "Y",
                    'quote_no': quote_no
                },
                url: "{!! route('stage.nm_ben') !!}",
                success: function(data) {

                }
            });

            $(".benefit_" + ben_id).remove()

            let ben_amount = removeCommas($(this).parents('tr.section').find('.benefit_amount').text())
            let ben_desc = $(this).attr('benefit_desc');
            // console.log(ben_desc);

            if ($('#extensions').children(':visible.added_benefit').length < 1) {
                $('#extensions').append('<div class="alert alert-info">No added benefits</div>')
            }

            let qtebasic = $('#basic_premium').text()
            let qtetotal = $('#total_premium').text()
            let regno = $('#rsk_reg').text()
            console.log(ben_amount);

            let basic = removeCommas($('#basic_premium').text())
            let total = removeCommas($('#total_premium').text())
            //$("#basic_prem").val(basic)

            total = Number(total) - Number(ben_amount)
            qtetotal = Number(qtetotal) - Number(ben_amount)
            $('#total_prem_field').val(total)
            $('#rsk_tprem').text(total)
            $('#total_premium').text(numberWithCommas(total))
            $('#qtotal').text(qtetotal)
            $('#benefit_total').text(numberWithCommas(Number(total) - Number(basic)))


            $(':hidden.section').each(function() {
                let hidden_desc = $(this).find('.benefit_desc').text()
                if ($.trim(hidden_desc) === $.trim(ben_desc)) {
                    $(this).removeClass("d-none")
                    $(this).addClass("d-block border border-bottom")
                }


            });

        })

        function numberWithCommas(num) {
            //var amt=num.toFixed(2);
            return num.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
        }
        $('#sec_location').trigger('change')
    </script>
@endsection
