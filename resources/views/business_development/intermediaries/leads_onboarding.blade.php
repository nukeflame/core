@extends('layouts.app')

@section('styles')
    <style>
        .card-align-top {
            border-radius: 0px !important;
        }

        .card-align-center {
            margin-top: -21px;
            margin-bottom: 3px !important;
            border-radius: 0px !important;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .section-divider {
            margin: 2rem 0;
            border-top: 1px solid #dee2e6;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .card.custom-card .card-header .card-title:before {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-0">Prospect
                    @if (is_null($prospect))
                        Onboarding
                    @else
                        Details
                    @endif
                </h1>

                <p class="text-muted mb-0 mt-1 fs-13">
                    @if (is_null($prospect))
                        Capture prospect and insurance cover details.
                    @else
                        Review and update prospect and insurance cover details.
                    @endif
                </p>
            </div>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/">Business Development</a></li>
                        <li class="breadcrumb-item"><a href="/">Pipeline</a></li>
                        <li class="breadcrumb-item"><a href="/">Facultative</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Prospect Onboarding</li>
                    </ol>
                </nav>
            </div>
        </div>

        <form id="prospectsForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="prospectId" name="prospect"
                value="{{ old('prospect', $prospect->opportunity_id ?? '') }}">
            <input type="hidden" name="updateState" value="{{ !is_null($prospect) ? 'U' : '' }}">

            <div class="row mt-3">
                <div class="col-md-12">
                    <div id="cedantDetails"class="card mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="ri-file-text-line me-2 text-primary"></i>
                            <h5 class="card-title mb-0 fs-15 flex-grow-1">Basic Information</h5>
                            <span class="badge bg-primary-transparent">Required</span>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row mb-4">
                                <x-OnboardingInputDiv id="prequalification_div">
                                    <x-SearchableSelect name="type_of_bus" id="type_of_bus" req="required"
                                        inputLabel="Type of Business" placeholder="-- Select Type of Business --">
                                        <option value="">--Select type of business--</option>
                                        @foreach ($types_of_bus as $type_of_bus)
                                            @if (in_array($type_of_bus->bus_type_id, ['FPR', 'FNP']))
                                                <option value="{{ $type_of_bus->bus_type_id }}"
                                                    {{ old('type_of_bus', $prospect->type_of_bus ?? '') == $type_of_bus->bus_type_id ? 'selected' : '' }}>
                                                    {{ firstUpper($type_of_bus->bus_type_name) }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </x-SearchableSelect>
                                    <div class="error-message" id="type_of_bus_error"></div>
                                </x-OnboardingInputDiv>

                                <x-OnboardingInputDiv>
                                    <x-SearchableSelect name="customer_id" id="customer_id" req="required"
                                        inputLabel="Cedant" placeholder="-- Select Cedant --">
                                        <option value="">---Select Cedant---</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->customer_id }}"
                                                {{ old('customer_id', $prospect->customer_id ?? '') == $customer->customer_id ? 'selected' : '' }}>
                                                {{ firstUpper($customer->name) }}
                                            </option>
                                        @endforeach
                                    </x-SearchableSelect>
                                    <div class="error-message" id="customer_id_error"></div>
                                </x-OnboardingInputDiv>

                                <x-OnboardingInputDiv id="countryDiv">
                                    <x-SearchableSelect name="client_type" id="client_type" req="required"
                                        inputLabel="Lead Type" placeholder="-- Select Lead Type --">
                                        <option value="">Select Lead type</option>
                                        @foreach ($customer_types as $cusType)
                                            <option value="{{ $cusType->type_name }}"
                                                {{ old('client_type', $prospect->client_type ?? '') == $cusType->type_name ? 'selected' : '' }}>
                                                {{ firstUpper($cusType->type_name) }}
                                            </option>
                                        @endforeach
                                    </x-SearchableSelect>
                                    <div class="error-message" id="client_type_error"></div>
                                </x-OnboardingInputDiv>

                                <x-OnboardingInputDiv id="leadNameDiv">
                                    <x-Input name="lead_name" id="lead_name" req="required" inputLabel="Lead Name"
                                        value="{{ old('lead_name', $prospect->lead_name ?? '') }}" />
                                    <div id="lead_name_results" class="dropdown-menu"
                                        style="display: none; max-width: 500px; width: 100%;"></div>
                                    <div class="error-message" id="lead_name_error"></div>
                                </x-OnboardingInputDiv>

                                <x-OnboardingInputDiv id="lead_year_div" class="mt-2">
                                    <x-SearchableSelect name="lead_year" id="lead_year" req="required" inputLabel="Year">
                                        @php
                                            $selectedLeadYearId = old(
                                                'lead_year',
                                                $prospect->lead_year ?? ($prospect->pip_year ?? ''),
                                            );
                                            $selectedProspectCode = old(
                                                'prospect',
                                                $prospect->opportunity_id ?? request('prospect'),
                                            );
                                            $prospectCodeYear = null;
                                            if (
                                                !empty($selectedProspectCode) &&
                                                preg_match('/-(\d{4})-/', $selectedProspectCode, $matches)
                                            ) {
                                                $prospectCodeYear = (int) $matches[1];
                                            }
                                        @endphp
                                        <option value="">Select year</option>
                                        @foreach ($pipeYear as $year)
                                            <option value="{{ $year->id }}"
                                                {{ (string) $selectedLeadYearId === (string) $year->id || ((is_null($selectedLeadYearId) || $selectedLeadYearId === '') && !old('lead_year') && !empty($prospectCodeYear) && $prospectCodeYear === (int) $year->year) || ($year->year == now()->year && !old('lead_year') && is_null($prospect)) ? 'selected' : '' }}>
                                                {{ $year->year }}
                                            </option>
                                        @endforeach
                                    </x-SearchableSelect>
                                    <div class="error-message" id="lead_year_error"></div>
                                </x-OnboardingInputDiv>

                                <x-OnboardingInputDiv class="mt-2">
                                    <x-SearchableSelect name="client_category" id="client_category" req="required"
                                        inputLabel="Insured Category">
                                        <option value="">Select prospect category</option>
                                        <option value="N"
                                            {{ old('client_category', $prospect->client_category ?? '') == 'N' ? 'selected' : '' }}>
                                            New prospect
                                        </option>
                                        <option value="O"
                                            {{ old('client_category', $prospect->client_category ?? '') == 'O' ? 'selected' : '' }}>
                                            Organic growth
                                        </option>
                                    </x-SearchableSelect>
                                    <div class="error-message" id="client_category_error"></div>
                                </x-OnboardingInputDiv>

                                <x-OnboardingInputDiv id="countryDiv" class="mt-2">
                                    <x-SearchableSelect name="country_code" class="form" id="country" req="required"
                                        inputLabel="Country">
                                        <option value="">Select country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->country_iso }}"
                                                {{ old('country_code', $prospect->country_code ?? '') == $country->country_iso || ($country->country_iso == 'KEN' && !old('country_code') && !isset($prospect)) ? 'selected' : '' }}>
                                                {{ $country->country_name }} ({{ $country->country_iso }})
                                            </option>
                                        @endforeach
                                    </x-SearchableSelect>
                                    <div class="error-message" id="country_code_error"></div>
                                </x-OnboardingInputDiv>

                                <x-OnboardingInputDiv id="branchcode" class="mt-2">
                                    <x-SearchableSelect name="branchcode" id="branchcode" req="required"
                                        inputLabel="Branch">
                                        <option value="">--Select branch--</option>
                                        @foreach ($branches as $branch)
                                            @if ($branch->status == 'A')
                                                <option value="{{ $branch->branch_code }}"
                                                    {{ old('branchcode', $prospect->branchcode ?? '') == $branch->branch_code ? 'selected' : '' }}>
                                                    {{ firstUpper($branch->branch_name) }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </x-SearchableSelect>
                                    <div class="error-message" id="branchcode_error"></div>
                                </x-OnboardingInputDiv>
                            </div>
                        </div>
                    </div>

                    <div id="contactDetails" class="card mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="ri-file-text-line me-2 text-primary"></i>
                            <h5 class="card-title mb-0 fs-15 flex-grow-1">Contact Details</h5>
                            <span class="badge bg-primary-transparent">Required</span>
                        </div>
                        <div class="card-body">
                            @php
                                $firstContact = $contacts_det[0] ?? [];
                            @endphp
                            <div id="contactsContainer" class="customScrollBar"
                                style="max-height: 500px; overflow-x: hidden; overflow-y: auto; padding-right:12px;">
                                <div class="row contactsContainers" data-counter="0">
                                    <x-OnboardingInputDiv>
                                        <x-Input name="contact_name[]" id="contact_name-0" class="contact_name-0"
                                            placeholder="Enter name" inputLabel="Contact Full Name" req="required"
                                            value="{{ old('contact_name.0', $firstContact['contact_name'] ?? '') }}" />
                                        <div id="full_name_results_0" class="dropdown-menu full-name-results"
                                            style="display: none; max-width: 500px; width: 100%;"></div>
                                        <div class="error-message" id="full_name_error_0"></div>
                                    </x-OnboardingInputDiv>
                                    <x-OnboardingInputDiv>
                                        <x-EmailInput id="email-0" name="email[]" req="required"
                                            inputLabel="Email Address" placeholder="Enter email"
                                            value="{{ old('email.0', $firstContact['email'] ?? '') }}" />
                                    </x-OnboardingInputDiv>
                                    <x-OnboardingInputDiv>
                                        <x-NumberInput id="phone_number-0" name="phone_number[]" req="required"
                                            inputLabel="Mobile." class="phone" placeholder="Enter phone number"
                                            value="{{ old('phone_number.0', $firstContact['phone'] ?? '') }}" />
                                    </x-OnboardingInputDiv>
                                    <div class="col-md-3 col-sm-12">
                                        <Label class="form-label fw-bold" for="telephone-0">Telephone</Label>
                                        <div class="input-group mb-3">
                                            <input type="number" id="telephone-0" class="form-control color-blk"
                                                name="telephone[]" placeholder="Enter telephone number"
                                                value="{{ old('telephone.0', $firstContact['telephone'] ?? '') }}" />
                                            <button class="btn btn-primary add-contact" type="button" id="add-contact-0"
                                                data-counter="0">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="insuranceDetails" class="card mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="ri-shield-check-line me-2 text-info"></i>
                            <h5 class="card-title mb-0 fs-15 flex-grow-1">Facultative Details</h5>
                            <span class="badge bg-info-transparent">Conditional</span>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row row-cols-12 mb-3">
                                <div class="col-sm-3">
                                    <label class="form-label required">Division <i style="color:red;">*</i></label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2" name="division" id="division"
                                            required>
                                            <option selected value="">Choose Division</option>
                                            @foreach ($reinsdivisions as $trtDivision)
                                                <option value="{{ $trtDivision->division_code }}"
                                                    {{ old('division', $prospect->divisions ?? '') == $trtDivision->division_code ? 'selected' : '' }}>
                                                    {{ firstUpper($trtDivision->division_name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <x-OnboardingInputDiv id="industry_div" class="new_pros">
                                    <x-SearchableSelect name="industry" id="industry" req="required"
                                        inputLabel="Industry">
                                        <option value="">--Select industry--</option>
                                        @foreach ($industries as $industry)
                                            <option value="{{ $industry->name }}"
                                                {{ old('industry', $prospect->industry ?? '') == $industry->name ? 'selected' : '' }}>
                                                {{ $industry->name }}
                                            </option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </x-OnboardingInputDiv>
                                <div class="col-sm-3 fac_section_div">
                                    <label class="form-label required">Currency <i style="color:red;">*</i></label>
                                    <div class="cover-card">
                                        <select class="form-inputs select2" name="currency_code" id="currency_code"
                                            required>
                                            <option selected value="">Choose Currency</option>
                                            @foreach ($currencies as $currency)
                                                <option value="{{ $currency->currency_code }}"
                                                    {{ old('currency_code', $prospect->currency_code ?? '') == $currency->currency_code ? 'selected' : '' }}>
                                                    {{ firstUpper($currency->currency_name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="text-danger">{{ $errors->first('currency_code') }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label required">Exchange Rate</label>
                                    <input type="text" name="today_currency" id="today_currency"
                                        class="form-inputs section" onkeyup="this.value=numberWithCommas(this.value)"
                                        onchange="this.value=numberWithCommas(this.value)"
                                        value="{{ old('today_currency', $prospect->today_currency ?? '') }}" />
                                </div>
                            </div>

                            <div class="row row-cols-12 mb-3">
                                <div class="col-sm-3 class_group_div fac_section_div">
                                    <label class="form-label required">Class Group <i style="color:red;">*</i></label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2 fac_section" name="class_group"
                                            id="class_group" required>
                                            <option selected value="">Choose Class Group</option>
                                            @foreach ($classGroups as $classGroup)
                                                <option value="{{ $classGroup->group_code }}"
                                                    {{ old('class_group', $prospect->class_group ?? '') == $classGroup->group_code ? 'selected' : '' }}>
                                                    {{ firstUpper($classGroup->group_name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 fac_section_div">
                                    <label class="form-label required">Class Name <i style="color:red;">*</i></label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2 fac_section" name="classcode"
                                            id="classcode"
                                            data-selected="{{ old('classcode', $prospect->classcode ?? '') }}" required>
                                            <option value="">-- Select Class Name--</option>
                                        </select>
                                        <div class="text-danger">{{ $errors->first('classcode') }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-3 fac_section_div">
                                    <label class="form-label required">Insured Name <i style="color:red;">*</i></label>
                                    <input type="text" class="form-inputs section fac_section" name="insured_name"
                                        id="insured_name"
                                        value="{{ old('insured_name', $prospect->insured_name ?? '') }}" required />
                                    <div id="insured_name_results" class="dropdown-menu"
                                        style="display: none; max-width: 500px; width: 100%;">
                                    </div>
                                    <div class="error-message" id="insured_name_error"></div>
                                </div>

                                <x-OnboardingInputDiv id="fac_date_offered" class="fac_section_div">
                                    <label class="form-label required">Expected Closure Date <i
                                            style="color:red;">*</i></label>
                                    <input type="date" class="form-inputs fac_section" aria-label="fac_date_offered"
                                        id="fac_date_offered" name="fac_date_offered"
                                        value="{{ old('fac_date_offered', $prospect->fac_date_offered ?? '') }}" required>
                                </x-OnboardingInputDiv>

                            </div>

                            <div class="row row-cols-12 mb-3">

                                <div class="col-sm-3 fac_section_div">
                                    <label class="form-label required">Sum Insured Type</label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2 fac_section" name="sum_insured_type"
                                            id="sum_insured_type" required>
                                            <option selected value="">Choose Sum Insured Type
                                            </option>
                                            @foreach ($types_of_sum_insured as $type_of_sum_insured)
                                                <option value="{{ $type_of_sum_insured->sum_insured_code }}"
                                                    {{ old('sum_insured_type', $prospect->sum_insured_type ?? '') == $type_of_sum_insured->sum_insured_code ? 'selected' : '' }}>
                                                    {{ firstUpper($type_of_sum_insured->sum_insured_name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 fac_section_div">
                                    <label class="form-label">100% SUM INSURED <span id="sum_insured_label"
                                            class="pl-2"></span> <i style="color:red;">*</i></label>
                                    <input type="text" class="form-inputs fac_section" aria-label="total_sum_insured"
                                        id="total_sum_insured" name="total_sum_insured"
                                        onkeyup="this.value=numberWithCommas(this.value)"
                                        value="{{ old('total_sum_insured', $prospect->total_sum_insured ?? '') }}"
                                        required>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label" for="apply_eml">Apply EML</label>
                                    <div class="cover-card">
                                        <select name="apply_eml" class="form-inputs section select2" id="apply_eml"
                                            required>
                                            <option value="">--select option-</option>
                                            <option value="Y"
                                                {{ old('apply_eml', $prospect->apply_eml ?? '') == 'Y' ? 'selected' : '' }}>
                                                Yes</option>
                                            <option value="N"
                                                {{ old('apply_eml', $prospect->apply_eml ?? '') == 'N' ? 'selected' : '' }}>
                                                No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 eml-div">
                                    <label class="form-label"> EML Rate</label>
                                    <div class="cover-card">
                                        <input type="number" class="form-inputs fac_section" aria-label="eml_rate"
                                            id="eml_rate" name="eml_rate" min="0" max="100"
                                            value="{{ old('eml_rate', $prospect->eml_rate ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-3 eml-div">
                                    <label class="form-label">EML Amount</label>
                                    <span id="eml_amt_error"></span>
                                    <div class="cover-card">
                                        <input type="text" class="form-inputs fac_section amount" aria-label="eml_amt"
                                            id="eml_amt" name="eml_amt"
                                            value="{{ old('eml_amt', $prospect->eml_amt ?? '') }}" required>
                                    </div>
                                </div>
                            </div>


                            <div class="row row-cols-12 mb-3">
                                <div class="col-sm-3">
                                    <label class="form-label">Effective Sum Insured <i style="color:red;">*</i></label>
                                    <span id="effective_sum_insured_error"></span>
                                    <input type="text" class="form-inputs fac_section amount"
                                        aria-label="effective_sum_insured" id="effective_sum_insured"
                                        name="effective_sum_insured"
                                        value="{{ old('effective_sum_insured', $prospect->effective_sum_insured ?? '') }}"
                                        required readonly>
                                </div>
                                <div class="col-sm-9">
                                    <label class="form-label">Risk Details <i style="color:red;">*</i></label>
                                    <textarea class="form-inputs section fac_section resize-none" id="risk_details" name="risk_details" required>{{ old('risk_details', $prospect->risk_details ?? '') }}</textarea>
                                </div>
                            </div>

                            <div class="row row-cols-12 mb-3">
                                <div class="col-md-3 fac_section_div">
                                    <label class="form-label" for="cede_premium">Cedant Premium <i
                                            style="color:red;">*</i></label>
                                    <input type="text" class="form-inputs fac_section" aria-label="cede_premium"
                                        id="cede_premium" name="cede_premium"
                                        onkeyup="this.value=numberWithCommas(this.value)"
                                        value="{{ old('cede_premium', $prospect->cede_premium ?? '') }}" required>
                                </div>
                                <div class="col-md-3 fac_section_div">
                                    <label class="form-label" for="rein_premium">Reinsurer Premium <i
                                            style="color:red;">*</i></label>
                                    <input type="text" class="form-inputs fac_section" aria-label="rein_premium"
                                        id="rein_premium" name="rein_premium"
                                        onkeyup="this.value=numberWithCommas(this.value)"
                                        value="{{ old('rein_premium', $prospect->rein_premium ?? '') }}" required>
                                </div>
                                <div class="col-md-3 fac_section_div">
                                    <label class="form-label">Written Share(%) <i style="color:red;">*</i></label>
                                    <input type="number" class="form-inputs fac_section" aria-label="fac_share_offered"
                                        id="fac_share_offered" name="fac_share_offered" data-counter="0" min="0"
                                        max="100" required
                                        value="{{ old('fac_share_offered', $prospect->fac_share_offered ?? '') }}"
                                        oninput="if(this.value>100)this.value=100; if(this.value<0)this.value=0;">
                                </div>
                            </div>

                            <div class="row row-cols-12 mb-3">
                                <div class="col-md-3 fac_section_div mb-2">
                                    <label class="form-label">Cedant Commission rate(%) <i
                                            style="color:red;">*</i></label>
                                    <input type="text" class="form-inputs fac_section" aria-label="comm_rate"
                                        id="comm_rate" name="comm_rate"
                                        value="{{ old('comm_rate', $prospect->comm_rate ?? '') }}" required>
                                </div>
                                <div class="col-md-3 fac_section_div">
                                    <label class="form-label">Cedant Commission Amount <i style="color:red;">*</i></label>
                                    <input type="text" class="form-inputs fac_section" aria-label="comm_amt"
                                        id="comm_amt" name="comm_amt" onkeyup="this.value=numberWithCommas(this.value)"
                                        value="{{ old('comm_amt', $prospect->comm_amt ?? '') }}" required>
                                </div>
                                <div class="col-md-3 fac_section_div reins_comm_type_div">
                                    <label class="form-label">Reinsurer Commission Type</label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2 fac_section reins_comm_type"
                                            name="reins_comm_type" id="reins_comm_type" required>
                                            <option value="">Choose Reinsurer Commission Type</option>
                                            <option value="R"
                                                {{ old('reins_comm_type', $prospect->reins_comm_type ?? '') == 'R' ? 'selected' : '' }}>
                                                Rate</option>
                                            <option value="A"
                                                {{ old('reins_comm_type', $prospect->reins_comm_type ?? '') == 'A' ? 'selected' : '' }}>
                                                Amount</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3  fac_section_div reins_comm_rate_div">
                                    <label class="form-label">Reinsurer Commission rate(%)</label>
                                    <input type="text" class="form-inputs fac_section reins_comm_rate"
                                        aria-label="reins_comm_rate" id="reins_comm_rate" name="reins_comm_rate"
                                        onkeyup="this.value=numberWithCommas(this.value)"
                                        value="{{ old('reins_comm_rate', $prospect->reins_comm_rate ?? '') }}" required
                                        disabled>
                                </div>
                                <div class="col-md-3 fac_section_div reins_comm_amt_div">
                                    <label class="form-label">Reinsurer Commission Amount</label>
                                    <input type="text" class="form-inputs fac_section reins_comm_amt"
                                        aria-label="reins_comm_amt" id="reins_comm_amt" name="reins_comm_amt"
                                        onkeyup="this.value=numberWithCommas(this.value)"
                                        onchange="this.value=numberWithCommas(this.value)"
                                        value="{{ old('reins_comm_amt', $prospect->reins_comm_amt ?? '') }}" required>
                                </div>
                            </div>

                            <div class="row row-cols-12 mb-3">
                                <div class="col-md-3 fac_section_div">
                                    <label class="form-label">Brokerage Commission Type</label>
                                    <div class="cover-card">
                                        <select name="brokerage_comm_type" id="brokerage_comm_type"
                                            class="form-inputs section select2">
                                            <option value="">Select Basis</option>
                                            <option value="R"
                                                {{ old('brokerage_comm_type', $prospect->brokerage_comm_type ?? '') == 'R' ? 'selected' : '' }}>
                                                Rate (<small><i>Reinsurer - Cedant</i></small>)
                                            </option>
                                            <option value="A"
                                                {{ old('brokerage_comm_type', $prospect->brokerage_comm_type ?? '') == 'A' ? 'selected' : '' }}>
                                                Quoted Amount</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 fac_section_div brokerage-amount-field" style="display: none;">
                                    <label class="form-label">Brokerage Amount</label>
                                    <input type="text" class="form-inputs fac_section amount" id="brokerage_comm_amt"
                                        name="brokerage_comm_amt"
                                        value="{{ old('brokerage_comm_amt', $prospect->brokerage_comm_amt ?? 0) }}">
                                </div>
                                <div class="col-md-3 fac_section_div brokerage-rate-field" style="display: none;">
                                    <label class="form-label">Brokerage Rate (%)</label>
                                    <input type="text" class="form-inputs fac_section amount" id="brokerage_comm_rate"
                                        name="brokerage_comm_rate"
                                        value="{{ old('brokerage_comm_rate', $prospect->brokerage_comm_rate ?? '') }}"
                                        readonly>
                                </div>
                                <div class="col-md-3 fac_section_div brokerage-rate-amount-field" style="display: none;">
                                    <label class="form-label">Brokerage Amount</label>
                                    <input type="text" class="form-inputs fac_section amount"
                                        id="brokerage_comm_rate_amnt" name="brokerage_comm_rate_amnt"
                                        value="{{ old('brokerage_comm_rate_amnt', $prospect->brokerage_comm_rate_amnt ?? 0) }}"
                                        readonly>
                                </div>
                                <input type="hidden" class="vat_charged fac_section" id="vat_charged"
                                    name="vat_charged" value="{{ old('vat_charged', $prospect->vat_charged ?? 0) }}">
                            </div>
                        </div>
                    </div>

                    <div id="engagementDetails" class="card mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="ri-shield-check-line me-2 text-primary"></i>
                            <h5 class="card-title mb-0 fs-15 flex-grow-1">Engagement Details</h5>
                            <span class="badge bg-danger-transparent">Required</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <x-OnboardingInputDiv>
                                    <x-SearchableSelect name="engage_type" id="engage_type" req="required"
                                        inputLabel="Nature of engagement">
                                        <option value="">Select engagement type </option>
                                        @foreach ($engage_types as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('engage_type', $prospect->engage_type ?? '') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </x-OnboardingInputDiv>
                                <x-OnboardingInputDiv id="lead_owner_div">
                                    <x-SearchableSelect name="lead_owner" id="lead_owner" req="required"
                                        inputLabel="Prospect Lead">
                                        <option value="">Select prospect Lead</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ (string) old('lead_owner', $prospect->lead_owner ?? auth()->id()) === (string) $user->id ? 'selected' : '' }}>
                                                {{ firstUpper($user->name) }}</option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </x-OnboardingInputDiv>
                                <x-OnboardingInputDiv id="date_effective_div">
                                    <x-DateInput name="effective_date" id="effective_date"
                                        placeholder="Enter cover start date" inputLabel="Cover Start Date"
                                        value="{{ old('effective_date', $prospect->effective_date ?? '') }}" />
                                </x-OnboardingInputDiv>
                                <x-OnboardingInputDiv id="date_closing_div">
                                    <x-DateInput name="closing_date" id="closing_date"
                                        placeholder="Enter bid closing date" inputLabel="Cover End  Date"
                                        value="{{ old('closing_date', $prospect->closing_date ?? '') }}" />
                                </x-OnboardingInputDiv>
                                <div class="col-md-12 mt-1">
                                    <input type="hidden" name="cover_dates_tba" value="0">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            id="cover_dates_tba" name="cover_dates_tba"
                                            {{ old('cover_dates_tba', !empty($prospect) && empty($prospect->effective_date) && empty($prospect->closing_date) ? 1 : 0) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="cover_dates_tba">
                                            Cover dates are To Be Advised
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="ri-information-line me-1"></i>
                                    All fields marked with <span class="text-danger">*</span> are required
                                </small>
                                <div class="d-flex gap-2">
                                    <button type="button" name="previous" class="btn btn-light btn-sm me-2"
                                        id="cancel"><span class="bx bx-times"></span> Cancel</button>
                                    <button type="submit" id="submits" class="btn btn-primary btn-sm"><span
                                            class="bx bx-save"></span>
                                        {{ !is_null($prospect) ? 'Update Details' : 'Save Details' }}
                                    </button>
                                    @if (!is_null($prospect))
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-prospect=""
                                            id="sales_mngt"><span class="fa fa-arrow-right"></span> Submit to
                                            Sales</button>
                                        <button style="display:none" type="button"
                                            class="btn btn-outline-primary btn-sm" data-prospect="" id="process_pq"><span
                                                class="fa fa-arrow-right"></span>
                                            Update PQ Status</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/prospect-onboarding.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#contactDetails').hide();
            $('#engagementDetails').hide();
            $('#contactDetails').hide();
            $('#insuranceDetails').hide();

            if (typeof ProspectOnboarding !== 'undefined') {
                ProspectOnboarding.init({
                    formId: 'prospectsForm',
                    prospect: @json($prospect),
                    contacts: @json($contacts_det ?? []),
                    routes: {
                        submit: "{{ route('pipeline.create.opportunity') }}",
                        submitToSales: "{{ route('prospect.add.pipeline') }}",
                        searchProspectNames: "{{ route('search-prospect-fullnames') }}",
                        searchInsuredNames: "{{ route('search-insured-names') }}",
                        searchLeadNames: "{{ route('search-lead-names') }}",
                        getClasses: "{{ route('get_class') }}",
                        getTreatyPerBusType: "{{ route('cover.get_treatyperbustype') }}",
                        getReinPremType: "{{ route('cover.get_reinprem_type') }}",
                        getDivisionClasses: "{{ route('get_division_classes') }}",
                        docPreview: "{{ route('doc_preview') }}",
                        checkUserExists: "{{ route('check_user_exists') }}",
                        getTodaysRate: "{{ route('get_todays_rate') }}",
                    },
                    csrfToken: "{{ csrf_token() }}"
                });
            } else {
                console.error('ProspectOnboarding module not loaded');
            }
        });
    </script>
@endpush
