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
    </style>
@endsection

@section('content')
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

        <div class="row mt-3">
            <div class="col-xl-12">
                <div class="card custom-card card-align-top">
                    <div class="card-header">
                        <div class="card-title">Cedant Details</div>
                    </div>
                    <div class="card-body pb-0">
                        <div class="row mb-4">
                            <x-OnboardingInputDiv id="prequalification_div">
                                <x-SearchableSelect name="type_of_bus" id="type_of_bus" req="required"
                                    inputLabel="Type of Business">
                                    <option value="">--Select type of business--</option>
                                    @foreach ($types_of_bus as $type_of_bus)
                                        @if (in_array($type_of_bus->bus_type_id, ['FPR', 'FNP']))
                                            <option value="{{ $type_of_bus->bus_type_id }}">
                                                {{ firstUpper($type_of_bus->bus_type_name) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </x-SearchableSelect>
                            </x-OnboardingInputDiv>
                            <x-OnboardingInputDiv>
                                <x-SearchableSelect name="customer_id" id="customer_id" req="" inputLabel="Cedant">
                                    <option value="">---Select Cedant---</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->customer_id }}">
                                            {{ firstUpper($customer->name) }}
                                        </option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-OnboardingInputDiv>
                            <x-OnboardingInputDiv id="countryDiv">
                                <x-SearchableSelect name="client_type" id="client_type" req="required"
                                    inputLabel="Lead Type">
                                    <option value="">Select Lead type</option>
                                    @foreach ($customer_types as $cusType)
                                        <option value="{{ $cusType->type_name }}">
                                            {{ firstUpper($cusType->type_name) }}
                                        </option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-OnboardingInputDiv>
                            <x-OnboardingInputDiv id="leadNameDiv">
                                <x-Input name="lead_name" id="lead_name" req="required" inputLabel="Lead Name"
                                    oninput="this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());" />
                                <div id="lead_name_results" class="dropdown-menu" style="display: none;">
                                </div>
                                <div class="error-message" id="lead_name_error"></div>
                            </x-OnboardingInputDiv>
                            <x-OnboardingInputDiv id="lead_year_div">
                                <x-SearchableSelect name="lead_year" id="lead_year" req="required" inputLabel="Year">
                                    <option value="">Select year</option>
                                    @foreach ($pipeYear as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-OnboardingInputDiv>
                            <x-OnboardingInputDiv>
                                <x-SearchableSelect name="client_category" id="client_category" req="required"
                                    inputLabel="Insured Category">
                                    <option value="">Select prospect category</option>
                                    <option value="N">New prospect</option>
                                    <option value="O">Organic growth</option>
                                </x-SearchableSelect>
                            </x-OnboardingInputDiv>
                            <x-OnboardingInputDiv id="countryDiv">
                                <x-SearchableSelect name="country_code" class="form" id="country" req="required"
                                    inputLabel="Country">
                                    <option value="">Select country code</option>
                                    @foreach ($countries as $country)
                                        <option @if ($country->country_iso == 'KEN') selected @endif
                                            value="{{ $country->country_iso }}">
                                            {{ $country->country_code ? $country->country_code : '' }}
                                            {{ $country->country_name }}
                                            {{ $country->country_iso }}</option>
                                    @endforeach
                                </x-SearchableSelect>
                            </x-OnboardingInputDiv>
                            <x-OnboardingInputDiv id="branchcode">
                                <x-SearchableSelect name="branchcode" id="branchcode" req="required" inputLabel="Branch">
                                    <option value="">--Select branch--</option>
                                    @foreach ($branches as $branch)
                                        @if ($branch->status == 'A')
                                            <option value="{{ $branch->branch_code }}">
                                                {{ firstUpper($branch->branch_name) }}</option>
                                        @endif
                                    @endforeach
                                </x-SearchableSelect>
                            </x-OnboardingInputDiv>
                        </div>
                    </div>
                    <hr />
                    <div class="card-header">
                        <div class="card-title">Contact Details</div>
                    </div>
                    <div class="card-body pb-0">
                        <div id="contactsContainer">
                            <div class="row mb-4 contactsContainers" data-counter="0">
                                <x-OnboardingInputDiv>
                                    <x-Input name="contact_name[]" id="contact_name-0" class="contact_name-0"
                                        placeholder="Enter name" inputLabel="Contact Full Name" req="required"
                                        oninput="this.value = this.value.toUpperCase();" />
                                    <div id="full_name_results_0" class="dropdown-menu full-name-results"
                                        style="display: none;"></div>
                                    <div class="error-message" id="full_name_error_0"></div>
                                </x-OnboardingInputDiv>
                                <x-OnboardingInputDiv>
                                    <x-EmailInput id="email-0" name="email[]" req="required" inputLabel="Email Address"
                                        placeholder="Enter email" />
                                </x-OnboardingInputDiv>
                                <x-OnboardingInputDiv>
                                    <x-NumberInput id="phone_number-0" name="phone_number[]" req="required"
                                        inputLabel="Mobile." class="phone" placeholder="Enter phone number" />
                                </x-OnboardingInputDiv>
                                <div class="col-md-3 col-sm-12 mt-2">
                                    <Label class="form-label fw-bold" for="telephone-0">Telephone</Label>
                                    <div class="input-group mb-3">
                                        <input type="number" id="telephone-0" class="form-control color-r"
                                            name="telephone[]" placeholder="Enter telephone number" />
                                        <button class="btn btn-primary add-contact" type="button" id="add-contact-0"
                                            data-counter="0">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                </div>
            </div>
        </div>
    @endsection
