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
    </style>
@endsection

@section('content')
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div>
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">Prospect
                @if (is_null($prospect))
                    Onboarding
                @else
                    Details
                @endif
            </h1>
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
            <input type="hidden" id="prospectId" name="prospect_id" value="{{ $prospect->id ?? '' }}">

            <div class="row mt-3">
                <div class="col-xl-12">
                    <div class="card custom-card card-align-top">
                        {{-- Cedant Details Section --}}
                        <div id="cedantDetails">
                            <div class="card-header">
                                <div class="card-title text-danger">Cedant Details</div>
                            </div>
                            <div class="card-body pb-0">
                                <div class="row mb-4">

                                    {{-- Type of Business --}}
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

                                    {{-- Cedant --}}
                                    <x-OnboardingInputDiv>
                                        <x-SearchableSelect name="customer_id" id="customer_id" req=""
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

                                    {{-- Lead Type --}}
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

                                    {{-- Lead Name --}}
                                    <x-OnboardingInputDiv id="leadNameDiv">
                                        <x-Input name="lead_name" id="lead_name" req="required" inputLabel="Lead Name"
                                            value="{{ old('lead_name', $prospect->lead_name ?? '') }}"
                                            oninput="this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());" />
                                        <div id="lead_name_results" class="dropdown-menu"
                                            style="display: none; max-width: 500px; width: 100%;"></div>
                                        <div class="error-message" id="lead_name_error"></div>
                                    </x-OnboardingInputDiv>

                                    {{-- Lead Year --}}
                                    <x-OnboardingInputDiv id="lead_year_div">
                                        <x-SearchableSelect name="lead_year" id="lead_year" req="required"
                                            inputLabel="Year">
                                            <option value="">Select year</option>
                                            @foreach ($pipeYear as $year)
                                                <option value="{{ $year->id }}"
                                                    {{ old('lead_year', $prospect->lead_year ?? '') == $year->id ? 'selected' : '' }}>
                                                    {{ $year->year }}
                                                </option>
                                            @endforeach
                                        </x-SearchableSelect>
                                        <div class="error-message" id="lead_year_error"></div>
                                    </x-OnboardingInputDiv>

                                    {{-- Insured Category --}}
                                    <x-OnboardingInputDiv>
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

                                    {{-- Country --}}
                                    <x-OnboardingInputDiv id="countryDiv">
                                        <x-SearchableSelect name="country_code" class="form" id="country" req="required"
                                            inputLabel="Country">
                                            <option value="">Select country code</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->country_iso }}"
                                                    {{ old('country_code', $prospect->country_code ?? '') == $country->country_iso || ($country->country_iso == 'KEN' && !old('country_code') && !isset($prospect)) ? 'selected' : '' }}>
                                                    {{ $country->country_code ? $country->country_code : '' }}
                                                    {{ $country->country_name }}
                                                    {{ $country->country_iso }}
                                                </option>
                                            @endforeach
                                        </x-SearchableSelect>
                                        <div class="error-message" id="country_code_error"></div>
                                    </x-OnboardingInputDiv>

                                    {{-- Branch --}}
                                    <x-OnboardingInputDiv id="branchcode">
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

                        {{-- Contact Details Section --}}
                        <div id="contactDetails">
                            <hr />
                            <div class="card-header">
                                <div class="card-title text-danger">Contact Details</div>
                            </div>
                            <div class="card-body pb-0">
                                <div id="contactsContainer" class="customScrollBar"
                                    style="max-height: 500px; overflow-x: hidden; overflow-y: auto; padding-right:12px;">
                                    <div class="row contactsContainers" data-counter="0">
                                        <x-OnboardingInputDiv>
                                            <x-Input name="contact_name[]" id="contact_name-0" class="contact_name-0"
                                                placeholder="Enter name" inputLabel="Contact Full Name" req="required"
                                                oninput="this.value = this.value.toUpperCase();" />
                                            <div id="full_name_results_0" class="dropdown-menu full-name-results"
                                                style="display: none; max-width: 500px; width: 100%;"></div>
                                            <div class="error-message" id="full_name_error_0"></div>
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv>
                                            <x-EmailInput id="email-0" name="email[]" req="required"
                                                inputLabel="Email Address" placeholder="Enter email" />
                                        </x-OnboardingInputDiv>
                                        <x-OnboardingInputDiv>
                                            <x-NumberInput id="phone_number-0" name="phone_number[]" req="required"
                                                inputLabel="Mobile." class="phone" placeholder="Enter phone number" />
                                        </x-OnboardingInputDiv>
                                        <div class="col-md-3 col-sm-12 mt-2">
                                            <Label class="form-label fw-bold" for="telephone-0">Telephone</Label>
                                            <div class="input-group mb-3">
                                                <input type="number" id="telephone-0" class="form-control color-blk"
                                                    name="telephone[]" placeholder="Enter telephone number" />
                                                <button class="btn btn-primary add-contact" type="button"
                                                    id="add-contact-0" data-counter="0">
                                                    <i class="bx bx-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Insurance Details Section --}}
                        <div id="insuranceDetails">
                            <hr />
                            <div class="card-header">
                                <div class="card-title text-danger">Insurance Details</div>
                            </div>
                            <div class="card-body pb-0">
                                <div class="row row-cols-12 mb-2">
                                    <div class="col-sm-3">
                                        <label class="form-label required">Division</label>
                                        <div class="cover-card">
                                            <select class="form-inputs section select2" name="division" id="division"
                                                required>
                                                <option selected value="">Choose Division</option>
                                                @foreach ($reinsdivisions as $trtDivision)
                                                    <option value="{{ $trtDivision->division_code }}">
                                                        {{ firstUpper($trtDivision->division_name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 class_group_div fac_section_div">
                                        <label class="form-label required">Class Group</label>
                                        <div class="cover-card">
                                            <select class="form-inputs section select2 fac_section" name="class_group"
                                                id="class_group" required>
                                                <option selected value="">Choose Class Group</option>
                                                @foreach ($classGroups as $classGroup)
                                                    <option value="{{ $classGroup->group_code }}">
                                                        {{ firstUpper($classGroup->group_name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 fac_section_div">
                                        <label class="form-label required">Class Name</label>
                                        <div class="cover-card">
                                            <select class="form-inputs section select2 fac_section" name="classcode"
                                                id="classcode" required>
                                                <option value="">-- Select Class Name--</option>
                                            </select>
                                            <div class="text-danger">{{ $errors->first('classcode') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 fac_section_div">
                                        <label class="form-label required">Insured Name</label>
                                        <div class="cover-card">
                                            <input type="text" class="form-inputs section  fac_section"
                                                name="insured_name" id="insured_name"
                                                oninput="this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());"
                                                required />
                                        </div>
                                        <div id="insured_name_results" class="dropdown-menu"
                                            style="display: none; max-width: 500px; width: 100%;">
                                        </div>
                                        <div class="error-message" id="insured_name_error"></div>
                                    </div>
                                </div>

                                <div class="row row-cols-12 mb-2">
                                    <x-OnboardingInputDiv id="industry_div" class="new_pros">
                                        <x-SearchableSelect name="industry" id="industry" req="required"
                                            inputLabel="Industry">
                                            <option value="">--Select industry--</option>
                                            @foreach ($industries as $industry)
                                                <option value="{{ $industry->name }}">{{ $industry->name }}
                                                </option>
                                            @endforeach
                                        </x-SearchableSelect>
                                    </x-OnboardingInputDiv>
                                    <x-OnboardingInputDiv id="fac_date_offered" class="fac_section_div">
                                        <label class="form-label required">Expected Closure Date</label>
                                        <input type="date" class="form-inputs fac_section"
                                            aria-label="fac_date_offered" id="fac_date_offered" name="fac_date_offered"
                                            required>
                                    </x-OnboardingInputDiv>

                                </div>

                                <div class="row row-cols-12 mb-2">
                                    <div class="col-sm-3 fac_section_div">
                                        <label class="form-label required">Currency</label>
                                        <div class="cover-card">
                                            <select class="form-inputs select2" name="currency_code" id="currency_code"
                                                required>
                                                <option selected value="">Choose Currency</option>
                                                @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->currency_code }}">
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
                                            change="this.value=numberWithCommas(this.value)" />
                                    </div>
                                    <div class="col-sm-3 fac_section_div">
                                        <label class="form-label required">Sum Insured Type</label>
                                        <div class="cover-card">
                                            <select class="form-inputs section select2 fac_section"
                                                name="sum_insured_type" id="sum_insured_type" required>
                                                <option selected value="">Choose Sum Insured Type
                                                </option>
                                                @foreach ($types_of_sum_insured as $type_of_sum_insured)
                                                    <option value="{{ $type_of_sum_insured->sum_insured_code }}">
                                                        {{ firstUpper($type_of_sum_insured->sum_insured_name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 fac_section_div">
                                        <label class="form-label">100% SUM INSURED<span
                                                id="sum_insured_label"></span></label>
                                        <input type="text" class="form-inputs fac_section"
                                            aria-label="total_sum_insured" id="total_sum_insured"
                                            name="total_sum_insured" onkeyup="this.value=numberWithCommas(this.value)"
                                            required>
                                    </div>
                                </div>

                                <div class="row row-cols-12 mb-2">
                                    <div class="col-sm-3">
                                        <label for="apply_eml">Apply EML</label>
                                        <div class="cover-card">
                                            <select name="apply_eml" class="form-inputs section select2" id="apply_eml"
                                                required>
                                                <option value="">--select option-</option>
                                                <option value="Y">Yes</option>
                                                <option value="N">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 eml-div">
                                        <label class="form-label"> EML Rate</label>
                                        <div class="cover-card">
                                            <input type="number" class="form-inputs fac_section" aria-label="eml_rate"
                                                id="eml_rate" name="eml_rate" min="0" max="100" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 eml-div">
                                        <label class="form-label">EML Amount</label>
                                        <span id="eml_amt_error"></span>
                                        <div class="cover-card">
                                            <input type="text" class="form-inputs fac_section amount"
                                                aria-label="eml_amt" id="eml_amt" name="eml_amt" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Effective Sum Insured</label>
                                        <span id="effective_sum_insured_error"></span>
                                        <input type="text" class="form-inputs fac_section amount"
                                            aria-label="effective_sum_insured" id="effective_sum_insured"
                                            name="effective_sum_insured" value="5" required readonly>
                                    </div>

                                </div>

                                <div class="row row-cols-12 mb-2">
                                    <div class="col-sm-3">
                                        <label class="form-label">Risk Details</label>
                                        <textarea class="form-inputs section fac_section resize-none" id="risk_details" name="risk_details" required></textarea>
                                    </div>
                                    <div class="col-xl-3 fac_section_div">
                                        <label class="form-label" for="cede_premium">Cedant Premium</label>
                                        <input type="text" class="form-inputs fac_section" aria-label="cede_premium"
                                            id="cede_premium" name="cede_premium"
                                            onkeyup="this.value=numberWithCommas(this.value)" required>
                                    </div>
                                    <div class="col-xl-3 fac_section_div">
                                        <label class="form-label" for="rein_premium">Reinsurer Premium</label>
                                        <input type="text" class="form-inputs fac_section" aria-label="rein_premium"
                                            id="rein_premium" name="rein_premium"
                                            onkeyup="this.value=numberWithCommas(this.value)" required>
                                    </div>
                                    <div class="col-xl-3 fac_section_div">
                                        <label class="form-label">Written Share(%)</label>
                                        <input type="number" class="form-inputs fac_section"
                                            aria-label="fac_share_offered" id="fac_share_offered"
                                            name="fac_share_offered" data-counter="0" min="0" max="100"
                                            required
                                            oninput="if(this.value>100)this.value=100; if(this.value<0)this.value=0;">
                                    </div>
                                </div>

                                <div class="row row-cols-12 mb-2">
                                    <div class="col-xl-3 fac_section_div">
                                        <label class="form-label">Cedant Commission rate(%)</label>
                                        <input type="text" class="form-inputs fac_section" aria-label="comm_rate"
                                            id="comm_rate" name="comm_rate" required>
                                    </div>
                                    <div class="col-xl-3 fac_section_div">
                                        <label class="form-label">Cedant Commission Amount</label>
                                        <input type="text" class="form-inputs fac_section" aria-label="comm_amt"
                                            id="comm_amt" name="comm_amt"
                                            onkeyup="this.value=numberWithCommas(this.value)" required>
                                    </div>
                                    <div class="col-xl-3 fac_section_div reins_comm_type_div">
                                        <label class="form-label">Reinsurer Commission Type</label>
                                        <div class="cover-card">
                                            <select class="form-inputs section select2 fac_section reins_comm_type"
                                                name="reins_comm_type" id="reins_comm_type" required>
                                                <option value="">Choose Reinsurer Commission Type</option>
                                                <option value="R">Rate</option>
                                                <option value="A">Amount</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3  fac_section_div reins_comm_rate_div">
                                        <label class="form-label">Reinsurer Commission rate(%)</label>
                                        <input type="text" class="form-inputs fac_section reins_comm_rate"
                                            aria-label="reins_comm_rate" id="reins_comm_rate" name="reins_comm_rate"
                                            onkeyup="this.value=numberWithCommas(this.value)" required disabled>
                                    </div>
                                </div>

                                <div class="row row-cols-12 mb-2">
                                    <div class="col-xl-3 fac_section_div reins_comm_amt_div">
                                        <label class="form-label">Reinsurer Commission Amount</label>
                                        <input type="text" class="form-inputs fac_section reins_comm_amt"
                                            aria-label="reins_comm_amt" id="reins_comm_amt" name="reins_comm_amt"
                                            onkeyup="this.value=numberWithCommas(this.value)"
                                            onchange="this.value=numberWithCommas(this.value)" required>
                                    </div>
                                    <div class="col-xl-3 fac_section_div">
                                        <label class="form-label">Brokerage Commission Type</label>
                                        <div class="cover-card">
                                            <select name="brokerage_comm_type" id="brokerage_comm_type"
                                                class="form-inputs section select2">
                                                <option value="">--Select basis--</option>
                                                <option value="R">Rate (<small><i>Reinsurer rate -
                                                            Cedant rate</i></small>)
                                                </option>
                                                <option value="A">Quoted Amount</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 fac_section_div brokerage_comm_amt_div">
                                        <label class="form-label">Brokerage Amount</label>
                                        <input type="text" class="form-inputs fac_section amount"
                                            id="brokerage_comm_amt" name="brokerage_comm_amt" value="0">
                                    </div>
                                    <div class="col-xl-3 fac_section_div brokerage_comm_rate_div">
                                        <label class="form-label" id="brokerage_comm_rate_label">Brokerage
                                            Rate</label>
                                        <input type="text" class="form-inputs fac_section amount"
                                            id="brokerage_comm_rate" name="brokerage_comm_rate" value="">
                                    </div>
                                </div>

                                <div class="row row-cols-12 mb-2">
                                    <div class="col-xl-3 fac_section_div brokerage_comm_rate_amt_div">
                                        <label class="form-label" id="brokerage_comm_rate_amount_label">Brokerage Rate
                                            Amount</label>
                                        <input type="text" class="form-inputs fac_section amount"
                                            id="brokerage_comm_rate_amt" name="brokerage_comm_amt" value="0"
                                            readonly>
                                    </div>
                                    <input type="hidden" class="vat_charged fac_section" id="vat_charged"
                                        name="vat_charged" value="0">
                                </div>
                            </div>
                        </div>

                        {{-- Engagement Details Section --}}
                        <div id="engagementDetails">
                            <hr />
                            <div class="card-header">
                                <div class="card-title text-danger">Engagement Details</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <x-OnboardingInputDiv>
                                        <x-SearchableSelect name="engage_type" id="engage_type" req="required"
                                            inputLabel="Nature of engagement">
                                            <option value="">Select engagement type </option>
                                            @foreach ($engage_types as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}
                                                </option>
                                            @endforeach
                                        </x-SearchableSelect>
                                    </x-OnboardingInputDiv>
                                    <x-OnboardingInputDiv id="lead_owner_div">
                                        <x-SearchableSelect name="lead_owner" id="lead_owner" req="required"
                                            inputLabel="Prospect Lead">
                                            <option value="">Select prospect Lead</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">
                                                    {{ firstUpper($user->name) }}</option>
                                            @endforeach
                                        </x-SearchableSelect>
                                    </x-OnboardingInputDiv>
                                    <x-OnboardingInputDiv id="date_effective_div">
                                        <x-DateInput name="effective_date" id="effective_date"
                                            placeholder="Enter cover start date" inputLabel="Cover Start Date"
                                            req="" />
                                    </x-OnboardingInputDiv>
                                    <x-OnboardingInputDiv id="date_closing_div">
                                        <x-DateInput name="closing_date" id="closing_date"
                                            placeholder="Enter bid closing date" inputLabel="Cover End  Date"
                                            req="" />
                                    </x-OnboardingInputDiv>
                                </div>

                            </div>
                        </div>

                        <div class="card-body">
                            <div style="float:right" class="mb-3">
                                <button type="button" name="previous" class="btn btn-light me-2" id="cancel"><span
                                        class="bx bx-times"></span> Cancel</button>

                                <button type="submit" id="submits" class="btn btn-primary"><span
                                        class="bx bx-save"></span>
                                    Save Details
                                </button>
                                @if (!is_null($prospect))
                                    <button type="button" class="btn btn-outline-primary" data-prospect=""
                                        id="sales_mngt"><span class="fa fa-arrow-right"></span> Submit to
                                        Sales</button>
                                    <button style="display:none" type="button" class="btn btn-outline-primary"
                                        data-prospect="" id="process_pq"><span class="fa fa-arrow-right"></span>
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
