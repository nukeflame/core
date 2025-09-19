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
                        <div class="card-header">
                            <div class="card-title text-danger">Cedant Details</div>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row mb-4">
                                {{-- Type of Business --}}
                                <x-OnboardingInputDiv id="prequalification_div">
                                    <x-SearchableSelect name="type_of_bus" id="type_of_bus" req="required"
                                        inputLabel="Type of Business">
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
                                        inputLabel="Cedant">
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
                                        inputLabel="Lead Type">
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
                                    <div id="lead_name_results" class="dropdown-menu" style="display: none;"></div>
                                    <div class="error-message" id="lead_name_error"></div>
                                </x-OnboardingInputDiv>

                                {{-- Lead Year --}}
                                <x-OnboardingInputDiv id="lead_year_div">
                                    <x-SearchableSelect name="lead_year" id="lead_year" req="required" inputLabel="Year">
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

                        {{-- Contact Details Section --}}
                        {{-- @include('prospect.sections.contact-details') --}}

                        {{-- Insurance Details Section --}}
                        {{-- @include('prospect.sections.insurance-details') --}}

                        {{-- Engagement Details Section --}}
                        {{-- @include('prospect.sections.engagement-details') --}}

                        {{-- Form Actions --}}
                        <div class="card-footer d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="cancelBtn">
                                <i class="bx bx-x"></i> Cancel
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                <i class="bx bx-save"></i> Save Details
                            </button>
                            @if (!is_null($prospect))
                                <button type="button" class="btn btn-outline-primary" id="submitToSalesBtn"
                                    data-prospect="{{ $prospect->id ?? '' }}">
                                    <i class="fa fa-arrow-right"></i> Submit to Sales
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script src="{{ asset('Bd_views/intermediaries/partials/js/prospect-onboarding.js') }}"></script>
    <script>
        $(document).ready(function() {

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
                        checkUserExists: "{{ route('check_user_exists') }}"
                    },
                    csrfToken: "{{ csrf_token() }}"
                });
            } else {
                console.error('ProspectOnboarding module not loaded');
            }
        });
    </script>
@endpush
