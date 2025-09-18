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
                    <div class="card custom-card card-align-center">
                        <div class="card-header">
                            <div class="card-title">Contact Details</div>
                        </div>
                        <div class="card-body">
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
                                {{-- cedant commented --}}
                                {{-- <x-OnboardingInputDiv>
                                            <x-SearchableSelect name="customer_id" id="customer_id" req="required"
                                                inputLabel="Choose Cedant">
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}
                                {{-- <x-OnboardingInputDiv id="prequalification_div">
                                            <x-SearchableSelect name="" id="" req="required"
                                                inputLabel="Type Of Treaty">
                                                <option value="">Facultative Pipeline</option>
                                                <option value="">Treaty Pipeline</option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}
                                <x-OnboardingInputDiv>
                                    <x-SearchableSelect name="customer_id" id="customer_id" req=""
                                        inputLabel="Cedant">
                                        <option value="">---Select Cedant---</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->customer_id }}">
                                                {{ firstUpper($customer->name) }}
                                                <!-- Access the 'name' property -->
                                            </option>
                                        @endforeach
                                        <!-- Dynamically populated options will be inserted here -->


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
                                {{-- <x-OnboardingInputDiv id="countryDiv">
                                            <x-Input name="lead_name" id="lead_name" req="required"
                                                inputLabel=" Lead Name" req="required" />
                                        </x-OnboardingInputDiv> --}}
                                <x-OnboardingInputDiv id="leadNameDiv">
                                    <x-Input name="lead_name" id="lead_name" req="required" inputLabel="Lead Name"
                                        oninput="this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());" />
                                    <div id="lead_name_results" class="dropdown-menu" style="display: none;">
                                    </div>
                                    <div class="error-message" id="lead_name_error"></div>
                                </x-OnboardingInputDiv>
                                {{-- <x-OnboardingInputDiv>
                                            <x-SearchableSelect name="ccustomer_id" id="ccustomer_id" req="required"
                                                inputLabel="Cedant">
                                                <select id="ccustomer_id" name="ccustomer_id" class="form-control">

                                                    <!-- Dynamically populated options will be inserted here -->
                                                </select>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}
                                <x-OnboardingInputDiv id="lead_year_div">
                                    <x-SearchableSelect name="lead_year" id="lead_year" req="required"
                                        inputLabel="Year">
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
                                {{-- <x-OnboardingInputDiv class="new_pros">
                                            <x-SearchableSelect name="client_type" id="client_type" req="required"
                                                inputLabel="Client Type">
                                                <option value="">Select prospect type</option>
                                                <option value="C">Corporate</option>
                                                <option value="I">Retail</option>
                                                <option value="N">NGO</option>
                                                <option value="G">Government</option>
                                                <option value="S">SME's</option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="organic_growth_div">
                                            <x-SearchableSelect name="client_select" id="client_select" req="required"
                                                inputLabel="Select Client">
                                                <option value="">Select client</option>
                                                @foreach ($clients as $client)
                                                <option value="{{ $client->global_customer_id }}">
                                                    {{ $client->full_name }}</option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="full_name_div" class="new_pros">
                                            <label for="full_name_input">Full Name</label>
                                            <input type="text" id="full_name_input" name="full_name"
                                                class="form-control" placeholder="Search full name" required>
                                            <div id="full_name_results" class="dropdown-menu" style="display: none;">
                                            </div>
                                            <div class="error-message" id="full_name_error"></div>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="salutation" class="new_pros">
                                            <x-SearchableSelect name="salutation" id="salutation" req="required"
                                                inputLabel="Salutation">
                                                <option value="">Select salutation</option>
                                                @foreach ($salutations as $salutation)
                                                <option value="{{ $salutation->name }}">{{ $salutation->name }}
                                                </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="industry_div" class="new_pros">
                                            <x-SearchableSelect name="industry" id="industry" req="required"
                                                inputLabel="Industry">
                                                <option value="">Select industry</option>
                                                @foreach ($industries as $industry)
                                                <option value="{{ $industry->name }}">{{ $industry->name }}
                                                </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{--
                                        <x-OnboardingInputDiv>
                                            <x-Input name="prod_cost" id="prod_cost" inputLabel="Cost of Production"
                                                req="required" oninput="formatNumber(this)" />
                                        </x-OnboardingInputDiv> --}}

                                <x-OnboardingInputDiv id="countryDiv">
                                    <x-SearchableSelect name="country_code" class="form" id="country" req="required"
                                        inputLabel="Country">
                                        <option value="">Select country code</option>
                                        @foreach ($countries as $country)
                                            <option @if ($country->country_iso == 'KEN') selected @endif
                                                value="{{ $country->country_iso }}">
                                                ({{ $country->country_code }})
                                                {{ $country->country_name }}
                                                {{ $country->country_iso }}</option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </x-OnboardingInputDiv>

                                <!--branch-->
                                <div class="col-sm-3">
                                    <label class="form-label required">Branch</label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2" name="branchcode" id="branchcode"
                                            required>
                                            <option selected value="">Choose Branch</option>
                                            @foreach ($branches as $branch)
                                                @if ($branch->status == 'A')
                                                    <option value="{{ $branch->branch_code }}">
                                                        {{ firstUpper($branch->branch_name) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card custom-card card-align-top">
                        <div class="card-header">
                            <div class="card-title">Cedant Details</div>
                        </div>
                        <div class="card-body">
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
                                {{-- cedant commented --}}
                                {{-- <x-OnboardingInputDiv>
                                            <x-SearchableSelect name="customer_id" id="customer_id" req="required"
                                                inputLabel="Choose Cedant">
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}
                                {{-- <x-OnboardingInputDiv id="prequalification_div">
                                            <x-SearchableSelect name="" id="" req="required"
                                                inputLabel="Type Of Treaty">
                                                <option value="">Facultative Pipeline</option>
                                                <option value="">Treaty Pipeline</option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}
                                <x-OnboardingInputDiv>
                                    <x-SearchableSelect name="customer_id" id="customer_id" req=""
                                        inputLabel="Cedant">
                                        <option value="">---Select Cedant---</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->customer_id }}">
                                                {{ firstUpper($customer->name) }}
                                                <!-- Access the 'name' property -->
                                            </option>
                                        @endforeach
                                        <!-- Dynamically populated options will be inserted here -->


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
                                {{-- <x-OnboardingInputDiv id="countryDiv">
                                            <x-Input name="lead_name" id="lead_name" req="required"
                                                inputLabel=" Lead Name" req="required" />
                                        </x-OnboardingInputDiv> --}}
                                <x-OnboardingInputDiv id="leadNameDiv">
                                    <x-Input name="lead_name" id="lead_name" req="required" inputLabel="Lead Name"
                                        oninput="this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());" />
                                    <div id="lead_name_results" class="dropdown-menu" style="display: none;">
                                    </div>
                                    <div class="error-message" id="lead_name_error"></div>
                                </x-OnboardingInputDiv>
                                {{-- <x-OnboardingInputDiv>
                                            <x-SearchableSelect name="ccustomer_id" id="ccustomer_id" req="required"
                                                inputLabel="Cedant">
                                                <select id="ccustomer_id" name="ccustomer_id" class="form-control">

                                                    <!-- Dynamically populated options will be inserted here -->
                                                </select>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}
                                <x-OnboardingInputDiv id="lead_year_div">
                                    <x-SearchableSelect name="lead_year" id="lead_year" req="required"
                                        inputLabel="Year">
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
                                {{-- <x-OnboardingInputDiv class="new_pros">
                                            <x-SearchableSelect name="client_type" id="client_type" req="required"
                                                inputLabel="Client Type">
                                                <option value="">Select prospect type</option>
                                                <option value="C">Corporate</option>
                                                <option value="I">Retail</option>
                                                <option value="N">NGO</option>
                                                <option value="G">Government</option>
                                                <option value="S">SME's</option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="organic_growth_div">
                                            <x-SearchableSelect name="client_select" id="client_select" req="required"
                                                inputLabel="Select Client">
                                                <option value="">Select client</option>
                                                @foreach ($clients as $client)
                                                <option value="{{ $client->global_customer_id }}">
                                                    {{ $client->full_name }}</option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="full_name_div" class="new_pros">
                                            <label for="full_name_input">Full Name</label>
                                            <input type="text" id="full_name_input" name="full_name"
                                                class="form-control" placeholder="Search full name" required>
                                            <div id="full_name_results" class="dropdown-menu" style="display: none;">
                                            </div>
                                            <div class="error-message" id="full_name_error"></div>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="salutation" class="new_pros">
                                            <x-SearchableSelect name="salutation" id="salutation" req="required"
                                                inputLabel="Salutation">
                                                <option value="">Select salutation</option>
                                                @foreach ($salutations as $salutation)
                                                <option value="{{ $salutation->name }}">{{ $salutation->name }}
                                                </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="industry_div" class="new_pros">
                                            <x-SearchableSelect name="industry" id="industry" req="required"
                                                inputLabel="Industry">
                                                <option value="">Select industry</option>
                                                @foreach ($industries as $industry)
                                                <option value="{{ $industry->name }}">{{ $industry->name }}
                                                </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{--
                                        <x-OnboardingInputDiv>
                                            <x-Input name="prod_cost" id="prod_cost" inputLabel="Cost of Production"
                                                req="required" oninput="formatNumber(this)" />
                                        </x-OnboardingInputDiv> --}}

                                <x-OnboardingInputDiv id="countryDiv">
                                    <x-SearchableSelect name="country_code" class="form" id="country" req="required"
                                        inputLabel="Country">
                                        <option value="">Select country code</option>
                                        @foreach ($countries as $country)
                                            <option @if ($country->country_iso == 'KEN') selected @endif
                                                value="{{ $country->country_iso }}">
                                                ({{ $country->country_code }})
                                                {{ $country->country_name }}
                                                {{ $country->country_iso }}</option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </x-OnboardingInputDiv>

                                <!--branch-->
                                <div class="col-sm-3">
                                    <label class="form-label required">Branch</label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2" name="branchcode" id="branchcode"
                                            required>
                                            <option selected value="">Choose Branch</option>
                                            @foreach ($branches as $branch)
                                                @if ($branch->status == 'A')
                                                    <option value="{{ $branch->branch_code }}">
                                                        {{ firstUpper($branch->branch_name) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="card custom-card card-align-center">
                        <div class="card-header">
                            <div class="card-title">Contact Details</div>
                        </div>
                        <div class="card-body">
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
                                {{-- cedant commented --}}
                                {{-- <x-OnboardingInputDiv>
                                            <x-SearchableSelect name="customer_id" id="customer_id" req="required"
                                                inputLabel="Choose Cedant">
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}
                                {{-- <x-OnboardingInputDiv id="prequalification_div">
                                            <x-SearchableSelect name="" id="" req="required"
                                                inputLabel="Type Of Treaty">
                                                <option value="">Facultative Pipeline</option>
                                                <option value="">Treaty Pipeline</option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}
                                <x-OnboardingInputDiv>
                                    <x-SearchableSelect name="customer_id" id="customer_id" req=""
                                        inputLabel="Cedant">
                                        <option value="">---Select Cedant---</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->customer_id }}">
                                                {{ firstUpper($customer->name) }}
                                                <!-- Access the 'name' property -->
                                            </option>
                                        @endforeach
                                        <!-- Dynamically populated options will be inserted here -->


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
                                {{-- <x-OnboardingInputDiv id="countryDiv">
                                            <x-Input name="lead_name" id="lead_name" req="required"
                                                inputLabel=" Lead Name" req="required" />
                                        </x-OnboardingInputDiv> --}}
                                <x-OnboardingInputDiv id="leadNameDiv">
                                    <x-Input name="lead_name" id="lead_name" req="required" inputLabel="Lead Name"
                                        oninput="this.value = this.value.replace(/\b\w/g, char => char.toUpperCase());" />
                                    <div id="lead_name_results" class="dropdown-menu" style="display: none;">
                                    </div>
                                    <div class="error-message" id="lead_name_error"></div>
                                </x-OnboardingInputDiv>
                                {{-- <x-OnboardingInputDiv>
                                            <x-SearchableSelect name="ccustomer_id" id="ccustomer_id" req="required"
                                                inputLabel="Cedant">
                                                <select id="ccustomer_id" name="ccustomer_id" class="form-control">

                                                    <!-- Dynamically populated options will be inserted here -->
                                                </select>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}
                                <x-OnboardingInputDiv id="lead_year_div">
                                    <x-SearchableSelect name="lead_year" id="lead_year" req="required"
                                        inputLabel="Year">
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
                                {{-- <x-OnboardingInputDiv class="new_pros">
                                            <x-SearchableSelect name="client_type" id="client_type" req="required"
                                                inputLabel="Client Type">
                                                <option value="">Select prospect type</option>
                                                <option value="C">Corporate</option>
                                                <option value="I">Retail</option>
                                                <option value="N">NGO</option>
                                                <option value="G">Government</option>
                                                <option value="S">SME's</option>
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="organic_growth_div">
                                            <x-SearchableSelect name="client_select" id="client_select" req="required"
                                                inputLabel="Select Client">
                                                <option value="">Select client</option>
                                                @foreach ($clients as $client)
                                                <option value="{{ $client->global_customer_id }}">
                                                    {{ $client->full_name }}</option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="full_name_div" class="new_pros">
                                            <label for="full_name_input">Full Name</label>
                                            <input type="text" id="full_name_input" name="full_name"
                                                class="form-control" placeholder="Search full name" required>
                                            <div id="full_name_results" class="dropdown-menu" style="display: none;">
                                            </div>
                                            <div class="error-message" id="full_name_error"></div>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="salutation" class="new_pros">
                                            <x-SearchableSelect name="salutation" id="salutation" req="required"
                                                inputLabel="Salutation">
                                                <option value="">Select salutation</option>
                                                @foreach ($salutations as $salutation)
                                                <option value="{{ $salutation->name }}">{{ $salutation->name }}
                                                </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{-- <x-OnboardingInputDiv id="industry_div" class="new_pros">
                                            <x-SearchableSelect name="industry" id="industry" req="required"
                                                inputLabel="Industry">
                                                <option value="">Select industry</option>
                                                @foreach ($industries as $industry)
                                                <option value="{{ $industry->name }}">{{ $industry->name }}
                                                </option>
                                                @endforeach
                                            </x-SearchableSelect>
                                        </x-OnboardingInputDiv> --}}

                                {{--
                                        <x-OnboardingInputDiv>
                                            <x-Input name="prod_cost" id="prod_cost" inputLabel="Cost of Production"
                                                req="required" oninput="formatNumber(this)" />
                                        </x-OnboardingInputDiv> --}}

                                <x-OnboardingInputDiv id="countryDiv">
                                    <x-SearchableSelect name="country_code" class="form" id="country" req="required"
                                        inputLabel="Country">
                                        <option value="">Select country code</option>
                                        @foreach ($countries as $country)
                                            <option @if ($country->country_iso == 'KEN') selected @endif
                                                value="{{ $country->country_iso }}">
                                                ({{ $country->country_code }})
                                                {{ $country->country_name }}
                                                {{ $country->country_iso }}</option>
                                        @endforeach
                                    </x-SearchableSelect>
                                </x-OnboardingInputDiv>

                                <!--branch-->
                                <div class="col-sm-3">
                                    <label class="form-label required">Branch</label>
                                    <div class="cover-card">
                                        <select class="form-inputs section select2" name="branchcode" id="branchcode"
                                            required>
                                            <option selected value="">Choose Branch</option>
                                            @foreach ($branches as $branch)
                                                @if ($branch->status == 'A')
                                                    <option value="{{ $branch->branch_code }}">
                                                        {{ firstUpper($branch->branch_name) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
