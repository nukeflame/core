@extends('layouts.app', [
    'pageTitle' => 'Edit Customer - ' . $company->company_name,
])

@section('content')
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Customer</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('customer.info') }}">Customer</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ isset($customer) ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="mt-3">
        <form action="{{ route('customer.update', $customer->customer_id) }}" method="PUT" id="partnerForm">
            @csrf

            <div class="card border border-dark custom-card mb-3">
                <div class="card-header">
                    <div class="card-title">Partner Details</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="partnerName" class="form-label">Partner Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('partnerName') is-invalid @enderror"
                                id="partnerName" name="partnerName" value="{{ old('partnerName', $customer->name ?? '') }}"
                                placeholder="Customer name" required>
                            @error('partnerName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="customerType" class="form-label">Type of Customer</label>
                            <select class="form-control select2 @error('customerType') is-invalid @enderror"
                                id="customerType" name="customerType[]" multiple>
                                @if (isset($type_of_cust) && count($type_of_cust) > 0)
                                    @foreach ($type_of_cust as $cust_type)
                                        @php
                                            $selectedTypes = old('customerType', $customer->customer_type ?? []);
                                            $isSelected = is_array($selectedTypes)
                                                ? in_array($cust_type->type_id, $selectedTypes)
                                                : false;
                                        @endphp
                                        <option value="{{ $cust_type->type_id }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ $cust_type->type_name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No customer types available</option>
                                @endif
                            </select>
                            @error('customerType')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="financialRating" class="form-label">Financial Rating</label>
                            <input type="text" class="form-control @error('financialRating') is-invalid @enderror"
                                id="financialRating" name="financialRating"
                                value="{{ old('financialRating', $customer->financial_rate ?? '') }}"
                                placeholder="Financial Rating">
                            @error('financialRating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="agencyRating" class="form-label">Agency Rating</label>
                            <input type="text" class="form-control @error('agencyRating') is-invalid @enderror"
                                id="agencyRating" name="agencyRating"
                                value="{{ old('agencyRating', $customer->agency_rate ?? '') }}"
                                placeholder="Agency Rating">
                            @error('agencyRating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $customer->email ?? '') }}"
                                placeholder="customer@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website"
                                name="website" value="{{ old('website', $customer->website ?? '') }}"
                                placeholder="https://www.website.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="taxNo" class="form-label">Tax No</label>
                            <input type="text" class="form-control @error('taxNo') is-invalid @enderror" id="taxNo"
                                name="taxNo" value="{{ old('taxNo', $customer->tax_no ?? '') }}" placeholder="Tax No">
                            @error('taxNo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="incorporationNo" class="form-label">Incorporation No</label>
                            <input type="text" class="form-control @error('incorporationNo') is-invalid @enderror"
                                id="incorporationNo" name="incorporationNo" placeholder="Registration No"
                                value="{{ old('incorporationNo', $customer->registration_no ?? '') }}">
                            @error('incorporationNo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="street" class="form-label">Street</label>
                            <input type="text" class="form-control @error('street') is-invalid @enderror"
                                id="street" name="street" value="{{ old('street', $customer->street ?? '') }}"
                                placeholder="Enter street name">
                            @error('street')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                name="city" value="{{ old('city', $customer->city ?? '') }}" placeholder="City">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="identityType" class="form-label">Identity Type</label>
                            <select class="form-control select2 @error('identityType') is-invalid @enderror"
                                id="identityType" name="identityType">
                                <option value="">
                                    -- Select Identity Type --
                                </option>
                                @foreach ($partnersId as $pId)
                                    <option value="{{ $pId->identification_type }}"
                                        {{ (old('identityType') ?: $customer->identity_number_type ?? '') == $pId->identification_type ? 'selected' : '' }}>
                                        {{ $pId->identification_type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('identityType')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="identityNo" class="form-label">Identity No.</label>
                            <input type="text" class="form-control @error('identityNo') is-invalid @enderror"
                                id="identityNo" name="identityNo" placeholder="ID No"
                                value="{{ old('identityNo', $customer->identity_number ?? '') }}">
                            @error('identityNo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="telephone" class="form-label">Telephone</label>
                            <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                id="telephone" name="telephone"
                                value="{{ old('telephone', $customer->telephone ?? '') }}" placeholder="+254700000000" />
                            @error('telephone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="postalCode" class="form-label">Postal Code</label>
                            <input type="text" class="form-control @error('postalCode') is-invalid @enderror"
                                id="postalCode" name="postalCode"
                                value="{{ old('postalCode', $customer->postal_address ?? '') }}"
                                placeholder="Enter postal code">
                            @error('postalCode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select class="form-control select2 @error('country') is-invalid @enderror" id="country"
                                name="country">
                                <option value="" {{ !old('country', $customer->country ?? '') ? 'selected' : '' }}
                                    disabled>-- Select Country --</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->country_iso }}"
                                        {{ old('country', $customer->country_iso ?? '') == $country->country_iso ? 'selected' : '' }}>
                                        {{ $country->country_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Information Section --}}
            <div class="card border border-dark custom-card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Contact Information</div>
                    <button type="button" class="btn btn-outline-dark" id="addContactBtn">
                        <i class="bx bx-plus me-1"></i> Add Contact
                    </button>
                </div>
                <div class="card-body">
                    <div id="contactsContainer">
                        {{-- Primary Contact (always present) --}}
                        <div class="contact-item card mb-3" data-contact-index="0">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0">Primary Contact</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" name="contacts[0][name]"
                                            value="{{ old('contacts.0.name', $customer->contacts[0]->name ?? '') }}"
                                            placeholder="Enter contact name">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Contact Position</label>
                                        <input type="text" class="form-control" name="contacts[0][position]"
                                            value="{{ old('contacts.0.position', $customer->contacts[0]->position ?? '') }}"
                                            placeholder="Enter contact position">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Contact Mobile No</label>
                                        <input type="tel" class="form-control" name="contacts[0][mobile]"
                                            value="{{ old('contacts.0.mobile', $customer->contacts[0]->mobile ?? '') }}"
                                            placeholder="Enter mobile number">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Contact Email</label>
                                        <input type="email" class="form-control" name="contacts[0][email]"
                                            value="{{ old('contacts.0.email', $customer->contacts[0]->email ?? '') }}"
                                            placeholder="Enter contact email">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Department</label>
                                        <select class="form-control select2" name="contacts[0][department]">
                                            <option value=""
                                                {{ !old('contacts.0.department', $customer->contacts[0]->department ?? '') ? 'selected' : '' }}
                                                disabled>-- Select Department --</option>
                                            <option value="sales"
                                                {{ old('contacts.0.department', $customer->contacts[0]->department ?? '') == 'sales' ? 'selected' : '' }}>
                                                Sales</option>
                                            <option value="marketing"
                                                {{ old('contacts.0.department', $customer->contacts[0]->department ?? '') == 'marketing' ? 'selected' : '' }}>
                                                Marketing</option>
                                            <option value="finance"
                                                {{ old('contacts.0.department', $customer->contacts[0]->department ?? '') == 'finance' ? 'selected' : '' }}>
                                                Finance</option>
                                            <option value="technical"
                                                {{ old('contacts.0.department', $customer->contacts[0]->department ?? '') == 'technical' ? 'selected' : '' }}>
                                                Technical</option>
                                            <option value="operations"
                                                {{ old('contacts.0.department', $customer->contacts[0]->department ?? '') == 'operations' ? 'selected' : '' }}>
                                                Operations</option>
                                            <option value="other"
                                                {{ old('contacts.0.department', $customer->contacts[0]->department ?? '') == 'other' ? 'selected' : '' }}>
                                                Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Is Primary Contact</label>
                                        <select class="form-control select2" name="contacts[0][isPrimary]">
                                            <option value="1"
                                                {{ old('contacts.0.isPrimary', $customer->contacts[0]->is_primary ?? '1') == '1' ? 'selected' : '' }}>
                                                Yes</option>
                                            <option value="0"
                                                {{ old('contacts.0.isPrimary', $customer->contacts[0]->is_primary ?? '1') == '0' ? 'selected' : '' }}>
                                                No</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="contacts[0][order]" value="0" class="contact-order">
                                @if (isset($customer->contacts[0]->id))
                                    <input type="hidden" name="contacts[0][id]"
                                        value="{{ $customer->contacts[0]->id }}">
                                @endif
                            </div>
                        </div>

                        @if (isset($customer) && $customer->contacts && count($customer->contacts) > 1)
                            @foreach ($customer->contacts->slice(1) as $index => $contact)
                                @php $contactIndex = $index + 1; @endphp
                                <div class="contact-item card mb-3" data-contact-index="{{ $contactIndex }}">
                                    <div
                                        class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                        <h6 class="mb-0">Contact {{ $contactIndex + 1 }}</h6>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-contact">
                                            <i class="bi bi-x"></i> Remove
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Contact Name</label>
                                                <input type="text" class="form-control"
                                                    name="contacts[{{ $contactIndex }}][name]"
                                                    value="{{ old('contacts.' . $contactIndex . '.name', $contact->name) }}"
                                                    placeholder="Enter contact name">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Contact Position</label>
                                                <input type="text" class="form-control"
                                                    name="contacts[{{ $contactIndex }}][position]"
                                                    value="{{ old('contacts.' . $contactIndex . '.position', $contact->position) }}"
                                                    placeholder="Enter contact position">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Contact Mobile No</label>
                                                <input type="tel" class="form-control"
                                                    name="contacts[{{ $contactIndex }}][mobile]"
                                                    value="{{ old('contacts.' . $contactIndex . '.mobile', $contact->mobile) }}"
                                                    placeholder="Enter mobile number">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Contact Email</label>
                                                <input type="email" class="form-control"
                                                    name="contacts[{{ $contactIndex }}][email]"
                                                    value="{{ old('contacts.' . $contactIndex . '.email', $contact->email) }}"
                                                    placeholder="Enter contact email">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Department</label>
                                                <select class="form-control select2"
                                                    name="contacts[{{ $contactIndex }}][department]">
                                                    <option value=""
                                                        {{ !old('contacts.' . $contactIndex . '.department', $contact->department) ? 'selected' : '' }}
                                                        disabled>-- Select Department --</option>
                                                    <option value="sales"
                                                        {{ old('contacts.' . $contactIndex . '.department', $contact->department) == 'sales' ? 'selected' : '' }}>
                                                        Sales</option>
                                                    <option value="marketing"
                                                        {{ old('contacts.' . $contactIndex . '.department', $contact->department) == 'marketing' ? 'selected' : '' }}>
                                                        Marketing</option>
                                                    <option value="finance"
                                                        {{ old('contacts.' . $contactIndex . '.department', $contact->department) == 'finance' ? 'selected' : '' }}>
                                                        Finance</option>
                                                    <option value="technical"
                                                        {{ old('contacts.' . $contactIndex . '.department', $contact->department) == 'technical' ? 'selected' : '' }}>
                                                        Technical</option>
                                                    <option value="operations"
                                                        {{ old('contacts.' . $contactIndex . '.department', $contact->department) == 'operations' ? 'selected' : '' }}>
                                                        Operations</option>
                                                    <option value="other"
                                                        {{ old('contacts.' . $contactIndex . '.department', $contact->department) == 'other' ? 'selected' : '' }}>
                                                        Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Is Primary Contact</label>
                                                <select class="form-control select2"
                                                    name="contacts[{{ $contactIndex }}][isPrimary]">
                                                    <option value="1"
                                                        {{ old('contacts.' . $contactIndex . '.isPrimary', $contact->is_primary) == '1' ? 'selected' : '' }}>
                                                        Yes</option>
                                                    <option value="0"
                                                        {{ old('contacts.' . $contactIndex . '.isPrimary', $contact->is_primary) == '0' ? 'selected' : '' }}>
                                                        No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="contacts[{{ $contactIndex }}][order]"
                                            value="{{ $contactIndex }}" class="contact-order">
                                        <input type="hidden" name="contacts[{{ $contactIndex }}][id]"
                                            value="{{ $contact->id }}">
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mt-3 mb-5 text-end">
                <div class="col">
                    <a href="{{ route('customer.info') }}" class="btn btn-light me-2 px-4">Cancel</a>
                    <button type="submit" class="btn btn-dark px-4">
                        <i class="bi bi-save me-1"></i> {{ isset($customer) ? 'Update' : 'Submit' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let contactIndex = {{ isset($customer) && $customer->contacts ? count($customer->contacts) : 1 }};

            // Add new contact functionality
            document.getElementById('addContactBtn').addEventListener('click', function() {
                const contactsContainer = document.getElementById('contactsContainer');
                const newContactHtml = `
            <div class="contact-item card mb-3" data-contact-index="${contactIndex}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0">Contact ${contactIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-contact">
                        <i class="bi bi-x"></i> Remove
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contact Name</label>
                            <input type="text" class="form-control" name="contacts[${contactIndex}][name]"
                                placeholder="Enter contact name">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contact Position</label>
                            <input type="text" class="form-control" name="contacts[${contactIndex}][position]"
                                placeholder="Enter contact position">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contact Mobile No</label>
                            <input type="tel" class="form-control" name="contacts[${contactIndex}][mobile]"
                                placeholder="Enter mobile number">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contacts[${contactIndex}][email]"
                                placeholder="Enter contact email">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Department</label>
                            <select class="form-control select2" name="contacts[${contactIndex}][department]">
                                <option value="" selected disabled>-- Select Department --</option>
                                <option value="sales">Sales</option>
                                <option value="marketing">Marketing</option>
                                <option value="finance">Finance</option>
                                <option value="technical">Technical</option>
                                <option value="operations">Operations</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Is Primary Contact</label>
                            <select class="form-control select2" name="contacts[${contactIndex}][isPrimary]">
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="contacts[${contactIndex}][order]" value="${contactIndex}" class="contact-order">
                </div>
            </div>
            `;

                contactsContainer.insertAdjacentHTML('beforeend', newContactHtml);
                contactIndex++;

                // Initialize Select2 for new elements
                initializeSelect2();
            });

            // Remove contact functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-contact')) {
                    e.target.closest('.contact-item').remove();
                    reorderContacts();
                }
            });

            // Reorder contacts after removal
            function reorderContacts() {
                const contactItems = document.querySelectorAll('.contact-item');
                contactItems.forEach((item, index) => {
                    if (index === 0) return; // Skip primary contact

                    item.setAttribute('data-contact-index', index);
                    item.querySelector('h6').textContent = `Contact ${index + 1}`;

                    // Update all input names
                    const inputs = item.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        const name = input.getAttribute('name');
                        if (name && name.includes('contacts[')) {
                            const newName = name.replace(/contacts\[\d+\]/, `contacts[${index}]`);
                            input.setAttribute('name', newName);
                        }
                    });
                });
            }

            // Initialize Select2 (if using Select2)
            function initializeSelect2() {
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('.select2').select2({
                        theme: 'bootstrap4',
                        width: '100%'
                    });
                }
            }

            // Initial Select2 initialization
            initializeSelect2();

            // Form validation
            document.getElementById('partnerForm').addEventListener('submit', function(e) {
                const partnerName = document.getElementById('partnerName').value.trim();

                if (!partnerName) {
                    e.preventDefault();
                    alert('Partner Name is required');
                    document.getElementById('partnerName').focus();
                    return false;
                }

                return true;
            });
        });
    </script>
@endpush
