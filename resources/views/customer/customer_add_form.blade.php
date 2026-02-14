@extends('layouts.app', [
    'pageTitle' => (isset($customer) ? __('Edit Customer') : __('Create Customer')) . ' - ' . $company->company_name,
])

@section('content')
    @php
        $editCustomer = $customer ?? null;
    @endphp
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div>
            <h1 class="page-title fw-semibold fs-18 mb-0">
                <i class="bi bi-building me-2"></i>
                {{ isset($customer) ? __('Edit Customer') : __('Create New Customer') }}
            </h1>
            <p class="text-muted small mb-0">
                {{ isset($customer) ? __('Review and update customer details in this unified form') : __('Add a new customer or partner to the system') }}
            </p>
        </div>
        <div class="ms-md-1 ms-0 mt-3 mt-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.info') }}">{{ __('Customers') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ isset($customer) ? __('Edit') : __('Create') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ isset($customer) ? route('customer.update', $customer->customer_id) : route('customer.store') }}" method="POST" id="customerForm"
        data-redirect-url="{{ route('customer.info') }}" novalidate aria-label="{{ __('Customer creation form') }}">
        @csrf
        @if (isset($customer))
            @method('PUT')
        @endif

        <div class="card border-0 shadow-sm mb-4" id="section-essential">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('Customer Name') }}
                    </h5>
                    <span class="badge bg-light text-primary">{{ __('Required') }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="partnerName" class="form-label required">
                            {{ __('Legal/Trading Name') }}
                            <i class="bi bi-question-circle text-muted px-1" data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="{{ __('Enter the official registered name of the organization') }}"></i>
                        </label>
                        <input type="text" class="form-control @error('partnerName') is-invalid @enderror"
                            id="partnerName" name="partnerName" value="{{ old('partnerName', $editCustomer?->name ?? '') }}"
                            placeholder="{{ __('e.g., ABC Insurance Ltd') }}" aria-required="true"
                            aria-describedby="partnerName-help"
                            aria-invalid="{{ $errors->has('partnerName') ? 'true' : 'false' }}" maxlength="255"
                            autocomplete="organization" autofocus>
                        <small id="partnerName-help" class="form-text text-muted">
                            <span id="partnerNameCount">0</span>/255 {{ __('characters') }}
                        </small>
                        @error('partnerName')
                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="customerType" class="form-label required">
                            {{ __('Entity Type') }}
                            <i class="bi bi-question-circle text-muted px-1" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="{{ __('Select the applicable entity type') }}"></i>
                        </label>
                        <select class="form-select select2 @error('customerType') is-invalid @enderror" multiple
                            id="customerType" name="customerType[]" aria-required="true"
                            aria-describedby="customerType-help" data-placeholder="{{ __('Select entity type...') }}">
                            <option value="" data-slug="">{{ __('-- Select Entity Type --') }}</option>
                            @foreach ($type_of_cust as $cust_type)
                                <option value="{{ $cust_type->type_id }}" data-slug="{{ $cust_type->slug }}"
                                    {{ in_array((string) $cust_type->type_id, array_map('strval', (array) old('customerType', $editCustomer?->customer_type ?? []))) ? 'selected' : '' }}>
                                    {{ $cust_type->type_name }}
                                </option>
                            @endforeach
                        </select>
                        <small id="customerType-help" class="form-text text-muted">
                            {{ __('Different fields will appear based on the entity type selected') }}
                        </small>
                        @error('customerType')
                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label required">
                            {{ __('Primary Email Address') }}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $editCustomer?->email ?? '') }}" placeholder="{{ __('customer@example.com') }}"
                                aria-required="true" autocomplete="email" inputmode="email">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block" role="alert">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="telephone" class="form-label required">
                            {{ __('Primary Telephone') }}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                id="telephone" name="telephone" value="{{ old('telephone', $editCustomer?->telephone ?? '') }}"
                                placeholder="{{ __('+254 700 000000') }}" aria-required="true" autocomplete="tel"
                                inputmode="tel">
                        </div>
                        <small class="form-text text-muted">
                            {{ __('International format preferred (e.g., +254 for Kenya)') }}
                        </small>
                        @error('telephone')
                            <div class="invalid-feedback d-block" role="alert">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" id="section-legal">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        {{ __('Legal & Identification') }}
                    </h6>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none section-toggle"
                        data-bs-toggle="collapse" data-bs-target="#legalSection" aria-expanded="true"
                        aria-controls="legalSection">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="collapse show" id="legalSection">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="incorporationNo" class="form-label required">
                                {{ __('Registration/Incorporation Number') }}
                            </label>
                            <input type="text" class="form-control @error('incorporationNo') is-invalid @enderror"
                                id="incorporationNo" name="incorporationNo" value="{{ old('incorporationNo', $editCustomer?->registration_no ?? '') }}"
                                placeholder="{{ __('e.g., PVT-XXXXXX') }}" aria-required="true" autocomplete="off">
                            @error('incorporationNo')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="taxNo" class="form-label required">
                                {{ __('Tax Identification Number') }}
                            </label>
                            <input type="text" class="form-control @error('taxNo') is-invalid @enderror"
                                id="taxNo" name="taxNo" value="{{ old('taxNo', $editCustomer?->tax_no ?? '') }}"
                                placeholder="{{ __('e.g., AXXXXXXXXX') }}" aria-required="true" autocomplete="off">
                            <small class="form-text text-muted">
                                {{ __('VAT, GST, or equivalent tax number') }}
                            </small>
                            @error('taxNo')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="identityType" class="form-label required">
                                {{ __('Identity Document Type') }}
                            </label>
                            <select class="form-select select2 @error('identityType') is-invalid @enderror"
                                id="identityType" name="identityType" aria-required="true"
                                data-placeholder="{{ __('Select document type...') }}">
                                <option value="">{{ __('-- Select --') }}</option>
                                @foreach ($partnersId as $pId)
                                    <option value="{{ $pId->identification_type }}"
                                        {{ old('identityType', $editCustomer?->identity_number_type ?? '') == $pId->identification_type ? 'selected' : '' }}>
                                        {{ $pId->identification_type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('identityType')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="identityNo" class="form-label required">
                                {{ __('Identity Document Number') }}
                            </label>
                            <input type="text" class="form-control @error('identityNo') is-invalid @enderror"
                                id="identityNo" name="identityNo" value="{{ old('identityNo', $editCustomer?->identity_number ?? '') }}"
                                placeholder="{{ __('Enter document number') }}" aria-required="true" autocomplete="off">
                            @error('identityNo')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="website" class="form-label">
                                {{ __('Website') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-globe"></i>
                                </span>
                                <input type="text" class="form-control @error('website') is-invalid @enderror"
                                    id="website" name="website" value="{{ old('website', $editCustomer?->website ?? '') }}"
                                    placeholder="{{ __('www.geminiainsurance.com') }}" autocomplete="url"
                                    inputmode="url">
                            </div>
                            @error('website')
                                <div class="invalid-feedback d-block" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        {{--
                            Dynamic fields will be added here by JavaScript based on entity type:
                            - Reinsurer: securityRating, ratingAgency, ratingDate, amlDetails
                            - Cedant: regulatorLicenseNo, licensingTerritory, amlDetails
                            - Reinsurance Broker: regulatorLicenseNo, licensingAuthority, licensingTerritory, amlDetails
                            - Insurance Broker: regulatorLicenseNo, licensingAuthority, licensingTerritory, amlDetails
                            - Insured: insuredType, industryOccupation, dateOfBirthIncorporation, amlDetails
                        --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Address Information Section --}}
        <div class="card border-0 shadow-sm mb-4" id="section-address">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-geo-alt me-2"></i>
                        {{ __('Address Information') }}
                    </h6>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none section-toggle"
                        data-bs-toggle="collapse" data-bs-target="#addressSection" aria-expanded="true"
                        aria-controls="addressSection">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="collapse show" id="addressSection">
                <div class="card-body">
                    <fieldset>
                        <legend class="visually-hidden">{{ __('Physical Address') }}</legend>
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="country" class="form-label required">
                                    {{ __('Country of Incorporation/Citizenship') }}
                                </label>
                                <select class="form-select select2 @error('country') is-invalid @enderror" id="country"
                                    name="country" aria-required="true" autocomplete="country"
                                    data-placeholder="{{ __('Select country...') }}">
                                    <option value="">{{ __('-- Select --') }}</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->country_iso }}"
                                            {{ old('country', $editCustomer?->country_iso ?? 'KE') == $country->country_iso ? 'selected' : '' }}
                                            data-code="{{ $country->country_iso }}">
                                            {{ $country->country_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="street" class="form-label required">
                                    {{ __('Street Address') }}
                                </label>
                                <input type="text" class="form-control @error('street') is-invalid @enderror"
                                    id="street" name="street" value="{{ old('street', $editCustomer?->street ?? '') }}"
                                    placeholder="{{ __('Building name, street name, P.O. Box') }}" aria-required="true"
                                    autocomplete="street-address">
                                @error('street')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="city" class="form-label required">
                                    {{ __('City/Town') }}
                                </label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    id="city" name="city" value="{{ old('city', $editCustomer?->city ?? '') }}"
                                    placeholder="{{ __('e.g., Nairobi') }}" aria-required="true"
                                    autocomplete="address-level2" list="cities">
                                <datalist id="cities">
                                    <option value="Nairobi">
                                    <option value="Mombasa">
                                    <option value="Kisumu">
                                    <option value="Nakuru">
                                    <option value="Eldoret">
                                </datalist>
                                @error('city')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-lg-4 d-none" id="stateField">
                                <label for="state" class="form-label">
                                    {{ __('State/Province') }}
                                </label>
                                <input type="text" class="form-control" id="state" name="state"
                                    value="{{ old('state') }}" placeholder="{{ __('Enter state/province') }}"
                                    autocomplete="address-level1">
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">
                                <label for="postalCode" class="form-label required">
                                    {{ __('Postal/ZIP Code') }}
                                </label>
                                <input type="text" class="form-control @error('postalCode') is-invalid @enderror"
                                    id="postalCode" name="postalCode" value="{{ old('postalCode', $editCustomer?->postal_address ?? '') }}"
                                    placeholder="{{ __('00000') }}" aria-required="true" autocomplete="postal-code">
                                @error('postalCode')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        {{-- Financial Information Section --}}
        <div class="card border-0 shadow-sm mb-4" id="section-financial">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        {{ __('Financial Information') }}
                    </h6>
                    <button type="button" class="btn btn-sm btn-link text-decoration-none section-toggle"
                        data-bs-toggle="collapse" data-bs-target="#financialSection" aria-expanded="false"
                        aria-controls="financialSection">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="collapse" id="financialSection">
                <div class="card-body">
                    <div class="alert alert-info" role="status">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('Financial ratings help assess partner reliability and creditworthiness') }}
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="financialRating" class="form-label">
                                {{ __('Financial Rating') }}
                                <i class="bi bi-question-circle text-muted px-1" data-bs-toggle="tooltip"
                                    title="{{ __('Credit rating or financial strength assessment') }}"></i>
                            </label>
                            <select class="form-select select2 @error('financialRating') is-invalid @enderror"
                                id="financialRating" name="financialRating"
                                data-placeholder="{{ __('Select rating...') }}">
                                <option value="">{{ __('-- Select --') }}</option>
                                <option value="AAA" {{ old('financialRating', $editCustomer?->financial_rate ?? '') == 'AAA' ? 'selected' : '' }}>AAA -
                                    {{ __('Excellent') }}</option>
                                <option value="AA" {{ old('financialRating', $editCustomer?->financial_rate ?? '') == 'AA' ? 'selected' : '' }}>AA -
                                    {{ __('Very Good') }}</option>
                                <option value="A" {{ old('financialRating', $editCustomer?->financial_rate ?? '') == 'A' ? 'selected' : '' }}>A -
                                    {{ __('Good') }}</option>
                                <option value="BBB" {{ old('financialRating', $editCustomer?->financial_rate ?? '') == 'BBB' ? 'selected' : '' }}>BBB -
                                    {{ __('Adequate') }}</option>
                                <option value="BB" {{ old('financialRating', $editCustomer?->financial_rate ?? '') == 'BB' ? 'selected' : '' }}>BB -
                                    {{ __('Fair') }}</option>
                                <option value="B" {{ old('financialRating', $editCustomer?->financial_rate ?? '') == 'B' ? 'selected' : '' }}>B -
                                    {{ __('Marginal') }}</option>
                                <option value="CCC" {{ old('financialRating', $editCustomer?->financial_rate ?? '') == 'CCC' ? 'selected' : '' }}>CCC -
                                    {{ __('Weak') }}</option>
                                <option value="NR" {{ old('financialRating', $editCustomer?->financial_rate ?? '') == 'NR' ? 'selected' : '' }}>NR -
                                    {{ __('Not Rated') }}</option>
                            </select>
                            @error('financialRating')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="agencyRating" class="form-label">
                                {{ __('Agency Rating') }}
                                <i class="bi bi-question-circle text-muted px-1" data-bs-toggle="tooltip"
                                    title="{{ __('Rating from credit rating agencies (e.g., S&P, Moody\'s)') }}"></i>
                            </label>
                            <input type="text" class="form-control @error('agencyRating') is-invalid @enderror"
                                id="agencyRating" name="agencyRating" value="{{ old('agencyRating', $editCustomer?->agency_rate ?? '') }}"
                                placeholder="{{ __('e.g., A+, Baa2') }}">
                            @error('agencyRating')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact Persons Section --}}
        <div class="card border-0 shadow-sm mb-4" id="section-contacts">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        {{ __('Contact Persons') }}
                    </h6>
                    <button type="button" class="btn btn-primary btn-sm" id="addContactBtn"
                        aria-label="{{ __('Add new contact person') }}">
                        <i class="bi bi-plus-circle me-1"></i>
                        {{ __('Add Contact') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="contactsContainer" role="list">
                    {{-- Primary Contact (Required) --}}
                    <div class="contact-item mb-3" role="listitem" data-contact-index="0">
                        <div class="card border-start border-primary border-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 contact-title">
                                    <i class="bi bi-person-badge me-2"></i>
                                    {{ __('Primary Contact') }}
                                </h6>
                                <span class="badge bg-primary">{{ __('Required') }}</span>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label class="form-label required">{{ __('Full Name') }}</label>
                                            <input type="text"
                                            class="form-control @error('contacts.0.name') is-invalid @enderror"
                                            name="contacts[0][name]" value="{{ old('contacts.0.name', $editCustomer?->primaryContact?->contact_name ?? '') }}"
                                            placeholder="{{ __('John Doe') }}" required autocomplete="name">
                                        @error('contacts.0.name')
                                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label class="form-label required">{{ __('Position/Title') }}</label>
                                            <input type="text"
                                            class="form-control @error('contacts.0.position') is-invalid @enderror"
                                            name="contacts[0][position]" value="{{ old('contacts.0.position', $editCustomer?->primaryContact?->contact_position ?? '') }}"
                                            placeholder="{{ __('e.g., CEO, Manager') }}" required
                                            autocomplete="organization-title">
                                        @error('contacts.0.position')
                                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label class="form-label required">{{ __('Mobile Number') }}</label>
                                            <input type="tel"
                                            class="form-control contact-phone @error('contacts.0.mobile') is-invalid @enderror"
                                            name="contacts[0][mobile]" value="{{ old('contacts.0.mobile', $editCustomer?->primaryContact?->contact_mobile_no ?? '') }}"
                                            placeholder="{{ __('+254 700 000000') }}" required autocomplete="tel">
                                        @error('contacts.0.mobile')
                                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label class="form-label required">{{ __('Email Address') }}</label>
                                            <input type="email"
                                            class="form-control @error('contacts.0.email') is-invalid @enderror"
                                            name="contacts[0][email]" value="{{ old('contacts.0.email', $editCustomer?->primaryContact?->contact_email ?? '') }}"
                                            placeholder="{{ __('john@example.com') }}" required autocomplete="email">
                                        @error('contacts.0.email')
                                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">{{ __('Department') }}</label>
                                        <select
                                            class="form-select select2 @error('contacts.0.department') is-invalid @enderror"
                                            name="contacts[0][department]">
                                            <option value="">{{ __('-- Select --') }}</option>
                                            <option value="executive"
                                                {{ old('contacts.0.department') == 'executive' ? 'selected' : '' }}>
                                                {{ __('Executive Management') }}
                                            </option>
                                            <option value="underwriting"
                                                {{ old('contacts.0.department') == 'underwriting' ? 'selected' : '' }}>
                                                {{ __('Underwriting') }}
                                            </option>
                                            <option value="claims"
                                                {{ old('contacts.0.department') == 'claims' ? 'selected' : '' }}>
                                                {{ __('Claims') }}
                                            </option>
                                            <option value="sales"
                                                {{ old('contacts.0.department') == 'sales' ? 'selected' : '' }}>
                                                {{ __('Sales') }}
                                            </option>
                                            <option value="marketing"
                                                {{ old('contacts.0.department') == 'marketing' ? 'selected' : '' }}>
                                                {{ __('Marketing') }}
                                            </option>
                                            <option value="finance"
                                                {{ old('contacts.0.department') == 'finance' ? 'selected' : '' }}>
                                                {{ __('Finance') }}
                                            </option>
                                            <option value="technical"
                                                {{ old('contacts.0.department') == 'technical' ? 'selected' : '' }}>
                                                {{ __('Technical') }}
                                            </option>
                                            <option value="operations"
                                                {{ old('contacts.0.department') == 'operations' ? 'selected' : '' }}>
                                                {{ __('Operations') }}
                                            </option>
                                            <option value="legal"
                                                {{ old('contacts.0.department') == 'legal' ? 'selected' : '' }}>
                                                {{ __('Legal') }}
                                            </option>
                                            <option value="hr"
                                                {{ old('contacts.0.department') == 'hr' ? 'selected' : '' }}>
                                                {{ __('Human Resources') }}
                                            </option>
                                            <option value="other"
                                                {{ old('contacts.0.department') == 'other' ? 'selected' : '' }}>
                                                {{ __('Other') }}
                                            </option>
                                        </select>
                                        @error('contacts.0.department')
                                            <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">{{ __('Primary Contact') }}</label>
                                        <select class="form-select primary-contact-select" name="contacts[0][isPrimary]">
                                            <option value="1" selected>{{ __('Yes') }}</option>
                                            <option value="0">{{ __('No') }}</option>
                                        </select>
                                        <small class="form-text text-muted">
                                            {{ __('Main point of contact for this customer') }}
                                        </small>
                                    </div>
                                </div>
                                <input type="hidden" name="contacts[0][order]" value="0" class="contact-order">
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mb-0 mt-3">
                    <i class="bi bi-shield-check me-1"></i>
                    {{ __('Contact information is stored securely and used only for communication purposes') }}
                </p>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            {{ __('Fields marked with') }} <span class="text-danger">*</span> {{ __('are required') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('customer.info') }}" class="btn btn-light">
                            <i class="bi bi-x-circle me-1"></i>
                            {{ __('Cancel') }}
                        </a>
                        <button type="button" class="btn btn-outline-primary" id="resetFormBtn">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            {{ __('Reset') }}
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ isset($customer) ? __('Save Customer') : __('Create Customer') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Screen Reader Announcements --}}
    <div role="status" aria-live="polite" aria-atomic="true" class="visually-hidden" id="formStatus"></div>
@endsection

@push('styles')
    <style>
        /* Custom Form Styles */
        .required::after {
            content: " *";
            color: var(--bs-danger);
        }

        .contact-item {
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            transform: translateY(-2px);
        }

        .section-toggle i {
            transition: transform 0.3s ease;
        }

        .section-toggle[aria-expanded="false"] i {
            transform: rotate(0deg);
        }

        .section-toggle[aria-expanded="true"] i {
            transform: rotate(90deg);
        }

        /* Select2 enhancements */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: calc(1.5em + 0.75rem + 2px);
        }

        /* Dynamic field animations */
        .dynamic-field {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Remove contact button */
        .remove-contact-btn {
            transition: all 0.2s ease;
        }

        .remove-contact-btn:hover {
            transform: scale(1.05);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            .card-header h5,
            .card-header h6 {
                font-size: 0.9rem;
            }

            .contact-item .card-body {
                padding: 1rem;
            }
        }

        /* Print styles */
        @media print {

            .page-header-breadcrumb,
            .btn,
            .alert,
            .section-toggle {
                display: none !important;
            }

            .collapse {
                display: block !important;
                height: auto !important;
            }

            .card {
                page-break-inside: avoid;
            }
        }

        /* Loading state */
        .form-loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .form-loading::after {
            content: "";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 9999;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>
@endpush

@push('script')
    <script src="{{ asset('js/add-customer-reinsurance.js') }}"></script>
@endpush
