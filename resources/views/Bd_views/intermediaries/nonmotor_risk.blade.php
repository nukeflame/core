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
                    <x-Input name="fname" id="" inputLabel="Full Name" req=""  value="{{$clientdtls->full_name}}" readonly/>
                </x-QuotationInputDiv>

                <x-QuotationInputDiv>
                    <x-Input name="email" id=""  inputLabel="Email" req=""  value="{{$clientdtls->email}}" readonly/>
                </x-QuotationInputDiv>

                <x-QuotationInputDiv>
                    <x-Input name="phone" id=""  inputLabel="Phone Number" req=""  value="{{$clientdtls->phone_1}}" readonly/>
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
                            <input type="hidden" name="class_type" val="">
                        </x-QuotationInputDiv>
                        <input type="text" name="batch_no" id="batch_no" hidden>
                        <input type="text" name="quote_no" id="quote_no" hidden>

                        <x-QuotationInputDiv>
                            <x-SearchableSelect name="classbs" id="classbs" req="required" inputLabel="Class" >
                                <option value="">Select class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->class_code }}" bypass_loc="{{$class->bypass_location}}" earthquake="{{$class->earthquake}}"
                                    bond="{{$class->bond}}" engineering="{{$class->engineering}}" travel="{{$class->travel}}" marine="{{$class->marine}}"
                                    git="{{$class->git}}">
                                        {{ $class->class_description }}</option> 
                                @endforeach
                            </x-SearchableSelect>
                        </x-QuotationInputDiv>

                        <input type='hidden'  id="client_no" value="{{ $clientdtls->lob_customer_id }}" name="client_no" />
                        <x-QuotationInputDiv>
                            <x-SelectInput name="plan" id="plan" req="required" inputLabel="Plan">
                                <option value="">Select plan</option>
                                <option value="A">Annual</option>
                                <option value="S">Short term</option>
                            </x-SelectInput>
                        </x-QuotationInputDiv>
                        
                        
                        <x-QuotationInputDiv>
                            <x-NumberInput name="cover_days" id="cover_days"  placeholder="Enter cover days"  inputLabel="Cover Days" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-DateInput name="eff_date" id="eff_date"  placeholder="Enter effective date"  inputLabel="Effective Date" req="required"/>
                        </x-QuotationInputDiv>

                        <x-QuotationInputDiv>
                            <x-SearchableSelect  class="select2" name="currency" id="currency" req="required" inputLabel="Currency">
                                <option value="">Select Currency</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->currency_code }}" shortcode="{{$currency->short_description }}">
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
                                <x-Input class="loc_det" name="location" id="location"  placeholder="Enter location"  inputLabel="Location" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>
                        
                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="plot" id="plot"  placeholder="Enter plot number"  inputLabel="Plot Number" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                                <input type="hidden" name="bypasslocation" value="" id="bypasslocation">
                                <input type="hidden" name="engineering_project" value="" id="engineering_project">
                            </x-QuotationInputDiv>
                        
                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="town" id="town"  placeholder="Enter town"  inputLabel="Town" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="loc_det" name="street" id="street"  placeholder="Enter street"  inputLabel="Street" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-SelectInput class="loc_det" name="earthquake" id="earthquake" req="required" inputLabel="Apply Earthquake">  
                                    <option value="">Select an option</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </x-SelectInput>
                            </x-QuotationInputDiv>

                            <div class="col-md-6  mt-2">
                                <x-TextArea class="loc_det" name="locdescription" id="locdescription"  inputLabel="Description" req="required" onkeyup="this.value=this.value.toUpperCase()">

                                </x-TextArea>
                            </div>
                        </div>
                    </div>
                    
                    <div  style="display:none" id="project_details">
                        <h6 class="text-start mt-3">Project details</h6>
                        <hr>
                        <div class="row">
                            <x-QuotationInputDiv>
                                <x-TextArea class="proj_det" name="project_nature" id="project_nature"  inputLabel="Project Nature" req="required" onkeyup="this.value=this.value.toUpperCase()">

                                </x-TextArea>
                            </x-QuotationInputDiv>
                        
                            <x-QuotationInputDiv>
                                <x-TextArea class="proj_det" name="contractor_details" id="contractor_details"  inputLabel="Contractor Details" req="required" onkeyup="this.value=this.value.toUpperCase()">

                                </x-TextArea>
                            </x-QuotationInputDiv>
                        
                            <x-QuotationInputDiv>
                                <x-TextArea class="proj_det" name="principal" id="principal"  inputLabel="Principal" req="required" onkeyup="this.value=this.value.toUpperCase()">

                                </x-TextArea>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="proj_det" name="principal_address" id="principal_address"  inputLabel="Principal Address" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="proj_det" name="principal_town" id="principal_town"  inputLabel="Principal Town" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="proj_det" name="site" id="site"  inputLabel="Construction Site" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="proj_det" name="maintenance_period" id="maintenance_period"  inputLabel="Maintenance Period(Months)" req="required" onkeyup="this.value=this.value.toUpperCase()"/>
                            </x-QuotationInputDiv>

                        </div>
                    </div>

                    <div style="display:none" class="row display_location">
                        <div class="col d-flex justify-content-start">
                            <x-button.next type="button" class="btn btn-sm float-end mb-2" id="add_location_button">Add Location</x-button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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

        <div class="p-4 step"  style="display: none">
            <div id="bond_details">
                <div class="card-body">
                    <h5 class="text-start my-2">Bond details</h5>
                    <hr>

                    <form id="bond_details_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-2">
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="bond_det" name="bond_type" id="bond_type" req="required" inputLabel="Bond Type">
                                    <option value="">Select bond type</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                            <div class="col-md-9">
                                <x-TextArea onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="bond_desc" id="bond_desc"  inputLabel="Bond Description" req="required">

                                </x-TextArea>
                            </div>
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer" id="employer"  inputLabel="Employer" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer_addr1" id="employer_addr1"  inputLabel="Employer Address 1" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer_addr2" id="employer_addr2"  inputLabel="Employer Address 2" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="employer_addr3" id="employer_addr3"  inputLabel="Employer Address 3" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal" id="principal"  inputLabel="Principal" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal_addr1" id="principal_addr1"  inputLabel="Principal Address 1" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal_addr2" id="principal_addr2"  inputLabel="Principal Address 2" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="principal_addr3" id="principal_addr3"  inputLabel="Principal Address 3" req="required"/>
                            </x-QuotationInputDiv>
                        </div>

                        <hr>
                        <div class="row">
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety" id="surety"  inputLabel="Surety" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety_addr2" id="surety_addr2"  inputLabel="Surety Address 2" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="surety_addr3" id="surety_addr3"  inputLabel="Surety  Address 3" req="required"/>
                            </x-QuotationInputDiv>
                        </div>

                        <hr>
                        <div class="row">
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="project_bond" id="project_bond"  inputLabel="Project" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="bond_det" name="valid_days" id="valid_days"  inputLabel="Valid Days" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-DateInput class="bond_det" name="signing_date" id="signing_date"  inputLabel="Signing Date" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="bond_det" name="bond_per" id="bond_per"  inputLabel="Percentage Contract Amount" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="bond_det" name="contract_amt" id="contract_amt"  inputLabel="Contract Amount" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="bond_det" name="bond_sum" id="bond_sum"  inputLabel="Sum Insured" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="bond_det" name="loc_equiv" id="loc_equiv"  inputLabel="Local Equivalent" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="bond_det" name="bond_security" id="bond_security" req="required" inputLabel="Security Type">
                                    <option value="">Select security type</option>
                                    <option value="L" >Logbook</option>
                                    <option value="T" >Title deed</option>
                                    <option value="C" >Cash deposit</option>
                                    <option value="O" >Other security type</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="security_desc" id="security_desc"  inputLabel="Security Description" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="bond_det" name="security_val" id="security_val"  inputLabel="Security Value" req="required" />
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="bond_det" name="signatority" id="signatority"  inputLabel="Signatority" req="required"/>
                            </x-QuotationInputDiv>
                        </div>
                    </form>
                </div>
            </div>

            
            <div id="marine_open_details">
                <div class="card-body">
                    <h5 class="text-start my-2">Marine Declaration</h5>
                    <hr>

                    <form id="marine_open_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-NumberInput class="marine_dec" name="item_no" id="item_no"  inputLabel="Item Number" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="product_group" id="product_group" req="required" inputLabel="Product Group">
                                    <option value="">Select product group</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="marine_dec" name="cargo_type" id="cargo_type"  inputLabel="Cargo Type" req="required"/>
                            </x-QuotationInputDiv>

                            <div class="col-md-9">
                                <x-TextArea onkeyup="this.value=this.value.toUpperCase()" class="marine_dec" name="marine_desc" id="marine_desc"  inputLabel="Description" req="required">

                                </x-TextArea>
                            </div>
                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <h6>Mode of cover</h6>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="radio" value="icca" name="icc" class="icc"/> I.C.C (A)
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="radio" value="iccb" name="icc" class="icc"/> I.C.C (B)
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="radio" value="iccc" name="icc" class="icc"/> I.C.C (C)
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="checkbox" name="war" id="war" value="Y" /> WAR
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="checkbox" name="srrc" id="srcc" value="Y" /> S.R.R.C
                            </x-QuotationInputDiv>
                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <h6>Mode of conveyance</h6>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <input type="radio" name="conveyance" id="dc_mode_air" value="mode_air" /> AIR
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="radio" name="conveyance" id="dc_mode_sea" value="mode_sea" /> SEA
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                Rate
                                <input type="text" name="air_sea_rate" id="air_sea_rate" value=""/> 
                            </x-QuotationInputDiv>

                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="marine_dec" name="survey_agent" id="survey_agent"  inputLabel="Survey Agent" req="required"/>
                            </x-QuotationInputDiv>


                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="package_type" id="package_type" req="required" inputLabel="Packaging Type">
                                    <option value="">Select packaging type</option>
                                    @foreach($packtypes as $type)
                                        <option value="{{$type->pack_type}}">{{$type->description}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="source" id="source" req="required" inputLabel="Source">
                                    <option value="">Select source</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->iso}}">{{$port->iso}}-{{$port->nicename}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="loading_at" id="loading_at" req="required" inputLabel="Loading At">
                                    <option value="">Select loading at</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="destination" id="destination" req="required" inputLabel="Destination">
                                    <option value="">Select destination</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->iso}}">{{$port->iso}}-{{$port->nicename}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="port_of_discharge" id="port_of_discharge" req="required" inputLabel="Port of Discharge">
                                    <option value="">Select port of discharge</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-Input onkeyup="this.value=this.value.toUpperCase()" class="marine_dec" name="vessel_name" id="vessel_name"  inputLabel="Vessel" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="transhipment" id="transhipment" req="required" inputLabel="Transhipment">
                                    <option value="">Select transhipment</option>
                                    <option value="N">NO</option>
                                    <option value="Y">YES</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="transhipment_country" id="transhipment_country" req="required" inputLabel=" Transhipment Country">
                                    <option value="">Select destination</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->iso}}">{{$port->iso}}-{{$port->nicename}}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="marine_dec" name="transhipment_port" id="transhipment_port" req="required" inputLabel="Transhipment Destination">
                                    <option value="">Select port</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>
                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="bill_of_landing" id="bill_of_landing"  inputLabel="Bill of Landing No" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-DateInput class="marine_dec" name="bill_of_landing_date" id="bill_of_landing_date"  inputLabel="Issue Date (Bill of Landing)" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-DateInput class="marine_dec" name="invoice_date" id="invoice_date"  inputLabel="Invoice Date" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-DateInput class="marine_dec" name="date_signed" id="date_signed"  inputLabel="Date Signed" req="required"/>
                            </x-QuotationInputDiv>
                        </div>

                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="insurance_value" id="insurance_value"  inputLabel="Value of Insurance" req="required" />
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="clearing_charges" id="clearing_charges"  inputLabel="Clearing Charges & Internal Freight" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="customs_duty" id="customs_duty"  inputLabel="Customs Duty" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="vat" id="vat"  inputLabel="Value Added Tax(VAT)" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="profit" id="profit"  inputLabel="Profit(% of C&F)" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="profit_amount" id="profit_amount"  inputLabel="Profit Amount" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="marine_dec" name="total_sum_insured" id="total_sum_insured"  inputLabel="Total Sum Insured" req="required"/>
                            </x-QuotationInputDiv>
                        </div>
                    </form>
                </div>
            </div>

            <div id="git_details">
                <div class="card-body">
                    <h5 class="text-start my-2">Goods In Transit Declaration</h5>
                    <hr>

                    <form id="git_form" enctype="multipart/form-data">
                        @csrf
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="business" id="business"  inputLabel="Business" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="physical_location" id="physical_location" req="required" inputLabel="Physical Location"/>
                            </x-QuotationInputDiv>
                        </div>
                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <input type="checkbox" name="one_off_transit" id="one_off_transit" /> One off transit
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="checkbox" name="road" id="road_transport" /> Road Transportation
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <input type="checkbox" name="rail" id="rail_transport" /> Rail Transportation
                            </x-QuotationInputDiv>
                        </div>

                        <hr>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-NumberInput class="git_dec" name="limit_of_liability" id="limit_of_liability"  inputLabel="Limit of Liability" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="git_dec" name="est_annual_carry" id="est_annual_carry"  inputLabel="Estimated Annua Carry" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-SearchableSelect class="git_dec" name="package_type" id="package_type" req="required" inputLabel="Package Type">
                                    <option value="">Select package type</option>
                                    <option value="N">Containerized</option>
                                    <option value="Y">Uncontainerized</option>
                                </x-SearchableSelect>
                            </x-QuotationInputDiv>

                            <div class="col-md-9">
                                <x-Input class="git_dec" name="goods_descr" id="goods_descr"  inputLabel="Description of Goods Carried" req="required"/>
                            </div>
                        </div>
                        <div class="row m-2">
                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="loading_location" id="loading_location"  inputLabel="Loading location" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="unloading_location" id="unloading_location"  inputLabel="Unloading Location" req="required"/>
                            </x-QuotationInputDiv>

                            <x-QuotationInputDiv>
                                <x-NumberInput class="git_dec" name="territory" id="territory"  inputLabel="Territorial Limit" req="required"/>
                            </x-QuotationInputDiv>
                        </div>
                        
                        <hr>
                        <div class="row">
                            <h6>Freight vehicle details (optional)</h6>
                            <x-QuotationInputDiv>
                                <x-Input class="git_dec" name="veh_reg_no" id="veh_reg_no"  inputLabel="Vehicle Reg No" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-NumberInput class="git_dec" name="veh_value" id="veh_value"  inputLabel="Value Of Vehicle" req="required"/>
                            </x-QuotationInputDiv>
                            
                            <x-QuotationInputDiv>
                                <x-NumberInput class="git_dec" name="veh_max_load" id="veh_max_load"  inputLabel="Maximum Load for Vehicle. (kgs)" req="required"/>
                            </x-QuotationInputDiv>
                        </div>
                        <hr> 
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
                                        <x-SelectInput name="locations[]" id="locations" req="required" inputLabel="Select Location" class="locsect locations">
                                            <option value="">Location</option>
                                        </x-SelectInput>
                                    </div>
                                    <div class="col-md-3">
                                        <x-SearchableSelect name="classgrp[]" id="classgrp_0" req="required" inputLabel="Group Section" class="locsect classgroup">
                                            <option selected value="">Select Group Section</option>
                                        </x-SearchableSelect>
                                    </div>

                                    <div class="col-md-3" >
                                        <x-SearchableSelect name="section[]" id="section_0" req="required" inputLabel="Section" class="grpsection locsect">
                                            <option selected value="">Select Section</option>
                                        </x-SearchableSelect>
                                    </div>

                                    <div  class="col-md-1">
                                        <x-NumberInput name="units[]" id="units_0" data-counter="0" value="1" inputLabel="Units" class="locsect units" req="required" />
                                    </div>

                                    <div  class="col-md-1">
                                        <x-NumberInput  name="rate[]" id="rate_0" data-counter="0" value="" inputLabel="Rate" class="locsect rate" req="required" />
                                    </div>

                                    <div class="col-md-2">
                                        <x-Input name="sum_insured[]"  id="sum_insured_0"  inputLabel="Sum Insured" class="locsect sectionsum" req="required" />
                                    </div>
                                    
                                    <div class="premium col-md-2">
                                        <label for="premium">Premium</label>
                                        <div class="input-group">
                                            <input  name="premium[]" id="premium_0" required value=""  class="form-control premfield locsect" readonly />
                                            <span class="btn btn-primary" id="add_section">&plus;</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> 

        <div class="p-4 step"  style="display: none">
            <div class="mt-3 card-body">
                <div class="row">
                    <div class="col-md-6">
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
                                            <h6 class="text-success font-weight-bold"><span id="selcurr"></span> </h6>
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
                                            <h6 class="text-success font-weight-bold text-align-right"><span id="basic_premium"></span> </h6>
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
                                            <h6 class="text-success font-weight-bold" ><span id="benefit_total"></span> </h6>
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
                                            <h6 class="text-success font-weight-bold" ><span id="discount_total">0</span> </h6>
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
                                            <h6 class="text-success font-weight-bold"><span id="total_premium"></span></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form id="premiumdetails">
                            <input type="text" name="total_prem" id="total_prem_field" hidden>
                            <input type="text" name="discount" id="discount" hidden>
                            <input type="text" name="client_no"  value="{{$clientdtls->lob_customer_id}}" hidden>
                            <!-- <input type="text" name="total_prem" id="total_prem_field" hidden> -->
                            <input class="form-control" type="text" id="basic_prem" name="basic_prem" hidden>
                        </form>
                    </div>

                    <div class="col-md-6">
                        <!-- benefits -->
                        <div class="card" id="benefit_card">
                            <div class="card-header">
                                Optional Benefits
                            </div>
                            <div class="card-body">
                                <div class="row" id="benefit_per_location">
                                    <div class="col-md-4">
                                        <x-SelectInput class="locations" name="sec_location" id="sec_location" req="" inputLabel="Select Location to apply Benefit">
                                            <option selected disabled>Location</option>
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
                        <label for="">{{$doc->doc_name}}</label>
                        <input class="form-control checkempty" type="file" name="doc_name[]" >
                        <input class="form-control mb-4" name="doc_type[]" type="text" value="{{ $doc->id }}" hidden>
                                 
                    @endforeach
                </div>
            </form>
        </div>
      
        <div class="card-footer">
            <x-button.back style="display: none" class="col-2 back" >Back</x-button>
            <x-button.next type="button" class="next col-2 float-end">Next</x-button>
            <x-button.submit type="button" class="save_loc col-2 float-end" id="save_loc" style="display: none">Save Location</x-button>
            <x-button.submit type="button" class="submit col-2 float-end" style="display: none" id="buy_save">Submit</x-button>
        </div>
    </div>
@endsection

@section('page_scripts')
<script>
    var step = 1;
$(document).ready(function () {
    let batch_serial ="BN"+ Math.floor(100000 + Math.random() * 900000)
    $('#batch_no').val(batch_serial)
     stepProgress(step); 
    $('.loc_det').removeClass('checkempty')

    $(".next").on("click", function () {
        var nextstep = false;
        console.log(step);
        

        if (step == 1) {
            // nextstep = checkForm("loc_details");
            nextstep = true;
        }
        else if(step == 2) {
            nextstep = true;
            // nextstep = checkForm("section_details_form");
        }
        else if (step == 4) {
            nextstep = checkForm("attach_docs");
        }
        else{
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
    $(".back").on("click", function () {
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
function stepProgress (currstep) {
  var percent = parseFloat(100 / $(".step").length) * currstep;
  percent = percent.toFixed();
  $(".progress-bar")
    .css("width", percent + "%")
    .html(percent + "%");
};

// DISPLAY AND HIDE "NEXT", "BACK" AND "SUMBIT" BUTTONS
function hideButtons (step) {
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
  $("#" + val + " .checkempty").each(function () {
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

$('#add_section').on('click', function(e){
    i++;


    if (i > 1) {
        var classgrp = $('#classgrp_' + (i-1)).val()
        var section = $('#section_' + (i-1)).val()
        var rate = $('#rate_' + (i-1)).val()
        var sum_insured = $('#sum_insured_' + (i-1)).val()
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
            '<div class="row" style="margin-top: 10px;" id="section'+i+'">'+
                '<div class="col-md-3">'+
                    '<label>Location</label>'+
                    '<select name="locations[]" id="locations_'+i+'" class="form-control" required>'+
                        '<option selected value="">Select location</option>'+
                    '</select>'+
                '</div>'+
                '<div class="col-md-3">'+
                    '<label>Group Section</label>'+
                    '<select name="classgrp[]" id="classgrp_'+i+'" class="form-control select2 classgroup" required>'+
                    '<option selected value="">Select Group Section</option>'+
                        '@foreach($classgrp as $grp)'+
                            '<option value="{{ $grp->classgrp }}"  rate="{{ $grp->rate }}">{{ $grp->group_description }}</option>'+
                        '@endforeach'+
                    '</select>'+
                '</div>'+
                '<div class="col-md-3" >'+
                    '<label>Section</label>'+
                    '<select name="section[]" id="section_'+i+'" class="form-control select2 grpsection" required>'+
                        '<option selected value="">Select Section</option>'+
                    '</select>'+
                '</div>'+
                '<div  class="col-md-1">'+
                    '<label>Units</label>'+
                    '<input type="number" name="units[]" id="units_'+i+'" data-counter="0" value="1" class="form-control units" >'+
                '</div>'+
                '<div  class="col-md-1">'+
                    '<label>Rate</label>'+
                    '<input type="number" name="rate[]" id="rate_'+i+'" data-counter="0" class="form-control rate" >'+
                '</div>'+
                '<div class="col-md-2">'+
                    '<label>Sum Insured</label>'+
                    '<div class="input-group">'+
                        '<input type="text" name="sum_insured[]"  id="sum_insured_'+i+'" class="form-control sectionsum">'+
                        // '<span class="btn btn-danger" id="remove_section">&minus;</span>'+
                    '</div>'+
                '</div>'+
                '<div class="premium col-md-2">'+
                    '<label>Premium</label>'+
                    '<div class="input-group">'+
                        '<input type="text" name="premium[]" id="premium_'+i+'" value=""  class="premfield form-control" readonly>'+
                        '<span class="btn btn-danger" id="remove_section">&minus;</span>'+
                    '</div>'+
                '</div>'+
            '</div>'
        )

        let classtype = $('#class_type').val()
        if (classtype == 'bond') {
            let id = "sum_insured_"+i
            $("#id").val($("#sum_insured_0").val())
            $("#id").attr('readonly', true)
        }

        let classbs = $('#classbs').val()
        getClassgroups(classbs, "#classgrp_"+i);
    }
});

$('body').on('click','#remove_section', function(){
    $(this).parent().parent().parent().remove();
    sumPremium()
})

$('#sectionrows').on('change', 'input, select', function(){
    console.log("change");
    sumPremium()
})

$('body').on('click','.add_benefit', function(){
    $(this).parents('div.section').removeClass("border border-bottom")
    $(this).parents('div.section').addClass("d-none").append('')
    let ben_id = $(this).attr('benefit_id');
    let ben_desc = $(this).attr('benefit_desc');
    let ben_amount =  $(this).parents('div.section').find('.benefit_amount').text()
    console.log(ben_amount);

    let basic = $('#basic_premium').text()
    let total = $('#total_premium').text()
    $("#basic_prem").val(basic)

    total = Number(total) + Number(ben_amount)

    $('#total_premium').text(numberWithCommas(total))
    $('#total_prem_field').val(total)
    
    $('#ext').css('display', 'block')
    $('#extensions').children('.alert').addClass('d-none')
    $('#extensions').append('<div class="d-flex border border-bottom justify-content-between text-align-center added_benefit"><div><div class="added_desc">' + ben_desc + '</div><div class="fw-bold text-success added_amount">'+ben_amount+'</div></div><div class="float-right"><button class="btn btn-sm remove_benefit" ben_id="'+ben_id+'"><span class="fa fa-times"></span></button></div></div>')
    $('#premiumdetails').append('<input class="benefit_'+ben_id+'" name="extensions[]" value="'+ben_amount+'" hidden>')
    $('#premiumdetails').append('<input class="benefit_'+ben_id+'" name="ext_types[]" value="'+ben_id+'" hidden>')
})

$('body').on('click','.remove_benefit', function(){
    $(this).parents('div.added_benefit').removeClass("border border-bottom")
    $(this).parents('div.added_benefit').addClass("d-none")
    
    let ben_id = $(this).attr('ben_id')
    $(".benefit_"+ben_id).remove()

    let ben_desc =  $(this).parents('div.added_benefit').find('.added_desc').text()
    let ben_amount =  $(this).parents('div.added_benefit').find('.added_amount').text()
    // console.log(ben_desc);
    
    if ($('#extensions').children(':visible.added_benefit').length < 1) {
        $('#extensions').append('<div class="alert alert-info">No added benefits</div>')
    }
    
    let total = $('#total_premium').text()

    total = Number(total) - Number(ben_amount)

    $('#total_premium').text(total)
    $('#total_prem_field').val(total)
    

    $(':hidden.section').each(function(){
        let hidden_desc = $(this).find('.benefit_desc').text()
        if($.trim(hidden_desc) === $.trim(ben_desc)){
            $(this).removeClass("d-none")
            $(this).addClass("d-block border border-bottom")
        }


    });
})


$('.sectionsum').on('change', function(e){   
    let sumins = $(this).val()
    $(this).val(numberWithCommas(sumins))
})


$('#buy_save').on('click', function(e){
    let bond = $("option:selected", "#classbs").attr('bond');
    let marine = $("option:selected", "#classbs").attr('marine');
    let git = $("option:selected", "#classbs").attr('git');
    $(this).attr('disabled', 'disabled')

    e.preventDefault();

    let form_data = new FormData();
    let premium_det = new FormData(document.getElementById('section_details_form'));
    let docs = new FormData(document.getElementById('attach_docs'));
    let prem = new FormData(document.getElementById('premiumdetails'));
    let bond_det = new FormData(document.getElementById('bond_details_form'));
    let location_det = new FormData(document.getElementById('loc_details'));
    let git_det = new FormData(document.getElementById('git_form'));
    let marine_det = new FormData(document.getElementById('marine_open_form'));
    
    for (var [key, value] of location_det.entries()) { 
        form_data.append(key, value);
    }
    if (marine == "Y") {
        for (var [key, value] of marine_det.entries()) { 
            form_data.append(key, value);
        }
    }
    if (git == "Y") {
        for (var [key, value] of git_det.entries()) { 
            form_data.append(key, value);
        }
    }
    if (bond == "Y") {
        for (var [key, value] of bond_det.entries()) { 
            form_data.append(key, value);
        }
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
        url: "{{ route('process_nm_quote')}}",
        data: form_data,
        contentType: false,
        processData: false,
        success:function(resp){
            if (resp.status == 1) {
                swal.fire({
                    icon: "success",
                    title: "Quotation details send",
                    text: "Quotations has been registered pending approval"
                })
                window.location="{{route('Agent.view_quote', '')}}"+"/"+resp.quote_no;
            }
        }
    })
})

function sumPremium(){
   let sum = 0

    $('.sectionsum').each(function(i, obj) {
        
        var id = $(this).attr('id')
        var id_length = id.length
        var rowID = id.slice(11, id_length)
        let sinsured = $(this).val()
        sinsured = sinsured.replaceAll(',', '')
        console.log(sinsured)
        let rate = $('#rate'+rowID).val();
        let prem = parseFloat(rate)* parseFloat(sinsured)/100
        if (prem > 0) {
            $('#premium'+rowID).val(numberWithCommas(prem))
            sum = sum + prem
        }
        
    });
    
    let total = removeCommas($('#total_premium').text())

    $("#basic_premium").text(numberWithCommas(sum.toFixed(2)))
    $("#basic_prem").val(sum)
    $("#total_premium").text(numberWithCommas(sum.toFixed(2)))
    $('#total_prem_field').val(sum)

    $('.benefit_amount').each(function(){
        let ben_rate = $(this).attr('benefit_rate');
        let benefit_amount = sum*ben_rate/100;

        $(this).text(benefit_amount);
    });
}

    $("#currency").change(function(){ 
        var curr = $(this).find('option:selected'); 
        var currency_code = curr.attr("shortcode"); 

        $('#selcurr').text(currency_code); 
    }); 

$('#classbs').on('change', function(){
    let classbs = $(this).val()
    let bypass_location = $("option:selected", this).attr('bypass_loc');
    let earthquake = $("option:selected", this).attr('earthquake');
    let bond = $("option:selected", this).attr('bond');
    let marine = $("option:selected", this).attr('marine');
    let git = $("option:selected", this).attr('git');
    let project_det = $("option:selected", this).attr('engineering');

    if (bypass_location == 'N') {
        $('#benefit_per_location').css('display', 'block')
        $('#bypasslocation').val('N')
        $('#location_header').css('display', 'block')
        $('#location_div').css('display', 'block')
        // $('.display_location').css('display', 'block')
        $('.loc_det').addClass('checkempty')
        $('#save_loc').show()
        $('#add_location_button').show()
        $('.next').hide()
    } else {
        $('#benefit_per_location').css('display', 'none')
        $('#bypasslocation').val('Y')
        $('#location_header').css('display', 'none')
        $('.display_location').css('display', 'none')
        $('#location_div').css('display', 'none')
        $('#project_details').css('display', 'none')
        $('.loc_det').removeClass('checkempty')
        $('.proj_det').removeClass('checkempty')
        $('#save_loc').show()
        $('.next').hide()
        $('#add_location_button').hide()
        $('#sec_location').trigger("change");
    }

    if (earthquake == 'Y') {
        $('#earthquake').attr('disabled', false)
    } else {
        $('#earthquake').attr('disabled', true)
        $('#earthquake').val('N')
    }

    if (project_det == 'Y') {
        $('#engineering_project').val('Y')
        $('#project_details').css('display', 'block')
    } else {
        $('#engineering_project').val('N')
        $('#project_details').css('display', 'none')
    }


    if (bond == 'Y') {
        $('#class_type').val('bond')
        $('#section_details').css('display', 'none')
        $('#bond_details').css('display', 'block')
        $('.bond_det').addClass('checkempty')
        $("#sum_insured_0").attr('readonly', true)
        // $('.locsect').removeClass('checkempty')

        $.ajax({
            type: "GET",
            data: {'class': classbs},
            url: "{{ route('get_bond_types')}}",
            success:function(resp){
                if (resp.status == 1) {
                    $('#bond_type').empty()
                    $('#bond_type').append($("<option />").val('').text('Select bond type'));
                    $.each(resp.bonds, function() {
                        $('#bond_type').append($("<option />").val(this.bond_type).text(this.description));
                    });
                }
            }
        })
    } else {
        $('#section_details').css('display', 'block')
        $('#bond_details').css('display', 'none')
        // $('.locsect').addClass('checkempty')
        $('.bond_det').removeClass('checkempty')
    }


    if (marine == 'Y') {
        $('#class_type').val('marine')
        $('#section_details').css('display', 'none')
        $('#marine_open_details').css('display', 'block')
        $('.marine_dec').addClass('checkempty')
        // $('.locsect').removeClass('checkempty')

        $.ajax({
            type: "GET",
            data: {'class': classbs},
            url: "{{ route('get_marine_groups')}}",
            success:function(resp){
                if (resp.status == 1) {
                    console.log("done");
                    $('#product_group').empty()
                    $('#product_group').append($("<option />").val('').text('Select product group'));
                    $.each(resp.margroups, function() {
                        $('#product_group').append($("<option />").val(this.group_code).text(this.group_code+" - "+this.description).attr('data-sea', this.sea_rate).attr('data-air', this.air_rate));
                    });
                }
            }
        })
    } else {
        $('#section_details').css('display', 'block')
        $('#marine_open_details').css('display', 'none')
        // $('.locsect').addClass('checkempty')
        $('.marine_dec').removeClass('checkempty')
    }

    
    if (git == 'Y') {
        $('#class_type').val('git')
        $('#git_details').css('display', 'block')
        $('.git_dec').addClass('checkempty')
    } else {
        $('#git_details').css('display', 'none')
        $('.git_dec').removeClass('checkempty')
    }


    getClassgroups(classbs,'#classgrp_0');



})

$('#finish_bond').on('click', function(){
    $('#section_details').css('display', 'block')
    $('.locsect').addClass('checkempty')
})

$('#add_location_button').on('click', function(){
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

function getClassgroups(classbs, selector){
    $.ajax({
        type: "GET",
        data: {'class': classbs},
        url: "{{ route('get_class_grp')}}",
        success:function(resp){
            if (resp.status == 1) {
                $(selector).empty()
                $(selector).append($("<option />").val('').text('Select Section Group'));
                $.each(resp.grps, function() {
                    $(selector).append($("<option />").val(this.classgrp).text(this.group_description));
                });
            }
        }
    })
    // $.ajax({
    //     type: 'GET',
    //     data:{'cls':classbs},
    //     url: "{!! route('agent.fetchnmsections')!!}",
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
//         url: "{{ route('get_class_sections')}}",
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


$('#sectionrows').on('change', '.classgroup', function(){
    console.log("ian");
    let grp = $(this).val()
    let classbs = $('#classbs').val()
    var id = $(this).attr('id')
    var id_length = id.length
    var rowID = id.slice(8, id_length)
    console.log(rowID);

    $.ajax({
        type: "GET",
        data: {'class': classbs, 'classgrp': grp},
        url: "{{ route('get_class_sections')}}",
        success:function(resp){
            if (resp.status == 1) {
                $("#section"+rowID).empty()
                $("#section"+rowID).append($("<option />").val('').text('Select Section'));
                $.each(resp.sections, function() {
                    $("#section"+rowID).append($("<option />").val(this.section_no).text(this.description).attr('rate', this.rate).attr('min_rate', this.minimum_rate));
                });
            }
        }
    })

})

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

$('#sectionrows').on('change', '.grpsection', function(){
    let rate = $("option:selected", this).attr('rate')
    let min_rate = $("option:selected", this).attr('min_rate')
    // let min_rate = $(this).attr('min_rate')
    var id = $(this).attr('id')
    var id_length = id.length
    var rowID = id.slice(7, id_length)
    console.log(rate,min_rate);

    $('#rate'+rowID).val(rate)
    $('#rate'+rowID).attr('min', min_rate)

})

$('#sectionrows').on('change', '.rate', function(){
    console.log("rate");
    let rate = $(this).val()
    let min_rate = $(this).attr('min')

    if (parseFloat(rate) < parseFloat(min_rate)) {
        swal.fire({
            icon: "warning",
            text: "Minimum rate is "+min_rate
        })
    }
    $(this).val(min_rate)

})


            
$('#plan').on('change', function() {
    let plan =$(this).val(); 
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
            ajax:{
                'url' : '{{ route("get.quote.nmrisks") }}',
                'data' : function(d){
                        var quote= $('#quote_no').val()
                        d.quote_no=quote
                    },
            },
            
            columns: [
                {data:'location',name:'location'},
                {data:'name',name:'name'},
                {data:'plot_no',name:'plot_no'},
                {data:'action',name:'action'},
                ]		
});
$('#save_loc').on('click', function(){
        
        let data = $('#loc_details').serialize()
        $.ajax({
            type: 'GET',
            data:data,
            url: "{!! route('agent.stage_location_process')!!}",
            success:function(data){
                console.log(data)
                if(data.status== 1){
                    $(".locations").empty()
                    $(".locations").append($('<option>', {
                                    value: '',
                                    text: 'Select Location',
                                    disabled: true,
                                    selected: true
                                    }));
                    $.each(data.locations, function() {
                        $(".locations").append($("<option />").val(this.location).text(this.name));
                    });
                    $('#quote_no').val(data.quote_no)
                    $('#location_div').hide()
                    $('.display_location').show()
                    
                    $('#project_details').css('display', 'none')
                    // $('#est_value').val(data.value)
                    // calculateBenefitAmount(data.value)
                    $('#save_loc').hide()
                    $('.next').show()
                    
                    swal.fire({
                            icon: "success",
                            title: "Success",
                            html:"<h6 class='text-success'>Quotation</h6> "+data.quote_no +" <h6 class='text-success'>Generated Continue with the process</h6>"
                    })
                    var table = $('#risks_data_table').DataTable()
                    table.ajax.reload();

                }else if(data.status == 2){
                    $('#location_div').hide()
                    $('.display_location').show()
                    $('#save_loc').hide()
                    $('.next').show()
                    swal.fire({
                            icon: "error",
                            title: "Error",
                            html:"<h6 class='text-success'>Location Already Exists </h6>"
                    })

                }else{
                    $('#location_div').hide()
                    $('.display_location').show()
                    $('#save_loc').hide()
                    $('.next').show()
                    swal.fire({
                            icon: "error",
                            title: "Error",
                            html:"<h6 class='text-success'>Something Went Wrong , Try again </h6>"
                    })

                }
             
             

            }
        });
        
    });



});
$('#risks_data_table').on('click', '.deletedetails', function(e) {
    e.preventDefault()
        var itemno = $(this).closest('tr').find('td:eq(0)').text();
        var batch_no =  $('#batch_no').val();
    Swal.fire({
        title: "Warning!",
        html: "Are You Sure You Want to delete this Location?",
        icon: "warning",
        confirmButtonText: "Yes"
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                type: 'GET',
                data:{'itemno':itemno,'batch_no':batch_no},
                url: "{!! route('agent.delete.nmrisk')!!}",
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
$('#sec_location').on('change', function(){
    let bypass_location = $("option:selected", "#classbs").attr('bypass_loc');
    
    let sec_id =$(this).val()
    if (bypass_location == 'Y') {
        sec_id = 1
    }
    let classbs = $('#classbs').val()
    let quote_no = $('#quote_no').val()
    $.ajax({
        type: 'GET',
        data:{'cls':classbs,'quote_no':quote_no,'location':sec_id},
        url: "{!! route('agent.fetchnmsections')!!}",
        success:function(data){
            console.log(data)
                $("#opt_benefit tbody").empty()
            // $("#covtype").append($("<option />").val('').text('Choose cover type'));
            $.each(data.sections, function() {
                console.log(this.grp_code)
                var exist = $.inArray(this.ext_code, data.choosen);
                var checked =null
                if(exist != -1){
                    checked="checked"
                }else{
                    checked =""
                }
                console.log(exist)
                $("#opt_benefit tbody").append(
                    '<tr class="section">'
                        + '<td>'
                            +'<div class="benefit_amt">'
                                +'<div class="d-block font-weight-bold benefit_desc">'
                                    + this.ext_description
                                +'</div>'
                            +'</div>'
                        +'</td>'
                        +'<td align="right">'
                            +'<div class="benefit_amt">'
                                +'<div class="d-block">'
                                    +`<span class="benefit_amount"  benefit_rate=${this.rate} basis=${this.rate_basis}></span>`
                                +'</div>'
                            +'</div>'
                        +'</td>'
                        +'<td align="right">'
                            +'<div class="form-check">'
                                +`<input class="form-check-input add_nm_benefit" type="checkbox" value="" benefit_id=${this.ext_code} benefit_desc=${this.ext_description} benefit_amount=""  benefit_rate=${this.rate} ${checked}>`
                            +'</div>'
                        +'</td>'
                    +'</tr>'
                    )
            });
            $('.benefit_amount').each(function(){
                let vehicle_value = 0;
                let ben_rate = $(this).attr('benefit_rate');
                let basis=$(this).attr('basis');
                if(basis=="L"){
                    vehicle_value=  removeCommas($('#sum_insured_0').val())
                }else if(basis=="B"){
                    vehicle_value=removeCommas($('#premium_0').val())

                }
            
                let benefit_amount = vehicle_value*ben_rate/100;

                $(this).text(numberWithCommas(benefit_amount));

            });
        }
    });
})

$('body').on('click','.add_nm_benefit', function(){
 
   
    $(this).removeClass("add_nm_benefit")
    $(this).addClass("remove_nm_benefit")
    // $(this).html('<span class="fa fa-times"></span>');
    let ben_id = $(this).attr('benefit_id');
    let ben_desc = $(this).attr('benefit_desc');
    let rate = $(this).attr('benefit_rate');
    let ben_amount =  removeCommas($(this).parents('tr.section').find('.benefit_amount').text())
    let qtebasic = removeCommas( $('#basic_premium').text())
    let qtetotal = $('#total_premium').text()
    let regno = $('#rsk_reg').text()


    let basic = removeCommas($('#basic_premium').text())
    let total = removeCommas($('#total_premium').text())
    //$("#basic_prem").val(basic)
    console.log(ben_amount,basic,total);

    total = Number(total) + Number(ben_amount)
    qtetotal= Number(qtetotal) + Number(ben_amount)
    
    $('#total_prem_field').val(total)
    $('#total_premium').text(numberWithCommas(total))
    $('#qtotal').text(qtetotal)
    $('#benefit_total').text(numberWithCommas(Number(total) - Number(basic)))
    //$('#total_prem_field').val(total)
    
    //$('#ext_risk').css('display', 'block')
    $('#risk_extensions').children('.alert').addClass('d-none')
    $('#risk_extensions').append('<div class="d-flex border border-bottom justify-content-between text-align-center added_benefit"><div><div class="added_desc">' + ben_desc + '</div><div class="fw-bold text-success added_amount">'+ben_amount+'</div></div><div class="float-right"><button class="btn btn-sm remove_risk_benefit" ben_id="'+ben_id+'"><span class="fa fa-times"></span></button></div></div>')
    $('#premiumdetails').append('<input class="benefit_'+ben_id+'" name="rsk_extensions[]" value="'+ben_amount+'" hidden>')
    $('#premiumdetails').append('<input class="benefit_'+ben_id+'" name="rsk_ext_types[]" value="'+ben_id+'" hidden>')
    var quote_no= $('#quote_no').val()
    var loc  = $('#sec_location').val()

    $.ajax({
        type: 'GET',
        data:{'ben_id':ben_id,'ben_desc':ben_desc,'ben_amount':ben_amount,'location':loc,'quote_no':quote_no,'rate':rate},
        url: "{!! route('stage.nm_ben')!!}",
        success:function(data){
         

        }
    });
})

$('body').on('click','.remove_nm_benefit', function(){
    
    $(this).removeClass("remove_nm_benefit")
    $(this).addClass("add_nm_benefit")
    
    let ben_id = $(this).attr('benefit_id')
    var quote_no= $('#quote_no').val()
    $(".benefit_"+ben_id).remove()
    $.ajax({
        type: 'GET',
        data:{'ben_id':ben_id,'delete_flag':"Y",'quote_no':quote_no},
        url: "{!! route('stage.nm_ben')!!}",
        success:function(data){
            
        }
    });

    $(".benefit_"+ben_id).remove()

    let ben_amount =  removeCommas($(this).parents('tr.section').find('.benefit_amount').text())
    let ben_desc = $(this).attr('benefit_desc');
    // console.log(ben_desc);
    
    if ($('#extensions').children(':visible.added_benefit').length < 1) {
        $('#extensions').append('<div class="alert alert-info">No added benefits</div>')
    }

    let qtebasic = $('#basic_premium').text()
    let qtetotal = $('#total_premium').text()
    let regno = $('#rsk_reg').text()
    console.log(ben_amount);

    let basic =removeCommas( $('#basic_premium').text())
    let total = removeCommas($('#total_premium').text())
    //$("#basic_prem").val(basic)

    total = Number(total) - Number(ben_amount)
    qtetotal= Number(qtetotal) - Number(ben_amount)
    $('#total_prem_field').val(total)
    $('#rsk_tprem').text(total)
    $('#total_premium').text(numberWithCommas(total))
    $('#qtotal').text(qtetotal)
    $('#benefit_total').text(numberWithCommas(Number(total) - Number(basic)))
    

    $(':hidden.section').each(function(){
        let hidden_desc = $(this).find('.benefit_desc').text()
        if($.trim(hidden_desc) === $.trim(ben_desc)){
            $(this).removeClass("d-none")
            $(this).addClass("d-block border border-bottom")
        }


    });
        
})


    /***** fetch loading ports ******/
    $("#source").on('change', function(){
        var source = $('#source').val();
    
        $.ajax({
            url:"{{route('fetch_ports')}}",
            data:{'source':source},
            type:"get",
            success:function(resp){
                $('#loading_at').empty();
            
                if (resp.status == 1) {
                    $('#loading_at').empty()
                    $('#loading_at').append($("<option />").val('').text('Select loading at'));
                    $.each(resp.ports, function() {
                        $('#loading_at').append($('<option>').text(this.port_name).attr('value', this.port_code));
                    });
                }     
            },
            error:function(resp){
                //alert('error');
                console.error;
            }
        });
    });


    
    /***** fetch destination ports ******/
    $("#destination").on('change', function(){
        var source = $(this).val();
    
        $.ajax({
            url:"{{route('fetch_ports')}}",
            data:{'source':source},
            type:"get",
            success:function(resp){
                $('#port_of_discharge').empty();
            
                if (resp.status == 1) {
                    $('#port_of_discharge').empty()
                    $('#port_of_discharge').append($("<option />").val('').text('Select port of discharge'));
                    $.each(resp.ports, function() {
                        $('#port_of_discharge').append($('<option>').text(this.port_name).attr('value', this.port_code));
                    });
                }     
            },
            error:function(resp){
                //alert('error');
                console.error;
            }
        });
    });

    /***** fetch transhipment ports ******/
    $("#transhipment_country").on('change', function(){
        var source = $(this).val();
    
        $.ajax({
            url:"{{route('fetch_ports')}}",
            data:{'source':source},
            type:"get",
            success:function(resp){
            
                if (resp.status == 1) {
                    $('#transhipment_port').empty()
                    $('#transhipment_port').append($("<option />").val('').text('Select transhipment destination port'));
                    $.each(resp.ports, function() {
                        $('#transhipment_port').append($('<option>').text(this.port_name).attr('value', this.port_code));
                    });
                }     
            },
            error:function(resp){
                //alert('error');
                console.error;
            }
        });
    });

    $('#one_off_transit').click(function() {
        checked = $(this).prop('checked')
        aoc_limit = $('#limit_of_liability').val()

        if (checked == true) {
            $('#est_annual_carry').prop('readonly', true)
            $('#est_annual_carry').val(aoc_limit)
        } else {
            $('#est_annual_carry').prop('readonly', false)
        }
    })

    $('#limit_of_liability').keyup(function() {
        checked = $('#one_off_transit').prop('checked')
        limit = $(this).val()

        if (checked == true) {
            $('#est_annual_carry').val(limit)
        }
    })
    
    

    $('.icc').on('click', function() {
        icc_type = $(this).val()

        if (icc_type == 'icca') {
            $('#war').prop('checked', true)
            $('#srcc').prop('checked', true)

            $('#war').prop('disabled', true)
            $('#srcc').prop('disabled', true)
        } else {
            $('#war').prop('checked', false)
            $('#srcc').prop('checked', false)

            $('#war').prop('disabled', false)
            $('#srcc').prop('disabled', false)
        }
    })

    $("#bond_per").on('change', function(){
        let percentage = $(this).val()
        let contract_amt = $('#contract_amt').val()
        let sum_insured = 0;

        if (contract_amt > 0) {
            sum_insured = percentage*contract_amt/100;
            $("#bond_sum").val(numberWithCommas(sum_insured))
            $("#loc_equiv").val(numberWithCommas(sum_insured))
            $("#sum_insured_0").val(numberWithCommas(sum_insured))
            $("#sum_insured_0").attr('readonly', true)
        }
    })
    

    $("#contract_amt").on('change', function(){
        let contract_amt = $(this).val()
        let percentage = $('#bond_per').val()
        let sum_insured = 0;

        if (percentage > 0) {
            sum_insured = percentage*contract_amt/100;
            $("#bond_sum").val(numberWithCommas(sum_insured))
            $("#loc_equiv").val(numberWithCommas(sum_insured))
            $("#sum_insured_0").val(numberWithCommas(sum_insured))
            $("#sum_insured_0").attr('readonly', true)
        }
    })

    $("#profit").on("change", function(){
        let profit = $(this).val()
        let val_insurance = $('#insurance_value').val()

        let profit_amt = parseFloat(val_insurance) * profit/100
        $("#profit_amount").val(profit_amt)
        $("#profit_amount").trigger('change')


    })

    $('#insurance_value').keyup(function(){
        compute_total_sum();
    })

    $('#clearing_charges').keyup(function(){
        compute_total_sum();
    })

    $('#customs_duty').keyup(function(){
        compute_total_sum();
    })

    $('#vat').keyup(function(){
        compute_total_sum();
    })
    
    $('#profit_amount').change(function(){
        compute_total_sum();
    })

    function compute_total_sum(){
        val_insurance = $('#insurance_value').val()
        clearing = $('#clearing_charges').val()
        duty = $('#customs_duty').val()
        vat = $('#vat').val()
        profit_amt = $('#profit_amount').val()

        val_insurance = parseFloat(val_insurance) || 0;
        clearing = parseFloat(clearing) || 0;
        duty = parseFloat(duty) || 0;
        vat = parseFloat(vat) || 0;
        profit_amt=parseFloat(profit_amt) || 0;

        tot_amt = val_insurance + clearing + duty + vat + profit_amt;

        computeMarinePremium(tot_amt)
        tot_amt = numberWithCommas(tot_amt)
        $('#total_sum_insured').val(tot_amt)
    }


    function computeMarinePremium(si) {
        let icc = $('.icc:checked').val()
        let rate = parseFloat($('#air_sea_rate').val())
        let conveyance = $("input[name='conveyance']:checked").val()
        let war_rate = parseFloat({{$marine_rates->war_rate}})
        let srcc_rate = parseFloat({{$marine_rates->srcc_rate}})
        let sea_prem = 0
        let air_prem = 0
        let war_prem = 0
        let srcc_prem = 0
        let gross_amount = 0
        let mode_air = ""
        let mode_sea = ""

        if (icc = "icca") {
            if (conveyance == 'mode_air') {
                mode_air = 'Y';
            } else if (conveyance == 'mode_sea') {
                mode_sea = 'Y';
            }
    
            if (mode_air == 'Y'){
                air_prem = parseFloat(si) * rate / 100;
            }
    
            if (mode_sea == 'Y'){
                sea_prem = parseFloat(si) * rate / 100;
            }

            war_prem = parseFloat(si) * war_rate / 100;
            srcc_prem = parseFloat(si) * srcc_rate / 100;

            gross_amount = sea_prem + air_prem;
        } else if(icc = "iccb") {
            rate = rate*85/100
            if (conveyance == 'mode_air') {
                mode_air = 'Y';
            } else if (conveyance == 'mode_sea') {
                mode_sea = 'Y';
            }
    
            if (mode_air == 'Y') {
                air_prem = parseFloat(si) * rate / 100;
            }
    
            if (mode_sea == 'Y') {
                sea_prem = parseFloat(si) * rate / 100;
            }


            war_prem = parseFloat(si) * war_rate / 100;
            srcc_prem = parseFloat(si) * srcc_rate / 100;

            gross_amount = sea_prem + air_prem + war_prem + srcc_prem
        } else if(icc = "iccc"){
            rate = rate*80/100
            if (conveyance == 'mode_air') {
                mode_air = 'Y';
            } else if (conveyance == 'mode_sea') {
                mode_sea = 'Y';
            }
    
            if (mode_air == 'Y') {
                air_prem = parseFloat(si) * rate / 100;
            }
    
            if (mode_sea == 'Y') {
                sea_prem = parseFloat(si) * rate / 100;
            }


            war_prem = parseFloat(si) * war_rate / 100;
            srcc_prem = parseFloat(si) * srcc_rate / 100;

            gross_amount = sea_prem + air_prem + war_prem + srcc_prem
        }else{}

        $('#basic_premium').text(numberWithCommas(gross_amount.toFixed(2)))
        $("#basic_prem").val(gross_amount)
        $("#total_premium").text(numberWithCommas(gross_amount.toFixed(2)))
        $('#total_prem_field').val(gross_amount)
    }
</script>
@endsection